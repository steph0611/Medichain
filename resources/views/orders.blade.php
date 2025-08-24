@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto mt-10">
    <h2 class="text-2xl font-bold mb-6 text-center">Orders</h2>

    {{-- Place Order Form --}}
    <div class="mb-6">
        <form action="{{ route('orders.store') }}" method="POST" class="flex gap-2">
            @csrf
            <input type="text" name="customer_id" placeholder="Customer ID" class="border p-2 rounded" required>
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Place Order</button>
        </form>
    </div>

    @if(empty($orders) || count($orders) === 0)
        <p class="text-center text-gray-600">No orders yet.</p>
    @else
        @foreach($orders as $order)
        <div class="bg-white shadow-md rounded-xl p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Order #{{ $order['id'] }}</h3>
                <span class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($order['created_at'])->format('M d, Y H:i') }}</span>
            </div>

            {{-- Status tracker --}}
            <div class="flex items-center justify-between">
                @php
                    $steps = ['pending', 'accepted', 'processing', 'ready', 'delivered'];
                @endphp

                @foreach($steps as $step)
                    <div class="flex-1 flex flex-col items-center relative">
                        <div class="w-10 h-10 flex items-center justify-center rounded-full
                            {{ array_search($order['status'], $steps) >= array_search($step, $steps) 
                                ? 'bg-green-500 text-white' 
                                : 'bg-gray-300 text-gray-600' }}">
                            @if($step === 'pending') ⏳
                            @elseif($step === 'accepted') ✅
                            @elseif($step === 'processing') 🔄
                            @elseif($step === 'ready') 🍽️
                            @elseif($step === 'delivered') 🚚
                            @endif
                        </div>
                        <span class="mt-2 text-sm capitalize">{{ $step }}</span>

                        @if(!$loop->last)
                            <div class="absolute top-5 left-1/2 w-full h-1 
                                {{ array_search($order['status'], $steps) > array_search($step, $steps) 
                                    ? 'bg-green-500' 
                                    : 'bg-gray-300' }}">
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endforeach
    @endif
</div>
@endsection
