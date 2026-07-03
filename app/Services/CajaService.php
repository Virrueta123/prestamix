<?php

namespace App\Services;

use App\Models\caja;
use App\Models\User;

class CajaService
{
    /**
     * Busca una caja chica abierta (status A) para registrar el gasto.
     * Prioridad: caja del usuario → cualquier caja abierta de su sucursal → cualquier caja abierta.
     */
    public function resolverCajaDisponible(User $user): ?caja
    {
        $queryAbierta = fn () => caja::query()->where('status', 'A');

        $propia = (clone $queryAbierta())
            ->where('created_user', $user->id)
            ->when($user->sucursal_id, fn ($q) => $q->where('sucursal_id', $user->sucursal_id))
            ->orderByDesc('caja_chica_id')
            ->first();

        if ($propia) {
            return $propia;
        }

        if ($user->sucursal_id) {
            $sucursal = (clone $queryAbierta())
                ->where('sucursal_id', $user->sucursal_id)
                ->orderByDesc('caja_chica_id')
                ->first();

            if ($sucursal) {
                return $sucursal;
            }
        }

        return (clone $queryAbierta())
            ->orderByDesc('caja_chica_id')
            ->first();
    }
}