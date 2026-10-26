@extends('layouts.app')

@section('title', 'APJAD - Mengelola Jadwal Kuliah')

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
                    <a href="{{ route('fakultas.dashboard') }}#section-jadwal" class="sidebar-link">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"></path>
                        </svg>
                        <span>Rekap Jadwal</span>
                    </a>
                    <a href="{{ route('jadwal-kuliah.index') }}" class="sidebar-link active">
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

            <h1 class="page-title">Mengelola Jadwal Kuliah</h1>

            <!-- Tabs Navigation -->
            <div class="tabs-container">
                <div class="tabs-header">
                    <button class="tab-btn active" onclick="switchTab('rekap-jadwal')">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle; margin-right:4px;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        Jadwal Kuliah Terjadwal
                    </button>
                    @if(Auth::user()->role === 'Super Admin')
                        <button class="tab-btn" onclick="switchTab('prodi')">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle; margin-right:4px;"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                            Data Program Studi & MK
                        </button>
                    @endif
                    <button class="tab-btn" onclick="switchTab('fakultas')">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle; margin-right:4px;"><path d="M3 21h18M5 21V7l8-4 8 4v14M8 21v-8h8v8"/></svg>
                        Fakultas & MK Fakultas
                    </button>
                </div>

                <!-- 1. TAB JADWAL KULIAH TERJADWAL (LIVE SYNC PENJADWALAN) -->
                <div id="rekap-jadwal-tab" class="tab-content active">
                    <div style="background:#eff6ff; border:1.5px solid #bfdbfe; border-radius:12px; padding:16px 20px; margin-bottom:24px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
                        <div style="display:flex; align-items:center; gap:14px;">
                            <div style="width:42px; height:42px; border-radius:10px; background:#1d4ed8; color:#ffffff; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            </div>
                            <div>
                                <h3 style="margin:0; font-size:1.05rem; font-weight:700; color:#1e3a8a;">Data Jadwal Tersinkronisasi Otomatis</h3>
                                <p style="margin:3px 0 0 0; font-size:0.85rem; color:#3b82f6;">
                                    Tersambung langsung dengan Sistem Penjadwalan: <strong>{{ $totalJadwalTerjadwal }} kelas</strong> aktif terjadwal.
                                </p>
                            </div>
                        </div>
                        <div style="display:flex; gap:10px; flex-wrap:wrap;">
                            <a href="{{ route('penjadwalan.index') }}" class="btn-add" style="text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                Buka Sistem Penjadwalan
                            </a>
                        </div>
                    </div>

                    {{-- Bar Filter Periode & Filter Jurusan/Prodi --}}
                    <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:14px 18px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span style="font-size:13px; font-weight:700; color:#334155;">Periode:</span>
                            <div style="display:inline-flex; background:#f1f5f9; padding:3px; border-radius:8px;">
                                <button type="button" id="btnToggleGanjilSync" onclick="switchPeriodeSync('ganjil')" style="border:none; padding:5px 14px; border-radius:6px; font-size:12.5px; font-weight:700; cursor:pointer; background:#0860be; color:#fff; transition:all 0.2s;">Ganjil (1,3,5,7)</button>
                                <button type="button" id="btnToggleGenapSync" onclick="switchPeriodeSync('genap')" style="border:none; padding:5px 14px; border-radius:6px; font-size:12.5px; font-weight:700; cursor:pointer; background:transparent; color:#64748b; transition:all 0.2s;">Genap (2,4,6,8)</button>
                            </div>
                        </div>

                        @if(Auth::user()->role === 'Prodi' && Auth::user()->prodi)
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span style="font-size:13px; font-weight:700; color:#334155;">Program Studi:</span>
                                <span style="padding:5px 14px; border-radius:16px; font-size:12.5px; font-weight:700; background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd;">
                                    {{ Auth::user()->prodi->nama }}
                                </span>
                            </div>
                        @else
                            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                <span style="font-size:13px; font-weight:700; color:#334155;">Filter Prodi:</span>
                                <button type="button" class="btn-filter-sync active" onclick="filterJadwalSyncProdi('all', this)" style="padding:5px 12px; border-radius:16px; font-size:12px; font-weight:600; cursor:pointer; border:1px solid #0860be; background:#0860be; color:#fff; transition:all 0.2s;">Semua Prodi</button>
                                @foreach ($prodi as $p)
                                    @php
                                        $pLabel = $p->nama;
                                        if ($p->kode && !str_contains($p->nama, '(')) {
                                            $pLabel .= ' (' . $p->kode . ')';
                                        }
                                    @endphp
                                    <button type="button" class="btn-filter-sync" onclick="filterJadwalSyncProdi('{{ strtolower($p->nama) }}', this)" style="padding:5px 12px; border-radius:16px; font-size:12px; font-weight:600; cursor:pointer; border:1px solid #cbd5e1; background:#ffffff; color:#475569; transition:all 0.2s;">{{ $pLabel }}</button>
                                @endforeach
                                @foreach ($extraProdiButtons ?? [] as $extraKey => $extraName)
                                    @php
                                        $extraLabel = $extraName;
                                        if (str_contains(strtolower($extraName), 'elektro') && !str_contains($extraName, '(')) {
                                            $extraLabel .= ' (EEL)';
                                        } elseif (str_contains(strtolower($extraName), 'informatik') && !str_contains($extraName, '(')) {
                                            $extraLabel .= ' (INF)';
                                        }
                                    @endphp
                                    <button type="button" class="btn-filter-sync" onclick="filterJadwalSyncProdi('{{ strtolower($extraName) }}', this)" style="padding:5px 12px; border-radius:16px; font-size:12px; font-weight:600; cursor:pointer; border:1px solid #cbd5e1; background:#ffffff; color:#475569; transition:all 0.2s;">{{ $extraLabel }}</button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Daftar Semester Ganjil --}}
                    <div id="container-sync-ganjil">
                        @foreach ($semesterGanjil as $sem)
                            @php
                                $barisSem = $jadwal[$sem] ?? collect();
                            @endphp
                            <div class="semester-section-sync" style="margin-bottom:20px; border:1.5px solid #e2e8f0; border-radius:10px; background:#fff; overflow:hidden;">
                                <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 18px; background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <span style="width:28px; height:28px; border-radius:6px; background:#0860be; color:#fff; display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-size:13px;">{{ $sem }}</span>
                                        <h3 style="margin:0; font-size:1rem; color:#0f172a; font-weight:700;">Semester {{ $sem }}</h3>
                                    </div>
                                    <span style="font-size:12px; padding:3px 10px; border-radius:12px; font-weight:600; {{ $barisSem->isNotEmpty() ? 'background:#e0f2fe; color:#0369a1;' : 'background:#f1f5f9; color:#94a3b8;' }}">
                                        {{ $barisSem->count() }} Kelas
                                    </span>
                                </div>
                                <div style="padding:14px 18px;">
                                    @if ($barisSem->isEmpty())
                                        <div style="text-align:center; padding:20px; color:#94a3b8; font-size:0.9rem;">Belum ada jadwal yang di-generate untuk Semester {{ $sem }}.</div>
                                    @else
                                        <div class="table-card" style="box-shadow:none; border:none; margin:0;">
                                            <table class="data-table">
                                                <thead>
                                                    <tr>
                                                        <th style="width:11%;">Waktu</th>
                                                        <th style="width:10%;">Kode MK</th>
                                                        <th style="width:18%;">Mata Kuliah</th>
                                                        <th style="width:5%; text-align:center;">SKS</th>
                                                        <th style="width:8%; text-align:center;">Kelas</th>
                                                        <th style="width:12%;">Program Studi</th>
                                                        <th style="width:10%;">Ruangan</th>
                                                        <th style="width:13%;">Dosen Ketua</th>
                                                        <th style="width:13%;">Dosen Anggota</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($barisSem as $item)
                                                        @php
                                                            $itemProdi = strtolower($item->prodi ?? '');
                                                            $isPraktik = !empty($item->butuh_lab) 
                                                                || stripos($item->nama_mk, 'prak') !== false 
                                                                || stripos($item->nama_mk, 'lab') !== false 
                                                                || ($item->tipe_ruangan ?? '') === 'Lab'
                                                                || stripos($item->kode_ruangan ?? '', 'lab') !== false;
                                                        @endphp
                                                        <tr class="jadwal-sync-row" data-prodi="{{ $itemProdi }}" data-kode-mk="{{ strtolower($item->kode_mk) }}">
                                                            <td>
                                                                <span style="font-weight:700; color:#0f172a;">{{ $item->hari }}</span><br>
                                                                <span style="font-size:12px; color:#475569;">{{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }}</span>
                                                            </td>
                                                            <td><strong style="color:#0860be;">{{ $item->kode_mk }}</strong></td>
                                                            <td>
                                                                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                                                    <strong>{{ $item->nama_mk }}</strong>
                                                                    @if ($isPraktik)
                                                                        <span style="display:inline-flex; align-items:center; gap:4px; padding:2px 8px; border-radius:10px; font-size:11px; font-weight:700; background:#fef3c7; color:#92400e; border:1px solid #fde68a;" title="Kelas Praktik / Laboratorium">
                                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10 2v7.31L4.35 18A2 2 0 0 0 6 21h12a2 2 0 0 0 1.65-3L14 9.31V2"/></svg>
                                                                            Praktik
                                                                        </span>
                                                                    @else
                                                                        <span style="display:inline-flex; align-items:center; gap:4px; padding:2px 8px; border-radius:10px; font-size:11px; font-weight:700; background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe;" title="Kelas Teori">
                                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                                                                            Teori
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                            <td style="text-align:center;"><span style="font-weight:700;">{{ $item->sks }}</span></td>
                                                            <td style="text-align:center;">
                                                                <span style="display:inline-block; padding:2px 8px; border-radius:4px; background:#f1f5f9; font-weight:600; font-size:12px;">
                                                                    {{ $item->rombel_kelas ?: ($item->mk_kelas ?: 'Kls '.$item->nomor_rombel) }}
                                                                </span>
                                                                <div style="font-size:11px; color:#64748b; font-weight:500; margin-top:2px;">
                                                                    Kuota: <strong style="color:#0f172a;">{{ $item->rombel_kapasitas ?? ($item->kapasitas_per_kelas ?? 40) }}</strong> Mhs
                                                                </div>
                                                            </td>
                                                            <td>
                                                                @if (str_contains($itemProdi, 'elektro') || str_starts_with(strtoupper($item->kode_mk), 'EEL'))
                                                                    <span style="display:inline-block; padding:2px 8px; border-radius:10px; background:#f3e8ff; color:#7e22ce; font-size:11.5px; font-weight:600;">Teknik Elektro (EEL)</span>
                                                                @elseif (str_contains($itemProdi, 'informatik') || str_starts_with(strtoupper($item->kode_mk), 'INF'))
                                                                    <span style="display:inline-block; padding:2px 8px; border-radius:10px; background:#dbeafe; color:#1d4ed8; font-size:11.5px; font-weight:600;">Teknik Informatika (INF)</span>
                                                                @else
                                                                    <span style="display:inline-block; padding:2px 8px; border-radius:10px; background:#f1f5f9; color:#475569; font-size:11.5px; font-weight:600;">{{ $item->prodi ?: 'Umum' }}</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <span style="display:inline-block; padding:3px 8px; border-radius:6px; {{ (stripos($item->kode_ruangan ?? '', 'lab') !== false || ($item->tipe_ruangan ?? '') === 'Lab') ? 'background:#fef2f2; color:#b91c1c; border:1px solid #fecaca;' : 'background:#f8fafc; border:1px solid #cbd5e1; color:#0f172a;' }} font-weight:600; font-size:12px;">
                                                                    {{ $item->kode_ruangan ?: 'Tanpa Ruangan' }}
                                                                </span>
                                                                @if (! empty($item->kapasitas_ruangan))
                                                                    <div style="font-size:11px; color:#64748b; font-weight:500; margin-top:2px;">
                                                                        Kapasitas: {{ $item->kapasitas_ruangan }} Kursi
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            <td style="font-size:12px;">
                                                                {{ $item->nama_dosen_ketua ?: ($item->nama_dosen_jadwal ?: '-') }}
                                                            </td>
                                                            <td style="font-size:12px;">
                                                                @if (! empty($item->nama_dosen_anggota))
                                                                    {{ $item->nama_dosen_anggota }}
                                                                @else
                                                                    <span style="color:#94a3b8;">-</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Daftar Semester Genap --}}
                    <div id="container-sync-genap" style="display:none;">
                        @foreach ($semesterGenap as $sem)
                            @php
                                $barisSem = $jadwal[$sem] ?? collect();
                            @endphp
                            <div class="semester-section-sync" style="margin-bottom:20px; border:1.5px solid #e2e8f0; border-radius:10px; background:#fff; overflow:hidden;">
                                <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 18px; background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <span style="width:28px; height:28px; border-radius:6px; background:#0860be; color:#fff; display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-size:13px;">{{ $sem }}</span>
                                        <h3 style="margin:0; font-size:1rem; color:#0f172a; font-weight:700;">Semester {{ $sem }}</h3>
                                    </div>
                                    <span style="font-size:12px; padding:3px 10px; border-radius:12px; font-weight:600; {{ $barisSem->isNotEmpty() ? 'background:#e0f2fe; color:#0369a1;' : 'background:#f1f5f9; color:#94a3b8;' }}">
                                        {{ $barisSem->count() }} Kelas
                                    </span>
                                </div>
                                <div style="padding:14px 18px;">
                                    @if ($barisSem->isEmpty())
                                        <div style="text-align:center; padding:20px; color:#94a3b8; font-size:0.9rem;">Belum ada jadwal yang di-generate untuk Semester {{ $sem }}.</div>
                                    @else
                                        <div class="table-card" style="box-shadow:none; border:none; margin:0;">
                                            <table class="data-table">
                                                <thead>
                                                    <tr>
                                                        <th style="width:11%;">Waktu</th>
                                                        <th style="width:10%;">Kode MK</th>
                                                        <th style="width:18%;">Mata Kuliah</th>
                                                        <th style="width:5%; text-align:center;">SKS</th>
                                                        <th style="width:8%; text-align:center;">Kelas</th>
                                                        <th style="width:12%;">Program Studi</th>
                                                        <th style="width:10%;">Ruangan</th>
                                                        <th style="width:13%;">Dosen Ketua</th>
                                                        <th style="width:13%;">Dosen Anggota</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($barisSem as $item)
                                                        @php
                                                            $itemProdi = strtolower($item->prodi ?? '');
                                                            $isPraktik = !empty($item->butuh_lab) 
                                                                || stripos($item->nama_mk, 'prak') !== false 
                                                                || stripos($item->nama_mk, 'lab') !== false 
                                                                || ($item->tipe_ruangan ?? '') === 'Lab'
                                                                || stripos($item->kode_ruangan ?? '', 'lab') !== false;
                                                        @endphp
                                                        <tr class="jadwal-sync-row" data-prodi="{{ $itemProdi }}" data-kode-mk="{{ strtolower($item->kode_mk) }}">
                                                            <td>
                                                                <span style="font-weight:700; color:#0f172a;">{{ $item->hari }}</span><br>
                                                                <span style="font-size:12px; color:#475569;">{{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }}</span>
                                                            </td>
                                                            <td><strong style="color:#0860be;">{{ $item->kode_mk }}</strong></td>
                                                            <td>
                                                                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                                                    <strong>{{ $item->nama_mk }}</strong>
                                                                    @if ($isPraktik)
                                                                        <span style="display:inline-flex; align-items:center; gap:4px; padding:2px 8px; border-radius:10px; font-size:11px; font-weight:700; background:#fef3c7; color:#92400e; border:1px solid #fde68a;" title="Kelas Praktik / Laboratorium">
                                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10 2v7.31L4.35 18A2 2 0 0 0 6 21h12a2 2 0 0 0 1.65-3L14 9.31V2"/></svg>
                                                                            Praktik
                                                                        </span>
                                                                    @else
                                                                        <span style="display:inline-flex; align-items:center; gap:4px; padding:2px 8px; border-radius:10px; font-size:11px; font-weight:700; background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe;" title="Kelas Teori">
                                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                                                                            Teori
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                            <td style="text-align:center;"><span style="font-weight:700;">{{ $item->sks }}</span></td>
                                                            <td style="text-align:center;">
                                                                <span style="display:inline-block; padding:2px 8px; border-radius:4px; background:#f1f5f9; font-weight:600; font-size:12px;">
                                                                    {{ $item->rombel_kelas ?: ($item->mk_kelas ?: 'Kls '.$item->nomor_rombel) }}
                                                                </span>
                                                                <div style="font-size:11px; color:#64748b; font-weight:500; margin-top:2px;">
                                                                    Kuota: <strong style="color:#0f172a;">{{ $item->rombel_kapasitas ?? ($item->kapasitas_per_kelas ?? 40) }}</strong> Mhs
                                                                </div>
                                                            </td>
                                                            <td>
                                                                @if (str_contains($itemProdi, 'elektro') || str_starts_with(strtoupper($item->kode_mk), 'EEL'))
                                                                    <span style="display:inline-block; padding:2px 8px; border-radius:10px; background:#f3e8ff; color:#7e22ce; font-size:11.5px; font-weight:600;">Teknik Elektro (EEL)</span>
                                                                @elseif (str_contains($itemProdi, 'informatik') || str_starts_with(strtoupper($item->kode_mk), 'INF'))
                                                                    <span style="display:inline-block; padding:2px 8px; border-radius:10px; background:#dbeafe; color:#1d4ed8; font-size:11.5px; font-weight:600;">Teknik Informatika (INF)</span>
                                                                @else
                                                                    <span style="display:inline-block; padding:2px 8px; border-radius:10px; background:#f1f5f9; color:#475569; font-size:11.5px; font-weight:600;">{{ $item->prodi ?: 'Umum' }}</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <span style="display:inline-block; padding:3px 8px; border-radius:6px; {{ (stripos($item->kode_ruangan ?? '', 'lab') !== false || ($item->tipe_ruangan ?? '') === 'Lab') ? 'background:#fef2f2; color:#b91c1c; border:1px solid #fecaca;' : 'background:#f8fafc; border:1px solid #cbd5e1; color:#0f172a;' }} font-weight:600; font-size:12px;">
                                                                    {{ $item->kode_ruangan ?: 'Tanpa Ruangan' }}
                                                                </span>
                                                                @if (! empty($item->kapasitas_ruangan))
                                                                    <div style="font-size:11px; color:#64748b; font-weight:500; margin-top:2px;">
                                                                        Kapasitas: {{ $item->kapasitas_ruangan }} Kursi
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            <td style="font-size:12px;">
                                                                {{ $item->nama_dosen_ketua ?: ($item->nama_dosen_jadwal ?: '-') }}
                                                            </td>
                                                            <td style="font-size:12px;">
                                                                @if (! empty($item->nama_dosen_anggota))
                                                                    {{ $item->nama_dosen_anggota }}
                                                                @else
                                                                    <span style="color:#94a3b8;">-</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 2. Fakultas Tab Content -->
                <div id="fakultas-tab" class="tab-content">
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

                    <div class="tab-header" style="margin-top: 40px;">
                        <h2>Mata Kuliah Fakultas</h2>
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
                @if(Auth::user()->role === 'Super Admin')
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

                    <div class="tab-header" style="margin-top: 40px;">
                        <div>
                            <h2>Mata Kuliah Program Studi</h2>
                            <p style="margin:4px 0 0 0; font-size:0.85rem; color:#64748b;">Kelola mata kuliah spesifik jurusan dengan pemisahan filter prodi.</p>
                        </div>
                        <button type="button" class="btn-add" onclick="openMataKuliahProdiModal()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Tambah MK Prodi
                        </button>
                    </div>

                    {{-- Filter Bar Khusus MK Prodi --}}
                    <div style="display:flex; align-items:center; gap:8px; margin: 15px 0 18px 0; flex-wrap:wrap; background:#f8fafc; padding:10px 14px; border-radius:10px; border:1px solid #e2e8f0;">
                        <span style="font-size:12.5px; font-weight:700; color:#475569; display:inline-flex; align-items:center; gap:6px;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                            Filter Jurusan / Prodi:
                        </span>
                        <button type="button" class="btn-filter-mk-prodi active" onclick="filterMkProdi('all', this)" style="padding:5px 12px; border-radius:16px; font-size:12px; font-weight:600; cursor:pointer; border:1px solid #0860be; background:#0860be; color:#fff; transition:all 0.2s;">Semua Prodi</button>
                        @foreach ($prodi as $p)
                            <button type="button" class="btn-filter-mk-prodi" onclick="filterMkProdi('{{ $p->id }}', this)" style="padding:5px 12px; border-radius:16px; font-size:12px; font-weight:600; cursor:pointer; border:1px solid #cbd5e1; background:#ffffff; color:#475569; transition:all 0.2s;">{{ $p->nama }} ({{ $p->kode ?? '-' }})</button>
                        @endforeach
                        @foreach ($extraProdiButtons ?? [] as $extraKey => $extraName)
                            @php
                                $extraBtnLabel = $extraName;
                                if (str_contains(strtolower($extraName), 'elektro') && !str_contains($extraName, '(')) {
                                    $extraBtnLabel .= ' (EEL)';
                                } elseif (str_contains(strtolower($extraName), 'informatik') && !str_contains($extraName, '(')) {
                                    $extraBtnLabel .= ' (INF)';
                                }
                            @endphp
                            <button type="button" class="btn-filter-mk-prodi" onclick="filterMkProdi('{{ $extraKey }}', this)" style="padding:5px 12px; border-radius:16px; font-size:12px; font-weight:600; cursor:pointer; border:1px solid #cbd5e1; background:#ffffff; color:#475569; transition:all 0.2s;">{{ $extraBtnLabel }}</button>
                        @endforeach
                    </div>

                    <div class="table-card">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="width: 18%;">Kode</th>
                                    <th style="width: 27%;">Nama Mata Kuliah</th>
                                    <th style="width: 10%;">SKS</th>
                                    <th style="width: 10%;">Semester</th>
                                    <th style="width: 20%;">Program Studi</th>
                                    <th style="width: 15%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($mataKuliahProdi as $mk)
                                    <tr class="mk-prodi-row" 
                                        data-prodi-key="{{ $mk->filter_prodi_key ?? $mk->level_id }}" 
                                        data-kode-mk="{{ strtolower($mk->kode) }}"
                                        data-prodi-nama="{{ strtolower($mk->nama_prodi) }}">
                                        <td><strong>{{ $mk->kode }}</strong></td>
                                        <td>{{ $mk->nama }}</td>
                                        <td>{{ $mk->sks }} SKS</td>
                                        <td>Semester {{ $mk->semester }}</td>
                                        <td>
                                            @if (str_contains(strtolower($mk->nama_prodi), 'elektro') || str_starts_with(strtoupper($mk->kode), 'EEL'))
                                                <span style="display:inline-block; padding:3px 10px; background:#f3e8ff; color:#7e22ce; border-radius:12px; font-size:0.82rem; font-weight:600;">Teknik Elektro (EEL)</span>
                                            @elseif (str_contains(strtolower($mk->nama_prodi), 'informatik') || str_starts_with(strtoupper($mk->kode), 'INF') || str_starts_with(strtoupper($mk->kode), 'TIF'))
                                                <span style="display:inline-block; padding:3px 10px; background:#dbeafe; color:#1d4ed8; border-radius:12px; font-size:0.82rem; font-weight:600;">Teknik Informatika (INF)</span>
                                            @else
                                                <span style="display:inline-block; padding:3px 10px; background:#f1f5f9; color:#475569; border-radius:12px; font-size:0.82rem; font-weight:600;">{{ $mk->nama_prodi }}</span>
                                            @endif
                                        </td>
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
                @endif
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
                            @foreach ($semesterAll as $sem)
                                <option value="{{ $sem }}">Semester {{ $sem }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-form-row">
                    <label for="mk_prodi_prodi">Program Studi</label>
                    <div class="modal-input-container">
                        <select id="mk_prodi_prodi" name="level_id" required onchange="document.getElementById('mk_prodi_level_id').value = this.value">
                            <option value="">Pilih Program Studi</option>
                            @foreach ($prodi as $p)
                                <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->kode ?? '-' }})</option>
                            @endforeach
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

// Toggle Periode Ganjil/Genap untuk Jadwal Terjadwal Sync
function switchPeriodeSync(mode) {
    const btnGanjil = document.getElementById('btnToggleGanjilSync');
    const btnGenap = document.getElementById('btnToggleGenapSync');
    const cGanjil = document.getElementById('container-sync-ganjil');
    const cGenap = document.getElementById('container-sync-genap');

    if (mode === 'ganjil') {
        btnGanjil.style.background = '#0860be';
        btnGanjil.style.color = '#fff';
        btnGenap.style.background = 'transparent';
        btnGenap.style.color = '#64748b';
        cGanjil.style.display = 'block';
        cGenap.style.display = 'none';
    } else {
        btnGenap.style.background = '#0860be';
        btnGenap.style.color = '#fff';
        btnGanjil.style.background = 'transparent';
        btnGanjil.style.color = '#64748b';
        cGenap.style.display = 'block';
        cGanjil.style.display = 'none';
    }
}

// Filter Prodi untuk Jadwal Terjadwal Sync
function filterJadwalSyncProdi(prodiQuery, btn) {
    document.querySelectorAll('.btn-filter-sync').forEach(b => {
        b.style.background = '#ffffff';
        b.style.color = '#475569';
        b.style.borderColor = '#cbd5e1';
    });
    btn.style.background = '#0860be';
    btn.style.color = '#ffffff';
    btn.style.borderColor = '#0860be';

    const filterVal = (prodiQuery || 'all').toLowerCase().trim();
    document.querySelectorAll('.jadwal-sync-row').forEach(row => {
        if (filterVal === 'all') {
            row.style.display = '';
            return;
        }

        const rowProdi = (row.getAttribute('data-prodi') || '').toLowerCase();
        const rowKodeMk = (row.getAttribute('data-kode-mk') || '').toLowerCase();

        if (filterVal.includes('elektro') || filterVal === 'te' || filterVal === 'eel') {
            const isMatch = (rowProdi.includes('elektro') || rowKodeMk.startsWith('eel') || rowKodeMk.startsWith('te')) && !rowKodeMk.startsWith('inf');
            row.style.display = isMatch ? '' : 'none';
        } else if (filterVal.includes('informatik') || filterVal === 'inf' || filterVal === 'ti') {
            const isMatch = (rowProdi.includes('informatik') || rowKodeMk.startsWith('inf') || rowKodeMk.startsWith('tif')) && !rowKodeMk.startsWith('eel');
            row.style.display = isMatch ? '' : 'none';
        } else {
            row.style.display = rowProdi.includes(filterVal) ? '' : 'none';
        }
    });
}

// Filter Prodi untuk Tabel Mata Kuliah Prodi
function filterMkProdi(filterVal, btn) {
    document.querySelectorAll('.btn-filter-mk-prodi').forEach(b => {
        b.style.background = '#ffffff';
        b.style.color = '#475569';
        b.style.borderColor = '#cbd5e1';
    });
    btn.style.background = '#0860be';
    btn.style.color = '#ffffff';
    btn.style.borderColor = '#0860be';

    const val = String(filterVal).toLowerCase().trim();
    const btnText = (btn ? btn.textContent : '').toLowerCase().trim();

    // Deteksi intensi filter: apakah tombol yang diklik adalah Informatika atau Elektro
    const isFilterInformatika = btnText.includes('informatik') || val === 'inf' || val === 'ti' || val === 'informatika';
    const isFilterElektro = btnText.includes('elektro') || val === 'elektro' || val === 'te' || val === 'eel';

    document.querySelectorAll('.mk-prodi-row').forEach(row => {
        if (val === 'all') {
            row.style.display = '';
            return;
        }

        const rowKey = (row.getAttribute('data-prodi-key') || '').toLowerCase().trim();
        const rowKodeMk = (row.getAttribute('data-kode-mk') || '').toLowerCase().trim();
        const rowNamaProdi = (row.getAttribute('data-prodi-nama') || '').toLowerCase().trim();

        const isEelCourse = rowKodeMk.startsWith('eel') || rowKodeMk.startsWith('te') || rowKodeMk.startsWith('el') || rowNamaProdi.includes('elektro') || rowKey === 'elektro';
        const isInfCourse = rowKodeMk.startsWith('inf') || rowKodeMk.startsWith('tif') || rowKodeMk.startsWith('if') || rowNamaProdi.includes('informatik') || rowKey === 'informatika';

        if (isFilterInformatika) {
            // JIKA FILTER INFORMATIKA:
            // 1. Mata kuliah EEL / Elektro PASTI DISEMBUNYIKAN
            if (isEelCourse) {
                row.style.display = 'none';
                return;
            }
            // 2. Mata kuliah INF / TIF atau kecocokan key prodi TAMPILKAN
            if (isInfCourse || rowKey === val) {
                row.style.display = '';
                return;
            }
            row.style.display = 'none';
            return;
        }

        if (isFilterElektro) {
            // JIKA FILTER ELEKTRO:
            // 1. Mata kuliah INF / Informatika PASTI DISEMBUNYIKAN
            if (isInfCourse) {
                row.style.display = 'none';
                return;
            }
            // 2. Mata kuliah EEL / TE atau kecocokan key prodi TAMPILKAN
            if (isEelCourse || rowKey === val) {
                row.style.display = '';
                return;
            }
            row.style.display = 'none';
            return;
        }

        // Prodi umum lainnya
        row.style.display = (rowKey === val) ? '' : 'none';
    });
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
        const levelHidden = document.getElementById('mk_prodi_level_id');
        if (levelHidden) levelHidden.value = mkData.level_id || '';
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
        const levelHidden = document.getElementById('mk_prodi_level_id');
        if (levelHidden) levelHidden.value = '';
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

