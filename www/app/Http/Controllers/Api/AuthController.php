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
            'password'              => 'required|min:6',
            'confirmation_password' => 'required|same:password',
            'type'                  => 'required|in:seller,customer',
        ];
    }

    /**
     * @OA\Post(
     *     tags={"Auth"},
     *     summary="Store a newly created resource in storage.",
     *     description="create a new user",
     *     path="/register",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", description="nome do usuário"),
     *             @OA\Property(property="email", type="string", description="e-mail do usuário"),
     *             @OA\Property(property="password", type="string", description="senha"),
     *             @OA\Property(property="confirmation_password", type="string", description="confirmação da senha"),
     *             @OA\Property(property="type", type="string", description="tipo do usuário: customer ou seller"),
     *             @OA\Property(property="cpf", type="string", description="documento necessário caso type seja customer"),
     *             @OA\Property(property="cnpj", type="string", description="documento necessário caso type seja seller"),
     *       )
     *     ),
     *     @OA\Response(
     *         response="201", description="New user created"
     *     )
     * )
     *
     * Handle the registration process.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->setTypeableRules($request);

        $validatedFields = $this->validate($request, $this->rules);
        $validatedFields['password'] = bcrypt($validatedFields['password']);

        $type = '\\App\\Models\\' . ucfirst($validatedFields['type']);
        $typeable = $type::create($validatedFields);

        $user = User::create($validatedFields + [
            'typeable_id' => $typeable->id,
            'typeable_type' => get_class($typeable)
        ]);

        $data['token'] = $user->createToken('MyApp')->plainTextToken;
        $data['user'] = $user->refresh();

        return response()->json(['data' => $data], Response::HTTP_CREATED);
    }

    /**
     *  @OA\Post(
     *     tags={"Auth"},
     *     summary="Store a newly created resource in storage.",
     *     description="create a new user",
     *     path="/login",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *       )
     *     ),
     *     @OA\Response(
     *         response="200", description="User Authenticated"
     *     )
     * )
     *
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
                'authentication' => 'E-mail or password is wrong.'
            ]
        ];

        return response()->json($data, Response::HTTP_UNAUTHORIZED);
    }

    private function setTypeableRules(Request $request)
    {
        $type = '\\App\\Models\\' . ucfirst($request['type']);

        switch ($type) {
            case '\\App\\Models\\Customer':
                $this->rules += ['cpf' => 'required|cpf|formato_cpf'];
                break;
            case '\\App\\Models\\Seller':
                $this->rules += ['cnpj' => 'required|cnpj|formato_cnpj'];
                break;
            default:
                return null;
                break;
        }
    }
}
