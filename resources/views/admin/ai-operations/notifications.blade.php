@extends('layouts.admin.app')
@section('title', 'AI Smart Notifications')

@section('content')
<div class="content container-fluid">
    <div class="mb-3 d-flex align-items-center justify-content-between">
        <h2 class="h1 mb-0 d-flex align-items-center gap-2">
            <img src="{{asset('public/assets/admin/img/ai.png')}}" alt="" width="24" onerror="this.style.display='none'">
            AI Smart Notifications
        </h2>
        <a href="{{ route('admin.ai-operations.notifications.trigger') }}" class="btn btn-primary">
            Refresh AI Insights
        </a>
    </div>

    {{-- STATS ROW --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 rounded p-3">
                        <i class="tio-shopping-basket-add" style="font-size:28px;color:#5570f1"></i>
                    </div>
                    <div>
                        <div class="fs-24 fw-bold">{{ $stats['totalProducts'] }}</div>
                        <div class="text-muted fs-12">Active Products</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-warning bg-opacity-10 rounded p-3">
                        <i class="tio-warning" style="font-size:28px;color:#f1a515"></i>
                    </div>
                    <div>
                        <div class="fs-24 fw-bold text-warning">{{ $stats['missingMeta'] }}</div>
                        <div class="text-muted fs-12">Missing SEO Meta</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-danger bg-opacity-10 rounded p-3">
                        <i class="tio-image" style="font-size:28px;color:#e8536f"></i>
                    </div>
                    <div>
                        <div class="fs-24 fw-bold text-danger">{{ $stats['missingImg'] }}</div>
                        <div class="text-muted fs-12">Missing Images</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-success bg-opacity-10 rounded p-3">
                        <i class="tio-orders" style="font-size:28px;color:#00aa6d"></i>
                    </div>
                    <div>
                        <div class="fs-24 fw-bold text-success">{{ $stats['recentOrders'] }}</div>
                        <div class="text-muted fs-12">Orders (7 days)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- AI INSIGHTS --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex align-items-center gap-2 py-3">
            <span class="badge bg-primary">AI</span>
            <h5 class="mb-0">Gemini AI Smart Insights</h5>
            <span class="text-muted fs-12 ms-auto">Auto-refreshes every hour</span>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(!empty($insights))
                @php
                    $icons = ['warning'=>'tio-warning','info'=>'tio-info','success'=>'tio-checkmark-circle','danger'=>'tio-error'];
                    $colors = ['warning'=>'warning','info'=>'info','success'=>'success','danger'=>'danger'];
                @endphp
                @foreach(collect($insights)->sortBy('priority') as $insight)
                <div class="alert alert-{{ $colors[$insight['type']] ?? 'info' }} d-flex align-items-start gap-3 mb-3">
                    <i class="{{ $icons[$insight['type']] ?? 'tio-info' }} fs-24 mt-1"></i>
                    <div>
                        <strong>{{ $insight['title'] ?? 'Insight' }}</strong>
                        <div class="mt-1">{{ $insight['message'] ?? '' }}</div>
                        @if(isset($insight['priority']))
                        <span class="badge bg-secondary mt-2">Priority {{ $insight['priority'] }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="tio-info fs-48 text-muted"></i>
                    <p class="text-muted mt-3">No AI insights available yet. Click "Refresh AI Insights" to generate.</p>
                    <a href="{{ route('admin.ai-operations.notifications.trigger') }}" class="btn btn-primary mt-2">
                        Generate Insights Now
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
