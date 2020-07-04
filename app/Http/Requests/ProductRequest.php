<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'name' => 'required|max:50',
            'description' => 'required',
            'image' => 'required|image',
            'price' => 'required|numeric'
        ];

        if (request()->isMethod('PUT') || request()->isMethod('PATCH'))
            $rules['image'] = 'nullable|image';

        return $rules;
    }
}
