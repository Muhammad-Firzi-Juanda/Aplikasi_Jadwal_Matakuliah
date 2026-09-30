<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\MataKuliahDetail;
use App\Models\Ruangan;
use App\Services\MataKuliahImportService;
use App\Services\SchedulerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PenjadwalanController extends Controller
{
    public const SEMESTER_GANJIL = [1, 3, 5, 7];

    public function index()
    {
        $mataKuliah = MataKuliahDetail::with(['dosenKetua', 'dosenAnggota'])
            ->orderBy('semester')
            ->orderBy('kode_mk')
            ->get()
            ->groupBy('semester');

        $jadwal = DB::table('jadwals')
            ->join('rombels', 'rombels.id', '=', 'jadwals.rombel_id')
            ->join('mata_kuliah_details', 'mata_kuliah_details.id', '=', 'rombels.mata_kuliah_detail_id')
            ->join('ruangans', 'ruangans.id', '=', 'jadwals.ruangan_id')
            ->leftJoin('dosens', 'dosens.id', '=', 'jadwals.dosen_id')
            ->where('jadwals.is_active', 1)
            ->where('rombels.is_active', 1)
            ->select([
                'mata_kuliah_details.semester',
                'mata_kuliah_details.kode_mk',
                'mata_kuliah_details.nama_mk',
                'mata_kuliah_details.sks',
                'mata_kuliah_details.tipe',
                'mata_kuliah_details.prodi',
                'rombels.kode_rombel',
                'rombels.nomor_rombel',
                'jadwals.hari',
                'jadwals.jam_mulai',
                'jadwals.jam_selesai',
                'ruangans.kode as kode_ruangan',
                'ruangans.nama as nama_ruangan',
                'dosens.nama as nama_dosen',
            ])
            ->orderBy('mata_kuliah_details.semester')
            ->orderBy('jadwals.hari')
            ->orderBy('jadwals.jam_mulai')
            ->orderBy('rombels.nomor_rombel')
            ->get()
            ->groupBy('semester');

        return view('penjadwalan.index', [
            'mataKuliah' => $mataKuliah,
            'jadwal' => $jadwal,
            'dosens' => Dosen::orderBy('nama')->get(),
            'ruangan' => Ruangan::orderBy('kode')->get(),
            'ruanganAktif' => Ruangan::where('is_active', 1)->orderBy('kode')->get(),
            'semesterGanjil' => self::SEMESTER_GANJIL,
            'maksPilihan' => SchedulerService::MAKS_PILIHAN,
            'logGenerate' => session('log_generate', []),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);

        $mk = MataKuliahDetail::create($data);

        return $this->kembali('Mata kuliah ' . $mk->kode_mk . ' berhasil ditambahkan.');
    }

    public function update(Request $request, MataKuliahDetail $mataKuliah)
    {
        $data = $this->validasi($request, $mataKuliah->id);

        $mataKuliah->update($data);

        return $this->kembali('Mata kuliah ' . $mataKuliah->kode_mk . ' berhasil diperbarui.');
    }

    public function destroy(MataKuliahDetail $mataKuliah)
    {
        $kode = $mataKuliah->kode_mk;

        DB::table('jadwals')
            ->whereIn('rombel_id', function ($q) use ($mataKuliah) {
                $q->select('id')->from('rombels')
                    ->where('mata_kuliah_detail_id', $mataKuliah->id);
            })
            ->delete();

        $mataKuliah->rombels()->delete();
        $mataKuliah->delete();

        return $this->kembali('Mata kuliah ' . $kode . ' berhasil dihapus.');
    }

    public function generate(Request $request, SchedulerService $scheduler)
    {
        $request->validate([
            'semester' => ['required', 'array', 'min:1'],
            'semester.*' => [Rule::in(self::SEMESTER_GANJIL)],
            'prodi' => ['nullable', 'string', 'max:100'],
            'hari' => ['nullable', 'array'],
            'hari.*' => [Rule::in(SchedulerService::HARI)],
            'jam_mulai' => ['nullable', 'array'],
            'jam_mulai.*' => ['date_format:H:i'],
            'meta' => ['nullable', 'array'],
        ]);

        $semesters = $request->input('semester');
        $prodi = $request->input('prodi') ?: 'Informatika';
        $options = array_filter([
            'hari' => $request->input('hari'),
            'jam_mulai' => $request->input('jam_mulai'),
            'meta' => $request->input('meta'),
        ]);

        $log = [];
        foreach ($semesters as $sem) {
            $hasil = $scheduler->generate((int) $sem, $prodi, $options);

            $log[] = [
                'semester' => $hasil['semester'],
                'jumlah' => $hasil['jumlah_jadwal'],
                'log' => $hasil['log'],
                'peringatan' => $hasil['peringatan'],
                'slot_pilihan' => array_column($hasil['slot_pilihan'], 'label'),
                'kuota_pilihan' => $hasil['kuota_pilihan'],
            ];
        }

        return redirect()
            ->route('penjadwalan.index')
            ->with('log_generate', $log)
            ->with('flash_success', 'Jadwal berhasil digenerate untuk semester ' . implode(', ', $semesters) . '.');
    }

    public function downloadTemplate(MataKuliahImportService $importer): BinaryFileResponse
    {
        $path = $importer->templatePath();

        return response()->download($path, 'template-mata-kuliah.xlsx')->deleteFileAfterSend(true);
    }

    public function import(Request $request, MataKuliahImportService $importer)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ], [
            'file.required' => 'Pilih file Excel dulu.',
            'file.mimes' => 'File harus .xlsx, .xls, atau .csv.',
            'file.max' => 'Ukuran file maksimal 5MB.',
        ]);

        $path = $request->file('file')->getRealPath();
        $hasil = $importer->import($path);

        if ($hasil['total'] === 0) {
            return redirect()->back()->with('flash_error', 'File kosong atau tidak ada baris valid.');
        }

        if (!empty($hasil['failed'])) {
            return redirect()->back()
                ->with('flash_error', 'Import selesai dengan galat: '.$hasil['failed'][0].(count($hasil['failed']) > 1 ? ' (+'.(count($hasil['failed']) - 1).' galat lain)' : ''))
                ->with('import_gagal', $hasil['failed'])
                ->with('flash_success', "Import: {$hasil['created']} tambah, {$hasil['updated']} update.");
        }

        return redirect()->back()->with('flash_success', "Import sukses: {$hasil['created']} tambah, {$hasil['updated']} update.");
    }

    protected function validasi(Request $request, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'kode_mk' => [
                'required', 'string', 'max:20',
                Rule::unique('mata_kuliah_details', 'kode_mk')->ignore($ignoreId),
            ],
            'nama_mk' => ['required', 'string', 'max:150'],
            'sks' => ['required', 'integer', 'min:1', 'max:4'],
            'semester' => ['required', 'integer', Rule::in(self::SEMESTER_GANJIL)],
            'tipe' => ['required', Rule::in(['Wajib', 'Pilihan'])],
            'prodi' => ['required', 'string', 'max:100'],
            'dosen_ketua_id' => ['nullable', 'integer', 'exists:dosens,id'],
            'dosen_anggota_id' => ['nullable', 'integer', 'exists:dosens,id', 'different:dosen_ketua_id'],
                'jumlah_kelas' => ['required', 'integer', 'min:1', 'max:10'],
                'kapasitas_per_kelas' => ['required', 'integer', 'min:10', 'max:200'],
                'butuh_lab' => ['nullable', 'boolean'],
        ], [
            'kode_mk.unique' => 'Kode MK sudah dipakai mata kuliah lain.',
            'kode_mk.required' => 'Kode MK wajib diisi.',
            'nama_mk.required' => 'Nama MK wajib diisi.',
            'semester.in' => 'Semester harus ganjil: 1, 3, 5, atau 7.',
            'sks.max' => 'SKS maksimal 4.',
            'tipe.in' => 'Tipe harus Wajib atau Pilihan.',
            'dosen_ketua_id.exists' => 'Dosen ketua tidak ditemukan.',
            'dosen_anggota_id.exists' => 'Dosen anggota tidak ditemukan.',
            'dosen_anggota_id.different' => 'Dosen anggota harus berbeda dari dosen ketua.',
            'jumlah_kelas.max' => 'Jumlah kelas maksimal 10.',
            'jumlah_kelas.min' => 'Jumlah kelas minimal 1.',
            'kapasitas_per_kelas.min' => 'Kapasitas per kelas minimal 10.',
            'kapasitas_per_kelas.max' => 'Kapasitas per kelas maksimal 200.',
            'nama_mk.max' => 'Nama MK maksimal 150 karakter.',
            'kode_mk.max' => 'Kode MK maksimal 20 karakter.',
            'dosen_ketua_id.integer' => 'Dosen ketua tidak valid.',
            'dosen_anggota_id.integer' => 'Dosen anggota tidak valid.',
            'sks.min' => 'SKS minimal 1.',
            'sks.integer' => 'SKS harus berupa angka.',
            'sks.required' => 'SKS wajib diisi.',
            'prodi.required' => 'Program studi wajib diisi.',
            'prodi.max' => 'Program studi maksimal 100 karakter.',
            'semester.required' => 'Semester wajib diisi.',
            'semester.integer' => 'Semester harus berupa angka.',
            'tipe.required' => 'Tipe wajib diisi.',
            'jumlah_kelas.required' => 'Jumlah kelas wajib diisi.',
            'jumlah_kelas.integer' => 'Jumlah kelas harus berupa angka.',
            'kapasitas_per_kelas.required' => 'Kapasitas per kelas wajib diisi.',
            'kapasitas_per_kelas.integer' => 'Kapasitas per kelas harus berupa angka.',
        ], [
            'kode_mk' => 'kode MK',
            'nama_mk' => 'nama MK',
            'sks' => 'SKS',
            'semester' => 'semester',
            'tipe' => 'tipe',
            'prodi' => 'prodi',
            'dosen_ketua_id' => 'dosen ketua',
            'dosen_anggota_id' => 'dosen anggota',
            'jumlah_kelas' => 'jumlah kelas',
            'kapasitas_per_kelas' => 'kapasitas per kelas',
            'butuh_lab' => 'kebutuhan laboratorium',
        ]);

        // Field nullable yang tidak dikirim tidak muncul di hasil validasi.
        return [
            'kode_mk' => trim($validated['kode_mk']),
            'nama_mk' => trim($validated['nama_mk']),
            'sks' => $validated['sks'],
            'semester' => $validated['semester'],
            'tipe' => $validated['tipe'],
            'prodi' => trim($validated['prodi']),
            'dosen_ketua_id' => $validated['dosen_ketua_id'] ?? null,
            'dosen_anggota_id' => $validated['dosen_anggota_id'] ?? null,
            'jumlah_kelas' => $validated['jumlah_kelas'],
            'kapasitas_per_kelas' => $validated['kapasitas_per_kelas'],
            'butuh_lab' => $request->boolean('butuh_lab'),
        ];
    }

    protected function kembali(string $pesan)
    {
        return redirect()
            ->route('penjadwalan.index')
            ->with('flash_success', $pesan);
    }
}
