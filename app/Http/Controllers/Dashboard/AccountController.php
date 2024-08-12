<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\Dashboard\AccountRequest;
use App\Models\User;
use App\Models\Account;
use App\Enums\AccountType;

class AccountController extends Controller
{
    private $viewPath = 'dashboard.account.';
    public function index()
    {
        $total = Account::count();

        $manager = 'no';

        $m = Account::where('profile', '===', 0)->first();



        if($m){
            $manager = 'yes';
        }

        $accounts = Account::orderBy('profile')->paginate(config('pagination.default'));

        return view($this->viewPath . 'index', compact('accounts', 'total', 'manager'));
    }
    public function create()
    {
       
        return view($this->viewPath . 'create');
    }

    public function store(AccountRequest $request)
    {
        $account = Account::create($request->all());

        if ($account) {
            return redirect()
                ->route('accounts.index')
                ->with(['message' => 'Successfully registered.', 'code' => 'success']);
        } else {
            return redirect()
                ->route('account.create')
                ->with(['message' => 'Error when registering. Try again!', 'code' => 'danger']);
        }
    }

    public function edit(Account $account)
    {
       
        return view($this->viewPath . 'edit', ['account' => $account]);
    }

    public function update(AccountRequest $request, Account $account)
    {
        $data = [];

        $data = $request->all();

        $account->fill($data)->update();

        if ($account) {
            return redirect()
                ->route('accounts.index')
                ->with(['message' => 'Editing completed successfully.', 'code' => 'success']);
        } else {
            return redirect()
                ->route('account.create')
                ->with(['message' => 'Error when editing. Try again!', 'code' => 'danger']);
        }
    }

    public function destroy(Account $account)
    {

        $account->delete();

        if ($account) {
            return redirect()
                ->route('accounts.index')
                ->with(['message' => 'Deletion action completed successfully.', 'code' => 'success']);
        } else {
            return redirect()
                ->route('accounts.index')
                ->with(['message' => 'Error deleting. Try again!', 'code' => 'danger']);
        }
    }

    public function cardProfile(Account $account)
    {
        return view($this->viewPath . 'card-profile', ['account' => $account]);
    }


    public function createWallet(Account $account)
    {
        $acc = $account;

        $data = [
            'name' => $acc->organization_name,
            'mail' =>  $acc->contact_email,
            'naan' =>  $acc->naan,
            'default_payload_schema' =>  $acc->payload_schema
        ];
        $url = env('CREATE_WALLET');

        try {
            $response = Http::post($url, $data);
        
            if ($response->successful()) {
                $d = $response->json();
                $account->checkin_date = $d['checkinDate'];
                $account->auth_id = $d['decentralizedNameMappingAuthority']['auth_id'];
                $account->payload_schema = $d['decentralizedNameMappingAuthority']['payload_schema'];
                $account->address = $d['wallet']['address'];
                $account->balance = $d['wallet']['balance'];
                $account->private_key = $d['wallet']['private_key'];
                $account->shoulder = $d['decentralizedNameMappingAuthority']['shoulder'];
                $account->dnam_auth_id = $d['noidProvider']['dnam_auth_id'];
                $account->noid_len = $d['noidProvider']['noid_len'];
                $account->noidprovider_addr = $d['noidProvider']['noidprovider_addr'];

                $account->update();

                return redirect()
                    ->route('accounts.index')
                    ->with(['message' => 'Creating action completed successfully.', 'code' => 'success']);
            } else {
                return redirect()
                    ->route('accounts.index')
                    ->with(['message' => 'Error creating. Try again!', 'code' => 'danger']);
            }
        } catch (\Throwable $th) {
            return redirect()
                    ->route('accounts.index')
                    ->with(['message' => 'Error in connect to API, try again later', 'code' => 'danger']);
        }
        
    }

    public function accountManager(Request $request)
    {
       
        $request->validate([
            'address' => 'required|string',
        ]);

        try {
            $account = Account::where('address', $request->address)->first();

            if ($account) {
            
                $account->profile = AccountType::Manager;
                $account->update();

                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'message' => 'Account not found']);
            }
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Error in create manager, try again. ']);
        }

       
        
    }

    public function transferFunds(Request $request)
    {
        // /recharge/:account_from/:key_from/:account_to/:dark
        $request->validate([
            'address' => 'required|string',
            'balance' => 'required|numeric|min:0.01', 
        ]);

       
        $accountManager = Account::where('profile', '==', 0)->first();

        $account = Account::where('address', '==',$request['adress'])->first();

        $accFrom = $accountManager->address;
        $accPK = $accountManager->private_key;

        try {

            $url = env('API_DASHBOARD');
            $url = $url.'/recharge/'.$accFrom.'/'.$accPK.'/'.$request['adress'].$request['balance'];

            $response = Http::get($url);
            if ($response['success'] == true) {

                $url = env('API_DASHBOARD');
                $url2 = $url.'/balance/'.$request['address'];
                $response2 = Http::get($url2);

                if($response2['balance']){
                    $account->balance = $response2['balance'];
                    $account->update();
                }

                return redirect()
                    ->route('accounts.index')
                    ->with(['message' => 'Creating action completed successfully.', 'code' => 'success']);
            } else {
                return redirect()->route('accounts.index')
                    ->with(['message' => 'Error creating. Try again!', 'code' => 'danger']);
            }
        } catch (\Throwable $th) {
            return redirect()->route('accounts.index')
                    ->with(['message' => 'Error creating. Try again!', 'code' => 'danger']);
        }

        
    }

    public function getBalance(Account $account)
    {
        $url = env('API_DASHBOARD');
            $url2 = $url.'/get-balance/'.$account->address;

            
        try {

            $response2 = Http::get($url2);
            
            if($response2['balance']){
                $account->balance = $response2['balance'];
                $account->update();

                return redirect()
                    ->route('accounts.index')
                    ->with(['message' => 'Balance successfully.', 'code' => 'success']);
            }else{
                return redirect()->route('accounts.index')
                    ->with(['message' => 'Error in connect with api. Try again!', 'code' => 'danger']);
            }

        } catch (\Throwable $th) {
            return redirect()->route('accounts.index')
                    ->with(['message' => 'Error creating. Try again!', 'code' => 'danger']);
        } 
        
    }

}