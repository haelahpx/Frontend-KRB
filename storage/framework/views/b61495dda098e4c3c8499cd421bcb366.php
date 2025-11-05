

<div class="bg-gray-50">
    <?php
        $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
        $label = 'block text-sm font-medium text-gray-700 mb-2';
        $input = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
        $btnBlk = 'px-3 py-2 text-xs font-medium rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
        $btnRed = 'px-3 py-2 text-xs font-medium rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition';
        $chip = 'inline-flex items-center gap-2 px-2.5 py-1 rounded-lg bg-gray-100 text-xs';
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
                    <div
                        class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.284-1.255-.778-1.664M6 18H2v-2a3 3 0 015.356-1.857M14 5a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg sm:text-xl font-semibold">User Management</h2>
                        <p class="text-sm text-white/80">
                            Perusahaan: <span class="font-semibold"><?php echo e($company_name); ?></span>
                            <span class="mx-2">•</span>
                            Departemen: <span class="font-semibold"><?php echo e($department_name); ?></span>
                        </p>
                        <p class="text-xs text-white/60 mt-1">
                            Halaman ini <strong>terkunci</strong> untuk menampilkan & mengelola user pada departemen
                            yang sama dengan Anda.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        
        <section class="<?php echo e($card); ?>">
            <div class="px-5 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Tambah User</h3>
                <p class="text-sm text-gray-500">User baru otomatis masuk ke departemen: <span
                        class="font-medium"><?php echo e($department_name); ?></span></p>
            </div>

            <form class="p-5" wire:submit.prevent="store">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="<?php echo e($label); ?>">Full Name</label>
                        <input type="text" class="<?php echo e($input); ?>" wire:model.defer="full_name"
                            placeholder="e.g. John Doe">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['full_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div>
                        <label class="<?php echo e($label); ?>">Email Address</label>
                        <input type="email" class="<?php echo e($input); ?>" wire:model.defer="email"
                            placeholder="e.g. john@company.com">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['email'];
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
                        <input type="text" class="<?php echo e($input); ?>" wire:model.defer="phone_number"
                            placeholder="08123456789">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['phone_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div>
                        <label class="<?php echo e($label); ?>">Password</label>
                        <input type="password" class="<?php echo e($input); ?>" wire:model.defer="password"
                            autocomplete="new-password">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div>
                        <label class="<?php echo e($label); ?>">Role</label>
                        <select class="<?php echo e($input); ?>" wire:model.defer="role_id">
                            <option value="">Pilih role</option>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($r['id']); ?>"><?php echo e($r['name']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </select>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['role_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div>
                        <label class="<?php echo e($label); ?>">Department (Terkunci)</label>
                        <input type="text" class="<?php echo e($input); ?>" value="<?php echo e($department_name); ?>" readonly>
                    </div>
                </div>

                <div class="pt-5">
                    <button type="submit" wire:loading.attr="disabled" wire:target="store"
                        class="inline-flex items-center gap-2 px-5 h-10 rounded-xl bg-gray-900 text-white text-sm font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900/20 transition active:scale-95 hover:bg-black disabled:opacity-60 relative overflow-hidden"
                        wire:loading.class="opacity-80 cursor-wait">
                        <span class="flex items-center gap-2" wire:loading.remove wire:target="store">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Data
                        </span>
                        <span class="flex items-center gap-2" wire:loading wire:target="store">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4" />
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0A12 12 0 000 12h4z" />
                            </svg>
                            Menyimpan…
                        </span>
                    </button>
                </div>
            </form>
        </section>

        
        <div class="<?php echo e($card); ?>">
            <div class="px-5 py-4 border-b border-gray-200">
                <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                    <div class="relative flex-1">
                        <input type="text" wire:model.live="search" placeholder="Cari nama atau email..."
                            class="<?php echo e($input); ?> pl-10 w-full placeholder:text-gray-400">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m21 21-4.3-4.3M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                        </svg>
                    </div>

                    <div class="relative">
                        <select wire:model.live="roleFilter" class="<?php echo e($input); ?> pl-10 w-full lg:w-60">
                            <option value="">Semua Role</option>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($r['id']); ?>"><?php echo e($r['name']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </select>
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.284-1.255-.778-1.664M6 18H2v-2a3 3 0 015.356-1.857M14 5a3 3 0 11-6 0 3 3 0 016 0zM4.5 8.5a2.5 2.5 0 115 0 2.5 2.5 0 01-5 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="divide-y divide-gray-200">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $roleName = strtolower($u->role->name ?? '');
                        $isSelf = auth()->id() === $u->user_id;
                        $canEdit = !in_array($roleName, ['admin', 'superadmin']) || $isSelf;   // admin/superadmin hanya boleh edit diri sendiri
                        $canDelete = !in_array($roleName, ['admin', 'superadmin']);              // tidak boleh hapus admin/superadmin

                        // Nomor urut rapi (berdasar pagination): 1,2,3,4...
                        $rowNo = (($users->firstItem() ?? 1) - 0) + $loop->index;
                    ?>

                    <div class="px-5 py-5 hover:bg-gray-50 transition-colors" wire:key="user-<?php echo e($u->user_id); ?>">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <div class="flex items-start gap-3 flex-1">
                                <div class="<?php echo e($ico); ?>"><?php echo e(strtoupper(substr($u->full_name, 0, 1))); ?></div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-semibold text-gray-900 text-sm sm:text-base truncate">
                                            <?php echo e($u->full_name); ?>

                                        </h4>
                                        <span class="<?php echo e($chip); ?>">
                                            <span class="font-medium text-gray-700"><?php echo e($u->role->name ?? 'No Role'); ?></span>
                                        </span>
                                        <span class="<?php echo e($chip); ?>">
                                            <span class="text-gray-500">Dept:</span>
                                            <span
                                                class="font-medium text-gray-700"><?php echo e($u->department->department_name ?? 'N/A'); ?></span>
                                        </span>
                                        <!--[if BLOCK]><![endif]--><?php if($isSelf): ?>
                                            <span class="<?php echo e($chip); ?>">You</span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    <p class="text-[12px] text-gray-500 truncate"><?php echo e($u->email); ?></p>
                                    <!--[if BLOCK]><![endif]--><?php if($u->phone_number): ?>
                                        <p class="text-[12px] text-gray-500 mt-0.5"><?php echo e($u->phone_number); ?></p>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>

                            <div class="text-right shrink-0 space-y-2">
                                
                                <div class="<?php echo e($mono); ?>">No. <?php echo e($rowNo); ?></div>
                                <div class="flex flex-wrap gap-2 justify-end pt-1">
                                    <!--[if BLOCK]><![endif]--><?php if($canEdit): ?>
                                        <button class="<?php echo e($btnBlk); ?>" wire:click="openEdit(<?php echo e($u->user_id); ?>)"
                                            wire:loading.attr="disabled" wire:target="openEdit(<?php echo e($u->user_id); ?>)"
                                            wire:key="btn-edit-<?php echo e($u->user_id); ?>">
                                            <span wire:loading.remove wire:target="openEdit(<?php echo e($u->user_id); ?>)">Edit</span>
                                            <span wire:loading wire:target="openEdit(<?php echo e($u->user_id); ?>)">Loading…</span>
                                        </button>
                                    <?php else: ?>
                                        <button class="<?php echo e($btnBlk); ?> opacity-50 cursor-not-allowed" disabled
                                            title="Anda tidak bisa mengedit akun Admin lain">
                                            Edit
                                        </button>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                    <!--[if BLOCK]><![endif]--><?php if($canDelete): ?>
                                        <button class="<?php echo e($btnRed); ?>" wire:click="delete(<?php echo e($u->user_id); ?>)"
                                            onclick="return confirm('Hapus user ini?')" wire:loading.attr="disabled"
                                            wire:target="delete(<?php echo e($u->user_id); ?>)" wire:key="btn-del-<?php echo e($u->user_id); ?>">
                                            <span wire:loading.remove wire:target="delete(<?php echo e($u->user_id); ?>)">Hapus</span>
                                            <span wire:loading wire:target="delete(<?php echo e($u->user_id); ?>)">Menghapus…</span>
                                        </button>
                                    <?php else: ?>
                                        <button class="<?php echo e($btnRed); ?> opacity-50 cursor-not-allowed" disabled
                                            title="Akun Admin tidak bisa dihapus">
                                            Hapus
                                        </button>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="px-5 py-14 text-center text-gray-500 text-sm">Tidak ada user pada departemen ini.</div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <!--[if BLOCK]><![endif]--><?php if($users->hasPages()): ?>
                <div class="px-5 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-center">
                        <?php echo e($users->links()); ?>

                    </div>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        
        <!--[if BLOCK]><![endif]--><?php if($modalEdit): ?>
            <div class="fixed inset-0 z-[60] flex items-center justify-center" role="dialog" aria-modal="true"
                wire:key="modal-edit" wire:keydown.escape.window="closeEdit">
                
                <button type="button" class="absolute inset-0 bg-black/50" aria-label="Close overlay"
                    wire:click="closeEdit"></button>

                
                <div class="relative w-full max-w-2xl mx-4 <?php echo e($card); ?> focus:outline-none" tabindex="-1">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-gray-900">Edit User</h3>
                        <button class="text-gray-500 hover:text-gray-700" type="button" wire:click="closeEdit"
                            aria-label="Close">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form class="p-5" wire:submit.prevent="update">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="<?php echo e($label); ?>">Full Name</label>
                                <input type="text" class="<?php echo e($input); ?>" wire:model.defer="edit_full_name" autofocus>
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['edit_full_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600 font-medium"><?php echo e($message); ?>

                                </p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <div>
                                <label class="<?php echo e($label); ?>">Email Address</label>
                                <input type="email" class="<?php echo e($input); ?>" wire:model.defer="edit_email">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['edit_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <div>
                                <label class="<?php echo e($label); ?>">Phone Number</label>
                                <input type="text" class="<?php echo e($input); ?>" wire:model.defer="edit_phone_number">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['edit_phone_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600 font-medium"><?php echo e($message); ?>

                                </p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <div>
                                <label class="<?php echo e($label); ?>">Password (kosongkan jika tidak diubah)</label>
                                <input type="password" class="<?php echo e($input); ?>" wire:model.defer="edit_password"
                                    autocomplete="new-password">
                            </div>

                            <div>
                                <label class="<?php echo e($label); ?>">Role</label>
                                <select class="<?php echo e($input); ?>" wire:model.live="edit_role_id">
                                    <option value="">Pilih role</option>
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($r['id']); ?>"><?php echo e($r['name']); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </select>
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['edit_role_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-rose-600 font-medium"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                            <div>
                                <label class="<?php echo e($label); ?>">Department (Terkunci)</label>
                                <input type="text" class="<?php echo e($input); ?>" value="<?php echo e($department_name); ?>" readonly>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-4">
                            <button type="button"
                                class="px-4 h-10 rounded-xl border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-100 hover:border-gray-400 transition"
                                wire:click="closeEdit">
                                Batal
                            </button>
                            <button type="submit" wire:loading.attr="disabled" wire:target="update"
                                class="inline-flex items-center gap-2 px-5 h-10 rounded-xl bg-gray-900 text-white text-sm font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900/20 transition active:scale-95 hover:bg-black disabled:opacity-60">
                                <span class="flex items-center gap-2" wire:loading.remove wire:target="update">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Simpan Perubahan
                                </span>
                                <span class="flex items-center gap-2" wire:loading wire:target="update">
                                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4" />
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0A12 12 0 000 12h4z" />
                                    </svg>
                                    Menyimpan…
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </main>
</div><?php /**PATH /home/haelahpx/Documents/GitHub/Frontend-KRB/resources/views/livewire/pages/admin/usermanagement.blade.php ENDPATH**/ ?>