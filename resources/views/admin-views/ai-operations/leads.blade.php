@extends('layouts.admin.app')

@section('title', translate('Power_Calculator_Leads'))

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <h1 class="h3 mb-0 text-capitalize d-flex align-items-center gap-2">
            <i class="fi fi-sr-calculator text-success"></i>
            {{ translate('Power_Calculator_Leads') }}
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.ai-operations.leads.export') }}" class="btn btn-success">
                <i class="fi fi-sr-download me-1"></i> {{ translate('Export_CSV') }}
            </a>
            <a href="{{ route('admin.ai-operations.dashboard') }}" class="btn btn-outline-primary">
                <i class="fi fi-sr-arrow-left me-1"></i> {{ translate('Back') }}
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="mb-1">{{ $stats['total'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">{{ translate('Total_Leads') }}</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="mb-1 text-primary">{{ $stats['today'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">{{ translate('Today') }}</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="mb-1 text-success">{{ $stats['contacted'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">{{ translate('Contacted') }}</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="mb-1 text-warning">{{ number_format($stats['avg_battery'] ?? 0, 0) }} Ah</h3>
                    <p class="text-muted mb-0">{{ translate('Avg_Battery_Size') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Leads Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ translate('All_Leads') }}</h5>
        </div>
        <div class="card-body p-0">
            @if($leads->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>{{ translate('Name') }}</th>
                            <th>{{ translate('Phone') }}</th>
                            <th>{{ translate('Monthly_kWh') }}</th>
                            <th>{{ translate('Battery') }}</th>
                            <th>{{ translate('Inverter') }}</th>
                            <th>{{ translate('Solar') }}</th>
                            <th>{{ translate('Status') }}</th>
                            <th>{{ translate('Date') }}</th>
                            <th>{{ translate('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leads as $lead)
                        <tr>
                            <td>
                                <strong>{{ $lead->name ?? 'Anonymous' }}</strong>
                            </td>
                            <td>
                                @if($lead->phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $lead->phone) }}" 
                                   target="_blank" class="text-success">
                                    <i class="fi fi-brands-whatsapp me-1"></i>
                                    {{ $lead->phone }}
                                </a>
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>{{ $lead->monthly_kwh ?? 0 }} kWh</td>
                            <td>
                                <span class="badge badge-soft-primary">{{ $lead->battery_ah ?? 0 }} Ah</span>
                            </td>
                            <td>{{ $lead->inverter_va ?? 0 }} VA</td>
                            <td>{{ $lead->solar_panels ?? 0 }} panels</td>
                            <td>
                                @if($lead->contacted ?? false)
                                    <span class="badge badge-success">{{ translate('Contacted') }}</span>
                                @else
                                    <span class="badge badge-warning">{{ translate('Pending') }}</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($lead->created_at)->format('M d, H:i') }}</td>
                            <td>
                                @if(!($lead->contacted ?? false))
                                <form action="{{ route('admin.ai-operations.leads.contacted', $lead->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="{{ translate('Mark_Contacted') }}">
                                        <i class="fi fi-sr-check"></i>
                                    </button>
                                </form>
                                @endif
                                @if($lead->phone)
                                <a href="tel:{{ $lead->phone }}" class="btn btn-sm btn-outline-primary" title="{{ translate('Call') }}">
                                    <i class="fi fi-sr-phone-call"></i>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="p-3">
                {{ $leads->links() }}
            </div>
            @else
            <div class="text-center text-muted py-5">
                <i class="fi fi-sr-calculator fs-1 mb-3 d-block"></i>
                <h5>{{ translate('No_leads_yet') }}</h5>
                <p class="text-muted">{{ translate('Power_calculator_leads_will_appear_here') }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
