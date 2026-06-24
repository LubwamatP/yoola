@extends('theme-views.layouts.app')

@section('title', 'Yoola vs Jumia Uganda: Which Electronics Store is Better? (2026)')

@section('content')
<div class="container py-5">
    <h1 class="text-center mb-4">Yoola vs Jumia Uganda</h1>
    <p class="text-center lead">Compare the two leading electronics stores in Uganda</p>
    
    <div class="row mt-5">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0">✅ Yoola.ug</h3>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li>✓ Free delivery in Kampala</li>
                        <li>✓ Electronics specialists</li>
                        <li>✓ Local warranty support</li>
                        <li>✓ WhatsApp ordering</li>
                        <li>✓ Same-day delivery available</li>
                        <li>✓ Genuine products only</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-warning">
                    <h3 class="mb-0">Jumia Uganda</h3>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li>• Wide product range</li>
                        <li>• Multiple sellers</li>
                        <li>• Delivery fees apply</li>
                        <li>• Various product quality</li>
                        <li>• Standard delivery times</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="text-center mt-5">
        <h3>Ready to shop?</h3>
        <a href="/" class="btn btn-success btn-lg mt-3">Shop at Yoola Now</a>
    </div>
</div>
@endsection
