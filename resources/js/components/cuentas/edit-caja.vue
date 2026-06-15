<template>

    <div class="intro-y box mt-5">
        

        <form id="form_editar_cuenta" method="POST" action="#">
            <div id="vertical-form" class="p-5">
                <div class="">

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Nombre </label>
                            <input name="cuentas_nombre_edit" v-model="cuentas_nombre_edit" type="text" class="form-control"
                                placeholder="Nombre">
                        </div>

                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Entidad </label>
                            <input name="cuentas_entidad_edit" v-model="cuentas_entidad_edit" type="text" class="form-control"
                                placeholder="Nombre de la entidad">
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Numero de cuenta </label>
                            <input type="number" name="cuentas_numero_edit" v-model="cuentas_numero_edit" class="form-control"
                                placeholder="Numero de cuenta">

                        </div>

                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">CCI </label>
                            <input type="number" name="cuentas_cci_edit" v-model="cuentas_cci_edit" class="form-control"
                                placeholder="Numero de cci">
                        </div>
                    </div>

                    <div id="basic-button" class="p-1 mt-4">
                        <div class="preview">
                            <button type="submit" class="btn btn-primary mr-1 mb-2">
                                <Icon icon="plus" class="ml-2" /> Editar Cuenta
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
            get_cuentas : this.$attrs.get_cuentas,
            cuentas_nombre_edit: "",
            cuentas_entidad_edit: "",
            cuentas_numero_edit: "",
            cuentas_cci_edit: "",
        }
    },
    methods: {
        // Your methods here
        editar_cuenta() {
            const data = {
                urlapi:this.get_cuentas.urlapi,
                cuentas_nombre_edit: this.cuentas_nombre_edit,
                cuentas_entidad_edit: this.cuentas_entidad_edit,
                cuentas_numero_edit: this.cuentas_numero_edit,
                cuentas_cci_edit: this.cuentas_cci_edit,
            };

            const headers = this.headers;

            this.loading_start();

            Axios
                .put("/cuentas_edit", data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {
                        this.alert_success(response.data.message);
                        this.$emit('llamarEditarCuenta', response.data.data);
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

            this.cuentas_nombre_edit = this.get_cuentas.cuentas_nombre;
            this.cuentas_entidad_edit = this.get_cuentas.cuentas_entidad;
            this.cuentas_numero_edit = this.get_cuentas.cuentas_numero;
            this.cuentas_cci_edit = this.get_cuentas.cuentas_cci;

        var self = this;
 
        $("#form_editar_cuenta").validate({
            rules: {
                cuentas_nombre_edit: {
                    required: true
                },
                cuentas_entidad_edit: {
                    required: true
                },
                cuentas_numero_edit: {
                    required: true
                }
            },
            submitHandler: function (form) {
                self.editar_cuenta();
                return false;
            }
        });

    }
}
</script>

<style></style>