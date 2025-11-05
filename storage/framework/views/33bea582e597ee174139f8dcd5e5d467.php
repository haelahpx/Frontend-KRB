<div>
    <!--[if BLOCK]><![endif]--><?php if($show): ?>
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/40" wire:click="close"></div>

            <div class="relative z-10 w-full max-w-xl mx-4 bg-white rounded-xl border-2 border-black shadow-lg"
                wire:keydown.escape="close">
                <div class="border-b-2 border-black p-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Quick Book</h3>
                    <button class="px-2 py-1 text-gray-600 hover:text-gray-900" wire:click="close">✕</button>
                </div>

                <div class="p-4 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Room</label>
                            <input type="text" value="<?php echo e($roomName ?? ''); ?>" disabled
                                class="w-full px-3 py-2 bg-gray-100 text-gray-900 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Date</label>
                            <input type="date" wire:model="date"
                                class="w-full px-3 py-2 text-gray-900 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Start Time</label>
                            <input type="time" wire:model="start_time" min="<?php echo e($minStart); ?>"
                                class="w-full px-3 py-2 text-gray-900 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['start_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">End Time</label>
                            <input type="time" wire:model="end_time" min="<?php echo e($start_time ?: $minStart); ?>"
                                class="w-full px-3 py-2 text-gray-900 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['end_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Meeting Title</label>
                            <input type="text" wire:model="meeting_title" placeholder="Enter meeting title"
                                class="w-full px-3 py-2 text-gray-900 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['meeting_title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Attendees</label>
                            <input type="number" wire:model="number_of_attendees" min="1" placeholder="0"
                                class="w-full px-3 py-2 text-gray-900 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['number_of_attendees'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">Additional Requirements</label>
                        <div class="grid grid-cols-2 gap-3">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = ['projector', 'whiteboard', 'video_conference', 'catering', 'other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" wire:model.live="requirements" value="<?php echo e($req); ?>"
                                        class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                                    <span class="text-sm text-gray-900"><?php echo e(ucwords(str_replace('_', ' ', $req))); ?></span>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>

                    <!--[if BLOCK]><![endif]--><?php if(in_array('other', $requirements ?? [], true)): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Special Notes</label>
                            <textarea wire:model.defer="special_notes" rows="3"
                                placeholder="Please specify your other requirement…"
                                class="w-full px-3 py-2 text-gray-900 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 resize-none"></textarea>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['special_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <div class="border-t border-gray-200 px-4 py-3 flex items-center justify-end gap-2">
                    <button wire:click="close"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button wire:click="submit" class="px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800">
                        Confirm Booking
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div><?php /**PATH /home/haelahpx/Documents/GitHub/Frontend-KRB/resources/views/livewire/booking/quick-book-modal.blade.php ENDPATH**/ ?>