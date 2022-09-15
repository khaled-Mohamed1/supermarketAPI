<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        if (request()->isMethod('post')) {
            return [
                'offer_name' => 'required|string|max:258',
                'offer_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
                'offer_quantity' => 'required|integer',
                'offer_price' => 'required|numeric'
            ];
        } else {
            return [
                'offer_name' => 'required|string|max:258',
                'offer_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
                'offer_quantity' => 'required|integer',
                'offer_price' => 'required|numeric'
            ];
        }
    }

    public function messages(): array
    {
        if (request()->isMethod('post')) {
            return [
                'offer_name.required' => 'يجب ادخال اسم العرض!',
                'offer_image.required' => 'يجب ادخال صورة العرض!',
                'offer_image.amx' => 'مساجة الصورة يجب ان تكون اقل من 4 ميجا!',
                'offer_quantity.required' => 'يجد ادخال كمية العرض!',
                'offer_price.required' => 'يجب ادخال سعر العرض!',

            ];
        } else {
            return [
                'offer_name.required' => 'يجب ادخال اسم العرض!',
                'offer_quantity.required' => 'يجد ادخال كمية العرض!',
                'offer_price.required' => 'يجب ادخال سعر العرض!',
            ];
        }
    }
}
