@extends('layouts.admin.app')

@section('title', translate('Product_Analytics_Dashboard'))

@push('css_or_js')
<style>
    .analytics-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .analytics-card:hover {
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
    .product-view-item {
        border-bottom: 1px solid #eee;
        padding: 10px 0;
        transition: background 0.2s;
    }
    .product-view-item:hover {
        background: #f8f9fa;
    }
    .product-view-item:last-child {
        border-bottom: none;
    }
    .search-tag {
        display: inline-block;
        padding: 5px 12px;
        background: #e9ecef;
        border-radius: 20px;
        margin: 3px;
        font-size: 13px;
        transition: all 0.2s;
    }
    .search-tag:hover {
        background: #007bff;
        color: white;
    }
    .zero-result-tag {
        background: #fff3cd;
        border: 1px solid #ffc107;
    }
    .zero-result-tag:hover {
        background: #ffc107;
        color: #000;
    }
    .chart-container {
        position: relative;
        height: 300px;
    }
    .period-selector .btn {
        padding: 5px 15px;
        font-size: 13px;
    }
    .period-selector .btn.active {
        background: #007bff;
        color: white;
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="mb-3">
        <h1 class="page-header-title d-flex align-items-center gap-2">
            <i class="tio-chart-bar-4"></i>
            {{ translate('Product_Analytics_Dashboard') }}
        </h1>
        <p>{{ translate('Monitor_product_views_searches_and_user_behavior_in_real-time') }}</p>
    </div>

    <!-- Quick Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card analytics-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon bg-primary bg-opacity-10">
                            <i class="tio-visible text-primary" style="font-size: 24px;"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">{{ translate('Today_Views') }}</h6>
                            <h3 class="mb-0" id="today-views">{{ number_format($todayViews) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card analytics-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon bg-success bg-opacity-10">
                            <i class="tio-user text-success" style="font-size: 24px;"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">{{ translate('Unique_Visitors') }}</h6>
                            <h3 class="mb-0" id="unique-visitors">{{ number_format($todayUniqueVisitors) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card analytics-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon bg-info bg-opacity-10">
                            <i class="tio-search text-info" style="font-size: 24px;"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">{{ translate('Today_Searches') }}</h6>
                            <h3 class="mb-0" id="today-searches">{{ number_format($todaySearches) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card analytics-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon bg-warning bg-opacity-10">
                            <span class="live-indicator"></span>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">{{ translate('Active_Now') }}</h6>
                            <h3 class="mb-0" id="active-viewers">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 🤖 Bot vs Human Stats -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="tio-user-outlined me-2"></i>
                        {{ translate('Real-time_Visitors') }} - {{ translate('Humans_vs_Bots') }}
                    </h5>
                    <span class="badge bg-success"><span class="live-indicator"></span> {{ translate('Live') }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Total Active -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="p-3 rounded bg-light text-center">
                                <h2 class="mb-1">{{ $totalActiveSessions ?? 0 }}</h2>
                                <p class="text-muted mb-0">{{ translate('Total_Active') }} (5min)</p>
                            </div>
                        </div>
                        <!-- Humans -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="p-3 rounded bg-success bg-opacity-10 text-center">
                                <h2 class="mb-1 text-success">{{ $activeHumans ?? 0 }}</h2>
                                <p class="text-muted mb-0">👤 {{ translate('Real_Humans') }}</p>
                            </div>
                        </div>
                        <!-- Bots -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="p-3 rounded bg-warning bg-opacity-10 text-center">
                                <h2 class="mb-1 text-warning">{{ $activeBots ?? 0 }}</h2>
                                <p class="text-muted mb-0">🤖 {{ translate('AI_Bots') }}</p>
                            </div>
                        </div>
                        <!-- Ratio -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="p-3 rounded bg-info bg-opacity-10 text-center">
                                <h2 class="mb-1 text-info">
                                    {{ $totalActiveSessions > 0 ? round(($activeHumans / $totalActiveSessions) * 100) : 0 }}%
                                </h2>
                                <p class="text-muted mb-0">{{ translate('Human_Rate') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Bot Breakdown -->
                    @if(isset($botBreakdown) && $botBreakdown->count() > 0)
                    <hr class="my-4">
                    <h6 class="mb-3">🤖 {{ translate('Active_Bots_Breakdown') }}</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($botBreakdown as $bot)
                        <span class="badge bg-secondary">
                            {{ $bot->bot_name ?? 'Unknown' }} 
                            <span class="badge bg-dark ms-1">{{ $bot->count }}</span>
                        </span>
                        @endforeach
                    </div>
                    @endif

                    <!-- Last Hour Summary -->
                    <hr class="my-4">
                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="mb-0">{{ $sessionsLastHour ?? 0 }}</h4>
                            <small class="text-muted">{{ translate('Sessions_Last_Hour') }}</small>
                        </div>
                        <div class="col-4">
                            <h4 class="mb-0 text-success">{{ $humansLastHour ?? 0 }}</h4>
                            <small class="text-muted">{{ translate('Humans_Last_Hour') }}</small>
                        </div>
                        <div class="col-4">
                            <h4 class="mb-0 text-warning">{{ $botsLastHour ?? 0 }}</h4>
                            <small class="text-muted">{{ translate('Bots_Last_Hour') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bot Activity -->
    @if(isset($recentBots) && $recentBots->count() > 0)
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">🤖 {{ translate('Recent_Bot_Activity') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ translate('Bot_Name') }}</th>
                                    <th>{{ translate('Page') }}</th>
                                    <th>{{ translate('IP_Address') }}</th>
                                    <th>{{ translate('Last_Activity') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentBots as $bot)
                                <tr>
                                    <td><span class="badge bg-warning">{{ $bot->bot_name ?? 'Unknown Bot' }}</span></td>
                                    <td>{{ Str::limit($bot->current_page ?? '/', 40) }}</td>
                                    <td><code>{{ $bot->ip_address ?? 'N/A' }}</code></td>
                                    <td>{{ $bot->last_activity ? $bot->last_activity->diffForHumans() : 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Period Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted">{{ translate('This_Week') }}</h6>
                    <h2 class="text-primary">{{ number_format($viewsThisWeek) }}</h2>
                    <small class="text-muted">{{ translate('views') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted">{{ translate('This_Month') }}</h6>
                    <h2 class="text-success">{{ number_format($viewsThisMonth) }}</h2>
                    <small class="text-muted">{{ translate('views') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted">{{ translate('This_Year') }}</h6>
                    <h2 class="text-info">{{ number_format($viewsThisYear) }}</h2>
                    <small class="text-muted">{{ translate('views') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <!-- Live Viewers Section -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <span class="live-indicator"></span>
                        {{ translate('Live_Product_Viewers') }}
                    </h5>
                    <small class="text-muted" id="live-timestamp">{{ translate('Updating...') }}</small>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <div id="live-viewers-list">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">{{ translate('Loading...') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Viewed Today -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="tio-star text-warning"></i>
                        {{ translate('Most_Viewed_Today') }}
                    </h5>
                    <div class="btn-group btn-group-sm period-selector" role="group">
                        <button type="button" class="btn btn-outline-primary active" data-period="today">{{ translate('Today') }}</button>
                        <button type="button" class="btn btn-outline-primary" data-period="week">{{ translate('Week') }}</button>
                        <button type="button" class="btn btn-outline-primary" data-period="month">{{ translate('Month') }}</button>
                    </div>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <div id="most-viewed-list">
                        @forelse($topViewedToday as $item)
                            @if($item->product)
                                <div class="product-view-item d-flex align-items-center gap-3">
                                    <img src="{{ getStorageImages(path: $item->product->thumbnail_full_url, type: 'product') }}"
                                         class="rounded" width="50" height="50" style="object-fit: cover;">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h6 class="mb-1 text-truncate">{{ $item->product->name }}</h6>
                                        <small class="text-muted">{{ webCurrencyConverter($item->product->unit_price) }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-primary">{{ number_format($item->views) }}</span>
                                        <small class="d-block text-muted">{{ translate('views') }}</small>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="tio-chart-bar-4" style="font-size: 48px;"></i>
                                <p class="mt-2">{{ translate('No_views_recorded_today') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Views Chart -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="tio-chart-line"></i>
                        {{ translate('Views_Over_Time') }}
                    </h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary chart-period-btn" data-period="today">{{ translate('Today') }}</button>
                        <button type="button" class="btn btn-outline-primary chart-period-btn active" data-period="week">{{ translate('Week') }}</button>
                        <button type="button" class="btn btn-outline-primary chart-period-btn" data-period="month">{{ translate('Month') }}</button>
                        <button type="button" class="btn btn-outline-primary chart-period-btn" data-period="year">{{ translate('Year') }}</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="viewsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Analytics -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="tio-search"></i>
                        {{ translate('Top_Searches') }}
                    </h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-info search-period-btn active" data-period="week">{{ translate('Week') }}</button>
                        <button type="button" class="btn btn-outline-info search-period-btn" data-period="month">{{ translate('Month') }}</button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="trending-searches">
                        @foreach($trendingSearches as $search)
                            <span class="search-tag">
                                {{ $search->query }}
                                <span class="badge bg-secondary ms-1">{{ $search->count }}</span>
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="tio-warning text-warning"></i>
                        {{ translate('Zero_Result_Searches') }}
                    </h5>
                    <small class="text-muted">{{ translate('What_users_want_but_cant_find') }}</small>
                </div>
                <div class="card-body">
                    <div id="zero-result-searches">
                        <div class="text-center py-3 text-muted">
                            {{ translate('Loading...') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Volume Chart -->
    <div class="row g-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="tio-chart-bar-1"></i>
                        {{ translate('Search_Volume') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="searchChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let viewsChart, searchChart;

    $(document).ready(function() {
        // Initialize charts
        initViewsChart();
        initSearchChart();

        // Load initial data
        loadLiveViewers();
        loadSearchAnalytics('week');

        // Auto-refresh live viewers every 30 seconds
        setInterval(loadLiveViewers, 30000);

        // Period selector for most viewed
        $('.period-selector .btn').on('click', function() {
            $('.period-selector .btn').removeClass('active');
            $(this).addClass('active');
            loadMostViewed($(this).data('period'));
        });

        // Chart period selector
        $('.chart-period-btn').on('click', function() {
            $('.chart-period-btn').removeClass('active');
            $(this).addClass('active');
            updateViewsChart($(this).data('period'));
        });

        // Search period selector
        $('.search-period-btn').on('click', function() {
            $('.search-period-btn').removeClass('active');
            $(this).addClass('active');
            loadSearchAnalytics($(this).data('period'));
        });
    });

    function loadLiveViewers() {
        $.get('{{ route("admin.analytics.live-viewers") }}', function(data) {
            $('#active-viewers').text(data.total_active_users);
            $('#live-timestamp').text('{{ translate("Updated") }}: ' + data.timestamp);

            let html = '';
            if (data.products.length === 0) {
                html = '<div class="text-center py-4 text-muted"><i class="tio-user" style="font-size: 48px;"></i><p class="mt-2">{{ translate("No_active_viewers_right_now") }}</p></div>';
            } else {
                data.products.forEach(function(item) {
                    html += `
                        <div class="product-view-item d-flex align-items-center gap-3">
                            <img src="${item.product.thumbnail_full_url?.key ? '{{ asset("storage") }}/' + item.product.thumbnail_full_url.key : '{{ dynamicAsset("public/assets/back-end/img/image-place-holder.png") }}'}"
                                 class="rounded" width="50" height="50" style="object-fit: cover;">
                            <div class="flex-grow-1 overflow-hidden">
                                <h6 class="mb-1 text-truncate">${item.product.name}</h6>
                                <small class="text-muted">${item.last_view}</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-success">${item.active_viewers}</span>
                                <small class="d-block text-muted">{{ translate("viewing") }}</small>
                            </div>
                        </div>
                    `;
                });
            }
            $('#live-viewers-list').html(html);
        });
    }

    function loadMostViewed(period) {
        $.get('{{ route("admin.analytics.most-viewed") }}', { period: period }, function(data) {
            let html = '';
            if (data.products.length === 0) {
                html = '<div class="text-center py-4 text-muted"><i class="tio-chart-bar-4" style="font-size: 48px;"></i><p class="mt-2">{{ translate("No_data_for_this_period") }}</p></div>';
            } else {
                data.products.forEach(function(item) {
                    const imgSrc = item.product.thumbnail_full_url?.key
                        ? '{{ asset("storage") }}/' + item.product.thumbnail_full_url.key
                        : '{{ dynamicAsset("public/assets/back-end/img/image-place-holder.png") }}';
                    html += `
                        <div class="product-view-item d-flex align-items-center gap-3">
                            <img src="${imgSrc}" class="rounded" width="50" height="50" style="object-fit: cover;">
                            <div class="flex-grow-1 overflow-hidden">
                                <h6 class="mb-1 text-truncate">${item.product.name}</h6>
                                <small class="text-muted">${item.unique_visitors} {{ translate("unique_visitors") }}</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-primary">${item.views}</span>
                                <small class="d-block text-muted">{{ translate("views") }}</small>
                            </div>
                        </div>
                    `;
                });
            }
            $('#most-viewed-list').html(html);
        });
    }

    function initViewsChart() {
        const ctx = document.getElementById('viewsChart').getContext('2d');
        viewsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: '{{ translate("Total_Views") }}',
                    data: [],
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    fill: true,
                    tension: 0.4
                }, {
                    label: '{{ translate("Unique_Visitors") }}',
                    data: [],
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        updateViewsChart('week');
    }

    function updateViewsChart(period) {
        $.get('{{ route("admin.analytics.views-chart") }}', { period: period }, function(data) {
            viewsChart.data.labels = data.labels;
            viewsChart.data.datasets[0].data = data.views;
            viewsChart.data.datasets[1].data = data.unique_visitors;
            viewsChart.update();
        });
    }

    function initSearchChart() {
        const ctx = document.getElementById('searchChart').getContext('2d');
        searchChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: '{{ translate("Searches") }}',
                    data: [],
                    backgroundColor: 'rgba(23, 162, 184, 0.7)',
                    borderColor: '#17a2b8',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    function loadSearchAnalytics(period) {
        $.get('{{ route("admin.analytics.search-analytics") }}', { period: period }, function(data) {
            // Update trending searches
            let searchHtml = '';
            data.top_searches.forEach(function(search) {
                searchHtml += `<span class="search-tag">${search.query} <span class="badge bg-secondary ms-1">${search.search_count}</span></span>`;
            });
            $('#trending-searches').html(searchHtml || '<div class="text-muted">{{ translate("No_searches_found") }}</div>');

            // Update zero result searches
            let zeroHtml = '';
            data.zero_result_searches.forEach(function(search) {
                zeroHtml += `<span class="search-tag zero-result-tag">${search.query} <span class="badge bg-warning text-dark ms-1">${search.count}</span></span>`;
            });
            $('#zero-result-searches').html(zeroHtml || '<div class="text-muted text-center py-2">{{ translate("No_zero_result_searches") }}</div>');

            // Update search volume chart
            const labels = data.search_volume.map(item => item.label);
            const counts = data.search_volume.map(item => item.count);
            searchChart.data.labels = labels;
            searchChart.data.datasets[0].data = counts;
            searchChart.update();
        });
    }
</script>
@endpush
