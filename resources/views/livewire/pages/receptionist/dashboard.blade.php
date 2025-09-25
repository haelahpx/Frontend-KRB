<div class="min-h-screen bg-gray-50 p-6" wire:poll.1000ms>
    <div class="mx-auto max-w-7xl grid gap-6 lg:grid-cols-[260px_1fr]">
        <main class="space-y-8">
            <section class="relative">
                <div
                    class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 to-black text-white shadow-2xl">
                    <div class="pointer-events-none absolute inset-0 opacity-10">
                        <div class="absolute -right-6 -top-6 h-28 w-28 rounded-full bg-white blur-2xl"></div>
                        <div class="absolute -left-6 -bottom-6 h-20 w-20 rounded-full bg-white blur-xl"></div>
                    </div>

                    <div class="relative z-10 p-6">
                        <div class="mb-2 flex items-center gap-4">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl border border-white/20 bg-white/10 backdrop-blur-sm">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <h1 class="text-2xl font-bold tracking-tight">Receptionist Dashboard</h1>
                        </div>

                        <div class="flex items-center gap-3 text-sm">
                            <p class="text-gray-200">Kelola aktivitas dan kunjungan dengan mudah</p>
                            <span class="inline-block h-2 w-2 animate-pulse rounded-full bg-green-400"></span>
                            <p class="font-mono text-gray-200">{{ now()->format('H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                <article class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-2xl">
                    <div class="p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl border-2 border-gray-200 bg-gray-50">
                                <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <span class="h-3 w-3 animate-pulse rounded-full bg-green-400"></span>
                        </div>
                        <h2 class="mb-1 text-lg font-medium text-gray-700">Total Appointments</h2>
                        <p class="text-3xl font-bold text-gray-900">123</p>
                    </div>
                    <div class="h-1 bg-gradient-to-r from-gray-900 to-black"></div>
                </article>

                <article class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-2xl">
                    <div class="p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl border-2 border-gray-200 bg-gray-50">
                                <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="h-3 w-3 animate-pulse rounded-full bg-yellow-400"></span>
                        </div>
                        <h2 class="mb-1 text-lg font-medium text-gray-700">Pending Inquiries</h2>
                        <p class="text-3xl font-bold text-gray-900">45</p>
                    </div>
                    <div class="h-1 bg-gradient-to-r from-gray-900 to-black"></div>
                </article>

                <article class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-2xl">
                    <div class="p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl border-2 border-gray-200 bg-gray-50">
                                <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <span class="h-3 w-3 animate-pulse rounded-full bg-blue-400"></span>
                        </div>
                        <h2 class="mb-1 text-lg font-medium text-gray-700">Visitors Today</h2>
                        <p class="text-3xl font-bold text-gray-900">78</p>
                    </div>
                    <div class="h-1 bg-gradient-to-r from-gray-900 to-black"></div>
                </article>
            </section>

            <section class="flex justify-center lg:justify-end">
                <form method="POST" action="{{ route('logout') }}" class="pt-2">
                    @csrf
                </form>
            </section>
        </main>
    </div>
</div>