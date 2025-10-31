<?php

namespace App\Http\Controllers;

use App\Http\Requests\CSVRequest;
use App\Models\Parameter;
use App\Models\Type;
use App\Services\ImportCSVParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;

class ParameterController extends Controller
{
    public function __construct(private readonly ImportCSVParameter $CSVParameter) {}

    /**
     * @OA\Get(
     *   path="/api/parameter",
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
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/PaginatedParameters"))
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
            ->orderBy('name')
            ->paginate($perPage);

        return response()->json($page);
    }

    /**
     * @OA\Get(
     *   path="/api/parameter/{id}",
     *   tags={"Parameters"},
     *   security={{"bearerAuth": {}}},
     *   summary="Szczegóły parametru",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Parameter")),
     *   @OA\Response(response=404, description="Not Found", @OA\JsonContent(ref="#/components/schemas/MessageResponse"))
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
     *   path="/api/parameter",
     *   tags={"Parameters"},
     *   summary="Utwórz nowy parametr",
     *   security={{"bearerAuth": {}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"name"},
     *       @OA\Property(property="name", type="string", example="Zupy"),
     *       @OA\Property(property="type", type="string", enum={"category","cuisine","flavour","other"}, example="category"),
     *       @OA\Property(property="type_id", type="integer", example=1, nullable=true),
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
            'type'      => ['sometimes','string', Rule::in(['category','cuisine','flavour','other'])],
            'type_id'   => ['sometimes','integer','exists:types,id'],
            'value'     => ['nullable','numeric'],
            'is_active' => ['sometimes','boolean'],
        ]);

        if (!isset($data['type_id']) && !isset($data['type'])) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors'  => ['type' => ['The type field is required.']]
            ], 422);
        }

        if (isset($data['type']) && !isset($data['type_id'])) {
            $type = Type::firstOrCreate(['name' => $data['type']]);
            $data['type_id'] = $type->id;
        }

        $data['value']     = $data['value']     ?? 1;
        $data['is_active'] = $data['is_active'] ?? true;

        $parameter = Parameter::create($data);

        return response()->json($parameter, 201);
    }

    /**
     * @OA\Put(
     *   path="/api/parameter/{id}",
     *   tags={"Parameters"},
     *   summary="Aktualizuj parametr",
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="name", type="string"),
     *       @OA\Property(property="type", type="string", enum={"category","cuisine","flavour","other"}),
     *       @OA\Property(property="type_id", type="integer", nullable=true),
     *       @OA\Property(property="value", type="number", format="float"),
     *       @OA\Property(property="is_active", type="boolean")
     *     )
     *   ),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Parameter")),
     *   @OA\Response(response=404, description="Not Found")
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
            'type'      => ['sometimes','string', Rule::in(['category','cuisine','flavour','other'])],
            'type_id'   => ['sometimes','integer','exists:types,id'],
            'value'     => ['sometimes','numeric'],
            'is_active' => ['sometimes','boolean'],
        ]);

        if (isset($data['type']) && !isset($data['type_id'])) {
            $type = Type::firstOrCreate(['name' => $data['type']]);
            $data['type_id'] = $type->id;
        }

        $parameter->update($data);

        return response()->json($parameter);
    }

    /**
     * @OA\Delete(
     *   path="/api/parameter/{id}",
     *   tags={"Parameters"},
     *   summary="Usuń parametr",
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/MessageResponse")),
     *   @OA\Response(response=404, description="Not Found")
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

        return response()->json(['message' => 'Success'], 200);
    }

    /**
     * @OA\Post(
     *   path="/api/parameter/import-csv",
     *   tags={"Parameters"},
     *   summary="Importuj CSV z plików",
     *   security={{"bearerAuth": {}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         type="object",
     *         required={"file"},
     *         @OA\Property(property="file", type="string", format="binary"),
     *         @OA\Property(
     *           property="delimiter",
     *           type="string",
     *           enum={"comma","semicolon","pipe","tab",",",";","|","\t"},
     *           default="comma"
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent(ref="#/components/schemas/MessageResponse")),
     *   @OA\Response(response=422, description="Bad data", @OA\JsonContent(ref="#/components/schemas/MessageResponse"))
     * )
     */
    public function importCSV(CSVRequest $request): JsonResponse
    {
        $data = $request->validated();
        $uploaded = $request->file('file');

        // Zamiana aliasów na rzeczywisty znak delimiter'a (oraz wsparcie gdy przyjdzie już znak)
        $map = [
            'comma'     => ',',
            'semicolon' => ';',
            'pipe'      => '|',
            'tab'       => "\t",
            ','         => ',',
            ';'         => ';',
            '|'         => '|',
            "\t"        => "\t",
        ];
        $data['delimiter'] = $map[$data['delimiter'] ?? 'comma'] ?? ',';

        try {
            $ok = $this->CSVParameter->createParameterByFile($uploaded, $data);
        } catch (\Throwable $e) {
            // Serwis zgłosił błąd – mapujemy na 422 zgodnie z testami
            return response()->json(['message' => 'Bad data'], 422);
        }

        return $ok
            ? response()->json(['message' => 'Success'], 200)
            : response()->json(['message' => 'Bad data'], 422);
    }

}
