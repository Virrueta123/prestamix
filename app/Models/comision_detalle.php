<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class comision_detalle extends Model
{
    protected $table = 'comision_detalle';
    protected $primaryKey = 'comision_detalle_id';

    protected $fillable = [
        'comision_periodo_id',
        'ingreso_id',
        'detalle_ingreso_id',
        'cronograma_id',
        'prestamo_id',
        'interes_pagado',
        'comision_monto',
        'descripcion',
    ];

    protected $casts = [
        'interes_pagado' => 'decimal:2',
        'comision_monto' => 'decimal:2',
    ];

    public function periodo()
    {
        return $this->belongsTo(comision_periodo::class, 'comision_periodo_id', 'comision_periodo_id');
    }

    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class, 'prestamo_id', 'prestamo_id');
    }
}