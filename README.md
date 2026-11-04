# Etalase Digital Stella (PHP Native & MySQL)

Proyek Web Application berdasarkan Lembar Kerja Peserta Didik (LKPD) **Etalase Digital Stella** menggunakan **PHP Native**, **MySQL**, **JavaScript (Bootstrap 5)**, dan **CSS**.

## 🚀 Fitur Utama
- **Autentikasi & Otorisasi Role (RBAC):**
  - **Super Admin:** Mengelola user (tambah/edit/hapus admin & siswa), mengedit & menghapus semua karya digital.
  - **Admin:** Mengedit semua karya digital (tidak dapat menghapus karya atau mengelola user).
  - **Siswa (User):** Menambahkan karya digital, mengedit & menghapus karya miliknya sendiri.
- **Tampilan UI & Layout:**
  - Halaman Login & Sign Up (Register) dilengkapi *placeholder* logo Telkom School di bagian tengah.
  - Sidebar navigasi dengan item Dashboard & Kelola User (Super Admin).
  - Header dengan sapaan nama pengguna yang sedang login, tombol Logout, fitur **Cari Karya**, dan tombol **Tambah Karya**.
  - *Placeholder* logo Telkom School bulat kecil di sudut kiri atas setiap halaman.
  - Galeri karya berbentuk **Card Grid** interaktif dengan link GitHub/Project.
  - Layout responsive dengan Footer di bagian bawah.

---

## 🛠️ Persyaratan Sistem
- Web Server (XAMPP / Laragon / Apache dengan PHP 7.4+)
- MySQL Database

---

## 📦 Cara Penginstalan & Menjalankan Aplikasi

1. **Ekstrak File ZIP** ke folder `htdocs` XAMPP Anda (misalnya `C:/xampp/htdocs/etalase-digital-stella`).
2. **Impor Database:**
   - Buka `phpMyAdmin` (`http://localhost/phpmyadmin`).
   - Buat database baru bernama `etalase_stella` atau langsung impor file `schema.sql` yang ada di root folder proyek.
3. **Konfigurasi Database (jika perlu):**
   - Buka file `config/database.php` dan sesuaikan kredensial MySQL jika password DB Anda bukan kosong.
4. **Jalankan Aplikasi:**
   - Buka browser dan akses: `http://localhost/etalase-digital-stella`

---

## 🔑 Akun Demo bawaan (`schema.sql`):
- **Super Admin:** `superadmin@stella.sch.id` / `Password123!`
- **Admin:** `admin@stella.sch.id` / `Password123!`
- **Siswa:** `siswa@stella.sch.id` / `Password123!`
