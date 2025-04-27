{{-- @extends('layouts.master')

@section('title', 'Sensor Status Summary Report')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-semibold mb-6">Sensor Status Summary Report</h2>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('reports.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label for="type" class="block font-medium text-sm">Sensor Type</label>
            <select name="type" id="type" class="form-select w-full">
                <option value="">All Types</option>
                <option value="Partial Discharge" {{ request('type') == 'Partial Discharge' ? 'selected' : '' }}>Partial Discharge</option>
                <option value="Thermal" {{ request('type') == 'Thermal' ? 'selected' : '' }}>Thermal</option>
            </select>
        </div>

        <div>
            <label for="status" class="block font-medium text-sm">Status</label>
            <select name="status" id="status" class="form-select w-full">
                <option value="">All Statuses</option>
                <option value="NORMAL" {{ request('status') == 'NORMAL' ? 'selected' : '' }}>Normal</option>
                <option value="AWAIT" {{ request('status') == 'AWAIT' ? 'selected' : '' }}>Await</option>
                <option value="ALARM" {{ request('status') == 'ALARM' ? 'selected' : '' }}>Alarm</option>
            </select>
        </div>

        <div>
            <label for="start_date" class="block font-medium text-sm">Start Date</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-input w-full">
        </div>

        <div>
            <label for="end_date" class="block font-medium text-sm">End Date</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-input w-full">
        </div>

        <div class="md:col-span-4 flex justify-end items-end">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Apply Filters</button>
        </div>
    </form>
    <div class="flex justify-end gap-4 mb-4">
        <a href="{{ route('reports.sensor-status', array_merge(request()->query(), ['export' => 'pdf'])) }}" 
           class="btn btn-sm btn-danger">
           Download PDF
        </a>
        <a href="{{ route('reports.sensor-status', array_merge(request()->query(), ['export' => 'excel'])) }}" 
           class="btn btn-sm btn-success">
           Export Excel
        </a>
        <a href="{{ route('reports.sensor-status', array_merge(request()->query(), ['export' => 'csv'])) }}" 
           class="btn btn-sm btn-secondary">
           Export CSV
        </a>
    </div>

    <!-- Data Table -->
    <div class="overflow-x-auto">
        <table class="table table-bordered w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th>#</th>
                    <th>Sensor Tag</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Substation</th>
                    <th>Panel</th>
                    <th>Latest Reading Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sensors as $index => $sensor)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $sensor->tag ?? '-' }}</td>
                        <td>{{ $sensor->type ?? '-' }}</td>
                        <td>{{ optional($sensor->latest_measurement)->status ?? '-' }}</td>
                        <td>{{ optional($sensor->switchgear->substation)->substation_name ?? '-' }}</td>
                        <td>{{ optional($sensor->switchgear)->panel ?? '-' }}</td>
                        <td>
                            {{ optional(optional($sensor->latest_measurement)->created_at)->format('Y-m-d H:i') ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No data available for the selected filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <!-- Pagination Links -->
        <div class="mt-4">
            {{ $sensors->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection --}}
@extends('layouts.master')

@section('title', 'Sensor Status Summary Report')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-3xl font-bold mb-8">Sensor Status Summary Report</h2>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
        <div>
            <label for="substation" class="block font-medium text-sm mb-1">Substation</label>
            <select name="substation" id="substation" class="form-select w-full">
                <option value="">All Substations</option>
                @foreach($substations as $substation)
                    <option value="{{ $substation->id }}" {{ request('substation') == $substation->id ? 'selected' : '' }}>
                        {{ $substation->substation_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="type" class="block font-medium text-sm mb-1">Sensor Type</label>
            <select name="type" id="type" class="form-select w-full">
                <option value="">All Types</option>
                <option value="Partial Discharge" {{ request('type') == 'Partial Discharge' ? 'selected' : '' }}>Partial Discharge</option>
                <option value="Thermal" {{ request('type') == 'Thermal' ? 'selected' : '' }}>Thermal</option>
            </select>
        </div>

        <div>
            <label for="status" class="block font-medium text-sm mb-1">Status</label>
            <select name="status" id="status" class="form-select w-full">
                <option value="">All Statuses</option>
                <option value="NORMAL" {{ request('status') == 'NORMAL' ? 'selected' : '' }}>Normal</option>
                <option value="AWAIT" {{ request('status') == 'AWAIT' ? 'selected' : '' }}>Await</option>
                <option value="ALARM" {{ request('status') == 'ALARM' ? 'selected' : '' }}>Alarm</option>
            </select>
        </div>

        <div>
            <label for="start_date" class="block font-medium text-sm mb-1">Start Date</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-input w-full">
        </div>

        <div>
            <label for="end_date" class="block font-medium text-sm mb-1">End Date</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-input w-full">
        </div>

        {{-- <div class="md:col-span-5 flex justify-end items-end mt-2">
            <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Apply Filters</button>
        </div> --}}
        {{-- <div class="md:col-span-5 flex justify-end items-end mt-2">
            <button type="submit" class="px-5 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 shadow-md transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                Apply Filters
            </button>
        </div> --}}
        <button type="submit" class="btn btn-primary">
            Apply Filters
        </button>
        
        
        
    </form>

    <!-- Export Buttons -->
    <div class="flex justify-end gap-4 mb-6">
        <a href="{{ route('reports.sensor-status', array_merge(request()->query(), ['export' => 'pdf'])) }}" 
           class="btn btn-danger">Download PDF</a>
        <a href="{{ route('reports.sensor-status', array_merge(request()->query(), ['export' => 'excel'])) }}" 
           class="btn btn-success">Export Excel</a>
        <a href="{{ route('reports.sensor-status', array_merge(request()->query(), ['export' => 'csv'])) }}" 
           class="btn btn-secondary">Export CSV</a>
    </div>

    <!-- Data Table -->
    {{-- <div class="overflow-x-auto">
        <table class="table-auto w-full text-sm text-left sortable">
            <thead class="bg-black-100">
                <tr>
                    <th class="px-4 py-2 cursor-pointer">#</th>
                    <th class="px-4 py-2 cursor-pointer">Sensor Tag</th>
                    <th class="px-4 py-2 cursor-pointer">Type</th>
                    <th class="px-4 py-2 cursor-pointer">Status</th>
                    <th class="px-4 py-2 cursor-pointer">Substation</th>
                    <th class="px-4 py-2 cursor-pointer">Panel</th>
                    <th class="px-4 py-2 cursor-pointer">Latest Reading Date</th>
                    <th class="px-4 py-2 cursor-pointer">Start Date</th>
                    <th class="px-4 py-2 cursor-pointer">End Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sensors as $index => $sensor)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $index + 1 }}</td>
                        <td class="px-4 py-2">{{ $sensor->tag ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $sensor->type ?? '-' }}</td>
                        <td class="px-4 py-2">{{ optional($sensor->latest_measurement)->status ?? '-' }}</td>
                        <td class="px-4 py-2">{{ optional($sensor->switchgear->substation)->substation_name ?? '-' }}</td>
                        <td class="px-4 py-2">{{ optional($sensor->switchgear)->panel ?? '-' }}</td>
                        <td class="px-4 py-2">{{ optional(optional($sensor->latest_measurement)->created_at)->format('Y-m-d H:i') ?? '-' }}</td>
                        <td class="px-4 py-2">{{ optional($sensor->start_date)->format('Y-m-d') ?? '-' }}</td>
                        <td class="px-4 py-2">{{ optional($sensor->end_date)->format('Y-m-d') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-6">No data available for the selected filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <!-- Pagination Links -->
        <div class="mt-6">
            {{ $sensors->appends(request()->query())->links() }}
        </div>
    </div> --}}
    <!-- Data Table -->
    <div class="overflow-x-auto">
        <table class="table-auto w-full text-sm text-left sortable border border-gray-300">
            {{-- <thead class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-700 text-white font-semibold text-sm uppercase tracking-wider">

                <tr>
                    @php $headers = ['#', 'Sensor Tag', 'Type', 'Status', 'Substation', 'Panel', 'Latest Reading Date', 'Start Date', 'End Date']; @endphp
                    @foreach ($headers as $i => $header)
                        <th class="px-4 py-3 cursor-pointer relative hover:bg-gray-700 transition" data-index="{{ $i }}">
                            {{ $header }}
                            <span class="sort-icon absolute right-2 text-xs"></span>
                        </th>
                    @endforeach
                </tr>
            </thead> --}}
            {{-- <thead>
                <tr class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-700 text-white font-semibold text-xs uppercase tracking-wider">
                    @php $headers = ['#', 'Sensor Tag', 'Type', 'Status', 'Substation', 'Panel', 'Latest Reading Date', 'Start Date', 'End Date']; @endphp
                    @foreach ($headers as $i => $header)
                        <th class="px-4 py-3 cursor-pointer relative hover:bg-gray-700 transition">
                            {{ $header }}
                            <span class="sort-icon absolute right-2 text-xs"></span>
                        </th>
                    @endforeach
                </tr>
            </thead>
             --}}
             <thead>
                <tr class="bg-gray-800">
                    @php $headers = ['#', 'Sensor Tag', 'Type', 'Status', 'Substation', 'Panel', 'Latest Reading Date', 'Start Date', 'End Date']; @endphp
                    @foreach ($headers as $i => $header)
                        <th class="px-4 py-3 text-black text-xs font-semibold uppercase tracking-wide">
                            {{ $header }}
                            <span class="sort-icon absolute right-2 text-xs"></span>
                        </th>
                    @endforeach
                </tr>
            </thead>
            
            <tbody>
                @forelse ($sensors as $index => $sensor)
                    <tr class="border-b hover:bg-gray-100 even:bg-gray-50">
                        <td class="px-4 py-2">{{ $index + 1 }}</td>
                        <td class="px-4 py-2">{{ $sensor->tag ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $sensor->type ?? '-' }}</td>
                        <td class="px-4 py-2">{{ optional($sensor->latest_measurement)->status ?? '-' }}</td>
                        <td class="px-4 py-2">{{ optional($sensor->switchgear->substation)->substation_name ?? '-' }}</td>
                        <td class="px-4 py-2">{{ optional($sensor->switchgear)->panel ?? '-' }}</td>
                        <td class="px-4 py-2">{{ optional(optional($sensor->latest_measurement)->created_at)->format('Y-m-d H:i') ?? '-' }}</td>
                        <td class="px-4 py-2">{{ optional($sensor->start_date)->format('Y-m-d') ?? '-' }}</td>
                        <td class="px-4 py-2">{{ optional($sensor->end_date)->format('Y-m-d') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-6">No data available for the selected filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

<!-- Include Sortable Script -->
{{-- <script>
    document.querySelectorAll('.sortable th').forEach(header => {
        header.addEventListener('click', () => {
            const table = header.parentElement.parentElement.parentElement;
            const rows = Array.from(table.querySelectorAll('tr:nth-child(n+2)'));
            const index = Array.from(header.parentElement.children).indexOf(header);
            const ascending = header.asc = !header.asc;
            rows.sort((rowA, rowB) => {
                const cellA = rowA.children[index].innerText.trim();
                const cellB = rowB.children[index].innerText.trim();
                return (cellA.localeCompare(cellB, undefined, {numeric: true})) * (ascending ? 1 : -1);
            });
            rows.forEach(row => table.appendChild(row));
        });
    });
</script> --}}
<script>
    document.querySelectorAll('.sortable th').forEach(header => {
        header.addEventListener('click', () => {
            const table = header.closest('table');
            const index = Array.from(header.parentElement.children).indexOf(header);
            const ascending = header.asc = !header.asc;
            const icon = header.querySelector('.sort-icon');

            // Reset all other icons
            document.querySelectorAll('.sort-icon').forEach(el => el.textContent = '');

            icon.textContent = ascending ? '↑' : '↓';

            const rows = Array.from(table.querySelectorAll('tbody > tr'));
            rows.sort((a, b) => {
                const cellA = a.children[index].textContent.trim();
                const cellB = b.children[index].textContent.trim();
                return cellA.localeCompare(cellB, undefined, {numeric: true}) * (ascending ? 1 : -1);
            });
            rows.forEach(row => table.querySelector('tbody').appendChild(row));
        });
    });
</script>

@endsection
