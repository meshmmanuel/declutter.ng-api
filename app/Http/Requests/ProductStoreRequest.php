<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use function App\Http\Requests\rules as func_rules;

class ProductStoreRequest extends FormRequest
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
            'name' => 'required|max:255',
            'description' => 'required',
            'selling_price' => 'required',
            'product_status' => 'required',
            'video' => 'sometimes',
            'images' => 'required',
            'defect.*.description' => 'required',
            'defect.*.video' => 'sometimes',
            'defect.*.images' => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        // Include validation messages
        $messages = include 'ValidationMessages.php';

        // Return messages
        return $messages;
    }
}
