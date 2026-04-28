<?php

namespace App\Http\Controllers;

use App\Models\DirectChatSession;
use App\Models\DirectMessage;
use App\Models\DirectMessageAttachment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DirectChatController extends Controller
{
    /** How many messages to load on the initial chat screen render. */
    protected const INITIAL_PAGE_SIZE = 30;

    /** How many older messages to fetch per infinite-scroll page. */
    protected const HISTORY_PAGE_SIZE = 30;

    // ──────────────────────────────────────────────────────────────────────────
    //  ENTRY:  /c/{agentId}?cid=...&token=...
    // ──────────────────────────────────────────────────────────────────────────
    public function enter(Request $request, string $agentId): View|\Illuminate\Http\RedirectResponse
    {
         $agentId = (int) $agentId;
        $agent = User::where('id', $agentId)
            ->where('role', 'agent')
            ->where('is_active', true)
            ->firstOrFail();

        $customerRef  = $request->query('cid', 'guest');
        $sessionToken = $request->query('token');

        // Resume by token
        if ($sessionToken) {
            $session = DirectChatSession::where('session_token', $sessionToken)
                ->where('agent_id', $agentId)
                ->where('status', 'active')
                ->with(['customer', 'agent'])
                ->first();

            if ($session) {
                return $this->renderChat($session, $agent, $customerRef);
            }
        }

        // Fallback: find customer by `cid` (numeric user id) or create guest
        $customer = $this->resolveCustomer($customerRef, $request);

        // Reuse active session if it already exists
        $existing = DirectChatSession::where('customer_id', $customer->id)
            ->where('agent_id', $agentId)
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return redirect()->route('direct-chat.enter', [
                'agentId' => $agentId,
                'cid'     => $customerRef,
                'token'   => $existing->session_token,
                'allSessions' => DirectChatSession::where('agent_id', auth()->id())
    ->with(['customer', 'lastMsg'])
    ->orderByDesc('last_activity_at')
    ->get(),
            ]);
        }

        // Create new session
        $newToken = Str::random(48);
        $session  = DirectChatSession::create([
            'session_token'    => $newToken,
            'customer_id'      => $customer->id,
            'agent_id'         => $agentId,
            'customer_ref'     => $customerRef,
            'status'           => 'active',
            'last_activity_at' => now(),
            'expires_at'       => now()->addDays(30),
        ]);

        DirectMessage::create([
            'session_id' => $session->id,
            'sender_id'  => $agentId,
            'body'       => "Hello! I'm {$agent->name}, your dedicated agent. How can I help you today?",
            'type'       => 'system',
        ]);

        return redirect()->route('direct-chat.enter', [
            'agentId' => $agentId,
            'cid'     => $customerRef,
            'token'   => $newToken,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  RENDER CHAT — last N messages only. Older loaded via /history endpoint.
    // ──────────────────────────────────────────────────────────────────────────
    
    protected function renderChat(DirectChatSession $session, User $agent, string $customerRef): View
    {
        // Mark agent messages as read (customer is viewing)
        $session->messages()
            ->where('sender_id', $session->agent_id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    
        // ── Load ALL sessions for this customer across ALL agents ──
        $allSessionIds = DirectChatSession::where('customer_id', $session->customer_id)
            ->orderBy('created_at')
            ->pluck('id');
    
        $totalCount = \App\Models\DirectMessage::whereIn('session_id', $allSessionIds)->count();
    
        // Last N messages across all sessions, oldest-first for rendering
        $latest = \App\Models\DirectMessage::whereIn('session_id', $allSessionIds)
            ->with(['sender', 'attachments'])
            ->latest('id')
            ->limit(self::INITIAL_PAGE_SIZE)
            ->get()
            ->sortBy('id')
            ->values();
    
        $messages = $this->formatMessages($latest, $session->customer_id);
    
        $oldestLoadedId = $latest->isNotEmpty() ? $latest->first()->id : null;
        $hasMoreHistory = $totalCount > $latest->count();
    
        // Pass all session ids so the poll endpoint knows what to watch
        $watchSessionIds = $allSessionIds->toArray();
    
        return view('chat.direct', compact(
            'session', 'agent', 'messages', 'customerRef',
            'oldestLoadedId', 'hasMoreHistory', 'totalCount', 'watchSessionIds'
        ));
    }


    //OLD
    // protected function renderChat(DirectChatSession $session, User $agent, string $customerRef): View
    // {
    //     // Mark agent messages as read (customer is viewing)
    //     $session->messages()
    //         ->where('sender_id', $session->agent_id)
    //         ->where('is_read', false)
    //         ->update(['is_read' => true, 'read_at' => now()]);

    //     // Total count — so the client knows whether to offer "load older"
    //     $totalCount = $session->messages()->count();

    //     // Last N messages, oldest-first for rendering
    //     $latest = $session->messages()
    //         ->with(['sender', 'attachments'])
    //         ->latest('id')
    //         ->limit(self::INITIAL_PAGE_SIZE)
    //         ->get()
    //         ->sortBy('id')
    //         ->values();

    //     $messages = $this->formatMessages($latest, $session->customer_id);

    //     // Oldest id currently on screen → used as the cursor for loading older pages
    //     $oldestLoadedId = $latest->isNotEmpty() ? $latest->first()->id : null;
    //     $hasMoreHistory = $totalCount > $latest->count();

    //     return view('chat.direct', compact(
    //         'session', 'agent', 'messages', 'customerRef',
    //         'oldestLoadedId', 'hasMoreHistory', 'totalCount'
    //     ));
    // }

    // ──────────────────────────────────────────────────────────────────────────
    //  /direct-chat/history  — infinite scroll upward (load older messages)
    //  Returns messages OLDER than `before_id`, ordered oldest-first.
    // ──────────────────────────────────────────────────────────────────────────
    //NEW
public function history(Request $request): JsonResponse
{
    $request->validate([
        'session_token' => ['required', 'string'],
        'before_id'     => ['required', 'integer', 'min:1'],
    ]);

    $session = DirectChatSession::where('session_token', $request->session_token)
        ->firstOrFail();

    // All sessions for this customer across all agents
    $allSessionIds = DirectChatSession::where('customer_id', $session->customer_id)
        ->pluck('id');

    $older = \App\Models\DirectMessage::whereIn('session_id', $allSessionIds)
        ->with(['sender', 'attachments'])
        ->where('id', '<', (int) $request->before_id)
        ->latest('id')
        ->limit(self::HISTORY_PAGE_SIZE)
        ->get()
        ->sortBy('id')
        ->values();

    $formatted = $this->formatMessages($older, $this->viewerId($session));

    return response()->json([
        'messages'         => $formatted,
        'has_more'         => $older->count() === self::HISTORY_PAGE_SIZE,
        'oldest_loaded_id' => $older->isNotEmpty() ? $older->first()->id : null,
    ]);
}

    // ──────────────────────────────────────────────────────────────────────────
    //  SEND — unchanged behaviour
    // ──────────────────────────────────────────────────────────────────────────
    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'session_token' => ['required', 'string'],
            'body'          => ['nullable', 'string', 'max:4000'],
            'attachments.*' => ['nullable', 'file', 'max:10240',
                'mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt,zip'],
        ]);

        $session = DirectChatSession::where('session_token', $request->session_token)
            ->where('status', 'active')
            ->firstOrFail();

        $senderId = auth()->check() ? auth()->id() : $session->customer_id;

        abort_unless(
            in_array($senderId, [$session->customer_id, $session->agent_id]),
            403, 'Not a participant of this session.'
        );

        abort_if(
            empty($request->body) && ! $request->hasFile('attachments'),
            422, 'Message body or attachment required.'
        );

        $type = $request->hasFile('attachments') ? 'attachment' : 'text';

        $message = DirectMessage::create([
            'session_id' => $session->id,
            'sender_id'  => $senderId,
            'body'       => $request->body ?? '',
            'type'       => $type,
            'is_read'    => false,
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $stored = $file->store("chat/{$session->id}", 'local');
                DirectMessageAttachment::create([
                    'message_id'    => $message->id,
                    'original_name' => $file->getClientOriginalName(),
                    'stored_name'   => basename($stored),
                    'mime_type'     => $file->getMimeType(),
                    'size'          => $file->getSize(),
                    'path'          => $stored,
                ]);
            }
        }

        $session->update(['last_activity_at' => now()]);
        $message->load(['sender', 'attachments']);
        if ($session->bitrix_deal_id) {
            $attachmentModels = $message->attachments ?? collect();
            $attachmentArray  = $attachmentModels->map(fn($a) => [
                'original_name' => $a->original_name,
            ])->all();
        
            $comment = BitrixService::formatMessageComment(
                senderName:  $message->sender->name ?? 'Unknown',
                senderRole:  $message->sender->role ?? 'customer',
                body:        $message->body,
                attachments: $attachmentArray,
            );
        
            // Fire async (queue) to not block response — or sync if no queue
            dispatch(function () use ($session, $comment) {
                (new BitrixService())->addDealComment($session->bitrix_deal_id, $comment);
            })->afterResponse();
        }
        return response()->json(
            $this->formatMessages(collect([$message]), $this->viewerId($session))[0]
        );
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  POLL — fetch messages NEWER than `after_id`
    // ──────────────────────────────────────────────────────────────────────────
   public function poll(Request $request): JsonResponse
    {
        $request->validate([
            'session_token' => ['required', 'string'],
            'after_id'      => ['nullable', 'integer', 'min:0'],
        ]);
    
        $session = DirectChatSession::where('session_token', $request->session_token)
            ->firstOrFail();
    
        $afterId = (int) ($request->after_id ?? 0);
    
        // All sessions for this customer
        $allSessionIds = DirectChatSession::where('customer_id', $session->customer_id)
            ->pluck('id');
    
        $new = \App\Models\DirectMessage::whereIn('session_id', $allSessionIds)
            ->with(['sender', 'attachments'])
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->get();
    
        $viewerId = auth()->check() ? auth()->id() : $session->customer_id;
    
        // Auto-mark messages from agents as read (customer is viewing)
        \App\Models\DirectMessage::whereIn('session_id', $allSessionIds)
            ->where('sender_id', '!=', $session->customer_id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    
        return response()->json([
            'messages' => $this->formatMessages($new, $viewerId),
        ]);
    }
    // ──────────────────────────────────────────────────────────────────────────
    //  Attachment download — unchanged
    // ──────────────────────────────────────────────────────────────────────────
    public function downloadAttachment(DirectMessageAttachment $attachment)
    {
        abort_unless(Storage::disk('local')->exists($attachment->path), 404);
        return Storage::disk('local')->download($attachment->path, $attachment->original_name);
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  AGENT INBOX — list of the agent's conversations
    //  Route: GET /agent/inbox  (middleware: auth, role:admin,agent)
    // ──────────────────────────────────────────────────────────────────────────
    public function agentInbox(Request $request): View
    {
        $agentId = auth()->id();
    
        $sessions = DirectChatSession::where('agent_id', $agentId)
            ->with(['customer'])
            ->withCount(['messages'])
            ->withCount(['messages as unread_count' => function ($q) use ($agentId) {
                $q->where('sender_id', '!=', $agentId)->where('is_read', false);
            }])
            ->orderByDesc('last_activity_at')  // ← already correct
            ->paginate(20);
    
        foreach ($sessions as $s) {
            $s->last_msg = $s->messages()->latest('id')->first();
        }
    
        return view('chat.agent-inbox', compact('sessions'));
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  AGENT SESSION VIEW — opens one conversation
    //  Route: GET /agent/session/{session}
    //  Uses the same "latest N + infinite scroll up" pattern as the customer view.
    // ──────────────────────────────────────────────────────────────────────────
//     public function agentSession(DirectChatSession $session): View
//     {
//         abort_unless($session->agent_id === auth()->id(), 403);

//         $session->load(['customer', 'agent']);
//         $agent = $session->agent;

//         // Mark customer messages as read (agent is now viewing)
//         $session->messages()
//             ->where('sender_id', $session->customer_id)
//             ->where('is_read', false)
//             ->update(['is_read' => true, 'read_at' => now()]);

//         $totalCount = $session->messages()->count();

//         $latest = $session->messages()
//             ->with(['sender', 'attachments'])
//             ->latest('id')
//             ->limit(self::INITIAL_PAGE_SIZE)
//             ->get()
//             ->sortBy('id')
//             ->values();

//         // Viewer is the agent — so "mine" = messages sent by this agent
//         $messages = $this->formatMessages($latest, $session->agent_id);

//         $oldestLoadedId = $latest->isNotEmpty() ? $latest->first()->id : null;
//         $hasMoreHistory = $totalCount > $latest->count();
//         $customerRef    = $session->customer_ref;

// $allSessions = DirectChatSession::where('agent_id', auth()->id())
//     ->with(['customer', 'lastMsg'])
//     ->orderByDesc('last_activity_at')
//     ->get();
//         return view('chat.agent-session', compact(
//             'session', 'agent', 'messages', 'customerRef',
//             'oldestLoadedId', 'hasMoreHistory', 'totalCount','allSessions'
//         ));
//     }

    public function agentSession(DirectChatSession $session): View
    {
        abort_unless($session->agent_id === auth()->id(), 403);
    
        $session->load(['customer', 'agent']);
        $agent = $session->agent;
    
        // Mark customer messages as read (agent is now viewing)
        $session->messages()
            ->where('sender_id', $session->customer_id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    
        // ── UNIFIED HISTORY across all agents for this customer ──
        $messages = $this->getAllCustomerMessages($session->customer_id, $session->agent_id);
    
        // For infinite scroll cursor — use the last N messages approach
        $totalCount     = count($messages);
        $hasMoreHistory = false; // We're loading all; can paginate later if needed
        $oldestLoadedId = !empty($messages) ? $messages[0]['id'] : null;
    
        $customerRef = $session->customer_ref;
    
        $allSessions = DirectChatSession::where('agent_id', auth()->id())
            ->with(['customer', 'lastMsg'])
            ->orderByDesc('last_activity_at')
            ->get();
    
        return view('chat.agent-session', compact(
            'session', 'agent', 'messages', 'customerRef',
            'oldestLoadedId', 'hasMoreHistory', 'totalCount', 'allSessions'
        ));
    }

    // ──────────────────────────────────────────────────────────────────────────
    //  Helpers
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Determine the viewer's user id for a given session. If an authenticated
     * staff user is one of the participants (i.e. the agent), they are the viewer;
     * otherwise the customer is the viewer (public chat screen).
     */
    protected function viewerId(DirectChatSession $session): int
    {
        if (auth()->check()
            && in_array(auth()->id(), [$session->customer_id, $session->agent_id], true)) {
            return auth()->id();
        }
        return $session->customer_id;
    }

    protected function resolveCustomer(string $customerRef, Request $request): User
    {
        // If cid is numeric and matches a user, use them
        if (ctype_digit($customerRef)) {
            $user = User::find((int) $customerRef);
            if ($user) return $user;
        }

        // Otherwise create a guest user
        return User::create([
            'name'        => $request->query('name', 'Guest ' . substr($customerRef, 0, 6)),
            'email'       => 'guest_' . Str::random(10) . '@chat.local',
            'role'        => 'customer',
            'is_active'   => true,
            'is_verified' => false,
        ]);
    }

    /**
     * Format messages for rendering. `$viewerId` is the id of the user looking
     * at the screen — messages where sender_id === viewerId are "mine" (right side).
     * Customer chat screen passes customer_id; agent inbox passes agent_id.
     */
    // protected function formatMessages($messages, int $viewerId): array
    // {
    //     return $messages->map(function ($m) use ($viewerId) {
    //         return [
    //             'id'          => $m->id,
    //             'body'        => $m->body,
    //             'type'        => $m->type,
    //             'mine'        => $m->sender_id === $viewerId,
    //             'sender_name' => $m->sender->name ?? 'System',
    //             'time'        => $m->created_at->format('g:i A'),
    //             'date'        => $m->created_at->format('Y-m-d'),
    //             'is_read'     => (bool) $m->is_read,
    //             'attachments' => $m->attachments->map(fn ($a) => $this->formatAttachment($a))->all(),
    //         ];
    //     })->values()->all();
    // }
    protected function formatMessages($messages, int $viewerId): array
    {
        return $messages->map(function ($m) use ($viewerId) {
            return [
                'id'          => $m->id,
                'body'        => $m->body,
                'type'        => $m->type,
                'mine'        => $m->sender_id === $viewerId,
                'sender_name' => $m->sender->name ?? 'System',
                'sender_role' => $m->sender->role ?? 'system',
                'initial'     => strtoupper(substr($m->sender->name ?? 'S', 0, 1)),
                'time'        => $m->created_at->format('g:i A'),
                'date'        => $m->created_at->format('Y-m-d'),
                'is_read'     => (bool) $m->is_read,
                'attachments' => $m->attachments->map(fn ($a) => $this->formatAttachment($a))->all(),
            ];
        })->values()->all();
    }

    protected function getAllCustomerMessages(int $customerId, int $viewerId): array
    {
        // Load ALL sessions for this customer (across all agents), ordered oldest-first
        $sessions = \App\Models\DirectChatSession::where('customer_id', $customerId)
            ->with(['agent'])
            ->orderBy('created_at')
            ->get();
    
        $allMessages = collect();
    
        foreach ($sessions as $session) {
            $msgs = $session->messages()
                ->with(['sender', 'attachments'])
                ->orderBy('id')
                ->get()
                ->map(function ($m) use ($viewerId, $session) {
                    return [
                        'id'           => $m->id,
                        'body'         => $m->body,
                        'type'         => $m->type,
                        'mine'         => $m->sender_id === $viewerId,
                        'sender_name'  => $m->sender->name ?? 'System',
                        'sender_role'  => $m->sender->role ?? 'system',
                        'initial'      => strtoupper(substr($m->sender->name ?? 'S', 0, 1)),
                        'time'         => $m->created_at->format('g:i A'),
                        'date'         => $m->created_at->format('Y-m-d'),
                        'is_read'      => (bool) $m->is_read,
                        'attachments'  => $m->attachments->map(fn ($a) => $this->formatAttachment($a))->all(),
                        'session_id'   => $session->id,
                        'agent_name'   => $session->agent->name ?? 'Unknown Agent',
                        // Flag: is this message from a *different* session than current?
                        'is_history'   => false, // will set below
                    ];
                });
            $allMessages = $allMessages->concat($msgs);
        }
    
        return $allMessages->sortBy('id')->values()->all();
    }

    protected function formatAttachment(DirectMessageAttachment $a): array
    {
        return [
            'id'        => $a->id,
            'name'      => $a->original_name,
            'size'      => $a->size_human ?? $a->size,
            'mime'      => $a->mime_type,
            'url'       => route('direct-chat.attachment.download', $a->id),
            'is_image'  => str_starts_with($a->mime_type, 'image/'),
        ];
    }
}