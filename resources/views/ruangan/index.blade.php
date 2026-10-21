@extends('layouts.app')

@section('title', 'APJAD - Manajemen Ruangan')

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
                @if(Auth::user()->role === 'Fakultas')
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
                    <a href="{{ route('ruangan.index') }}" class="sidebar-link active">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 21h18M5 21V7l8-4 8 4v14M8 21v-8h8v8"></path>
                        </svg>
                        <span>Manajemen Ruangan</span>
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
                    <a href="{{ route('penjadwalan.index') }}" class="sidebar-link">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span>Penjadwalan</span>
                    </a>
                @else
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
        <div class="admin-header">
            <h1>Manajemen Ruangan</h1>
            <a href="{{ route('ruangan.create') }}" class="btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Tambah Ruangan
            </a>
        </div>

        <div class="admin-content">
            @if(session('flash_success'))
                <div class="alert alert-success">
                    {{ session('flash_success') }}
                </div>
            @endif

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Tipe</th>
                            <th>Kapasitas</th>
                            <th>Gedung</th>
                            <th>Lantai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ruangans as $ruangan)
                            <tr>
                                <td>
                                    <strong>{{ $ruangan->kode }}</strong>
                                    @if($ruangan->isDefault())
                                        <span style="display: inline-block; font-size: 0.7rem; font-weight: 600; padding: 1px 6px; background: #e0f2fe; color: #0369a1; border-radius: 4px; margin-left: 4px;" title="Ruangan bawaan resmi kampus">Kampus</span>
                                    @endif
                                </td>
                                <td>{{ $ruangan->nama }}</td>
                                <td>
                                    @if($ruangan->tipe === 'Lab')
                                        <span style="display: inline-block; padding: 2px 8px; background: #dbeafe; color: #059669; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">Lab</span>
                                    @elseif($ruangan->tipe === 'Ruang Kelas')
                                        <span style="display: inline-block; padding: 2px 8px; background: #fef3c7; color: #92400e; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">Ruang Kelas</span>
                                    @else
                                        <span style="display: inline-block; padding: 2px 8px; background: #e0e7ff; color: #4338ca; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">Aula</span>
                                    @endif
                                </td>
                                <td>{{ $ruangan->kapasitas }} orang</td>
                                <td>{{ $ruangan->gedung ?? '-' }}</td>
                                <td>{{ $ruangan->lantai ?? '-' }}</td>
                                <td>
                                    @if($ruangan->is_active)
                                        <span style="display: inline-block; padding: 2px 8px; background: #d1fae5; color: #059669; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">Aktif</span>
                                    @else
                                        <span style="display: inline-block; padding: 2px 8px; background: #fee2e2; color: #dc2626; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">Non-Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <a href="{{ route('ruangan.edit', $ruangan) }}" class="btn-sm btn-secondary">
                                            Edit
                                        </a>
                                        <form action="{{ route('ruangan.toggle-status', $ruangan) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status ruangan ini?');">
                                            @csrf
                                            <button type="submit" class="btn-sm" style="background: {{ $ruangan->is_active ? '#f59e0b' : '#10b981' }}; color: white; border: none; border-radius: 4px; padding: 4px 8px; cursor: pointer;">
                                                {{ $ruangan->is_active ? 'Nonaktif' : 'Aktif' }}
                                            </button>
                                        </form>
                                        @if($ruangan->isDefault())
                                            <button type="button" class="btn-sm" disabled title="Ruangan bawaan kampus tidak dapat dihapus (cukup nonaktifkan jika tidak dipakai)" style="opacity: 0.45; cursor: not-allowed; background: #94a3b8; color: white; border: none; border-radius: 4px; padding: 4px 8px;">
                                                Terkunci
                                            </button>
                                        @else
                                            <form action="{{ route('ruangan.destroy', $ruangan) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ruangan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-sm btn-danger">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 40px;">
                                    <p style="color: #64748b; margin: 0;">Belum ada data ruangan.</p>
                                    <a href="{{ route('ruangan.create') }}" style="color: #0860be; text-decoration: none; font-weight: 600;">Tambah Ruangan</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
@endsection
