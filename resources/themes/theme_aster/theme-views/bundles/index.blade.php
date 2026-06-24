@extends('theme-views.layouts.app')

@section('title', 'Bundle Deals - Save Big | Yoola Uganda')
@section('meta_description', 'Save up to 20% with Yoola bundle deals. TV + Soundbar, Kitchen Starter Pack, Home Entertainment bundles. Free delivery Kampala.')

@section('content')
<div class="container py-4">
    <!-- Hero -->
    <div class="text-center mb-5 bg-danger text-white p-5 rounded">
        <h1 class="display-5 fw-bold">🎁 Bundle Deals</h1>
        <p class="lead mb-0">Save BIG when you buy more! Up to 20% OFF on bundles.</p>
    </div>
    
    <!-- Bundle Categories -->
    <div class="row g-4">
        
        <!-- TV + Audio Bundles -->
        <div class="col-12">
            <h2 class="border-bottom pb-2 mb-4">📺 TV & Audio Bundles</h2>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-danger">
                <div class="card-header bg-danger text-white text-center">
                    <span class="badge bg-warning text-dark">SAVE 15%</span>
                    <h5 class="mt-2 mb-0">Cinema Experience</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li>✓ Hisense 43" Smart TV</li>
                        <li>✓ Hisense 2.1ch Soundbar</li>
                        <li>✓ HDMI Cable</li>
                        <li>✓ Surge Protector</li>
                    </ul>
                    <p class="text-muted small">Perfect for apartments and living rooms</p>
                    <div class="text-center">
                        <del class="text-muted">UGX 1,500,000</del>
                        <div class="h4 text-danger fw-bold">UGX 1,275,000</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="https://wa.me/256704229768?text=Hi!%20I%20want%20the%20Cinema%20Experience%20Bundle%20(43%22%20TV%20%2B%20Soundbar)" 
                       class="btn btn-success w-100">
                        <i class="bi bi-whatsapp"></i> Order This Bundle
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-primary">
                <div class="card-header bg-primary text-white text-center">
                    <span class="badge bg-warning text-dark">MOST POPULAR</span>
                    <h5 class="mt-2 mb-0">Home Theater Setup</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li>✓ Hisense 55" 4K Smart TV</li>
                        <li>✓ Hisense 5.1ch Soundbar</li>
                        <li>✓ TV Wall Mount</li>
                        <li>✓ Premium HDMI Cable</li>
                        <li>✓ Surge Protector</li>
                    </ul>
                    <p class="text-muted small">The ultimate movie night experience</p>
                    <div class="text-center">
                        <del class="text-muted">UGX 2,800,000</del>
                        <div class="h4 text-primary fw-bold">UGX 2,380,000</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="https://wa.me/256704229768?text=Hi!%20I%20want%20the%20Home%20Theater%20Bundle%20(55%22%204K%20TV%20%2B%205.1%20Soundbar)" 
                       class="btn btn-success w-100">
                        <i class="bi bi-whatsapp"></i> Order This Bundle
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-dark text-white text-center">
                    <span class="badge bg-danger">PREMIUM</span>
                    <h5 class="mt-2 mb-0">Ultimate Entertainment</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li>✓ CHiQ 65" 4K Smart TV</li>
                        <li>✓ JBL 5.1ch Soundbar</li>
                        <li>✓ Professional Installation</li>
                        <li>✓ Wall Mount + Cables</li>
                        <li>✓ Extended 2-Year Warranty</li>
                    </ul>
                    <p class="text-muted small">For serious home cinema enthusiasts</p>
                    <div class="text-center">
                        <del class="text-muted">UGX 4,500,000</del>
                        <div class="h4 text-dark fw-bold">UGX 3,825,000</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="https://wa.me/256704229768?text=Hi!%20I%20want%20the%20Ultimate%20Entertainment%20Bundle%20(65%22%204K%20%2B%20JBL%20Soundbar)" 
                       class="btn btn-success w-100">
                        <i class="bi bi-whatsapp"></i> Order This Bundle
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Kitchen Bundles -->
        <div class="col-12 mt-5">
            <h2 class="border-bottom pb-2 mb-4">🍳 Kitchen Bundles</h2>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-success text-white text-center">
                    <span class="badge bg-warning text-dark">STARTER</span>
                    <h5 class="mt-2 mb-0">Bachelor Kitchen</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li>✓ Mini Fridge 90L</li>
                        <li>✓ Electric Kettle</li>
                        <li>✓ 2-Plate Hot Plate</li>
                    </ul>
                    <p class="text-muted small">Perfect for hostel or small apartment</p>
                    <div class="text-center">
                        <del class="text-muted">UGX 850,000</del>
                        <div class="h4 text-success fw-bold">UGX 720,000</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="https://wa.me/256704229768?text=Hi!%20I%20want%20the%20Bachelor%20Kitchen%20Bundle" 
                       class="btn btn-success w-100">
                        <i class="bi bi-whatsapp"></i> Order This Bundle
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-success">
                <div class="card-header bg-success text-white text-center">
                    <span class="badge bg-warning text-dark">SAVE 20%</span>
                    <h5 class="mt-2 mb-0">Family Kitchen Starter</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li>✓ Double Door Fridge 200L</li>
                        <li>✓ Microwave Oven</li>
                        <li>✓ Blender</li>
                        <li>✓ Electric Kettle</li>
                        <li>✓ Stabilizer</li>
                    </ul>
                    <p class="text-muted small">Everything for a new home</p>
                    <div class="text-center">
                        <del class="text-muted">UGX 2,200,000</del>
                        <div class="h4 text-success fw-bold">UGX 1,760,000</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="https://wa.me/256704229768?text=Hi!%20I%20want%20the%20Family%20Kitchen%20Starter%20Bundle" 
                       class="btn btn-success w-100">
                        <i class="bi bi-whatsapp"></i> Order This Bundle
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-warning text-dark text-center">
                    <span class="badge bg-dark">BUSINESS</span>
                    <h5 class="mt-2 mb-0">Restaurant Essentials</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li>✓ Chest Freezer 300L</li>
                        <li>✓ Display Fridge</li>
                        <li>✓ Commercial Blender</li>
                        <li>✓ Stabilizer x2</li>
                    </ul>
                    <p class="text-muted small">Start your food business right</p>
                    <div class="text-center">
                        <del class="text-muted">UGX 3,500,000</del>
                        <div class="h4 fw-bold">UGX 2,975,000</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="https://wa.me/256704229768?text=Hi!%20I%20want%20the%20Restaurant%20Essentials%20Bundle" 
                       class="btn btn-success w-100">
                        <i class="bi bi-whatsapp"></i> Order This Bundle
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Laundry Bundle -->
        <div class="col-12 mt-5">
            <h2 class="border-bottom pb-2 mb-4">👕 Laundry Bundles</h2>
        </div>
        
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-info text-white text-center">
                    <span class="badge bg-danger">SAVE 15%</span>
                    <h5 class="mt-2 mb-0">Laundry Day Bundle</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li>✓ 7kg Automatic Washing Machine</li>
                        <li>✓ Steam Iron</li>
                        <li>✓ Ironing Board</li>
                        <li>✓ Stabilizer</li>
                    </ul>
                    <div class="text-center mt-3">
                        <del class="text-muted">UGX 1,400,000</del>
                        <div class="h4 text-info fw-bold">UGX 1,190,000</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="https://wa.me/256704229768?text=Hi!%20I%20want%20the%20Laundry%20Day%20Bundle" 
                       class="btn btn-success w-100">
                        <i class="bi bi-whatsapp"></i> Order This Bundle
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-secondary text-white text-center">
                    <span class="badge bg-success">BEST VALUE</span>
                    <h5 class="mt-2 mb-0">Home Comfort Bundle</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li>✓ 12000 BTU Split AC</li>
                        <li>✓ Standing Fan</li>
                        <li>✓ Free Installation</li>
                        <li>✓ Stabilizer</li>
                    </ul>
                    <div class="text-center mt-3">
                        <del class="text-muted">UGX 1,600,000</del>
                        <div class="h4 fw-bold">UGX 1,360,000</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="https://wa.me/256704229768?text=Hi!%20I%20want%20the%20Home%20Comfort%20Bundle%20(AC%20%2B%20Fan)" 
                       class="btn btn-success w-100">
                        <i class="bi bi-whatsapp"></i> Order This Bundle
                    </a>
                </div>
            </div>
        </div>
        
    </div>
    
    <!-- Custom Bundle CTA -->
    <div class="text-center mt-5 p-5 bg-light rounded">
        <h3>🎨 Want a Custom Bundle?</h3>
        <p class="text-muted mb-4">Tell us what you need and we will create a personalized deal for you!</p>
        <a href="https://wa.me/256704229768?text=Hi!%20I%20want%20to%20create%20a%20custom%20bundle.%20Here%20is%20what%20I%20need:" 
           class="btn btn-danger btn-lg">
            💬 Create My Custom Bundle
        </a>
    </div>
    
    <!-- Trust Signals -->
    <div class="row mt-5 text-center">
        <div class="col-md-3">
            <div class="p-3">
                <i class="bi bi-truck text-success fs-1"></i>
                <h6 class="mt-2">Free Delivery</h6>
                <small class="text-muted">In Kampala</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3">
                <i class="bi bi-shield-check text-primary fs-1"></i>
                <h6 class="mt-2">Genuine Products</h6>
                <small class="text-muted">With Warranty</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3">
                <i class="bi bi-cash-stack text-warning fs-1"></i>
                <h6 class="mt-2">Pay on Delivery</h6>
                <small class="text-muted">Cash or Mobile Money</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3">
                <i class="bi bi-headset text-danger fs-1"></i>
                <h6 class="mt-2">WhatsApp Support</h6>
                <small class="text-muted">+256 780 221 421</small>
            </div>
        </div>
    </div>
</div>
@endsection
