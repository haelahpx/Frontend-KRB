<div class="min-h-screen bg-gray-50" wire:poll.1000ms>
    <?php
        use Carbon\Carbon;
        use Illuminate\Support\Facades\Storage;

        if (!function_exists('fmtDate')) {
            function fmtDate($v){ try{ return $v ? Carbon::parse($v)->format('d M Y') : '—'; }catch(\Throwable){ return '—'; } }
        }
        if (!function_exists('fmtTime')) {
            function fmtTime($v){ try{ return $v ? Carbon::parse($v)->format('H:i') : '—'; }catch(\Throwable){ return (is_string($v) && preg_match('/^\d{2}:\d{2}/',$v)) ? substr($v,0,5) : '—'; } }
        }

        // Theme tokens
        $card   = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
        $label  = 'block text-sm font-medium text-gray-700 mb-2';
        $input  = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
        $btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
        $btnGrn = 'px-3 py-2 text-xs font-medium rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600/20 disabled:opacity-60 transition';

        $chip      = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';
        $icoAvatar = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';
    ?>

    <div class="px-4 sm:px-6 py-6 space-y-8">
        
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <div class="pointer-events-none absolute inset-0 opacity-10">
                <div class="absolute top-0 -right-4 w-24 h-24 bg-white rounded-full blur-xl"></div>
                <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-lg"></div>
            </div>
            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 7h18M3 12h18M3 17h18" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-semibold">Documents & Packages — Status</h2>
                            <p class="text-sm text-white/80">Pantau item pending & tersimpan sebelum delivered/taken.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <section class="<?php echo e($card); ?>">
            <div class="px-6 py-5 bg-gray-50 border-b border-gray-200">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                    <div class="lg:col-span-3">
                        <label class="<?php echo e($label); ?>">Search</label>
                        <div class="relative">
                            <input type="text" class="<?php echo e($input); ?> pl-9"
                                   placeholder="Cari nama item / pengirim / penerima / receptionist…" wire:model.live="q">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="m21 21-4.3-4.3M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                            </svg>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <label class="<?php echo e($label); ?>">Type</label>
                        <select wire:model.live="type" class="<?php echo e($input); ?>">
                            <option value="all">Semua Type</option>
                            <option value="document">Document</option>
                            <option value="package">Package</option>
                        </select>
                    </div>

                    <div class="lg:col-span-2">
                        <label class="<?php echo e($label); ?>">Tanggal (created)</label>
                        <div class="relative">
                            <input type="date" wire:model.live="selectedDate" class="<?php echo e($input); ?> pl-9">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <label class="<?php echo e($label); ?>">Urutkan</label>
                        <select wire:model.live="dateMode" class="<?php echo e($input); ?>">
                            <option value="semua">Urut Default</option>
                            <option value="terbaru">Terbaru</option>
                            <option value="terlama">Terlama</option>
                        </select>
                    </div>

                    <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="space-y-2">
                            <label class="<?php echo e($label); ?>">Department</label>
                            <input type="text" wire:model.live="departmentQ" class="<?php echo e($input); ?>"
                                   placeholder="Cari department..." />
                            <select wire:model.live="departmentId" class="<?php echo e($input); ?>">
                                <option value="">Semua Department</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($dept->department_id); ?>"><?php echo e($dept->department_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="<?php echo e($label); ?>">Receptionist / User</label>
                            <input type="text" wire:model.live="userQ" class="<?php echo e($input); ?>"
                                   placeholder="Cari user..." />
                            <select wire:model.live="userId" class="<?php echo e($input); ?>">
                                <option value="">Semua User</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($u->user_id); ?>"><?php echo e($u->full_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            
            <div class="<?php echo e($card); ?>">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 bg-amber-500 rounded-full"></div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Pending</h3>
                            <p class="text-sm text-gray-500">Item yang baru diterima receptionist, belum disimpan.</p>
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-gray-200">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $pending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $rowNoPending = ($pending->firstItem() ?? 1) + $loop->index; ?>
                        <div class="px-6 py-5 hover:bg-gray-50 transition-colors" wire:key="pending-<?php echo e($row->delivery_id); ?>">
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                <div class="flex items-start gap-3 flex-1 min-w-0">
                                    <div class="<?php echo e($icoAvatar); ?>">
                                        <!--[if BLOCK]><![endif]--><?php if($row->image): ?>
                                            <img
                                                src="<?php echo e(Storage::disk('public')->url($row->image)); ?>"
                                                alt="Bukti foto"
                                                class="w-full h-full object-cover rounded-xl"
                                            >
                                        <?php else: ?>
                                            <?php echo e(strtoupper(substr(($row->item_name ?? 'P')[0] ?? 'P', 0, 1))); ?>

                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 mb-1.5">
                                            <h4 class="font-semibold text-gray-900 text-base truncate">
                                                [<?php echo e(strtoupper($row->type)); ?>] <?php echo e($row->item_name); ?>

                                            </h4>
                                            <span class="text-[11px] px-2 py-0.5 rounded-md bg-gray-100 text-gray-600 border border-gray-200">
                                                #<?php echo e($row->delivery_id); ?>

                                            </span>
                                        </div>

                                        <div class="flex flex-wrap gap-1.5 mb-2">
                                            <!--[if BLOCK]><![endif]--><?php if($row->nama_pengirim): ?>
                                                <span class="<?php echo e($chip); ?>">
                                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    <span class="font-medium text-gray-700">From: <?php echo e($row->nama_pengirim); ?></span>
                                                </span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <!--[if BLOCK]><![endif]--><?php if($row->nama_penerima): ?>
                                                <span class="<?php echo e($chip); ?>">
                                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    <span class="font-medium text-gray-700">To: <?php echo e($row->nama_penerima); ?></span>
                                                </span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <span class="<?php echo e($chip); ?>">
                                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="font-medium text-gray-700">Status: pending</span>
                                            </span>
                                            <!--[if BLOCK]><![endif]--><?php if($row->image): ?>
                                                <span class="<?php echo e($chip); ?>">
                                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M3 7h2l2-3h10l2 3h2v10H3V7z" />
                                                    </svg>
                                                    <span class="font-medium text-gray-700">Ada bukti foto</span>
                                                </span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>

                                        <div class="text-[11px] text-gray-500">
                                            <!--[if BLOCK]><![endif]--><?php if($row->created_at): ?>
                                                Dibuat: <?php echo e($row->created_at->format('d M Y H:i')); ?>

                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right shrink-0 space-y-2">
                                    <span class="inline-block text-[11px] px-2 py-0.5 rounded-lg bg-gray-100 text-gray-600 border border-gray-200">
                                        No. <?php echo e($rowNoPending); ?>

                                    </span>
                                    <div class="mt-2 flex flex-col gap-2">
                                        <button class="<?php echo e($btnBlk); ?>"
                                                wire:click="openEdit(<?php echo e($row->delivery_id); ?>)">
                                            Edit
                                        </button>
                                        <button class="<?php echo e($btnGrn); ?>"
                                                wire:click="storeItem(<?php echo e($row->delivery_id); ?>)">
                                            Tandai sudah disimpan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="px-6 py-14 text-center text-gray-500 text-sm">Tidak ada item pending.</div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <div class="px-6 py-5 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-center">
                        <?php echo e($pending->onEachSide(1)->links('pagination::tailwind')); ?>

                    </div>
                </div>
            </div>

            
            <div class="<?php echo e($card); ?>">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 bg-sky-500 rounded-full"></div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Stored</h3>
                            <p class="text-sm text-gray-500">Item sudah disimpan, siap delivered/taken.</p>
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-gray-200">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $stored; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $rowNoStored = ($stored->firstItem() ?? 1) + $loop->index;
                            $dir = $storedDirections[$row->delivery_id] ?? 'taken';
                        ?>
                        <div class="px-6 py-5 hover:bg-gray-50 transition-colors" wire:key="stored-<?php echo e($row->delivery_id); ?>">
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                <div class="flex items-start gap-3 flex-1 min-w-0">
                                    <div class="<?php echo e($icoAvatar); ?>">
                                        <!--[if BLOCK]><![endif]--><?php if($row->image): ?>
                                            <img
                                                src="<?php echo e(Storage::disk('public')->url($row->image)); ?>"
                                                alt="Bukti foto"
                                                class="w-full h-full object-cover rounded-xl"
                                            >
                                        <?php else: ?>
                                            <?php echo e(strtoupper(substr(($row->item_name ?? 'S')[0] ?? 'S', 0, 1))); ?>

                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 mb-1.5">
                                            <h4 class="font-semibold text-gray-900 text-base truncate">
                                                [<?php echo e(strtoupper($row->type)); ?>] <?php echo e($row->item_name); ?>

                                            </h4>
                                            <span class="text-[11px] px-2 py-0.5 rounded-md bg-gray-100 text-gray-600 border border-gray-200">
                                                #<?php echo e($row->delivery_id); ?>

                                            </span>
                                        </div>

                                        <div class="flex flex-wrap gap-1.5 mb-2">
                                            <!--[if BLOCK]><![endif]--><?php if($row->nama_pengirim): ?>
                                                <span class="<?php echo e($chip); ?>">
                                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    <span class="font-medium text-gray-700">From: <?php echo e($row->nama_pengirim); ?></span>
                                                </span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <!--[if BLOCK]><![endif]--><?php if($row->nama_penerima): ?>
                                                <span class="<?php echo e($chip); ?>">
                                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    <span class="font-medium text-gray-700">To: <?php echo e($row->nama_penerima); ?></span>
                                                </span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <span class="<?php echo e($chip); ?>">
                                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="font-medium text-gray-700">Status: stored</span>
                                            </span>
                                            <!--[if BLOCK]><![endif]--><?php if($row->image): ?>
                                                <span class="<?php echo e($chip); ?>">
                                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M3 7h2l2-3h10l2 3h2v10H3V7z" />
                                                    </svg>
                                                    <span class="font-medium text-gray-700">Ada bukti foto</span>
                                                </span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <span class="<?php echo e($chip); ?>">
                                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span class="font-medium text-gray-700">
                                                    Direction: <?php echo e($dir === 'deliver' ? 'Deliver' : 'Taken'); ?>

                                                </span>
                                            </span>
                                        </div>

                                        <div class="text-[11px] text-gray-500">
                                            <!--[if BLOCK]><![endif]--><?php if($row->created_at): ?>
                                                Dibuat: <?php echo e($row->created_at->format('d M Y H:i')); ?>

                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right shrink-0 space-y-2">
                                    <span class="inline-block text-[11px] px-2 py-0.5 rounded-lg bg-gray-100 text-gray-600 border border-gray-200">
                                        No. <?php echo e($rowNoStored); ?>

                                    </span>
                                    <div class="mt-2 flex flex-col gap-2">
                                        <button class="<?php echo e($btnBlk); ?>"
                                                wire:click="openEdit(<?php echo e($row->delivery_id); ?>)">
                                            Edit
                                        </button>
                                        <button class="<?php echo e($btnGrn); ?>"
                                                wire:click="finalizeItem(<?php echo e($row->delivery_id); ?>)">
                                            <?php echo e($dir === 'deliver' ? 'Mark as Delivered' : 'Mark as Taken'); ?>

                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="px-6 py-14 text-center text-gray-500 text-sm">Tidak ada item tersimpan.</div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <div class="px-6 py-5 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-center">
                        <?php echo e($stored->onEachSide(1)->links('pagination::tailwind')); ?>

                    </div>
                </div>
            </div>
        </div>

        
        <!--[if BLOCK]><![endif]--><?php if($showEdit): ?>
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-all duration-300" wire:click="$set('showEdit', false)"></div>
                <div class="relative w-full max-w-lg bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden transform transition-all duration-300 scale-100 max-h-[90vh] flex flex-col">
                    <div class="bg-gradient-to-r from-gray-900 to-black p-5 text-white relative overflow-hidden">
                        <div class="absolute inset-0 opacity-10 pointer-events-none">
                            <div class="absolute top-0 -right-6 w-24 h-24 bg-white rounded-full blur-2xl"></div>
                            <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-xl"></div>
                        </div>
                        <div class="relative z-10 flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold tracking-tight">Edit Item</h3>
                            </div>
                            <button class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/20 flex items-center justify-center transition-all duration-200" wire:click="$set('showEdit', false)">
                                <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="p-5 space-y-4 overflow-y-auto flex-1">
                        <div>
                            <label class="<?php echo e($label); ?>">Item Name</label>
                            <input type="text" class="<?php echo e($input); ?>" wire:model.defer="edit.item_name">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['edit.item_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-[11px] text-red-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="<?php echo e($label); ?>">Nama Pengirim</label>
                                <input type="text" class="<?php echo e($input); ?>" wire:model.defer="edit.nama_pengirim">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['edit.nama_pengirim'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-[11px] text-red-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <div>
                                <label class="<?php echo e($label); ?>">Nama Penerima</label>
                                <input type="text" class="<?php echo e($input); ?>" wire:model.defer="edit.nama_penerima">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['edit.nama_penerima'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-[11px] text-red-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                        <div>
                            <label class="<?php echo e($label); ?>">Catatan</label>
                            <textarea
                                class="w-full min-h-[100px] px-3 py-2 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition"
                                wire:model.defer="edit.catatan"></textarea>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['edit.catatan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-[11px] text-red-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>

                    <div class="bg-gray-50 border-top border-gray-200 p-5">
                        <div class="flex items-center justify-end gap-2.5">
                            <button type="button" wire:click="$set('showEdit', false)"
                                    class="px-4 h-10 rounded-lg border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-100 hover:border-gray-400 transition">
                                Batal
                            </button>
                            <button type="button" wire:click="saveEdit" wire:loading.attr="disabled" wire:target="saveEdit"
                                    class="px-4 h-10 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition shadow-sm">
                                <span wire:loading.remove wire:target="saveEdit">Simpan Perubahan</span>
                                <span wire:loading wire:target="saveEdit" class="flex items-center gap-2">
                                    <svg class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                    </svg>
                                    Menyimpan…
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
</div>
<?php /**PATH C:\Users\Ochie\OneDrive\Documents\GitHub\Frontend-KRB\resources\views/livewire/pages/receptionist/docpackstatus.blade.php ENDPATH**/ ?>