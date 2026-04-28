<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DirectChatSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminChatViewController extends Controller
{
    public function agentSessions(User $agent): View
    {
        abort_unless(in_array(auth()->user()->role, ['admin']), 403);

        $sessions = DirectChatSession::where('agent_id', $agent->id)
            ->with(['customer'])
            ->withCount(['messages'])
            ->withCount(['messages as unread_count' => function ($q) use ($agent) {
                $q->where('sender_id', '!=', $agent->id)->where('is_read', false);
            }])
            ->orderByDesc('last_activity_at')
            ->paginate(30);

        foreach ($sessions as $s) {
            $s->last_msg = $s->messages()->latest('id')->first();
        }

        return view('admin.agent-sessions', compact('sessions', 'agent'));
    }

    public function viewSession(DirectChatSession $session): View
    {
        abort_unless(in_array(auth()->user()->role, ['admin']), 403);

        $session->load(['customer', 'agent']);
        $agent = $session->agent;

        // Reuse the same unified history logic from DirectChatController
        $directChatController = app(\App\Http\Controllers\DirectChatController::class);
        $messages = $this->getAllMessages($session);

        $allSessions = DirectChatSession::where('agent_id', $session->agent_id)
            ->with(['customer', 'lastMsg'])
            ->orderByDesc('last_activity_at')
            ->get();

        $oldestLoadedId = !empty($messages) ? $messages[0]['id'] : null;
        $hasMoreHistory = false;
        $totalCount     = count($messages);
        $customerRef    = $session->customer_ref;
        $isAdminView    = true;   // flag to hide composer in view

        return view('chat.agent-session', compact(
            'session', 'agent', 'messages', 'customerRef',
            'oldestLoadedId', 'hasMoreHistory', 'totalCount',
            'allSessions', 'isAdminView'
        ));
    }

    protected function getAllMessages(DirectChatSession $session): array
    {
        $allSessionIds = DirectChatSession::where('customer_id', $session->customer_id)
            ->pluck('id');

        $msgs = \App\Models\DirectMessage::whereIn('session_id', $allSessionIds)
            ->with(['sender', 'attachments'])
            ->orderBy('id')
            ->get();

        return $msgs->map(function ($m) use ($session) {
            return [
                'id'          => $m->id,
                'body'        => $m->body,
                'type'        => $m->type,
                'mine'        => $m->sender_id === $session->agent_id,
                'sender_name' => $m->sender->name ?? 'System',
                'sender_role' => $m->sender->role ?? 'system',
                'initial'     => strtoupper(substr($m->sender->name ?? 'S', 0, 1)),
                'time'        => $m->created_at->format('g:i A'),
                'date'        => $m->created_at->format('Y-m-d'),
                'is_read'     => (bool) $m->is_read,
                'attachments' => $m->attachments->map(fn ($a) => [
                    'id'       => $a->id,
                    'name'     => $a->original_name,
                    'size'     => $a->size,
                    'mime'     => $a->mime_type,
                    'url'      => route('direct-chat.attachment.download', $a->id),
                    'is_image' => str_starts_with($a->mime_type, 'image/'),
                ])->all(),
            ];
        })->values()->all();
    }
}