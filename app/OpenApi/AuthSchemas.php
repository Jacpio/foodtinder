<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *   schema="AuthUser",
 *   type="object",
 *   required={"id","name","email"},
 *   @OA\Property(property="id", type="integer", example=7),
 *   @OA\Property(property="name", type="string", example="Jan Kowalski"),
 *   @OA\Property(property="email", type="string", format="email", example="jan@example.com"),
 *   @OA\Property(property="email_verified_at", type="string", format="date-time", example="2025-10-17T19:52:15.000000Z")
 * )
 *
 * @OA\Schema(
 *   schema="AuthTokenResponse",
 *   type="object",
 *   required={"token","user"},
 *   @OA\Property(property="token", type="string", example="1|abcdefgh..."),
 *   @OA\Property(property="user", ref="#/components/schemas/AuthUser")
 * )
 *
 * @OA\Schema(
 *   schema="LoginRequest",
 *   type="object",
 *   required={"email","password"},
 *   @OA\Property(property="email", type="string", format="email", example="jan@example.com"),
 *   @OA\Property(property="password", type="string", format="password", example="secret123")
 * )
 *
 * @OA\Schema(
 *   schema="RegisterRequest",
 *   type="object",
 *   required={"name","email","password","password_confirmation"},
 *   @OA\Property(property="name", type="string", example="Jan Kowalski"),
 *   @OA\Property(property="email", type="string", format="email", example="jan@example.com"),
 *   @OA\Property(property="password", type="string", format="password", example="secret123"),
 *   @OA\Property(property="password_confirmation", type="string", format="password", example="secret123")
 * )
 *
 * @OA\Schema(
 *   schema="EmailOnlyRequest",
 *   type="object",
 *   required={"email"},
 *   @OA\Property(property="email", type="string", format="email", example="jan@example.com")
 * )
 *
 * @OA\Schema(
 *   schema="ResetPasswordRequest",
 *   type="object",
 *   required={"email","token","password","password_confirmation"},
 *   @OA\Property(property="token", type="string", example="reset-token"),
 *   @OA\Property(property="email", type="string", format="email", example="jan@example.com"),
 *   @OA\Property(property="password", type="string", format="password", example="newPass123"),
 *   @OA\Property(property="password_confirmation", type="string", format="password", example="newPass123")
 * )
 *
 * @OA\Schema(
 *   schema="MessageResponse",
 *   type="object",
 *   @OA\Property(property="message", type="string", example="Successfully logged out")
 * )
 *
 * @OA.Schema(
 *   schema="StatusResponse",
 *   type="object",
 *   @OA\Property(property="status", type="string", example="verification-link-sent")
 * )
 *
 * @OA\Schema(
 *   schema="ValidationError",
 *   type="object",
 *   required={"message","errors"},
 *   @OA\Property(property="message", type="string", example="The given data was invalid."),
 *   @OA\Property(
 *     property="errors",
 *     type="object",
 *     additionalProperties=@OA\Schema(
 *       type="array",
 *       @OA\Items(type="string", example="The field is required.")
 *     )
 *   )
 * )
 */
final class AuthSchemas {}
