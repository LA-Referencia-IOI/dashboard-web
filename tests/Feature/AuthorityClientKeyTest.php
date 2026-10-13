<?php

namespace Tests\Feature;

use App\Models\Authority;
use App\Models\User;
use App\Support\Ed25519PublicKey;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthorityClientKeyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:',
            'services.dark.admin_api_url' => 'http://admin-api:8000']);
        Schema::create('authorities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('responsible');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('wallet_address')->nullable();
            $table->decimal('balance', 18, 8)->default(0);
            $table->string('status')->default('pending');
            $table->timestamps();
        });
        // Exercise the real migration against an existing authority table.
        require_once database_path('migrations/2026_10_10_000001_add_client_public_key_to_authorities.php');
        (new \AddClientPublicKeyToAuthorities)->up();
        Schema::create('naans', function (Blueprint $table) { $table->id(); });
        Schema::create('authority_naan', function (Blueprint $table) {
            $table->uuid('authority_id'); $table->unsignedBigInteger('naan_id'); $table->timestamps();
        });
        Schema::create('institutions', function (Blueprint $table) { $table->id(); $table->uuid('authority_id'); });
        Schema::create('accounts', function (Blueprint $table) { $table->id(); $table->uuid('authority_id'); });
        Schema::create('blockchains', function (Blueprint $table) { $table->id(); $table->uuid('authority_id'); });
        Schema::create('settings', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('user_id'); $table->string('name'); $table->string('value')->nullable();
        });
        Schema::create('files', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->boolean('highlight')->default(false); $table->timestamps();
        });
        Schema::create('file_user', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id'); $table->unsignedBigInteger('file_id');
        });
        $user = new User; $user->id = 1; $user->name = 'Operator'; $user->profile = 0;
        $this->actingAs($user);
    }

    private function authority(array $extra = []): Authority
    {
        return Authority::create(array_merge(['name' => 'Example', 'responsible' => 'Operator', 'email' => 'operator@example.org'], $extra));
    }

    public function test_create_submits_key_and_only_validated_fields(): void
    {
        $key = base64_encode(str_repeat("\x07", 32));
        Http::fake(['*' => Http::response(['wallet_address' => '0xwallet', 'active' => true], 200)]);
        $response = $this->post(route('authorities.store'), ['name' => 'Example', 'responsible' => 'Operator',
            'email' => 'operator@example.org', 'client_public_key' => $key, 'id' => 'injected', 'status' => 'disabled']);
        $authority = Authority::first();
        $response->assertRedirect(route('authorities.show', $authority->id));
        $this->assertNotSame('injected', $authority->id);
        $this->assertSame('active', $authority->status);
        $this->assertSame($key, $authority->client_public_key);
        Http::assertSent(fn ($request) => $request['client_public_key'] === $key);
    }

    public function test_creation_failure_remains_pending_and_preserves_key(): void
    {
        $key = base64_encode(str_repeat("\x07", 32));
        Http::fake(['*' => Http::response(['error' => 'offline'], 503)]);
        $this->post(route('authorities.store'), ['name' => 'Example', 'responsible' => 'Operator', 'email' => 'operator@example.org',
            'client_public_key' => $key])->assertSessionHas('code', 'warning');
        $this->assertSame('pending', Authority::first()->status);
        $this->assertSame($key, Authority::first()->client_public_key);
    }

    public function test_replacement_is_separate_from_contact_edit_and_requires_confirmation(): void
    {
        $old = base64_encode(str_repeat("\x07", 32)); $new = base64_encode(str_repeat("\x09", 32));
        $authority = $this->authority(['status' => 'active', 'wallet_address' => '0xwallet', 'client_public_key' => $old]);
        $this->put(route('authorities.update', $authority), ['name' => 'Renamed', 'responsible' => 'Operator',
            'email' => 'operator@example.org', 'client_public_key' => $new]);
        $this->assertSame($old, $authority->fresh()->client_public_key);
        Http::fake(['*' => Http::sequence()->push(['status' => 'success', 'transaction_hash' => 'confirmed'], 200)->push([], 503)]);
        $this->put(route('authorities.clientKey', $authority), ['client_public_key' => $new])->assertSessionHas('code', 'success');
        $this->assertSame($new, $authority->fresh()->client_public_key);
        Http::assertSent(fn ($request) => $request->method() === 'PUT' && $request['client_public_key'] === $new);
        $this->put(route('authorities.clientKey', $authority), ['client_public_key' => $old])->assertSessionHas('code', 'danger');
        $this->assertSame($new, $authority->fresh()->client_public_key);
    }

    public function test_current_key_is_read_from_admin_not_local_copy(): void
    {
        $key = base64_encode(str_repeat("\x09", 32));
        $authority = $this->authority(['status' => 'active', 'wallet_address' => '0xwallet']);
        Http::fake(['*' => Http::sequence()->push(['uuid' => $authority->id, 'active' => true, 'client_public_key' => $key], 200)->push([], 503)]);
        $this->get(route('authorities.show', $authority))->assertOk()->assertViewHas('clientKeyState',
            fn ($state) => $state['status'] === 'registered' && $state['public_key'] === $key);
        $this->get(route('authorities.show', $authority))->assertOk()->assertViewHas('clientKeyState',
            fn ($state) => $state['status'] === 'unavailable' && $state['public_key'] === null);
    }

    public function test_invalid_private_key_rejected_before_api_or_database_write(): void
    {
        Http::fake();
        $this->post(route('authorities.store'), ['name' => 'Example', 'responsible' => 'Operator',
            'email' => 'operator@example.org', 'client_public_key' => '-----BEGIN OPENSSH PRIVATE KEY-----'])
            ->assertSessionHasErrors('client_public_key');
        $this->assertSame(0, Authority::count());
        Http::assertNothingSent();
    }

    public function test_openssh_is_normalized(): void
    {
        $raw = str_repeat("\x07", 32);
        $ssh = 'ssh-ed25519 ' . base64_encode(pack('N', 11) . 'ssh-ed25519' . pack('N', 32) . $raw) . ' client-label';
        $this->assertSame(base64_encode($raw), Ed25519PublicKey::normalize($ssh));
    }
}
