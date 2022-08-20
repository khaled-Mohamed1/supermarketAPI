<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
                'category_name' => 'required|string|max:258',
                'category_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'category_description' => 'required|string'
            ];
        } else {
            return [
                'category_name' => 'required|string|max:258',
                'category_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'category_description' => 'required|string'
            ];
        }
    }

    public function messages()
    {
        if (request()->isMethod('post')) {
            return [
                'category_name.required' => 'category_name is required!',
                'category_description.required' => 'category_description is required!',
                'category_image.required' => 'category_image is required!'
            ];
        } else {
            return [
                'category_name.required' => 'category_name is required!',
                'category_description.required' => 'category_description is required!'
            ];
        }
    }
}
