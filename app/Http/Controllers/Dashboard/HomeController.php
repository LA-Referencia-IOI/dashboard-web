<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\Blockchain;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

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

        $blockchains = Blockchain::all(); // Supondo que você tenha um modelo Blockchain
        $client = new Client();

        $upCount = 0;
        $downCount = 0;

        foreach ($blockchains as $blockchain) {
            try {
                $response = $client->get($blockchain->url);
                $responseBody = json_decode($response->getBody(), true);

                if (isset($responseBody['status']) && $responseBody['status'] === 'UP') {
                    $upCount++;
                } else {
                    $downCount++;
                }
            } catch (\Exception $e) {
                // Caso haja erro na requisição, contaremos como 'Down'
                $downCount++;
            }
        }
        
        return view('dashboard.home.index',compact('institutions','locations','countInstitutions', 'upCount', 'downCount'));
    }
}
