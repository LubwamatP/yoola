@extends('layouts.admin.app')

@section('title', translate('Add Electricity Tariff'))

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center mb-3">
        <h2 class="h1 mb-0 text-capitalize">
            <img src="{{ dynamicAsset('public/assets/back-end/img/icons/settings.png') }}" class="mb-1 mr-1" width="20" alt="">
            {{ translate('Add New Tariff') }}
        </h2>
        <a href="{{ route('admin.electricity-tariffs.index') }}" class="btn btn-outline-primary">
            <i class="tio-arrow-left"></i> {{ translate('Back to List') }}
        </a>
    </div>

    <!-- Form -->
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.electricity-tariffs.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="title-color">{{ translate('Tariff Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" 
                                   placeholder="{{ translate('e.g., Domestic Standard') }}" 
                                   value="{{ old('name') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="title-color">{{ translate('Code') }} <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" 
                                   placeholder="{{ translate('e.g., DOM_STANDARD') }}" 
                                   value="{{ old('code') }}" required>
                            <small class="text-muted">{{ translate('Unique identifier, will be converted to uppercase') }}</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="title-color">{{ translate('Rate per kWh (UGX)') }} <span class="text-danger">*</span></label>
                            <input type="number" name="rate_per_kwh" class="form-control" 
                                   step="0.01" min="0" placeholder="0.00" 
                                   value="{{ old('rate_per_kwh') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="title-color">{{ translate('Service Fee (UGX)') }} <span class="text-danger">*</span></label>
                            <input type="number" name="service_fee" class="form-control" 
                                   step="0.01" min="0" placeholder="0.00" 
                                   value="{{ old('service_fee', 0) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="title-color">{{ translate('VAT Percentage') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="vat_percentage" class="form-control" 
                                       step="0.01" min="0" max="100" placeholder="18.00" 
                                       value="{{ old('vat_percentage', 18) }}" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="title-color">{{ translate('Minimum Units') }} <span class="text-danger">*</span></label>
                            <input type="number" name="min_units" class="form-control" 
                                   min="0" placeholder="0" 
                                   value="{{ old('min_units', 0) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="title-color">{{ translate('Maximum Units') }}</label>
                            <input type="number" name="max_units" class="form-control" 
                                   min="0" placeholder="{{ translate('Leave empty for unlimited') }}" 
                                   value="{{ old('max_units') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="title-color">{{ translate('Sort Order') }}</label>
                            <input type="number" name="sort_order" class="form-control" 
                                   min="0" placeholder="0" 
                                   value="{{ old('sort_order', 0) }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="title-color">{{ translate('Description') }}</label>
                    <textarea name="description" class="form-control" rows="3" 
                              placeholder="{{ translate('Optional description for this tariff') }}">{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="switcher">
                        <input type="checkbox" name="is_active" class="switcher_input" checked>
                        <span class="switcher_control"></span>
                        <span class="ml-2">{{ translate('Active') }}</span>
                    </label>
                </div>

                <div class="d-flex justify-content-end gap-3">
                    <a href="{{ route('admin.electricity-tariffs.index') }}" class="btn btn-secondary">
                        {{ translate('Cancel') }}
                    </a>
                    <button type="submit" class="btn btn--primary">
                        <i class="tio-save"></i> {{ translate('Save Tariff') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
