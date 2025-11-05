<div class="min-h-screen bg-gray-50">
    <?php
        $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
        $label = 'block text-sm font-medium text-gray-700 mb-2';
        $input =
            'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
        $btnBlk =
            'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 transition';
        $chip = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';

        // helper initials
        $initials = function (?string $fullName): string {
            $fullName = trim($fullName ?? '');
            if ($fullName === '') {
                return 'US';
            }
            $parts = preg_split('/\s+/', $fullName);
            $first = strtoupper(mb_substr($parts[0] ?? 'U', 0, 1));
            $last = strtoupper(mb_substr($parts[count($parts) - 1] ?? ($parts[0] ?? 'S'), 0, 1));
            return $first . $last;
        };
    ?>

    <main class="px-4 sm:px-6 py-6 space-y-8">
        
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 002-2v-6H3v6a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-lg sm:text-xl font-semibold">Ticket Details</h2>

                        
                        <?php
                            $ownerName = $t->user->full_name ?? 'Unknown User';
                            $deptName =
                                $t->department->department_name ??
                                ($t->user->department->department_name ??
                                    null ?? // fallback via user kalau ada
                                    '-');
                            $ownerInit = $initials($ownerName);
                        ?>
                        <div class="mt-2 flex flex-wrap items-center gap-2 text-sm">
                            <span
                                class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-white/10 border border-white/20">
                                <span
                                    class="inline-flex h-6 w-6 rounded-full bg-white/20 text-white items-center justify-center text-[11px] font-bold">
                                    <?php echo e($ownerInit); ?>

                                </span>
                                <span class="font-medium"><?php echo e($ownerName); ?></span>
                            </span>
                            <span
                                class="inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-white/10 border border-white/20">
                                <span class="opacity-80">Dept:</span>
                                <span class="font-medium"><?php echo e($deptName); ?></span>
                            </span>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            
            <section class="lg:col-span-2 <?php echo e($card); ?>">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">Ticket Info</h2>
                </div>
                <div class="p-5 space-y-4">
                    
                    <div>
                        <label class="<?php echo e($label); ?>">Subject</label>
                        <div
                            class="w-full h-10 flex items-center px-3 rounded-lg border border-gray-200 bg-gray-50 text-gray-800">
                            <?php echo e($t->subject); ?>

                        </div>
                    </div>

                    
                    <div>
                        <label class="<?php echo e($label); ?>">Description</label>
                        <div class="p-3 rounded-lg border border-gray-200 bg-gray-50 text-gray-800 whitespace-pre-line">
                            <?php echo e($t->description); ?>

                        </div>
                    </div>

                    
                    <div>
                        <label class="<?php echo e($label); ?>">Priority</label>
                        <div class="px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-gray-800 capitalize">
                            <?php echo e($t->priority); ?>

                        </div>
                    </div>

                    
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="<?php echo e($label); ?>">Status</label>
                            <select wire:model.defer="status" class="<?php echo e($input); ?>">
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="resolved">Resolved</option>
                                <option value="closed">Closed</option>
                                <option value="deleted">Deleted</option>
                            </select>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        <div>
                            <label class="<?php echo e($label); ?>">Assigned Agent</label>
                            <select wire:model.defer="agent_id" class="<?php echo e($input); ?>">
                                <option value="">— Unassigned —</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($a->user_id); ?>"><?php echo e($a->full_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['agent_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-xs text-rose-600 mt-1"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>

                    <div class="pt-2">
                        <button wire:click="save" class="<?php echo e($btnBlk); ?>" wire:loading.attr="disabled">Save
                            Changes</button>
                    </div>
                </div>
            </section>

            
            <aside class="<?php echo e($card); ?>">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-gray-900">Attachments</h3>
                </div>
                <div class="p-5 space-y-3 text-sm">
                    <?php
                        $okImg = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp'];
                        $atts = collect($t->attachments ?? [])
                            ->map(function ($a) {
                                $url = (string) ($a->file_url ?? '');
                                $name = (string) ($a->original_filename ?? '');
                                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                                return (object) [
                                    'url' => $url,
                                    'name' => $name ?: basename($url) ?: 'attachment',
                                    'bytes' => $a->bytes ?? null,
                                    'ext' => $ext,
                                ];
                            })
                            ->filter(fn($x) => $x->url);
                    ?>

                    <!--[if BLOCK]><![endif]--><?php if($atts->isEmpty()): ?>
                        <div class="text-gray-500">No attachments.</div>
                    <?php else: ?>
                        
                        <?php $images = $atts->filter(fn($x)=>in_array($x->ext,$okImg,true))->values(); ?>
                        <!--[if BLOCK]><![endif]--><?php if($images->isNotEmpty()): ?>
                            <div class="grid grid-cols-3 gap-2 mb-3">
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <button wire:click="openPreview('<?php echo e($img->url); ?>')"
                                        class="block focus:outline-none">
                                        <div
                                            class="relative aspect-square overflow-hidden rounded-lg border border-gray-200 bg-white hover:scale-105 transition">
                                            <img src="<?php echo e($img->url); ?>" alt="<?php echo e($img->name); ?>"
                                                class="absolute inset-0 w-full h-full object-cover" />
                                        </div>
                                    </button>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        
                        <?php $others = $atts->reject(fn($x)=>in_array($x->ext,$okImg,true))->values(); ?>
                        <!--[if BLOCK]><![endif]--><?php if($others->isNotEmpty()): ?>
                            <div class="space-y-2">
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $others; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div
                                        class="flex items-center gap-3 p-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 transition">
                                        <div
                                            class="w-8 h-8 rounded-md bg-gray-900 text-white flex items-center justify-center shrink-0 text-[10px] font-bold">
                                            <?php echo e(strtoupper($f->ext ?: 'FILE')); ?>

                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="truncate text-sm font-medium text-gray-900"
                                                title="<?php echo e($f->name); ?>"><?php echo e($f->name); ?></div>
                                        </div>
                                        <div class="shrink-0 flex items-center gap-2">
                                            <a href="<?php echo e($f->url); ?>" target="_blank"
                                                class="px-2.5 py-1.5 text-xs rounded-lg border border-gray-300 hover:bg-gray-100 transition">Open</a>
                                            <a href="<?php echo e($f->url); ?>" download
                                                class="px-2.5 py-1.5 text-xs rounded-lg bg-gray-900 text-white hover:bg-black transition">Download</a>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </aside>
        </div>

        
        <section class="<?php echo e($card); ?>">
            <div class="px-5 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Discussion 💬</h3>
            </div>

            <div class="p-5 space-y-6">
                
                <form wire:submit.prevent="addComment" class="mb-2">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <?php $meInitials = $initials(auth()->user()->full_name ?? auth()->user()->name ?? 'User'); ?>
                            <span
                                class="inline-flex h-10 w-10 rounded-full bg-gray-900 text-white items-center justify-center text-xs font-bold">
                                <?php echo e($meInitials); ?>

                            </span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <textarea wire:model.defer="newComment" rows="3"
                                class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 transition"
                                placeholder="Tulis komentar..."></textarea>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['newComment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->

                            <div class="mt-3 flex items-center justify-end">
                                <button type="submit" class="<?php echo e($btnBlk); ?>">Post Comment</button>
                            </div>
                        </div>
                    </div>
                </form>

                
                <div class="space-y-5">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $t->comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $isMine = $comment->user_id === auth()->id();
                            $name = $comment->user->full_name ?? ($comment->user->name ?? 'User');
                            $init = $initials($name);
                        ?>

                        <div class="flex <?php echo e($isMine ? 'flex-row' : 'flex-row-reverse'); ?> items-start gap-3">
                            
                            <div class="flex-shrink-0">
                                <span
                                    class="inline-flex h-9 w-9 rounded-full
                                        <?php echo e($isMine ? 'bg-gray-900 text-white' : 'bg-gray-200 text-gray-800'); ?>

                                        items-center justify-center text-[11px] font-bold">
                                    <?php echo e($init); ?>

                                </span>
                            </div>

                            
                            <div class="max-w-[80%]">
                                <div
                                    class="flex items-center <?php echo e($isMine ? 'justify-between' : 'flex-row-reverse justify-between'); ?> gap-3">
                                    <p class="text-xs font-semibold text-gray-700 truncate"><?php echo e($name); ?></p>
                                    <p class="text-[11px] text-gray-500"
                                        title="<?php echo e($comment->created_at->format('Y-m-d H:i')); ?>">
                                        <?php echo e($comment->created_at->diffForHumans()); ?>

                                    </p>
                                </div>

                                <div
                                    class="mt-1 rounded-xl
                                            <?php echo e($isMine ? 'bg-gray-900 text-white border border-gray-900' : 'bg-gray-50 text-gray-900 border border-gray-200'); ?>

                                            px-4 py-3 shadow-sm">
                                    <p class="text-sm whitespace-pre-wrap leading-relaxed">
                                        <?php echo e($comment->comment_text); ?>

                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="rounded-lg border border-dashed border-gray-300 p-8 text-center text-gray-600">
                            Belum ada komentar.
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        </section>

        
        <!--[if BLOCK]><![endif]--><?php if($previewUrl): ?>
            <div class="fixed inset-0 bg-black/80 flex items-center justify-center z-50">
                <div class="relative">
                    <img src="<?php echo e($previewUrl); ?>" class="max-h-[90vh] max-w-[90vw] rounded-lg shadow-lg" />
                    <button wire:click="closePreview"
                        class="absolute top-2 right-2 bg-white/80 hover:bg-white text-gray-900 px-2 py-1 rounded-full">✕</button>
                </div>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </main>
</div>
<?php /**PATH /home/haelahpx/Documents/GitHub/Frontend-KRB/resources/views/livewire/pages/admin/ticketshow.blade.php ENDPATH**/ ?>