<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DirectChatSession;
use App\Models\DirectMessage;
use App\Models\User;
use App\Models\ShortUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;



class CustomerChatApiController extends Controller
{
public function generateLink(Request $request): JsonResponse
{
    // ── 1. Validate ────────────────────────────────────────────────────────
    $data = $request->validate([
        'mobile'           => ['required', 'string', 'regex:/^\+?[0-9\s\-()]{7,20}$/'],
        'email'            => ['nullable', 'email', 'max:190'],
        'name'             => ['nullable', 'string', 'max:120'],
        'bitrix_agent_id'  => ['nullable'],
        'bitrix_contact_id'   => ['nullable'],
        'bitrix_deal_id'   => ['nullable'],
        'bitrix_deal_link' => ['nullable', 'string', 'max:520'],
    ], [
        'mobile.regex' => 'Mobile number format is invalid.',
    ]);

    $mobile         = preg_replace('/[\s\-()]/', '', $data['mobile']);
    $bitrixAgentId  = isset($data['bitrix_agent_id']) ? (int) $data['bitrix_agent_id'] : null;
    $bitrixDealId   = isset($data['bitrix_deal_id'])  ? (int) $data['bitrix_deal_id']  : null;
    $bitrixDealLink = $data['bitrix_deal_link'] ?? null;

    // ── 2. Resolve agent ───────────────────────────────────────────────────
    $agent = null;

    if ($bitrixAgentId) {
        $agent = User::where('bitrix_agent_id', $bitrixAgentId)
            ->where('role', 'agent')
            ->where('is_active', true)
            ->first();

        if (!$agent) {
            $agent = User::where('id', $bitrixAgentId)
                ->where('role', 'agent')
                ->where('is_active', true)
                ->first();
        }
    }

    if (!$agent) {
        $fallbackId = config('chat.default_agent_id');
        if ($fallbackId) {
            $agent = User::where('id', $fallbackId)
                ->where('role', 'agent')
                ->where('is_active', true)
                ->first();
        }
    }

    if (!$agent) {
        $agent = User::where('role', 'agent')
            ->where('is_active', true)
            ->first();
    }

    if (!$agent) {
        return response()->json([
            'success' => false,
            'message' => 'No active agent available to handle this chat.',
        ], 422);
    }

    // ── 3. Find or create customer ─────────────────────────────────────────
    try {
        [$customer, $isNew] = DB::transaction(function () use ($mobile, $data, $bitrixAgentId) {

            $customer = User::where('mobile', $mobile)->first();

            if (!$customer && !empty($data['email'])) {
                $customer = User::where('email', $data['email'])->first();
                if ($customer && empty($customer->mobile)) {
                    $customer->mobile = $mobile;
                    $customer->save();
                }
            }

            if ($customer) {
                $dirty = false;
                if (!empty($data['name'])  && empty($customer->name))  { $customer->name  = $data['name'];  $dirty = true; }
                if (!empty($data['email']) && empty($customer->email)) { $customer->email = $data['email']; $dirty = true; }
                if (!empty($data['bitrix_contact_id']) && empty($customer->bitrix_contact_id)) { $customer->bitrix_contact_id = $data['bitrix_contact_id'];$dirty = true;}
                if ($dirty) $customer->save();

                return [$customer, false];
            }

            $customer = User::create([
                'name'            => $data['name'] ?? 'Customer ' . substr($mobile, -4),
                'mobile'          => $mobile,
                'email'           => $data['email'] ?? null,
                'bitrix_agent_id' => $bitrixAgentId,
                'bitrix_contact_id' => $data['bitrix_contact_id'] ?? null,
                'role'            => 'customer',
                'is_active'       => true,
                'is_verified'     => false,
            ]);

            return [$customer, true];
        });
    } catch (\Illuminate\Database\QueryException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Could not create customer — email may already be in use for a different mobile number.',
            'error'   => config('app.debug') ? $e->getMessage() : null,
        ], 409);
    }

    // ── 4. Find or create session ──────────────────────────────────────────
    $session        = DirectChatSession::where('customer_id', $customer->id)
        ->where('agent_id', $agent->id)
        ->where('status', 'active')
        ->first();

    $sessionUpdated = false;

    if ($session) {
        $needsUpdate = false;

        if ($bitrixDealId && $session->bitrix_deal_id !== $bitrixDealId) {
            $session->bitrix_deal_id  = $bitrixDealId;
            $needsUpdate = true;
        }

        if ($bitrixDealLink && $session->bitrix_deal_link !== $bitrixDealLink) {
            $session->bitrix_deal_link = $bitrixDealLink;
            $needsUpdate = true;
        }

        if ($needsUpdate) {
            $session->save();
            $sessionUpdated = true;
        }

    } else {
        $session = DirectChatSession::create([
            'session_token'    => Str::random(48),
            'customer_id'      => $customer->id,
            'agent_id'         => $agent->id,
            'bitrix_deal_id'   => $bitrixDealId,
            'bitrix_deal_link' => $bitrixDealLink,
            'customer_ref'     => (string) $customer->id,
            'status'           => 'active',
            'last_activity_at' => now(),
            'expires_at'       => now()->addDays(30),
        ]);

        DirectMessage::create([
            'session_id' => $session->id,
            'sender_id'  => $agent->id,
            'body'       => "Hello {$customer->name}! I'm {$agent->name}, your dedicated agent. How can I help you today?",
            'type'       => 'system',
        ]);
    }

    // ── 5. Build URLs without route() helper ──────────────────────────────
    $baseUrl     = rtrim(config('app.url'), '/');
    $queryString = '?cid='   . urlencode((string) $customer->id)
                 . '&token=' . urlencode($session->session_token);

    $chatUrl     = $baseUrl . '/c/' . $agent->id . $queryString;
    $dynamicPart = $agent->id . $queryString;

    // ── 6. Short URL ───────────────────────────────────────────────────────
    $short = ShortUrl::where('session_id', $session->id)->first();

    if (!$short) {
        $short = ShortUrl::create([
            'code'       => $this->uniqueShortCode(),
            'target_url' => $chatUrl,
            'session_id' => $session->id,
            'expires_at' => $session->expires_at,
        ]);
    } elseif ($sessionUpdated && $short->target_url !== $chatUrl) {
        $short->update(['target_url' => $chatUrl]);
    }

    $shortUrl = $baseUrl . '/short/' . $short->code;
    $agentSessionUrl = $baseUrl . '/agent/session/' . $session->id;
    // ── 7. Return response ─────────────────────────────────────────────────
    return response()->json([
        'success'         => true,
        'is_new'          => $isNew,
        'session_updated' => $sessionUpdated,
        'customer'        => [
            'id'     => $customer->id,
            'name'   => $customer->name,
            'mobile' => $customer->mobile,
            'email'  => $customer->email,
        ],
        'agent' => [
            'id'              => $agent->id,
            'name'            => $agent->name,
            'bitrix_agent_id' => $agent->bitrix_agent_id,
            'agent_session_url' => $agentSessionUrl,
        ],
        'session' => [
            'token'            => $session->session_token,
            'bitrix_deal_id'   => $session->bitrix_deal_id,
            'bitrix_deal_link' => $session->bitrix_deal_link,
            'expires_at'       => $session->expires_at?->toIso8601String(),
        ],
        'chat_url'     => $chatUrl,
        'dynamic_part' => $dynamicPart,
        'short_url'    => $shortUrl,
    ], $isNew ? 201 : 200);
}

private function uniqueShortCode(int $length = 7): string
{
    do {
        $code = Str::random($length);
    } while (ShortUrl::where('code', $code)->exists());

    return $code;
}
}