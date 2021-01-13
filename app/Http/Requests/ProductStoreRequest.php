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
            'video' => 'required',
            'images' => 'required',
            'defect.*.description' => 'required_with:defect.*.video',
            'defect.*.video' => 'required_with:defect.*.description',
            'defect.*.images' => 'sometimes',
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
