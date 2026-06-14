# Desa Digital

Platform manajemen administrasi desa berbasis web. Dibangun untuk membantu pengelolaan data kependudukan, bantuan sosial, pembangunan desa, dan kegiatan desa secara digital.

## Fitur

- **Manajemen Penduduk** — Kelola data Kepala Keluarga dan Anggota Keluarga
- **Bantuan Sosial** — Kelola program bantuan dan penerima bantuan
- **Pembangunan Desa** — Catat dan pantau proyek pembangunan desa
- **Kegiatan Desa** — Kelola event dan partisipasi warga
- **Dashboard Staff** — Interface untuk admin dan staf desa
- **Dashboard Warga** — Interface untuk warga (Kepala Keluarga)

## Tech Stack

- **Backend:** Laravel 12, PHP 8.5
- **Frontend:** Vue 3 (Composition API, Vue Router)
- **Styling:** Tailwind CSS v4
- **State Management:** Pinia
- **Testing:** Pest
- **API:** RESTful, Sanctum authentication

## Role Pengguna

| Role | Akses |
|------|-------|
| `admin` | Manajemen pengguna, pengaturan sistem |
| `head_village` | Dashboard kepala desa |
| `staff` | Dashboard staf, kelola data kependudukan |
| `head_of_family` | Dashboard warga, kelola anggota keluarga |
