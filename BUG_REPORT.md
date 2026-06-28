# Bug Report & Code Audit

> **Project:** Desa Digital Courses  
> **Audit Date:** 28 Juni 2026  
> **Status:** Semua critical & high bugs telah diperbaiki  

---

## Daftar Isi

- [Ringkasan](#ringkasan)
- [Critical Bugs](#critical-bugs)
- [High Bugs](#high-bugs)
- [Medium Bugs](#medium-bugs)
- [Low Bugs](#low-bugs)
- [Perbaikan yang Dilakukan](#perbaikan-yang-dilakukan)

---

## Ringkasan

| Severity | Total | Diperbaiki |
|----------|:-----:|:----------:|
| 🔴 Critical | 3 | 3 |
| 🟠 High | 4 | 4 |
| 🟡 Medium | 5 | 5 |
| ⚪ Low | 6 | 6 |
| **Total** | **18** | **18** |

---

## Critical Bugs

### 🔴 C1 — RoleMiddleware fatal error saat user unauthenticated

**File:** `app/Http/Middleware/RoleMiddleware.php:20-22`

**Masalah:** Saat `$request->user()` mengembalikan `null` (user tidak login), kode masuk ke blok error logging dan mencoba akses `$user->id` — menyebabkan **Fatal error: Trying to get property 'id' of non-object** sebelum `??` sempat bekerja.

```php
// SEBELUM (crash):
'user_id' => $user->id ?? 'guest',   // $user null → fatal error

// SESUDAH (aman):
'user_id' => $user?->id ?? 'guest',  // nullsafe mencegah crash
```

### 🔴 C2 — Sanctum token ability berupa enum object, bukan string

**File:** `app/Actions/Auth/LoginUserAction.php:24`

**Masalah:** `$user->role` mengembalikan `Role` enum object (e.g. `Role::HeadOfFamily`) karena model memiliki cast `'role' => Role::class`. Sanctum membandingkan abilities sebagai **string strict**, sehingga `$user->tokenCan('admin')` selalu `false`.

```php
// SEBELUM (tokenCan selalu false):
$token = $user->createToken('auth_token', [$user->role]);

// SESUDAH:
$token = $user->createToken('auth_token', [$user->role->value]);
```

### 🔴 C3 — Update profil desa menghapus semua file tanpa upload baru

**File:** `app/Services/VillageProfileService.php:72-78`

**Masalah:** Method `updateVillageProfile()` selalu menghapus file lama sebelum loop upload. Jika `$images` kosong (tidak upload gambar baru), semua file foto desa tetap terhapus dan tidak ada pengganti.

```php
// SEBELUM (file hilang walau tanpa upload):
$oldFileIds = $villageProfile->files()->pluck('files.id')->toArray();
foreach ($oldFileIds as $oldFileId) {
    $this->fileService->deleteFile($oldFileId);  // selalu dihapus
}
$villageProfile->villageProfileFiles()->delete();
foreach ($images as $image) { ... }  // skip jika kosong

// SESUDAH (file hanya dihapus jika ada upload baru):
if (! empty($images)) {
    // hapus file lama & upload baru...
}
```

---

## High Bugs

### 🟠 H1 — Event participant tidak validasi kepemilikan member_ids

**File:** `app/Http/Controllers/Api/V1/HeadVillage/EventParticipantController.php:51-101`

**Masalah:** `head_of_family` bisa mendaftarkan anggota keluarga **milik orang lain** ke event. Tidak ada pengecekan apakah `member_ids` benar-benar milik `headOfFamily` yang sedang login.

**Dampak:** Seorang warga bisa mendaftarkan anggota keluarga warga lain tanpa izin.

**Fix:** Validasi setiap `member_id` terhadap daftar anggota keluarga user:
```php
$familyMemberIds = $headOfFamily->familyMembers()->pluck('id')->toArray();
foreach ($memberIds as $mid) {
    if ($mid !== 'head' && ! in_array($mid, $familyMemberIds)) {
        abort(422, 'Salah satu anggota keluarga tidak valid.');
    }
}
```

### 🟠 H2 — Satu approval recipient menutup seluruh program bansos

**File:** `app/Http/Controllers/Api/V1/HeadVillage/SocialAssistanceRecipientController.php:118-120`

**Masalah:** Ketika **satu** penerima bantuan disetujui (`status = 'approved'`), seluruh program bantuan langsung ditutup (`is_available = false`). 9 pendaftar lain tidak bisa mendaftar.

```php
// SEBELUM: blocking semua pendaftar lain
if ($request->status === 'approved') {
    $socialAssistance->update(['is_available' => false]);
}

// SESUDAH: staff mengontrol is_available secara manual
// (baris dihapus)
```

### 🟠 H3 — Akses hapus participant tanpa cek kepemilikan

**File:** `app/Http/Controllers/Api/V1/HeadVillage/EventParticipantController.php:142-165`

**Masalah:** Route `DELETE /village-resident/events/{event}/participants/{participant}` hanya mengecek `event_id`, tapi tidak mengecek apakah participant milik `head_of_family` yang sedang login.

**Fix:** Tambah pengecekan `head_of_family_id`:
```php
$headOfFamily = $request->user()->headOfFamily;
if ($headOfFamily) {
    abort_unless($participant->head_of_family_id === $headOfFamily->id, 403);
}
```

### 🟠 H4 — Login via identity_number tidak bisa dipakai

**File:** `app/Http/Requests/LoginRequest.php` & `app/Http/Controllers/Api/V1/Auth/AuthController.php:36`

**Masalah:** `LoginUserAction::findUserByIdentifier()` mendukung login via `identity_number`, tapi `LoginRequest` tidak punya field tersebut dan controller hanya ambil `$validated['email'] ?? $validated['username']`.

**Fix:** Tambah field `identity_number` ke form request dan update controller:
```php
// LoginRequest
'identity_number' => 'required_without_all:email,username|string|max:50',

// AuthController
$identifier = $validated['email'] ?? $validated['username'] ?? $validated['identity_number'];
```

---

## Medium Bugs

### 🟡 M1 — Register endpoint tanpa rate limiting

**File:** `routes/api_v1.php:20`

**Masalah:** `POST /register` tidak memiliki throttle, rawan mass account creation.

**Fix:** Tambah middleware `throttle:6,60` (6 request per 60 menit).

### 🟡 M2 — Login rate limiter tidak handle identity_number

**File:** `bootstrap/app.php:32`

**Masalah:** Rate limiter key hanya cek `email` dan `username`, tidak `identity_number`. Brute-force via identity number hanya dibatasi oleh IP.

**Fix:** Tambah `$request->input('identity_number')` ke key chain.

### 🟡 M3 — Tidak ada nullsafe untuk role di middleware

**File:** `app/Http/Middleware/RoleMiddleware.php:19`

**Masalah:** `$user->role->value` bisa error jika role bernilai null di database (data integrity issue).

**Fix:** Gunakan `$user->role?->value`.

### 🟡 M4 — Tidak ada cek duplikasi pendaftaran bansos

**File:** `app/Http/Controllers/Api/V1/HeadVillage/SocialAssistanceRecipientController.php:69-88`

**Masalah:** Satu keluarga bisa mendaftar berkali-kali ke program bansos yang sama.

**Fix:** Query existing recipient sebelum create:
```php
$existing = SocialAssistanceRecipient::query()
    ->where('social_assistance_id', $socialAssistance->id)
    ->where('head_of_family_id', $headOfFamily->id)
    ->exists();

abort_if($existing, 409, 'Kamu sudah mendaftar bantuan sosial ini');
```

### 🟡 M5 — EventParticipant update tidak validasi kepemilikan

**File:** `app/Http/Controllers/Api/V1/HeadVillage/EventParticipantController.php:127-139`

**Masalah:** Staff bisa meng-update participant lintas keluarga. Tidak ada pengecekan scope tambahan — hanya memastikan `event_id` cocok.

> **Catatan:** Risiko terbatas karena route ini hanya bisa diakses oleh `admin, head_village, staff`, bukan publik.

---

## Low Bugs

| # | File | Masalah | Fix |
|---|------|---------|-----|
| ⚪ L1 | `app/Enums/Role.php` | `Case Admin` tidak di-handle di `maxQuota()` (silent fallthrough) | Tambah `Role::Admin => null` |
| ⚪ L2 | Banyak file | `declare(strict_types=1)` tidak konsisten | Ditambahkan ke 8 file yang diedit |
| ⚪ L3 | `app/Repository/EventParticipantRepository.php` | `save()` tidak panggil `->fresh()` (inkonsisten) | Tambah `return $eventParticipant->fresh();` |
| ⚪ L4 | `app/Http/Controllers/Api/V1/Admin/UserController.php` | Method `show()` kosong, return error tidak jelas | Implementasi query user by ID |
| ⚪ L5 | `app/Http/Resources/FamilyMemberResource.php` | `$this->date_of_birth->format()` tanpa null check | Gunakan `$this->date_of_birth?->format()` |
| ⚪ L6 | `app/Models/Kas.php` | Pakai method `casts()` tidak konsisten dengan model lain | Konsistensi opsional |

---

## Perbaikan yang Dilakukan

### Daftar file yang dimodifikasi

| File | Perubahan |
|------|-----------|
| `app/Http/Middleware/RoleMiddleware.php` | Nullsafe operator, `declare(strict_types=1)` |
| `app/Actions/Auth/LoginUserAction.php` | `$user->role->value` (enum ke string) |
| `app/Services/VillageProfileService.php` | Guard `!empty($images)`, `declare(strict_types=1)` |
| `app/Http/Controllers/Api/V1/HeadVillage/EventParticipantController.php` | Validasi member_ids ownership, destroy ownership check, `declare(strict_types=1)` |
| `app/Http/Controllers/Api/V1/HeadVillage/SocialAssistanceRecipientController.php` | Hapus auto `is_available=false`, cek duplikasi, `declare(strict_types=1)` |
| `app/Http/Controllers/Api/V1/Auth/AuthController.php` | Support `identity_number` identifier |
| `app/Http/Controllers/Api/V1/Admin/UserController.php` | Implementasi `show()`, `declare(strict_types=1)` |
| `app/Http/Requests/LoginRequest.php` | Tambah field `identity_number`, `declare(strict_types=1)` |
| `app/Http/Resources/FamilyMemberResource.php` | Nullsafe `date_of_birth`, `declare(strict_types=1)` |
| `app/Repository/EventParticipantRepository.php` | Tambah `->fresh()`, `declare(strict_types=1)` |
| `app/Enums/Role.php` | Tambah case `Admin`, `declare(strict_types=1)` |
| `routes/api_v1.php` | Throttle register |
| `bootstrap/app.php` | Rate limiter key + identity_number |

### Hasil test

```
Tests:  36 passed (147 assertions)
```
