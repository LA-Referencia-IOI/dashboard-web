<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Dashboard\InstitutionRequest;
use App\Models\User;
use App\Models\Institution;
use Illuminate\Support\Facades\Http;

class InstitutionController extends Controller
{
    private $viewPath = 'dashboard.institution.';
    public function index(Request $request)
    {
        $total = Institution::count();
        $s = isset($request['s']) ? $request['s'] : null;

        
        if ($s) {
            $institutions = Institution::with('account')
                ->where('name', 'LIKE', '%' . $s . '%')
                ->orderBy('name')
                ->paginate(config('pagination.default'));
        } else {
            $institutions = Institution::with('account')
                ->orderBy('name')
                ->paginate(config('pagination.default'));
        }

        return view($this->viewPath . 'index', compact('institutions', 'total', 's'));
    }
    public function create()
    {
        return view($this->viewPath . 'create');
    }

    public function store(InstitutionRequest $request)
    {
        $institution = Institution::create($request->all());

        if ($institution) {
            return redirect()
                ->route('institutions.index')
                ->with(['message' => 'Successfully registered.', 'code' => 'success']);
        } else {
            return redirect()
                ->route('institution.create')
                ->with(['message' => 'Error when registering. Try again!', 'code' => 'danger']);
        }
    }

    public function edit(Institution $institution)
    {
        return view($this->viewPath . 'edit', ['institution' => $institution]);
    }

    public function update(InstitutionRequest $request, Institution $institution)
    {
        $data = [];

        $data = $request->all();

        $institution->fill($data)->update();

        if ($institution) {
            return redirect()
                ->route('institutions.index')
                ->with(['message' => 'Editing completed successfully.', 'code' => 'success']);
        } else {
            return redirect()
                ->route('institution.create')
                ->with(['message' => 'Error when editing. Try again!', 'code' => 'danger']);
        }
    }

    public function addIdBlockchain(Institution $institution)
    {
        $codeHash = hash('sha256', $institution->latitude);
        $institution->code = $codeHash;
        $institution->update();
       

        if ($institution->code) {
            return redirect()->back()
                            ->with(['message' => 'Key assigned successfully..', 'code' => 'success']);
        } else {
            return redirect()->route('institution.index')
                             ->with(['message' => 'Error registering Device. Try again!', 'code' => 'danger']);
        }
    }

    public function registerAuthority(Institution $institution)
    {
        $adminApiUrl = env('ADMIN_API_BASE_URL');

        if (!$adminApiUrl) {
            return redirect()->back()->with(['message' => 'ADMIN_API_BASE_URL not configured.', 'code' => 'danger']);
        }

        try {
            // 1. Create the Authority
            $authResponse = Http::timeout(180)->post($adminApiUrl . '/api/v1/admin/authority', [
                'uuid' => (string) $institution->id,
                'naans' => [],
                'fund_amount_eth' => 0.05
            ]);

            // 409 means already exists, which is acceptable
            if (!$authResponse->successful() && $authResponse->status() != 409) {
                return redirect()->back()->with(['message' => 'Error creating authority: ' . $authResponse->body(), 'code' => 'danger']);
            }

            // Flag as registered
            $institution->authority_registered = true;
            $institution->save();

            // 2. Authorize NAAN if account exists
            if ($institution->account && $institution->account->naan) {
                $naanResponse = Http::timeout(180)->post($adminApiUrl . '/api/v1/admin/authority/' . $institution->id . '/authorize-naan', [
                    'naan' => $institution->account->naan
                ]);

                if (!$naanResponse->successful()) {
                    return redirect()->back()->with(['message' => 'Authority created, but error authorizing NAAN: ' . $naanResponse->body(), 'code' => 'danger']);
                }

                return redirect()->back()->with(['message' => 'Authority registered and NAAN authorized successfully.', 'code' => 'success']);
            }

            return redirect()->back()->with(['message' => 'Authority registered, but no Account/NAAN found to authorize.', 'code' => 'warning']);

        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => 'Error connecting to API: ' . $th->getMessage(), 'code' => 'danger']);
        }
    }

    public function destroy(Institution $institution)
    {

        $institution->delete();

        if ($institution) {
            return redirect()
                ->route('institutions.index')
                ->with(['message' => 'Deletion action completed successfully.', 'code' => 'success']);
        } else {
            return redirect()
                ->route('institutions.index')
                ->with(['message' => 'Error deleting. Try again!', 'code' => 'danger']);
        }
    }

}
