# APJAD - Aplikasi Penjadwalan Mata Kuliah Otomatis

[![Laravel](https://img.shields.io/badge/Laravel-11%2F12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tests](https://img.shields.io/badge/Tests-80%20Passed%20(100%25)-success?style=for-the-badge&logo=phpunit)](https://phpunit.de)
[![Code Style](https://img.shields.io/badge/Code%20Style-Laravel%20Pint-00D26A?style=for-the-badge)](https://laravel.com/docs/pint)
[![Architecture Docs](https://img.shields.io/badge/Documentation-Backend%20Architecture-blue?style=for-the-badge)](docs/BACKEND_ARCHITECTURE.md)

Sistem Informasi Manajemen dan Penjadwalan Mata Kuliah Otomatis berbasis web (*Monolith Laravel MVC*) yang dirancang untuk mengotomatisasi penyusunan jadwal perkuliahan bebas bentrok (*conflict-free*), mengelola data akademik per program studi dan fakultas dengan isolasi wewenang yang aman, serta memproses impor data perkuliahan dari Excel/Spreadsheet secara cerdas.

> 📖 **Dokumentasi Lengkap Logika Backend & Developer Guide:**  
> Untuk penjelasan mendalam mengenai alur logika, constraint satisfaction, isolasi multi-prodi, skema database, dan panduan pengembang, silakan baca [**Dokumentasi Arsitektur Backend (docs/BACKEND_ARCHITECTURE.md)**](docs/BACKEND_ARCHITECTURE.md).

---

## 📌 Ringkasan & Spesifikasi Sistem

- **Framework:** Laravel 11/12 (PHP 8.4)
- **Arsitektur:** Model-View-Controller (MVC) Monolith + Service Layer Pattern
- **Frontend:** Blade Templates, Responsive Modern Vanilla CSS & Vanilla JavaScript
- **Database:** MySQL / SQLite
- **Dependency Management:** Composer & NPM
- **Testing Suite:** PHPUnit (80 Feature & Unit Tests, 422 Assertions - 100% Passed)
- **Formatting Standard:** Laravel Pint (PSR-12 / Laravel Standard)

---

## 🚀 Fitur Utama

### 1. Penjadwalan Kuliah Otomatis (Scheduler Engine)
- **Algoritma:** *Greedy Sequential + First-Fit Slot Placement* dengan validasi interval waktu real-time.
- **Pencegahan Bentrok Multi-Dimensi:**
  - **Bentrok Cohort:** Satu angkatan/prodi tidak akan memiliki lebih dari satu kelas pada slot waktu yang sama.
  - **Bentrok Dosen:** Dosen ketua dan dosen anggota dipastikan tidak memiliki jadwal tumpang tindih antar-mata kuliah.
  - **Bentrok Ruangan:** Ruangan teori maupun laboratorium tidak dapat digunakan lebih dari satu rombel pada jam yang sama.
  - **Manajemen MK Pilihan:** Pengelompokan slot MK pilihan secara terisolasi (kuota dan slot terdistribusi adil).
  - **Alokasi Slot Waktu Terstandar:** Sesi perkuliahan berurutan mulai pukul 07:30, 10:00, 13:00, dan 14:50 dengan penyesuaian durasi per SKS.
- **Dukungan MK Tanpa Ruangan:** Mata kuliah seperti Skripsi, Seminar, Kukerta, KKN, dan Magang otomatis dijadwalkan tanpa alokasi ruangan fisik (ruangan ditampilkan `-`).
- **Toleransi Dosen Kosong / TBA:** Mata kuliah yang belum memiliki dosen tetap dapat dijadwalkan secara aman tanpa menyebabkan error sistem.

### 2. Isolasi Program Studi & Laboratorium Terpisah
- **Pemisahan Lingkup Jadwal Per Prodi:** Pengguna dengan role **Prodi** hanya dapat melihat jadwal dan mata kuliah milik program studinya sendiri ditambah mata kuliah bersama tingkat fakultas/universitas.
- **Ruangan Lab Khusus Prodi:** Pemisahan laboratorium khusus per Program Studi (misal: *LAB INF 1 & 2* untuk Teknik Informatika, *LAB EEL 1 & 2* untuk Teknik Elektro). Praktikum suatu prodi tidak akan mengambil laboratorium prodi lain.
- **Pembersihan Kelas Mengulang Otomatis:** Kelas-kelas mengulang yang dinonaktifkan tidak akan memadati antrean slot jadwal reguler.

### 3. Impor Spreadsheet 100% Otomatis (Excel & CSV)
- **Deteksi Delimiter Pintar:** Otomatis mendeteksi pemisah CSV (koma `,`, titik koma `;`, atau tab `\t`) tanpa memecah data teks yang memiliki baris kop/judul instansi.
- **Dukungan Berbagai Format:** Mendukung file `.xlsx`, `.xls`, `.csv`, dan `.ods`.
- **Pemetaan Kolom & Header Fleksibel:** Mengenali berbagai alias nama kolom (`Kode MK`, `KD MKK`, `Nama Mata Kuliah`, `Kredit SKS`, `SMSTR`, `Kuota`, `Dosen PJMK`, `Jenis`, dll.).
- **Deteksi Merged Cells (Carry-Forward Semester):** Sel semester yang kosong akibat penggabungan baris (*merged cells*) di Excel otomatis mewarisi semester dari baris sebelumnya.
- **Pembersihan Dosen Otomatis:** Mengabaikan teks non-dosen (seperti `PJMK`, `TBA`, `team teaching`, tanda `-`) dan mencocokkan inisial dosen yang sudah terdaftar guna menghindari data ganda (*junk records*).
- **Perhitungan Kelas Paralel Otomatis:** Menghitung jumlah paralel kelas dari frekuensi baris kelas (A, B, C, dst.) per kode mata kuliah.
- **Deteksi Laboratorium Otomatis:** Mengenali mata kuliah praktikum secara otomatis dan menetapkan kebutuhan laboratorium (`butuh_lab = YA`).

### 4. Manajemen Akun Berbasis Role (RBAC) & Penetapan Prodi
- **Super Admin:** Akses penuh konfigurasi sistem, data fakultas, pengguna, ruangan, mata kuliah, dan penjadwalan.
- **Fakultas:** Manajemen jadwal dan mata kuliah di lingkup fakultas.
- **Jurusan:** Dashboard pemantauan dan pengelolaan tingkat jurusan.
- **Prodi:** Dashboard dan pemantauan jadwal spesifik program studi.
- **Admin:** Pengelolaan akun pengguna.
- **Penetapan Program Studi Spesifik:** Form Tambah dan Edit Akun menyediakan pilihan spesifik Program Studi untuk role **Prodi**, dilengkapi badge indikator prodi pada tabel pengguna dan validasi penyimpanan persisten.

### 5. Fitur Reset & Hapus Data Massal
- **Hapus Semua Data MK & Jadwal:** Menghapus seluruh data mata kuliah beserta relasi rombel, jadwal, dan secara otomatis membersihkan data dosen yatim (*orphaned dosen*) dalam satu klik.
- **Hapus Semua Hasil Jadwal:** Mengosongkan jadwal hasil *generate* di tab Jadwal Semester dengan tetap mempertahankan master data mata kuliah.
- **Modal Konfirmasi Aman:** Dialog konfirmasi interaktif untuk mencegah kehilangan data akibat klik tidak disengaja.

### 6. Tampilan Jadwal Semester Interaktif
- **Mode Periode Semester:** Pengalihan instan antara semester **Ganjil** (1, 3, 5, 7) dan **Genap** (2, 4, 6, 8).
- **Filter Semester & Accordion:** Tampilan jadwal per semester yang dapat dibuka/tutup (*accordion*) serta filter pemilihan semester tertentu.
- **Rincian Komprehensif:** Menampilkan jam mulai-selesai, kode rombel/kelas, ruangan, dosen pengampu, SKS, dan tipe perkuliahan.
- **Notifikasi Auto-Dismiss:** Pesan flash alert menghilang otomatis setelah beberapa detik dilengkapi progress bar countdown dan tombol tutup manual.

---

## 🔍 Hasil Audit Keamanan & Kualitas Kode

| Aspek Audit | Status | Keterangan |
| :--- | :---: | :--- |
| **SQL Injection** | **AMAN** | Seluruh query menggunakan Laravel Eloquent ORM & Query Builder dengan *parameter binding*. |
| **Cross-Site Scripting (XSS)** | **AMAN** | Output HTML di-*escape* secara otomatis oleh Blade Engine `{{ ... }}`. |
| **Cross-Site Request Forgery (CSRF)** | **AMAN** | Semua rute `POST`, `PUT`, dan `DELETE` diproteksi token `@csrf` dan middleware VerifyCsrfToken. |
| **Database Integrity & Cascading** | **AMAN** | Foreign key constraints pada tabel `rombels`, `jadwals`, dan `users` dengan `onDelete('cascade')`/`onDelete('set null')`. Kolom `dosen_id`, `ruangan_id`, dan `prodi_id` bersifat nullable dan terindeks. |
| **Autentikasi & Sandi** | **AMAN** | Enkripsi kata sandi menggunakan Bcrypt Hash, pembatasan rate limit login (`throttle:20,1`). |
| **Format & Gaya Kode** | **LULUS** | Seluruh kode PHP telah diformat dan divalidasi menggunakan **Laravel Pint**. |
| **Feature & Regression Tests** | **LULUS** | 80 dari 80 skenario pengujian (`UserManagementTest`, `JadwalKuliahFilterTest`, `MataKuliahImportTest`, `PenjadwalanDestroyAllTest`, `SchedulerServiceTest`, `RuanganManagementTest`) lulus 100%. |

---

## 📂 Struktur Direktori Utama

```
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php            # Autentikasi & Profil Pengguna
│   │   ├── JadwalKuliahController.php    # Tampilan & Filter Jadwal Kuliah (Role Scoped)
│   │   ├── PenjadwalanController.php     # Controller Penjadwalan, Impor & Reset
│   │   ├── ProdiDashboardController.php  # Dashboard Khusus Admin Prodi
│   │   ├── JurusanDashboardController.php# Dashboard Khusus Admin Jurusan
│   │   ├── UserController.php            # Manajemen Akun & Penetapan Prodi
│   │   └── ...
│   ├── Models/
│   │   ├── MataKuliahDetail.php          # Master Data Mata Kuliah
│   │   ├── Rombel.php                    # Data Rombel / Kelas Paralel
│   │   ├── Jadwal.php                    # Data Slot Jadwal Terplot
│   │   ├── Dosen.php                     # Master Dosen
│   │   ├── Ruangan.php                   # Master Ruangan Teori & Lab
│   │   ├── Prodi.php                     # Master Program Studi
│   │   └── User.php                      # Pengguna Sistem & Relasi Prodi
│   └── Services/
│       ├── MataKuliahImportService.php   # Service Cerdas Impor Spreadsheet
│       └── SchedulerService.php          # Algoritma Penjadwalan Otomatis
├── database/
│   ├── migrations/                       # Skema Database & Relasi Foreign Key
│   └── seeders/                          # Data Awal Akun, Ruangan, & Pengguna
├── resources/views/
│   ├── admin/
│   │   └── manajemen-akun.blade.php      # Manajemen Akun & Penetapan Prodi
│   ├── jadwal-kuliah/
│   │   └── index.blade.php               # Tampilan Jadwal Kuliah & Filter Prodi
│   └── penjadwalan/
│       ├── index.blade.php               # Halaman Utama Penjadwalan (Tab 1, 2, 3)
│       └── partials/
│           └── jadwal-semester.blade.php # Komponen Accordion Jadwal Semester
├── routes/
│   └── web.php                           # Rute Aplikasi & Middleware RBAC
└── tests/
    └── Feature/
        ├── JadwalKuliahFilterTest.php    # Pengujian Filter Jadwal & Hak Akses Prodi
        ├── UserManagementTest.php        # Pengujian CRUD Akun & Persistensi Prodi
        ├── MataKuliahImportTest.php      # Pengujian Impor Spreadsheet Otomatis
        ├── PenjadwalanDestroyAllTest.php # Pengujian Hapus Semua Data / Jadwal
        └── SchedulerServiceTest.php      # Pengujian Algoritma Scheduler
```

---

## 🛠️ Panduan Instalasi & Menjalankan Aplikasi

### 1. Kebutuhan Sistem
- **PHP** >= 8.4 (Disarankan PHP 8.4.x)
- **Composer** >= 2.0
- **Node.js** >= 18 & **NPM**
- **SQLite** (bawaan tanpa setup) atau **MySQL** >= 8.0 / MariaDB

### 2. Langkah Instalasi Cepat (Direkomendasikan)
Cukup jalankan perintah berikut untuk mengonfigurasi seluruh aplikasi (dependensi, database, migrasi, seeder, dan build aset):

```bash
composer run setup
php artisan serve
```

---

### 3. Langkah Instalasi Manual
```bash
# Clone repositori
git clone https://github.com/Muhammad-Firzi-Juanda/Aplikasi_Jadwal_Matakuliah.git
cd Aplikasi_Jadwal_Matakuliah

# Instal dependensi PHP
composer install

# Instal dependensi frontend & build asset
npm install
npm run build

# Salin file environment
cp .env.example .env

# Generate application key
php artisan key:generate

# Migrasi tabel dan data awal (seeder akun, prodi & ruangan)
php artisan migrate --seed

# Jalankan pengujian unit & feature
php artisan test --compact

# Jalankan server lokal
php artisan serve
```

Aplikasi dapat diakses melalui browser pada `http://127.0.0.1:8000`.

---

## 👤 Akun Pengujian Bawaan

| Role | Email | Password | Keterangan |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `admin@siwalan.com` | `admin123` | Akses penuh seluruh sistem |
| **Admin Akun** | `admin@apjad.com` | `admin123` | Pengelola akun pengguna |
| **Admin Fakultas** | `adminf@gmail.com` | `password123` | Tingkat fakultas |
| **Admin Jurusan** | `adminj@gmail.com` | `password123` | Tingkat jurusan |
| **Admin Prodi (Informatika)** | `adminif@gmail.com` | `password123` | Prodi Teknik Informatika (INF) |
| **Admin Prodi (Elektro)** | `adminprodie@gmail.com` | `password123` | Prodi Teknik Elektro (EEL) |

---

## 📄 Lisensi
Aplikasi ini dikembangkan untuk kebutuhan proyek perkuliahan dan pengelolaan jadwal akademik. Hak cipta dilindungi.
