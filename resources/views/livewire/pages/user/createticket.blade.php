<div class="max-w-6xl mx-auto">
  <div class="bg-white rounded-lg shadow-sm border-2 border-black p-6 mb-8">
    <div class="flex items-center justify-between">
      <h1 class="text-3xl font-bold text-gray-900">Support Ticket System</h1>
      <div class="inline-flex rounded-md overflow-hidden bg-gray-100 border border-gray-200">
        <span class="px-4 py-2 text-sm font-medium bg-gray-900 text-white select-none">Create Ticket</span>
        <a href="{{ route('ticketstatus') }}"
          class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">Ticket Status</a>
      </div>
    </div>
  </div>

  @if (session('success'))
    <div class="mb-4 rounded-md border border-green-200 bg-green-50 p-3 text-green-800">{{ session('success') }}</div>
  @endif

  <div class="bg-white rounded-xl border-2 border-black/80 shadow-md p-4">
    <div class="flex flex-col lg:flex-row gap-6">
      <div class="flex-1">
        <div class="bg-white rounded-xl border-2 border-black/80 shadow-md p-6">
          <h2 class="text-2xl font-semibold text-gray-900 mb-2">Create Support Ticket</h2>
          <p class="text-gray-600 mb-6">Fill out the form below to submit a new support ticket</p>

          {{-- layout must include:
          <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
          <form class="space-y-6" wire:submit.prevent="save" onsubmit="return beforeSubmitAttachSync()">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-900 mb-2">Subject</label>
                <input type="text" wire:model.defer="subject" placeholder="Enter ticket subject"
                  class="w-full px-3 py-2 text-gray-900 placeholder:text-gray-400 border rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900" />
                @error('subject') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-900 mb-2">Priority</label>
                <select wire:model="priority" class="w-full px-3 py-2 text-gray-900 border rounded-md">
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
                <input type="text" value="{{ $this->requester_department }}" readonly
                  class="w-full px-3 py-2 text-gray-700 bg-gray-100 border rounded-md" />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-900 mb-2">Assigned to what department</label>
                <select wire:model="assigned_department_id" class="w-full px-3 py-2 text-gray-900 border rounded-md">
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
              <textarea wire:model.defer="description" rows="6" placeholder="Describe your issue in detail..."
                class="w-full px-3 py-2 text-gray-900 placeholder:text-gray-400 border rounded-md"></textarea>
              @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- TEMP upload hidden + preview --}}
            @php($uuid = \Illuminate\Support\Str::uuid())
            <input type="hidden" id="tmp_key" value="{{ $uuid }}">
            <input type="hidden" id="temp_items_json" name="temp_items_json" value="{{ $temp_items_json }}"
              wire:model.defer="temp_items_json">

            <div>
              <label class="block text-sm font-medium text-gray-900 mb-2">Attachments</label>
              <div class="border-2 border-dashed border-gray-300 rounded-md p-4 text-center">
                <input type="file" id="file-upload" class="hidden" multiple
                  accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xlsx,.zip">
                <label for="file-upload" class="cursor-pointer block">
                  <div class="text-gray-600">
                    <p class="text-sm">Click to upload files or drag and drop</p>
                    <p class="text-xs text-gray-500 mt-1">PNG, JPG, WEBP, PDF, DOC, DOCX, XLSX, ZIP up to
                      {{ $per_file_max_mb }}MB/file · Total {{ $total_quota_mb }}MB/ticket
                    </p>
                  </div>
                </label>

                {{-- progress --}}
                <div id="progwrap" class="hidden mt-3">
                  <div class="w-full bg-gray-200 rounded h-2 overflow-hidden">
                    <div id="progress" class="bg-gray-900 h-2" style="width:0%"></div>
                  </div>
                  <div class="flex justify-between text-xs mt-1">
                    <span id="progmsg" class="text-gray-600">Preparing…</span>
                    <span id="progpercent">0%</span>
                  </div>
                </div>

                {{-- preview list --}}
                <div class="mt-3 text-left">
                  <p class="text-sm font-medium text-gray-700 mb-1">Selected files:</p>
                  <ul id="preview-list" class="text-sm text-gray-600 list-disc pl-5 space-y-1">
                    {{-- JS will fill --}}
                  </ul>
                </div>
              </div>
              @error('temp_items_json') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
              @if($errors->has('attachments'))
                <p class="text-red-600 text-sm mt-1">{{ $errors->first('attachments') }}</p>
              @endif
            </div>

            <div class="flex space-x-4 pt-4">
              <a href="{{ route('home') }}"
                class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">Cancel</a>
              <button type="submit"
                class="px-6 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 transition-colors"
                wire:loading.attr="disabled">
                <span wire:loading.remove>Submit Ticket</span>
                <span wire:loading>Submitting...</span>
              </button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>

{{-- ===== client JS: upload to local temp endpoints ===== --}}
<script>
  (function () {
    const ALLOWED = ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'doc', 'docx', 'xlsx', 'zip'];
    const MAX10 = 10 * 1024 * 1024; // 10MB/file

    const tmpKey = document.getElementById('tmp_key').value;
    const input = document.getElementById('file-upload');
    const listEl = document.getElementById('preview-list');
    const hidden = document.getElementById('temp_items_json');
    const progWrap = document.getElementById('progwrap');
    const progBar = document.getElementById('progress');
    const progPct = document.getElementById('progpercent');
    const progMsg = document.getElementById('progmsg');

    let tempItems = JSON.parse(hidden.value || '[]');

    function setProgress(p, msg) {
      if (!progWrap) return;
      progWrap.classList.remove('hidden');
      const c = Math.max(0, Math.min(100, Math.round(p)));
      progBar.style.width = c + '%';
      progPct.textContent = c + '%';
      if (msg) progMsg.textContent = msg;
      if (c >= 100) setTimeout(() => progWrap.classList.add('hidden'), 900);
    }

    function hideProgress() {
      if (!progWrap) return;
      progWrap.classList.add('hidden');
    }

    function syncHidden() {
      hidden.value = JSON.stringify(tempItems || []);
      // Livewire: make sure change detected
      hidden.dispatchEvent(new Event('input', { bubbles: true }));
      hidden.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function humanKB(b) {
      return (b / 1024).toFixed(1) + ' KB';
    }

    function renderList() {
      listEl.innerHTML = '';
      if (!tempItems.length) {
        listEl.innerHTML = '<li class="text-gray-500">No files yet.</li>';
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
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            await fetch('/attachments/temp', {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': csrf,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
              },
              body: JSON.stringify({ public_id: item.public_id })
            });
          } catch (e) {
            console.warn('Delete temp failed', e);
          }
          tempItems = tempItems.filter(x => x.public_id !== item.public_id);
          syncHidden();
          renderList();
        });
        listEl.appendChild(li);
      });
    }

    async function uploadSingle(file) {
      return new Promise((resolve, reject) => {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrf) return reject(new Error('CSRF token not found'));

        const fd = new FormData();
        fd.append('file', file);
        fd.append('tmp_key', tmpKey);

        // also include _token in formdata for maximum compatibility
        fd.append('_token', csrf);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/attachments/temp');

        // also set header (some servers/middleware expect header)
        try { xhr.setRequestHeader('X-CSRF-TOKEN', csrf); } catch (e) { /* some browsers ignore when sending FormData */ }

        xhr.upload.onprogress = (evt) => {
          if (evt.lengthComputable) setProgress((evt.loaded / evt.total) * 100, `Uploading ${file.name}…`);
        };

        xhr.onload = () => {
          // handle http status
          if (xhr.status >= 200 && xhr.status < 300) {
            try {
              const json = JSON.parse(xhr.responseText);
              resolve(json);
            } catch (e) {
              console.error('Invalid JSON response', xhr.responseText);
              reject(new Error('Invalid server response'));
            }
          } else if (xhr.status === 419) {
            // CSRF/session expired
            // show clear message to user
            alert('Session expired (CSRF). Silakan refresh halaman dan login lagi.');
            reject(new Error('CSRF / Session expired (419)'));
          } else {
            console.error('Upload failed', xhr.status, xhr.responseText);
            reject(new Error('Upload failed: ' + xhr.status));
          }
        };

        xhr.onerror = () => reject(new Error('Network error during upload'));
        xhr.send(fd);
      });
    }

    input?.addEventListener('change', async (e) => {
      const files = Array.from(e.target.files || []);
      if (!files.length) return;

      // sequential upload (easier to reason about)
      for (const f of files) {
        const ext = (f.name.split('.').pop() || '').toLowerCase();
        if (!ALLOWED.includes(ext)) {
          console.warn('Format not allowed:', f.name);
          continue;
        }
        if (f.size > MAX10) {
          console.warn('File too large:', f.name);
          continue;
        }

        try {
          setProgress(5, 'Requesting upload…');
          const data = await uploadSingle(f);
          // expected response: object with public_id, secure_url, bytes, resource_type, format, original_filename
          if (data && data.public_id) {
            const item = {
              public_id: data.public_id,
              secure_url: data.secure_url || data.url || null,
              bytes: data.bytes || f.size,
              resource_type: data.resource_type || data.type || null,
              format: data.format || null,
              original_filename: data.original_filename || f.name
            };
            tempItems.push(item);
            syncHidden();
            renderList();
            setProgress(100, 'Done');
          } else {
            console.warn('Upload succeeded but response missing public_id', data);
          }
        } catch (err) {
          console.error('Upload error', err);
          // helpful guidance for common cause
          if (String(err).includes('CSRF') || String(err).includes('419')) {
            // already alerted inside uploadSingle
          } else {
            alert('Upload failed: ' + (err.message || err));
          }
          hideProgress();
        }
      }

      // reset input so same file can be chosen again later
      e.target.value = '';
    });

    // called before form submit
    window.beforeSubmitAttachSync = function () {
      // ensure hidden field up-to-date
      syncHidden();
      return true;
    };

    renderList();
  })();
</script>