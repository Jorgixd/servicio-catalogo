<?php

namespace App\Http\Controllers\CatalogoService;

use App\Http\Controllers\Controller;
use App\Http\Requests\CatalogoService\StoreItemCatalogoRequest;
use App\Http\Requests\CatalogoService\UpdateItemCatalogoRequest;
use App\Services\CatalogoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CatalogoController extends Controller
{
    public function __construct(
        protected CatalogoService $catalogoService
    ) {}

    public function index(Request $request): JsonResponse
    {
        // Si el admin envía ?ver_eliminados=1, los mostramos
        $incluirEliminados = $request->boolean('ver_eliminados');
        $soloActivos = !$request->boolean('todos');

        $items = $this->catalogoService->listarItems($soloActivos, $incluirEliminados);

        return response()->json([
            'mensaje' => 'Catálogo obtenido exitosamente.',
            'data' => $items
        ], 200);
    }

    public function store(StoreItemCatalogoRequest $request): JsonResponse
    {
        try {
            $item = $this->catalogoService->crearItem($request->validated());
            return response()->json(['mensaje' => 'Item creado exitosamente.', 'data' => $item], 201);
        } catch (\Exception $e) {
            return response()->json(['mensaje' => 'Error al crear el item.', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(UpdateItemCatalogoRequest $request, int $id): JsonResponse
    {
        try {
            $item = $this->catalogoService->actualizarItem($id, $request->validated());
            return response()->json(['mensaje' => 'Item actualizado exitosamente.', 'data' => $item], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['mensaje' => 'El item no existe.'], 404);
        } catch (\Exception $e) {
            return response()->json(['mensaje' => 'Error al actualizar.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     *  NUEVO: DELETE /api/catalogo/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->catalogoService->eliminarItem($id);

            return response()->json([
                'mensaje' => 'Item eliminado lógicamente del catálogo (Baja registrada).'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['mensaje' => 'El item no existe o ya fue eliminado.'], 404);
        } catch (\Exception $e) {
            return response()->json(['mensaje' => 'Error al eliminar.', 'error' => $e->getMessage()], 500);
        }
    }

    public function buscar(Request $request): JsonResponse
    {
        // ✅ Validación automática (Devuelve 422 si falla, consistente con el resto de la API)
        $validated = $request->validate([
            'q' => 'required|string|min:2|max:100'
        ]);

        $items = $this->catalogoService->buscar($validated['q']);

        return response()->json([
            'mensaje' => 'Búsqueda realizada exitosamente.',
            'data' => $items
        ], 200);
    }
}
