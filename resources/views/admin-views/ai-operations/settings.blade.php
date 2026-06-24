@extends('layouts.admin.app')

@section('title', translate('AI_Settings'))

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <h1 class="h3 mb-0 text-capitalize d-flex align-items-center gap-2">
            <i class="fi fi-sr-settings text-primary"></i>
            {{ translate('AI_Settings') }}
        </h1>
        <a href="{{ route('admin.ai-operations.dashboard') }}" class="btn btn-outline-primary">
            <i class="fi fi-sr-arrow-left me-1"></i> {{ translate('Back_to_Dashboard') }}
        </a>
    </div>

    <div class="row g-4">
        <!-- AI Provider -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">{{ translate('AI_Provider') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">{{ translate('Current_Provider') }}</label>
                        <div class="d-flex align-items-center gap-2">
                            @if($settings['ai_provider'] == 'gemini')
                                <i class="fi fi-brands-google text-primary fs-4"></i>
                                <span>Google Gemini</span>
                            @elseif($settings['ai_provider'] == 'claude')
                                <span class="fs-4">🤖</span>
                                <span>Anthropic Claude</span>
                            @else
                                <span>{{ ucfirst($settings['ai_provider']) }}</span>
                            @endif
                            <span class="badge badge-success ms-2">{{ translate('Active') }}</span>
                        </div>
                    </div>
                    <p class="text-muted small">
                        {{ translate('AI_provider_is_configured_in_env_file') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Notification Settings -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">{{ translate('Notification_Limits') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">{{ translate('Daily_Limit_Per_User') }}</label>
                        <input type="number" class="form-control" value="{{ $settings['daily_notification_limit'] }}" disabled>
                        <small class="text-muted">{{ translate('Maximum_notifications_per_user_per_day') }}</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ translate('Escalation_Threshold_UGX') }}</label>
                        <input type="number" class="form-control" value="{{ $settings['escalation_threshold'] }}" disabled>
                        <small class="text-muted">{{ translate('Cart_value_above_which_to_escalate_to_human') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feature Toggles -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ translate('Smart_Notification_Types') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                <div>
                                    <h6 class="mb-0">{{ translate('Cart_Abandonment') }}</h6>
                                    <small class="text-muted">{{ translate('Recover_abandoned_carts') }}</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" {{ $settings['cart_abandonment_enabled'] ? 'checked' : '' }} disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                <div>
                                    <h6 class="mb-0">{{ translate('Price_Drop') }}</h6>
                                    <small class="text-muted">{{ translate('Wishlist_price_alerts') }}</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" {{ $settings['price_drop_enabled'] ? 'checked' : '' }} disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                <div>
                                    <h6 class="mb-0">{{ translate('Back_in_Stock') }}</h6>
                                    <small class="text-muted">{{ translate('Restock_notifications') }}</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" {{ $settings['back_in_stock_enabled'] ? 'checked' : '' }} disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                <div>
                                    <h6 class="mb-0">{{ translate('Accessory_Upsell') }}</h6>
                                    <small class="text-muted">{{ translate('Post_purchase_suggestions') }}</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" {{ $settings['accessory_upsell_enabled'] ? 'checked' : '' }} disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info mt-4 mb-0">
                        <i class="fi fi-sr-info me-2"></i>
                        {{ translate('Settings_are_currently_read_only_Edit_in_config_files_or_database') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
