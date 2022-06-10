<?php

namespace Vanguard\Http\Requests\Auth;

use Vanguard\Http\Requests\Request;
use Vanguard\Support\Enum\UserStatus;

class RegisterRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8',
            'role_id'  => 'required',
        ];

        // if (setting('registration.captcha.enabled')) {
        //     $rules['g-recaptcha-response'] = 'required|captcha';
        // }

        // if (setting('tos')) {
        //     $rules['tos'] = 'accepted';
        // }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    // public function messages()
    // {
    //     return [
    //         'tos.accepted' => __('You have to accept Terms of Service.')
    //     ];
    // }

    /**
     * Get the valid request data.
     *
     * @return array
     */
    public function validFormData()
    {
        // Determine user status. User's status will be set to UNCONFIRMED
        // if he has to confirm his email or to ACTIVE if email confirmation is not required
        // $status = setting('reg_email_confirmation')
            // ? UserStatus::UNCONFIRMED
            // : UserStatus::ACTIVE;

        // $status = 'ACTIVE';

        return $this->only('email', 'username', 'password', 'role_id');
    }
}
