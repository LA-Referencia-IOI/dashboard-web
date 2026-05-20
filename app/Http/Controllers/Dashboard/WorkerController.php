<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class WorkerController extends Controller
{
    public function index()
    {
        return view('dashboard.workers.index');
    }

    public function apiData()
    {
        $url = rtrim(env('WORKER_STATUS_URL', ''), '/');

        if (empty($url)) {
            return response()->json(['error' => 'WORKER_STATUS_URL not configured'], 500);
        }

        try {
            $res = Http::timeout(10)->get($url);
            return response()->json($res->json(), $res->status());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 503);
        }
    }
}
