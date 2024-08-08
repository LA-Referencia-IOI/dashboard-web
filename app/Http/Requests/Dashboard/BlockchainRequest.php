<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class BlockchainRequest extends FormRequest
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
            'institution_id' => 'required|max:255',
            'type' => 'required|max:255',
            'number_nodes' => 'required|max:255',
            'local' => 'required|max:255',
            'status' => 'required|max:255',
        ];
        return $rules;
    }

    public function attributes()
    {
        return [
            'status' => 'Status',
            'local' => 'Local',
            'type' => 'Type',
            'number_nodes' => 'Nº nodes',
            'institution_id' => 'Institution ID'
        ];
    }
}