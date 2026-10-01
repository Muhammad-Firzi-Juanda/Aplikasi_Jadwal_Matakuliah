@extends('layouts.app')

@section('title', 'SIWALAN - Dashboard Jurusan')

@section('content')
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="sidebar-top">
            <div class="sidebar-brand">
                <h2>SIWALAN</h2>
                <span class="sidebar-role-badge" style="font-size:10px; color:#94a3b8; text-transform:uppercase; letter-spacing:1px; margin-top:2px;">Admin Jurusan</span>
            </div>
            <nav class="sidebar-nav">
                <a href="{{ route('jurusan.dashboard') }}" class="sidebar-link active">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    <span>Dashboard</span>
                </a>
                <a href="#section-dosen" class="sidebar-link" onclick="scrollToSection('section-dosen')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span>Data Dosen</span>
                </a>
                <a href="#section-mk" class="sidebar-link" onclick="scrollToSection('section-mk')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5h16M6.5 15.5l5-3 5 3m-10 0l5-3 5 3M12 12l-9-5h18l-9 5"></path>
                    </svg>
                    <span>Mata Kuliah</span>
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

            <h1 class="page-title">Dashboard Jurusan</h1>

            {{-- Stats Cards --}}
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon dosen-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <p class="stat-label">Total Dosen</p>
                        <p class="stat-value">{{ $stats['total_dosen'] }}</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon mata-kuliah-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 19.5h16M6.5 15.5l5-3 5 3m-10 0l5-3 5 3M12 12l-9-5h18l-9 5"></path>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <p class="stat-label">Total Mata Kuliah</p>
                        <p class="stat-value">{{ $stats['total_mata_kuliah'] }}</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon kelas-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                            <path d="M3 9h18M9 3v18"></path>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <p class="stat-label">Total Rombel</p>
                        <p class="stat-value">{{ $stats['total_rombel'] }}</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon fakultas-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                            <path d="M3 9h18"></path>
                            <circle cx="12" cy="15" r="2"></circle>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <p class="stat-label">Jadwal Aktif</p>
                        <p class="stat-value">{{ $stats['total_jadwal'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Data Dosen --}}
            <div id="section-dosen" style="margin-top: 32px;">
                <div class="tab-header">
                    <h2>Data Dosen</h2>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIDN</th>
                            <th>Nama Dosen</th>
                            <th>Jabatan</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($dosens as $i => $d)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td><strong>{{ $d->nidn ?? '-' }}</strong></td>
                                <td>{{ $d->nama }}</td>
                                <td>{{ $d->jabatan ?? '-' }}</td>
                                <td>{{ $d->email ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="text-align:center; padding:24px;">Belum ada data dosen.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mata Kuliah per Semester --}}
            <div id="section-mk" style="margin-top: 40px;">
                <div class="tab-header">
                    <h2>Mata Kuliah per Semester</h2>
                </div>
                @forelse ($mataKuliahs as $semester => $listMk)
                    <h3 style="margin: 18px 0 8px; font-size:15px; color:#1e3a5f;">Semester {{ $semester }}</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Kode MK</th>
                                <th>Nama Mata Kuliah</th>
                                <th>SKS</th>
                                <th>Tipe</th>
                                <th>Dosen Ketua</th>
                                <th>Dosen Anggota</th>
                                <th>Jumlah Kelas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($listMk as $mk)
                                <tr>
                                    <td><strong>{{ $mk->kode_mk }}</strong></td>
                                    <td>{{ $mk->nama_mk }}</td>
                                    <td>{{ $mk->sks }}</td>
                                    <td>{{ $mk->tipe }}</td>
                                    <td>{{ $mk->dosenKetua->nama ?? '-' }}</td>
                                    <td>{{ $mk->dosenAnggota->nama ?? '-' }}</td>
                                    <td>{{ $mk->jumlah_kelas }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @empty
                    <p style="color:#666; padding:18px 0;">Belum ada data mata kuliah.</p>
                @endforelse
            </div>
        </section>
    </main>
</div>

{{-- MODAL: EDIT PROFILE --}}
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

<script>
function openModal(id) { document.getElementById(id).classList.add('show'); }
function closeModal(id) { document.getElementById(id).classList.remove('show'); }
function scrollToSection(id) {
    event.preventDefault();
    document.getElementById(id).scrollIntoView({ behavior: 'smooth' });
}
</script>
@endsection
