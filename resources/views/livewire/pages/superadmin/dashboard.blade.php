<div>
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Superadmin Dashboard</h1>
    <div class="mt-4 w-16 h-0.5 bg-gray-800"></div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
        <!-- Example Card 1 -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-medium text-gray-700 mb-4">Total Users</h2>
            <p class="text-3xl font-bold text-gray-900">1,234</p>
        </div>

        <!-- Example Card 2 -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-medium text-gray-700 mb-4">Active Sessions</h2>
            <p class="text-3xl font-bold text-gray-900">567</p>
        </div>
        <!-- Example Card 3 -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-medium text-gray-700 mb-4">System
                Health</h2>
            <p class="text-3xl font-bold text-gray-900">Good</p>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="pt-2">
            @csrf
            <button type="submit"
                class="block w-full text-center px-4 py-3 text-base font-medium text-black bg-white rounded-md hover:bg-gray-200 transition-colors">
                Logout
            </button>
        </form>
    </div>