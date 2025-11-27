@extends('layouts.app')

@section('content')
<div class="w-full h-full flex" style="min-height: 100vh;">
    <!-- Sidebar -->
    <div class="w-64 text-white shadow-2xl flex flex-col" style="background: linear-gradient(180deg, #dc2626 0%, #f59e0b 100%);">
        <!-- Same sidebar as dashboard -->
        @include('admin.partials.sidebar')
    </div>
    
    <!-- Main Content -->
    <div class="flex-1" style="background-color: #f3f4f6;">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold" style="color: #1f2937;">Staff Management</h2>
                <button onclick="showAddStaffForm()" class="px-6 py-3 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition" style="background-color: #dc2626;">
                    ➕ Add Staff
                </button>
            </div>
            
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="space-y-4">
                    @if($users->count() === 0)
                        <p class="text-gray-500 text-center py-8">No staff accounts yet</p>
                    @else
                        @foreach($users as $user)
                            <div class="flex justify-between items-center p-5 border-2 border-gray-200 rounded-xl hover:shadow-lg transition">
                                <div class="flex-1">
                                    <h4 class="font-bold text-xl" style="color: #1f2937;">{{ $user->username }}</h4>
                                    <p class="text-sm text-gray-600 mt-1">Role: <strong>{{ $user->role }}</strong></p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="px-4 py-2 text-sm font-bold rounded-lg text-white 
                                        {{ $user->role === 'admin' ? 'bg-red-600' : 
                                           ($user->role === 'cashier' ? 'bg-orange-500' : 'bg-gray-600') }}">
                                        {{ strtoupper($user->role) }}
                                    </span>
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700"
                                                    onclick="return confirm('Are you sure you want to delete this user?')">
                                                Delete
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-sm text-gray-500 italic">(Current User)</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Staff Modal -->
<div id="addStaffModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-8 max-w-md w-full mx-4">
        <h3 class="text-2xl font-bold mb-6" style="color: #1f2937;">Add Staff Member</h3>
        
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-2" style="color: #1f2937;">Username</label>
                <input type="text" name="username" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-2" style="color: #1f2937;">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-2" style="color: #1f2937;">Role</label>
                <select name="role" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                    <option value="cashier">Cashier</option>
                    <option value="kitchen">Kitchen Staff</option>
                </select>
            </div>
            
            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 bg-red-600 text-white py-3 rounded-lg font-semibold hover:bg-red-700">
                    Create Staff
                </button>
                <button type="button" onclick="hideAddStaffForm()" class="flex-1 bg-gray-200 text-gray-800 py-3 rounded-lg font-semibold">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function showAddStaffForm() {
        document.getElementById('addStaffModal').classList.remove('hidden');
    }
    
    function hideAddStaffForm() {
        document.getElementById('addStaffModal').classList.add('hidden');
    }
</script>
@endsection