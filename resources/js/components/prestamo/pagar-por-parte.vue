<template>
    <ScrollPanel style="width: 100%; height: 400px">
        <div class="intro-y box mt-5">
            <form id="form_crear_ingreso_cuota_grupal" method="POST" action="#">
                <div id="vertical-form">
                    <div class="">

                        <div class="grid grid-cols-12 gap-6 mt-2">
                            <div
                                class="p-5 border-t border-slate-200/60 dark:border-darkmode-400 col-span-12 lg:col-span-4">
                                <table class="table">
                                    <thead>

                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="border-b dark:border-darkmode-400 text-right"> Saldo pendiente
                                            </td>
                                            <td class="border-b dark:border-darkmode-400 text-right"> {{
                                                formatear_dinero_soles(saldo_pendiente) }} </td>
                                        </tr>
                                        <tr>
                                            <td class="border-b dark:border-darkmode-400 text-right"> Amortizacion </td>
                                            <td class="border-b dark:border-darkmode-400 text-right"> {{
                                                formatear_dinero_soles(totalCuota_pagos) }} </td>
                                        </tr>

                                        <tr>
                                            <td class="border-b dark:border-darkmode-400 text-right"> Saldo restante
                                            </td>
                                            <td class="border-b dark:border-darkmode-400 text-right"
                                                v-if="!is_cancelado"> {{ formatear_dinero_soles(saldo_pendiente -
                                                    totalCuota_pagos) }} </td>
                                            <td class="border-b dark:border-darkmode-400 text-right" v-else> Cancelado
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>


                            </div>
                        </div>

                        <div class="preview">
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
                                            <th class="whitespace-nowrap">Eliminar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(pago, index_pago) in pagos" :key="index_pago">
                                            <td>{{ pago.numero }}</td>
                                            <td> <select-cuenta v-on:change="change_cuenta(index_pago, $event)"
                                                    @comunicarCuenta="escucharCuenta"
                                                    v-model="cuentas_id"></select-cuenta></td>
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
                                    <strong>oficina</strong>?
                                </label>

                                <div class="form-check form-switch">
                                    <label class="form-check-label mr-2" for="checkbox-switch-7">No </label>
                                    <input id="checkbox-switch-7" v-model="yes_office" checked class="form-check-input"
                                        type="checkbox">
                                    <label class="form-check-label ml-2" for="checkbox-switch-7">Si </label>
                                </div>

                            </div>
                        </div>


                        <div id="basic-button" class="p-1 mt-4">
                            <div class="preview">
                                <button v-if="btn_ingreso" type="submit" class="btn btn-primary mr-1 mb-2">
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
        totalCuota_pagos() {
            return this.pagos.reduce((sum, pa) => sum + parseFloat(pa.monto), 0);
        },
        saldo_pendiente() {
            return this.redondear(this.redondear(this.get_cuota.d_t) - this.sumar_ingresos)
        },
        is_cancelado() {
            if(this.totalCuota_pagos == 0){
                this.btn_ingreso = false;
            }else{
                this.btn_ingreso = true;
            }
 
            
            if (this.totalCuota_pagos > this.saldo_pendiente) { 
                if (this.pagos.length == 0) {
                    this.pagos[0].monto = this.saldo_pendiente;
                } else { 
                    this.pagos[0].monto = this.saldo_pendiente;
                    this.pagos.splice(1, 1);
                }
            }

            if (this.totalCuota_pagos == this.saldo_pendiente) {
                return true;
            } else {
                return false;
            }
        }
    },
    data() {
        return {
            localeOptions: {
                decimalSeparator: '.',
                thousandsSeparator: ''
            },
            get_cuota: this.$attrs.get_cuota,
            sumar_ingresos: this.$attrs.sumar_ingresos,
            descripcion: "",
            monto: "",
            cuentas_id: "",
            yes_office: true,
            pagos: [],
            is_btn_pagos: false,
            btn_ingreso: false
        }
    },
    created() {
        this.pagos.push({
            numero: 1,
            cuentas_id: "MFNhZTlXUkhTSmpWNDQ5ZUc2YUhldz09",
            monto: 0
        });
    },
    methods: {
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
        },
        // Your methods here
        crear_ingreso_por_parte() {

            var alMenosUnoEsCero = this.pagos.some(function (pago) {
                return pago.monto === 0;
            });

            if (alMenosUnoEsCero) {
                this.alert_warning("Al menos uno de los pagos tiene un monto igual a 0.")
               return false;
            }  

            const data = {
                get_prestamo: this.get_cuota,
                monto_total: this.totalCuota_pagos,
                yes_office: this.yes_office,
                pagos: this.pagos,
                is_cancelado: this.is_cancelado,
                saldo_pendiente: this.saldo_pendiente
            };

            const headers = this.headers;

            this.loading_start();

            Axios
                .post("/ingreso_cuota_por_parte", data, {
                    headers,
                }) 
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
                    self.crear_ingreso_por_parte();
                } catch (error) {
             
                }
                return false;
            }
        });

        

    }
}
</script>

<style></style>