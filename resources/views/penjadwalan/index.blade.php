@extends('layouts.app')

@section('title', 'APJAD - Penjadwalan Otomatis')

@push('styles')
<style>
/* ==========================================================================
   Tab Generate Jadwal - Form Layout & Responsiveness
   ========================================================================== */
.generate-form {
    display: flex;
    flex-direction: column;
    gap: 22px; /* Padding/gap vertikal antar baris seragam 20-24px */
}

.generate-form-row {
    display: grid;
    grid-template-columns: 190px 1fr; /* Kolom label tetap 180-200px, kolom input 1fr */
    gap: 20px;
    align-items: start; /* Rata atas */
}

.generate-form-label {
    white-space: nowrap; /* Label nowrap */
    color: var(--primary-blue, #0860be);
    font-size: 0.95rem; /* Tidak lebih besar dari teks input */
    font-weight: 700;
    line-height: 1.5;
    padding-top: 3px;
    user-select: none;
}

.generate-form-label.label-input {
    padding-top: 10px; /* Sejajar dengan teks di dalam input Prodi */
}

/* Radio Pilihan Periode */
.generate-radio-group {
    display: flex;
    flex-wrap: wrap;
    gap: 12px 28px; /* Satu baris horizontal, gap 12px 28px */
    align-items: center;
    min-height: 28px;
}

.generate-radio-item {
    display: inline-flex;
    align-items: center;
    gap: 8px; /* Radio dan teks sejajar vertikal */
    cursor: pointer;
    white-space: nowrap; /* Setiap opsi white-space nowrap */
    font-weight: 600;
    font-size: 0.95rem;
    color: var(--primary-blue, #0860be);
    user-select: none;
    transition: var(--transition, all 0.2s ease);
}

.generate-radio-item:hover {
    color: var(--primary-blue-dark, #064b96);
}

.generate-radio-item input[type="radio"] {
    width: 17px;
    height: 17px;
    margin: 0;
    cursor: pointer;
    accent-color: var(--primary-blue, #0860be);
    flex-shrink: 0;
}

/* Checkbox Grid (Semester spesifik, Hari tersedia, Jam tersedia) */
.generate-checkbox-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 10px 24px; /* Gap konsisten 10px 24px */
    align-items: center;
}

.generate-check-item {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    white-space: nowrap;
    font-size: 0.95rem;
    font-weight: 400;
    color: var(--text-main, #0f172a);
    user-select: none;
    transition: var(--transition, all 0.2s ease);
}

.generate-check-item:hover {
    color: var(--primary-blue, #0860be);
}

.generate-check-item input[type="checkbox"] {
    width: 17px;
    height: 17px;
    margin: 0;
    cursor: pointer;
    accent-color: var(--primary-blue, #0860be);
    flex-shrink: 0;
}

/* Input Prodi */
.generate-input-wrap {
    width: 100%;
}

.generate-input-wrap input {
    width: 100%;
    padding: 10px 16px;
    border: 1.5px solid var(--border-color, #e2e8f0);
    border-radius: var(--radius-md, 10px);
    font-size: 0.95rem;
    font-family: var(--font-sans, inherit);
    outline: none;
    background: #ffffff;
    color: var(--text-main, #0f172a);
    transition: var(--transition, all 0.2s ease);
}

.generate-input-wrap input:focus {
    border-color: var(--primary-blue, #0860be);
    box-shadow: 0 0 0 3px rgba(8, 96, 190, 0.12);
}

/* Tombol Generate Sekarang */
.generate-form-actions {
    display: flex;
    justify-content: flex-end; /* Rata kanan */
    margin-top: 24px;          /* Margin atas 24px */
    margin-bottom: 24px;       /* Margin bawah minimal 24px */
}

.btn-generate-submit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 11px 28px; /* Padding nyaman */
    background-color: var(--color-success, #10b981);
    color: #ffffff;
    border: none;
    border-radius: var(--radius-md, 10px);
    font-size: 0.95rem;
    font-weight: 600;
    font-family: var(--font-sans, inherit);
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(16, 185, 129, 0.25);
    transition: var(--transition, all 0.2s cubic-bezier(0.4, 0, 0.2, 1));
}

.btn-generate-submit:hover:not(:disabled) {
    background-color: var(--color-success-hover, #059669);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(16, 185, 129, 0.32);
}

.btn-generate-submit:active:not(:disabled) {
    transform: translateY(0);
}

.btn-generate-submit:disabled,
.btn-generate-submit[disabled] {
    opacity: 0.65;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* Kotak Aturan yang Dijalankan */
.generate-rules-box {
    margin-top: 0;
    padding: 16px 20px;
    background: #f5f8fc;
    border: 1px solid #e2e8f0;
    border-radius: var(--radius-md, 8px);
    font-size: 13px;
    line-height: 1.7;
}

/* Responsive Layar <= 768px */
@media (max-width: 768px) {
    .generate-form {
        gap: 18px;
    }

    .generate-form-row {
        grid-template-columns: 1fr; /* Satu kolom: label di atas input */
        gap: 8px;
    }

    .generate-form-label,
    .generate-form-label.label-input {
        padding-top: 0;
        white-space: normal;
    }

    .generate-radio-group {
        gap: 10px 20px;
    }

    .generate-checkbox-grid {
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 8px 16px;
    }

    .generate-form-actions {
        justify-content: stretch;
    }

    .btn-generate-submit {
        width: 100%;
    }
}
</style>
@endpush

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
                </div>
            @endif

            @if (session('flash_error'))
                <div class="flash-alert error">
                    <span>{{ session('flash_error') }}</span>
                </div>
            @endif

            @if (session('flash_warning'))
                <div class="flash-alert warning" style="background:#fffbeb; color:#b45309; border:1px solid #fcd34d;">
                    <span>{{ session('flash_warning') }}</span>
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
                    <button type="button" class="tab-btn" data-tab="tab-ruangan" onclick="showTab('tab-ruangan', this)">Manajemen Ruangan</button>
                    <button type="button" class="tab-btn" data-tab="tab-generate" onclick="showTab('tab-generate', this)">Generate Jadwal</button>
                    <button type="button" class="tab-btn" data-tab="tab-jadwal" onclick="showTab('tab-jadwal', this)">Jadwal Semester</button>
                </div>

                {{-- ============ INPUT MATA KULIAH ============ --}}
                <div class="tab-content active" id="tab-input">
                    <div class="tab-header">
                        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                            <h2>Data Mata Kuliah</h2>
                            @if (($userRole ?? '') === 'Jurusan')
                                <span style="background:#e0f2fe; color:#0369a1; padding:5px 12px; border-radius:20px; font-size:12.5px; font-weight:600; display:inline-flex; align-items:center; gap:6px;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                                    Mode Monitoring Jurusan
                                </span>
                            @elseif (($userRole ?? '') === 'Prodi')
                                <span style="background:#f0fdf4; color:#15803d; padding:5px 12px; border-radius:20px; font-size:12.5px; font-weight:600; display:inline-flex; align-items:center; gap:6px;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                                    Prodi: {{ $userProdi ?? 'Teknik Informatika' }}
                                </span>
                            @endif
                        </div>
                        <div style="display:flex; gap:10px; flex-wrap:wrap;">
                            @if (($userRole ?? '') !== 'Jurusan')
                                <button type="button" class="btn-add" onclick="openModal('modalImportMk')" style="background:#16a34a;">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" y1="3" x2="12" y2="15"></line>
                                    </svg>
                                    Import Excel / Spreadsheet
                                </button>
                                <button type="button" class="btn-add" onclick="openTambahMk()">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    Tambah MK
                                </button>
                                @if ($mataKuliah->isNotEmpty())
                                    <button type="button" class="btn-add" onclick="openModalHapusSemua('all')" style="background:#dc2626;" title="Hapus semua data mata kuliah dan jadwal">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                        </svg>
                                        Hapus Semua Data
                                    </button>
                                @endif
                            @endif
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
                                            @if (($userRole ?? '') === 'Jurusan')
                                                <span style="font-size:12px; color:#64748b; font-style:italic;">Hanya Lihat</span>
                                            @elseif (($userRole ?? '') === 'Prodi' && ! empty($userProdi) && stripos($mk->prodi, $userProdi) === false)
                                                <span style="font-size:11px; background:#f1f5f9; color:#475569; padding:2px 8px; border-radius:4px;" title="Mata Kuliah Fakultas/Universitas">MK Bersama</span>
                                            @else
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
                                            @endif
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

                {{-- ============ MANAJEMEN RUANGAN ============ --}}
                <div class="tab-content" id="tab-ruangan">
                    <div class="tab-header">
                        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                            <h2>Manajemen Ruangan Kuliah & Lab</h2>
                            <span style="background:#eff6ff; color:#1d4ed8; padding:5px 12px; border-radius:20px; font-size:12.5px; font-weight:600; display:inline-flex; align-items:center; gap:6px;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V7l8-4 8 4v14M8 21v-8h8v8"/></svg>
                                Total: {{ $ruangans->count() }} Ruangan ({{ $ruanganAktif->count() }} Aktif)
                            </span>
                            @if (($userRole ?? '') === 'Jurusan')
                                <span style="background:#e0f2fe; color:#0369a1; padding:5px 12px; border-radius:20px; font-size:12.5px; font-weight:600; display:inline-flex; align-items:center; gap:6px;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                                    Mode Monitoring Jurusan
                                </span>
                            @endif
                        </div>
                        <div style="display:flex; gap:10px; flex-wrap:wrap;">
                            @if (($userRole ?? '') !== 'Jurusan')
                                <button type="button" class="btn-add" onclick="openTambahRuangan()">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    Tambah Ruangan
                                </button>
                            @endif
                        </div>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Kode Ruangan</th>
                                <th>Nama Ruangan</th>
                                <th>Tipe</th>
                                <th>Kapasitas</th>
                                <th>Gedung & Lantai</th>
                                <th style="text-align:center;">Status</th>
                                <th style="text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ruangans as $r)
                                <tr>
                                    <td>
                                        <strong>{{ $r->kode }}</strong>
                                        @if ($r->isDefault())
                                            <span style="display: inline-block; font-size: 0.72rem; font-weight: 600; padding: 2px 7px; background: #e0f2fe; color: #0369a1; border-radius: 4px; margin-left: 6px;" title="Ruangan bawaan resmi kampus">Kampus</span>
                                        @endif
                                    </td>
                                    <td>{{ $r->nama }}</td>
                                    <td>
                                        @if ($r->tipe === 'Lab')
                                            <span style="display: inline-block; padding: 3px 10px; background: #dcfce7; color: #15803d; border-radius: 12px; font-size: 0.82rem; font-weight: 600;">Lab</span>
                                        @elseif ($r->tipe === 'Ruang Kelas')
                                            <span style="display: inline-block; padding: 3px 10px; background: #eff6ff; color: #1d4ed8; border-radius: 12px; font-size: 0.82rem; font-weight: 600;">Ruang Kelas</span>
                                        @else
                                            <span style="display: inline-block; padding: 3px 10px; background: #fef3c7; color: #92400e; border-radius: 12px; font-size: 0.82rem; font-weight: 600;">{{ $r->tipe }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $r->kapasitas }} Kursi</td>
                                    <td>{{ $r->gedung ?? '-' }} / Lt. {{ $r->lantai ?? '-' }}</td>
                                    <td style="text-align:center;">
                                        @if (($userRole ?? '') === 'Jurusan')
                                            @if ($r->is_active)
                                                <span style="background:#ecfdf5; color:#047857; border:1px solid #a7f3d0; border-radius:14px; padding:3px 10px; font-size:12px; font-weight:600; display:inline-flex; align-items:center; gap:5px;">
                                                    <span style="width:7px; height:7px; border-radius:50%; background:#10b981; display:inline-block;"></span>
                                                    Aktif
                                                </span>
                                            @else
                                                <span style="background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; border-radius:14px; padding:3px 10px; font-size:12px; font-weight:600; display:inline-flex; align-items:center; gap:5px;">
                                                    <span style="width:7px; height:7px; border-radius:50%; background:#ef4444; display:inline-block;"></span>
                                                    Nonaktif
                                                </span>
                                            @endif
                                        @else
                                            <form action="{{ route('ruangan.toggle-status', $r->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @if ($r->is_active)
                                                    <button type="submit" style="background:#ecfdf5; color:#047857; border:1px solid #a7f3d0; border-radius:14px; padding:3px 10px; font-size:12px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:5px; transition:all 0.2s;" title="Klik untuk menonaktifkan ruangan">
                                                        <span style="width:7px; height:7px; border-radius:50%; background:#10b981; display:inline-block;"></span>
                                                        Aktif
                                                    </button>
                                                @else
                                                    <button type="submit" style="background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; border-radius:14px; padding:3px 10px; font-size:12px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:5px; transition:all 0.2s;" title="Klik untuk mengaktifkan ruangan">
                                                        <span style="width:7px; height:7px; border-radius:50%; background:#ef4444; display:inline-block;"></span>
                                                        Nonaktif
                                                    </button>
                                                @endif
                                            </form>
                                        @endif
                                    </td>
                                    <td style="text-align:center;">
                                        @if (($userRole ?? '') === 'Jurusan')
                                            <span style="font-size:12px; color:#64748b; font-style:italic;">Hanya Lihat</span>
                                        @else
                                            <div class="action-buttons-group" style="justify-content:center;">
                                                <button type="button" class="btn-action edit-btn" title="Edit Ruangan"
                                                    onclick='openEditRuangan(@json($r))'>
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                        <path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4z"></path>
                                                    </svg>
                                                </button>
                                                @if ($r->isDefault())
                                                    <button type="button" class="btn-action" disabled title="Ruangan bawaan resmi kampus tidak dapat dihapus (dapat dinonaktifkan jika tidak digunakan)" style="opacity:0.4; cursor:not-allowed;">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                                        </svg>
                                                    </button>
                                                @else
                                                    <form action="{{ route('ruangan.destroy', $r->id) }}" method="POST" style="display:inline;"
                                                        onsubmit="return konfirmasiHapusRuangan(event, '{{ $r->kode }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn-action delete-btn" title="Hapus Ruangan">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align:center; padding:24px;">Belum ada data ruangan.</td>
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

                    <form action="{{ route('penjadwalan.generate') }}" method="POST" id="formGenerate" class="generate-form">
                        @csrf
                        <div class="generate-form-row">
                            <label class="generate-form-label">Pilihan Periode</label>
                            <div class="generate-radio-group">
                                <label class="generate-radio-item">
                                    <input type="radio" name="periode_radio" value="ganjil" checked onchange="updateSemesterCheckboxes('ganjil')">
                                    Semester Ganjil (1, 3, 5, 7)
                                </label>
                                <label class="generate-radio-item">
                                    <input type="radio" name="periode_radio" value="genap" onchange="updateSemesterCheckboxes('genap')">
                                    Semester Genap (2, 4, 6, 8)
                                </label>
                                <label class="generate-radio-item">
                                    <input type="radio" name="periode_radio" value="keduanya" onchange="updateSemesterCheckboxes('keduanya')">
                                    Keduanya (1 s/d 8)
                                </label>
                            </div>
                        </div>

                        <div class="generate-form-row">
                            <label class="generate-form-label">Semester spesifik</label>
                            <div class="generate-checkbox-grid">
                                @foreach ($semesterAll as $sem)
                                    <label class="generate-check-item sem-check-label sem-{{ $sem % 2 === 1 ? 'ganjil' : 'genap' }}">
                                        <input type="checkbox" name="semester[]" value="{{ $sem }}" class="sem-check-input"
                                            @checked($sem % 2 === 1)>
                                        Semester {{ $sem }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="generate-form-row">
                            <label class="generate-form-label label-input" for="genProdi">Program Studi</label>
                            <div class="generate-input-wrap">
                                @if (($userRole ?? '') === 'Prodi')
                                    <input type="text" id="genProdi" name="prodi" value="{{ $userProdi ?? 'Teknik Informatika' }}" readonly style="background:#f1f5f9; cursor:not-allowed; font-weight:600; padding:9px 14px; border:1.5px solid #cbd5e1; border-radius:8px; width:100%;" required>
                                    <span style="font-size:12px; color:#64748b; margin-top:4px; display:block;">Terkunci khusus Program Studi Anda ({{ $userProdi ?? 'Teknik Informatika' }}).</span>
                                @else
                                    <select id="genProdi" name="prodi" style="padding:9px 14px; border:1.5px solid #cbd5e1; border-radius:8px; font-size:0.95rem; background:#ffffff; color:#1e293b; width:100%; cursor:pointer; font-weight:500;" required>
                                        <option value="Teknik Informatika" selected>Teknik Informatika (TI) &mdash; Hanya mata kuliah TI</option>
                                        <option value="Teknik Elektro">Teknik Elektro (TE) &mdash; Hanya mata kuliah Elektro</option>
                                        <option value="Semua">Semua Program Studi (Lintas Prodi)</option>
                                    </select>
                                    <span style="font-size:12px; color:#64748b; margin-top:4px; display:block;">Pilih prodi spesifik agar mata kuliah prodi lain tidak ikut di-generate.</span>
                                @endif
                            </div>
                        </div>

                        <div class="generate-form-row">
                            <label class="generate-form-label">Hari tersedia</label>
                            <div class="generate-checkbox-grid">
                                @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                                    <label class="generate-check-item">
                                        <input type="checkbox" name="hari[]" value="{{ $hari }}" checked>
                                        {{ $hari }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="generate-form-row">
                            <label class="generate-form-label">Jam tersedia</label>
                            <div class="generate-checkbox-grid">
                                @foreach (['07:30', '10:00', '13:00', '14:50'] as $jam)
                                    <label class="generate-check-item">
                                        <input type="checkbox" name="jam_mulai[]" value="{{ $jam }}" checked>
                                        {{ $jam }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="generate-form-actions">
                            @if (($userRole ?? '') === 'Jurusan')
                                <div style="background:#f1f5f9; color:#475569; padding:10px 18px; border-radius:8px; font-size:13.5px; display:inline-flex; align-items:center; gap:8px;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                                    <span><strong>Mode Monitoring:</strong> Akun Jurusan hanya berwenang untuk memantau & evaluasi jadwal (generate jadwal dilakukan oleh Prodi / Fakultas).</span>
                                </div>
                            @else
                                <button type="submit" class="btn-generate-submit btn-modal-submit" id="btnSubmitGenerate">Generate Sekarang</button>
                            @endif
                        </div>
                    </form>

                    <div class="generate-rules-box">
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
                                @if (! empty($hasil['kegagalan']))
                                    <div style="background:#fef2f2; border:1px solid #f87171; border-radius:8px; padding:12px 16px; margin-bottom:14px;">
                                        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                                            <div style="display:flex; align-items:center; gap:8px;">
                                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#b91c1c" stroke-width="2">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                                </svg>
                                                <span style="color:#b91c1c; font-size:13.5px; font-weight:700;">
                                                    Diagnosa Penjadwalan: {{ count($hasil['kegagalan']) }} Mata Kuliah Memerlukan Penyesuaian
                                                </span>
                                            </div>
                                            <button type="button" 
                                                onclick="const el = document.getElementById('diagnosaList-{{ $hasil['semester'] }}'); const isHidden = el.style.display === 'none'; el.style.display = isHidden ? 'flex' : 'none'; this.innerHTML = isHidden ? 'Sembunyikan Detail &times;' : 'Lihat Detail &darr;';" 
                                                style="background:#ffffff; color:#b91c1c; border:1px solid #fca5a5; border-radius:6px; padding:5px 12px; font-size:12.5px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:4px; transition:all 0.2s ease;">
                                                Lihat Detail &darr;
                                            </button>
                                        </div>
                                        <div id="diagnosaList-{{ $hasil['semester'] }}" style="display:none; flex-direction:column; gap:10px; margin-top:12px; border-top:1px dashed #fca5a5; padding-top:12px;">
                                            @foreach ($hasil['kegagalan'] as $keg)
                                                <div style="background:#ffffff; border:1px solid #fecaca; border-radius:6px; padding:12px; font-size:13px;">
                                                    <div style="display:flex; justify-content:space-between; align-items:center; font-weight:600; margin-bottom:4px;">
                                                        <span style="color:#1e293b; font-size:13px;">{{ $keg['kode_mk'] }} - {{ $keg['nama_mk'] }} ({{ $keg['sks'] }} SKS)</span>
                                                        <span style="background:#fee2e2; color:#b91c1c; padding:2px 8px; border-radius:4px; font-size:11.5px;">{{ $keg['prodi'] }}</span>
                                                    </div>
                                                    <div style="color:#b91c1c; margin-top:4px;">
                                                        <strong>Penyebab:</strong> {{ $keg['alasan'] }}
                                                    </div>
                                                    <div style="color:#15803d; margin-top:3px;">
                                                        <strong>Rekomendasi Solusi:</strong> {{ $keg['rekomendasi'] }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

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

                {{-- ============ TAB JADWAL SEMESTER ============ --}}
                <div class="tab-content" id="tab-jadwal">
                    <div class="tab-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:20px;">
                        <div>
                            <h2 style="margin:0 0 4px 0;">Jadwal Semester</h2>
                            <p style="margin:0; font-size:0.9rem; color:#64748b;">Lihat jadwal terstruktur per semester dalam mode Ganjil atau Genap.</p>
                        </div>

                        <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
                            {{-- Filter Dropdown --}}
                            <div style="display:flex; align-items:center; gap:8px;">
                                <label for="filterSemesterSelect" style="font-size:0.9rem; font-weight:600; color:#1e293b; white-space:nowrap;">Semester:</label>
                                <select id="filterSemesterSelect" onchange="filterSemester(this.value)" 
                                    style="padding:7px 14px; border:1.5px solid #cbd5e1; border-radius:18px; font-size:0.9rem; outline:none; background:#ffffff; color:#1e293b; cursor:pointer;">
                                    <option value="all">Semua Semester</option>
                                    {{-- Options dynamically rendered by JS based on active toggle --}}
                                </select>
                            </div>

                            {{-- Filter Prodi Dropdown --}}
                            @if (($userRole ?? '') === 'Prodi')
                                <span style="font-size:0.9rem; font-weight:600; color:#1e293b; background:#f1f5f9; padding:6px 14px; border-radius:18px; border:1px solid #cbd5e1;">
                                    Prodi: {{ $userProdi ?? 'Teknik Informatika' }}
                                </span>
                            @else
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <label for="filterProdiSelect" style="font-size:0.9rem; font-weight:600; color:#1e293b; white-space:nowrap;">Prodi:</label>
                                    <select id="filterProdiSelect" onchange="filterProdi(this.value)" 
                                        style="padding:7px 14px; border:1.5px solid #cbd5e1; border-radius:18px; font-size:0.9rem; outline:none; background:#ffffff; color:#1e293b; cursor:pointer; font-weight:600;">
                                        <option value="all">Semua Prodi</option>
                                        <option value="Teknik Informatika">Teknik Informatika</option>
                                        <option value="Teknik Elektro">Teknik Elektro</option>
                                    </select>
                                </div>
                            @endif

                            {{-- Pill Toggle Ganjil | Genap --}}
                            <div class="pill-toggle-container" style="display:inline-flex; background:#e2e8f0; padding:4px; border-radius:30px; user-select:none;">
                                <button type="button" id="btnToggleGanjil" onclick="switchPeriode('ganjil')" 
                                    style="padding:7px 20px; border:none; border-radius:24px; font-weight:700; font-size:0.9rem; cursor:pointer; transition:all 0.2s ease; background:#0860be; color:#ffffff; box-shadow:0 2px 4px rgba(8,96,190,0.25);">
                                    Ganjil
                                </button>
                                <button type="button" id="btnToggleGenap" onclick="switchPeriode('genap')" 
                                    style="padding:7px 20px; border:none; border-radius:24px; font-weight:700; font-size:0.9rem; cursor:pointer; transition:all 0.2s ease; background:transparent; color:#64748b;">
                                    Genap
                                </button>
                            </div>

                            @if (($userRole ?? '') !== 'Jurusan' && $jadwal->isNotEmpty())
                                <button type="button" class="btn-add" onclick="openModalHapusSemua('jadwal_only')" 
                                    style="background:#dc2626; padding:7px 16px; font-size:0.875rem;" title="Hapus semua jadwal kuliah yang telah di-generate">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px; height:16px;">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                    Hapus Semua Jadwal
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Container Mode Ganjil (Semester 1, 3, 5, 7) --}}
                    <div id="container-ganjil" class="periode-container">
                        @foreach ($semesterGanjil as $sem)
                            @include('penjadwalan.partials.jadwal-semester', ['sem' => $sem, 'jadwal' => $jadwal])
                        @endforeach
                    </div>

                    {{-- Container Mode Genap (Semester 2, 4, 6, 8) --}}
                    <div id="container-genap" class="periode-container" style="display:none;">
                        @foreach ($semesterGenap as $sem)
                            @include('penjadwalan.partials.jadwal-semester', ['sem' => $sem, 'jadwal' => $jadwal])
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

{{-- ============ MODAL IMPORT MK (SMART IMPORTER WORKFLOW) ============ --}}
<div class="modal-backdrop" id="modalImportMk">
    <div class="modal-window" id="modalImportMkWindow" style="max-width: 600px; transition: max-width 0.3s ease;">
        <div class="modal-header-blue">
            <h3>Smart Import Mata Kuliah (Excel / Spreadsheet)</h3>
        </div>
        <div class="modal-body">
            <form action="{{ route('penjadwalan.import') }}" method="POST" enctype="multipart/form-data" class="modal-form" id="formImportSpreadsheet">
                @csrf
                <input type="hidden" name="verified_items" id="verifiedItemsInput">

                <div class="modal-form-row" style="align-items: flex-start;">
                    <label for="importFile" style="padding-top:8px;">File Spreadsheet</label>
                    <div class="modal-input-container" style="flex-direction:column; align-items:flex-start; gap:6px;">
                        <input type="file" id="importFile" name="file" accept=".xlsx,.xls,.csv,.ods,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,application/vnd.oasis.opendocument.spreadsheet" required>
                        <span style="font-size:12px; color:#64748b;">Mendukung: Excel (.xlsx, .xls), CSV (.csv), dan ODS (.ods).</span>
                    </div>
                </div>

                <div id="importPreviewStatus" style="display:none; padding:12px 14px; background:#f0f9ff; border:1px solid #bae6fd; border-radius:8px; font-size:13px; margin-top:12px;"></div>

                {{-- Interactive Smart Preview Section --}}
                <div id="importPreviewContainer" style="display:none; margin-top:16px;">
                    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(110px, 1fr)); gap:10px; margin-bottom:14px;">
                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px; text-align:center;">
                            <span style="font-size:11px; color:#64748b; font-weight:600; text-transform:uppercase;">Total Baris</span>
                            <div id="badgeTotal" style="font-size:18px; font-weight:700; color:#1e293b; margin-top:2px;">0</div>
                        </div>
                        <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:10px; text-align:center;">
                            <span style="font-size:11px; color:#166534; font-weight:600; text-transform:uppercase;">Dikenali</span>
                            <div id="badgeDikenali" style="font-size:18px; font-weight:700; color:#15803d; margin-top:2px;">0</div>
                        </div>
                        <div style="background:#fefce8; border:1px solid #fef08a; border-radius:8px; padding:10px; text-align:center;">
                            <span style="font-size:11px; color:#854d0e; font-weight:600; text-transform:uppercase;">Duplikat</span>
                            <div id="badgeDuplikat" style="font-size:18px; font-weight:700; color:#a16207; margin-top:2px;">0</div>
                        </div>
                        <div style="background:#fff7ed; border:1px solid #fed7aa; border-radius:8px; padding:10px; text-align:center;">
                            <span style="font-size:11px; color:#9a3412; font-weight:600; text-transform:uppercase;">Perlu Verifikasi</span>
                            <div id="badgePerluVerifikasi" style="font-size:18px; font-weight:700; color:#c2410c; margin-top:2px;">0</div>
                        </div>
                        <div style="background:#f0f9ff; border:1px solid #bae6fd; border-radius:8px; padding:10px; text-align:center;">
                            <span style="font-size:11px; color:#0369a1; font-weight:600; text-transform:uppercase;">Dosen Baru</span>
                            <div id="badgeDosenBaru" style="font-size:18px; font-weight:700; color:#0284c7; margin-top:2px;">0</div>
                        </div>
                    </div>

                    <div style="max-height: 280px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 12px;">
                        <table class="data-table" style="font-size: 12.5px; margin: 0;">
                            <thead>
                                <tr style="background:#f8fafc; position:sticky; top:0; z-index:1;">
                                    <th style="padding:8px;">Kode MK</th>
                                    <th style="padding:8px;">Nama Mata Kuliah</th>
                                    <th style="padding:8px;">SKS</th>
                                    <th style="padding:8px;">Sem</th>
                                    <th style="padding:8px;">Kelas</th>
                                    <th style="padding:8px;">Prodi</th>
                                    <th style="padding:8px;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="previewTableBody"></tbody>
                        </table>
                    </div>
                </div>

                <div id="importInfoDefault" style="padding:12px 14px; background:#f0f9ff; border:1px solid #bae6fd; border-radius:8px; font-size:13px; color:#0369a1; line-height:1.6; margin-top:8px;">
                    <strong>Smart Auto-Classification:</strong><br>
                    Sistem mendeteksi Sheet1 & Sheet2, mengklasifikasi MK Universitas (UNV), MK Fakultas (FST), MK Prodi, serta memetakan inisial dosen (MP, NH, dll.) secara otomatis.
                </div>

                <div class="modal-footer-buttons" style="margin-top:20px;">
                    <button type="button" class="btn-modal-cancel" onclick="closeModal('modalImportMk')">Batal</button>
                    <button type="submit" class="btn-modal-submit" id="btnSubmitImport">Import Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============ MODAL HAPUS SEMUA ============ --}}
<div class="modal-backdrop" id="modalHapusSemua">
    <div class="modal-window" style="max-width: 480px;">
        <div class="modal-header-blue" style="background:#dc2626;">
            <h3 id="modalHapusSemuaTitle">Konfirmasi Hapus Semua</h3>
        </div>
        <div class="modal-body">
            <div style="display:flex; align-items:flex-start; gap:14px; margin-bottom:18px;">
                <div style="width:40px; height:40px; border-radius:50%; background:#fee2e2; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </div>
                <div>
                    <p id="modalHapusSemuaDesc" style="color:#334155; font-size:14px; line-height:1.6; margin:0;">
                        Apakah Anda yakin ingin menghapus seluruh data? Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
            </div>
            <form action="{{ route('penjadwalan.destroy-all') }}" method="POST" id="formHapusSemua">
                @csrf
                @method('DELETE')
                <input type="hidden" name="scope" id="hapusSemuaScope" value="all">
                <div class="modal-footer-buttons" style="margin-top:16px;">
                    <button type="button" class="btn-modal-cancel" onclick="closeModal('modalHapusSemua')">Batal</button>
                    <button type="submit" class="btn-modal-submit" style="background:#dc2626;" id="btnSubmitHapusSemua">Ya, Hapus Semua</button>
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
                            <optgroup label="Semester Ganjil">
                                @foreach ($semesterGanjil as $s)
                                    <option value="{{ $s }}">Semester {{ $s }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Semester Genap">
                                @foreach ($semesterGenap as $s)
                                    <option value="{{ $s }}">Semester {{ $s }}</option>
                                @endforeach
                            </optgroup>
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

                <div class="modal-form-row">
                    <label for="mkTanpaRuangan">Tanpa Ruangan</label>
                    <div class="modal-input-container">
                        <select id="mkTanpaRuangan" name="tanpa_ruangan">
                            <option value="0">Tidak (butuh ruang kelas)</option>
                            <option value="1">Ya (Skripsi / KKN / Magang)</option>
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

{{-- ============ MODAL INPUT/EDIT RUANGAN ============ --}}
<div class="modal-backdrop" id="modalRuangan">
    <div class="modal-window">
        <div class="modal-header-blue">
            <h3 id="modalRuanganTitle">Tambah Ruangan</h3>
        </div>
        <div class="modal-body">
            <form id="formRuangan" method="POST" class="modal-form">
                @csrf
                <input type="hidden" name="_method" id="ruanganMethod" value="">

                <div class="modal-form-row">
                    <label for="ruanganKode">Kode Ruangan</label>
                    <div class="modal-input-container">
                        <input type="text" id="ruanganKode" name="kode" placeholder="Contoh: R01-FTTK, LAB-03" required>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="ruanganNama">Nama Ruangan</label>
                    <div class="modal-input-container">
                        <input type="text" id="ruanganNama" name="nama" placeholder="Contoh: Ruang Kelas 01 FTTK" required>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="ruanganTipe">Tipe Ruangan</label>
                    <div class="modal-input-container">
                        <select id="ruanganTipe" name="tipe" required>
                            <option value="Ruang Kelas">Ruang Kelas</option>
                            <option value="Lab">Laboratorium (Lab)</option>
                            <option value="Aula">Aula</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="ruanganKapasitas">Kapasitas (Kursi)</label>
                    <div class="modal-input-container">
                        <input type="number" id="ruanganKapasitas" name="kapasitas" min="1" max="500" value="40" required>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="ruanganGedung">Gedung</label>
                    <div class="modal-input-container">
                        <input type="text" id="ruanganGedung" name="gedung" placeholder="Contoh: Gedung FTTK / Utama">
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="ruanganLantai">Lantai</label>
                    <div class="modal-input-container">
                        <input type="text" id="ruanganLantai" name="lantai" placeholder="Contoh: 1, 2, atau 3">
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="ruanganIsActive">Status Aktif</label>
                    <div class="modal-input-container">
                        <select id="ruanganIsActive" name="is_active">
                            <option value="1">Aktif (Dipakai untuk generate jadwal)</option>
                            <option value="0">Nonaktif (Jangan dipakai generate)</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer-buttons">
                    <button type="button" class="btn-modal-cancel" onclick="closeModal('modalRuangan')">Batal</button>
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
                        <button type="button" class="btn-toggle-password" onclick="togglePasswordVisibilityModal('profile_password_lama', this)">
                            <svg id="profile_password_lama_eye" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="modal-form-row">
                    <label for="profile_password_baru">Password Baru</label>
                    <div class="modal-input-container">
                        <input type="password" id="profile_password_baru" name="password_baru" placeholder="Kosongkan jika tidak diubah">
                        <button type="button" class="btn-toggle-password" onclick="togglePasswordVisibilityModal('profile_password_baru', this)">
                            <svg id="profile_password_baru_eye" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="modal-form-row">
                    <label for="profile_password_baru_confirmation">Konfirmasi Password Baru</label>
                    <div class="modal-input-container">
                        <input type="password" id="profile_password_baru_confirmation" name="password_baru_confirmation" placeholder="Ulangi password baru">
                        <button type="button" class="btn-toggle-password" onclick="togglePasswordVisibilityModal('profile_password_baru_confirmation', this)">
                            <svg id="profile_password_baru_confirmation_eye" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
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
const RUANGAN_FORM_ACTION_STORE = "{{ route('ruangan.store') }}";
const RUANGAN_FORM_ACTION_UPDATE = "{{ route('ruangan.update', ['ruangan' => '__ID__']) }}";

let currentPeriode = 'ganjil';

function showTab(id, btn) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    
    const targetContent = document.getElementById(id);
    if (targetContent) targetContent.classList.add('active');
    
    const targetBtn = btn || document.querySelector(`.tab-btn[data-tab="${id}"]`);
    if (targetBtn) targetBtn.classList.add('active');

    // Simpan tab aktif di localStorage
    localStorage.setItem('penjadwalan_active_tab', id);
}

// Toggle Periode Ganjil / Genap pada Tab Jadwal Semester
function switchPeriode(mode, saveState = true) {
    currentPeriode = mode;
    const btnGanjil = document.getElementById('btnToggleGanjil');
    const btnGenap = document.getElementById('btnToggleGenap');
    const cGanjil = document.getElementById('container-ganjil');
    const cGenap = document.getElementById('container-genap');

    if (mode === 'ganjil') {
        btnGanjil.style.background = '#0860be';
        btnGanjil.style.color = '#ffffff';
        btnGanjil.style.boxShadow = '0 2px 4px rgba(8,96,190,0.25)';
        btnGenap.style.background = 'transparent';
        btnGenap.style.color = '#64748b';
        btnGenap.style.boxShadow = 'none';

        cGanjil.style.display = 'block';
        cGenap.style.display = 'none';
    } else {
        btnGenap.style.background = '#0860be';
        btnGenap.style.color = '#ffffff';
        btnGenap.style.boxShadow = '0 2px 4px rgba(8,96,190,0.25)';
        btnGanjil.style.background = 'transparent';
        btnGanjil.style.color = '#64748b';
        btnGanjil.style.boxShadow = 'none';

        cGenap.style.display = 'block';
        cGanjil.style.display = 'none';
    }

    updateFilterDropdown(mode);

    if (saveState) {
        localStorage.setItem('penjadwalan_periode', mode);
        const url = new URL(window.location);
        url.searchParams.set('periode', mode);
        window.history.replaceState({}, '', url);
    }
}

// Update opsi dropdown filter berdasarkan mode Ganjil/Genap
function updateFilterDropdown(mode) {
    const select = document.getElementById('filterSemesterSelect');
    if (!select) return;

    select.innerHTML = '<option value="all">Semua Semester</option>';
    const list = mode === 'ganjil' ? [1, 3, 5, 7] : [2, 4, 6, 8];

    list.forEach(sem => {
        const opt = document.createElement('option');
        opt.value = sem;
        opt.textContent = 'Semester ' + sem;
        select.appendChild(opt);
    });
    select.value = 'all';

    // Tampilkan kembali semua section di container aktif
    filterSemester('all');
}

// Filter semester tertentu di tab jadwal
function filterSemester(val) {
    const activeContainer = currentPeriode === 'ganjil' ? document.getElementById('container-ganjil') : document.getElementById('container-genap');
    if (!activeContainer) return;

    const sections = activeContainer.querySelectorAll('.semester-section');
    sections.forEach(sec => {
        if (val === 'all' || sec.getAttribute('data-semester') === String(val)) {
            sec.style.display = 'block';
        } else {
            sec.style.display = 'none';
        }
    });
}

// Filter prodi tertentu di tab jadwal
function filterProdi(val) {
    const rows = document.querySelectorAll('.jadwal-row-item');
    const filterLower = (val || 'all').toLowerCase();

    rows.forEach(r => {
        const rowProdi = (r.getAttribute('data-prodi') || '').toLowerCase();
        let show = true;
        if (filterLower !== 'all' && filterLower !== 'semua') {
            if (filterLower.includes('informatika') || filterLower === 'ti') {
                show = rowProdi.includes('informatika') && !rowProdi.includes('elektro');
            } else if (filterLower.includes('elektro') || filterLower === 'te') {
                show = rowProdi.includes('elektro') && !rowProdi.includes('informatika');
            } else {
                show = rowProdi.includes(filterLower);
            }
        }
        r.style.display = show ? '' : 'none';
    });

    // Update group counts per hari
    document.querySelectorAll('.hari-jadwal-group').forEach(grp => {
        const visibleRows = grp.querySelectorAll('.jadwal-row-item:not([style*="display: none"])');
        const badge = grp.querySelector('.hari-count-badge');
        if (badge) {
            badge.textContent = visibleRows.length + ' kelas';
        }
        grp.style.display = visibleRows.length > 0 ? '' : 'none';
    });

    // Update semester accordion headers
    document.querySelectorAll('.semester-section').forEach(sec => {
        const visibleRows = sec.querySelectorAll('.jadwal-row-item:not([style*="display: none"])');
        const sem = sec.getAttribute('data-semester');
        const badge = document.getElementById('accordion-badge-' + sem);
        if (badge) {
            badge.textContent = visibleRows.length > 0 ? 'Terjadwal (' + visibleRows.length + ')' : 'Kosong';
            if (visibleRows.length > 0) {
                badge.style.background = '#e0f2fe';
                badge.style.color = '#0284c7';
            } else {
                badge.style.background = '#f1f5f9';
                badge.style.color = '#94a3b8';
            }
        }
    });

    localStorage.setItem('penjadwalan_filter_prodi', val);
}

// Accordion Collapse / Expand per Semester
function toggleAccordion(sem) {
    const body = document.getElementById('accordion-body-' + sem);
    const chevron = document.getElementById('chevron-sem-' + sem);
    if (!body || !chevron) return;

    if (body.style.display === 'none') {
        body.style.display = 'block';
        chevron.style.transform = 'rotate(0deg)';
    } else {
        body.style.display = 'none';
        chevron.style.transform = 'rotate(-90deg)';
    }
}

// Update Checkbox Semester di Tab Generate Jadwal
function updateSemesterCheckboxes(mode) {
    const checkboxes = document.querySelectorAll('.sem-check-input');
    checkboxes.forEach(cb => {
        const val = parseInt(cb.value);
        if (mode === 'ganjil') {
            cb.checked = (val % 2 === 1);
        } else if (mode === 'genap') {
            cb.checked = (val % 2 === 0);
        } else if (mode === 'keduanya') {
            cb.checked = true;
        }
    });
}

// Inisialisasi State Saat Halaman Dimuat
document.addEventListener('DOMContentLoaded', function() {
    // 1. Cek parameter session flash (setelah generate) atau URL query atau localStorage
    const serverTab = "{{ session('tab_aktif') }}";
    const serverPeriode = "{{ session('periode_aktif') }}";
    const serverProdi = "{{ session('prodi_aktif') }}";
    const urlParams = new URLSearchParams(window.location.search);
    const queryPeriode = urlParams.get('periode');
    const localPeriode = localStorage.getItem('penjadwalan_periode');
    const localTab = localStorage.getItem('penjadwalan_active_tab');
    const localProdi = localStorage.getItem('penjadwalan_filter_prodi');

    // Tentukan filter prodi awal
    const prodiSelect = document.getElementById('filterProdiSelect');
    if (prodiSelect) {
        if (serverProdi && serverProdi !== 'all') {
            prodiSelect.value = serverProdi;
            filterProdi(serverProdi);
            localStorage.setItem('penjadwalan_filter_prodi', serverProdi);
        } else if (localProdi) {
            prodiSelect.value = localProdi;
            filterProdi(localProdi);
        }
    }

    // Tentukan periode awal
    let targetPeriode = 'ganjil';
    if (serverPeriode && (serverPeriode === 'ganjil' || serverPeriode === 'genap')) {
        targetPeriode = serverPeriode;
    } else if (queryPeriode && (queryPeriode === 'ganjil' || queryPeriode === 'genap')) {
        targetPeriode = queryPeriode;
    } else if (localPeriode && (localPeriode === 'ganjil' || localPeriode === 'genap')) {
        targetPeriode = localPeriode;
    }
    switchPeriode(targetPeriode, true);

    // Tentukan tab awal
    let targetTab = 'tab-input';
    if (serverTab && document.getElementById(serverTab)) {
        targetTab = serverTab;
    } else if (localTab && document.getElementById(localTab)) {
        targetTab = localTab;
    }
    showTab(targetTab);

    // Disabled state saat proses generate berjalan
    const formGenerateEl = document.getElementById('formGenerate');
    if (formGenerateEl) {
        formGenerateEl.addEventListener('submit', function() {
            const btn = document.getElementById('btnSubmitGenerate');
            if (btn) {
                setTimeout(function() {
                    btn.disabled = true;
                }, 10);
            }
        });
    }
});

// Modal Tambah & Edit Mata Kuliah
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

// Modal Tambah & Edit Ruangan
function openTambahRuangan() {
    const form = document.getElementById('formRuangan');
    form.action = RUANGAN_FORM_ACTION_STORE;
    form.reset();
    document.getElementById('ruanganMethod').value = '';
    document.getElementById('modalRuanganTitle').textContent = 'Tambah Ruangan';
    document.getElementById('ruanganKode').readOnly = false;
    document.getElementById('ruanganKapasitas').value = '40';
    document.getElementById('ruanganTipe').value = 'Ruang Kelas';
    document.getElementById('ruanganIsActive').value = '1';
    openModal('modalRuangan');
}

function openEditRuangan(r) {
    const form = document.getElementById('formRuangan');
    form.action = RUANGAN_FORM_ACTION_UPDATE.replace('__ID__', r.id);
    document.getElementById('ruanganMethod').value = 'PUT';
    document.getElementById('modalRuanganTitle').textContent = 'Edit Ruangan ' + r.kode;
    document.getElementById('ruanganKode').value = r.kode;
    document.getElementById('ruanganNama').value = r.nama;
    document.getElementById('ruanganTipe').value = r.tipe;
    document.getElementById('ruanganKapasitas').value = r.kapasitas;
    document.getElementById('ruanganGedung').value = r.gedung || '';
    document.getElementById('ruanganLantai').value = r.lantai || '';
    document.getElementById('ruanganIsActive').value = r.is_active ? '1' : '0';
    openModal('modalRuangan');
}

function konfirmasiHapusRuangan(event, kode) {
    if (!confirm('Yakin ingin menghapus ruangan ' + kode + '?')) {
        event.preventDefault();
        return false;
    }
    return true;
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
    document.getElementById('mkTanpaRuangan').value = mk.tanpa_ruangan ? '1' : '0';

    openModal('modalMk');
}

function konfirmasiHapus(event, kode) {
    if (!confirm('Yakin ingin menghapus mata kuliah ' + kode + '?')) {
        event.preventDefault();
        return false;
    }
    return true;
}

function openModalHapusSemua(scope) {
    const scopeInput = document.getElementById('hapusSemuaScope');
    const titleEl = document.getElementById('modalHapusSemuaTitle');
    const descEl = document.getElementById('modalHapusSemuaDesc');

    if (scope === 'jadwal_only') {
        scopeInput.value = 'jadwal_only';
        titleEl.textContent = 'Hapus Semua Hasil Jadwal';
        descEl.innerHTML = 'Apakah Anda yakin ingin <strong>menghapus semua hasil jadwal kuliah yang telah di-generate</strong>? Data mata kuliah tetap aman tersimpan.';
    } else {
        scopeInput.value = 'all';
        titleEl.textContent = 'Hapus Semua Data Mata Kuliah dan Jadwal ?';
        descEl.innerHTML = 'Apakah Anda yakin ingin <strong>menghapus seluruh data mata kuliah beserta seluruh jadwal</strong>? Semua baris data akan dibersihkan dan tindakan ini tidak dapat dibatalkan.';
    }

    openModal('modalHapusSemua');
}

// Disabled state saat submit impor berjalan & Smart Preview
document.addEventListener('DOMContentLoaded', function() {
    const formImportEl = document.getElementById('formImportSpreadsheet');
    const importFileInput = document.getElementById('importFile');
    const modalWindow = document.getElementById('modalImportMkWindow');
    const previewContainer = document.getElementById('importPreviewContainer');
    const previewStatus = document.getElementById('importPreviewStatus');
    const previewTableBody = document.getElementById('previewTableBody');
    const verifiedInput = document.getElementById('verifiedItemsInput');
    const submitBtn = document.getElementById('btnSubmitImport');
    const infoDefault = document.getElementById('importInfoDefault');

    if (importFileInput) {
        importFileInput.addEventListener('change', function() {
            if (!this.files || !this.files[0]) return;

            const file = this.files[0];
            const formData = new FormData();
            formData.append('file', file);

            if (previewStatus) {
                previewStatus.style.display = 'block';
                previewStatus.innerHTML = '<div style="display:flex; align-items:center; gap:8px; color:#0369a1;"><strong>Menganalisis file Excel...</strong> (Sheet1, Sheet2, relasi dosen, dan klasifikasi MK)</div>';
            }
            if (previewContainer) previewContainer.style.display = 'none';

            fetch('{{ route('penjadwalan.import.preview') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(response => {
                if (response.success && response.data) {
                    const data = response.data;
                    if (modalWindow) modalWindow.style.maxWidth = '920px';
                    if (previewStatus) previewStatus.style.display = 'none';
                    if (infoDefault) infoDefault.style.display = 'none';
                    if (previewContainer) previewContainer.style.display = 'block';

                    // Update badges
                    if (document.getElementById('badgeTotal')) document.getElementById('badgeTotal').textContent = data.summary.total || 0;
                    if (document.getElementById('badgeDikenali')) document.getElementById('badgeDikenali').textContent = data.summary.dikenali || 0;
                    if (document.getElementById('badgeDuplikat')) document.getElementById('badgeDuplikat').textContent = data.summary.duplikat || 0;
                    if (document.getElementById('badgePerluVerifikasi')) document.getElementById('badgePerluVerifikasi').textContent = data.summary.perlu_verifikasi || 0;
                    if (document.getElementById('badgeDosenBaru')) document.getElementById('badgeDosenBaru').textContent = data.summary.dosen_baru || 0;

                    // Fill table rows
                    if (previewTableBody) {
                        previewTableBody.innerHTML = '';
                        const itemsToShow = (data.items || []).slice(0, 50);
                        itemsToShow.forEach(item => {
                            const tr = document.createElement('tr');
                            const statusBadge = item.status_verifikasi === 'duplikat'
                                ? '<span style="background:#fef08a; color:#854d0e; padding:2px 6px; border-radius:4px; font-size:11px;">Duplikat</span>'
                                : (item.status_verifikasi === 'perlu_verifikasi'
                                    ? '<span style="background:#fed7aa; color:#9a3412; padding:2px 6px; border-radius:4px; font-size:11px;">Verifikasi</span>'
                                    : '<span style="background:#bbf7d0; color:#166534; padding:2px 6px; border-radius:4px; font-size:11px;">OK</span>');

                            tr.innerHTML = `
                                <td style="padding:6px 8px; font-weight:600;">${item.kode_mk || '-'}</td>
                                <td style="padding:6px 8px;">${item.nama_mk || '-'}</td>
                                <td style="padding:6px 8px; text-align:center;">${item.sks || 0}</td>
                                <td style="padding:6px 8px; text-align:center;">${item.semester || 1}</td>
                                <td style="padding:6px 8px; text-align:center;">${item.kelas || '-'}</td>
                                <td style="padding:6px 8px;">${item.prodi || '-'}</td>
                                <td style="padding:6px 8px; text-align:center;">${statusBadge}</td>
                            `;
                            previewTableBody.appendChild(tr);
                        });

                        if ((data.items || []).length > 50) {
                            const trMore = document.createElement('tr');
                            trMore.innerHTML = `<td colspan="7" style="text-align:center; padding:8px; color:#64748b; font-style:italic;">... dan ${(data.items.length - 50)} data lainnya</td>`;
                            previewTableBody.appendChild(trMore);
                        }
                    }

                    // Save verified items JSON for submission
                    if (verifiedInput) {
                        verifiedInput.value = JSON.stringify(data.items);
                    }

                    if (submitBtn) {
                        submitBtn.textContent = 'Konfirmasi & Simpan (' + (data.summary.total || 0) + ' MK)';
                    }
                } else {
                    if (previewStatus) {
                        previewStatus.innerHTML = '<div style="color:#b91c1c;">Gagal membaca pratinjau: ' + (response.message || 'Format tidak sesuai') + '. Anda tetap bisa klik Import Sekarang.</div>';
                    }
                }
            })
            .catch(err => {
                if (previewStatus) {
                    previewStatus.innerHTML = '<div style="color:#64748b;">Pratinjau otomatis tidak tersedia. Silakan klik Import Sekarang langsung.</div>';
                }
            });
        });
    }

    if (formImportEl) {
        formImportEl.addEventListener('submit', function() {
            const btn = document.getElementById('btnSubmitImport');
            if (btn) {
                setTimeout(function() {
                    btn.disabled = true;
                    btn.textContent = 'Memproses Impor...';
                }, 10);
            }
        });
    }
});
</script>
@endpush
