{{-- Homepage Trust Strip - Builds buyer confidence --}}
<section class="trust-strip-section py-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="trust-strip d-flex flex-wrap justify-content-center align-items-center gap-3 gap-md-5">
                    
                    <div class="trust-item d-flex align-items-center gap-2">
                        <div class="trust-icon" style="width: 40px; height: 40px; background: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" fill="white" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ translate('verified_sellers') }}</div>
                            <div class="text-muted" style="font-size: 0.8rem;">{{ translate('100%_genuine_products') }}</div>
                        </div>
                    </div>

                    <div class="trust-item d-flex align-items-center gap-2">
                        <div class="trust-icon" style="width: 40px; height: 40px; background: #3b82f6; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" fill="white" viewBox="0 0 24 24">
                                <path d="M18 18.5c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5-1.5.67-1.5 1.5.67 1.5 1.5 1.5zM19.96 10H16V7a4 4 0 0 0-8 0v3H3.04c-.55 0-1 .45-1 1v6c0 2.21 1.79 4 4 4h12c2.21 0 4-1.79 4-4v-6c-.01-.55-.46-1-1.04-1zM10 7a2 2 0 0 1 4 0v3h-4V7zm8 10c0 1.1-.9 2-2 2H6c-1.1 0-2-.9-2-2v-5h14v5z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ translate('free_kampala_delivery') }}</div>
                            <div class="text-muted" style="font-size: 0.8rem;">{{ translate('on_orders_above') }} 100K</div>
                        </div>
                    </div>

                    <div class="trust-item d-flex align-items-center gap-2">
                        <div class="trust-icon" style="width: 40px; height: 40px; background: #f59e0b; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" fill="white" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ translate('cash_on_delivery') }}</div>
                            <div class="text-muted" style="font-size: 0.8rem;">{{ translate('pay_when_you_receive') }}</div>
                        </div>
                    </div>

                    <div class="trust-item d-flex align-items-center gap-2">
                        <div class="trust-icon" style="width: 40px; height: 40px; background: #8b5cf6; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" fill="white" viewBox="0 0 24 24">
                                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ translate('warranty_included') }}</div>
                            <div class="text-muted" style="font-size: 0.8rem;">{{ translate('on_all_electronics') }}</div>
                        </div>
                    </div>

                    <div class="trust-item d-flex align-items-center gap-2">
                        <div class="trust-icon" style="width: 40px; height: 40px; background: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" fill="white" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ translate('7_day_returns') }}</div>
                            <div class="text-muted" style="font-size: 0.8rem;">{{ translate('easy_return_policy') }}</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .trust-strip-section {
        border-top: 1px solid #e5e7eb;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .trust-item {
        padding: 8px 16px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        transition: all 0.3s ease;
    }
    
    .trust-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    @media (max-width: 768px) {
        .trust-strip {
            gap: 10px !important;
        }
        .trust-item {
            flex: 1 1 calc(50% - 10px);
            min-width: 150px;
        }
    }
</style>
