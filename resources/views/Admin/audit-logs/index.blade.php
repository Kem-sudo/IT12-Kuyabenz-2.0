@extends('layouts.App')

@section('content')
<div class="w-full h-full flex" style="min-height: 100vh;">
    <div class="w-64 bg-gray-800 text-white shadow-lg flex flex-col h-screen sticky top-0 overflow-y-auto">
        @include('Admin.partials.sidebar')
    </div>

    <div class="flex-1 bg-gray-50">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Audit Logs</h2>
                    <p class="text-gray-600">Role-based activity</p>
                </div>
            </div>

            <form method="GET" class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700">Role</label>
                        <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <option value="">All</option>
                            <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>admin</option>
                            <option value="cashier" {{ $role === 'cashier' ? 'selected' : '' }}>cashier</option>
                            <option value="kitchen" {{ $role === 'kitchen' ? 'selected' : '' }}>kitchen</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button class="px-4 py-2 bg-gray-800 text-white rounded-lg font-semibold">Filter</button>
                        <a href="{{ route('admin.audit-logs') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg font-semibold">Reset</a>
                    </div>
                </div>
            </form>

            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="text-left px-4 py-3">Time</th>
                                <th class="text-left px-4 py-3">Role</th>
                                <th class="text-left px-4 py-3">User</th>
                                <th class="text-left px-4 py-3">Action</th>
                                <th class="text-left px-4 py-3">Subject</th>
                                <th class="text-left px-4 py-3">IP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($logs as $log)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ optional($log->created_at)->format('Y-m-d H:i:s') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded text-xs font-semibold
                                            {{ $log->role === 'admin' ? 'bg-purple-100 text-purple-800' : '' }}
                                            {{ $log->role === 'cashier' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $log->role === 'kitchen' ? 'bg-green-100 text-green-800' : '' }}
                                        ">
                                            {{ $log->role ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-800">
                                        {{ $log->user?->username ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 font-mono text-gray-800">{{ $log->action }}</td>
                                    <td class="px-4 py-3 text-gray-700">
                                        {{ $log->subject_type ? class_basename($log->subject_type) : '—' }}
                                        {{ $log->subject_id ? '#'.$log->subject_id : '' }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ $log->ip_address ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">No logs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

