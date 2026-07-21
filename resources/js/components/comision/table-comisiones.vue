<template>
    <div class="comision-page">
        <div class="comision-wrap">
            <!-- Encabezado -->
            <div class="comision-hero">
                <div class="comision-hero__info">
                    <div class="comision-hero__icon">
                        <i class="fa fa-percent"></i>
                    </div>
                    <div>
                        <h1 class="comision-hero__title">Comisiones de cobradores</h1>
                        <p class="comision-hero__sub">
                            Del interés cobrado:
                            <strong>{{ porcentaje }}% cobrador</strong>
                            ·
                            <strong class="comision-hero__empresa">{{ porcentajeEmpresa }}% empresa</strong>
                            · período mensual
                        </p>
                    </div>
                </div>
                <div class="comision-hero__badges">
                    <div class="comision-hero__badge">
                        <span class="comision-hero__badge-label">Cobrador</span>
                        <span class="comision-hero__badge-value">{{ porcentaje }}%</span>
                    </div>
                    <div class="comision-hero__badge comision-hero__badge--empresa">
                        <span class="comision-hero__badge-label">Empresa</span>
                        <span class="comision-hero__badge-value">{{ porcentajeEmpresa }}%</span>
                    </div>
                    <div class="comision-hero__badge">
                        <span class="comision-hero__badge-label">Período</span>
                        <span class="comision-hero__badge-value">{{ mesLabel }} {{ anio }}</span>
                    </div>
                </div>
            </div>

            <!-- Barra de filtros -->
            <div class="comision-toolbar">
                <div class="comision-toolbar__filters">
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
                    <button class="btn btn-primary comision-btn-refresh" @click="cargar">
                        <i class="fa fa-sync-alt"></i>
                        <span>Actualizar</span>
                    </button>
                </div>

                <div v-if="puedeProcesar" class="comision-toolbar__config">
                    <div class="comision-field comision-field--sm">
                        <label class="comision-field__label">% Cobrador</label>
                        <div class="comision-input-group">
                            <input
                                v-model.number="porcentajeEdit"
                                type="number"
                                min="0"
                                max="100"
                                step="0.5"
                                class="form-control comision-field__input"
                            />
                            <button
                                class="btn btn-outline-primary"
                                :disabled="guardandoConfig"
                                @click="guardarPorcentaje"
                            >
                                {{ guardandoConfig ? '...' : 'Guardar' }}
                            </button>
                        </div>
                        <p class="comision-field__hint">
                            Empresa se queda con
                            <strong>{{ porcentajeEmpresaEdit }}%</strong>
                            del interés
                        </p>
                    </div>
                </div>
            </div>

            <!-- Resumen -->
            <div v-if="!loading && periodos.length" class="comision-stats">
                <div class="comision-stat">
                    <span class="comision-stat__label">Cobradores</span>
                    <span class="comision-stat__value">{{ periodos.length }}</span>
                </div>
                <div class="comision-stat">
                    <span class="comision-stat__label">Pendientes</span>
                    <span class="comision-stat__value comision-stat__value--warn">{{ totalPendientes }}</span>
                </div>
                <div class="comision-stat">
                    <span class="comision-stat__label">Interés del mes</span>
                    <span class="comision-stat__value">S/ {{ formatear_dinero_soles(totalInteresMes) }}</span>
                </div>
                <div class="comision-stat comision-stat--highlight">
                    <span class="comision-stat__label">Cobradores ({{ porcentaje }}%)</span>
                    <span class="comision-stat__value comision-stat__value--primary">S/ {{ formatear_dinero_soles(totalComisionMes) }}</span>
                </div>
                <div class="comision-stat comision-stat--empresa">
                    <span class="comision-stat__label">Empresa ({{ porcentajeEmpresa }}%)</span>
                    <span class="comision-stat__value comision-stat__value--empresa">S/ {{ formatear_dinero_soles(totalEmpresaMes) }}</span>
                </div>
            </div>

            <!-- Tabla -->
            <div class="comision-card">
                <div class="comision-card__head">
                    <h2 class="comision-card__title">Listado de cobradores</h2>
                    <p class="comision-card__desc">Seleccione un cobrador para ver su historial y acumulado del mes.</p>
                </div>

                <div v-if="loading" class="comision-empty">
                    <i class="fa fa-spinner fa-spin comision-empty__icon"></i>
                    <p>Cargando comisiones...</p>
                </div>

                <div v-else-if="periodos.length === 0" class="comision-empty">
                    <i class="fa fa-inbox comision-empty__icon"></i>
                    <p>No hay comisiones acumuladas para este mes.</p>
                </div>

                <div v-else class="comision-table-wrap">
                    <table class="table comision-table">
                        <thead>
                            <tr>
                                <th>Cobrador</th>
                                <th class="text-center">Préstamos</th>
                                <th class="text-center">Cuotas</th>
                                <th class="text-right">Interés</th>
                                <th class="text-right">Comisión ({{ porcentaje }}%)</th>
                                <th class="text-right">Empresa ({{ porcentajeEmpresa }}%)</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="p in periodos" :key="p.comision_periodo_id">
                                <td>
                                    <div class="comision-cobrador-cell">
                                        <span class="comision-avatar">{{ iniciales(p) }}</span>
                                        <span class="comision-cobrador-cell__name">{{ nombreTrabajador(p) }}</span>
                                    </div>
                                </td>
                                <td class="text-center">{{ p.prestamos_count ?? '—' }}</td>
                                <td class="text-center">{{ p.detalles_count }}</td>
                                <td class="text-right">S/ {{ formatear_dinero_soles(p.monto_interes_pagado) }}</td>
                                <td class="text-right">
                                    <span class="comision-monto">S/ {{ formatear_dinero_soles(p.monto_acumulado) }}</span>
                                </td>
                                <td class="text-right">
                                    <span class="comision-monto comision-monto--empresa">
                                        S/ {{ formatear_dinero_soles(empresaDePeriodo(p)) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span :class="p.status === 'P' ? 'comision-pill comision-pill--pending' : 'comision-pill comision-pill--done'">
                                        {{ p.status === 'P' ? 'Pendiente' : 'Procesado' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-primary btn-sm comision-btn-action" @click="verAcumulado(p)">
                                        <i class="fa fa-chart-line"></i>
                                        Ver acumulado
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Axios from 'axios';
import { myMixin } from '../../mixin.js';
import { porcentajeEmpresa, empresaDesdeInteres } from '../../utils/comisionHelper.js';

export default {
    mixins: [myMixin],
    data() {
        const now = new Date();
        return {
            periodos: [],
            loading: false,
            mes: now.getMonth() + 1,
            anio: now.getFullYear(),
            porcentaje: 30,
            porcentajeEdit: 30,
            guardandoConfig: false,
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
        mesLabel() {
            return this.meses.find((m) => m.val === this.mes)?.label ?? '';
        },
        porcentajeEmpresa() {
            return porcentajeEmpresa(this.porcentaje);
        },
        porcentajeEmpresaEdit() {
            return porcentajeEmpresa(this.porcentajeEdit);
        },
        totalPendientes() {
            return this.periodos.filter((p) => p.status === 'P').length;
        },
        totalInteresMes() {
            return this.periodos.reduce((s, p) => s + parseFloat(p.monto_interes_pagado || 0), 0);
        },
        totalComisionMes() {
            return this.periodos.reduce((s, p) => s + parseFloat(p.monto_acumulado || 0), 0);
        },
        totalEmpresaMes() {
            return Math.round((this.totalInteresMes - this.totalComisionMes) * 100) / 100;
        },
    },
    mounted() {
        this.cargarConfig();
        this.cargar();
    },
    methods: {
        empresaDePeriodo(p) {
            return empresaDesdeInteres(p.monto_interes_pagado, this.porcentaje);
        },
        iniciales(p) {
            const t = p.trabajador;
            if (!t) return '?';
            const n = (t.name || '').charAt(0);
            const a = (t.lastname || '').charAt(0);
            return (n + a).toUpperCase() || '?';
        },
        nombreTrabajador(p) {
            const t = p.trabajador;
            if (!t) return '—';
            return `${t.name || ''} ${t.lastname || ''}`.trim();
        },
        async cargarConfig() {
            try {
                const { data } = await Axios.get('/get_comision_config');
                if (data.success) {
                    this.porcentaje = data.porcentaje;
                    this.porcentajeEdit = data.porcentaje;
                }
            } catch (e) { /* ignore */ }
        },
        async guardarPorcentaje() {
            if (this.porcentajeEdit < 0 || this.porcentajeEdit > 100) {
                this.$swal.fire('Error', 'El porcentaje debe estar entre 0 y 100', 'error');
                return;
            }
            this.guardandoConfig = true;
            try {
                const { data } = await Axios.post('/save_comision_config', { porcentaje: this.porcentajeEdit });
                if (data.success) {
                    this.porcentaje = data.porcentaje;
                    this.$swal.fire('Guardado', data.message, 'success');
                } else {
                    this.$swal.fire('Error', data.message || 'No se pudo guardar', 'error');
                }
            } catch (e) {
                this.$swal.fire('Error', e.response?.data?.message || 'Error del servidor', 'error');
            } finally {
                this.guardandoConfig = false;
            }
        },
        async cargar() {
            this.loading = true;
            try {
                const { data } = await Axios.post('/load_comisiones', { mes: this.mes, anio: this.anio });
                if (data.success) {
                    this.periodos = data.data || [];
                    if (data.porcentaje != null) {
                        this.porcentaje = data.porcentaje;
                        this.porcentajeEdit = data.porcentaje;
                    }
                }
            } catch (e) {
                this.$swal.fire('Error', 'No se pudieron cargar las comisiones', 'error');
            } finally {
                this.loading = false;
            }
        },
        verAcumulado(periodo) {
            const trabajadorId = periodo.trabajador?.urlapi;
            if (!trabajadorId) {
                this.$swal.fire('Error', 'No se encontró el cobrador.', 'error');
                return;
            }
            window.location.href = `/comisiones/cobrador/${trabajadorId}?mes=${this.mes}&anio=${this.anio}`;
        },
    },
};
</script>

<style scoped>
.comision-page {
    padding: 1.25rem 1rem 2.5rem;
}

.comision-wrap {
    max-width: 1100px;
    margin: 0 auto;
}

.comision-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
    flex-wrap: wrap;
    padding: 1.5rem 1.75rem;
    background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 55%, #f8fafc 100%);
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    margin-bottom: 1.25rem;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
}

.comision-hero__info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.comision-hero__icon {
    width: 3rem;
    height: 3rem;
    border-radius: 0.85rem;
    background: #05be50;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
}

.comision-hero__title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #0f172a;
    margin: 0;
    line-height: 1.3;
}

.comision-hero__sub {
    font-size: 0.875rem;
    color: #64748b;
    margin: 0.2rem 0 0;
}

.comision-hero__badges {
    display: flex;
    flex-wrap: wrap;
    gap: 0.65rem;
}

.comision-hero__badge {
    text-align: center;
    padding: 0.65rem 1rem;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    min-width: 100px;
}

.comision-hero__badge--empresa {
    border-color: #bfdbfe;
    background: #eff6ff;
}

.comision-hero__badge-label {
    display: block;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #94a3b8;
    font-weight: 600;
}

.comision-hero__badge-value {
    display: block;
    font-size: 1rem;
    font-weight: 600;
    color: #05be50;
    margin-top: 0.15rem;
}

.comision-hero__badge--empresa .comision-hero__badge-value {
    color: #1e40af;
}

.comision-hero__empresa {
    color: #1e40af;
}

.comision-field__hint {
    margin: 0.4rem 0 0;
    font-size: 0.75rem;
    color: #64748b;
}

.comision-field__hint strong {
    color: #1e40af;
}

.comision-toolbar {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
    padding: 1rem 1.25rem;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 0.85rem;
    margin-bottom: 1.25rem;
}

.comision-toolbar__filters {
    display: flex;
    align-items: flex-end;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.comision-toolbar__config {
    margin-left: auto;
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

.comision-input-group {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.comision-btn-refresh {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    border-radius: 0.5rem;
    padding: 0.45rem 1rem;
    height: 38px;
}

.comision-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-bottom: 1.25rem;
}

.comision-stat {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 0.85rem;
    padding: 1rem 1.25rem;
    text-align: center;
}

.comision-stat--highlight {
    background: linear-gradient(135deg, #f0fdf4, #fff);
    border-color: #bbf7d0;
}

.comision-stat--empresa {
    background: linear-gradient(135deg, #eff6ff, #fff);
    border-color: #bfdbfe;
}

.comision-stat__label {
    display: block;
    font-size: 0.75rem;
    color: #64748b;
    margin-bottom: 0.35rem;
}

.comision-stat__value {
    display: block;
    font-size: 1.25rem;
    font-weight: 700;
    color: #0f172a;
}

.comision-stat__value--warn { color: #d97706; }
.comision-stat__value--primary { color: #05be50; }
.comision-stat__value--empresa { color: #1e40af; }

.comision-monto--empresa {
    color: #1e40af !important;
    font-weight: 700;
}

.comision-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
}

.comision-card__head {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f1f5f9;
    text-align: center;
}

.comision-card__title {
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
    margin: 0;
}

.comision-card__desc {
    font-size: 0.8rem;
    color: #94a3b8;
    margin: 0.35rem 0 0;
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
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #94a3b8;
    font-weight: 600;
    border-bottom: 2px solid #f1f5f9;
    padding: 0.85rem 0.75rem;
    white-space: nowrap;
}

.comision-table tbody td {
    padding: 0.9rem 0.75rem;
    vertical-align: middle;
    border-bottom: 1px solid #f8fafc;
    font-size: 0.875rem;
}

.comision-table tbody tr:hover {
    background: #f8fafc;
}

.comision-cobrador-cell {
    display: flex;
    align-items: center;
    gap: 0.65rem;
}

.comision-avatar {
    width: 2.1rem;
    height: 2.1rem;
    border-radius: 50%;
    background: #e0f2fe;
    color: #0369a1;
    font-size: 0.7rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.comision-cobrador-cell__name {
    font-weight: 500;
    color: #0f172a;
}

.comision-monto {
    font-weight: 600;
    color: #05be50;
}

.comision-pill {
    display: inline-block;
    padding: 0.2rem 0.65rem;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 600;
}

.comision-pill--pending {
    background: #fef3c7;
    color: #b45309;
}

.comision-pill--done {
    background: #dcfce7;
    color: #15803d;
}

.comision-btn-action {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    border-radius: 0.5rem;
    white-space: nowrap;
}

.comision-empty {
    text-align: center;
    padding: 3rem 1.5rem;
    color: #94a3b8;
}

.comision-empty__icon {
    font-size: 2rem;
    margin-bottom: 0.75rem;
    display: block;
    color: #cbd5e1;
}
</style>