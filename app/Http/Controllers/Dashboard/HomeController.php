<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\Blockchain;
use Illuminate\Support\Facades\Http;

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

        // URL of your Besu node from .env
        $originalNodeUrl = env('BLOCK_NUMBER');

        $blockNumber = '?';

        // Ensure URL is not null
        if ($originalNodeUrl) {
            try {
                // getting number of blocks
                $response = Http::post($originalNodeUrl, [
                    'jsonrpc' => '2.0',
                    'method' => 'eth_blockNumber',
                    'params' => [],
                    'id' => 1,
                ]);
                
                // Check if the request was successful
                if ($response->successful()) {
                    // Get the hexadecimal result of the block number
                    $blockHex = $response->json('result');

                    if ($blockHex) {
                        // Remove the '0x' prefix and convert from hexadecimal to decimal
                        $blockNumber = hexdec($blockHex);
                        $blockNumber = number_format($blockNumber, 0, ',');
                    }
                }
            } catch (\Exception $e) {
                $blockNumber = '?';
            }
        }

        // getting number of darks
        $response2 = Http::get('http://dark-01.dark-pid.net:5000/check-number');

        try {
            // Check if the request was successful
            if ($response2->successful()) {
                $numberDark = $response2->json();
                $numberDark = number_format($numberDark, 0, ',');
            } else {
                $numberDark = '1.091,02';
            }
        } catch (\Exception $e) {
            $numberDark = '1.091,02';
        }

        $countNaans = \App\Models\Naan::count();
        
        return view('dashboard.home.index',compact('institutions','locations','countInstitutions', 'countNaans', 'upCount', 'downCount', 'blockNumber', 'numberDark'));
    }
}
