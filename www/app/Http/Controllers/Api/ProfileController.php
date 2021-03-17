<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class ProfileController extends Controller
{
    /**
     * @OA\Get(
     *      path="/profile",
     *      summary="Retrieve profile information",
     *      description="Get profile information",
     *      tags={"Profile"},
     *      security={{"bearerAuth": {}}},
     *      @OA\Response(
     *          response="200", description="Get the authenticated user"
     *      ),
     * )
     *
     * Display the authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $user->wallet;
        return response()->json(['data' => $user], Response::HTTP_OK);
    }
}
