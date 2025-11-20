<div class="max-w-7xl mx-auto">
  {{-- Header --}}
  <div class="bg-white rounded-xl shadow-sm border-2 border-black p-4 md:p-6 mb-4 md:mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <h1 class="text-xl md:text-2xl font-bold text-gray-900">Support Ticket System</h1>

      {{-- Navigation Tabs --}}
      <div class="inline-flex rounded-lg overflow-hidden bg-gray-100 border border-gray-200 self-start md:self-center">
        <span class="px-3 md:px-4 py-2 text-sm font-medium bg-gray-900 text-white cursor-default border-r border-gray-200">
          Create Ticket
        </span>
        <a href="{{ route('ticketstatus') }}"
          class="px-3 md:px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 transition-colors">
          Ticket Status
        </a>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
      <div class="bg-white rounded-xl shadow-sm border-2 border-black p-4 md:p-5">
        <h2 class="text-lg font-semibold text-gray-900 mb-2">Create Support Ticket</h2>
        <p class="text-sm text-gray-600 mb-6">Fill out the form below to submit a new support ticket.</p>

        <form class="space-y-5" wire:submit.prevent="save" onsubmit="return beforeSubmitAttachSync()">
          @csrf

          {{-- Inputs: Subject & Priority --}}
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium text-gray-900 mb-1.5">Subject</label>
              <input type="text" wire:model.defer="subject" placeholder="Enter ticket subject"
                class="w-full px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent" />
              @error('subject') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="block text-xs font-medium text-gray-900 mb-1.5">Priority</label>
              <select wire:model="priority"
                class="w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
              </select>
              @error('priority') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
          </div>

          {{-- Inputs: Dept & Assigned --}}
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium text-gray-900 mb-1.5">Department (Your Dept)</label>
              <input type="text" value="{{ $this->requester_department }}" readonly
                class="w-full px-3 py-2 text-sm text-gray-500 bg-gray-100 border border-gray-200 rounded-md cursor-not-allowed" />
            </div>

            <div>
              <label class="block text-xs font-medium text-gray-900 mb-1.5">Assigned To</label>
              <select wire:model="assigned_department_id"
                class="w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                <option value="" selected>Select department</option>
                @foreach($this->departments as $dept)
                <option value="{{ $dept['department_id'] }}">{{ $dept['department_name'] }}</option>
                @endforeach
              </select>
              @error('assigned_department_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
          </div>

          <div>
            <label class="block text-xs font-medium text-gray-900 mb-1.5">Description</label>
            <textarea wire:model.defer="description" rows="6" placeholder="Describe your issue in detail..."
              class="w-full px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent resize-none"></textarea>
            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
          </div>

          {{-- ATTACHMENTS SECTION --}}
          @php($uuid = \Illuminate\Support\Str::uuid())
          <input type="hidden" id="tmp_key" value="{{ $uuid }}">
          <input type="hidden" id="temp_items_json" name="temp_items_json" value="{{ $temp_items_json }}"
            wire:model.defer="temp_items_json">

          <div>
            <label class="block text-xs font-medium text-gray-900 mb-1.5">Attachments</label>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:bg-gray-50 transition-colors">

              {{-- Hidden Inputs --}}
              <input type="file" id="file-upload" class="hidden" multiple accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xlsx,.zip">
              <input type="file" id="mobile-camera-upload" class="hidden" accept="image/*" capture="environment">

              {{-- ACTION BUTTONS --}}
              <div class="flex flex-col md:flex-row justify-center items-center gap-4 py-4">

                {{-- 1. Upload Files --}}
                <label for="file-upload" class="cursor-pointer group flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-gray-100 transition border border-transparent hover:border-gray-200 w-32">
                  <div class="p-3 bg-gray-100 rounded-full group-hover:bg-white border border-gray-200 shadow-sm transition">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                  </div>
                  <span class="text-xs font-bold text-gray-900">Upload Files</span>
                </label>

                {{-- 2. Mobile Camera (Hidden on Desktop, Visible on Mobile) --}}
                <label for="mobile-camera-upload" class="md:hidden cursor-pointer group flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-gray-100 transition border border-transparent hover:border-gray-200 w-32">
                  <div class="p-3 bg-gray-100 rounded-full group-hover:bg-white border border-gray-200 shadow-sm transition">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                  </div>
                  <span class="text-xs font-bold text-gray-900">Take Photo</span>
                </label>

                {{-- 3. Desktop Webcam (Hidden on Mobile, Visible on Desktop) --}}
                <button type="button" id="btn-webcam-open" class="hidden md:flex cursor-pointer group flex-col items-center gap-2 p-3 rounded-xl hover:bg-gray-100 transition border border-transparent hover:border-gray-200 w-32">
                  <div class="p-3 bg-gray-100 rounded-full group-hover:bg-white border border-gray-200 shadow-sm transition">
                    {{-- Webcam Icon --}}
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                  </div>
                  <span class="text-xs font-bold text-gray-900">Webcam (PC)</span>
                </button>

              </div>

              <p class="text-[10px] text-gray-400">Max {{ $per_file_max_mb }}MB/file. Total {{ $total_quota_mb }}MB.</p>

              {{-- progress --}}
              <div id="progwrap" class="hidden mt-4 max-w-md mx-auto">
                <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                  <div id="progress" class="bg-gray-900 h-1.5 transition-all duration-300" style="width:0%"></div>
                </div>
                <div class="flex justify-between text-[10px] mt-1">
                  <span id="progmsg" class="text-gray-600">Preparing…</span>
                  <span id="progpercent" class="font-medium text-gray-900">0%</span>
                </div>
              </div>

              {{-- preview list --}}
              <div class="mt-4 text-left max-w-2xl mx-auto">
                <p class="text-xs font-semibold text-gray-900 mb-2">Selected files:</p>
                <ul id="preview-list" class="text-xs text-gray-600 space-y-2 border-t border-gray-100 pt-2">
                  {{-- JS will fill --}}
                </ul>
              </div>
            </div>
            @error('temp_items_json') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @if($errors->has('attachments'))
            <p class="text-red-500 text-xs mt-1">{{ $errors->first('attachments') }}</p>
            @endif
          </div>

          <div class="flex gap-4 pt-4 border-t border-gray-100">
            <a href="{{ route('home') }}"
              class="px-4 py-2 text-sm font-medium border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
              Cancel
            </a>
            <button type="submit"
              class="px-4 py-2 text-sm font-medium bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors"
              wire:loading.attr="disabled">
              <span wire:loading.remove>Submit Ticket</span>
              <span wire:loading>Submitting...</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">

      {{-- Card 1: Help Tips (Existing) --}}
      <div class="bg-blue-50 rounded-xl shadow-sm border-2 border-black p-4 md:p-5">
        <div class="flex items-center gap-2 mb-3">
          <div class="p-1.5 bg-yellow-100 rounded-lg border border-yellow-200">
            <svg class="w-4 h-4 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <h3 class="text-lg font-bold text-gray-900">Help Tips</h3>
        </div>
        <ul class="text-xs md:text-sm text-gray-700 list-disc pl-5 space-y-2">
          <li>Berikan deskripsi masalah yang jelas.</li>
          <li>Gunakan "Take Photo" di HP atau "Webcam" di Laptop untuk bukti visual.</li>
          <li>Upload screenshot atau log error jika ada.</li>
        </ul>
      </div>

      {{-- Card 2: Contact Support (New) --}}
      <div class="bg-white rounded-xl shadow-sm border-2 border-black p-4 md:p-5">
        <h3 class="text-lg font-bold text-gray-900 mb-3">Need Immediate Help?</h3>
        <p class="text-xs text-gray-600 mb-4">For critical issues preventing business operations, please contact us directly:</p>

        <div class="space-y-3">
          <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-100">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
            </svg>
            <div>
              <p class="text-xs font-semibold text-gray-500">Hotline IT</p>
              <p class="text-sm font-bold text-gray-900">Ext. 1005</p>
            </div>
          </div>

          <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-100">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v9a2 2 0 002 2z"></path>
            </svg>
            <div>
              <p class="text-xs font-semibold text-gray-500">Email Support</p>
              <p class="text-sm font-bold text-gray-900">it.support@company.com</p>
            </div>
          </div>
        </div>
      </div>

      {{-- Card 3: Operational Hours (New) --}}
      <div class="bg-white rounded-xl shadow-sm border-2 border-black p-4 md:p-5">
        <h3 class="text-lg font-bold mb-2">Operational Hours</h3>
        <p class="text-xs text-gray-400 mb-4">Our standard response time is within 24 hours during working days.</p>

        <div class="space-y-2 text-sm">
          <div class="flex justify-between border-b border-gray-800 pb-2">
            <span class="text-gray-400">Mon - Fri</span>
            <span class="font-medium">08:00 - 17:00</span>
          </div>
          <div class="flex justify-between border-b border-gray-800 pb-2">
            <span class="text-gray-400">Saturday</span>
            <span class="font-medium">08:00 - 13:00</span>
          </div>
          <div class="flex justify-between pt-1">
            <span class="text-gray-400">Sunday</span>
            <span class="text-red-400 font-medium">Closed</span>
          </div>
        </div>
      </div>

    </div>
  </div>

  {{-- ====== WEBCAM MODAL (Hidden by default) ====== --}}
  <div id="webcam-modal" class="fixed inset-0 bg-black/80 z-[9999] items-center justify-center p-4 hidden">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full overflow-hidden relative">
      {{-- Modal Header --}}
      <div class="bg-gray-900 text-white px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
          <span class="text-sm font-medium">Webcam Live</span>
        </div>
        <button type="button" id="btn-webcam-close" class="text-white hover:text-gray-300">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      {{-- Video Area --}}
      <div class="p-4 space-y-4">
        <div class="bg-black rounded-lg overflow-hidden flex items-center justify-center relative aspect-video">
          <video id="webcam-video" autoplay playsinline class="w-full h-full object-cover transform scale-x-[-1]"></video>
        </div>
        <div class="flex items-center justify-between">
          <span class="text-xs text-gray-500">Pastikan izin kamera aktif.</span>
          <button type="button" id="btn-webcam-capture" class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-medium hover:bg-black flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <circle cx="12" cy="12" r="3"></circle>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h2l2-3h10l2 3h2v10H3V7z"></path>
            </svg>
            Capture
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ===== CLIENT JS: Upload + Webcam Logic ===== --}}
<script>
  (function() {
    const ALLOWED = ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'doc', 'docx', 'xlsx', 'zip'];
    const MAX10 = 10 * 1024 * 1024;
    const tmpKey = document.getElementById('tmp_key').value;

    // Main Elements
    const fileInput = document.getElementById('file-upload');
    const mobileCamInput = document.getElementById('mobile-camera-upload');
    const listEl = document.getElementById('preview-list');
    const hidden = document.getElementById('temp_items_json');
    const progWrap = document.getElementById('progwrap');
    const progBar = document.getElementById('progress');
    const progPct = document.getElementById('progpercent');
    const progMsg = document.getElementById('progmsg');

    // Webcam Elements
    const btnOpenCam = document.getElementById('btn-webcam-open');
    const btnCloseCam = document.getElementById('btn-webcam-close');
    const btnCapture = document.getElementById('btn-webcam-capture');
    const modalCam = document.getElementById('webcam-modal');
    const videoEl = document.getElementById('webcam-video');
    let videoStream = null;

    let tempItems = JSON.parse(hidden.value || '[]');

    // --- 1. UI Helpers ---
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
      hidden.dispatchEvent(new Event('input', {
        bubbles: true
      }));
    }

    function humanKB(b) {
      return (b / 1024).toFixed(1) + ' KB';
    }

    function renderList() {
      listEl.innerHTML = '';
      if (!tempItems.length) {
        listEl.innerHTML = '<li class="text-gray-400 italic">No files selected yet.</li>';
        return;
      }
      tempItems.forEach(item => {
        const li = document.createElement('li');
        li.className = 'flex items-center justify-between bg-gray-50 p-2 rounded border border-gray-200';
        li.innerHTML = `
                <div class="flex items-center gap-2 overflow-hidden">
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span class="truncate font-medium text-gray-700">${item.original_filename}</span>
                    <span class="text-[10px] text-gray-400 shrink-0">(${humanKB(item.bytes)})</span>
                </div>
                <button type="button" class="text-red-600 hover:text-red-800 text-[10px] font-semibold uppercase px-2">Remove</button>
              `;
        li.querySelector('button').addEventListener('click', async () => {
          try { 
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            await fetch('/attachments/temp', {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': csrf,
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({
                public_id: item.public_id
              })
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

    // --- 2. Upload Logic ---
    async function uploadSingle(file) {
      return new Promise((resolve, reject) => {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrf) return reject(new Error('CSRF token not found'));

        const fd = new FormData();
        fd.append('file', file);
        fd.append('tmp_key', tmpKey);
        fd.append('_token', csrf);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/attachments/temp');
        try {
          xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
        } catch (e) {}

        xhr.upload.onprogress = (evt) => {
          if (evt.lengthComputable) setProgress((evt.loaded / evt.total) * 100, `Uploading ${file.name}…`);
        };

        xhr.onload = () => {
          if (xhr.status >= 200 && xhr.status < 300) {
            try {
              resolve(JSON.parse(xhr.responseText));
            } catch (e) {
              reject(new Error('Invalid server response'));
            }
          } else {
            reject(new Error('Upload failed: ' + xhr.status));
          }
        };
        xhr.onerror = () => reject(new Error('Network error'));
        xhr.send(fd);
      });
    }

    async function processFiles(files) {
      if (!files || !files.length) return;

      // Close modal if it was open
      closeWebcamModal();

      for (const f of files) {
        // Simple Validation
        const ext = (f.name.split('.').pop() || 'jpg').toLowerCase();
        if (!ALLOWED.includes(ext) && f.type !== 'image/jpeg') { // quick fix for blob
          alert('Format not allowed: ' + f.name);
          continue;
        }
        if (f.size > MAX10) {
          alert('File too large: ' + f.name);
          continue;
        }

        try {
          setProgress(5, 'Requesting upload…');
          const data = await uploadSingle(f);
          if (data && data.public_id) {
            tempItems.push({
              public_id: data.public_id,
              secure_url: data.secure_url || data.url,
              bytes: data.bytes || f.size,
              resource_type: data.resource_type,
              format: data.format,
              original_filename: data.original_filename || f.name
            });
            syncHidden();
            renderList();
            setProgress(100, 'Done');
          }
        } catch (err) {
          console.error(err);
          alert('Upload failed: ' + err.message);
          hideProgress();
        }
      }
    }

    function handleInputChange(e) {
      processFiles(Array.from(e.target.files || []));
      e.target.value = ''; // Reset
    }

    // --- 3. Webcam Modal Functions ---
    async function openWebcamModal() {
      if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        alert('Browser does not support camera access.');
        return;
      }
      try {
        videoStream = await navigator.mediaDevices.getUserMedia({
          video: true
        });
        videoEl.srcObject = videoStream;
        modalCam.classList.remove('hidden');
        modalCam.classList.add('flex');
      } catch (e) {
        console.error(e);
        alert('Cannot access camera. Check permissions.');
      }
    }

    function closeWebcamModal() {
      if (videoStream) {
        videoStream.getTracks().forEach(track => track.stop());
        videoStream = null;
      }
      videoEl.srcObject = null;
      modalCam.classList.add('hidden');
      modalCam.classList.remove('flex');
    }

    function captureFromWebcam() {
      if (!videoStream) return;

      const canvas = document.createElement('canvas');
      canvas.width = videoEl.videoWidth;
      canvas.height = videoEl.videoHeight;
      const ctx = canvas.getContext('2d');

      // Flip context horizontally if you want mirrored capture, or keep standard
      // ctx.scale(-1, 1); ctx.drawImage(videoEl, -canvas.width, 0); // Mirror
      ctx.drawImage(videoEl, 0, 0); // Standard

      canvas.toBlob(blob => {
        if (blob) {
          // Create a File object from the blob
          const file = new File([blob], "webcam_" + Date.now() + ".jpg", {
            type: "image/jpeg"
          });
          // Send to existing upload logic
          processFiles([file]);
        }
      }, 'image/jpeg', 0.85);
    }

    // --- 4. Bind Events ---
    if (fileInput) fileInput.addEventListener('change', handleInputChange);
    if (mobileCamInput) mobileCamInput.addEventListener('change', handleInputChange);

    // Webcam Button Triggers
    if (btnOpenCam) btnOpenCam.addEventListener('click', openWebcamModal);
    if (btnCloseCam) btnCloseCam.addEventListener('click', closeWebcamModal);
    if (btnCapture) btnCapture.addEventListener('click', captureFromWebcam);

    window.beforeSubmitAttachSync = function() {
      syncHidden();
      return true;
    };
    renderList();
  })();
</script>