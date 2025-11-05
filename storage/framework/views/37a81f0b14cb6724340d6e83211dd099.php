<div
    x-data="{
        profile: <?php echo \Illuminate\Support\Js::from($profile)->toHtml() ?>,
        stats: <?php echo \Illuminate\Support\Js::from($stats)->toHtml() ?>,
        passwordSuccess: false,
        initials() {
            const n = (this.profile?.fullName || 'U').trim();
            const parts = n.split(/\s+/);
            return (parts[0]?.[0] || 'U') + (parts[1]?.[0] || '');
        }
    }"
    x-on:password-updated.window="
        passwordSuccess = true;
        setTimeout(() => passwordSuccess = false, 3000);
    ">
    <style>
        .krbs-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .1)
        }
        .krbs-button {
            background: #1f2937;
            color: #fff;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: background-color .2s
        }
        .krbs-button:hover { background: #374151 }
        .krbs-button-outline {
            background: #fff;
            color: #374151;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            border: 1px solid #d1d5db;
            cursor: pointer;
            transition: all .2s
        }
        .krbs-button-outline:hover {
            background: #f9fafb;
            border-color: #9ca3af
        }
        .krbs-input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            background: #fff;
            transition: border-color .2s
        }
        .krbs-input:focus {
            outline: none;
            border-color: #1f2937;
            box-shadow: 0 0 0 1px #1f2937
        }
        .success-message {
            background: #d1fae5;
            color: #065f46;
            padding: 12px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 16px
        }
        .error-message {
            background: #fee2e2;
            color: #dc2626;
            padding: 12px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 16px
        }
    </style>

    <div class="min-h-screen py-8 px-4 bg-gray-50">
        <div class="max-w-6xl mx-auto">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">User Profile</h1>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Profile Info -->
                <div class="lg:col-span-2">
                    <div class="krbs-card p-6">
                        <div class="flex items-center mb-8 p-6 bg-gray-50 rounded-lg">
                            <div class="w-20 h-20 bg-gray-800 rounded-full flex items-center justify-center text-white text-2xl font-bold mr-6"
                                x-text="initials().toUpperCase()"></div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900" x-text="profile.fullName"></h2>
                                <p class="text-gray-600" x-text="profile.email"></p>
                                <div class="flex items-center mt-1">
                                    <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <circle cx="10" cy="10" r="3"></circle>
                                    </svg>
                                    <span class="text-sm text-green-600 font-medium">Online</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">Profile Information</h3>
                            <p class="text-gray-600 text-sm">Your account information and details.</p>
                        </div>

                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                    <div class="krbs-input bg-gray-50 text-gray-700" x-text="profile.fullName"></div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                    <div class="krbs-input bg-gray-50 text-gray-700" x-text="profile.email"></div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                    <div class="krbs-input bg-gray-50 text-gray-700" x-text="profile.phone_number || 'Not provided'"></div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Employee ID</label>
                                    <div class="krbs-input bg-gray-50 text-gray-700" x-text="profile.employeeId || '-'"></div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                                    <div class="krbs-input bg-gray-50 text-gray-700" x-text="profile.department"></div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Branch</label>
                                    <div class="krbs-input bg-gray-50 text-gray-700" x-text="profile.company"></div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                                    <div class="krbs-input bg-gray-50 text-gray-700" x-text="profile.role || '-'"></div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Join Date</label>
                                    <div class="krbs-input bg-gray-50 text-gray-700" x-text="profile.joinDate || '-'"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="krbs-card p-6">
                        <div class="mb-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-1">Change Password</h2>
                            <p class="text-gray-600 text-sm">Ensure your account is using a long, random password to stay secure.</p>
                        </div>

                        <div x-show="passwordSuccess" x-transition class="success-message" style="display:none;">
                            Password changed successfully!
                        </div>

                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error-message"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['new_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error-message"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->

                        <form wire:submit.prevent="updatePassword">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                    <input type="password" class="krbs-input" wire:model.defer="current_password" autocomplete="current-password" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                    <input type="password" class="krbs-input" wire:model.defer="new_password" autocomplete="new-password" required>
                                    <p class="text-xs text-gray-500 mt-1">Min. 8 characters and must differ from current password.</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                    <input type="password" class="krbs-input" wire:model.defer="new_password_confirmation" autocomplete="new-password" required>
                                </div>

                                <div class="pt-2">
                                    <button type="submit" class="krbs-button w-full" wire:loading.attr="disabled">
                                        <span wire:loading.remove>Change Password</span>
                                        <span wire:loading>Changing...</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Account Summary -->
                    <div class="krbs-card p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Summary</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Open Tickets</span>
                                <span class="text-sm font-medium text-yellow-600" x-text="stats.openTickets"></span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Active Bookings</span>
                                <span class="text-sm font-medium text-blue-600" x-text="stats.activeBookings"></span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Packages</span>
                                <span class="text-sm font-medium text-green-600" x-text="stats.packages"></span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm text-gray-600">Member Since</span>
                                <span class="text-sm font-medium text-gray-900" x-text="stats.memberSince"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Back to Home -->
            <div class="mt-8">
                <a href="<?php echo e(route('home')); ?>" class="krbs-button-outline inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Home
                </a>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /home/adomancer/Documents/GitHub/KRB-System/resources/views/livewire/pages/user/profile.blade.php ENDPATH**/ ?>