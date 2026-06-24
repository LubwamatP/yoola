@extends('layouts.admin.app')
@section('title','AI Operations Settings')
@section('content')
<div class="content container-fluid">
    <h2 class="h1 mb-4">AI Operations Settings</h2>
    <div class="card">
        <div class="card-body">
            <p><strong>Gemini API Key:</strong> {{ $geminiKey ? substr($geminiKey,0,15).'...' : 'Not configured' }}</p>
            <a href="{{ route('admin.third-party.ai-setting.index') }}" class="btn btn-primary">Configure AI Providers</a>
        </div>
    </div>
</div>
@endsection
