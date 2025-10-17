<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *   schema="Dish",
 *   type="object",
 *   required={"id","name","category_id","cuisine_id","flavour_id"},
 *   @OA\Property(property="id", type="integer", example=101),
 *   @OA\Property(property="name", type="string", example="Spaghetti Carbonara"),
 *   @OA\Property(property="category_id", type="integer", example=1),
 *   @OA\Property(property="cuisine_id", type="integer", example=3),
 *   @OA\Property(property="flavour_id", type="integer", example=5),
 *   @OA\Property(property="image_url", type="string", nullable=true, example="spaghetti.jpg"),
 *   @OA\Property(property="image_url_full", type="string", example="http://localhost/storage/spaghetti.jpg"),
 *   @OA\Property(property="description", type="string", nullable=true, example="Classic Roman pasta.")
 * )
 *
 * @OA\Schema(
 *   schema="DishWithScore",
 *   allOf={
 *     @OA\Schema(ref="#/components/schemas/Dish"),
 *     @OA\Schema(
 *       type="object",
 *       @OA\Property(property="match_score", type="number", format="float", example=0.93)
 *     )
 *   }
 * )
 *
 * @OA\Schema(
 *   schema="SwipeDecisionRequest",
 *   type="object",
 *   required={"dish_id","decision"},
 *   @OA\Property(property="dish_id", type="integer", example=101),
 *   @OA\Property(property="decision", type="string", enum={"like","dislike"})
 * )
 */
final class Schemas {}
