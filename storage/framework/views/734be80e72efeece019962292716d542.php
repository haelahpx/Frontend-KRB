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

                
                <div class="pt-2">
                    <button type="submit" class="<?php echo e($btnBlk); ?>" wire:loading.attr="disabled" wire:target="save">
                        <span wire:loading.remove wire:target="save">Simpan</span>
                        <span wire:loading wire:target="save" class="inline-flex items-center gap-2">
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
</div>
<?php /**PATH /home/haelahpx/Documents/GitHub/Frontend-KRB/resources/views/livewire/pages/receptionist/docpackform.blade.php ENDPATH**/ ?>