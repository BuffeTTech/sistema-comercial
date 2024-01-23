<?php

namespace App\Http\Requests\SatisfactionSurvey;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSatisfactionQuestionRequest extends FormRequest
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
            'question' => 'required|max:255',
            
        ];

        return $rules;
    }
}