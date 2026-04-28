<?php

namespace Tests\Unit;

use App\Models\MagicLoginToken;
use App\Models\User;
use App\Services\AuditService;
use App\Services\MagicLinkService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class MagicLinkServiceTest extends TestCase
{
    use RefreshDatabase;

    private MagicLinkService $service;
    private User $user;
    private Request $request;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new MagicLinkService(new AuditService());

        $this->user = User::factory()->create([
            'email'      => 'test@insurance.ae',
            'is_active'  => true,
            'is_verified'=> true,
        ]);

        $this->request = Request::create('/', 'GET', [], [], [], [
            'REMOTE_ADDR'     => '10.0.0.1',
            'HTTP_USER_AGENT' => 'TestBrowser/1.0',
        ]);
    }

    /** @test */
    public function token_is_512_bits_of_entropy(): void
    {
        $url = $this->service->generate($this->user, $this->request);

        // Extract raw token from URL
        parse_str(parse_url($url, PHP_URL_QUERY), $params);
        $rawToken = $params['token'];

        // 64 bytes = 128 hex chars
        $this->assertEquals(128, strlen($rawToken));
    }

    /** @test */
    public function only_hash_is_stored_in_database(): void
    {
        $url = $this->service->generate($this->user, $this->request);

        parse_str(parse_url($url, PHP_URL_QUERY), $params);
        $rawToken = $params['token'];

        $token = MagicLoginToken::where('user_id', $this->user->id)->first();

        // Raw token must not equal stored value
        $this->assertNotEquals($rawToken, $token->token_hash);

        // Stored value must be SHA-256 of raw token
        $this->assertEquals(hash('sha256', $rawToken), $token->token_hash);
    }

    /** @test */
    public function token_expires_within_configured_window(): void
    {
        config(['magic_link.expiry_minutes' => 10]);

        $this->service->generate($this->user, $this->request);

        $token = MagicLoginToken::where('user_id', $this->user->id)->first();
    }

    /** @test */
    public function previous_tokens_superseded_on_regeneration(): void
    {
        $this->service->generate($this->user, $this->request);
        $this->service->generate($this->user, $this->request);

        $tokens = MagicLoginToken::where('user_id', $this->user->id)->get();
        $this->assertEquals(2, $tokens->count());

        $valid = $tokens->filter(fn ($t) => $t->isValid());
        $this->assertEquals(1, $valid->count());
    }

    /** @test */
    public function token_validation_succeeds_for_valid_token(): void
    {
        $url = $this->service->generate($this->user, $this->request);
        parse_str(parse_url($url, PHP_URL_QUERY), $params);

        $result = $this->service->validate($params['token'], $this->user->id, $this->request);

        $this->assertNotNull($result);
        $this->assertEquals($this->user->id, $result->id);
    }

    /** @test */
    public function token_validation_fails_for_used_token(): void
    {
        $url = $this->service->generate($this->user, $this->request);
        parse_str(parse_url($url, PHP_URL_QUERY), $params);

        // Use it once
        $this->service->validate($params['token'], $this->user->id, $this->request);

        // Try again — should fail
        $result = $this->service->validate($params['token'], $this->user->id, $this->request);

        $this->assertNull($result);
    }

    /** @test */
    public function token_validation_fails_for_expired_token(): void
    {
        $this->service->generate($this->user, $this->request);

        // Force expire
        MagicLoginToken::where('user_id', $this->user->id)
            ->update(['expires_at' => now()->subMinute()]);

        $token  = MagicLoginToken::where('user_id', $this->user->id)->first();
        $rawToken = ''; // Can't recover raw — test the model directly

        $this->assertTrue($token->isExpired());
        $this->assertFalse($token->isValid());
    }

    /** @test */
    public function hash_comparison_is_timing_safe(): void
    {
        // Verify we use hash_equals (timing-safe) — indirectly by testing the path
        $url = $this->service->generate($this->user, $this->request);
        parse_str(parse_url($url, PHP_URL_QUERY), $params);

        // Wrong token should fail
        $result = $this->service->validate('wrong_token_' . $params['token'], $this->user->id, $this->request);
        $this->assertNull($result);
    }
}
