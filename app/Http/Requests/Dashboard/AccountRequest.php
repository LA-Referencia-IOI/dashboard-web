<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class AccountRequest extends FormRequest
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
            'organization_name' => 'required|max:255',
            'naan' => 'required|max:255',
            'payload_schema' => 'required|max:255',
            'contact_email' => 'required|email|max:255',
            'institution_id' => 'required|max:255',
            'url' => 'required|max:255',
        ];
        return $rules;
    }

    public function attributes()
    {
        return [
            'organization_name' => 'Name',
            'contact_email' => 'Email',
            'naan' => 'Naan',
            'payload_schema' => 'Payload_schema',
            'institution_id' => 'Institution ID',
            'url' => 'URL'
        ];
    }
}