<?php

namespace App\Http\Requests;

use App\Models\Decoration;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDecorationRequest extends FormRequest
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
            'main_theme'=>['required','max:255','unique:' .Decoration::class,'string'],
            'slug'=>['required','max:20','unique:'.Decoration::class, 'string',],
            'description'=>['required','max:255','string'],
            'price'=>['required','numeric'],
        ];
    }
}
