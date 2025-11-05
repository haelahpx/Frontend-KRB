
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border-2 border-black p-6 mb-8">
        <div class="flex items-center justify-start gap-3">
            <h1 class="text-3xl font-bold text-gray-900">Support Ticket System</h1>
            <div class="ml-auto inline-flex rounded-md overflow-hidden bg-gray-100 border border-gray-200">
                <button
                    wire:click="$set('tab','queue')"
                    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                        'px-4 py-2 text-sm font-medium transition-colors',
                        'bg-gray-900 text-white' => $tab === 'queue',
                        'text-gray-700 hover:text-gray-900' => $tab !== 'queue',
                    ]); ?>">
                    Ticket Queue
                </button>
                <button
                    wire:click="$set('tab','claims')"
                    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                        'px-4 py-2 text-sm font-medium transition-colors border-l border-gray-200',
                        'bg-gray-900 text-white' => $tab === 'claims',
                        'text-gray-700 hover:text-gray-900' => $tab !== 'claims',
                    ]); ?>">
                    My Claims
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border-2 border-black/80 shadow-md p-4">
        <!--[if BLOCK]><![endif]--><?php if($tab === 'queue'): ?>
            <div class="flex flex-col md:flex-row md:items-center gap-6 pb-4 mb-4 border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 w-full">
                    <input type="text" wire:model.debounce.400ms="search" placeholder="Search subject/description..." class="px-3 py-2 border border-gray-300 rounded-md text-gray-900 w-full">
                    <select wire:model="status" class="px-3 py-2 border border-gray-300 rounded-md text-gray-900">
                        <option value="">All Status</option>
                        <option value="OPEN">OPEN</option>
                        <option value="IN_PROGRESS">IN_PROGRESS</option>
                        <option value="RESOLVED">RESOLVED</option>
                        <option value="CLOSED">CLOSED</option>
                    </select>
                    <select wire:model="priority" class="px-3 py-2 border border-gray-300 rounded-md text-gray-900">
                        <option value="">All Priority</option>
                        <option value="low">low</option>
                        <option value="medium">medium</option>
                        <option value="high">high</option>
                    </select>
                    <div></div>
                </div>
            </div>

            <!--[if BLOCK]><![endif]--><?php if(!$tickets || $tickets->isEmpty()): ?>
                <div class="rounded-lg border-2 border-dashed border-gray-300 p-10 text-center text-gray-600">
                    Tidak ada tiket untuk departemen anda.
                </div>
            <?php else: ?>
                <div class="space-y-5">
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $priority = strtolower($t->priority ?? '');
                            $statusUp = strtoupper($t->status ?? 'OPEN');
                            $statusLabel = ucfirst(strtolower(str_replace('_',' ',$statusUp)));
                            $isHigh = $priority === 'high';
                            $isMedium = $priority === 'medium';
                            $isLow = $priority === 'low' || $priority === '';
                            $priorityBadge = $isHigh ? 'bg-orange-50 text-orange-700 border-2 border-orange-400' : ($isMedium ? 'bg-yellow-50 text-yellow-700 border-2 border-yellow-400' : 'bg-gray-50 text-gray-700 border-2 border-gray-400');
                            $statusBadge = match(true){
                                $statusUp === 'OPEN' => 'bg-yellow-50 text-yellow-700 border-2 border-yellow-500',
                                in_array($statusUp, ['ASSIGNED','IN_PROGRESS']) => 'bg-blue-50 text-blue-700 border-2 border-blue-500',
                                in_array($statusUp, ['RESOLVED','CLOSED']) => 'bg-green-50 text-green-700 border-2 border-green-500',
                                default => 'bg-gray-50 text-gray-700 border-2 border-gray-500'
                            };
                        ?>

                        <div class="relative bg-white rounded-xl border-2 border-black/80 shadow-md p-6 hover:shadow-lg hover:-translate-y-0.5 transition">
                            <div class="flex items-start justify-between gap-4 mb-3">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2 truncate">#<?php echo e($t->ticket_id); ?> — <?php echo e($t->subject); ?></h3>
                                    <div class="flex flex-wrap items-center gap-2 text-xs">
                                        <span class="font-mono font-medium text-gray-800">#<?php echo e($t->ticket_id); ?></span>
                                        <span class="text-gray-300">•</span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium <?php echo e($priorityBadge); ?>"><?php echo e($priority ? ucfirst($priority) : 'Low'); ?></span>
                                        <!--[if BLOCK]><![endif]--><?php if($t->requester): ?>
                                            <span class="text-gray-300">•</span>
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg border-2 border-gray-400 bg-gray-50 text-gray-700">
                                                <span class="font-medium"><?php echo e($t->requester->full_name ?? $t->requester->email); ?></span>
                                            </span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium <?php echo e($statusBadge); ?>"><?php echo e($statusLabel); ?></span>
                            </div>

                            <p class="text-sm text-gray-600 leading-relaxed">
                                <?php echo e(\Illuminate\Support\Str::limit($t->description, 220)); ?>

                            </p>

                            <div class="mt-3 text-[11px] text-gray-500">
                                <span>Dibuat: <?php echo e(\Carbon\Carbon::parse($t->created_at)->diffForHumans()); ?></span>
                                <span class="mx-2">•</span>
                                <span>Updated: <?php echo e(optional($t->updated_at)->format('Y-m-d H:i')); ?></span>
                            </div>

                            <!--[if BLOCK]><![endif]--><?php if($t->attachments->count()): ?>
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="text-xs uppercase tracking-wide text-gray-500 mb-2">Attachments (<?php echo e($t->attachments->count()); ?>)</div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $t->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php $isImage = str_starts_with(strtolower($a->file_type ?? ''), 'image/'); ?>
                                            <div class="group rounded-lg overflow-hidden border border-gray-200 bg-white">
                                                <!--[if BLOCK]><![endif]--><?php if($isImage): ?>
                                                    <a href="<?php echo e($a->file_url); ?>" target="_blank" class="block">
                                                        <img src="<?php echo e($a->file_url); ?>" alt="<?php echo e($a->original_filename ?? 'image'); ?>" class="w-full h-28 object-cover group-hover:opacity-90 transition">
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?php echo e($a->file_url); ?>" target="_blank" class="flex items-center gap-2 p-2 text-xs text-gray-700 hover:bg-gray-50">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a2.625 2.625 0 00-2.625-2.625h-9.75A2.625 2.625 0 004.5 11.625v6.75A2.625 2.625 0 007.125 21h6.75M9 7.5V6.75A2.25 2.25 0 0111.25 4.5h1.5A2.25 2.25 0 0115 6.75V7.5" />
                                                        </svg>
                                                        <span class="line-clamp-1"><?php echo e($a->original_filename ?? 'file'); ?></span>
                                                    </a>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            <div class="mt-4 flex justify-end gap-2">
                                <button
                                    wire:click="claim(<?php echo e($t->ticket_id); ?>)"
                                    wire:loading.attr="disabled"
                                    wire:target="claim"
                                    type="button"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 10.5V6.75A2.25 2.25 0 0014.25 4.5h-6A2.25 2.25 0 006 6.75v10.5A2.25 2.25 0 008.25 19.5h7.5A2.25 2.25 0 0018 17.25V12M12 12l9-9m0 0v6m0-6h-6"></path>
                                    </svg>
                                    <span wire:loading.remove wire:target="claim">Claim</span>
                                    <span wire:loading wire:target="claim">Processing...</span>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <div class="mt-6">
                    <?php echo e($tickets->onEachSide(1)->links()); ?>

                </div>
            <?php endif; ?>>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!--[if BLOCK]><![endif]--><?php if($tab === 'claims'): ?>
            <div class="flex flex-col md:flex-row md:items-center gap-6 pb-4 mb-4 border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 w-full">
                    <select wire:model="claimPriority" class="px-3 py-2 border border-gray-300 rounded-md text-gray-900">
                        <option value="">All Priority</option>
                        <option value="low">low</option>
                        <option value="medium">medium</option>
                        <option value="high">high</option>
                    </select>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>

            <!--[if BLOCK]><![endif]--><?php if(!$claims || $claims->isEmpty()): ?>
                <div class="rounded-lg border-2 border-dashed border-gray-300 p-10 text-center text-gray-600">
                    Belum ada tiket yang Anda claim.
                </div>
            <?php else: ?>
                <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $claims; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asgn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $t = $asgn->ticket;
                            if (!$t) continue;
                            $prio = strtolower($t->priority ?? '');
                            $priorityBorder = [
                                'low' => 'border-green-300 ring-0',
                                'medium' => 'border-yellow-300 ring-0',
                                'high' => 'border-red-500 ring-2 ring-red-300',
                            ][$prio] ?? 'border-gray-200 ring-0';
                            $badgeClass = [
                                'low' => 'bg-green-50 text-green-700 border-2 border-green-400',
                                'medium' => 'bg-yellow-50 text-yellow-700 border-2 border-yellow-400',
                                'high' => 'bg-orange-50 text-orange-700 border-2 border-orange-400',
                            ][$prio] ?? 'bg-gray-50 text-gray-700 border-2 border-gray-400';
                        ?>

                        <a href="<?php echo e(route('user.ticket.show', $t)); ?>" class="block rounded-2xl border bg-white p-5 shadow-sm hover:shadow-md transition <?php echo e($priorityBorder); ?>">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="text-lg font-semibold text-gray-900 line-clamp-1"><?php echo e($t->subject); ?></h3>
                                <span class="text-xs px-2 py-1 rounded-lg <?php echo e($badgeClass); ?>"><?php echo e(strtoupper($t->priority)); ?></span>
                            </div>
                            <p class="mt-2 text-sm text-gray-600">
                                <?php echo e(\Illuminate\Support\Str::limit($t->description, 160)); ?>

                            </p>
                            <div class="mt-3 text-xs text-gray-500 flex items-center justify-between">
                                <span>Claimed <?php echo e(\Carbon\Carbon::parse($asgn->created_at)->diffForHumans()); ?></span>
                                <span class="font-medium text-gray-700"><?php echo e($t->status); ?></span>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <div class="mt-6">
                    <?php echo e($claims->onEachSide(1)->links()); ?>

                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
</div>
<?php /**PATH /home/haelahpx/Documents/GitHub/Frontend-KRB/resources/views/livewire/pages/user/ticketqueue.blade.php ENDPATH**/ ?>