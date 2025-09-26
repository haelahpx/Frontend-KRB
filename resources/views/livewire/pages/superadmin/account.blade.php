<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">

    {{-- Style Variables --}}
    @php
    $card = 'bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden';
    $head = 'bg-gradient-to-r from-black to-gray-800';
    $hpad = 'px-8 py-6';
    $tag = 'w-2 h-8 bg-white rounded-full';
    $label = 'block text-sm font-semibold text-gray-700 mb-2';
    $input = 'w-full px-4 py-3 rounded-xl border-2 border-gray-200 text-gray-700 focus:border-black focus:ring-4 focus:ring-black/10 bg-gray-50 focus:bg-white transition';
    $btnBlk = 'px-4 py-2 text-sm rounded-xl bg-black text-white hover:bg-gray-800 disabled:opacity-60 font-semibold shadow-lg hover:shadow-xl transition';
    $btnRed = 'px-4 py-2 text-sm rounded-xl bg-red-600 text-white hover:bg-red-700 disabled:opacity-60 font-semibold shadow-lg hover:shadow-xl transition';
    $chip = 'inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-gray-100 text-sm';
    $mono = 'text-xs font-mono text-gray-400 bg-gray-100 px-3 py-1 rounded-lg';
    $icoAvatar = 'w-12 h-12 bg-black rounded-2xl flex items-center justify-center text-white font-bold text-lg shrink-0';
    @endphp

    <div class="space-y-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">User Management</h1>
            <p class="text-gray-600 mt-1">
                Managing users for company: <span class="font-semibold">{{ $company_name }}</span>
            </p>
        </div>

        {{-- Flash Messages --}}
        @if (session()->has('message'))
        <div class="bg-gray-900 text-white px-6 py-4 font-semibold rounded-2xl shadow-lg">{{ session('message') }}</div>
        @endif

        <div class="{{ $card }}">
            <div class="{{ $head }} {{ $hpad }}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="{{ $tag }}"></div>
                        <div>
                            <h2 class="text-xl font-semibold text-white">{{ $isEdit ? 'Edit User' : 'Add New User' }}</h2>
                            <p class="text-gray-300 text-sm">
                                {{ $isEdit ? 'Update the details for the selected user.' : 'Fill in the form to create a new user.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="{{ $label }}">Full Name</label>
                        <input type="text" wire:model.defer="full_name" class="{{ $input }}" placeholder="e.g. John Doe">
                        @error('full_name') <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $label }}">Email Address</label>
                        <input type="email" wire:model.defer="email" class="{{ $input }}" placeholder="e.g. john.doe@example.com">
                        @error('email') <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $label }}">Phone Number</label>
                        <input type="text" wire:model.defer="phone_number" class="{{ $input }}" placeholder="e.g. 08123456789">
                    </div>
                    <div>
                        <label class="{{ $label }}">Password {{ $isEdit ? '(Leave blank to keep unchanged)' : '' }}</label>
                        <input type="password" wire:model.defer="password" class="{{ $input }}">
                        @error('password') <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $label }}">Company</label>
                        <input type="text" class="{{ $input }}" value="{{ $company_name }}" readonly>
                        @error('company_id') <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="{{ $label }}">Role</label>
                        <select wire:model.defer="role_id" class="{{ $input }}">
                            <option value="">Select role</option>
                            @foreach ($roles as $r)
                            <option value="{{ $r['id'] }}">{{ $r['name'] }}</option>
                            @endforeach
                        </select>
                        @error('role_id') <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="{{ $label }}">Department</label>
                        <select wire:model.defer="department_id" class="{{ $input }}" required>
                            <option value="">Select department</option>
                            @foreach ($departments as $d)
                            <option value="{{ $d->department_id }}">{{ $d->department_name }}</option>
                            @endforeach
                        </select>
                        @error('department_id') <p class="mt-1 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="pt-4 flex items-center gap-4">
                    <button type="submit" class="{{ $btnBlk }} px-8 py-3">
                        {{ $isEdit ? 'Update User' : 'Create User' }}
                    </button>
                    @if($isEdit)
                    <button type="button" wire:click="resetForm" class="px-8 py-3 rounded-xl border-2 font-semibold hover:bg-gray-100 transition">Cancel Edit</button>
                    @endif
                </div>
            </form>
        </div>

        <div class="{{ $card }}">
            <div class="{{ $head }} {{ $hpad }}">
                <div class="flex items-center gap-4">
                    <div class="{{ $tag }}"></div>
                    <div>
                        <h2 class="text-xl font-semibold text-white">Registered Users</h2>
                        <p class="text-gray-300 text-sm">A list of all users in your company.</p>
                    </div>
                </div>
            </div>

            <div class="px-8 py-6 bg-gray-50/70 border-b border-gray-100">
                <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                    <div class="relative flex-1">
                        <input type="text" wire:model.live="search" placeholder="Search by name or email..." class="{{ $input }} pl-12 w-full placeholder:text-gray-400">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.3-4.3M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                        </svg>
                    </div>
                    <div class="relative">
                        <select wire:model.live="roleFilter" class="{{ $input }} pl-12 w-full lg:w-60">
                            <option value="">All Roles</option>
                            @foreach ($roles as $r)
                            <option value="{{ $r['id'] }}">{{ $r['name'] }}</option>
                            @endforeach
                        </select>
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.284-1.255-.778-1.664M6 18H2v-2a3 3 0 015.356-1.857M14 5a3 3 0 11-6 0 3 3 0 016 0zM4.5 8.5a2.5 2.5 0 115 0 2.5 2.5 0 01-5 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse ($users as $u)
                <div class="px-8 py-6 hover:bg-gray-50/70 transition-colors" wire:key="user-{{ $u->user_id }}">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                        <div class="flex items-start gap-4 flex-1">
                            <div class="{{ $icoAvatar }}">{{ substr($u->full_name, 0, 1) }}</div>
                            <div class="min-w-0 flex-1">
                                <h4 class="font-semibold text-gray-800 text-lg">{{ $u->full_name }}</h4>
                                <p class="text-sm text-gray-500 truncate">{{ $u->email }}</p>

                                <div class="flex flex-wrap gap-2 mt-3">
                                    <span class="{{ $chip }}">
                                        <span class="font-medium text-gray-700">{{ $u->role->name ?? 'No Role' }}</span>
                                    </span>
                                    <span class="{{ $chip }}">
                                        <span class="text-gray-500">Dept:</span>
                                        <span class="font-medium text-gray-700">{{ $u->department->department_name ?? 'N/A' }}</span>
                                    </span>
                                    @if($u->phone_number)
                                    <span class="{{ $chip }}">
                                        <span class="font-medium text-gray-700">{{ $u->phone_number }}</span>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="text-right shrink-0 space-y-2">
                            <div class="{{ $mono }}">#{{ $u->user_id }}</div>
                            <div class="flex flex-wrap gap-2 justify-end pt-2">
                                <button wire:click="edit({{ $u->user_id }})" class="{{ $btnBlk }}">Edit</button>
                                <button wire:click="delete({{ $u->user_id }})" onclick="return confirm('Are you sure you want to delete this user?')" class="{{ $btnRed }}">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-8 py-16 text-center text-gray-500">No users found matching your criteria.</div>
                @endforelse
            </div>

            @if($users->hasPages())
            <div class="px-8 py-6 bg-gray-50/70 border-t border-gray-100">
                <div class="flex justify-center">
                    {{ $users->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>