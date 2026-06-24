@extends('layouts.admin.app')

@section('title', translate('AI_Chat_Monitor'))

@push('css_or_js')
<style>
    .conversation-card {
        cursor: pointer;
        transition: all 0.2s;
        border-left: 3px solid transparent;
    }
    .conversation-card:hover, .conversation-card.active {
        background: #f8f9fa;
        border-left-color: #007bff;
    }
    .conversation-card.escalated {
        border-left-color: #dc3545;
        background: #fff5f5;
    }
    .chat-panel {
        height: 500px;
        overflow-y: auto;
    }
    .message-bubble {
        max-width: 80%;
        padding: 10px 15px;
        border-radius: 15px;
        margin-bottom: 10px;
    }
    .message-user {
        background: #e9ecef;
        margin-right: auto;
    }
    .message-ai {
        background: #007bff;
        color: white;
        margin-left: auto;
    }
    .message-admin {
        background: #28a745;
        color: white;
        margin-left: auto;
    }
    .live-indicator {
        width: 8px;
        height: 8px;
        background: #28a745;
        border-radius: 50%;
        animation: pulse 1.5s infinite;
        display: inline-block;
    }
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <h1 class="h3 mb-0 text-capitalize d-flex align-items-center gap-2">
            <i class="fi fi-sr-comments text-primary"></i>
            {{ translate('AI_Chat_Monitor') }}
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
                    <h3 class="mb-1">{{ $stats['today'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">{{ translate('Conversations_Today') }}</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="mb-1 text-success">{{ $stats['ai_resolved'] ?? 0 }}%</h3>
                    <p class="text-muted mb-0">{{ translate('AI_Resolution_Rate') }}</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="mb-1 text-danger">{{ $stats['escalated'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">{{ translate('Escalated') }}</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="mb-1 text-primary">
                        <span class="live-indicator me-1"></span>
                        {{ $stats['active'] ?? 0 }}
                    </h3>
                    <p class="text-muted mb-0">{{ translate('Active_Now') }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($conversations->count() > 0)
    <div class="row">
        <!-- Conversation List -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ translate('Active_Conversations') }}</h5>
                </div>
                <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                    @foreach($conversations as $conv)
                    <div class="conversation-card p-3 border-bottom {{ $conv->status == 'escalated' ? 'escalated' : '' }}" 
                         data-id="{{ $conv->id }}"
                         onclick="loadConversation({{ $conv->id }})">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $conv->user_name ?? 'Guest' }}</h6>
                                <small class="text-muted">{{ $conv->user_phone ?? 'No phone' }}</small>
                            </div>
                            <span class="badge {{ $conv->status == 'escalated' ? 'badge-danger' : 'badge-primary' }}">
                                {{ ucfirst($conv->status) }}
                            </span>
                        </div>
                        <small class="text-muted d-block mt-2">
                            {{ \Carbon\Carbon::parse($conv->updated_at)->diffForHumans() }}
                        </small>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Chat Panel -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0" id="chatHeader">{{ translate('Select_a_conversation') }}</h5>
                    <div id="chatActions" style="display: none;">
                        <button class="btn btn-sm btn-success" onclick="takeoverChat()">
                            <i class="fi fi-sr-user"></i> {{ translate('Take_Over') }}
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="handbackChat()" style="display: none;" id="handbackBtn">
                            <i class="fi fi-sr-robot"></i> {{ translate('Hand_Back_to_AI') }}
                        </button>
                    </div>
                </div>
                <div class="card-body chat-panel" id="chatMessages">
                    <div class="text-center text-muted py-5">
                        <i class="fi fi-sr-comments fs-1 mb-3 d-block"></i>
                        {{ translate('Select_a_conversation_to_view_messages') }}
                    </div>
                </div>
                <div class="card-footer" id="chatInput" style="display: none;">
                    <form onsubmit="sendMessage(event)">
                        <div class="input-group">
                            <input type="text" class="form-control" id="messageInput" placeholder="{{ translate('Type_your_message') }}...">
                            <button class="btn btn-primary" type="submit">
                                <i class="fi fi-sr-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fi fi-sr-comments text-muted fs-1 mb-3"></i>
            <h5>{{ translate('No_active_conversations') }}</h5>
            <p class="text-muted">{{ translate('Active_AI_chat_conversations_will_appear_here') }}</p>
        </div>
    </div>
    @endif
</div>

@push('script')
<script>
let currentConversationId = null;

function loadConversation(id) {
    currentConversationId = id;
    document.querySelectorAll('.conversation-card').forEach(c => c.classList.remove('active'));
    document.querySelector(`[data-id="${id}"]`).classList.add('active');
    
    fetch(`{{ url('admin/ai-operations/conversations') }}/${id}/messages`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('chatActions').style.display = 'block';
            document.getElementById('chatHeader').textContent = data.conversation?.user_name || 'Chat';
            
            let html = '';
            data.messages.forEach(m => {
                const cls = m.sender_type === 'user' ? 'message-user' : 
                           (m.sender_type === 'admin' ? 'message-admin' : 'message-ai');
                html += `<div class="message-bubble ${cls}">${m.message}</div>`;
            });
            document.getElementById('chatMessages').innerHTML = html || '<div class="text-center text-muted">No messages</div>';
            document.getElementById('chatMessages').scrollTop = 99999;
        });
}

function takeoverChat() {
    if (!currentConversationId) return;
    fetch(`{{ url('admin/ai-operations/conversations') }}/${currentConversationId}/takeover`, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
    }).then(() => {
        document.getElementById('chatInput').style.display = 'block';
        document.getElementById('handbackBtn').style.display = 'inline-block';
    });
}

function handbackChat() {
    if (!currentConversationId) return;
    fetch(`{{ url('admin/ai-operations/conversations') }}/${currentConversationId}/handback`, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
    }).then(() => {
        document.getElementById('chatInput').style.display = 'none';
        document.getElementById('handbackBtn').style.display = 'none';
    });
}

function sendMessage(e) {
    e.preventDefault();
    const input = document.getElementById('messageInput');
    if (!input.value.trim() || !currentConversationId) return;
    
    fetch(`{{ url('admin/ai-operations/conversations') }}/${currentConversationId}/send`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({message: input.value})
    }).then(() => {
        input.value = '';
        loadConversation(currentConversationId);
    });
}
</script>
@endpush
@endsection
