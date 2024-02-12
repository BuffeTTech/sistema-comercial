<?php

namespace App\Http\Requests\Bookings;

use App\Enums\BookingStatus;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
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
            'name_birthdayperson'=>['required','max:150', 'string'],
            'years_birthdayperson'=>['required','max:150', 'string'],
            'num_guests'=>['required','integer'],
            'party_day'=>['required','date'],
            'food_id'=>['required','integer', 'exists:foods,id'],
            'price_food'=>['numeric',],
            'decoration_id'=>['required','integer', 'exists:decorations,id'],
            'price_decoration'=>['numeric',],
            'schedule_id'=>['required','integer', 'exists:schedules,id'],
            'price_schedule'=>['numeric',],
            'discount'=>['integer'],
            'status'=>['in:' . implode(',', BookingStatus::array())],
        ];
    }
}
