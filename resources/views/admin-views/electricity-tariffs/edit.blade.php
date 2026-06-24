@extends('layouts.admin.app')

@section('title', translate('Edit Electricity Tariff'))

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center mb-3">
        <h2 class="h1 mb-0 text-capitalize">
            <img src="{{ dynamicAsset('public/assets/back-end/img/icons/settings.png') }}" class="mb-1 mr-1" width="20" alt="">
            {{ translate('Edit Tariff') }}: {{ $tariff->name }}
        </h2>
        <a href="{{ route('admin.electricity-tariffs.index') }}" class="btn btn-outline-primary">
            <i class="tio-arrow-left"></i> {{ translate('Back to List') }}
        </a>
    </div>

    <!-- Form -->
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.electricity-tariffs.update', $tariff->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="title-color">{{ translate('Tariff Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" 
                                   value="{{ old('name', $tariff->name) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="title-color">{{ translate('Code') }} <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" 
                                   value="{{ old('code', $tariff->code) }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="title-color">{{ translate('Rate per kWh (UGX)') }} <span class="text-danger">*</span></label>
                            <input type="number" name="rate_per_kwh" class="form-control" 
                                   step="0.01" min="0" 
                                   value="{{ old('rate_per_kwh', $tariff->rate_per_kwh) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="title-color">{{ translate('Service Fee (UGX)') }} <span class="text-danger">*</span></label>
                            <input type="number" name="service_fee" class="form-control" 
                                   step="0.01" min="0" 
                                   value="{{ old('service_fee', $tariff->service_fee) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="title-color">{{ translate('VAT Percentage') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="vat_percentage" class="form-control" 
                                       step="0.01" min="0" max="100" 
                                       value="{{ old('vat_percentage', $tariff->vat_percentage) }}" required>
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
                                   min="0" 
                                   value="{{ old('min_units', $tariff->min_units) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="title-color">{{ translate('Maximum Units') }}</label>
                            <input type="number" name="max_units" class="form-control" 
                                   min="0" placeholder="{{ translate('Leave empty for unlimited') }}" 
                                   value="{{ old('max_units', $tariff->max_units) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="title-color">{{ translate('Sort Order') }}</label>
                            <input type="number" name="sort_order" class="form-control" 
                                   min="0" 
                                   value="{{ old('sort_order', $tariff->sort_order) }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="title-color">{{ translate('Description') }}</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $tariff->description) }}</textarea>
                </div>

                <div class="form-group">
                    <label class="switcher">
                        <input type="checkbox" name="is_active" class="switcher_input" 
                               {{ $tariff->is_active ? 'checked' : '' }}>
                        <span class="switcher_control"></span>
                        <span class="ml-2">{{ translate('Active') }}</span>
                    </label>
                </div>

                <div class="d-flex justify-content-end gap-3">
                    <a href="{{ route('admin.electricity-tariffs.index') }}" class="btn btn-secondary">
                        {{ translate('Cancel') }}
                    </a>
                    <button type="submit" class="btn btn--primary">
                        <i class="tio-save"></i> {{ translate('Update Tariff') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
