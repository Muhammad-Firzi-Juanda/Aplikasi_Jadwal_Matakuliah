<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
use App\Models\MataKuliah;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class JadwalKuliahController extends Controller
{
    public const SEMESTER_GANJIL = [1, 3, 5, 7];

    public const SEMESTER_GENAP = [2, 4, 6, 8];

    public const SEMESTER_ALL = [1, 2, 3, 4, 5, 6, 7, 8];

    public function index(Request $request): View
    {
        $user = auth()->user();
        $userRole = $user?->role;
        $userProdi = $user?->prodi?->nama;

        $fakultas = Fakultas::all();
        $prodi = Prodi::with('fakultas')->orderBy('nama')->get();
        $mataKuliahFakultas = MataKuliah::where('tipe', 'Fakultas')->orderBy('semester')->orderBy('kode')->get();

        // Ambil mata kuliah prodi (difilter jika user adalah role Prodi)
        $mkProdiQuery = MataKuliah::where('tipe', 'Prodi');
        if ($userRole === 'Prodi' && $userProdi) {
            $mkProdiQuery->where(function ($q) use ($user, $userProdi) {
                if ($user->prodi_id) {
                    $q->where('level_id', $user->prodi_id);
                }
                $q->orWhere('pemilik', 'like', "%{$userProdi}%");
            });
        }
        $mataKuliahProdi = $mkProdiQuery
            ->orderBy('semester')
            ->orderBy('kode')
            ->get();

        // Identifikasi prodi master terdaftar
        $prodiMap = $prodi->keyBy('id');
        $prodiInformatika = $prodi->first(function ($p) {
            $norm = strtolower($p->nama ?? '');
            $kd = strtoupper($p->kode ?? '');

            return str_contains($norm, 'informatik') || in_array($kd, ['INF', 'TIF', 'IF', 'TI']);
        });
        $prodiElektro = $prodi->first(function ($p) {
            $norm = strtolower($p->nama ?? '');
            $kd = strtoupper($p->kode ?? '');

            return str_contains($norm, 'elektro') || in_array($kd, ['TE', 'EEL', 'EL']);
        });
        $prodiSI = $prodi->first(function ($p) {
            $norm = strtolower($p->nama ?? '');
            $kd = strtoupper($p->kode ?? '');

            return str_contains($norm, 'sistem informasi') || in_array($kd, ['SI', 'TSI']);
        });

        // Kumpulkan daftar prodi unik non-master jika ada mata kuliah dari prodi yang belum dibuat di master
        $extraProdiButtons = collect();

        // Petakan nama dan kunci prodi ke setiap item mata kuliah prodi untuk filtering akurat
        $mataKuliahProdi->transform(function ($mk) use ($prodiMap, $prodiInformatika, $prodiElektro, $prodiSI, &$extraProdiButtons) {
            $kodeUpper = strtoupper(trim($mk->kode ?? ''));
            $pemilikLower = strtolower(trim($mk->pemilik ?? ''));
            $namaLower = strtolower(trim($mk->nama ?? ''));

            $isElektro = str_starts_with($kodeUpper, 'EEL') || str_starts_with($kodeUpper, 'TE') || str_starts_with($kodeUpper, 'TET') || str_starts_with($kodeUpper, 'EL') || str_contains($pemilikLower, 'elektro') || str_contains($namaLower, 'elektro');
            $isInformatika = str_starts_with($kodeUpper, 'INF') || str_starts_with($kodeUpper, 'TIF') || str_starts_with($kodeUpper, 'IF') || str_starts_with($kodeUpper, 'KOM') || str_contains($pemilikLower, 'informatik') || str_contains($namaLower, 'informatik');
            $isSI = str_starts_with($kodeUpper, 'SI') || str_starts_with($kodeUpper, 'TSI') || str_contains($pemilikLower, 'sistem informasi');

            if ($isElektro) {
                if ($prodiElektro) {
                    $mk->filter_prodi_key = (string) $prodiElektro->id;
                    $mk->nama_prodi = $prodiElektro->nama;
                    $mk->kode_prodi = $prodiElektro->kode ?: 'EEL';
                } else {
                    $mk->filter_prodi_key = 'elektro';
                    $mk->nama_prodi = $mk->pemilik ?: 'Teknik Elektro';
                    $mk->kode_prodi = 'EEL';
                    $extraProdiButtons->put('elektro', $mk->nama_prodi);
                }
            } elseif ($isInformatika) {
                if ($prodiInformatika) {
                    $mk->filter_prodi_key = (string) $prodiInformatika->id;
                    $mk->nama_prodi = $prodiInformatika->nama;
                    $mk->kode_prodi = $prodiInformatika->kode ?: 'INF';
                } else {
                    $mk->filter_prodi_key = 'informatika';
                    $mk->nama_prodi = $mk->pemilik ?: 'Teknik Informatika';
                    $mk->kode_prodi = 'INF';
                    $extraProdiButtons->put('informatika', $mk->nama_prodi);
                }
            } elseif ($isSI) {
                if ($prodiSI) {
                    $mk->filter_prodi_key = (string) $prodiSI->id;
                    $mk->nama_prodi = $prodiSI->nama;
                    $mk->kode_prodi = $prodiSI->kode ?: 'SI';
                } else {
                    $mk->filter_prodi_key = 'sistem_informasi';
                    $mk->nama_prodi = $mk->pemilik ?: 'Sistem Informasi';
                    $mk->kode_prodi = 'SI';
                    $extraProdiButtons->put('sistem_informasi', $mk->nama_prodi);
                }
            } else {
                $matchedProdi = $prodiMap->get($mk->level_id);
                if ($matchedProdi) {
                    $mk->filter_prodi_key = (string) $matchedProdi->id;
                    $mk->nama_prodi = $matchedProdi->nama;
                    $mk->kode_prodi = $matchedProdi->kode;
                } elseif (! empty($mk->pemilik) && ! in_array($mk->pemilik, ['Fakultas', 'Universitas', 'Umum'], true)) {
                    $cleanKey = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $mk->pemilik));
                    $mk->filter_prodi_key = $cleanKey;
                    $mk->nama_prodi = $mk->pemilik;
                    $mk->kode_prodi = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $mk->pemilik), 0, 4));
                    $extraProdiButtons->put($cleanKey, $mk->nama_prodi);
                } else {
                    $mk->filter_prodi_key = 'umum';
                    $mk->nama_prodi = 'Umum / Belum Ditentukan';
                    $mk->kode_prodi = '-';
                }
            }

            return $mk;
        });

        // Sinkronisasi data jadwal riil dari database (tabel jadwals hasil penjadwalan)
        $jadwalQuery = DB::table('jadwals')
            ->join('rombels', 'rombels.id', '=', 'jadwals.rombel_id')
            ->join('mata_kuliah_details', 'mata_kuliah_details.id', '=', 'rombels.mata_kuliah_detail_id')
            ->leftJoin('ruangans', 'ruangans.id', '=', 'jadwals.ruangan_id')
            ->leftJoin('dosens as dosen_jadwal', 'dosen_jadwal.id', '=', 'jadwals.dosen_id')
            ->leftJoin('dosens as dosen_ketua', 'dosen_ketua.id', '=', 'mata_kuliah_details.dosen_ketua_id')
            ->leftJoin('dosens as dosen_anggota', 'dosen_anggota.id', '=', 'mata_kuliah_details.dosen_anggota_id')
            ->where('jadwals.is_active', 1)
            ->where('rombels.is_active', 1)
            ->where(function ($q) {
                $q->whereNull('rombels.kelas')
                    ->orWhere(function ($sub) {
                        $sub->where('rombels.kelas', 'not like', '%ulang%')
                            ->where('rombels.kelas', 'not like', '%mengulang%');
                    });
            })
            ->where('rombels.kode_rombel', 'not like', '%-ulang%')
            ->where('rombels.kode_rombel', 'not like', '%-mengulang%')
            ->where('mata_kuliah_details.kode_mk', 'not like', '%-ulang%')
            ->where('mata_kuliah_details.kode_mk', 'not like', '%-mengulang%')
            ->where('mata_kuliah_details.nama_mk', 'not like', '%(mengulang)%');

        if ($userRole === 'Prodi' && $userProdi) {
            $jadwalQuery->where(function ($q) use ($userProdi) {
                $q->where('mata_kuliah_details.prodi', 'like', "%{$userProdi}%")
                    ->orWhereIn('mata_kuliah_details.kategori', ['MK Universitas', 'MK Fakultas']);
            });
        }

        $jadwal = $jadwalQuery
            ->select([
                'mata_kuliah_details.semester',
                'mata_kuliah_details.kode_mk',
                'mata_kuliah_details.nama_mk',
                'mata_kuliah_details.sks',
                'mata_kuliah_details.tipe',
                'mata_kuliah_details.prodi',
                'mata_kuliah_details.butuh_lab',
                'mata_kuliah_details.tanpa_ruangan',
                'mata_kuliah_details.kapasitas_per_kelas',
                'mata_kuliah_details.kelas as mk_kelas',
                'rombels.kode_rombel',
                'rombels.kelas as rombel_kelas',
                'rombels.nomor_rombel',
                'rombels.kapasitas as rombel_kapasitas',
                'jadwals.hari',
                'jadwals.jam_mulai',
                'jadwals.jam_selesai',
                'ruangans.kode as kode_ruangan',
                'ruangans.nama as nama_ruangan',
                'ruangans.tipe as tipe_ruangan',
                'ruangans.kapasitas as kapasitas_ruangan',
                'dosen_jadwal.nama as nama_dosen_jadwal',
                'dosen_jadwal.inisial as inisial_dosen_jadwal',
                'dosen_ketua.nama as nama_dosen_ketua',
                'dosen_ketua.inisial as inisial_dosen_ketua',
                'dosen_anggota.nama as nama_dosen_anggota',
                'dosen_anggota.inisial as inisial_dosen_anggota',
            ])
            ->orderBy('mata_kuliah_details.semester')
            ->orderBy('jadwals.hari')
            ->orderBy('jadwals.jam_mulai')
            ->get()
            ->groupBy('semester');

        $totalJadwalQuery = DB::table('jadwals')
            ->join('rombels', 'rombels.id', '=', 'jadwals.rombel_id')
            ->join('mata_kuliah_details', 'mata_kuliah_details.id', '=', 'rombels.mata_kuliah_detail_id')
            ->where('jadwals.is_active', 1)
            ->where('rombels.is_active', 1);

        if ($userRole === 'Prodi' && $userProdi) {
            $totalJadwalQuery->where(function ($q) use ($userProdi) {
                $q->where('mata_kuliah_details.prodi', 'like', "%{$userProdi}%")
                    ->orWhereIn('mata_kuliah_details.kategori', ['MK Universitas', 'MK Fakultas']);
            });
        }

        $totalJadwalTerjadwal = $totalJadwalQuery->count();
        $defaultProdiFilter = ($userRole === 'Prodi' && $userProdi) ? strtolower($userProdi) : 'all';

        return view('jadwal-kuliah.index', [
            'fakultas' => $fakultas,
            'prodi' => $prodi,
            'mataKuliahFakultas' => $mataKuliahFakultas,
            'mataKuliahProdi' => $mataKuliahProdi,
            'jadwal' => $jadwal,
            'totalJadwalTerjadwal' => $totalJadwalTerjadwal,
            'semesterGanjil' => self::SEMESTER_GANJIL,
            'semesterGenap' => self::SEMESTER_GENAP,
            'semesterAll' => self::SEMESTER_ALL,
            'prodiFilter' => $request->get('prodi', $defaultProdiFilter),
            'extraProdiButtons' => $extraProdiButtons,
        ]);
    }
}
