<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\MagicLinkMail;
use App\Models\AuditLog;
use App\Models\DirectChatSession;
use App\Models\User;
use App\Services\AuditService;
use App\Services\MagicLinkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private readonly MagicLinkService $magicLinkService,
        private readonly AuditService     $auditService,
    ) {}

    public function index(Request $request): View
    {
        $users = User::withCount('auditLogs')
            ->when($request->role,   fn ($q) => $q->where('role', $request->role))
            ->when($request->search, fn ($q) => $q->where('email', 'like', '%' . $request->search . '%')
                ->orWhere('name', 'like', '%' . $request->search . '%'))
            ->latest()
            ->paginate(25);

        $allAgents = User::where('role', 'agent')->where('is_active', true)->get(['id', 'name']);

        return view('admin.users', compact('users', 'allAgents'));
    }

    // ── CREATE ────────────────────────────────────────────────────────────────
    // public function store(Request $request): JsonResponse
    // {
    //     $validated = $this->validatePayload($request);

    //     $data = [
    //         'name'        => $validated['name'],
    //         'email'       => strtolower(trim($validated['email'])),
    //         'phone'       => $validated['phone'] ?? null,
    //         'role'        => $validated['role'],
    //         'password'   => $validated['password'],
    //         'is_active'   => (bool)($validated['is_active'] ?? true),
    //         'is_verified' => true,
    //     ];

    //     // Staff users must have a password
    //     if (in_array($validated['role'], ['admin', 'agent', 'auditor'])) {
    //         if (empty($validated['password'])) {
    //             return response()->json(['errors' => ['password' => ['Password is required for staff accounts.']]], 422);
    //         }
    //         $data['password'] = Hash::make($validated['password']);
    //     } else {
    //         $data['password'] = null; // customers use magic link only
    //     }

    //     $user = User::create($data);

    //     $this->auditService->log(
    //         action:      AuditLog::ACTION_LOGIN_SUCCESS, // reuse, or add ACTION_USER_CREATED
    //         request:     $request,
    //         user:        auth()->user(),
    //         description: "Created user: {$user->email} (role: {$user->role})"
    //     );

    //     return response()->json([
    //         'message' => 'User created successfully.',
    //         'user'    => $this->formatUser($user),
    //     ]);
    // }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validatePayload($request);
    
        $isStaff = in_array($validated['role'], ['admin','agent','auditor']);
    
        if ($isStaff && empty($validated['password'])) {
            return response()->json([
                'errors' => ['password' => ['Password is required for staff accounts.']]
            ], 422);
        }
    
        $user = User::create([
            'name'        => $validated['name'],
            'email'       => strtolower(trim($validated['email'])),
            'phone'       => $validated['phone'] ?? null,
            'role'        => $validated['role'],
            'password'    => $isStaff ? Hash::make($validated['password']) : null,
            'bitrix_agent_id' => $validated['role'] === 'agent'
                ? ($validated['bitrix_agent_id'] ?? null)
                : null,
            'is_active'   => (bool)($validated['is_active'] ?? true),
            'is_verified' => true,
        ]);
    
        return response()->json([
            'message' => 'User created successfully.',
            'user' => $this->formatUser($user)
        ]);
    }
    // ── READ (single) ─────────────────────────────────────────────────────────
    public function show(User $user): JsonResponse
    {
        return response()->json($this->formatUser($user));
    }

    // ── UPDATE ────────────────────────────────────────────────────────────────
    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $this->validatePayload($request, $user->id);
    
        $isStaff = in_array($validated['role'], ['admin','agent','auditor']);
    
        $data = [
            'name'        => $validated['name'],
            'email'       => strtolower(trim($validated['email'])),
            'phone'       => $validated['phone'] ?? null,
            'role'        => $validated['role'],
            'is_active'   => (bool)($validated['is_active'] ?? true),
            'bitrix_agent_id' => $validated['role'] === 'agent'
                ? ($validated['bitrix_agent_id'] ?? null)
                : null,
        ];
    
        // 🔥 FIX 1: Only update password if provided
        if (!empty($validated['password'])) {
            if ($isStaff) {
                $data['password'] = Hash::make($validated['password']);
            }
        }
    
        // 🔥 FIX 2: If changed to customer → remove password
        if ($validated['role'] === 'customer') {
            $data['password'] = null;
        }
    
        $user->update($data);
    
        return response()->json([
            'message' => 'User updated successfully.',
            'user' => $this->formatUser($user->fresh())
        ]);
    }

    // ── DELETE (soft) ─────────────────────────────────────────────────────────
    public function destroy(Request $request, User $user): JsonResponse
    {
        // Safety: don't delete self
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'You cannot delete your own account.'], 403);
        }

        $email = $user->email;
        $user->delete();

        $this->auditService->log(
            action:      AuditLog::ACTION_LOGIN_FAILED,
            request:     $request,
            user:        auth()->user(),
            status:      AuditLog::STATUS_WARNING,
            description: "Deleted user: {$email}"
        );

        return response()->json(['message' => 'User deleted successfully.']);
    }

    // ── Send Magic Link Email to a specific customer ──────────────────────────
    public function sendMagicLink(Request $request, User $user): JsonResponse
    {
        if ($user->role !== 'customer') {
            return response()->json(['message' => 'Magic links are only for customer accounts.'], 422);
        }

        if (!$user->is_active) {
            return response()->json(['message' => 'Cannot send link to an inactive account.'], 422);
        }

        try {
            $magicUrl = $this->magicLinkService->generate($user, $request);
            $this->magicLinkService->send($user, $magicUrl, $request);

            return response()->json([
                'message' => "Magic link sent to {$user->email}.",
                'email'   => $user->email,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to send magic link: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ── Send Customer-Agent Chat Link via Email ───────────────────────────────
    public function sendChatLink(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'agent_id' => ['required', 'exists:users,id'],
        ]);

        if ($user->role !== 'customer') {
            return response()->json(['message' => 'Chat links can only be sent to customer accounts.'], 422);
        }

        $agent = User::where('id', $request->agent_id)->where('role', 'agent')->firstOrFail();

        $url = route('direct-chat.enter', ['agentId' => $agent->id])
             . '?cid=' . urlencode($user->id)
             . '&name=' . urlencode($user->name);

        try {
            // Email the chat link using the existing MagicLinkMail template
            // (the "magic URL" here is the direct chat URL)
            Mail::raw(
                "Hello {$user->name},\n\n" .
                "You have been connected with your insurance agent {$agent->name}.\n\n" .
                "Click the link below to start chatting:\n{$url}\n\n" .
                "This link is unique to you — please do not share it.\n\n" .
                "Axis Communication Platform",
                function ($message) use ($user, $agent) {
                    $message->to($user->email)
                        ->subject("💬 Chat with your agent {$agent->name} — Axis Communication");
                }
            );

            $this->auditService->log(
                action:      AuditLog::ACTION_EMAIL_SENT,
                request:     $request,
                user:        auth()->user(),
                description: "Sent chat link to {$user->email} (agent: {$agent->email})"
            );

            return response()->json([
                'message' => "Chat link sent to {$user->email}.",
                'url'     => $url,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to send chat link: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ── Generate Chat Link (no email, just return URL) ────────────────────────
    public function generateChatLink(Request $request): JsonResponse
    {
        $request->validate([
            'customer_id' => ['required', 'exists:users,id'],
            'agent_id'    => ['required', 'exists:users,id'],
        ]);

        $customer = User::findOrFail($request->customer_id);
        $agent    = User::where('id', $request->agent_id)->where('role', 'agent')->firstOrFail();

        $url = route('direct-chat.enter', ['agentId' => $agent->id])
             . '?cid=' . urlencode($customer->id)
             . '&name=' . urlencode($customer->name);

        return response()->json([
            'url'           => $url,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'agent_name'    => $agent->name,
        ]);
    }

    // ── Agent's Sessions List ─────────────────────────────────────────────────
    public function agentSessions(User $user): JsonResponse
    {
        abort_unless($user->role === 'agent', 422, 'User is not an agent.');

        $sessions = DirectChatSession::where('agent_id', $user->id)
            ->with(['customer'])
            ->withCount('messages')
            ->orderByDesc('last_activity_at')
            ->limit(50)
            ->get()
            ->map(fn ($s) => [
                'id'              => $s->id,
                'customer_name'   => $s->customer->name,
                'customer_email'  => $s->customer->email,
                'customer_ref'    => $s->customer_ref,
                'status'          => $s->status,
                'messages_count'  => $s->messages_count,
                'last_activity'   => $s->last_activity_at?->diffForHumans() ?? $s->created_at->diffForHumans(),
                'view_url'        => route('direct-chat.agent.session', $s->id),
            ]);

        return response()->json($sessions);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    private function validatePayload(Request $request, ?int $userId = null): array
    {
        $rules = [
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)->whereNull('deleted_at')],
            'phone'     => ['nullable', 'string', 'max:30'],
            'role'      => ['required', Rule::in(['admin', 'agent', 'auditor', 'customer'])],
            'is_active' => ['nullable', 'boolean'],
            'password'  => ['nullable', 'string', 'min:8'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            abort(response()->json(['errors' => $validator->errors()], 422));
        }
        return $validator->validated();
    }

    private function formatUser(User $user): array
    {
        return [
            'id'            => $user->id,
            'name'          => $user->name,
            'email'         => $user->email,
            'phone'         => $user->phone,
            'role'          => $user->role,
            'is_active'     => $user->is_active,
            'has_password'  => !empty($user->password),
            'last_login_at' => $user->last_login_at?->format('d M Y H:i'),
            'created_at'    => $user->created_at->format('d M Y'),
        ];
    }
}