<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kuya Benz POS - Cashier</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="h-screen overflow-hidden bg-gray-100">

<div class="w-full h-full flex">

    <!-- LEFT SIDE -->
    <div class="flex-1 bg-gray-50 flex flex-col">

        <!-- TOP NAV -->
        <nav class="bg-gray-800 text-white p-4 shadow">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold">Kuya Benz</h1>
                    <p class="text-sm text-gray-300">
                        Cashier: {{ auth()->user()->username }}
                    </p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="bg-gray-700 px-5 py-2 rounded-lg hover:bg-gray-600">
                        Logout
                    </button>
                </form>
            </div>
        </nav>

        <!-- MENU -->
        <div class="p-4 overflow-y-auto flex-1">

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">

                @foreach($menuItems as $item)
                <div onclick="addToOrder({{ $item->id }}, '{{ $item->name }}', {{ $item->price }})"
                     class="bg-white rounded-xl shadow hover:shadow-lg cursor-pointer p-4">

                    <img src="{{ $item->image ? asset('storage/'.$item->image) : asset('images/Errorimage.jpg') }}"
                         class="w-full h-28 object-cover rounded-lg mb-3">

                    <h3 class="font-bold">{{ $item->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $item->category }}</p>

                    <div class="flex justify-between mt-2">
                        <span class="font-bold">₱{{ number_format($item->price,2) }}</span>
                        <span class="text-sm text-gray-500">Stock: {{ $item->stock }}</span>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>

    <!-- RIGHT SIDE -->
    <div class="w-96 bg-white border-l flex flex-col">

        <div class="p-6 border-b">

            <h2 class="text-2xl font-bold mb-4">Current Order</h2>

            <input type="text" id="nickname"
                placeholder="Customer Name"
                class="w-full border px-4 py-3 rounded-lg mb-4">

            <select id="orderType"
                class="w-full border px-4 py-3 rounded-lg">
                <option>Dine In</option>
                <option>Take Out</option>
            </select>

        </div>

        <!-- ORDER LIST -->
        <div class="flex-1 overflow-y-auto p-4" id="orderList">
            <p class="text-center text-gray-500">No items</p>
        </div>

        <!-- PAYMENT -->
        <div class="p-6 border-t">

            <div class="flex justify-between text-xl font-bold mb-4">
                <span>Total:</span>
                <span id="totalText">₱0.00</span>
            </div>

            <input type="number"
                   id="payment"
                   placeholder="Cash Amount"
                   class="w-full border px-4 py-3 rounded-lg mb-4">

            <form method="POST" action="{{ route('cashier.process-order') }}" id="orderForm">
                @csrf

                <input type="hidden" name="items" id="itemsInput">
                <input type="hidden" name="payment_amount" id="paymentInput">
                <input type="hidden" name="nickname" id="nicknameInput">
                <input type="hidden" name="order_type" id="typeInput">

                <button type="button"
                    onclick="submitOrder()"
                    class="w-full bg-gray-800 text-white py-4 rounded-lg font-bold hover:bg-gray-700">
                    Process Payment
                </button>
            </form>

        </div>
    </div>
</div>

<!-- ADMIN REMOVE ITEM MODAL -->
<div id="adminRemoveModal"
     class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">

    <div class="bg-white rounded-xl w-96 p-6">

        <h2 class="text-xl font-bold mb-4">
            Admin Approval Required
        </h2>

        <div>

            <input type="text"
                   id="admin_username"
                   placeholder="Admin Username"
                   class="w-full border px-4 py-3 rounded-lg mb-3"
                   required>

            <input type="password"
                   id="admin_password"
                   placeholder="Admin Password"
                   class="w-full border px-4 py-3 rounded-lg mb-4"
                   required>

            <div class="flex gap-2">
                <button type="button"
                        onclick="closeAdminRemoveModal()"
                        class="flex-1 bg-gray-300 py-3 rounded-lg">
                    Cancel
                </button>

                <button type="button"
                        onclick="approveRemove(event)"
                        class="flex-1 bg-red-600 text-white py-3 rounded-lg hover:bg-red-700">
                    Approve
                </button>
            </div>

        </div>
    </div>
</div>

<script>

let order = [];
let pendingRemoveIndex = null;

/* ADD ITEM */
function addToOrder(id,name,price)
{
    let found = order.find(i => i.id === id);

    if(found){
        found.quantity++;
    } else {
        order.push({ id,name,price,quantity:1 });
    }

    renderOrder();
}

/* INCREASE */
function increaseQty(index)
{
    order[index].quantity++;
    renderOrder();
}

/* DECREASE (requires admin approval if last item) */
function decreaseQty(index)
{
    if(order[index].quantity > 1){
        order[index].quantity--;
        renderOrder();
    } else {
        requestRemoveItem(index);
    }
}

/* RENDER */
function renderOrder()
{
    let box = document.getElementById('orderList');
    let total = 0;

    if(order.length === 0){
        box.innerHTML = '<p class="text-center text-gray-500">No items</p>';
        document.getElementById('totalText').innerText = '₱0.00';
        return;
    }

    box.innerHTML = '';

    order.forEach((item,index)=>{

        total += item.price * item.quantity;

        box.innerHTML += `
        <div class="border rounded-lg p-3 mb-3">

            <div class="flex justify-between items-center">
                <strong>${item.name}</strong>

                <!-- KEEP X BUTTON -->
                <button onclick="requestRemoveItem(${index})"
                    class="text-red-600 font-bold">X</button>
            </div>

            <div class="flex justify-between items-center mt-2">

                <!-- QTY CONTROLS -->
                <div class="flex items-center gap-2">
                    <button onclick="decreaseQty(${index})"
                        class="bg-gray-300 px-2 rounded">-</button>

                    <span>${item.quantity}</span>

                    <button onclick="increaseQty(${index})"
                        class="bg-gray-300 px-2 rounded">+</button>
                </div>

                <span>₱${(item.price*item.quantity).toFixed(2)}</span>
            </div>

        </div>
        `;
    });

    document.getElementById('totalText').innerText = '₱' + total.toFixed(2);
}

/* REQUEST REMOVE */
function requestRemoveItem(index)
{
    pendingRemoveIndex = index;

    document.getElementById('adminRemoveModal').classList.remove('hidden');
}

/* CLOSE MODAL */
function closeAdminRemoveModal()
{
    document.getElementById('adminRemoveModal').classList.add('hidden');

    pendingRemoveIndex = null;

    // CLEAR INPUTS
    document.getElementById('admin_username').value = '';
    document.getElementById('admin_password').value = '';
}

/* APPROVE REMOVE */
function approveRemove(event)
{
    event.preventDefault();

    fetch('/admin/validate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            username: document.getElementById('admin_username').value,
            password: document.getElementById('admin_password').value
        })
    })
    .then(res => res.json())
    .then(data => {

        if(data.success){

            order.splice(pendingRemoveIndex,1);
            renderOrder();
            closeAdminRemoveModal();

        } else {
            alert("Invalid admin credentials");

            // CLEAR ON FAIL TOO (optional but better UX)
            document.getElementById('admin_password').value = '';
        }

    });
}

/* SUBMIT ORDER */
function submitOrder()
{
    document.getElementById('itemsInput').value = JSON.stringify(order);
    document.getElementById('paymentInput').value = document.getElementById('payment').value;
    document.getElementById('nicknameInput').value = document.getElementById('nickname').value;
    document.getElementById('typeInput').value = document.getElementById('orderType').value;

    document.getElementById('orderForm').submit();
}

</script>

</body>
</html>