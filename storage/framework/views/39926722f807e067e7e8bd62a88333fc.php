<div>
    <!--[if BLOCK]><![endif]--><?php if($show): ?>
        <div class="fixed inset-0 z-[100] flex items-center justify-center">
            
            <div class="absolute inset-0 bg-black/50" wire:click="close"></div>

            
            <div class="relative z-10 w-full max-w-lg mx-4 bg-white rounded-xl border-2 border-black shadow-xl"
                wire:keydown.escape="close" tabindex="-1">
                <div class="p-5 border-b-2 border-black flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-gray-900">New Ticket</h3>
                    <button class="p-2 rounded hover:bg-gray-100" wire:click="close">✕</button>
                </div>

                <div class="p-5 overflow-y-auto max-h-[80vh] text-black">
                    <form wire:submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Subject</label>
                            <input type="text" wire:model.defer="subject" placeholder="Enter ticket subject"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-black">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Priority</label>
                                <select wire:model.defer="priority"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-black">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['priority'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2">Department</label>
                                <select wire:model.defer="department_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-black">
                                    <option value="">—</option>
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($d['id']); ?>"><?php echo e($d['name']); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </select>
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['department_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Description</label>
                            <textarea rows="5" wire:model.defer="description" placeholder="Describe your issue in detail..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-black"></textarea>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        <div class="pt-2 flex gap-3">
                            <button type="button" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50"
                                wire:click="close">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-black text-white rounded-md hover:bg-gray-800">
                                Submit Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

</div><?php /**PATH /home/adomancer/Documents/GitHub/KRB-System/resources/views/livewire/tickets/quick-ticket-modal.blade.php ENDPATH**/ ?>