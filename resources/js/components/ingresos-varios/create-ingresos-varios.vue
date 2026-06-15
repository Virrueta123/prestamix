<template>

    <div class="intro-y box mt-5">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
            <h2 class="font-medium text-base mr-auto text-primary">
                <Icon icon='table-list' class='mr-2' /> Formulario para crear un ingresos ( varios )
            </h2>
        </div>

        <form id="form_crear_ingresos_varios" method="POST" action="#">
            <div id="vertical-form" class="p-5">
                <div class="">

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-12">
                            <label for="vertical-form-2" class="form-label">Nombre </label>
                            <input name="descripcion" v-model="descripcion" type="text" class="form-control"
                                placeholder="Descripcion">
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Monto del ingreso </label>
                            <InputNumber class="form-control p-2 border border-success" v-model="monto" name="monto"
                                placeholder="" inputId="locale-us" locale="en-US" :minFractionDigits="2" />

                        </div>

                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label"> Cuenta </label>
                            <select-cuenta  @comunicarCuenta="escucharCuenta" v-model="cuentas_id"></select-cuenta>
                        </div>
                    </div>


                    <div class="grid grid-cols-12 gap-6 mt-2">

                        <div class=" col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label" id="switch1">Deseas agregar un
                                <strong>cliente</strong> a este ingreso?
                            </label>

                            <div class="form-check form-switch">
                                <label class="form-check-label mr-2" for="checkbox-switch-7">No </label>
                                <input id="checkbox-switch-7" v-model="yes_cliente" checked class="form-check-input"
                                    type="checkbox">
                                <label class="form-check-label ml-2" for="checkbox-switch-7">Si </label>
                            </div>

                        </div>
                    </div>


                    <div v-if="yes_cliente" class="mt-4">
                        <label for="regular-form-1" class="form-label">Buscar Cliente</label>
                        <div class="input-group mt-2">

                            <select ref="select_cliente" v-model="cli_id" name="cli_id" class="form-control">
                            </select>
                        </div>
                    </div>


                    <div id="basic-button" class="p-1 mt-4">
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
            cuentas_id: null,
            descripcion: "",
            monto: "",
            yes_cliente: false,
            select_cliente: null,
            cli_id:null
        }
    },
    computed: {

    },
    watch: {
        yes_cliente: function (newVal) {

            if (newVal) {
                this.$nextTick(() => {
                    this.select_cliente = new TomSelect(this.$refs.select_cliente, {
                        valueField: 'urlapi',
                        labelField: 'name',
                        searchField: 'name',
                        options: [],
                        render: {
                            option: function (item, escape) {

                                return `<div class="intro-x">
                                            <div class="box px-4 py-4 mb-1 flex items-center ">
                                                <div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">
                                                    <img alt="Midone - HTML Admin Template" src="../../dist/images/avatar/${item.cli_sexo == "M" ? "masculino" : "femenino"}.png">
                                                </div>
                                                <div class="ml-4 mr-auto">
                                                    <div class="font-medium">${item.cli_nombre + "-" + item.cli_apellido}</div>
                                                    <div class="text-slate-500 text-xs mt-0.5">Dni ( ${item.cli_dni} ) </div>
                                                </div>
                                                
                                            </div>
                            </div>`;
                            },
                            item: function (item, escape) {
                                return `<div class="intro-x" style="width:100%;">
                                            <div class="box px-4 py-4 mb-1 flex items-center ">
                                                <div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">
                                                    <img alt="Midone - HTML Admin Template" src="../../dist/images/avatar/${item.cli_sexo == "M" ? "masculino" : "femenino"}.png">
                                                </div>
                                                <div class="ml-4 mr-auto">
                                                    <div class="font-medium">${item.cli_nombre + "-" + item.cli_apellido}</div>
                                                    <div class="text-slate-500 text-xs mt-0.5">Dni ( ${item.cli_dni} ) </div>
                                                </div>
                                                
                                            </div>
                            </div>`;
                            }
                        },
                        load: function (query, callback) {
                            const data = {
                                search: query,
                            };

                            const headers = this.headers;

                            Axios
                                .post("/search_cliente_select", data, {
                                    headers,
                                })
                                .then((response) => {

                                    if (response.data.success) {
                                        console.log(response.data);

                                        callback(response.data.data);
                                    }
                                })
                                .catch((error) => {
                                    callback();
                                    this.loading_end();
                                    this.alert_error_modal("Error en el servidor, recargue el navegador");
                                    console.error(error);
                                });

                        },
                    });

                  
                });
            }

        }
    },
    methods: {
        escucharCuenta( value ){

            this.cuentas_id = value;

        },
      
        crear_ingreso_varios() {

            const data = {
                descripcion: this.descripcion,
                monto: this.monto,
                cuentas_id: this.cuentas_id,
                cli_id : this.cli_id,
                yes_cliente:this.yes_cliente
            };

            const headers = this.headers;

            this.loading_start();

            Axios
                .post("/ingresos_varios", data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {
                        window.location.reload();
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

        $("#form_crear_ingresos_varios").validate({
            rules: {
                descripcion: {
                    required: true,
                    noSpecial: true
                },
                monto: {
                    required: true
                },
                cuentas_id: {
                    required: true
                },
                cli_id:{
                    required: true
                }
            },
            submitHandler: function (form) {
                try {
                    self.crear_ingreso_varios();
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