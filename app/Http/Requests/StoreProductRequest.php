<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        if (request()->isMethod('post')) {
            return [
                'category_id' => 'required',
                'product_name' => 'required|string|max:258',
                'product_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'product_description' => 'required|string',
                'product_quantity' => 'required|integer',
            ];
        } else {
            return [
                'category_id' => 'required',
                'product_name' => 'required|string|max:258',
                'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'product_description' => 'required|string',
                'product_quantity' => 'required|integer',
            ];
        }
    }

    public function messages()
    {
        if (request()->isMethod('post')) {
            return [
                'category_id.required' => 'category_id is required!',
                'product_name.required' => 'product_name is required!',
                'product_description.required' => 'product_description is required!',
                'product_image.required' => 'product_image is required!',
                'product_quantity.required' => 'product_quantity is required!',
            ];
        } else {
            return [
                'category_id.required' => 'category_id is required!',
                'product_name.required' => 'product_name is required!',
                'product_description.required' => 'product_description is required!',
                'product_quantity.required' => 'product_quantity is required!',
            ];
        }
    }
}
