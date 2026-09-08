<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WorkerController extends Controller
{
    public function index()
    {
        return view('dashboard.workers.index');
    }

    public function errorsPage()
    {
        return view('dashboard.workers.errors');
    }

    public function apiData(Request $request)
    {
        $url = rtrim(env('WORKER_STATUS_URL', ''), '/');

        if (empty($url)) {
            return response()->json(['error' => 'WORKER_STATUS_URL not configured'], 500);
        }

        try {
            $detail = $request->query('detail', 'simple');
            if (!in_array($detail, ['simple', 'workload', 'infrastructure', 'full'], true)) {
                return response()->json(['error' => 'Invalid status detail'], 422);
            }
            if ($detail === 'simple' || $detail === 'workload' || $detail === 'infrastructure') {
                $res = $detail === 'simple'
                    ? Http::timeout(10)->get($url)
                    : Http::timeout(15)->get($url, ['detail' => $detail]);
                return response()->json($res->json(), $res->status());
            }

            $cacheKey = 'dashboard:worker-status:full';
            if ($request->boolean('refresh')) {
                Cache::forget($cacheKey);
            }
            $payload = Cache::get($cacheKey);
            if ($payload === null) {
                $lock = Cache::lock($cacheKey . ':lock', 10);
                try {
                    $lock->block(5);
                    $payload = Cache::get($cacheKey);
                    if ($payload === null) {
                        $res = Http::timeout(30)->get($url, ['detail' => 'full']);
                        $payload = ['status' => $res->status(), 'body' => $res->json()];
                        Cache::put($cacheKey, $payload, now()->addSeconds(30));
                    }
                } finally {
                    optional($lock)->release();
                }
            }
            return response()->json($payload['body'], $payload['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 503);
        }
    }

    public function errors(Request $request)
    {
        $url = rtrim(env('WORKER_STATUS_URL', ''), '/');
        if (empty($url)) {
            return response()->json(['error' => 'WORKER_STATUS_URL not configured'], 500);
        }
        try {
            $query = $request->only(['stage', 'authority_id', 'error_code', 'page', 'page_size']);
            $errorsUrl = preg_replace('/\/status$/', '/errors', $url);
            $res = Http::timeout(15)->get($errorsUrl, $query);
            return response()->json($res->json(), $res->status());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 503);
        }
    }
}
