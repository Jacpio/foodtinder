<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use OpenApi\Annotations as OA;

class VerifyEmailController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/verify-email/{id}/{hash}",
     *   tags={"Auth"},
     *   summary="Verify email address",
     *   description="Marks the authenticated user's email as verified.",
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Parameter(name="hash", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\Response(response=302, description="Email already verified"),
     *   @OA\Response(response=200, description="Email successfully verified"),
     *   @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function __invoke(EmailVerificationRequest $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json('Email already verified', 302);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return response()->json('Email successfully verified');
    }
}
