<template>
    <div class="pcb-simple">
        <table class="pcb-table">
            <thead>
                <tr>
                    <th colspan="4" class="pcb-table__title">
                        Cobrador: {{ nombreCobrador }}
                        <span class="pcb-table__pct">
                            (reparto {{ porcentaje }}% cobrador / {{ porcentajeEmpresa }}% empresa sobre interés + mora)
                        </span>
                    </th>
                </tr>
                <tr v-if="mostrarResumen">
                    <th>Concepto</th>
                    <th class="text-right">Monto base</th>
                    <th class="text-right">Comisión cobrador ({{ porcentaje }}%)</th>
                    <th class="text-right">Empresa ({{ porcentajeEmpresa }}%)</th>
                </tr>
            </thead>
            <tbody v-if="mostrarResumen">
                <tr>
                    <td>Interés de las cuotas</td>
                    <td class="text-right">S/ {{ formatear_dinero_soles(totalInteres) }}</td>
                    <td class="text-right">S/ {{ formatear_dinero_soles(comisionInteres) }}</td>
                    <td class="text-right">S/ {{ formatear_dinero_soles(empresaInteres) }}</td>
                </tr>
                <tr>
                    <td>Mora a cobrar</td>
                    <td class="text-right">S/ {{ formatear_dinero_soles(totalMora) }}</td>
                    <td class="text-right">S/ {{ formatear_dinero_soles(comisionMora) }}</td>
                    <td class="text-right">S/ {{ formatear_dinero_soles(empresaMora) }}</td>
                </tr>
            </tbody>
            <tfoot v-if="mostrarResumen">
                <tr>
                    <td><strong>Total (interés + mora)</strong></td>
                    <td class="text-right"><strong>S/ {{ formatear_dinero_soles(totalBase) }}</strong></td>
                    <td class="text-right pcb-table__highlight">
                        <strong>S/ {{ formatear_dinero_soles(totalComision) }}</strong>
                    </td>
                    <td class="text-right"><strong>S/ {{ formatear_dinero_soles(totalEmpresa) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</template>

<script>
import Axios from 'axios';
import { myMixin } from '../../mixin.js';
import {
    nombreCobradorDesdePrestamo,
    interesBaseCuota,
    moraCobradaCuota,
    comisionDesdeInteres,
    porcentajeEmpresa,
    empresaDesdeInteres,
} from '../../utils/comisionHelper.js';

export default {
    mixins: [myMixin],
    props: {
        getCuota: { type: Object, default: null },
        pagoGrupal: { type: Array, default: () => [] },
        cuotaUnica: { type: Object, default: null },
        mostrarResumen: { type: Boolean, default: true },
    },
    data() {
        return { porcentaje: 30 };
    },
    computed: {
        nombreCobrador() {
            return nombreCobradorDesdePrestamo(this.getCuota);
        },
        porcentajeEmpresa() {
            return porcentajeEmpresa(this.porcentaje);
        },
        items() {
            if (this.cuotaUnica) return [this.cuotaUnica];
            return this.pagoGrupal || [];
        },
        totalInteres() {
            return this.items.reduce((sum, p) => sum + interesBaseCuota(p), 0);
        },
        totalMora() {
            return this.items.reduce((sum, p) => sum + moraCobradaCuota(p), 0);
        },
        totalBase() {
            return Math.round((this.totalInteres + this.totalMora) * 100) / 100;
        },
        comisionInteres() {
            return comisionDesdeInteres(this.totalInteres, this.porcentaje);
        },
        comisionMora() {
            return comisionDesdeInteres(this.totalMora, this.porcentaje);
        },
        totalComision() {
            return Math.round((this.comisionInteres + this.comisionMora) * 100) / 100;
        },
        empresaInteres() {
            return empresaDesdeInteres(this.totalInteres, this.porcentaje);
        },
        empresaMora() {
            return empresaDesdeInteres(this.totalMora, this.porcentaje);
        },
        totalEmpresa() {
            return Math.round((this.totalBase - this.totalComision) * 100) / 100;
        },
    },
    mounted() {
        Axios.get('/get_comision_config')
            .then((res) => {
                if (res.data?.success) {
                    this.porcentaje = res.data.porcentaje;
                }
            })
            .catch(() => {});
    },
};
</script>

<style scoped>
.pcb-simple {
    margin-bottom: 0.75rem;
}

.pcb-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
    border: 1px solid #e5e7eb;
}

.pcb-table th,
.pcb-table td {
    border: 1px solid #e5e7eb;
    padding: 0.4rem 0.55rem;
    vertical-align: middle;
}

.pcb-table thead th {
    background: #f9fafb;
    color: #374151;
    font-weight: 600;
    font-size: 11px;
}

.pcb-table__title {
    background: #fff !important;
    text-align: left !important;
    font-size: 13px !important;
    color: #111827 !important;
    padding: 0.5rem 0.55rem !important;
}

.pcb-table__pct {
    font-weight: 400;
    color: #6b7280;
    font-size: 11px;
    margin-left: 0.25rem;
}

.pcb-table tbody td {
    color: #111827;
}

.pcb-table tfoot td {
    background: #f9fafb;
}

.pcb-table__highlight {
    color: #05be50;
}

.text-right {
    text-align: right;
}
</style>
