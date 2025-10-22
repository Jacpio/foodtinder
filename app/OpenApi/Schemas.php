<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *   schema="Dish",
 *   type="object",
 *   required={"id","name"},
 *   @OA\Property(property="id", type="integer", example=101),
 *   @OA\Property(property="name", type="string", example="Spaghetti Carbonara"),
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
 *   schema="ErrorResponse",
 *   type="object",
 *   @OA\Property(property="message", type="string", example="The given data was invalid.")
 * )
 *
 * @OA\Schema(
 *   schema="SwipeDecisionRequest",
 *   type="object",
 *   required={"dish_id","decision"},
 *   @OA\Property(property="dish_id", type="integer", example=101),
 *   @OA\Property(property="decision", type="string", enum={"like","dislike"})
 * )
 *
 * @OA\Schema(
 *   schema="MessageResponse",
 *   type="object",
 *   required={"message"},
 *   @OA\Property(property="message", type="string", example="Success")
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
 *     additionalProperties=@OA\Schema(type="array", @OA\Items(type="string")),
 *     example={"name":{"The name field is required."}}
 *   )
 * )
 */
final class Schemas {}
