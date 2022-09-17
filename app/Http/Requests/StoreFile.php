<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreFile extends FormRequest
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
        if (Auth::user()->hasAnyRole(['admin'])) {
            $rules = 'required';
        } else {
            $rules = 'required|mimes:pdf,doc,docx,odt,xls,xlsx,ods,zip,exe,dmg|max:524288'; // 512MB
        }

        return [
            'file' => $rules,
        ];
    }
}
