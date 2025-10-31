<?php

namespace App\Http\Controllers;

use App\Http\Requests\CSVRequest;
use App\Models\Dish;
use App\Services\ImportCSVDish;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;

class DishController extends Controller
{
    public function __construct(private readonly ImportCSVDish $CSVDish) {}

    /**
     * @OA\Get(
     *   path="/api/dish",
     *   tags={"Dishes"},
     *   security={{"bearerAuth": {}}},
     *   summary="List dishes (with parameters)",
     *   description="Returns a paginated list of dishes with their attached parameters.",
     *   @OA\Parameter(
     *     name="per_page", in="query", required=false,
     *     description="Pagination size (1–100, default 10)",
     *     @OA\Schema(type="integer", minimum=1, maximum=100, default=10)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Paginated list of dishes",
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
     *           @OA\Property(property="is_vegan", type="boolean", example=false),
     *           @OA\Property(
     *             property="parameters",
     *             type="array",
     *             @OA\Items(
     *               type="object",
     *               @OA\Property(property="id", type="integer", example=12),
     *               @OA\Property(property="name", type="string", example="Italian"),
     *               @OA\Property(property="type_id", type="integer", example=2),
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
            $perPage = (int)($validated['per_page'] ?? 10);

            // Tests expect only type_id on parameters (no nested type relation here).
            $page = Dish::with('parameters')->paginate($perPage);

            return response()->json($page);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'The given data was invalid.', 'errors' => $e->errors()], 422);
        }
    }

    /**
     * @OA\Get(
     *   path="/api/dish/{id}",
     *   tags={"Dishes"},
     *   security={{"bearerAuth": {}}},
     *   summary="Show a single dish (with parameters)",
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
     *       @OA\Property(property="is_vegan", type="boolean", example=false),
     *       @OA\Property(
     *         property="parameters",
     *         type="array",
     *         @OA\Items(
     *           type="object",
     *           @OA\Property(property="id", type="integer", example=12),
     *           @OA\Property(property="name", type="string", example="Italian"),
     *           @OA\Property(property="type_id", type="integer", example=2),
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
     *   path="/api/dish",
     *   tags={"Dishes"},
     *   summary="Create a new dish",
     *   security={{"bearerAuth": {}}},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Supports JSON and multipart/form-data. Include `parameter_ids[]` to attach parameters.",
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *         required={"name"},
     *         @OA\Property(property="name", type="string", example="Pizza Margherita"),
     *         @OA\Property(property="description", type="string", nullable=true, example="Classic"),
     *         @OA\Property(property="is_vegan", type="boolean", example=false),
     *         @OA\Property(
     *           property="parameter_ids",
     *           type="array",
     *           @OA\Items(type="integer", example=1),
     *           description="IDs of parameters to attach"
     *         )
     *       )
     *     ),
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         required={"name"},
     *         @OA\Property(property="name", type="string", example="Pizza Margherita"),
     *         @OA\Property(property="description", type="string", nullable=true, example="Classic"),
     *         @OA\Property(property="is_vegan", type="boolean", example=false),
     *         @OA\Property(
     *           property="parameter_ids[]",
     *           type="array",
     *           @OA\Items(type="integer", example=1),
     *           description="IDs of parameters to attach"
     *         ),
     *         @OA\Property(
     *           property="image", type="string", format="binary", nullable=true,
     *           description="Optional image. Stored on the 'public' disk."
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Created",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="id", type="integer", example=101),
     *       @OA\Property(property="name", type="string", example="Pizza Margherita"),
     *       @OA\Property(property="description", type="string", nullable=true),
     *       @OA\Property(property="image_url", type="string", nullable=true),
     *       @OA\Property(property="is_vegan", type="boolean", example=false),
     *       @OA\Property(
     *         property="parameters",
     *         type="array",
     *         @OA\Items(
     *           type="object",
     *           @OA\Property(property="id", type="integer"),
     *           @OA\Property(property="name", type="string"),
     *           @OA\Property(property="type_id", type="integer")
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'image'          => 'nullable|file|image|max:5120',
            'is_vegan'       => 'nullable|boolean',
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
     *   path="/api/dish/{id}",
     *   tags={"Dishes"},
     *   summary="Update a dish",
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(
     *     required=false,
     *     description="Supports JSON and multipart/form-data. Use `parameter_ids` to replace attached parameters.",
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="description", type="string", nullable=true),
     *         @OA\Property(property="is_vegan", type="boolean", example=false),
     *         @OA\Property(property="remove_image", type="boolean", example=false),
     *         @OA\Property(
     *           property="parameter_ids",
     *           type="array",
     *           @OA\Items(type="integer"),
     *           description="Set of parameter IDs to sync (overwrites previous)"
     *         )
     *       )
     *     ),
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="description", type="string", nullable=true),
     *         @OA\Property(property="is_vegan", type="boolean", example=false),
     *         @OA\Property(property="remove_image", type="boolean", example=false),
     *         @OA\Property(
     *           property="parameter_ids[]",
     *           type="array",
     *           @OA\Items(type="integer"),
     *           description="Set of parameter IDs to sync (overwrites previous)"
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
            'is_vegan'       => 'sometimes|boolean',
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

        foreach (['is_vegan', 'name', 'description'] as $field) {
            if (array_key_exists($field, $data)) {
                $dish->{$field} = $data[$field];
            }
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
     *   path="/api/dish/{id}",
     *   tags={"Dishes"},
     *   summary="Delete a dish",
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
            \Validator::validate(['id' => $id], [
                'id' => 'required|exists:dishes,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'The given data was invalid.', 'errors' => $e->errors()], 422);
        }

        $dish = Dish::find($id);

        if (method_exists($dish, 'parameters')) {
            $dish->parameters()->detach();
        }

        if ($dish->image_url) {
            Storage::disk('public')->delete($dish->image_url);
        }

        $dish->delete();

        return response()->json(['message' => 'Success'], 200);
    }

    /**
     * @OA\Post(
     *   path="/api/dish/import-csv",
     *   tags={"Dishes"},
     *   summary="Import dishes from CSV",
     *   description="Imports dishes from a CSV file.",
     *   security={{"bearerAuth": {}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         type="object",
     *         required={"file"},
     *         @OA\Property(
     *           property="file",
     *           type="string",
     *           format="binary",
     *           description="CSV file (headers: id,name,image_url,description,is_vegan)"
     *         ),
     *         @OA\Property(
     *           property="delimiter",
     *           type="string",
     *           description="Allowed: comma(,), semicolon(;), pipe(|), tab(\\t). Default: comma",
     *           enum={"comma","semicolon","pipe","tab"},
     *           default="comma"
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(ref="#/components/schemas/MessageResponse")
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Bad data (validation or invalid CSV)",
     *     @OA\JsonContent(ref="#/components/schemas/MessageResponse")
     *   ),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function importCSV(CSVRequest $request): JsonResponse
    {
        $data     = $request->validated();
        $uploaded = $request->file('file');

        $status = $this->CSVDish->createDishByFile($uploaded, $data);

        if (!$status) {
            return response()->json(['message' => 'Bad data'], 422);
        }

        return response()->json(['message' => 'Success'], 200);
    }
}
