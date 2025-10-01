{{-- resources/views/livewire/pages/superadmin/dashboard.blade.php --}}
<div class="bg-gray-50" wire:poll.5000ms>
    <main class="px-4 sm:px-6 py-6">
        <div class="space-y-8">
            {{-- HERO --}}
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
                <div class="pointer-events-none absolute inset-0 opacity-10">
                    <div class="absolute top-0 -right-4 w-24 h-24 bg-white rounded-full blur-xl"></div>
                    <div class="absolute bottom-0 -left-4 w-16 h-16 bg-white rounded-full blur-lg"></div>
                </div>
                <div class="relative z-10 p-6 sm:p-8">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 16v-2m0-10v2m0 6v2M6 12H4m16 0h-2m-10 0h2m6 0h2M9 17l-2 2M15 7l2-2M7 7l-2-2M17 17l2 2" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-semibold">Welcome, {{ $admin_name }}!</h2>
                            <p class="text-sm text-white/80">Here is the summary of all company activities.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STATS CARDS --}}
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($stats as $s)
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">{{ $s['label'] }}</p>
                    <div class="mt-2 flex items-end gap-2">
                        <h3 class="text-2xl font-semibold text-gray-900">{{ $s['value'] }}</h3>
                    </div>
                </div>
                @endforeach
            </section>

            {{-- WIDGETS --}}
            <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Recent Users --}}
                <div class="lg:col-span-2 rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="text-base font-semibold text-gray-900">Recent User Signups</h3>
                        <p class="text-sm text-gray-500">The latest 5 users added to the system</p>
                    </div>
                    <ul class="divide-y divide-gray-200">
                        @forelse ($recentUsers as $user)
                        <li class="px-5 py-4 flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900">{{ $user->full_name }}</p>
                                <p class="text-sm text-gray-500">{{ optional($user->company)->company_name ?? 'No Company' }}</p>
                            </div>
                            <span class="text-sm text-gray-500">{{ $user->created_at->diffForHumans() }}</span>
                        </li>
                        @empty
                        <li class="px-5 py-8 text-center text-sm text-gray-500">No new users found.</li>
                        @endforelse
                    </ul>
                </div>

                {{-- Company User Count --}}
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="text-base font-semibold text-gray-900">Top Companies by Users</h3>
                        <p class="text-sm text-gray-500">Most active companies</p>
                    </div>
                    <ul class="divide-y divide-gray-200">
                        @forelse ($companiesByUserCount as $company)
                        <li class="px-5 py-4 flex items-center justify-between">
                            <p class="font-medium text-gray-900">{{ $company->company_name }}</p>
                            <span class="text-sm font-semibold text-gray-700">{{ $company->users_count }} users</span>
                        </li>
                        @empty
                        <li class="px-5 py-8 text-center text-sm text-gray-500">No company data available.</li>
                        @endforelse
                    </ul>
                </div>
            </section>

            <section>
                {{-- System Wide Announcements --}}
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="text-base font-semibold text-gray-900">Recent Announcements</h3>
                        <p class="text-sm text-gray-500">Latest 5 announcements from all companies</p>
                    </div>
                    <ul class="divide-y divide-gray-200">
                        @forelse ($recentAnnouncements as $announcement)
                        <li class="px-5 py-4">
                            <p class="text-sm text-gray-900">{{ $announcement->description }}</p>
                            <div class="text-xs text-gray-500 mt-1">
                                By {{ optional($announcement->company)->company_name ?? 'N/A' }} • {{ $announcement->created_at->diffForHumans() }}
                            </div>
                        </li>
                        @empty
                        <li class="px-5 py-8 text-center text-sm text-gray-500">No recent announcements.</li>
                        @endforelse
                    </ul>
                </div>
            </section>
        </div>
    </main>
</div>