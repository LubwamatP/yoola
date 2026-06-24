@extends('layouts.admin.app')

@section('title', translate('AI_Operations_Dashboard'))

@push('css_or_js')
<style>
    .ai-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border-radius: 12px;
    }
    .ai-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .stat-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
    }
    .live-indicator {
        width: 10px;
        height: 10px;
        background: #28a745;
        border-radius: 50%;
        animation: pulse 1.5s infinite;
        display: inline-block;
        margin-right: 5px;
    }
    @keyframes pulse {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.2); }
        100% { opacity: 1; transform: scale(1); }
    }
    .resolution-rate {
        font-size: 48px;
        font-weight: bold;
        color: #28a745;
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <h1 class="h3 mb-0 text-capitalize d-flex align-items-center gap-2">
            <i class="fi fi-sr-brain-circuit text-primary"></i>
            {{ translate('AI_Operations_Dashboard') }}
        </h1>
        <span class="badge badge-soft-success">
            <span class="live-indicator"></span>
            {{ translate('Live') }}
        </span>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <!-- AI Chat Stats -->
        <div class="col-sm-6 col-lg-3">
            <div class="card ai-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon bg-primary-light">
                            <i class="fi fi-sr-comments text-primary fs-4"></i>
                        </div>
                    </div>
                    <h3 class="mb-1">{{ $chatStats['total_today'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">{{ translate('Chats_Today') }}</p>
                </div>
            </div>
        </div>

        <!-- AI Resolution Rate -->
        <div class="col-sm-6 col-lg-3">
            <div class="card ai-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon bg-success-light">
                            <i class="fi fi-sr-check-circle text-success fs-4"></i>
                        </div>
                    </div>
                    <h3 class="mb-1">{{ $aiResolutionRate }}%</h3>
                    <p class="text-muted mb-0">{{ translate('AI_Resolution_Rate') }}</p>
                </div>
            </div>
        </div>

        <!-- Notifications Sent -->
        <div class="col-sm-6 col-lg-3">
            <div class="card ai-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon bg-warning-light">
                            <i class="fi fi-sr-bell text-warning fs-4"></i>
                        </div>
                    </div>
                    <h3 class="mb-1">{{ $notificationStats['today']['sent'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">{{ translate('Notifications_Today') }}</p>
                </div>
            </div>
        </div>

        <!-- Leads -->
        <div class="col-sm-6 col-lg-3">
            <div class="card ai-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon bg-info-light">
                            <i class="fi fi-sr-user-add text-info fs-4"></i>
                        </div>
                    </div>
                    <h3 class="mb-1">{{ $totalLeads }}</h3>
                    <p class="text-muted mb-0">{{ translate('Leads_This_Month') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <a href="{{ route('admin.ai-operations.conversations') }}" class="card ai-card h-100 text-decoration-none">
                <div class="card-body text-center py-4">
                    <i class="fi fi-sr-comments text-primary fs-1 mb-3"></i>
                    <h5>{{ translate('AI_Chat_Monitor') }}</h5>
                    <p class="text-muted mb-0">{{ translate('View_live_conversations') }}</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.ai-operations.notifications') }}" class="card ai-card h-100 text-decoration-none">
                <div class="card-body text-center py-4">
                    <i class="fi fi-sr-bell text-warning fs-1 mb-3"></i>
                    <h5>{{ translate('Smart_Notifications') }}</h5>
                    <p class="text-muted mb-0">{{ translate('Manage_automated_notifications') }}</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.ai-operations.leads') }}" class="card ai-card h-100 text-decoration-none">
                <div class="card-body text-center py-4">
                    <i class="fi fi-sr-calculator text-success fs-1 mb-3"></i>
                    <h5>{{ translate('Power_Calculator_Leads') }}</h5>
                    <p class="text-muted mb-0">{{ translate('View_and_contact_leads') }}</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Leads -->
    @if($recentLeads->count() > 0)
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ translate('Recent_Leads') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>{{ translate('Name') }}</th>
                            <th>{{ translate('Phone') }}</th>
                            <th>{{ translate('Battery_Size') }}</th>
                            <th>{{ translate('Date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentLeads as $lead)
                        <tr>
                            <td>{{ $lead->name ?? 'N/A' }}</td>
                            <td>{{ $lead->phone ?? 'N/A' }}</td>
                            <td>{{ $lead->battery_ah ?? 0 }} Ah</td>
                            <td>{{ \Carbon\Carbon::parse($lead->created_at)->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fi fi-sr-info text-muted fs-1 mb-3"></i>
            <h5>{{ translate('No_data_yet') }}</h5>
            <p class="text-muted">{{ translate('AI_operations_data_will_appear_here_once_available') }}</p>
        </div>
    </div>
    @endif
</div>
@endsection
