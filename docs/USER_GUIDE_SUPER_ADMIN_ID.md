# Panduan Pengguna Super Admin

## Daftar Isi

1. [Memulai](#memulai)
2. [Manajemen Properti](#manajemen-properti)
3. [Manajemen Pengguna](#manajemen-pengguna)
4. [Manajemen Lead](#manajemen-lead)
5. [Pengaturan Sistem](#pengaturan-sistem)
6. [Dashboard Analitik](#dashboard-analitik)
7. [Alur Kerja Umum](#alur-kerja-umum)
8. [Pemecahan Masalah](#pemecahan-masalah)

---

## Memulai

### Mengakses Panel Admin

1. Navigasi ke domain PAMS Anda: `https://domainanda.com/admin`
2. Masukkan kredensial Super Admin Anda
3. Anda akan diarahkan ke dashboard utama

### Ringkasan Dashboard

Dashboard Super Admin menampilkan:

-   **Statistik Global**: Total traffic, lead, dan affiliate aktif
-   **Grafik Performa**: Tren traffic dan konversi
-   **Affiliate Teratas**: Affiliate berkinerja terbaik berdasarkan jumlah lead
-   **Aktivitas Terbaru**: Lead dan tampilan properti terbaru
-   **Google Analytics**: Grafik analytics yang tertanam

---

## Manajemen Properti

### Membuat Properti Baru

1. Klik **Properties** di sidebar
2. Klik tombol **New Property**
3. Isi field yang diperlukan:
    - **Title**: Nama properti (contoh: "Villa Mewah di Bali")
    - **Price**: Harga properti dalam IDR
    - **Location**: Alamat lengkap atau area
    - **Description**: Deskripsi properti detail (mendukung rich text)
    - **Status**: Pilih Draft, Published, atau Sold
4. Tambahkan **Features** (klik "Add item" untuk setiap fitur):
    - Kolam Renang
    - 3 Kamar Tidur
    - Taman
    - dll.
5. Tambahkan **Specifications** (pasangan key-value):
    - Luas Tanah: 500 m²
    - Luas Bangunan: 300 m²
    - Sertifikat: SHM
    - dll.
6. Upload **Images**:
    - Klik area upload atau drag gambar
    - Mendukung multiple gambar
    - Gambar pertama menjadi thumbnail
7. Klik **Create** untuk menyimpan

**Catatan**: Sistem otomatis menghasilkan slug SEO-friendly dari title.

### Mengedit Properti

1. Buka daftar **Properties**
2. Klik ikon **Edit** pada properti mana pun
3. Modifikasi field sesuai kebutuhan
4. Klik **Save changes**

### Mengelola Gambar Properti

-   **Tambah Gambar**: Klik "Add files" di bagian Images
-   **Urutkan Gambar**: Drag dan drop gambar untuk mengurutkan
-   **Hapus Gambar**: Klik ikon X pada gambar mana pun
-   **Optimasi Gambar**: Sistem otomatis membuat versi yang dioptimalkan (WebP, thumbnails)

### Manajemen Status Properti

-   **Draft**: Properti disembunyikan dari katalog publik
-   **Published**: Properti muncul di katalog publik dan link affiliate
-   **Sold**: Properti menampilkan badge "SOLD", dikecualikan dari link affiliate

### Menghapus Properti

1. Pilih properti menggunakan checkbox
2. Klik **Bulk Actions** → **Delete**
3. Konfirmasi penghapusan
4. Semua file media terkait otomatis dihapus

---

## Manajemen Pengguna

### Melihat Daftar Pengguna

1. Klik **Users** di sidebar
2. Lihat semua pengguna terdaftar dengan:
    - Nama dan email
    - Status (Pending, Active, Blocked)
    - Tanggal registrasi
    - Role yang ditugaskan

### Menyetujui Affiliate Baru

Ketika pengguna mendaftar sebagai affiliate, mereka dimulai dengan status "Pending":

1. Buka daftar **Users**
2. Filter berdasarkan **Status: Pending**
3. Review detail pengguna
4. Pilih pengguna yang akan disetujui
5. Klik **Bulk Actions** → **Approve Affiliates**
6. Sistem otomatis:
    - Menghasilkan kode affiliate unik
    - Menugaskan role Affiliate
    - Mengirim email selamat datang dengan kode affiliate
    - Mengubah status ke Active

### Memblokir/Membuka Blokir Pengguna

Untuk memblokir pengguna:

1. Temukan pengguna di daftar
2. Klik **Edit**
3. Ubah **Status** ke **Blocked**
4. Klik **Save**

Pengguna yang diblokir tidak dapat login ke sistem.

Untuk membuka blokir:

1. Ubah **Status** kembali ke **Active**
2. Klik **Save**

### Mengedit Informasi Pengguna

1. Klik **Edit** pada pengguna mana pun
2. Modifikasi:
    - Nama
    - Email
    - Nomor WhatsApp
    - Foto profil
    - Status
3. Klik **Save changes**

### Melihat Aktivitas Pengguna

1. Klik pada pengguna untuk melihat detail
2. Lihat yang terkait:
    - Total kunjungan yang dihasilkan
    - Total lead yang diterima
    - Tingkat konversi
    - Aktivitas terbaru

---

## Manajemen Lead

### Melihat Semua Lead

1. Klik **Leads** di sidebar
2. Lihat daftar lead lengkap dengan:
    - Nama pengunjung dan WhatsApp
    - Nama properti
    - Affiliate yang ditugaskan
    - Status
    - Tanggal pengiriman

### Memfilter Lead

Gunakan filter untuk menemukan lead spesifik:

-   **Status**: New, Follow Up, Survey, Closed, Lost
-   **Affiliate**: Filter berdasarkan affiliate tertentu
-   **Property**: Filter berdasarkan properti
-   **Date Range**: Filter tanggal kustom

### Mengekspor Lead

1. Terapkan filter yang diinginkan
2. Klik tombol **Export**
3. Pilih format (CSV, Excel)
4. Download file

### Arti Status Lead

-   **New**: Baru dikirim, menunggu kontak pertama
-   **Follow Up**: Kontak awal sudah dibuat, perlu follow-up
-   **Survey**: Viewing properti dijadwalkan atau selesai
-   **Closed**: Deal berhasil ditutup
-   **Lost**: Lead tidak berkonversi

---

## Pengaturan Sistem

### Mengakses Pengaturan

1. Klik **Settings** di sidebar
2. Konfigurasi opsi sistem

### Konfigurasi GoWA API

Konfigurasi notifikasi WhatsApp:

1. Buka **Settings** → **GoWA Integration**
2. Masukkan:
    - **Username**: Username GoWA Anda
    - **Password**: Password GoWA Anda
    - **API URL**: `https://api.gowa.id/v1` (atau endpoint kustom Anda)
3. Klik **Test Connection** untuk verifikasi
4. Klik **Save**

**Troubleshooting**: Jika notifikasi gagal, cek:

-   Kredensial sudah benar
-   API URL dapat diakses
-   Akun memiliki kredit yang cukup

### Manajemen Logo

1. Buka **Settings** → **Branding**
2. Klik **Upload Logo**
3. Pilih file gambar (PNG, JPG, SVG)
4. Logo muncul di:
    - Header panel admin
    - Header website publik
    - Notifikasi email

### Pengaturan SEO

Konfigurasi metadata SEO global:

1. Buka **Settings** → **SEO**
2. Set:
    - **Site Title**: Title default untuk halaman
    - **Meta Description**: Deskripsi default
    - **Meta Keywords**: Keywords dipisahkan koma
3. Klik **Save**

**Catatan**: Halaman properti individual menimpa ini dengan SEO spesifik properti.

### Integrasi Google Analytics

1. Buka **Settings** → **Analytics**
2. Masukkan **Google Analytics ID** Anda (contoh: G-XXXXXXXXXX)
3. Klik **Save**
4. Widget analytics muncul di dashboard

---

## Dashboard Analitik

### Memahami Metrik

**Kartu Statistik Global**:

-   **Total Traffic**: Semua kunjungan di semua affiliate
-   **Total Leads**: Semua lead yang dihasilkan
-   **Active Affiliates**: Pengguna dengan status aktif
-   **Conversion Rate**: (Total Lead / Total Traffic) × 100

**Grafik Performa**:

-   Grafik garis menunjukkan traffic dan lead dari waktu ke waktu
-   Toggle antara tampilan harian, mingguan, bulanan
-   Hover untuk titik data spesifik

**Tabel Affiliate Teratas**:

-   Diurutkan berdasarkan jumlah lead
-   Menampilkan kunjungan, lead, dan tingkat konversi
-   Klik nama affiliate untuk melihat detail

**Feed Aktivitas Terbaru**:

-   Lead terbaru yang dikirim
-   Tampilan properti terbaru
-   Update real-time

### Memfilter Analitik

1. Gunakan pemilih **Date Range** di atas
2. Pilih rentang preset:
    - Hari Ini
    - Minggu Ini
    - Bulan Ini
    - 30 Hari Terakhir
    - Rentang Kustom
3. Semua widget diperbarui otomatis

### Mengekspor Laporan

1. Set rentang tanggal yang diinginkan
2. Klik **Export Report**
3. Pilih format (PDF, Excel)
4. Laporan mencakup:
    - Statistik ringkasan
    - Rincian performa affiliate
    - Distribusi status lead
    - Properti teratas

---

## Alur Kerja Umum

### Alur Kerja 1: Onboarding Properti Baru

1. **Buat Properti**:

    - Tambahkan detail properti
    - Upload gambar berkualitas tinggi
    - Set status ke "Published"

2. **Verifikasi SEO**:

    - Cek slug yang auto-generated
    - Review meta description
    - Pastikan gambar memiliki alt text

3. **Tes Tampilan Publik**:

    - Kunjungi halaman properti: `/p/{slug}`
    - Verifikasi semua informasi ditampilkan dengan benar
    - Tes formulir kontak

4. **Notifikasi Affiliate**:
    - Affiliate otomatis melihat properti baru
    - Mereka dapat membuat link tracking segera

### Alur Kerja 2: Menyetujui Affiliate Baru

1. **Review Registrasi**:

    - Cek detail pengguna di daftar Users
    - Verifikasi format nomor WhatsApp
    - Review informasi profil

2. **Setujui Pengguna**:

    - Pilih pengguna
    - Gunakan aksi bulk approve
    - Sistem mengirim email selamat datang

3. **Verifikasi Setup**:

    - Cek kode affiliate yang dihasilkan
    - Konfirmasi role ditugaskan
    - Tes login affiliate

4. **Berikan Pelatihan**:
    - Bagikan Panduan Pengguna Affiliate
    - Jelaskan pembuatan link
    - Tunjukkan fitur dashboard

### Alur Kerja 3: Mengelola Lead

1. **Monitor Lead Baru**:

    - Cek dashboard untuk lead baru
    - Review detail lead
    - Verifikasi atribusi affiliate

2. **Follow Up**:

    - Lead otomatis memberi notifikasi affiliate via WhatsApp
    - Monitor update status lead
    - Lacak progress konversi

3. **Tutup Deal**:
    - Affiliate memperbarui status ke "Closed"
    - Review deal yang ditutup di laporan
    - Hitung komisi jika berlaku

### Alur Kerja 4: Pelaporan Bulanan

1. **Set Rentang Tanggal**: Bulan sebelumnya
2. **Review Metrik**:
    - Total traffic dan lead
    - Tingkat konversi
    - Performa teratas
3. **Ekspor Data**: Download laporan Excel
4. **Analisis Tren**: Bandingkan dengan bulan sebelumnya
5. **Ambil Tindakan**:
    - Beri reward affiliate teratas
    - Dukung affiliate yang kurang berkinerja
    - Optimalkan listing properti

---

## Pemecahan Masalah

### Masalah: Notifikasi WhatsApp Tidak Terkirim

**Gejala**: Affiliate tidak menerima notifikasi lead

**Solusi**:

1. Cek kredensial GoWA API di Settings
2. Verifikasi API URL sudah benar
3. Tes koneksi menggunakan tombol "Test Connection"
4. Cek kredit akun GoWA
5. Review error logs: `/storage/logs/laravel.log`
6. Hubungi support GoWA jika masalah berlanjut

### Masalah: Gambar Properti Tidak Ditampilkan

**Gejala**: Link gambar rusak di halaman properti

**Solusi**:

1. Verifikasi storage link ada: `php artisan storage:link`
2. Cek permission file pada direktori `/storage`
3. Pastikan gambar berhasil diupload
4. Hapus cache browser
5. Cek konfigurasi media library

### Masalah: Link Affiliate Tidak Melacak

**Gejala**: Kunjungan tidak tercatat ketika menggunakan link affiliate

**Solusi**:

1. Verifikasi kode affiliate sudah benar
2. Cek pengaturan cookie di browser (cookie harus diaktifkan)
3. Tes dengan browser berbeda
4. Review konfigurasi middleware
5. Cek tabel visits untuk record
6. Verifikasi status affiliate adalah "Active"

### Masalah: Pencarian Tidak Berfungsi

**Gejala**: Pencarian properti tidak mengembalikan hasil

**Solusi**:

1. Verifikasi Laravel Scout sudah dikonfigurasi
2. Cek database driver di `config/scout.php`
3. Pastikan properti memiliki status "Published"
4. Tes dengan istilah pencarian berbeda
5. Cek fulltext index pada tabel properties

### Masalah: Widget Dashboard Tidak Loading

**Gejala**: Kosong atau error di dashboard

**Solusi**:

1. Hapus application cache: `php artisan cache:clear`
2. Hapus view cache: `php artisan view:clear`
3. Cek koneksi database
4. Review error logs
5. Verifikasi pengguna memiliki permission yang benar

### Masalah: Tidak Bisa Login

**Gejala**: Login gagal dengan kredensial yang benar

**Solusi**:

1. Verifikasi status pengguna adalah "Active" (bukan Blocked atau Pending)
2. Cek alamat email sudah benar
3. Reset password jika diperlukan
4. Hapus cookie browser
5. Coba browser berbeda
6. Cek konfigurasi session

### Mendapatkan Bantuan

Jika Anda mengalami masalah yang tidak tercakup di sini:

1. **Cek Logs**: `/storage/logs/laravel.log`
2. **Review Dokumentasi**: Dokumentasi teknis di `/docs`
3. **Hubungi Support**: Berikan:
    - Pesan error
    - Langkah untuk mereproduksi
    - Screenshot
    - Kutipan log

---

## Praktik Terbaik

### Manajemen Properti

-   Gunakan gambar berkualitas tinggi (minimum lebar 1200px)
-   Tulis deskripsi yang detail dan akurat
-   Perbarui status properti segera ketika terjual
-   Gunakan konvensi penamaan yang konsisten
-   Tambahkan spesifikasi yang komprehensif

### Manajemen Pengguna

-   Review aplikasi affiliate dengan segera
-   Monitor performa affiliate secara teratur
-   Blokir akun yang mencurigakan segera
-   Jaga informasi kontak tetap diperbarui
-   Komunikasikan perubahan kebijakan dengan jelas

### Manajemen Lead

-   Monitor lead baru setiap hari
-   Follow up affiliate yang tidak responsif
-   Lacak tingkat konversi
-   Analisis kualitas lead
-   Berikan feedback kepada affiliate

### Pemeliharaan Sistem

-   Review error logs setiap minggu
-   Perbarui pengaturan sesuai kebutuhan
-   Monitor penggunaan API dan kredit
-   Backup database secara teratur
-   Tes fitur kritis setiap bulan

---

## Shortcut Keyboard

-   `Ctrl/Cmd + K`: Pencarian global
-   `Ctrl/Cmd + S`: Simpan form
-   `Esc`: Tutup modal
-   `Tab`: Navigasi field form
-   `Enter`: Submit form

---

## Sumber Dukungan

-   **Dokumentasi Teknis**: `/docs/TECHNICAL_DOCUMENTATION.md`
-   **Dokumentasi API**: `/docs/API_DOCUMENTATION.md`
-   **Setup RBAC**: `/docs/RBAC_SETUP.md`
-   **Panduan Performa**: `/docs/PERFORMANCE_OPTIMIZATION.md`

---

_Terakhir Diperbarui: November 2025_
_Versi: 1.0_
