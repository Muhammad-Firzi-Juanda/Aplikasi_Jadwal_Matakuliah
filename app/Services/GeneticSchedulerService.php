<?php

namespace App\Services;

use App\Models\Jadwal;
use App\Models\MataKuliahDetail;
use App\Models\Rombel;
use App\Models\Ruangan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Genetic Algorithm untuk Optimasi Jadwal Kuliah
 *
 * Algoritma ini menggunakan pendekatan evolusioner untuk mencari solusi
 * jadwal yang lebih optimal dibandingkan greedy algorithm biasa.
 *
 * Workflow:
 *  1. Generate populasi awal (random chromosome)
 *  2. Evaluasi fitness setiap chromosome
 *  3. Selection (pilih chromosome terbaik)
 *  4. Crossover (cross dua chromosome)
 *  5. Mutation (random change)
 *  6. Repeat sampai konvergen atau max generation
 */
class GeneticSchedulerService
{
    public const HARI = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

    public const SLOT_MULAI = ['07:30', '10:00', '13:00', '14:50'];

    public const PERIOD_SLOTS = [
        '07:30', '08:20', '09:10', '10:00', '10:50',
        '13:00', '13:50', '14:40', '15:30', '16:20',
    ];

    public const POPULASI_SIZE = 20;

    public const MAX_GENERATION = 30;

    public const MUTATION_RATE = 0.15;

    public const ELITISM_RATE = 0.3;

    protected array $hariTersedia = [];

    protected array $slotMulaiTersedia = [];

    protected array $externalSchedules = [];

    protected array $log = [];

    protected array $peringatan = [];

    protected array $kegagalan = [];

    protected Collection $mk;

    protected string $cohort;

    protected int $semester;

    protected array $ruanganCache = [];

    protected array $dosenCache = [];

    public function generate(int $semester, ?string $prodi = 'Informatika', array $options = []): array
    {
        // Set time limit to prevent timeout
        set_time_limit(300); // 5 minutes

        return DB::transaction(function () use ($semester, $prodi, $options) {
            $this->reset($semester, $prodi, $options);

            $this->mk = $this->ambilMk($semester, $prodi);

            if ($this->mk->isEmpty()) {
                return $this->hasil($semester, 0, 'Belum ada mata kuliah aktif untuk semester ini.');
            }

            $this->cohort = $this->namaCohort($prodi, $semester);

            // Preload resource
            $this->preloadResource();

            // Generate populasi awal
            $populasi = $this->generatePopulasiAwal();

            $this->log[] = "Generasi 0: Best fitness = {$this->hitungFitness($populasi[0])}";

            // Evolusi
            for ($generasi = 1; $generasi <= self::MAX_GENERATION; $generasi++) {
                $populasi = $this->evolusi($populasi);

                $bestFitness = $this->hitungFitness($populasi[0]);

                if ($generasi % 5 === 0) {
                    $this->log[] = "Generasi {$generasi}: Best fitness = {$bestFitness}";
                }

                // Early exit jika fitness sudah sangat bagus (tidak ada conflict)
                if ($bestFitness >= 1000) {
                    $this->log[] = "Solusi optimal tercapai pada generasi {$generasi}";
                    break;
                }

                // Konvergensi: jika fitness tidak improve dalam 10 generasi
                if ($generasi > 10 && $this->cekKonvergensi($populasi)) {
                    $this->log[] = "Konvergensi tercapai pada generasi {$generasi}";
                    break;
                }
            }

            // Ambil chromosome terbaik
            $chromosomeTerbaik = $populasi[0];

            // Evaluasi potensi bentrok akhir
            $conflicts = $this->hitungConflict($chromosomeTerbaik);
            if ($conflicts['total'] > 0) {
                $this->peringatan[] = "Ditemukan {$conflicts['total']} potensi bentrok (Dosen: {$conflicts['dosen']}, Ruangan: {$conflicts['ruangan']}, Cohort: {$conflicts['cohort']}).";
            }

            // Simpan hasil
            $this->simpanChromosome($chromosomeTerbaik);

            $statistik = [
                'total_mk' => $this->mk->count(),
                'mk_terjadwal' => count($chromosomeTerbaik),
                'mk_gagal' => max(0, $this->mk->where('tipe', '!=', 'Pilihan')->count() - count($chromosomeTerbaik)),
                'conflicts' => $conflicts['total'],
            ];

            return $this->hasil($semester, count($chromosomeTerbaik), null, $statistik);
        });
    }

    protected function reset(int $semester, ?string $prodi, array $options = []): void
    {
        $this->log = [];
        $this->peringatan = [];
        $this->kegagalan = [];
        $this->externalSchedules = [];
        $this->hariTersedia = array_values(array_intersect(self::HARI, $options['hari'] ?? self::HARI));
        $this->slotMulaiTersedia = array_values(array_intersect(self::SLOT_MULAI, $options['jam_mulai'] ?? self::SLOT_MULAI));
        $this->semester = $semester;

        if (empty($this->hariTersedia)) {
            $this->hariTersedia = self::HARI;
        }

        if (empty($this->slotMulaiTersedia)) {
            $this->slotMulaiTersedia = self::SLOT_MULAI;
        }

        // Hapus jadwal lama untuk semester & prodi ini
        $mkQuery = MataKuliahDetail::where('semester', $semester);
        $this->applyProdiFilter($mkQuery, $prodi);
        $idsMk = $mkQuery->pluck('id');

        if ($idsMk->isNotEmpty()) {
            $idsRombelLama = Rombel::whereIn('mata_kuliah_detail_id', $idsMk)->pluck('id');
            Jadwal::whereIn('rombel_id', $idsRombelLama)->delete();
            Rombel::whereIn('mata_kuliah_detail_id', $idsMk)->delete();
        }

        // Muat jadwal aktif lainnya (lintas prodi / semester lain) untuk deteksi bentrok
        $existing = Jadwal::with('rombel.mataKuliah')->where('is_active', 1)->get();
        foreach ($existing as $j) {
            $this->externalSchedules[] = [
                'hari' => $j->hari,
                'ruangan_id' => $j->ruangan_id,
                'dosen_id' => $j->dosen_id,
                'mulai' => $this->menit($j->jam_mulai),
                'selesai' => $this->menit($j->jam_selesai),
            ];
        }
    }

    public function applyProdiFilter($query, ?string $prodi): void
    {
        if (! $prodi || in_array(strtolower(trim($prodi)), ['semua', 'all'], true)) {
            return;
        }

        $p = strtolower(trim($prodi));
        if (str_contains($p, 'informatika') || $p === 'ti') {
            $query->where(function ($q) {
                $q->where('prodi', 'like', '%Informatika%')
                    ->orWhere('prodi', 'like', '%TI%');
            })->where('prodi', 'not like', '%Elektro%');
        } elseif (str_contains($p, 'elektro') || $p === 'te') {
            $query->where(function ($q) {
                $q->where('prodi', 'like', '%Elektro%')
                    ->orWhere('prodi', 'like', '%TE%');
            })->where('prodi', 'not like', '%Informatika%');
        } else {
            $query->where('prodi', 'like', "%{$prodi}%");
        }
    }

    protected function ambilMk(int $semester, ?string $prodi): Collection
    {
        $query = MataKuliahDetail::with(['dosenKetua', 'dosenAnggota'])
            ->where('semester', $semester)
            ->where('is_active', 1)
            ->where('status_tawar', 1);

        $this->applyProdiFilter($query, $prodi);

        return $query->get();
    }

    protected function preloadResource(): void
    {
        $this->ruanganCache = Ruangan::where('is_active', 1)->get()->keyBy('id')->toArray();
    }

    protected function generatePopulasiAwal(): array
    {
        $populasi = [];

        for ($i = 0; $i < self::POPULASI_SIZE; $i++) {
            $chromosome = $this->generateRandomChromosome();
            $populasi[] = $chromosome;
        }

        // Sort by fitness
        usort($populasi, fn ($a, $b) => $this->hitungFitness($b) <=> $this->hitungFitness($a));

        return $populasi;
    }

    protected function generateRandomChromosome(): array
    {
        $chromosome = [];

        foreach ($this->mk as $mk) {
            if ($mk->tipe === 'Pilihan') {
                continue; // Handle MK pilihan separately
            }

            $slot = $this->cariRandomSlot($mk);
            if ($slot) {
                $chromosome[] = [
                    'mk' => $mk,
                    'slot' => $slot,
                    'ruangan' => $this->cariRandomRuangan($mk, $slot),
                    'dosen' => $this->cariRandomDosen($mk, $slot),
                ];
            }
        }

        return $chromosome;
    }

    protected function cariRandomSlot(MataKuliahDetail $mk): ?array
    {
        $durasi = $mk->sks * 50;
        $hari = $this->hariTersedia[array_rand($this->hariTersedia)];
        $slotMulaiList = $this->getModularSlotMulaiForSks($mk->sks);
        $mulai = $slotMulaiList[array_rand($slotMulaiList)];

        return [
            'hari' => $hari,
            'mulai' => $this->menit($mulai),
            'selesai' => $this->menit($mulai) + $durasi,
        ];
    }

    public function getModularSlotMulaiForSks(int $sks): array
    {
        if (! empty($this->slotMulaiTersedia) && $this->slotMulaiTersedia !== self::SLOT_MULAI) {
            return $this->slotMulaiTersedia;
        }

        $durasi = $sks * 50;

        $candidateMinutes = [
            450, // 07:30
            600, // 10:00
            780, // 13:00
            890, // 14:50
            880, // 14:40
            500, // 08:20
            550, // 09:10
            650, // 10:50
            830, // 13:50
            930, // 15:30
            980, // 16:20
        ];

        $valid = [];
        foreach ($candidateMinutes as $start) {
            $end = $start + $durasi;
            if ($end > 1080) { // maksimal jam 18:00
                continue;
            }

            // Validasi jeda istirahat siang (12:30 - 13:00 / 750 - 780 menit)
            if ($start < 780 && $end > 750) {
                if (! ($end <= 750 || $start >= 780)) {
                    continue;
                }
            }

            $timeStr = sprintf('%02d:%02d', intdiv($start, 60), $start % 60);
            if (! in_array($timeStr, $valid, true)) {
                $valid[] = $timeStr;
            }
        }

        return ! empty($valid) ? $valid : self::SLOT_MULAI;
    }

    protected function cariRandomRuangan(MataKuliahDetail $mk, array $slot): ?int
    {
        if ($mk->tanpa_ruangan) {
            return null;
        }

        $ruangan = Ruangan::where('is_active', 1)
            ->where('kapasitas', '>=', $mk->kapasitas_per_kelas)
            ->when(
                $mk->butuh_lab,
                fn ($q) => $q->where('tipe', 'Lab'),
                fn ($q) => $q->whereIn('tipe', ['Ruang Kelas', 'Aula'])
            )
            ->inRandomOrder()
            ->first();

        return $ruangan?->id;
    }

    protected function cariRandomDosen(MataKuliahDetail $mk, array $slot): ?int
    {
        $dosenList = array_filter([
            $mk->dosen_ketua_id,
            $mk->dosen_anggota_id,
        ]);

        if (empty($dosenList)) {
            return null;
        }

        return $dosenList[array_rand($dosenList)];
    }

    protected function hitungFitness(array $chromosome): float
    {
        $fitness = 0;
        $conflicts = $this->hitungConflict($chromosome);

        // Base score: setiap MK terjadwal
        $fitness += count($chromosome) * 100;

        // Penalty untuk conflict
        $fitness -= $conflicts['total'] * 50;

        // Bonus untuk tidak ada conflict
        if ($conflicts['total'] === 0) {
            $fitness += 200;
        }

        // Bonus untuk distribusi hari yang seimbang
        $distribusiHari = $this->hitungDistribusiHari($chromosome);
        $fitness += $distribusiHari * 10;

        // Bonus untuk MK berat di pagi
        $mkBeratPagi = $this->hitungMkBeratPagi($chromosome);
        $fitness += $mkBeratPagi * 5;

        return max(0, $fitness);
    }

    protected function hitungConflict(array $chromosome): array
    {
        $conflicts = [
            'dosen' => 0,
            'ruangan' => 0,
            'cohort' => 0,
            'external' => 0,
            'total' => 0,
        ];

        $count = count($chromosome);
        for ($i = 0; $i < $count; $i++) {
            $gen1 = $chromosome[$i];

            // 1. Cek bentrok dengan jadwal aktif lain (lintas prodi/semester)
            foreach ($this->externalSchedules as $ext) {
                if ($gen1['slot']['hari'] !== $ext['hari']) {
                    continue;
                }

                if (! $this->tumpang($gen1['slot']['mulai'], $gen1['slot']['selesai'], $ext['mulai'], $ext['selesai'])) {
                    continue;
                }

                if ($gen1['ruangan'] && $ext['ruangan_id'] && $gen1['ruangan'] === $ext['ruangan_id']) {
                    $conflicts['ruangan']++;
                    $conflicts['external']++;
                }

                if ($gen1['dosen'] && $ext['dosen_id'] && $gen1['dosen'] === $ext['dosen_id']) {
                    $conflicts['dosen']++;
                    $conflicts['external']++;
                }
            }

            // 2. Cek bentrok internal antar chromosome
            for ($j = $i + 1; $j < $count; $j++) {
                $gen2 = $chromosome[$j];

                if ($gen1['slot']['hari'] !== $gen2['slot']['hari']) {
                    continue;
                }

                if (! $this->tumpang($gen1['slot']['mulai'], $gen1['slot']['selesai'], $gen2['slot']['mulai'], $gen2['slot']['selesai'])) {
                    continue;
                }

                // Cek conflict dosen
                if ($gen1['dosen'] && $gen1['dosen'] === $gen2['dosen']) {
                    $conflicts['dosen']++;
                }

                // Cek conflict ruangan
                if ($gen1['ruangan'] && $gen1['ruangan'] === $gen2['ruangan']) {
                    $conflicts['ruangan']++;
                }

                // Cek conflict cohort
                if ($gen1['mk']->tipe === 'Wajib' && $gen2['mk']->tipe === 'Wajib') {
                    $coh1 = $this->namaCohort($gen1['mk']->prodi, (int) $gen1['mk']->semester, $gen1['mk']->kelas);
                    $coh2 = $this->namaCohort($gen2['mk']->prodi, (int) $gen2['mk']->semester, $gen2['mk']->kelas);

                    $parts1 = explode('|', $coh1);
                    $parts2 = explode('|', $coh2);
                    $base1 = $parts1[0].'|'.$parts1[1];
                    $base2 = $parts2[0].'|'.$parts2[1];

                    // Same cohort if exact match or if either is a general cohort of the same prodi & semester
                    if ($coh1 === $coh2 || ($base1 === $base2 && (count($parts1) < 3 || count($parts2) < 3))) {
                        $conflicts['cohort']++;
                    }
                }
            }
        }

        $conflicts['total'] = $conflicts['dosen'] + $conflicts['ruangan'] + $conflicts['cohort'] + $conflicts['external'];

        return $conflicts;
    }

    protected function hitungDistribusiHari(array $chromosome): int
    {
        $hariCount = array_fill_keys($this->hariTersedia, 0);

        foreach ($chromosome as $gen) {
            $hariCount[$gen['slot']['hari']]++;
        }

        $variance = array_reduce($hariCount, fn ($acc, $val) => $acc + pow($val - array_sum($hariCount) / count($hariCount), 2), 0);
        $stdDev = sqrt($variance / count($hariCount));

        // Semakin kecil std dev, semakin seimbang distribusinya
        return max(0, 10 - $stdDev);
    }

    protected function hitungMkBeratPagi(array $chromosome): int
    {
        $count = 0;

        foreach ($chromosome as $gen) {
            if ($gen['mk']->sks >= 3 && $gen['slot']['mulai'] < 600) {
                $count++;
            }
        }

        return $count;
    }

    protected function evolusi(array $populasi): array
    {
        $newPopulasi = [];

        // Elitism: simpan chromosome terbaik
        $elitismCount = (int) (self::POPULASI_SIZE * self::ELITISM_RATE);
        for ($i = 0; $i < $elitismCount; $i++) {
            $newPopulasi[] = $populasi[$i];
        }

        // Crossover dan mutation untuk sisanya
        while (count($newPopulasi) < self::POPULASI_SIZE) {
            $parent1 = $this->selection($populasi);
            $parent2 = $this->selection($populasi);

            $offspring = $this->crossover($parent1, $parent2);
            $offspring = $this->mutation($offspring);

            $newPopulasi[] = $offspring;
        }

        // Sort by fitness
        usort($newPopulasi, fn ($a, $b) => $this->hitungFitness($b) <=> $this->hitungFitness($a));

        return $newPopulasi;
    }

    protected function selection(array $populasi): array
    {
        // Tournament selection
        $tournamentSize = 3;
        $best = $populasi[array_rand($populasi)];

        for ($i = 1; $i < $tournamentSize; $i++) {
            $competitor = $populasi[array_rand($populasi)];
            if ($this->hitungFitness($competitor) > $this->hitungFitness($best)) {
                $best = $competitor;
            }
        }

        return $best;
    }

    protected function crossover(array $parent1, array $parent2): array
    {
        // Single-point crossover
        if (count($parent1) < 2 || count($parent2) < 2) {
            return $this->hitungFitness($parent1) > $this->hitungFitness($parent2) ? $parent1 : $parent2;
        }

        $point = rand(1, min(count($parent1), count($parent2)) - 1);

        $offspring = array_merge(
            array_slice($parent1, 0, $point),
            array_slice($parent2, $point)
        );

        return $offspring;
    }

    protected function mutation(array $chromosome): array
    {
        if (rand(0, 100) / 100 > self::MUTATION_RATE) {
            return $chromosome;
        }

        if (empty($chromosome)) {
            return $chromosome;
        }

        // Random pick gene dan mutate slot, ruangan, atau dosennya
        $index = array_rand($chromosome);
        $mk = $chromosome[$index]['mk'];

        $mutationType = rand(1, 3);
        if ($mutationType === 1) {
            $newSlot = $this->cariRandomSlot($mk);
            if ($newSlot) {
                $chromosome[$index]['slot'] = $newSlot;
            }
        } elseif ($mutationType === 2) {
            $chromosome[$index]['ruangan'] = $this->cariRandomRuangan($mk, $chromosome[$index]['slot']);
        } else {
            $chromosome[$index]['dosen'] = $this->cariRandomDosen($mk, $chromosome[$index]['slot']);
        }

        return $chromosome;
    }

    protected function cekKonvergensi(array $populasi): bool
    {
        if (count($populasi) < 2) {
            return false;
        }

        $fitnessValues = array_map(fn ($c) => $this->hitungFitness($c), $populasi);
        $mean = array_sum($fitnessValues) / count($fitnessValues);
        $variance = array_reduce($fitnessValues, fn ($acc, $val) => $acc + pow($val - $mean, 2), 0);
        $stdDev = sqrt($variance / count($fitnessValues));

        // Konvergen jika variasi fitness sangat kecil
        return $stdDev < 5;
    }

    protected function simpanChromosome(array $chromosome): void
    {
        foreach ($chromosome as $i => $gen) {
            $mk = $gen['mk'];
            $slot = $gen['slot'];
            $ruanganId = $gen['ruangan'];
            $dosenId = $gen['dosen'];

            // Gunakan kelas dari MataKuliahDetail jika ada, jika tidak gunakan nomor rombel
            // Jangan double append kelas ke kode_rombel
            $kelas = $mk->kelas ?? null;
            if ($kelas) {
                // Jika ada kelas di MK, gunakan kode_mk langsung (sudah termasuk kelas)
                $kodeRombel = $mk->kode_mk;
            } else {
                // Jika tidak ada kelas, gunakan format lama
                $kodeRombel = $mk->kode_mk.'-1';
            }

            $rombel = Rombel::updateOrCreate(
                [
                    'mata_kuliah_detail_id' => $mk->id,
                    'nomor_rombel' => 1,
                ],
                [
                    'kode_rombel' => $kodeRombel,
                    'kelas' => $kelas,
                    'kapasitas' => $mk->kapasitas_per_kelas,
                    'ruangan_id' => $ruanganId,
                    'hari' => $slot['hari'],
                    'jam_mulai' => $this->format($slot['mulai']),
                    'jam_selesai' => $this->format($slot['selesai']),
                    'dosen_pengampu_id' => $dosenId,
                    'peran_dosen' => $dosenId === $mk->dosen_ketua_id ? 'Ketua' : 'Anggota',
                    'is_active' => 1,
                ]
            );

            Jadwal::updateOrCreate(
                ['rombel_id' => $rombel->id],
                [
                    'ruangan_id' => $ruanganId,
                    'dosen_id' => $dosenId,
                    'hari' => $slot['hari'],
                    'jam_mulai' => $this->format($slot['mulai']),
                    'jam_selesai' => $this->format($slot['selesai']),
                    'is_active' => 1,
                ]
            );

            $this->log[] = sprintf(
                'OK     %s - %s | %s %s-%s',
                $mk->kode_mk,
                $mk->nama_mk,
                $slot['hari'],
                $this->format($slot['mulai']),
                $this->format($slot['selesai'])
            );
        }
    }

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

    protected function namaCohort(?string $prodi, int $semester, ?string $kelas = null): string
    {
        $p = strtolower(trim($prodi ?? ''));
        if (str_contains($p, 'informatika')) {
            $prodiClean = 'Informatika';
        } elseif (str_contains($p, 'elektro')) {
            $prodiClean = 'Elektro';
        } else {
            $prodiClean = $prodi ?: 'umum';
        }

        $base = $prodiClean.'|'.$semester;

        return $kelas ? $base.'|'.$kelas : $base;
    }

    protected function hasil(int $semester, int $jumlah, ?string $catatan = null, array $statistik = []): array
    {
        return [
            'semester' => $semester,
            'jumlah_jadwal' => $jumlah,
            'log' => $this->log,
            'peringatan' => $this->peringatan,
            'kegagalan' => $this->kegagalan,
            'catatan' => $catatan,
            'metode' => 'Genetic Algorithm',
            'statistik' => $statistik,
        ];
    }
}
