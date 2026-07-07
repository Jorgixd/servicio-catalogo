<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemCatalogo extends Model
{
    use SoftDeletes;

    protected $table = 'items_catalogo';
    protected $primaryKey = 'id_item';

    protected $fillable = [
        'sku_codigo',
        'tipo_item',
        'nombre',
        'descripcion',
        'unidad_medida',
        'precio_ref',
        'activo'
    ];

    protected $casts = [
        'precio_ref' => 'decimal:2',
        'activo' => 'boolean',
    ];
}
