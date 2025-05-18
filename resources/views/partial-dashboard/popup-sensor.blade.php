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
            <button onclick="closeStatusPopup()" class="text-gray-500 text-xl hover:text-red-600">&times;</button>
        </div>
        <div id="popupContent">
            <!-- Sensor info will be injected here -->
        </div>
    </div>

    <!-- ONLINE TEMPLATE -->
    <div id="onlineSensorTemplate" style="display: none; visibility: hidden;">
        <div class="space-y-6">
            @php
                $grouped = $onlineSensors->groupBy(fn($s) => $s->switchgear->substation->substation_name ?? 'Unknown');
            @endphp

            @foreach($grouped as $substation => $sensors)
                <div class="border rounded-lg p-4 shadow-sm">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="font-semibold text-blue-700">📍 {{ $substation }}</h3>
                        <span class="text-xs bg-green-100 text-green-700 font-medium px-2 py-1 rounded-full">
                            {{ $sensors->count() }} Online
                        </span>
                    </div>

                    <ul class="text-sm text-gray-700 space-y-2">
                        @foreach($sensors as $sensor)
                            <li class="flex justify-between">
                                <span><strong>{{ $sensor->tag }}</strong> ({{ $sensor->type }})</span>
                                <span class="text-green-700 text-xs font-semibold">{{ ucfirst($sensor->status) }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-3 text-right">
                        <a href="{{ route('sensors.index', ['filter' => 'online', 'substation' => $substation]) }}"
                           class="text-blue-600 hover:underline text-xs">View more →</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- REPAIRING TEMPLATE -->
    <div id="repairingSensorTemplate" style="display: none; visibility: hidden;">
        <div class="space-y-6">
            @php
                $grouped = $repairingSensors->groupBy(fn($s) => $s->switchgear->substation->substation_name ?? 'Unknown');
            @endphp

            @foreach($grouped as $substation => $sensors)
                <div class="border rounded-lg p-4 shadow-sm">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="font-semibold text-yellow-700">📍 {{ $substation }}</h3>
                        <span class="text-xs bg-yellow-100 text-yellow-800 font-medium px-2 py-1 rounded-full">
                            {{ $sensors->count() }} Repairing
                        </span>
                    </div>

                    <ul class="text-sm text-gray-700 space-y-2">
                        @foreach($sensors as $sensor)
                            <li class="flex justify-between">
                                <span><strong>{{ $sensor->tag }}</strong> ({{ $sensor->type }})</span>
                                <span class="text-yellow-800 text-xs font-semibold">{{ ucfirst($sensor->status) }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-3 text-right">
                        <a href="{{ route('sensors.index', ['filter' => 'repairing', 'substation' => $substation]) }}"
                           class="text-yellow-600 hover:underline text-xs">View more →</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- OFFLINE TEMPLATE -->
    <div id="offlineSensorTemplate" style="display: none; visibility: hidden;">
        <div class="space-y-6">
            @php
                $grouped = $offlineSensors->groupBy(fn($s) => $s->switchgear->substation->substation_name ?? 'Unknown');
            @endphp

            @foreach($grouped as $substation => $sensors)
                <div class="border rounded-lg p-4 shadow-sm">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="font-semibold text-red-700">📍 {{ $substation }}</h3>
                        <span class="text-xs bg-red-100 text-red-700 font-medium px-2 py-1 rounded-full">
                            {{ $sensors->count() }} Offline
                        </span>
                    </div>

                    <ul class="text-sm text-gray-700 space-y-2">
                        @foreach($sensors as $sensor)
                            <li class="flex justify-between">
                                <span><strong>{{ $sensor->tag }}</strong> ({{ $sensor->type }})</span>
                                <span class="text-red-700 text-xs font-semibold">{{ ucfirst($sensor->status) }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-3 text-right">
                        <a href="{{ route('sensors.index', ['filter' => 'offline', 'substation' => $substation]) }}"
                           class="text-red-600 hover:underline text-xs">View more →</a>
                    </div>
                </div>
            @endforeach
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
        const html = document.getElementById('repairingSensorTemplate').innerHTML;
        openStatusPopup('Repairing Sensors', html);
    }

    function showOnlineSensors() {
        const html = document.getElementById('onlineSensorTemplate').innerHTML;
        openStatusPopup('Online Sensors', html);
    }

    function showOfflineSensors() {
        const html = document.getElementById('offlineSensorTemplate').innerHTML;
        openStatusPopup('Offline Sensors', html);
    }

</script>
@endpush