<?php

namespace App\Http\Requests\Packages;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePackageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name_package' => 'required|max:255',
            'slug' => 'required|max:255',
            'food_description' => 'required',
            'beverages_description' => 'required',
            //'images' => 'required',
            //'images.*' => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'price' => 'required|numeric'
            // 'photo_1' =>'required|max:255',
            // 'photo_2' =>'required|max:255',
            // 'photo_3' =>'required|max:255'
        ];

        // if ($this->method() === 'PUT') {
        //     unset($rules['ímages']);
        //     unset($rules['images.*']);
        // }
        return $rules;
    }
}
