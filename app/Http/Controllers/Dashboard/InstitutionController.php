<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Dashboard\InstitutionRequest;
use App\Models\Authority;
use App\Models\Institution;

class InstitutionController extends Controller
{
    private $viewPath = 'dashboard.institution.';

    public function index(Request $request)
    {
        $total = Institution::count();
        $s = $request->get('s');

        $query = Institution::with('authority')->orderBy('name');

        if ($s) {
            $query->where('name', 'LIKE', '%' . $s . '%');
        }

        $institutions = $query->paginate(config('pagination.default'));

        return view($this->viewPath . 'index', compact('institutions', 'total', 's'));
    }

    /**
     * Called from Authority show page — authority_id comes as query param.
     */
    public function create(Request $request)
    {
        $authority = Authority::findOrFail($request->get('authority_id'));
        return view($this->viewPath . 'create', compact('authority'));
    }

    public function store(InstitutionRequest $request)
    {
        $institution = Institution::create($request->all());

        if ($institution) {
            return redirect()
                ->route('authorities.show', $request->authority_id)
                ->with(['message' => 'Institution created successfully.', 'code' => 'success']);
        }

        return redirect()->back()
            ->with(['message' => 'Error creating Institution. Try again!', 'code' => 'danger']);
    }

    public function edit(Institution $institution)
    {
        return view($this->viewPath . 'edit', compact('institution'));
    }

    public function update(InstitutionRequest $request, Institution $institution)
    {
        $institution->fill($request->all())->save();

        return redirect()
            ->route('authorities.show', $institution->authority_id)
            ->with(['message' => 'Institution updated successfully.', 'code' => 'success']);
    }

    public function destroy(Institution $institution)
    {
        $authorityId = $institution->authority_id;
        $institution->delete();

        return redirect()
            ->route('authorities.show', $authorityId)
            ->with(['message' => 'Institution deleted successfully.', 'code' => 'success']);
    }
}
