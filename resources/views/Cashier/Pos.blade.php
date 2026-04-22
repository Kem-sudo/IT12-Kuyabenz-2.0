<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuya Benz POS - Cashier</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            height: 100vh;
            overflow: hidden;
        }
        
        .btn-primary {
            transition: all 0.2s;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .card {
            transition: all 0.2s;
        }
        
        .card:hover {
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        
        .menu-item {
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .menu-item:hover {
            transform: scale(1.02);
        }
        
        .order-item {
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        /* Custom scrollbar for menu items */
        #menu-items-container::-webkit-scrollbar {
            width: 8px;
        }
        
        #menu-items-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        #menu-items-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        
        #menu-items-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>
<body>
    <div class="w-full h-full flex" style="height: 100vh;">
        <!-- LEFT PANEL - MENU ITEMS -->
        <div class="flex-1 bg-gray-50 flex flex-col" style="height: 100vh;">
            <nav class="text-white p-4 shadow-lg bg-gray-800">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold">Kuya Benz</h1>
                        <p class="text-sm text-gray-300">Cashier: {{ auth()->user()->username }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="px-6 py-2 bg-gray-700 text-white rounded-lg font-semibold hover:bg-gray-600 transition">
                            Logout
                        </button>
                    </form>
                </div>
            </nav>
            
            <div class="p-4 flex flex-col" style="height: calc(100vh - 84px);">
                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- Categories -->
                <div class="flex gap-2 mb-4 overflow-x-auto pb-2" id="categories-container">
                    <!-- Categories will be loaded here -->
                </div>
                
                <!-- Menu Items Grid - FIXED HEIGHT WITH SCROLLING -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 overflow-y-auto pb-4" 
                     id="menu-items-container"
                     style="max-height: calc(100vh - 200px);">
                    <!-- This will be populated by JavaScript -->
                </div>
            </div>
        </div>
        
        <!-- RIGHT PANEL - CURRENT ORDER -->
        <div class="w-96 bg-white border-l border-gray-200 flex flex-col" style="height: 100vh;">
            <div class="p-6 border-b border-gray-200 bg-white flex-shrink-0">
                <h2 class="text-2xl font-bold mb-4 text-gray-800">Current Order</h2>
                
                <!-- CUSTOMER NICKNAME (Temporary - Not stored in DB) -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2 text-gray-700">Name</label>
                    <input type="text" id="nickname-input" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 text-gray-800"
                           maxlength="30"
                           oninput="updateOrderHeader()">
                </div>
                
                <!-- ORDER TYPE -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2 text-gray-700">Order Type</label>
                    <div class="flex gap-2">
                        <button onclick="selectOrderType('Dine In')" id="btnDineIn" class="flex-1 py-2 rounded-lg font-semibold text-sm text-white bg-gray-800">
                            Dine In
                        </button>
                        <button onclick="selectOrderType('Take Out')" id="btnTakeOut" class="flex-1 py-2 rounded-lg font-semibold text-sm bg-gray-200 text-gray-800 hover:bg-gray-300">
                            Take Out
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- ORDER ITEMS WITH NICKNAME DISPLAY - SCROLLABLE AREA -->
            <div class="flex-1 overflow-y-auto p-4" id="order-items-container" style="min-height: 0;">
                <!-- Nickname display inside order (appears when nickname entered) -->
                <div id="nickname-display" class="mb-4 p-3 bg-blue-50 rounded-lg border-l-4 border-blue-500 hidden">
                    <p class="text-sm font-semibold text-gray-800">For: <span id="nickname-display-text" class="text-blue-600 font-bold"></span></p>
                </div>
                
                <!-- Order items list -->
                <div id="order-items-list">
                    <p class="text-center text-gray-500 py-8">No items in order</p>
                </div>
            </div>
            
            <!-- PAYMENT SECTION -->
            <div class="p-6 border-t border-gray-200 bg-white flex-shrink-0">
                <div class="flex justify-between items-center mb-4 text-2xl font-bold">
                    <span class="text-gray-800">Total:</span>
                    <span class="text-gray-800" id="order-total">₱0.00</span>
                </div>
                
                <div class="mb-4">
                    <div class="w-full px-4 py-3 bg-gray-100 rounded-lg text-lg font-semibold text-center text-gray-800">
                        Cash
                    </div>
                    <input type="hidden" id="payment-method" value="Cash">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2 text-gray-700">Cash Payment Amount</label>
                    <input type="number" id="payment-amount" step="0.01" min="0" placeholder="Enter cash amount"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg text-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500" oninput="calculateChange()">
                </div>
                
                <div id="change-display" class="mb-4 p-4 bg-gray-100 rounded-lg hidden">
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-gray-800">Change:</span>
                        <span class="text-2xl font-bold text-green-600" id="change-amount">₱0.00</span>
                    </div>
                </div>
                
                <!-- ORDER FORM -->
                <form method="POST" action="{{ route('cashier.process-order') }}" id="order-form">
                    @csrf
                    <input type="hidden" name="items" id="order-items-input">
                    <input type="hidden" name="payment_amount" id="payment-amount-input">
                    <input type="hidden" name="order_type" id="order-type-input" value="Dine In">
                    <input type="hidden" name="nickname" id="nickname-hidden-input">
                    
                    <button type="button" onclick="submitOrder()" id="process-order-btn"
                            class="w-full bg-gray-800 text-white py-4 rounded-lg font-bold text-lg opacity-50 cursor-not-allowed hover:bg-gray-700 transition"
                            disabled>
                        Process Payment
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Pass menu items from Laravel to JavaScript
        const menuItems = @json($menuItems);
        
        let currentOrder = [];
        let selectedCategory = 'all';
        let currentOrderType = 'Dine In';

        function renderCategories() {
            const categories = ['all', ...new Set(menuItems.map(item => item.category))];
            const container = document.getElementById('categories-container');
            
            container.innerHTML = categories.map(cat => `
                <button onclick="selectCategory('${cat}')"
                        class="px-4 py-2 rounded-lg font-semibold whitespace-nowrap ${selectedCategory === cat ? 'text-white bg-gray-800' : 'bg-white text-gray-800 border border-gray-300 hover:bg-gray-100'}">
                    ${cat === 'all' ? 'All Items' : cat}
                </button>
            `).join('');
        }

        function renderMenuItems() {
    // Filter items by category
    let filteredItems = selectedCategory === 'all'
        ? [...menuItems]
        : menuItems.filter(item => item.category === selectedCategory);

    const inStockItems = filteredItems.filter(item => item.stock > 0);
    const outOfStockItems = filteredItems.filter(item => item.stock === 0);

    const sortedItems = [...inStockItems, ...outOfStockItems];
    const container = document.getElementById('menu-items-container');

    if (sortedItems.length === 0) {
        container.innerHTML = `
            <div class="col-span-full text-center text-gray-500 py-8">
                <p>No items available in this category</p>
            </div>
        `;
        return;
    }

    container.innerHTML = sortedItems.map((item, index) => {
        // --- FIXED IMAGE PATH ---
        let imageUrl = '/images/Errorimage.jpg'; // fallback by default
        if (item.image && item.image.trim() !== '') {
            imageUrl = '/storage/' + item.image.trim();
        }

        // --- DEBUG LOG ---
        console.log('Menu Item:', item.name, 'Image:', item.image, 'URL:', imageUrl);

        return `
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition cursor-pointer relative ${item.stock === 0 ? 'opacity-50' : ''}"
                 onclick="${item.stock === 0 ? '' : `addToOrder(${item.id})`}">
                <div class="h-24 bg-gray-200 rounded-t-lg overflow-hidden">
                    <img src="${imageUrl}" 
                         alt="${item.name}" 
                         class="w-full h-full object-cover"
                         onerror="this.src='/images/Errorimage.jpg'">
                </div>

                <div class="p-3">
                    <h3 class="font-semibold text-gray-800 text-sm mb-1">${item.name}</h3>
                    <p class="text-xs text-gray-600 mb-2">${item.category}</p>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-gray-800 text-sm">₱${parseFloat(item.price).toFixed(2)}</span>
                        <span class="text-xs ${item.stock < 10 ? 'text-red-600 font-semibold' : 'text-gray-500'}">
                            Servings: ${item.stock}
                        </span>
                    </div>
                </div>

                ${item.stock === 0 ? `
                    <span class="absolute top-2 right-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">
                        Unavailable
                    </span>
                ` : ''}
            </div>
        `;
    }).join('');

    // Add separator for out-of-stock items
    if (inStockItems.length > 0 && outOfStockItems.length > 0) {
        const items = container.children;
        if (items.length >= inStockItems.length) {
            const separator = document.createElement('div');
            separator.className = 'col-span-full border-t border-gray-300 mt-2 mb-2 relative';
            separator.innerHTML = `
                <span class="absolute left-1/2 transform -translate-x-1/2 -mt-3 px-4 bg-gray-50 text-gray-500 text-sm font-medium">
                    Unavailable Menu
                </span>
            `;
            items[inStockItems.length].parentNode.insertBefore(separator, items[inStockItems.length]);
        }
    }
}

        function selectCategory(category) {
            selectedCategory = category;
            renderCategories();
            renderMenuItems();
        }

        // Update order header with nickname
        function updateOrderHeader() {
            const nickname = document.getElementById('nickname-input').value.trim();
            const nicknameDisplay = document.getElementById('nickname-display');
            const nicknameText = document.getElementById('nickname-display-text');
            
            if (nickname) {
                nicknameText.textContent = nickname;
                nicknameDisplay.classList.remove('hidden');
            } else {
                nicknameDisplay.classList.add('hidden');
            }
        }

        function selectOrderType(type) {
            currentOrderType = type;
            document.getElementById('order-type-input').value = type;
            
            const btnDineIn = document.getElementById('btnDineIn');
            const btnTakeOut = document.getElementById('btnTakeOut');
            
            if (type === 'Dine In') {
                btnDineIn.className = 'flex-1 py-2 rounded-lg font-semibold text-sm text-white bg-gray-800';
                btnTakeOut.className = 'flex-1 py-2 rounded-lg font-semibold text-sm bg-gray-200 text-gray-800 hover:bg-gray-300';
            } else {
                btnTakeOut.className = 'flex-1 py-2 rounded-lg font-semibold text-sm text-white bg-gray-800';
                btnDineIn.className = 'flex-1 py-2 rounded-lg font-semibold text-sm bg-gray-200 text-gray-800 hover:bg-gray-300';
            }
        }

        function addToOrder(itemId) {
            const item = menuItems.find(m => m.id === itemId);
            if (!item || item.stock === 0) return;
            
            const existingItem = currentOrder.find(orderItem => orderItem.id === item.id);
            if (existingItem) {
                if (existingItem.quantity < item.stock) {
                    existingItem.quantity++;
                } else {
                    alert(`Only ${item.stock} items available in stock!`);
                    return;
                }
            } else {
                currentOrder.push({
                    id: item.id,
                    name: item.name,
                    price: parseFloat(item.price),
                    quantity: 1
                });
            }
            renderOrderSummary();
        }

        function removeFromOrder(index) {
            currentOrder.splice(index, 1);
            renderOrderSummary();
        }

        function increaseQuantity(index) {
            const orderItem = currentOrder[index];
            const menuItem = menuItems.find(m => m.id === orderItem.id);
            if (orderItem.quantity < menuItem.stock) {
                orderItem.quantity++;
                renderOrderSummary();
            } else {
                alert(`Only ${menuItem.stock} items available in stock!`);
            }
        }

        function decreaseQuantity(index) {
            if (currentOrder[index].quantity > 1) {
                currentOrder[index].quantity--;
                renderOrderSummary();
            } else {
                removeFromOrder(index);
            }
        }

        function renderOrderSummary() {
            const container = document.getElementById('order-items-list');
            const totalElement = document.getElementById('order-total');
            const processBtn = document.getElementById('process-order-btn');
            
            const orderTotal = currentOrder.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            
            totalElement.textContent = `₱${orderTotal.toFixed(2)}`;
            
            if (currentOrder.length === 0) {
                container.innerHTML = '<p class="text-center text-gray-500 py-8">No items in order</p>';
                processBtn.disabled = true;
                processBtn.classList.add('opacity-50', 'cursor-not-allowed');
                return;
            }
            
            processBtn.disabled = false;
            processBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            
            // Show nickname if entered
            updateOrderHeader();
            
            container.innerHTML = currentOrder.map((item, index) => `
                <div class="order-item p-3 border border-gray-200 rounded-lg mb-3 bg-white shadow-sm">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-semibold flex-1 text-gray-800">${item.name}</h4>
                        <button onclick="removeFromOrder(${index})" class="text-red-600 hover:text-red-800 font-bold ml-2 text-lg">✕</button>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <button onclick="decreaseQuantity(${index})" class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-white bg-gray-600 hover:bg-gray-700 transition">-</button>
                            <span class="font-semibold w-8 text-center text-gray-800">${item.quantity}</span>
                            <button onclick="increaseQuantity(${index})" class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-white bg-gray-600 hover:bg-gray-700 transition">+</button>
                        </div>
                        <span class="font-bold text-gray-800">₱${(item.price * item.quantity).toFixed(2)}</span>
                    </div>
                </div>
            `).join('');
            
            calculateChange();
        }

        function calculateChange() {
            const paymentAmount = parseFloat(document.getElementById('payment-amount').value) || 0;
            const orderTotal = currentOrder.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const change = paymentAmount - orderTotal;
            
            const changeDisplay = document.getElementById('change-display');
            const changeAmount = document.getElementById('change-amount');
            
            if (paymentAmount > 0 && change >= 0) {
                changeAmount.textContent = `₱${change.toFixed(2)}`;
                changeDisplay.classList.remove('hidden');
            } else {
                changeDisplay.classList.add('hidden');
            }
        }

        function submitOrder() {
            if (currentOrder.length === 0) return;
            
            const paymentAmount = parseFloat(document.getElementById('payment-amount').value) || 0;
            const orderTotal = currentOrder.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            
            if (paymentAmount < orderTotal) {
                alert('Insufficient payment amount! Please enter sufficient cash amount.');
                return;
            }
            
            if (paymentAmount === 0) {
                alert('Please enter the cash payment amount.');
                return;
            }
            
            // Get nickname (can be same as previous customers)
            const nickname = document.getElementById('nickname-input').value.trim();
            
            // Set the form values
            document.getElementById('order-items-input').value = JSON.stringify(currentOrder);
            document.getElementById('payment-amount-input').value = paymentAmount;
            document.getElementById('nickname-hidden-input').value = nickname;
            
            // Disable button and show loading
            const btn = document.getElementById('process-order-btn');
            btn.disabled = true;
            btn.textContent = nickname ? `Processing for ${nickname}...` : 'Processing...';
            
            // Submit the form
            document.getElementById('order-form').submit();
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            renderCategories();
            renderMenuItems();
            selectOrderType('Dine In');
        });
    </script>
</body>
</html>