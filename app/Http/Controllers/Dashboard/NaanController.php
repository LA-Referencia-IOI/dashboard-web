<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Naan;

class NaanController extends Controller
{
    public function index()
    {
        $naans = Naan::all();
        return view('dashboard.naan.index', compact('naans'));
    }

    public function create()
    {
        return view('dashboard.naan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'naan' => 'required|string|unique:naans,naan',
            'organization_name' => 'required|string',
            'organization_acronym' => 'nullable|string',
            'target_url_template' => 'nullable|string',
            'contact_name' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'status' => 'nullable|string',
            'registered_at' => 'nullable|date',
        ]);

        Naan::create($request->all());

        return redirect()->route('naans.index')->with('success', 'NAAN created successfully.');
    }

    public function edit(Naan $naan)
    {
        return view('dashboard.naan.edit', compact('naan'));
    }

    public function update(Request $request, Naan $naan)
    {
        $request->validate([
            'naan' => 'required|string|unique:naans,naan,' . $naan->id,
            'organization_name' => 'required|string',
            'organization_acronym' => 'nullable|string',
            'target_url_template' => 'nullable|string',
            'contact_name' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'status' => 'nullable|string',
            'registered_at' => 'nullable|date',
        ]);

        $naan->update($request->all());

        return redirect()->route('naans.index')->with('success', 'NAAN updated successfully.');
    }

    public function destroy(Naan $naan)
    {
        $naan->delete();
        return redirect()->route('naans.index')->with('success', 'NAAN deleted successfully.');
    }
}
