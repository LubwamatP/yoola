@extends('layouts.back-end.app')

@section('title', translate('Edit Category Landing Page'))

@push('css_or_js')
<style>
    .value-prop-row, .faq-row, .trust-badge-row {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 0.75rem;
    }
    .section-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        margin-bottom: 1.5rem;
    }
    .section-card .card-header {
        background: transparent;
        border-bottom: 1px solid #e9ecef;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
            <i class="bi bi-pencil-square"></i>
            {{ translate('Edit Landing Page') }}: {{ $category->name }}
        </h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.category-landing.index') }}">{{ translate('Category Landing Pages') }}</a></li>
                <li class="breadcrumb-item active">{{ $category->name }}</li>
            </ol>
        </nav>
    </div>

    <form action="{{ route('admin.category-landing.update', $category->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-lg-8">

                {{-- Hero Section --}}
                <div class="section-card card">
                    <div class="card-header">
                        <i class="bi bi-image"></i> {{ translate('Hero Section') }}
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ translate('Hero Title') }}</label>
                            <input type="text" name="hero_title" class="form-control" value="{{ old('hero_title', $category->hero_title) }}" maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ translate('Hero Subtitle') }}</label>
                            <textarea name="hero_subtitle" class="form-control" rows="2" maxlength="500">{{ old('hero_subtitle', $category->hero_subtitle) }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ translate('CTA Button Text') }}</label>
                                <input type="text" name="hero_cta_text" class="form-control" value="{{ old('hero_cta_text', $category->hero_cta_text ?? 'Shop Now') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ translate('CTA Link') }}</label>
                                <input type="text" name="hero_cta_link" class="form-control" value="{{ old('hero_cta_link', $category->hero_cta_link) }}" placeholder="/category/{{ $category->slug }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ translate('Hero Image') }}</label>
                            @if($category->hero_image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/category/hero/' . $category->hero_image) }}" alt="" class="rounded" style="max-height: 120px;">
                            </div>
                            @endif
                            <input type="file" name="hero_image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                            <small class="text-muted">{{ translate('Recommended: 1200x400px, JPG/WebP, max 2MB') }}</small>
                        </div>
                    </div>
                </div>

                {{-- Content Blocks --}}
                <div class="section-card card">
                    <div class="card-header">
                        <i class="bi bi-body-text"></i> {{ translate('Content Blocks') }}
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ translate('Top Content (above products)') }}</label>
                            <textarea name="content_top" class="form-control editor" rows="6">{{ old('content_top', $category->content_top) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ translate('Bottom Content (after products)') }}</label>
                            <textarea name="content_bottom" class="form-control editor" rows="6">{{ old('content_bottom', $category->content_bottom) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Value Propositions --}}
                <div class="section-card card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-stars"></i> {{ translate('Value Propositions') }}</span>
                        <button type="button" class="btn btn-sm btn-outline-primary add-value-prop">
                            <i class="bi bi-plus"></i> {{ translate('Add') }}
                        </button>
                    </div>
                    <div class="card-body" id="value-props-container">
                        @php $vProps = old('value_prop_title') ? null : ($category->value_props ?? $defaultValueProps ?? []); @endphp
                        @if(old('value_prop_title'))
                            @foreach(old('value_prop_title') as $i => $title)
                            <div class="value-prop-row">
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <label class="form-label small">{{ translate('Icon') }}</label>
                                        <input type="text" name="value_prop_icon[]" class="form-control form-control-sm" value="{{ old('value_prop_icon')[$i] ?? 'bi-star' }}" placeholder="bi-star">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small">{{ translate('Title') }}</label>
                                        <input type="text" name="value_prop_title[]" class="form-control form-control-sm" value="{{ $title }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small">{{ translate('Description') }}</label>
                                        <input type="text" name="value_prop_description[]" class="form-control form-control-sm" value="{{ old('value_prop_description')[$i] ?? '' }}">
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-row">&times;</button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @elseif($vProps)
                            @foreach($vProps as $prop)
                            <div class="value-prop-row">
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <label class="form-label small">{{ translate('Icon') }}</label>
                                        <input type="text" name="value_prop_icon[]" class="form-control form-control-sm" value="{{ $prop['icon'] ?? 'bi-star' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small">{{ translate('Title') }}</label>
                                        <input type="text" name="value_prop_title[]" class="form-control form-control-sm" value="{{ $prop['title'] ?? '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small">{{ translate('Description') }}</label>
                                        <input type="text" name="value_prop_description[]" class="form-control form-control-sm" value="{{ $prop['description'] ?? '' }}">
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-row">&times;</button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                    <div class="card-body pt-0">
                        <div class="mb-3">
                            <label class="form-label">{{ translate('Value Props Headline') }}</label>
                            <input type="text" name="value_prop_headline" class="form-control" value="{{ old('value_prop_headline', $category->value_prop_headline) }}" placeholder="{{ translate('Why shop with us') }}">
                        </div>
                    </div>
                </div>

                {{-- Trust Badges --}}
                <div class="section-card card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-shield-check"></i> {{ translate('Trust Badges') }}</span>
                        <button type="button" class="btn btn-sm btn-outline-primary add-trust-badge">
                            <i class="bi bi-plus"></i> {{ translate('Add') }}
                        </button>
                    </div>
                    <div class="card-body" id="trust-badges-container">
                        @php $tBadges = old('trust_badge_text') ? null : ($category->trust_badges ?? $defaultTrustBadges ?? []); @endphp
                        @if(old('trust_badge_text'))
                            @foreach(old('trust_badge_text') as $i => $text)
                            <div class="trust-badge-row">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label small">{{ translate('Icon') }}</label>
                                        <input type="text" name="trust_badge_icon[]" class="form-control form-control-sm" value="{{ old('trust_badge_icon')[$i] ?? 'bi-check-circle' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">{{ translate('Text') }}</label>
                                        <input type="text" name="trust_badge_text[]" class="form-control form-control-sm" value="{{ $text }}">
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-check">
                                            <input type="checkbox" name="trust_badge_active[{{ $i }}]" class="form-check-input" {{ isset(old('trust_badge_active')[$i]) ? 'checked' : '' }}>
                                            <label class="form-check-label small">{{ translate('Active') }}</label>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-row">&times;</button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @elseif($tBadges)
                            @foreach($tBadges as $i => $badge)
                            <div class="trust-badge-row">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label small">{{ translate('Icon') }}</label>
                                        <input type="text" name="trust_badge_icon[]" class="form-control form-control-sm" value="{{ $badge['icon'] ?? 'bi-check-circle' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">{{ translate('Text') }}</label>
                                        <input type="text" name="trust_badge_text[]" class="form-control form-control-sm" value="{{ $badge['text'] ?? '' }}">
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-check">
                                            <input type="checkbox" name="trust_badge_active[{{ $i }}]" class="form-check-input" {{ ($badge['active'] ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label small">{{ translate('Active') }}</label>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-row">&times;</button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                {{-- FAQs --}}
                <div class="section-card card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-question-circle"></i> {{ translate('FAQs (SEO Schema)') }}</span>
                        <button type="button" class="btn btn-sm btn-outline-primary add-faq">
                            <i class="bi bi-plus"></i> {{ translate('Add FAQ') }}
                        </button>
                    </div>
                    <div class="card-body" id="faqs-container">
                        @php $faqItems = old('faq_question') ? null : ($category->faqs ?? []); @endphp
                        @if(old('faq_question'))
                            @foreach(old('faq_question') as $i => $question)
                            <div class="faq-row">
                                <div class="row g-2">
                                    <div class="col-md-5">
                                        <label class="form-label small">{{ translate('Question') }}</label>
                                        <input type="text" name="faq_question[]" class="form-control form-control-sm" value="{{ $question }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">{{ translate('Answer') }}</label>
                                        <textarea name="faq_answer[]" class="form-control form-control-sm" rows="2">{{ old('faq_answer')[$i] ?? '' }}</textarea>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-row">&times;</button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            @foreach($faqItems as $faq)
                            <div class="faq-row">
                                <div class="row g-2">
                                    <div class="col-md-5">
                                        <label class="form-label small">{{ translate('Question') }}</label>
                                        <input type="text" name="faq_question[]" class="form-control form-control-sm" value="{{ $faq['question'] ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">{{ translate('Answer') }}</label>
                                        <textarea name="faq_answer[]" class="form-control form-control-sm" rows="2">{{ $faq['answer'] ?? '' }}</textarea>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-row">&times;</button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                {{-- Buying Guide --}}
                <div class="section-card card">
                    <div class="card-header">
                        <i class="bi bi-book"></i> {{ translate('Buying Guide') }}
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ translate('Guide Title') }}</label>
                            <input type="text" name="buying_guide_title" class="form-control" value="{{ old('buying_guide_title', $category->buying_guide_title) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ translate('Guide Content (HTML)') }}</label>
                            <textarea name="buying_guide" class="form-control editor" rows="8">{{ old('buying_guide', $category->buying_guide) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- SEO Meta --}}
                <div class="section-card card">
                    <div class="card-header">
                        <i class="bi bi-search"></i> {{ translate('SEO Meta') }}
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ translate('Meta Title') }}</label>
                            <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $category->seo->title ?? '') }}" maxlength="70">
                            <small class="text-muted">{{ strlen(old('meta_title', $category->seo->title ?? '')) }}/70</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ translate('Meta Description') }}</label>
                            <textarea name="meta_description" class="form-control" rows="3" maxlength="160">{{ old('meta_description', $category->seo->description ?? '') }}</textarea>
                            <small class="text-muted">{{ strlen(old('meta_description', $category->seo->description ?? '')) }}/160</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ translate('Index') }}</label>
                                <select name="meta_index" class="form-select">
                                    <option value="index" {{ (old('meta_index', $category->seo->index ?? 'index') == 'index') ? 'selected' : '' }}>Index</option>
                                    <option value="noindex" {{ (old('meta_index', $category->seo->index ?? '') == 'noindex') ? 'selected' : '' }}>No Index</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="meta_no_follow" class="form-check-input" value="1" {{ ($category->seo->no_follow ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ translate('No Follow') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                {{-- Publish Settings --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="bi bi-gear"></i> {{ translate('Settings') }}
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="use_landing_page" class="form-check-input" value="1" {{ $category->use_landing_page ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold">{{ translate('Enable Landing Page') }}</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ translate('Layout Type') }}</label>
                            <select name="layout_type" class="form-select">
                                <option value="standard" {{ ($category->layout_type ?? 'standard') == 'standard' ? 'selected' : '' }}>Standard</option>
                                <option value="landing" {{ ($category->layout_type ?? '') == 'landing' ? 'selected' : '' }}>Landing (Full Width)</option>
                                <option value="minimal" {{ ($category->layout_type ?? '') == 'minimal' ? 'selected' : '' }}>Minimal</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ translate('Products Per Page') }}</label>
                            <input type="number" name="products_per_page" class="form-control" value="{{ old('products_per_page', $category->products_per_page ?? 20) }}" min="4" max="100">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ translate('Default Sort') }}</label>
                            <select name="default_sort" class="form-select">
                                <option value="popularity" {{ ($category->default_sort ?? 'popularity') == 'popularity' ? 'selected' : '' }}>Popularity</option>
                                <option value="latest" {{ ($category->default_sort ?? '') == 'latest' ? 'selected' : '' }}>Latest</option>
                                <option value="price-low-high" {{ ($category->default_sort ?? '') == 'price-low-high' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price-high-low" {{ ($category->default_sort ?? '') == 'price-high-low' ? 'selected' : '' }}>Price: High to Low</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Conversion Elements --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="bi bi-lightning"></i> {{ translate('Conversion Elements') }}
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ translate('Urgency Text') }}</label>
                            <input type="text" name="urgency_text" class="form-control" value="{{ old('urgency_text', $category->urgency_text) }}" placeholder="{{ translate('Limited stock available') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ translate('Promo Banner Text') }}</label>
                            <input type="text" name="promo_banner_text" class="form-control" value="{{ old('promo_banner_text', $category->promo_banner_text) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ translate('Promo Banner Color') }}</label>
                            <input type="color" name="promo_banner_color" class="form-control form-control-color" value="{{ old('promo_banner_color', $category->promo_banner_color ?? '#dc3545') }}">
                        </div>
                    </div>
                </div>

                {{-- WhatsApp --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="bi bi-whatsapp"></i> {{ translate('WhatsApp') }}
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ translate('WhatsApp Message') }}</label>
                            <textarea name="whatsapp_message" class="form-control" rows="2">{{ old('whatsapp_message', $category->whatsapp_message) }}</textarea>
                        </div>
                        <div class="form-check form-switch">
                            <input type="checkbox" name="show_whatsapp_float" class="form-check-input" value="1" {{ $category->show_whatsapp_float ? 'checked' : '' }}>
                            <label class="form-check-label">{{ translate('Show WhatsApp Float Button') }}</label>
                        </div>
                    </div>
                </div>

                {{-- Social Proof --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="bi bi-star"></i> {{ translate('Social Proof') }}
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ translate('Review Highlight') }}</label>
                            <textarea name="review_highlight" class="form-control" rows="2">{{ old('review_highlight', $category->review_highlight) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ translate('Review Count Display') }}</label>
                            <input type="number" name="review_count_display" class="form-control" value="{{ old('review_count_display', $category->review_count_display) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ translate('Average Rating Display') }}</label>
                            <input type="number" name="avg_rating_display" class="form-control" step="0.1" min="1" max="5" value="{{ old('avg_rating_display', $category->avg_rating_display) }}">
                        </div>
                    </div>
                </div>

                {{-- Duplicate to Another Category --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="bi bi-copy"></i> {{ translate('Duplicate Settings') }}
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">{{ translate('Copy these landing page settings to another category.') }}</p>
                        <form action="{{ route('admin.category-landing.duplicate', $category->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <select name="target_category_id" class="form-select" required>
                                    <option value="">{{ translate('Select target category...') }}</option>
                                    @foreach(\App\Models\Category::where('id', '!=', $category->id)->orderBy('name')->get() as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                                <i class="bi bi-copy"></i> {{ translate('Duplicate') }}
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Save --}}
                <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                    <i class="bi bi-check-lg"></i> {{ translate('Save Changes') }}
                </button>
                <a href="{{ route('admin.category-landing.preview', $category->id) }}" target="_blank" class="btn btn-outline-success w-100">
                    <i class="bi bi-eye"></i> {{ translate('Preview Landing Page') }}
                </a>
            </div>
        </div>
    </form>
</div>
@endsection

@push('script')
<script>
    // Add/remove value props
    $('.add-value-prop').on('click', function() {
        const row = `<div class="value-prop-row">
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label small">{{ translate('Icon') }}</label>
                    <input type="text" name="value_prop_icon[]" class="form-control form-control-sm" value="bi-star">
                </div>
                <div class="col-md-4">
                    <label class="form-label small">{{ translate('Title') }}</label>
                    <input type="text" name="value_prop_title[]" class="form-control form-control-sm">
                </div>
                <div class="col-md-4">
                    <label class="form-label small">{{ translate('Description') }}</label>
                    <input type="text" name="value_prop_description[]" class="form-control form-control-sm">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-row">&times;</button>
                </div>
            </div>
        </div>`;
        $('#value-props-container').append(row);
    });

    // Add/remove FAQs
    $('.add-faq').on('click', function() {
        const row = `<div class="faq-row">
            <div class="row g-2">
                <div class="col-md-5">
                    <label class="form-label small">{{ translate('Question') }}</label>
                    <input type="text" name="faq_question[]" class="form-control form-control-sm">
                </div>
                <div class="col-md-6">
                    <label class="form-label small">{{ translate('Answer') }}</label>
                    <textarea name="faq_answer[]" class="form-control form-control-sm" rows="2"></textarea>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-row">&times;</button>
                </div>
            </div>
        </div>`;
        $('#faqs-container').append(row);
    });

    // Add/remove trust badges
    $('.add-trust-badge').on('click', function() {
        const idx = $('#trust-badges-container .trust-badge-row').length;
        const row = `<div class="trust-badge-row">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small">{{ translate('Icon') }}</label>
                    <input type="text" name="trust_badge_icon[]" class="form-control form-control-sm" value="bi-check-circle">
                </div>
                <div class="col-md-6">
                    <label class="form-label small">{{ translate('Text') }}</label>
                    <input type="text" name="trust_badge_text[]" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <div class="form-check">
                        <input type="checkbox" name="trust_badge_active[${idx}]" class="form-check-input" checked>
                        <label class="form-check-label small">{{ translate('Active') }}</label>
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-row">&times;</button>
                </div>
            </div>
        </div>`;
        $('#trust-badges-container').append(row);
    });

    // Remove row
    $(document).on('click', '.remove-row', function() {
        $(this).closest('.value-prop-row, .faq-row, .trust-badge-row').remove();
    });
</script>
@endpush
