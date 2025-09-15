<form action="{{ route('prescriptions.store') }}" method="POST">
    @csrf

    <!-- Prescription fields here -->
    <div class="mb-4">
        <label for="medicine" class="block text-sm font-medium">Medicine</label>
        <input type="text" name="medicine" id="medicine" class="mt-1 block w-full border rounded p-2">
    </div>

    <div class="mb-4">
        <label for="quantity" class="block text-sm font-medium">Quantity</label>
        <input type="number" name="quantity" id="quantity" class="mt-1 block w-full border rounded p-2">
    </div>

    <!-- Payment method -->
    <div class="mb-4">
        <label for="payment_method" class="block text-sm font-medium">Payment Method</label>
        <select name="payment_method" id="payment_method" class="mt-1 block w-full border rounded p-2">
            <option value="cod">Cash on Delivery</option>
            <option value="card">Card Payment</option>
        </select>
    </div>

    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
        Place Prescription
    </button>
</form>
