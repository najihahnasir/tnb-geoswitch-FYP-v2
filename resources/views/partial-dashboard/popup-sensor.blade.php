@push('after-styles')
<style>
    #statusPopup {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.95);
        z-index: 9999;
        background-color: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        max-width: 90vw;
        width: 800px;
        max-height: 80vh;
        overflow-y: auto;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: all 0.3s ease;
    }

    #statusPopup.active {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    #popupOverlay {
        position: fixed;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease;
    }

    #popupOverlay.active {
        opacity: 1;
        visibility: visible;
    }

    #statusPopup::-webkit-scrollbar {
        width: 6px;
    }

    #statusPopup::-webkit-scrollbar-thumb {
        background-color: #ccc;
        border-radius: 3px;
    }
</style>
@endpush

<div>
     <!-- Background dimmer -->
    <div id="popupOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden" onclick="closeStatusPopup()"></div>

    <!-- Popup Section -->
    <div id="statusPopup" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 
    bg-white p-6 rounded-lg shadow-lg z-[9999] max-w-3xl w-[90%] hidden">
    <div class="flex justify-between items-center mb-4">
            <h3 id="popupTitle" class="text-lg font-bold">Status Detail</h3>
            <button onclick="closeStatusPopup()" class="text-gray-500 text-xl hover:text-red-600">×</button>
        </div>
        <div id="popupContent">
            <!-- Sensor info will be injected here -->
        </div>
    </div>

    <div id="onlineSensorTemplate" style="display: none; visibility: hidden;">
        {{-- Blade-only content for popup, not rendered on screen --}}
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($onlineSensors as $sensor)
                <div class="border rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-lg text-blue-600">{{ $sensor->tag }}</h3>
                            <p class="text-sm text-gray-600">{{ $sensor->type }} Sensor</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                            {{ ucfirst($sensor->status) }}
                        </span>
                    </div>
    
                    <div class="mt-3 space-y-1 text-sm">
                        <div class="flex items-center">
                            📍 {{ $sensor->switchgear->substation->substation_name ?? 'Unknown' }}
                        </div>
                        <div class="flex items-center">
                            ⚡ Panel: {{ $sensor->switchgear->panel ?? 'Unknown' }}
                        </div>
                        <div class="flex items-center">
                            🕒 Updated: {{ $sensor->updated_at->format('M j, Y H:i') }}
                        </div>
                    </div>
    
                    <div class="mt-4 pt-3 border-t text-right">
                        <a href="{{ route('sensors.index', ['status' => 'online']) }}" 
                           class="text-blue-600 hover:underline text-sm">View Details →</a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('after-scripts')
<script>
    function openStatusPopup(title, contentHtml) {
        const popup = document.getElementById('statusPopup');
        const overlay = document.getElementById('popupOverlay');

        document.getElementById('popupTitle').innerText = title;
        document.getElementById('popupContent').innerHTML = contentHtml;

        // Show popup and overlay
        popup.classList.remove('hidden');
        popup.classList.add('active');

        overlay.classList.remove('hidden');
        overlay.classList.add('active');
    }

    function closeStatusPopup() {
        const popup = document.getElementById('statusPopup');
        const overlay = document.getElementById('popupOverlay');

        // Smooth transition
        popup.classList.remove('active');
        overlay.classList.remove('active');

        setTimeout(() => {
            popup.classList.add('hidden');
            overlay.classList.add('hidden');
        }, 300); // match transition duration in CSS
    }

    // Example handlers (replace with Blade data in production)
    

    function showRepairingSensors() {
        const html = `
            <ul class="list-disc pl-5">
                @foreach($repairingSensors as $sensor)
                    <li>{{ $sensor->tag }} – {{ $sensor->switchgear->substation->substation_name ?? '-' }}</li>
                @endforeach
            </ul>
            <a href="{{ route('sensors.index', ['status' => 'repairing']) }}" class="text-blue-500 hover:underline text-sm block mt-2">View all</a>
        `;
        openStatusPopup('Repairing Sensors', html);
    }

    function showOnlineSensors() {
        const html = document.getElementById('onlineSensorTemplate').innerHTML;
        openStatusPopup('Online Sensors', html);
    }

</script>
@endpush