<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

use App\Http\Requests\Dashboard\UserRequest;

class UserController extends Controller
{
    private $viewPath = 'dashboard.user.';
    public function index(Request $request)
    {
        $total = User::count();
        $s = isset($request['s']) ? $request['s'] : null;
        if ($s) {
            $users = User::where('name', 'LIKE', '%' . $s . '%')
                ->orderBy('name')
                ->paginate(config('pagination.default'));
        }else{
            $users = User::orderBy('name')->paginate(config('pagination.default'));
        }
        return view($this->viewPath . 'index', compact('users', 'total', 's'));
    }
    public function create()
    {
        return view($this->viewPath . 'create');
    }

    public function store(UserRequest $request)
    {
        $request['password'] = bcrypt($request['password']);

        $user = User::create($request->all());

        if ($user) {
            return redirect()
                ->route('users.index')
                ->with(['message' => 'Cadastrado realizado com sucesso.', 'code' => 'success']);
        } else {
            return redirect()
                ->route('user.create')
                ->with(['message' => 'Erro ao cadastrar. Tente novamente!', 'code' => 'danger']);
        }
    }

    public function edit(User $user)
    {
        return view($this->viewPath . 'edit', ['user' => $user]);
    }

    public function update(UserRequest $request, User $user)
    {
        $data = [];

        $data = $request->all();

        if (!empty($request['password'])) {
            $request['password'] = bcrypt($request['password']);
            $data = $request->all();
        } else {
            $data = $request->except(['password', 'password_confirmation']);
        }

        $user->fill($data)->update();

        if ($user) {
            return redirect()
                ->route('users.index')
                ->with(['message' => 'Edição realizada com sucesso.', 'code' => 'success']);
        } else {
            return redirect()
                ->route('user.create')
                ->with(['message' => 'Erro ao editar. Tente novamente!', 'code' => 'danger']);
        }
    }

    public function destroy(User $user)
    {

        $user->delete();

        if ($user) {
            return redirect()
                ->route('users.index')
                ->with(['message' => 'Ação de exclusão realizada com sucesso.', 'code' => 'success']);
        } else {
            return redirect()
                ->route('users.index')
                ->with(['message' => 'Erro ao excluir. Tente novamente!', 'code' => 'danger']);
        }
    }

}
