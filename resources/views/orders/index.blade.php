<div class="card" style="margin-bottom: 1.5rem;">
    <h2 style="font-size: 1.25rem; font-weight: bold; margin-bottom: 1rem;">
        My Orders
    </h2>

    <ul style="display:flex; gap:1rem; margin-bottom:1rem; border-bottom:1px solid #ccc; padding-bottom:0.5rem;">
        <li><a href="#ongoing" onclick="showTab('ongoing')">Ongoing Orders</a></li>
        <li><a href="#recent" onclick="showTab('recent')">Recent Orders</a></li>
    </ul>

    {{-- Ongoing Orders --}}
    <div id="ongoing" class="tab">
        @forelse($ongoingOrders as $order)
            <div style="border:1px solid #ddd; padding:1rem; margin-bottom:1rem;">
                <strong>Pharmacy:</strong> {{ $order->pharmacy->shop_name }} <br>
                <strong>Status:</strong> {{ $order->status }} <br>
                <strong>Items:</strong>
                <ul>
                    @foreach($order->items as $item)
                        <li>{{ $item['name'] }} (x{{ $item['qty'] }})</li>
                    @endforeach
                </ul>
            </div>
        @empty
            <p style="color:#888;">No ongoing orders</p>
        @endforelse
    </div>

    {{-- Recent Orders --}}
    <div id="recent" class="tab" style="display:none;">
        @forelse($recentOrders as $order)
            <div style="border:1px solid #ddd; padding:1rem; margin-bottom:1rem;">
                <strong>Pharmacy:</strong> {{ $order->pharmacy->shop_name }} <br>
                <strong>Status:</strong> {{ $order->status }} <br>
                <strong>Items:</strong>
                <ul>
                    @foreach($order->items as $item)
                        <li>{{ $item['name'] }} (x{{ $item['qty'] }})</li>
                    @endforeach
                </ul>
            </div>
        @empty
            <p style="color:#888;">No past orders</p>
        @endforelse
    </div>
</div>

<script>
function showTab(tab) {
    document.getElementById('ongoing').style.display = 'none';
    document.getElementById('recent').style.display = 'none';
    document.getElementById(tab).style.display = 'block';
}
</script>
