@extends('layouts.app')

@section('title', 'APJAD - Penjadwalan Otomatis')

@section('content')
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="sidebar-top">
            <div class="sidebar-brand">
                <h2>APJAD</h2>
                @if(Auth::user()->role === 'Fakultas')
                    <span class="sidebar-role-badge" style="font-size:10px; color:#94a3b8; text-transform:uppercase; letter-spacing:1px; margin-top:2px;">Admin Fakultas</span>
                @else
                    <span class="sidebar-role-badge" style="font-size:10px; color:#94a3b8; text-transform:uppercase; letter-spacing:1px; margin-top:2px;">Super Admin</span>
                @endif
            </div>
            <nav class="sidebar-nav">
                @if(Auth::user()->role === 'Super Admin')
                    <a href="{{ route('dashboard') }}" class="sidebar-link">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('manajemen-akun') }}" class="sidebar-link">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>Manajemen Akun</span>
                    </a>
                    <a href="{{ route('jadwal-kuliah.index') }}" class="sidebar-link">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                            <path d="M3 9h18M9 3v18"></path>
                        </svg>
                        <span>Jadwal Kuliah</span>
                    </a>
                    <a href="{{ route('penjadwalan.index') }}" class="sidebar-link active">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span>Penjadwalan</span>
                    </a>
                @elseif(Auth::user()->role === 'Fakultas')
                    <a href="{{ route('fakultas.dashboard') }}" class="sidebar-link">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('fakultas.dashboard') }}#section-prodi" class="sidebar-link">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                            <path d="M2 17l10 5 10-5"></path>
                            <path d="M2 12l10 5 10-5"></path>
                        </svg>
                        <span>Program Studi</span>
                    </a>
                    <a href="{{ route('fakultas.dashboard') }}#section-jadwal" class="sidebar-link">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"></path>
                        </svg>
                        <span>Rekap Jadwal</span>
                    </a>
                    <a href="{{ route('jadwal-kuliah.index') }}" class="sidebar-link">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                            <path d="M3 9h18M9 3v18"></path>
                        </svg>
                        <span>Data Mata Kuliah</span>
                    </a>
                    <a href="{{ route('penjadwalan.index') }}" class="sidebar-link active">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span>Penjadwalan</span>
                    </a>
                @endif
            </nav>
        </div>

        <div class="sidebar-bottom">
            <button type="button" class="sidebar-link" onclick="openModal('modalEditProfile')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span>Edit Profile</span>
            </button>
            <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            <a href="{{ route('logout') }}" class="sidebar-link" onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <main class="admin-main">
        <header class="admin-header">
            <div class="user-badge">
                <span>{{ Auth::user()->role }}, <span class="user-name">{{ Auth::user()->nama }}</span></span>
            </div>
        </header>

        <section class="admin-content">
            @if (session('flash_success'))
                <div class="flash-alert success">
                    <span>{{ session('flash_success') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;">X</button>
                </div>
            @endif

            @if (session('flash_error'))
                <div class="flash-alert error">
                    <span>{{ session('flash_error') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;">X</button>
                </div>
            @endif

            @if ($errors->any())
                <div class="flash-alert error">
                    <span>
                        @foreach ($errors->all() as $e)
                            {{ $e }}@if (! $loop->last), @endif
                        @endforeach
                    </span>
                </div>
            @endif

            <div class="page-title">
                <h1>Penjadwalan Otomatis</h1>
            </div>

            <div class="tabs-container">
                <div class="tabs-header">
                    <button type="button" class="tab-btn active" data-tab="tab-input" onclick="showTab('tab-input', this)">Input Mata Kuliah</button>
                    <button type="button" class="tab-btn" data-tab="tab-generate" onclick="showTab('tab-generate', this)">Generate Jadwal</button>
                    @foreach ($semesterGanjil as $sem)
                        <button type="button" class="tab-btn" data-tab="tab-sem-{{ $sem }}" onclick="showTab('tab-sem-{{ $sem }}', this)">
                            Semester {{ $sem }}
                        </button>
                    @endforeach
                </div>

                {{-- ============ INPUT MATA KULIAH ============ --}}
                <div class="tab-content active" id="tab-input">
                    <div class="tab-header">
                        <h2>Data Mata Kuliah</h2>
                        <div style="display:flex; gap:10px; flex-wrap:wrap;">
                            <a href="{{ route('penjadwalan.template') }}" class="btn-add" style="text-decoration:none; background:#0ea5e9;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                                Template Excel
                            </a>
                            <button type="button" class="btn-add" onclick="openModal('modalImportMk')" style="background:#16a34a;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                Import Excel
                            </button>
                            <button type="button" class="btn-add" onclick="openTambahMk()">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                Tambah MK
                            </button>
                        </div>
                    </div>

                    @if (session('import_gagal'))
                        <div class="flash-alert error" style="margin-bottom:16px;">
                            <div>
                                <strong>Sebagian baris gagal:</strong>
                                <ul style="margin:8px 0 0 18px;">
                                    @foreach (session('import_gagal') as $gagal)
                                        <li>{{ $gagal }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Kode MK</th>
                                <th>Nama MK</th>
                                <th>SKS</th>
                                <th>Semester</th>
                                <th>Tipe</th>
                                <th>Dosen Ketua</th>
                                <th>Dosen Anggota</th>
                                <th>Jumlah Kelas</th>
                                <th style="text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mataKuliah as $sem => $listMk)
                                @foreach ($listMk as $mk)
                                    <tr>
                                        <td><strong>{{ $mk->kode_mk }}</strong></td>
                                        <td>{{ $mk->nama_mk }}</td>
                                        <td>{{ $mk->sks }}</td>
                                        <td>{{ $mk->semester }}</td>
                                        <td>{{ $mk->tipe }}</td>
                                        <td>{{ $mk->dosenKetua->nama ?? '-' }}</td>
                                        <td>{{ $mk->dosenAnggota->nama ?? '-' }}</td>
                                        <td>{{ $mk->jumlah_kelas }}</td>
                                        <td style="text-align:center;">
                                            <div class="action-buttons-group" style="justify-content:center;">
                                                <button type="button" class="btn-action" title="Edit"
                                                    onclick='openEditMk(@json($mk))'>
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                        <path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4z"></path>
                                                    </svg>
                                                </button>
                                                <form action="{{ route('penjadwalan.destroy', $mk->id) }}" method="POST" style="display:inline;"
                                                    onsubmit="return konfirmasiHapus(event, '{{ $mk->kode_mk }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-action delete-btn" title="Hapus">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <polyline points="3 6 5 6 21 6"></polyline>
                                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="9" style="text-align:center; padding:24px;">Belum ada mata kuliah.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- ============ GENERATE ============ --}}
                <div class="tab-content" id="tab-generate">
                    <div class="tab-header">
                        <h2>Generate Jadwal</h2>
                    </div>

                    <form action="{{ route('penjadwalan.generate') }}" method="POST" id="formGenerate">
                        @csrf
                        <div class="modal-form-row">
                            <label>Semester yang dijadwalkan</label>
                            <div class="modal-input-container" style="display:flex; gap:16px; flex-wrap:wrap;">
                                @foreach ($semesterGanjil as $sem)
                                    <label style="display:flex; align-items:center; gap:6px; font-weight:400;">
                                        <input type="checkbox" name="semester[]" value="{{ $sem }}"
                                            style="width:auto;" @checked($sem === 1)>
                                        Semester {{ $sem }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="modal-form-row">
                            <label for="genProdi">Prodi</label>
                            <div class="modal-input-container">
                                <input type="text" id="genProdi" name="prodi" value="Informatika" required>
                            </div>
                        </div>

                        <div class="modal-form-row">
                            <label>Hari tersedia</label>
                            <div class="modal-input-container" style="display:flex; gap:16px; flex-wrap:wrap;">
                                @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                                    <label style="display:flex; align-items:center; gap:6px; font-weight:400;">
                                        <input type="checkbox" name="hari[]" value="{{ $hari }}" style="width:auto;" checked>
                                        {{ $hari }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="modal-form-row">
                            <label>Jam tersedia</label>
                            <div class="modal-input-container" style="display:flex; gap:16px; flex-wrap:wrap;">
                                @foreach (['07:30', '10:00', '13:00', '14:50'] as $jam)
                                    <label style="display:flex; align-items:center; gap:6px; font-weight:400;">
                                        <input type="checkbox" name="jam_mulai[]" value="{{ $jam }}" style="width:auto;" checked>
                                        {{ $jam }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="modal-footer-buttons">
                            <button type="submit" class="btn-modal-submit">Generate Sekarang</button>
                        </div>
                    </form>

                    <div style="margin-top:16px; padding:14px; background:#f5f8fc; border-radius:8px; font-size:13px; line-height:1.7;">
                        <strong>Aturan yang dijalankan:</strong>
                        <ul style="margin:8px 0 0 18px;">
                            <li>Bentrok kelas tidak boleh &mdash; satu prodi+semester tidak punya 2 MK pada waktu sama.</li>
                            <li>Dosen ketua tidak boleh bentrok di MK mana pun; kelas paralel dikRotate antar ketua/anggota.</li>
                            <li>Jadwal aktif yang sudah ada ikut dihitung: ruangan, dosen, dan cohort tidak ditimpa.</li>
                            <li>Hari/jam yang tidak dicentang tidak dipakai generate.</li>
                            <li>Slot berurutan mulai 07:30, lalu 10:00, 13:00, 14:50. Durasi = SKS x 50 menit.</li>
                            <li>MK Pilihan dibagi ke {{ $maksPilihan }} kelompok slot. MK satu kelompok saling bentrok; mahasiswa ambil 1 per kelompok (maks {{ $maksPilihan }} MK).</li>
                            <li>Ruang aktif saat ini:
                                @foreach ($ruanganAktif as $r)
                                    <strong>{{ $r->kode }}</strong>@if (! $loop->last), @endif
                                @endforeach
                            </li>
                        </ul>
                    </div>

                    @if (! empty($logGenerate))
                        <div class="tab-header" style="margin-top:24px;">
                            <h2>Log Generate Terakhir</h2>
                        </div>
                        @foreach ($logGenerate as $hasil)
                            <div style="margin-bottom:18px;">
                                <p style="font-weight:600; margin-bottom:6px;">
                                    Semester {{ $hasil['semester'] }} &rarr; {{ $hasil['jumlah'] }} jadwal
                                    @if (! empty($hasil['slot_pilihan']))
                                        <span style="font-weight:400; color:#555;">
                                            (Kuota MK Pilihan: {{ $hasil['kuota_pilihan'] }} &mdash;
                                            {{ implode(' | ', $hasil['slot_pilihan']) }})
                                        </span>
                                    @endif
                                </p>
                                <table class="data-table">
                                    <tbody>
                                        @foreach ($hasil['log'] as $baris)
                                            <tr>
                                                <td style="font-family:monospace; font-size:12px;">{{ $baris }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @foreach ($hasil['peringatan'] as $p)
                                    <p style="color:#b26a00; font-size:13px; margin-top:6px;">Peringatan: {{ $p }}</p>
                                @endforeach
                            </div>
                        @endforeach
                    @endif
                </div>

                {{-- ============ OUTPUT PER SEMESTER ============ --}}
                @foreach ($semesterGanjil as $sem)
                    <div class="tab-content" id="tab-sem-{{ $sem }}">
                        <div class="tab-header">
                            <h2>Jadwal Semester {{ $sem }}</h2>
                        </div>

                        @php
                            $baris = $jadwal[$sem] ?? collect();
                            $perHari = $baris->groupBy('hari');
                        @endphp

                        @if ($baris->isEmpty())
                            <p style="color:#666; padding:18px 0;">
                                Belum ada jadwal untuk semester {{ $sem }}. Gunakan tab <strong>Generate Jadwal</strong>.
                            </p>
                        @else
                            @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                                @if (($perHari[$hari] ?? collect())->isNotEmpty())
                                    <h3 style="margin:18px 0 8px; font-size:15px; color:#1e3a5f;">{{ $hari }}</h3>
                                    <table class="data-table">
                                        <thead>
                                            <tr>
                                                <th>Jam</th>
                                                <th>Kode MK</th>
                                                <th>Nama MK</th>
                                                <th>SKS</th>
                                                <th>Kelas</th>
                                                <th>Dosen</th>
                                                <th>Ruangan</th>
                                                <th>Tipe</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($perHari[$hari] as $j)
                                                <tr>
                                                    <td style="white-space:nowrap;">{{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}</td>
                                                    <td><strong>{{ $j->kode_mk }}</strong></td>
                                                    <td>{{ $j->nama_mk }}</td>
                                                    <td>{{ $j->sks }}</td>
                                                    <td>{{ $j->kode_rombel }}</td>
                                                    <td>{{ $j->nama_dosen ?? '-' }}</td>
                                                    <td>{{ $j->kode_ruangan }}</td>
                                                    <td>{{ $j->tipe }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            @endforeach
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    </main>
</div>

{{-- ============ MODAL IMPORT MK ============ --}}
<div class="modal-backdrop" id="modalImportMk">
    <div class="modal-window">
        <div class="modal-header-blue">
            <h3>Import Mata Kuliah dari Excel</h3>
        </div>
        <div class="modal-body">
            <form action="{{ route('penjadwalan.import') }}" method="POST" enctype="multipart/form-data" class="modal-form">
                @csrf
                <div class="modal-form-row">
                    <label for="importFile">File Excel</label>
                    <div class="modal-input-container">
                        <input type="file" id="importFile" name="file" accept=".xlsx,.xls,.csv" required>
                    </div>
                </div>
                <p style="font-size:13px; color:#475569; line-height:1.6;">
                    Header fleksibel: <strong>kode / kode mk</strong>, <strong>nama mk</strong>, <strong>sks</strong>,
                    <strong>smt / semester</strong>, <strong>jenis / tipe</strong>, <strong>jurusan / prodi</strong>,
                    <strong>ketua / dosen ketua</strong>, <strong>anggota / dosen anggota</strong>,
                    <strong>kelas / jumlah kelas</strong>, <strong>kapasitas</strong>, <strong>lab</strong>.
                    Dosen baru otomatis dibuat. Data cocok by <strong>kode_mk</strong>.
                </p>
                <div class="modal-footer-buttons">
                    <button type="button" class="btn-modal-cancel" onclick="closeModal('modalImportMk')">Batal</button>
                    <button type="submit" class="btn-modal-submit">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============ MODAL INPUT/EDIT MK ============ --}}
<div class="modal-backdrop" id="modalMk">
    <div class="modal-window">
        <div class="modal-header-blue">
            <h3 id="modalMkTitle">Tambah Mata Kuliah</h3>
        </div>
        <div class="modal-body">
            <form id="formMk" method="POST" class="modal-form">
                @csrf
                <input type="hidden" name="_method" id="mkMethod" value="">

                <div class="modal-form-row">
                    <label for="mkKode">Kode MK</label>
                    <div class="modal-input-container">
                        <input type="text" id="mkKode" name="kode_mk" required>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="mkNama">Nama MK</label>
                    <div class="modal-input-container">
                        <input type="text" id="mkNama" name="nama_mk" required>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="mkSks">SKS</label>
                    <div class="modal-input-container">
                        <input type="number" id="mkSks" name="sks" min="1" max="4" value="3" required>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="mkSemester">Semester</label>
                    <div class="modal-input-container">
                        <select id="mkSemester" name="semester" required>
                            @foreach ($semesterGanjil as $s)
                                <option value="{{ $s }}">Semester {{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="mkTipe">Tipe</label>
                    <div class="modal-input-container">
                        <select id="mkTipe" name="tipe" required>
                            <option value="Wajib">Wajib</option>
                            <option value="Pilihan">Pilihan</option>
                        </select>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="mkProdi">Prodi</label>
                    <div class="modal-input-container">
                        <input type="text" id="mkProdi" name="prodi" value="Teknik Informatika" required>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="mkKetua">Dosen Ketua</label>
                    <div class="modal-input-container">
                        <select id="mkKetua" name="dosen_ketua_id">
                            <option value="">- Tidak ada -</option>
                            @foreach ($dosens as $d)
                                <option value="{{ $d->id }}">{{ $d->nama }} ({{ $d->jabatan }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="mkAnggota">Dosen Anggota</label>
                    <div class="modal-input-container">
                        <select id="mkAnggota" name="dosen_anggota_id">
                            <option value="">- Tidak ada -</option>
                            @foreach ($dosens as $d)
                                <option value="{{ $d->id }}">{{ $d->nama }} ({{ $d->jabatan }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="mkKelas">Jumlah Kelas</label>
                    <div class="modal-input-container">
                        <input type="number" id="mkKelas" name="jumlah_kelas" min="1" max="10" value="1" required>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="mkKapasitas">Kapasitas per Kelas</label>
                    <div class="modal-input-container">
                        <input type="number" id="mkKapasitas" name="kapasitas_per_kelas" min="1" max="200" value="30" required>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="mkLab">Perlu Laboratorium</label>
                    <div class="modal-input-container">
                        <select id="mkLab" name="butuh_lab">
                            <option value="0">Tidak</option>
                            <option value="1">Ya</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer-buttons">
                    <button type="button" class="btn-modal-cancel" onclick="closeModal('modalMk')">Batal</button>
                    <button type="submit" class="btn-modal-submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============ MODAL EDIT PROFILE ============ --}}
<div class="modal-backdrop" id="modalEditProfile">
    <div class="modal-window">
        <div class="modal-header-blue">
            <h3>Edit Profile</h3>
        </div>
        <div class="modal-body">
            <form action="{{ route('profile.update') }}" method="POST" class="modal-form">
                @csrf
                <div class="modal-form-row">
                    <label for="profile_nama">Nama</label>
                    <div class="modal-input-container">
                        <input type="text" id="profile_nama" name="nama" value="{{ Auth::user()->nama }}" required>
                    </div>
                </div>
                <div class="modal-form-row">
                    <label for="profile_email">E-mail</label>
                    <div class="modal-input-container">
                        <input type="email" id="profile_email" name="email" value="{{ Auth::user()->email }}" required>
                    </div>
                </div>
                <div class="modal-form-row">
                    <label for="profile_password_lama">Password lama</label>
                    <div class="modal-input-container">
                        <input type="password" id="profile_password_lama" name="password_lama" placeholder="Isi jika ingin ganti password">
                    </div>
                </div>
                <div class="modal-form-row">
                    <label for="profile_password_baru">Password Baru</label>
                    <div class="modal-input-container">
                        <input type="password" id="profile_password_baru" name="password_baru" placeholder="Kosongkan jika tidak diubah">
                    </div>
                </div>
                <div class="modal-form-row">
                    <label for="profile_password_baru_confirmation">Konfirmasi Password Baru</label>
                    <div class="modal-input-container">
                        <input type="password" id="profile_password_baru_confirmation" name="password_baru_confirmation" placeholder="Ulangi password baru">
                    </div>
                </div>
                <div class="modal-footer-buttons">
                    <button type="button" class="btn-modal-cancel" onclick="closeModal('modalEditProfile')">Batal</button>
                    <button type="submit" class="btn-modal-submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const MK_FORM_ACTION_STORE = "{{ route('penjadwalan.store') }}";
const MK_FORM_ACTION_UPDATE = "{{ route('penjadwalan.update', ['mataKuliah' => '__ID__']) }}";

function showTab(id, btn) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    document.getElementById(id).classList.add('active');
    if (btn) btn.classList.add('active');
}

// openModal, closeModal dan penutupan backdrop sudah disediakan assets/js/app.js

function openTambahMk() {
    const form = document.getElementById('formMk');
    form.action = MK_FORM_ACTION_STORE;
    form.reset();
    document.getElementById('mkMethod').value = '';
    document.getElementById('modalMkTitle').textContent = 'Tambah Mata Kuliah';
    document.getElementById('mkSemester').value = '1';
    document.getElementById('mkSks').value = '3';
    document.getElementById('mkKelas').value = '1';
    document.getElementById('mkKapasitas').value = '30';
    document.getElementById('mkProdi').value = 'Teknik Informatika';
    openModal('modalMk');
}

function openEditMk(mk) {
    const form = document.getElementById('formMk');
    form.action = MK_FORM_ACTION_UPDATE.replace('__ID__', mk.id);
    document.getElementById('mkMethod').value = 'PUT';
    document.getElementById('modalMkTitle').textContent = 'Edit ' + mk.kode_mk;

    document.getElementById('mkKode').value = mk.kode_mk;
    document.getElementById('mkNama').value = mk.nama_mk;
    document.getElementById('mkSks').value = mk.sks;
    document.getElementById('mkSemester').value = mk.semester;
    document.getElementById('mkTipe').value = mk.tipe;
    document.getElementById('mkProdi').value = mk.prodi;
    document.getElementById('mkKetua').value = mk.dosen_ketua_id || '';
    document.getElementById('mkAnggota').value = mk.dosen_anggota_id || '';
    document.getElementById('mkKelas').value = mk.jumlah_kelas;
    document.getElementById('mkKapasitas').value = mk.kapasitas_per_kelas;
    document.getElementById('mkLab').value = mk.butuh_lab ? '1' : '0';

    openModal('modalMk');
}

function konfirmasiHapus(event, kode) {
    if (!confirm('Yakin ingin menghapus mata kuliah ' + kode + '?')) {
        event.preventDefault();
        return false;
    }
    return true;
}
</script>
@endpush
