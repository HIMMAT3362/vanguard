<?php

namespace Vanguard\Http\Controllers\Api\Auth;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Vanguard\Events\User\LoggedIn;
use Vanguard\Events\User\LoggedOut;
use Vanguard\Http\Controllers\Api\ApiController;
use Vanguard\Http\Requests\Auth\ApiLoginRequest;
use Vanguard\User;

/**
 * Class LoginController
 * @package Vanguard\Http\Controllers\Api\Auth
 */
class AuthController extends ApiController
{
    public function __construct()
    {
        $this->middleware('guest')->only('login');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Attempt to log the user in and generate unique
     * JWT token on successful authentication.
     *
     * @param ApiLoginRequest $request
     * @return JsonResponse|Response
     * @throws BindingResolutionException
     * @throws ValidationException
     */
    public function token(ApiLoginRequest $request)
    {
        $input = $request->all();
        $user = $this->findUser($request, $input);

        if ($user->isBanned()) {
            return $this->errorUnauthorized(__('Your account is banned by administrators.'));
        }

        if ($user->Active == 0) {
            return $this->errorUnauthorized(__('Sorry you can\'t logged in, because your account is deleted.'));
        }

        Auth::setUser($user);

        event(new LoggedIn);

        $response = [
            'success' => true,
            'code'    => 200,
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'user_name' => $user->first_name . " " . $user->last_name,
            'role_id' => $user->role_id,
            'token'   => $user->createToken($request->password)->plainTextToken,
            'message' => "success."
        ];
        return response()->json($response, 200);
    }

    /**
     * Find the user instance from the API request.
     *
     * @param ApiLoginRequest $request
     * @return mixed
     * @throws BindingResolutionException
     * @throws ValidationException
     */
    private function findUser(ApiLoginRequest $request, $data)
    {
        $user = User::where($request->getCredentials())->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }
        return $user;
    }

    /**
     * Logout user and invalidate token.
     * @return JsonResponse
     */
    public function logout()
    {
        event(new LoggedOut);

        auth()->user()->currentAccessToken()->delete();

        return $this->respondWithSuccess();
    }
}
