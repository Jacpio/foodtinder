<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *   @OA\Info(title="FoodTinder API", version="1.0.0", description="API do logowania, swipe’ów i rekomendacji dań. Po prostu Tinder dla jedzenia!"),
 *   @OA\Server(url="http://localhost:8000", description="Local")
 * )
 * @OA\SecurityScheme(
 *   securityScheme="bearerAuth",
 *   type="http",
 *   scheme="bearer",
 *   bearerFormat="Token"
 * )
 */
final class OpenApi {}
