<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - KRBS Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .krbs-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .krbs-button {
            background: #1f2937;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .krbs-button:hover {
            background: #374151;
        }

        .krbs-button-outline {
            background: white;
            color: #374151;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            border: 1px solid #d1d5db;
            cursor: pointer;
            transition: all 0.2s;
        }

        .krbs-button-outline:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }

        .krbs-input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            background: white;
            transition: border-color 0.2s;
        }

        .krbs-input:focus {
            outline: none;
            border-color: #1f2937;
            box-shadow: 0 0 0 1px #1f2937;
        }

        .success-message {
            background: #d1fae5;
            color: #065f46;
            padding: 12px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 16px;
        }

        .error-message {
            background: #fee2e2;
            color: #dc2626;
            padding: 12px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 16px;
        }
    </style>
</head>

<body class="bg-gray-50">
    <div class="min-h-screen py-8 px-4">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">User Profile</h1>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="profileData()">
                <!-- Profile Information -->
                <div class="lg:col-span-2">
                    <div class="krbs-card p-6">
                        <!-- Profile Header with Avatar -->
                        <div class="flex items-center mb-8 p-6 bg-gray-50 rounded-lg">
                            <div class="w-20 h-20 bg-gray-800 rounded-full flex items-center justify-center text-white text-2xl font-bold mr-6"
                                x-text="getInitials(profile.fullName)">
                            </div>
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
                                    <div class="krbs-input bg-gray-50 text-gray-700"
                                        x-text="profile.phone || 'Not provided'"></div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Employee ID</label>
                                    <div class="krbs-input bg-gray-50 text-gray-700" x-text="profile.employeeId"></div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                                    <div class="krbs-input bg-gray-50 text-gray-700" x-text="profile.department"></div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Branch</label>
                                    <div class="krbs-input bg-gray-50 text-gray-700" x-text="profile.branch"></div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Join Date</label>
                                <div class="krbs-input bg-gray-50 text-gray-700" x-text="profile.joinDate"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="lg:col-span-1">
                    <div class="krbs-card p-6 mb-6">
                        <div class="mb-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-1">Change Password</h2>
                            <p class="text-gray-600 text-sm">Ensure your account is using a long, random password to
                                stay secure.</p>
                        </div>

                        <div x-show="passwordSuccess" x-transition class="success-message" style="display: none;">
                            Password changed successfully!
                        </div>

                        <div x-show="passwordError" x-transition class="error-message" style="display: none;">
                            <span x-text="passwordErrorMessage"></span>
                        </div>

                        <form @submit.prevent="changePassword()">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                    <input type="password" x-model="password.current" class="krbs-input"
                                        placeholder="Enter current password" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                    <input type="password" x-model="password.new" class="krbs-input"
                                        placeholder="Enter new password" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New
                                        Password</label>
                                    <input type="password" x-model="password.confirm" class="krbs-input"
                                        placeholder="Confirm new password" required>
                                </div>

                                <div class="pt-2">
                                    <button type="submit" class="krbs-button w-full" :disabled="passwordLoading">
                                        <span x-show="!passwordLoading">Change Password</span>
                                        <span x-show="passwordLoading">Changing...</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Account Stats -->
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
                <a href="#" class="krbs-button-outline inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Home
                </a>
            </div>
        </div>
    </div>

    <script>
        function profileData() {
            return {
                profile: {
                    fullName: 'John Doe',
                    email: 'john.doe@company.com',
                    phone: '+62 812 3456 7890',
                    department: 'IT Support'
                },
                password: {
                    current: '',
                    new: '',
                    confirm: ''
                },
                stats: {
                    openTickets: 2,
                    activeBookings: 1,
                    packages: 3,
                    memberSince: 'Jan 2024'
                },
                profileLoading: false,
                profileSuccess: false,
                passwordLoading: false,
                passwordSuccess: false,
                passwordError: false,
                passwordErrorMessage: '',

                async updateProfile() {
                    this.profileLoading = true;
                    this.profileSuccess = false;

                    // Simulate API call
                    await new Promise(resolve => setTimeout(resolve, 1000));

                    this.profileLoading = false;
                    this.profileSuccess = true;

                    // Hide success message after 3 seconds
                    setTimeout(() => {
                        this.profileSuccess = false;
                    }, 3000);
                },

                async changePassword() {
                    this.passwordLoading = true;
                    this.passwordSuccess = false;
                    this.passwordError = false;

                    // Basic validation
                    if (this.password.new !== this.password.confirm) {
                        this.passwordError = true;
                        this.passwordErrorMessage = 'New passwords do not match.';
                        this.passwordLoading = false;
                        return;
                    }

                    if (this.password.new.length < 8) {
                        this.passwordError = true;
                        this.passwordErrorMessage = 'Password must be at least 8 characters long.';
                        this.passwordLoading = false;
                        return;
                    }

                    // Simulate API call
                    await new Promise(resolve => setTimeout(resolve, 1000));

                    // Reset form
                    this.password = { current: '', new: '', confirm: '' };
                    this.passwordLoading = false;
                    this.passwordSuccess = true;

                    // Hide success message after 3 seconds
                    setTimeout(() => {
                        this.passwordSuccess = false;
                    }, 3000);
                }
            }
        }
    </script>
</body>

</html>