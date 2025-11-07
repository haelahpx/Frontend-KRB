<div class="min-h-screen bg-gray-50" wire:poll.1000ms.keep-alive>
    @php
        $label = 'block text-xs font-medium text-gray-700 mb-1';
        $input = 'w-full text-sm rounded-md border border-gray-300 px-3 py-2 bg-white';
        $inputDisabled = $input . ' cursor-not-allowed bg-gray-100 text-gray-500';
        $errorText = 'text-xs text-red-600 mt-1';
    @endphp

    <style>
        :root { color-scheme: light; }
        select, option {
            color:#111827 !important;
            background:#ffffff !important;
            -webkit-text-fill-color:#111827 !important;
        }
        option:checked { background:#e5e7eb !important; color:#111827 !important; }
    </style>

    <div class="max-w-7xl mx-auto py-6 grid grid-cols-1 lg:grid-cols-3 gap-6 px-4">
        {{-- LEFT: MAIN FORM --}}
        <div class="lg:col-span-2">
            <div class="bg-white border-2 border-black/5 rounded-2xl shadow-sm overflow-hidden">
                {{-- HEADER --}}
                <div class="px-6 py-5 border-b border-black/5 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-2xl font-semibold">Doc/Pack Form (Receptionist)</h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Input paket / dokumen yang masuk maupun titipan untuk dikirim.
                        </p>
                    </div>

                    {{-- SMALL NAV SWITCHER (sesuaikan route kalau perlu) --}}
                    <div class="inline-flex rounded-lg overflow-hidden bg-gray-100 border border-gray-200">
                        <a href="{{ route('receptionist.docpackstatus') ?? '#' }}"
                           class="px-3 md:px-4 py-2 text-xs md:text-sm font-medium text-gray-700 hover:text-gray-900 border-r border-gray-200 inline-flex items-center gap-2">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M4 7h16v11H4z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M9 7V5h6v2" />
                            </svg>
                            Doc/Pack Status
                        </a>
                        <span
                            class="px-3 md:px-4 py-2 text-xs md:text-sm font-medium bg-gray-900 text-white inline-flex items-center gap-2">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M12 7v10M7 12h10" />
                            </svg>
                            New Entry
                        </span>
                    </div>
                </div>

                {{-- BODY --}}
                <div class="p-6">
                    <form wire:submit.prevent="save" class="space-y-5">
                        {{-- TOP: ARAH + TIPE + STORAGE --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- Arah --}}
                            <div>
                                <label class="{{ $label }}">
                                    Arah <span class="text-red-600">*</span>
                                </label>
                                <select class="{{ $input }}" wire:model.live="direction" wire:key="direction-select">
                                    <option value="taken">Masuk untuk internal (Taken)</option>
                                    <option value="deliver">Titip untuk dikirim (Deliver later)</option>
                                </select>
                                @error('direction')
                                <div class="{{ $errorText }}">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tipe --}}
                            <div>
                                <label class="{{ $label }}">
                                    Tipe <span class="text-red-600">*</span>
                                </label>
                                <select class="{{ $input }}" wire:model.live="itemType" wire:key="type-select">
                                    <option value="package">Package</option>
                                    <option value="document">Document</option>
                                </select>
                                @error('itemType')
                                <div class="{{ $errorText }}">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Storage --}}
                            <div>
                                <label class="{{ $label }}">
                                    Tempat Penyimpanan <span class="text-red-600">*</span>
                                </label>
                                <select class="{{ $input }}" wire:model.defer="storageId" wire:key="storage-select">
                                    <option value="">Pilih penyimpanan…</option>
                                    @foreach($storages as $s)
                                        <option wire:key="storage-{{ $s['id'] }}" value="{{ $s['id'] }}">
                                            {{ $s['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('storageId')
                                <div class="{{ $errorText }}">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- NAMA PAKET/DOKUMEN --}}
                        <div>
                            <label class="{{ $label }}">
                                Nama Paket/Dokumen <span class="text-red-600">*</span>
                            </label>
                            <input type="text"
                                   class="{{ $input }}"
                                   wire:model.defer="itemName"
                                   placeholder="Contoh: Dokumen Kontrak PT ABC">
                            @error('itemName')
                            <div class="{{ $errorText }}">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- DEPARTEMEN + USER + FREE TEXT --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-4">
                                {{-- Departemen --}}
                                <div>
                                    <label class="{{ $label }}">
                                        {{ $direction === 'taken' ? 'Departemen Penerima' : 'Departemen Pengirim' }}
                                        <span class="text-red-600">*</span>
                                    </label>
                                    <select class="{{ $input }}" wire:model.live="departmentId" wire:key="dept-select">
                                        <option value="">Pilih departemen…</option>
                                        @foreach($departments as $d)
                                            <option wire:key="dept-{{ $d['id'] }}" value="{{ $d['id'] }}">
                                                {{ $d['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('departmentId')
                                    <div class="{{ $errorText }}">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- User --}}
                                <div>
                                    <label class="{{ $label }}">
                                        {{ $direction === 'taken' ? 'Nama Penerima (User)' : 'Nama Pengirim (User)' }}
                                        <span class="text-red-600">*</span>
                                    </label>

                                    <select
                                        class="{{ $departmentId && !empty($users) ? $input : $inputDisabled }}"
                                        wire:model.live="userId"
                                        wire:key="user-select-{{ $departmentId ?? 'none' }}"
                                        @disabled(!$departmentId || empty($users))
                                    >
                                        <option value="" @if(!$departmentId) selected @endif>
                                            @if(!$departmentId)
                                                Pilih departemen dulu…
                                            @elseif(empty($users))
                                                Tidak ada user pada departemen ini
                                            @else
                                                Pilih user…
                                            @endif
                                        </option>

                                        @if($departmentId && !empty($users))
                                            @foreach($users as $id => $name)
                                                <option wire:key="user-{{ $id }}" value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        @endif
                                    </select>

                                    @error('userId')
                                    <div class="{{ $errorText }}">{{ $message }}</div>
                                    @enderror

                                    <p class="text-[11px] text-gray-500 mt-1">
                                        User diambil dari master karyawan sesuai departemen.
                                    </p>
                                </div>
                            </div>

                            {{-- Free text sender/receiver --}}
                            <div class="space-y-4">
                                @if ($direction === 'taken')
                                    <div>
                                        <label class="{{ $label }}">
                                            Nama Pengirim (Free Text) <span class="text-red-600">*</span>
                                        </label>
                                        <input type="text"
                                               class="{{ $input }}"
                                               wire:model.defer="senderText"
                                               placeholder="Kurir / Ekspedisi / Pengirim">
                                        @error('senderText')
                                        <div class="{{ $errorText }}">{{ $message }}</div>
                                        @enderror
                                        <p class="text-[11px] text-gray-500 mt-1">
                                            Contoh: JNE, SiCepat, atau nama perorangan pengirim.
                                        </p>
                                    </div>
                                @else
                                    <div>
                                        <label class="{{ $label }}">
                                            Nama Penerima (Free Text) <span class="text-red-600">*</span>
                                        </label>
                                        <input type="text"
                                               class="{{ $input }}"
                                               wire:model.defer="receiverText"
                                               placeholder="Nama penerima di luar kantor">
                                        @error('receiverText')
                                        <div class="{{ $errorText }}">{{ $message }}</div>
                                        @enderror
                                        <p class="text-[11px] text-gray-500 mt-1">
                                            Contoh: Nama pihak eksternal yang akan menerima paket.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- FOTO BUKTI --}}
                        <div class="space-y-2">
                            <label class="{{ $label }}">Bukti Foto (opsional)</label>

                            <div class="space-y-3">
                                {{-- Upload dari file / galeri --}}
                                <input
                                    id="photo-input"
                                    type="file"
                                    class="{{ $input }} !py-1.5"
                                    wire:model="photo"
                                    accept="image/*"
                                    capture="environment"
                                >
                                @error('photo')
                                <div class="{{ $errorText }}">{{ $message }}</div>
                                @enderror

                                {{-- Tombol kamera --}}
                                <button
                                    type="button"
                                    id="open-camera-btn"
                                    class="inline-flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                              d="M3 7h2l2-3h10l2 3h2v10H3z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    Ambil dari kamera
                                </button>

                                {{-- Preview --}}
                                @if ($photo)
                                    <div class="mt-2">
                                        <p class="text-xs text-gray-500 mb-1">Preview bukti:</p>
                                        <img src="{{ $photo->temporaryUrl() }}"
                                             class="w-40 h-40 object-cover rounded-xl border border-gray-200">
                                    </div>
                                @endif

                                <p class="text-[11px] text-gray-500">
                                    Di HP: bisa pakai kamera atau galeri. Di laptop/PC: klik "Ambil dari kamera",
                                    lalu izinkan akses kamera di browser.
                                </p>
                            </div>
                        </div>

                        {{-- SUBMIT --}}
                        <div class="pt-2 flex items-center gap-3">
                            <button type="submit"
                                    class="px-4 py-2 rounded-md bg-slate-900 text-white text-sm hover:bg-black"
                                    wire:loading.attr="disabled"
                                    wire:target="save,photo">
                                <span wire:loading.remove wire:target="save,photo">Simpan</span>
                                <span wire:loading wire:target="save,photo" class="inline-flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4" />
                                        <path class="opacity-75" fill="currentColor"
                                              d="M4 12a8 8 0 018-8V0A12 12 0 000 12h4z" />
                                    </svg>
                                    Menyimpan…
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- RIGHT: INFO / TIPS --}}
        <div class="space-y-6">
            <div class="bg-white border-2 border-black/5 rounded-2xl shadow-sm p-5 text-sm text-gray-600">
                <p class="mb-2">
                    Gunakan halaman ini untuk mencatat semua paket / dokumen yang melewati resepsionis.
                </p>
                <ul class="list-disc list-inside text-[13px] space-y-1">
                    <li><span class="font-semibold">Arah = Taken</span> untuk paket masuk ke user internal.</li>
                    <li><span class="font-semibold">Arah = Deliver later</span> untuk paket titipan yang akan dikirim.</li>
                    <li>Departemen & user diambil dari data master karyawan.</li>
                    <li>Bukti foto membantu tracking paket saat pengambilan / pengiriman.</li>
                </ul>
            </div>

            <div class="bg-amber-50 border border-amber-100 rounded-2xl shadow-sm p-4 text-xs text-amber-800">
                Pastikan data <span class="font-semibold">nama pengirim/penerima</span> dan
                <span class="font-semibold">penyimpanan</span> sudah benar sebelum simpan.
            </div>
        </div>
    </div>

    {{-- MODAL KAMERA (wire:ignore) --}}
    <div id="camera-modal"
         wire:ignore
         class="fixed inset-0 bg-black/60 z-50 items-center justify-center p-4 hidden">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full overflow-hidden">
            <div class="bg-gray-900 text-white px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-sm font-medium">Kamera</span>
                </div>
                <button id="close-camera-btn"
                        class="w-8 h-8 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/20">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-4 space-y-4">
                <div class="bg-black/5 rounded-lg overflow-hidden flex items-center justify-center">
                    <video id="camera-video" autoplay playsinline class="max-h-[60vh]"></video>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500">
                        Pastikan browser mengizinkan akses kamera (HTTPS / localhost).
                    </span>
                    <button id="capture-btn"
                            class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="12" cy="12" r="3.5" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 7h3l2-3h8l2 3h3v12H3z" />
                        </svg>
                        Ambil Foto
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- JS kamera + DEBUG LOGS --}}
    <script>
        (function () {
            console.log('[DocPackForm] <script> tag evaluated');

            function initCameraScript() {
                console.log('[DocPackForm] initCameraScript called');

                let stream = null;

                const openBtn    = document.getElementById('open-camera-btn');
                const modal      = document.getElementById('camera-modal');
                const closeBtn   = document.getElementById('close-camera-btn');
                const video      = document.getElementById('camera-video');
                const captureBtn = document.getElementById('capture-btn');
                const fileInput  = document.getElementById('photo-input');

                console.log('[DocPackForm] DOM lookup:', {
                    openBtn: !!openBtn,
                    modal: !!modal,
                    closeBtn: !!closeBtn,
                    video: !!video,
                    captureBtn: !!captureBtn,
                    fileInput: !!fileInput,
                });

                if (!openBtn || !modal || !video || !captureBtn || !fileInput || !closeBtn) {
                    console.warn('[DocPackForm] Some elements not found, aborting initCameraScript');
                    return;
                }

                async function openCamera() {
                    console.log('[DocPackForm] openCamera() called');

                    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                        console.error('[DocPackForm] navigator.mediaDevices.getUserMedia NOT available');
                        alert('Browser tidak mendukung kamera (getUserMedia tidak tersedia).');
                        return;
                    }

                    try {
                        console.log('[DocPackForm] Requesting getUserMedia...');
                        stream = await navigator.mediaDevices.getUserMedia({ video: true });
                        console.log('[DocPackForm] getUserMedia SUCCESS, stream tracks:', stream.getTracks().length);
                        video.srcObject = stream;
                        video.play().then(() => {
                            console.log('[DocPackForm] video.play() resolved');
                        }).catch((e) => {
                            console.error('[DocPackForm] video.play() error:', e);
                        });
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                        console.log('[DocPackForm] camera modal shown');
                    } catch (e) {
                        console.error('[DocPackForm] getUserMedia ERROR:', e);
                        alert('Gagal mengakses kamera. Cek izin browser & HTTPS / localhost.');
                    }
                }

                function closeCamera() {
                    console.log('[DocPackForm] closeCamera() called');
                    if (stream) {
                        console.log('[DocPackForm] stopping stream tracks');
                        stream.getTracks().forEach(t => t.stop());
                        stream = null;
                    }
                    video.srcObject = null;
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }

                openBtn.addEventListener('click', () => {
                    console.log('[DocPackForm] open-camera-btn CLICK');
                    openCamera();
                });

                closeBtn.addEventListener('click', () => {
                    console.log('[DocPackForm] close-camera-btn CLICK');
                    closeCamera();
                });

                captureBtn.addEventListener('click', () => {
                    console.log('[DocPackForm] capture-btn CLICK');
                    if (!stream) {
                        console.warn('[DocPackForm] No stream when capture clicked');
                        return;
                    }

                    const canvas = document.createElement('canvas');
                    canvas.width  = video.videoWidth  || 640;
                    canvas.height = video.videoHeight || 480;
                    console.log('[DocPackForm] capture canvas size:', canvas.width, canvas.height);

                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    canvas.toBlob((blob) => {
                        if (!blob) {
                            console.error('[DocPackForm] canvas.toBlob returned null blob');
                            return;
                        }

                        console.log('[DocPackForm] canvas.toBlob OK, size:', blob.size);

                        const file = new File([blob], 'camera-photo.png', { type: 'image/png' });
                        const dt = new DataTransfer();
                        dt.items.add(file);

                        fileInput.files = dt.files;
                        console.log('[DocPackForm] fileInput.files set from camera, dispatching change event');
                        fileInput.dispatchEvent(new Event('change', { bubbles: true }));

                        closeCamera();
                    }, 'image/png');
                });

                if (window.Livewire) {
                    Livewire.hook('message.processed', () => {
                        console.log('[DocPackForm] Livewire message.processed (poll re-render)');
                    });
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                console.log('[DocPackForm] DOMContentLoaded');
                initCameraScript();
            });

            document.addEventListener('livewire:load', () => {
                console.log('[DocPackForm] livewire:load');
                initCameraScript();
            });
        })();
    </script>
</div>
