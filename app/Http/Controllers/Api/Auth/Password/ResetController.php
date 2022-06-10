<?php

namespace Vanguard\Http\Controllers\Api\Auth\Password;

use Hash;
use Illuminate\Auth\Events\PasswordReset;
use Password;
use Vanguard\Http\Controllers\Api\ApiController;
use Vanguard\Http\Requests\Auth\PasswordResetRequest;
use Vanguard\User;

class ResetController extends ApiController
{
    /**
     * Reset the given user's password.
     *
     * @param PasswordResetRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(PasswordResetRequest $request)
    {
        $user = User::where('email', $request->email)->get();
        if (Hash::check($request->password, $user[0]->password)) {
            $response = [
                "message" => "Your new password must be different from previously used passwords."
            ];
            return response()->json($response, 422);
        }
        $response = Password::reset($request->credentials(), function ($user, $password) {
            $this->resetPassword($user, $password);
        });

        switch ($response) {
            case Password::PASSWORD_RESET:
                return $this->respondWithSuccess();

            default:
                return $this->setStatusCode(400)
                    ->respondWithError(trans($response));
        }
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->password = $password;
        $user->save();

        event(new PasswordReset($user));
    }
}
