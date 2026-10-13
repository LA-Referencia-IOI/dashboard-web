<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class AuthorityRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name'        => 'required|max:255',
            'responsible' => 'required|max:255',
            'email'       => 'required|email|max:255',
            'phone'       => 'nullable|max:50',
        ];
        if ($this->routeIs('authorities.store')) {
            $rules['creation_token'] = ['required', 'string', 'max:2048'];
            $rules['client_public_key'] = ['nullable', 'string', 'max:4096', new \App\Rules\Ed25519PublicKey];
        }
        return $rules;
    }

    public function attributes()
    {
        return [
            'name'        => 'Name',
            'responsible' => 'Responsible',
            'email'       => 'Email',
            'phone'       => 'Phone',
        ];
    }
}
