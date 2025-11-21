# Property Affiliate Management System (PAMS)

> Sistem Manajemen Afiliasi Properti Berbasis Web
> 
> **Tryan Teams** | Mei 2025

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=flat&logo=php)](https://php.net)
[![FilamentPHP](https://img.shields.io/badge/Filament-4.x-FFAA00?style=flat)](https://filamentphp.com)
[![Livewire](https://img.shields.io/badge/Livewire-3.x-FB70A9?style=flat)](https://livewire.laravel.com)

---

## 📋 Daftar Isi

- [Tentang Proyek](#-tentang-proyek)
- [Fitur Utama](#-fitur-utama)
- [Teknologi Stack](#-teknologi-stack)
- [Sistem Workflow](#-sistem-workflow)
- [Struktur Database](#-struktur-database)
- [Role & Fitur](#-role--fitur)
- [Instalasi](#-instalasi)
- [Roadmap Pengembangan](#-roadmap-pengembangan)
- [Lisensi](#-lisensi)

---

## 🎯 Tentang Proyek

**Property Affiliate Management System (PAMS)** adalah platform katalog properti berbasis afiliasi yang dirancang untuk memberdayakan pemilik properti/admin dalam mendistribusikan informasi properti secara efisien melalui jaringan agen (affiliate).

Setiap affiliate dilengkapi dengan:
- 🔗 **Tautan Unik** untuk tracking performa
- 📊 **Dashboard Analitik Mandiri** untuk monitoring traffic & leads
- 🎨 **Materi Pemasaran Digital** siap promosi

---

## ✨ Fitur Utama

### 🌐 Dynamic Landing Page
Halaman properti yang dioptimalkan dengan SEO-friendly URL dan responsive design.

### 📱 Real-time Lead Notification
Notifikasi prospek instan melalui WhatsApp menggunakan integrasi GoWA API.

### 📈 Hybrid Analytics
Kombinasi analitik internal (database) dan eksternal (Google Analytics embed).

### 🔐 Advanced Access Control
Manajemen role dan permission yang terperinci menggunakan Filament Shield.

### 🔍 Smart Search
Implementasi pencarian properti yang cepat dan efisien dengan Laravel Scout.

---

## 🛠 Teknologi Stack

### Core Environment

| Komponen | Spesifikasi | Keterangan |
|----------|-------------|------------|
| **Bahasa Pemrograman** | PHP 8.3+ | Wajib untuk kompatibilitas Laravel 12 |
| **Framework** | Laravel 12.x | Backend utama |
| **Frontend** | Livewire 3 | Interactive UI components |
| **Admin Panel** | FilamentPHP v4 | Dashboard Admin & Affiliate |
| **Database** | MySQL 8.0+ / MariaDB | Penyimpanan data utama |
| **Web Server** | Nginx / Apache / OpenLiteSpeed | Direkomendasikan untuk performa optimal |

### Library & Integrasi

| Fungsionalitas | Package/Service | Deskripsi |
|----------------|-----------------|-----------|
| **Kontrol Akses** | `filament/shield` | Manajemen Role dan Permission yang terperinci |
| **Mesin Pencari** | `laravel/scout` | Implementasi pencarian properti yang cepat |
| **Otentikasi API** | `laravel/sanctum` | Persiapan untuk aplikasi mobile/eksternal |
| **SEO** | `spatie/laravel-sitemap` | Sitemap generation & Meta Tags management |
| **Media Management** | `spatie/media-library` | Image upload & optimization |
| **Analitik** | Hybrid Integration | Internal Database + Google Analytics |
| **WhatsApp Gateway** | GoWA API | Sistem notifikasi real-time |

---

## 🔄 Sistem Workflow

### A. Mekanisme Afiliasi (Tracking Logic)

#### 1. Struktur URL Unik
```
domain.com/ref/{affiliate_code}
domain.com/p/nama-properti?ref={affiliate_code}
```

#### 2. Deteksi Middleware
Saat pengunjung mengakses tautan, sistem menjalankan:

- ✅ Mencatat data kunjungan (IP, Device, Browser) ke tabel `visits`
- 🍪 Menyimpan `affiliate_id` ke Browser Cookie (durasi 30 hari)
- 🎯 **Benefit**: Pengunjung yang kembali tanpa referral link tetap diidentifikasi sebagai klien affiliate tersebut

### B. Lead Capture & Notifikasi

#### Alur Proses:

1. **Visitor Action**: Pengunjung klik tombol "Hubungi Saya" atau isi form kontak
2. **Data Storage**: Sistem simpan data prospek ke tabel `leads`
3. **Trigger Event**: Otomatis kirim WhatsApp via GoWA API:
   - **Ke Affiliate**: 
     > "Halo, ada prospek baru atas nama [Nama Visitor] untuk properti [Nama Properti]. Segera follow up!"
   - **Ke Visitor** (Opsional):
     > "Halo, terima kasih telah menghubungi kami. Agen kami akan segera merespons Anda."

---

## 🗄 Struktur Database

### Tabel: `properties` (Katalog Properti)

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| `id` | BigInt | Primary Key |
| `title` | String | Judul Listing Properti |
| `slug` | String | URL Friendly (Unique Index) |
| `price` | BigInt | Harga (Format Rupiah) |
| `location` | Text | Alamat Lengkap / Embed Map |
| `features` | JSON | Array fitur: `["Kolam Renang", "Dekat Tol"]` |
| `specs` | JSON | Key-Value: `{"LT": "100m²", "LB": "45m²"}` |
| `status` | Enum | `draft`, `published`, `sold` |
| `media` | - | Relasi Spatie Media Library |

### Tabel: `leads` (Data Calon Pembeli)

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| `id` | BigInt | Primary Key |
| `affiliate_id` | BigInt | Relasi ke User (Affiliate) |
| `property_id` | BigInt | Properti yang diminati |
| `name` | String | Nama Visitor |
| `whatsapp` | String | Nomor WA Visitor |
| `status` | Enum | `new`, `follow_up`, `survey`, `closed`, `lost` |
| `notes` | Text | Catatan dari affiliate |

### Tabel: `visits` (Tracking Internal)

| Kolom | Tipe Data | Keterangan |
|-------|-----------|------------|
| `id` | BigInt | Primary Key |
| `affiliate_id` | BigInt | Pemilik Tautan Afiliasi |
| `visitor_ip` | String | IP Address Pengunjung |
| `device` | String | Mobile / Desktop |
| `url` | String | Halaman yang dikunjungi |
| `created_at` | Timestamp | Waktu kunjungan |

---

## 👥 Role & Fitur

### 🔴 Super Admin

#### Dashboard Utama
- 📊 Gambaran umum traffic global (Google Analytics Chart)
- 📈 Total Prospek & Konversi

#### Manajemen Properti
- ✏️ CRUD properti lengkap
- 🖼️ Upload gambar drag-and-drop
- 🔍 SEO optimization tools

#### User Management
- ✅ Validasi pendaftaran affiliate baru
- 🚫 Blokir affiliate yang melanggar ketentuan
- 👁️ Monitor aktivitas user

#### Konfigurasi Sistem
- 🔧 API Key GoWA
- 🎨 Logo Website
- 🌐 SEO Global Settings

---

### 🟢 User / Affiliate

#### Dashboard Affiliate
- 📊 **Statistik Kinerja**: "Hari ini 50 klik, 2 leads masuk"
- 📈 Grafik performansi bulanan
- 💰 Tracking komisi (jika ada)

#### Katalog & Link Generator
- 📋 Daftar properti tersedia
- 🔗 Tombol **"Copy Link Saya"** (auto-generate URL unik)
- 📥 Tombol **"Download Materi Promosi"**

#### My Leads (Manajemen Prospek)
- 📋 Tabel prospek yang masuk
- 💬 Tombol **"Click to WA"** (buka WhatsApp Web)
- 🔄 Update status prospek: `New` → `Follow Up` → `Survey` → `Closed`

#### Pengaturan Profil
- 👤 Foto profil
- ✏️ Nama display di footer halaman properti

---

### 🟡 Public Visitor

#### Pencarian Katalog
- 🔍 Filter: Lokasi, Harga, Kategori
- 🏷️ Sorting: Terbaru, Termurah, Termahal

#### Halaman Detail Properti
- 🖼️ Galeri foto interaktif
- 📝 Deskripsi lengkap
- 📊 Spesifikasi teknis (JSON-based)
- 🗺️ Embed Google Maps

#### Form Kontak
- 📱 Terintegrasi dengan tracking affiliate
- ⚡ Real-time notification via WhatsApp

---

## 🚀 Instalasi

### Requirements

- PHP >= 8.3
- Composer
- Node.js & NPM
- MySQL 8.0+ / MariaDB
- Web Server (Nginx/Apache)

### Langkah Instalasi

```bash
# Clone repository
git clone https://github.com/username/pams.git
cd pams

# Install dependencies
composer install
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Setup database
# Edit .env dengan kredensial database Anda

# Run migrations
php artisan migrate --seed

# Create storage link
php artisan storage:link

# Build assets
npm run build

# Run development server
php artisan serve
```

### Konfigurasi GoWA API

Edit file `.env`:

```env
GOWA_API_KEY=your_api_key_here
GOWA_API_URL=https://api.gowa.id/v1
```

### Setup Google Analytics

1. Dapatkan Tracking ID dari Google Analytics
2. Tambahkan di `.env`:

```env
GOOGLE_ANALYTICS_ID=UA-XXXXXXXXX-X
```

---

## 📅 Roadmap Pengembangan

**Estimasi Total: 4-6 Minggu**

### Minggu 1: Core & Database Setup
- ✅ Setup Laravel 12 + Filament 4
- ✅ Implementasi Database Schema (JSON Columns)
- ✅ Setup Filament Shield (Roles & Permissions)

### Minggu 2: Property Management
- ✅ CRUD Properti (Admin Panel)
- ✅ Integrasi Spatie Media Library
- ✅ Image Optimization

### Minggu 3: Affiliate Logic & Frontend
- ✅ Implementasi Middleware Tracking (Cookie & Session)
- ✅ Frontend Katalog dengan Livewire
- ✅ UI/UX Slicing

### Minggu 4: Integrasi Fitur
- ✅ Link Generator Logic
- ✅ Integrasi GoWA (Notifikasi)
- ✅ Setup Google Analytics

### Minggu 5: Dashboard Affiliate
- ✅ Visualisasi Data (Chart traffic)
- ✅ Tabel Lead Management
- ✅ Export Reports

### Minggu 6: Finalisasi & Deployment
- ✅ SEO Optimization
- ✅ Security Audit (Sanctum/Auth)
- ✅ Performance Testing
- ✅ Deployment ke Production Server

---

## 📝 Dokumentasi Tambahan

- [API Documentation](docs/API.md)
- [User Guide](docs/USER_GUIDE.md)
- [Deployment Guide](docs/DEPLOYMENT.md)
- [Troubleshooting](docs/TROUBLESHOOTING.md)

---

## 🤝 Contributing

Kontribusi sangat diterima! Silakan buat Pull Request atau buka Issue untuk diskusi.

1. Fork repository ini
2. Buat branch fitur (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

---

## 📄 Lisensi

Project ini dilisensikan di bawah [MIT License](LICENSE).

---

## 👨‍💻 Tim Pengembang

**Tryan Teams**

- Website: [tryanteams.com](https://tryanteams.com)
- Email: contact@tryanteams.com

---

## 💰 Budgeting

**Biaya Pengembangan Aplikasi**: Rp 15.000.000,-

Meliputi:
- ✅ Full Development (6 Minggu)
- ✅ Testing & Quality Assurance
- ✅ Deployment Setup
- ✅ Dokumentasi Lengkap
- ✅ Training & Handover

---

## 📞 Support

Untuk pertanyaan atau dukungan teknis:

- 📧 Email: support@tryanteams.com
- 💬 WhatsApp: +62-XXX-XXXX-XXXX
- 📱 Telegram: @tryanteams

---

<div align="center">

**Built with ❤️ by Tryan Teams**

⭐ Star repository ini jika bermanfaat!

</div>