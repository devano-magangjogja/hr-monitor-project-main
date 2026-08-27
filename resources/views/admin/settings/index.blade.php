@extends('layouts.app')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')
@section('page-subtitle', 'Kelola informasi aplikasi, template WhatsApp, dan link grup')

@section('sidebar')
    @include('components.sidebar-admin')
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        {{-- ── Kartu: Informasi Aplikasi ───────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-5 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Informasi Aplikasi
            </h2>

            <form action="{{ route('admin.settings.app-info') }}" method="POST" enctype="multipart/form-data"
                class="space-y-5" x-data="{
                                              hasLogo: {{ !empty($settings['app_logo']) ? 'true' : 'false' }},
                                              removeLogo: false,
                                              previewUrl: '{{ !empty($settings['app_logo']) ? asset('storage/' . $settings['app_logo']) : '' }}',
                                              handleFile(e) {
                                                  const f = e.target.files[0];
                                                  if (!f) return;
                                                  this.previewUrl = URL.createObjectURL(f);
                                                  this.hasLogo = true;
                                                  this.removeLogo = false;
                                              },
                                              triggerRemove() {
                                                  this.removeLogo = true;
                                                  this.hasLogo = false;
                                                  this.previewUrl = '';
                                                  this.$refs.fileInput.value = '';
                                              }
                                          }">
                @csrf

                {{-- Nama Aplikasi --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Aplikasi</label>
                    <input type="text" name="app_name" value="{{ old('app_name', $settings['app_name'] ?? 'HR-DWMS') }}" required
                        maxlength="60" class="w-full px-3 py-2.5 border rounded-lg text-sm
                                                          focus:outline-none focus:ring-2 focus:ring-primary-500
                                                          {{ $errors->has('app_name') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                    @error('app_name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Logo --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">Logo Aplikasi</label>
                    <div class="flex items-start gap-4">
                        {{-- Preview --}}
                        <div
                            class="w-16 h-16 rounded-xl border border-gray-200 bg-gray-50
                                                            flex items-center justify-center overflow-hidden flex-shrink-0">
                            <template x-if="hasLogo && previewUrl">
                                <img :src="previewUrl" class="w-full h-full object-contain p-1">
                            </template>
                            <template x-if="!hasLogo || !previewUrl">
                                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </template>
                        </div>
                        <div class="flex-1 space-y-2">
                            <div class="flex flex-wrap gap-2">
                                <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5
                                                                      text-xs font-medium text-primary-600 border border-primary-300
                                                                      hover:bg-primary-50 rounded-lg transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    Pilih Logo
                                    <input type="file" name="logo" x-ref="fileInput"
                                        accept="image/png,image/jpg,image/jpeg,image/webp,image/svg+xml"
                                        @change="handleFile($event)" class="hidden">
                                </label>
                                <button type="button" x-show="hasLogo" @click="triggerRemove()"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium
                                                                       text-red-600 border border-red-200 hover:bg-red-50 rounded-lg transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Hapus Logo
                                </button>
                            </div>
                            <p class="text-xs text-gray-400">PNG, JPG, WebP, SVG. Maks 2 MB.</p>
                            <input type="hidden" name="remove_logo" :value="removeLogo ? '1' : '0'">
                        </div>
                    </div>
                    @error('logo')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full py-2.5 bg-primary-600 hover:bg-primary-700 text-white
                                                       text-sm font-medium rounded-lg transition">
                    Simpan Informasi Aplikasi
                </button>
            </form>
        </div>

        {{-- ── Kartu: Template Pesan WhatsApp (Tidak Hadir) ───────────── --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col justify-between" x-data="{
                                         template: '{{ addslashes($settings['wa_template_tidak_hadir'] ?? "Halo {nama}, kami dari tim HR ingin menanyakan konfirmasi kehadiran Anda untuk kegiatan magang ({divisi} - {kampus}) hari ini. Mohon informasikan keterangan atau kendala Anda. Terima kasih.") }}',
                                         get preview() {
                                             return this.template
                                                 .replace(/{nama}/g, 'Budi Santoso')
                                                 .replace(/{divisi}/g, 'Programmer')
                                                 .replace(/{kampus}/g, 'Universitas Gadjah Mada');
                                         },
                                         insertVar(tag) {
                                             const el = this.$refs.textarea;
                                             const start = el.selectionStart;
                                             const end = el.selectionEnd;
                                             this.template = this.template.substring(0, start) + tag + this.template.substring(end);
                                             this.$nextTick(() => {
                                                 el.focus();
                                                 el.setSelectionRange(start + tag.length, start + tag.length);
                                             });
                                         }
                                     }">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-600" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z" />
                            <path
                                d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.118 1.528 5.845L.057 23.428a.5.5 0 00.515.572l5.736-1.507A11.95 11.95 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.95 0-3.784-.523-5.363-1.437l-.384-.228-3.98 1.046 1.065-3.871-.25-.398A9.956 9.956 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z" />
                        </svg>
                        Template WhatsApp
                    </h2>
                    <span
                        class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-50 text-green-700 border border-green-200">
                        Konfirmasi Absensi
                    </span>
                </div>

                <p class="text-xs text-gray-500 mb-4 leading-relaxed">
                    Pesan ini disiapkan otomatis saat HR atau Asisten mengklik tombol WhatsApp untuk menanyakan konfirmasi
                    kepada pemagang yang <strong>Tidak Hadir</strong>.
                </p>

                <form action="{{ route('admin.settings.wa-template') }}" method="POST" class="space-y-4">
                    @csrf

                    {{-- Textarea Template --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-medium text-gray-700">Format Template Pesan</label>
                            <span class="text-[10px] text-gray-400 font-medium">Klik variabel untuk menyisipkan:</span>
                        </div>

                        {{-- Variable chips (clickable to insert) --}}
                        <div class="flex flex-wrap gap-1.5 mb-2">
                            <button type="button" @click="insertVar('{nama}')"
                                class="px-2 py-1 bg-gray-100 hover:bg-green-50 hover:text-green-700 hover:border-green-300 border border-gray-200 rounded-md text-[11px] font-mono text-gray-700 transition"
                                title="Klik untuk menyisipkan nama pemagang">
                                + {nama}
                            </button>
                            <button type="button" @click="insertVar('{divisi}')"
                                class="px-2 py-1 bg-gray-100 hover:bg-green-50 hover:text-green-700 hover:border-green-300 border border-gray-200 rounded-md text-[11px] font-mono text-gray-700 transition"
                                title="Klik untuk menyisipkan divisi">
                                + {divisi}
                            </button>
                            <button type="button" @click="insertVar('{kampus}')"
                                class="px-2 py-1 bg-gray-100 hover:bg-green-50 hover:text-green-700 hover:border-green-300 border border-gray-200 rounded-md text-[11px] font-mono text-gray-700 transition"
                                title="Klik untuk menyisipkan asal kampus/sekolah">
                                + {kampus}
                            </button>
                        </div>

                        <textarea name="wa_template_tidak_hadir" x-ref="textarea" x-model="template" rows="4" required
                            maxlength="1000" placeholder="Ketik template pesan WhatsApp di sini..."
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition leading-relaxed"></textarea>
                    </div>

                    {{-- Live Preview Box --}}
                    <div class="p-3 bg-emerald-50/60 border border-emerald-200 rounded-xl space-y-1">
                        <div
                            class="flex items-center justify-between text-[11px] font-bold text-emerald-800 uppercase tracking-wider">
                            <span>Pratinjau Pesan:</span>
                            <span
                                class="text-[10px] font-normal text-emerald-600 bg-white px-1.5 py-1 mb-0.5 rounded border border-emerald-200">Live
                                Preview</span>
                        </div>
                        <p class="text-xs text-emerald-950 font-normal leading-relaxed whitespace-pre-wrap bg-white/80 p-2.5 rounded-lg border border-emerald-100 italic"
                            x-text="preview"></p>
                    </div>

                    <button type="submit"
                        class="w-full py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                        Simpan Template Pesan
                    </button>
                </form>
            </div>
        </div>

    </div>

    {{-- ── Kartu: Link Grup WhatsApp ───────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-5 flex items-center gap-2">
            <svg class="w-4 h-4 text-green-500" viewBox="0 0 24 24" fill="currentColor">
                <path
                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z" />
                <path
                    d="M12 0C5.373 0 0 5.373 0 12c0 2.123.554 4.118 1.528 5.845L.057 23.428a.5.5 0 00.515.572l5.736-1.507A11.95 11.95 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.95 0-3.784-.523-5.363-1.437l-.384-.228-3.98 1.046 1.065-3.871-.25-.398A9.956 9.956 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z" />
            </svg>
            Link Grup WhatsApp
        </h2>

        {{-- Form Tambah --}}
        <form action="{{ route('admin.settings.wa-groups.store') }}" method="POST"
            class="space-y-3 mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            @csrf
            <p class="text-xs font-semibold text-gray-600 mb-3">Tambah Link Baru</p>
            <div>
                <input type="text" name="label" placeholder="Nama Grup (contoh: Grup HR Staff)" required maxlength="80"
                    value="{{ old('label') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                                      focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <input type="url" name="url" placeholder="https://chat.whatsapp.com/..." required maxlength="255"
                    value="{{ old('url') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                                      focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-green-600 hover:bg-green-700 text-white
                                                   text-sm font-medium rounded-lg transition whitespace-nowrap">
                Tambah
            </button>
        </form>

        {{-- Daftar link --}}
        <div class="space-y-2">
            @forelse($waGroups as $group)
                <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition"
                    x-data="{ editing: false }">

                    {{-- Info (default) --}}
                    <div class="flex-1 min-w-0" x-show="!editing">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $group->label }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $group->url }}</p>
                    </div>

                    {{-- Form edit inline --}}
                    <form x-show="editing" x-cloak action="{{ route('admin.settings.wa-groups.update', $group) }}" method="POST"
                        class="flex-1 space-y-2">
                        @csrf
                        @method('PATCH')
                        <input type="text" name="label" value="{{ $group->label }}" required maxlength="80"
                            class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-xs
                                                                                          focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <input type="url" name="url" value="{{ $group->url }}" required maxlength="255"
                            class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-xs
                                                                                          focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <div class="flex items-center gap-2">
                            <button type="submit"
                                class="px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white
                                                                                               text-xs font-medium rounded-lg transition">
                                Simpan
                            </button>
                            <button type="button" @click="editing = false"
                                class="px-3 py-1.5 text-xs text-gray-500 border border-gray-300
                                                                                               rounded-lg hover:bg-gray-50 transition">
                                Batal
                            </button>
                        </div>
                    </form>

                    {{-- Tombol aksi --}}
                    <div class="flex items-center gap-1 flex-shrink-0" x-show="!editing">
                        <button @click="editing = true" class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50
                                                                                           rounded-lg transition" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <form action="{{ route('admin.settings.wa-groups.destroy', $group) }}" method="POST"
                            onsubmit="return confirm('Hapus link grup ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50
                                                                                               rounded-lg transition"
                                title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="py-10 text-center text-sm text-gray-400">
                    Belum ada link grup. Tambahkan di atas.
                </div>
            @endforelse
        </div>
    </div>
@endsection