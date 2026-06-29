# Arsitektur Desa Digital

## Stack

| Layer | Teknologi |
|-------|-----------|
| **Backend** | Laravel 12 + PHP 8.5 |
| **Frontend** | Vue 3 + Vue Router + Pinia |
| **API Auth** | Laravel Sanctum (Token-based) |
| **CSS** | Tailwind CSS v4 |
| **Build** | Vite 7 |
| **Database** | MySQL / MariaDB |
| **Testing** | Pest 4 |

---

## Struktur Folder

```
desa-digital-courses/
│
├── app/                            # Laravel Backend
│   ├── Actions/                    # 🔹 Single Responsibility Actions
│   │   └── Auth/
│   │       ├── RegisterUserAction.php
│   │       ├── LoginUserAction.php
│   │       └── LogoutUserAction.php
│   │
│   ├── Enums/                      # PHP 8 Enums
│   │   ├── Role.php                # Admin, HeadVillage, Staff, HeadOfFamily
│   │   ├── Gender.php
│   │   ├── MaritalStatus.php
│   │   └── FamilyRelation.php
│   │
│   ├── Helpers/                    # Utility Helpers (all with strict_types & return types)
│   │   ├── ResponseHelper.php      # Standard JSON response
│   │   ├── FileHelpers.php         # File upload utilities
│   │   └── LoggerHelper.php        # Secure logging (no tokens/credentials logged)
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/V1/
│   │   │       ├── Auth/           # AuthController
│   │   │       ├── Admin/          # UserController (admin only)
│   │   │       ├── HeadVillage/    # CRUD untuk Kepala Desa
│   │   │       └── FamilyMember/   # Untuk role Kepala Keluarga
│   │   ├── Middleware/
│   │   │   └── RoleMiddleware.php
│   │   ├── Requests/               # Form Request validasi
│   │   └── Resources/              # Eloquent API Resources
│   │
│   ├── Models/                     # Eloquent Models
│   ├── Providers/
│   ├── Repository/                 # Data access layer (12 repos)
│   └── Services/                   # Business logic layer (12 services)
│       ├── EventService.php
│       ├── EventParticipantService.php
│       ├── DevelopmentService.php
│       ├── KasService.php
│       ├── SocialAssistanceService.php
│       ├── VillageProfileService.php
│       ├── FileService.php
│       ├── UserService.php
│       └── ...
│
├── resources/                      # Vue 3 Frontend
│   ├── js/
│   │   ├── app.js                  # Vue entry point
│   │   ├── App.vue                 # Root component
│   │   │
│   │   ├── api/
│   │   │   └── client.js           # Axios instance + Bearer interceptor
│   │   │
│   │   ├── router/
│   │   │   └── index.js            # Vue Router + auth/role guards
│   │   │
│   │   ├── stores/
│   │   │   └── auth.js             # Pinia auth store
│   │   │
│   │   ├── helpers/
│   │   │   ├── format.js           # Format Rupiah, tanggal
│   │   │   └── permissionHelper.js # Permission check
│   │   │
│   │   ├── layouts/
│   │   │   └── AdminLayout.vue     # Main dashboard layout
│   │   │
│   │   ├── components/
│   │   │   ├── sidebar/
│   │   │   │   ├── Sidebar.vue     # Sidebar navigation
│   │   │   │   └── SidebarItem.vue # Menu item + accordion
│   │   │   ├── Topbar.vue          # Top bar (search, avatar, logout)
│   │   │   └── ui/                 # Reusable UI components
│   │   │       ├── Button.vue
│   │   │       ├── Input.vue
│   │   │       ├── ModalDelete.vue
│   │   │       └── Pagination.vue
│   │   │
│   │   ├── pages/
│   │   │   ├── auth/
│   │   │   │   └── Login.vue
│   │   │   ├── dashboard/
│   │   │   │   └── Index.vue       # Statistics + shortcuts
│   │   │   ├── users/
│   │   │   │   └── Index.vue       # Admin only
│   │   │   ├── head-families/
│   │   │   │   └── Index.vue       # Table Kepala Rumah
│   │   │   ├── family-members/
│   │   │   │   ├── Index.vue       # KD: lihat anggota via head-family
│   │   │   │   └── KKIndex.vue     # KK: lihat anggota sendiri
│   │   │   ├── events/
│   │   │   │   └── Index.vue       # Table Event Desa
│   │   │   ├── social-assistances/
│   │   │   │   ├── Index.vue       # Table Bantuan Sosial
│   │   │   │   └── Recipients.vue  # Pengajuan Bansos
│   │   │   ├── developments/
│   │   │   │   └── Index.vue       # Table Pembangunan
│   │   │   └── village-profile/
│   │   │       └── Index.vue       # Profile Desa
│   │   │
│   │   └── assets/                 # Icons & images (copied from public/desa-digital/)
│   │
│   ├── css/
│   │   └── app.css                 # Tailwind v4 + desa theme colors
│   │
│   └── views/
│       └── app.blade.php           # SPA entry blade
│
├── routes/
│   ├── web.php                     # Catch-all route → SPA
│   ├── api.php                     # Loader for api_v1.php
│   ├── api_v1.php                  # All API routes
│   └── console.php
│
├── database/
│   ├── migrations/                 # Table schemas
│   ├── factories/                  # Model factories
│   └── seeders/                    # Database seeders
│
├── public/
│   ├── build/                      # Vite production build output
│   └── desa-digital/               # Existing static design files
│       ├── components/             # Vue component originals
│       └── src/                    # HTML prototypes + assets
│
├── tests/
│   ├── Feature/
│   │   └── Auth/
│   │       ├── LoginTest.php
│   │       ├── LogoutTest.php
│   │       └── RegisterTest.php
│   └── Pest.php                    # Global helpers (createUserWithPassword)
│
├── ARCHITECTURE.md                 # This file
├── AGENTS.md                       # AI coding guidelines
├── vite.config.js                  # Vite + Vue + Tailwind + alias @
└── package.json                    # Frontend dependencies
```

---

## Aliran Data

```
Browser → Vue Router → Page Component → API Client (Axios)
                                              ↓
                                         Laravel API
                                     (auth:sanctum)
                                              ↓
                          ┌──────────────────────────────┐
                          │  Controller (thin)            │
                          │    → FormRequest (validation) │
                          │    → Action/Service (logic)    │
                          │    → ResponseHelper + Resource │
                          └──────────────────────────────┘
                                              ↓
                                          JSON Response
                                              ↓
                                     Vue Component → Render
```

> **Security:** Token abilities tidak digunakan untuk authorization — semua akses dikontrol via `RoleMiddleware` + role enum. Token tidak pernah di-log, bahkan secara parsial.

---

## Role & Hak Akses

| Role | Endpoint Prefix | Dashboard |
|------|----------------|-----------|
| `admin` | `/admin/*` | Kelola users |
| `head_village` | `/village-staff/*` | CRUD semua data |
| `staff` | `/village-staff/*` | Sama dengan head_village |
| `head_of_family` | `/village-resident/*` | Lihat + daftar (events/bansos) |

---

## Cara Menjalankan

```bash
# Development (Laravel + Vite hot reload)
composer run dev

# Atau manual
php artisan serve    # Terminal 1
npm run dev          # Terminal 2

# Build production
npm run build

# Testing
php artisan test --compact
```

---

## Design System

Warna kustom (didefinisikan di `resources/css/app.css` via `@theme`):

| Token | Value | Penggunaan |
|-------|-------|-----------|
| `desa-dark-green` | `#34613A` | Tombol, teks aktif |
| `desa-soft-green` | `#8EBD55` | Badge sukses |
| `desa-secondary` | `#5A7A7E` | Teks sekunder |
| `desa-background` | `#F2F9F6` | Background halaman |
| `desa-foreshadow` | `#F1FAE6` | Hover, active state |
| `desa-black` | `#001B1A` | Tombol manage |
| `desa-red` | `#FF5070` | Status closed/ditolak |
| `desa-yellow` | `#FBAD4A` | Status pending |

Font: **Lexend Deca** (Google Fonts)
