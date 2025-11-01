<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Ark;
use App\Http\Requests\Dashboard\ArkRequest;
use Illuminate\Support\Facades\DB;

class ArkController extends Controller
{
    private $viewPath = 'dashboard.institution.';

    public function index(Request $request)
    {
        $total = Ark::count();
        $s = isset($request['s']) ? $request['s'] : null;

        
        if ($s) {
            $arks = Ark::where('who', 'LIKE', '%' . $s . '%')
                ->orderBy('who')
                ->paginate(config('pagination.default'));
        }else{
            $arks = Ark::orderBy('who')->paginate(config('pagination.default'));
        }
        return view($this->viewPath . 'index-ark', compact('arks', 'total', 's'));
    }
    public function create()
    {
       
        return view($this->viewPath . 'create-ark');
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

    public function edit(ArkRequest $request)
    {
        dd("here");
        return view($this->viewPath . 'edit', ['ark' => $ark]);
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
}
