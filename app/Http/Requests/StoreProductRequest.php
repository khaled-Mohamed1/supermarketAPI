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
                'product_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
                'product_description' => 'required|string',
                'product_quantity' => 'required|integer',
                'product_price' => 'required|numeric'
            ];
        } else {
            return [
                'category_id' => 'required',
                'product_name' => 'required|string|max:258',
                'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
                'product_description' => 'required|string',
                'product_quantity' => 'required|integer',
                'product_price' => 'required|numeric'
            ];
        }
    }

    public function messages()
    {
        if (request()->isMethod('post')) {
            return [
                'category_id.required' => 'يجب ادخال تصنيف!',
                'product_name.required' => 'يجب ادخال اسم المنتج!',
                'product_description.required' => 'يجب ادخال وصف المنتج!',
                'product_image.required' => 'يجب ادخال صورة المنتج!',
                'product_image.max' => 'مساحة الصورة يجب ان تكون اقل من 4 ميجا!',
                'product_quantity.required' => 'يجب ادخال كمية المنتج!',
                'product_price.required' => 'يجب ادخال سعر المنتج!',

            ];
        } else {
            return [
                'category_id.required' => 'يجب ادخال تصنيف!',
                'product_name.required' => 'يجب ادخال اسم المنتج!',
                'product_description.required' => 'يجب ادخال وصف المنتج!',
                'product_quantity.required' => 'يجب ادخال كمية المنتج!',
                'product_price.required' => 'يجب ادخال سعر المنتج!',
            ];
        }
    }
}
