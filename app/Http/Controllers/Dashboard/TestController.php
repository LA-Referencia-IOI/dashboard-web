<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Institution;

class TestController extends Controller
{
    private $viewPath = 'dashboard.tests.';

    public function index()
    {
        $institutions = Institution::all();
        $lastRun = Storage::exists('tests/last_run.json') ? json_decode(Storage::get('tests/last_run.json'), true) : null;
        return view($this->viewPath . 'index', compact('lastRun', 'institutions'));
    }

    public function download()
    {
        if (Storage::exists('tests/last_run.txt')) {
            return Storage::download('tests/last_run.txt', 'e2e_last_run_' . date('Y_m_d_His') . '.txt');
        }
        return redirect()->back()->with(['message' => 'No log file found.', 'code' => 'warning']);
    }

    public function run(Request $request)
    {
        set_time_limit(300); // 5 minutes timeout for the test runner

        $logs = [];
        $log = function($msg) use (&$logs) {
            $logs[] = '[' . date('H:i:s') . '] ' . $msg;
        };

        $adminApi = rtrim(env('ADMIN_API_BASE_URL'), '/');
        $minterApi = rtrim(env('MINTER_BASE_URL'), '/');
        $resolverApi = rtrim(env('RESOLVER_BASE_URL'), '/');
        $storeApi = rtrim(env('STORE_API_BASE_URL'), '/');
        $ipfsApi = rtrim(env('IPFS_API_BASE_URL'), '/');
        $ipfsClusterApi = rtrim(env('IPFS_CLUSTER_API_URL'), '/');

        $adminApiV1 = "$adminApi/api/v1/admin";
        $minterApiV1 = "$minterApi/api/v1";
        $resolverApiV1 = "$resolverApi/api/v1";

        $authorityId = $request->input('institution_id') ?: 'resolver-e2e-' . time();
        $naan = env('NAAN', '12345');
        $targetUrl = 'https://sedici.unlp.edu.ar/handle/10915/5605';
        $pollInterval = (int) env('POLL_INTERVAL_SECONDS', 2);
        $pollTimeout = (int) env('POLL_TIMEOUT_SECONDS', 90);

        $minterHeaders = ['X-Authority-Id' => $authorityId];

        $l1Metadata = [
            'title' => 'Distancias genéticas en poblaciones aborígenes de la Argentina',
            'authors' => ['Goicoechea, Alicia Susana', 'Soria, Marcelo', 'Haedo, Ana Silvia', 'Crognier, Emile', 'Carnese, Francisco R.'],
            'year' => 1996,
            'publisher' => 'Asociación de Antropología Biológica de la República Argentina (AABRA)',
            'resource_type' => 'article',
            'language' => 'es',
            'abstract' => 'En el presente estudio se estimaron las distancias...',
            'journal_title' => 'Revista Argentina de Antropología Biológica',
            'journal_volume_issue' => 'vol. 1, no. 1',
            'pages' => '153-166',
            'issn' => '1853-6387',
            'source_repository' => 'SEDICI - Universidad Nacional de La Plata',
            'source_oai_identifier' => 'oai:sedici.unlp.edu.ar:10915/5605',
            'source_oai_record_url' => 'https://sedici.unlp.edu.ar/oai/request?verb=GetRecord&metadataPrefix=oai_dc&identifier=oai:sedici.unlp.edu.ar:10915/5605',
            'source_pdf_url' => 'https://sedici.unlp.edu.ar/bitstream/handle/10915/5605/Documento_completo.pdf?sequence=1&isAllowed=y',
            'source_external_url' => 'https://revistas.unlp.edu.ar/raab/article/view/161/58',
            'subjects' => ['Antropología', 'antropología biológica', 'Argentina', 'Población Indígena', 'afinidades biológicas', 'distancia genética'],
            'rights' => 'Creative Commons Attribution-NonCommercial 2.5 Argentina (CC BY-NC 2.5)',
            'license_url' => 'http://creativecommons.org/licenses/by-nc/2.5/ar/',
            'alternate_identifiers' => [
                ['schema' => 'uri', 'value' => $targetUrl],
                ['schema' => 'oai', 'value' => 'oai:sedici.unlp.edu.ar:10915/5605'],
                ['schema' => 'issn', 'value' => '1853-6387'],
                ['schema' => 'sedici', 'value' => 'ARG-AABRA-ART-0000000012'],
            ],
            'alternate_urls' => [$targetUrl, 'https://sedici.unlp.edu.ar/bitstream/handle/10915/5605/Documento_completo.pdf?sequence=1&isAllowed=y', 'https://revistas.unlp.edu.ar/raab/article/view/161/58', 'https://sedici.unlp.edu.ar/oai/request?verb=GetRecord&metadataPrefix=oai_dc&identifier=oai:sedici.unlp.edu.ar:10915/5605'],
        ];

        $l2Xml = '<oai_dc:dc xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/" xmlns:doc="http://www.lyncode.com/xoai" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:dc="http://purl.org/dc/elements/1.1/" xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd">
<dc:identifier>http://sedici.unlp.edu.ar/handle/10915/5605</dc:identifier>
<dc:title>Distancias genéticas en poblaciones aborígenes de la Argentina</dc:title>
<dc:creator>Goicoechea, Alicia Susana</dc:creator>
<dc:date>1996</dc:date>
<dc:language>es</dc:language>
<dc:subject>Antropología</dc:subject>
<dc:format>application/pdf</dc:format>
</oai_dc:dc>';

        try {
            $log("Starting E2E Tests...");
            
            // 1. Smoke Checks
            $log("1. Running Smoke Checks...");
            $res = Http::timeout(30)->get("$adminApiV1/status");
            if (!$res->successful()) throw new \Exception("Admin API down: " . $res->body());
            $log("Admin API OK");

            $res = Http::timeout(30)->get("$minterApi/health");
            if (!in_array($res->status(), [200, 503])) throw new \Exception("Minter API down: " . $res->body());
            $log("Minter API OK");

            $workerStatusUrl = rtrim(env('WORKER_STATUS_URL', "$minterApiV1/worker/status"), '/');
            $res = Http::timeout(30)->withHeaders($minterHeaders)->get($workerStatusUrl);
            if (!$res->successful() || !$res->json('running')) throw new \Exception("Worker not running: " . $res->body());
            $log("Worker OK");

            $res = Http::timeout(30)->get("$storeApi/health");
            if (!$res->successful()) throw new \Exception("Store API down: " . $res->body());
            $log("Store API OK");

            $res = Http::timeout(30)->post("$ipfsApi/api/v0/id");
            if (!$res->successful()) throw new \Exception("IPFS API down: " . $res->body());
            $log("IPFS API OK");

            $res = Http::timeout(30)->get("$ipfsClusterApi/id");
            if (!$res->successful()) throw new \Exception("IPFS Cluster API down: " . $res->body());
            $log("IPFS Cluster API OK");

            $res = Http::timeout(30)->get("$resolverApi/health");
            if (!$res->successful()) throw new \Exception("Resolver API down: " . $res->body());
            $log("Resolver API OK");

            // 2. Create Authority
            $log("2. Creating Authority and Assigning NAAN ($authorityId)...");
            $res = Http::timeout(180)->post("$adminApiV1/authority", ['uuid' => $authorityId, 'naans' => [], 'fund_amount_eth' => 0.05]);
            if (!in_array($res->status(), [200, 201, 409])) throw new \Exception("Failed to create authority: " . $res->body());
            
            $res = Http::timeout(180)->post("$adminApiV1/authority/$authorityId/authorize-naan", ['naan' => $naan]);
            if (!$res->successful()) throw new \Exception("Failed to authorize NAAN: " . $res->body());
            $log("Authority created and NAAN authorized.");

            // 3. Reserve ARK
            $log("3. Reserving an ARK...");
            $res = Http::timeout(180)->withHeaders($minterHeaders)->post("$minterApiV1/arks", ['authority_id' => $authorityId, 'naan' => $naan]);
            if ($res->status() != 201) throw new \Exception("Failed to reserve ARK: " . $res->body());
            $ark = $res->json('ark');
            $log("ARK reserved: $ark");

            // 4. Stage Metadata
            $log("4. Staging L1 and L2 Metadata...");
            $res = Http::timeout(180)->withHeaders($minterHeaders)->put("$minterApiV1/arks/$ark", [
                'authority_id' => $authorityId,
                'target' => $targetUrl,
                'minimal_metadata' => $l1Metadata,
                'original_metadata' => $l2Xml,
                'metadata_schema' => 'oai_dc',
                'metadata_media_type' => 'application/xml',
            ]);
            if (!$res->successful() || $res->json('state') != 'D') throw new \Exception("Failed to stage metadata: " . $res->body());
            $log("Metadata staged successfully.");

            // 5. Wait for Publish
            $log("5. Waiting for worker to publish ARK...");
            $deadline = time() + $pollTimeout;
            $publishedArk = null;
            while (time() < $deadline) {
                $res = Http::timeout(180)->withHeaders($minterHeaders)->get("$minterApiV1/arks/$ark");
                $state = $res->json('state');
                $log("Worker poll: state=$state");
                if ($res->successful() && $state == 'P') {
                    $publishedArk = $res->json();
                    break;
                }
                sleep($pollInterval);
            }
            if (!$publishedArk) throw new \Exception("Timed out waiting for published state");
            $log("ARK Published successfully.");

            // 6. Resolve Target
            $log("6. Resolving ARK to Default Target...");
            $res = Http::timeout(180)->withOptions(['allow_redirects' => false])->get("$resolverApiV1/arks/$ark");
            if (!in_array($res->status(), [302, 307]) || $res->header('location') != $targetUrl) throw new \Exception("Resolver failed to redirect to target. Status: " . $res->status());
            $log("Resolver default target OK.");

            // 7. Resolve Info
            $log("7. Resolving ?info...");
            $res = Http::timeout(180)->get("$resolverApiV1/arks/$ark?info");
            if (!$res->successful() || $res->json('title') != $l1Metadata['title']) throw new \Exception("Resolver ?info failed: " . $res->body());
            $log("Resolver ?info OK.");

            // 8. Resolve Metadata
            $log("8. Resolving ?metadata...");
            $res = Http::timeout(180)->get("$resolverApiV1/arks/$ark?metadata");
            if (!$res->successful() || strpos($res->header('content-type'), 'xml') === false) throw new \Exception("Resolver ?metadata failed: " . $res->body());
            $log("Resolver ?metadata OK.");

            // 9. Verify IPFS
            $log("9. Verifying IPFS storage...");
            $l1Cid = $publishedArk['level1_cid'] ?? null;
            $l2Cid = $publishedArk['level2_cid'] ?? null;
            $log("L1 CID = $l1Cid, L2 CID = $l2Cid");

            if (!$l1Cid || !$l2Cid) throw new \Exception("Missing CID in published ARK");

            $checkPin = function($cid) use ($storeApi, $pollTimeout, $pollInterval, $log) {
                $deadline = time() + $pollTimeout;
                while (time() < $deadline) {
                    $res = Http::timeout(180)->get("$storeApi/v1/status/$cid");
                    $status = $res->json('status');
                    $log("Pin poll $cid: status=$status");
                    if ($res->successful() && $res->json('pinned') === true) return true;
                    sleep($pollInterval);
                }
                return false;
            };

            if (!$checkPin($l1Cid)) throw new \Exception("L1 CID not pinned in time");
            if (!$checkPin($l2Cid)) throw new \Exception("L2 CID not pinned in time");
            $log("CIDs are successfully pinned.");

            $log("E2E Test Completed Successfully!");

            Storage::put('tests/last_run.txt', implode("\n", $logs));
            Storage::put('tests/last_run.json', json_encode(['date' => now()->toDateTimeString(), 'status' => 'Success']));

            return response()->json(['success' => true, 'logs' => $logs]);

        } catch (\Exception $e) {
            $log("ERROR: " . $e->getMessage());

            Storage::put('tests/last_run.txt', implode("\n", $logs));
            Storage::put('tests/last_run.json', json_encode(['date' => now()->toDateTimeString(), 'status' => 'Failed']));

            return response()->json(['success' => false, 'logs' => $logs]);
        }
    }

    public function apiHealth()
    {
        $apis = [
            ['name' => 'Block Number',       'env' => 'BLOCK_NUMBER',        'url' => env('BLOCK_NUMBER')],
            ['name' => 'Liveness',           'env' => 'LIVENESS',            'url' => env('LIVENESS')],
            ['name' => 'Admin API',          'env' => 'ADMIN_API_BASE_URL',  'url' => env('ADMIN_API_BASE_URL')],
            ['name' => 'Minter API',         'env' => 'MINTER_BASE_URL',     'url' => env('MINTER_BASE_URL')],
            ['name' => 'Resolver API',       'env' => 'RESOLVER_BASE_URL',   'url' => env('RESOLVER_BASE_URL')],
            ['name' => 'Store API',          'env' => 'STORE_API_BASE_URL',  'url' => env('STORE_API_BASE_URL')],
            ['name' => 'IPFS API',           'env' => 'IPFS_API_BASE_URL',   'url' => env('IPFS_API_BASE_URL')],
            ['name' => 'IPFS Cluster API',   'env' => 'IPFS_CLUSTER_API_URL','url' => env('IPFS_CLUSTER_API_URL')],
        ];

        return view($this->viewPath . 'api_health', compact('apis'));
    }

    public function checkApis(Request $request)
    {
        $healthChecks = [
            'CREATE_WALLET'       => ['method' => 'GET',  'path' => ''],
            'BLOCK_NUMBER'        => ['method' => 'POST', 'path' => '', 'body' => ['jsonrpc' => '2.0', 'method' => 'eth_blockNumber', 'params' => [], 'id' => 1]],
            'LIVENESS'            => ['method' => 'GET',  'path' => ''],
            'ADMIN_API_BASE_URL'  => ['method' => 'GET',  'path' => '/api/v1/admin/status'],
            'MINTER_BASE_URL'     => ['method' => 'GET',  'path' => '/health'],
            'RESOLVER_BASE_URL'   => ['method' => 'GET',  'path' => '/health'],
            'STORE_API_BASE_URL'  => ['method' => 'GET',  'path' => '/health'],
            'IPFS_API_BASE_URL'   => ['method' => 'POST', 'path' => '/api/v0/id'],
            'IPFS_CLUSTER_API_URL'=> ['method' => 'GET',  'path' => '/id'],
        ];

        $results = [];

        foreach ($healthChecks as $envKey => $config) {
            $baseUrl = rtrim(env($envKey, ''), '/');
            $url = $baseUrl . $config['path'];

            if (empty($baseUrl)) {
                $results[] = [
                    'env'     => $envKey,
                    'url'     => 'Not configured',
                    'status'  => 'warning',
                    'code'    => '-',
                    'message' => 'Variable not set in .env',
                    'time_ms' => 0,
                ];
                continue;
            }

            try {
                $start = microtime(true);

                if ($config['method'] === 'POST') {
                    $body = $config['body'] ?? [];
                    $res = Http::timeout(15)->post($url, $body);
                } else {
                    $res = Http::timeout(15)->get($url);
                }

                $elapsed = round((microtime(true) - $start) * 1000);

                $results[] = [
                    'env'     => $envKey,
                    'url'     => $url,
                    'status'  => $res->successful() ? 'online' : 'error',
                    'code'    => $res->status(),
                    'message' => $res->successful() ? 'OK' : substr($res->body(), 0, 120),
                    'time_ms' => $elapsed,
                ];
            } catch (\Exception $e) {
                $elapsed = round((microtime(true) - $start) * 1000);
                $results[] = [
                    'env'     => $envKey,
                    'url'     => $url,
                    'status'  => 'offline',
                    'code'    => '-',
                    'message' => substr($e->getMessage(), 0, 120),
                    'time_ms' => $elapsed,
                ];
            }
        }

        return response()->json($results);
    }

    public function updateApiUrl(Request $request)
    {
        $request->validate([
            'env_key' => 'required|string',
            'url'     => 'required|string',
        ]);

        $envKey = $request->input('env_key');
        $newUrl = $request->input('url');

        $allowedKeys = [
            'CREATE_WALLET', 'BLOCK_NUMBER', 'LIVENESS',
            'ADMIN_API_BASE_URL', 'MINTER_BASE_URL', 'RESOLVER_BASE_URL',
            'STORE_API_BASE_URL', 'IPFS_API_BASE_URL', 'IPFS_CLUSTER_API_URL',
        ];

        if (!in_array($envKey, $allowedKeys)) {
            return response()->json(['success' => false, 'message' => 'Invalid env key.'], 400);
        }

        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        $pattern = '/^' . preg_quote($envKey, '/') . '=.*/m';

        if (preg_match($pattern, $envContent)) {
            $envContent = preg_replace($pattern, $envKey . '="' . $newUrl . '"', $envContent);
        } else {
            $envContent .= "\n" . $envKey . '="' . $newUrl . '"';
        }

        file_put_contents($envPath, $envContent);

        return response()->json(['success' => true, 'message' => 'URL updated successfully.']);
    }
}
