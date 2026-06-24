@extends('layouts.admin.app')

@section('title', translate('Add_Price_Page'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <i class="fi fi-rr-chart-line-up text-primary"></i>
                {{ translate('Add_Price_Page') }}
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.price-pages.index') }}">{{ translate('Price_Pages') }}</a></li>
                    <li class="breadcrumb-item active">{{ translate('Add_New') }}</li>
                </ol>
            </nav>
        </div>

        <form action="{{ route('admin.price-pages.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-lg-8">
                    <!-- Basic Info -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ translate('Basic_Information') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">{{ translate('URL_Slug') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">/prices/</span>
                                    <input type="text" name="slug" class="form-control" 
                                        placeholder="hisense-tv-price-in-uganda" required
                                        pattern="[a-z0-9-]+" title="Only lowercase letters, numbers, and hyphens">
                                </div>
                                <small class="text-muted">Example: hisense-tv-price-in-uganda</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ translate('SEO_Title') }} <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" maxlength="70" required
                                    placeholder="Hisense TV Price in Uganda 2026 | From 490,000 UGX | Yoola">
                                <small class="text-muted">Max 70 characters for Google</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ translate('Meta_Description') }} <span class="text-danger">*</span></label>
                                <textarea name="meta_description" class="form-control" rows="2" maxlength="160" required
                                    placeholder="Compare Hisense TV prices in Uganda. Starting from 490,000 UGX. ✓ Genuine warranty ✓ Free Kampala delivery."></textarea>
                                <small class="text-muted">Max 160 characters for Google</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ translate('H1_Heading') }} <span class="text-danger">*</span></label>
                                <input type="text" name="h1" class="form-control" required
                                    placeholder="Hisense TV Price in Uganda (Updated February 2026)">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ translate('Intro_Paragraph') }} <span class="text-danger">*</span></label>
                                <textarea name="intro_text" class="form-control" rows="4" required
                                    placeholder="Looking for Hisense TV prices in Uganda? Hisense offers the best value for Smart TVs, with prices starting from 490,000 UGX..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ translate('Buying_Guide') }}</label>
                                <textarea name="buying_guide" class="form-control" rows="6"
                                    placeholder="<h3>How to Choose...</h3><p>...</p>"></textarea>
                                <small class="text-muted">HTML allowed. This should be unique content.</small>
                            </div>
                        </div>
                    </div>

                    <!-- FAQs -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ translate('FAQs') }} (for Google Rich Snippets)</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addFaq()">
                                + Add FAQ
                            </button>
                        </div>
                        <div class="card-body" id="faq-container">
                            <div class="faq-item border rounded p-3 mb-3">
                                <div class="mb-2">
                                    <label class="form-label">Question 1</label>
                                    <input type="text" name="faq_questions[]" class="form-control"
                                        placeholder="What is the cheapest Hisense TV in Uganda?">
                                </div>
                                <div>
                                    <label class="form-label">Answer</label>
                                    <textarea name="faq_answers[]" class="form-control" rows="2"
                                        placeholder="The cheapest Hisense TV starts from 490,000 UGX..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Product Filters -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ translate('Product_Filters') }}</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small">Products matching these filters will show on this page.</p>
                            
                            <div class="mb-3">
                                <label class="form-label">{{ translate('Product_Type') }}</label>
                                <select name="product_type" class="form-control">
                                    <option value="">All Products</option>
                                    @foreach($productTypes as $type)
                                        <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ translate('Category') }}</label>
                                <select name="category_id" class="form-control">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ translate('Brand') }}</label>
                                <select name="brand_id" class="form-control">
                                    <option value="">All Brands</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ translate('Brand_Filter') }} (text match)</label>
                                <input type="text" name="brand_filter" class="form-control"
                                    placeholder="hisense, samsung">
                                <small class="text-muted">Matches product name containing this text</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ translate('Size_Filter') }}</label>
                                <input type="text" name="size_filter" class="form-control"
                                    placeholder="32, 43, 55">
                                <small class="text-muted">e.g. "32" for 32-inch TVs</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ translate('Feature_Filter') }}</label>
                                <input type="text" name="feature_filter" class="form-control"
                                    placeholder="smart, automatic">
                                <small class="text-muted">e.g. "smart" for Smart TVs</small>
                            </div>
                        </div>
                    </div>

                    <!-- Settings -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ translate('Settings') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-3">
                                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" checked>
                                <label class="form-check-label" for="is_active">{{ translate('Active') }}</label>
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" name="is_indexed" class="form-check-input" id="is_indexed" checked>
                                <label class="form-check-label" for="is_indexed">{{ translate('Include_in_Sitemap') }}</label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            {{ translate('Create_Price_Page') }}
                        </button>
                        <a href="{{ route('admin.price-pages.index') }}" class="btn btn-outline-secondary">
                            {{ translate('Cancel') }}
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        let faqCount = 1;
        function addFaq() {
            faqCount++;
            const container = document.getElementById('faq-container');
            const html = `
                <div class="faq-item border rounded p-3 mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <label class="form-label">Question ${faqCount}</label>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.faq-item').remove()">Remove</button>
                    </div>
                    <div class="mb-2">
                        <input type="text" name="faq_questions[]" class="form-control" placeholder="Question...">
                    </div>
                    <div>
                        <label class="form-label">Answer</label>
                        <textarea name="faq_answers[]" class="form-control" rows="2" placeholder="Answer..."></textarea>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        }
    </script>
@endsection
