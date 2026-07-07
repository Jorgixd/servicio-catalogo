<?php

namespace App\Services;

use App\Models\ItemCatalogo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CatalogoService
{
    /**
     * Lista los items. 
     * SoftDeletes oculta automáticamente los que tienen 'deleted_at' lleno.
     */
    public function listarItems(bool $soloActivos = true, bool $incluirEliminados = false): Collection
    {
        $query = ItemCatalogo::query();

        // Si el admin quiere ver los eliminados lógicamente, usamos withTrashed()
        if ($incluirEliminados) {
            $query->withTrashed();
        }

        if ($soloActivos) {
            $query->where('activo', true);
        }

        return $query->orderBy('nombre', 'asc')->get();
    }

    public function crearItem(array $datos): ItemCatalogo
    {
        if (!isset($datos['activo'])) {
            $datos['activo'] = true;
        }
        return ItemCatalogo::create($datos);
    }

    public function actualizarItem(int $id, array $datos): ItemCatalogo
    {
        $item = ItemCatalogo::findOrFail($id);
        $item->update($datos);
        return $item->fresh();
    }

    /**
     *  NUEVO: Eliminación Lógica (Baja definitiva del catálogo)
     * No borra de la BD, solo llena la columna 'deleted_at'.
     */
    public function eliminarItem(int $id): bool
    {
        $item = ItemCatalogo::findOrFail($id);

        // Esto ejecuta: UPDATE items_catalogo SET deleted_at = NOW() WHERE id_item = ?
        return $item->delete();
    }

    public function buscar(string $query): \Illuminate\Database\Eloquent\Collection
    {
        return ItemCatalogo::where('activo', true) // Solo mostrar items activos
            ->where(function ($q) use ($query) {
                $q->where('nombre', 'LIKE', "%{$query}%")
                    ->orWhere('sku_codigo', 'LIKE', "%{$query}%");
            })
            ->orderBy('nombre', 'asc')
            ->get();
    }
}
