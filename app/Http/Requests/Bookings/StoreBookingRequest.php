<?php

namespace App\Http\Requests\Bookings;

use App\Enums\BookingStatus;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'dietary_restrictions' => $this->boolean('dietary_restrictions'),
            'external_food' => $this->boolean('external_food'),
            'external_decoration' => $this->boolean('external_decoration'),
        ]);
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
            'years_birthdayperson'=>['required','integer'],
            'num_guests'=>['required','integer'],
            'birthday_date'=>['required','date'],
            'party_day'=>['required','string', 'max:20'],
            'food_id'=>['required', 'string', 'exists:foods,slug'],
            'price_food'=>['numeric',],
            'decoration_id'=>['required', 'string', 'exists:decorations,slug'],
            'price_decoration'=>['numeric',],
            // 'schedule_id'=>['required','integer', 'exists:schedules,id'],
            'price_schedule'=>['numeric',],
            'discount'=>['integer'],
            'status'=>['in:' . implode(',', BookingStatus::array())],

            'additional_foods_observations'=>['string', 'nullable'], //op
            'dietary_restrictions'=>['boolean'], // op
            'external_food'=>['boolean'], // op
            'external_decoration'=>['boolean'], // op
            // 'daytime_preference'=>['required'], // req
            'final_notes'=>['string', 'nullable'], // op

            // party_day contem scheudle_id e party day
        ];
    }
}
