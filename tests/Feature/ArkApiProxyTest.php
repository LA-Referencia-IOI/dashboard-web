<?php

namespace Tests\Feature;

use App\Http\Controllers\Dashboard\ArkController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ArkApiProxyTest extends TestCase
{
    public function test_recent_arks_are_fetched_server_side(): void
    {
        config(['services.dark.admin_api_url' => 'http://10.20.30.20:8000']);
        Http::fake([
            'http://10.20.30.20:8000/api/v1/arks/recent*' => Http::response([
                'arks' => [],
            ]),
        ]);

        $response = (new ArkController())->recentApiData(
            Request::create('/dashboard/arks/api/recent?limit=5', 'GET')
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['arks' => []], json_decode($response->getContent(), true));
        Http::assertSent(function ($request) {
            return $request->url() === 'http://10.20.30.20:8000/api/v1/arks/recent?limit=5';
        });
    }

    public function test_ark_count_is_fetched_server_side(): void
    {
        config(['services.dark.admin_api_url' => 'http://10.20.30.20:8000']);
        Http::fake([
            'http://10.20.30.20:8000/api/v1/arks/count' => Http::response(['count' => 42]),
        ]);

        $response = (new ArkController())->countApiData(
            Request::create('/dashboard/arks/api/count', 'GET')
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['count' => 42], json_decode($response->getContent(), true));
        Http::assertSent(function ($request) {
            return $request->url() === 'http://10.20.30.20:8000/api/v1/arks/count';
        });
    }

    public function test_metadata_is_fetched_server_side(): void
    {
        config(['services.dark.resolver_api_url' => 'http://10.20.30.20:8002']);
        Http::fake([
            'http://10.20.30.20:8002/api/v1/arks/*' => Http::response(
                '{"title":"Example"}',
                200,
                ['Content-Type' => 'application/json']
            ),
        ]);

        $response = (new ArkController())->metadataApiData(
            Request::create(
                '/dashboard/arks/api/metadata?pid=ark%3A%2F12345%2Fexample',
                'GET'
            )
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('{"title":"Example"}', $response->getContent());
        Http::assertSent(function ($request) {
            return $request->url() ===
                'http://10.20.30.20:8002/api/v1/arks/ark:/12345/example?metadata=true';
        });
    }
}
