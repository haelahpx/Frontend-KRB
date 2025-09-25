<div class="p-6 bg-white rounded-lg shadow">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold">User Management</h1>
        <p class="text-sm text-gray-600">Company:
            <span class="font-semibold">{{ $company_name }}</span>
        </p>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
    <div class="mb-4 rounded bg-green-600 text-white px-4 py-2">
        {{ session('message') }}
    </div>
    @endif

    <!-- Filters -->
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <input wire:model.live="search"
            type="text"
            placeholder="Search name/email..."
            class="w-full sm:w-1/3 px-3 py-2 border rounded-md">

        <select wire:model.live="roleFilter"
            class="px-3 py-2 border rounded-md w-full sm:w-60">
            <option value="">All Roles</option>
            @foreach ($roles as $r)
            <option value="{{ $r['id'] }}">{{ $r['name'] }}</option>
            @endforeach
        </select>
    </div>

    <!-- Form -->
    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}"
        class="grid sm:grid-cols-2 gap-4 mb-8">

        <!-- Full Name -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Full Name</label>
            <input type="text" wire:model.defer="full_name"
                class="mt-1 w-full px-3 py-2 border rounded-md">
            @error('full_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Email -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" wire:model.defer="email"
                class="mt-1 w-full px-3 py-2 border rounded-md">
            @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Phone -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Phone</label>
            <input type="text" wire:model.defer="phone_number"
                class="mt-1 w-full px-3 py-2 border rounded-md">
        </div>

        <!-- Password (only for create) -->
        @unless($isEdit)
        <div>
            <label class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" wire:model.defer="password"
                class="mt-1 w-full px-3 py-2 border rounded-md">
            @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        @endunless

        <!-- Role -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Role</label>
            <select wire:model.defer="role_id"
                class="mt-1 w-full px-3 py-2 border rounded-md">
                <option value="">Select role</option>
                @foreach ($roles as $r)
                <option value="{{ $r['id'] }}">{{ $r['name'] }}</option>
                @endforeach
            </select>
            @error('role_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Company (locked) -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Company</label>
            <input type="text"
                class="mt-1 w-full px-3 py-2 border rounded-md bg-gray-100"
                value="{{ $company_name }}" readonly>
            @error('company_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Department -->
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Department</label>
            <select wire:model.defer="department_id"
                class="mt-1 w-full px-3 py-2 border rounded-md" required>
                <option value="">Select department</option>
                @foreach ($departments as $d)
                <option value="{{ $d->department_id }}">{{ $d->department_name }}</option>
                @endforeach
            </select>
            @error('department_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Submit Button -->
        <div class="sm:col-span-2">
            <button type="submit"
                class="px-4 py-2 bg-gray-900 text-white rounded-md">
                {{ $isEdit ? 'Update' : 'Create' }} User
            </button>
        </div>
    </form>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 border">ID</th>
                    <th class="px-3 py-2 border">Name</th>
                    <th class="px-3 py-2 border">Email</th>
                    <th class="px-3 py-2 border">Phone</th>
                    <th class="px-3 py-2 border">Role</th>
                    <th class="px-3 py-2 border w-44">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $u)
                <tr wire:key="user-row-{{ $u->user_id }}">
                    <td class="px-3 py-2 border">{{ $u->user_id }}</td>
                    <td class="px-3 py-2 border">{{ $u->full_name }}</td>
                    <td class="px-3 py-2 border">{{ $u->email }}</td>
                    <td class="px-3 py-2 border">{{ $u->phone_number }}</td>
                    <td class="px-3 py-2 border">{{ $u->role->name ?? '-' }}</td>
                    <td class="px-3 py-2 border space-x-2">
                        <button type="button"
                            wire:click="edit({{ $u->user_id }})"
                            class="px-3 py-1 rounded bg-yellow-500 text-white">
                            Edit
                        </button>
                        <button type="button"
                            wire:click="delete({{ $u->user_id }})"
                            class="px-3 py-1 rounded bg-red-600 text-white">
                            Delete
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-3 py-6 text-center">No users found</td>
                </tr>
                @endforelse
            </tbody>

        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">{{ $users->links() }}</div>
</div>