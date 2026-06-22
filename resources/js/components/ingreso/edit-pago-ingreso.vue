<template>

    <div class="intro-y box mt-5">

        <h2 class="font-medium mt-2 text-base mr-auto text-primary">
            <Icon icon='coins' class='mr-2' /> Pagos a este Ingreso
        </h2>

        <div class="preview" v-if="pagos.length">
            <button @click="agregar_cuenta()" v-if="is_btn_pagos" type="button" class="btn btn-primary mr-1 mt-2 mb-2">
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
                            <td> <select-cuenta
                                    @comunicarCuenta="(value) => escucharCuenta(index_pago, value)"
                                    :default="pago.cuentas_id"></select-cuenta>
                            </td>
                            <td>
                                <InputNumber class="form-control p-2 border border-success" type="number"
                                    v-model="pagos[index_pago].monto" @input="keyup_cuenta(index_pago, $event)"
                                    name="monto" placeholder="Monto del gasto" inputId="locale-us" locale="en-US"
                                    :minFractionDigits="2" />

                            </td>
                            <td class="m-auto">
                                <buteton @click="deleted_pago(index_pago)" v-if="index_pago == 1" type="button" name=""
                                    id="" class="btn btn-danger m-auto btn-xs">
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
                <button v-on:click="actualizar_pago()" v-if="is_created && !isLoading" type="submit"
                    class="btn btn-primary mr-1 mb-2">
                    <Icon icon="plus" class="ml-2" /> Editar Pago
                </button>
                <btn-loading v-if="isLoading"></btn-loading>
            </div>
        </div>

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

export default {
    mixins: [myMixin],
    data() {
        return {
            get_ingreso: this.$attrs.get_ingreso,
            monto: "",
            pagos: [],
            delete_array: [],
            is_btn_pagos: false,
            is_created: false,
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
    },
    methods: {
        // esta funcion sirve para actualizar un pago
        actualizar_pago() {

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
                monto: this.monto,
                get_ingreso: this.get_ingreso,
                delete_array: this.delete_array
            };

            const headers = this.headers;

            this.loading_start();

            this.isLoading = true;

            Axios
                .post("/editar_pago_ingreso", data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {
                        window.location.reload();
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

        },
        // Your methods here
         
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

            if (this.pagos[index].urlapi) {
                this.delete_array.push(this.pagos[index].urlapi);
            }
            this.pagos.splice(index, 1);
        },
        escucharCuenta(index, value) {
            this.pagos[index].cuentas_id = value;
        },
        agregar_cuenta() {
            if (this.pagos.length < 2) {
                this.is_btn_pagos = false;
                this.pagos.push({
                    numero: 2,
                    cuentas_id: "",
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

    },
    mounted() { 
        this.monto = this.get_ingreso.monto;
 
        let numero = 0;

        this.get_ingreso.pago.forEach(element => {
            numero++;
            console.log(element);
            this.pagos.push({
                urlapi: element.urlapi,
                numero: numero,
                cuentas_id: element.cuenta.urlapi,
                monto: element.monto
            });
            console.log(this.pagos);

        });

    }
}
</script>

<style></style>