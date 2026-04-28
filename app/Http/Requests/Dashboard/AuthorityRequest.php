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
        return [
            'name'        => 'required|max:255',
            'responsible' => 'required|max:255',
            'email'       => 'required|email|max:255',
            'phone'       => 'nullable|max:50',
        ];
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
