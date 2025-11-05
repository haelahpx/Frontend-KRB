<div class="min-h-screen bg-gray-50" wire:poll.1000ms.keep-alive>
    <?php
        use Carbon\Carbon;

        if (!function_exists('fmtDate')) {
            function fmtDate($v){ try{ return $v ? Carbon::parse($v)->format('d M Y') : '—'; }catch(\Throwable){ return '—'; } }
        }
        if (!function_exists('fmtTime')) {
            function fmtTime($v){ try{ return $v ? Carbon::parse($v)->format('H:i') : '—'; }catch(\Throwable){ return (is_string($v) && preg_match('/^\d{2}:\d{2}/',$v)) ? substr($v,0,5) : '—'; } }
        }

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
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg sm:text-xl font-semibold">Documents & Packages — Status</h2>
                        <p class="text-sm text-white/80">Flow: Pending → Stored → Delivered/Taken.</p>
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
                                   placeholder="Cari nama item / pengirim / penerima / receptionist…"
                                   wire:model.live="q">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.3-4.3M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
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
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <input type="text" wire:model.live="departmentQ" class="<?php echo e($input); ?>" placeholder="Cari department..." />
                            <select wire:model.live="departmentId" class="<?php echo e($input); ?>">
                                <option value="">Semua Department</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($dept->department_id); ?>"><?php echo e($dept->department_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="<?php echo e($label); ?>">Receptionist / User</label>
                            <input type="text" wire:model.live="userQ" class="<?php echo e($input); ?>" placeholder="Cari user..." />
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
                            <p class="text-sm text-gray-500">Masih di receptionist dan belum disimpan di slot.</p>
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-gray-200">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $pending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $rowNoPending = ($pending->firstItem() ?? 1) + $loop->index; ?>
                        <div class="px-6 py-5 hover:bg-gray-50 transition-colors" wire:key="pending-<?php echo e($row->delivery_id); ?>">
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                <div class="flex items-start gap-3 flex-1 min-w-0">
                                    <div class="<?php echo e($icoAvatar); ?>"><?php echo e(strtoupper(substr(($row->item_name ?? 'D')[0] ?? 'D',0,1))); ?></div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 mb-1.5">
                                            <h4 class="font-semibold text-gray-900 text-base truncate">
                                                [<?php echo e(strtoupper($row->type)); ?>] <?php echo e($row->item_name); ?>

                                            </h4>
                                            <span class="text-[11px] px-2 py-0.5 rounded-md bg-gray-100 text-gray-600 border border-gray-200">#<?php echo e($row->delivery_id); ?></span>
                                        </div>

                                        <div class="flex flex-wrap gap-1.5 mb-2">
                                            <!--[if BLOCK]><![endif]--><?php if($row->nama_pengirim): ?>
                                                <span class="<?php echo e($chip); ?>"><span class="font-medium text-gray-700">From: <?php echo e($row->nama_pengirim); ?></span></span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <!--[if BLOCK]><![endif]--><?php if($row->nama_penerima): ?>
                                                <span class="<?php echo e($chip); ?>"><span class="font-medium text-gray-700">To: <?php echo e($row->nama_penerima); ?></span></span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <!--[if BLOCK]><![endif]--><?php if($row->receptionist?->full_name): ?>
                                                <span class="<?php echo e($chip); ?>"><span class="font-medium text-gray-700">Recp: <?php echo e($row->receptionist->full_name); ?></span></span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <span class="<?php echo e($chip); ?>"><span class="font-medium text-gray-700">Created: <?php echo e(optional($row->created_at)->format('d M Y H:i')); ?></span></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right shrink-0 space-y-2">
                                    <span class="inline-block text-[11px] px-2 py-0.5 rounded-lg bg-gray-100 text-gray-600 border border-gray-200">No. <?php echo e($rowNoPending); ?></span>
                                    <div class="mt-2 flex flex-col gap-2">
                                        <button type="button" wire:click="openEdit(<?php echo e($row->delivery_id); ?>)" class="<?php echo e($btnBlk); ?>">Edit</button>
                                        <button type="button" wire:click="storeItem(<?php echo e($row->delivery_id); ?>)" class="<?php echo e($btnGrn); ?>">Store</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="px-6 py-14 text-center text-gray-500 text-sm">Tidak ada data pending.</div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <div class="px-6 py-5 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-center">
                        <?php echo e($pending->onEachSide(1)->links()); ?>

                    </div>
                </div>
            </div>

            
            <div class="<?php echo e($card); ?>">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 bg-emerald-600 rounded-full"></div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Stored</h3>
                            <p class="text-sm text-gray-500">Sudah disimpan di slot perusahaan.</p>
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-gray-200">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $stored; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $rowNoStored = ($stored->firstItem() ?? 1) + $loop->index;
                            $pk = $row->delivery_id;
                            // Preferred: DB column; Fallback: map built in render()
                            $dirFromDb = $row->direction === 'deliver' || $row->direction === 'taken' ? $row->direction : null;
                            $dir = $dirFromDb ?? ($storedDirections[$pk] ?? 'deliver');
                            $finalLabel = $dir === 'deliver' ? 'Delivered' : 'Taken';
                        ?>
                        <div class="px-6 py-5 hover:bg-gray-50 transition-colors" wire:key="stored-<?php echo e($row->delivery_id); ?>">
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                <div class="flex items-start gap-3 flex-1 min-w-0">
                                    <div class="<?php echo e($icoAvatar); ?>"><?php echo e(strtoupper(substr(($row->item_name ?? 'D')[0] ?? 'D',0,1))); ?></div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 mb-1.5">
                                            <h4 class="font-semibold text-gray-900 text-base truncate">
                                                [<?php echo e(strtoupper($row->type)); ?>] <?php echo e($row->item_name); ?>

                                            </h4>
                                            <span class="text-[11px] px-2 py-0.5 rounded-md bg-gray-100 text-gray-600 border border-gray-200">#<?php echo e($row->delivery_id); ?></span>
                                        </div>

                                        <div class="flex flex-wrap gap-1.5 mb-2">
                                            <!--[if BLOCK]><![endif]--><?php if($row->receptionist?->full_name): ?>
                                                <span class="<?php echo e($chip); ?>"><span class="font-medium text-gray-700">Recp: <?php echo e($row->receptionist->full_name); ?></span></span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <span class="<?php echo e($chip); ?>"><span class="font-medium text-gray-700">Created: <?php echo e(optional($row->created_at)->format('d M Y H:i')); ?></span></span>
                                            <span class="<?php echo e($chip); ?>"><span class="font-medium text-gray-700">Direction: <?php echo e($dir); ?></span></span>
                                            <span class="<?php echo e($chip); ?>"><span class="font-medium text-gray-700">Status: <?php echo e($row->status); ?></span></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right shrink-0 space-y-2">
                                    <span class="inline-block text-[11px] px-2 py-0.5 rounded-lg bg-gray-100 text-gray-600 border border-gray-200">No. <?php echo e($rowNoStored); ?></span>
                                    <div class="mt-2">
                                        <button type="button"
                                                wire:click="finalizeItem(<?php echo e($row->delivery_id); ?>)"
                                                class="<?php echo e($btnGrn); ?>">
                                            <?php echo e($finalLabel); ?>

                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="px-6 py-14 text-center text-gray-500 text-sm">Tidak ada data stored.</div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <div class="px-6 py-5 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-center">
                        <?php echo e($stored->onEachSide(1)->links()); ?>

                    </div>
                </div>
            </div>
        </div>
        
    </div>

    
    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'e60dd9d2c3a62d619c9acb38f20d5aa5::modal.index','data' => ['wire:model' => 'showEdit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'showEdit']); ?>
         <?php $__env->slot('title', null, []); ?> Edit Data <?php $__env->endSlot(); ?>
        <div class="space-y-3">
            <div>
                <label class="<?php echo e($label); ?>">Item Name</label>
                <input type="text" class="<?php echo e($input); ?>" wire:model.defer="edit.item_name">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="<?php echo e($label); ?>">Nama Pengirim</label>
                    <input type="text" class="<?php echo e($input); ?>" wire:model.defer="edit.nama_pengirim">
                </div>
                <div>
                    <label class="<?php echo e($label); ?>">Nama Penerima</label>
                    <input type="text" class="<?php echo e($input); ?>" wire:model.defer="edit.nama_penerima">
                </div>
            </div>
            <div>
                <label class="<?php echo e($label); ?>">Catatan</label>
                <textarea class="w-full min-h-[100px] px-3 py-2 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition" wire:model.defer="edit.catatan"></textarea>
            </div>
        </div>
         <?php $__env->slot('footer', null, []); ?> 
            <div class="flex items-center justify-end gap-2">
                <button type="button" class="px-3 py-2 text-xs font-medium rounded-lg bg-gray-100 text-gray-700 border hover:bg-gray-200" wire:click="$set('showEdit', false)">Cancel</button>
                <button type="button" class="<?php echo e($btnBlk); ?>" wire:click="saveEdit">Save</button>
            </div>
         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
</div>
<?php /**PATH /home/haelahpx/Documents/GitHub/Frontend-KRB/resources/views/livewire/pages/receptionist/docpackstatus.blade.php ENDPATH**/ ?>