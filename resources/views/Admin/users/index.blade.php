@extends('layouts.app')

@section('content')
<div class="w-full h-full flex" style="min-height: 100vh;">
    <!-- Sidebar -->
    <div class="w-64 bg-gray-800 text-white shadow-lg flex flex-col">
        <!-- Same sidebar as dashboard -->
        @include('admin.partials.sidebar')
    </div>
    
    <!-- Main Content -->
    <div class="flex-1 bg-gray-50">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold text-gray-800">Staff Management</h2>
                <button onclick="showAddStaffForm()" class="px-6 py-3 bg-gray-800 text-white rounded-lg font-semibold shadow hover:bg-gray-700 transition">
                    ➕ Add Staff
                </button>
            </div>
            
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                <div class="space-y-4">
                    @if($users->count() === 0)
                        <p class="text-gray-500 text-center py-8">No staff accounts yet</p>
                    @else
                        @foreach($users as $user)
                            <div class="flex justify-between items-center p-5 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                                <div class="flex-1">
                                    <h4 class="font-bold text-xl text-gray-800">{{ $user->username }}</h4>
                                    <p class="text-sm text-gray-600 mt-1">Role: <strong>{{ $user->role }}</strong></p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="px-4 py-2 text-sm font-bold rounded-lg 
                                        {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : 
                                           ($user->role === 'cashier' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ strtoupper($user->role) }}
                                    </span>
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
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
    <div class="bg-white rounded-lg border border-gray-200 p-8 max-w-md w-full mx-4">
        <h3 class="text-2xl font-bold mb-6 text-gray-800">Add Staff Member</h3>
        
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700">Username</label>
                <input type="text" name="username" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 text-gray-800">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 text-gray-800">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700">Role</label>
                <select name="role" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 text-gray-800">
                    <option value="cashier">Cashier</option>
                    <option value="kitchen">Kitchen Staff</option>
                </select>
            </div>
            
            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 bg-gray-800 text-white py-3 rounded-lg font-semibold hover:bg-gray-700 transition">
                    Create Staff
                </button>
                <button type="button" onclick="hideAddStaffForm()" class="flex-1 bg-gray-200 text-gray-800 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
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