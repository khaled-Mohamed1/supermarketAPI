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
                'category_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4069',
            ];
        } else {
            return [
                'category_name' => 'required|string|max:258',
                'category_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            ];
        }
    }

    public function messages()
    {
        if (request()->isMethod('post')) {
            return [
                'category_name.required' => 'يجب ادخال اسم التصنيف',
                'category_image.required' => 'يجب ادخال الصورة التصنيف',
                'category_image.max' => '!!مساحة الصورة يجب ان تكون اقل من 4 ميجا',
            ];
        } else {
            return [
                'category_name.required' => 'يجب ادخال اسم التصنيف',
            ];
        }
    }
}
