<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

class SiteController extends Controller
{
    public function index()
    {
        // The installer supplies the site-local RPC endpoint.
        $originalNodeUrl = env('BLOCK_NUMBER');


        // Ensure URL is not null and has the correct format
        if (!$originalNodeUrl || (!str_starts_with($originalNodeUrl, 'http://') && !str_starts_with($originalNodeUrl, 'https://'))) {
            \Log::error('Invalid or missing node URL.');
            return view('lanpage.index', ['blockNumber' => 'Invalid Node URL']);
        }

        // JSON-RPC request body
        $response = Http::post($originalNodeUrl, [
            'jsonrpc' => '2.0',
            'method' => 'eth_blockNumber',
            'params' => [],
            'id' => 1,
        ]);

        $response2 = Http::get(env('LIVENESS', $originalNodeUrl . '/liveness'));
        
        try {
            // Check if the request was successful
            if ($response->successful() && $response2->successful()) {
                // Get the hexadecimal result of the block number
                $blockHex = $response->json('result');

                // Remove the '0x' prefix and convert from hexadecimal to decimal
                $blockNumber = hexdec($blockHex);
                $blockNumber = number_format($blockNumber, 0, ',');

                $numberDark = $response2->json();
                $numberDark = number_format($numberDark, 0, ',');

                return view('lanpage.index', compact('blockNumber', 'numberDark'));

            } else {
                $blockNumber = '1,091';
                $numberDark = '1,000';
                return view('lanpage.index', compact('blockNumber', 'numberDark'));
            }

        } catch (\Exception $e) {
            $blockNumber = '1,091';
            $numberDark = '1,000';
            \Log::error('Error fetching block number: ' . $e->getMessage());
            return view('lanpage.index', compact('blockNumber', 'numberDark'));
        }

        
        

      
    }

    private function attemptRequest($url, $postData)
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($url, $postData);
    }

    private function isProtocolError($response)
    {
        $status = $response->status();
        return $status === 0 || $status === 404 || $status === 400;
    }

    private function switchProtocol($url)
    {
        if (str_starts_with($url, 'http://')) {
            return str_replace('http://', 'https://', $url);
        } elseif (str_starts_with($url, 'https://')) {
            return str_replace('https://', 'http://', $url);
        }
        return $url;
    }

    


    public function documentation()
    {
        return view('lanpage.documentation');
    }
}
