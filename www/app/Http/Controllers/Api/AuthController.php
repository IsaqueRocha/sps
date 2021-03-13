<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    private $rules;

    public function __construct()
    {
        $this->rules = [
            'name'                  => 'required|min:3|max:255',
            'email'                 => 'required|email|unique:users',
            'password'              => 'required',
            'confirmation_password' => 'required|same:password',
        ];
    }
    /**
     * Handle the registration process.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validatedFields = $this->validate($request, $this->rules);
        $validatedFields['password'] = bcrypt($validatedFields['password']);

        $user = User::create($validatedFields);

        $data['token'] = $user->createToken('MyApp')->plainTextToken;
        $data['user'] = $user;

        return response()->json(['data' => $data], Response::HTTP_CREATED);
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            /** @var User $user */
            $user = Auth::user();
            $data['token'] = $user->createToken('MyApp')->plainTextToken;
            $data['user'] = $user;

            return response()->json(['data' => $data], Response::HTTP_OK);
        }

        $data = [
            'message' => 'The given data was invalid.',
            'errors' => [
                'authentication' => 'E-mail or password is wrong.']
            ];

        return response()->json($data, Response::HTTP_UNAUTHORIZED);
    }
}
