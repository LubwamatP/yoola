<!-- Yoola Floating WhatsApp Order Button -->
<style>
/* HIDE OLD WHATSAPP WIDGET */
.social-chat-icons,
.social-chat-icons * { 
    display: none !important; 
    visibility: hidden !important;
    width: 0 !important;
    height: 0 !important;
}

/* Our floating button */
.yoola-wa-float {
    position: fixed;
    bottom: 80px;
    right: 20px;
    z-index: 9999;
    display: flex !important;
    align-items: center;
    gap: 10px;
    background: #25D366;
    color: white !important;
    padding: 12px 20px;
    border-radius: 50px;
    box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4);
    text-decoration: none !important;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
}
.yoola-wa-float:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(37, 211, 102, 0.5);
    color: white !important;
}
.yoola-wa-float i { font-size: 20px; }

@media (max-width: 768px) {
    .yoola-wa-float {
        bottom: 75px;
        right: 15px;
        padding: 12px 16px;
    }
    .yoola-wa-float span { display: none; }
    .yoola-wa-float i { font-size: 24px; }
}
</style>

<a href="https://wa.me/256704229768?text=Hi!%20I%20want%20to%20order%20from%20Yoola" 
   target="_blank" class="yoola-wa-float" id="yoola-wa-btn">
    <i class="bi bi-whatsapp"></i>
    <span>Order on WhatsApp</span>
</a>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var waBtn = document.getElementById('yoola-wa-btn');
    if (!waBtn) return;
    
    // Check if we're on a product page
    if (window.location.href.includes('/product/')) {
        // Get product title - try multiple selectors
        var titleEl = document.querySelector('.discounted-unit-price')?.closest('.product__price')?.parentElement?.querySelector('h1, h2') 
                   || document.querySelector('h2.fs-16.mb-2')
                   || document.querySelector('.product-title-text')
                   || document.querySelector('h1');
        
        // Get the CORRECT price - discounted-unit-price is the actual selling price
        var priceEl = document.querySelector('.discounted-unit-price');
        
        if (titleEl || priceEl) {
            var name = titleEl ? titleEl.textContent.trim().substring(0, 80) : 'this product';
            var price = priceEl ? 'UGX ' + priceEl.textContent.trim() : '';
            var url = window.location.href;
            
            var msg = 'Hi Yoola! I want to order:\n\n';
            msg += '📦 ' + name + '\n';
            if (price) msg += '💰 Price: ' + price + '\n';
            msg += '🔗 ' + url;
            
            waBtn.href = 'https://wa.me/256704229768?text=' + encodeURIComponent(msg);
        }
    }
});
</script>