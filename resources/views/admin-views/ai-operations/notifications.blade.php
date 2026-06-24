@extends('layouts.admin.app')

@section('title', translate('Smart_Notifications'))

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <h1 class="h3 mb-0 text-capitalize d-flex align-items-center gap-2">
            <i class="fi fi-sr-bell text-warning"></i>
            {{ translate('Smart_Notifications') }}
        </h1>
        <a href="{{ route('admin.ai-operations.dashboard') }}" class="btn btn-outline-primary">
            <i class="fi fi-sr-arrow-left me-1"></i> {{ translate('Back_to_Dashboard') }}
        </a>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="mb-1">{{ $stats['sent_today'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">{{ translate('Sent_Today') }}</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="mb-1">{{ $stats['click_rate'] ?? 0 }}%</h3>
                    <p class="text-muted mb-0">{{ translate('Click_Rate') }}</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="mb-1 text-success">{{ $stats['conversions'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">{{ translate('Conversions') }}</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="mb-1 text-primary">{{ number_format($stats['revenue'] ?? 0) }}</h3>
                    <p class="text-muted mb-0">{{ translate('Revenue_UGX') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Types -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">{{ translate('Notification_Types') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <h6 class="mb-0">{{ translate('Cart_Abandonment') }}</h6>
                            <small class="text-muted">{{ translate('Remind_customers_about_abandoned_carts') }}</small>
                        </div>
                        <span class="badge badge-success">{{ translate('Active') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <h6 class="mb-0">{{ translate('Price_Drop') }}</h6>
                            <small class="text-muted">{{ translate('Alert_when_wishlist_items_go_on_sale') }}</small>
                        </div>
                        <span class="badge badge-success">{{ translate('Active') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <h6 class="mb-0">{{ translate('Back_in_Stock') }}</h6>
                            <small class="text-muted">{{ translate('Notify_when_out_of_stock_items_return') }}</small>
                        </div>
                        <span class="badge badge-success">{{ translate('Active') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">{{ translate('Accessory_Upsell') }}</h6>
                            <small class="text-muted">{{ translate('Suggest_accessories_after_purchase') }}</small>
                        </div>
                        <span class="badge badge-success">{{ translate('Active') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">{{ translate('Performance_by_Type') }}</h5>
                </div>
                <div class="card-body">
                    @if($performanceByType->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>{{ translate('Type') }}</th>
                                        <th>{{ translate('Sent') }}</th>
                                        <th>{{ translate('Clicked') }}</th>
                                        <th>{{ translate('Converted') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($performanceByType as $perf)
                                    <tr>
                                        <td>{{ ucwords(str_replace('_', ' ', $perf->type)) }}</td>
                                        <td>{{ $perf->sent }}</td>
                                        <td>{{ $perf->clicked ?? 0 }}</td>
                                        <td>{{ $perf->converted ?? 0 }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fi fi-sr-chart-line-up fs-1 mb-2 d-block"></i>
                            {{ translate('No_performance_data_yet') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Notifications -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ translate('Recent_Notifications') }}</h5>
        </div>
        <div class="card-body p-0">
            @if($recentNotifications->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>{{ translate('Type') }}</th>
                            <th>{{ translate('User') }}</th>
                            <th>{{ translate('Status') }}</th>
                            <th>{{ translate('Sent_At') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentNotifications as $notif)
                        <tr>
                            <td>
                                <span class="badge badge-soft-primary">
                                    {{ ucwords(str_replace('_', ' ', $notif->type ?? 'unknown')) }}
                                </span>
                            </td>
                            <td>{{ $notif->user_id ?? 'N/A' }}</td>
                            <td>
                                @if($notif->converted ?? false)
                                    <span class="badge badge-success">{{ translate('Converted') }}</span>
                                @elseif($notif->clicked ?? false)
                                    <span class="badge badge-info">{{ translate('Clicked') }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ translate('Sent') }}</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($notif->sent_at)->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center text-muted py-5">
                <i class="fi fi-sr-bell fs-1 mb-3 d-block"></i>
                <h5>{{ translate('No_notifications_sent_yet') }}</h5>
                <p class="text-muted">{{ translate('Smart_notifications_will_be_logged_here') }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
