<div class="min-h-screen bg-white">
  <section class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8 py-6 md:py-8 space-y-8">
    
    <!-- Header -->
    <div class="flex items-start justify-between gap-4 flex-wrap">
      <div>
        <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-black">KRBS Home</h1>
        <p class="mt-2 text-gray-600">Selamat datang! Kebun Raya Bogor System.</p>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
      <div class="bg-white rounded-xl p-6 transition-all duration-300 hover:-translate-y-2 hover:shadow-xl border border-black">
        <div class="flex items-center gap-3 mb-3">
          <div class="bg-black text-white px-3 py-1 rounded-full text-sm font-medium">Tickets</div>
          <h2 class="text-xl font-semibold text-black">Open Tickets</h2>
        </div>
        <p class="text-gray-600 mb-4">Ringkasan tiket yang belum selesai.</p>
        <div class="flex justify-between items-center">
          <div class="text-sm text-gray-500">Total: <span class="font-semibold text-black">{{ $openTicketsCount ?? 0 }}</span></div>
          <a href="{{ route('create-ticket') }}" class="bg-black text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-gray-800 hover:shadow-lg">New Ticket</a>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 transition-all duration-300 hover:-translate-y-2 hover:shadow-xl border border-black">
        <div class="flex items-center gap-3 mb-3">
          <div class="bg-black text-white px-3 py-1 rounded-full text-sm font-medium">Booking</div>
          <h2 class="text-xl font-semibold text-black">Upcoming Bookings</h2>
        </div>
        <p class="text-gray-600 mb-4">Lihat booking ruangan.</p>
        <div class="flex justify-between items-center">
          <div class="text-sm text-gray-500">Minggu ini: <span class="font-semibold text-black">{{ $upcomingBookings ?? 0 }}</span></div>
          <a href="" class="bg-black text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-gray-800 hover:shadow-lg">Manage</a>
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 transition-all duration-300 hover:-translate-y-2 hover:shadow-xl border border-black">
        <div class="flex items-center gap-3 mb-3">
          <div class="bg-black text-white px-3 py-1 rounded-full text-sm font-medium">Packages</div>
          <h2 class="text-xl font-semibold text-black">Packages</h2>
        </div>
        <p class="text-gray-600 mb-4">Pantau paket.</p>
        <div class="flex justify-between items-center">
          <div class="text-sm text-gray-500">In Transit: <span class="font-semibold text-black">{{ $packagesInTransit ?? 0 }}</span></div>
          <a href="" class="bg-black text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-gray-800 hover:shadow-lg">View All</a>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-xl p-6 shadow-lg border border-black">
      <h3 class="text-xl font-semibold text-black mb-4">Shortcuts</h3>
      <div class="flex flex-wrap gap-3">
        <a href="{{ route('create-ticket') }}" class="bg-black text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 hover:bg-gray-800 hover:shadow-lg">+ Ticket</a>
        <a href="" class="bg-white border border-black text-black px-4 py-2 rounded-lg font-medium transition-all duration-200 hover:bg-gray-50">+ Booking</a>
        <a href="" class="bg-white border border-black text-black px-4 py-2 rounded-lg font-medium transition-all duration-200 hover:bg-gray-50">+ Package</a>
      </div>
    </div>

  </section>
</div>