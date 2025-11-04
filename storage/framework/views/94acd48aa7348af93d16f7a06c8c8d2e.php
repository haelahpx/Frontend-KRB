<div class="min-h-screen bg-gray-50" wire:poll.1000ms.keep-alive>
    <?php
        $card   = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
        $head   = 'bg-gradient-to-r from-gray-900 to-black';
        $hpad   = 'px-6 py-5';
        $label  = 'block text-sm font-medium text-gray-700 mb-2';
        $input  = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-900 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
        $btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
    ?>

    <style>
      :root { color-scheme: light; }
      select, option {
        color:#111827 !important;
        background:#ffffff !important;
        -webkit-text-fill-color:#111827 !important;
      }
      option:checked { background:#e5e7eb !important; color:#111827 !important; }
    </style>

    <div class="px-4 sm:px-6 py-6 space-y-8">
        
        <div class="relative overflow-hidden rounded-2xl <?php echo e($head); ?> text-white shadow-2xl">
            <div class="pointer-events-none absolute inset-0 opacity-10">
                <div class="absolute top-0 -right-4 w-24 h-24 bg-white rounded-full blur-xl"></div>
                <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-lg"></div>
            </div>
            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m-4-4h8M4 6h16v12H4z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg sm:text-xl font-semibold">Doc/Pack Form</h2>
                        <p class="text-sm text-white/80">Input paket/dokumen dengan alur masuk/keluar</p>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="<?php echo e($card); ?>">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 bg-gray-900 rounded-full"></div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Tambah Data</h3>
                        <p class="text-sm text-gray-500">Lengkapi detail paket/dokumen</p>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="save" class="p-6 space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="<?php echo e($label); ?>">Arah</label>
                        <select class="<?php echo e($input); ?>" wire:model.live="direction" wire:key="direction-select">
                            <option value="taken">Masuk untuk internal (Taken)</option>
                            <option value="deliver">Titip untuk dikirim (Deliver later)</option>
                        </select>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['direction'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div>
                        <label class="<?php echo e($label); ?>">Tipe</label>
                        <select class="<?php echo e($input); ?>" wire:model.live="itemType" wire:key="type-select">
                            <option value="package">Package</option>
                            <option value="document">Document</option>
                        </select>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['itemType'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div>
                        <label class="<?php echo e($label); ?>">Tempat Penyimpanan</label>
                        <select class="<?php echo e($input); ?>" wire:model.defer="storageId" wire:key="storage-select">
                            <option value="">Pilih penyimpanan…</option>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $storages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option wire:key="storage-<?php echo e($s['id']); ?>" value="<?php echo e($s['id']); ?>"><?php echo e($s['name']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </select>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['storageId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                
                <div>
                    <label class="<?php echo e($label); ?>">Nama Paket/Dokumen</label>
                    <input type="text" class="<?php echo e($input); ?>" wire:model.defer="itemName" placeholder="Contoh: Dokumen Kontrak PT ABC">
                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['itemName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-5">
                        <div>
                            <label class="<?php echo e($label); ?>">
                                <?php echo e($direction === 'taken' ? 'Departemen Penerima' : 'Departemen Pengirim'); ?>

                            </label>
                            <select class="<?php echo e($input); ?>" wire:model.live="departmentId" wire:key="dept-select">
                                <option value="">Pilih departemen…</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option wire:key="dept-<?php echo e($d['id']); ?>" value="<?php echo e($d['id']); ?>"><?php echo e($d['name']); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['departmentId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        <div>
                            <label class="<?php echo e($label); ?>">
                                <?php echo e($direction === 'taken' ? 'Nama Penerima (User)' : 'Nama Pengirim (User)'); ?>

                            </label>

                            <select
                                class="<?php echo e($input); ?> bg-white text-gray-900"
                                wire:model.live="userId"
                                wire:key="user-select-<?php echo e($departmentId ?? 'none'); ?>"
                                <?php if(!$departmentId || empty($users)): echo 'disabled'; endif; ?>
                            >
                                <option value="" selected disabled>
                                    <?php echo e(!$departmentId ? 'Pilih departemen dulu…' : (empty($users) ? 'Tidak ada user pada departemen ini' : 'Pilih user…')); ?>

                                </option>

                                <!--[if BLOCK]><![endif]--><?php if($departmentId && !empty($users)): ?>
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option wire:key="user-<?php echo e($id); ?>" value="<?php echo e($id); ?>"><?php echo e($name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['userId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>

                    <div class="space-y-5">
                        <!--[if BLOCK]><![endif]--><?php if($direction === 'taken'): ?>
                            <div>
                                <label class="<?php echo e($label); ?>">Nama Pengirim (Free Text)</label>
                                <input type="text" class="<?php echo e($input); ?>" wire:model.defer="senderText" placeholder="Kurir / Ekspedisi / Pengirim">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['senderText'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        <?php else: ?>
                            <div>
                                <label class="<?php echo e($label); ?>">Nama Penerima (Free Text)</label>
                                <input type="text" class="<?php echo e($input); ?>" wire:model.defer="receiverText" placeholder="Nama penerima">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['receiverText'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                
                <div>
                    <label class="<?php echo e($label); ?>">Bukti Foto (opsional)</label>

                    <div class="space-y-3">
                        
                        <input
                            id="photo-input"
                            type="file"
                            class="<?php echo e($input); ?> !h-auto py-2"
                            wire:model="photo"
                            accept="image/*"
                            capture="environment"
                        >
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->

                        
                        <button
                            type="button"
                            id="open-camera-btn"
                            class="inline-flex items-center gap-2 px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 7h2l2-3h10l2 3h2v10H3V7z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            Ambil dari kamera
                        </button>

                        
                        <!--[if BLOCK]><![endif]--><?php if($photo): ?>
                            <div class="mt-2">
                                <p class="text-xs text-gray-500 mb-1">Preview bukti:</p>
                                <img src="<?php echo e($photo->temporaryUrl()); ?>" class="w-40 h-40 object-cover rounded-xl border border-gray-200">
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <p class="text-[11px] text-gray-500">
                            Di HP: bisa pakai kamera atau galeri. Di laptop/PC: klik "Ambil dari kamera", beri izin akses kamera.
                        </p>
                    </div>
                </div>

                
                <div class="pt-2">
                    <button type="submit" class="<?php echo e($btnBlk); ?>" wire:loading.attr="disabled" wire:target="save,photo">
                        <span wire:loading.remove wire:target="save,photo">Simpan</span>
                        <span wire:loading wire:target="save,photo" class="inline-flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0A12 12 0 000 12h4z" />
                            </svg>
                            Menyimpan…
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    
    <div id="camera-modal"
         wire:ignore   
         class="fixed inset-0 bg-black/60 z-50 items-center justify-center p-4 hidden">
        <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full overflow-hidden">
            <div class="bg-gray-900 text-white px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-sm font-medium">Kamera</span>
                </div>
                <button id="close-camera-btn" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/10 hover:bg.white/20">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
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
                    <button id="capture-btn" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black">
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

    
    <script>
        (function () {
            console.log('[DocPackForm] <script> tag evaluated');

            function initCameraScript() {
                console.log('[DocPackForm] initCameraScript called');

                let stream = null;

                const openBtn   = document.getElementById('open-camera-btn');
                const modal     = document.getElementById('camera-modal');
                const closeBtn  = document.getElementById('close-camera-btn');
                const video     = document.getElementById('camera-video');
                const captureBtn= document.getElementById('capture-btn');
                const fileInput = document.getElementById('photo-input');

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

                // Attach listeners
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

                // Optional: log setiap Livewire re-render
                if (window.Livewire) {
                    Livewire.hook('message.processed', () => {
                        console.log('[DocPackForm] Livewire message.processed (poll re-render)');
                    });
                }
            }

            // Try to init on both events, for safety
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
<?php /**PATH C:\Users\Ochie\OneDrive\Documents\GitHub\Frontend-KRB\resources\views/livewire/pages/receptionist/docpackform.blade.php ENDPATH**/ ?>