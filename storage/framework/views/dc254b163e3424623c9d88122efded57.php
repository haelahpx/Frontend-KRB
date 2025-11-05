<div class="space-y-6">
    
    <div class="border border-slate-300 rounded-lg bg-white shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 p-4 md:p-5">
            <h1 class="text-xl font-semibold text-slate-900">Support Ticket System</h1>

            <div class="flex rounded-md overflow-hidden border border-slate-300">
                <a href="<?php echo e(route('user.ticket.queue')); ?>"
                   class="<?php if(request()->routeIs('user.ticket.queue')): ?> bg-slate-900 text-white <?php else: ?> bg-white text-slate-700 hover:bg-slate-50 <?php endif; ?>
                          text-sm font-medium px-4 py-2 border-r border-slate-300">
                    Ticket Queue
                </a>

                <a href="<?php echo e(route('user.ticket.claim')); ?>"
                   class="<?php if(request()->routeIs('user.ticket.claim')): ?> bg-slate-900 text-white <?php else: ?> bg-white text-slate-700 hover:bg-slate-50 <?php endif; ?>
                          text-sm font-medium px-4 py-2">
                    My Claims
                </a>
            </div>
        </div>
    </div>

    
    <div class="flex items-center gap-3">
        <label class="text-sm text-slate-600">Priority:</label>
        <select wire:model="priority"
                class="rounded-lg border border-slate-300 bg-white text-slate-900 px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
            <option value="">All</option>
            <option value="low">low</option>
            <option value="medium">medium</option>
            <option value="high">high</option>
        </select>
    </div>

    <!--[if BLOCK]><![endif]--><?php if(!$claims || $claims->isEmpty()): ?>
        <div class="text-center text-slate-500 py-10 border border-dashed border-slate-300 rounded-xl bg-white">
            Belum ada tiket yang Anda claim.
        </div>
    <?php else: ?>
        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $claims; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asgn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $t = $asgn->ticket;
                    if (!$t) continue;

                    $priorityClass = [
                        'low'    => 'border-green-300 ring-0',
                        'medium' => 'border-amber-300 ring-0',
                        'high'   => 'border-red-500 ring-2 ring-red-300',
                    ][$t->priority] ?? 'border-slate-200 ring-0';

                    $badgeClass = [
                        'low'    => 'bg-green-600/10 text-green-700 border-green-200',
                        'medium' => 'bg-amber-600/10 text-amber-700 border-amber-200',
                        'high'   => 'bg-red-600/10 text-red-700 border-red-200',
                    ][$t->priority] ?? 'bg-slate-100 text-slate-700 border-slate-200';

                    // (opsional) short code non-sensitif untuk tampilan, bukan ID
                    $shortCode = strtoupper(substr($t->ulid, 0, 6));
                ?>

                
                <a href="<?php echo e(route('user.ticket.show', $t)); ?>"
                   class="block rounded-2xl border bg-white p-4 shadow-sm hover:shadow-md transition <?php echo e($priorityClass); ?>">
                    <div class="flex items-start justify-between gap-3">
                        
                        <h3 class="text-lg font-semibold text-slate-900 line-clamp-1">
                            
                            <?php echo e($t->subject); ?>

                        </h3>
                        <span class="text-xs px-2 py-1 rounded-full border <?php echo e($badgeClass); ?>">
                            <?php echo e(strtoupper($t->priority)); ?>

                        </span>
                    </div>

                    <p class="mt-2 text-sm text-slate-600">
                        <?php echo e(\Illuminate\Support\Str::limit($t->description, 160)); ?>

                    </p>

                    <div class="mt-3 text-xs text-slate-500 flex items-center justify-between">
                        <span>Claimed <?php echo e(\Carbon\Carbon::parse($asgn->created_at)->diffForHumans()); ?></span>
                        <span class="font-medium text-slate-700"><?php echo e($t->status); ?></span>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        <div class="mt-4">
            <?php echo e($claims->links()); ?>

        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH /home/haelahpx/Documents/GitHub/Frontend-KRB/resources/views/livewire/pages/user/ticketclaim.blade.php ENDPATH**/ ?>