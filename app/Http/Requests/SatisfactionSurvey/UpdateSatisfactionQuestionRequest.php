<?php

namespace App\Http\Requests\SatisfactionSurvey;

use App\Enums\QuestionType;
use App\Models\Buffet;
use App\Models\SatisfactionQuestion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            'question_type' => [
                'required',
                'string',
                Rule::in(array_column(QuestionType::cases(), 'name'))
            ]
        ];

        return $rules;
    }
}