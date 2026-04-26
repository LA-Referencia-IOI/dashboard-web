<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    private $viewPath = 'dashboard.tests.';

    public function index()
    {
        return view($this->viewPath . 'index');
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

        $authorityId = 'resolver-e2e-' . time();
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
            $res = Http::timeout(5)->get("$adminApiV1/status");
            if (!$res->successful()) throw new \Exception("Admin API down: " . $res->body());
            $log("Admin API OK");

            $res = Http::timeout(5)->get("$minterApi/health");
            if (!in_array($res->status(), [200, 503])) throw new \Exception("Minter API down: " . $res->body());
            $log("Minter API OK");

            $res = Http::timeout(5)->withHeaders($minterHeaders)->get("$minterApiV1/worker/status");
            if (!$res->successful() || !$res->json('running')) throw new \Exception("Worker not running: " . $res->body());
            $log("Worker OK");

            $res = Http::timeout(5)->get("$storeApi/health");
            if (!$res->successful()) throw new \Exception("Store API down: " . $res->body());
            $log("Store API OK");

            $res = Http::timeout(5)->post("$ipfsApi/api/v0/id");
            if (!$res->successful()) throw new \Exception("IPFS API down: " . $res->body());
            $log("IPFS API OK");

            $res = Http::timeout(5)->get("$ipfsClusterApi/id");
            if (!$res->successful()) throw new \Exception("IPFS Cluster API down: " . $res->body());
            $log("IPFS Cluster API OK");

            $res = Http::timeout(5)->get("$resolverApi/health");
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
            return response()->json(['success' => true, 'logs' => $logs]);

        } catch (\Exception $e) {
            $log("ERROR: " . $e->getMessage());
            return response()->json(['success' => false, 'logs' => $logs]);
        }
    }
}
