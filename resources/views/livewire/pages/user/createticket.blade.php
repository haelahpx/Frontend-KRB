<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border-2 border-black p-6 mb-8">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900">Support Ticket System</h1>
            <div class="inline-flex rounded-md overflow-hidden bg-gray-100 border border-gray-200">
                <span class="px-4 py-2 text-sm font-medium bg-gray-900 text-white select-none">
                    Create Ticket
                </span>
                <a href="{{ route('ticketstatus') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">
                    Ticket Status
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border-2 border-black/80 shadow-md p-4">
        <div class="flex flex-col lg:flex-row gap-6">
            <div class="flex-1">
                <div class="bg-white rounded-xl border-2 border-black/80 shadow-md p-6">
                <h2 class="text-2xl font-semibold text-gray-900 mb-2">Create Support Ticket</h2>
                <p class="text-gray-600 mb-6">Fill out the form below to submit a new support ticket</p>

                <form class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Subject</label>
                            <input type="text" placeholder="Enter ticket subject"
                                class="w-full px-3 py-2 text-gray-900 placeholder:text-gray-400 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Priority</label>
                            <select
                                class="w-full px-3 py-2 text-gray-900 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                <option>Select priority</option>
                                <option value="LOW">Low</option>
                                <option value="MEDIUM">Medium</option>
                                <option value="HIGH">High</option>
                                <option value="CRITICAL">Critical</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Department</label>
                            <select
                                class="w-full px-3 py-2 text-gray-900 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                <option>Select department</option>
                                <option value="1">IT Support</option>
                                <option value="2">Human Resources</option>
                                <option value="3">Finance</option>
                                <option value="4">Operations</option>
                                <option value="5">Marketing</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Assigned to what
                                department</label>
                            <select
                                class="w-full px-3 py-2 text-gray-900 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                <option>Select user</option>
                                <option value="1">IT Support</option>
                                <option value="2">Human Resources</option>
                                <option value="3">Finance</option>
                                <option value="4">Operations</option>
                                <option value="5">Marketing</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">Description</label>
                        <textarea placeholder="Describe your issue in detail..." rows="6"
                            class="w-full px-3 py-2 text-gray-900 placeholder:text-gray-400 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent resize-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">Attachments</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-md p-4 text-center">
                            <input type="file" multiple accept=".jpg,.png,.pdf,.doc,.docx" class="hidden"
                                id="file-upload">
                            <label for="file-upload" class="cursor-pointer">
                                <div class="text-gray-600">
                                    <p class="text-sm">Click to upload files or drag and drop</p>
                                    <p class="text-xs text-gray-500 mt-1">PNG, JPG, PDF, DOC up to 10MB</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="flex space-x-4 pt-4">
                        <button type="button"
                            class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            onclick="window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', title: 'Success!', message: 'Ticket created successfully.', duration: 4000 } }))"
                            class="px-6 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 transition-colors">
                            Submit Ticket
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>
</div>