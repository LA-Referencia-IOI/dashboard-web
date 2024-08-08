<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\Blockchain;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $institutionLoc = Institution::where('latitude', '!=', null)->where('longitude','!=',null)->get();

        $institutions = Institution::all();

        $countInstitutions = $institutions->count();

        $locations = [];

        $locations = $institutionLoc->map(function ($institution) {
            return [
                'latitude' => $institution['latitude'],
                'longitude' => $institution['longitude'],
                'name' => $institution['name'],
                'responsible' => $institution['responsible'],
                'email' => $institution['email'],
                'typeNodes' => $institution->getTypeNodes(),
            ];
        })->toArray();
        $locations = collect($locations);
        
        return view('dashboard.home.index',compact('institutions','locations','countInstitutions'));
    }
}
