<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Institution;
use Carbon\Carbon;

class NetworkController extends Controller
{

    public function checkingAccess(Request $request)
    {
        try {
            $institution = Institution::where('id', '=', $request['id'])->first(); 

            if(!$institution){
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Id institution not exist, please try again.'
                ], 404);
            }
    
            if($institution->status == 0){
                //enable
                $code = $institution->code;
                if(!$code){
                    return response()->json([
                        'status' => 'error', 
                        'message' => 'Code not exist, please try again.'
                    ], 404);
                }else{

                    if($code !== $request['code']){
                        return response()->json([
                            'status' => 'error', 
                            'message' => 'Access credentials do not match., please try again or contact info@darkpid.com.'
                        ], 404);
                    }else{
                        return response()->json([
                            'status' => 'success', 
                            'message' => 'Access credentials it is ok!'
                        ], 404);                        
                    }
                }
            }
            else{
                return response()->json([
                    'status' => 'error', 
                    'message' => 'This Institution is disabled in this moment. Please contact: info@darkpid.com.'
                ], 500);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => 'error', 
                'message' => 'Error, please try again.'
            ], 404);
        }
    }
}

