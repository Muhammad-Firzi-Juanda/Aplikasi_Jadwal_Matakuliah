<?php

namespace App\Services;

use App\Models\Dosen;
use App\Models\MataKuliahDetail;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MataKuliahImportService
{
    public const HEADERS = [
        'kode_mk',
        'nama_mk',
        'sks',
        'semester',
        'tipe',
        'prodi',
        'dosen_ketua',
        'dosen_anggota',
        'jumlah_kelas',
        'kapasitas_per_kelas',
        'butuh_lab',
    ];

    protected array $aliases = [
        'kode_mk' => ['kode_mk', 'kode mk', 'kode', 'code', 'kd_mk', 'kd mk', 'kode matkul', 'kode matakuliah'],
        'nama_mk' => ['nama_mk', 'nama mk', 'nama', 'nama matkul', 'nama matakuliah', 'matkul', 'mata kuliah', 'name'],
        'sks' => ['sks', 'bobot', 'kredit'],
        'semester' => ['semester', 'smt', 'sem'],
        'tipe' => ['tipe', 'type', 'jenis', 'kategori', 'sifat'],
        'prodi' => ['prodi', 'program studi', 'program_studi', 'jurusan', 'study program'],
        'dosen_ketua' => ['dosen_ketua', 'dosen ketua', 'ketua', 'dosen ketua pengampu', 'pengampu ketua', 'dosen utama', 'dosen1'],
        'dosen_anggota' => ['dosen_anggota', 'dosen anggota', 'anggota', 'dosen pendamping', 'pengampu anggota', 'dosen kedua', 'dosen2'],
        'jumlah_kelas' => ['jumlah_kelas', 'jumlah kelas', 'jml kelas', 'kelas', 'jml_kls', 'rombel', 'jumlah rombel'],
        'kapasitas_per_kelas' => ['kapasitas_per_kelas', 'kapasitas per kelas', 'kapasitas', 'kapasitas kelas', 'kuota', 'daya tampung'],
        'butuh_lab' => ['butuh_lab', 'butuh lab', 'lab', 'laboratorium', 'perlu lab', 'need_lab'],
    ];

    public function templatePath(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('mata_kuliah');

        $headers = [
            'kode_mk',
            'nama_mk',
            'sks',
            'semester',
            'tipe',
            'prodi',
            'dosen_ketua',
            'dosen_anggota',
            'jumlah_kelas',
            'kapasitas_per_kelas',
            'butuh_lab',
        ];

        $sheet->fromArray([$headers], null, 'A1');

        $sheet->fromArray([
            ['TIF101', 'Algoritma dan Pemrograman', 3, 1, 'Wajib', 'Informatika', 'Budi Santoso', 'Siti Aminah', 2, 40, 'Tidak'],
            ['TIF102', 'Kecerdasan Buatan', 3, 5, 'Pilihan', 'Informatika', 'Budi Santoso', '', 1, 30, 'Ya'],
        ], null, 'A2');

        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $panduan = $spreadsheet->createSheet();
        $panduan->setTitle('panduan');
        $panduan->fromArray([
            ['Kolom', 'Wajib', 'Contoh / Keterangan'],
            ['kode_mk', 'Ya', 'Kode unik, mis. TIF101'],
            ['nama_mk', 'Ya', 'Nama mata kuliah'],
            ['sks', 'Ya', '1-4'],
            ['semester', 'Ya', '1, 3, 5, atau 7'],
            ['tipe', 'Ya', 'Wajib atau Pilihan'],
            ['prodi', 'Ya', 'mis. Informatika'],
            ['dosen_ketua', 'Tidak', 'Nama dosen atau ID. Kosong = tanpa dosen'],
            ['dosen_anggota', 'Tidak', 'Nama dosen atau ID. Harus beda dari ketua'],
            ['jumlah_kelas', 'Tidak', 'Default 1, maks 10'],
            ['kapasitas_per_kelas', 'Tidak', 'Default 40, 10-200'],
            ['butuh_lab', 'Tidak', 'Ya/Tidak, Y/N, 1/0, True/False'],
            [],
            ['Nama header fleksibel: kode, nama mk, smt, jenis, jurusan, ketua, anggota, kelas, kapasitas, lab tetap terbaca.'],
            ['Baris kosong dilewati. Duplikat kode_mk di file: baris terakhir menang.'],
            ['Data cocok by kode_mk: ada = update, belum ada = tambah.'],
        ], null, 'A1');
        $panduan->getColumnDimension('A')->setAutoSize(true);
        $panduan->getColumnDimension('B')->setAutoSize(true);
        $panduan->getColumnDimension('C')->setAutoSize(true);

        $path = tempnam(sys_get_temp_dir(), 'mk_template_').'.xlsx';
        (new Xlsx($spreadsheet))->save($path);

        return $path;
    }

    public function import(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getSheet(0);
        $rows = $sheet->toArray(null, true, true, false);

        if (empty($rows)) {
            return $this->result(0, 0, [], ['File kosong.']);
        }

        $headerMap = $this->mapHeaders(array_shift($rows));
        $missing = array_diff(['kode_mk', 'nama_mk', 'sks', 'semester', 'tipe', 'prodi'], array_keys($headerMap));

        if (!empty($missing)) {
            return $this->result(0, 0, [], [
                'Header wajib hilang: '.implode(', ', $missing).'. Unduh template untuk contoh.',
            ]);
        }

        $created = 0;
        $updated = 0;
        $failed = [];
        $seen = [];

        foreach ($rows as $index => $row) {
            $line = $index + 2;

            if ($this->isEmptyRow($row)) {
                continue;
            }

            $data = $this->extractRow($row, $headerMap);
            $seen[$data['kode_mk'] ?? ''] = $line;

            $errors = $this->validateRow($data);
            if (!empty($errors)) {
                $failed[] = 'Baris '.$line.': '.implode(' ', $errors);
                continue;
            }

            $dosenKetuaId = $this->resolveDosen($data['dosen_ketua'] ?? null, $data['prodi']);
            if (($data['dosen_ketua'] ?? null) && !$dosenKetuaId) {
                $failed[] = 'Baris '.$line.': dosen ketua "'.$data['dosen_ketua'].'" tidak ditemukan dan gagal dibuat.';
                continue;
            }

            $dosenAnggotaId = $this->resolveDosen($data['dosen_anggota'] ?? null, $data['prodi']);
            if (($data['dosen_anggota'] ?? null) && !$dosenAnggotaId) {
                $failed[] = 'Baris '.$line.': dosen anggota "'.$data['dosen_anggota'].'" tidak ditemukan dan gagal dibuat.';
                continue;
            }

            if ($dosenKetuaId && $dosenKetuaId === $dosenAnggotaId) {
                $failed[] = 'Baris '.$line.': dosen anggota harus berbeda dari dosen ketua.';
                continue;
            }

            $payload = [
                'nama_mk' => $data['nama_mk'],
                'sks' => (int) $data['sks'],
                'semester' => (int) $data['semester'],
                'tipe' => $data['tipe'],
                'prodi' => $data['prodi'],
                'dosen_ketua_id' => $dosenKetuaId,
                'dosen_anggota_id' => $dosenAnggotaId,
                'jumlah_kelas' => isset($data['jumlah_kelas']) && $data['jumlah_kelas'] !== '' ? (int) $data['jumlah_kelas'] : 1,
                'kapasitas_per_kelas' => isset($data['kapasitas_per_kelas']) && $data['kapasitas_per_kelas'] !== '' ? (int) $data['kapasitas_per_kelas'] : 40,
                'butuh_lab' => $this->toBool($data['butuh_lab'] ?? null),
                'is_active' => true,
            ];

            $exists = MataKuliahDetail::where('kode_mk', $data['kode_mk'])->exists();
            MataKuliahDetail::updateOrCreate(['kode_mk' => $data['kode_mk']], $payload);

            if ($exists) {
                $updated++;
            } else {
                $created++;
            }
        }

        return $this->result($created, $updated, $seen, $failed);
    }

    protected function result(int $created, int $updated, array $seen, array $failed): array
    {
        return [
            'created' => $created,
            'updated' => $updated,
            'failed' => $failed,
            'total' => $created + $updated + count($failed),
        ];
    }

    protected function mapHeaders(array $headerRow): array
    {
        $map = [];
        foreach ($headerRow as $col => $value) {
            $key = $this->normalize((string) $value);
            if ($key === '') {
                continue;
            }
            foreach ($this->aliases as $field => $names) {
                foreach ($names as $name) {
                    if ($key === $this->normalize($name)) {
                        $map[$field] = $col;
                        break 2;
                    }
                }
            }
        }

        return $map;
    }

    protected function extractRow(array $row, array $headerMap): array
    {
        $data = [];
        foreach ($headerMap as $field => $col) {
            $value = $row[$col] ?? null;
            $data[$field] = is_string($value) ? trim($value) : $value;
        }

        if (isset($data['tipe'])) {
            $tipe = ucfirst(strtolower(trim((string) $data['tipe'])));
            $data['tipe'] = $tipe === 'Pilihan' ? 'Pilihan' : ($tipe === 'Wajib' ? 'Wajib' : $data['tipe']);
        }

        return $data;
    }

    protected function validateRow(array $data): array
    {
        $errors = [];

        if (empty($data['kode_mk'])) {
            $errors[] = 'kode_mk wajib diisi.';
        } elseif (strlen((string) $data['kode_mk']) > 20) {
            $errors[] = 'kode_mk maksimal 20 karakter.';
        }

        if (empty($data['nama_mk'])) {
            $errors[] = 'nama_mk wajib diisi.';
        }

        if (!is_numeric($data['sks'] ?? null) || (int) $data['sks'] < 1 || (int) $data['sks'] > 4) {
            $errors[] = 'sks harus 1-4.';
        }

        if (!in_array((int) ($data['semester'] ?? 0), [1, 3, 5, 7], true)) {
            $errors[] = 'semester harus 1, 3, 5, atau 7.';
        }

        if (!in_array($data['tipe'] ?? null, ['Wajib', 'Pilihan'], true)) {
            $errors[] = 'tipe harus Wajib atau Pilihan.';
        }

        if (empty($data['prodi'])) {
            $errors[] = 'prodi wajib diisi.';
        }

        if (isset($data['jumlah_kelas']) && $data['jumlah_kelas'] !== '' && ((int) $data['jumlah_kelas'] < 1 || (int) $data['jumlah_kelas'] > 10)) {
            $errors[] = 'jumlah_kelas harus 1-10.';
        }

        if (isset($data['kapasitas_per_kelas']) && $data['kapasitas_per_kelas'] !== '' && ((int) $data['kapasitas_per_kelas'] < 10 || (int) $data['kapasitas_per_kelas'] > 200)) {
            $errors[] = 'kapasitas_per_kelas harus 10-200.';
        }

        return $errors;
    }

    protected function resolveDosen($value, ?string $prodi): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = trim((string) $value);

        if (ctype_digit($value)) {
            $dosen = Dosen::find((int) $value);
            if ($dosen) {
                return $dosen->id;
            }
        }

        $dosen = Dosen::where('nama', $value)->first()
            ?? Dosen::where('nama', 'like', '%'.$value.'%')->first();

        if ($dosen) {
            return $dosen->id;
        }

        $created = Dosen::create([
            'nama' => $value,
            'jabatan' => 'Ketua & Anggota',
            'prodi' => $prodi,
            'is_active' => true,
        ]);

        return $created->id;
    }

    protected function toBool($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $normalized = strtolower(trim((string) $value));

        return in_array($normalized, ['1', 'ya', 'y', 'yes', 'true', 'lab', 'butuh', 'perlu'], true);
    }

    protected function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if ($value !== null && trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    protected function normalize(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[_-]+/', ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value);

        return $value;
    }
}
