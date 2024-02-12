<?php

namespace App\Http\Requests\Foods;

use App\Enums\FoodStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFoodRequest extends FormRequest
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
            'name_food' => 'required|max:255|string',
            'slug' => 'required|max:255|string',
            'food_description' => 'required|string',
            'beverages_description' => 'required|string',
            'status'=>['in:' . implode(',', FoodStatus::array())],
            'foods_photo' => 'required|array',
            'foods_photo.*' => 'required|image|mimes:png,jpg,jpeg',
            'price' => 'required|numeric'
        ];
        return $rules;
    }
}
