<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Api\Controller;
use App\Models\Chats\Chat;
use App\Models\Chats\ChatMessage;
use App\Models\System\Log;
use App\Models\System\Paginator;
use App\Models\System\System;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function chats(Request $request)
    {

        $query = Chat::select('*');

        $query = ($request->created_by) ? $query->whereCreatedBy($request->created_by) : $query;
        $query = ($request->status) ? $query->whereStatus($request->status) : $query;
        $query = ($request->type_id) ? $query->whereChatTypeId($request->type_id) : $query;

        return Paginator::process('chats', $request, $query, [
            'default_order_by' => 'id',
            'default_order_direction' => 'DESC',
            'ignore_removed' => true,
            'ignore_text_search' => true,
        ]);
    }

    public function messages(Request $request, Chat $chat)
    {

        $query = ChatMessage::select('*')->whereChatId($chat->id);

        return Paginator::process('chats_messages', $request, $query, [
            'default_order_by' => 'id',
            'default_order_direction' => 'DESC',
            'ignore_removed' => true,
            'ignore_text_search' => true,
        ]);
    }


    public function put(Request $request, Chat $chat)
    {

        $new = false;

        if (!$chat) {
            $new = true;
        }

        Chat::saveChat(['chat' => $chat ? $chat : new Chat()]);
        Log::log(($new) ? 'chat\add' : 'chat\edit', $chat);

        return success();
    }

    public function message(Request $request, Chat $chat)
    {

        $payload = ['message' => $request->message, 'files' => $request->file('files')];

        if ($request->sent_by) {
            $payload['sent_by'] = $request->sent_by;
        }

        if ($request->sent_to) {
            $payload['sent_to'] = $request->sent_to;
        }

        ChatMessage::message($chat, $payload);

        Log::log('message\add', $chat);

        return success();
    }


}
