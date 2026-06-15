<template>

    <div class="intro-y box mt-5">

        <form id="form_crear_ingreso_cuota" method="POST" action="#">
            <div id="vertical-form" class="p-5">
                <div class="">

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Descripcion </label>
                            <input name="descripcion" v-model="descripcion" type="text" class="form-control"
                                placeholder="Nombre">
                        </div>

                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Monto ingreso </label>
                            <InputNumber class="form-control p-2 border border-success" name="monto" v-model="monto"
                                placeholder="Monto" inputId="locale-us" locale="en-US" :minFractionDigits="2" disabled />
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label"> Por que metodo cancelo esta cuota </label>
                            <select-cuenta @comunicarCuenta="escucharCuenta" v-model="cuentas_id"></select-cuenta> 
                        </div>
                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label" id="switch1">Este pago es en <strong>oficina</strong>?
                            </label>

                            <div class="form-check form-switch">
                                <label class="form-check-label mr-2" for="checkbox-switch-7">No </label>
                                <input id="checkbox-switch-7" v-model="yes_office" checked class="form-check-input" type="checkbox">
                                <label class="form-check-label ml-2" for="checkbox-switch-7">Si </label>
                            </div>

                        </div> 
                    </div>
 

                    <div id="basic-button" class="p-1 mt-4">
                        <div class="preview">
                            <button type="submit" class="btn btn-primary mr-1 mb-2">
                                <Icon icon="plus" class="ml-2" /> Registrar cuota
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
            get_cuota : this.$attrs.get_cuota,
            select_cronograma: this.$attrs.select_cronograma,
            descripcion: "",
            monto: "", 
            cuentas_id:"",
            yes_office:true
        }
    },
    methods: {
        escucharCuenta(event){
            this.cuentas_id = event; 
        },
        // Your methods here
        crear_ingreso_cuenta() {
            const data = {
                urlapi:this.select_cronograma.urlapi,
                descripcion:this.descripcion,
                monto:this.monto,
                cuentas_id:this.cuentas_id,
                yes_office:this.yes_office
            };

            const headers = this.headers;

            this.loading_start();

            Axios
                .post("/ingreso_cuota", data, {
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
 
        this.monto =  this.select_cronograma.cuota;
        this.descripcion = `Pago cuota ${this.select_cronograma.periodo} de la solicitud n° ${this.get_cuota.solicitud.code}` ;

        $("#form_crear_ingreso_cuota").validate({
            rules: {
                cuentas_id: {
                    required: true
                }, 
                descripcion: {
                    required: true
                },
                monto: {
                    required: true
                }
            },
            submitHandler: function (form) {
                self.crear_ingreso_cuenta();
                return false;
            }
        });

    }
}
</script>

<style></style>