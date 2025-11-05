<div class="bg-gray-50">
    
    <?php
        $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
        $label = 'block text-sm font-medium text-gray-700 mb-2';
        $input = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
        $btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
        $btnRed = 'px-3 py-2 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition';
        $chip = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';
        $mono = 'text-[10px] font-mono text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md';
        $ico = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';
    ?>

    <main class="px-4 sm:px-6 py-6 space-y-8">
        
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <div class="pointer-events-none absolute inset-0 opacity-10">
                <div class="absolute top-0 -right-4 w-24 h-24 bg-white rounded-full blur-xl"></div>
                <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-lg"></div>
            </div>
            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.136a1.76 1.76 0 011.164-2.288l5.398-1.592a1.76 1.76 0 012.288 1.164l2.147 6.136a1.76 1.76 0 01-3.417-.592V5.882z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg sm:text-xl font-semibold">Announcement Management</h2>
                        <p class="text-sm text-white/80">
                            Company: <span
                                class="font-semibold"><?php echo e(optional(Auth::user()->company)->company_name ?? '-'); ?></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        
        <section class="<?php echo e($card); ?>">
            <div class="px-5 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Add New Announcement</h3>
            </div>
            <form class="p-5" wire:submit.prevent="store">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div class="md:col-span-1">
                        <label class="<?php echo e($label); ?>">Company</label>
                        <input type="text" class="<?php echo e($input); ?>"
                            value="<?php echo e(optional(Auth::user()->company)->company_name ?? '-'); ?>" readonly>
                    </div>
                    <div class="md:col-span-2">
                        <label class="<?php echo e($label); ?>">Description</label>
                        <input type="text" wire:model.defer="description" class="<?php echo e($input); ?>"
                            placeholder="e.g. Company wide meeting next Monday...">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div class="md:col-span-1">
                        <label class="<?php echo e($label); ?>">Event Date (Optional)</label>
                        <input type="datetime-local" wire:model.defer="event_at" class="<?php echo e($input); ?>">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['event_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
                <div class="pt-5">
                    <button type="submit" wire:loading.attr="disabled" wire:target="store"
                        class="inline-flex items-center gap-2 px-5 h-10 rounded-xl bg-gray-900 text-white text-sm font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900/20 transition active:scale-95 hover:bg-black disabled:opacity-60 relative overflow-hidden">
                        <span class="flex items-center gap-2" wire:loading.remove wire:target="store">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Save Announcement
                        </span>
                        <span class="flex items-center gap-2" wire:loading wire:target="store">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4" />
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0A12 12 0 000 12h4z" />
                            </svg>
                            Saving...
                        </span>
                    </button>
                </div>
            </form>
        </section>

        
        <div class="<?php echo e($card); ?>">
            <div class="px-5 py-4 border-b border-gray-200">
                <div class="relative flex-1">
                    <input type="text" wire:model.live="search" placeholder="Search announcements..."
                        class="<?php echo e($input); ?> pl-10 w-full placeholder:text-gray-400">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m21 21-4.3-4.3M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                    </svg>
                </div>
            </div>
            <div class="divide-y divide-gray-200">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $rowNo = (($announcements->firstItem() ?? 1) + $loop->index);
                    ?>

                    <div class="px-5 py-5 hover:bg-gray-50 transition-colors"
                        wire:key="announcement-<?php echo e($announcement->announcements_id); ?>">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <div class="flex items-start gap-3 flex-1">
                                <div class="<?php echo e($ico); ?>">
                                    <?php echo e(substr(optional($announcement->company)->company_name ?? 'C', 0, 1)); ?>

                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-semibold text-gray-900 text-sm sm:text-base">
                                        <?php echo e($announcement->description); ?>

                                    </h4>
                                    <div class="flex flex-wrap items-center gap-2 mt-2">
                                        <!--[if BLOCK]><![endif]--><?php if($announcement->event_at): ?>
                                            <span class="<?php echo e($chip); ?>">
                                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                                <span
                                                    class="font-medium text-gray-700"><?php echo e($announcement->formatted_event_date); ?></span>
                                            </span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <span class="<?php echo e($chip); ?>">
                                            <span class="text-gray-500">Created:</span>
                                            <span
                                                class="font-medium text-gray-700"><?php echo e($announcement->formatted_created_date); ?></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right shrink-0 space-y-2">
                                <div class="<?php echo e($mono); ?>">No. <?php echo e($rowNo); ?></div>
                                <div class="flex flex-wrap gap-2 justify-end pt-1">
                                    <button wire:click="openEdit(<?php echo e($announcement->announcements_id); ?>)"
                                        class="<?php echo e($btnBlk); ?>" wire:loading.attr="disabled"
                                        wire:target="openEdit(<?php echo e($announcement->announcements_id); ?>)"
                                        wire:key="btn-edit-ann-<?php echo e($announcement->announcements_id); ?>">
                                        <span wire:loading.remove
                                            wire:target="openEdit(<?php echo e($announcement->announcements_id); ?>)">Edit</span>
                                        <span wire:loading
                                            wire:target="openEdit(<?php echo e($announcement->announcements_id); ?>)">Loading…</span>
                                    </button>

                                    <button wire:click="delete(<?php echo e($announcement->announcements_id); ?>)"
                                        onclick="return confirm('Are you sure you want to delete this announcement?')"
                                        class="<?php echo e($btnRed); ?>" wire:loading.attr="disabled"
                                        wire:target="delete(<?php echo e($announcement->announcements_id); ?>)"
                                        wire:key="btn-del-ann-<?php echo e($announcement->announcements_id); ?>">
                                        <span wire:loading.remove
                                            wire:target="delete(<?php echo e($announcement->announcements_id); ?>)">Delete</span>
                                        <span wire:loading
                                            wire:target="delete(<?php echo e($announcement->announcements_id); ?>)">Deleting…</span>
                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="px-5 py-14 text-center text-gray-500 text-sm">No announcements found.</div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
            <!--[if BLOCK]><![endif]--><?php if($announcements->hasPages()): ?>
                <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-center">
                        <?php echo e($announcements->links()); ?>

                    </div>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        
        <!--[if BLOCK]><![endif]--><?php if($modalEdit): ?>
            <div class="fixed inset-0 z-[60] flex items-center justify-center" role="dialog" aria-modal="true"
                wire:key="modal-edit-announcement" wire:keydown.escape.window="closeEdit">
                <button type="button" class="absolute inset-0 bg-black/50" aria-label="Close overlay"
                    wire:click="closeEdit"></button>
                <div class="relative w-full max-w-xl mx-4 <?php echo e($card); ?> focus:outline-none" tabindex="-1">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-gray-900">Edit Announcement</h3>
                        <button class="text-gray-500 hover:text-gray-700" type="button" wire:click="closeEdit"
                            aria-label="Close">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form class="p-5" wire:submit.prevent="update">
                        <div class="space-y-5">
                            <div>
                                <label class="<?php echo e($label); ?>">📝 Description</label>
                                <input type="text" class="<?php echo e($input); ?>" wire:model.defer="edit_description" autofocus>
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['edit_description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600 font-medium"><?php echo e($message); ?>

                                </p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <div>
                                <label class="<?php echo e($label); ?>">🗓️ Event Date (Optional)</label>
                                <input type="datetime-local" class="<?php echo e($input); ?>" wire:model.defer="edit_event_at">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['edit_event_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                        <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-4">
                            <button type="button"
                                class="px-4 h-10 rounded-xl border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-100 hover:border-gray-400 transition"
                                wire:click="closeEdit">Cancel</button>
                            <button type="submit" wire:loading.attr="disabled" wire:target="update"
                                class="inline-flex items-center gap-2 px-5 h-10 rounded-xl bg-gray-900 text-white text-sm font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900/20 transition active:scale-95 hover:bg-black disabled:opacity-60">
                                <span class="flex items-center gap-2" wire:loading.remove wire:target="update">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Save Changes
                                </span>
                                <span class="flex items-center gap-2" wire:loading wire:target="update">
                                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4" />
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0A12 12 0 000 12h4z" />
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
</div><?php /**PATH /home/haelahpx/Documents/GitHub/Frontend-KRB/resources/views/livewire/pages/superadmin/announcement.blade.php ENDPATH**/ ?>