/**
 * roomTypeDropdown — Alpine.js component
 *
 * Hybrid permanent dropdown for room type management.
 * Allows admins to select, add, edit, and delete room types directly
 * from within the dropdown on Create/Edit Fasilitas pages.
 *
 * Data bridge: reads/writes via window.__alpineRoot so all instances
 * on the same page share the same roomTypes array reference.
 *
 * Usage (Blade):
 *   <div x-data="roomTypeDropdown"
 *        data-rooms-var="rooms"
 *        data-room-index="0">
 *   </div>
 */

document.addEventListener('alpine:init', () => {
    Alpine.data('roomTypeDropdown', function () {
        // Capture element ref and CSRF token at component construction time.
        // 'this' here is the Alpine magic object (has $el, $refs, etc.)
        const el   = this.$el;
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        return {
            // ── State ─────────────────────────────────────────────────────────
            open:         false,   // Is the dropdown panel open?
            addMode:      false,   // Is the "add new type" form visible?
            editingId:    null,    // ID of the type currently being edited (null = none)
            editingName:  '',      // Input value while editing a type
            newTypeName:  '',      // Input value while adding a new type
            saving:       false,   // True while a POST or PUT request is in flight
            deleting:     null,    // ID of the type currently being deleted (null = none)
            errorMessage: '',      // Inline error message to display in the dropdown

            // ── Data bridge ───────────────────────────────────────────────────
            // rVar  — name of the rooms array on the parent Alpine component
            //         e.g. 'rooms'  →  window.__alpineRoot.rooms[rIdx].tipe
            // rIdx  — index of this dropdown's room in that array
            //
            // Both are stored as dataset attributes on the root element so that
            // multiple dropdown instances (one per room row) stay independent.
            get rVar()  { return el.dataset.roomsVar; },
            get rIdx()  { return parseInt(el.dataset.roomIndex); },

            // ── Derived getters ───────────────────────────────────────────────

            /**
             * Returns 'rooms[rIdx][tipe]' — used as the hidden input name so
             * the selected type is included in the native form submission.
             */
            get hiddenName() {
                return `${this.rVar}[${this.rIdx}][tipe]`;
            },

            // ── Core access methods ───────────────────────────────────────────

            /**
             * allTypes() — Returns the shared room types array from the parent
             * Alpine component via window.__alpineRoot.
             *
             * All dropdown instances on the same page reference this same array,
             * so any mutation (push / splice / name update) is immediately
             * visible in every instance without a page refresh.
             *
             * Requirements: 6.4, 6.6, 11.2
             */
            allTypes() {
                return window.__alpineRoot?.roomTypes ?? [];
            },

            /**
             * currentTipe() — Returns the currently selected room type name for
             * this specific room instance (rooms[rIdx].tipe).
             *
             * Reads from window.__alpineRoot[rVar][rIdx].tipe so every
             * individual dropdown slot reflects the correct per-room value.
             *
             * Requirements: 2.1, 2.2, 12.1
             */
            currentTipe() {
                const arr = window.__alpineRoot?.[this.rVar];
                return arr?.[this.rIdx]?.tipe ?? '';
            },

            /**
             * setTipe(val) — Writes a new room type value to the parent
             * component's rooms array at the correct index.
             *
             * Because window.__alpineRoot IS the parent Alpine component object,
             * this mutation is reactive and automatically triggers any Alpine
             * watchers set up in the parent.
             *
             * Requirements: 2.1, 6.4, 12.1
             */
            setTipe(val) {
                const arr = window.__alpineRoot?.[this.rVar];
                if (arr?.[this.rIdx] !== undefined) {
                    arr[this.rIdx].tipe = val;
                }
            },

            // ── Panel control methods ─────────────────────────────────────────

            /**
             * toggle() — Opens or closes the dropdown panel.
             * Resets sub-modes (addMode, editingId) when closing.
             *
             * Requirements: 2.2, 2.5
             */
            toggle() {
                this.open = !this.open;
                if (!this.open) {
                    this.addMode      = false;
                    this.editingId    = null;
                    this.errorMessage = '';
                }
            },

            /**
             * close() — Closes the panel and resets all sub-states.
             *
             * Requirements: 2.2, 2.5
             */
            close() {
                this.open         = false;
                this.addMode      = false;
                this.editingId    = null;
                this.errorMessage = '';
            },

            /**
             * selectType(name) — Sets the room type for this instance and
             * closes the dropdown.
             *
             * Requirements: 2.1, 2.2
             */
            selectType(name) {
                this.setTipe(name);
                this.close();
            },

            // ── Add mode ─────────────────────────────────────────────────────

            /**
             * startAdd() — Activates the inline add form and focuses the input.
             *
             * Requirements: 3.1
             */
            startAdd() {
                this.addMode      = true;
                this.newTypeName  = '';
                this.errorMessage = '';
                this.$nextTick(() => this.$refs.newInput?.focus());
            },

            /**
             * saveNew() — POSTs the new room type name to the server.
             *
             * On success (HTTP 201):
             *  - Pushes the new type into window.__alpineRoot.roomTypes (shared)
             *  - Auto-selects the new type for this room instance
             *  - Closes add mode
             *
             * On failure (HTTP 422 unique):
              *  - Shows "Tipe lapangan ini sudah ada."
             *
             * On other failures:
             *  - Shows server message or generic error
             *
             * Requirements: 3.2, 3.3, 3.4, 3.5, 8.1, 8.2
             */
            async saveNew() {
                const name = this.newTypeName.trim();
                if (!name || this.saving) return;
                this.saving       = true;
                this.errorMessage = '';
                try {
                    const res  = await fetch('/admin/room-types', {
                        method:  'POST',
                        headers: {
                            'Content-Type':  'application/json',
                            'X-CSRF-TOKEN':  csrf,
                            'Accept':        'application/json',
                        },
                        body: JSON.stringify({ name }),
                    });
                    const data = await res.json();
                    if (res.ok) {
                        window.__alpineRoot?.roomTypes.push({ id: data.id, name: data.name });
                        this.selectType(data.name);
                        this.newTypeName = '';
                        this.addMode     = false;
                    } else if (res.status === 422) {
                        // Check errors.name array first (Laravel validation format),
                        // then fall back to top-level message.
                        // Laravel's default unique message is "has already been taken";
                        // also handle custom messages that may contain "unique".
                        const errors   = data.errors?.name ?? [];
                        const allMsgs  = errors.length > 0
                            ? errors
                            : (data.message ? [data.message] : []);
                        const isUnique = allMsgs.some(msg =>
                            typeof msg === 'string' && (
                                msg.toLowerCase().includes('unique') ||
                                msg.toLowerCase().includes('has already been taken')
                            )
                        );
                        this.errorMessage = isUnique
                            ? 'Tipe lapangan ini sudah ada.'
                            : (data.message || 'Validasi gagal.');
                    } else {
                        this.errorMessage = data.message || 'Gagal menyimpan tipe lapangan.';
                    }
                } catch {
                    this.errorMessage = 'Terjadi kesalahan jaringan.';
                } finally {
                    this.saving = false;
                }
            },

            // ── Edit mode ─────────────────────────────────────────────────────

            /**
             * startEdit(id, name) — Activates inline edit mode for a specific
             * type item and auto-focuses the edit input.
             *
             * Requirements: 4.1, 4.2, 4.7
             */
            startEdit(id, name) {
                this.editingId    = id;
                this.editingName  = name;
                this.errorMessage = '';
                // Auto-focus the edit input once Alpine renders it
                this.$nextTick(() => this.$refs.editInput?.focus());
            },

            /**
             * handleEscape() — Handles Escape key press on the dropdown root.
             *
             * Priority:
             *  1. If add mode is active → exit add mode
             *  2. If edit mode is active → exit edit mode
             *  3. If panel is open → close panel
             *
             * Requirements: 3.5, 3.7, 4.7
             */
            handleEscape() {
                if (this.addMode) {
                    this.addMode      = false;
                    this.errorMessage = '';
                } else if (this.editingId !== null) {
                    this.editingId    = null;
                    this.errorMessage = '';
                } else {
                    this.close();
                }
            },

            /**
             * saveEdit(id) — PUTs the updated name to the server.
             *
             * On success (HTTP 200):
             *  - Updates the name in window.__alpineRoot.roomTypes
             *  - If the current room's tipe matches the old name, updates it too
             *  - Closes edit mode
             *
             * On failure (HTTP 422):
             *  - Shows "Nama sudah digunakan."
             *
             * Requirements: 4.3, 4.4, 4.5, 7.4
             */
            async saveEdit(id) {
                const name = this.editingName.trim();
                if (!name || this.saving) return;
                this.saving       = true;
                this.errorMessage = '';
                try {
                    const res  = await fetch(`/admin/room-types/${id}`, {
                        method:  'PUT',
                        headers: {
                            'Content-Type':  'application/json',
                            'X-CSRF-TOKEN':  csrf,
                            'Accept':        'application/json',
                        },
                        body: JSON.stringify({ name }),
                    });
                    const data = await res.json();
                    if (res.ok) {
                        const types = window.__alpineRoot?.roomTypes ?? [];
                        const idx   = types.findIndex(t => t.id === id);
                        if (idx !== -1) {
                            const oldName   = types[idx].name;
                            types[idx].name = data.name;
                            // Keep the per-room tipe in sync if it referenced the old name
                            if (this.currentTipe() === oldName) {
                                this.setTipe(data.name);
                            }
                        }
                        this.editingId = null;
                    } else if (res.status === 422) {
                        // Check errors.name array first (Laravel validation format),
                        // then fall back to top-level message.
                        // Laravel's default unique message is "has already been taken";
                        // also handle custom messages that may contain "unique".
                        const errors   = data.errors?.name ?? [];
                        const allMsgs  = errors.length > 0
                            ? errors
                            : (data.message ? [data.message] : []);
                        const isUnique = allMsgs.some(msg =>
                            typeof msg === 'string' && (
                                msg.toLowerCase().includes('unique') ||
                                msg.toLowerCase().includes('has already been taken')
                            )
                        );
                        this.errorMessage = isUnique
                            ? 'Nama sudah digunakan.'
                            : (data.message || 'Validasi gagal.');
                    } else {
                        this.errorMessage = data.message || 'Gagal mengubah tipe lapangan.';
                    }
                } catch {
                    this.errorMessage = 'Terjadi kesalahan jaringan.';
                } finally {
                    this.saving = false;
                }
            },

            // ── Delete ────────────────────────────────────────────────────────

            /**
             * deleteType(id, name) — Shows a SweetAlert2 confirmation then
             * sends DELETE to the server.
             *
             * On success (HTTP 200):
             *  - Removes the type from window.__alpineRoot.roomTypes
             *  - Clears rooms[rIdx].tipe if it matched the deleted name
             *
             * Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 5.7, 8.1, 8.6
             */
            async deleteType(id, name) {
                const confirmed = await Swal.fire({
                    title:              `Hapus "${name}"?`,
                    text:               `Tipe lapangan "${name}" akan dihapus permanen.`,
                    icon:               'warning',
                    showCancelButton:   true,
                    confirmButtonColor: '#E24B4A',
                    cancelButtonColor:  '#94a3b8',
                    confirmButtonText:  'Ya, Hapus',
                    cancelButtonText:   'Batal',
                    reverseButtons:     true,
                    customClass: { popup: 'rounded-[2.5rem] p-8' },
                });
                if (!confirmed.isConfirmed) return;

                this.deleting     = id;
                this.errorMessage = '';
                try {
                    const res = await fetch(`/admin/room-types/${id}`, {
                        method:  'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept':       'application/json',
                        },
                    });
                    if (res.ok) {
                        const types = window.__alpineRoot?.roomTypes ?? [];
                        const idx   = types.findIndex(t => t.id === id);
                        if (idx !== -1) types.splice(idx, 1);
                        // Clear this room's tipe if it referenced the deleted name
                        if (this.currentTipe() === name) {
                            this.setTipe('');
                        }
                    } else {
                        this.errorMessage = 'Gagal menghapus tipe lapangan.';
                    }
                } catch {
                    this.errorMessage = 'Terjadi kesalahan jaringan.';
                } finally {
                    this.deleting = null;
                }
            },
        };
    });
});
