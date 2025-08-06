@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">My Orders</h1>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if (empty($orders))
        <p class="text-gray-600">You have no orders yet.</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 shadow-md rounded-lg">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="py-2 px-4 border-b">Order ID</th>
                        <th class="py-2 px-4 border-b">Image</th>
                        <th class="py-2 px-4 border-b">Note</th>
                        <th class="py-2 px-4 border-b">Status</th>
                        <th class="py-2 px-4 border-b">Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr class="text-center border-b">
                            <td class="py-2 px-4">{{ $order['id'] }}</td>
                            <td class="py-2 px-4">
                                @if (!empty($order['image']))
                                    <img src="{{ $order['image'] }}" alt="Order Image" class="w-16 h-16 object-cover mx-auto rounded">
                                @else
                                    <span class="text-gray-500 italic">No Image</span>
                                @endif
                            </td>
                            <td class="py-2 px-4">{{ $order['note'] ?? '-' }}</td>
                            <td class="py-2 px-4">
                                <span class="px-2 py-1 rounded text-white
                                    @switch($order['status'])
                                        @case('Pending') bg-yellow-500 @break
                                        @case('Accepted') bg-blue-500 @break
                                        @case('Ready') bg-green-500 @break
                                        @case('Delivered') bg-gray-500 @break
                                        @default bg-red-500
                                    @endswitch
                                ">
                                    {{ $order['status'] }}
                                </span>
                            </td>
                            <td class="py-2 px-4">{{ \Carbon\Carbon::parse($order['created_at'])->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
