
<div class="bg-gray-50">
    <?php
        $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
        $head = 'bg-gradient-to-r from-black to-gray-800';
        $hpad = 'px-8 py-6';
        $label = 'block text-sm font-semibold text-gray-700 mb-2';
        $input = 'w-full px-4 py-3 rounded-xl border-2 border-gray-200 text-gray-700 focus:border-black focus:ring-4 focus:ring-black/10 bg-gray-50 focus:bg-white transition';
        $btnBlk = 'px-4 py-2 text-sm rounded-xl bg-black text-white hover:bg-gray-800 disabled:opacity-60 font-semibold shadow-lg hover:shadow-xl transition';
        $btnRed = 'px-4 py-2 text-sm rounded-xl bg-red-600 text-white hover:bg-red-700 disabled:opacity-60 font-semibold shadow-lg hover:shadow-xl transition';
        $btnLite = 'px-4 py-2 text-sm rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 disabled:opacity-60 font-semibold transition';
        $chip = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';
        $mono = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md';
        $ico = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';

        $company = Auth::user()->company->company_name ?? 'Unknown Company';
    ?>

    <main class="px-4 sm:px-6 py-6 space-y-8">
        
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <div class="pointer-events-none absolute inset-0 opacity-10">
                <div class="absolute top-0 -right-4 w-24 h-24 bg-white rounded-full blur-xl"></div>
                <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-lg"></div>
            </div>
            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 6h14M5 18h7" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-lg sm:text-xl font-semibold truncate">Manage Requirements</h2>
                        <p class="text-sm text-white/80">
                            Scope:
                            <span class="font-semibold">Company</span>
                            <span class="opacity-90">— <?php echo e($company); ?></span>
                        </p>
                    </div>
                    <a href="<?php echo e(route('superadmin.manageroom')); ?>" class="<?php echo e($btnLite); ?>">Go to Rooms</a>
                </div>
            </div>
        </div>

        
        <section class="<?php echo e($card); ?>">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between gap-3">
                <h3 class="text-base font-semibold text-gray-900">Add New Requirement</h3>
                <div class="w-72">
                    <input type="text" wire:model.live="req_search" class="<?php echo e($input); ?> h-10" placeholder="Cari requirement…">
                </div>
            </div>

            
            <form class="p-5" wire:submit.prevent="reqStore">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div class="md:col-span-1">
                        <label class="<?php echo e($label); ?>">Scope</label>
                        <input type="text" class="<?php echo e($input); ?>" value="<?php echo e($company); ?>" readonly>
                    </div>
                    <div class="md:col-span-2">
                        <label class="<?php echo e($label); ?>">Requirement Name</label>
                        <input type="text" wire:model.defer="req_name" class="<?php echo e($input); ?>" placeholder="e.g. Projector, Whiteboard">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['req_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
                <div class="pt-5">
                    <button type="submit" wire:loading.attr="disabled" wire:target="reqStore" class="<?php echo e($btnBlk); ?>">
                        <span wire:loading.remove wire:target="reqStore">Save Requirement</span>
                        <span class="inline-flex items-center gap-2" wire:loading wire:target="reqStore">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0A12 12 0 000 12h4z" />
                            </svg>
                            Saving...
                        </span>
                    </button>
                </div>
            </form>

            
            <div class="divide-y divide-gray-200">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $requirements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php $rowNo = (($requirements->firstItem() ?? 1) + $loop->index); ?>
                    <div class="px-5 py-5 hover:bg-gray-50 transition-colors" wire:key="req-<?php echo e($req->requirement_id); ?>">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <div class="flex items-start gap-3 flex-1">
                                <div class="<?php echo e($ico); ?>">R</div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-semibold text-gray-900 text-sm sm:text-base"><?php echo e($req->name); ?></h4>
                                    <div class="flex flex-wrap items-center gap-2 mt-2">
                                        <span class="<?php echo e($chip); ?>"><span class="text-gray-500">Company:</span>
                                            <span class="font-medium text-gray-700"><?php echo e($company); ?></span></span>
                                        <span class="<?php echo e($chip); ?>"><span class="text-gray-500">Created:</span>
                                            <span class="font-medium text-gray-700"><?php echo e($req->created_at?->format('d M Y, H:i')); ?></span></span>
                                        <span class="<?php echo e($chip); ?>"><span class="text-gray-500">Updated:</span>
                                            <span class="font-medium text-gray-700"><?php echo e($req->updated_at?->format('d M Y, H:i')); ?></span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right shrink-0 space-y-2">
                                <div class="<?php echo e($mono); ?>">No. <?php echo e($rowNo); ?></div>
                                <div class="flex flex-wrap gap-2 justify-end pt-1">
                                    <button wire:click="reqOpenEdit(<?php echo e($req->requirement_id); ?>)" class="<?php echo e($btnBlk); ?>"
                                        wire:loading.attr="disabled" wire:target="reqOpenEdit(<?php echo e($req->requirement_id); ?>)"
                                        wire:key="btn-edit-req-<?php echo e($req->requirement_id); ?>">
                                        <span wire:loading.remove wire:target="reqOpenEdit(<?php echo e($req->requirement_id); ?>)">Edit</span>
                                        <span wire:loading wire:target="reqOpenEdit(<?php echo e($req->requirement_id); ?>)">Loading…</span>
                                    </button>

                                    <button wire:click="reqDelete(<?php echo e($req->requirement_id); ?>)"
                                        onclick="return confirm('Soft delete requirement ini?')" class="<?php echo e($btnRed); ?>"
                                        wire:loading.attr="disabled" wire:target="reqDelete(<?php echo e($req->requirement_id); ?>)"
                                        wire:key="btn-del-req-<?php echo e($req->requirement_id); ?>">
                                        <span wire:loading.remove wire:target="reqDelete(<?php echo e($req->requirement_id); ?>)">Delete</span>
                                        <span wire:loading wire:target="reqDelete(<?php echo e($req->requirement_id); ?>)">Deleting…</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="px-5 py-14 text-center text-gray-500 text-sm">
                        No requirements found for <?php echo e($company); ?>.
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            
            <!--[if BLOCK]><![endif]--><?php if($requirements->hasPages()): ?>
                <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-center">
                        <?php echo e($requirements->links()); ?>

                    </div>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </section>

        
        <!--[if BLOCK]><![endif]--><?php if($reqModal): ?>
            <div class="fixed inset-0 z-[60] flex items-center justify-center" role="dialog" aria-modal="true"
                 wire:key="modal-edit-req" wire:keydown.escape.window="reqCloseEdit">
                <button type="button" class="absolute inset-0 bg-black/50" aria-label="Close overlay" wire:click="reqCloseEdit"></button>
                <div class="relative w-full max-w-xl mx-4 <?php echo e($card); ?> focus:outline-none" tabindex="-1">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-gray-900">Edit Requirement</h3>
                        <button class="text-gray-500 hover:text-gray-700" type="button" wire:click="reqCloseEdit" aria-label="Close">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form class="p-5" wire:submit.prevent="reqUpdate">
                        <div class="space-y-5">
                            <div>
                                <label class="<?php echo e($label); ?>">Requirement Name</label>
                                <input type="text" class="<?php echo e($input); ?>" wire:model.defer="req_edit_name" autofocus>
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['req_edit_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                        <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-4">
                            <button type="button" class="<?php echo e($btnLite); ?>" wire:click="reqCloseEdit">Cancel</button>
                            <button type="submit" wire:loading.attr="disabled" wire:target="reqUpdate" class="<?php echo e($btnBlk); ?>">
                                <span wire:loading.remove wire:target="reqUpdate">Save Changes</span>
                                <span class="inline-flex items-center gap-2" wire:loading wire:target="reqUpdate">
                                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0A12 12 0 000 12h4z" />
                                    </svg>
                                    Saving...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </main>
</div>
<?php /**PATH /home/haelahpx/Documents/GitHub/Frontend-KRB/resources/views/livewire/pages/superadmin/managerequirement.blade.php ENDPATH**/ ?>