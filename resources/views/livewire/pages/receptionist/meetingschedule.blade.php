@php
    use Carbon\Carbon;

    if (!function_exists('fmtDate')) {
        function fmtDate($v)
        {
            try {
                return $v ? Carbon::parse($v)->format('d M Y') : '—';
            } catch (\Throwable) {
                return '—';
            }
        }
    }
    if (!function_exists('fmtTime')) {
        function fmtTime($v)
        {
            try {
                return $v ? Carbon::parse($v)->format('H:i') : '—';
            } catch (\Throwable) {
                return (is_string($v) && preg_match('/^\d{2}:\d{2}/', $v)) ? substr($v, 0, 5) : '—';
            }
        }
    }

    $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    $label = 'block text-sm font-medium text-gray-700 mb-2';
    $input = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
    $btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';

    $otherId = null;
    foreach ($requirementOptions as $o) {
        if (strtolower($o['name']) === 'other') {
            $otherId = $o['id'];
            break;
        }
    }
@endphp

<div class="bg-gray-50">
    <main class="px-4 sm:px-6 py-6 space-y-8">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3M5 11h14M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2zm7-6v3l2 2" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg sm:text-xl font-semibold">Meeting Schedule</h2>
                        <p class="text-sm text-white/80">Form Booking Ruangan & Online Meeting.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- FORM: BOOKING ROOM (OFFLINE) --}}
        <section class="{{ $card }}">
            <div class="px-5 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Tambah Booking Room</h3>
                <p class="text-sm text-gray-500">Saat disimpan akan masuk <b>Pending</b> (menunggu approval).</p>
            </div>

            <form class="p-5" wire:submit.prevent="saveOffline">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div class="md:col-span-3">
                        <label class="{{ $label }}">Meeting Title</label>
                        <input type="text" wire:model.defer="form.meeting_title" class="{{ $input }}"
                            placeholder="Contoh: Weekly Sync">
                        @error('form.meeting_title') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">Room</label>
                        <select wire:model.defer="form.room_id" class="{{ $input }}">
                            <option value="" hidden>Pilih room</option>
                            @foreach ($rooms as $r)
                                <option value="{{ $r['id'] }}">{{ $r['name'] }}</option>
                            @endforeach
                        </select>
                        @error('form.room_id') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Department with SEARCH filter (OFFLINE) --}}
                    <div>
                        <label class="{{ $label }}">Departemen</label>
                        <input type="text" wire:model.live="deptQueryOffline" class="{{ $input }}"
                            placeholder="Cari departemen…">
                        <select wire:model.defer="form.department_id" class="{{ $input }} mt-2">
                            <option value="" hidden>Pilih departemen</option>
                            @forelse ($departmentsOffline as $d)
                                <option value="{{ $d['id'] }}">{{ $d['name'] }}</option>
                            @empty
                                <option value="" disabled>Tidak ada hasil</option>
                            @endforelse
                        </select>
                        @error('form.department_id') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">Tanggal</label>
                        <input type="date" wire:model.defer="form.date" class="{{ $input }}">
                        @error('form.date') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">Peserta</label>
                        <input type="number" min="1" wire:model.defer="form.participant" class="{{ $input }}">
                        @error('form.participant') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">Mulai</label>
                        <input type="time" wire:model.defer="form.time" class="{{ $input }}">
                        @error('form.time') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">Selesai</label>
                        <input type="time" wire:model.defer="form.time_end" class="{{ $input }}">
                        @error('form.time_end') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-3">
                        <label class="{{ $label }}">Kebutuhan Ruangan</label>
                        <div
                            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-3 text-sm text-gray-700">
                            @foreach ($requirementOptions as $opt)
                                <label class="inline-flex items-center gap-2 cursor-pointer"
                                    wire:key="req-{{ $opt['id'] }}">
                                    <input type="checkbox" value="{{ $opt['id'] }}" wire:model="form.requirements"
                                        class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                                    <span>{{ $opt['name'] }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('form.requirements.*') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}
                        </p>@enderror
                    </div>

                    @if ($otherId && in_array($otherId, ($form['requirements'] ?? []), true))
                        <div class="md:col-span-3">
                            <label class="{{ $label }}">Catatan</label>
                            <textarea rows="4" wire:model.defer="form.notes" class="{{ $input }} !h-auto resize-none"
                                placeholder="Jelaskan kebutuhan lainnya (wajib jika memilih 'Other')"></textarea>
                            @error('form.notes') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>

                <div class="pt-5">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-5 h-10 rounded-xl bg-gray-900 text-white text-sm font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900/20 transition active:scale-95 hover:bg-black disabled:opacity-60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Data
                    </button>
                </div>
            </form>
        </section>

        {{-- FORM: ONLINE MEETING --}}
        <section class="{{ $card }}">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Create Online Meeting</h3>
                        <p class="text-sm text-gray-500">Form terpisah untuk meeting online. Status approval di halaman
                            lain.</p>
                    </div>
                </div>
            </div>

            <form class="p-5 grid grid-cols-1 lg:grid-cols-2 gap-6" wire:submit.prevent="saveOnline">
                <div class="space-y-4">
                    <div>
                        <label class="{{ $label }}">Meeting Title</label>
                        <input type="text" wire:model.defer="online_meeting_title" class="{{ $input }}"
                            placeholder="Contoh: Standup harian">
                        @error('online_meeting_title') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}
                        </p>@enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="{{ $label }}">Platform</label>
                            <select wire:model.defer="online_platform" class="{{ $input }}">
                                <option value="google_meet">Google Meet</option>
                                <option value="zoom">Zoom</option>
                            </select>
                            @error('online_platform') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}
                            </p>@enderror
                        </div>
                        <div class="flex items-end">
                            @if($online_platform === 'google_meet')
                                <span class="text-[11px] px-2 py-1 rounded bg-yellow-100 text-yellow-800">
                                    {{ $googleConnected ? 'Google connected' : 'Google NOT connected' }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Department with SEARCH filter (ONLINE) --}}
                    <div>
                        <label class="{{ $label }}">Department</label>
                        <input type="text" wire:model.live="deptQueryOnline" class="{{ $input }}"
                            placeholder="Search department…">
                        <select wire:model.live="online_department_id" class="{{ $input }} mt-2">
                            <option value="">— Select Department —</option>
                            @forelse($departmentsOnline as $d)
                                <option value="{{ $d['id'] }}">{{ $d['name'] }}</option>
                            @empty
                                <option value="" disabled>No results</option>
                            @endforelse
                        </select>
                        @error('online_department_id') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}
                        </p>@enderror
                    </div>

                    <div>
                        <label class="{{ $label }}">User (filtered by department)</label>
                        <select wire:model.defer="online_user_id" class="{{ $input }}" @disabled(empty($usersByDept))>
                            <option value="">— Select User —</option>
                            @foreach($usersByDept as $u)
                                <option value="{{ $u['id'] }}">{{ $u['name'] }}</option>
                            @endforeach
                        </select>
                        @error('online_user_id') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="{{ $label }}">Date</label>
                            <input type="date" wire:model.defer="online_date" class="{{ $input }}">
                            @error('online_date') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="{{ $label }}">Start</label>
                            <input type="time" wire:model.defer="online_start_time" class="{{ $input }}">
                            @error('online_start_time') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}
                            </p>@enderror
                        </div>
                        <div>
                            <label class="{{ $label }}">End</label>
                            <input type="time" wire:model.defer="online_end_time" class="{{ $input }}">
                            @error('online_end_time') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}
                            </p>@enderror
                        </div>
                    </div>

                    <div class="lg:sticky lg:top-5 pt-1">
                        <button type="submit" class="{{ $btnBlk }} h-10" wire:loading.attr="disabled">
                            Submit Online Meeting
                        </button>
                    </div>
                </div>
            </form>
        </section>
    </main>

    @if (session('toast'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.dispatchEvent(new CustomEvent('toast', { detail: @json(session('toast')) }));
            });
        </script>
    @endif

    <div x-data="{
            toasts: [],
            addToast(t) {
                t.id = crypto.randomUUID ? crypto.randomUUID() : Date.now() + Math.random();
                t.type = t.type || 'info';
                t.message = t.message || '';
                t.title = t.title || '';
                t.duration = Number(t.duration ?? 3500);
                this.toasts.push(t);
                if (t.duration > 0) {
                    setTimeout(() => this.removeToast(t.id), t.duration);
                }
            },
            removeToast(id) { this.toasts = this.toasts.filter(tt => tt.id !== id); },
            getToastClasses(type) {
                const base =
                'relative overflow-hidden rounded-xl p-4 border border-black/15 bg-white/95 text-black shadow-[0_10px_30px_-12px_rgba(0,0,0,0.35)] backdrop-blur-sm transition-all duration-500 ease-out';
                const variants = {
                    success: 'border-black/20',
                    error:   'border-black/25',
                    warning: 'border-black/20',
                    info:    'border-black/15',
                    neutral: 'border-black/15'
                };
                return base + ' ' + (variants[type] || variants.info);
            },
            getAccentClasses(type) {
                const variants = {
                    success: 'bg-gradient-to-r from-black/90 to-black/70',
                    error:   'bg-gradient-to-r from-black/90 to-black/70',
                    warning: 'bg-gradient-to-r from-black/90 to-black/70',
                    info:    'bg-gradient-to-r from-black/90 to-black/70',
                    neutral: 'bg-gradient-to-r from-black/90 to-black/70'
                };
                return variants[type] || variants.info;
            },
            getIconClasses(type) {
                const base = 'flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg';
                const variants = {
                    success: 'bg-black text-white',
                    error:   'bg-black text-white',
                    warning: 'bg-black text-white',
                    info:    'bg-black text-white',
                    neutral: 'bg-black text-white'
                };
                return base + ' ' + (variants[type] || variants.info);
            },
            getIcon(type) {
                const icons = { success: '✓', error: '✕', warning: '⚠', info: 'ⓘ', neutral: '•' };
                return icons[type] || icons.info;
            }
        }" x-on:toast.window="addToast($event.detail)"
        class="fixed top-6 right-6 z-50 flex flex-col gap-4 w-[calc(100vw-3rem)] max-w-sm pointer-events-none"
        aria-live="polite">

        <template x-for="toast in toasts" :key="toast.id">
            <div x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="translate-x-full opacity-0 scale-95"
                x-transition:enter-end="translate-x-0 opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="translate-x-0 opacity-100 scale-100"
                x-transition:leave-end="translate-x-full opacity-0 scale-95" class="pointer-events-auto"
                :class="getToastClasses(toast.type)">
                <div class="absolute top-0 left-0 right-0 h-1" :class="getAccentClasses(toast.type)"></div>
                <div class="absolute top-0 left-0 right-0 h-1 opacity-50 animate-pulse"
                    :class="getAccentClasses(toast.type)"></div>
                <div class="flex items-start gap-4">
                    <div :class="getIconClasses(toast.type)"><span x-text="getIcon(toast.type)"></span></div>
                    <div class="flex-1 min-w-0 pt-1">
                        <h4 x-show="toast.title" x-text="toast.title"
                            class="font-semibold text-base mb-1 leading-tight tracking-tight"></h4>
                        <p x-show="toast.message" x-text="toast.message" class="text-sm leading-relaxed text-black/70">
                        </p>
                    </div>
                    <button @click="removeToast(toast.id)"
                        class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg text-black/60 hover:text-black hover:bg:black/5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-black/20"
                        aria-label="Close notification">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>