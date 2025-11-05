
<?php
$initials = function (?string $fullName): string {
    $fullName = trim($fullName ?? '');
    if ($fullName === '') return 'US';
    $parts = preg_split('/\s+/', $fullName);
    $first = strtoupper(mb_substr($parts[0] ?? 'U', 0, 1));
    $last = strtoupper(mb_substr($parts[count($parts)-1] ?? $parts[0] ?? 'S', 0, 1));
    return $first.$last;
};

$isCreate = request()->routeIs('create-ticket') || request()->is('create-ticket');
$isStatus = request()->routeIs('ticketstatus') || request()->is('ticketstatus');

$priority = strtolower($ticket->priority ?? '');
$status = strtolower($ticket->status ?? 'open');

$agents = collect($ticket->assignments ?? [])
    ->pluck('user.full_name')
    ->filter()
    ->unique()
    ->values();
$hasAgent = $agents->isNotEmpty();
?>

<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border-2 border-black p-6 mb-8">
        <div class="flex items-center justify-start gap-3">
            <h1 class="text-3xl font-bold text-gray-900">Support Ticket System</h1>

            <div class="ml-auto inline-flex rounded-md overflow-hidden bg-gray-100 border border-gray-200">
                <a href="<?php echo e(route('create-ticket')); ?>"
                    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                        'px-4 py-2 text-sm font-medium transition-colors',
                        'bg-gray-900 text-white' => $isCreate,
                        'text-gray-700 hover:text-gray-900' => ! $isCreate,
                    ]); ?>">
                    Create Ticket
                </a>

                <a href="<?php echo e(route('ticketstatus')); ?>"
                    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                        'px-4 py-2 text-sm font-medium transition-colors border-l border-gray-200',
                        'bg-gray-900 text-white' => true,
                    ]); ?>">
                    Ticket Status
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border-2 border-black/80 shadow-md p-4 space-y-6">
        <div class="relative bg-white rounded-xl border-2 border-black/80 shadow p-6">
            <div class="flex items-start justify-between gap-4 mb-2">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2 break-words"><?php echo e($ticket->subject); ?></h2>

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
<?php endif; ?> <?php echo e($ticket->ticket_id); ?>

                        </span>

                        <span class="text-gray-300">•</span>

                        <?php
                            $isHigh = $priority === 'high';
                            $isMedium = $priority === 'medium';
                            $isLow = $priority === 'low' || $priority === '';
                        ?>
                        <span
                            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
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

                        <!--[if BLOCK]><![endif]--><?php if($ticket->department): ?>
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
                                <span class="font-medium"><?php echo e($ticket->department->department_name); ?></span>
                            </span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        <span class="text-gray-300">•</span>

                        <?php
                            $isOpen = $status === 'open';
                            $isAssignedOrProgress = in_array($status, ['assigned','in_progress','process'], true);
                            $isResolved = in_array($status, ['resolved','closed','complete'], true);
                        ?>
                        <span
                            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                'inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium',
                                'bg-yellow-50 text-yellow-700 border-2 border-yellow-500' => $isOpen,
                                'bg-blue-50 text-blue-700 border-2 border-blue-500' => $isAssignedOrProgress,
                                'bg-green-50 text-green-700 border-2 border-green-500' => $isResolved,
                                'bg-gray-50 text-gray-700 border-2 border-gray-500' => (! $isOpen && ! $isAssignedOrProgress && ! $isResolved),
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
                            <?php echo e(ucfirst(str_replace('_', ' ', $status))); ?>

                        </span>

                        <span class="text-gray-300">•</span>
                        <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                            'inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium border-2',
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
                                <span class="font-medium truncate max-w-[260px]"><?php echo e($agents->join(', ')); ?></span>
                            </span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <!--[if BLOCK]><![endif]--><?php if($canEditStatus ?? false): ?>
                        <div class="mt-3">
                            <form wire:submit.prevent="updateStatus" class="flex items-center gap-2">
                                <select
                                    wire:model="statusEdit"
                                    class="rounded-lg border-2 border-black/40 bg-white text-gray-900 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = ($allowedStatuses ?? ['OPEN','IN_PROGRESS','RESOLVED','CLOSED']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($st); ?>"><?php echo e(str_replace('_',' ', $st)); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </select>

                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-black text-white text-sm font-medium hover:bg-gray-800">
                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-arrow-path'); ?>
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
                                    Update Status
                                </button>
                            </form>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>

            <p class="text-sm text-gray-700 leading-relaxed"><?php echo e($ticket->description); ?></p>

            <div class="mt-3 text-[11px] text-gray-500 inline-flex items-center gap-2">
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
                <span>Created: <?php echo e(optional($ticket->created_at)->format('Y-m-d H:i')); ?></span>
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
                <span>Updated: <?php echo e(optional($ticket->updated_at)->format('Y-m-d H:i')); ?></span>
            </div>

            <!--[if BLOCK]><![endif]--><?php if(method_exists($ticket, 'attachments') && $ticket->relationLoaded('attachments') && $ticket->attachments->count()): ?>
                <div class="mt-5 border-t border-black/10 pt-4">
                    <div class="text-xs uppercase tracking-wide text-gray-600 mb-2">Attachments (<?php echo e($ticket->attachments->count()); ?>)</div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $ticket->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $isImage = str_starts_with(strtolower($a->file_type ?? ''), 'image/'); ?>
                            <div class="rounded-lg overflow-hidden border-2 border-black/20">
                                <!--[if BLOCK]><![endif]--><?php if($isImage): ?>
                                    <a href="<?php echo e($a->file_url); ?>" target="_blank" class="block">
                                        <img src="<?php echo e($a->file_url); ?>" class="w-full h-40 object-cover" alt="<?php echo e($a->original_filename ?? 'image'); ?>">
                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo e($a->file_url); ?>" target="_blank" class="flex items-center gap-2 p-3 text-sm text-gray-800 hover:bg-gray-50">
                                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-document'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5']); ?>
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
                                        <span class="truncate"><?php echo e($a->original_filename ?? 'file'); ?></span>
                                    </a>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        <div class="bg-white rounded-xl border-2 border-black/80 shadow p-6">
            <h3 class="text-xl font-bold mb-4 inline-flex items-center gap-2">
                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-chat-bubble-left-right'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?> Discussion
            </h3>

            <form wire:submit.prevent="addComment" class="mb-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <?php $meInitials = $initials(auth()->user()->full_name ?? auth()->user()->name ?? 'User'); ?>
                        <span class="inline-flex h-10 w-10 rounded-full bg-black text-white items-center justify-center text-xs font-bold">
                            <?php echo e($meInitials); ?>

                        </span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <textarea
                            wire:model.defer="newComment"
                            rows="3"
                            class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 transition"
                            placeholder="Add your comment..."></textarea>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['newComment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-rose-600 text-xs mt-1"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->

                        <div class="mt-3 flex items-center justify-end">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-paper-airplane'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4 rotate-45']); ?>
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
                                Post Comment
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="space-y-5">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $ticket->comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $isMine = $comment->user_id === auth()->id();
                        $name = $comment->user->full_name ?? $comment->user->name ?? 'User';
                        $init = $initials($name);
                    ?>

                    <div class="flex <?php echo e($isMine ? 'flex-row' : 'flex-row-reverse'); ?> items-start gap-3">
                        <div class="flex-shrink-0">
                            <span
                                class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                    'inline-flex h-9 w-9 rounded-full items-center justify-center text-[11px] font-bold',
                                    'bg-black text-white' => $isMine,
                                    'bg-gray-200 text-gray-800' => ! $isMine,
                                ]); ?>">
                                <?php echo e($init); ?>

                            </span>
                        </div>

                        <div class="max-w-[80%]">
                            <div class="flex items-center <?php echo e($isMine ? 'justify-between' : 'flex-row-reverse justify-between'); ?> gap-3">
                                <p class="text-xs font-semibold text-gray-700 truncate"><?php echo e($name); ?></p>
                                <p class="text-[11px] text-gray-500" title="<?php echo e($comment->created_at->format('Y-m-d H:i')); ?>">
                                    <?php echo e($comment->created_at->diffForHumans()); ?>

                                </p>
                            </div>

                            <div
                                class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                    'mt-1 rounded-xl px-4 py-3 shadow-sm',
                                    'bg-black text-white border-2 border-black' => $isMine,
                                    'bg-gray-50 text-gray-900 border-2 border-black/20' => ! $isMine,
                                ]); ?>">
                                <p class="text-sm whitespace-pre-wrap leading-relaxed"><?php echo e($comment->comment_text); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="rounded-lg border-2 border-dashed border-gray-300 p-8 text-center text-gray-600">
                        No comments yet. Be the first to reply!
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <div class="mt-6">
                <a href="<?php echo e(route('ticketstatus')); ?>"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border-2 border-black bg-white hover:bg-gray-50 font-medium">
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-arrow-uturn-left'); ?>
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
                    Back to list
                </a>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /home/haelahpx/Documents/GitHub/Frontend-KRB/resources/views/livewire/pages/user/ticketshow.blade.php ENDPATH**/ ?>