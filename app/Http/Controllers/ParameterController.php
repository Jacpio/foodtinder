<?php

namespace App\Http\Controllers;

use App\Http\Requests\CSVRequest;
use App\Models\Parameter;
use App\Services\ImportCSVParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *   schema="Parameter",
 *   type="object",
 *   properties={
 *     @OA\Property(property="id", type="integer", example=12),
 *     @OA\Property(property="name", type="string", example="Włoska"),
 *     @OA\Property(property="type", type="string", enum={"category","cuisine","flavour","other"}, example="cuisine"),
 *     @OA\Property(property="value", type="number", format="float", example=1),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 *   }
 * )
 *
 * @OA\Schema(
 *   schema="PaginatedParameters",
 *   type="object",
 *   properties={
 *     @OA\Property(property="current_page", type="integer", example=1),
 *     @OA\Property(
 *       property="data",
 *       type="array",
 *       @OA\Items(ref="#/components/schemas/Parameter")
 *     ),
 *     @OA\Property(property="first_page_url", type="string", example="http://localhost/api/parameters?page=1"),
 *     @OA\Property(property="from", type="integer", nullable=true, example=1),
 *     @OA\Property(property="last_page", type="integer", example=3),
 *     @OA\Property(property="last_page_url", type="string", example="http://localhost/api/parameters?page=3"),
 *     @OA\Property(
 *       property="links",
 *       type="array",
 *       @OA\Items(type="object",
 *         @OA\Property(property="url", type="string", nullable=true),
 *         @OA\Property(property="label", type="string"),
 *         @OA\Property(property="active", type="boolean")
 *       )
 *     ),
 *     @OA\Property(property="next_page_url", type="string", nullable=true),
 *     @OA\Property(property="path", type="string", example="http://localhost/api/parameters"),
 *     @OA\Property(property="per_page", type="integer", example=10),
 *     @OA\Property(property="prev_page_url", type="string", nullable=true),
 *     @OA\Property(property="to", type="integer", nullable=true, example=10),
 *     @OA\Property(property="total", type="integer", example=25)
 *   }
 * )
 */
class ParameterController extends Controller
{
    public function __construct(private readonly ImportCSVParameter $CSVParameter)
    {}

    /**
     * @OA\Get(
     *   path="/api/parameters",
     *   tags={"Parameters"},
     *   security={{"bearerAuth": {}}},
     *   summary="Lista parametrów (paginowana)",
     *   @OA\Parameter(
     *     name="per_page", in="query", required=false,
     *     description="Ilość na stronę (1–10, domyślnie 10)",
     *     @OA\Schema(type="integer", minimum=1, maximum=10, default=10)
     *   ),
     *   @OA\Parameter(
     *     name="page", in="query", required=false,
     *     description="Numer strony (>=1, domyślnie 1)",
     *     @OA\Schema(type="integer", minimum=1, default=1)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(ref="#/components/schemas/PaginatedParameters")
     *   )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'per_page' => 'sometimes|integer|min:1|max:10',
            'page'     => 'sometimes|integer|min:1',
        ]);

        $perPage = (int)($validated['per_page'] ?? 10);

        $page = Parameter::query()
            ->orderBy('type')
            ->orderBy('name')
            ->paginate($perPage);

        return response()->json($page);
    }

    /**
     * @OA\Get(
     *   path="/api/parameters/{id}",
     *   tags={"Parameters"},
     *   security={{"bearerAuth": {}}},
     *   summary="Szczegóły parametru",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Parameter")),
     *   @OA\Response(response=404, description="Not Found",
     *     @OA\JsonContent(ref="#/components/schemas/MessageResponse"))
     * )
     */
    public function show(int $id): JsonResponse
    {
        $parameter = Parameter::find($id);
        if (!$parameter) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($parameter);
    }

    /**
     * @OA\Post(
     *   path="/api/parameters",
     *   tags={"Parameters"},
     *   summary="Utwórz nowy parametr",
     *   security={{"bearerAuth": {}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"name","type"},
     *       @OA\Property(property="name", type="string", example="Włoska"),
     *       @OA\Property(property="type", type="string", enum={"category","cuisine","flavour","other"}, example="cuisine"),
     *       @OA\Property(property="value", type="number", format="float", example=1),
     *       @OA\Property(property="is_active", type="boolean", example=true)
     *     )
     *   ),
     *   @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/Parameter")),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=403, description="Forbidden"),
     *   @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'      => ['required','string','max:255','unique:parameters,name'],
            'type'      => ['required', Rule::in(['category','cuisine','flavour','other'])],
            'value'     => ['nullable','numeric'],
            'is_active' => ['sometimes','boolean'],
        ]);

        $data['value'] ??= 1;
        $data['is_active'] ??= true;

        $parameter = Parameter::create($data);

        return response()->json($parameter, 201);
    }

    /**
     * @OA\Put(
     *   path="/api/parameters/{id}",
     *   tags={"Parameters"},
     *   summary="Aktualizuj parametr",
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(
     *     required=false,
     *     @OA\JsonContent(
     *       @OA\Property(property="name", type="string", example="Polska"),
     *       @OA\Property(property="type", type="string", enum={"category","cuisine","flavour","other"}),
     *       @OA\Property(property="value", type="number", format="float", example=1),
     *       @OA\Property(property="is_active", type="boolean", example=true)
     *     )
     *   ),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Parameter")),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=403, description="Forbidden"),
     *   @OA\Response(response=404, description="Not Found",
     *     @OA\JsonContent(ref="#/components/schemas/MessageResponse")),
     *   @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $parameter = Parameter::find($id);
        if (!$parameter) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $data = $request->validate([
            'name'      => ['sometimes','string','max:255', Rule::unique('parameters','name')->ignore($parameter->id)],
            'type'      => ['sometimes', Rule::in(['category','cuisine','flavour','other'])],
            'value'     => ['sometimes','numeric'],
            'is_active' => ['sometimes','boolean'],
        ]);

        $parameter->update($data);

        return response()->json($parameter);
    }

    /**
     * @OA\Delete(
     *   path="/api/parameters/{id}",
     *   tags={"Parameters"},
     *   summary="Usuń parametr",
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/MessageResponse")),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=403, description="Forbidden"),
     *   @OA\Response(response=404, description="Not Found",
     *     @OA\JsonContent(ref="#/components/schemas/MessageResponse"))
     * )
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $parameter = Parameter::find($id);
        if (!$parameter) {
            return response()->json(['message' => 'Not found'], 404);
        }

        \DB::transaction(function () use ($parameter) {
            if (method_exists($parameter, 'dishes')) {
                $parameter->dishes()->detach();
            }

            if (method_exists($parameter, 'weights')) {
                $parameter->weights()->delete();
            }

            $parameter->delete();
        });


        $parameter->delete();

        return response()->json(['message' => 'Success'], 200);
    }

    /**
     * @OA\Post(
     *   path="/api/parameter/import-csv",
     *   tags={"Parameters"},
     *   summary="Importuj CSV z plików",
     *   description="Importuje dania za pomocą CSV",
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
     *           description="Plik CSV (nagłówki: id,name,type,value, isActive)"
     *         ),
     *         @OA\Property(
     *           property="delimiter",
     *           type="string",
     *           description="Dozwolone: comma(,), semicolon(;), pipe(|), tab(\\t). Default: comma",
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
        $data =  $request->validated();
        $uploaded = $request->file('file');
        $status = $this->CSVParameter->createParameterByFile($uploaded, $data);
        if (!$status) {
            return response()->json(['message' => 'Bad data'], 422);
        }
        return response()->json(['message' => 'Success'], 200);
    }
}
