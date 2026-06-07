<div
    x-data="roomTypeDropdown"
    data-rooms-var="{{ $roomsVar }}"
    x-bind:data-room-index="{{ $roomIndex }}"
    class="relative w-full"
    @click.outside="close()"
    @keydown.window.escape="handleEscape()"
>
    <button type="button"
        @click="toggle()"
        :class="!currentTipe() ? 'border-red-400 bg-red-50' : 'border-slate-200'"
        class="w-full flex items-center justify-between px-4 py-3 bg-white border rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-[#1265A8] outline-none transition-all font-semibold text-sm cursor-pointer shadow-sm">
        <span :class="currentTipe() ? 'text-slate-800' : 'text-slate-400'" x-text="currentTipe() || 'Pilih Tipe Kamar'"></span>
        <svg class="w-4 h-4 text-slate-400 transition-transform shrink-0 ml-2" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-1"
         class="absolute z-50 left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">

        <div class="max-h-48 overflow-y-auto">
            <template x-for="(t, index) in allTypes()" :key="t.id">
                <div class="flex items-center group hover:bg-blue-50 transition-colors">

                    {{-- Edit form: shown inline when this item is being edited (Requirements: 4.2, 4.6) --}}
                    <div x-show="editingId === t.id"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="flex items-center gap-1 flex-1 px-3 py-2">
                        <input type="text"
                            x-ref="editInput"
                            x-model="editingName"
                            @keydown.enter.prevent="saveEdit(t.id)"
                            @keydown.escape.stop="handleEscape()"
                            class="flex-1 px-2 py-1 text-xs font-semibold border border-[#1265A8] rounded-lg outline-none focus:ring-2 focus:ring-blue-100 text-slate-800">
                        <button type="button" @click="saveEdit(t.id)" :disabled="saving"
                            class="px-2 py-1 bg-[#1265A8] text-white rounded-lg text-[10px] font-black hover:bg-blue-700 transition-colors disabled:opacity-50">
                            <span x-show="!saving">✓</span>
                            <span x-show="saving">…</span>
                        </button>
                        <button type="button" @click.stop="editingId = null; errorMessage = ''"
                            class="px-2 py-1 bg-slate-100 text-slate-500 rounded-lg text-[10px] font-black hover:bg-slate-200 transition-colors">✕</button>
                    </div>

                    {{-- Normal item row: shown when this item is NOT being edited --}}
                    <div x-show="editingId !== t.id"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="flex items-center flex-1 min-w-0">
                        <button type="button" @click="selectType(t.name)"
                            class="flex-1 text-left px-4 py-2.5 text-sm font-semibold text-slate-700 truncate"
                            :class="currentTipe() === t.name ? 'text-[#1265A8] bg-blue-50' : ''"
                            x-text="t.name">
                        </button>
                        <div class="flex items-center gap-0.5 pr-2 opacity-0 group-hover:opacity-100 transition-opacity shrink-0">
                            <button type="button" @click.stop="startEdit(t.id, t.name)" title="Ubah nama"
                                class="p-1.5 rounded-lg text-slate-400 hover:text-[#1265A8] hover:bg-blue-100 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </button>
                            <button type="button" @click.stop="deleteType(index)" :disabled="deleting === index" title="Hapus"
                                class="p-1.5 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors disabled:opacity-40">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                </div>
            </template>

            <div x-show="allTypes().length === 0 && !addMode"
                 class="px-4 py-3 text-xs text-slate-400 font-semibold text-center">
                Belum ada tipe kamar. Tambahkan di bawah.
            </div>
        </div>

        <div class="border-t border-slate-100"></div>

        <div x-show="!addMode"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="px-3 py-2">
            <button type="button" @click="startAdd()"
                class="w-full flex items-center gap-2 px-3 py-2 text-xs font-black text-[#1265A8] hover:bg-blue-50 rounded-lg transition-colors uppercase tracking-widest">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Tipe
            </button>
        </div>

        {{-- Add form: shown inline when addMode is active (Requirements: 3.1, 3.6) --}}
        <div x-show="addMode"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1"
             class="px-3 py-2.5 flex items-center gap-2">
            <input x-ref="newInput" type="text" x-model="newTypeName"
                @keydown.enter.prevent="saveNew()"
                @keydown.escape.stop="handleEscape()"
                placeholder="Nama tipe baru..."
                class="flex-1 px-3 py-2 text-xs font-semibold bg-slate-50 border border-slate-200 rounded-lg outline-none focus:border-[#1265A8] focus:ring-2 focus:ring-blue-100 transition-all text-slate-800">
            <button type="button" @click="saveNew()" :disabled="saving || !newTypeName.trim()"
                class="px-3 py-2 bg-[#1265A8] text-white rounded-lg text-xs font-black hover:bg-blue-700 transition-colors disabled:opacity-50 whitespace-nowrap">
                <span x-show="!saving">Tambah</span>
                <span x-show="saving">…</span>
            </button>
            <button type="button" @click="addMode = false; errorMessage = ''"
                class="px-2 py-2 bg-slate-100 text-slate-500 rounded-lg text-xs font-black hover:bg-slate-200 transition-colors">✕</button>
        </div>

        {{-- Inline error message area (Requirements: 3.4, 3.5, 4.5, 8.3) --}}
        <div x-show="errorMessage"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="px-4 py-2 text-xs font-semibold text-red-600 bg-red-50 border-t border-red-100"
             x-text="errorMessage">
        </div>

    </div>

    {{-- Hidden input for form submission (Requirements: 2.4, 12.1) --}}
    <input type="hidden"
           x-bind:name="'rooms[' + rIdx + '][tipe]'"
           x-bind:value="currentTipe()">
</div>
