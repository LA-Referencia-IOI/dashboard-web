<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SiteController extends Controller
{
    public function index()
    {
        // URL do seu nó Besu
        $nodeUrl = env('BLOCK_NUMBER');

        $postData = [
            'jsonrpc' => '2.0',
            'method'  => 'eth_blockNumber',
            'params'  => [],
            'id'      => 1
        ];
        $blockNumber = '1.091,02';

        try {
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($nodeUrl, $postData);
    
            
            if ($response->successful()) {
                $responseData = $response->json();
    
                
                $hexValue = $responseData['result'];
    
                
                $blockNumber = hexdec($hexValue);

                $blockNumber = number_format($blockNumber, 0, '.', ',');
    
                
                return view('lanpage.index', compact('blockNumber'));
            } else {
                return view('lanpage.index', compact('blockNumber'));
            }
        } catch (\Exception $e) {
            return view('lanpage.index', compact('blockNumber'));
        }
        
    
    }

    public function documentation()
    {
        return view('lanpage.documentation');
    }
}