<div class="min-h-screen bg-white">
    <section class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8 py-6 md:py-8 space-y-8">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-black">KRBS Home</h1>
                <p class="mt-2 text-gray-600">Selamat datang! Kebun Raya Bogor System.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl p-6 shadow-lg border border-black flex flex-wrap gap-3">
                <h3 class="text-[#b10303] text-xl font-semibold mb-4">Announcement!</h3>
                <div class="grid grid-cols-1 md:grid-cols-[auto_1fr] md:gap-x-4 items-start">
                    <div>
                        <h4 class="text-[#b10303] bg-white">2025-09-12</h4>
                    </div>
                    <div>
                        <p class="text-gray-600">Akan ada maintain website pada tanggal 12 September 2025.</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-[auto_1fr] md:gap-x-4 items-start">
                    <div>
                        <h4 class="text-[#b10303] bg-white">2025-09-25</h4>
                    </div>
                    <div>
                        <p class="text-gray-600">Akan ada acara bonding karyawan tanggal 25 September 2025.</p>
                    </div>
                </div>
            </div>

            <div
                class="grid grid-cols-1 md:grid-cols-2bg-white rounded-xl p-6 shadow-lg border border-black flex flex-wrap gap-3">
                <h3 class="text-[#b10303] text-xl font-semibold mb-4">Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-[auto_1fr] md:gap-x-4 items-start">
                    <div>
                        <p class="text-gray-600 bg-white">Event berkebun</p>
                    </div>
                    <div>
                        <p class="text-gray-600">2025-09-1 until 2025-10-2</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-[auto_1fr] md:gap-x-4 items-start">
                    <div>
                        <h4 class="text-gray-600 bg-white">Rapat bulanan</h4>
                    </div>
                    <div>
                        <p class="text-gray-600">2025-09-27</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl p-6 transition-all duration-300 hover:shadow-xl border border-black">
                <div class="flex items-center gap-3 mb-3">
                    <div class="bg-black text-white px-3 py-1 rounded-full text-sm font-medium">Tickets</div>
                    <h2 class="text-xl font-semibold text-black">Tickets Status</h2>
                </div>
                <p class="text-gray-600 mb-4">Ringkasan tiket yang belum selesai.</p>
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-500">Total: <span
                            class="font-semibold text-black">{{ $openTicketsCount ?? 0 }}</span></div>
                    <a href="{{ route('ticketstatus') }}"
                        class="bg-black text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-red-800 hover:shadow-lg">Manage</a>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 transition-all duration-300 hover:shadow-xl border border-black">
                <div class="flex items-center gap-3 mb-3">
                    <div class="bg-black text-white px-3 py-1 rounded-full text-sm font-medium">Booking</div>
                    <h2 class="text-xl font-semibold text-black">Booking Status</h2>
                </div>
                <p class="text-gray-600 mb-4">Lihat booking ruangan.</p>
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-500">Minggu ini: <span
                            class="font-semibold text-black">{{ $upcomingBookings ?? 0 }}</span></div>
                    <a href=""
                        class="bg-black text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-red-800 hover:shadow-lg">Manage</a>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 transition-all duration-300 hover:shadow-xl border border-black">
                <div class="flex items-center gap-3 mb-3">
                    <div class="bg-black text-white px-3 py-1 rounded-full text-sm font-medium">Packages</div>
                    <h2 class="text-xl font-semibold text-black">Packages</h2>
                </div>
                <p class="text-gray-600 mb-4">Pantau paket.</p>
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-500">In Transit: <span
                            class="font-semibold text-black">{{ $packagesInTransit ?? 0 }}</span></div>
                    <a href=""
                        class="bg-black text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-red-800 hover:shadow-lg">View
                        All</a>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-lg border border-black">
            <h3 class="text-xl font-semibold text-black mb-4">Shortcuts</h3>
            <div class="flex flex-wrap gap-3">
                <flux:modal.trigger name="new-ticket">
                    <button type="button"
                        class="bg-black text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 hover:bg-red-800 hover:shadow-lg">
                        + Ticket
                    </button>
                </flux:modal.trigger>

                <flux:modal.trigger name="booking-room">
                    <button type="button"
                        class="bg-black text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 hover:bg-red-800 hover:shadow-lg">
                        + Booking
                    </button>
                </flux:modal.trigger>

                <flux:modal.trigger name="new-package">
                    <button type="button"
                        class="bg-black text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 hover:bg-red-800 hover:shadow-lg">
                        + Package
                    </button>
                </flux:modal.trigger>
            </div>

            <div class="my-6 h-px bg-black/20"></div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div
                    class="rounded-xl p-4 sm:p-6 border border-green-500 bg-emerald-900 text-white h-full flex flex-col">
                    <h2 class="text-base sm:text-lg font-bold tracking-wide">WIFI ACCESS</h2>
                    <div class="mt-3 sm:mt-4 space-y-3 sm:space-y-4 text-sm">
                        <div class="flex gap-3">
                            <div class="mt-1">📍</div>
                            <div>
                                <p class="font-semibold uppercase">Gedung Konservasi</p>
                                <p>Network : <span class="font-semibold">EVENT_5G</span></p>
                                <p>User / Password : <span class="font-semibold">magang-it / kebunraya</span></p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="mt-1">📍</div>
                            <div>
                                <p class="font-semibold uppercase">Kebun Raya</p>
                                <p>Network : <span class="font-semibold">blablabla</span></p>
                                <p>User / Password : <span class="font-semibold">blablalba / blablabla</span></p>
                            </div>
                        </div>
                    </div>
                    <a href="#" class="mt-4 sm:mt-6 self-end text-[11px] sm:text-xs hover:underline">More info »</a>
                </div>

                <div
                    class="rounded-xl p-4 sm:p-6 border border-yellow-500 bg-yellow-700 text-white h-full flex flex-col">
                    <h2 class="text-base sm:text-lg font-bold tracking-wide">NEED HELP?</h2>
                    <div class="mt-3 sm:mt-4 space-y-2.5 sm:space-y-3 text-sm">
                        <div>
                            <p class="font-semibold">Technical Matters</p>
                            <a class="underline break-words" href="mailto:meowmeow@gmail.co">meowmeow@gmail.co</a>
                        </div>
                        <div>
                            <p class="font-semibold">Other Problems</p>
                            <a class="underline break-words" href="mailto:meowmeow@gmail.co">meowmeow@gmail.co</a>
                        </div>
                    </div>
                    <a href="#" class="mt-4 sm:mt-6 self-end text-[11px] sm:text-xs hover:underline">More info »</a>
                </div>

                <div class="rounded-xl p-4 sm:p-6 border border-red-500 bg-red-900 text-white h-full flex flex-col">
                    <h2 class="text-base sm:text-lg font-bold tracking-wide">BUG REPORTING</h2>
                    <div class="mt-3 sm:mt-4 space-y-2.5 sm:space-y-3 text-sm">
                        <div>
                            <p class="font-semibold">KRBS</p>
                            <a class="underline break-words" href="mailto:meowmeow@gmail.co">meowmeow@gmail.co</a>
                        </div>
                        <div>
                            <p class="font-semibold">Lost and Found</p>
                            <a class="underline break-words" href="mailto:meowmeow@gmail.co">meowmeow@gmail.co</a>
                        </div>
                    </div>
                    <a href="#" class="mt-4 sm:mt-6 self-end text-[11px] sm:text-xs hover:underline">More info »</a>
                </div>
            </div>
        </div>


        <flux:modal name="new-ticket" variant="flyout" class="text-black">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Create Support Ticket</flux:heading>
                    <flux:text class="mt-2">Fill out the form below to submit a new support ticket.</flux:text>
                </div>

                <form method="POST" enctype="multipart/form-data" id="new-ticket-form" class="space-y-4 text-black
            [&_label]:text-black
            [&_input]:text-black [&_input]:bg-white
            [&_textarea]:text-black [&_textarea]:bg-white
            [&_select]:text-black [&_select]:bg-white
            [&_input::placeholder]:text-gray-600
            [&_textarea::placeholder]:text-gray-600">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input label="Subject" name="subject" placeholder="Enter ticket subject" />

                        <div>
                            <label class="block text-sm font-medium mb-2">Priority</label>
                            <select name="priority" class="w-full px-3 py-2 border border-black rounded-md
                        focus:outline-none focus:ring-2 focus:ring-black focus:border-black">
                                <option value="" class="text-gray-500">Select priority</option>
                                <option value="LOW">Low</option>
                                <option value="MEDIUM">Medium</option>
                                <option value="HIGH">High</option>
                                <option value="CRITICAL">Critical</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Company</label>
                            <select name="company_id" class="w-full px-3 py-2 border border-black rounded-md
                        focus:outline-none focus:ring-2 focus:ring-black focus:border-black">
                                <option value="" class="text-gray-500">Select company</option>
                                <option value="1">Tech Solutions Ltd</option>
                                <option value="2">Digital Marketing Co</option>
                                <option value="3">Healthcare Systems</option>
                                <option value="4">Finance Corp</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Department</label>
                            <select name="department_id" class="w-full px-3 py-2 border border-black rounded-md
                        focus:outline-none focus:ring-2 focus:ring-black focus:border-black">
                                <option value="" class="text-gray-500">Select department</option>
                                <option value="1">IT Support</option>
                                <option value="2">Human Resources</option>
                                <option value="3">Finance</option>
                                <option value="4">Operations</option>
                                <option value="5">Marketing</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Assigned User</label>
                            <select name="assigned_user_id" class="w-full px-3 py-2 border border-black rounded-md
                        focus:outline-none focus:ring-2 focus:ring-black focus:border-black">
                                <option value="" class="text-gray-500">Select user</option>
                                <option value="1">Haikal - IT Admin</option>
                                <option value="2">Clan - HR Manager</option>
                                <option value="3">Sam - Finance Lead</option>
                                <option value="4">Yusuf - Support Specialist</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Status</label>
                            <select name="status" class="w-full px-3 py-2 border border-black rounded-md
                        focus:outline-none focus:ring-2 focus:ring-black focus:border-black">
                                <option value="PENDING">Pending</option>
                                <option value="PROCESS">In Process</option>
                                <option value="COMPLETE">Complete</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Description</label>
                        <textarea name="description" rows="5" placeholder="Describe your issue in detail..." class="w-full px-3 py-2 border border-black rounded-md
                        focus:outline-none focus:ring-2 focus:ring-black resize-y"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Attachments</label>
                        <div class="border-2 border-dashed border-black rounded-md p-4 text-center">
                            <input id="file-upload" type="file" name="attachments[]" multiple
                                accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" class="hidden">
                            <label for="file-upload" class="cursor-pointer">
                                <div>
                                    <p class="text-sm text-black">Click to upload files or drag and drop</p>
                                    <p class="text-xs text-gray-600 mt-1">PNG, JPG, PDF, DOC up to 10MB</p>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="button"
                            class="px-6 py-2 border border-black text-black rounded-md hover:bg-gray-100 transition-colors">
                            Cancel
                        </button>
                        <flux:spacer />
                        <flux:button type="submit" variant="primary">
                            Submit Ticket
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>


        <flux:modal name="booking-room" variant="flyout">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">New Room Booking</flux:heading>
                    <flux:text class="mt-2">Fill in the details to request a room.</flux:text>
                </div>
                <form method="POST" class="space-y-4 text-black [&_input::placeholder]:text-black">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input label="Title" name="title" placeholder="e.g., Weekly Standup" />
                        <div>
                            <label class="block text-sm font-medium mb-2">Room</label>
                            <select name="room_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900">
                                <option value="">Select room</option>
                                <option value="1">Auditorium</option>
                                <option value="2">Meeting Room A</option>
                                <option value="3">Meeting Room B</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input label="Date" type="date" name="date" />
                        <div class="grid grid-cols-2 gap-3">
                            <flux:input label="Start" type="time" name="start_time" />
                            <flux:input label="End" type="time" name="end_time" />
                        </div>
                    </div>
                    <flux:input label="Purpose" name="purpose" placeholder="Describe the meeting purpose" />
                    <div class="flex">
                        <flux:spacer />
                        <flux:button type="submit" variant="primary">Request Booking</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

        <flux:modal name="new-package" variant="flyout">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Create Package</flux:heading>
                    <flux:text class="mt-2">Add a new package to the system.</flux:text>
                </div>
                <form method="POST"
                    class="space-y-4 text-black [&_input::placeholder]:text-black [&_textarea::placeholder]:text-black">
                    @csrf
                    <flux:input label="Package Name" name="name" placeholder="e.g., Maintenance Gold" />
                    <flux:input label="Code" name="code" placeholder="PKG-001" />
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:input label="Price" name="price" type="number" step="0.01" placeholder="e.g., 499000" />
                        <div>
                            <label class="block text-sm font-medium mb-2">Status</label>
                            <select name="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900">
                                <option value="ACTIVE">Active</option>
                                <option value="INACTIVE">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <label class="block text-sm font-medium mb-2">Description</label>
                    <textarea name="description" rows="4" placeholder="Brief description..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 resize-y"></textarea>
                    <div class="flex">
                        <flux:spacer />
                        <flux:button type="submit" variant="primary">Save Package</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

    </section>
</div>