@extends('layouts.admin.app')

@section('title', translate('Price_Pages_SEO'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <i class="fi fi-rr-chart-line-up text-primary"></i>
                {{ translate('Price_Pages_SEO') }}
            </h2>
            <p class="text-muted mt-1">Programmatic SEO pages for "[Product] price in Uganda" searches</p>
        </div>

        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body d-flex flex-column gap-20">
                        <div class="d-flex justify-content-between align-items-center gap-20 flex-wrap">
                            <h3 class="mb-0">
                                {{ translate('price_pages') }}
                                <span class="badge text-dark bg-body-secondary fw-semibold rounded-50">
                                    {{ $pricePages->total() }}
                                </span>
                            </h3>
                            <div class="d-flex flex-wrap gap-3 align-items-stretch">
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group flex-grow-1 max-w-280">
                                        <input type="search" name="search" class="form-control"
                                            placeholder="{{ translate('Search_by_title_or_slug') }}"
                                            value="{{ request('search') }}">
                                        <div class="input-group-append search-submit">
                                            <button type="submit">
                                                <i class="fi fi-rr-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <a href="{{ route('admin.price-pages.create') }}" class="btn btn-primary">
                                    + {{ translate('Add_New') }}
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless align-middle">
                                <thead class="text-capitalize">
                                    <tr>
                                        <th>{{ translate('SL') }}</th>
                                        <th>{{ translate('Title') }}</th>
                                        <th>{{ translate('Slug') }}</th>
                                        <th>{{ translate('Type') }}</th>
                                        <th class="text-center">{{ translate('Status') }}</th>
                                        <th class="text-center">{{ translate('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse ($pricePages as $key => $page)
                                    <tr>
                                        <td>{{ $pricePages->firstItem() + $key }}</td>
                                        <td>
                                            <div class="max-w-300 text-truncate" title="{{ $page->title }}">
                                                {{ $page->title }}
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ url('/prices/' . $page->slug) }}" target="_blank" class="text-primary">
                                                /prices/{{ $page->slug }}
                                            </a>
                                        </td>
                                        <td>{{ ucfirst($page->product_type ?? 'All') }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('admin.price-pages.toggle-status', $page->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <label class="switcher">
                                                    <input type="checkbox" class="switcher_input" 
                                                        {{ $page->is_active ? 'checked' : '' }}
                                                        onchange="this.form.submit()">
                                                    <span class="switcher_control"></span>
                                                </label>
                                            </form>
                                        </td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-outline--primary dropdown-toggle" type="button" 
                                                    data-bs-toggle="dropdown">
                                                    <i class="fi fi-rr-menu-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ url('/prices/' . $page->slug) }}" target="_blank">
                                                            <i class="fi fi-rr-eye me-2"></i>{{ translate('View') }}
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.price-pages.edit', $page->id) }}">
                                                            <i class="fi fi-rr-pencil me-2"></i>{{ translate('Edit') }}
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('admin.price-pages.destroy', $page->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger" 
                                                                onclick="return confirm('{{ translate('Are_you_sure?') }}')">
                                                                <i class="fi fi-rr-trash me-2"></i>{{ translate('Delete') }}
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <p class="mb-0 text-muted">{{ translate('No_price_pages_found') }}</p>
                                            <a href="{{ route('admin.price-pages.create') }}" class="btn btn-primary btn-sm mt-2">
                                                {{ translate('Create_your_first_page') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            {{ $pricePages->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5>Total Pages</h5>
                        <h2>{{ $pricePages->total() }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5>Active Pages</h5>
                        <h2>{{ \App\Models\PricePage::where('is_active', true)->count() }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5>Sitemap URL</h5>
                        <a href="{{ url('/sitemap-prices.xml') }}" target="_blank" class="text-white">
                            <small>{{ url('/sitemap-prices.xml') }}</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
