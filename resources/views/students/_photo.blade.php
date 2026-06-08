{{-- Composant photo élève : upload fichier OU capture webcam.
     Variable optionnelle : $existingPhoto (chemin storage) --}}
@php $existingPhoto = $existingPhoto ?? null; @endphp

<div class="flex items-start gap-5">

    {{-- Aperçu --}}
    <div class="shrink-0">
        <div id="photoPreview"
             class="w-28 h-32 rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden">
            @if($existingPhoto)
                <img id="previewImg" src="{{ asset('storage/'.$existingPhoto) }}" class="w-full h-full object-cover" alt="">
            @else
                <div id="previewPlaceholder" class="text-center px-2">
                    <svg class="w-8 h-8 text-gray-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-xs text-gray-400">Aucune photo</span>
                </div>
                <img id="previewImg" src="" class="w-full h-full object-cover hidden" alt="">
            @endif
        </div>
    </div>

    {{-- Contrôles --}}
    <div class="flex-1">
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Photo de l'élève</label>
        <p class="text-xs text-gray-400 mb-3">Prenez une photo avec la webcam ou importez un fichier (max 2 Mo).</p>

        {{-- Champ fichier réel (reçoit aussi la capture webcam) --}}
        <input type="file" name="photo" id="photoInput" accept="image/*" class="hidden" onchange="onPhotoFileSelected(event)">

        <div class="flex flex-wrap gap-2">
            <button type="button" onclick="startCamera()"
                    class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4 py-2 rounded-lg font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Prendre une photo
            </button>
            <button type="button" onclick="document.getElementById('photoInput').click()"
                    class="inline-flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm px-4 py-2 rounded-lg font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Importer un fichier
            </button>
            <button type="button" id="removePhotoBtn" onclick="clearPhoto()"
                    class="{{ $existingPhoto ? '' : 'hidden' }} inline-flex items-center gap-1.5 text-red-500 hover:text-red-700 text-sm px-3 py-2 rounded-lg font-medium transition">
                Retirer
            </button>
        </div>
        @error('photo')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror
    </div>
</div>

{{-- Modal caméra --}}
<div id="cameraModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm"
     onclick="if(event.target===this) stopCamera()">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100">
            <h3 class="font-bold text-gray-900">Prendre une photo</h3>
            <button type="button" onclick="stopCamera()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-4">
            <div class="relative rounded-xl overflow-hidden bg-gray-900 aspect-[4/3]">
                <video id="cameraVideo" autoplay playsinline class="w-full h-full object-cover"></video>
                <p id="cameraError" class="hidden absolute inset-0 flex items-center justify-center text-center text-sm text-white px-6"></p>
            </div>
        </div>
        <div class="px-4 pb-4 flex items-center justify-between gap-2">
            <button type="button" onclick="stopCamera()" class="text-sm text-gray-500 hover:text-gray-700">Annuler</button>
            <button type="button" id="captureBtn" onclick="capturePhoto()"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                </svg>
                Capturer
            </button>
        </div>
    </div>
</div>

<canvas id="photoCanvas" class="hidden"></canvas>

<script>
let cameraStream = null;

async function startCamera() {
    const modal = document.getElementById('cameraModal');
    const video = document.getElementById('cameraVideo');
    const errEl = document.getElementById('cameraError');
    errEl.classList.add('hidden');
    modal.classList.remove('hidden');

    try {
        cameraStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
            audio: false,
        });
        video.srcObject = cameraStream;
    } catch (e) {
        errEl.textContent = "Impossible d'accéder à la caméra. Vérifiez les autorisations du navigateur, ou importez un fichier.";
        errEl.classList.remove('hidden');
        document.getElementById('captureBtn').disabled = true;
        document.getElementById('captureBtn').classList.add('opacity-50', 'cursor-not-allowed');
    }
}

function stopCamera() {
    if (cameraStream) {
        cameraStream.getTracks().forEach(t => t.stop());
        cameraStream = null;
    }
    document.getElementById('cameraModal').classList.add('hidden');
}

function capturePhoto() {
    const video  = document.getElementById('cameraVideo');
    const canvas = document.getElementById('photoCanvas');
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);

    canvas.toBlob(function (blob) {
        const file = new File([blob], 'photo-' + Date.now() + '.jpg', { type: 'image/jpeg' });
        // Injecte le fichier capturé dans l'input file (compatible avec le contrôleur)
        const dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('photoInput').files = dt.files;

        showPreview(URL.createObjectURL(blob));
        stopCamera();
    }, 'image/jpeg', 0.9);
}

function onPhotoFileSelected(event) {
    const file = event.target.files[0];
    if (file) showPreview(URL.createObjectURL(file));
}

function showPreview(url) {
    const img = document.getElementById('previewImg');
    const ph  = document.getElementById('previewPlaceholder');
    img.src = url;
    img.classList.remove('hidden');
    if (ph) ph.classList.add('hidden');
    document.getElementById('removePhotoBtn').classList.remove('hidden');
}

function clearPhoto() {
    document.getElementById('photoInput').value = '';
    const img = document.getElementById('previewImg');
    const ph  = document.getElementById('previewPlaceholder');
    img.src = '';
    img.classList.add('hidden');
    if (ph) ph.classList.remove('hidden');
    document.getElementById('removePhotoBtn').classList.add('hidden');
}
</script>
