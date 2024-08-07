<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Dashboard\AccountRequest;
use App\Models\User;
use App\Models\Account;

class AccountController extends Controller
{
    private $viewPath = 'dashboard.account.';
    public function index(Request $request)
    {
        $total = Account::count();
        $s = isset($request['s']) ? $request['s'] : null;

        
        if ($s) {
            $accounts = Account::where('organization_name', 'LIKE', '%' . $s . '%')
                ->orderBy('organization_name')
                ->paginate(config('pagination.default'));
        }else{
            $accounts = Account::orderBy('organization_name')->paginate(config('pagination.default'));
        }
        return view($this->viewPath . 'index', compact('accounts', 'total', 's'));
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
}