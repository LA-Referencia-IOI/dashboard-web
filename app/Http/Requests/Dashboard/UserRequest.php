<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'address' => 'max:191',
            'name' => 'required|max:191',
            'profile' => 'required|max:191',
        ];

        if (isset($this->user->id)) { // se tem id é edição


            $rules['email'] = 'required|email|max:255|unique:users,email,' . $this->user->id;
            $rules['profile'] = 'required|max:191';
            $rules['password'] = 'confirmed|nullable|min:6';
            $rules['password_confirmation'] = 'nullable|min:6|same:password';
        } else {
            $rules['email'] = 'nullable|email|unique:users';
            $rules['profile'] = 'required|max:191';
            $rules['password'] = 'confirmed|required|min:6';
            $rules['password_confirmation'] = 'required|min:6|same:password';
        }

        return $rules;
    }
}
