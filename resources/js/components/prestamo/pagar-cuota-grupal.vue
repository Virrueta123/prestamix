<template>
    <ScrollPanel style="width: 100%; height: 420px">
        <div class="intro-y box mt-2">
            <form id="form_crear_ingreso_cuota_grupal" method="POST" action="#">
                <div class="p-3">
                    <cobrador-comision-banner
                        :get-cuota="get_cuota"
                        :pago-grupal="pago_grupal"
                    ></cobrador-comision-banner>

                    <p class="pg-estado">
                        <strong>{{ estado }}</strong>
                        — {{ pago_grupal.length }} cuota(s) seleccionada(s)
                    </p>

                    <!-- Detalle por cuota -->
                    <table class="pg-table">
                        <thead>
                            <tr>
                                <th>Cuota / solicitud</th>
                                <th class="text-right">Interés</th>
                                <th class="text-right">Mora del sistema</th>
                                <th class="text-right">Mora a cobrar</th>
                                <th class="text-right">Comisión sobre interés</th>
                                <th class="text-right">Comisión sobre mora</th>
                                <th class="text-right">Comisión total cobrador</th>
                                <th class="text-right">Monto de la cuota</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(p_g, index_p_g) in pago_grupal" :key="index_p_g">
                                <td>
                                    Cuota {{ p_g.periodo }}
                                    — solicitud n° {{ get_cuota.solicitud.code }}
                                    <template v-if="p_g.yes_mora == 'Y'">
                                        <br />
                                        <small class="pg-muted">
                                            Atraso: {{ diasAtraso(p_g) }} día(s) (mora auto. máx. 30 días)
                                        </small>
                                    </template>
                                </td>
                                <td class="text-right">
                                    {{ formatear_dinero_soles(interesLinea(p_g)) }}
                                </td>
                                <td class="text-right">
                                    <template v-if="p_g.yes_mora == 'Y'">
                                        {{ formatear_dinero_soles(p_g.mora_calculada || 0) }}
                                    </template>
                                    <template v-else>S/. 0.00</template>
                                </td>
                                <td class="text-right">
                                    <template v-if="p_g.yes_mora == 'Y'">
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            class="pg-importe"
                                            :value="p_g.monto_mora_cobrar"
                                            @input="onMoraInput(p_g, $event)"
                                            @blur="onMoraBlur(p_g)"
                                            title="Mora a cobrar (puede ser menor a la del sistema)"
                                        />
                                        <button
                                            type="button"
                                            class="pg-link-auto"
                                            @click="restaurarMora(p_g)"
                                            title="Restaurar mora del sistema"
                                        >
                                            auto
                                        </button>
                                    </template>
                                    <template v-else>S/. 0.00</template>
                                </td>
                                <td class="text-right">
                                    {{ formatear_dinero_soles(comisionInteresLinea(p_g)) }}
                                </td>
                                <td class="text-right">
                                    {{ formatear_dinero_soles(comisionMoraLinea(p_g)) }}
                                </td>
                                <td class="text-right pg-main">
                                    {{ formatear_dinero_soles(comisionLinea(p_g)) }}
                                </td>
                                <td class="text-right">
                                    {{ formatear_dinero_soles(p_g.cuota) }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td><strong>Totales del pago</strong></td>
                                <td class="text-right">
                                    <strong>{{ formatear_dinero_soles(totalInteresPago) }}</strong>
                                </td>
                                <td class="text-right">
                                    <strong>{{ formatear_dinero_soles(totalMoraCalculada) }}</strong>
                                </td>
                                <td class="text-right">
                                    <strong>{{ formatear_dinero_soles(totalMora) }}</strong>
                                </td>
                                <td class="text-right">
                                    <strong>{{ formatear_dinero_soles(totalComisionInteres) }}</strong>
                                </td>
                                <td class="text-right">
                                    <strong>{{ formatear_dinero_soles(totalComisionMora) }}</strong>
                                </td>
                                <td class="text-right pg-main">
                                    <strong>{{ formatear_dinero_soles(totalComisionPago) }}</strong>
                                </td>
                                <td class="text-right">
                                    <strong>{{ formatear_dinero_soles(totalCuota) }}</strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    <!-- Resumen del cobro al cliente -->
                    <table class="pg-table pg-table--mt">
                        <thead>
                            <tr>
                                <th colspan="2">Resumen del cobro al cliente</th>
                            </tr>
                            <tr>
                                <th>Descripción</th>
                                <th class="text-right">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Suma de las cuotas</td>
                                <td class="text-right">{{ formatear_dinero_soles(totalCuota) }}</td>
                            </tr>
                            <tr>
                                <td>Mora a cobrar (puede ser menor a la del sistema)</td>
                                <td class="text-right">{{ formatear_dinero_soles(totalMora) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total cuota + mora</strong></td>
                                <td class="text-right pg-main">
                                    <strong>{{ formatear_dinero_soles(totalGeneral) }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td>Ya amortizado en pagos anteriores</td>
                                <td class="text-right">{{ total_pagado_amortizado }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total restante a pagar</strong></td>
                                <td class="text-right">
                                    <strong>{{ formatear_dinero_soles(totalrestante) }}</strong>
                                </td>
                            </tr>
                            <tr v-if="adelanto != 0">
                                <td>Adelanto (monto que excede lo restante)</td>
                                <td class="text-right">{{ adelanto }}</td>
                            </tr>
                            <tr>
                                <td>Saldo que quedaría después de este pago</td>
                                <td class="text-right">{{ formatear_dinero_soles(saldo_restante) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Medio de pago -->
                    <div v-if="pagos.length" class="pg-table--mt">
                        <div class="pg-pay-head">
                            <strong>Medio de pago</strong>
                            <button
                                v-if="is_btn_pagos"
                                @click="agregar_cuenta()"
                                type="button"
                                class="btn btn-primary btn-sm"
                            >
                                <Icon icon="plus" class="mr-1" /> Agregar cuenta
                            </button>
                        </div>
                        <table class="pg-table">
                            <thead>
                                <tr>
                                    <th style="width: 3rem">#</th>
                                    <th>Cuenta</th>
                                    <th class="text-right" style="width: 10rem">Monto</th>
                                    <th class="text-center" style="width: 4rem">Quitar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(pago, index_pago) in pagos" :key="index_pago">
                                    <td>{{ pago.numero }}</td>
                                    <td>
                                        <select-cuenta
                                            v-on:change="change_cuenta(index_pago, $event)"
                                            @comunicarCuenta="escucharCuenta"
                                            v-model="cuentas_id"
                                        ></select-cuenta>
                                    </td>
                                    <td class="text-right">
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            name="monto"
                                            class="pg-importe pg-importe--wide"
                                            v-model.number="pagos[index_pago].monto"
                                            @input="keyup_cuenta(index_pago, $event)"
                                        />
                                    </td>
                                    <td class="text-center">
                                        <button
                                            v-if="index_pago == 1"
                                            @click="deleted_pago(index_pago)"
                                            type="button"
                                            class="btn btn-outline-danger btn-sm"
                                        >
                                            <Icon icon="trash" />
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="pg-foot">
                        <div class="form-check form-switch mb-0">
                            <label class="form-check-label mr-2" for="checkbox-switch-7">
                                ¿Este pago es en oficina?
                            </label>
                            <input
                                id="checkbox-switch-7"
                                v-model="yes_office"
                                class="form-check-input"
                                type="checkbox"
                            />
                        </div>
                        <button
                            v-if="is_btn_insertar"
                            type="submit"
                            class="btn btn-primary"
                        >
                            <Icon icon="plus" class="mr-1" /> Registrar ingreso
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </ScrollPanel>
</template>

<script>
import $ from 'jquery';
import 'tom-select/dist/css/tom-select.css';
import Axios from 'axios';
import 'jquery-validation';
import 'jquery-validation/dist/localization/messages_es';
import { myMixin } from '../../mixin.js';
import {
    interesBaseCuota,
    moraCobradaCuota,
    moraCalculadaCuota,
    diasAtrasoCuota,
    comisionDesdeInteresBase,
    comisionDesdeMora,
    comisionTotalCuota,
    inicializarMoraCuota,
} from '../../utils/comisionHelper.js';

export default {
    mixins: [myMixin],
    computed: {
        saldo_restante() {
            if (this.totalrestante >= this.totalCuota_pagos) {
                this.is_btn_insertar = true;
            } else {
                this.is_btn_insertar = false;
            }
            return this.totalrestante - this.totalCuota_pagos;
        },
        total_pagado_amortizado() {
            return this.detalle_ingresos.reduce((sum, pa) => sum + parseFloat(pa.ingreso.monto), 0);
        },
        totalMoraCalculada() {
            return this.pago_grupal.reduce((sum, p_g) => {
                if (p_g.yes_mora !== 'Y') return sum;
                return sum + parseFloat(p_g.mora_calculada || 0);
            }, 0);
        },
        totalMora() {
            return this.pago_grupal.reduce((sum, p_g) => sum + moraCobradaCuota(p_g), 0);
        },
        totalCuota() {
            return this.pago_grupal.reduce((sum, p_g) => sum + parseFloat(p_g.cuota), 0);
        },
        totalCuota_pagos() {
            return this.pagos.reduce((sum, pa) => sum + parseFloat(pa.monto || 0), 0);
        },
        totalrestante() {
            var total = this.redondear(this.totalGeneral - this.total_pagado_amortizado);

            if (this.totalCuota_pagos == 0) {
                this.is_btn_pagos = false;
            } else {
                this.is_btn_pagos = true;
            }

            if (parseFloat(total) == parseFloat(this.totalCuota_pagos)) {
                this.estado = 'Pagar cuota completa';
                this.adelanto = 0;
            } else if (total < this.totalCuota_pagos) {
                this.adelanto = this.redondear(this.totalCuota_pagos - total);
                this.estado = 'Adelanto';
            } else {
                this.adelanto = this.totalCuota_pagos;
                this.estado = 'Adelanto';
            }
            return this.redondear(total);
        },
        totalGeneral() {
            return this.redondear(this.totalMora + this.totalCuota);
        },
        totalInteresPago() {
            return this.pago_grupal.reduce((sum, p) => sum + this.interesLinea(p), 0);
        },
        totalComisionInteres() {
            return this.pago_grupal.reduce(
                (sum, p) => sum + comisionDesdeInteresBase(p, this.porcentajeComision),
                0
            );
        },
        totalComisionMora() {
            return this.pago_grupal.reduce(
                (sum, p) => sum + comisionDesdeMora(p, this.porcentajeComision),
                0
            );
        },
        totalComisionPago() {
            return this.redondear(this.totalComisionInteres + this.totalComisionMora);
        },
    },
    data() {
        return {
            estado: 'Pagar cuota',
            get_cuota: this.$attrs.get_cuota,
            pago_grupal: this.$attrs.pago_grupal,
            descripcion: '',
            monto: '',
            cuentas_id: '',
            yes_office: true,
            pagos: [],
            adelanto: 0,
            is_btn_pagos: false,
            is_btn_insertar: true,
            detalle_ingresos: [],
            mora: 0,
            porcentajeComision: 30,
        };
    },
    methods: {
        interesLinea(pG) {
            return interesBaseCuota(pG);
        },
        diasAtraso(pG) {
            return diasAtrasoCuota(pG);
        },
        comisionInteresLinea(pG) {
            return comisionDesdeInteresBase(pG, this.porcentajeComision);
        },
        comisionMoraLinea(pG) {
            return comisionDesdeMora(pG, this.porcentajeComision);
        },
        comisionLinea(pG) {
            return comisionTotalCuota(pG, this.porcentajeComision);
        },
        inicializarMorasPago() {
            if (!Array.isArray(this.pago_grupal)) return;
            this.pago_grupal.forEach((p) => inicializarMoraCuota(p));
        },
        onMoraInput(pG, event) {
            const raw = event?.target?.value;
            const v = parseFloat(raw);
            pG.monto_mora_cobrar = Number.isNaN(v) || v < 0 ? 0 : Math.round(v * 100) / 100;
            if (this.pagos.length === 1) {
                this.$nextTick(() => {
                    this.pagos[0].monto = parseFloat(this.totalrestante);
                });
            }
        },
        onMoraBlur(pG) {
            const v = parseFloat(pG.monto_mora_cobrar);
            pG.monto_mora_cobrar = Number.isNaN(v) || v < 0 ? 0 : Math.round(v * 100) / 100;
        },
        restaurarMora(pG) {
            const calc = moraCalculadaCuota(pG);
            pG.mora_calculada = calc;
            pG.monto_mora_cobrar = calc;
            if (this.pagos.length === 1) {
                this.$nextTick(() => {
                    this.pagos[0].monto = parseFloat(this.totalrestante);
                });
            }
        },
        cargarPorcentajeComision() {
            Axios.get('/get_comision_config')
                .then((res) => {
                    if (res.data?.success) {
                        this.porcentajeComision = res.data.porcentaje;
                    }
                })
                .catch(() => {});
        },
        load_ingresos_por_cuota() {
            const data = { urlapi: this.get_cuota.cuota_actual.urlapi };
            const headers = this.headers;
            this.loading_start();

            Axios.post('/load_ingresos_por_cuota', data, { headers })
                .then((response) => {
                    if (response.data.success) {
                        this.detalle_ingresos = response.data.data;
                        this.pagos.push({
                            numero: 1,
                            cuentas_id: 'MFNhZTlXUkhTSmpWNDQ5ZUc2YUhldz09',
                            monto: parseFloat(this.totalrestante),
                        });
                    } else {
                        this.alert_warning(response.data.data);
                    }
                    this.loading_end();
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal('Error en el servidor');
                    console.error(error);
                });
        },
        deleted_pago(index) {
            this.pagos[0].monto = parseFloat(this.pagos[0].monto) + parseFloat(this.pagos[index].monto);
            this.pagos.splice(index, 1);
        },
        agregar_cuenta() {
            if (this.pagos.length < 2) {
                this.is_btn_pagos = false;
                this.pagos.push({
                    numero: 2,
                    cuentas_id: 'MFNhZTlXUkhTSmpWNDQ5ZUc2YUhldz09',
                    monto: 0,
                });
            }
        },
        keyup_cuenta() {
            this.is_btn_pagos = this.totalCuota_pagos != this.totalGeneral;
        },
        change_cuenta(index, evento) {
            this.pagos[index].cuentas_id = evento.target.value;
        },
        escucharCuenta(event) {
            this.cuentas_id = event;
        },
        crear_ingreso_cuenta() {
            this.pago_grupal.forEach((p) => {
                if (p.yes_mora === 'Y') {
                    p.monto_mora_cobrar = moraCobradaCuota(p);
                    p.mora_calculada = p.mora_calculada ?? moraCalculadaCuota(p);
                } else {
                    p.monto_mora_cobrar = 0;
                    p.mora_calculada = 0;
                }
            });

            const data = {
                totalGeneral: this.totalGeneral,
                pago_grupal: this.pago_grupal,
                get_prestamo: this.get_cuota,
                descripcion: this.descripcion,
                monto: this.monto,
                adelanto: this.adelanto,
                cuentas_id: this.cuentas_id,
                yes_office: this.yes_office,
                pagos: this.pagos,
                totalCuota_pagos: this.totalCuota_pagos,
                saldo_restante: this.saldo_restante,
            };

            const headers = this.headers;
            this.loading_start();

            Axios.post('/ingreso_cuota_grupal', data, { headers })
                .then((response) => {
                    if (response.data.success) {
                        window.location.assign(response.data.data);
                    } else {
                        this.alert_warning(response.data.message);
                    }
                    this.loading_end();
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal('Error en el servidor');
                    console.error(error);
                });
        },
    },
    mounted() {
        this.cargarPorcentajeComision();
        this.inicializarMorasPago();
        var self = this;

        $('#form_crear_ingreso_cuota_grupal').validate({
            rules: {
                cuentas_id: { required: true },
            },
            submitHandler: function () {
                try {
                    self.crear_ingreso_cuenta();
                } catch (error) {
                    console.log(error);
                }
                return false;
            },
        });

        this.load_ingresos_por_cuota();
    },
};
</script>

<style scoped>
.pg-estado {
    margin: 0 0 0.6rem;
    font-size: 13px;
    color: #374151;
}

.pg-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11px;
    border: 1px solid #e5e7eb;
    background: #fff;
}

.pg-table--mt {
    margin-top: 0.75rem;
}

.pg-table th,
.pg-table td {
    border: 1px solid #e5e7eb;
    padding: 0.4rem 0.45rem;
    vertical-align: middle;
}

.pg-table thead th {
    background: #f9fafb;
    color: #374151;
    font-weight: 600;
    font-size: 10px;
    white-space: nowrap;
}

.pg-table tbody td {
    color: #111827;
}

.pg-table tfoot td {
    background: #f9fafb;
}

.pg-table .text-right {
    text-align: right;
}

.pg-table .text-center {
    text-align: center;
}

.pg-main {
    color: #05be50;
    font-weight: 600;
}

.pg-muted {
    color: #6b7280;
    font-size: 10px;
}

/* Importe editable: mismo tamaño visual que los montos de la tabla */
.pg-importe {
    display: inline-block;
    width: 5.5rem;
    max-width: 100%;
    height: 1.5rem;
    margin: 0;
    padding: 0 0.25rem;
    border: 1px solid #d1d5db;
    border-radius: 2px;
    background: #fff;
    color: #111827;
    font-size: 11px;
    font-family: inherit;
    line-height: 1.4;
    text-align: right;
    vertical-align: middle;
    box-shadow: none;
    -moz-appearance: textfield;
}

.pg-importe::-webkit-outer-spin-button,
.pg-importe::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.pg-importe:focus {
    border-color: #9ca3af;
    outline: none;
    box-shadow: none;
}

.pg-importe--wide {
    width: 6.5rem;
}

.pg-link-auto {
    display: inline;
    margin-left: 0.25rem;
    padding: 0;
    border: none;
    background: none;
    color: #6b7280;
    font-size: 10px;
    text-decoration: underline;
    cursor: pointer;
    vertical-align: middle;
    line-height: 1;
}

.pg-link-auto:hover {
    color: #374151;
}

.pg-pay-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.35rem;
    font-size: 13px;
}

.pg-foot {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    margin-top: 0.85rem;
    padding-top: 0.65rem;
    border-top: 1px solid #e5e7eb;
    flex-wrap: wrap;
}
</style>
