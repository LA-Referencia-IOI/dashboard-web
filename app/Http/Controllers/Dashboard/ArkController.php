<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Ark;
use App\Models\Institution;
use App\Models\Account;
use App\Http\Requests\Dashboard\ArkRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class ArkController extends Controller
{
    private $viewPath = 'dashboard.institution.';

    public function index(Request $request)
    {
        return view($this->viewPath . 'index-ark');
    }

    public function recentApiData(Request $request)
    {
        $adminApiUrl = rtrim((string) config('services.dark.admin_api_url'), '/');
        if ($adminApiUrl === '') {
            return response()->json([
                'error' => true,
                'message' => 'ADMIN_API_BASE_URL not configured.',
            ], 503);
        }

        $limit = min(max((int) $request->query('limit', 5), 1), 100);

        try {
            $upstream = Http::acceptJson()
                ->timeout(15)
                ->get($adminApiUrl . '/api/v1/arks/recent', ['limit' => $limit]);
        } catch (ConnectionException $exception) {
            return response()->json([
                'error' => true,
                'message' => 'Unable to connect to the Admin API.',
            ], 502);
        }

        return response($upstream->body(), $upstream->status())
            ->header('Content-Type', $upstream->header('Content-Type') ?: 'application/json');
    }

    public function countApiData(Request $request)
    {
        $adminApiUrl = rtrim((string) config('services.dark.admin_api_url'), '/');
        if ($adminApiUrl === '') {
            return response()->json([
                'error' => true,
                'message' => 'ADMIN_API_BASE_URL not configured.',
            ], 503);
        }

        try {
            $upstream = Http::acceptJson()
                ->timeout(15)
                ->get($adminApiUrl . '/api/v1/arks/count');
        } catch (ConnectionException $exception) {
            return response()->json([
                'error' => true,
                'message' => 'Unable to connect to the Admin API.',
            ], 502);
        }

        return response($upstream->body(), $upstream->status())
            ->header('Content-Type', $upstream->header('Content-Type') ?: 'application/json');
    }

    public function metadataApiData(Request $request)
    {
        $request->validate(['pid' => ['required', 'string', 'max:512']]);
        $resolverApiUrl = rtrim((string) config('services.dark.resolver_api_url'), '/');
        if ($resolverApiUrl === '') {
            return response()->json([
                'error' => true,
                'message' => 'RESOLVER_BASE_URL not configured.',
            ], 503);
        }

        $pid = ltrim($request->query('pid'), '/');

        try {
            $upstream = Http::timeout(15)->get(
                $resolverApiUrl . '/api/v1/arks/' . $pid,
                ['metadata' => 'true']
            );
        } catch (ConnectionException $exception) {
            return response()->json([
                'error' => true,
                'message' => 'Unable to connect to the Resolver API.',
            ], 502);
        }

        return response($upstream->body(), $upstream->status())
            ->header('Content-Type', $upstream->header('Content-Type') ?: 'text/plain');
    }

    public function create(Institution $institution)
    {
        $currentDateTime = now()->setTimezone('UTC')->format('Y-m-d\TH:i:sP'); // pre-filling the when field in form ark.  

        $resolverUrl = env('RESOLVER_URL');

        $targetUrl = $resolverUrl . '/ark:/${content}';

        

        return view($this->viewPath . 'create-ark', compact('currentDateTime', 'resolverUrl', 'targetUrl', 'institution'));
    }

    public function store(ArkRequest $request)
    {
        $ark = Ark::create($request->all());

        if ($ark) {
            return redirect()
                ->route('institutions.index-ark')
                ->with(['message' => 'Successfully registered.', 'code' => 'success']);
        } else {
            return redirect()
                ->route('institutions.create-ark')
                ->with(['message' => 'Error when registering. Try again!', 'code' => 'danger']);
        }
    }

    public function edit(Ark $ark)
    {

        return view($this->viewPath . 'create-ark', ['ark' => $ark]);
    }

    public function update(ArkRequest $request, Ark $ark)
    {
        $data = [];

        $data = $request->all();

        $ark->fill($data)->update();

        if ($ark) {
            return redirect()
                ->route('institutions.index-ark')
                ->with(['message' => 'Editing completed successfully.', 'code' => 'success']);
        } else {
            return redirect()
                ->route('institutions.create-ark')
                ->with(['message' => 'Error when editing. Try again!', 'code' => 'danger']);
        }
    }

    public function destroy(Ark $ark)
    {

        $ark->delete();

        if ($ark) {
            return redirect()
                ->route('institutions.index-ark')
                ->with(['message' => 'Deletion action completed successfully.', 'code' => 'success']);
        } else {
            return redirect()
                ->route('institutions.index-ark')
                ->with(['message' => 'Error deleting. Try again!', 'code' => 'danger']);
        }
    }

    public function exportTxt()
    {
       
        $records = DB::table('arks')->get();

        
        $content = '';
        foreach ($records as $record) {
            
            $content .= "naa:\n";
            $content .= "who:    {$record->who}\n";
            $content .= "what:   {$record->what}\n";
            $content .= "when:   {$record->when}\n";
            $content .= "where:  {$record->where}\n";
            $content .= "how:    {$record->how}\n";
            $content .= "why:    {$record->why}\n";
            $content .= "contact:    {$record->contact}\n";
            $content .= "address:    {$record->address}\n\n";
        }


        $filename = 'registers_' . date('Y_m_d_His') . '.txt';


        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }

    public function downloadJson($id)
    {
        $ark = Ark::findOrFail($id);

        $json = [
            "what" => $ark->what,
            "where" => $ark->where,
            "target" => [
                "url" => $ark->target_url,
                "http_code" => (int) $ark->target_http_code
            ],
            "when" => $ark->when,
            "who" => [
                "name" => $ark->who_name,
                "name_native" => $ark->who_name_native,
                "acronym" => $ark->who_acronym,
                "location" => $ark->who_location_lat && $ark->who_location_lon
                    ? [
                        "lat" => $ark->who_location_lat,
                        "lon" => $ark->who_location_lon,
                    ]
                    : null,
                "address" => $ark->address
            ],
            "na_policy" => [
                "orgtype" => $ark->na_orgtype,
                "policy" => $ark->na_policy,
                "tenure" => $ark->contact_tenure,
                "policy_url" => $ark->na_policy_url
            ],
            "test_identifier" => $ark->test_identifier,
            "service_provider" => $ark->service_provider,
            "purpose" => $ark->purpose,
            "rtype" => $ark->rtype,
            "why" => $ark->why,
            "contact" => [
                "name" => $ark->contact_name,
                "unit" => $ark->contact_unit,
                "tenure" => $ark->contact_tenure,
                "email" => $ark->contact,
                "phone" => $ark->contact_phone
            ],
            "alternate_contact" => $ark->alternate_contact,
            "comments" => $ark->comments,
            "provider" => $ark->provider
        ];

        $filename = "ark_{$ark->id}.json";

        return response()->streamDownload(function () use ($json) {
            echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }, $filename, [
            "Content-Type" => "application/json",
        ]);
    }

    public function downloadAll()
    {
        $arks = Ark::orderBy('id', 'desc')->get();

        $jsonList = [];

        foreach ($arks as $ark) {
            $jsonList[] = [
                "what" => $ark->what,
                "where" => $ark->where,
                "target" => [
                    "url" => $ark->target_url,
                    "http_code" => (int) $ark->target_http_code
                ],
                "when" => $ark->when,
                "who" => [
                    "name" => $ark->who,
                    "name_native" => $ark->who_name_native,
                    "acronym" => $ark->who_acronym,
                    "location" => $ark->who_location_lat && $ark->who_location_lon
                        ? [
                            "lat" => $ark->who_location_lat,
                            "lon" => $ark->who_location_lon,
                        ]
                        : null,
                    "address" => $ark->address
                ],
                "na_policy" => [
                    "orgtype" => $ark->na_orgtype,
                    "policy" => $ark->na_policy,
                    "tenure" => $ark->contact_tenure,
                    "policy_url" => $ark->na_policy_url
                ],
                "test_identifier" => $ark->test_identifier,
                "service_provider" => $ark->service_provider,
                "purpose" => $ark->purpose,
                "rtype" => $ark->rtype,
                "why" => $ark->why,
                "contact" => [
                    "name" => $ark->contact_name,
                    "unit" => $ark->contact_unit,
                    "tenure" => $ark->contact_tenure,
                    "email" => $ark->contact,
                    "phone" => $ark->contact_phone
                ],
                "alternate_contact" => $ark->alternate_contact,
                "comments" => $ark->comments,
                "provider" => $ark->provider
            ];
        }

        $filename = "ark_all.json";

        return response()->streamDownload(function () use ($jsonList) {
            echo json_encode($jsonList, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }, $filename, [
            "Content-Type" => "application/json",
        ]);
    }


}
