@extends('layouts.admin.app')

@section('title', translate('Power_Calculator_Settings'))

@push('css_or_js')
<style>
    .settings-card {
        transition: all 0.2s;
    }
    .settings-card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .appliance-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 10px 15px;
        margin-bottom: 8px;
    }
    .appliance-item:hover {
        background: #e9ecef;
    }
    .category-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px;
        border-radius: 8px 8px 0 0;
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <h1 class="h3 mb-0 text-capitalize d-flex align-items-center gap-2">
            <i class="fi fi-sr-calculator text-primary"></i>
            {{ translate('Power_Calculator_Settings') }}
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ url('/power-calculator') }}" target="_blank" class="btn btn-outline-info">
                <i class="fi fi-sr-eye me-1"></i> {{ translate('View_Calculator') }}
            </a>
            <form action="{{ route('admin.power-calculator.seed-defaults') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('{{ translate('This_will_create_default_tariffs_and_appliances') }}')">
                    <i class="fi fi-sr-database me-1"></i> {{ translate('Seed_Defaults') }}
                </button>
            </form>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="mb-1">{{ $stats['total_categories'] }}</h3>
                    <p class="text-muted mb-0">{{ translate('Categories') }}</p>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="mb-1">{{ $stats['total_appliances'] }}</h3>
                    <p class="text-muted mb-0">{{ translate('Appliances') }}</p>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="mb-1">{{ $stats['total_leads'] }}</h3>
                    <p class="text-muted mb-0">{{ translate('Total_Leads') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tariffs Section -->
    <div class="card settings-card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fi fi-sr-bolt text-warning me-2"></i>
                {{ translate('Electricity_Tariffs') }}
            </h5>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addTariffModal">
                <i class="fi fi-sr-plus me-1"></i> {{ translate('Add_Tariff') }}
            </button>
        </div>
        <div class="card-body">
            @if($tariffs->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead class="bg-light">
                        <tr>
                            <th>{{ translate('Code') }}</th>
                            <th>{{ translate('Name') }}</th>
                            <th>{{ translate('Type') }}</th>
                            <th>{{ translate('Rates') }} (UGX/kWh)</th>
                            <th>{{ translate('Status') }}</th>
                            <th>{{ translate('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tariffs as $tariff)
                        <tr>
                            <td><code>{{ $tariff->code ?? '-' }}</code></td>
                            <td>
                                <strong>{{ $tariff->name }}</strong>
                                @if($tariff->is_default)
                                    <span class="badge badge-primary ms-1">{{ translate('Default') }}</span>
                                @endif
                                @if($tariff->voltage)
                                    <br><small class="text-muted">{{ $tariff->voltage }}</small>
                                @endif
                            </td>
                            <td>
                                @if($tariff->tariff_type == 'domestic')
                                    <span class="badge badge-soft-info">{{ translate('Domestic') }}</span>
                                @elseif($tariff->tariff_type == 'commercial')
                                    <span class="badge badge-soft-warning">{{ translate('Commercial') }}</span>
                                @else
                                    <span class="badge badge-soft-success">{{ translate('Industrial') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($tariff->tariff_type == 'domestic')
                                    <small>
                                        <strong>Lifeline:</strong> {{ number_format($tariff->rate_lifeline ?? 0, 1) }}<br>
                                        <strong>Tier 1:</strong> {{ number_format($tariff->rate_tier1 ?? 0, 1) }}<br>
                                        <strong>Tier 2:</strong> {{ number_format($tariff->rate_tier2 ?? 0, 1) }}<br>
                                        <strong>Tier 3:</strong> {{ number_format($tariff->rate_tier3 ?? 0, 1) }}
                                    </small>
                                @else
                                    <small>
                                        @if($tariff->rate_peak)
                                            <strong>Peak:</strong> {{ number_format($tariff->rate_peak, 1) }}<br>
                                            <strong>Shoulder:</strong> {{ number_format($tariff->rate_shoulder, 1) }}<br>
                                            <strong>Off-Peak:</strong> {{ number_format($tariff->rate_off_peak, 1) }}<br>
                                        @endif
                                        <strong>Average:</strong> {{ number_format($tariff->rate_average, 1) }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                @if($tariff->is_active)
                                    <span class="badge badge-success">{{ translate('Active') }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ translate('Inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" 
                                        onclick="editTariff({{ json_encode($tariff) }})"
                                        data-bs-toggle="modal" data-bs-target="#editTariffModal">
                                    <i class="fi fi-sr-pencil"></i>
                                </button>
                                @if(!$tariff->is_default)
                                <form action="{{ route('admin.power-calculator.tariffs.delete', $tariff->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ translate('Are_you_sure') }}')">
                                        <i class="fi fi-sr-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-4">
                <i class="fi fi-sr-bolt text-muted fs-1 mb-3 d-block"></i>
                <p class="text-muted">{{ translate('No_tariffs_configured') }}</p>
                <p class="small text-muted">{{ translate('Click_Seed_Defaults_to_load_UEDCL_tariffs') }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Categories & Appliances Section -->
    <div class="card settings-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fi fi-sr-plug text-success me-2"></i>
                {{ translate('Appliance_Categories') }}
            </h5>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="fi fi-sr-plus me-1"></i> {{ translate('Add_Category') }}
            </button>
        </div>
        <div class="card-body">
            @if($categories->count() > 0)
            <div class="row">
                @foreach($categories as $category)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="category-header d-flex justify-content-between align-items-center">
                            <span>
                                @if($category->icon)<i class="{{ $category->icon }} me-2"></i>@endif
                                {{ $category->name }}
                            </span>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                    <i class="fi fi-sr-menu-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="addAppliance({{ $category->id }}, '{{ $category->name }}')" data-bs-toggle="modal" data-bs-target="#addApplianceModal">
                                            <i class="fi fi-sr-plus me-2"></i> {{ translate('Add_Appliance') }}
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.power-calculator.categories.delete', $category->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('{{ translate('Delete_category_and_all_appliances') }}?')">
                                                <i class="fi fi-sr-trash me-2"></i> {{ translate('Delete') }}
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body p-2" style="max-height: 300px; overflow-y: auto;">
                            @forelse($category->appliances as $appliance)
                            <div class="appliance-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $appliance->name }}</strong>
                                    <small class="d-block text-muted">{{ $appliance->wattage }}W · {{ $appliance->default_hours }}h/day</small>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-secondary" onclick="editAppliance({{ json_encode($appliance) }})" data-bs-toggle="modal" data-bs-target="#editApplianceModal">
                                        <i class="fi fi-sr-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.power-calculator.appliances.delete', $appliance->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('{{ translate('Delete_this_appliance') }}?')">
                                            <i class="fi fi-sr-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @empty
                            <p class="text-muted text-center py-3 mb-0">{{ translate('No_appliances') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-4">
                <i class="fi fi-sr-plug text-muted fs-1 mb-3 d-block"></i>
                <p class="text-muted">{{ translate('No_categories_configured') }}</p>
                <p class="small text-muted">{{ translate('Click_Seed_Defaults_to_load_common_appliances') }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Tariff Modal -->
<div class="modal fade" id="addTariffModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.power-calculator.tariffs.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('Add_Tariff') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ translate('Name') }} *</label>
                        <input type="text" class="form-control" name="name" required placeholder="e.g., UEDCL Domestic Q1 2026">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">{{ translate('Lifeline_Rate') }} (0-15 kWh)</label>
                            <input type="number" step="0.1" class="form-control" name="rate_lifeline" value="250" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">{{ translate('Tier_1_Rate') }} (16-80 kWh)</label>
                            <input type="number" step="0.1" class="form-control" name="rate_tier_1" value="756.2" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">{{ translate('Tier_2_Rate') }} (81-150 kWh)</label>
                            <input type="number" step="0.1" class="form-control" name="rate_tier_2" value="412" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">{{ translate('Tier_3_Rate') }} (150+ kWh)</label>
                            <input type="number" step="0.1" class="form-control" name="rate_tier_3" value="756.2" required>
                        </div>
                    </div>
                    <input type="hidden" name="limit_lifeline" value="15">
                    <input type="hidden" name="limit_tier_1" value="80">
                    <input type="hidden" name="limit_tier_2" value="150">
                    <input type="hidden" name="limit_eligibility" value="100">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_default" value="1" id="tariffDefault">
                        <label class="form-check-label" for="tariffDefault">{{ translate('Set_as_default') }}</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ translate('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ translate('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Tariff Modal -->
<div class="modal fade" id="editTariffModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editTariffForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('Edit_Tariff') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ translate('Name') }} *</label>
                        <input type="text" class="form-control" name="name" id="editTariffName" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">{{ translate('Lifeline_Rate') }}</label>
                            <input type="number" step="0.1" class="form-control" name="rate_lifeline" id="editTariffLifeline" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">{{ translate('Tier_1_Rate') }}</label>
                            <input type="number" step="0.1" class="form-control" name="rate_tier_1" id="editTariffTier1" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">{{ translate('Tier_2_Rate') }}</label>
                            <input type="number" step="0.1" class="form-control" name="rate_tier_2" id="editTariffTier2" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">{{ translate('Tier_3_Rate') }}</label>
                            <input type="number" step="0.1" class="form-control" name="rate_tier_3" id="editTariffTier3" required>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_default" value="1" id="editTariffDefault">
                        <label class="form-check-label" for="editTariffDefault">{{ translate('Set_as_default') }}</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ translate('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.power-calculator.categories.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('Add_Category') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ translate('Name') }} *</label>
                        <input type="text" class="form-control" name="name" required placeholder="e.g., Kitchen Appliances">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ translate('Icon') }} (Flaticon class)</label>
                        <input type="text" class="form-control" name="icon" placeholder="e.g., fi-sr-utensils">
                        <small class="text-muted">{{ translate('Leave_empty_for_no_icon') }}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ translate('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ translate('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Appliance Modal -->
<div class="modal fade" id="addApplianceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.power-calculator.appliances.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('Add_Appliance_to') }} <span id="addApplianceCategoryName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="category_id" id="addApplianceCategoryId">
                    <div class="mb-3">
                        <label class="form-label">{{ translate('Name') }} *</label>
                        <input type="text" class="form-control" name="name" required placeholder="e.g., LED TV (43&quot;)">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">{{ translate('Wattage') }} (W) *</label>
                            <input type="number" class="form-control" name="wattage" required placeholder="60">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">{{ translate('Typical_Hours') }}/day *</label>
                            <input type="number" step="0.1" class="form-control" name="typical_hours" required value="1" placeholder="5">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ translate('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ translate('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Appliance Modal -->
<div class="modal fade" id="editApplianceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editApplianceForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">{{ translate('Edit_Appliance') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ translate('Name') }} *</label>
                        <input type="text" class="form-control" name="name" id="editApplianceName" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">{{ translate('Wattage') }} (W) *</label>
                            <input type="number" class="form-control" name="wattage" id="editApplianceWattage" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">{{ translate('Typical_Hours') }}/day *</label>
                            <input type="number" step="0.1" class="form-control" name="typical_hours" id="editApplianceHours" required>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="editApplianceActive" checked>
                        <label class="form-check-label" for="editApplianceActive">{{ translate('Active') }}</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ translate('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
<script>
function editTariff(tariff) {
    document.getElementById('editTariffForm').action = '{{ url("admin/power-calculator/tariffs") }}/' + tariff.id;
    document.getElementById('editTariffName').value = tariff.name;
    document.getElementById('editTariffLifeline').value = tariff.rate_lifeline;
    document.getElementById('editTariffTier1').value = tariff.rate_tier_1;
    document.getElementById('editTariffTier2').value = tariff.rate_tier_2;
    document.getElementById('editTariffTier3').value = tariff.rate_tier_3;
    document.getElementById('editTariffDefault').checked = tariff.is_default;
}

function addAppliance(categoryId, categoryName) {
    document.getElementById('addApplianceCategoryId').value = categoryId;
    document.getElementById('addApplianceCategoryName').textContent = categoryName;
}

function editAppliance(appliance) {
    document.getElementById('editApplianceForm').action = '{{ url("admin/power-calculator/appliances") }}/' + appliance.id;
    document.getElementById('editApplianceName').value = appliance.name;
    document.getElementById('editApplianceWattage').value = appliance.wattage;
    document.getElementById('editApplianceHours').value = appliance.typical_hours;
    document.getElementById('editApplianceActive').checked = appliance.is_active;
}
</script>
@endpush
@endsection
