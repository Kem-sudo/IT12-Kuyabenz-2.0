<div class="p-6 border-b border-white border-opacity-20">
    <h1 class="text-2xl font-bold mb-1">Kuya Benz</h1>
    <p class="text-sm opacity-90">Admin Panel</p>
</div>

<nav class="flex-1 p-4">
    <a href="{{ route('admin.dashboard') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition hover:bg-white hover:bg-opacity-10">
        <span class="text-xl">📊</span>
        <span>Dashboard</span>
    </a>
    
    <a href="{{ route('admin.transactions') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition hover:bg-white hover:bg-opacity-10">
        <span class="text-xl">📡</span>
        <span>Live Monitor</span>
    </a>
    
    <a href="{{ route('admin.menu') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition hover:bg-white hover:bg-opacity-10">
        <span class="text-xl">🍽️</span>
        <span>Menu</span>
    </a>
    
    <a href="{{ route('admin.sales') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition hover:bg-white hover:bg-opacity-10">
        <span class="text-xl">💰</span>
        <span>Sales Report</span>
    </a>
    
    <a href="{{ route('admin.users') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition hover:bg-white hover:bg-opacity-10">
        <span class="text-xl">👥</span>
        <span>Staff</span>
    </a>
</nav>

<div class="p-4 border-t border-white border-opacity-20">
    <div class="mb-4 p-3 bg-white bg-opacity-10 rounded-lg">
        <p class="text-xs opacity-75 mb-1">Logged in as</p>
        <p class="font-bold">{{ auth()->user()->username }}</p>
    </div>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full bg-white text-center py-3 rounded-lg font-semibold hover:bg-opacity-90 transition" style="color: #dc2626;">
            Logout
        </button>
    </form>
</div>