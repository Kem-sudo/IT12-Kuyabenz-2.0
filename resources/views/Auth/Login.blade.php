@extends('layouts.app')

@section('content')
<div class="w-full h-full flex items-center justify-center p-8" style="background: linear-gradient(135deg, #dc2626 0%, #f59e0b 100%); min-height: 100vh;">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold mb-2" style="color: #dc2626;">Kuya Benz</h1>
            <p class="text-gray-600">Delicious Filipino Cuisine</p>
        </div>
        
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <h2 class="text-2xl font-bold mb-6 text-center" style="color: #1f2937;">Sign In</h2>
        
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label for="username" class="block text-sm font-medium mb-2" style="color: #1f2937;">Username</label>
                <input type="text" id="username" name="username" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:outline-none">
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium mb-2" style="color: #1f2937;">Password</label>
                <input type="password" id="password" name="password" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:outline-none">
            </div>
            
            <button type="submit" class="w-full btn-primary text-white py-3 rounded-lg font-semibold" 
                    style="background-color: #dc2626;">
                Sign In
            </button>
        </form>
    </div>
</div>
@endsection