<template>
    <div class="ben-page">
        <div class="ben-wrap">
            <!-- Encabezado -->
            <div class="ben-hero">
                <div class="ben-hero__info">
                    <div class="ben-hero__icon">
                        <i class="fa fa-hand-holding-usd"></i>
                    </div>
                    <div>
                        <h1 class="ben-hero__title">Clientes que pagan</h1>
                        <p class="ben-hero__sub">
                            Lista de pagos de clientes ordenada por fecha (más recientes primero)
                        </p>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="ben-toolbar">
                <div class="ben-toolbar__filters">
                    <div class="ben-field">
                        <label class="ben-field__label">Desde</label>
                        <input
                            v-model="fechaInicio"
                            type="date"
                            class="form-control ben-field__input"
                        />
                    </div>
                    <div class="ben-field">
                        <label class="ben-field__label">Hasta</label>
                        <input
                            v-model="fechaFin"
                            type="date"
                            class="form-control ben-field__input"
                        />
                    </div>
                    <button class="btn btn-primary ben-btn" @click="cargar">
                        <i class="fa fa-filter"></i>
                        <span>Filtrar</span>
                    </button>
                    <button class="btn btn-outline-secondary ben-btn" @click="limpiarFiltros">
                        <i class="fa fa-times"></i>
                        <span>Limpiar</span>
                    </button>
                    <button class="btn btn-outline-primary ben-btn" @click="cargar">
                        <i class="fa fa-sync-alt"></i>
                        <span>Actualizar</span>
                    </button>
                </div>
            </div>

            <!-- Resumen -->
            <div v-if="!loading && resumen" class="ben-stats">
                <div class="ben-stat">
                    <span class="ben-stat__label">Pagos</span>
                    <span class="ben-stat__value">{{ resumen.total_registros }}</span>
                </div>
                <div class="ben-stat">
                    <span class="ben-stat__label">Total pagado</span>
                    <span class="ben-stat__value">S/ {{ formatear_dinero_soles(resumen.total_pagado) }}</span>
                </div>
                <div class="ben-stat ben-stat--highlight">
                    <span class="ben-stat__label">Total intereses</span>
                    <span class="ben-stat__value ben-stat__value--primary">
                        S/ {{ formatear_dinero_soles(resumen.total_interes) }}
                    </span>
                </div>
            </div>

            <!-- Tabla -->
            <div class="ben-card">
                <div class="ben-card__head">
                    <h2 class="ben-card__title">Listado de clientes</h2>
                    <p class="ben-card__desc">Pagos de cuotas con interés cobrado, del más nuevo al más antiguo.</p>
                </div>

                <div v-if="loading" class="ben-empty">
                    <i class="fa fa-spinner fa-spin ben-empty__icon"></i>
                    <p>Cargando pagos...</p>
                </div>

                <div v-else-if="pagos.length === 0" class="ben-empty">
                    <i class="fa fa-inbox ben-empty__icon"></i>
                    <p>No hay pagos de clientes en el rango seleccionado.</p>
                </div>

                <div v-else class="ben-table-wrap">
                    <table class="table ben-table">
                        <thead>
                            <tr>
                                <th>Fecha pago</th>
                                <th>Cliente</th>
                                <th>DNI</th>
                                <th class="text-center">N° solicitud</th>
                                <th class="text-center">Cuota</th>
                                <th class="text-right">Monto pagado</th>
                                <th class="text-right">Interés</th>
                                <th class="text-center">Frecuencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(p, idx) in pagos" :key="p.ingreso_id + '-' + idx">
                                <td>
                                    <span class="ben-date">{{ p.fecha_pago_fmt }}</span>
                                </td>
                                <td>
                                    <div class="ben-cliente-cell">
                                        <span class="ben-avatar">{{ iniciales(p.cliente) }}</span>
                                        <span class="ben-cliente-cell__name">{{ p.cliente }}</span>
                                    </div>
                                </td>
                                <td>{{ p.cli_dni || '—' }}</td>
                                <td class="text-center">
                                    <span class="ben-code">{{ p.solicitud }}</span>
                                </td>
                                <td class="text-center">{{ p.periodo }}</td>
                                <td class="text-right">S/ {{ formatear_dinero_soles(p.monto_pagado) }}</td>
                                <td class="text-right">
                                    <span
                                        :class="p.interes > 0 ? 'ben-interes' : 'ben-interes ben-interes--zero'"
                                    >
                                        S/ {{ formatear_dinero_soles(p.interes) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="ben-pill">{{ p.frecuencia_pagos || '—' }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Gráfico de líneas: intereses mensuales -->
            <div class="ben-card ben-card--chart">
                <div class="ben-card__head">
                    <h2 class="ben-card__title">Intereses cobrados por mes</h2>
                    <p class="ben-card__desc">Evolución mensual del interés según el filtro de fechas.</p>
                </div>
                <div v-if="loading" class="ben-empty">
                    <i class="fa fa-spinner fa-spin ben-empty__icon"></i>
                    <p>Cargando gráfico...</p>
                </div>
                <div
                    v-else-if="!grafico.labels || grafico.labels.length === 0"
                    class="ben-empty"
                >
                    <i class="fa fa-chart-line ben-empty__icon"></i>
                    <p>Sin datos de interés para graficar.</p>
                </div>
                <div v-else ref="chartEl" class="ben-chart" style="width: 100%; height: 380px;"></div>
            </div>
        </div>
    </div>
</template>

<script>
import Axios from 'axios';
import * as echarts from 'echarts';
import { myMixin } from '../../mixin.js';

export default {
    mixins: [myMixin],
    data() {
        const now = new Date();
        const y = now.getFullYear();
        const m = String(now.getMonth() + 1).padStart(2, '0');
        const firstDay = `${y}-${m}-01`;
        const lastDay = new Date(y, now.getMonth() + 1, 0);
        const last = `${y}-${m}-${String(lastDay.getDate()).padStart(2, '0')}`;

        return {
            loading: false,
            fechaInicio: firstDay,
            fechaFin: last,
            pagos: [],
            grafico: { labels: [], intereses: [], pagos: [] },
            resumen: null,
            chartInstance: null,
        };
    },
    mounted() {
        this.cargar();
        window.addEventListener('resize', this.resizeChart);
    },
    beforeUnmount() {
        window.removeEventListener('resize', this.resizeChart);
        if (this.chartInstance) {
            this.chartInstance.dispose();
            this.chartInstance = null;
        }
    },
    methods: {
        iniciales(nombre) {
            if (!nombre) return '?';
            const parts = nombre.trim().split(/\s+/);
            const a = (parts[0] || '').charAt(0);
            const b = (parts[1] || '').charAt(0);
            return (a + b).toUpperCase() || '?';
        },
        limpiarFiltros() {
            this.fechaInicio = '';
            this.fechaFin = '';
            this.cargar();
        },
        async cargar() {
            this.loading = true;
            try {
                const { data } = await Axios.post('/load_clientes_que_pagan', {
                    fecha_inicio: this.fechaInicio || null,
                    fecha_fin: this.fechaFin || null,
                });

                if (data.success) {
                    this.pagos = data.data.pagos || [];
                    this.grafico = data.data.grafico || { labels: [], intereses: [], pagos: [] };
                    this.resumen = data.data.resumen || null;
                    this.$nextTick(() => this.renderChart());
                } else {
                    this.$swal?.fire?.('Aviso', data.message || 'No se pudieron cargar los datos', 'warning');
                }
            } catch (e) {
                console.error(e);
                this.$swal?.fire?.('Error', 'No se pudieron cargar los pagos de clientes', 'error');
            } finally {
                this.loading = false;
            }
        },
        resizeChart() {
            if (this.chartInstance) {
                this.chartInstance.resize();
            }
        },
        renderChart() {
            if (!this.$refs.chartEl) {
                if (this.chartInstance) {
                    this.chartInstance.dispose();
                    this.chartInstance = null;
                }
                return;
            }

            if (!this.chartInstance) {
                this.chartInstance = echarts.init(this.$refs.chartEl);
            }

            const labels = this.grafico.labels || [];
            const intereses = this.grafico.intereses || [];

            const option = {
                tooltip: {
                    trigger: 'axis',
                    valueFormatter: (v) => `S/ ${Number(v).toFixed(2)}`,
                },
                legend: {
                    data: ['Interés cobrado'],
                    top: 0,
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    top: 40,
                    containLabel: true,
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: labels,
                    axisLabel: {
                        rotate: labels.length > 6 ? 30 : 0,
                    },
                },
                yAxis: {
                    type: 'value',
                    name: 'Soles',
                    axisLabel: {
                        formatter: (v) => `S/ ${v}`,
                    },
                },
                series: [
                    {
                        name: 'Interés cobrado',
                        type: 'line',
                        smooth: true,
                        symbol: 'circle',
                        symbolSize: 8,
                        data: intereses,
                        areaStyle: {
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                { offset: 0, color: 'rgba(16, 185, 129, 0.35)' },
                                { offset: 1, color: 'rgba(16, 185, 129, 0.02)' },
                            ]),
                        },
                        lineStyle: {
                            width: 3,
                            color: '#10b981',
                        },
                        itemStyle: {
                            color: '#059669',
                        },
                    },
                ],
            };

            this.chartInstance.setOption(option, true);
        },
    },
};
</script>

<style scoped>
.ben-page {
    padding: 1.25rem 1rem 2.5rem;
}

.ben-wrap {
    max-width: 1200px;
    margin: 0 auto;
}

.ben-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
    flex-wrap: wrap;
    padding: 1.5rem 1.75rem;
    background: linear-gradient(135deg, #ecfdf5 0%, #ffffff 55%, #f8fafc 100%);
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    margin-bottom: 1.25rem;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
}

.ben-hero__info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.ben-hero__icon {
    width: 3rem;
    height: 3rem;
    border-radius: 0.85rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #10b981;
    color: #fff;
    font-size: 1.25rem;
}

.ben-hero__title {
    margin: 0;
    font-size: 1.35rem;
    font-weight: 700;
    color: #0f172a;
}

.ben-hero__sub {
    margin: 0.25rem 0 0;
    color: #64748b;
    font-size: 0.9rem;
}

.ben-toolbar {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 0.85rem;
    padding: 1rem 1.25rem;
    margin-bottom: 1.25rem;
}

.ben-toolbar__filters {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 0.75rem;
}

.ben-field {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.ben-field__label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.ben-field__input {
    min-width: 150px;
}

.ben-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}

.ben-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 0.85rem;
    margin-bottom: 1.25rem;
}

.ben-stat {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 0.85rem;
    padding: 1rem 1.15rem;
}

.ben-stat--highlight {
    border-color: #a7f3d0;
    background: linear-gradient(135deg, #ecfdf5, #fff);
}

.ben-stat__label {
    display: block;
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 0.35rem;
}

.ben-stat__value {
    font-size: 1.25rem;
    font-weight: 700;
    color: #0f172a;
}

.ben-stat__value--primary {
    color: #059669;
}

.ben-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    margin-bottom: 1.25rem;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
}

.ben-card__head {
    padding: 1.15rem 1.35rem 0.5rem;
}

.ben-card__title {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 700;
    color: #0f172a;
}

.ben-card__desc {
    margin: 0.25rem 0 0;
    color: #64748b;
    font-size: 0.875rem;
}

.ben-card--chart {
    padding-bottom: 0.5rem;
}

.ben-empty {
    text-align: center;
    padding: 2.5rem 1rem;
    color: #94a3b8;
}

.ben-empty__icon {
    font-size: 2rem;
    margin-bottom: 0.75rem;
    display: block;
}

.ben-table-wrap {
    overflow-x: auto;
    padding: 0 0.5rem 0.75rem;
}

.ben-table {
    width: 100%;
    margin: 0;
}

.ben-table thead th {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: #64748b;
    border-bottom: 1px solid #e2e8f0;
    white-space: nowrap;
    padding: 0.75rem 0.85rem;
}

.ben-table tbody td {
    padding: 0.8rem 0.85rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.9rem;
}

.ben-table tbody tr:hover {
    background: #f8fafc;
}

.ben-date {
    font-variant-numeric: tabular-nums;
    color: #334155;
    white-space: nowrap;
}

.ben-cliente-cell {
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.ben-avatar {
    width: 2rem;
    height: 2rem;
    border-radius: 999px;
    background: #d1fae5;
    color: #047857;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    font-weight: 700;
    flex-shrink: 0;
}

.ben-cliente-cell__name {
    font-weight: 600;
    color: #0f172a;
}

.ben-code {
    font-family: ui-monospace, monospace;
    font-size: 0.85rem;
    background: #f1f5f9;
    padding: 0.15rem 0.45rem;
    border-radius: 0.35rem;
}

.ben-interes {
    font-weight: 700;
    color: #059669;
}

.ben-interes--zero {
    color: #94a3b8;
    font-weight: 500;
}

.ben-pill {
    display: inline-block;
    font-size: 0.75rem;
    padding: 0.15rem 0.5rem;
    border-radius: 999px;
    background: #f1f5f9;
    color: #475569;
}

.ben-chart {
    padding: 0.5rem 1rem 1rem;
}
</style>
