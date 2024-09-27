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

        // URL of your Besu node with a default fallback
        $originalNodeUrl = 'http://dark-01.dark-pid.net:8545';//env('BLOCK_NUMBER');


        // Ensure URL is not null and has the correct format
        if (!$originalNodeUrl || (!str_starts_with($originalNodeUrl, 'http://') && !str_starts_with($originalNodeUrl, 'https://'))) {
            \Log::error('Invalid or missing node URL.');
            return view('lanpage.index', ['blockNumber' => 'Invalid Node URL']);
        }

        // getting number of blocks
        $response = Http::post($originalNodeUrl, [
            'jsonrpc' => '2.0',
            'method' => 'eth_blockNumber',
            'params' => [],
            'id' => 1,
        ]);
        
        try {
            // Check if the request was successful
            if ($response->successful()) {
                // Get the hexadecimal result of the block number
                $blockHex = $response->json('result');

                // Remove the '0x' prefix and convert from hexadecimal to decimal
                $blockNumber = hexdec($blockHex);
                $blockNumber = number_format($blockNumber, 0, ',');

              

            } else {
                $blockNumber = '1.091,02';
               
            }

        } catch (\Exception $e) {
            $blockNumber = '1.091,02';
           
        }

        // getting number of darks
        $response2 = Http::get('http://dark-01.dark-pid.net:5000/check-number');

        
        try {
            // Check if the request was successful
            if ($response2->successful()) {
                // Get the hexadecimal result of the block number
                $numberDark = $response2->json();


                $numberDark = number_format($numberDark, 0, ',');

              

            } else {
                $numberDark = '1.091,02';
               
            }

        } catch (\Exception $e) {
            $numberDark = '1.091,02';
           
        }


        
        return view('dashboard.home.index',compact('institutions','locations','countInstitutions', 'upCount', 'downCount', 'blockNumber', 'numberDark'));
    }
}
