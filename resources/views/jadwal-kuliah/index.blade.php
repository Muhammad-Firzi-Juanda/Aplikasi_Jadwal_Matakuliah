@extends('layouts.app')

@section('title', 'SIWALAN - Mengelola Jadwal Kuliah')

@section('content')
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="sidebar-top">
            <div class="sidebar-brand">
                <h2>SIWALAN</h2>
            </div>
<nav class="sidebar-nav">
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
                <a href="{{ route('jadwal-kuliah.index') }}" class="sidebar-link active">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                        <path d="M3 9h18M9 3v18"></path>
                    </svg>
                    <span>Jadwal Kuliah</span>
                </a>
                <a href="{{ route('penjadwalan.index') }}" class="sidebar-link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span>Penjadwalan</span>
                </a>
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
                    <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;">✕</button>
                </div>
            @endif

            @if (session('flash_error'))
                <div class="flash-alert error">
                    <span>{{ session('flash_error') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;">✕</button>
                </div>
            @endif

            @if ($errors->any())
                <div class="flash-alert error">
                    <span>{{ $errors->first() }}</span>
                    <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;">✕</button>
                </div>
            @endif

            <h1 class="page-title">Mengelola Jadwal Kuliah</h1>

            <!-- Tabs Navigation -->
            <div class="tabs-container">
                <div class="tabs-header">
                    <button class="tab-btn active" onclick="switchTab('fakultas')">Fakultas</button>
                    <button class="tab-btn" onclick="switchTab('prodi')">Program Studi</button>
                </div>

                <!-- Fakultas Tab Content -->
                <div id="fakultas-tab" class="tab-content active">
                    <div class="tab-header">
                        <h2>Data Fakultas</h2>
                        <button type="button" class="btn-add" onclick="openFakultasModal()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Tambah Fakultas
                        </button>
                    </div>

                    <div class="table-card">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="width: 40%;">Nama Fakultas</th>
                                    <th style="width: 20%;">Kode</th>
                                    <th style="width: 30%;">Dekan</th>
                                    <th style="width: 10%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($fakultas as $f)
                                    <tr>
                                        <td>{{ $f->nama }}</td>
                                        <td>{{ $f->kode ?? '-' }}</td>
                                        <td>{{ $f->dekan ?? '-' }}</td>
                                        <td>
                                            <div class="action-buttons-group">
                                                <button type="button" class="btn-action edit-btn" title="Edit" onclick="openFakultasModal({{ json_encode($f) }})">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                    </svg>
                                                </button>
                                                <button type="button" class="btn-action delete-btn" title="Hapus" onclick="openHapusModal('fakultas', {{ $f->id }})">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                                                        <line x1="10" y1="11" x2="14" y2="15"></line>
                                                        <line x1="14" y1="11" x2="10" y2="15"></line>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" style="padding: 24px; color: #64748b;">Belum ada data fakultas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 40px;">
                        <h2 style="margin: 0;">Mata Kuliah Fakultas</h2>
                        <button type="button" class="btn-add" onclick="openMataKuliahFakultasModal()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Tambah MK Fakultas
                        </button>
                    </div>
                    <div class="table-card">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="width: 20%;">Kode</th>
                                    <th style="width: 25%;">Nama Mata Kuliah</th>
                                    <th style="width: 10%;">SKS</th>
                                    <th style="width: 10%;">Semester</th>
                                    <th style="width: 20%;">Fakultas</th>
                                    <th style="width: 15%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($mataKuliahFakultas as $mk)
                                    <tr>
                                        <td>{{ $mk->kode }}</td>
                                        <td>{{ $mk->nama }}</td>
                                        <td>{{ $mk->sks }}</td>
                                        <td>{{ $mk->semester }}</td>
                                        <td>Fakultas ID: {{ $mk->level_id }}</td>
                                        <td>
                                            <div class="action-buttons-group">
                                                <button type="button" class="btn-action edit-btn" title="Edit" onclick="openMataKuliahFakultasModal({{ json_encode($mk) }})">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                    </svg>
                                                </button>
                                                <button type="button" class="btn-action delete-btn" title="Hapus" onclick="openHapusModal('mata_kuliah_fakultas', {{ $mk->id }})">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                                                        <line x1="10" y1="11" x2="14" y2="15"></line>
                                                        <line x1="14" y1="11" x2="10" y2="15"></line>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="padding: 24px; color: #64748b;">Belum ada data mata kuliah fakultas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Prodi Tab Content -->
                <div id="prodi-tab" class="tab-content">
                    <div class="tab-header">
                        <h2>Data Program Studi</h2>
                        <button type="button" class="btn-add" onclick="openProdiModal()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Tambah Prodi
                        </button>
                    </div>

                    <div class="table-card">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="width: 25%;">Nama Prodi</th>
                                    <th style="width: 15%;">Kode</th>
                                    <th style="width: 30%;">Fakultas</th>
                                    <th style="width: 20%;">Kaprodi</th>
                                    <th style="width: 10%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($prodi as $p)
                                    <tr>
                                        <td>{{ $p->nama }}</td>
                                        <td>{{ $p->kode ?? '-' }}</td>
                                        <td>{{ $p->fakultas->nama ?? '-' }}</td>
                                        <td>{{ $p->kaprodi ?? '-' }}</td>
                                        <td>
                                            <div class="action-buttons-group">
                                                <button type="button" class="btn-action edit-btn" title="Edit" onclick="openProdiModal({{ json_encode($p) }})"> <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                    </svg>
                                                </button>
                                                <button type="button" class="btn-action delete-btn" title="Hapus" onclick="openHapusModal('prodi', {{ $p->id }})"> <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"> <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                                                        <line x1="10" y1="11" x2="14" y2="15"></line>
                                                        <line x1="14" y1="11" x2="10" y2="15"></line>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" style="padding: 24px; color: #64748b;">Belum ada data prodi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 40px;">
                        <h2 style="margin: 0;">Mata Kuliah Program Studi</h2>
                        <button type="button" class="btn-add" onclick="openMataKuliahProdiModal()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Tambah MK Prodi
                        </button>
                    </div>
                    <div class="table-card">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="width: 20%;">Kode</th>
                                    <th style="width: 25%;">Nama Mata Kuliah</th>
                                    <th style="width: 10%;">SKS</th>
                                    <th style="width: 10%;">Semester</th>
                                    <th style="width: 20%;">Prodi</th>
                                    <th style="width: 15%;">Aksi</th>
                                </tr>
                            </thead>
<tbody>
                                @forelse ($mataKuliahProdi as $mk)
                                    <tr>
                                        <td>{{ $mk->kode }}</td>
                                        <td>{{ $mk->nama }}</td>
                                        <td>{{ $mk->sks }}</td>
                                        <td>{{ $mk->semester }}</td>
                                        <td>Informatika</td>
                                        <td>
                                            <div class="action-buttons-group">
                                                <button type="button" class="btn-action edit-btn" title="Edit" onclick="openMataKuliahProdiModal({{ json_encode($mk) }})">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                    </svg>
                                                </button>
                                                <button type="button" class="btn-action delete-btn" title="Hapus" onclick="openHapusModal('mata_kuliah_prodi', {{ $mk->id }})">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                                                        <line x1="10" y1="11" x2="14" y2="15"></line>
                                                        <line x1="14" y1="11" x2="10" y2="15"></line>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="padding: 24px; color: #64748b;">Belum ada data mata kuliah prodi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

<!-- =========================================================================
     MODAL: TAMBAH/EDIT FAKULTAS
     ========================================================================= -->
<div class="modal-backdrop" id="modalFakultas">
    <div class="modal-window">
        <div class="modal-header-blue">
            <h3 id="modalFakultasTitle">Tambah Fakultas</h3>
        </div>
        <div class="modal-body">
            <form id="formFakultas" action="{{ route('fakultas.store') }}" method="POST" class="modal-form">
                @csrf
                <input type="hidden" id="fakultas_id" name="fakultas_id">

                <div class="modal-form-row">
                    <label for="fakultas_nama">Nama Fakultas</label>
                    <div class="modal-input-container">
                        <input type="text" id="fakultas_nama" name="nama" required>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="fakultas_kode">Kode</label>
                    <div class="modal-input-container">
                        <input type="text" id="fakultas_kode" name="kode" placeholder="FT, FEB, FH, dll">
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="fakultas_dekan">Dekan</label>
                    <div class="modal-input-container">
                        <input type="text" id="fakultas_dekan" name="dekan" placeholder="Dr. Nama Dekan, M.Si">
                    </div>
                </div>

                <div class="modal-footer-buttons">
                    <button type="button" class="btn-modal-cancel" onclick="closeModal('modalFakultas')">Batal</button>
                    <button type="submit" class="btn-modal-submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =========================================================================
     MODAL: TAMBAH/EDIT PRODI
     ========================================================================= -->
<div class="modal-backdrop" id="modalProdi">
    <div class="modal-window">
        <div class="modal-header-blue">
            <h3 id="modalProdiTitle">Tambah Program Studi</h3>
        </div>
        <div class="modal-body">
            <form id="formProdi" action="{{ route('prodi.store') }}" method="POST" class="modal-form">
                @csrf
                <input type="hidden" id="prodi_id" name="prodi_id">

                <div class="modal-form-row">
                    <label for="prodi_nama">Nama Prodi</label>
                    <div class="modal-input-container">
                        <input type="text" id="prodi_nama" name="nama" required>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="prodi_kode">Kode</label>
                    <div class="modal-input-container">
                        <input type="text" id="prodi_kode" name="kode" placeholder="TI, MNJ, HUK, dll">
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="prodi_fakultas">Fakultas</label>
                    <div class="modal-input-container">
                        <select id="prodi_fakultas" name="fakultas_id" required>
                            <option value="">Pilih Fakultas</option>
                            @foreach ($fakultas as $f)
                                <option value="{{ $f->id }}">{{ $f->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="prodi_kaprodi">Kaprodi</label>
                    <div class="modal-input-container">
                        <input type="text" id="prodi_kaprodi" name="kaprodi" placeholder="Dr. Nama Kaprodi, M.Kom">
                    </div>
                </div>

                <div class="modal-footer-buttons">
                    <button type="button" class="btn-modal-cancel" onclick="closeModal('modalProdi')">Batal</button>
                    <button type="submit" class="btn-modal-submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =========================================================================
     MODAL: TAMBAH/EDIT MATA KULIAH (FAKULTAS)
     ========================================================================= -->
<div class="modal-backdrop" id="modalMataKuliahFakultas">
    <div class="modal-window">
        <div class="modal-header-blue">
            <h3 id="modalMataKuliahFakultasTitle">Tambah Mata Kuliah Fakultas</h3>
        </div>
        <div class="modal-body">
            <form id="formMataKuliahFakultas" action="{{ route('mata-kuliah.store') }}" method="POST" class="modal-form">
                @csrf
                <input type="hidden" id="mk_fakultas_id" name="mata_kuliah_id">
                <input type="hidden" name="tipe" value="Fakultas">
                <input type="hidden" name="level_id" id="mk_fakultas_level_id" value="1">

                <div class="modal-form-row">
                    <label for="mk_fakultas_nama">Nama Mata Kuliah</label>
                    <div class="modal-input-container">
                        <input type="text" id="mk_fakultas_nama" name="nama" required>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="mk_fakultas_kode">Kode</label>
                    <div class="modal-input-container">
                        <input type="text" id="mk_fakultas_kode" name="kode" required placeholder="PAI101, BIN101">
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="mk_fakultas_sks">SKS</label>
                    <div class="modal-input-container">
                        <select id="mk_fakultas_sks" name="sks" required>
                            <option value="">Pilih SKS</option>
                            <option value="1">1 SKS</option>
                            <option value="2">2 SKS</option>
                            <option value="3">3 SKS</option>
                            <option value="4">4 SKS</option>
                            <option value="5">5 SKS</option>
                            <option value="6">6 SKS</option>
                        </select>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="mk_fakultas_semester">Semester</label>
                    <div class="modal-input-container">
                        <select id="mk_fakultas_semester" name="semester" required>
                            <option value="">Pilih Semester</option>
                            @foreach ($semesterGanjil as $sem)
                                <option value="{{ $sem }}">Semester {{ $sem }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="mk_fakultas_fakultas">Fakultas</label>
                    <div class="modal-input-container">
                        <select id="mk_fakultas_fakultas" name="level_id" required onchange="document.getElementById('mk_fakultas_level_id').value = this.value">
                            <option value="">Pilih Fakultas</option>
                            @foreach ($fakultas as $f)
                                <option value="{{ $f->id }}">{{ $f->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer-buttons">
                    <button type="button" class="btn-modal-cancel" onclick="closeModal('modalMataKuliahFakultas')">Batal</button>
                    <button type="submit" class="btn-modal-submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =========================================================================
     MODAL: TAMBAH/EDIT MATA KULIAH (PRODI)
     ========================================================================= -->
<div class="modal-backdrop" id="modalMataKuliahProdi">
    <div class="modal-window">
        <div class="modal-header-blue">
            <h3 id="modalMataKuliahProdiTitle">Tambah Mata Kuliah Prodi</h3>
        </div>
        <div class="modal-body">
            <form id="formMataKuliahProdi" action="{{ route('mata-kuliah.store') }}" method="POST" class="modal-form">
                @csrf
                <input type="hidden" id="mk_prodi_id" name="mata_kuliah_id">
                <input type="hidden" name="tipe" value="Prodi">
                <input type="hidden" name="level_id" id="mk_prodi_level_id">

                <div class="modal-form-row">
                    <label for="mk_prodi_nama">Nama Mata Kuliah</label>
                    <div class="modal-input-container">
                        <input type="text" id="mk_prodi_nama" name="nama" required>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="mk_prodi_kode">Kode</label>
                    <div class="modal-input-container">
                        <input type="text" id="mk_prodi_kode" name="kode" required placeholder="TIF101, TIF202">
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="mk_prodi_sks">SKS</label>
                    <div class="modal-input-container">
                        <select id="mk_prodi_sks" name="sks" required>
                            <option value="">Pilih SKS</option>
                            <option value="1">1 SKS</option>
                            <option value="2">2 SKS</option>
                            <option value="3">3 SKS</option>
                            <option value="4">4 SKS</option>
                            <option value="5">5 SKS</option>
                            <option value="6">6 SKS</option>
                        </select>
                    </div>
                </div>

<div class="modal-form-row">
                    <label for="mk_prodi_semester">Semester</label>
                    <div class="modal-input-container">
                        <select id="mk_prodi_semester" name="semester" required>
                            <option value="">Pilih Semester</option>
                            @foreach ($semesterGanjil as $sem)
                                <option value="{{ $sem }}">Semester {{ $sem }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

<div class="modal-form-row">
                    <label for="mk_prodi_prodi">Program Studi</label>
                    <div class="modal-input-container">
                        <select id="mk_prodi_prodi" name="level_id" required onchange="document.getElementById('mk_prodi_level_id').value = this.value">
                            <option value="">Pilih Prodi</option>
                            @if ($prodiInformatika)
                                <option value="{{ $prodiInformatika->id }}">{{ preg_replace('/^S1\s+/i', '', $prodiInformatika->nama) }}</option>
                            @else
                                @foreach ($prodi as $p)
                                    <option value="{{ $p->id }}">{{ preg_replace('/^S1\s+/i', '', $p->nama) }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="modal-footer-buttons">
                    <button type="button" class="btn-modal-cancel" onclick="closeModal('modalMataKuliahProdi')">Batal</button>
                    <button type="submit" class="btn-modal-submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =========================================================================
     MODAL: KONFIRMASI HAPUS
     ========================================================================= -->
<div class="modal-backdrop" id="modalHapusJadwal">
    <div class="modal-confirm-card">
        <h4 class="confirm-question" id="modalHapusTitle">Yakin ingin menghapus data ini?</h4>
        <form id="formDeleteData" action="#" method="POST">
            @csrf
            @method('DELETE')
            <input type="hidden" id="delete_data_id" name="data_id">

            <div class="confirm-actions">
                <button type="submit" class="btn-confirm-yes">Ya</button>
                <button type="button" class="btn-confirm-cancel" onclick="closeModal('modalHapusJadwal')">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
function switchTab(tabName) {
    const tabs = document.querySelectorAll('.tab-content');
    const tabBtns = document.querySelectorAll('.tab-btn');
    
    tabs.forEach(tab => tab.classList.remove('active'));
    tabBtns.forEach(btn => btn.classList.remove('active'));
    
    document.getElementById(`${tabName}-tab`).classList.add('active');
    document.querySelector(`.tab-btn[onclick="switchTab('${tabName}')"]`).classList.add('active');
}

function openModal(modalId) {
    document.getElementById(modalId).classList.add('show');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('show');
}

function openFakultasModal(fakultasData = null) {
    const form = document.getElementById('formFakultas');
    const title = document.getElementById('modalFakultasTitle');
    const idField = document.getElementById('fakultas_id');
    const namaField = document.getElementById('fakultas_nama');
    const kodeField = document.getElementById('fakultas_kode');
    const dekanField = document.getElementById('fakultas_dekan');

    if (fakultasData) {
        // Edit mode
        title.textContent = 'Edit Fakultas';
        idField.value = fakultasData.id;
        namaField.value = fakultasData.nama;
        kodeField.value = fakultasData.kode || '';
        dekanField.value = fakultasData.dekan || '';
        form.action = "{{ url('/fakultas') }}" + '/' + fakultasData.id;
        form.method = 'POST';
        form.innerHTML = form.innerHTML.replace('@method('PUT')', '');
        form.insertAdjacentHTML('afterbegin', '<input type="hidden" name="_method" value="PUT">');
    } else {
        // Add mode
        title.textContent = 'Tambah Fakultas';
        idField.value = '';
        namaField.value = '';
        kodeField.value = '';
        dekanField.value = '';
        form.action = "{{ route('fakultas.store') }}";
        form.method = 'POST';
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.remove();
    }

    openModal('modalFakultas');
}

function openProdiModal(prodiData = null) {
    const form = document.getElementById('formProdi');
    const title = document.getElementById('modalProdiTitle');
    const idField = document.getElementById('prodi_id');
    const namaField = document.getElementById('prodi_nama');
    const kodeField = document.getElementById('prodi_kode');
    const fakultasField = document.getElementById('prodi_fakultas');
    const kaprodiField = document.getElementById('prodi_kaprodi');

    if (prodiData) {
        // Edit mode
        title.textContent = 'Edit Program Studi';
        idField.value = prodiData.id;
        namaField.value = prodiData.nama;
        kodeField.value = prodiData.kode || '';
        fakultasField.value = prodiData.fakultas_id || '';
        kaprodiField.value = prodiData.kaprodi || '';
        form.action = "{{ url('/prodi') }}" + '/' + prodiData.id;
        form.method = 'POST';
        form.innerHTML = form.innerHTML.replace('@method('PUT')', '');
        form.insertAdjacentHTML('afterbegin', '<input type="hidden" name="_method" value="PUT">');
    } else {
        // Add mode
        title.textContent = 'Tambah Program Studi';
        idField.value = '';
        namaField.value = '';
        kodeField.value = '';
        fakultasField.value = '';
        kaprodiField.value = '';
        form.action = "{{ route('prodi.store') }}";
        form.method = 'POST';
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.remove();
    }

    openModal('modalProdi');
}

function openMataKuliahFakultasModal(mkData = null) {
    const form = document.getElementById('formMataKuliahFakultas');
    const title = document.getElementById('modalMataKuliahFakultasTitle');
    const idField = document.getElementById('mk_fakultas_id');
    const namaField = document.getElementById('mk_fakultas_nama');
    const kodeField = document.getElementById('mk_fakultas_kode');
    const sksField = document.getElementById('mk_fakultas_sks');
    const semesterField = document.getElementById('mk_fakultas_semester');
    const fakultasField = document.getElementById('mk_fakultas_fakultas');

    if (mkData) {
        // Edit mode
        title.textContent = 'Edit Mata Kuliah Fakultas';
        idField.value = mkData.id;
        namaField.value = mkData.nama;
        kodeField.value = mkData.kode || '';
        sksField.value = mkData.sks || '';
        semesterField.value = mkData.semester || '';
        fakultasField.value = mkData.level_id || '';
        form.action = "{{ url('/mata-kuliah') }}" + '/' + mkData.id;
        form.method = 'POST';
        form.insertAdjacentHTML('afterbegin', '<input type="hidden" name="_method" value="PUT">');
    } else {
        // Add mode
        title.textContent = 'Tambah Mata Kuliah Fakultas';
        idField.value = '';
        namaField.value = '';
        kodeField.value = '';
        sksField.value = '';
        semesterField.value = '';
        fakultasField.value = '';
        form.action = "{{ route('mata-kuliah.store') }}";
        form.method = 'POST';
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.remove();
    }

    openModal('modalMataKuliahFakultas');
}

function openMataKuliahProdiModal(mkData = null) {
    const form = document.getElementById('formMataKuliahProdi');
    const title = document.getElementById('modalMataKuliahProdiTitle');
    const idField = document.getElementById('mk_prodi_id');
    const namaField = document.getElementById('mk_prodi_nama');
    const kodeField = document.getElementById('mk_prodi_kode');
    const sksField = document.getElementById('mk_prodi_sks');
    const semesterField = document.getElementById('mk_prodi_semester');
    const prodiField = document.getElementById('mk_prodi_prodi');

    if (mkData) {
        // Edit mode
        title.textContent = 'Edit Mata Kuliah Prodi';
        idField.value = mkData.id;
        namaField.value = mkData.nama;
        kodeField.value = mkData.kode || '';
        sksField.value = mkData.sks || '';
        semesterField.value = mkData.semester || '';
        prodiField.value = mkData.level_id || '';
        form.action = "{{ url('/mata-kuliah') }}" + '/' + mkData.id;
        form.method = 'POST';
        form.insertAdjacentHTML('afterbegin', '<input type="hidden" name="_method" value="PUT">');
    } else {
        // Add mode
        title.textContent = 'Tambah Mata Kuliah Prodi';
        idField.value = '';
        namaField.value = '';
        kodeField.value = '';
        sksField.value = '';
        semesterField.value = '';
        prodiField.value = '';
        form.action = "{{ route('mata-kuliah.store') }}";
        form.method = 'POST';
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.remove();
    }

    openModal('modalMataKuliahProdi');
}

function openHapusModal(type, id) {
    const form = document.getElementById('formDeleteData');
    const title = document.getElementById('modalHapusTitle');
    const idField = document.getElementById('delete_data_id');
    
    let route = '';
    let message = '';
    
    switch (type) {
        case 'fakultas':
            route = "{{ url('/fakultas') }}" + '/' + id;
            message = 'Yakin ingin menghapus fakultas ini?';
            break;
        case 'prodi':
            route = "{{ url('/prodi') }}" + '/' + id;
            message = 'Yakin ingin menghapus program studi ini?';
            break;
        case 'mata_kuliah_fakultas':
        case 'mata_kuliah_prodi':
            route = "{{ url('/mata-kuliah') }}" + '/' + id;
            message = 'Yakin ingin menghapus mata kuliah ini?';
            break;
    }
    
    title.textContent = message;
    idField.name = type.includes('mata_kuliah') ? 'mata_kuliah_id' : type + '_id';
    idField.value = id;
    form.action = route;
    
openModal('modalHapusJadwal');
}
</script>

<!-- =========================================================================
     MODAL: EDIT PROFILE
     ========================================================================= -->
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

                <div class="modal-footer-buttons">
                    <button type="button" class="btn-modal-cancel" onclick="closeModal('modalEditProfile')">Batal</button>
                    <button type="submit" class="btn-modal-submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

