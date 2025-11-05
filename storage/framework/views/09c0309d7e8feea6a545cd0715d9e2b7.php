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
            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center border border-white/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 002-2v-6H3v6a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg sm:text-xl font-semibold">Ticket Support</h2>
                        <p class="text-sm text-white/80">Scope: <span class="font-semibold">Company</span></p>
                    </div>
                    <div class="ml-auto">
                        <a href="#" class="<?php echo e($btnLt); ?>">Go to Agents</a>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="<?php echo e($card); ?>">
            <div class="px-5 py-4 border-b border-gray-200">
                <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                    <div class="relative flex-1">
                        <input type="text" wire:model.live="search" placeholder="Search subject, description or user..."
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
                    </div>

                    <div class="relative">
                        <select wire:model.live="priorityFilter" class="<?php echo e($input); ?> pl-10 w-full lg:w-40">
                            <option value="">All Priorities</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>

                    <div class="relative">
                        <select wire:model.live="perPage" class="<?php echo e($input); ?> pl-10 w-full lg:w-40">
                            <option value="10">10 / page</option>
                            <option value="20">20 / page</option>
                            <option value="50">50 / page</option>
                        </select>
                    </div>
                </div>
            </div>

            
            <div class="divide-y divide-gray-200">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $rowNo = (($tickets->firstItem() ?? 1) + $loop->index);
                ?>
                <div class="px-5 py-5 hover:bg-gray-50 transition-colors" wire:key="ticket-<?php echo e($t->ticket_id); ?>">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                        <div class="flex items-start gap-3 flex-1">
                            <div class="<?php echo e($ico); ?>">
                                <?php echo e(strtoupper(substr($t->subject ?? 'T',0,1))); ?>

                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-semibold text-gray-900 text-sm sm:text-base truncate">
                                        <?php echo e($t->subject); ?>

                                    </h4>

                                    <?php
                                        $p = strtolower($t->priority ?? 'low');
                                        $priorityBadge = match($p) {
                                            'urgent' => 'bg-rose-600 text-white',
                                            'high' => 'bg-amber-500 text-white',
                                            'medium' => 'bg-yellow-100 text-gray-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    ?>
                                    <span class="text-[11px] px-2 py-0.5 rounded-md font-medium <?php echo e($priorityBadge); ?>"><?php echo e(ucfirst($p)); ?></span>
                                </div>

                                <p class="text-[12px] text-gray-500 mt-1">
                                    By: <?php echo e($t->user->full_name ?? '—'); ?>

                                </p>

                                <p class="text-sm text-gray-600 mt-2 line-clamp-2"><?php echo e($t->description); ?></p>

                                
                                <!--[if BLOCK]><![endif]--><?php if($t->attachments && $t->attachments->count()): ?>
                                    <div class="mt-3 flex flex-wrap items-center gap-3">
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $t->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $url = $att->file_url;
                                                $name = $att->original_filename ?? basename($url);
                                                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                                                $isImage = in_array($ext, ['jpg','jpeg','png','gif','webp']);
                                            ?>

                                            <div class="flex items-center gap-2 bg-white p-2 rounded-md border">
                                                <!--[if BLOCK]><![endif]--><?php if($isImage): ?>
                                                    <a href="<?php echo e($url); ?>" target="_blank" rel="noopener noreferrer" class="block">
                                                        <img src="<?php echo e($url); ?>" alt="<?php echo e($name); ?>" class="h-20 w-auto rounded-md object-cover"/>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?php echo e($url); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-xs px-2 py-1 border rounded">
                                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v8l4-4m6 16H6a2 2 0 01-2-2V6a2 2 0 012-2h7"/></svg>
                                                        <?php echo e($name); ?>

                                                    </a>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                                <a href="<?php echo e($url); ?>" download class="text-xs text-gray-500 ml-2">Download</a>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                <div class="flex flex-wrap items-center gap-2 mt-2">
                                    <span class="<?php echo e($chip); ?>"><span class="text-gray-500">Dept:</span><span class="font-medium text-gray-700"><?php echo e($t->department->department_name ?? ($deptLookup[$t->department_id] ?? '—')); ?></span></span>
                                    <span class="<?php echo e($chip); ?>"><span class="text-gray-500">Created:</span><span class="font-medium text-gray-700"><?php echo e(\Illuminate\Support\Carbon::parse($t->created_at)->format('d M Y H:i')); ?></span></span>
                                    <span class="<?php echo e($chip); ?>"><span class="text-gray-500">Status:</span><span class="font-medium text-gray-700"><?php echo e(ucfirst(str_replace('_',' ',$t->status))); ?></span></span>
                                </div>
                            </div>
                        </div>

                        <div class="text-right shrink-0 space-y-2">
                            <div class="<?php echo e($mono); ?>">No. <?php echo e($rowNo); ?></div>
                            <div class="flex flex-wrap gap-2 justify-end pt-1">
                                <button class="<?php echo e($btnBlk); ?>"
                                    wire:click="openEdit(<?php echo e($t->ticket_id); ?>)"
                                    wire:loading.attr="disabled"
                                    wire:target="openEdit(<?php echo e($t->ticket_id); ?>)">
                                    <span wire:loading.remove wire:target="openEdit(<?php echo e($t->ticket_id); ?>)">Edit</span>
                                    <span wire:loading wire:target="openEdit(<?php echo e($t->ticket_id); ?>)">Loading…</span>
                                </button>
                                <button class="<?php echo e($btnRed); ?>"
                                    wire:click="delete(<?php echo e($t->ticket_id); ?>)"
                                    onclick="return confirm('Delete this ticket?')"
                                    wire:loading.attr="disabled"
                                    wire:target="delete(<?php echo e($t->ticket_id); ?>)">
                                    <span wire:loading.remove wire:target="delete(<?php echo e($t->ticket_id); ?>)">Delete</span>
                                    <span wire:loading wire:target="delete(<?php echo e($t->ticket_id); ?>)">Deleting…</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="px-5 py-14 text-center text-gray-500 text-sm">No tickets found.</div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <!--[if BLOCK]><![endif]--><?php if($tickets->hasPages()): ?>
            <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex justify-center">
                    <?php echo e($tickets->withQueryString()->links()); ?>

                </div>
            </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        
        <!--[if BLOCK]><![endif]--><?php if($modal): ?>
        <div class="fixed inset-0 z-[60] flex items-center justify-center" role="dialog" aria-modal="true" wire:key="modal-ticket" wire:keydown.escape.window="closeModal">
            <button type="button" class="absolute inset-0 bg-black/50" aria-label="Close overlay" wire:click="closeModal"></button>

            <div class="relative w-full max-w-2xl mx-4 <?php echo e($card); ?> focus:outline-none" tabindex="-1">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900">Edit Ticket</h3>
                    <button class="text-gray-500 hover:text-gray-700" type="button" wire:click="closeModal" aria-label="Close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form class="p-5" wire:submit.prevent="update">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
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

                        <div>
                            <label class="<?php echo e($label); ?>">Priority</label>
                            <select class="<?php echo e($input); ?>" wire:model.defer="priority">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['priority'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        <div class="md:col-span-2">
                            <label class="<?php echo e($label); ?>">Subject</label>
                            <input type="text" class="<?php echo e($input); ?>" wire:model.defer="subject">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        <div class="md:col-span-2">
                            <label class="<?php echo e($label); ?>">Description</label>
                            <textarea class="<?php echo e($input); ?> h-28" wire:model.defer="description"></textarea>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        <div>
                            <label class="<?php echo e($label); ?>">Status</label>
                            <select class="<?php echo e($input); ?>" wire:model.defer="status">
                                <option value="">-- choose --</option>
                                <option value="OPEN">Open</option>
                                <option value="IN_PROGRESS">In Progress</option>
                                <option value="RESOLVED">Resolved</option>
                                <option value="CLOSED">Closed</option>
                            </select>
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
</div>
<?php /**PATH /home/haelahpx/Documents/GitHub/Frontend-KRB/resources/views/livewire/pages/superadmin/ticketsupport.blade.php ENDPATH**/ ?>