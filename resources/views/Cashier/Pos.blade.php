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
    </style>
</head>
<body>
    <div class="w-full h-full flex" style="min-height: 100vh;">
        <div class="flex-1" style="background-color: #f3f4f6;">
            <nav class="text-white p-4 shadow-lg" style="background-color: #dc2626;">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold">Kuya Benz</h1>
                        <p class="text-sm opacity-90">Cashier: {{ auth()->user()->username }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="px-6 py-2 bg-white rounded-lg font-semibold" style="color: #dc2626;">
                            Logout
                        </button>
                    </form>
                </div>
            </nav>
            
            <div class="p-4">
                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="flex gap-2 mb-4 overflow-x-auto pb-2" id="categories-container">
                    <!-- Categories will be loaded here -->
                </div>
                
                <<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" id="menu-items-container">
    <!-- This will be populated by JavaScript -->
</div>
            </div>
        </div>
        
        <div class="w-96 bg-white shadow-2xl flex flex-col" style="height: 100vh;">
            <div class="p-6 border-b" style="background-color: #ffffff;">
                <h2 class="text-2xl font-bold mb-4" style="color: #1f2937;">Current Order</h2>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2" style="color: #1f2937;">Order Type</label>
                    <div class="flex gap-2">
                        <button onclick="selectOrderType('Dine In')" id="btnDineIn" class="flex-1 py-2 rounded-lg font-semibold text-sm text-white" style="background-color: #dc2626;">
                            🍽️ Dine In
                        </button>
                        <button onclick="selectOrderType('Take Out')" id="btnTakeOut" class="flex-1 py-2 rounded-lg font-semibold text-sm bg-gray-200" style="color: #1f2937;">
                            🥡 Take Out
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto p-4" id="order-items-container">
                <p class="text-center text-gray-500 py-8">No items in order</p>
            </div>
            
            <div class="p-6 border-t" style="background-color: #ffffff;">
                <div class="flex justify-between items-center mb-4 text-2xl font-bold">
                    <span style="color: #1f2937;">Total:</span>
                    <span style="color: #dc2626;" id="order-total">₱0.00</span>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2" style="color: #1f2937;">Payment Method</label>
                    <div class="w-full px-4 py-3 bg-gray-100 rounded-lg text-lg font-semibold text-center" style="color: #1f2937;">
                        💵 Cash
                    </div>
                    <input type="hidden" id="payment-method" value="Cash">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2" style="color: #1f2937;">Cash Payment Amount</label>
                    <input type="number" id="payment-amount" step="0.01" min="0" placeholder="Enter cash amount" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg text-lg" oninput="calculateChange()">
                </div>
                
                <div id="change-display" class="mb-4 p-4 bg-gray-100 rounded-lg hidden">
                    <div class="flex justify-between items-center">
                        <span class="font-medium" style="color: #1f2937;">Change:</span>
                        <span class="text-2xl font-bold" style="color: #10b981;" id="change-amount">₱0.00</span>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('cashier.process-order') }}" id="order-form">
                    @csrf
                    <input type="hidden" name="items" id="order-items-input">
                    <input type="hidden" name="payment_amount" id="payment-amount-input">
                    <input type="hidden" name="order_type" id="order-type-input" value="Dine In">
                    
                    <button type="button" onclick="submitOrder()" id="process-order-btn" 
                            class="w-full text-white py-4 rounded-lg font-bold text-lg opacity-50 cursor-not-allowed" 
                            style="background-color: #dc2626;" disabled>
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
                        class="px-4 py-2 rounded-lg font-semibold whitespace-nowrap ${selectedCategory === cat ? 'text-white' : 'bg-white'}" 
                        style="background-color: ${selectedCategory === cat ? '#dc2626' : 'white'}; color: ${selectedCategory === cat ? 'white' : '#1f2937'};">
                    ${cat === 'all' ? 'All Items' : cat}
                </button>
            `).join('');
        }

         function renderMenuItems() {
        const filteredItems = selectedCategory === 'all' 
            ? menuItems 
            : menuItems.filter(item => item.category === selectedCategory);
        
        const container = document.getElementById('menu-items-container');
        
        if (filteredItems.length === 0) {
            container.innerHTML = `
                <div class="col-span-full text-center text-gray-500 py-8">
                    <div class="text-4xl mb-2">🍽️</div>
                    <p>No items available in this category</p>
                </div>
            `;
            return;
        }

        container.innerHTML = filteredItems.map(item => `
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition cursor-pointer ${item.stock === 0 ? 'opacity-50' : ''}" 
                 onclick="${item.stock === 0 ? '' : `addToOrder(${item.id})`}">
                <!-- Item Image -->
                <div class="h-24 bg-gray-200 rounded-t-lg overflow-hidden">
                    <img src="${item.image_url || '/images/default-food.png'}" 
                         alt="${item.name}" 
                         class="w-full h-full object-cover"
                         onerror="this.src='/images/default-food.png'">
                </div>
                
                <!-- Item Details -->
                <div class="p-3">
                    <h3 class="font-semibold text-gray-800 text-sm mb-1">${item.name}</h3>
                    <p class="text-xs text-gray-600 mb-2">${item.category}</p>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-green-600 text-sm">₱${parseFloat(item.price).toFixed(2)}</span>
                        <span class="text-xs ${item.stock < 10 ? 'text-red-600 font-semibold' : 'text-gray-500'}">
                            Stock: ${item.stock}
                        </span>
                    </div>
                </div>
                
                ${item.stock === 0 ? 
                    '<div class="absolute inset-0 bg-red-50 bg-opacity-80 flex items-center justify-center rounded-lg"><span class="text-red-600 text-xs font-semibold">Out of Stock</span></div>' : 
                    ''
                }
            </div>
        `).join('');
    }

        function selectCategory(category) {
            selectedCategory = category;
            renderCategories();
            renderMenuItems();
        }

        function selectOrderType(type) {
            currentOrderType = type;
            document.getElementById('order-type-input').value = type;
            
            const btnDineIn = document.getElementById('btnDineIn');
            const btnTakeOut = document.getElementById('btnTakeOut');
            
            if (type === 'Dine In') {
                btnDineIn.style.backgroundColor = '#dc2626';
                btnDineIn.style.color = 'white';
                btnTakeOut.style.backgroundColor = '#e5e7eb';
                btnTakeOut.style.color = '#1f2937';
            } else {
                btnTakeOut.style.backgroundColor = '#dc2626';
                btnTakeOut.style.color = 'white';
                btnDineIn.style.backgroundColor = '#e5e7eb';
                btnDineIn.style.color = '#1f2937';
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
            const container = document.getElementById('order-items-container');
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
            
            container.innerHTML = currentOrder.map((item, index) => `
                <div class="order-item p-3 border border-gray-200 rounded-lg mb-3 bg-white shadow-sm">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-semibold flex-1" style="color: #1f2937;">${item.name}</h4>
                        <button onclick="removeFromOrder(${index})" class="text-red-600 hover:text-red-800 font-bold ml-2 text-lg">✕</button>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <button onclick="decreaseQuantity(${index})" class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-white hover:bg-orange-600 transition" style="background-color: #f59e0b;">-</button>
                            <span class="font-semibold w-8 text-center">${item.quantity}</span>
                            <button onclick="increaseQuantity(${index})" class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-white hover:bg-orange-600 transition" style="background-color: #f59e0b;">+</button>
                        </div>
                        <span class="font-bold" style="color: #dc2626;">₱${(item.price * item.quantity).toFixed(2)}</span>
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
            
            // Set the form values
            document.getElementById('order-items-input').value = JSON.stringify(currentOrder);
            document.getElementById('payment-amount-input').value = paymentAmount;
            
            // Disable button and show loading
            const btn = document.getElementById('process-order-btn');
            btn.disabled = true;
            btn.textContent = 'Processing...';
            
            // Submit the form
            document.getElementById('order-form').submit();
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            renderCategories();
            renderMenuItems();
            selectOrderType('Dine In');
            console.log('Menu items loaded:', menuItems);
        });
    </script>
</body>
</html>