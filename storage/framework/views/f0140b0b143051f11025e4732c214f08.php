<div class="bg-gray-50">
    <?php
        $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
        $label = 'block text-sm font-semibold text-gray-700 mb-2';
        $input = 'w-full px-4 py-3 rounded-xl border-2 border-gray-200 text-gray-700 focus:border-black focus:ring-4 focus:ring-black/10 bg-gray-50 focus:bg-white transition';
        $btnBlk = 'px-4 py-2 text-sm rounded-xl bg-black text-white hover:bg-gray-800 disabled:opacity-60 font-semibold shadow-lg hover:shadow-xl transition';
        $btnRed = 'px-4 py-2 text-sm rounded-xl bg-red-600 text-white hover:bg-red-700 disabled:opacity-60 font-semibold shadow-lg hover:shadow-xl transition';
        $btnLite = 'px-4 py-2 text-sm rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 disabled:opacity-60 font-semibold transition';
        $chip = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';
        $ico = 'w-10 h-10 bg-gray-900 rounded-xl flex items-center justify-center text-white font-semibold text-sm shrink-0';
    ?>

    <main class="px-4 sm:px-6 py-6 space-y-8">
        
        <div
            class="rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white p-6 sm:p-8 shadow-2xl relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        
                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-user-group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-6 h-6 text-white']); ?>
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
                    </div>
                    <div class="flex-1">
                        <h2 class="text-lg sm:text-xl font-semibold">Guestbook Management</h2>
                        <p class="text-sm text-white/80">
                            Company: <?php echo e(optional(Auth::user()->company)->company_name ?? '-'); ?>

                        </p>
                    </div>
                </div>
            </div>
        </div>

        
        <section class="<?php echo e($card); ?>">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between gap-3">
                <h3 class="text-base font-semibold text-gray-900">Add New Guest</h3>
                <div class="w-full sm:w-72 flex items-center gap-3">
                    <input type="text" wire:model.live="search" class="<?php echo e($input); ?> h-10"
                        placeholder="Search name / purpose / phone…">
                </div>
            </div>

            
            <form class="p-5" wire:submit.prevent="create">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="<?php echo e($label); ?>">Date</label>
                        <input type="date" wire:model.defer="date" class="<?php echo e($input); ?>">
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
                        <label class="<?php echo e($label); ?>">Check-in Time</label>
                        <input type="time" wire:model.defer="jam_in" class="<?php echo e($input); ?>">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['jam_in'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div>
                        <label class="<?php echo e($label); ?>">Check-out Time</label>
                        <input type="time" wire:model.defer="jam_out" class="<?php echo e($input); ?>">
                    </div>

                    <div>
                        <label class="<?php echo e($label); ?>">Name</label>
                        <input type="text" wire:model.defer="name" class="<?php echo e($input); ?>" placeholder="Visitor name">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div>
                        <label class="<?php echo e($label); ?>">Phone Number</label>
                        <input type="text" wire:model.defer="phone_number" class="<?php echo e($input); ?>"
                            placeholder="08xxxxxxxxxx">
                    </div>
                    <div>
                        <label class="<?php echo e($label); ?>">Institution</label>
                        <input type="text" wire:model.defer="instansi" class="<?php echo e($input); ?>">
                    </div>
                    <div class="md:col-span-2">
                        <label class="<?php echo e($label); ?>">Purpose</label>
                        <input type="text" wire:model.defer="keperluan" class="<?php echo e($input); ?>">
                    </div>
                    <div>
                        <label class="<?php echo e($label); ?>">Security Officer</label>
                        <input type="text" wire:model.defer="petugas_penjaga" class="<?php echo e($input); ?>">
                    </div>
                </div>

                <div class="pt-5">
                    <button type="submit" class="<?php echo e($btnBlk); ?>">Save</button>
                </div>
            </form>

            
            <div class="divide-y divide-gray-200">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="px-5 py-5 hover:bg-gray-50 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <div class="flex items-start gap-3 flex-1">
                                <div class="<?php echo e($ico); ?>">
                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-user'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 text-white']); ?>
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
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="font-semibold text-gray-900"><?php echo e($row->name); ?></h4>
                                        <span class="<?php echo e($chip); ?>">Date:
                                            <?php echo e(\Illuminate\Support\Carbon::parse($row->date)->format('Y-m-d')); ?></span>
                                        <span class="<?php echo e($chip); ?>">In:
                                            <?php echo e($row->jam_in ? \Illuminate\Support\Str::of($row->jam_in)->substr(0, 5) : '-'); ?></span>
                                        <span class="<?php echo e($chip); ?>">Out:
                                            <?php echo e($row->jam_out ? \Illuminate\Support\Str::of($row->jam_out)->substr(0, 5) : '-'); ?></span>
                                        <!--[if BLOCK]><![endif]--><?php if($row->deleted_at): ?>
                                            <span class="<?php echo e($chip); ?>"><span
                                                    class="w-2 h-2 bg-rose-500 rounded-full"></span>Trashed</span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    <div class="flex flex-wrap gap-2 mt-2 text-sm text-gray-700">
                                        <span class="<?php echo e($chip); ?>">Institution: <?php echo e($row->instansi ?: '-'); ?></span>
                                        <span class="<?php echo e($chip); ?>">Purpose: <?php echo e($row->keperluan ?: '-'); ?></span>
                                        <span class="<?php echo e($chip); ?>">Phone: <?php echo e($row->phone_number ?: '-'); ?></span>
                                        <span class="<?php echo e($chip); ?>">Officer: <?php echo e($row->petugas_penjaga ?: '-'); ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right shrink-0 space-y-2">
                                <div class="flex flex-wrap gap-2 justify-end">
                                    <!--[if BLOCK]><![endif]--><?php if(!$row->deleted_at): ?>
                                        <button class="<?php echo e($btnLite); ?>"
                                            wire:click="openEdit(<?php echo e($row->guestbook_id); ?>)">Edit</button>
                                        <button class="<?php echo e($btnRed); ?>" wire:click="delete(<?php echo e($row->guestbook_id); ?>)"
                                            onclick="return confirm('Move to trash?')">Delete</button>
                                    <?php else: ?>
                                        <button class="<?php echo e($btnLite); ?>"
                                            wire:click="restore(<?php echo e($row->guestbook_id); ?>)">Restore</button>
                                        <button class="<?php echo e($btnRed); ?>" wire:click="forceDelete(<?php echo e($row->guestbook_id); ?>)"
                                            onclick="return confirm('Delete permanently?')">Delete Permanently</button>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="px-5 py-10 text-center text-gray-500">No guest entries found.</div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <!--[if BLOCK]><![endif]--><?php if($rows->hasPages()): ?>
                <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-center">
                        <?php echo e($rows->links()); ?>

                    </div>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </section>

        
        <!--[if BLOCK]><![endif]--><?php if($modalEdit): ?>
            <div class="fixed inset-0 z-50 flex items-center justify-center" role="dialog" aria-modal="true">
                <button type="button" class="absolute inset-0 bg-black/50" wire:click="$set('modalEdit', false)"></button>
                <div class="relative w-full max-w-xl mx-4 <?php echo e($card); ?>">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-gray-900">Edit Guest</h3>
                        <button type="button" wire:click="$set('modalEdit', false)">
                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-x-mark'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 text-gray-600']); ?>
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
                        </button>
                    </div>
                    <form class="p-5 space-y-5" wire:submit.prevent="update">
                        <div>
                            <label class="<?php echo e($label); ?>">Date</label>
                            <input type="date" wire:model.defer="edit_date" class="<?php echo e($input); ?>">
                        </div>
                        <div>
                            <label class="<?php echo e($label); ?>">Check-in Time</label>
                            <input type="time" wire:model.defer="edit_jam_in" class="<?php echo e($input); ?>">
                        </div>
                        <div>
                            <label class="<?php echo e($label); ?>">Check-out Time</label>
                            <input type="time" wire:model.defer="edit_jam_out" class="<?php echo e($input); ?>">
                        </div>
                        <div>
                            <label class="<?php echo e($label); ?>">Name</label>
                            <input type="text" wire:model.defer="edit_name" class="<?php echo e($input); ?>">
                        </div>
                        <div>
                            <label class="<?php echo e($label); ?>">Phone Number</label>
                            <input type="text" wire:model.defer="edit_phone_number" class="<?php echo e($input); ?>">
                        </div>
                        <div>
                            <label class="<?php echo e($label); ?>">Institution</label>
                            <input type="text" wire:model.defer="edit_instansi" class="<?php echo e($input); ?>">
                        </div>
                        <div>
                            <label class="<?php echo e($label); ?>">Purpose</label>
                            <input type="text" wire:model.defer="edit_keperluan" class="<?php echo e($input); ?>">
                        </div>
                        <div>
                            <label class="<?php echo e($label); ?>">Security Officer</label>
                            <input type="text" wire:model.defer="edit_petugas_penjaga" class="<?php echo e($input); ?>">
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-4">
                            <button type="button" class="<?php echo e($btnLite); ?>"
                                wire:click="$set('modalEdit', false)">Cancel</button>
                            <button type="submit" class="<?php echo e($btnBlk); ?>">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </main>
</div><?php /**PATH /home/haelahpx/Documents/GitHub/Frontend-KRB/resources/views/livewire/pages/superadmin/guestbookmanagement.blade.php ENDPATH**/ ?>