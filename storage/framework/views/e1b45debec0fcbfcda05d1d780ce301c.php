
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border-2 border-black p-6 mb-8">
        <div class="flex items-center justify-start gap-3">
            <h1 class="text-3xl font-bold text-gray-900">Support Ticket System</h1>

            <?php
            $isCreate = request()->routeIs('create-ticket') || request()->is('create-ticket');
            $isStatus = request()->routeIs('ticketstatus') || request()->is('ticketstatus');
            ?>

            
            <div class="ml-auto inline-flex rounded-md overflow-hidden bg-gray-100 border border-gray-200">
                <a href="<?php echo e(route('create-ticket')); ?>"
                   class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                       'px-4 py-2 text-sm font-medium transition-colors inline-flex items-center',
                       'bg-gray-900 text-white' => $isCreate,
                       'text-gray-700 hover:text-gray-900' => !$isCreate,
                   ]); ?>">
                    Create Ticket
                </a>
                <a href="<?php echo e(route('ticketstatus')); ?>"
                   class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                       'px-4 py-2 text-sm font-medium transition-colors border-l border-gray-200 inline-flex items-center',
                       'bg-gray-900 text-white' => $isStatus,
                       'text-gray-700 hover:text-gray-900' => !$isStatus,
                   ]); ?>">
                    Ticket Status
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border-2 border-black/80 shadow-md p-4">
        <div class="flex flex-col md:flex-row md:items-center gap-6 pb-4 mb-4 border-b border-gray-200">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 w-full md:w-auto">
                <select wire:model.live="statusFilter" class="px-3 py-2 border border-gray-300 rounded-md text-gray-900 w-full">
                    <option value="">All Status</option>
                    <option value="open">Open</option>
                    <option value="in_progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                </select>

                <select wire:model.live="priorityFilter" class="px-3 py-2 border border-gray-300 rounded-md text-gray-900 w-full">
                    <option value="">All Priority</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>

                <select wire:model.live="departmentFilter" class="px-3 py-2 border border-gray-300 rounded-md text-gray-900 w-full">
                    <option value="">All Departments</option>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($dept->department_id); ?>"><?php echo e($dept->department_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </select>

                <select wire:model.live="sortFilter" class="px-3 py-2 border border-gray-300 rounded-md text-gray-900 w-full">
                    <option value="recent">Recent first</option>
                    <option value="oldest">Oldest first</option>
                    <option value="due">Nearest due</option>
                </select>
            </div>
        </div>

        
        <div class="space-y-5">
            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $priority = strtolower($t->priority ?? '');
                    $statusUp = strtoupper($t->status ?? 'OPEN');
                    $statusLabel = ucfirst(strtolower(str_replace('_',' ',$statusUp)));
                    $userName = $t->user->full_name ?? $t->user->name ?? 'User';
                    $initial = strtoupper(mb_substr($userName, 0, 1));
                    $isOpen = $statusUp === 'OPEN';
                    $isAssignedOrProgress = in_array($statusUp, ['ASSIGNED','IN_PROGRESS'], true);
                    $isResolvedOrClosed = in_array($statusUp, ['RESOLVED','CLOSED'], true);
                    $hasAgent = (int)($t->agent_count ?? 0) > 0;
                    $agents = collect($t->assignments ?? [])->pluck('user.full_name')->filter()->unique()->values();
                ?>

                <div class="relative bg-white rounded-xl border-2 border-black/80 shadow-md p-6 hover:shadow-lg hover:-translate-y-0.5 transition">
                    
                    <a href="<?php echo e(route('user.ticket.show', $t)); ?>"
                       class="absolute inset-0 z-20"
                       aria-label="Open ticket #<?php echo e($t->ticket_id); ?>"></a>

                    <div class="flex items-start justify-between gap-4 mb-3 relative z-0 pointer-events-none">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 truncate"><?php echo e($t->subject); ?></h3>

                            <div class="flex flex-wrap items-center gap-2 text-xs">
                                <span class="font-mono font-medium text-gray-800 inline-flex items-center gap-1">
                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-hashtag'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-3.5 h-3.5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?> <?php echo e($t->ticket_id); ?>

                                </span>
                                <span class="text-gray-300">•</span>

                                <?php
                                    $isHigh   = $priority === 'high';
                                    $isMedium = $priority === 'medium';
                                    $isLow    = $priority === 'low' || $priority === '';
                                ?>
                                <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                    'inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium',
                                    'bg-orange-50 text-orange-700 border-2 border-orange-400' => $isHigh,
                                    'bg-yellow-50 text-yellow-700 border-2 border-yellow-400' => $isMedium,
                                    'bg-gray-50 text-gray-700 border-2 border-gray-400' => $isLow,
                                ]); ?>">
                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-bolt'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-3.5 h-3.5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                    <?php echo e($priority ? ucfirst($priority) : 'Low'); ?>

                                </span>

                                <!--[if BLOCK]><![endif]--><?php if($t->department): ?>
                                    <span class="text-gray-300">•</span>
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg border-2 border-gray-400 bg-gray-50 text-gray-700">
                                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-building-office-2'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-3.5 h-3.5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                        <span class="font-medium"><?php echo e($t->department->department_name); ?></span>
                                    </span>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                <span class="text-gray-300">•</span>
                                <span class="inline-flex items-center gap-2 px-2 py-1 rounded-lg border-2 border-gray-400 bg-gray-50 text-gray-700">
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full border border-gray-400 text-[10px] leading-none">
                                        <?php echo e($initial); ?>

                                    </span>
                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-user'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-3.5 h-3.5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                    <span class="font-medium"><?php echo e($userName); ?></span>
                                </span>

                                <span class="text-gray-300">•</span>
                                <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                    'inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium',
                                    'border-2',
                                    $hasAgent ? 'bg-emerald-50 text-emerald-700 border-emerald-400' : 'bg-red-50 text-red-700 border-red-400'
                                ]); ?>">
                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-user-circle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-3.5 h-3.5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                    <?php echo e($hasAgent ? 'Agent assigned' : 'No agent yet'); ?>

                                </span>

                                <!--[if BLOCK]><![endif]--><?php if($agents->isNotEmpty()): ?>
                                    <span class="text-gray-300">•</span>
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg border-2 border-blue-400 bg-blue-50 text-blue-700">
                                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-users'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-3.5 h-3.5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                        <span class="font-medium truncate max-w-[220px]">
                                            <?php echo e($agents->join(', ')); ?>

                                        </span>
                                    </span>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>

                        <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                            'inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium',
                            'bg-yellow-50 text-yellow-700 border-2 border-yellow-500' => $isOpen,
                            'bg-blue-50 text-blue-700 border-2 border-blue-500'   => $isAssignedOrProgress,
                            'bg-green-50 text-green-700 border-2 border-green-500'=> $isResolvedOrClosed,
                            'bg-gray-50 text-gray-700 border-2 border-gray-500'   => (!$isOpen && !$isAssignedOrProgress && !$isResolvedOrClosed),
                        ]); ?>">
                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-check-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                            <?php echo e($statusLabel); ?>

                        </span>
                    </div>

                    <p class="text-sm text-gray-600 leading-relaxed relative z-0 pointer-events-none">
                        <?php echo e($t->description); ?>

                    </p>

                    <div class="mt-3 text-[11px] text-gray-500 relative z-0 pointer-events-none inline-flex items-center gap-2">
                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-clock'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-3.5 h-3.5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                        <span>Created: <?php echo e(optional($t->created_at)->format('Y-m-d H:i')); ?></span>
                        <span class="mx-2">•</span>
                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-arrow-path'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-3.5 h-3.5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                        <span>Updated: <?php echo e(optional($t->updated_at)->format('Y-m-d H:i')); ?></span>
                    </div>

                    <!--[if BLOCK]><![endif]--><?php if(in_array($statusUp, ['OPEN','ASSIGNED','IN_PROGRESS','RESOLVED','CLOSED'], true)): ?>
                        <div class="mt-4 flex flex-col sm:flex-row sm:items-center justify-end gap-2 relative z-30 pointer-events-auto">
                            <!--[if BLOCK]><![endif]--><?php if(in_array($statusUp, ['OPEN','ASSIGNED','IN_PROGRESS'], true)): ?>
                                <button
                                    <?php if(!$hasAgent): echo 'disabled'; endif; ?>
                                    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                        'inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2',
                                        $hasAgent
                                            ? 'text-white bg-black hover:bg-gray-800 focus:ring-gray-500'
                                            : 'text-gray-400 bg-gray-200 cursor-not-allowed'
                                    ]); ?>"
                                    <?php if($hasAgent): ?>
                                        wire:click.stop="markComplete(<?php echo e((int) $t->ticket_id); ?>)"
                                    <?php endif; ?>
                                    wire:loading.attr="disabled"
                                    wire:target="markComplete"
                                    type="button"
                                    title="<?php echo e($hasAgent ? 'Mark this ticket as Resolved' : 'Cannot resolve: no agent assigned'); ?>">
                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-check-circle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                    Mark as Resolved
                                </button>
                            <?php elseif($statusUp === 'RESOLVED'): ?>
                                <button
                                    wire:click.stop="markComplete(<?php echo e((int) $t->ticket_id); ?>)"
                                    wire:loading.attr="disabled"
                                    wire:target="markComplete"
                                    type="button"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200"
                                    title="Close this ticket">
                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-lock-closed'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                    Close Ticket
                                </button>
                            <?php elseif($statusUp === 'CLOSED'): ?>
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-200 rounded-lg">
                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-lock-closed'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                    Closed
                                </span>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="rounded-lg border-2 border-dashed border-gray-300 p-10 text-center text-gray-600">
                    No tickets found. Try adjusting filters above.
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        <!--[if BLOCK]><![endif]--><?php if(method_exists($tickets, 'links')): ?>
            <div class="mt-6">
                <?php echo e($tickets->links()); ?>

            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
</div>
<?php /**PATH /home/adomancer/Documents/GitHub/KRB-System/resources/views/livewire/pages/user/ticketstatus.blade.php ENDPATH**/ ?>