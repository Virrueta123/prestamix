<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comision_periodo', function (Blueprint $table) {
            $table->id('comision_periodo_id');
            $table->unsignedBigInteger('trabajador_id');
            $table->unsignedInteger('sucursal_id')->nullable();
            $table->unsignedSmallInteger('anio');
            $table->unsignedTinyInteger('mes');
            $table->decimal('monto_interes_pagado', 12, 2)->default(0);
            $table->decimal('monto_acumulado', 12, 2)->default(0);
            $table->char('status', 1)->default('P'); // P=pendiente, C=procesado
            $table->unsignedBigInteger('gastos_id')->nullable();
            $table->unsignedBigInteger('procesado_por')->nullable();
            $table->timestamp('fecha_procesado')->nullable();
            $table->timestamps();

            $table->index(['trabajador_id', 'anio', 'mes', 'status'], 'comision_periodo_trabajador_mes_idx');
            $table->index(['sucursal_id', 'anio', 'mes']);
        });

        Schema::create('comision_detalle', function (Blueprint $table) {
            $table->id('comision_detalle_id');
            $table->unsignedBigInteger('comision_periodo_id');
            $table->unsignedBigInteger('ingreso_id')->nullable();
            $table->unsignedBigInteger('detalle_ingreso_id')->nullable();
            $table->unsignedBigInteger('cronograma_id')->nullable();
            $table->unsignedBigInteger('prestamo_id')->nullable();
            $table->decimal('interes_pagado', 12, 2)->default(0);
            $table->decimal('comision_monto', 12, 2)->default(0);
            $table->string('descripcion', 255)->nullable();
            $table->timestamps();

            $table->index('comision_periodo_id');
            $table->index('ingreso_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comision_detalle');
        Schema::dropIfExists('comision_periodo');
    }
};