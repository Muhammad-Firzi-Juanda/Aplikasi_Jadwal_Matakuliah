@extends('layouts.app')

@section('title', 'APJAD - Dashboard Fakultas')

@section('content')
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="sidebar-top">
            <div class="sidebar-brand">
                <h2>APJAD</h2>
                <span class="sidebar-role-badge" style="font-size:10px; color:#94a3b8; text-transform:uppercase; letter-spacing:1px; margin-top:2px;">Admin Fakultas</span>
            </div>
            <nav class="sidebar-nav">
                <a href="#section-dashboard" class="sidebar-link active" onclick="showSection(event, 'section-dashboard', this)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    <span>Dashboard</span>
                </a>
                <a href="#section-prodi" class="sidebar-link" onclick="showSection(event, 'section-prodi', this)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                        <path d="M2 17l10 5 10-5"></path>
                        <path d="M2 12l10 5 10-5"></path>
                    </svg>
                    <span>Program Studi</span>
                </a>
                <a href="#section-jadwal" class="sidebar-link" onclick="showSection(event, 'section-jadwal', this)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                        <path d="M3 9h18M9 3v18"></path>
                    </svg>
                    <span>Rekap Jadwal</span>
                </a>
                <a href="{{ route('jadwal-kuliah.index') }}" class="sidebar-link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    <span>Data Mata Kuliah</span>
                </a>
                <a href="{{ route('penjadwalan.index') }}" class="sidebar-link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span>Penjadwalan Otomatis</span>
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

            <h1 class="page-title">Dashboard Fakultas</h1>

            {{-- Stats Cards --}}
            <div class="stats-grid content-section" id="section-dashboard">
                <div class="stat-card">
                    <div class="stat-icon prodi-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                            <path d="M2 17l10 5 10-5"></path>
                            <path d="M2 12l10 5 10-5"></path>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <p class="stat-label">Total Program Studi</p>
                        <p class="stat-value">{{ $stats['total_prodi'] }}</p>
                    </div>
                </div>

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
                        <p class="stat-label">Jadwal Aktif</p>
                        <p class="stat-value">{{ $stats['total_jadwal'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Daftar Program Studi --}}
            <div id="section-prodi" class="content-section" style="margin-top: 32px; display: none;">
                <div class="tab-header">
                    <h2>Daftar Program Studi</h2>
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
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Program Studi</th>
                                <th>Kaprodi</th>
                                <th>Fakultas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($prodis as $i => $p)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td><strong>{{ $p->kode }}</strong></td>
                                    <td>{{ $p->nama }}</td>
                                    <td>{{ $p->kaprodi ?? '-' }}</td>
                                    <td>{{ $p->fakultas->nama ?? '-' }}</td>
                                    <td>
                                        <div class="action-buttons-group">
                                            <button type="button" class="btn-action edit-btn" title="Edit" onclick="openProdiModal({{ json_encode($p) }})">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                            </button>
                                            <button type="button" class="btn-action delete-btn" title="Hapus" onclick="openHapusModal({{ $p->id }})">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6l-1 14H6L5 6"></path>
                                                    <path d="M10 11v6M14 11v6"></path>
                                                    <path d="M9 6V4h6v2"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" style="text-align:center; padding:24px;">Belum ada data program studi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Rekap Jadwal --}}
            <div id="section-jadwal" class="content-section" style="margin-top: 40px; display: none;">
                <div class="tab-header">
                    <h2>Rekap Jadwal Aktif</h2>
                </div>
                @php $hariList = ['Senin','Selasa','Rabu','Kamis','Jumat']; @endphp
                @foreach ($hariList as $hari)
                    @if (isset($jadwals[$hari]) && $jadwals[$hari]->isNotEmpty())
                        <h3 style="margin: 18px 0 8px; font-size:15px; color:#1e3a5f;">{{ $hari }}</h3>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Jam</th>
                                    <th>Mata Kuliah</th>
                                    <th>Dosen</th>
                                    <th>Ruangan</th>
                                    <th>Rombel</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jadwals[$hari] as $j)
                                    <tr>
                                        <td style="white-space:nowrap;">{{ substr($j->jam_mulai, 0, 5) }} – {{ substr($j->jam_selesai, 0, 5) }}</td>
                                        <td>{{ $j->rombel->mataKuliah->nama_mk ?? '-' }}</td>
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
                    <div class="table-card" style="margin-top: 15px;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Hari</th>
                                    <th>Jam</th>
                                    <th>Mata Kuliah</th>
                                    <th>Dosen</th>
                                    <th>Ruangan</th>
                                    <th>Rombel</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" style="text-align:center; padding:24px; color:#666;">Belum ada jadwal aktif.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </section>
    </main>
</div>

{{-- MODAL: TAMBAH/EDIT PRODI --}}
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
                    <label for="prodi_nama">Nama Program Studi</label>
                    <div class="modal-input-container">
                        <input type="text" id="prodi_nama" name="nama" required placeholder="Teknik Informatika">
                    </div>
                </div>
                <div class="modal-form-row">
                    <label for="prodi_kode">Kode</label>
                    <div class="modal-input-container">
                        <input type="text" id="prodi_kode" name="kode" placeholder="TI, SI, dll">
                    </div>
                </div>
                <div class="modal-form-row">
                    <label for="prodi_fakultas_id">Fakultas</label>
                    <div class="modal-input-container">
                        <select id="prodi_fakultas_id" name="fakultas_id" required>
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
                        <input type="text" id="prodi_kaprodi" name="kaprodi" placeholder="Dr. Nama Kaprodi, M.Sc">
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

{{-- MODAL: HAPUS PRODI --}}
<div class="modal-backdrop" id="modalHapusProdi">
    <div class="modal-window" style="max-width:420px;">
        <div class="modal-header-blue" style="background:#ef4444;">
            <h3>Hapus Program Studi</h3>
        </div>
        <div class="modal-body">
            <p style="color:#374151; margin-bottom:20px;">Apakah Anda yakin ingin menghapus program studi ini? Tindakan ini tidak dapat dibatalkan.</p>
            <form id="formHapusProdi" method="POST" class="modal-form">
                @csrf
                @method('DELETE')
                <div class="modal-footer-buttons">
                    <button type="button" class="btn-modal-cancel" onclick="closeModal('modalHapusProdi')">Batal</button>
                    <button type="submit" class="btn-modal-submit" style="background:#ef4444;">Hapus</button>
                </div>
            </form>
        </div>
    </div>
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

<script>
function openModal(id) { document.getElementById(id).classList.add('show'); }
function closeModal(id) { document.getElementById(id).classList.remove('show'); }

function showSection(event, id, element) {
    if (event) event.preventDefault();

    document.querySelectorAll('.content-section').forEach(function(sec) {
        sec.style.display = 'none';
    });

    let target = document.getElementById(id);
    if (target) {
        target.style.display = target.classList.contains('stats-grid') ? 'grid' : 'block';
    }

    if (element) {
        document.querySelectorAll('.sidebar-nav .sidebar-link').forEach(function(link) {
            link.classList.remove('active');
        });
        element.classList.add('active');
    }

    // Update hash without scrolling
    history.replaceState(null, null, '#' + id);
}

// On page load: check URL hash and activate correct section
document.addEventListener('DOMContentLoaded', function () {
    const hash = window.location.hash; // e.g. "#section-prodi"
    if (hash) {
        const sectionId = hash.replace('#', '');
        const section = document.getElementById(sectionId);
        const matchingLink = document.querySelector('.sidebar-nav a[href="#' + sectionId + '"]');
        if (section) {
            // Hide all sections first
            document.querySelectorAll('.content-section').forEach(function(sec) {
                sec.style.display = 'none';
            });
            // Show target section
            section.style.display = section.classList.contains('stats-grid') ? 'grid' : 'block';
            // Update active link
            document.querySelectorAll('.sidebar-nav .sidebar-link').forEach(function(link) {
                link.classList.remove('active');
            });
            if (matchingLink) matchingLink.classList.add('active');
        }
    }
});

function openProdiModal(prodi = null) {
    const form = document.getElementById('formProdi');
    const title = document.getElementById('modalProdiTitle');
    const idInput = document.getElementById('prodi_id');

    form.reset();
    idInput.value = '';
    form.action = '{{ route("prodi.store") }}';

    let oldMethod = form.querySelector('input[name="_method"]');
    if (oldMethod) oldMethod.remove();

    if (prodi) {
        title.textContent = 'Edit Program Studi';
        idInput.value = prodi.id;
        document.getElementById('prodi_nama').value = prodi.nama || '';
        document.getElementById('prodi_kode').value = prodi.kode || '';
        document.getElementById('prodi_kaprodi').value = prodi.kaprodi || '';
        document.getElementById('prodi_fakultas_id').value = prodi.fakultas_id || '';
        form.action = '/prodi/' + prodi.id;
        let method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'PUT';
        form.appendChild(method);
    } else {
        title.textContent = 'Tambah Program Studi';
    }

    openModal('modalProdi');
}

function openHapusModal(prodiId) {
    const form = document.getElementById('formHapusProdi');
    form.action = '/prodi/' + prodiId;
    openModal('modalHapusProdi');
}
</script>
@endsection
