<?php

namespace Tests\Feature;

use App\Enums\UserType;
use App\Http\Controllers\Dashboard\TestController;
use App\Models\User;
use Tests\TestCase;

class LivenessPageTest extends TestCase
{
    public function test_administrator_receives_all_operational_links(): void
    {
        config([
            'services.dark.grafana_url' => 'http://localhost:3000/dashboards',
            'services.dark.prometheus_url' => 'http://localhost:9090/targets',
            'services.dark.block_explorer_url' => 'http://localhost/explorer/',
        ]);
        $this->be($this->userWithProfile(UserType::Administrator));

        $view = (new TestController())->apiHealth();
        $links = collect($view->getData()['observabilityLinks'])->keyBy('name');

        $this->assertSame([
            'Grafana',
            'Prometheus',
            'Block Explorer',
        ], $links->keys()->all());
        $this->assertSame('http://localhost:3000/dashboards', $links['Grafana']['url']);
        $this->assertSame('http://localhost:9090/targets', $links['Prometheus']['url']);
        $this->assertSame('http://localhost/explorer/', $links['Block Explorer']['url']);
    }

    public function test_non_administrator_only_receives_block_explorer_link(): void
    {
        config([
            'services.dark.grafana_url' => 'http://localhost:3000/dashboards',
            'services.dark.prometheus_url' => 'http://localhost:9090/targets',
            'services.dark.block_explorer_url' => 'http://localhost/explorer/',
        ]);
        $this->be($this->userWithProfile(UserType::User));

        $view = (new TestController())->apiHealth();
        $links = $view->getData()['observabilityLinks'];

        $this->assertCount(1, $links);
        $this->assertSame('Block Explorer', $links[0]['name']);
    }

    private function userWithProfile(int $profile): User
    {
        $user = new User();
        $user->profile = $profile;

        return $user;
    }
}
