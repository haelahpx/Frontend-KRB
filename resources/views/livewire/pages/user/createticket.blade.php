<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Ticket Support</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <div class="max-w-6xl mx-auto p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-2">Create Support Ticket</h2>
                    <p class="text-gray-600 mb-6">Fill out the form below to submit a new support ticket</p>

                    <form class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                                <input type="text" placeholder="Enter ticket subject"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
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
                                <label class="block text-sm font-medium text-gray-700 mb-2">Company</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                    <option>Select company</option>
                                    <option value="1">Tech Solutions Ltd</option>
                                    <option value="2">Digital Marketing Co</option>
                                    <option value="3">Healthcare Systems</option>
                                    <option value="4">Finance Corp</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                    <option>Select department</option>
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
                                <label class="block text-sm font-medium text-gray-700 mb-2">Assigned User</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                    <option>Select user</option>
                                    <option value="1">John Smith - IT Admin</option>
                                    <option value="2">Sarah Johnson - HR Manager</option>
                                    <option value="3">Mike Davis - Finance Lead</option>
                                    <option value="4">Lisa Chen - Support Specialist</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                    <option value="PENDING" selected>Pending</option>
                                    <option value="PROCESS">In Process</option>
                                    <option value="COMPLETE">Complete</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea placeholder="Describe your issue in detail..." rows="6"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent resize-none"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Attachments</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-md p-4 text-center">
                                <input type="file" multiple accept=".jpg,.png,.pdf,.doc,.docx" class="hidden" id="file-upload">
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
                                class="px-6 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 transition-colors">
                                Submit Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Ticket Statistics</h3>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                            <div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                                    <span class="font-medium text-gray-900">Pending Tickets</span>
                                </div>
                            </div>
                            <span class="text-sm font-medium text-yellow-700">12</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-md">
                            <div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                    <span class="font-medium text-gray-900">In Process</span>
                                </div>
                            </div>
                            <span class="text-sm font-medium text-blue-700">8</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-md">
                            <div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                    <span class="font-medium text-gray-900">Completed</span>
                                </div>
                            </div>
                            <span class="text-sm font-medium text-green-700">45</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-red-50 border border-red-200 rounded-md">
                            <div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                    <span class="font-medium text-gray-900">Critical Priority</span>
                                </div>
                            </div>
                            <span class="text-sm font-medium text-red-700">3</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Tickets</h3>

                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-red-100 rounded-md flex items-center justify-center flex-shrink-0">
                                <div class="w-2 h-2 bg-red-600 rounded-full"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-900 truncate">Server Downtime Issue</h4>
                                <p class="text-sm text-gray-600">Critical • IT Support</p>
                                <p class="text-xs text-gray-500 mt-1">#TCK-2024-001</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center flex-shrink-0">
                                <div class="w-2 h-2 bg-yellow-600 rounded-full"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-900 truncate">Login Authentication Error</h4>
                                <p class="text-sm text-gray-600">High • IT Support</p>
                                <p class="text-xs text-gray-500 mt-1">#TCK-2024-002</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center flex-shrink-0">
                                <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-900 truncate">Email Setup Request</h4>
                                <p class="text-sm text-gray-600">Medium • Human Resources</p>
                                <p class="text-xs text-gray-500 mt-1">#TCK-2024-003</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center flex-shrink-0">
                                <div class="w-2 h-2 bg-green-600 rounded-full"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-900 truncate">Software License Query</h4>
                                <p class="text-sm text-gray-600">Low • Finance</p>
                                <p class="text-xs text-gray-500 mt-1">#TCK-2024-004</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>

                    <div class="space-y-2">
                        <button class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md transition-colors">
                            View All Tickets
                        </button>
                        <button class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md transition-colors">
                            My Assigned Tickets
                        </button>
                        <button class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md transition-colors">
                            Critical Priority Queue
                        </button>
                        <button class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md transition-colors">
                            Generate Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>