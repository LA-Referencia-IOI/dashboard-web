<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class AdminAuthorityApi
{
    private function url(string $path): string
    {
        $base = rtrim((string) config('services.dark.admin_api_url'), '/');
        if ($base === '') {
            throw new RuntimeException('Admin API is not configured.');
        }
        return $base . '/api/v1/admin/' . $path;
    }

    public function register(string $uuid, ?string $publicKey)
    {
        $payload = ['uuid' => $uuid, 'naans' => [], 'fund_amount_eth' => 0.05];
        if ($publicKey !== null) {
            $payload['client_public_key'] = $publicKey;
        }
        return Http::acceptJson()->timeout(180)->post($this->url('authority'), $payload);
    }

    public function authority(string $uuid)
    {
        return Http::acceptJson()->timeout(5)->get($this->url('authority/' . rawurlencode($uuid)));
    }

    public function clientKey(string $uuid)
    {
        return Http::acceptJson()->timeout(5)->get($this->url('authority/' . rawurlencode($uuid) . '/client-key'));
    }

    public function replaceClientKey(string $uuid, string $publicKey)
    {
        return Http::acceptJson()->timeout(180)->put(
            $this->url('authority/' . rawurlencode($uuid) . '/client-key'),
            ['client_public_key' => $publicKey]
        );
    }
    public function ownership(string $uuid)
    {
        return Http::acceptJson()->timeout(30)->get($this->url('authority/' . rawurlencode($uuid) . '/ownership'));
    }

    public function pids(string $uuid, int $page = 1)
    {
        return Http::acceptJson()->timeout(180)->get($this->url('authority/' . rawurlencode($uuid) . '/pids'), ['page' => $page]);
    }

    public function deactivate(string $uuid)
    {
        return Http::acceptJson()->timeout(180)->post($this->url('authority/' . rawurlencode($uuid) . '/deactivate'));
    }

    public function transferOwner(array $payload)
    {
        return Http::acceptJson()->timeout(180)->post($this->url('arks/transfer-owner'), $payload);
    }
}
