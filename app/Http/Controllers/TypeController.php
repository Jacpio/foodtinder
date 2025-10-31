<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *   schema="Type",
 *   type="object",
 *   required={"id","name"},
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="cuisine"),
 *   @OA\Property(property="parameters_count", type="integer", nullable=true, example=7),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *   schema="PaginatedTypes",
 *   type="object",
 *   @OA\Property(property="current_page", type="integer", example=1),
 *   @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Type")),
 *   @OA\Property(property="first_page_url", type="string"),
 *   @OA\Property(property="from", type="integer", nullable=true),
 *   @OA\Property(property="last_page", type="integer"),
 *   @OA\Property(property="last_page_url", type="string"),
 *   @OA\Property(
 *     property="links",
 *     type="array",
 *     @OA\Items(type="object",
 *       @OA\Property(property="url", type="string", nullable=true),
 *       @OA\Property(property="label", type="string"),
 *       @OA\Property(property="active", type="boolean")
 *     )
 *   ),
 *   @OA\Property(property="next_page_url", type="string", nullable=true),
 *   @OA\Property(property="path", type="string"),
 *   @OA\Property(property="per_page", type="integer", example=10),
 *   @OA\Property(property="prev_page_url", type="string", nullable=true),
 *   @OA\Property(property="to", type="integer", nullable=true),
 *   @OA\Property(property="total", type="integer")
 * )
 */
class TypeController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/types",
     *   tags={"Types"},
     *   summary="List types (paginated)",
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(
     *     name="per_page", in="query", required=false,
     *     description="Items per page (1–100; default 10)",
     *     @OA\Schema(type="integer", minimum=1, maximum=100)
     *   ),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PaginatedTypes"))
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'per_page' => 'sometimes|integer|min:1|max:100',
        ]);

        $perPage = (int)($validated['per_page'] ?? 10);

        $page = Type::query()
            ->withCount('parameters')
            ->orderBy('name')
            ->paginate($perPage);

        return response()->json($page);
    }

    /**
     * @OA\Get(
     *   path="/api/types/{id}",
     *   tags={"Types"},
     *   summary="Get single type",
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Type")),
     *   @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $type = Type::withCount('parameters')->find($id);
        if (! $type) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($type);
    }

    /**
     * @OA\Post(
     *   path="/api/types",
     *   tags={"Types"},
     *   summary="Create a new type",
     *   security={{"bearerAuth": {}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"name"},
     *       @OA\Property(property="name", type="string", example="flavour")
     *     )
     *   ),
     *   @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/Type")),
     *   @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:255','unique:types,name'],
        ]);

        $type = Type::create($data);

        return response()->json($type, 201);
    }

    /**
     * @OA\Put(
     *   path="/api/types/{id}",
     *   tags={"Types"},
     *   summary="Update a type",
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(
     *     required=false,
     *     @OA\JsonContent(@OA\Property(property="name", type="string", example="category"))
     *   ),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Type")),
     *   @OA\Response(response=404, description="Not Found"),
     *   @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $type = Type::find($id);
        if (! $type) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $data = $request->validate([
            'name' => ['sometimes','string','max:255', Rule::unique('types','name')->ignore($type->id)],
        ]);

        $type->update($data);

        return response()->json($type->fresh()->loadCount('parameters'));
    }

    /**
     * @OA\Delete(
     *   path="/api/types/{id}",
     *   tags={"Types"},
     *   summary="Delete a type",
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Success"))),
     *     @OA\Response(response=404, description="Not Found"),
     *   @OA\Response(response=409, description="Conflict (type has parameters)")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $type = Type::withCount('parameters')->find($id);
        if (! $type) {
            return response()->json(['message' => 'Not found'], 404);
        }

        if ($type->parameters_count > 0) {
            return response()->json([
                'message' => 'Cannot delete: type is referenced by parameters.'
            ], 409);
        }

        $type->delete();

        return response()->json(['message' => 'Success'], 200);
    }
}
