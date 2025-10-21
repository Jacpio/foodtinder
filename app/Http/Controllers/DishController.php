<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;

class DishController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/dishes",
     *   tags={"Dishes"},
     *   security={{"bearerAuth": {}}},
     *   summary="Lista potraw (z parametrami)",
     *   @OA\Parameter(
     *     name="per_page", in="query", required=false,
     *     description="Paginacja (1–100, domyślnie 10)",
     *     @OA\Schema(type="integer", minimum=1, maximum=100, default=10)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK (paginacja Laravel: data + meta + links)",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="data",
     *         type="array",
     *         @OA\Items(
     *           type="object",
     *           @OA\Property(property="id", type="integer", example=1),
     *           @OA\Property(property="name", type="string", example="Spaghetti Bolognese"),
     *           @OA\Property(property="description", type="string", nullable=true),
     *           @OA\Property(property="image_url", type="string", nullable=true, example="spaghetti.jpg"),
     *           @OA\Property(
     *             property="parameters",
     *             type="array",
     *             @OA\Items(
     *               type="object",
     *               @OA\Property(property="id", type="integer", example=12),
     *               @OA\Property(property="name", type="string", example="Włoska"),
     *               @OA\Property(property="type", type="string", enum={"category","cuisine","flavour","other"}, example="cuisine"),
     *               @OA\Property(property="value", type="number", format="float", example=1),
     *               @OA\Property(property="is_active", type="boolean", example=true)
     *             )
     *           ),
     *           @OA\Property(property="created_at", type="string", format="date-time"),
     *           @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *       )
     *     )
     *   )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $validated = \Validator::validate($request->all(), [
                'per_page' => 'integer|min:1|max:100|nullable',
            ]);
            $perPage = $validated['per_page'] ?? 10;

            $page = Dish::with('parameters')->paginate((int)$perPage);

            return response()->json($page);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation failed'], 422);
        }
    }

    /**
     * @OA\Get(
     *   path="/api/dishes/{id}",
     *   tags={"Dishes"},
     *   security={{"bearerAuth": {}}},
     *   summary="Pokaż jedną potrawę (z parametrami)",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="id", type="integer", example=1),
     *       @OA\Property(property="name", type="string", example="Spaghetti Bolognese"),
     *       @OA\Property(property="description", type="string", nullable=true),
     *       @OA\Property(property="image_url", type="string", nullable=true, example="spaghetti.jpg"),
     *       @OA\Property(
     *         property="parameters",
     *         type="array",
     *         @OA\Items(
     *           type="object",
     *           @OA\Property(property="id", type="integer", example=12),
     *           @OA\Property(property="name", type="string", example="Włoska"),
     *           @OA\Property(property="type", type="string", enum={"category","cuisine","flavour","other"}, example="cuisine"),
     *           @OA\Property(property="value", type="number", format="float", example=1),
     *           @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *       ),
     *       @OA\Property(property="created_at", type="string", format="date-time"),
     *       @OA\Property(property="updated_at", type="string", format="date-time")
     *     )
     *   ),
     *   @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $dish = Dish::with('parameters')->find($id);
        if (!$dish) {
            return response()->json(['message' => 'Not found'], 404);
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
     *     description="Wspiera JSON oraz multipart/form-data. Dołącz `parameter_ids[]` aby przypiąć parametry.",
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *         required={"name"},
     *         @OA\Property(property="name", type="string", example="Pizza Margherita"),
     *         @OA\Property(property="description", type="string", nullable=true, example="Klasyk"),
     *         @OA\Property(
     *           property="parameter_ids",
     *           type="array",
     *           @OA\Items(type="integer", example=1),
     *           description="ID parametrów do przypięcia (category/cuisine/flavour/other)"
     *         )
     *       )
     *     ),
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         required={"name"},
     *         @OA\Property(property="name", type="string", example="Pizza Margherita"),
     *         @OA\Property(property="description", type="string", nullable=true, example="Klasyk"),
     *         @OA\Property(
     *           property="parameter_ids[]",
     *           type="array",
     *           @OA\Items(type="integer", example=1),
     *           description="ID parametrów do przypięcia"
     *         ),
     *         @OA\Property(
     *           property="image", type="string", format="binary", nullable=true,
     *           description="Obrazek (opcjonalnie). Zapis w dysku 'public'."
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Utworzono",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="id", type="integer", example=101),
     *       @OA\Property(property="name", type="string", example="Pizza Margherita"),
     *       @OA\Property(property="description", type="string", nullable=true),
     *       @OA\Property(property="image_url", type="string", nullable=true),
     *       @OA\Property(
     *         property="parameters",
     *         type="array",
     *         @OA\Items(
     *           type="object",
     *           @OA\Property(property="id", type="integer"),
     *           @OA\Property(property="name", type="string"),
     *           @OA\Property(property="type", type="string", enum={"category","cuisine","flavour","other"})
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=422, description="Validation error")
     * )
     */
    public function create(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'image'          => 'nullable|file|image|max:5120',
            'parameter_ids'  => 'nullable|array',
            'parameter_ids.*'=> 'integer|distinct|exists:parameters,id',
        ]);

        if ($request->hasFile('image')) {
            $data['image_url'] = $request->file('image')->store('', 'public');
        }

        $parameterIds = $data['parameter_ids'] ?? [];
        unset($data['parameter_ids'], $data['image']);

        $dish = Dish::create($data);

        if (!empty($parameterIds)) {
            $dish->parameters()->sync($parameterIds);
        }

        $dish->load('parameters');

        return response()->json($dish, 201);
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
     *     description="Wspiera JSON oraz multipart/form-data. Użyj `parameter_ids` aby podmienić przypięte parametry.",
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="description", type="string", nullable=true),
     *         @OA\Property(property="remove_image", type="boolean", example=false),
     *         @OA\Property(
     *           property="parameter_ids",
     *           type="array",
     *           @OA\Items(type="integer"),
     *           description="Zestaw parametrów do zsynchronizowania (nadpisuje poprzednie)"
     *         )
     *       )
     *     ),
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="description", type="string", nullable=true),
     *         @OA\Property(property="remove_image", type="boolean", example=false),
     *         @OA\Property(
     *           property="parameter_ids[]",
     *           type="array",
     *           @OA\Items(type="integer"),
     *           description="Zestaw parametrów do zsynchronizowania (nadpisuje poprzednie)"
     *         ),
     *         @OA\Property(property="image", type="string", format="binary", nullable=true)
     *       )
     *     )
     *   ),
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=404, description="Not Found"),
     *   @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $dish = Dish::find($id);
        if (!$dish) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $data = $request->validate([
            'name'           => 'sometimes|string|max:255',
            'description'    => 'sometimes|nullable|string',
            'remove_image'   => 'sometimes|boolean',
            'image'          => 'sometimes|nullable|file|image|max:5120',
            'parameter_ids'  => 'sometimes|array',
            'parameter_ids.*'=> 'integer|distinct|exists:parameters,id',
        ]);


        if (!empty($data['remove_image']) && $dish->image_url) {
            Storage::disk('public')->delete($dish->image_url);
            $dish->image_url = null;
        }

        if ($request->hasFile('image')) {
            if ($dish->image_url) {
                Storage::disk('public')->delete($dish->image_url);
            }
            $dish->image_url = $request->file('image')->store('', 'public');
        }

        if (array_key_exists('name', $data)) {
            $dish->name = $data['name'];
        }
        if (array_key_exists('description', $data)) {
            $dish->description = $data['description'];
        }

        $dish->save();

        if (array_key_exists('parameter_ids', $data)) {
            $dish->parameters()->sync($data['parameter_ids'] ?? []);
        }

        $dish->load('parameters');

        return response()->json($dish);
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
     *     @OA\JsonContent(type="object",
     *       @OA\Property(property="message", type="string", example="Success")
     *     )
     *   ),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=422, description="Validation error")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $validated = \Validator::validate(['id' => $id], [
                'id' => 'required|exists:dishes,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'The given data was invalid.'], 422);
        }

        $dish = Dish::find($validated['id']);
        if ($dish->image_url) {
            Storage::disk('public')->delete($dish->image_url);
        }
        $dish->delete();

        return response()->json(['message' => 'Success'], 200);
    }
}
