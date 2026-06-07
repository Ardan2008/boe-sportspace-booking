# Design Document - Hybrid Room Type Dropdown

## Overview

Fitur ini mengimplementasikan **Hybrid Dropdown Permanen untuk Tipe Kamar** pada aplikasi BOE Space Reserve Booking (Laravel + Alpine.js). Komponen ini memungkinkan admin memilih, menambah, mengubah, dan menghapus tipe kamar langsung dari dalam dropdown — tanpa berpindah halaman — saat mengisi form Create atau Edit Fasilitas.

Komponen terdiri dari tiga lapisan utama:

1. **Backend API** — `GlobalRoomTypeController` yang melayani operasi CRUD asinkron terhadap tabel `global_room_types` melalui route group `/admin/room-types`.
2. **Alpine.js Component** — `roomTypeDropdown`, terdaftar secara global via `Alpine.data()`, mengelola seluruh state UI (pemilihan, tambah, edit, hapus) dan berkomunikasi ke backend via Fetch API.
3. **Blade Partial** — `_room_type_dropdown.blade.php`, berisi markup HTML yang menerima parameter `$roomIndex` dan `$roomsVar` untuk menyesuaikan diri ke setiap kamar dalam loop Alpine `x-for`.

Arsitektur ini memungkinkan banyak instance dropdown (satu per baris kamar) berbagi daftar `roomTypes` yang sama melalui `window.__alpineRoot`, sehingga perubahan data (tambah/ubah/hapus) langsung terefleksi di seluruh instance pada halaman yang sama.

---

## Architecture

### Gambaran Tingkat Tinggi

```mermaid
graph TD
    A[Admin Browser] -->|GET/POST/PUT/DELETE| B[Laravel Routes /admin/room-types]
    B --> C[Middleware: admin.access:can_edit]
    C --> D[GlobalRoomTypeController]
    D --> E[GlobalRoomType Model]
    E --> F[(DB: global_room_types)]

    A -->|Renders| G[createFasilitas.blade.php / editFasilitas.blade.php]
    G -->|@include| H[_room_type_dropdown.blade.php]
    G -->|Alpine.data| I[roomTypeDropdown Component]
    I -->|allTypes / setTipe via| J[window.__alpineRoot]
    J -->|shared roomTypes| K[Parent Alpine Component rooms]
```

### Alur Data

1. **Inisialisasi halaman**: Controller meneruskan `$roomTypes` (hasil `GlobalRoomType::orderBy('name')->get(['id','name'])`) ke view Blade. Alpine membaca data ini dari variabel yang diinjeksi (`@json($roomTypes->toArray())`) ke `window.__alpineRoot.roomTypes` milik komponen induk.

2. **Interaksi runtime**: Setiap operasi CRUD dilakukan via Fetch API langsung dari `roomTypeDropdown`. Setelah respons sukses, Alpine memutasi `window.__alpineRoot.roomTypes` secara reaktif, sehingga semua dropdown instance pada halaman ikut diperbarui.

3. **Pengiriman form**: Sebuah `<input type="hidden">` dengan `x-bind:name` dan `x-bind:value` pada setiap instance dropdown memastikan nilai `rooms[n].tipe` ikut terkirim dalam payload form HTML standar.

### Stack Teknologi

| Layer | Teknologi |
|-------|-----------|
| Backend | Laravel 11, PHP |
| ORM | Eloquent (`GlobalRoomType`) |
| Frontend Reaktivitas | Alpine.js v3 |
| HTTP Client | Fetch API (browser native) |
| Templating | Blade (partial include) |
| Dialog konfirmasi | SweetAlert2 |
| Animasi | Alpine `x-transition` directives |
| Auth/Security | Laravel middleware `admin.access:can_edit`, CSRF token |

---

## Components and Interfaces

### 1. `GlobalRoomTypeController`

File: `app/Http/Controllers/GlobalRoomTypeController.php`

Menangani seluruh operasi CRUD untuk tipe kamar via JSON API.

| Method | Route | HTTP | Request Body | Response |
|--------|-------|------|--------------|----------|
| `index()` | `/admin/room-types` | GET | — | `[{id, name}, ...]` 200 |
| `store()` | `/admin/room-types` | POST | `{name: string}` | `{id, name}` 201 / `{errors}` 422 |
| `update()` | `/admin/room-types/{id}` | PUT | `{name: string}` | `{id, name}` 200 / `{errors}` 422 |
| `destroy()` | `/admin/room-types/{id}` | DELETE | — | `{success: true}` 200 / 404 |

Semua route di-group dalam `middleware(['admin.access:can_edit'])`.

### 2. `GlobalRoomType` Model

File: `app/Models/GlobalRoomType.php`

```php
protected $table    = 'global_room_types';
protected $fillable = ['name'];
```

Eloquent timestamps aktif secara default (`created_at`, `updated_at`).

### 3. Alpine.js Component: `roomTypeDropdown`

Didaftarkan di `createFasilitas.blade.php` via `Alpine.data('roomTypeDropdown', function() { ... })` dan dapat digunakan dari view Edit Fasilitas dengan definisi yang sama.

#### State Properties

| Property | Tipe | Deskripsi |
|----------|------|-----------|
| `open` | `boolean` | Apakah panel dropdown sedang terbuka |
| `addMode` | `boolean` | Apakah form tambah tipe baru sedang aktif |
| `editingId` | `number\|null` | ID tipe kamar yang sedang di-edit (null = tidak ada) |
| `editingName` | `string` | Nilai input saat mode edit aktif |
| `newTypeName` | `string` | Nilai input saat mode tambah aktif |
| `saving` | `boolean` | Apakah request POST/PUT sedang diproses |
| `deleting` | `number\|null` | ID tipe yang sedang dihapus (untuk disabled state) |

#### Methods

| Method | Deskripsi |
|--------|-----------|
| `allTypes()` | Mengembalikan `window.__alpineRoot?.roomTypes ?? []` |
| `currentTipe()` | Membaca `rooms[rIdx].tipe` dari komponen induk via `window.__alpineRoot` |
| `setTipe(val)` | Menulis nilai ke `rooms[rIdx].tipe` via `window.__alpineRoot` |
| `toggle()` | Membuka/menutup panel; reset `addMode` dan `editingId` saat menutup |
| `close()` | Menutup panel dan reset semua sub-state |
| `selectType(name)` | Memanggil `setTipe(name)` lalu `close()` |
| `startAdd()` | Set `addMode = true`, fokus ke input baru |
| `saveNew()` | Fetch POST, update `roomTypes`, auto-select tipe baru |
| `startEdit(id, name)` | Set `editingId` dan `editingName` |
| `saveEdit(id)` | Fetch PUT, update nama di `roomTypes`, clear `editingId` |
| `deleteType(id, name)` | Tampilkan SweetAlert2, Fetch DELETE, hapus dari `roomTypes` |

#### Data Bridge via `window.__alpineRoot`

Komponen induk (form fasilitas) meng-expose dirinya ke `window.__alpineRoot = this` saat Alpine `init`. Dropdown children mengakses `window.__alpineRoot.roomTypes` (shared list) dan `window.__alpineRoot[rVar][rIdx].tipe` (nilai per kamar). Ini memungkinkan banyak instance dropdown tetap sinkron tanpa event bus.

```
window.__alpineRoot
  ├── roomTypes: [{id, name}, ...]   ← shared across all instances
  └── rooms: [{tipe, jumlah, ...}]   ← per-room state (indexed by rIdx)
```

### 4. Blade Partial: `_room_type_dropdown.blade.php`

File: `resources/views/admin/dashboard/partials/_room_type_dropdown.blade.php`

#### Parameter yang Diterima

| Parameter | Tipe | Deskripsi |
|-----------|------|-----------|
| `$roomIndex` | `int` | Indeks kamar dalam array `rooms` Alpine |
| `$roomsVar` | `string` | Nama variabel array rooms di komponen Alpine induk (misal: `'rooms'`) |

Parameter ini di-render ke atribut `data-*` pada elemen root:

```html
<div x-data="roomTypeDropdown"
     data-rooms-var="{{ $roomsVar }}"
     data-room-index="{{ $roomIndex }}"
     ...>
```

Component membaca nilai ini via `el.dataset.roomsVar` dan `parseInt(el.dataset.roomIndex)`.

#### Hidden Input

```html
<input type="hidden"
       x-bind:name="'rooms[' + rIdx + '][tipe]'"
       x-bind:value="currentTipe()">
```

Memastikan nilai tipe kamar terpilih ikut terkirim saat form di-submit.

### 5. `FasilitasController` (Integrasi)

Metode `edit($id)` dan route `dashboardcreateFasilitas` sudah melewatkan `$roomTypes` ke view:

```php
$roomTypes = \App\Models\GlobalRoomType::orderBy('name')->get(['id', 'name']);
return view('...', compact('roomTypes'));
```

View kemudian menginisialisasi Alpine dengan data ini:

```js
window.__alpineRoot.roomTypes = @json($roomTypes->toArray());
```

---

## Data Models

### Tabel `global_room_types`

| Kolom | Tipe | Constraint | Deskripsi |
|-------|------|-----------|-----------|
| `id` | `bigint unsigned` | PK, auto-increment | Identifier unik |
| `name` | `varchar(100)` | NOT NULL, UNIQUE | Nama tipe kamar (sudah di-trim) |
| `created_at` | `timestamp` | nullable | Waktu dibuat |
| `updated_at` | `timestamp` | nullable | Waktu diperbarui terakhir |

**Invariant**: Nilai `name` selalu disimpan tanpa leading/trailing whitespace (controller melakukan `trim()` sebelum menyimpan). Tidak ada dua baris yang memiliki `name` yang sama (constraint `unique`).

### Struktur JSON `paket_harian` pada Tabel `fasilitass`

Setiap elemen array merepresentasikan satu kamar/ruangan:

```json
[
  {
    "tipe": "Standar",
    "jumlah": 1,
    "kode_blok": "A",
    "max_dewasa": 2,
    "max_anak": 1,
    "foto": [],
    "harga_harian": 500000,
    "harga_mingguan": "",
    "harga_bulanan": 1500000,
    "harga_tahunan": ""
  }
]
```

Kolom `tipe` menyimpan `name` dari `GlobalRoomType` sebagai string (denormalized). Jika tipe kamar dihapus dari `global_room_types`, kolom `tipe` pada data paket yang sudah tersimpan tidak otomatis dikosongkan — hanya nilai yang sedang aktif di UI Alpine yang di-reset oleh komponen.

### State Alpine.js `roomTypeDropdown`

```
{
  open:        false,        // boolean — panel visibility
  addMode:     false,        // boolean — add form visibility
  editingId:   null,         // number|null — ID of type being edited
  editingName: '',           // string — current edit input value
  newTypeName: '',           // string — current add input value
  saving:      false,        // boolean — async POST/PUT in progress
  deleting:    null,         // number|null — ID of type being deleted
}
```

---

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system — essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Reflection

Setelah prework analysis, properti-properti berikut dikonsolidasi:
- **3.3 dan 8.2** digabung: auto-select setelah POST sukses sudah mencakup keduanya.
- **7.1 dan 7.2** digabung: respons 422 adalah konsekuensi langsung dari validasi gagal.
- **8.1** (loading state) digabung dengan operasi spesifik (3.2, 4.3, 5.3) menjadi satu properti loading state yang lebih umum.

---

### Property 1: Daftar tipe kamar selalu urut ascending berdasarkan nama

*For any* set of room type names yang tersimpan di tabel `global_room_types`, response dari `GET /admin/room-types` SHALL mengembalikan array yang urutan elemen-elemennya ascending secara alfabetis berdasarkan field `name`.

**Validates: Requirements 1.2**

---

### Property 2: Pemilihan tipe kamar memperbarui state rooms yang tepat

*For any* array `rooms` dengan panjang n, dan *for any* indeks `i` dalam rentang `[0, n-1]`, dan *for any* nama tipe kamar yang valid, setelah `selectType(name)` dipanggil pada instance dropdown dengan `roomIndex = i`, nilai `rooms[i].tipe` SHALL sama dengan `name` dan semua `rooms[j].tipe` untuk `j ≠ i` SHALL tidak berubah.

**Validates: Requirements 2.1**

---

### Property 3: Highlight item mencerminkan tipe yang sedang terpilih

*For any* daftar tipe kamar dan *for any* satu tipe yang ditetapkan sebagai `currentTipe()`, hanya elemen item dengan `name === currentTipe()` yang SHALL memiliki kelas `text-[#1265A8] bg-blue-50`, sedangkan semua item lainnya SHALL tidak memiliki kelas tersebut.

**Validates: Requirements 2.3**

---

### Property 4: Tombol Tambah dinonaktifkan untuk input yang hanya berisi whitespace

*For any* string yang seluruhnya terdiri dari karakter whitespace (termasuk string kosong), nilai `newTypeName.trim()` SHALL menjadi falsy, dan tombol "Tambah" SHALL memiliki atribut `disabled` sehingga `saveNew()` tidak dapat dipanggil melalui UI.

**Validates: Requirements 3.6**

---

### Property 5: Penambahan tipe baru menambah roomTypes dan auto-select

*For any* nama tipe kamar baru yang valid (non-empty, non-duplicate), setelah `saveNew()` berhasil (HTTP 201), `window.__alpineRoot.roomTypes` SHALL mengandung entry baru dengan `{id, name}` yang diterima dari server, dan `rooms[roomIndex].tipe` SHALL langsung diset ke nama tersebut tanpa interaksi tambahan.

**Validates: Requirements 3.2, 3.3, 8.2**

---

### Property 6: Mode edit mengirim PUT dengan payload yang benar

*For any* `id` tipe kamar dan *for any* nama baru yang valid, pemanggilan `saveEdit(id)` SHALL mengirim tepat satu request PUT ke `/admin/room-types/{id}` dengan body JSON `{ name: editingName.trim() }` dan header `X-CSRF-TOKEN`.

**Validates: Requirements 4.3, 7.4**

---

### Property 7: Update nama tipe kamar memperbarui seluruh referensi di roomTypes

*For any* tipe kamar dengan `id` tertentu yang tersimpan di `window.__alpineRoot.roomTypes`, setelah `saveEdit(id)` berhasil (HTTP 200) dengan `name` baru yang dikembalikan server, semua entri di `roomTypes` dengan `id` tersebut SHALL memiliki `name` diperbarui, dan jika `currentTipe()` sama dengan nama lama, nilai `rooms[roomIndex].tipe` SHALL otomatis diperbarui ke nama baru.

**Validates: Requirements 4.4**

---

### Property 8: Penghapusan tipe kamar menghapus entri dari roomTypes dan mengosongkan tipe jika aktif

*For any* tipe kamar `{id, name}` yang ada di `window.__alpineRoot.roomTypes`, setelah `deleteType(id, name)` dikonfirmasi dan request DELETE berhasil (HTTP 200), tipe tersebut SHALL tidak lagi ada dalam array `roomTypes`, dan *for any* kamar `rooms[i]` yang `tipe === name`, nilai `rooms[i].tipe` SHALL menjadi string kosong `''`.

**Validates: Requirements 5.3, 5.4, 5.5**

---

### Property 9: Validasi server menolak nama yang tidak valid

*For any* nilai `name` yang melanggar aturan validasi (string kosong, panjang > 100 karakter, atau nama yang sudah ada di tabel untuk operasi POST, atau nama milik record lain untuk operasi PUT), `GlobalRoomTypeController` SHALL mengembalikan HTTP 422 dengan body JSON yang mengandung detail error di field `errors.name`.

**Validates: Requirements 7.1, 7.2**

---

### Property 10: Nama tipe selalu disimpan dalam bentuk trimmed

*For any* nama yang dikirim ke POST `/admin/room-types` atau PUT `/admin/room-types/{id}` yang mengandung leading dan/atau trailing whitespace, nama yang tersimpan di database SHALL sama dengan `trim()` dari nama tersebut — sehingga `" Standar "` dan `"Standar"` menghasilkan nilai yang tersimpan identik.

**Validates: Requirements 7.5**

---

### Property 11: Semua instance dropdown berbagi roomTypes yang sama via window.__alpineRoot

*For any* jumlah instance `roomTypeDropdown` yang dirender pada halaman yang sama (dalam konteks `x-for`), perubahan apapun pada `window.__alpineRoot.roomTypes` (push, splice, atau update nama) SHALL segera terefleksi di return value `allTypes()` pada setiap instance, tanpa perlu refresh halaman.

**Validates: Requirements 6.4, 6.6**

---

### Property 12: Hidden input selalu mencerminkan tipe yang dipilih per kamar

*For any* nilai `rooms[i].tipe` dan indeks `i`, elemen `<input type="hidden">` pada instance dropdown ke-`i` SHALL memiliki `name="rooms[i][tipe]"` dan `value` yang sama dengan `rooms[i].tipe` secara reaktif, sehingga perubahan pada tipe SHALL langsung tercermin di hidden input sebelum form di-submit.

**Validates: Requirements 2.4, 6.5**

---

### Property 13: Trigger button selalu menampilkan state terpilih yang akurat

*For any* nilai `rooms[roomIndex].tipe` (termasuk string kosong), teks yang ditampilkan pada tombol trigger dropdown SHALL sama dengan `tipe` tersebut jika non-empty, atau teks placeholder `"Pilih Tipe Kamar"` jika `tipe` adalah string kosong atau undefined.

**Validates: Requirements 8.4**

---

## Error Handling

### Strategi Umum

Semua error handling di frontend dilakukan di dalam blok `try/catch/finally` pada setiap async method. Error tidak pernah ditelan diam-diam — selalu ada umpan balik visual kepada pengguna.

### Error dari Backend (4xx / 5xx)

| Skenario | Respons Server | Penanganan Frontend |
|---------|----------------|---------------------|
| Nama sudah ada (POST) | HTTP 422, `{message, errors}` | Tampilkan "Tipe kamar ini sudah ada." di area dropdown, `addMode` tetap terbuka |
| Nama sudah digunakan (PUT) | HTTP 422, `{message, errors}` | Tampilkan "Nama sudah digunakan." di area dropdown, `editingId` tetap aktif |
| Validasi lain gagal | HTTP 422, `{message}` | Tampilkan `data.message` dari respons |
| ID tidak ditemukan (PUT/DELETE) | HTTP 404 | Tampilkan pesan error umum, perbarui state jika perlu |
| Error server internal | HTTP 500 | Tampilkan "Terjadi kesalahan sistem." |

### Error Jaringan

Jika `fetch()` melempar exception (misalnya koneksi putus), blok `catch` menampilkan "Terjadi kesalahan jaringan." di dalam area dropdown.

### Loading State

Selama request berlangsung:
- `saving = true` → tombol "Tambah" dan "✓" menjadi `disabled`, teks berubah ke `"…"`.
- `deleting = id` → tombol hapus pada item bersangkutan menjadi `disabled`.
- Setelah request selesai (sukses atau gagal), `saving = false` dan `deleting = null` diset di blok `finally`.

### Konfirmasi Destruktif

Operasi hapus selalu memerlukan konfirmasi melalui SweetAlert2 yang menyebutkan nama tipe secara eksplisit sebelum request DELETE dikirim. Jika admin membatalkan, tidak ada request yang dikirim.

### Integritas Data Client-Side

Jika tipe kamar yang sedang dipilih oleh suatu kamar dihapus, `rooms[roomIndex].tipe` di-reset ke `''` agar tidak ada nilai "dangling" yang terkirim saat form di-submit.

---

## Testing Strategy

### Pendekatan Pengujian

Fitur ini menggunakan dua lapisan pengujian yang saling melengkapi:

1. **Backend Unit & Integration Tests** (PHP / PHPUnit / PestPHP) — menguji logika controller, validasi, dan model.
2. **Frontend Unit & Property Tests** (JavaScript / Vitest + fast-check) — menguji logika Alpine.js component secara terisolasi.

Property-based testing (PBT) **sesuai** untuk fitur ini karena:
- `GlobalRoomTypeController` adalah fungsi-fungsi murni yang menerima input berbeda (nama tipe, id) dan mengembalikan output yang deterministik.
- Logika Alpine component (`allTypes()`, `selectType()`, `saveNew()`, dll.) memiliki input space yang luas (nama string sembarang, indeks sembarang, array roomTypes sembarang).
- Terdapat banyak invariant yang harus berlaku untuk seluruh input (sorting, trim, highlight, hidden input sync).

### Backend Tests (PHPUnit / PestPHP)

#### Property-Based Tests

Gunakan library seperti [eris/eris](https://github.com/giorgiosironi/eris) atau kombinasi Faker + data provider untuk mensimulasikan input acak:

**Property 1 — Sorting**: Generate N nama acak, insert ke DB, panggil `GET /admin/room-types`, verifikasi response `name` urut ascending.

**Property 9 — Validasi nama invalid**: Generate nama kosong, nama dengan panjang > 100 char, nama duplikat. Untuk setiap input, panggil `POST /admin/room-types`, verifikasi HTTP 422 dan `errors.name` ada dalam response.

**Property 10 — Trim whitespace**: Generate nama dengan berbagai kombinasi leading/trailing spaces. Panggil `store()/update()`, baca dari DB, verifikasi nama tersimpan = `trim(input)`.

#### Example-Based Tests

- `GET /admin/room-types` tanpa auth → 401/403
- `POST /admin/room-types` dengan nama valid → 201 + data tipe baru
- `PUT /admin/room-types/{id}` yang tidak ada → 404
- `DELETE /admin/room-types/{id}` yang tidak ada → 404
- `DELETE /admin/room-types/{id}` yang ada → 200 + `{success: true}`

### Frontend Tests (Vitest + fast-check)

Library PBT yang digunakan: **[fast-check](https://fast-check.dev/)** (JavaScript, aktif dirawat, terintegrasi baik dengan Vitest).

Konfigurasi minimum: `{ numRuns: 100 }` per property test.

Setiap test ditag dengan komentar format:
```
// Feature: hybrid-room-type-dropdown, Property N: <property_text>
```

#### Property Tests

**Property 2 — Pemilihan tipe memperbarui state yang tepat**
```
// Feature: hybrid-room-type-dropdown, Property 2: selectType updates only rooms[roomIndex].tipe
fc.property(fc.array(roomGen), fc.nat(), fc.string(), ...)
```
Generate array rooms, indeks acak, nama acak. Panggil `selectType(name)`. Verifikasi hanya `rooms[idx].tipe` berubah.

**Property 3 — Highlight mencerminkan tipe terpilih**
```
// Feature: hybrid-room-type-dropdown, Property 3: only selected item has highlight classes
```
Generate daftar tipe acak, pilih satu sebagai current. Verifikasi hanya item tersebut memiliki kelas highlight.

**Property 4 — Tombol Tambah dinonaktifkan untuk whitespace**
```
// Feature: hybrid-room-type-dropdown, Property 4: add button disabled for whitespace-only input
```
Generate string whitespace-only (termasuk `''`, `'   '`, `'\t\n'`). Verifikasi `newTypeName.trim()` falsy dan tombol disabled.

**Property 5 — Penambahan tipe baru: state update dan auto-select**
```
// Feature: hybrid-room-type-dropdown, Property 5: saveNew adds to roomTypes and auto-selects
```
Mock fetch POST → 201. Generate nama acak. Panggil `saveNew()`. Verifikasi `roomTypes` bertambah dan `rooms[roomIndex].tipe === name`.

**Property 6 — saveEdit mengirim PUT dengan payload benar**
```
// Feature: hybrid-room-type-dropdown, Property 6: saveEdit sends PUT with correct payload
```
Generate id dan nama acak. Spy pada `fetch`. Panggil `saveEdit(id)`. Verifikasi URL `/admin/room-types/{id}`, method PUT, body `{name: trimmed}`, header `X-CSRF-TOKEN`.

**Property 7 — Update nama memperbarui roomTypes dan tipe aktif**
```
// Feature: hybrid-room-type-dropdown, Property 7: successful update propagates name change
```
Generate roomTypes, pick satu, mock PUT → 200. Verifikasi nama diperbarui di array dan `currentTipe()` ikut diperbarui jika cocok.

**Property 8 — Penghapusan menghapus dari roomTypes dan mengosongkan tipe aktif**
```
// Feature: hybrid-room-type-dropdown, Property 8: delete removes type and clears matching rooms
```
Generate roomTypes, set salah satu sebagai `currentTipe()`. Mock DELETE → 200. Verifikasi entri hilang dari `roomTypes` dan `rooms[roomIndex].tipe === ''`.

**Property 11 — Shared state via window.__alpineRoot**
```
// Feature: hybrid-room-type-dropdown, Property 11: all instances see same roomTypes
```
Inisialisasi dua instance. Mutasi `window.__alpineRoot.roomTypes`. Verifikasi `allTypes()` di kedua instance mengembalikan array yang sama.

**Property 12 — Hidden input sinkron dengan tipe terpilih**
```
// Feature: hybrid-room-type-dropdown, Property 12: hidden input reflects rooms[i].tipe
```
Generate berbagai nilai tipe. Set `rooms[i].tipe`. Verifikasi hidden input `value === tipe` dan `name === 'rooms[i][tipe]'`.

**Property 13 — Trigger button menampilkan nilai yang akurat**
```
// Feature: hybrid-room-type-dropdown, Property 13: trigger shows tipe or placeholder
```
Generate tipe string acak termasuk string kosong. Verifikasi trigger button menampilkan tipe (jika non-empty) atau placeholder "Pilih Tipe Kamar" (jika empty).

#### Example-Based Tests

- Klik di luar dropdown menutup panel (`open = false`)
- Tekan Escape saat `addMode = true` → `addMode = false`
- Tekan Escape saat `editingId !== null` → `editingId = null`
- Klik batal (✕) saat edit tidak mengirim request
- Klik batal pada SweetAlert2 tidak mengirim DELETE
- Error 422 dengan pesan "unique" menampilkan "Tipe kamar ini sudah ada."
- Daftar kosong menampilkan pesan "Belum ada tipe kamar. Tambahkan di bawah."

### Smoke Tests

- Rendered partial mengandung elemen `<input type="hidden">` dengan `x-bind:name` dan `x-bind:value`
- Atribut `data-rooms-var` dan `data-room-index` dirender dengan nilai yang benar dari parameter Blade
- Panel dropdown memiliki directive `x-transition` untuk animasi buka/tutup
- Tombol edit/hapus memiliki kelas `opacity-0 group-hover:opacity-100`
