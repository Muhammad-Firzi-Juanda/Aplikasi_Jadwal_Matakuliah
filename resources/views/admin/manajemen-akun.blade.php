@extends('layouts.app')

@section('title', 'APJAD - Manajemen Akun')

@section('content')
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="sidebar-top">
            <div class="sidebar-brand">
                <h2>APJAD</h2>
                @if(Auth::user()->role === 'Fakultas')
                    <span class="sidebar-role-badge" style="font-size:10px; color:#94a3b8; text-transform:uppercase; letter-spacing:1px; margin-top:2px;">Admin Fakultas</span>
                @elseif(Auth::user()->role === 'Jurusan')
                    <span class="sidebar-role-badge" style="font-size:10px; color:#94a3b8; text-transform:uppercase; letter-spacing:1px; margin-top:2px;">Admin Jurusan</span>
                @elseif(Auth::user()->role === 'Prodi')
                    <span class="sidebar-role-badge" style="font-size:10px; color:#94a3b8; text-transform:uppercase; letter-spacing:1px; margin-top:2px;">Admin Prodi</span>
                @else
                    <span class="sidebar-role-badge" style="font-size:10px; color:#94a3b8; text-transform:uppercase; letter-spacing:1px; margin-top:2px;">Super Admin</span>
                @endif
            </div>
            <nav class="sidebar-nav">
                @if (Auth::user()->role === \App\Enums\UserRole::SUPER_ADMIN)
                <a href="{{ route('dashboard') }}" class="sidebar-link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    <span>Dashboard</span>
                </a>
                @endif
                <a href="{{ route('manajemen-akun') }}" class="sidebar-link active">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span>Manajemen Akun</span>
                </a>
                @if (Auth::user()->role === \App\Enums\UserRole::SUPER_ADMIN)
                <a href="{{ route('jadwal-kuliah.index') }}" class="sidebar-link">
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

            <h1 class="page-title">Manajemen User</h1>

            <!-- User Management Table -->
            <div class="table-card">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Nama</th>
                            <th style="width: 35%;">Email</th>
                            <th style="width: 20%;">Role</th>
                            <th style="width: 20%;">
                                <div class="action-header-cell">
                                    <span>Aksi</span>
                                    <button type="button" class="btn-add-account" onclick="openModal('modalTambah')" title="Tambah Akun Baru">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="12" y1="5" x2="12" y2="19"></line>
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                        </svg>
                                    </button>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $u)
                            <tr>
                                <td>{{ $u->nama }}</td>
                                <td>{{ $u->email }}</td>
                                <td>
                                    <span class="role-badge role-{{ strtolower(str_replace(' ', '-', $u->role)) }}">
                                        {{ $u->role }}
                                    </span>
                                    @if ($u->role === 'Prodi' && $u->prodi)
                                        <div style="font-size: 0.8rem; color: #0284c7; margin-top: 3px; font-weight: 600;">
                                            {{ $u->prodi->nama }} ({{ $u->prodi->kode }})
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons-group">
                                        <button type="button" class="btn-action edit-btn" title="Edit Data Akun"
                                            data-id="{{ $u->id }}"
                                            data-nama="{{ $u->nama }}"
                                            data-email="{{ $u->email }}"
                                            data-role="{{ $u->role }}"
                                            data-prodi-id="{{ $u->prodi_id ?? '' }}"
                                            onclick="handleEditUserClick(this)">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                        </button>

                                        <button type="button" class="btn-action delete-btn" title="Hapus User"
                                            onclick="openDeleteModal({{ $u->id }})">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
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
                                <td colspan="4" style="padding: 24px; color: #64748b;">Belum ada data user.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<!-- MODAL 1: TAMBAH AKUN BARU -->
<div class="modal-backdrop" id="modalTambah">
    <div class="modal-window">
        <div class="modal-header-blue">
            <h3>Tambah Akun Baru</h3>
        </div>
        <div class="modal-body">
            <form action="{{ route('users.store') }}" method="POST" class="modal-form">
                @csrf

                <div class="modal-form-row">
                    <label for="create_nama">Nama</label>
                    <div class="modal-input-container">
                        <input type="text" id="create_nama" name="nama" required>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="create_email">E-mail</label>
                    <div class="modal-input-container">
                        <input type="email" id="create_email" name="email" required>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="create_role">Role</label>
                    <div class="modal-input-container">
                        <select id="create_role" name="role" required onchange="toggleProdiSelect('create')">
                            @if (Auth::user()->role === \App\Enums\UserRole::SUPER_ADMIN)
                                <option value="Admin">Admin</option>
                            @endif
                            <option value="Fakultas">Fakultas</option>
                            <option value="Jurusan">Jurusan</option>
                            <option value="Prodi">Prodi</option>
                        </select>
                    </div>
                </div>

                <div class="modal-form-row" id="row_create_prodi" style="display: none;">
                    <label for="create_prodi_id">Program Studi</label>
                    <div class="modal-input-container">
                        <select id="create_prodi_id" name="prodi_id">
                            <option value="">-- Pilih Program Studi --</option>
                            @foreach ($prodis ?? [] as $p)
                                <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->kode }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="create_password">Password</label>
                    <div class="modal-input-container">
                        <input type="password" id="create_password" name="password" required>
                        <button type="button" class="btn-toggle-password" onclick="togglePasswordVisibilityModal('create_password', this)">
                            <svg id="create_password_eye" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="create_password_confirmation">Konfirmasi Password</label>
                    <div class="modal-input-container">
                        <input type="password" id="create_password_confirmation" name="password_confirmation" required>
                        <button type="button" class="btn-toggle-password" onclick="togglePasswordVisibilityModal('create_password_confirmation', this)">
                            <svg id="create_password_confirmation_eye" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="modal-footer-buttons">
                    <button type="button" class="btn-modal-cancel" onclick="closeModal('modalTambah')">Batal</button>
                    <button type="submit" class="btn-modal-submit">Buat</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL 2: EDIT DATA AKUN -->
<div class="modal-backdrop" id="modalEdit">
    <div class="modal-window">
        <div class="modal-header-blue">
            <h3>Edit Data Akun</h3>
        </div>
        <div class="modal-body">
            <form id="formEditUser" action="{{ route('users.update', ['user' => 'USER_ID']) }}" data-base-url="{{ url('/users') }}" method="POST" class="modal-form">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_user_id" name="user_id">

                <div class="modal-form-row">
                    <label for="edit_nama">Nama</label>
                    <div class="modal-input-container">
                        <input type="text" id="edit_nama" name="nama" required>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="edit_email">E-mail</label>
                    <div class="modal-input-container">
                        <input type="email" id="edit_email" name="email" required>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="edit_role">Role</label>
                    <div class="modal-input-container">
                        <select id="edit_role" name="role" required onchange="toggleProdiSelect('edit')">
                            @if (Auth::user()->role === \App\Enums\UserRole::SUPER_ADMIN)
                                <option value="Admin">Admin</option>
                            @endif
                            <option value="Fakultas">Fakultas</option>
                            <option value="Jurusan">Jurusan</option>
                            <option value="Prodi">Prodi</option>
                        </select>
                    </div>
                </div>

                <div class="modal-form-row" id="row_edit_prodi" style="display: none;">
                    <label for="edit_prodi_id">Program Studi</label>
                    <div class="modal-input-container">
                        <select id="edit_prodi_id" name="prodi_id">
                            <option value="">-- Pilih Program Studi --</option>
                            @foreach ($prodis ?? [] as $p)
                                <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->kode }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="edit_password">Password</label>
                    <div class="modal-input-container">
                        <input type="password" id="edit_password" name="password" placeholder="Kosongkan jika tidak diubah">
                        <button type="button" class="btn-toggle-password" onclick="togglePasswordVisibilityModal('edit_password', this)">
                            <svg id="edit_password_eye" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="edit_password_confirmation">Konfirmasi Password</label>
                    <div class="modal-input-container">
                        <input type="password" id="edit_password_confirmation" name="password_confirmation" placeholder="Kosongkan jika tidak diubah">
                        <button type="button" class="btn-toggle-password" onclick="togglePasswordVisibilityModal('edit_password_confirmation', this)">
                            <svg id="edit_password_confirmation_eye" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="modal-footer-buttons">
                    <button type="button" class="btn-modal-cancel" onclick="closeModal('modalEdit')">Batal</button>
                    <button type="submit" class="btn-modal-submit">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL 3: EDIT PROFILE -->
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

<!-- MODAL 4: KONFIRMASI HAPUS -->
<div class="modal-backdrop" id="modalHapus">
    <div class="modal-confirm-card">
        <h4 class="confirm-question">Yakin ingin menghapus user ini?</h4>
        <form id="formDeleteUser" action="{{ route('users.destroy', ['user' => 'USER_ID']) }}" data-base-url="{{ url('/users') }}" method="POST">
            @csrf
            @method('DELETE')
            <input type="hidden" id="delete_user_id" name="user_id">

            <div class="confirm-actions">
                <button type="submit" class="btn-confirm-yes">Ya</button>
                <button type="button" class="btn-confirm-cancel" onclick="closeModal('modalHapus')">Batal</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
    }
    if (modalId === 'modalTambah') {
        toggleProdiSelect('create');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
    }
}

function toggleProdiSelect(type) {
    const roleSelect = document.getElementById(type + '_role');
    const prodiRow = document.getElementById('row_' + type + '_prodi');
    const prodiSelect = document.getElementById(type + '_prodi_id');
    
    if (roleSelect && prodiRow && prodiSelect) {
        if (roleSelect.value === 'Prodi') {
            prodiRow.style.display = 'flex';
            prodiSelect.required = true;
        } else {
            prodiRow.style.display = 'none';
            prodiSelect.required = false;
            prodiSelect.value = '';
        }
    }
}

function handleEditUserClick(btn) {
    const userData = {
        id: btn.getAttribute('data-id'),
        nama: btn.getAttribute('data-nama'),
        email: btn.getAttribute('data-email'),
        role: btn.getAttribute('data-role'),
        prodi_id: btn.getAttribute('data-prodi-id')
    };
    openEditModal(userData);
}

function openEditModal(userData) {
    if (typeof userData === 'string') {
        try {
            userData = JSON.parse(userData);
        } catch (e) {
            console.error('Invalid user data', e);
        }
    }

    if ("{{ Auth::user()->role }}" === "Admin" && userData && userData.id == {{ Auth::id() }}) {
        alert("Admin tidak dapat mengedit akun sendiri melalui tabel. Silakan gunakan menu Edit Profile di sidebar.");
        return;
    }

    const form = document.getElementById('formEditUser');
    if (form && userData && userData.id) {
        const baseUrl = form.getAttribute('data-base-url') || '/users';
        form.action = baseUrl.replace(/\/$/, '') + '/' + userData.id;
    }
    
    const idEl = document.getElementById('edit_user_id');
    if (idEl) idEl.value = (userData && userData.id) || '';
    
    const namaEl = document.getElementById('edit_nama');
    if (namaEl) namaEl.value = (userData && userData.nama) || '';
    
    const emailEl = document.getElementById('edit_email');
    if (emailEl) emailEl.value = (userData && userData.email) || '';
    
    const roleEl = document.getElementById('edit_role');
    if (roleEl) roleEl.value = (userData && userData.role) || 'Prodi';
    
    const prodiSelect = document.getElementById('edit_prodi_id');
    if (prodiSelect) {
        prodiSelect.value = (userData && userData.prodi_id !== null && userData.prodi_id !== undefined && userData.prodi_id !== '') ? String(userData.prodi_id) : '';
    }
    
    toggleProdiSelect('edit');
    
    const pwEl = document.getElementById('edit_password');
    if (pwEl) pwEl.value = '';
    
    const pwConfEl = document.getElementById('edit_password_confirmation');
    if (pwConfEl) pwConfEl.value = '';
    
    openModal('modalEdit');
}

function openDeleteModal(userId) {
    const form = document.getElementById('formDeleteUser');
    if (form && userId) {
        const baseUrl = form.getAttribute('data-base-url') || '/users';
        form.action = baseUrl.replace(/\/$/, '') + '/' + userId;
    }
    const input = document.getElementById('delete_user_id');
    if (input) input.value = userId;
    openModal('modalHapus');
}

function togglePasswordVisibilityModal(fieldId, button) {
    const passwordInput = document.getElementById(fieldId);
    if (!passwordInput) return;
    const eyeIcon = document.getElementById(fieldId + '_eye');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        if (eyeIcon) {
            eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
        }
    } else {
        passwordInput.type = 'password';
        if (eyeIcon) {
            eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
        }
    }
}
</script>
@endpush
