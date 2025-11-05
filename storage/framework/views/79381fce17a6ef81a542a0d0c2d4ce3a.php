<div class="bg-gray-50">
    <?php
    $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
    $label = 'block text-sm font-medium text-gray-700 mb-2';
    $input = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
    $btnBlk= 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
    $btnRed= 'px-3 py-2 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition';
    $btnLt = 'px-3 py-2 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-900/10 disabled:opacity-60 transition';
    $chip = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-[11px]';
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
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 002-2v-6H3v6a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg sm:text-xl font-semibold">Booking Room</h2>
                        <p class="text-sm text-white/80">Scope: <span class="font-semibold">Company</span></p>
                    </div>

                    <div class="ml-auto">
                        <a href="<?php echo e(route('superadmin.manageroom')); ?>" class="<?php echo e($btnLt); ?>">Go to Rooms</a>
                    </div>
                </div>
            </div>
        </div>

        <!--[if BLOCK]><![endif]--><?php if(session()->has('success')): ?>
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl px-4 py-3 text-sm text-gray-800">
            <?php echo e(session('success')); ?>

        </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        
        <div class="<?php echo e($card); ?>">
            <div class="px-5 py-4 border-b border-gray-200">
                <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                    <div class="relative flex-1">
                        <input type="text" wire:model.live="search" placeholder="Search title or notes..."
                            class="<?php echo e($input); ?> pl-10 w-full placeholder:text-gray-400">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.3-4.3M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                        </svg>
                    </div>

                    <div class="relative">
                        <select wire:model.live="departmentFilter" class="<?php echo e($input); ?> pl-10 w-full lg:w-60">
                            <option value="">All Departments</option>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $deptLookup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($id); ?>"><?php echo e($name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </select>
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.284-1.255-.778-1.664M6 18H2v-2a3 3 0 015.356-1.857M14 5a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>

                    <div class="relative">
                        <select wire:model.live="perPage" class="<?php echo e($input); ?> pl-10 w-full lg:w-40">
                            <option value="10">10 / page</option>
                            <option value="20">20 / page</option>
                            <option value="50">50 / page</option>
                        </select>
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h10M4 17h16" />
                        </svg>
                    </div>
                </div>
            </div>

            
            <div class="divide-y divide-gray-200">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $rowNo = (($bookings->firstItem() ?? 1) + $loop->index);
                ?>
                <div class="px-5 py-5 hover:bg-gray-50 transition-colors" wire:key="br-<?php echo e($b->bookingroom_id); ?>">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                        <div class="flex items-start gap-3 flex-1">
                            <div class="<?php echo e($ico); ?>">
                                <?php echo e(strtoupper(substr($b->meeting_title,0,1))); ?>

                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-semibold text-gray-900 text-sm sm:text-base truncate">
                                        <?php echo e($b->meeting_title); ?>

                                    </h4>
                                </div>

                                <p class="text-[12px] text-gray-500">
                                    By: <?php echo e($b->user_full_name ?: '—'); ?>

                                </p>

                                <div class="flex flex-wrap items-center gap-2 mt-2">
                                    <span class="<?php echo e($chip); ?>"><span class="text-gray-500">Room:</span><span class="font-medium text-gray-700"><?php echo e($roomLookup[$b->room_id] ?? '—'); ?></span></span>
                                    <span class="<?php echo e($chip); ?>"><span class="text-gray-500">Dept:</span><span class="font-medium text-gray-700"><?php echo e($deptLookup[$b->department_id] ?? '—'); ?></span></span>
                                    <span class="<?php echo e($chip); ?>"><span class="text-gray-500">Date:</span><span class="font-medium text-gray-700"><?php echo e(\Illuminate\Support\Carbon::parse($b->date)->format('d M Y')); ?></span></span>
                                    <span class="<?php echo e($chip); ?>"><span class="text-gray-500">Time:</span><span class="font-medium text-gray-700"><?php echo e(\Illuminate\Support\Carbon::parse($b->start_time)->format('H:i')); ?>–<?php echo e(\Illuminate\Support\Carbon::parse($b->end_time)->format('H:i')); ?></span></span>
                                    <span class="<?php echo e($chip); ?>"><span class="text-gray-500">Att:</span><span class="font-medium text-gray-700"><?php echo e($b->number_of_attendees); ?></span></span>
                                </div>

                                
                                <?php $reqs = $requirementsMap[$b->bookingroom_id] ?? []; ?>
                                <!--[if BLOCK]><![endif]--><?php if(count($reqs)): ?>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $reqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rname): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="<?php echo e($chip); ?> bg-gray-200">
                                        <span class="text-gray-600">Req:</span>
                                        <span class="font-medium text-gray-700"><?php echo e($rname); ?></span>
                                    </span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>

                        <div class="text-right shrink-0 space-y-2">
                            <div class="<?php echo e($mono); ?>">No. <?php echo e($rowNo); ?></div>
                            <div class="flex flex-wrap gap-2 justify-end pt-1">
                                <button class="<?php echo e($btnBlk); ?>"
                                    wire:click="openEdit(<?php echo e($b->bookingroom_id); ?>)"
                                    wire:loading.attr="disabled"
                                    wire:target="openEdit(<?php echo e($b->bookingroom_id); ?>)">
                                    <span wire:loading.remove wire:target="openEdit(<?php echo e($b->bookingroom_id); ?>)">Edit</span>
                                    <span wire:loading wire:target="openEdit(<?php echo e($b->bookingroom_id); ?>)">Loading…</span>
                                </button>
                                <button class="<?php echo e($btnRed); ?>"
                                    wire:click="delete(<?php echo e($b->bookingroom_id); ?>)"
                                    onclick="return confirm('Delete this booking?')"
                                    wire:loading.attr="disabled"
                                    wire:target="delete(<?php echo e($b->bookingroom_id); ?>)">
                                    <span wire:loading.remove wire:target="delete(<?php echo e($b->bookingroom_id); ?>)">Delete</span>
                                    <span wire:loading wire:target="delete(<?php echo e($b->bookingroom_id); ?>)">Deleting…</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="px-5 py-14 text-center text-gray-500 text-sm">No bookings found.</div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <!--[if BLOCK]><![endif]--><?php if($bookings->hasPages()): ?>
            <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex justify-center">
                    <?php echo e($bookings->withQueryString()->links()); ?>

                </div>
            </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        
        <!--[if BLOCK]><![endif]--><?php if($modal): ?>
        <div class="fixed inset-0 z-[60] flex items-center justify-center" role="dialog" aria-modal="true" wire:key="modal-br" wire:keydown.escape.window="closeModal">
            <button type="button" class="absolute inset-0 bg-black/50" aria-label="Close overlay" wire:click="closeModal"></button>

            <div class="relative w-full max-w-2xl mx-4 <?php echo e($card); ?> focus:outline-none" tabindex="-1">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900">Edit Booking</h3>
                    <button class="text-gray-500 hover:text-gray-700" type="button" wire:click="closeModal" aria-label="Close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form class="p-5" wire:submit.prevent="update">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="<?php echo e($label); ?>">Room</label>
                            <select class="<?php echo e($input); ?>" wire:model.defer="room_id">
                                <option value="">Choose room</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $roomLookup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rid => $rname): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($rid); ?>"><?php echo e($rname); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['room_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div>
                            <label class="<?php echo e($label); ?>">Department</label>
                            <select class="<?php echo e($input); ?>" wire:model.defer="department_id">
                                <option value="">Choose department</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $deptLookup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $did => $dname): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($did); ?>"><?php echo e($dname); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['department_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div class="md:col-span-2">
                            <label class="<?php echo e($label); ?>">Meeting Title</label>
                            <input type="text" class="<?php echo e($input); ?>" wire:model.defer="meeting_title" placeholder="Weekly Sync / Project Kickoff">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['meeting_title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div>
                            <label class="<?php echo e($label); ?>">Date</label>
                            <input type="date" class="<?php echo e($input); ?>" wire:model.defer="date">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div>
                            <label class="<?php echo e($label); ?>">Attendees</label>
                            <input type="number" min="1" class="<?php echo e($input); ?>" wire:model.defer="number_of_attendees" placeholder="e.g. 10">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['number_of_attendees'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div>
                            <label class="<?php echo e($label); ?>">Start Time</label>
                            <input type="datetime-local" class="<?php echo e($input); ?>" wire:model.defer="start_time">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['start_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div>
                            <label class="<?php echo e($label); ?>">End Time</label>
                            <input type="datetime-local" class="<?php echo e($input); ?>" wire:model.defer="end_time">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['end_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div class="md:col-span-2">
                            <label class="<?php echo e($label); ?>">Special Notes (optional)</label>
                            <textarea class="<?php echo e($input); ?> h-24" wire:model.defer="special_notes" placeholder="Agenda, equipment needs, etc."></textarea>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['special_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        
                        <div class="md:col-span-2">
                            <label class="<?php echo e($label); ?>">Requirements</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-1">
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $allRequirements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-center space-x-2 text-sm text-gray-700">
                                    <input type="checkbox"
                                        wire:model.defer="selectedRequirements"
                                        value="<?php echo e($req->requirement_id); ?>"
                                        class="rounded border-gray-300 text-gray-900 focus:ring-gray-900/30">
                                    <span><?php echo e($req->name); ?></span>
                                </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['selectedRequirements'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-4">
                        <button type="button" class="<?php echo e($btnLt); ?>" wire:click="closeModal">Cancel</button>
                        <button type="submit" class="<?php echo e($btnBlk); ?>" wire:loading.attr="disabled" wire:target="update">
                            <span wire:loading.remove wire:target="update">Update</span>
                            <span class="inline-flex items-center gap-2" wire:loading wire:target="update">
                                <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0A12 12 0 000 12h4z" />
                                </svg>
                                Processing…
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </main>
</div><?php /**PATH /home/haelahpx/Documents/GitHub/Frontend-KRB/resources/views/livewire/pages/superadmin/bookingroom.blade.php ENDPATH**/ ?>