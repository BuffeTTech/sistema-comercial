<?php

namespace App\Http\Requests\Decorations;

use App\Models\Decoration;
use Illuminate\Foundation\Http\FormRequest;

class StoreDecorationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return TRUE;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'main_theme'=>'required|max:255',
            'slug'=> 'required|max:255',
            'description'=>'required',
            'price'=>'required|numeric'
        ];
    }
}
