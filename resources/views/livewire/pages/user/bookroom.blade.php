<div class="max-w-6xl mx-auto p-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border-2 border-black p-6">
                <h2 class="text-2xl font-semibold text-gray-900 mb-2">Book a Meeting Room</h2>
                <p class="text-gray-600 mb-6">Fill out the form below to request a room booking</p>

                <form class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Meeting Title</label>
                            <input type="text" placeholder="Enter meeting title"
                                   class="w-full px-3 py-2 text-gray-900 placeholder:text-gray-400 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Room Type</label>
                            <select class="w-full px-3 py-2 border text-gray-900 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                <option>Select room type</option>
                                <option>Conference Room</option>
                                <option>Board Room</option>
                                <option>Meeting Room</option>
                                <option>Training Room</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Date</label>
                            <input type="date"
                                   class="w-full px-3 text-gray-900 py-2 border-2 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Duration</label>
                            <select class="w-full px-3 text-gray-900 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                <option>Select duration</option>
                                <option>30 minutes</option>
                                <option>1 hour</option>
                                <option>1.5 hours</option>
                                <option>2 hours</option>
                                <option>3 hours</option>
                                <option>4+ hours</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Start Time</label>
                            <input type="time"
                                   class="w-full px-3 py-2 border text-gray-900 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Number of Attendees</label>
                            <input type="number" placeholder="0" min="1"
                                   class="w-full px-3 text-gray-900 placeholder:text-gray-400 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-3">Additional Requirements</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                                <span class="text-sm text-gray-900">Projector</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                                <span class="text-sm text-gray-900">Whiteboard</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                                <span class="text-sm text-gray-900">Video Conference</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                                <span class="text-sm text-gray-900">Catering</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">Special Notes</label>
                        <textarea placeholder="Any additional information or special requirements..." rows="4"
                                  class="w-full px-3 py-2 text-gray-900 placeholder:text-gray-400 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent resize-none"></textarea>
                    </div>

                    <div class="flex space-x-4 pt-4">
                        <button type="button"
                                class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                onclick="window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', title: 'Success!', message: 'Ticket created successfully.', duration: 4000 } }))"
                                class="px-6 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 transition-colors">
                            Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border-2 border-black p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Room Availability</h3>

                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-md">
                        <div>
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="font-medium text-gray-900">Conference Room A</span>
                            </div>
                            <p class="text-sm text-gray-600">Capacity: 12 people</p>
                        </div>
                        <span class="text-sm font-medium text-green-700">Available</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-red-50 border border-red-200 rounded-md">
                        <div>
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                <span class="font-medium text-gray-900">Board Room</span>
                            </div>
                            <p class="text-sm text-gray-600">Capacity: 20 people</p>
                        </div>
                        <span class="text-sm font-medium text-red-700">Occupied</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-md">
                        <div>
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="font-medium text-gray-900">Meeting Room B</span>
                            </div>
                            <p class="text-sm text-gray-600">Capacity: 8 people</p>
                        </div>
                        <span class="text-sm font-medium text-green-700">Available</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border-2 border-black p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Bookings</h3>

                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-gray-100 rounded-md flex items-center justify-center flex-shrink-0">
                            <div class="w-2 h-2 bg-gray-600 rounded-full"></div>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Team Standup</h4>
                            <p class="text-sm text-gray-600">Today, 2:00 PM • Conference Room A</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-gray-100 rounded-md flex items-center justify-center flex-shrink-0">
                            <div class="w-2 h-2 bg-gray-600 rounded-full"></div>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Client Presentation</h4>
                            <p class="text-sm text-gray-600">Tomorrow, 10:00 AM • Board Room</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-gray-100 rounded-md flex items-center justify-center flex-shrink-0">
                            <div class="w-2 h-2 bg-gray-600 rounded-full"></div>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Training Session</h4>
                            <p class="text-sm text-gray-600">Dec 15, 9:00 AM • Training Room</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>