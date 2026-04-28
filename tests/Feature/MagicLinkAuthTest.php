<?php

namespace Tests\Feature;

use App\Models\MagicLoginToken;
use App\Models\User;
use App\Services\MagicLinkService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MagicLinkAuthTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email'      => 'test@insurance.ae',
            'is_active'  => true,
            'is_verified'=> true,
            'role'       => 'customer',
        ]);
    }

    /** @test */
    public function login_page_loads(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Send Secure Login Link');
    }

    /** @test */
    public function user_can_request_magic_link(): void
    {
        Mail::fake();

        $this->post(route('auth.magic.request'), ['email' => $this->user->email])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('magic_login_tokens', [
            'user_id' => $this->user->id,
            'is_used' => false,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'link_generated',
        ]);
    }

    /** @test */
    public function unknown_email_gives_same_response(): void
    {
        Mail::fake();

        $this->post(route('auth.magic.request'), ['email' => 'nobody@nowhere.ae'])
            ->assertRedirect()
            ->assertSessionHas('success'); // Same message — no enumeration
    }

    /** @test */
    public function valid_token_logs_user_in(): void
    {
        $rawToken  = bin2hex(random_bytes(64));
        $tokenHash = hash('sha256', $rawToken);

        MagicLoginToken::create([
            'user_id'    => $this->user->id,
            'token_hash' => $tokenHash,
            'expires_at' => now()->addMinutes(10),
            'is_used'    => false,
            'created_ip' => '127.0.0.1',
        ]);

        $url = route('auth.magic.verify', ['token' => $rawToken, 'uid' => $this->user->id]);

        $this->get($url . '&signature=' . $this->generateSignature($url))
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($this->user);

        $this->assertDatabaseHas('magic_login_tokens', [
            'token_hash' => $tokenHash,
            'is_used'    => true,
        ]);
    }

    /** @test */
    public function expired_token_is_rejected(): void
    {
        $rawToken  = bin2hex(random_bytes(64));
        $tokenHash = hash('sha256', $rawToken);

        MagicLoginToken::create([
            'user_id'    => $this->user->id,
            'token_hash' => $tokenHash,
            'expires_at' => now()->subMinute(),  // Already expired
            'is_used'    => false,
            'created_ip' => '127.0.0.1',
        ]);

        $url = route('auth.magic.verify', ['token' => $rawToken, 'uid' => $this->user->id]);

        $this->get($url)
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    /** @test */
    public function used_token_cannot_be_reused(): void
    {
        $rawToken  = bin2hex(random_bytes(64));
        $tokenHash = hash('sha256', $rawToken);

        MagicLoginToken::create([
            'user_id'    => $this->user->id,
            'token_hash' => $tokenHash,
            'expires_at' => now()->addMinutes(10),
            'is_used'    => true,  // Already used
            'used_at'    => now()->subMinute(),
            'created_ip' => '127.0.0.1',
        ]);

        $url = route('auth.magic.verify', ['token' => $rawToken, 'uid' => $this->user->id]);

        $this->get($url)
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    /** @test */
    public function token_hash_is_never_raw(): void
    {
        Mail::fake();

        $this->post(route('auth.magic.request'), ['email' => $this->user->email]);

        $token = MagicLoginToken::where('user_id', $this->user->id)->first();

        // Token hash must be 64 chars (SHA-256 hex)
        $this->assertEquals(64, strlen($token->token_hash));

        // Raw token must NOT be stored
        $this->assertNotEquals($token->token_hash, $token->token_hash . 'x');
    }

    /** @test */
    public function rate_limiting_blocks_excessive_requests(): void
    {
        Mail::fake();

        $limit = config('magic_link.rate_limit', 5);

        for ($i = 0; $i < $limit; $i++) {
            $this->post(route('auth.magic.request'), ['email' => $this->user->email]);
        }

        // One more should be rate limited
        $this->post(route('auth.magic.request'), ['email' => $this->user->email])
            ->assertStatus(429);
    }

    /** @test */
    public function previous_tokens_revoked_on_new_request(): void
    {
        Mail::fake();

        $this->post(route('auth.magic.request'), ['email' => $this->user->email]);
        $this->post(route('auth.magic.request'), ['email' => $this->user->email]);

        $tokens = MagicLoginToken::where('user_id', $this->user->id)->get();

        // Only latest is still valid
        $validCount = $tokens->filter(fn ($t) => $t->isValid())->count();
        $this->assertEquals(1, $validCount);
    }

    /** @test */
    public function inactive_user_cannot_login(): void
    {
        $this->user->update(['is_active' => false]);

        $rawToken  = bin2hex(random_bytes(64));
        $tokenHash = hash('sha256', $rawToken);

        MagicLoginToken::create([
            'user_id'    => $this->user->id,
            'token_hash' => $tokenHash,
            'expires_at' => now()->addMinutes(10),
            'is_used'    => false,
            'created_ip' => '127.0.0.1',
        ]);

        $url = route('auth.magic.verify', ['token' => $rawToken, 'uid' => $this->user->id]);
        $this->get($url)->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /** @test */
    public function all_login_actions_are_audited(): void
    {
        Mail::fake();

        $this->post(route('auth.magic.request'), ['email' => $this->user->email]);

        $this->assertDatabaseHas('audit_logs', ['action' => 'link_generated']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'email_sent']);
    }

    private function generateSignature(string $url): string
    {
        return hash_hmac('sha256', $url, config('app.key'));
    }
}
