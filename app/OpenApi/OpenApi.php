<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *   @OA\Info(title="FoodTinder API", version="1.0.0", description="API do logowania, swipe’ów i rekomendacji dań."),
 *   @OA\Server(url="http://localhost", description="Local")
 * )
 * @OA\SecurityScheme(
 *   securityScheme="bearerAuth",
 *   type="http",
 *   scheme="bearer",
 *   bearerFormat="Token"
 * )
 */
final class OpenApi {}
