<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

class AuthenticatedSessionController extends Controller
{
    /**
     * @OA\Post(
     *   path="api/login",
     *   summary="Logowanie",
     *   tags={"Auth"},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *      type="array",
     *      @OA\Items(ref="#/components/schemas/LoginRequest")
     * )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(ref="#/components/schemas/AuthTokenResponse")
     *   )
     * )
     */
    public function store(LoginRequest $request): JsonResponse
    {

        $request->authenticate();

        $token = $request->user()->createToken('api_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $request->user(),
        ]);
    }

    /**
     * @OA\Post(
     *   path="api/logout",
     *   tags={"Auth"},
     *   summary="Wylogowanie (inwalidacja tokenu)",
     *   security={{"bearerAuth": {}}},
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/MessageResponse")),
     *   @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }
}
