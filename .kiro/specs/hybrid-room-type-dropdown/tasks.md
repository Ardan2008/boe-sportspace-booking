# Implementation Plan: Hybrid Room Type Dropdown

## Overview

This implementation plan converts the hybrid room type dropdown feature design into actionable coding tasks. The feature enables admins to manage room types (create, read, update, delete) directly from within a dropdown on Create/Edit Fasilitas pages, without page navigation. The backend provides JSON API endpoints via Laravel, while the frontend renders an interactive Alpine.js component with reactive state management and real-time synchronization across multiple dropdown instances.

---

## Tasks

- [x] 1. Set up backend API controller and routing infrastructure
  - [x] 1.1 Create `GlobalRoomTypeController` with CRUD methods
    - Implement `index()`, `store()`, `update()`, `destroy()` methods
    - Return JSON responses with appropriate HTTP status codes
    - _Requirements: 1.1, 7.1, 7.6_
  
  - [x] 1.2 Register routes at `/admin/room-types` with `admin.access:can_edit` middleware
    - Create REST routes: GET, POST, PUT, DELETE
    - Ensure CSRF protection is applied
    - _Requirements: 7.3, 7.4_
  
  - [x] 1.3 Add validation logic to controller methods
    - Validate `name` field: required, string, max 100 chars, unique constraint
    - Apply trim whitespace before saving
    - Return HTTP 422 with error details on validation failure
    - _Requirements: 7.1, 7.2, 7.5_

- [x] 2. Implement database model and integrate with `FasilitasController`
  - [x] 2.1 Verify `GlobalRoomType` model and timestamps
    - Ensure model has `$fillable = ['name']`
    - Confirm timestamps are active (created_at, updated_at)
    - _Requirements: 1.2_
  
  - [x] 2.2 Update `FasilitasController::edit()` and `store()` methods
    - Pass `$roomTypes` to view via `GlobalRoomType::orderBy('name')->get(['id','name'])`
    - Ensure data is available for both Create and Edit views
    - _Requirements: 6.2, 6.3_

- [x] 3. Checkpoint - Ensure all routes respond with correct JSON structure
  - Ensure all API endpoints respond with correct HTTP status codes and JSON format, ask the user if questions arise.

- [ ] 4. Create Alpine.js component and register globally
  - [x] 4.1 Create `roomTypeDropdown` Alpine component with full state management
    - Define state properties: `open`, `addMode`, `editingId`, `editingName`, `newTypeName`, `saving`, `deleting`
    - Implement methods: `allTypes()`, `currentTipe()`, `setTipe()`, `toggle()`, `close()`
    - _Requirements: 2.1, 2.2, 2.3_
  
  - [x] 4.2 Implement add/edit/delete methods in Alpine component
    - Implement `startAdd()`, `saveNew()` for POST operation
    - Implement `startEdit()`, `saveEdit()` for PUT operation
    - Implement `deleteType()` for DELETE with SweetAlert2 confirmation
    - Include error handling with try/catch/finally blocks
    - _Requirements: 3.1, 3.2, 3.3, 4.1, 4.2, 4.3, 5.1, 5.2, 5.3_
  
  - [x] 4.3 Implement data bridge via `window.__alpineRoot`
    - Read `roomsVar` and `roomIndex` from element dataset attributes
    - Expose methods to access/mutate parent component's rooms array
    - Ensure all instances share the same `roomTypes` array reference
    - _Requirements: 6.4, 6.6, 11.2, 12.1_
  
  - [x] 4.4 Add keyboard event handlers and focus management
    - Handle Escape key to close panel/exit add/edit modes
    - Handle Enter key in input fields
    - Implement auto-focus when entering add/edit modes
    - _Requirements: 3.5, 3.7, 4.7_

- [ ] 5. Create Blade partial for dropdown UI markup
  - [-] 5.1 Create `resources/views/admin/dashboard/partials/_room_type_dropdown.blade.php`
    - Accept parameters: `$roomIndex` and `$roomsVar`
    - Render root element with `x-data="roomTypeDropdown"`
    - Store parameters in `data-room-index` and `data-rooms-var` attributes
    - _Requirements: 6.1, 2.1, 4.1_
  
  - [-] 5.2 Implement dropdown trigger button with conditional text
    - Display selected tipe or placeholder "Pilih Tipe Kamar"
    - Bind to `currentTipe()` and apply click handler to `toggle()`
    - Apply Alpine `x-transition` directives for animation
    - _Requirements: 2.2, 8.4, 8.5_
  
  - [x] 5.3 Render list of room types with hover actions
    - Loop through `allTypes()` with `x-for` directive
    - Apply highlight class `text-[#1265A8] bg-blue-50` for selected item
    - Render edit (pencil) and delete (trash) icons on hover with `opacity-0 group-hover:opacity-100`
    - Bind click handlers: `selectType()`, `startEdit()`, `deleteType()`
    - _Requirements: 2.3, 4.1, 5.1_
  
  - [x] 5.4 Render add form and edit form inline
    - Add form: input field, "Tambah" button (disabled for whitespace), "✕" button
    - Edit form: input field pre-filled, "✓" button, "✕" button
    - Apply `x-show` and `x-transition` directives for visibility
    - _Requirements: 3.1, 3.6, 4.2, 4.6_
  
  - [x] 5.5 Create hidden input for form submission
    - Render `<input type="hidden">` with `x-bind:name` and `x-bind:value`
    - Bind name to `'rooms[' + rIdx + '][tipe]'`
    - Bind value to `currentTipe()`
    - _Requirements: 2.4, 12.1_
  
  - [x] 5.6 Add empty state message and error display area
    - Show "Belum ada tipe kamar. Tambahkan di bawah." when list is empty
    - Add alert area for displaying API error messages
    - _Requirements: 1.4, 8.3_

- [x] 6. Implement loading states and error handling
  - [x] 6.1 Add disabled states to buttons during async operations
    - Disable "Tambah" button when `saving === true`
    - Display "…" text in place of button label
    - Disable delete button when `deleting === id`
    - _Requirements: 8.1_
  
  - [x] 6.2 Implement error message display logic
    - Parse HTTP 422 response for `errors.name` field
    - Display "Tipe kamar ini sudah ada." for duplicate names (POST)
    - Display "Nama sudah digunakan." for duplicate names (PUT)
    - Display generic error message for other failures
    - _Requirements: 3.4, 3.5, 4.5, 8.3_
  
  - [x] 6.3 Handle network errors and timeout scenarios
    - Catch network failures in fetch operations
    - Display "Terjadi kesalahan jaringan." message
    - Ensure `saving` and `deleting` states reset in `finally` block
    - _Requirements: 8.1_

- [x] 7. Integrate component into Create and Edit Fasilitas views
  - [x] 7.1 Include dropdown partial in the room loop
    - Add `@include('admin.dashboard.partials._room_type_dropdown', ['roomIndex' => $loop->index, 'roomsVar' => 'rooms'])` inside the rooms `x-for` loop
    - Ensure partial is included only within the loop context
    - _Requirements: 6.1, 6.6_
  
  - [x] 7.2 Initialize parent Alpine component to expose `window.__alpineRoot`
    - Set `window.__alpineRoot = this` in parent component's `init()` hook
    - Expose `roomTypes` array to window scope
    - Ensure rooms array is accessible via `window.__alpineRoot[roomsVar][roomIndex]`
    - _Requirements: 6.4, 11.2_
  
  - [x] 7.3 Initialize `roomTypes` from PHP data
    - Inject `@json($roomTypes->toArray())` into `window.__alpineRoot.roomTypes`
    - Ensure data is available before any Alpine interactions
    - _Requirements: 1.1, 1.5_

- [ ] 8. Checkpoint - Ensure all components render and interact correctly
  - Test dropdown opens/closes, basic interaction works, inspect browser console for errors.

- [~] 9. Implement property-based tests for backend
  - [~] 9.1* Write property test for room types sorting
    - **Property 1: Daftar tipe kamar selalu urut ascending berdasarkan nama**
    - Generate random room type names, insert to DB, verify GET response is sorted ascending
    - _Requirements: 1.2_
  
  - [~] 9.2* Write property test for name validation and trimming
    - **Property 10: Nama tipe selalu disimpan dalam bentuk trimmed**
    - Generate names with leading/trailing whitespace, verify stored values are trimmed
    - _Requirements: 7.5_
  
  - [~] 9.3* Write property test for validation errors on invalid input
    - **Property 9: Validasi server menolak nama yang tidak valid**
    - Generate invalid names (empty, >100 chars, duplicates), verify HTTP 422 response
    - _Requirements: 7.1, 7.2_

- [~] 10. Implement property-based tests for Alpine component
  - [~] 10.1* Write property test for `selectType` updating correct room
    - **Property 2: Pemilihan tipe kamar memperbarui state rooms yang tepat**
    - Generate room array, random index, name; verify only `rooms[i].tipe` changes
    - _Requirements: 2.1_
  
  - [~] 10.2* Write property test for highlight reflecting selected type
    - **Property 3: Highlight item mencerminkan tipe yang sedang terpilih**
    - Generate type list, select one, verify only matching item has highlight classes
    - _Requirements: 2.3_
  
  - [~] 10.3* Write property test for disabled button on whitespace input
    - **Property 4: Tombol Tambah dinonaktifkan untuk input yang hanya berisi whitespace**
    - Generate whitespace-only strings, verify `newTypeName.trim()` is falsy
    - _Requirements: 3.6_
  
  - [~] 10.4* Write property test for auto-select after new type creation
    - **Property 5: Penambahan tipe baru menambah roomTypes dan auto-select**
    - Mock POST→201, call `saveNew()`, verify type added to array and auto-selected
    - _Requirements: 3.2, 3.3, 8.2_
  
  - [~] 10.5* Write property test for PUT request payload structure
    - **Property 6: Mode edit mengirim PUT dengan payload yang benar**
    - Generate id and name, spy on fetch, verify PUT to correct URL with trimmed name
    - _Requirements: 4.3, 7.4_
  
  - [~] 10.6* Write property test for name update propagation
    - **Property 7: Update nama tipe kamar memperbarui seluruh referensi di roomTypes**
    - Mock PUT→200, verify name updated in array and `currentTipe()` reflects change
    - _Requirements: 4.4_
  
  - [~] 10.7* Write property test for delete operation clearing active type
    - **Property 8: Penghapusan tipe kamar menghapus entri dari roomTypes dan mengosongkan tipe jika aktif**
    - Mock DELETE→200, verify type removed from array and `rooms[i].tipe` cleared
    - _Requirements: 5.3, 5.4, 5.5_
  
  - [~] 10.8* Write property test for shared state across instances
    - **Property 11: Semua instance dropdown berbagi roomTypes yang sama via window.__alpineRoot**
    - Init two instances, mutate `window.__alpineRoot.roomTypes`, verify both see changes
    - _Requirements: 6.4, 6.6_
  
  - [~] 10.9* Write property test for hidden input synchronization
    - **Property 12: Hidden input selalu mencerminkan tipe yang dipilih per kamar**
    - Generate tipe values, verify hidden input `name` and `value` match reactively
    - _Requirements: 2.4, 6.5_
  
  - [~] 10.10* Write property test for trigger button display accuracy
    - **Property 13: Trigger button selalu menampilkan state terpilih yang akurat**
    - Generate tipe values including empty string, verify button shows tipe or placeholder
    - _Requirements: 8.4_

- [~] 11. Implement unit tests for example-based scenarios
  - [~] 11.1* Write unit test for dropdown toggle behavior
    - Verify `toggle()` opens and closes panel, resets sub-modes on close
    - _Requirements: 2.5_
  
  - [~] 11.2* Write unit test for Escape key handling
    - Mock keyboard event, verify `addMode = false` and `editingId = null` as appropriate
    - _Requirements: 3.7, 4.7_
  
  - [~] 11.3* Write unit test for form cancellation
    - Click "✕" button in add/edit mode, verify no request sent and modes reset
    - _Requirements: 3.7, 4.6_
  
  - [~] 11.4* Write unit test for SweetAlert2 dialog cancellation
    - Mock delete action, cancel on SweetAlert2, verify no DELETE request sent
    - _Requirements: 5.6_
  
  - [~] 11.5* Write unit test for duplicate name error messages
    - Mock POST/PUT with 422 unique error, verify specific error message displayed
    - _Requirements: 3.4, 4.5_
  
  - [~] 11.6* Write unit test for API error responses
    - Test 404, 500 responses; verify error messages displayed to user
    - _Requirements: 8.3_

- [~] 12. Checkpoint - Ensure all unit and property tests pass
  - Run test suite, verify all tests pass, address any failures, ask the user if questions arise.

- [~] 13. Implement integration tests for Create/Edit Fasilitas workflows
  - [~] 13.1* Write integration test for loading roomTypes on page init
    - Load Create/Edit page, verify dropdown renders with populated types from database
    - _Requirements: 1.1, 1.2, 1.5_
  
  - [~] 13.2* Write integration test for multi-instance dropdown synchronization
    - Create form with multiple rooms, add/edit/delete type, verify all instances update
    - _Requirements: 6.4, 6.6, 11.2_
  
  - [~] 13.3* Write integration test for form submission with room types
    - Fill form with room types selected, submit, verify hidden inputs contain correct values
    - _Requirements: 2.4, 6.5, 12.1_
  
  - [~] 13.4* Write integration test for error handling in multi-instance scenario
    - Trigger duplicate name error in one instance, verify only that instance shows error
    - _Requirements: 3.4_

- [~] 14. Final checkpoint - Ensure all tests pass and feature works end-to-end
  - Run complete test suite including integration tests, verify feature works in browser, ask the user if questions arise.

---

## Notes

- Tasks marked with `*` are optional test-related tasks and can be skipped for MVP deployment
- Each task references specific requirements for traceability
- Backend tests use PHPUnit/PestPHP with fast-check or Faker for property-based testing
- Frontend tests use Vitest + fast-check for property-based testing of Alpine component
- All API responses must include proper error handling and CSRF protection
- The `window.__alpineRoot` data bridge is critical for multi-instance synchronization
- Hidden inputs with `x-bind:name` and `x-bind:value` ensure form submission includes room types

---

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1.1", "1.2", "1.3", "2.1"] },
    { "id": 1, "tasks": ["2.2", "4.1", "4.3"] },
    { "id": 2, "tasks": ["4.2", "4.4", "5.1", "5.2"] },
    { "id": 3, "tasks": ["5.3", "5.4", "5.5", "5.6", "6.1"] },
    { "id": 4, "tasks": ["6.2", "6.3", "7.1", "7.2"] },
    { "id": 5, "tasks": ["7.3", "9.1", "9.2", "9.3", "10.1", "10.2", "10.3"] },
    { "id": 6, "tasks": ["10.4", "10.5", "10.6", "10.7", "10.8", "10.9", "10.10"] },
    { "id": 7, "tasks": ["11.1", "11.2", "11.3", "11.4", "11.5", "11.6", "13.1"] },
    { "id": 8, "tasks": ["13.2", "13.3", "13.4"] }
  ]
}
```
