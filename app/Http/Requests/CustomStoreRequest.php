<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomStoreRequest extends FormRequest
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
            "product_id" => 'required|exists:products,id',
            'name' => 'required|max:255',
            'description' => 'required',
            'selling_price' => 'required',
            'release_date' => 'required'
        ];
    }
}
