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

    @if (session('success'))
        <div class="mb-4 rounded-md border border-green-200 bg-green-50 p-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border-2 border-black/80 shadow-md p-4">
        <div class="flex flex-col lg:flex-row gap-6">
            <div class="flex-1">
                <div class="bg-white rounded-xl border-2 border-black/80 shadow-md p-6">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-2">Create Support Ticket</h2>
                    <p class="text-gray-600 mb-6">Fill out the form below to submit a new support ticket</p>

                    <form class="space-y-6" wire:submit.prevent="save">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Subject</label>
                                <input type="text" wire:model.defer="subject" placeholder="Enter ticket subject"
                                       class="w-full px-3 py-2 text-gray-900 placeholder:text-gray-400 border @error('subject') border-red-500 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                @error('subject') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Priority</label>
                                <select wire:model="priority"
                                        class="w-full px-3 py-2 text-gray-900 border @error('priority') border-red-500 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                    <option value="">Select priority</option>
                                    <option value="LOW">Low</option>
                                    <option value="MEDIUM">Medium</option>
                                    <option value="HIGH">High</option>
                                    <option value="CRITICAL">Critical</option>
                                </select>
                                @error('priority') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Department (your dept)</label>
                                <input type="text"
                                       value="{{ $this->requester_department }}"
                                       readonly
                                       class="w-full px-3 py-2 text-gray-700 bg-gray-100 border border-gray-300 rounded-md">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Assigned to what department</label>
                                <select wire:model="assigned_department_id"
                                        class="w-full px-3 py-2 text-gray-900 border @error('assigned_department_id') border-red-500 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                    <option value="">Select department</option>
                                    @foreach($this->departments as $dept)
                                        <option value="{{ $dept['id'] }}">{{ $dept['department_name'] }}</option>
                                    @endforeach
                                </select>
                                @error('assigned_department_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Description</label>
                            <textarea wire:model.defer="description" rows="6" placeholder="Describe your issue in detail..."
                                      class="w-full px-3 py-2 text-gray-900 placeholder:text-gray-400 border @error('description') border-red-500 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent resize-none"></textarea>
                            @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Attachments</label>
                            <div class="border-2 border-dashed @error('attachments.*') border-red-500 @else border-gray-300 @enderror rounded-md p-4 text-center">
                                <input
                                    type="file"
                                    id="file-upload"
                                    class="hidden"
                                    multiple
                                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                                    wire:model="attachments"
                                >
                                <label for="file-upload" class="cursor-pointer block">
                                    <div class="text-gray-600">
                                        <p class="text-sm">Click to upload files or drag and drop</p>
                                        <p class="text-xs text-gray-500 mt-1">PNG, JPG, PDF, DOC up to 10MB</p>
                                    </div>
                                </label>

                                {{-- Progress upload --}}
                                <div class="mt-3" wire:loading wire:target="attachments">
                                    <p class="text-sm text-gray-600">Uploading files...</p>
                                </div>

                                @if ($this->attachments)
                                    <div class="mt-3 text-left">
                                        <p class="text-sm font-medium text-gray-700 mb-1">Selected files:</p>
                                        <ul class="text-sm text-gray-600 list-disc pl-5 space-y-1">
                                            @foreach ($this->attachments as $f)
                                                <li>{{ $f->getClientOriginalName() }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                            @error('attachments.*') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex space-x-4 pt-4">
                            <a href="{{ route('home') }}"
                               class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="px-6 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 transition-colors"
                                    wire:loading.attr="disabled">
                                <span wire:loading.remove>Submit Ticket</span>
                                <span wire:loading>Submitting...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div><!-- /flex-1 -->
        </div>
    </div>
</div>
