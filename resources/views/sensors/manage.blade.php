@extends('layouts.master')

@section('title', 'Manage Sensors')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-6">Sensor Management</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="table-auto w-full text-sm border border-gray-300">
            <thead class="bg-gray-700 text-white">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Sensor Tag</th>
                    <th class="px-4 py-3">Type</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Substation</th>
                    <th class="px-4 py-3">Panel</th>
                    <th class="px-4 py-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sensors as $index => $sensor)
                <tr class="border-b">
                    <td class="px-4 py-2">{{ $index + 1 }}</td>
                    <td class="px-4 py-2">{{ $sensor->tag }}</td>
                    <td class="px-4 py-2">{{ $sensor->type }}</td>
                    <td class="px-4 py-2 capitalize">{{ $sensor->status }}</td>
                    <td class="px-4 py-2">{{ optional($sensor->switchgear->substation)->substation_name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ optional($sensor->switchgear)->panel ?? '-' }}</td>
                    <td class="px-4 py-2">
                        <form action="{{ route('sensors.index', $sensor->id) }}" method="POST" class="flex space-x-2">
                            @csrf
                            <select name="status" class="form-select px-2 py-1 text-sm">
                                <option value="online" {{ $sensor->status == 'online' ? 'selected' : '' }}>Online</option>
                                <option value="offline" {{ $sensor->status == 'offline' ? 'selected' : '' }}>Offline</option>
                                <option value="repairing" {{ $sensor->status == 'repairing' ? 'selected' : '' }}>Repairing</option>
                            </select>
                            <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-xs">
                                Update
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-6">
            {{ $sensors->links() }}
        </div>
    </div>
</div>
@endsection
