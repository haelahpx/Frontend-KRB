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

                    {{-- IMPORTANT: pastikan layout punya <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
                    <form class="space-y-6" wire:submit.prevent="save" onsubmit="return beforeSubmitAttachSync()">
                        @csrf

                        {{-- ===== basic fields (tetap) ===== --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Subject</label>
                                <input
                                    type="text"
                                    wire:model.defer="subject"
                                    placeholder="Enter ticket subject"
                                    class="w-full px-3 py-2 text-gray-900 placeholder:text-gray-400 border @error('subject') border-red-500 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                @error('subject') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Priority</label>
                                <select
                                    wire:model="priority"
                                    class="w-full px-3 py-2 text-gray-900 border @error('priority') border-red-500 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                    <option value="">Select priority</option>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                                @error('priority') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Department (your dept)</label>
                                <input
                                    type="text"
                                    value="{{ $this->requester_department }}"
                                    readonly
                                    class="w-full px-3 py-2 text-gray-700 bg-gray-100 border border-gray-300 rounded-md">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Assigned to what department</label>
                                <select
                                    wire:model="assigned_department_id"
                                    class="w-full px-3 py-2 text-gray-900 border @error('assigned_department_id') border-red-500 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                    <option value="">Select department</option>
                                    @foreach($this->departments as $dept)
                                        <option value="{{ $dept['department_id'] }}">{{ $dept['department_name'] }}</option>
                                    @endforeach
                                </select>
                                @error('assigned_department_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Description</label>
                            <textarea
                                wire:model.defer="description"
                                rows="6"
                                placeholder="Describe your issue in detail..."
                                class="w-full px-3 py-2 text-gray-900 placeholder:text-gray-400 border @error('description') border-red-500 @else border-gray-300 @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent resize-none"></textarea>
                            @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- ====== ATTACHMENTS (Cloudinary TMP) ====== --}}
                        {{-- hidden: TMP key + daftar item TMP (json) terikat ke Livewire --}}
                        @php($uuid = \Illuminate\Support\Str::uuid())
                        <input type="hidden" id="tmp_key" value="{{ $uuid }}">
                        <input type="hidden" id="temp_items_json" name="temp_items_json" value="{{ $temp_items_json }}" wire:model.defer="temp_items_json">

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Attachments</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-md p-4 text-center">
                                {{-- input biasa; JS handle ke Cloudinary --}}
                                <input
                                    type="file"
                                    id="file-upload"
                                    class="hidden"
                                    multiple
                                    accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xlsx,.zip"
                                >
                                <label for="file-upload" class="cursor-pointer block">
                                    <div class="text-gray-600">
                                        <p class="text-sm">Click to upload files or drag and drop</p>
                                        <p class="text-xs text-gray-500 mt-1">PNG, JPG, WEBP, PDF, DOC, DOCX, XLSX, ZIP up to 10MB/file · Total 15MB/ticket</p>
                                    </div>
                                </label>

                                {{-- Progress upload (global) --}}
                                <div id="progwrap" class="hidden mt-3">
                                    <div class="w-full bg-gray-200 rounded h-2 overflow-hidden">
                                        <div id="progress" class="bg-gray-900 h-2" style="width:0%"></div>
                                    </div>
                                    <div class="flex justify-between text-xs mt-1">
                                        <span id="progmsg" class="text-gray-600">Preparing…</span>
                                        <span id="progpercent">0%</span>
                                    </div>
                                </div>

                                {{-- daftar file (preview) --}}
                                <div class="mt-3 text-left">
                                    <p class="text-sm font-medium text-gray-700 mb-1">Selected files:</p>
                                    <ul id="preview-list" class="text-sm text-gray-600 list-disc pl-5 space-y-1">
                                        {{-- diisi oleh JS --}}
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="flex space-x-4 pt-4">
                            <a href="{{ route('home') }}"
                               class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                                Cancel
                            </a>
                            <button
                                type="submit"
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

<script>
(function () {
  const ALLOWED = ['jpg','jpeg','png','webp','pdf','doc','docx','xlsx','zip'];
  const MAX10   = 10 * 1024 * 1024; // 10MB/file

  const tmpKey   = document.getElementById('tmp_key').value;
  const input    = document.getElementById('file-upload');
  const dropBox  = input?.closest('.border-dashed');
  const listEl   = document.getElementById('preview-list');
  const hidden   = document.getElementById('temp_items_json');

  const progWrap = document.getElementById('progwrap');
  const progBar  = document.getElementById('progress');
  const progPct  = document.getElementById('progpercent');
  const progMsg  = document.getElementById('progmsg');

  let tempItems     = JSON.parse(hidden.value || '[]');
  let activeUploads = 0;

  function setProgress(p, msg) {
    progWrap.classList.remove('hidden');
    const c = Math.max(0, Math.min(100, Math.round(p)));
    progBar.style.width = c + '%';
    progPct.textContent = c + '%';
    if (msg) progMsg.textContent = msg;
    if (c >= 100) setTimeout(() => progWrap.classList.add('hidden'), 900);
  }
  function hideProgress(){ progWrap.classList.add('hidden'); }
  function humanKB(b){ return (b/1024).toFixed(1) + ' KB'; }
  function toast(msg){ console.log(msg); } // ganti ke toast kustom kalau ada

  function syncHidden() {
    hidden.value = JSON.stringify(tempItems || []);
    // WAJIB: pakai input + change agar Livewire pasti detect
    hidden.dispatchEvent(new Event('input',  { bubbles: true }));
    hidden.dispatchEvent(new Event('change', { bubbles: true }));
  }

  function renderList() {
    listEl.innerHTML = '';
    if (!tempItems.length) {
      const li = document.createElement('li');
      li.className = 'text-gray-500';
      li.textContent = 'No files yet.';
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
      // OPTIMISTIC REMOVE + sync dulu, baru call API delete
      li.querySelector('button').addEventListener('click', () => {
        tempItems = tempItems.filter(x => x.public_id !== item.public_id);
        syncHidden();
        li.remove();

        fetch('/attachments/temp', {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ public_id: item.public_id, file_type: item.resource_type })
        }).catch(()=>{ /* no-op */ });
      });
      listEl.appendChild(li);
    });
  }
  renderList();

  // open dialog when clicking the box text
  dropBox?.addEventListener('click', (e) => {
    if (!(e.target instanceof HTMLInputElement)) input?.click();
  });

  // drag&drop UX
  dropBox?.addEventListener('dragover', e => { e.preventDefault(); dropBox.classList.add('bg-gray-50'); });
  dropBox?.addEventListener('dragleave', () => dropBox.classList.remove('bg-gray-50'));
  dropBox?.addEventListener('drop', async e => {
    e.preventDefault(); dropBox.classList.remove('bg-gray-50');
    if (e.dataTransfer?.files?.length) await handleFiles(e.dataTransfer.files);
  });

  input?.addEventListener('change', async (e) => {
    if (e.target.files?.length) await handleFiles(e.target.files);
    e.target.value = ''; // reset supaya bisa pilih file yang sama lagi
  });

  async function handleFiles(files) {
    for (const f of files) {
      const ext = (f.name.split('.').pop() || '').toLowerCase();
      if (!ALLOWED.includes(ext)) { toast('Format tidak diizinkan: ' + f.name); continue; }
      if (f.size > MAX10)        { toast('File >10MB: ' + f.name); continue; }

      try {
        setProgress(5, 'Requesting permission…');

        // 1) minta signature TEMP
        const sigResp = await fetch('/attachments/signature-temp', {
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

        // 2) upload Cloudinary TMP (progress)
        const fd = new FormData();
        fd.append('file', f);
        fd.append('api_key', sig.api_key);
        fd.append('timestamp', sig.timestamp);
        fd.append('signature', sig.signature);
        fd.append('upload_preset', sig.upload_preset);
        fd.append('folder', sig.folder);

        activeUploads++;
        const cloudJson = await new Promise((resolve, reject) => {
          const xhr = new XMLHttpRequest();
          xhr.open('POST', `https://api.cloudinary.com/v1_1/${sig.cloud_name}/auto/upload`);
          xhr.upload.onprogress = (evt) => {
            if (evt.lengthComputable) setProgress((evt.loaded / evt.total) * 100, 'Uploading…');
          };
          xhr.onload  = () => (xhr.status >= 200 && xhr.status < 300)
                          ? resolve(JSON.parse(xhr.responseText))
                          : reject({ status: xhr.status, body: xhr.responseText });
          xhr.onerror = () => reject({ status: 0, body: 'network error' });
          xhr.send(fd);
        }).finally(() => { activeUploads--; });

        // 3) add to list & sinkron ke Livewire
        const item = {
          public_id: cloudJson.public_id,
          secure_url: cloudJson.secure_url,
          bytes: cloudJson.bytes,
          resource_type: cloudJson.resource_type,
          format: cloudJson.format,
          original_filename: (cloudJson.original_filename || 'file') + (cloudJson.format ? '.' + cloudJson.format : '')
        };
        tempItems.push(item);
        syncHidden();
        renderList();

        setProgress(100, 'Done');
      } catch (err) {
        console.error(err);
        activeUploads = Math.max(0, activeUploads - 1);
        hideProgress();           // jangan nyangkut di 5%
        toast('Upload failed');
      }
    }
  }

  // dipanggil saat submit form (Livewire) — pastikan nilai terbaru terkirim
  window.beforeSubmitAttachSync = function () {
    if (activeUploads > 0) {
      alert('Masih ada upload yang berjalan. Tunggu selesai dulu ya.');
      return false;
    }
    syncHidden();
    // Debug (opsional): lihat payload final yang terkirim ke Livewire
    console.debug('Final payload temp_items_json:', hidden.value);
    return true; // izinkan submit
  };
})();
</script>
