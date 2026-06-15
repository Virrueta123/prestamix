<template>

    <div class="intro-y box mt-5">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
            <h2 class="font-medium text-base mr-auto text-primary">
                <Icon icon='table-list' class='mr-2' /> Formulario para crear una cuenta
            </h2>
        </div>

        <form id="form_crear_cuenta" method="POST" action="#">
            <div id="vertical-form" class="p-5">
                <div class="">

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Nombre </label>
                            <input name="cuentas_nombre" v-model="cuentas_nombre" type="text" class="form-control"
                                placeholder="Nombre">
                        </div>

                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Entidad </label>
                            <input name="cuentas_entidad" v-model="cuentas_entidad" type="text" class="form-control"
                                placeholder="Nombre de la entidad">
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Numero de cuenta </label>
                            <input type="number" name="cuentas_numero" v-model="cuentas_numero" class="form-control"
                                placeholder="Numero de cuenta">

                        </div>

                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">CCI </label>
                            <input type="number" name="cuentas_cci" v-model="cuentas_cci" class="form-control"
                                placeholder="Numero de cci">
                        </div>
                    </div>

                    <div id="basic-button" class="p-1 mt-4">
                        <div class="preview">
                            <button type="submit" class="btn btn-primary mr-1 mb-2">
                                <Icon icon="plus" class="ml-2" /> Registrar Cuenta
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
            cuentas_nombre: "",
            cuentas_entidad: "",
            cuentas_numero: "",
            cuentas_cci: "",
        }
    },
    methods: {
        // Your methods here
        crear_cuenta() {
            const data = {
                cuentas_nombre: this.cuentas_nombre,
                cuentas_entidad: this.cuentas_entidad,
                cuentas_numero: this.cuentas_numero,
                cuentas_cci: this.cuentas_cci,
            };

            const headers = this.headers;

            this.loading_start();

            Axios
                .post("/cuentas", data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) { 
                        window.location.assign( response.data.data );
                    } else {
                        this.alert_warning(response.data.message);
                    }
                    this.loading_end();
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor, ingrese los nombres y apellidos manualmente");
                    console.error(error);
                });
        }
    },
    mounted() {

        var self = this;

        $("#form_crear_cuenta").validate({
            rules: {
                cuentas_nombre: {
                    required: true
                },
                cuentas_entidad: {
                    required: true
                },
                cuentas_numero: {
                    required: true
                }, 
            },
            submitHandler: function (form) {
                self.crear_cuenta();
                return false;
            }
        });

    }
}
</script>

<style></style>