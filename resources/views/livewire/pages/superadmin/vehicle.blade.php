{{-- resources/views/livewire/pages/superadmin/vehicle.blade.php --}}
<div class="min-h-screen bg-gray-50">
    @php
        use Illuminate\Support\Str;
        $card = 'bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden';
        $label = 'block text-sm font-semibold text-gray-700 mb-2';
        $input = 'w-full h-10 px-3 rounded-lg border border-gray-300 text-gray-800 placeholder:text-gray-400 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
        $area = 'w-full min-h-[90px] px-3 py-2 rounded-lg border border-gray-300 text-gray-800 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 bg-white transition';
        $btnBlk = 'px-3 py-2 text-xs font-semibold rounded-lg bg-gray-900 text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-gray-900/20 disabled:opacity-60 transition';
        $btnRed = 'px-3 py-2 text-xs font-semibold rounded-lg bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-600/20 disabled:opacity-60 transition';
        $btnLite = 'px-3 py-2 text-xs font-semibold rounded-lg border border-gray-300 bg-white hover:bg-gray-50';
    @endphp

    <div class="max-w-6xl mx-auto p-6 space-y-8">

        {{-- FILTERS --}}
        <div class="{{ $card }}">
            <div class="px-6 py-5 border-b">
                <h1 class="text-xl font-semibold">Vehicles</h1>
                <p class="text-gray-500 text-sm mt-1">Kelola kendaraan per perusahaan.</p>
            </div>
            <div class="p-4 grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                <div class="md:col-span-2">
                    <label class="{{ $label }}">Search</label>
                    <input type="text" wire:model.debounce.500ms="search" class="{{ $input }}"
                        placeholder="Name / plate / year">
                </div>
                <div>
                    <label class="{{ $label }}">Category</label>
                    <select wire:model="categoryFilter" class="{{ $input }}">
                        <option value="">All</option>
                        @foreach($categories as $c)
                            <option value="{{ $c }}">{{ ucfirst($c) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $label }}">Active</label>
                    <select wire:model="activeFilter" class="{{ $input }}">
                        <option value="">All</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="pt-6">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" wire:model="showTrashed" class="rounded border-gray-300">
                        <span class="text-sm text-gray-700">Show trash</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">

            {{-- CREATE CARD --}}
            <div class="{{ $card }}">
                <div class="px-6 py-5 space-y-4">
                    <h3 class="text-lg font-semibold">Create Vehicle</h3>

                    <div>
                        <label class="{{ $label }}">Name</label>
                        <input class="{{ $input }}" wire:model.defer="name">
                        @error('name') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="{{ $label }}">Category</label>
                            <select class="{{ $input }}" wire:model.defer="category">
                                @foreach($categories as $c)
                                    <option value="{{ $c }}">{{ ucfirst($c) }}</option>
                                @endforeach
                            </select>
                            @error('category') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $label }}">Plate Number</label>
                            <input class="{{ $input }}" wire:model.defer="plate_number" placeholder="B 1234 ABC">
                            @error('plate_number') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="{{ $label }}">Year</label>
                            <input class="{{ $input }}" wire:model.defer="year" placeholder="2023">
                            @error('year') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="pt-7">
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox" wire:model.defer="is_active" class="rounded border-gray-300">
                                <span class="text-sm text-gray-700">Active</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="{{ $label }}">Notes</label>
                        <textarea class="{{ $area }}" wire:model.defer="notes" placeholder="Optional"></textarea>
                        @error('notes') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Upload (SIGNED) --}}
                    <div class="space-y-2">
                        <label class="{{ $label }}">Image</label>

                        <div id="drop-create"
                            class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center cursor-pointer hover:bg-gray-50">
                            <input id="file-upload-create" type="file" class="hidden"
                                accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xlsx,.zip">
                            <p class="text-sm text-gray-600">Click or drag a file here (max 10MB)</p>
                        </div>

                        <ul id="preview-list-create" class="text-sm space-y-1 mt-2"></ul>
                        <div id="progwrap-create" class="hidden w-full mt-2">
                            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div id="progress-create" class="h-2 bg-gray-800 transition-all duration-300 w-0"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-600 mt-1">
                                <span id="progpercent-create">0%</span>
                                <span id="progmsg-create">Uploading…</span>
                            </div>
                        </div>

                        <input type="hidden" id="tmp_key_create" value="{{ Str::random(8) }}">
                        <input type="hidden" id="temp_items_json" wire:model.defer="temp_items_json">
                    </div>

                    <div class="pt-2">
                        <button class="{{ $btnBlk }}" wire:click="store"
                            onclick="window.beforeSubmitAttachSyncCreate?.()">
                            Save
                        </button>
                    </div>
                </div>
            </div>

            {{-- LIST CARD --}}
            <div class="{{ $card }}">
                <div class="p-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500">
                                <th class="py-2">Image</th>
                                <th class="py-2">Name</th>
                                <th class="py-2">Plate</th>
                                <th class="py-2">Cat</th>
                                <th class="py-2">Active</th>
                                <th class="py-2 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($rows as $r)
                                <tr wire:key="row-{{ $r->vehicle_id }}">
                                    <td class="py-2">
                                        @if($r->image)
                                            <img src="{{ $r->image }}" class="h-12 w-16 object-cover rounded-lg border"
                                                alt="Vehicle">
                                        @else
                                            <div
                                                class="h-12 w-16 bg-gray-200 rounded-lg grid place-items-center text-xs text-gray-500">
                                                No Image</div>
                                        @endif
                                    </td>
                                    <td class="py-2">{{ $r->name }}</td>
                                    <td class="py-2 font-mono">{{ $r->plate_number }}</td>
                                    <td class="py-2">{{ ucfirst($r->category) }}</td>
                                    <td class="py-2">
                                        <span
                                            class="px-2 py-0.5 rounded text-xs {{ $r->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $r->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="py-2 text-right space-x-2">
                                        @if(!$showTrashed)
                                            <button class="{{ $btnLite }}"
                                                wire:click="openEdit({{ $r->vehicle_id }})">Edit</button>
                                            <button class="{{ $btnRed }}"
                                                wire:click="delete({{ $r->vehicle_id }})">Trash</button>
                                        @else
                                            <button class="{{ $btnLite }}"
                                                wire:click="restore({{ $r->vehicle_id }})">Restore</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-3">
                        {{ $rows->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- EDIT MODAL --}}
        @if($modalEdit)
            <div class="fixed inset-0 bg-black/40 z-40"></div>
            <div class="fixed inset-0 z-50 grid place-items-center p-4">
                <div class="{{ $card }} max-w-xl w-full">
                    <div class="px-6 py-5 space-y-4">
                        <h3 class="text-lg font-semibold">Edit Vehicle</h3>

                        <div>
                            <label class="{{ $label }}">Name</label>
                            <input class="{{ $input }}" wire:model.defer="edit_name">
                            @error('edit_name') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="{{ $label }}">Category</label>
                                <select class="{{ $input }}" wire:model.defer="edit_category">
                                    @foreach($categories as $c)
                                        <option value="{{ $c }}">{{ ucfirst($c) }}</option>
                                    @endforeach
                                </select>
                                @error('edit_category') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="{{ $label }}">Plate Number</label>
                                <input class="{{ $input }}" wire:model.defer="edit_plate_number">
                                @error('edit_plate_number') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="{{ $label }}">Year</label>
                                <input class="{{ $input }}" wire:model.defer="edit_year">
                                @error('edit_year') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="pt-7">
                                <label class="inline-flex items-center gap-2">
                                    <input type="checkbox" wire:model.defer="edit_is_active"
                                        class="rounded border-gray-300">
                                    <span class="text-sm text-gray-700">Active</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="{{ $label }}">Notes</label>
                            <textarea class="{{ $area }}" wire:model.defer="edit_notes"></textarea>
                            @error('edit_notes') <p class="text-rose-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Image Preview Current --}}
                        <div class="space-y-2">
                            <label class="{{ $label }}">Image</label>
                            @if($current_image)
                                <img src="{{ $current_image }}" class="h-16 w-24 object-cover rounded-lg border" alt="Current">
                            @endif

                            <div id="drop-edit"
                                class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center cursor-pointer hover:bg-gray-50 mt-2">
                                <input id="file-upload-edit" type="file" class="hidden"
                                    accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xlsx,.zip">
                                <p class="text-sm text-gray-600">Click or drag a file here (max 10MB)</p>
                            </div>

                            <ul id="preview-list-edit" class="text-sm space-y-1 mt-2"></ul>
                            <div id="progwrap-edit" class="hidden w-full mt-2">
                                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div id="progress-edit" class="h-2 bg-gray-800 transition-all duration-300 w-0"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-600 mt-1">
                                    <span id="progpercent-edit">0%</span>
                                    <span id="progmsg-edit">Uploading…</span>
                                </div>
                            </div>

                            <input type="hidden" id="tmp_key_edit" value="{{ Str::random(8) }}">
                            <input type="hidden" id="edit_temp_items_json" wire:model.defer="edit_temp_items_json">
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button class="{{ $btnLite }}" wire:click="closeEdit">Close</button>
                            <button class="{{ $btnBlk }}" wire:click="update"
                                onclick="window.beforeSubmitAttachSyncEdit?.()">
                                Update
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Cloudinary Uploader (SIGNED) for CREATE + EDIT --}}
    <script>
        (function () {
            const ALLOWED = ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'doc', 'docx', 'xlsx', 'zip'];
            const MAX10 = 10 * 1024 * 1024;

            function initUploader(cfg) {
                const input = document.getElementById(cfg.ids.inputId);
                const dropBox = document.getElementById(cfg.ids.dropId);
                const listEl = document.getElementById(cfg.ids.listId);
                const hidden = document.getElementById(cfg.ids.hiddenId);
                const tmpKeyEl = document.getElementById(cfg.ids.tmpKeyId);

                const progWrap = document.getElementById(cfg.ids.progWrapId);
                const progBar = document.getElementById(cfg.ids.progBarId);
                const progPct = document.getElementById(cfg.ids.progPctId);
                const progMsg = document.getElementById(cfg.ids.progMsgId);

                let tempItems = [];
                try { tempItems = JSON.parse(hidden.value || '[]') } catch (_) { tempItems = []; }

                function setProgress(p, msg) {
                    progWrap?.classList.remove('hidden');
                    const c = Math.max(0, Math.min(100, Math.round(p)));
                    if (progBar) progBar.style.width = c + '%';
                    if (progPct) progPct.textContent = c + '%';
                    if (msg && progMsg) progMsg.textContent = msg;
                    if (c >= 100) setTimeout(() => progWrap?.classList.add('hidden'), 900);
                }
                function hideProgress() { progWrap?.classList.add('hidden'); }
                function humanKB(b) { return (b / 1024).toFixed(1) + ' KB'; }
                function toast(msg) { console.log(msg); }

                // IMPORTANT: bubble so Livewire receives updates
                function syncHidden() {
                    hidden.value = JSON.stringify(tempItems || []);
                    hidden.dispatchEvent(new Event('input', { bubbles: true }));
                    hidden.dispatchEvent(new Event('change', { bubbles: true }));
                }

                function renderList() {
                    if (!listEl) return;
                    listEl.innerHTML = '';
                    if (!tempItems.length) {
                        const li = document.createElement('li');
                        li.className = 'text-gray-500';
                        li.textContent = 'No file.';
                        listEl.appendChild(li);
                        return;
                    }
                    tempItems.forEach(item => {
                        const li = document.createElement('li');
                        li.className = 'flex items-center justify-between';
                        li.innerHTML = `
                          <span>${item.original_filename}
                            <span class="text-xs text-gray-400">(${humanKB(item.bytes)})</span>
                          </span>
                          <button type="button" class="text-red-600 text-xs underline">Remove</button>
                        `;
                        li.querySelector('button').addEventListener('click', async () => {
                            try {
                                await fetch("{{ route('vehicle.attachments.deleteTemp') }}", {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        public_id: item.public_id,
                                        file_type: item.resource_type || ''
                                    })
                                });
                            } catch (_) { }
                            tempItems = tempItems.filter(x => x.public_id !== item.public_id);
                            syncHidden();
                            renderList();
                        });
                        listEl.appendChild(li);
                    });
                }
                renderList();

                dropBox?.addEventListener('click', (e) => {
                    if (!(e.target instanceof HTMLInputElement)) input?.click();
                });
                dropBox?.addEventListener('dragover', e => { e.preventDefault(); dropBox.classList.add('bg-gray-50'); });
                dropBox?.addEventListener('dragleave', () => dropBox.classList.remove('bg-gray-50'));
                dropBox?.addEventListener('drop', async e => {
                    e.preventDefault(); dropBox.classList.remove('bg-gray-50');
                    if (e.dataTransfer?.files?.length) await handleFiles(e.dataTransfer.files);
                });

                input?.addEventListener('change', async (e) => {
                    if (e.target.files?.length) await handleFiles(e.target.files);
                    e.target.value = '';
                });

                async function handleFiles(files) {
                    const tmpKey = tmpKeyEl?.value;
                    for (const f of files) {
                        const ext = (f.name.split('.').pop() || '').toLowerCase();
                        if (!ALLOWED.includes(ext)) { toast('Format tidak diizinkan: ' + f.name); continue; }
                        if (f.size > MAX10) { toast('File >10MB: ' + f.name); continue; }

                        try {
                            setProgress(5, 'Requesting…');

                            // 1) Get SIGNED config
                            const sigResp = await fetch("{{ route('vehicle.attachments.signatureTemp') }}", {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({ tmp_key: tmpKey, filename: f.name, bytes: f.size })
                            });
                            if (!sigResp.ok) {
                                hideProgress();
                                const ej = await sigResp.json().catch(() => ({}));
                                toast(ej.message || 'Failed to get signature');
                                continue;
                            }
                            const sig = await sigResp.json();

                            // 2) Upload to Cloudinary (SIGNED)
                            const fd = new FormData();
                            fd.append('file', f);
                            fd.append('folder', sig.folder);
                            fd.append('upload_preset', sig.upload_preset);
                            fd.append('api_key', sig.api_key);
                            fd.append('timestamp', sig.timestamp);
                            fd.append('signature', sig.signature);

                            const cloudJson = await new Promise((resolve, reject) => {
                                const xhr = new XMLHttpRequest();
                                xhr.open('POST', `https://api.cloudinary.com/v1_1/${sig.cloud_name}/auto/upload`);
                                xhr.upload.onprogress = (evt) => {
                                    if (evt.lengthComputable) setProgress((evt.loaded / evt.total) * 100, 'Uploading…');
                                };
                                xhr.onload = () => (xhr.status >= 200 && xhr.status < 300)
                                    ? resolve(JSON.parse(xhr.responseText))
                                    : reject({ status: xhr.status, body: xhr.responseText });
                                xhr.onerror = () => reject({ status: 0, body: 'network error' });
                                xhr.send(fd);
                            });

                            const item = {
                                public_id: cloudJson.public_id,
                                secure_url: cloudJson.secure_url,
                                bytes: cloudJson.bytes || f.size,
                                resource_type: cloudJson.resource_type,
                                format: cloudJson.format,
                                original_filename: (cloudJson.original_filename || 'file') + (cloudJson.format ? '.' + cloudJson.format : '')
                            };

                            tempItems = [item];
                            syncHidden();   // << important for Livewire
                            renderList();

                            setProgress(100, 'Done');
                        } catch (err) {
                            console.error(err);
                            hideProgress();
                            toast('Upload failed');
                        }
                    }
                }

                window[cfg.beforeSubmitFn] = function () { syncHidden(); };
            }

            // CREATE
            initUploader({
                single: true,
                beforeSubmitFn: 'beforeSubmitAttachSyncCreate',
                ids: {
                    dropId: 'drop-create',
                    inputId: 'file-upload-create',
                    listId: 'preview-list-create',
                    tmpKeyId: 'tmp_key_create',
                    hiddenId: 'temp_items_json',
                    progWrapId: 'progwrap-create',
                    progBarId: 'progress-create',
                    progPctId: 'progpercent-create',
                    progMsgId: 'progmsg-create',
                }
            });

            // EDIT
            initUploader({
                single: true,
                beforeSubmitFn: 'beforeSubmitAttachSyncEdit',
                ids: {
                    dropId: 'drop-edit',
                    inputId: 'file-upload-edit',
                    listId: 'preview-list-edit',
                    tmpKeyId: 'tmp_key_edit',
                    hiddenId: 'edit_temp_items_json',
                    progWrapId: 'progwrap-edit',
                    progBarId: 'progress-edit',
                    progPctId: 'progpercent-edit',
                    progMsgId: 'progmsg-edit',
                }
            });

        })();
    </script>
</div>