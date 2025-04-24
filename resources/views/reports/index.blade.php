@extends('layouts.master')

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

    <!-- Export Buttons -->
    <div class="flex justify-end gap-4 mb-4">
        <a href="{{ route('reports.export.pdf', request()->query()) }}" class="btn btn-sm btn-danger">Download PDF</a>
        <a href="{{ route('reports.export.excel', request()->query()) }}" class="btn btn-sm btn-success">Export Excel</a>
        <a href="{{ route('reports.export.csv', request()->query()) }}" class="btn btn-sm btn-secondary">Export CSV</a>
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
                        <td>{{ $sensor->latest_measurement->status ?? '-' }}</td>
                        <td>{{ $sensor->switchgear->substation->substation_name ?? '-' }}</td>
                        <td>{{ $sensor->switchgear->panel ?? '-' }}</td>
                        <td>{{ $sensor->latest_measurement->created_at->format('Y-m-d H:i') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No data available for the selected filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
