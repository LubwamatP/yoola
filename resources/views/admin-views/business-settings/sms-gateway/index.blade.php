@extends('layouts.back-end.app')

@section('title', translate('SMS Gateway Settings'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
            <i class="bi bi-chat-dots"></i>
            {{ translate('SMS Gateway Settings') }}
        </h2>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ translate('SMS Gateway Configuration') }}</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                {{ translate('Configure your SMS gateway to send order notifications, OTP codes, and marketing messages to customers.') }}
            </div>

            <form action="{{ route('admin.business-settings.sms-gateway.update') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ translate('SMS Gateway') }}</label>
                        <select name="sms_gateway" class="form-select">
                            <option value="">{{ translate('Select Gateway') }}</option>
                            <option value="twilio">Twilio</option>
                            <option value="africastalking">Africa's Talking</option>
                            <option value="infobip">Infobip</option>
                            <option value="nexmo">Vonage (Nexmo)</option>
                            <option value="custom">Custom SMS API</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ translate('Sender ID / From Number') }}</label>
                        <input type="text" name="sender_id" class="form-control" placeholder="YOOLA" maxlength="11">
                        <small class="text-muted">{{ translate('The name or number that appears as the sender.') }}</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ translate('API Key / Account SID') }}</label>
                        <input type="text" name="api_key" class="form-control" placeholder="Enter API key">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ translate('API Secret / Auth Token') }}</label>
                        <input type="password" name="api_secret" class="form-control" placeholder="Enter API secret">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ translate('API Endpoint URL') }}</label>
                        <input type="url" name="api_url" class="form-control" placeholder="https://api.example.com/sms/send">
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch mb-3">
                            <input type="checkbox" name="is_active" class="form-check-input" value="1">
                            <label class="form-check-label fw-semibold">{{ translate('Enable SMS Gateway') }}</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <h6 class="fw-semibold mb-3">{{ translate('SMS Templates') }}</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ translate('Event') }}</th>
                                        <th>{{ translate('Template') }}</th>
                                        <th>{{ translate('Variables') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ translate('Order Confirmation') }}</td>
                                        <td>
                                            <textarea name="template_order_confirm" class="form-control form-control-sm" rows="2">Your order #{order_id} has been confirmed. Total: {amount} UGX. Thank you for shopping with Yoola!</textarea>
                                        </td>
                                        <td><code>{order_id}</code> <code>{amount}</code></td>
                                    </tr>
                                    <tr>
                                        <td>{{ translate('Order Shipped') }}</td>
                                        <td>
                                            <textarea name="template_order_shipped" class="form-control form-control-sm" rows="2">Your order #{order_id} is on the way! Track it here: {tracking_url}</textarea>
                                        </td>
                                        <td><code>{order_id}</code> <code>{tracking_url}</code></td>
                                    </tr>
                                    <tr>
                                        <td>{{ translate('OTP Verification') }}</td>
                                        <td>
                                            <textarea name="template_otp" class="form-control form-control-sm" rows="2">Your Yoola verification code is: {otp}. Valid for 10 minutes.</textarea>
                                        </td>
                                        <td><code>{otp}</code></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> {{ translate('Save Settings') }}
                        </button>
                        <button type="button" class="btn btn-outline-secondary ms-2" id="test-sms">
                            <i class="bi bi-send"></i> {{ translate('Send Test SMS') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    $('#test-sms').on('click', function() {
        const phone = prompt("{{ translate('Enter phone number to send test SMS:') }}", "+256");
        if (phone) {
            $.ajax({
                url: '{{ route("admin.business-settings.sms-gateway.test") }}',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', phone: phone },
                success: function(resp) {
                    toastr.success(resp.message || '{{ translate("Test SMS sent") }}');
                },
                error: function() {
                    toastr.error('{{ translate("Failed to send test SMS") }}');
                }
            });
        }
    });
</script>
@endpush
