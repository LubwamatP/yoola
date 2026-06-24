@extends('layouts.back-end.app')

@section('title', translate('Preview Price Page'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-eye"></i>
            {{ translate('Preview') }}: {{ $pricePage->title }}
        </h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.price-pages.index') }}">{{ translate('Price Pages') }}</a></li>
                <li class="breadcrumb-item active">{{ translate('Preview') }}</li>
            </ol>
        </nav>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            {{-- Page Info --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> {{ translate('Page Information') }}
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr><th class="w-25">{{ translate('Slug') }}</th><td><code>/prices/{{ $pricePage->slug }}</code></td></tr>
                        <tr><th>{{ translate('Title') }}</th><td>{{ $pricePage->title }}</td></tr>
                        <tr><th>{{ translate('H1') }}</th><td>{{ $pricePage->h1 }}</td></tr>
                        <tr><th>{{ translate('Meta Description') }}</th><td>{{ $pricePage->meta_description }}</td></tr>
                        <tr><th>{{ translate('Status') }}</th>
                            <td>
                                @if($pricePage->is_active)
                                    <span class="badge bg-success">{{ translate('Active') }}</span>
                                @else
                                    <span class="badge bg-warning">{{ translate('Inactive') }}</span>
                                @endif
                                @if($pricePage->is_indexed)
                                    <span class="badge bg-info">{{ translate('Indexed') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr><th>{{ translate('Category') }}</th><td>{{ $pricePage->category?->name ?? '-' }}</td></tr>
                        <tr><th>{{ translate('Brand') }}</th><td>{{ $pricePage->brand?->name ?? $pricePage->brand_filter ?? '-' }}</td></tr>
                        <tr><th>{{ translate('Product Type') }}</th><td>{{ $pricePage->product_type ?? '-' }}</td></tr>
                        <tr><th>{{ translate('Size Filter') }}</th><td>{{ $pricePage->size_filter ?? '-' }}</td></tr>
                        <tr><th>{{ translate('Price Range') }}</th><td>{{ number_format($pricePage->min_price ?? 0) }}/= - {{ number_format($pricePage->max_price ?? 0) }}/=</td></tr>
                    </table>
                </div>
            </div>

            {{-- Intro Text --}}
            <div class="card mb-3">
                <div class="card-header">{{ translate('Intro Text') }}</div>
                <div class="card-body">
                    <p>{{ $pricePage->intro_text }}</p>
                </div>
            </div>

            {{-- Buying Guide --}}
            @if($pricePage->buying_guide)
            <div class="card mb-3">
                <div class="card-header">{{ translate('Buying Guide') }}</div>
                <div class="card-body">
                    {!! $pricePage->buying_guide !!}
                </div>
            </div>
            @endif

            {{-- Matched Products --}}
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between">
                    <span>{{ translate('Matched Products') }} ({{ $products->count() }})</span>
                </div>
                <div class="card-body p-0">
                    @if($products->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ translate('Product') }}</th>
                                    <th>{{ translate('Price') }}</th>
                                    <th>{{ translate('Stock') }}</th>
                                    <th>{{ translate('Brand') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($product->thumbnail_full_url['path'] ?? null)
                                            <img src="{{ $product->thumbnail_full_url['path'] }}" alt="" width="40" height="40" class="rounded" style="object-fit: contain;">
                                            @endif
                                            <a href="{{ route('product', $product->slug) }}" target="_blank">
                                                {{ Str::limit($product->name, 60) }}
                                            </a>
                                        </div>
                                    </td>
                                    <td class="fw-bold">{{ number_format($product->unit_price) }}/=</td>
                                    <td>
                                        @if($product->current_stock > 0)
                                            <span class="badge bg-success">{{ $product->current_stock }}</span>
                                        @else
                                            <span class="badge bg-danger">0</span>
                                        @endif
                                    </td>
                                    <td>{{ $product->brand?->name ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        {{ translate('No products match these filters. Add products or adjust filters.') }}
                    </div>
                    @endif
                </div>
            </div>

            {{-- FAQs --}}
            @if($pricePage->faqs && count($pricePage->faqs) > 0)
            <div class="card mb-3">
                <div class="card-header">{{ translate('FAQs') }} ({{ count($pricePage->faqs) }})</div>
                <div class="card-body">
                    @foreach($pricePage->faqs as $faq)
                    <div class="mb-3 pb-3 border-bottom">
                        <strong>{{ $faq['question'] ?? '' }}</strong>
                        <p class="text-muted mb-0 mt-1">{{ $faq['answer'] ?? '' }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">{{ translate('Actions') }}</div>
                <div class="card-body">
                    <a href="{{ route('admin.price-pages.edit', $pricePage->id) }}" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-pencil"></i> {{ translate('Edit') }}
                    </a>
                    <a href="{{ url('/prices/' . $pricePage->slug) }}" target="_blank" class="btn btn-outline-success w-100 mb-2">
                        <i class="bi bi-box-arrow-up-right"></i> {{ translate('View Live') }}
                    </a>
                    <form action="{{ route('admin.price-pages.toggle-status', $pricePage->id) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-outline-warning w-100">
                            <i class="bi bi-power"></i> {{ $pricePage->is_active ? translate('Deactivate') : translate('Activate') }}
                        </button>
                    </form>
                    <form action="{{ route('admin.price-pages.destroy', $pricePage->id) }}" method="POST" onsubmit="return confirm('{{ translate('Are you sure?') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-trash"></i> {{ translate('Delete') }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- Related Pages --}}
            @if($pricePage->related_slugs && count($pricePage->related_slugs) > 0)
            <div class="card">
                <div class="card-header">{{ translate('Related Price Pages') }}</div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @foreach($pricePage->related_slugs as $slug)
                            <li class="mb-1">
                                <i class="bi bi-link-45deg"></i>
                                <code>/prices/{{ $slug }}</code>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
