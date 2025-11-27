@extends('layouts.app')

@section('content')
<div class="w-full h-full flex" style="min-height: 100vh;">
    <!-- Sidebar -->
    <div class="w-64 text-white shadow-2xl flex flex-col" style="background: linear-gradient(180deg, #2d3748 0%, #4a5568 100%);">
        <div class="p-6 border-b border-white border-opacity-20">
            <h1 class="text-2xl font-bold mb-1">Kuya Benz</h1>
            <p class="text-sm opacity-90">Admin Panel</p>
        </div>
        
        <nav class="flex-1 p-4">
            <a href="{{ route('admin.dashboard') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition hover:bg-white hover:bg-opacity-10">
                <span class="text-lg">📊</span>
                <span>Dashboard</span>
            </a>
            
            <a href="{{ route('admin.transactions') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition hover:bg-white hover:bg-opacity-10">
                <span class="text-lg">📡</span>
                <span>Live Monitor</span>
            </a>
            
            <a href="{{ route('admin.menu') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition bg-white bg-opacity-20 font-bold">
                <span class="text-lg">🍽️</span>
                <span>Menu</span>
            </a>
            
            <a href="{{ route('admin.sales') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition hover:bg-white hover:bg-opacity-10">
                <span class="text-lg">💰</span>
                <span>Sales Report</span>
            </a>
            
            <a href="{{ route('admin.users') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg mb-2 transition hover:bg-white hover:bg-opacity-10">
                <span class="text-lg">👥</span>
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
                <button type="submit" class="w-full bg-white text-center py-3 rounded-lg font-semibold hover:bg-opacity-90 transition text-gray-800">
                    Logout
                </button>
            </form>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="flex-1 bg-gray-50">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Menu Management</h2>
                    <p class="text-gray-600">Add and manage your menu items</p>
                </div>
                <button onclick="showAddMenuItemForm()" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold shadow-lg hover:bg-blue-700 transition flex items-center gap-2">
                    <span>+</span>
                    <span>Add Menu Item</span>
                </button>
            </div>
            
            <!-- Menu Items Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @if($menuItems->count() === 0)
                    <div class="col-span-full text-center py-12">
                        <div class="text-4xl mb-4 text-gray-400">🍽️</div>
                        <p class="text-gray-500 text-lg">No menu items yet</p>
                        <p class="text-gray-400">Add your first menu item to get started</p>
                    </div>
                @else
                    @foreach($menuItems as $item)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition">
                            <!-- Item Image -->
                            <div class="h-40 bg-gray-200 rounded-t-xl overflow-hidden">
                                <img src="{{ $item->image_url }}" 
                                     alt="{{ $item->name }}" 
                                     class="w-full h-full object-cover"
                                     onerror="this.src='{{ asset('images/default-food.png') }}'">
                            </div>
                            
                            <!-- Item Details -->
                            <div class="p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-semibold text-gray-800 text-lg">{{ $item->name }}</h3>
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                        {{ $item->category }}
                                    </span>
                                </div>
                                
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-lg font-bold text-green-600">₱{{ number_format($item->price, 2) }}</span>
                                    <span class="text-sm text-gray-600 {{ $item->stock < 10 ? 'text-red-600 font-semibold' : '' }}">
                                        Stock: {{ $item->stock }}
                                    </span>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="flex gap-2">
                                    <button onclick="editMenuItem({{ $item->id }})" 
                                            class="flex-1 bg-yellow-500 text-white py-2 px-3 rounded-lg text-sm font-semibold hover:bg-yellow-600 transition">
                                        Edit
                                    </button>
                                    <form method="POST" action="{{ route('admin.menu.destroy', $item) }}" class="flex-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                onclick="return confirm('Are you sure you want to delete {{ $item->name }}?')"
                                                class="w-full bg-red-500 text-white py-2 px-3 rounded-lg text-sm font-semibold hover:bg-red-600 transition">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Menu Item Modal -->
<div id="menuItemModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold mb-4 text-gray-800" id="modalTitle">Add Menu Item</h3>
        
        <form method="POST" action="{{ route('admin.menu.store') }}" id="menuItemForm" enctype="multipart/form-data">
            @csrf
            <div id="formMethod" style="display: none;"></div>
            
            <div class="space-y-4">
                <!-- Item Image -->
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700">Item Image</label>
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 bg-gray-200 rounded-lg overflow-hidden flex items-center justify-center">
                            <img id="imagePreview" src="" alt="Preview" class="w-full h-full object-cover hidden">
                            <span id="imagePlaceholder" class="text-gray-400 text-2xl">📷</span>
                        </div>
                        <div class="flex-1">
                            <input type="file" name="image" id="imageInput" accept="image/*" 
                                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-xs text-gray-500 mt-1">JPEG, PNG, JPG, GIF (Max: 2MB)</p>
                        </div>
                    </div>
                </div>
                
                <!-- Item Name -->
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700">Item Name</label>
                    <input type="text" name="name" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-800">
                </div>
                
                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700">Category</label>
                    <select name="category" required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-800">
                        <option value="">Select Category</option>
                        <option value="Chicken">Chicken</option>
                        <option value="Pork">Pork</option>
                        <option value="Beef">Beef</option>
                        <option value="Seafood">Seafood</option>
                        <option value="Soup">Soup</option>
                        <option value="Vegetables">Vegetables</option>
                        <option value="Rice">Rice</option>
                        <option value="Dessert">Dessert</option>
                        <option value="Beverage">Beverage</option>
                    </select>
                </div>
                
                <!-- Price -->
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700">Price (₱)</label>
                    <input type="number" name="price" step="0.01" min="0" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-800">
                </div>
                
                <!-- Stock -->
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700">Stock</label>
                    <input type="number" name="stock" min="0" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-800">
                </div>
            </div>
            
            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Save Item
                </button>
                <button type="button" onclick="hideMenuItemForm()" class="flex-1 bg-gray-200 text-gray-800 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let editingItemId = null;

    function showAddMenuItemForm() {
        editingItemId = null;
        document.getElementById('modalTitle').textContent = 'Add Menu Item';
        document.getElementById('menuItemForm').action = '{{ route("admin.menu.store") }}';
        document.getElementById('formMethod').innerHTML = '';
        document.getElementById('menuItemForm').reset();
        document.getElementById('imagePreview').classList.add('hidden');
        document.getElementById('imagePlaceholder').classList.remove('hidden');
        document.getElementById('menuItemModal').classList.remove('hidden');
    }

    function editMenuItem(itemId) {
        // For now, we'll just show a message
        // In a real implementation, you would fetch the item data and populate the form
        alert('Edit functionality for item ID: ' + itemId + '\n\nIn a complete implementation, this would load the item data into the form for editing.');
        
        // Example of what the complete implementation would do:
        // fetch(`/admin/menu/${itemId}/edit`)
        //     .then(response => response.json())
        //     .then(item => {
        //         editingItemId = itemId;
        //         document.getElementById('modalTitle').textContent = 'Edit Menu Item';
        //         document.getElementById('menuItemForm').action = `/admin/menu/${itemId}`;
        //         document.getElementById('formMethod').innerHTML = '@method("PUT")';
        //         document.querySelector('input[name="name"]').value = item.name;
        //         document.querySelector('select[name="category"]').value = item.category;
        //         document.querySelector('input[name="price"]').value = item.price;
        //         document.querySelector('input[name="stock"]').value = item.stock;
        //         
        //         if (item.image_url) {
        //             document.getElementById('imagePreview').src = item.image_url;
        //             document.getElementById('imagePreview').classList.remove('hidden');
        //             document.getElementById('imagePlaceholder').classList.add('hidden');
        //         }
        //         
        //         document.getElementById('menuItemModal').classList.remove('hidden');
        //     });
    }

    function hideMenuItemForm() {
        document.getElementById('menuItemModal').classList.add('hidden');
    }

    // Image preview functionality
    document.getElementById('imageInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('imagePreview').classList.remove('hidden');
                document.getElementById('imagePlaceholder').classList.add('hidden');
            }
            reader.readAsDataURL(file);
        }
    });

    // Close modal when clicking outside
    document.getElementById('menuItemModal').addEventListener('click', function(e) {
        if (e.target === this) {
            hideMenuItemForm();
        }
    });
</script>
@endsection