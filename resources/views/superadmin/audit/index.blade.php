@extends('layouts.superadmin')

@section('title', 'Audit Logs')
@section('breadcrumb', 'Super Admin / Audit Logs')

@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-6 border-b">
        <h1 class="text-xl font-semibold">Audit Logs</h1>
        <p class="text-gray-600 text-sm mt-1">Track important actions in the system.</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="text-left px-4 py-3">Time</th>
                    <th class="text-left px-4 py-3">Actor</th>
                    <th class="text-left px-4 py-3">Action</th>
                    <th class="text-left px-4 py-3">Subject</th>
                    <th class="text-left px-4 py-3">Meta</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-600">{{ $log->created_at->format('d M Y, h:i A') }}</td>
                        <td class="px-4 py-3">{{ $log->actor_id }}</td>
                        <td class="px-4 py-3 font-medium">{{ $log->action }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $log->subject_type ? class_basename($log->subject_type) . ':' . $log->subject_id : '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            <pre class="text-xs whitespace-pre-wrap">{{ json_encode($log->meta, JSON_PRETTY_PRINT) }}</pre>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="p-4">
        {{ $logs->links() }}
    </div>
</div>
@endsection
