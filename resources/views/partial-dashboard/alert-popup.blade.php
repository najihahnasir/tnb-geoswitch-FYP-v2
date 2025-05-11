@push('after-styles')
<style>
    .overlay {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .alert-popup {
        background: white;
        padding: 20px;
        border-radius: 12px;
        width: 420px;
        max-width: 90%;
        box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        animation: fadeIn 0.3s ease;
    }

    .popup-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 1rem;
        margin-bottom: 8px;
    }

    .popup-body {
        font-size: 0.9rem;
        color: #333;
        margin-bottom: 12px;
    }

    .popup-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .close-btn {
        background: none;
        border: none;
        font-size: 20px;
        color: #dc3545;
        cursor: pointer;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@if($offlineCount > 0)
<div id="popupOverlay" class="overlay" style="display: none;">
    <div id="offlineSensorPopup" class="alert-popup shadow-lg">
        <div class="popup-header">
            <strong>🔴 Urgent Sensor Alert</strong>
            <button class="close-btn" onclick="closeOfflinePopup()">&times;</button>
        </div>
        <div class="popup-body">
            <p>There are <strong>{{ $offlineCount }}</strong> 🔴 <strong>OFFLINE sensors</strong> in the system.</p>
            <ul style="padding-left: 18px;">
                @foreach ($offlineSummary as $item)
                    <li><strong>{{ $item['substation'] }}</strong> – {{ $item['sensor_type'] }} (e.g., {{ $item['sensor_tag'] }})</li>
                @endforeach
            </ul>
            <small>Last updated: {{ \Carbon\Carbon::now()->format('d M Y, h:i A') }}</small>
        </div>
        <div class="popup-footer">
            <a href="{{ route('sensors.index', ['filter' => 'offline']) }}" class="btn btn-danger btn-sm">View Affected Sensors</a>
            <button onclick="closeOfflinePopup()" class="btn btn-secondary btn-sm">Dismiss</button>
        </div>
    </div>
</div>
@endif

@push('after-script')
<script>
    window.addEventListener('DOMContentLoaded', function () {
        const popupOverlay = document.getElementById('popupOverlay');
        const popup = document.getElementById('offlineSensorPopup');
        const offlineCount = {{ $offlineCount }};

        console.log("Popup script running. Offline count:", offlineCount);

        if (offlineCount > 0 && popupOverlay && popup && !sessionStorage.getItem('popupDismissed')) {
            popupOverlay.style.display = 'flex';
            popup.style.display = 'block';
        }
    });

    function closeOfflinePopup() {
        const popupOverlay = document.getElementById('popupOverlay');
        if (popupOverlay) {
            popupOverlay.style.display = 'none';
            sessionStorage.setItem('popupDismissed', true);
        }
    }
</script>
@endpush