<?php

namespace App\Http\Requests\Schedules;

use App\Enums\DayWeek;
use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleRequest extends FormRequest
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
            'day_week' => ['required', 'in:' . implode(',', DayWeek::array())],
            'start_time'=>'required|date_format:H:i', // esse formato é Hora:minuto
            'duration'=>'required|numeric|min:60', //minimo de uma hora em minutos 
            'start_block' => 'nullable|date_format:Y-m-d',
            'end_block' => 'nullable|date_format:Y-m-d|after_or_equal:start_block',
        ];
    }
}
