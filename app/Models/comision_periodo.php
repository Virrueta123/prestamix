<?php

namespace App\Models;

use App\Helpers\Encryptor;
use Illuminate\Database\Eloquent\Model;

class comision_periodo extends Model
{
    protected $table = 'comision_periodo';
    protected $primaryKey = 'comision_periodo_id';

    protected $fillable = [
        'trabajador_id',
        'sucursal_id',
        'anio',
        'mes',
        'monto_interes_pagado',
        'monto_acumulado',
        'status',
        'gastos_id',
        'procesado_por',
        'fecha_procesado',
    ];

    protected $casts = [
        'monto_interes_pagado' => 'decimal:2',
        'monto_acumulado' => 'decimal:2',
        'fecha_procesado' => 'datetime',
    ];

    protected $appends = ['urlapi', 'mes_nombre'];

    public function trabajador()
    {
        return $this->belongsTo(User::class, 'trabajador_id', 'id');
    }

    public function detalles()
    {
        return $this->hasMany(comision_detalle::class, 'comision_periodo_id', 'comision_periodo_id');
    }

    public function gasto()
    {
        return $this->belongsTo(gastos::class, 'gastos_id', 'gastos_id');
    }

    public function getUrlapiAttribute()
    {
        return Encryptor::encrypt($this->comision_periodo_id);
    }

    public function getMesNombreAttribute()
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        return $meses[(int) $this->mes] ?? (string) $this->mes;
    }
}