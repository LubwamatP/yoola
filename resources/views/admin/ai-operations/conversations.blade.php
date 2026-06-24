@extends('layouts.admin.app')
@section('title', 'AI Chat - Yoola Alpha')
@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0">AI Chat — Yoola Alpha</h2>
        <p class="text-muted">Ask me anything about SEO, content, strategy, or your store.</p>
    </div>
    <div class="card border-0 shadow-sm" style="max-width:800px">
        <div class="card-body p-0">
            <div id="chat-messages" style="height:420px;overflow-y:auto;padding:20px;background:#f8f9fa">
                <div class="d-flex gap-2 mb-3">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold" style="width:36px;height:36px;min-width:36px">Y</div>
                    <div class="bg-white rounded p-3 shadow-sm" style="max-width:80%">
                        Hello! I'm Yoola Alpha, your AI marketing brain. Ask me about SEO, social media content, product descriptions, competitor strategy, or anything to grow Yoola.ug. Let's dominate Uganda's electronics market!
                    </div>
                </div>
            </div>
            <div class="border-top p-3 d-flex gap-2">
                <input type="text" id="chat-input" class="form-control" placeholder="Ask Yoola Alpha..." onkeypress="if(event.key==='Enter') sendChat()">
                <button class="btn btn-primary px-4" onclick="sendChat()">Send</button>
            </div>
        </div>
    </div>
</div>
<script>
function sendChat() {
    const input = document.getElementById('chat-input');
    const msg = input.value.trim();
    if (!msg) return;
    const box = document.getElementById('chat-messages');
    box.innerHTML += `<div class="d-flex gap-2 mb-3 justify-content-end"><div class="bg-primary text-white rounded p-3" style="max-width:80%">${msg}</div><div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white fw-bold" style="width:36px;height:36px;min-width:36px">L</div></div>`;
    input.value = '';
    box.scrollTop = box.scrollHeight;
    box.innerHTML += `<div id="typing" class="d-flex gap-2 mb-3"><div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold" style="width:36px;height:36px;min-width:36px">Y</div><div class="bg-white rounded p-3 shadow-sm text-muted">Thinking...</div></div>`;
    box.scrollTop = box.scrollHeight;
    fetch('{{ route("admin.ai-operations.chat") }}', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body: JSON.stringify({message: msg})
    }).then(r=>r.json()).then(data=>{
        document.getElementById('typing').remove();
        box.innerHTML += `<div class="d-flex gap-2 mb-3"><div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold" style="width:36px;height:36px;min-width:36px">Y</div><div class="bg-white rounded p-3 shadow-sm" style="max-width:80%;white-space:pre-wrap">${data.response}</div></div>`;
        box.scrollTop = box.scrollHeight;
    }).catch(()=>{ document.getElementById('typing').remove(); });
}
</script>
@endsection
