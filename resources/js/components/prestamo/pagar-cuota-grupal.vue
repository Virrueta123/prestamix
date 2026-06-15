<template>
    <ScrollPanel style="width: 100%; height: 400px">
        <div class="intro-y box mt-5">
            <form id="form_crear_ingreso_cuota_grupal" method="POST" action="#">
                <div id="vertical-form">
                    <div class="">

                        <div class="text-primary font-semibold text-3xl">{{ estado }}</div>

                        <div id="striped-rows-table ">
                            <div class="preview">
                                <div class="overflow-x-auto">
                                    <table
                                        class="table table-pago table-xs table-bordered rounded border-1 border-primary"
                                        style="font-size: 11px;">
                                        <thead>
                                            <tr>
                                                <th class="whitespace-nowrap">Descripcion</th>
                                                <th class="whitespace-nowrap">Monto cuota</th>
                                                <th class="whitespace-nowrap">Mora</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(p_g, index_p_g) in pago_grupal" :key="index_p_g">
                                                <td>Pago cuota {{ p_g.periodo }} de la solicitud n°
                                                    {{ get_cuota.solicitud.code }} </td>
                                                <td> {{ formatear_dinero_soles(p_g.cuota) }}</td>
                                                <td v-if="p_g.yes_mora == 'Y'"> {{
                                                    formatear_dinero_soles(totalMora) }}
                                                </td>
                                                <td v-else> S/. 0.00 </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr class="text-primary">
                                                <td>Totales</td>
                                                <td class="bg-primary text-center text-white">{{
                                                    formatear_dinero_soles(totalCuota) }}</td>
                                                <td class="bg-primary text-center text-white">{{
                                                    formatear_dinero_soles(totalMora) }}</td>
                                            </tr>
                                            <tr class="text-primary">
                                                <td>Total de la cuota + mora</td>
                                                <td colspan="2" class="bg-primary text-center text-white">{{
                                                    formatear_dinero_soles(totalGeneral) }}</td>
                                            </tr>

                                            <tr class="text-primary">
                                                <td>Total que amortizaron</td>
                                                <td colspan="2" class="bg-primary text-center text-white"> {{
                                                    total_pagado_amortizado }} </td>
                                            </tr>

                                            <tr class="text-primary">
                                                <td>Total restante</td>
                                                <td colspan="2" class="bg-primary text-center text-white"> {{
                                                    formatear_dinero_soles(totalrestante) }} </td>
                                            </tr>

                                            <tr class="text-primary" v-if="adelanto != 0">
                                                <td>Adelanto de prestamo</td>
                                                <td colspan="2" class="bg-primary text-center text-white"> {{ adelanto
                                                    }} </td>
                                            </tr>

                                            <tr class="text-primary">
                                                <td>Saldo restante</td>
                                                <td colspan="2" class="bg-primary text-center text-white"> {{
                                                    formatear_dinero_soles(saldo_restante) }} </td>
                                            </tr>

                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="preview" v-if="pagos.length">
                            <button @click="agregar_cuenta()" v-if="is_btn_pagos" type="button"
                                class="btn btn-primary mr-1 mt-2 mb-2">
                                <Icon icon="plus" />
                            </button>
                            <div>
                                <table class="table table-bordered table-pago table-hover" style="font-size: 11px;">
                                    <thead>
                                        <tr>
                                            <th class="whitespace-nowrap">#</th>
                                            <th class="whitespace-nowrap">Cuenta</th>
                                            <th class="whitespace-nowrap">Monto</th>
                                            <th class="whitespace-nowrap text-center">Eliminar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(pago, index_pago) in pagos" :key="index_pago">
                                            <td>{{ pago.numero }}</td>
                                            <td> <select-cuenta v-on:change="change_cuenta(index_pago, $event)"
                                                    @comunicarCuenta="escucharCuenta"
                                                    v-model="cuentas_id"></select-cuenta>
                                            </td>
                                            <td>
                                                <InputNumber class="form-control p-2 border border-success"
                                                    type="number" v-model="pagos[index_pago].monto"
                                                    @input="keyup_cuenta(index_pago, $event)" name="monto"
                                                    placeholder="Monto del gasto" inputId="locale-us" locale="en-US"
                                                    :minFractionDigits="2" />

                                            </td>
                                            <td class="m-auto">
                                                <button @click="deleted_pago(index_pago)" v-if="index_pago == 1"
                                                    type="button" name="" id="" class="btn btn-danger m-auto btn-xs">
                                                    <Icon icon='trash' class='' />
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>



                        <div class="grid grid-cols-12 gap-6 mt-2">

                            <div class=" col-span-12 lg:col-span-6">
                                <label for="vertical-form-2" class="form-label" id="switch1">Este pago es en
                                    <strong>Oficina</strong>?
                                </label>

                                <div class="form-check form-switch">
                                    <label class="form-check-label mr-2" for="checkbox-switch-7">No </label>
                                    <input id="checkbox-switch-7" v-model="yes_office" checked class="form-check-input"
                                        type="checkbox">
                                    <label class="form-check-label ml-2" for="checkbox-switch-7">Si </label>
                                </div>

                            </div>
                        </div>


                        <div id="basic-button" class="p-1 mt-4" v-if="is_btn_insertar">
                            <div class="preview">
                                <button type="submit" class="btn btn-primary mr-1 mb-2">
                                    <Icon icon="plus" class="ml-2" /> Registrar Ingreso
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </form>

        </div>
    </ScrollPanel>
</template>

<script>
import $ from 'jquery';

import 'tom-select/dist/css/tom-select.css';
import TomSelect from 'tom-select';
import Axios from 'axios';

import 'jquery-validation';
import 'jquery-validation/dist/localization/messages_es';

import moment from 'moment';
import 'moment/locale/es';

// mixin
import {
    myMixin
} from "../../mixin.js";

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
        totalMora() {
            return this.pago_grupal.reduce((sum, p_g) => {
                if (p_g.yes_mora == "Y") {
                    const fecha_vencimiento = moment(p_g.fechaVencimiento);
                    const fecha_actual = moment();

                    var diferencia = fecha_actual.diff(fecha_vencimiento, 'days');

                    var interes_by_days = p_g.interes / 30;

                    var interes_cuota_actual = interes_by_days * diferencia;

                    

                    return sum + parseFloat(interes_cuota_actual);
                }
                this.mora =  interes_cuota_actual ;
                return sum;
            }, 0);
        },
        totalCuota() {
            return this.pago_grupal.reduce((sum, p_g) => sum + parseFloat(p_g.cuota), 0);
        },
        totalCuota_pagos() {
            return this.pagos.reduce((sum, pa) => sum + parseFloat(pa.monto), 0);
        },
        totalrestante() {
            var total = this.redondear(this.totalGeneral - this.total_pagado_amortizado);

            if (this.totalCuota_pagos == 0) {
                this.is_btn_pagos = false;
            } else {
                this.is_btn_pagos = true;
            }

            console.log(total);
            console.log("=");
            console.log(this.totalCuota_pagos);

            if (parseFloat(total) == parseFloat(this.totalCuota_pagos)) {
                console.log("Pagar Cuota completa");
                this.estado = "Pagar Cuota completa";
                this.adelanto = 0;
            } else {
                if (total < this.totalCuota_pagos) {
                    this.adelanto = this.redondear(this.totalCuota_pagos - total);
                    this.estado = "Adelanto";
                } else {
                    this.adelanto = this.totalCuota_pagos;
                    this.estado = "Adelanto";
                }
            }
            return this.redondear(total);
        },
        totalGeneral() {
            const totalMora = this.totalMora;
            const totalCuota = this.totalCuota;

            var total = this.redondear(totalMora + totalCuota);

            return this.redondear(total);
        }
    },
    data() {
        return {
            localeOptions: {
                decimalSeparator: '.',
                thousandsSeparator: ''
            },
            estado: "Pagar Cuota",
            get_cuota: this.$attrs.get_cuota,
            pago_grupal: this.$attrs.pago_grupal,
            descripcion: "",
            monto: "",
            cuentas_id: "",
            yes_office: true,
            pagos: [],
            adelanto: 0,
            is_btn_pagos: false,
            is_btn_insertar: true,
            detalle_ingresos: [],
            mora:0
        }
    },
    methods: {

        load_ingresos_por_cuota() {
            const data = {
                urlapi: this.get_cuota.cuota_actual.urlapi
            };

            const headers = this.headers;

            this.loading_start();

            Axios
                .post("/load_ingresos_por_cuota", data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {
                        this.detalle_ingresos = response.data.data;
                        console.log(this.detalle_ingresos);

                        this.pagos.push({
                            numero: 1,
                            cuentas_id: "MFNhZTlXUkhTSmpWNDQ5ZUc2YUhldz09",
                            monto: parseFloat(this.totalrestante)
                        });
                    } else {
                        this.alert_warning(response.data.data);
                    }
                    this.loading_end();
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor");
                    console.error(error);
                });
        },
        deleted_pago(index) {
            this.pagos[0].monto = parseFloat(this.pagos[0].monto) + parseFloat(this.pagos[index].monto);
            this.pagos.splice(index, 1);
        },
        // agergar otra cuenta
        agregar_cuenta() {

            if (this.pagos.length < 2) {
                this.is_btn_pagos = false;
                this.pagos.push({
                    numero: 2,
                    cuentas_id: "MFNhZTlXUkhTSmpWNDQ5ZUc2YUhldz09",
                    monto: 0
                });
            }

        },

        keyup_cuenta(index, evento) {
            if (this.totalCuota_pagos == this.totalGeneral) {
                this.is_btn_pagos = false;
            } else {
                this.is_btn_pagos = true;
            }
        },
        change_cuenta(index, evento) {
            this.pagos[index].cuentas_id = evento.target.value;
        },


        change_cuenta(index, evento) {
            this.pagos[index].cuentas_id = evento.target.value;
        },
        yes_mora(fechaVencimiento, yes_interes) {
            const fechaDada = moment(fechaVencimiento);
            const fechaActual = moment();
            // Comparar la fecha actual con la fecha dada 

            if (fechaDada.isBefore(fechaActual, 'day')) {
                if (yes_interes == "Y") {
                    return true;
                } else {
                    return false;
                }

            } else if (fechaDada.isSame(fechaActual, 'day')) {
                return false;
            } else {
                return false;
            }
        },
        escucharCuenta(event) {
            this.cuentas_id = event;
            console.log("dsdadasd" + event);
        },
        // Your methods here
        crear_ingreso_cuenta() {
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
                saldo_restante: this.saldo_restante
            };

            const headers = this.headers;

            this.loading_start();

            Axios
                .post("/ingreso_cuota_grupal", data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {
                        window.location.assign(response.data.data);
                    } else {
                        this.alert_warning(response.data.message);
                    }
                    this.loading_end();
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor");
                    console.error(error);
                });
        }
    },
    mounted() {

        var self = this;

        $("#form_crear_ingreso_cuota_grupal").validate({
            rules: {
                cuentas_id: {
                    required: true
                }
            },
            submitHandler: function (form) {

                try {
                    self.crear_ingreso_cuenta();
                } catch (error) {
                    console.log(error);
                }
                return false;
            }
        });

        this.load_ingresos_por_cuota();


    }
}
</script>

<style></style>