@extends('layouts.admin.app')

@section('title', translate('Electricity Tariffs'))

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center mb-3">
        <h2 class="h1 mb-0 text-capitalize">
            <img src="{{ dynamicAsset('public/assets/back-end/img/icons/electricity.png') }}" 
                 onerror="this.src='{{ dynamicAsset('public/assets/back-end/img/icons/settings.png') }}'"
                 class="mb-1 mr-1" width="20" alt="">
            {{ translate('Electricity Tariffs') }}
        </h2>
        <a href="{{ route('admin.electricity-tariffs.create') }}" class="btn btn--primary">
            <i class="tio-add"></i> {{ translate('Add New Tariff') }}
        </a>
    </div>

    <!-- Info Card -->
    <div class="alert alert-soft-info mb-3">
        <i class="tio-info-outined"></i>
        {{ translate('Manage electricity tariff rates for the Power Calculator. These rates are used to estimate monthly electricity costs for appliances.') }}
    </div>

    <!-- Tariffs Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ translate('All Tariffs') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ translate('SL') }}</th>
                            <th>{{ translate('Name') }}</th>
                            <th>{{ translate('Code') }}</th>
                            <th>{{ translate('Rate/kWh') }}</th>
                            <th>{{ translate('Service Fee') }}</th>
                            <th>{{ translate('VAT') }} %</th>
                            <th>{{ translate('Units Range') }}</th>
                            <th>{{ translate('Status') }}</th>
                            <th class="text-center">{{ translate('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tariffs as $key => $tariff)
                        <tr>
                            <td>{{ $tariffs->firstItem() + $key }}</td>
                            <td>
                                <strong>{{ $tariff->name }}</strong>
                                @if($tariff->description)
                                    <br><small class="text-muted">{{ Str::limit($tariff->description, 50) }}</small>
                                @endif
                            </td>
                            <td><code>{{ $tariff->code }}</code></td>
                            <td><strong>{{ number_format($tariff->rate_per_kwh) }}</strong> UGX</td>
                            <td>{{ number_format($tariff->service_fee) }} UGX</td>
                            <td>{{ $tariff->vat_percentage }}%</td>
                            <td>
                                {{ $tariff->min_units }} - {{ $tariff->max_units ?? '∞' }} units
                            </td>
                            <td>
                                <form action="{{ route('admin.electricity-tariffs.toggle-status', $tariff->id) }}" 
                                      method="POST" class="d-inline toggle-status-form">
                                    @csrf
                                    <label class="switcher">
                                        <input type="checkbox" class="switcher_input toggle-status" 
                                               {{ $tariff->is_active ? 'checked' : '' }}>
                                        <span class="switcher_control"></span>
                                    </label>
                                </form>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.electricity-tariffs.edit', $tariff->id) }}" 
                                       class="btn btn-outline-info btn-sm" title="{{ translate('Edit') }}">
                                        <i class="tio-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.electricity-tariffs.destroy', $tariff->id) }}" 
                                          method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                title="{{ translate('Delete') }}">
                                            <i class="tio-delete"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <img src="{{ dynamicAsset('public/assets/back-end/img/empty-state.png') }}" 
                                     width="100" alt="">
                                <p class="mt-2">{{ translate('No tariffs found') }}</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="card-footer">
                {{ $tariffs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    // Toggle status
    $('.toggle-status').on('change', function() {
        let form = $(this).closest('.toggle-status-form');
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                toastr.success(response.message);
            },
            error: function() {
                toastr.error('{{ translate("Something went wrong") }}');
            }
        });
    });

    // Delete confirmation
    $('.delete-form').on('submit', function(e) {
        e.preventDefault();
        let form = this;
        Swal.fire({
            title: '{{ translate("Are you sure?") }}',
            text: '{{ translate("You will not be able to recover this tariff!") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ translate("Yes, delete it!") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
@endpush
