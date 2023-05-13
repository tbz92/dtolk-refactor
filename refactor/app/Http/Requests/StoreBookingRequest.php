<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // this will be a request parameter and will be added in the authentication middleware
        return $this->__authenticatedUser->user_type == config('app.customer_role_id');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'from_language_id' => ['required'], // exist rule can be added with model.if I'd know the models
            'immediate' => ['required'],
            'due_date' => ['required_if:immediate,no', 'date', 'after:today'], //it can be changed as per requirement
            'due_time' => ['required_if:immediate,no'],
            'customer_phone_type' => [
                'required_if:immediate,no',
                function ($input) {
                    return !empty($this->input('customer_physical_type'));
                }
            ],
            'duration' => ['required'], // it is required in both the cases whether immediate is no or yes
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'from_language_id' => 'Du måste fylla in alla fält',
            'due_date' => 'Du måste fylla in alla fält',
            'due_time' => 'Du måste fylla in alla fält',
            'customer_phone_type' => 'Du måste göra ett val här',
            'duration' => 'Du måste fylla in alla fält',
        ];
    }
}