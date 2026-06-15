<template>

    <div class="intro-y box mt-5">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
            <h2 class="font-medium text-base mr-auto text-primary">
                <Icon icon='table-list' class='mr-2' /> Formulario para crear un gasto
            </h2>
        </div>

        <form id="form_crear_gasto" method="POST" action="#">
            <div id="vertical-form" class="p-5">
                <div class="">

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-12">
                            <label for="vertical-form-2" class="form-label">Nombre </label>
                            <input name="gastos_descripcion" v-model="gastos_descripcion" type="text"
                                class="form-control" placeholder="Descripcion">
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Monto del gasto </label>
                            <InputNumber class="form-control p-2 border border-success" v-model="monto" name="monto"
                                placeholder="Monto del gasto" inputId="locale-us" locale="en-US"
                                :minFractionDigits="2" />

                        </div>

                        <div v-if="yes_cliente" class="intro-y col-span-12 lg:col-span-6">
                            <label for="regular-form-1" class="form-label">Buscar ingreso</label>
                            <div class="input-group">
                                <select-ingreso></select-ingreso>
                            </div>
                        </div>

                        <div v-else class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label"> Tipo de gatos </label>
                            <select-tipo-gasto v-on:change="change_tipo_gasto_id()"
                                v-model="tipo_gasto_id"></select-tipo-gasto>
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6 mt-4">

                        <div class=" col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label" id="switch1">
                                Deseas retirar el monto guardado de un <strong>cliente</strong> ?
                            </label>

                            <div class="form-check form-switch">
                                <label class="form-check-label mr-2" for="checkbox-switch-7">No </label>
                                <input id="checkbox-switch-7" v-model="yes_cliente" checked class="form-check-input"
                                    type="checkbox">
                                <label class="form-check-label ml-2" for="checkbox-switch-7">Si </label>
                            </div>

                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6 mt-2" v-if="yes_gasolina">
                        <div class="col-span-12 lg:col-span-12">
                            <label for="vertical-form-2" class="form-label">Analistas </label>
                            <analistas name="analista" @comunicarAnalista="escucharAnalista" :analistas="analistas"
                                v-model="analista_id"></analistas>
                        </div>
                    </div>


                    <h2 class="font-medium mt-2 text-base mr-auto text-primary">
                        <Icon icon='coins' class='mr-2' /> Pagos a este gasto
                    </h2>

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
                                                @comunicarCuenta="escucharCuenta" v-model="cuentas_id"></select-cuenta>
                                        </td>
                                        <td>
                                            <InputNumber class="form-control p-2 border border-success" type="number"
                                                v-model="pagos[index_pago].monto"
                                                @input="keyup_cuenta(index_pago, $event)" name="monto"
                                                placeholder="Monto del gasto" inputId="locale-us" locale="en-US"
                                                :minFractionDigits="2" />

                                        </td>
                                        <td class="m-auto">
                                            <buteton @click="deleted_pago(index_pago)" v-if="index_pago == 1"
                                                type="button" name="" id="" class="btn btn-danger m-auto btn-xs">
                                                <Icon icon='trash' class='' />
                                            </buteton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>



                    <div id="basic-button" class="p-1 mt-4">
                        <div class="preview">
                            <button v-if="is_created && !isLoading" type="submit" class="btn btn-primary mr-1 mb-2">
                                <Icon icon="plus" class="ml-2" /> Registrar gasto
                            </button>
                            <btn-loading v-if="isLoading"></btn-loading>
                        </div>
                    </div>

                </div>
            </div>
        </form>

    </div>

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
import btnLoading from '../auxiliares/btn-loading.vue';
import { map } from 'lodash';

export default {
    components: { btnLoading },
    mixins: [myMixin],
    data() {
        return {
            tipo_gasto_id: null,
            gastos_descripcion: "",
            monto: "",
            pagos: [],
            is_btn_pagos: false,
            is_created: false,
            yes_gasolina: false,
            analista: null,
            yes_cliente: false,
            isLoading: false
        } 
    },
    computed: {
        totalCuota_pagos() {
            return this.pagos.reduce((sum, pa) => sum + parseFloat(pa.monto), 0);
        },
    },
    watch: {
        totalCuota_pagos: function (newValue) {
            if (this.monto == newValue) {
                this.is_created = true;
            } else {
                this.is_created = false;
            }
        },
        monto: function (newValue) {
            if (newValue > 0) {
                this.pagos[0] = {
                    numero: 1,
                    cuentas_id: "MFNhZTlXUkhTSmpWNDQ5ZUc2YUhldz09",
                    monto: parseFloat(newValue)
                };
            }
        }
    },
    methods: {
        escucharAnalista(event) {
            this.analista = event;
        },
        change_tipo_gasto_id() {


            const data = {
                tipo_gasto_id: this.tipo_gasto_id
            };

            console.log(this.tipo_gasto_id);

            const headers = this.headers;

            this.loading_start();

            Axios
                .post("/get_tipo_gasto", data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {
                        if (response.data.data.yes_gasolina == "Y") {
                            this.yes_gasolina = true;
                        } else {
                            this.yes_gasolina = false;
                        }
                    } else {
                        this.alert_warning(response.data.message);
                    }
                    this.loading_end();
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor, el campo de estar seleccionado");
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
                    cuentas_id: "MFNhZTlXUkhTSmpWNDQ5ZUc2YUhldz09",
                    monto: this.monto - this.pagos[0].monto
                });
            }

        },
        keyup_cuenta(index, evento) {

            if (this.totalCuota_pagos != this.monto) {
                this.is_btn_pagos = false;
            } else {
                this.is_btn_pagos = true;
            }
        },
        change_cuenta(index, evento) {
            this.pagos[index].cuentas_id = evento.target.value;
            console.log(this.pagos[index]);

        },
        // Your methods here
        crear_gasto() {

            let cuentas_vacias = 0;
            this.pagos.map((item, index) => {
                if (item.cuentas_id === "") {
                    cuentas_vacias++;
                }
            });

            if (cuentas_vacias > 0) {
                this.alert_warning("La cuenta de pago no puede estar vacía");
                return;
            }

            if (this.monto != this.totalCuota_pagos) {
                this.alert_warning("los pagos deben coincidir con el monto del gasto")
            }

            const data = {
                pagos: this.pagos,
                gastos_descripcion: this.gastos_descripcion,
                monto: this.monto,
                tipo_gasto_id: this.tipo_gasto_id,
                analista: this.analista,
                yes_cliente: this.yes_cliente
            };

            const headers = this.headers;

            this.loading_start();

            this.isLoading = true;

            Axios
                .post("/gastos", data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {
                        window.location.assign(response.data.data);
                    } else {
                        this.isLoading = false;
                        this.alert_warning(response.data.message);
                    }
                    this.loading_end();
                })
                .catch((error) => {
                    this.isLoading = false;
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor");
                    console.error(error);
                });
        }
    },
    mounted() {

        var self = this;

        $("#form_crear_gasto").validate({
            rules: {
                gastos_descripcion: {
                    required: true,
                    noSpecial: true
                },
                monto: {
                    required: true
                },
                tipo_gasto_id: {
                    required: true
                },
                analista: {
                    required: true
                },
            },
            submitHandler: function (form) {
                try {

                    self.crear_gasto();

                } catch (error) {
                    console.log(error);
                }
                return false;
            }
        });

    }
}
</script>

<style></style>