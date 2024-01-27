<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Dashboard\InstitutionRequest;
use App\Models\User;
use App\Models\Institution;

class InstitutionController extends Controller
{
    private $viewPath = 'dashboard.institution.';
    public function index(Request $request)
    {
        $total = Institution::count();
        $s = isset($request['s']) ? $request['s'] : null;
        if ($s) {
            $institutions = Institution::where('name', 'LIKE', '%' . $s . '%')
                ->orderBy('name')
                ->paginate(config('pagination.default'));
        }else{
            $institutions = Institution::orderBy('name')->paginate(config('pagination.default'));
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
        // Somente uma chave aletória
        $codeHash = hash('sha256', $institution->latitude);
        $institution->code = $codeHash;
        $institution->update();
       

        if ($institution->code) {
            return redirect()->back()
                            ->with(['message' => 'Chave atribuída com sucesso. Coloque este código no firmware do dispositivo.', 'code' => 'success']);
        } else {
            return redirect()->route('institution.index')
                             ->with(['message' => 'Erro ao registrar Dispositivo. Tente novamente!', 'code' => 'danger']);
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
