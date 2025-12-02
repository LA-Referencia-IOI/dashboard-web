<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class ArkRequest extends FormRequest
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
            'when' => 'required|max:255',
            'where' => 'required|max:255',
            'why' => 'required|max:255',
        ];
        return $rules;
    }

    public function attributes()
    {
        return [
            'when' => 'when',
            'where' => 'where',
            'why' => 'why',
        ];
    }
}