{{-- Stock Urgency Component - Shows "Only X left!" for low stock items --}}
@php
    $stockThreshold = 10; // Show urgency when stock is at or below this number
    $showUrgency = ($product['product_type'] == 'physical') && 
                   ($product['current_stock'] > 0) && 
                   ($product['current_stock'] <= $stockThreshold);
@endphp

@if($showUrgency)
    <div class="stock-urgency d-flex align-items-center gap-1 {{ $compact ?? false ? 'fs-10' : 'fs-12' }}">
        <span class="text-danger fw-bold animate-pulse">
            🔥 {{ translate('Only') }} {{ $product['current_stock'] }} {{ translate('left') }}!
        </span>
        @if($product['current_stock'] <= 3)
            <span class="badge bg-danger text-white">{{ translate('Selling_fast') }}</span>
        @endif
    </div>
@elseif(($product['product_type'] == 'physical') && ($product['current_stock'] <= 0))
    <div class="stock-urgency">
        <span class="text-muted fs-12">{{ translate('Out_of_stock') }}</span>
    </div>
@endif

<style>
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}
</style>
