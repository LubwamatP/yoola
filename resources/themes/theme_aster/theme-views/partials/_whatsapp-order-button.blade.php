{{-- WhatsApp Order Button Partial --}}
@if(isset($product) || isset($productDetails))
@php
    $productName = isset($productDetails) ? $productDetails->name : $product->name;
    $waMessage = urlencode("Hi! I want to order: " . $productName);
@endphp
<a href="https://wa.me/256704229768?text={{ $waMessage }}" 
   target="_blank" 
   class="btn fs-16 text-white d-flex align-items-center justify-content-center gap-2 mt-2"
   style="background-color: #25D366; border-color: #25D366; width: 100%;">
    <i class="bi bi-whatsapp"></i>
    <span>{{ translate("Order_via_WhatsApp") }}</span>
</a>
@endif
