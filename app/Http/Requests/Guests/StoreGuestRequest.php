<?php

namespace App\Http\Requests\Guests;

use App\Enums\GuestStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGuestRequest extends FormRequest
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
        return [
            'rows' => 'required|array',
            'rows.*.name' => 'required|string|max:255',
            'rows.*.document' => 'required|string|cpf',
            'rows.*.age' => 'required|integer',
            'rows.*.status' => [
                'string', Rule::in(array_column(GuestStatus::cases(), 'name'))
              ]
            ];
    }
}
