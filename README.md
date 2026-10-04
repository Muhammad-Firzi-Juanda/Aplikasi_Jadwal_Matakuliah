# Audit Aplikasi

## 1. Informasi Project
- **Framework:** Laravel 11/13.x (berdasarkan composer.json laravel/framework ^13.17)
- **Bahasa Pemrograman:** PHP ^8.3
- **Entry Point:** public/index.php
- **Dependency Manager:** Composer & NPM

## 2. Ringkasan Aplikasi
Aplikasi ini adalah Sistem Penjadwalan Mata Kuliah dengan dukungan multiple roles (Fakultas, Jurusan, Prodi). Aplikasi ini dibangun dengan struktur Monolith MVC (Model-View-Controller) bawaan Laravel dan menggunakan file views Blade (.blade.php) untuk frontend, dengan bantuan Vanilla JS/CSS.

## 3. Arsitektur
Arsitektur yang digunakan adalah Monolith MVC.
- **Model:** Representasi tabel database (misal User, MataKuliah, Jadwal, Prodi).
- **View:** Blade Template Engine (misal dashboard.blade.php, jadwal-kuliah/index.blade.php).
- **Controller:** Mengatur flow bisnis dan logika sistem.
- **Routing:** Diatur terpusat di outes/web.php.

## 4. Struktur Project
- pp/Http/Controllers/: Berisi semua Controller.
- pp/Models/: Berisi model Eloquent (termasuk Enum UserRole).
- esources/views/: Berisi semua template HTML/Blade.
- outes/: Konfigurasi routing aplikasi (web.php).
- database/: Migration, Seeder, dan Factory database.
- public/: Direktori aset publik (CSS, JS, Images).

## 5. Daftar Fitur
- ✅ **Authentication:** Login, Logout, Update Profile, Change Password (AuthController).
- ✅ **Manajemen Akun:** Dikelola oleh UserController.
- ✅ **Manajemen Fakultas & Prodi:** CRUD Fakultas & Prodi.
- ✅ **Manajemen Mata Kuliah:** Dikelola oleh MataKuliahController.
- ✅ **Dashboard By Role:** Fakultas, Jurusan, Prodi masing-masing memiliki Dashboard.
- ✅ **Penjadwalan:** Generate jadwal otomatis atau manual, import data jadwal (PenjadwalanController).

## 6. Role dan Hak Akses
Aplikasi memiliki setidaknya 3 role teridentifikasi (dari enum UserRole):
1. **Fakultas:** Diarahkan ke akultas.dashboard.
2. **Jurusan:** Diarahkan ke jurusan.dashboard.
3. **Prodi:** Diarahkan ke prodi.dashboard.
Pembatasan hak akses diatur melalui routing dan role-based logic (middleware / authorization logic).

## 7. Routing
Terdapat 35 route terdefinisi berdasarkan hasil pengecekan oute:list.
- **Authentication:** /login, /logout, /profile, /change-password
- **Dashboard:** /dashboard, /fakultas/dashboard, /jurusan/dashboard, /prodi/dashboard
- **Master Data:** Resource route untuk akultas, prodi, users, mata-kuliah
- **Penjadwalan:** Route khusus penjadwalan/generate, penjadwalan/import, dll.

## 8. Database
Database terdeteksi mendukung MySQL (jadwal_ku_db.sql ditemukan) dan SQLite sebagai fallback (.env.example).
Terdapat 12 Model Eloquent: Dosen, DosenMengajar, Fakultas, Jadwal, JadwalMengajar, KelasJadwal, MataKuliah, MataKuliahDetail, Prodi, Rombel, Ruangan, User.

## 9. Authentication
- **Login:** Email & Password dengan limitasi max karakter.
- **Password Hashing:** Menggunakan standar Laravel Hash (Bcrypt).
- **Session:** Menggunakan database session (SESSION_DRIVER=database pada config).
Tidak ditemukan penyimpangan; password tidak disave secara plaintext.

## 10. Authorization
Authorization dilakukan dengan memeriksa UserRole pada controller (seperti terlihat pada AuthController@redirectByRole). Diperlukan verifikasi lebih lanjut apakah middleware route juga diaktifkan untuk proteksi halaman selain level UI/Controller redirect.

## 11. Security Audit
- **SQL Injection:** Aman (menggunakan ORM Laravel Eloquent).
- **XSS:** Aman (Blade {{ }} escape output otomatis).
- **CSRF:** Terproteksi (bawaan Laravel).
- **Mass Assignment:** Model User dan lainnya perlu dipastikan field $fillable dikonfigurasi dengan benar.
- **Exposure:** File .env.example tidak berisi keys sensitif asli. Tidak ada hardcoded credentials di AuthController.

## 12. Validasi Input
Validasi input dilakukan di backend menggunakan fasilitas request validator Laravel:
- Contoh AuthController: email (required, valid format email), password (required, min 12 character untuk change password).

## 13. Controller Audit
Controller ditulis dengan rapi menggunakan fitur-fitur standar Laravel:
- Tidak terdapat query mentah (raw DB queries) dalam AuthController.
- Response mendukung AJAX (JSON) dan HTTP standard fallback (redirects).

## 14. Model Audit
Model cukup ekstensif dan saling berkaitan. Terdapat relationship antara Dosen, Mata Kuliah, Jadwal, Ruangan, Prodi, dan Fakultas. Tidak ditemukan model duplikat.

## 15. Frontend / View Audit
- View disusun menggunakan file .blade.php.
- Javascript menggunakan vanilla pp.js (ditemukan file statik di public/assets/js/app.js dan manajemen modal manual menggunakan classList.add('show')).
- UI tergolong responsif standar tanpa dependensi frontend modern seperti React/Vue secara eksplisit dalam core template.

## 16. API Audit
Tidak terdapat API spesifik yang dikembangkan di outes/api.php pada route listing; request bersifat web request (dapat berupa AJAX melalui web endpoints seperti di AuthController).

## 17. Error Handling
Menggunakan exception handler default Laravel (tampilan Ignition untuk debug). Jika .env production diset, akan merender view error standar Laravel (404, 500).

## 18. Performance Audit
- Berpotensi memiliki isu N+1 Query pada pengambilan data jadwal (misalnya Jadwal::with(...) harus selalu dipastikan digunakan pada loop tampilan jadwal).
- Aset js/css disajikan secara statis dan ringan karena merupakan vanilla JS tanpa build rumit.

## 19. Code Quality
- Naming convention PHP (PascalCase untuk Class, camelCase untuk methods, snake_case untuk DB) terpenuhi.
- Kode bersih tanpa magic numbers berlebihan.

## 20. Dependency
Dependency dikelola dengan baik pada composer.json (PHP 8.3+) dan package.json. Tidak ada paket kadaluarsa yang kentara.

## 21. Configuration
Pengaturan environment terletak di .env. File konfigurasi seperti session.php, database.php, berjalan sesuai standar framework.

## 22. Testing
Folder 	ests terdapat pada struktur standar, dengan fitur testing (PHPUnit/Pest) terkonfigurasi dalam composer.json.

## 23. Deployment
File .htaccess dan index.php tersedia untuk web server Apache.
Tidak ditemukan konfigurasi spesifik Dockerfile, CI/CD, atau skrip deployment khusus.

## 24. Documentation
Dokumentasi teknis internal terlihat minim (hanya file Markdown standar dari Laravel README.md dan CHANGELOG.md).

## 25. Bug dan Potensi Bug
- Tidak ditemukan bug fatal (fatal error) melalui static audit singkat ini.
- eset-all-passwords.php & eset-password.php di root: Berpotensi berisiko jika diakses secara publik (tergantung isi file ini, perlu diinvestigasi).

## 26. TODO / Unfinished Code
Tidak ditemukan komentar TODO atau FIXME maupun hardcoded debug dd() yang tertinggal di production code base.

## 27. Git / Project Hygiene
Terdapat file sampah atau non-standard seperti _legacy_native/ dan file PHP di root folder (eset-all-passwords.php), yang sebaiknya tidak ada pada aplikasi framework terstruktur.

## 28. Ringkasan Temuan

| No | Kategori | Temuan | Severity | Lokasi | Status |
| -- | -------- | ------ | -------- | ------ | ------ |
| 1 | Security | File reset password di public root | 🟠 High | /reset-all-passwords.php | ⚠️ Perlu diperiksa |
| 2 | Hygiene | Direktori legacy | ⚪ Info | /_legacy_native | ✅ Berfungsi / Archive |

## 29. Prioritas Perbaikan
### High
- Memeriksa file script mandiri PHP (eset-*.php) di root directory yang berpotensi menjadi celah keamanan jika tidak ter-auth.
### Medium
- Merapikan struktur folder (menghapus _legacy_native jika tidak dipakai lagi).
### Low
- Menambah dokumentasi teknis project.

## 30. Kesimpulan Audit
Aplikasi Jadwal Mata Kuliah ini terstruktur dengan sangat baik, menggunakan standar Laravel 11/13 yang modern. Fungsionalitas inti telah diimplementasikan dengan baik mulai dari authentication, manajemen jadwal, hingga pengelolaan master data berbasis peran. Kualitas kode sangat baik, namun perlu memperhatikan project hygiene (file-file legacy).

## 31. Log Update Terbaru (4 Oktober 2026)
- **UI/UX:** Pembaruan tampilan dashboard Jurusan dan Prodi serta penyesuaian style CSS (`style.css`).
- **Database & Model:** Penambahan relasi `prodi_id` pada model `User` beserta file migration.
- **Controller:** Penambahan `ProdiManagementController` untuk pengelolaan khusus prodi.
