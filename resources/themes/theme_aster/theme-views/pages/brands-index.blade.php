@extends('theme-views.layouts.app')

@section('title', 'Shop by Brand | Samsung, Hisense, CHiQ, ADH | Yoola.ug')

@section('content')
<div class="bg-primary text-white py-5">
    <div class="container text-center">
        <h1 class="fw-bold text-white">Shop by Brand</h1>
        <p class="text-white">Uganda's trusted brands. 100% genuine products with warranty. Free Kampala delivery.</p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        <div class="col-md-6 col-lg-3">
            <a href="{{ url('/samsung') }}" class="text-decoration-none">
                <div class="card h-100 text-center border-0 shadow-sm">
                    <div class="card-body py-5">
                        <h2 class="h4 text-dark mb-3">Samsung</h2>
                        <p class="text-muted mb-3">Exclusive in Uganda. Washing machines, fridges, TVs & more.</p>
                        <span class="badge bg-danger">Only at Yoola</span>
                    </div>
                    <div class="card-footer bg-primary text-white">
                        Shop Samsung →
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <a href="{{ url('/hisense') }}" class="text-decoration-none">
                <div class="card h-100 text-center border-0 shadow-sm">
                    <div class="card-body py-5">
                        <h2 class="h4 text-dark mb-3">Hisense</h2>
                        <p class="text-muted mb-3">Quality you can trust. TVs, fridges, washing machines.</p>
                        <span class="badge bg-success">Popular Choice</span>
                    </div>
                    <div class="card-footer bg-primary text-white">
                        Shop Hisense →
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <a href="{{ url('/chiq') }}" class="text-decoration-none">
                <div class="card h-100 text-center border-0 shadow-sm">
                    <div class="card-body py-5">
                        <h2 class="h4 text-dark mb-3">CHiQ</h2>
                        <p class="text-muted mb-3">Smart value. Android 4K TVs, fridges, freezers.</p>
                        <span class="badge bg-info">Best Value</span>
                    </div>
                    <div class="card-footer bg-primary text-white">
                        Shop CHiQ →
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <a href="{{ url('/adh') }}" class="text-decoration-none">
                <div class="card h-100 text-center border-0 shadow-sm">
                    <div class="card-body py-5">
                        <h2 class="h4 text-dark mb-3">ADH</h2>
                        <p class="text-muted mb-3">Built for Africa. Chest freezers & refrigerators.</p>
                        <span class="badge bg-warning text-dark">Durable</span>
                    </div>
                    <div class="card-footer bg-primary text-white">
                        Shop ADH →
                    </div>
                </div>
            </a>
        </div>
    </div>
    
    <div class="row g-4 mt-2">
        <div class="col-md-6 col-lg-3">
            <a href="{{ url('/brand/tcl') }}" class="text-decoration-none">
                <div class="card h-100 text-center border-0 shadow-sm">
                    <div class="card-body py-4">
                        <h3 class="h5 text-dark mb-2">TCL</h3>
                        <p class="text-muted small mb-0">Smart TVs</p>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <a href="{{ url('/brand/sony') }}" class="text-decoration-none">
                <div class="card h-100 text-center border-0 shadow-sm">
                    <div class="card-body py-4">
                        <h3 class="h5 text-dark mb-2">Sony</h3>
                        <p class="text-muted small mb-0">TVs & Audio</p>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <a href="{{ url('/brand/jbl') }}" class="text-decoration-none">
                <div class="card h-100 text-center border-0 shadow-sm">
                    <div class="card-body py-4">
                        <h3 class="h5 text-dark mb-2">JBL</h3>
                        <p class="text-muted small mb-0">Speakers & Audio</p>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <a href="{{ url('/brand/geepas') }}" class="text-decoration-none">
                <div class="card h-100 text-center border-0 shadow-sm">
                    <div class="card-body py-4">
                        <h3 class="h5 text-dark mb-2">Geepas</h3>
                        <p class="text-muted small mb-0">Home Appliances</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<div class="bg-primary text-white py-4 text-center">
    <h3 class="fw-bold text-white">Need Help? WhatsApp: 0704 229 768</h3>
</div>
@endsection
