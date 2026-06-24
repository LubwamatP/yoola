@extends('layouts.admin.app')
@section('title', 'AI Operations Dashboard')
@section('content')
<div class="content container-fluid">
    <div class="mb-4">
        <h2 class="h1 mb-0">AI Operations Dashboard</h2>
        <p class="text-muted">Yoola Alpha — Powered by Google Gemini</p>
    </div>
    <div class="row g-3">
        <div class="col-md-4">
            <a href="{{ route('admin.ai-operations.notifications') }}" class="card border-0 shadow-sm text-decoration-none h-100">
                <div class="card-body text-center py-4">
                    <i class="tio-notifications fs-48 text-primary"></i>
                    <h5 class="mt-3">Smart Notifications</h5>
                    <p class="text-muted fs-12">AI-powered store insights and alerts</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.ai-operations.conversations') }}" class="card border-0 shadow-sm text-decoration-none h-100">
                <div class="card-body text-center py-4">
                    <i class="tio-chat fs-48 text-success"></i>
                    <h5 class="mt-3">AI Chat</h5>
                    <p class="text-muted fs-12">Chat with Yoola Alpha for SEO, content & strategy</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.third-party.ai-setting.index') }}" class="card border-0 shadow-sm text-decoration-none h-100">
                <div class="card-body text-center py-4">
                    <i class="tio-settings fs-48 text-warning"></i>
                    <h5 class="mt-3">AI Settings</h5>
                    <p class="text-muted fs-12">Configure AI providers and API keys</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
