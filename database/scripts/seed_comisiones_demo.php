<?php

/**
 * Datos de prueba para comisiones de cobradores.
 *
 * - Activa préstamo #1 en planilla (status A)
 * - Asigna analista COBRADOR (user #1)
 * - Crea comisión acumulada de la cuota 1 ya pagada (interés S/ 30 → comisión S/ 9)
 *
 * Uso: php database/scripts/seed_comisiones_demo.php
 */

require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\comision_detalle;
use App\Models\comision_periodo;
use App\Models\Prestamo;
use App\Services\ComisionService;
use Carbon\Carbon;

echo "=== Seed comisiones demo ===\n";

$prestamo = Prestamo::find(1);
if (!$prestamo) {
    fwrite(STDERR, "No existe préstamo #1.\n");
    exit(1);
}

$prestamo->update([
    'status' => 'A',
    'analista_id' => 1,
]);

echo "Préstamo #1 activo, analista COBRADOR (id=1)\n";
echo "  Crédito: S/ {$prestamo->moto_credito}, interés {$prestamo->interes}%, frecuencia {$prestamo->frecuencia_pagos}\n";
echo "  Cuotas pendientes: períodos 2, 3 y 4 (interés S/ 30 c/u)\n";

$fecha = Carbon::parse('2026-06-17');
$service = app(ComisionService::class);

$periodoExistente = comision_periodo::where('trabajador_id', 1)
    ->where('anio', 2026)
    ->where('mes', 6)
    ->where('status', 'P')
    ->first();

$yaHayCuota1 = comision_detalle::where('cronograma_id', 1)
    ->whereHas('periodo', function ($q) {
        $q->where('trabajador_id', 1)->where('anio', 2026)->where('mes', 6)->where('status', 'P');
    })->exists();

if ($yaHayCuota1) {
    echo "La cuota 1 del préstamo 000001 ya está en el acumulado.\n";
} else {
    $detalle = $service->registrarLinea(
        1,
        1,
        $fecha,
        1,
        null,
        1,
        1,
        30.00,
        'Cuota período 1 — Préstamo 000001 (demo)'
    );

    if ($detalle) {
        echo "Comisión demo creada: interés S/ 30.00 → comisión S/ 9.00\n";
    }
}

$periodo = comision_periodo::where('trabajador_id', 1)
    ->where('anio', 2026)
    ->where('mes', 6)
    ->where('status', 'P')
    ->first();

if ($periodo) {
    echo "\nResumen acumulado COBRADOR — Junio 2026:\n";
    echo "  Interés cobrado: S/ {$periodo->monto_interes_pagado}\n";
    echo "  Comisión 30%:    S/ {$periodo->monto_acumulado}\n";
    echo "  Líneas:          " . comision_detalle::where('comision_periodo_id', $periodo->comision_periodo_id)->count() . "\n";
}

echo "\nPrueba:\n";
echo "  1. Menú → Recursos Humanos → Comisiones cobradores\n";
echo "  2. Ver acumulado S/ 9.00 del COBRADOR\n";
echo "  3. Planilla → préstamo 000001 → pagar cuota 2 → suma S/ 9.00 más (total S/ 18)\n";
echo "  4. Procesar pago mensual → queda en cero para julio\n";
echo "=== Listo ===\n";