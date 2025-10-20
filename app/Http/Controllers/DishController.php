<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDishRequest;
use App\Http\Requests\UpdateDishRequest;
use App\Http\Resources\DishResource;
use App\Models\Dish;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;

class DishController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/dishes",
     *   tags={"Dishes"},
     *   summary="Lista potraw",
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Dish"))
     *   )
     * )
     */
    public function index(): JsonResponse
    {
        return response()->json(Dish::with(['category', 'cuisine', 'flavour'])->get());
    }
    /**
     * @OA\Get(
     *   path="/api/dishes/{id}",
     *   tags={"Dishes"},
     *   summary="Pokaż jedną potrawę",
     *   @OA\Parameter(
     *     name="id", in="path", required=true, @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(ref="#/components/schemas/Dish")
     *   ),
     *   @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $dish = Dish::with(['category', 'cuisine', 'flavour'])->find($id);
        if (!$dish) {
            return response()->json([]);
        }
        return response()->json($dish);
    }
    /**
     * @OA\Post(
     *   path="/api/dishes",
     *   tags={"Dishes"},
     *   summary="Utwórz nową potrawę",
     *   security={{"bearerAuth": {}}},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Użyj multipart/form-data, obrazek jest opcjonalny",
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         required={"name","category_id","cuisine_id","flavour_id"},
     *         @OA\Property(property="name", type="string", example="Pizza Margherita"),
     *         @OA\Property(property="category_id", type="integer", example=1),
     *         @OA\Property(property="cuisine_id", type="integer", example=2),
     *         @OA\Property(property="flavour_id", type="integer", example=3),
     *         @OA\Property(property="description", type="string", nullable=true, example="Klasyk"),
     *         @OA\Property(
     *           property="image", type="string", format="binary", nullable=true,
     *           description="Obrazek (opcjonalnie). Zapisujesz w root dysku 'public'."
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Utworzono",
     *     @OA\JsonContent(ref="#/components/schemas/Dish")
     *   ),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ErrorResponse"))
     * )
     */
    public function create(StoreDishRequest $request): JsonResponse{
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('','public');
            $data['image_url'] = $path;
        }

        $dish = Dish::create($data)->load(['category','cuisine','flavour']);
        return (new DishResource($dish))->response()->setStatusCode(201);
    }
    /**
     * @OA\Put(
     *   path="/api/dishes/{id}",
     *   tags={"Dishes"},
     *   summary="Zaktualizuj potrawę",
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(
     *     required=false,
     *     description="Wspiera JSON lub multipart/form-data. W JSON obrazka nie wyślesz.",
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="category_id", type="integer"),
     *         @OA\Property(property="cuisine_id", type="integer"),
     *         @OA\Property(property="flavour_id", type="integer"),
     *         @OA\Property(property="description", type="string", nullable=true),
     *         @OA\Property(property="remove_image", type="boolean", example=false, description="Ustaw true, by usunąć istniejący obrazek")
     *       )
     *     ),
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="category_id", type="integer"),
     *         @OA\Property(property="cuisine_id", type="integer"),
     *         @OA\Property(property="flavour_id", type="integer"),
     *         @OA\Property(property="description", type="string", nullable=true),
     *         @OA\Property(property="remove_image", type="boolean", example=false),
     *         @OA\Property(property="image", type="string", format="binary", nullable=true)
     *       )
     *     )
     *   ),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Dish")),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=404, description="Not Found"),
     *   @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ErrorResponse"))
     * )
     */
    public function update(UpdateDishRequest $request, int $id): JsonResponse{
        $data = $request->validated();

        $dish = Dish::find($id);
        if (!$dish) {
            return response()->json(['message' => 'Not found'], 404);
        }
        if (!empty($data['remove_image']) && $dish->image_url) {
            Storage::disk('public')->delete($dish->image_url);
            $data['image_url'] = null;
        }

        if ($request->hasFile('image')) {
            if ($dish->image_url) {
                Storage::disk('public')->delete($dish->image_url);
            }
            $data['image_url'] = $request->file('image')->store('', 'public');
        }

        $dish->update($data);
        $dish->load(['category','cuisine','flavour']);

        return (new DishResource($dish))->response();
    }
    /**
     * @OA\Delete(
     *   path="/api/dishes/{id}",
     *   tags={"Dishes"},
     *   summary="Usuń potrawę",
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(type="object", properties={
     *       @OA\Property(property="message", type="string", example="Success")
     *     })
     *   ),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ErrorResponse"))
     * )
     */
    public function destroy($id): JsonResponse{
        try {
            $validated =  \Validator::validate(['id' => $id], [
                'id' => 'required|exists:dishes,id',
            ]);
        }catch (ValidationException $e){
            return response()->json(['message' => 'The given data was invalid.'], 422);
        }
        Dish::find($validated['id'])->delete();
        return response()->json(['message' => 'Success'], 200);
    }
}
