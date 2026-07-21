<template>
    <div class="mb-4 p-4 rounded-lg border border-primary/30 bg-primary/5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <div class="text-xs uppercase tracking-wide text-slate-500">Trabajador cobrador</div>
                <div class="text-lg font-semibold text-primary">{{ nombreCobrador }}</div>
            </div>
            <div class="text-right">
                <div class="text-xs uppercase tracking-wide text-slate-500">Reparto del interés</div>
                <div class="text-lg font-semibold">
                    Cobrador {{ porcentaje }}% · Empresa {{ porcentajeEmpresa }}%
                </div>
            </div>
        </div>
        <div v-if="mostrarResumen" class="mt-3 pt-3 border-t border-primary/20 grid grid-cols-1 sm:grid-cols-4 gap-2 text-sm">
            <div><span class="text-slate-500">Interés en este pago:</span> <strong>S/ {{ formatear_dinero_soles(totalInteres) }}</strong></div>
            <div><span class="text-slate-500">Comisión cobrador ({{ porcentaje }}%):</span> <strong class="text-primary">S/ {{ formatear_dinero_soles(totalComision) }}</strong></div>
            <div><span class="text-slate-500">Empresa ({{ porcentajeEmpresa }}%):</span> <strong class="text-blue-800">S/ {{ formatear_dinero_soles(totalEmpresa) }}</strong></div>
            <div class="text-slate-500">La comisión se acumula al mes del cobrador.</div>
        </div>
    </div>
</template>

<script>
import Axios from 'axios';
import { myMixin } from '../../mixin.js';
import {
    nombreCobradorDesdePrestamo,
    interesCuotaParaComision,
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
        totalInteres() {
            if (this.cuotaUnica) {
                return interesCuotaParaComision(this.cuotaUnica);
            }
            return this.pagoGrupal.reduce((sum, p) => sum + interesCuotaParaComision(p), 0);
        },
        totalComision() {
            return comisionDesdeInteres(this.totalInteres, this.porcentaje);
        },
        totalEmpresa() {
            return empresaDesdeInteres(this.totalInteres, this.porcentaje);
        },
    },
    mounted() {
        Axios.get('/get_comision_config').then((res) => {
            if (res.data?.success) {
                this.porcentaje = res.data.porcentaje;
            }
        }).catch(() => {});
    },
};
</script>