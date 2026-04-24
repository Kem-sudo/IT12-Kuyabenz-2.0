@extends('layouts.App')

@section('content')
<div class="w-full h-full flex" style="min-height: 100vh;">
    <!-- Sidebar -->
    <!-- Sidebar -->
<div class="w-64 bg-gray-800 text-white shadow-lg flex flex-col h-screen sticky top-0">

    <!-- Header -->
    <div class="p-6 border-b border-gray-700 flex-shrink-0">
        <h1 class="text-2xl font-bold mb-1">Kuya Benz</h1>
        <p class="text-sm text-gray-300">Admin Panel</p>
    </div>

    <!-- Menu (scroll only if needed) -->
    <nav class="flex-1 p-4 overflow-y-auto">

        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-lg mb-2 hover:bg-gray-700 transition">
            Dashboard
        </a>

        <a href="{{ route('admin.transactions') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-lg mb-2 hover:bg-gray-700 transition">
            Live Monitor
        </a>

        <a href="{{ route('admin.menu') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-lg mb-2 bg-gray-700 font-bold">
            Menu
        </a>

        <a href="{{ route('admin.sales') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-lg mb-2 hover:bg-gray-700 transition">
            Sales Report
        </a>

        <a href="{{ route('admin.users') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-lg mb-2 hover:bg-gray-700 transition">
            Staff
        </a>

        <a href="{{ route('admin.audit-logs') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-lg mb-2 hover:bg-gray-700 transition">
            Audit Logs
        </a>

    </nav>

    <!-- Footer (LOCKED - ALWAYS VISIBLE) -->
    <div class="p-4 border-t border-gray-700 flex-shrink-0">

        <div class="mb-4 p-3 bg-gray-700 rounded-lg">
            <p class="text-xs text-gray-300 mb-1">Logged in as</p>
            <p class="font-bold">{{ auth()->user()->username }}</p>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full bg-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-600 transition">
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
                <button onclick="showAddMenuItemForm()" class="px-6 py-3 bg-gray-800 text-white rounded-lg font-semibold shadow hover:bg-gray-700 transition flex items-center gap-2">
                    <span>+</span>
                    <span>Add Menu Item</span>
                </button>
            </div>

            <!-- Category Filters -->
            <div class="flex gap-2 mb-6 overflow-x-auto pb-2" id="categories-container">
                <button onclick="selectCategory('all')" id="category-all" 
                        class="px-4 py-2 rounded-lg font-semibold whitespace-nowrap text-white bg-gray-800">
                    All Items
                </button>
                
                <button onclick="selectCategory('Toppings')" id="category-Toppings" 
                        class="px-4 py-2 rounded-lg font-semibold whitespace-nowrap bg-white text-gray-800 border border-gray-300 hover:bg-gray-100">
                    Toppings
                </button>
                  <button onclick="selectCategory('Chicken')" id="category-Chicken" 
                        class="px-4 py-2 rounded-lg font-semibold whitespace-nowrap bg-white text-gray-800 border border-gray-300 hover:bg-gray-100">
                    Chicken
                </button>
                
                <button onclick="selectCategory('Pork')" id="category-Pork" 
                        class="px-4 py-2 rounded-lg font-semibold whitespace-nowrap bg-white text-gray-800 border border-gray-300 hover:bg-gray-100">
                    Pork
                </button>
                
                <button onclick="selectCategory('Dessert')" id="category-Dessert" 
                        class="px-4 py-2 rounded-lg font-semibold whitespace-nowrap bg-white text-gray-800 border border-gray-300 hover:bg-gray-100">
                    Dessert
                </button>
                
                <button onclick="selectCategory('Rice')" id="category-Rice" 
                        class="px-4 py-2 rounded-lg font-semibold whitespace-nowrap bg-white text-gray-800 border border-gray-300 hover:bg-gray-100">
                    Rice
                </button>
                
                <button onclick="selectCategory('Vegetables')" id="category-Vegetables" 
                        class="px-4 py-2 rounded-lg font-semibold whitespace-nowrap bg-white text-gray-800 border border-gray-300 hover:bg-gray-100">
                    Vegetables
                </button>
                
                <button onclick="selectCategory('Soup')" id="category-Soup" 
                        class="px-4 py-2 rounded-lg font-semibold whitespace-nowrap bg-white text-gray-800 border border-gray-300 hover:bg-gray-100">
                    Soup
                </button>
                
                <button onclick="selectCategory('Seafood')" id="category-Seafood" 
                        class="px-4 py-2 rounded-lg font-semibold whitespace-nowrap bg-white text-gray-800 border border-gray-300 hover:bg-gray-100">
                    Seafood
                </button>
                
                <button onclick="selectCategory('Drinks')" id="category-Drinks" 
                        class="px-4 py-2 rounded-lg font-semibold whitespace-nowrap bg-white text-gray-800 border border-gray-300 hover:bg-gray-100">
                    Drinks
                </button>
            </div>
            
            <!-- Menu Items Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="menu-items-container">
                <!-- This will be populated by JavaScript -->
                @if($menuItems->count() === 0)
                    <div class="col-span-full text-center py-12">
                        <div class="text-4xl mb-4 text-gray-400">🍽️</div>
                        <p class="text-gray-500 text-lg">No menu items yet</p>
                        <p class="text-gray-400">Add your first menu item to get started</p>
                    </div>
                @else
                    <!-- Items will be sorted and displayed by JavaScript -->
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Menu Item Modal -->
<div id="menuItemModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto border border-gray-200">
        <h3 class="text-xl font-bold mb-4 text-gray-800" id="modalTitle">Add Menu Item</h3>
        
        <form method="POST" action="{{ route('admin.menu.store') }}" id="menuItemForm" enctype="multipart/form-data">
            @csrf
            <!-- This div will hold the method spoofing for PUT requests -->
            <div id="methodContainer"></div>
            
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
                                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                            <p class="text-xs text-gray-500 mt-1">JPEG, PNG, JPG, GIF (Max: 2MB)</p>
                        </div>
                    </div>
                </div>
                
                <!-- Item Name -->
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700">Item Name</label>
                    <input type="text" name="name" id="itemName" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 text-gray-800">
                </div>
                
                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700">Category</label>
                    <select name="category" id="itemCategory" required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 text-gray-800">
                        <option value="">Select Category</option>
                        <option value="Toppings">Toppings</option>
                        <option value="Chicken">Chicken</option>
                        <option value="Pork">Pork</option>
                        <option value="Beef">Beef</option>
                        <option value="Seafood">Seafood</option>
                        <option value="Soup">Soup</option>
                        <option value="Vegetables">Vegetables</option>
                        <option value="Rice">Rice</option>
                        <option value="Dessert">Dessert</option>
                        <option value="Drinks">Drinks</option>
                    </select>
                </div>
                
                <!-- Price -->
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700">Price (₱)</label>
                    <input type="number" name="price" id="itemPrice" step="0.01" min="0" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 text-gray-800">
                </div>
                
                <!-- Stock -->
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700">Serving</label>
                    <input type="number" name="stock" id="itemStock" min="0" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 text-gray-800">
                </div>
            </div>
            
            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 bg-gray-800 text-white py-3 rounded-lg font-semibold hover:bg-gray-700 transition">
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
    let selectedCategory = 'all';
    
    // Pass menu items from Laravel to JavaScript
    const menuItemsData = @json($menuItems);
    
    function renderMenuItems() {
        let filteredItems = selectedCategory === 'all' 
            ? [...menuItemsData] // Create a copy to avoid mutating original array
            : menuItemsData.filter(item => item.category === selectedCategory);
        
        // Separate in-stock and out-of-stock items
        const inStockItems = filteredItems.filter(item => item.stock > 0);
        const outOfStockItems = filteredItems.filter(item => item.stock === 0);
        
        // Combine with out-of-stock items at the bottom
        const sortedItems = [...inStockItems, ...outOfStockItems];
        
        const container = document.getElementById('menu-items-container');
        
        if (sortedItems.length === 0) {
            container.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <div class="text-4xl mb-4 text-gray-400"></div>
                    <p class="text-gray-500 text-lg">No menu items in this category</p>
                    <p class="text-gray-400">Add a new item to this category</p>
                </div>
            `;
            return;
        }

        // Count items for display
        const inStockCount = inStockItems.length;
        const outOfStockCount = outOfStockItems.length;
        
        let itemsHTML = '';
        
        // Render in-stock items first
        inStockItems.forEach((item, index) => {
            itemsHTML += renderMenuItem(item);
        });
        
        // Add separator if there are both types of items
        if (inStockCount > 0 && outOfStockCount > 0) {
            itemsHTML += `
                <div class="col-span-full border-t border-gray-300 mt-2 mb-2 relative">
                    <span class="absolute left-1/2 transform -translate-x-1/2 -mt-3 px-4 bg-gray-50 text-gray-500 text-sm font-medium">
                        Unavailable Menu
                    </span>
                </div>
            `;
        }
        
        // Render out-of-stock items
        outOfStockItems.forEach((item, index) => {
            itemsHTML += renderMenuItem(item);
        });
        
        container.innerHTML = itemsHTML;
    }

    function renderMenuItem(item) {
        let imageUrl = item.image_url || '/images/Errorimage.jpg';
        
        if (imageUrl.includes('Errorimage.jpg') && item.image) {
            imageUrl = '/storage/' + item.image;
        }

        return `
            <div class="menu-item bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition" 
                 data-category="${item.category}"
                 data-stock="${item.stock}">
                <!-- Item Image -->
                <div class="h-40 bg-gray-200 rounded-t-lg overflow-hidden relative">
                    <img src="${imageUrl}" 
                         alt="${item.name}" 
                         class="w-full h-full object-cover"
                         onerror="this.src='/images/Errorimage.jpg'">
                    
                    ${item.stock === 0 ? `
                        <div class="absolute top-2 right-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">
                            Unavailable
                        </div>
                    ` : ''}
                </div>
                
                <!-- Item Details -->
                <div class="p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-semibold text-gray-800 text-lg">${item.name}</h3>
                        <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full">
                            ${item.category}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-lg font-bold text-gray-800">₱${parseFloat(item.price).toFixed(2)}</span>
                        <span class="text-sm ${item.stock < 10 ? 'text-red-600 font-semibold' : 'text-gray-600'}">
                            Servings: ${item.stock}
                        </span>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex gap-2">
                        <button onclick="editMenuItem(${item.id})" 
                               class="flex-1 bg-gray-600 text-white py-2 px-3 rounded-lg text-sm font-semibold hover:bg-gray-700 transition">
                            Edit
                        </button>
                        <form method="POST" action="/admin/menu/${item.id}" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    onclick="return confirm('Are you sure you want to delete ${item.name}?')"
                                    class="w-full bg-red-600 text-white py-2 px-3 rounded-lg text-sm font-semibold hover:bg-red-700 transition">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        `;
    }

    function selectCategory(category) {
        selectedCategory = category;
        
        // Update button styles
        document.querySelectorAll('#categories-container button').forEach(button => {
            button.classList.remove('text-white', 'bg-gray-800');
            button.classList.add('bg-white', 'text-gray-800', 'border', 'border-gray-300');
        });
        
        // Highlight selected button
        const selectedButton = document.getElementById(`category-${category}`);
        if (selectedButton) {
            selectedButton.classList.remove('bg-white', 'text-gray-800', 'border', 'border-gray-300');
            selectedButton.classList.add('text-white', 'bg-gray-800');
        }
        
        renderMenuItems();
    }

    function showAddMenuItemForm() {
        editingItemId = null;
        document.getElementById('modalTitle').textContent = 'Add Menu Item';
        document.getElementById('menuItemForm').action = '{{ route("admin.menu.store") }}';
        
        // Clear any existing method spoofing
        const methodContainer = document.getElementById('methodContainer');
        methodContainer.innerHTML = '';
        
        // Reset form
        document.getElementById('itemName').value = '';
        document.getElementById('itemCategory').value = '';
        document.getElementById('itemPrice').value = '';
        document.getElementById('itemStock').value = '';
        document.getElementById('imageInput').value = '';
        document.getElementById('imagePreview').classList.add('hidden');
        document.getElementById('imagePlaceholder').classList.remove('hidden');
        
        // Show modal
        document.getElementById('menuItemModal').classList.remove('hidden');
    }

    function editMenuItem(itemId) {
        // Fetch the menu item details from the server
        fetch(`/admin/menu/${itemId}/edit`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(item => {
                // Populate the form fields with the item's data
                editingItemId = itemId;

                document.getElementById('modalTitle').textContent = 'Edit Menu Item';
                document.getElementById('menuItemForm').action = `/admin/menu/${itemId}`;
                
                // Set up method spoofing for PUT
                const methodContainer = document.getElementById('methodContainer');
                methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
                
                // Populate form fields
                document.getElementById('itemName').value = item.name;
                document.getElementById('itemCategory').value = item.category;
                document.getElementById('itemPrice').value = item.price;
                document.getElementById('itemStock').value = item.stock;

                // Handle image preview if there's an existing image
                if (item.image_url) {
                    // Use the full image URL from the backend
                    const imageUrl = item.image_url.includes('Errorimage.jpg') ? '' : item.image_url;
                    if (imageUrl) {
                        document.getElementById('imagePreview').src = imageUrl;
                        document.getElementById('imagePreview').classList.remove('hidden');
                        document.getElementById('imagePlaceholder').classList.add('hidden');
                    } else {
                        document.getElementById('imagePreview').classList.add('hidden');
                        document.getElementById('imagePlaceholder').classList.remove('hidden');
                    }
                } else {
                    document.getElementById('imagePreview').classList.add('hidden');
                    document.getElementById('imagePlaceholder').classList.remove('hidden');
                }

                // Show the modal
                document.getElementById('menuItemModal').classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error fetching item data:', error);
                alert('Error loading item data. Please try again.');
            });
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

    // Handle form submission
    document.getElementById('menuItemForm').addEventListener('submit', function(e) {
        // Form validation can be added here if needed
        // The form will submit normally to the defined route
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        selectCategory('all');  
    });
</script>
@endsection