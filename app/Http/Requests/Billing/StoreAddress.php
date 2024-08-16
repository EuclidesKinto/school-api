<?php

namespace App\Http\Requests\Billing;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddress extends FormRequest
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
            'line_1' => 'required|string',
            'line_2' => 'nullable|string',
            'state' => 'required|string|size:2',
            'city' => 'required|string',
            'zip_code' => 'required',
            'type' => 'nullable',
            'billing_profile_id' => 'required|exists:billing_profiles,id',
            'country' => 'nullable|string|size:2'
        ];
    }
}
