@extends('layouts.app')

@section('title', 'SIWALAN - Dashboard Prodi')

@section('content')
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="sidebar-top">
            <div class="sidebar-brand">
                <h2>SIWALAN</h2>
                <span class="sidebar-role-badge" style="font-size:10px; color:#94a3b8; text-transform:uppercase; letter-spacing:1px; margin-top:2px;">Admin Prodi</span>
            </div>
            <nav class="sidebar-nav">
                <a href="{{ route('prodi.dashboard') }}" class="sidebar-link active">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    <span>Dashboard</span>
                </a>
                <a href="#section-jadwal" class="sidebar-link" onclick="scrollToSection('section-jadwal')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                        <path d="M3 9h18M9 3v18"></path>
                    </svg>
                    <span>Jadwal Kuliah</span>
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

            <h1 class="page-title">Dashboard Prodi</h1>

            {{-- Stats Cards --}}
            <div class="stats-grid">
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
                    <div class="stat-icon fakultas-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <p class="stat-label">Jadwal Aktif</p>
                        <p class="stat-value">{{ $stats['total_jadwal'] }}</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon prodi-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                            <path d="M2 17l10 5 10-5"></path>
                            <path d="M2 12l10 5 10-5"></path>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <p class="stat-label">Semester Berjalan</p>
                        <p class="stat-value">{{ $stats['semester_aktif'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Jadwal per Hari --}}
            <div id="section-jadwal" style="margin-top: 32px;">
                <div class="tab-header">
                    <h2>Jadwal Kuliah Aktif</h2>
                </div>

                {{-- Filter Semester --}}
                <div style="margin-bottom: 16px; display:flex; gap:10px; flex-wrap:wrap;">
                    <button type="button" class="btn-add semester-filter active" data-sem="all"
                        onclick="filterSemester('all', this)" style="padding:6px 16px; font-size:13px;">
                        Semua
                    </button>
                    @foreach ($semesterList as $sem)
                        <button type="button" class="btn-add semester-filter" data-sem="{{ $sem }}"
                            onclick="filterSemester('{{ $sem }}', this)"
                            style="padding:6px 16px; font-size:13px; background:#475569;">
                            Semester {{ $sem }}
                        </button>
                    @endforeach
                </div>

                @foreach ($hariList as $hari)
                    @if (isset($perHari[$hari]) && $perHari[$hari]->isNotEmpty())
                        <h3 style="margin: 18px 0 8px; font-size:15px; color:#1e3a5f;">{{ $hari }}</h3>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Jam</th>
                                    <th>Kode MK</th>
                                    <th>Mata Kuliah</th>
                                    <th>Semester</th>
                                    <th>SKS</th>
                                    <th>Dosen</th>
                                    <th>Ruangan</th>
                                    <th>Rombel</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($perHari[$hari] as $j)
                                    <tr class="jadwal-row" data-sem="{{ $j->rombel->mataKuliah->semester ?? '' }}">
                                        <td style="white-space:nowrap;">{{ substr($j->jam_mulai, 0, 5) }} – {{ substr($j->jam_selesai, 0, 5) }}</td>
                                        <td><strong>{{ $j->rombel->mataKuliah->kode_mk ?? '-' }}</strong></td>
                                        <td>{{ $j->rombel->mataKuliah->nama_mk ?? '-' }}</td>
                                        <td style="text-align:center;">{{ $j->rombel->mataKuliah->semester ?? '-' }}</td>
                                        <td style="text-align:center;">{{ $j->rombel->mataKuliah->sks ?? '-' }}</td>
                                        <td>{{ $j->dosen->nama ?? '-' }}</td>
                                        <td>{{ $j->ruangan->kode ?? '-' }}</td>
                                        <td>{{ $j->rombel->kode_rombel ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                @endforeach

                @if ($jadwals->isEmpty())
                    <p style="color:#666; padding:18px 0;">Belum ada jadwal aktif.</p>
                @endif
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

function filterSemester(sem, btn) {
    document.querySelectorAll('.semester-filter').forEach(b => {
        b.classList.remove('active');
        b.style.background = '#475569';
    });
    btn.classList.add('active');
    btn.style.background = '';

    document.querySelectorAll('.jadwal-row').forEach(row => {
        if (sem === 'all' || row.dataset.sem == sem) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
@endsection
