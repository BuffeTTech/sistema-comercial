<?php

namespace App\Http\Requests\Configuration;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConfigurationRequest extends FormRequest
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
            "min_days_booking"=>"required|integer|min:0",
            "max_days_unavaiable_booking"=>"required|integer|min:0",
            'buffet_instagram' => 'required|regex:/^https?:\/\/(www\.)?instagram\.com\/.+$/',
            'buffet_linkedin' => 'required|regex:/^https?:\/\/(www\.)?linkedin\.com\/.+$/',
            'buffet_facebook' => 'required|regex:/^https?:\/\/(www\.)?facebook\.com\/.+$/',
            'buffet_whatsapp' => 'required|regex:/^https:\/\/wa\.me\/\d{1,15}$/',
            "external_decoration"=>"nullable|boolean",
            "charge_by_schedule"=>"nullable|boolean",
            "allow_post_payment"=>"nullable|boolean",
            "children_affect_pricing"=>"nullable|boolean",
            "children_price_adjustment"=>"integer",
            "child_age_limit"=>"integer",
        ];
    }
}
