<template>
    <div class="home-page">
        <div class="home-stats">
            <div class="home-kpi">
                <div class="home-kpi__top">
                    <span class="home-kpi__label">Capital total</span>
                    <span class="home-kpi__icon home-kpi__icon--green"><i class="fa fa-credit-card"></i></span>
                </div>
                <p class="home-kpi__value">S/ {{ formatear_dinero_soles(resumen.capital_total) }}</p>
                <p class="home-kpi__hint">{{ resumen.prestamos_activos }} préstamos activos</p>
            </div>

            <div class="home-kpi">
                <div class="home-kpi__top">
                    <span class="home-kpi__label">Interés del mes</span>
                    <span class="home-kpi__icon home-kpi__icon--blue"><i class="fa fa-percent"></i></span>
                </div>
                <p class="home-kpi__value">S/ {{ formatear_dinero_soles(resumen.interes_mes) }}</p>
                <p class="home-kpi__hint">Interés cobrado en {{ mesLabel }}</p>
                <p class="home-kpi__delta" :class="deltaClass(resumen.delta_interes)">
                    <i :class="deltaIcon(resumen.delta_interes)"></i>
                    {{ absDelta(resumen.delta_interes) }}% vs mes anterior
                </p>
            </div>

            <div class="home-kpi home-kpi--highlight">
                <div class="home-kpi__top">
                    <span class="home-kpi__label">Comisión trabajador</span>
                    <span class="home-kpi__icon home-kpi__icon--green"><i class="fa fa-hand-holding-usd"></i></span>
                </div>
                <p class="home-kpi__value home-kpi__value--green">S/ {{ formatear_dinero_soles(resumen.comision_trabajador) }}</p>
                <p class="home-kpi__hint">{{ resumen.porcentaje_comision }}% del interés del mes</p>
                <p class="home-kpi__delta" :class="deltaClass(resumen.delta_comision)">
                    <i :class="deltaIcon(resumen.delta_comision)"></i>
                    {{ absDelta(resumen.delta_comision) }}% vs mes anterior
                </p>
            </div>

            <div class="home-kpi">
                <div class="home-kpi__top">
                    <span class="home-kpi__label">Clientes activos</span>
                    <span class="home-kpi__icon home-kpi__icon--cyan"><i class="fa fa-users"></i></span>
                </div>
                <p class="home-kpi__value">{{ resumen.clientes_activos }}</p>
                <p class="home-kpi__hint">Con préstamo vigente</p>
            </div>
        </div>

        <div class="home-mid">
            <div class="home-card home-calc">
                <div class="home-calc__title">
                    <i class="fa fa-calculator"></i>
                    Calculadora de préstamo
                </div>

                <label class="home-calc__label">Monto de crédito</label>
                <div class="home-calc__money">
                    <span>S/</span>
                    <input v-model.number="monto_credito" type="number" min="0" step="0.01" />
                </div>

                <div class="home-calc__row">
                    <div>
                        <label class="home-calc__label">Intereses (%)</label>
                        <input v-model.number="interes" type="number" min="0" step="0.5" />
                    </div>
                    <div>
                        <label class="home-calc__label">Fecha de desembolso</label>
                        <input v-model="fecha" type="date" />
                    </div>
                </div>

                <label class="home-calc__label">Frecuencia de los pagos</label>
                <div class="home-calc__tabs">
                    <button
                        v-for="f in frecuencias"
                        :key="f"
                        type="button"
                        :class="{ 'is-active': frecuencia_pagos === f }"
                        @click="frecuencia_pagos = f"
                    >{{ f }}</button>
                </div>

                <label class="home-calc__label">{{ frecuencia_pagos_a }}</label>
                <input v-model.number="intervalo" type="number" min="1" :placeholder="'2 ' + frecuencia_pagos_a" />

                <div v-if="frecuencia_pagos === 'Mensual'" class="home-calc__extra">
                    <label class="home-calc__check">
                        <input v-model="is_fecha_pago" type="checkbox" />
                        Cambiar fecha de 1.ª cuota
                    </label>
                    <input
                        v-if="is_fecha_pago"
                        v-model="fecha_de_pago_cuota"
                        type="date"
                        :min="fecha"
                    />
                    <p v-if="is_fecha_pago" class="home-calc__warn">Máximo 20 días después del desembolso.</p>
                </div>

                <div v-if="is_cronograma" class="home-calc__cuota">
                    <div class="home-calc__cuota-row">
                        <span>Cuota {{ frecuencia_pagos.toLowerCase() }}</span>
                        <strong>S/ {{ formatear_dinero_soles(cuotas) }}</strong>
                    </div>
                    <div class="home-calc__cuota-meta">
                        {{ intervalo }} {{ frecuencia_pagos_a.toLowerCase() }} · tasa {{ Number(interes).toFixed(2) }}%
                    </div>
                </div>

                <div v-if="is_cronograma" class="home-calc__totals">
                    <div><span>Total a pagar</span><strong>S/ {{ formatear_dinero_soles(sumar_cuota) }}</strong></div>
                    <div><span>Total intereses</span><b class="c-blue">S/ {{ formatear_dinero_soles(sumar_interes) }}</b></div>
                    <div>
                        <span>Comisión ({{ resumen.porcentaje_comision }}%)</span>
                        <b class="c-amber">S/ {{ formatear_dinero_soles(comisionSimulada) }}</b>
                    </div>
                </div>

                <div v-if="is_cronograma && cronograma.length" class="home-calc__crono">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Vence</th>
                                <th>Cuota</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(fila, idx) in cronograma" :key="idx">
                                <td>{{ fila.periodo }}</td>
                                <td>{{ fila.fechaVencimiento }}</td>
                                <td>S/ {{ formatear_dinero_soles(fila.cuota) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="button" class="home-calc__btn" @click="calcular_cronograma">
                    Generar préstamo
                </button>
            </div>

            <div class="home-card home-chart-card">
                <div class="home-card__head">
                    <div>
                        <h3 class="home-card__title">Evolución mensual</h3>
                        <p class="home-card__desc">Préstamos y comisiones — últimos 7 meses</p>
                    </div>
                    <div class="home-legend">
                        <span><i class="home-dot home-dot--loan"></i> Préstamos</span>
                        <span><i class="home-dot home-dot--com"></i> Comisiones</span>
                    </div>
                </div>

                <div class="home-chart" v-if="!loading">
                    <div class="home-chart__y">
                        <span v-for="tick in yTicks" :key="tick">S/{{ tick }}</span>
                    </div>
                    <div class="home-chart__plot">
                        <div
                            v-for="item in resumen.evolucion"
                            :key="item.label + item.anio"
                            class="home-chart__col"
                            @mouseenter="hoverChart = item"
                            @mouseleave="hoverChart = null"
                        >
                            <div class="home-chart__pair">
                                <div
                                    class="home-chart__bar home-chart__bar--loan"
                                    :style="{ height: barHeight(item.prestamos) }"
                                ></div>
                                <div
                                    class="home-chart__bar home-chart__bar--com"
                                    :style="{ height: barHeight(item.comisiones) }"
                                ></div>
                            </div>
                            <span class="home-chart__x">{{ item.label }}</span>
                            <div v-if="hoverChart && hoverChart.label === item.label && hoverChart.anio === item.anio" class="home-chart__tip">
                                <strong>{{ item.label }}</strong>
                                <div><span class="home-dot home-dot--loan"></span> Préstamos: S/ {{ formatear_dinero_soles(item.prestamos) }}</div>
                                <div><span class="home-dot home-dot--com"></span> Comisiones: S/ {{ formatear_dinero_soles(item.comisiones) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="home-empty">Cargando gráfico...</div>
            </div>
        </div>

        <div class="home-card home-table-card">
            <div class="home-card__head">
                <h3 class="home-card__title">Últimos 5 clientes</h3>
                <a href="/cliente" class="home-link">Ver todos <i class="fa fa-chevron-right"></i></a>
            </div>

            <div v-if="loading" class="home-empty">Cargando...</div>
            <div v-else-if="!resumen.ultimos_clientes.length" class="home-empty">Aún no hay clientes registrados.</div>
            <div v-else class="home-table-wrap">
                <table class="home-table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Monto</th>
                            <th>Cuota</th>
                            <th>Plazo</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(c, i) in resumen.ultimos_clientes" :key="i">
                            <td>
                                <a v-if="c.urlapi" :href="'/cliente/' + c.urlapi" class="home-client">{{ c.nombre || '—' }}</a>
                                <span v-else>{{ c.nombre || '—' }}</span>
                            </td>
                            <td class="c-green">S/ {{ formatear_dinero_soles(c.monto) }}</td>
                            <td>S/ {{ formatear_dinero_soles(c.cuota) }}</td>
                            <td class="muted">{{ c.plazo || '—' }}</td>
                            <td>
                                <span :class="pillClass(c.estado_key)">
                                    <i :class="pillIcon(c.estado_key)"></i>
                                    {{ c.estado }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import Axios from 'axios';
import { myMixin } from '../../mixin.js';

export default {
    mixins: [myMixin],
    data() {
        return {
            loading: false,
            hoverChart: null,
            frecuencias: ['Quincenal', 'Semanal', 'Mensual'],
            monto_credito: 1000,
            cuotas: 0,
            fecha_desembolso: null,
            frecuencia_pagos: 'Quincenal',
            frecuencia_pagos_a: 'Quincenas',
            interes: 15,
            intervalo: 3,
            is_cronograma: false,
            cronograma: [],
            fecha: null,
            is_fecha_pago: false,
            fecha_de_pago_cuota: null,
            d_t: 0,
            resumen: {
                capital_total: 0,
                prestamos_activos: 0,
                interes_mes: 0,
                comision_trabajador: 0,
                porcentaje_comision: 30,
                clientes_activos: 0,
                mes: new Date().getMonth() + 1,
                anio: new Date().getFullYear(),
                ultimos_clientes: [],
                evolucion: [],
                delta_interes: 0,
                delta_comision: 0,
            },
        };
    },
    computed: {
        mesLabel() {
            const meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            return meses[this.resumen.mes] || '';
        },
        chartMax() {
            const vals = (this.resumen.evolucion || []).flatMap((e) => [e.prestamos || 0, e.comisiones || 0]);
            const max = Math.max(...vals, 1);
            const mag = Math.pow(10, Math.max(0, Math.floor(Math.log10(max))));
            return Math.ceil(max / mag) * mag || 1;
        },
        yTicks() {
            const max = this.chartMax;
            const ticks = [];
            for (let i = 4; i >= 0; i--) {
                const v = (max / 4) * i;
                ticks.push(v >= 1000 ? `${Math.round(v / 1000)}k` : `${Math.round(v)}`);
            }
            return ticks;
        },
        sumar_interes() {
            if (!this.cronograma.length) return 0;
            const importe = this.cronograma.reduce((acc, res) => acc + parseFloat(res.interes || 0), 0);
            return this.redondear(importe);
        },
        sumar_cuota() {
            if (!this.cronograma.length) return 0;
            const importe = this.cronograma.reduce((acc, res) => acc + parseFloat(res.cuota || 0), 0);
            return this.redondear(importe);
        },
        comisionSimulada() {
            return (Number(this.sumar_interes) || 0) * ((Number(this.resumen.porcentaje_comision) || 0) / 100);
        },
    },
    watch: {
        fecha(newValue) {
            this.fecha_desembolso = newValue;
        },
        frecuencia_pagos(newValue) {
            this.is_cronograma = false;
            if (newValue === 'Quincenal') this.frecuencia_pagos_a = 'Quincenas';
            else if (newValue === 'Semanal') this.frecuencia_pagos_a = 'Semanas';
            else this.frecuencia_pagos_a = 'Menses';
        },
        monto_credito() { this.is_cronograma = false; },
        interes() { this.is_cronograma = false; },
        intervalo() { this.is_cronograma = false; },
        is_fecha_pago() { this.is_cronograma = false; },
    },
    methods: {
        barHeight(value) {
            const pct = Math.max(2, ((Number(value) || 0) / this.chartMax) * 100);
            return `${Math.min(100, pct)}%`;
        },
        absDelta(v) {
            return Math.abs(Number(v) || 0).toFixed(1);
        },
        deltaClass(v) {
            return Number(v) < 0 ? 'is-down' : 'is-up';
        },
        deltaIcon(v) {
            return Number(v) < 0 ? 'fa fa-arrow-down' : 'fa fa-arrow-up';
        },
        pillClass(key) {
            if (key === 'activo') return 'home-pill home-pill--ok';
            if (key === 'pagado') return 'home-pill home-pill--pay';
            if (key === 'pendiente') return 'home-pill home-pill--wait';
            return 'home-pill';
        },
        pillIcon(key) {
            if (key === 'pendiente') return 'fa fa-clock';
            if (key === 'none') return 'fa fa-minus';
            return 'fa fa-check-circle';
        },
        calcular_cronograma() {
            if (!this.monto_credito || !this.intervalo || !this.interes) {
                return;
            }
            if (!this.fecha) {
                this.fecha = this.hoyIso();
            }
            this.fecha_desembolso = this.fecha;

            switch (this.frecuencia_pagos) {
                case 'Semanal':
                    this.cronograma = this.calcularAmortizacionFrancesSemanalByDate(
                        this.monto_credito,
                        parseInt(this.intervalo),
                        parseInt(this.interes),
                        this.fecha
                    );
                    break;
                case 'Quincenal':
                    this.cronograma = this.calcularAmortizacionFrancesQuincenal(
                        this.monto_credito,
                        parseInt(this.intervalo),
                        parseInt(this.interes),
                        this.fecha
                    );
                    break;
                case 'Mensual':
                    if (this.is_fecha_pago && this.fecha_de_pago_cuota) {
                        if (this.is_now(this.fecha_desembolso)) {
                            this.cronograma = this.calcularAmortizacionFrancesMensual(
                                this.monto_credito,
                                parseInt(this.intervalo),
                                parseInt(this.interes),
                                this.fecha_de_pago_cuota
                            );
                        } else {
                            this.cronograma = this.calcularAmortizacionFrancesMensualFechaDesembolsoCambiada(
                                this.monto_credito,
                                parseInt(this.intervalo),
                                parseInt(this.interes),
                                this.fecha_desembolso,
                                this.fecha_de_pago_cuota
                            );
                        }
                    } else if (!this.is_now(this.fecha_desembolso)) {
                        this.cronograma = this.calcularAmortizacionFrancesMensual(
                            this.monto_credito,
                            parseInt(this.intervalo),
                            parseInt(this.interes),
                            this.fecha_desembolso
                        );
                    } else {
                        this.cronograma = this.calcularAmortizacionFrancesMensual(
                            this.monto_credito,
                            parseInt(this.intervalo),
                            parseInt(this.interes)
                        );
                    }
                    this.fecha_desembolso = this.fecha;
                    break;
            }

            if (this.is_fecha_pago && this.fecha_de_pago_cuota && this.cronograma && this.cronograma.length) {
                const differenceInDays = this.diffDays(this.fecha, this.fecha_de_pago_cuota);
                const dias_antes_cuota = differenceInDays + 1;
                const monto_interes_mes = this.monto_credito * this.interes / 100;
                const monto_por_dia = monto_interes_mes / 30;
                const monto_del_intervalo = monto_por_dia * dias_antes_cuota;
                this.d_t = (this.d_t || 0) + monto_del_intervalo;
                this.cronograma.unshift({
                    periodo: 0,
                    fechaVencimiento: this.formatFechaCorta(this.fecha_de_pago_cuota),
                    saldoCapital: 0,
                    amortizacion: 0,
                    interes: monto_del_intervalo,
                    cuota: monto_del_intervalo,
                });
            }

            const cuotaRef = (this.cronograma || []).find((r) => parseInt(r.periodo, 10) > 0) || (this.cronograma || [])[0];
            this.cuotas = cuotaRef ? parseFloat(cuotaRef.cuota) || 0 : 0;
            this.is_cronograma = Array.isArray(this.cronograma) && this.cronograma.length > 0;
        },
        hoyIso() {
            const d = new Date();
            const m = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${d.getFullYear()}-${m}-${day}`;
        },
        diffDays(from, to) {
            const a = new Date(from);
            const b = new Date(to);
            return Math.round((b - a) / 86400000);
        },
        formatFechaCorta(iso) {
            if (!iso) return '—';
            const d = new Date(`${iso}T00:00:00`);
            return `${d.getDate()}/${d.getMonth() + 1}/${d.getFullYear()}`;
        },
        async cargar() {
            this.loading = true;
            try {
                const { data } = await Axios.post('/dashboard_resumen');
                if (data.success) {
                    this.resumen = { ...this.resumen, ...data.data };
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },
    },
    mounted() {
        this.fecha = this.hoyIso();
        this.fecha_desembolso = this.fecha;
        this.cargar();
    },
};
</script>

<style scoped>
.home-page {
    display: flex;
    flex-direction: column;
    gap: 1.1rem;
    padding-bottom: 1.5rem;
}

.home-stats {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 1rem;
}

.home-kpi {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 0.9rem;
    padding: 1.1rem 1.2rem;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}

.home-kpi--highlight {
    background: linear-gradient(135deg, #f0fdf4, #fff);
    border-color: #bbf7d0;
}

.home-kpi__top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.home-kpi__label {
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: #94a3b8;
}

.home-kpi__icon {
    width: 2rem;
    height: 2rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.78rem;
}

.home-kpi__icon--green { background: #dcfce7; color: #059669; }
.home-kpi__icon--blue { background: #dbeafe; color: #2563eb; }
.home-kpi__icon--cyan { background: #cffafe; color: #0891b2; }

.home-kpi__value {
    margin: 0.55rem 0 0;
    font-size: 1.45rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.15;
}

.home-kpi__value--green { color: #05be50; }

.home-kpi__hint {
    margin: 0.3rem 0 0;
    font-size: 0.75rem;
    color: #94a3b8;
}

.home-kpi__delta {
    margin: 0.45rem 0 0;
    font-size: 0.72rem;
    font-weight: 600;
}

.home-kpi__delta.is-up { color: #059669; }
.home-kpi__delta.is-down { color: #dc2626; }

.home-mid {
    display: grid;
    grid-template-columns: minmax(280px, 1fr) minmax(0, 2fr);
    gap: 1rem;
}

.home-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 0.9rem;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}

.home-card__head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
    padding: 1.1rem 1.25rem 0.4rem;
}

.home-card__title {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 600;
    color: #0f172a;
}

.home-card__desc {
    margin: 0.2rem 0 0;
    font-size: 0.75rem;
    color: #94a3b8;
}

.home-legend {
    display: flex;
    gap: 0.9rem;
    font-size: 0.75rem;
    color: #64748b;
}

.home-dot {
    display: inline-block;
    width: 0.55rem;
    height: 0.55rem;
    border-radius: 50%;
    margin-right: 0.3rem;
    vertical-align: middle;
}

.home-dot--loan { background: #05be50; }
.home-dot--com { background: #3b82f6; }

.home-chart {
    display: grid;
    grid-template-columns: 48px 1fr;
    gap: 0.4rem;
    padding: 0.5rem 1.25rem 1.2rem;
    min-height: 240px;
}

.home-chart__y {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    font-size: 0.65rem;
    color: #94a3b8;
    padding: 0.2rem 0 1.4rem;
    text-align: right;
}

.home-chart__plot {
    display: flex;
    align-items: stretch;
    gap: 0.35rem;
    border-bottom: 1px solid #f1f5f9;
    position: relative;
}

.home-chart__col {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-end;
    position: relative;
    min-width: 0;
}

.home-chart__pair {
    display: flex;
    align-items: flex-end;
    gap: 4px;
    height: 180px;
    width: 100%;
    justify-content: center;
}

.home-chart__bar {
    width: 14px;
    max-width: 42%;
    border-radius: 4px 4px 0 0;
    min-height: 4px;
}

.home-chart__bar--loan { background: #05be50; }
.home-chart__bar--com { background: #3b82f6; }

.home-chart__x {
    font-size: 0.7rem;
    color: #64748b;
    margin-top: 0.4rem;
}

.home-chart__tip {
    position: absolute;
    bottom: 48px;
    left: 50%;
    transform: translateX(-50%);
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 0.6rem;
    padding: 0.5rem 0.7rem;
    font-size: 0.72rem;
    color: #334155;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.12);
    white-space: nowrap;
    z-index: 3;
}

.home-calc { padding: 1.15rem 1.2rem 1.25rem; }

.home-calc__title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 1rem;
}

.home-calc__title i { color: #05be50; }

.home-calc__label {
    display: block;
    font-size: 0.72rem;
    color: #64748b;
    margin: 0.7rem 0 0.35rem;
}

.home-calc__money {
    display: flex;
    align-items: center;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 0.55rem;
    padding: 0 0.75rem;
}

.home-calc__money span {
    color: #64748b;
    font-weight: 600;
    margin-right: 0.4rem;
}

.home-calc__money input,
.home-calc select,
.home-calc input[type="number"],
.home-calc input[type="date"] {
    width: 100%;
    height: 42px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    border-radius: 0.5rem;
    padding: 0.45rem 0.7rem;
    font-size: 0.875rem;
    color: #0f172a;
    outline: none;
    box-sizing: border-box;
}

.home-calc__money input {
    border: 0;
    background: transparent;
    height: 42px;
    padding: 0.45rem 0;
}

.home-calc__tabs {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.35rem;
    background: #f1f5f9;
    padding: 0.3rem;
    border-radius: 0.55rem;
}

.home-calc__tabs button {
    border: 0;
    background: transparent;
    font-size: 0.75rem;
    font-weight: 600;
    color: #64748b;
    padding: 0.4rem 0.2rem;
    border-radius: 0.4rem;
    cursor: pointer;
}

.home-calc__tabs button.is-active {
    background: #fff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
}

.home-calc__row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.7rem;
}

.home-calc__cuota {
    margin-top: 0.9rem;
    background: #eff6ff;
    border: 1px solid #dbeafe;
    border-radius: 0.55rem;
    padding: 0.75rem 0.9rem;
}

.home-calc__cuota-row {
    display: flex;
    justify-content: space-between;
    font-size: 0.8rem;
    color: #1d4ed8;
}

.home-calc__cuota-row strong {
    color: #05be50;
    font-size: 1rem;
}

.home-calc__cuota-meta {
    margin-top: 0.25rem;
    font-size: 0.68rem;
    color: #3b82f6;
}

.home-calc__totals {
    margin-top: 0.85rem;
    padding-top: 0.75rem;
    border-top: 1px solid #e2e8f0;
}

.home-calc__totals div {
    display: flex;
    justify-content: space-between;
    font-size: 0.8rem;
    color: #64748b;
    margin-bottom: 0.4rem;
}

.home-calc__totals strong { color: #0f172a; }
.c-blue { color: #2563eb; }
.c-amber { color: #d97706; }
.c-green { color: #05be50; font-weight: 600; }

.home-calc__btn {
    display: block;
    width: 100%;
    border: 0;
    text-align: center;
    margin-top: 0.85rem;
    background: #2563eb;
    color: #fff;
    font-weight: 600;
    font-size: 0.875rem;
    padding: 0.7rem;
    border-radius: 0.5rem;
    cursor: pointer;
}

.home-calc__btn:hover { background: #1d4ed8; color: #fff; }

.home-calc__extra {
    margin-top: 0.55rem;
}

.home-calc__check {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.75rem;
    color: #475569;
    margin-bottom: 0.4rem;
}

.home-calc__warn {
    margin: 0.3rem 0 0;
    font-size: 0.68rem;
    color: #dc2626;
}

.home-calc__crono {
    margin-top: 0.7rem;
    max-height: 140px;
    overflow: auto;
    border: 1px solid #f1f5f9;
    border-radius: 0.5rem;
}

.home-calc__crono table {
    width: 100%;
    font-size: 0.7rem;
    margin: 0;
}

.home-calc__crono th,
.home-calc__crono td {
    padding: 0.35rem 0.5rem;
    text-align: left;
    white-space: nowrap;
}

.home-calc__crono th {
    color: #94a3b8;
    font-weight: 600;
    position: sticky;
    top: 0;
    background: #fff;
}

.home-table-card .home-card__head { padding-bottom: 0.85rem; border-bottom: 1px solid #f1f5f9; }

.home-link {
    font-size: 0.78rem;
    color: #05be50;
    font-weight: 600;
    text-decoration: none;
}

.home-table-wrap { overflow-x: auto; }

.home-table {
    width: 100%;
    margin: 0;
    font-size: 0.82rem;
}

.home-table th {
    text-align: left;
    font-size: 0.68rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #94a3b8;
    font-weight: 600;
    padding: 0.75rem 1.15rem;
    border-bottom: 1px solid #f1f5f9;
}

.home-table td {
    padding: 0.85rem 1.15rem;
    border-bottom: 1px solid #f8fafc;
    color: #0f172a;
    white-space: nowrap;
}

.home-table tr:hover td { background: #f8fafc; }

.home-client {
    color: #0f172a;
    font-weight: 600;
    text-decoration: none;
}

.home-client:hover { color: #05be50; }

.muted { color: #94a3b8; }

.home-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.15rem 0.6rem;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 600;
    background: #f1f5f9;
    color: #64748b;
}

.home-pill--ok { background: #dcfce7; color: #15803d; }
.home-pill--pay { background: #dbeafe; color: #1d4ed8; }
.home-pill--wait { background: #fef3c7; color: #b45309; }

.home-empty {
    text-align: center;
    padding: 2.5rem 1rem;
    color: #94a3b8;
    font-size: 0.85rem;
}

@media (max-width: 1100px) {
    .home-mid { grid-template-columns: 1fr; }
}

@media (max-width: 900px) {
    .home-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .home-kpi { padding: 0.9rem 0.95rem; }
    .home-kpi__value { font-size: 1.2rem; }
}

@media (max-width: 640px) {
    .home-page { gap: 0.8rem; }
    .home-stats { gap: 0.65rem; }
    .home-calc__row { grid-template-columns: 1fr; }
    .home-chart {
        grid-template-columns: 32px 1fr;
        padding: 0.25rem 0.7rem 1rem;
        min-height: 200px;
    }
    .home-chart__pair { height: 150px; }
    .home-chart__bar { width: 9px; }
    .home-chart__tip {
        left: 0;
        transform: none;
        max-width: calc(100vw - 3rem);
        white-space: normal;
    }
    .home-card__head { padding: 0.9rem 0.9rem 0.35rem; }
    .home-calc { padding: 0.95rem 0.9rem 1rem; }
    .home-table th,
    .home-table td { padding: 0.7rem 0.75rem; }
    .home-calc__tabs button { font-size: 0.68rem; }
}
</style>
