<?php

namespace Vanguard\Http\Controllers\Api\Auth\Password;

use Password;
use Vanguard\Events\User\RequestedPasswordResetEmail;
use Vanguard\Http\Controllers\Api\ApiController;
use Vanguard\Http\Requests\Auth\PasswordRemindRequest;
use Vanguard\Mail\ResetPassword;
use Vanguard\Mail\GeneratePassword;
use Vanguard\Repositories\User\UserRepository;

class RemindController extends ApiController
{
    /**
     * Send a reset link to the given user.
     *
     * @param PasswordRemindRequest $request
     * @param UserRepository $users
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(PasswordRemindRequest $request, UserRepository $users)
    {
        $user = $users->findByEmail($request->email);

        $token = Password::getRepository()->create($user);
        $url = "https://develop.d1us243i5v574j.amplifyapp.com/#/reset-password/token=" . $token . "&email=" . $request->email;
        \Mail::to($user)->send(new ResetPassword($url));

        event(new RequestedPasswordResetEmail($user));

        return $token;
    }

    public function create(PasswordRemindRequest $request, UserRepository $users)
    {
        // $token['token'] = "";
        $user = $users->findByEmail($request->email);

        $token = Password::getRepository()->create($user);
        $url = "https://develop.d1us243i5v574j.amplifyapp.com/#/create-password/token=" . $token . "&email=" . $request->email;

        \Mail::to($user)->send(new GeneratePassword($url));

        event(new RequestedPasswordResetEmail($user));

        // return $this->respondWithSuccess();

        return $token;
    }
}
