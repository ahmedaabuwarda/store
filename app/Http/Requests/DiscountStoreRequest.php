<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiscountStoreRequest extends FormRequest
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
            'balance' => 'required|numeric',
            'date_created' => 'required|date',
        ];
    }

    /**
     * Get the messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'balance.required' => 'هذا الحقل مطلوب',
            'balance.numeric' => 'هذا الحقل يجب ان يكون رقم',
            'date_created.required' => 'هذا الحقل مطلوب',
            'date_created.date' => 'هذا الحقل يجب ان يكون تاريخ',
        ];
    }
}
