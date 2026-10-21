@extends('layouts.app')

@section('title', 'APJAD - Edit Ruangan')

@section('content')
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="sidebar-top">
            <div class="sidebar-brand">
                <h2>APJAD</h2>
                <span class="sidebar-role-badge" style="font-size:10px; color:#94a3b8; text-transform:uppercase; letter-spacing:1px; margin-top:2px;">Super Admin</span>
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
                <a href="{{ route('ruangan.index') }}" class="sidebar-link active">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 21h18M5 21V7l8-4 8 4v14M8 21v-8h8v8"></path>
                    </svg>
                    <span>Manajemen Ruangan</span>
                </a>
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
        <div class="admin-header">
            <h1>Edit Ruangan</h1>
            <a href="{{ route('ruangan.index') }}" class="btn-secondary">Kembali</a>
        </div>

        <div class="admin-content">
            @if($errors->any())
                <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
                    <ul style="margin: 0; padding-left: 20px; color: #dc2626;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('ruangan.update', $ruangan) }}" method="POST" class="form">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="kode">Kode Ruangan</label>
                    <input type="text" id="kode" name="kode" required value="{{ old('kode', $ruangan->kode) }}" placeholder="Contoh: R1-FTTK">
                    @error('kode')
                        <div style="color: #ef4444; font-size: 0.875rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="nama">Nama Ruangan</label>
                    <input type="text" id="nama" name="nama" required value="{{ old('nama', $ruangan->nama) }}" placeholder="Contoh: Ruang Kelas 1">
                    @error('nama')
                        <div style="color: #ef4444; font-size: 0.875rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="tipe">Tipe Ruangan</label>
                    <select id="tipe" name="tipe" required>
                        <option value="">Pilih Tipe</option>
                        <option value="Lab" {{ $ruangan->tipe === 'Lab' ? 'selected' : '' }}>Laboratorium</option>
                        <option value="Ruang Kelas" {{ $ruangan->tipe === 'Ruang Kelas' ? 'selected' : '' }}>Ruang Kelas</option>
                        <option value="Aula" {{ $ruangan->tipe === 'Aula' ? 'selected' : '' }}>Aula</option>
                        <option value="Lainnya" {{ $ruangan->tipe === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('tipe')
                        <div style="color: #ef4444; font-size: 0.875rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="kapasitas">Kapasitas</label>
                    <input type="number" id="kapasitas" name="kapasitas" required min="1" max="500" value="{{ old('kapasitas', $ruangan->kapasitas) }}" placeholder="Contoh: 40">
                    @error('kapasitas')
                        <div style="color: #ef4444; font-size: 0.875rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="gedung">Gedung</label>
                    <input type="text" id="gedung" name="gedung" value="{{ old('gedung', $ruangan->gedung) }}" placeholder="Contoh: FTTK">
                    @error('gedung')
                        <div style="color: #ef4444; font-size: 0.875rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="lantai">Lantai</label>
                    <input type="number" id="lantai" name="lantai" min="1" max="10" value="{{ old('lantai', $ruangan->lantai) }}" placeholder="Contoh: 1">
                    @error('lantai')
                        <div style="color: #ef4444; font-size: 0.875rem; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" {{ $ruangan->is_active ? 'checked' : '' }}>
                        Ruangan Aktif
                    </label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Update</button>
                    <a href="{{ route('ruangan.index') }}" class="btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </main>
</div>
@endsection
