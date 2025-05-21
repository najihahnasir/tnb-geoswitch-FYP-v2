<div id="popupOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden justify-center items-center">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md text-center">
        <h3 class="text-lg font-semibold mb-4 text-red-600">🚨 Sensor Alert</h3>
        <p class="mb-4">There are <strong>{{ $offlineCount }}</strong> sensors currently <span class="text-red-500 font-bold">OFFLINE</span>.</p>
        <ul class="text-left mb-4 text-sm text-gray-700 list-disc list-inside">
            @foreach ($offlineSummary as $item)
                <li><strong>{{ $item['substation'] }}</strong> – {{ $item['sensor_type'] }} ({{ $item['sensor_tag'] }})</li>
            @endforeach
        </ul>
        <div class="flex justify-between gap-4">
            <a href="{{ route('sensors.index', ['filter' => 'offline']) }}" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-sm">View Sensors</a>
            <button onclick="closeOfflinePopup()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 text-sm">Dismiss</button>
        </div>
    </div>
</div>
