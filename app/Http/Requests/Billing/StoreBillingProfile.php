<?php

namespace App\Http\Requests\Billing;

use Illuminate\Foundation\Http\FormRequest;

class StoreBillingProfile extends FormRequest
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
        return [
            'name' => 'required|max:100',
            'surname' => 'required|max:100',
            'document' => 'required|max:14',
            'document_type' => 'required',
            'gender' => 'nullable',
            'email' => 'required|email|unique:billing_profiles',
            'birthdate' => 'nullable|date',
            'phones' => 'nullable|array:home,mobile',
            'phones.*.ddi' => 'nullable|size:2',
            'phones.*.ddd' => 'nullable|size:2',
            'phones.*.number' => 'nullable',
            'metadata' => 'nullable|array'
        ];
    }
}
