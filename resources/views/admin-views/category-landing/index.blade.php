@extends('layouts.back-end.app')

@section('title', translate('Category Landing Pages'))

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="mb-3">
        <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
            <i class="bi bi-window-split"></i>
            {{ translate('Category Landing Pages') }}
        </h2>
        <p class="text-muted">{{ translate('Manage enhanced landing pages for SEO and conversion') }}</p>
    </div>

    <!-- Alert -->
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="bi bi-info-circle me-2"></i>
        <strong>{{ translate('Hormozi-Style Landing Pages') }}</strong>: 
        {{ translate('Enable enhanced landing pages with hero sections, trust badges, FAQs (for SEO), and conversion elements.') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <!-- Categories Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ translate('Categories') }}</h5>
            <form action="{{ route('admin.category-landing.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control form-control-sm" 
                       placeholder="{{ translate('Search category...') }}" value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ translate('Category') }}</th>
                            <th>{{ translate('Products') }}</th>
                            <th>{{ translate('Landing Page') }}</th>
                            <th>{{ translate('Views') }}</th>
                            <th class="text-center">{{ translate('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($category->icon)
                                    <img src="{{ $category->icon_full_url['path'] }}" alt="" width="40" height="40" class="rounded">
                                    @endif
                                    <div>
                                        <strong>{{ $category->name }}</strong>
                                        <br>
                                        <small class="text-muted">/{{ $category->slug }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $category->active_products_count ?? 0 }}</span>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input toggle-landing" type="checkbox" 
                                           data-id="{{ $category->id }}"
                                           {{ $category->use_landing_page ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        {{ $category->use_landing_page ? translate('Enabled') : translate('Disabled') }}
                                    </label>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted">{{ number_format($category->landing_view_count ?? 0) }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.category-landing.edit', $category->id) }}" 
                                       class="btn btn-outline-primary" title="{{ translate('Edit') }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ route('admin.category-landing.preview', $category->id) }}" 
                                       class="btn btn-outline-success" target="_blank" title="{{ translate('Preview') }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('category-products', $category->slug) }}" 
                                       class="btn btn-outline-secondary" target="_blank" title="{{ translate('View Live') }}">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                {{ translate('No categories found') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($categories->hasPages())
        <div class="card-footer">
            {{ $categories->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('script')
<script>
    $('.toggle-landing').on('change', function() {
        const id = $(this).data('id');
        const checkbox = $(this);
        
        $.ajax({
            url: '{{ route("admin.category-landing.toggle", ":id") }}'.replace(':id', id),
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                toastr.success(response.message);
                checkbox.next('label').text(response.status ? '{{ translate("Enabled") }}' : '{{ translate("Disabled") }}');
            },
            error: function() {
                toastr.error('{{ translate("Something went wrong") }}');
                checkbox.prop('checked', !checkbox.prop('checked'));
            }
        });
    });
</script>
@endpush
