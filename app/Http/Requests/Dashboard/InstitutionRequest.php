<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class InstitutionRequest extends FormRequest
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
            'name' => 'required|max:255',
            'profile' => 'required|max:255',
            'latitude' => 'required|max:255',
            'longitude' => 'required|max:255',
            'responsible' => 'required|max:255',
            'email' => 'required|email|max:255',
        ];
        return $rules;
    }

    public function attributes()
    {
        return [
            'name' => 'Name',
            'profile' => 'Profile',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'responsible' => 'Responsible',
            'email' => 'Email',
        ];
    }
}