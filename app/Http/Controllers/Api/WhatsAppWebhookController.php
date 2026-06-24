<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
class WhatsAppWebhookController extends Controller {
    public function webhook() { return response()->json(['status'=>'ok']); }
    public function verify() { return response()->json(['status'=>'ok']); }
}
