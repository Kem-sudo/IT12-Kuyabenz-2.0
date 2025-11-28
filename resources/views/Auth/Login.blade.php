@extends('layouts.app')

@section('content')
<div class="w-full h-full flex items-center justify-center p-8 bg-gray-900 min-h-screen">
    <div class="bg-white rounded-lg border border-gray-200 shadow-lg p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold mb-2 text-gray-800">Kuya Benz</h1>
            <p class="text-gray-600">Delicious Filipino Cuisine</p>
        </div>
        
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Sign In</h2>
        
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label for="username" class="block text-sm font-medium mb-2 text-gray-700">Username</label>
                <input type="text" id="username" name="username" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:outline-none text-gray-800">
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium mb-2 text-gray-700">Password</label>
                <input type="password" id="password" name="password" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:outline-none text-gray-800">
            </div>
            
            <button type="submit" class="w-full bg-gray-800 text-white py-3 rounded-lg font-semibold hover:bg-gray-700 transition">
                Sign In
            </button>
        </form>
    </div>
</div>
@endsection