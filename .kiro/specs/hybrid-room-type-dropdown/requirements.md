# Requirements Document

## Introduction

Fitur ini menambahkan **Hybrid Dropdown Permanen untuk Tipe Kamar** pada aplikasi booking ruangan/fasilitas BOE Sport Space (Laravel). Dropdown memungkinkan admin memilih tipe kamar dari daftar yang sudah tersimpan di database, sekaligus menambahkan tipe kamar baru langsung dari dalam dropdown tanpa berpindah halaman. Semua operasi (baca, tambah, ubah, hapus) dilakukan secara asinkron (AJAX) terhadap tabel `global_room_types` melalui `GlobalRoomTypeController` yang sudah ada. Dropdown digunakan di halaman **Create Fasilitas** dan **Edit Fasilitas**, diterapkan sebagai komponen Alpine.js (`roomTypeDropdown`) yang di-include via Blade partial `_room_type_dropdown.blade.php`.

---

## Glossary

- **Dropdown**: Elemen UI yang menampilkan daftar pilihan tipe kamar saat diklik, dan dapat tetap terbuka saat berinteraksi (mode permanen).
- **Hybrid**: Dropdown yang menggabungkan fungsi pemilihan data eksisting dengan kemampuan tambah/ubah/hapus data baru langsung di dalamnya.
- **RoomType**: Entitas tipe kamar yang disimpan di tabel `global_room_types` dengan kolom `id` dan `name`.
- **GlobalRoomTypeController**: Controller Laravel di `app/Http/Controllers/GlobalRoomTypeController.php` yang menangani operasi CRUD untuk `global_room_types`.
- **Alpine_Component**: Komponen JavaScript reaktif berbasis Alpine.js (`roomTypeDropdown`) yang mengelola state dan interaksi UI dropdown.
- **Blade_Partial**: File `resources/views/admin/dashboard/partials/_room_type_dropdown.blade.php` yang berisi markup HTML dropdown dan di-include ke halaman Create/Edit Fasilitas.
- **CSRF_Token**: Token keamanan Laravel yang wajib disertakan pada setiap request POST/PUT/DELETE.
- **Fasilitas**: Entitas ruangan/fasilitas yang memiliki properti paket kamar (`paket_harian`) dengan referensi ke tipe kamar.
- **Admin**: Pengguna dengan akses admin yang dapat mengelola data fasilitas dan tipe kamar.

---

## Requirements

### Requirement 1: Memuat Daftar Tipe Kamar dari Database

**User Story:** Sebagai Admin, saya ingin dropdown tipe kamar menampilkan semua tipe yang sudah ada di database saat halaman dimuat, sehingga saya dapat memilih tipe yang relevan tanpa perlu input manual berulang.

#### Acceptance Criteria

1. WHEN halaman Create Fasilitas atau Edit Fasilitas dimuat, THE Alpine_Component SHALL melakukan request GET ke `/admin/room-types` dan memuat hasilnya ke state `roomTypes` lokal.
2. WHEN request GET ke `/admin/room-types` berhasil, THE Dropdown SHALL menampilkan daftar `RoomType` yang diurutkan ascending berdasarkan kolom `name`.
3. IF request GET ke `/admin/room-types` gagal (status HTTP bukan 2xx), THEN THE Alpine_Component SHALL mempertahankan daftar `roomTypes` yang sudah ada tanpa mengosongkan daftar.
4. WHEN daftar `global_room_types` kosong, THE Dropdown SHALL menampilkan pesan "Belum ada tipe kamar. Tambahkan di bawah." pada area daftar.
5. THE Alpine_Component SHALL membaca data `roomTypes` awal dari variabel PHP yang diinjeksi Blade (`@json($roomTypes->toArray())`), sehingga daftar tersedia sebelum ada interaksi pengguna.

---

### Requirement 2: Memilih Tipe Kamar

**User Story:** Sebagai Admin, saya ingin mengklik salah satu tipe kamar di dropdown untuk menetapkannya sebagai tipe kamar pada baris paket kamar yang sedang diedit, sehingga proses pengisian form lebih cepat dan konsisten.

#### Acceptance Criteria

1. WHEN Admin mengklik salah satu item tipe kamar di Dropdown, THE Alpine_Component SHALL menetapkan nilai `rooms[roomIndex].tipe` menjadi `name` dari `RoomType` yang dipilih.
2. WHEN sebuah `RoomType` dipilih, THE Dropdown SHALL menampilkan nama tipe yang dipilih pada tombol trigger dan menutup panel daftar.
3. WHEN `rooms[roomIndex].tipe` sudah terisi dengan suatu nilai, THE Dropdown SHALL menyorot item yang bersesuaian dengan kelas `text-[#1265A8] bg-blue-50` di dalam daftar.
4. THE Blade_Partial SHALL menyertakan sebuah `<input type="hidden">` yang nilainya terikat (`x-bind:value`) dengan `rooms[roomIndex].tipe`, sehingga nilai terpilih ikut terkirim saat form di-submit.
5. WHEN Admin mengklik di luar area Dropdown, THE Alpine_Component SHALL menutup panel daftar (`open = false`).

---

### Requirement 3: Menambahkan Tipe Kamar Baru

**User Story:** Sebagai Admin, saya ingin menambahkan tipe kamar baru langsung dari dalam dropdown tanpa berpindah halaman, sehingga saya tidak perlu membuka halaman manajemen terpisah hanya untuk menambah satu tipe.

#### Acceptance Criteria

1. WHEN Admin mengklik tombol "+ Tambah Tipe" di dalam Dropdown, THE Alpine_Component SHALL menampilkan form input inline (`addMode = true`) dengan sebuah `<input>` teks dan tombol "Tambah" serta tombol "✕".
2. WHEN Admin mengisi nama dan menekan tombol "Tambah" atau menekan tombol Enter, THE Alpine_Component SHALL mengirim request POST ke `/admin/room-types` dengan payload `{ name: newTypeName }` dan header `X-CSRF-TOKEN`.
3. WHEN request POST berhasil (HTTP 201), THE Alpine_Component SHALL menambahkan `RoomType` baru ke state `roomTypes`, menetapkan tipe pada `rooms[roomIndex].tipe` dengan nama baru, dan menutup form `addMode`.
4. IF request POST gagal karena nama sudah ada (HTTP 422 dengan pesan validasi `unique`), THEN THE Alpine_Component SHALL menampilkan pesan error "Tipe kamar ini sudah ada." tanpa menutup form.
5. IF request POST gagal karena alasan lain (HTTP 4xx/5xx selain 422 unique), THEN THE Alpine_Component SHALL menampilkan pesan error umum dari respons server tanpa menutup form.
6. WHEN field input nama baru kosong, THE tombol "Tambah" SHALL memiliki atribut `disabled` sehingga tidak dapat diklik.
7. WHEN `addMode` aktif dan Admin menekan tombol Escape pada keyboard, THE Alpine_Component SHALL menutup form (`addMode = false`) tanpa menyimpan data.

---

### Requirement 4: Mengubah Nama Tipe Kamar

**User Story:** Sebagai Admin, saya ingin mengubah nama tipe kamar yang sudah ada langsung dari dalam dropdown, sehingga saya dapat memperbaiki kesalahan penulisan tanpa perlu keluar dari halaman form fasilitas.

#### Acceptance Criteria

1. WHEN Admin mengarahkan kursor (hover) ke salah satu item tipe kamar, THE Dropdown SHALL menampilkan ikon tombol edit (pensil) di sisi kanan item tersebut.
2. WHEN Admin mengklik ikon edit pada suatu item, THE Alpine_Component SHALL mengganti tampilan item tersebut menjadi mode edit inline (`editingId = t.id`) dengan sebuah `<input>` teks yang sudah terisi nama saat ini (`editingName`), tombol "✓", dan tombol "✕".
3. WHEN Admin mengubah nama dan mengklik tombol "✓" atau menekan tombol Enter, THE Alpine_Component SHALL mengirim request PUT ke `/admin/room-types/{id}` dengan payload `{ name: editingName }` dan header `X-CSRF-TOKEN`.
4. WHEN request PUT berhasil (HTTP 200), THE Alpine_Component SHALL memperbarui nama `RoomType` di state `roomTypes` dan menutup mode edit (`editingId = null`).
5. IF nama yang dimasukkan sudah digunakan oleh `RoomType` lain (HTTP 422), THEN THE Alpine_Component SHALL menampilkan pesan error "Nama sudah digunakan." tanpa menutup mode edit.
6. WHEN Admin mengklik tombol "✕" saat mode edit aktif, THE Alpine_Component SHALL membatalkan perubahan dan mengembalikan tampilan item ke tampilan normal tanpa mengirim request ke server.
7. WHEN `editingId` aktif dan Admin menekan tombol Escape pada keyboard, THE Alpine_Component SHALL menutup mode edit (`editingId = null`) tanpa menyimpan perubahan.

---

### Requirement 5: Menghapus Tipe Kamar

**User Story:** Sebagai Admin, saya ingin menghapus tipe kamar yang tidak relevan lagi langsung dari dalam dropdown, sehingga daftar tipe kamar tetap ringkas dan tidak menumpuk data usang.

#### Acceptance Criteria

1. WHEN Admin mengarahkan kursor (hover) ke salah satu item tipe kamar, THE Dropdown SHALL menampilkan ikon tombol hapus (tempat sampah) di sisi kanan item tersebut, berdampingan dengan ikon edit.
2. WHEN Admin mengklik ikon hapus pada suatu item, THE Alpine_Component SHALL menampilkan dialog konfirmasi (SweetAlert2) dengan teks konfirmasi yang menyebutkan nama `RoomType` yang akan dihapus.
3. WHEN Admin mengonfirmasi penghapusan pada dialog, THE Alpine_Component SHALL mengirim request DELETE ke `/admin/room-types/{id}` dengan header `X-CSRF-TOKEN`.
4. WHEN request DELETE berhasil (HTTP 200), THE Alpine_Component SHALL menghapus `RoomType` yang bersangkutan dari state `roomTypes`.
5. IF `rooms[roomIndex].tipe` saat ini sama dengan nama `RoomType` yang dihapus, THEN THE Alpine_Component SHALL mengosongkan `rooms[roomIndex].tipe` menjadi string kosong (`''`).
6. WHEN Admin mengklik tombol batal pada dialog konfirmasi, THE Alpine_Component SHALL menutup dialog tanpa mengirim request ke server.
7. WHILE request DELETE sedang diproses, THE tombol hapus pada item terkait SHALL memiliki atribut `disabled` untuk mencegah klik ganda.

---

### Requirement 6: Integrasi dengan Form Create dan Edit Fasilitas

**User Story:** Sebagai Admin, saya ingin komponen Hybrid Dropdown berfungsi konsisten di halaman Create Fasilitas dan Edit Fasilitas, sehingga pengalaman pengelolaan tipe kamar seragam di seluruh alur kerja.

#### Acceptance Criteria

1. THE Blade_Partial `_room_type_dropdown.blade.php` SHALL menerima dua parameter: `$roomIndex` (indeks kamar yang sedang diedit dalam loop Alpine) dan `$roomsVar` (nama variabel array rooms di Alpine).
2. WHEN halaman Create Fasilitas dimuat, THE FasilitasController SHALL mengirimkan variabel `$roomTypes` (hasil `GlobalRoomType::orderBy('name')->get(['id','name'])`) ke view `createFasilitas.blade.php`.
3. WHEN halaman Edit Fasilitas dimuat, THE FasilitasController SHALL mengirimkan variabel `$roomTypes` (hasil `GlobalRoomType::orderBy('name')->get(['id','name'])`) ke view `editFasilitas.blade.php`.
4. THE Alpine_Component `roomTypeDropdown` SHALL mengakses state `roomTypes` dan `rooms` dari komponen Alpine induk via `window.__alpineRoot` agar perubahan pada satu instance dropdown (tambah/ubah/hapus) terefleksi di semua instance dropdown lain pada halaman yang sama.
5. WHEN form fasilitas di-submit, THE nilai `rooms[n].tipe` untuk setiap kamar SHALL tersertakan dalam payload form sebagai hidden input dengan nama yang sesuai (misalnya `paket_harian[n][tipe]`).
6. THE Blade_Partial SHALL bekerja di dalam konteks loop Alpine (`x-for`) tanpa konflik state antar instance untuk kamar yang berbeda.

---

### Requirement 7: Validasi dan Keamanan

**User Story:** Sebagai sistem, saya ingin setiap operasi CRUD pada tipe kamar divalidasi dan diproteksi, sehingga data `global_room_types` tetap konsisten dan tidak dapat dimanipulasi oleh pihak yang tidak berwenang.

#### Acceptance Criteria

1. WHEN request POST atau PUT diterima oleh GlobalRoomTypeController, THE GlobalRoomTypeController SHALL memvalidasi bahwa field `name` wajib diisi (`required`), bertipe string, maksimal 100 karakter, dan unik di tabel `global_room_types` (kecuali record itu sendiri pada request PUT).
2. IF validasi gagal, THEN THE GlobalRoomTypeController SHALL mengembalikan respons HTTP 422 dengan detail error dalam format JSON.
3. THE GlobalRoomTypeController SHALL hanya dapat diakses oleh pengguna yang sudah terautentikasi sebagai admin (middleware `admin.access:can_edit` sesuai pola routes eksisting).
4. WHEN Alpine_Component mengirim request AJAX, THE request SHALL menyertakan header `X-CSRF-TOKEN` yang diambil dari meta tag `<meta name="csrf-token">` di halaman.
5. THE GlobalRoomTypeController SHALL melakukan trim whitespace pada field `name` sebelum menyimpan ke database, sehingga " Standar " dan "Standar" dianggap sama.
6. IF request DELETE dikirim untuk `id` yang tidak ada, THEN THE GlobalRoomTypeController SHALL mengembalikan HTTP 404.

---

### Requirement 8: Umpan Balik Visual dan Aksesibilitas

**User Story:** Sebagai Admin, saya ingin mendapatkan umpan balik visual yang jelas saat berinteraksi dengan dropdown, sehingga saya tahu apakah operasi CRUD berhasil, sedang diproses, atau gagal.

#### Acceptance Criteria

1. WHILE request AJAX (POST/PUT/DELETE) sedang diproses, THE Alpine_Component SHALL menonaktifkan (`disabled`) tombol yang memicu request tersebut dan menampilkan indikator loading (teks "…") pada tombol tersebut.
2. WHEN operasi tambah tipe kamar baru berhasil, THE Dropdown SHALL secara otomatis memilih tipe kamar baru tersebut sehingga Admin tidak perlu memilihnya secara manual.
3. IF terjadi error pada operasi AJAX, THE Alpine_Component SHALL menampilkan pesan error yang spesifik dan mudah dibaca di dalam area Dropdown (bukan hanya di console browser).
4. THE tombol trigger Dropdown SHALL selalu menampilkan nama tipe kamar yang sedang terpilih, atau teks placeholder "Pilih Tipe Kamar" jika belum ada yang dipilih.
5. THE Dropdown SHALL menerapkan animasi transisi (fade + slide) saat panel daftar terbuka dan tertutup menggunakan direktif `x-transition` Alpine.js.
6. WHEN Alpine_Component menampilkan dialog konfirmasi hapus, THE dialog SHALL menggunakan SweetAlert2 dengan judul konfirmasi yang menyebutkan nama tipe kamar secara eksplisit.
