# Desa Digital — Sistem Informasi Manajemen Desa

[![Laravel 12](https://img.shields.io/badge/Laravel-12-red?logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.5-777BB4?logo=php)](https://php.net)
[![Vue 3](https://img.shields.io/badge/Vue-3-4FC08D?logo=vue.js)](https://vuejs.org)
[![Tailwind CSS v4](https://img.shields.io/badge/Tailwind_CSS-v4-06B6D4?logo=tailwindcss)](https://tailwindcss.com)
[![Pest](https://img.shields.io/badge/Pest-4-CB2B6E?logo=pest)](https://pestphp.com)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)

**Desa Digital** adalah aplikasi web berbasis **Single Page Application (SPA)** untuk digitalisasi administrasi desa. Dibangun menggunakan Laravel 12 sebagai backend API dan Vue 3 sebagai frontend, aplikasi ini membantu perangkat desa mengelola data kependudukan, bantuan sosial, pembangunan desa, kegiatan desa, dan kas desa secara terpusat dan efisien.

---

## Fitur Utama

| Modul | Deskripsi |
|-------|-----------|
| **Manajemen Penduduk** | Kelola data Kepala Keluarga (KK) dan Anggota Keluarga dengan relasi lengkap |
| **Bantuan Sosial** | Kelola program bantuan dan pencairan (sembako, tunai, BBM, kesehatan) |
| **Pembangunan Desa** | Catat, pantau proyek pembangunan, dan kelola pendaftaran pekerja |
| **Kegiatan Desa** | Kelola event desa dan partisipasi warga (dengan pembayaran) |
| **Kas Desa** | Pantau saldo, pemasukan rutin, dan pengeluaran bulanan |
| **Profil Desa** | Informasi desa, data luas wilayah, jumlah penduduk |
| **Sistem Upload** | Upload file dengan konversi otomatis ke WebP |
| **Dashboard Berbasis Role** | 4 role dengan tampilan dan akses berbeda |

---

## Arsitektur

### Pola Desain

Aplikasi menggunakan **Service-Repository-Action Pattern** dengan lapisan yang terpisah jelas:

```
Request → Controller → Action/Service → Repository → Model → Database
                              ↓
                      ResponseHelper + API Resource
                              ↓
                          JSON Response
```

- **Actions** — Kelas invocable single-responsibility (RegisterUserAction, LoginUserAction)
- **Services** — Orchestrasi logika bisnis (AuthService, DevelopmentService, dll)
- **Repositories** — Abstraksi akses data (12 repository)
- **Form Requests** — Validasi terpusat (20 kelas)
- **API Resources** — Transformasi respons (13 resource)
- **Policies** — Otorisasi berbasis kebijakan (6 policy)

> Detail arsitektur lengkap lihat [`ARCHITECTURE.md`](ARCHITECTURE.md)

### Struktur Database (ERD)

Tabel inti:

| Tabel | Relasi Utama |
|-------|-------------|
| `users` | `1:1` → `head_of_families` |
| `head_of_families` | `1:N` → `family_members` |
| `events` | `1:N` → `event_participants` (`head_of_families`) |
| `developments` | `1:N` → `development_applicants` (`users`) |
| `social_assistances` | `1:N` → `social_assistance_recipients` (`head_of_families`) |
| `village_profiles` | `1:1` → `kas` |
| `files` | Polymorphic ke berbagai entitas |

> Semua primary key menggunakan UUID. Seluruh tabel utama menggunakan soft deletes.

### API Endpoints

| Prefix | Role | Endpoint |
|--------|------|----------|
| `POST /api/v1/login` | Publik | Autentikasi (rate limit: 5/menit) |
| `POST /api/v1/register` | Publik | Registrasi |
| `/api/v1/auth/*` | Terautentikasi | Profile, logout |
| `/api/v1/admin/*` | `admin` | Manajemen pengguna |
| `/api/v1/village-staff/*` | `admin, head_village, staff` | CRUD data desa |
| `/api/v1/village-resident/*` | `head_of_family` | Dashboard warga |

### Struktur Folder

```
app/
├── Actions/         # Single-responsibility actions
├── Enums/           # PHP 8 enums (Role, Gender, dll)
├── Helpers/         # ResponseHelper, FileHelpers, LoggerHelper
├── Http/
│   ├── Controllers/Api/V1/  # Auth, Admin, HeadVillage, FamilyMember
│   ├── Middleware/           # RoleMiddleware
│   ├── Requests/            # 20 Form Request validasi
│   └── Resources/           # 13 API Resource
├── Models/          # 13 Eloquent models (UUID, SoftDeletes)
├── Policies/        # 6 authorization policies
├── Repository/      # 12 data access repositories
└── Services/        # 11 business logic services

resources/js/
├── api/client.js    # Axios + Bearer interceptor
├── router/          # Vue Router + auth/role guards
├── stores/auth.js   # Pinia store
├── layouts/         # StaffLayout, CitizenLayout
├── components/      # Sidebar, Topbar, UI components
└── pages/           # Auth, Dashboard, CRUD pages per modul
```

---

## Role Pengguna

| Role | Prefix API | Dashboard | Kuota |
|------|-----------|-----------|-------|
| `admin` | `/admin/*`, `/village-staff/*` | Manajemen pengguna & sistem | ∞ |
| `head_village` | `/village-staff/*` | CRUD semua data desa | 1 |
| `staff` | `/village-staff/*` | Kelola data kependudukan | 3 |
| `head_of_family` | `/village-resident/*` | Dashboard warga | ∞ |

---

## Panduan Memulai

### Prasyarat

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL / MariaDB

### Instalasi

```bash
# 1. Clone repositori
git clone https://github.com/username/desa-digital-courses.git
cd desa-digital-courses

# 2. Install dependensi PHP
composer install

# 3. Environment setup
cp .env.example .env
# → Edit .env: atur DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 4. Generate key
php artisan key:generate

# 5. Jalankan migrasi & seeder
php artisan migrate --seed

# 6. Install dependensi frontend
npm install

# 7. Build aset frontend
npm run build

# 8. (Opsional) Buat storage symlink
php artisan storage:link
```

### Menjalankan Aplikasi

```bash
# Development (Laravel + Vite hot reload)
composer run dev

# Atau manual (2 terminal)
php artisan serve   # Terminal 1
npm run dev         # Terminal 2
```

### Akun Demo

Setelah `migrate --seed`, akun berikut tersedia:

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@test.com` | `admin123` |
| Kepala Desa | `kades@test.com` | `kades123` |
| Staff 1 | `staff1@test.com` | `staff123` |
| Staff 2 | `staff2@test.com` | `staff123` |
| Staff 3 | `staff3@test.com` | `staff123` |
| Warga | `slamet@test.com` | `warga123` |

---

## Testing

```bash
# Jalankan semua test
php artisan test --compact

# Test spesifik
php artisan test --compact --filter=LoginTest
```

Framework testing: **Pest 4** dengan SQLite in-memory.

---

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| **Backend** | Laravel 12, PHP 8.5, Sanctum |
| **Frontend** | Vue 3 (Composition API), Vue Router, Pinia |
| **Styling** | Tailwind CSS v4 |
| **Build** | Vite 7 |
| **Database** | MySQL / MariaDB |
| **Testing** | Pest 4, Mockery |
| **Tools** | Laravel Pint, Laravel Sail, Laravel Pail |

---

## Lisensi

Proyek ini menggunakan lisensi **MIT**. Silakan lihat file [LICENSE](LICENSE) untuk informasi lebih lanjut.
