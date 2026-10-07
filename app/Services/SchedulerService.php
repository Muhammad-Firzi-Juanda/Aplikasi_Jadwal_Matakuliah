<?php

namespace App\Services;

use App\Models\Jadwal;
use App\Models\MataKuliahDetail;
use App\Models\Rombel;
use App\Models\Ruangan;
use Illuminate\Support\Collection;

/**
 * Metode: Greedy Berurutan + First-Fit Slot dengan validasi interval.
 *
 * Aturan yang ditegakkan:
 *  1. Bentrok kelas  : satu cohort (prodi+semester) tidak boleh punya 2 MK pada waktu yang sama.
 *  2. Dosen ketua    : tidak boleh bentrok di MK mana pun (hierarki IPV #1).
 *  3. Dosen anggota  : tidak boleh bentrok juga, namun bergantian dengan ketua.
 *  4. Slot berurutan : mulai 07:30 -> 10:00 -> 13:00 -> 14:50 (jarak 10 menit).
 *  5. MK pilihan     : dibagi ke MAKS_PILIHAN kelompok slot. MK dalam satu
 *                      kelompok sharing slot sehingga saling bentrok; antar
 *                      kelompok memakai slot berbeda. Mahasiswa hanya boleh
 *                      mengambil 1 MK per kelompok, jadi maksimal 2 MK pilihan.
 *  6. Ruangan        : tidak boleh dipakai dua kelas pada waktu yang sama.
 */
class SchedulerService
{
    public const HARI = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

    /** Jam mulai slot. Slot berikutnya selalu >= slot ini. */
    public const SLOT_MULAI = ['07:30', '10:00', '13:00', '14:50'];

    public const MAKS_PILIHAN = 2;

    protected array $hariTersedia = [];

    protected array $slotMulaiTersedia = [];

    protected array $log = [];

    protected array $peringatan = [];

    protected array $slotPilihan = [];

    /** @var array<int,array{hari:string,ruangan_id:int,mulai:int,selesai:int}> */
    protected array $pakaiRuangan = [];

    /** @var array<int,array<int,array{hari:string,mulai:int,selesai:int}>> */
    protected array $pakaiDosen = [];

    /** @var array<string,array<int,array{hari:string,mulai:int,selesai:int}>> */
    protected array $kunciCohort = [];

    protected array $tersimpan = [];

    public function generate(int $semester, ?string $prodi = 'Informatika', array $options = []): array
    {
        $this->reset($semester, $prodi, $options);

        $mk = $this->ambilMk($semester, $prodi);

        if ($mk->isEmpty()) {
            return $this->hasil($semester, 0, 'Belum ada mata kuliah aktif untuk semester ini.');
        }

        $cohort = $this->namaCohort($prodi, $semester);

        $wajib = $mk->where('tipe', 'Wajib')
            ->sortByDesc(fn ($m) => [$m->butuh_lab, $m->sks, $m->jumlah_kelas])
            ->values();

        foreach ($wajib as $m) {
            $this->tempatkan($m, $cohort);
        }

        $pilihan = $mk->where('tipe', 'Pilihan');
        if ($pilihan->isNotEmpty()) {
            $this->tempatkanPilihan($pilihan, $cohort, $semester);
        }

        return $this->hasil($semester, count($this->tersimpan));
    }

    protected function ambilMk(int $semester, ?string $prodi): Collection
    {
        return MataKuliahDetail::with(['dosenKetua', 'dosenAnggota'])
            ->where('semester', $semester)
            ->where('is_active', 1)
            ->when($prodi, fn ($q) => $q->where('prodi', 'like', "%{$prodi}%"))
            ->get();
    }

    /* ------------------------------------------------------------------ */
    /* Penempatan wajib */
    /* ------------------------------------------------------------------ */

    protected function tempatkan(MataKuliahDetail $mk, string $cohort): void
    {
        $slot = $this->cariSlot($mk, $cohort);
        if (! $slot) {
            $this->log[] = "GAGAL  {$mk->kode_mk} - {$mk->nama_mk}: tidak ada slot yang memenuhi syarat (ruangan/dosen).";

            return;
        }

        $total = $mk->jumlah_kelas;
        $terpasang = 0;
        $dosenList = $this->dosenList($mk);

        // Satu slot dipakai bersama oleh semua kelas paralel, jadi jumlah kelas
        // tidak boleh melebihi jumlah dosen yang tersedia.
        if ($dosenList) {
            $total = min($total, count($dosenList));
        }

        while ($terpasang < $total) {
            $ruangan = $this->cariRuangan($mk, $slot);
            if (! $ruangan) {
                break;
            }

            $dosen = $this->pilihDosen($dosenList, $slot);
            if ($dosen === false) {
                break;
            }

            $peran = ($dosen !== null && $dosen === $mk->dosen_ketua_id) ? 'Ketua' : 'Anggota';

            $this->simpan($mk, $terpasang + 1, $slot, $ruangan, $dosen, $peran);
            $this->pakaiRuangan[] = [
                'hari' => $slot['hari'],
                'ruangan_id' => $ruangan->id,
                'mulai' => $slot['mulai'],
                'selesai' => $slot['selesai'],
            ];
            if ($dosen) {
                $this->pakaiDosen[$dosen][] = [
                    'hari' => $slot['hari'],
                    'mulai' => $slot['mulai'],
                    'selesai' => $slot['selesai'],
                ];
            }
            $terpasang++;
        }

        $this->kunciCohortSlot($cohort, $slot);

        $sisa = $mk->jumlah_kelas - $terpasang;
        $catatan = $sisa > 0
            ? " | PERINGATAN: {$sisa} kelas tidak dijadwalkan (kurang dosen/ruangan)"
            : '';

        $this->log[] = sprintf(
            'OK     %s - %s | %d/%d kelas | %s %s-%s%s',
            $mk->kode_mk,
            $mk->nama_mk,
            $terpasang,
            $mk->jumlah_kelas,
            $slot['hari'],
            $this->format($slot['mulai']),
            $this->format($slot['selesai']),
            $catatan
        );
    }

    /** @return int|false|null */
    protected function pilihDosen(array $dosenList, array $slot)
    {
        if (! $dosenList) {
            return null;
        }

        foreach ($dosenList as $d) {
            if (! $this->dosenBentrok($d, $slot)) {
                return $d;
            }
        }

        return false;
    }

    /* ------------------------------------------------------------------ */
    /* MK Pilihan: satu slot, saling bentrok, maksimal 2 */
    /* ------------------------------------------------------------------ */

    /**
     * MK Pilihan dibagi menjadi beberapa KELOMPOK SLOT (jumlahnya = MAKS_PILIHAN).
     *
     * Setiap kelompok Sharing satu slot sehingga MK di dalamnya saling bentrok
     * dan mahasiswa tidak bisa mengambil dua MK dari kelompok yang sama. Karena
     * kelompok memakai slot berbeda, mahasiswa tetap bisa mengambil satu MK dari
     * tiap kelompok, jadi batas pengambilanMK Pilihan = MAKS_PILIHAN.
     *
     * Contoh MAKS_PILIHAN = 2:
     *   Kelompok 1 -> Senin 07:30 : MK-A, MK-C
     *   Kelompok 2 -> Senin 10:00 : MK-B, MK-D
     * Ambil maksimal 2: satu dari kelompok 1 + satu dari kelompok 2.
     */
    protected function tempatkanPilihan(Collection $pilihan, string $cohort, int $semester): void
    {
        $kelompok = $this->bagiPilihan($pilihan);

        foreach ($kelompok as $noKelompok => $grup) {
            if ($grup->isEmpty()) {
                continue;
            }

            $slot = $this->cariSlotPilihan($grup, $cohort);
            if (! $slot) {
                $this->log[] = "GAGAL  MK Pilihan kelompok {$noKelompok}: tidak ada slot yang bebas.";

                continue;
            }

            $durasiMaks = 0;
            $terjadwal = [];

            foreach ($grup as $mk) {
                $ruangan = $this->cariRuangan($mk, $slot);
                if (! $ruangan) {
                    $this->peringatan[] = "MK Pilihan {$mk->kode_mk}: tidak ada ruangan kosong di slot pilihan.";

                    continue;
                }

                $dosen = $mk->dosen_ketua_id ?? $mk->dosen_anggota_id;
                if ($dosen && $this->dosenBentrok($dosen, $slot)) {
                    $this->peringatan[] = "MK Pilihan {$mk->kode_mk}: dosennya bentrok, dilewati.";

                    continue;
                }

                // Durasi mengikuti SKS masing-masing MK, bukan SKS terbesar di slot.
                $slotMk = [
                    'hari' => $slot['hari'],
                    'mulai' => $slot['mulai'],
                    'selesai' => $slot['mulai'] + ($mk->sks * 50),
                ];
                $durasiMaks = max($durasiMaks, $slotMk['selesai'] - $slotMk['mulai']);

                // MK Pilihan wajib punya dosen. Kalau tidak ada, jangan dijadwalkan
                // supaya tidak muncul kelas tanpa pengampu di tabel jadwal.
                if (! $dosen) {
                    $this->peringatan[] = "MK Pilihan {$mk->kode_mk}: belum ada dosen, dilewati.";

                    continue;
                }

                $peran = ($dosen === $mk->dosen_ketua_id) ? 'Ketua' : 'Anggota';
                $this->simpan($mk, 1, $slotMk, $ruangan, $dosen, $peran);
                $this->pakaiRuangan[] = [
                    'hari' => $slotMk['hari'],
                    'ruangan_id' => $ruangan->id,
                    'mulai' => $slotMk['mulai'],
                    'selesai' => $slotMk['selesai'],
                ];
                if ($dosen) {
                    $this->pakaiDosen[$dosen][] = [
                        'hari' => $slotMk['hari'],
                        'mulai' => $slotMk['mulai'],
                        'selesai' => $slotMk['selesai'],
                    ];
                }
                $terjadwal[] = $mk->kode_mk;

                $this->log[] = sprintf(
                    'OK     PILIHAN[%d] %s - %s | %s %s-%s | %s',
                    $noKelompok,
                    $mk->kode_mk,
                    $mk->nama_mk,
                    $slotMk['hari'],
                    $this->format($slotMk['mulai']),
                    $this->format($slotMk['selesai']),
                    $ruangan->kode
                );
            }

            if ($terjadwal === []) {
                continue;
            }

            // Kunci cohort dengan durasi terjavadoc yang sebenarnya terpakai,
            // bukan SKS terbesar, supaya tidak memblokir slot lain sia-sia.
            $slotTerkunci = [
                'hari' => $slot['hari'],
                'mulai' => $slot['mulai'],
                'selesai' => $slot['mulai'] + $durasiMaks,
            ];
            $this->kunciCohortSlot($cohort, $slotTerkunci);

            $slotTerkunci['label'] = sprintf(
                'Kelompok %d: %s %s-%s (%s)',
                $noKelompok,
                $slotTerkunci['hari'],
                $this->format($slotTerkunci['mulai']),
                $this->format($slotTerkunci['selesai']),
                implode(', ', $terjadwal)
            );
            $this->slotPilihan[$semester][] = $slotTerkunci;
        }

        if ($this->slotPilihan[$semester] ?? false) {
            $this->kunciCohortSlot($cohort, $this->slotPilihan[$semester][0]);
        }
    }

    /**
     * Bagi MK Pilihan ke MAKS_PILIHAN kelompok secara bergilir.
     *MK dengan SKS besar Biography lebih dulu agar(pk) durasi paling lama
     * mendapat kelompok dengan ruangan paling longgar.
     *
     * @return array<int,Collection>
     */
    protected function bagiPilihan(Collection $pilihan): array
    {
        $urut = $pilihan->sortByDesc(fn ($m) => [$m->sks, $m->butuh_lab, $m->jumlah_kelas])->values();

        $kelompok = [];
        for ($i = 0; $i < self::MAKS_PILIHAN; $i++) {
            $kelompok[$i + 1] = collect();
        }

        foreach ($urut as $i => $mk) {
            $kelompok[($i % self::MAKS_PILIHAN) + 1]->push($mk);
        }

        return $kelompok;
    }

    /* ------------------------------------------------------------------ */
    /* Pencarian slot */
    /* ------------------------------------------------------------------ */

    /**
     * Cari slot terbaik: dari semua slot yang valid, pilih yang ruangan
     * paling longgar. Ini menjaga kelas paralel tetap kebagian ruangan
     * walau jadwal semester lain sudah memakai sebagian ruangan.
     */
    protected function cariSlot(MataKuliahDetail $mk, string $cohort): ?array
    {
        $durasi = $mk->sks * 50;
        $dosenList = $this->dosenList($mk);
        $terbaik = null;
        $skorTerbaik = -1;

        foreach ($this->hariTersedia as $hari) {
            foreach ($this->slotMulaiTersedia as $mulai) {
                $slot = [
                    'hari' => $hari,
                    'mulai' => $this->menit($mulai),
                    'selesai' => $this->menit($mulai) + $durasi,
                ];

                if ($this->cohortBentrok($cohort, $slot)) {
                    continue;
                }

                $bebas = $this->hitungRuanganBebas($mk, $slot);
                if ($bebas < $mk->jumlah_kelas) {
                    continue;
                }

                // Setiap kelas paralel harus diampu dosen yang berbeda,
                // karena satu slot dipakai bersamaan oleh semua kelas paralel.
                if ($dosenList) {
                    $dosenBebas = count(array_filter(
                        $dosenList,
                        fn ($d) => ! $this->dosenBentrok($d, $slot)
                    ));
                    if ($dosenBebas < min($mk->jumlah_kelas, count($dosenList))) {
                        continue;
                    }
                }

                if ($bebas > $skorTerbaik) {
                    $skorTerbaik = $bebas;
                    $terbaik = $slot;
                }
            }
        }

        return $terbaik;
    }

    /**
     * Cari slot MK Pilihan yang bisa menampung SEMUA pilihan terpilih.
     * Semua MK Pilihan memang sengaja diletakkan pada slot yang sama,
     * jadi syaratnya: cukup ruangan terpisah dan tidak ada dosen yang bentrok.
     */
    protected function cariSlotPilihan(Collection $terambil, string $cohort): ?array
    {
        if ($terambil->isEmpty()) {
            return null;
        }

        $durasi = $terambil->max('sks') * 50;
        $terbaik = null;
        $skorTerbaik = -1;

        foreach ($this->hariTersedia as $hari) {
            foreach ($this->slotMulaiTersedia as $mulai) {
                $slot = [
                    'hari' => $hari,
                    'mulai' => $this->menit($mulai),
                    'selesai' => $this->menit($mulai) + $durasi,
                ];

                if ($this->cohortBentrok($cohort, $slot)) {
                    continue;
                }

                $skor = 0;
                $ruanganTerpakai = [];
                foreach ($terambil as $mk) {
                    $dosen = $mk->dosen_ketua_id ?? $mk->dosen_anggota_id;
                    if ($dosen && $this->dosenBentrok($dosen, $slot)) {
                        continue;
                    }

                    $ruang = $this->ruanganTersedia($mk, $slot)
                        ->reject(fn ($r) => in_array($r->id, $ruanganTerpakai, true))
                        ->first();

                    if (! $ruang) {
                        continue;
                    }

                    $ruanganTerpakai[] = $ruang->id;
                    $skor++;
                }

                if ($skor > $skorTerbaik) {
                    $skorTerbaik = $skor;
                    $terbaik = $slot;
                }
            }
        }

        return $skorTerbaik > 0 ? $terbaik : null;
    }

    /* ------------------------------------------------------------------ */
    /* Ruangan & dosen */
    /* ------------------------------------------------------------------ */

    protected function hitungRuanganBebas(MataKuliahDetail $mk, array $slot): int
    {
        return $this->ruanganTersedia($mk, $slot)->count();
    }

    protected function cariRuangan(MataKuliahDetail $mk, array $slot): ?Ruangan
    {
        return $this->ruanganTersedia($mk, $slot)->first();
    }

    protected function ruanganTersedia(MataKuliahDetail $mk, array $slot): Collection
    {
        return Ruangan::where('is_active', 1)
            ->where('kapasitas', '>=', $mk->kapasitas_per_kelas)
            ->when(
                $mk->butuh_lab,
                fn ($q) => $q->where('tipe', 'Lab'),
                fn ($q) => $q->whereIn('tipe', ['Ruang Kelas', 'Aula'])
            )
            ->get()
            ->reject(fn ($r) => $this->ruanganBentrok($r->id, $slot))
            ->values();
    }

    protected function ruanganBentrok(int $ruanganId, array $slot): bool
    {
        foreach ($this->pakaiRuangan as $p) {
            if ($p['hari'] !== $slot['hari']) {
                continue;
            }
            if ($p['ruangan_id'] !== $ruanganId) {
                continue;
            }
            if ($this->tumpang($slot['mulai'], $slot['selesai'], $p['mulai'], $p['selesai'])) {
                return true;
            }
        }

        return false;
    }

    protected function dosenBentrok(?int $dosenId, array $slot): bool
    {
        if (! $dosenId) {
            return false;
        }
        foreach ($this->pakaiDosen[$dosenId] ?? [] as $p) {
            if ($p['hari'] !== $slot['hari']) {
                continue;
            }
            if ($this->tumpang($slot['mulai'], $slot['selesai'], $p['mulai'], $p['selesai'])) {
                return true;
            }
        }

        return false;
    }

    protected function cohortBentrok(string $cohort, array $slot): bool
    {
        foreach ($this->kunciCohort[$cohort] ?? [] as $p) {
            if ($p['hari'] !== $slot['hari']) {
                continue;
            }
            if ($this->tumpang($slot['mulai'], $slot['selesai'], $p['mulai'], $p['selesai'])) {
                return true;
            }
        }

        return false;
    }

    protected function kunciCohortSlot(string $cohort, array $slot): void
    {
        $this->kunciCohort[$cohort][] = [
            'hari' => $slot['hari'],
            'mulai' => $slot['mulai'],
            'selesai' => $slot['selesai'],
        ];
    }

    protected function dosenList(MataKuliahDetail $mk): array
    {
        return array_values(array_unique(array_filter([
            $mk->dosen_ketua_id,
            $mk->dosen_anggota_id,
        ])));
    }

    /* ------------------------------------------------------------------ */
    /* Simpan */
    /* ------------------------------------------------------------------ */

    protected function simpan(
        MataKuliahDetail $mk,
        int $no,
        array $slot,
        Ruangan $ruangan,
        ?int $dosenId,
        string $peran
    ): void {
        $rombel = Rombel::updateOrCreate(
            [
                'mata_kuliah_detail_id' => $mk->id,
                'nomor_rombel' => $no,
            ],
            [
                'kode_rombel' => $mk->kode_mk.'-'.$no,
                'kapasitas' => $mk->kapasitas_per_kelas,
                'ruangan_id' => $ruangan->id,
                'hari' => $slot['hari'],
                'jam_mulai' => $this->format($slot['mulai']),
                'jam_selesai' => $this->format($slot['selesai']),
                'dosen_pengampu_id' => $dosenId,
                'peran_dosen' => $peran,
                'is_active' => 1,
            ]
        );

        Jadwal::updateOrCreate(
            ['rombel_id' => $rombel->id],
            [
                'ruangan_id' => $ruangan->id,
                'dosen_id' => $dosenId,
                'hari' => $slot['hari'],
                'jam_mulai' => $this->format($slot['mulai']),
                'jam_selesai' => $this->format($slot['selesai']),
                'is_active' => 1,
            ]
        );

        $this->tersimpan[] = compact('mk', 'no', 'slot', 'ruangan', 'dosenId', 'peran');
    }

    /* ------------------------------------------------------------------ */
    /* Reset & muat state semester lain */
    /* ------------------------------------------------------------------ */

    protected function reset(int $semester, ?string $prodi, array $options = []): void
    {
        $this->log = [];
        $this->peringatan = [];
        $this->slotPilihan = [];
        $this->pakaiRuangan = [];
        $this->pakaiDosen = [];
        $this->kunciCohort = [];
        $this->tersimpan = [];
        $this->hariTersedia = array_values(array_intersect(self::HARI, $options['hari'] ?? self::HARI));
        $this->slotMulaiTersedia = array_values(array_intersect(self::SLOT_MULAI, $options['jam_mulai'] ?? self::SLOT_MULAI));

        if (empty($this->hariTersedia)) {
            $this->hariTersedia = self::HARI;
        }

        if (empty($this->slotMulaiTersedia)) {
            $this->slotMulaiTersedia = self::SLOT_MULAI;
        }

        $idsMk = MataKuliahDetail::where('semester', $semester)
            ->when($prodi, fn ($w) => $w->where('prodi', 'like', "%{$prodi}%"))
            ->pluck('id');

        if ($idsMk->isNotEmpty()) {
            $idsRombelLama = Rombel::whereIn('mata_kuliah_detail_id', $idsMk)->pluck('id');
            Jadwal::whereIn('rombel_id', $idsRombelLama)->delete();
            Rombel::whereIn('mata_kuliah_detail_id', $idsMk)->delete();
        }

        $existing = Jadwal::with('rombel.mataKuliah')->where('is_active', 1)->get();

        foreach ($existing as $j) {
            $mulai = $this->menit($j->jam_mulai);
            $selesai = $this->menit($j->jam_selesai);

            $this->pakaiRuangan[] = [
                'hari' => $j->hari,
                'ruangan_id' => $j->ruangan_id,
                'mulai' => $mulai,
                'selesai' => $selesai,
            ];

            if ($j->dosen_id) {
                $this->pakaiDosen[$j->dosen_id][] = [
                    'hari' => $j->hari,
                    'mulai' => $mulai,
                    'selesai' => $selesai,
                ];
            }

            $mk = $j->rombel?->mataKuliah;
            if ($mk && $mk->tipe !== 'Pilihan') {
                $coh = $this->namaCohort($mk->prodi, (int) $mk->semester);
                $this->kunciCohort[$coh][] = [
                    'hari' => $j->hari,
                    'mulai' => $mulai,
                    'selesai' => $selesai,
                ];
            }
        }
    }

    /* ------------------------------------------------------------------ */
    /* Helper */
    /* ------------------------------------------------------------------ */

    protected function tumpang(int $a1, int $a2, int $b1, int $b2): bool
    {
        return $a1 < $b2 && $a2 > $b1;
    }

    protected function menit($waktu): int
    {
        $waktu = (string) $waktu;
        [$j, $m] = array_map('intval', explode(':', $waktu));

        return $j * 60 + $m;
    }

    protected function format(int $menit): string
    {
        return sprintf('%02d:%02d', intdiv($menit, 60), $menit % 60);
    }

    protected function namaCohort(?string $prodi, int $semester): string
    {
        return ($prodi ?: 'umum').'|'.$semester;
    }

    protected function hasil(int $semester, int $jumlah, ?string $catatan = null): array
    {
        $slot = $this->slotPilihan[$semester] ?? [];

        return [
            'semester' => $semester,
            'jumlah_jadwal' => $jumlah,
            'log' => $this->log,
            'peringatan' => $this->peringatan,
            'catatan' => $catatan,
            'slot_pilihan' => $slot,
            // Kuota MK Pilihan yang boleh diambil mahasiswa.
            'kuota_pilihan' => count($slot),
        ];
    }
}
