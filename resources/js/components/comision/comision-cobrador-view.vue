<template>
    <div class="comision-page">
        <div class="comision-wrap">
            <!-- Volver -->
            <button class="comision-back" @click="volver">
                <i class="fa fa-arrow-left"></i>
                Volver a comisiones
            </button>

            <!-- Perfil cobrador -->
            <div class="comision-profile">
                <div class="comision-profile__main">
                    <span class="comision-profile__avatar">{{ iniciales }}</span>
                    <div>
                        <h1 class="comision-profile__name">{{ nombreTrabajador }}</h1>
                        <p class="comision-profile__role">Cobrador · Comisión {{ porcentaje }}% del interés</p>
                    </div>
                </div>
                <div class="comision-profile__period">
                    <div class="comision-field">
                        <label class="comision-field__label">Mes</label>
                        <select v-model="mes" class="form-select comision-field__input" @change="cargar">
                            <option v-for="m in meses" :key="m.val" :value="m.val">{{ m.label }}</option>
                        </select>
                    </div>
                    <div class="comision-field comision-field--sm">
                        <label class="comision-field__label">Año</label>
                        <input
                            v-model.number="anio"
                            type="number"
                            class="form-control comision-field__input"
                            min="2020"
                            max="2099"
                            @change="cargar"
                        />
                    </div>
                </div>
            </div>

            <div v-if="loading" class="comision-empty">
                <i class="fa fa-spinner fa-spin comision-empty__icon"></i>
                <p>Cargando datos del cobrador...</p>
            </div>

            <template v-else>
                <!-- Historial -->
                <section class="comision-section">
                    <div class="comision-section__head">
                        <div class="comision-section__icon comision-section__icon--history">
                            <i class="fa fa-history"></i>
                        </div>
                        <div>
                            <h2 class="comision-section__title">Pagos anteriores</h2>
                            <p class="comision-section__desc">Comisiones mensuales ya procesadas y pagadas.</p>
                        </div>
                    </div>

                    <div v-if="historial.length === 0" class="comision-empty comision-empty--compact">
                        <i class="fa fa-receipt comision-empty__icon"></i>
                        <p>Aún no hay pagos de comisiones procesados.</p>
                    </div>

                    <div v-else class="comision-table-wrap">
                        <table class="table comision-table">
                            <thead>
                                <tr>
                                    <th>Período</th>
                                    <th class="text-center">Préstamos</th>
                                    <th class="text-center">Cuotas</th>
                                    <th class="text-right">Interés</th>
                                    <th class="text-right">Comisión pagada</th>
                                    <th>Fecha pago</th>
                                    <th>Gasto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="h in historial" :key="h.comision_periodo_id">
                                    <td class="font-medium">{{ h.mes_nombre }} {{ h.anio }}</td>
                                    <td class="text-center">{{ h.prestamos_count ?? '—' }}</td>
                                    <td class="text-center">{{ h.detalles_count }}</td>
                                    <td class="text-right">{{ formatear_dinero_soles(h.monto_interes_pagado) }}</td>
                                    <td class="text-right">
                                        <span class="comision-monto comision-monto--paid">{{ formatear_dinero_soles(h.monto_acumulado) }}</span>
                                    </td>
                                    <td class="text-sm">{{ formatear_fecha(h.fecha_procesado) }}</td>
                                    <td><span class="comision-code">{{ h.gasto?.codigo ?? '—' }}</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Acumulado actual -->
                <section class="comision-section comision-section--accent">
                    <div class="comision-section__head">
                        <div class="comision-section__icon comision-section__icon--current">
                            <i class="fa fa-coins"></i>
                        </div>
                        <div>
                            <h2 class="comision-section__title">Acumulado — {{ mesLabel }} {{ anio }}</h2>
                            <p class="comision-section__desc">Comisiones ganadas en el período actual.</p>
                        </div>
                    </div>

                    <div v-if="!resumen" class="comision-empty comision-empty--compact">
                        <i class="fa fa-chart-bar comision-empty__icon"></i>
                        <p>No hay comisiones acumuladas para este período.</p>
                    </div>

                    <template v-else>
                        <div class="comision-kpis">
                            <div class="comision-kpi">
                                <span class="comision-kpi__val">{{ resumen.totales.prestamos }}</span>
                                <span class="comision-kpi__lbl">Préstamos</span>
                            </div>
                            <div class="comision-kpi">
                                <span class="comision-kpi__val">{{ resumen.totales.cuotas }}</span>
                                <span class="comision-kpi__lbl">Cuotas</span>
                            </div>
                            <div class="comision-kpi">
                                <span class="comision-kpi__val">{{ formatear_dinero_soles(sumaInteresDetalle) }}</span>
                                <span class="comision-kpi__lbl">Interés cobrado</span>
                            </div>
                            <div class="comision-kpi comision-kpi--primary">
                                <span class="comision-kpi__val">{{ formatear_dinero_soles(sumaComisionDetalle) }}</span>
                                <span class="comision-kpi__lbl">Comisión {{ resumen.totales.porcentaje }}%</span>
                            </div>
                        </div>

                        <div class="comision-prestamos">
                            <div
                                v-for="grupo in resumen.por_prestamo"
                                :key="grupo.prestamo_id || grupo.prestamo_code"
                                class="comision-prestamo"
                            >
                                <div class="comision-prestamo__head">
                                    <div class="comision-prestamo__info">
                                        <span class="comision-prestamo__code">#{{ grupo.prestamo_code }}</span>
                                        <span class="comision-prestamo__cliente">{{ grupo.cliente_nombre }}</span>
                                    </div>
                                    <div class="comision-prestamo__totals">
                                        <span>{{ grupo.cuotas }} cuota(s)</span>
                                        <span class="comision-prestamo__sep">·</span>
                                        <span>Interés {{ formatear_dinero_soles(grupo.interes_total) }}</span>
                                        <span class="comision-prestamo__sep">·</span>
                                        <span class="comision-monto">{{ formatear_dinero_soles(grupo.comision_total) }}</span>
                                    </div>
                                    <a
                                        v-if="grupo.planilla_url"
                                        :href="grupo.planilla_url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="btn btn-outline-primary btn-sm comision-prestamo__link"
                                    >
                                        <i class="fa fa-external-link-alt"></i>
                                        Ver préstamo
                                    </a>
                                </div>
                                <table class="table comision-table comision-table--nested">
                                    <thead>
                                        <tr>
                                            <th>Detalle</th>
                                            <th class="text-right">Interés</th>
                                            <th class="text-right">Comisión</th>
                                            <th>Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="linea in grupo.lineas" :key="linea.comision_detalle_id">
                                            <td class="text-sm">{{ linea.descripcion }}</td>
                                            <td class="text-right">{{ formatear_dinero_soles(linea.interes_pagado) }}</td>
                                            <td class="text-right">
                                                <span class="comision-monto">{{ formatear_dinero_soles(linea.comision_monto) }}</span>
                                            </td>
                                            <td class="text-sm text-slate-500">{{ formatear_fecha(linea.created_at) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Panel de pago -->
                        <div v-if="periodoPendiente" class="comision-checkout">
                            <div class="comision-checkout__summary">
                                <h3 class="comision-checkout__title">Resumen del período</h3>
                                <table class="table comision-table comision-table--summary">
                                    <thead>
                                        <tr>
                                            <th>Préstamo</th>
                                            <th>Cliente</th>
                                            <th class="text-center">Cuotas</th>
                                            <th class="text-right">Interés</th>
                                            <th class="text-right">Comisión</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="g in resumen.por_prestamo" :key="'sum-' + g.prestamo_code">
                                            <td class="font-medium">#{{ g.prestamo_code }}</td>
                                            <td class="text-sm">{{ g.cliente_nombre }}</td>
                                            <td class="text-center">{{ g.cuotas }}</td>
                                            <td class="text-right">{{ formatear_dinero_soles(g.interes_total) }}</td>
                                            <td class="text-right">
                                                <span class="comision-monto">{{ formatear_dinero_soles(g.comision_total) }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-right font-semibold">Total acumulado</td>
                                            <td class="text-right font-semibold">
                                                {{ formatear_dinero_soles(totalConfirmado ? totalInteresFinal : sumaInteresDetalle) }}
                                            </td>
                                            <td class="text-right">
                                                <span class="comision-checkout__total">
                                                    {{ formatear_dinero_soles(totalConfirmado ? totalComisionFinal : sumaComisionDetalle) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="comision-checkout__actions">
                                <div class="comision-checkout__status">
                                    <template v-if="!totalConfirmado">
                                        <i class="fa fa-info-circle text-amber-500"></i>
                                        <span>Calcule la suma total antes de procesar el pago.</span>
                                    </template>
                                    <template v-else>
                                        <span class="comision-checkout__status-label">Monto a pagar</span>
                                        <div class="comision-checkout__monto-input">
                                            <span class="comision-checkout__monto-prefix">S/</span>
                                            <input
                                                v-model.number="montoPago"
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                class="form-control comision-checkout__monto-field"
                                                placeholder="0.00"
                                            />
                                        </div>
                                        <span v-if="montoAjustadoManual" class="comision-checkout__monto-hint">
                                            Calculado: {{ formatear_dinero_soles(totalComisionFinal) }} · ajustado manualmente
                                        </span>
                                        <span v-else class="comision-checkout__status-detail">
                                            {{ resumen.totales.cuotas }} cuotas · {{ resumen.totales.prestamos }} préstamos
                                        </span>
                                    </template>
                                </div>
                                <div class="comision-checkout__buttons">
                                    <button
                                        class="btn btn-primary"
                                        :disabled="!!calculando || !resumen.por_prestamo.length"
                                        @click="calcularSumaTotal"
                                    >
                                        <i class="fa fa-calculator"></i>
                                        {{ calculando ? 'Calculando...' : 'Calcular total' }}
                                    </button>
                                    <button
                                        v-if="puedeProcesar"
                                        class="btn btn-success"
                                        :disabled="!totalConfirmado || montoPago <= 0 || !!procesando"
                                        @click="procesar"
                                    >
                                        <i class="fa fa-check"></i>
                                        {{ procesando ? 'Procesando...' : 'Procesar pago' }}
                                    </button>
                                    <button class="btn btn-outline-secondary" @click="volver">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                            <p v-if="totalConfirmado && puedeProcesar" class="comision-checkout__note">
                                Al procesar se registra el gasto y el acumulado reinicia en cero para el siguiente mes.
                            </p>
                        </div>
                    </template>
                </section>
            </template>
        </div>
    </div>
</template>

<script>
import Axios from 'axios';
import { myMixin } from '../../mixin.js';

export default {
    mixins: [myMixin],
    props: {
        trabajador: { type: Object, required: true },
        mesInicial: { type: Number, default: () => new Date().getMonth() + 1 },
        anioInicial: { type: Number, default: () => new Date().getFullYear() },
    },
    data() {
        return {
            mes: this.mesInicial,
            anio: this.anioInicial,
            loading: false,
            historial: [],
            resumen: null,
            periodoPendiente: null,
            porcentaje: 30,
            totalConfirmado: false,
            totalComisionFinal: 0,
            totalInteresFinal: 0,
            montoPago: 0,
            calculando: false,
            procesando: false,
            meses: [
                { val: 1, label: 'Enero' }, { val: 2, label: 'Febrero' }, { val: 3, label: 'Marzo' },
                { val: 4, label: 'Abril' }, { val: 5, label: 'Mayo' }, { val: 6, label: 'Junio' },
                { val: 7, label: 'Julio' }, { val: 8, label: 'Agosto' }, { val: 9, label: 'Septiembre' },
                { val: 10, label: 'Octubre' }, { val: 11, label: 'Noviembre' }, { val: 12, label: 'Diciembre' },
            ],
        };
    },
    computed: {
        puedeProcesar() {
            return this.user && ['gerente', 'super_admin', 'admin'].includes(this.user.rol);
        },
        nombreTrabajador() {
            return `${this.trabajador.name || ''} ${this.trabajador.lastname || ''}`.trim() || '—';
        },
        iniciales() {
            const n = (this.trabajador.name || '').charAt(0);
            const a = (this.trabajador.lastname || '').charAt(0);
            return (n + a).toUpperCase() || '?';
        },
        mesLabel() {
            return this.meses.find((m) => m.val === this.mes)?.label ?? '';
        },
        sumaInteresDetalle() {
            if (!this.resumen?.por_prestamo) return 0;
            return this.resumen.por_prestamo.reduce((s, g) => s + parseFloat(g.interes_total || 0), 0);
        },
        sumaComisionDetalle() {
            if (!this.resumen?.por_prestamo) return 0;
            return this.resumen.por_prestamo.reduce((s, g) => s + parseFloat(g.comision_total || 0), 0);
        },
        montoAjustadoManual() {
            if (!this.totalConfirmado) return false;
            return Math.abs(parseFloat(this.montoPago || 0) - parseFloat(this.totalComisionFinal || 0)) > 0.009;
        },
    },
    mounted() {
        this.cargar();
    },
    methods: {
        formatear_fecha(fecha) {
            if (!fecha) return '—';
            return new Date(fecha).toLocaleString('es-PE');
        },
        resetTotalConfirmado() {
            this.totalConfirmado = false;
            this.totalComisionFinal = 0;
            this.totalInteresFinal = 0;
            this.montoPago = 0;
        },
        volver() {
            window.location.href = '/comisiones';
        },
        aplicarResumen(data, confirmar = false) {
            this.resumen = data;
            if (data?.periodo) {
                this.periodoPendiente = data.periodo;
            }
            if (data?.totales?.porcentaje != null) {
                this.porcentaje = data.totales.porcentaje;
            }
            if (confirmar && data?.totales) {
                this.totalConfirmado = true;
                this.totalComisionFinal = parseFloat(data.totales.comision || 0);
                this.totalInteresFinal = parseFloat(data.totales.interes || 0);
                this.montoPago = this.totalComisionFinal;
            }
        },
        async cargar() {
            this.loading = true;
            this.resetTotalConfirmado();
            try {
                const { data } = await Axios.post('/load_cobrador_comisiones', {
                    trabajador_id: this.trabajador.urlapi,
                    mes: this.mes,
                    anio: this.anio,
                });
                if (data.success) {
                    this.historial = data.historial_pagos || [];
                    this.porcentaje = data.porcentaje ?? this.porcentaje;
                    if (data.acumulado_actual) {
                        this.aplicarResumen(data.acumulado_actual, false);
                    } else {
                        this.resumen = null;
                        this.periodoPendiente = null;
                    }
                } else {
                    this.$swal.fire('Error', data.message || 'No se pudo cargar', 'error');
                }
            } catch (e) {
                this.$swal.fire('Error', e.response?.data?.message || 'Error del servidor', 'error');
            } finally {
                this.loading = false;
            }
        },
        async calcularSumaTotal() {
            if (!this.periodoPendiente) return;
            this.calculando = true;
            try {
                const { data } = await Axios.post('/recalcular_comision', {
                    comision_periodo_id: this.periodoPendiente.urlapi,
                });
                if (data.success) {
                    this.aplicarResumen(data.data, true);
                    this.$swal.fire({
                        title: 'Suma calculada',
                        html: `
                            <p>Interés total: <strong>${this.formatear_dinero_soles(this.totalInteresFinal)}</strong></p>
                            <p>Comisión (${data.data.totales.porcentaje}%): <strong>${this.formatear_dinero_soles(this.totalComisionFinal)}</strong></p>
                            <p class="text-sm">Ya puede usar <strong>Procesar pago</strong>.</p>
                        `,
                        icon: 'success',
                    });
                } else {
                    this.$swal.fire('Error', data.message || 'No se pudo calcular', 'error');
                }
            } catch (e) {
                this.$swal.fire('Error', e.response?.data?.message || 'Error del servidor', 'error');
            } finally {
                this.calculando = false;
            }
        },
        async procesar() {
            if (!this.periodoPendiente || !this.totalConfirmado) {
                this.$swal.fire('Atención', 'Primero debe pulsar "Calcular total".', 'warning');
                return;
            }

            const montoPago = parseFloat(this.montoPago || 0);
            if (montoPago <= 0) {
                this.$swal.fire('Atención', 'Ingrese un monto válido mayor a cero.', 'warning');
                return;
            }

            const monto = this.formatear_dinero_soles(montoPago);
            const interes = this.formatear_dinero_soles(this.totalInteresFinal);
            const pct = this.resumen?.totales?.porcentaje ?? this.porcentaje;
            const ajusteHtml = this.montoAjustadoManual
                ? `<p class="text-sm text-amber-600">Monto calculado: ${this.formatear_dinero_soles(this.totalComisionFinal)} · pago ajustado manualmente</p>`
                : '';

            const ok = await this.$swal.fire({
                title: '¿Procesar pago de comisiones?',
                html: `
                    <p>Trabajador: <strong>${this.nombreTrabajador}</strong></p>
                    <p>Período: <strong>${this.mesLabel} ${this.anio}</strong></p>
                    <p>Préstamos: <strong>${this.resumen?.totales?.prestamos}</strong> · Cuotas: <strong>${this.resumen?.totales?.cuotas}</strong></p>
                    <p>Interés sumado: <strong>${interes}</strong> × <strong>${pct}%</strong></p>
                    ${ajusteHtml}
                    <p class="text-xl mt-2">Total a pagar: <strong class="text-primary">${monto}</strong></p>
                    <p class="text-sm text-slate-500">El acumulado reinicia en cero para el próximo mes.</p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, procesar pago',
                cancelButtonText: 'Cancelar',
            });
            if (!ok.isConfirmed) return;

            this.procesando = true;
            try {
                const { data } = await Axios.post('/procesar_comision', {
                    comision_periodo_id: this.periodoPendiente.urlapi,
                    monto: montoPago,
                });
                if (data.success) {
                    const cajaInfo = data.gasto?.caja_codigo
                        ? `<p class="text-sm mt-2">Registrado en <strong>caja #${data.gasto.caja_codigo}</strong> · gasto <strong>${data.gasto.codigo}</strong></p>`
                        : '';
                    const titulo = data.already_processed ? 'Comisión ya pagada' : 'Pago procesado';
                    await this.$swal.fire({
                        title: titulo,
                        html: `<p>${data.message}</p>${cajaInfo}<p class="text-sm text-slate-500 mt-2">Volviendo a la lista de comisiones...</p>`,
                        icon: 'success',
                        timer: 2200,
                        showConfirmButton: true,
                        confirmButtonText: 'Ir a comisiones',
                    });
                    window.location.href = '/comisiones';
                } else {
                    const detalle = data.error ? `<p class="text-sm text-slate-500 mt-1">${data.error}</p>` : '';
                    this.$swal.fire({
                        title: 'No se pudo procesar',
                        html: `<p>${data.message || 'Error desconocido'}</p>${detalle}`,
                        icon: 'error',
                    });
                }
            } catch (e) {
                const msg = e.response?.data?.message || 'No se recibió respuesta del servidor.';
                const detalle = e.response?.data?.error ? `<p class="text-sm text-slate-500 mt-1">${e.response.data.error}</p>` : '';
                const verificar = !e.response
                    ? '<p class="text-sm text-amber-600 mt-2">Si el gasto apareció al recargar, el pago sí se registró. Actualice la página para confirmar.</p>'
                    : '';
                this.$swal.fire({
                    title: 'Error',
                    html: `<p>${msg}</p>${detalle}${verificar}`,
                    icon: 'error',
                });
            } finally {
                this.procesando = false;
            }
        },
    },
};
</script>

<style scoped>
.comision-page {
    padding: 1.25rem 1rem 3rem;
}

.comision-wrap {
    max-width: 960px;
    margin: 0 auto;
}

.comision-back {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    background: none;
    border: none;
    color: #64748b;
    font-size: 0.875rem;
    font-weight: 500;
    padding: 0.25rem 0;
    margin-bottom: 1rem;
    cursor: pointer;
    transition: color 0.15s;
}

.comision-back:hover {
    color: #05be50;
}

.comision-profile {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
    flex-wrap: wrap;
    padding: 1.5rem 1.75rem;
    background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 60%);
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
}

.comision-profile__main {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.comision-profile__avatar {
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 50%;
    background: #05be50;
    color: #fff;
    font-size: 1.1rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(5, 190, 80, 0.3);
}

.comision-profile__name {
    font-size: 1.35rem;
    font-weight: 600;
    color: #0f172a;
    margin: 0;
    line-height: 1.3;
}

.comision-profile__role {
    font-size: 0.85rem;
    color: #64748b;
    margin: 0.2rem 0 0;
}

.comision-profile__period {
    display: flex;
    align-items: flex-end;
    gap: 0.65rem;
}

.comision-field__label {
    display: block;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #94a3b8;
    margin-bottom: 0.35rem;
}

.comision-field__input {
    min-width: 130px;
    border-radius: 0.5rem !important;
}

.comision-field--sm .comision-field__input {
    min-width: 90px;
}

.comision-section {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    margin-bottom: 1.5rem;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
}

.comision-section--accent {
    border-color: #bbf7d0;
}

.comision-section__head {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    padding: 1.15rem 1.5rem;
    border-bottom: 1px solid #f1f5f9;
}

.comision-section__icon {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.65rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.comision-section__icon--history {
    background: #f1f5f9;
    color: #64748b;
}

.comision-section__icon--current {
    background: #dcfce7;
    color: #05be50;
}

.comision-section__title {
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
    margin: 0;
}

.comision-section__desc {
    font-size: 0.8rem;
    color: #94a3b8;
    margin: 0.15rem 0 0;
}

.comision-kpis {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0;
    border-bottom: 1px solid #f1f5f9;
}

@media (max-width: 768px) {
    .comision-kpis {
        grid-template-columns: repeat(2, 1fr);
    }
}

.comision-kpi {
    text-align: center;
    padding: 1.1rem 0.75rem;
    border-right: 1px solid #f1f5f9;
}

.comision-kpi:last-child {
    border-right: none;
}

.comision-kpi--primary {
    background: #f0fdf4;
}

.comision-kpi__val {
    display: block;
    font-size: 1.15rem;
    font-weight: 700;
    color: #0f172a;
}

.comision-kpi--primary .comision-kpi__val {
    color: #05be50;
}

.comision-kpi__lbl {
    display: block;
    font-size: 0.7rem;
    color: #94a3b8;
    margin-top: 0.2rem;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.comision-prestamos {
    padding: 1rem 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.comision-prestamo {
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    overflow: hidden;
}

.comision-prestamo__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    flex-wrap: wrap;
    padding: 0.85rem 1rem;
    background: #f8fafc;
    border-bottom: 1px solid #f1f5f9;
}

.comision-prestamo__info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 0;
}

.comision-prestamo__code {
    font-weight: 700;
    color: #05be50;
    font-size: 0.9rem;
}

.comision-prestamo__cliente {
    font-size: 0.85rem;
    color: #475569;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.comision-prestamo__totals {
    font-size: 0.8rem;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 0.35rem;
    flex-wrap: wrap;
}

.comision-prestamo__sep {
    color: #cbd5e1;
}

.comision-prestamo__link {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    border-radius: 0.5rem;
    white-space: nowrap;
    margin-left: auto;
}

.comision-table-wrap {
    padding: 0 1rem 1rem;
    overflow-x: auto;
}

.comision-table {
    margin-bottom: 0;
    width: 100%;
}

.comision-table thead th {
    font-size: 0.68rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #94a3b8;
    font-weight: 600;
    border-bottom: 2px solid #f1f5f9;
    padding: 0.75rem 0.65rem;
    white-space: nowrap;
}

.comision-table tbody td {
    padding: 0.75rem 0.65rem;
    vertical-align: middle;
    border-bottom: 1px solid #f8fafc;
    font-size: 0.85rem;
}

.comision-table--nested thead th {
    background: #fafbfc;
    padding: 0.5rem 0.65rem;
}

.comision-table--nested tbody td {
    padding: 0.55rem 0.65rem;
    font-size: 0.8rem;
}

.comision-table--summary tfoot td {
    background: #f0fdf4;
    padding: 0.85rem 0.65rem;
    border-top: 2px solid #bbf7d0;
}

.comision-monto {
    font-weight: 600;
    color: #05be50;
}

.comision-monto--paid {
    color: #15803d;
}

.comision-code {
    font-family: monospace;
    font-size: 0.75rem;
    background: #f1f5f9;
    padding: 0.15rem 0.45rem;
    border-radius: 0.35rem;
    color: #475569;
}

.comision-checkout {
    margin: 0 1.25rem 1.25rem;
    border: 2px solid #05be50;
    border-radius: 0.85rem;
    overflow: hidden;
    background: #fff;
}

.comision-checkout__summary {
    padding: 1rem 1.15rem 0;
}

.comision-checkout__title {
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #64748b;
    margin: 0 0 0.75rem;
    text-align: center;
}

.comision-checkout__total {
    font-size: 1.15rem;
    font-weight: 700;
    color: #05be50;
}

.comision-checkout__actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.25rem;
    flex-wrap: wrap;
    padding: 1.15rem 1.25rem;
    background: linear-gradient(180deg, #f8fafc, #fff);
    border-top: 1px solid #f1f5f9;
}

.comision-checkout__status {
    flex: 1;
    min-width: 200px;
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
    font-size: 0.85rem;
    color: #64748b;
}

.comision-checkout__status-label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #94a3b8;
    font-weight: 600;
}

.comision-checkout__total-lg {
    font-size: 1.75rem;
    font-weight: 700;
    color: #05be50;
    line-height: 1.2;
}

.comision-checkout__monto-input {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    max-width: 220px;
}

.comision-checkout__monto-prefix {
    font-size: 1.25rem;
    font-weight: 700;
    color: #05be50;
}

.comision-checkout__monto-field {
    font-size: 1.35rem;
    font-weight: 700;
    color: #05be50;
    border: 2px solid #bbf7d0;
    border-radius: 0.5rem;
    padding: 0.35rem 0.65rem;
    max-width: 160px;
}

.comision-checkout__monto-hint {
    font-size: 0.75rem;
    color: #b45309;
}

.comision-checkout__status-detail {
    font-size: 0.75rem;
    color: #94a3b8;
}

.comision-checkout__buttons {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.comision-checkout__buttons .btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    border-radius: 0.5rem;
    padding: 0.5rem 1.1rem;
    white-space: nowrap;
}

.comision-checkout__note {
    text-align: center;
    font-size: 0.75rem;
    color: #94a3b8;
    padding: 0 1.25rem 1rem;
    margin: 0;
}

.comision-empty {
    text-align: center;
    padding: 3rem 1.5rem;
    color: #94a3b8;
}

.comision-empty--compact {
    padding: 2rem 1.5rem;
}

.comision-empty__icon {
    font-size: 2rem;
    margin-bottom: 0.75rem;
    display: block;
    color: #cbd5e1;
}

@media (max-width: 640px) {
    .comision-profile {
        flex-direction: column;
        align-items: stretch;
    }

    .comision-profile__period {
        justify-content: center;
    }

    .comision-checkout__actions {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
    }

    .comision-checkout__buttons {
        justify-content: center;
    }

    .comision-prestamo__head {
        flex-direction: column;
        align-items: flex-start;
    }

    .comision-prestamo__link {
        margin-left: 0;
        width: 100%;
        justify-content: center;
    }
}
</style>