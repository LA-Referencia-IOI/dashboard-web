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
            'who' => 'required|max:255',
            'what' => 'required|max:255',
            'when' => 'required|max:255',
            'where' => 'required|max:255',
            'how' => 'required|max:255',
            'why' => 'required|max:255',
            'contact' => 'required|max:255',
            'address' => 'required|max:255'
        ];
        return $rules;
    }

    public function attributes()
    {
        return [
            'who' => 'who',
            'what' => 'what',
            'when' => 'when',
            'where' => 'where',
            'how' => 'how',
            'why' => 'why',
            'contact' => 'contact',
            'address' => 'adress'
        ];
    }
}