<template>

    <div class="intro-y box mt-5">

        <form id="form_editar_gasto" method="POST" action="#">
            <div id="vertical-form" class="p-5">
                <div class="">

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-12">
                            <label for="vertical-form-2" class="form-label">Nombre </label>
                            <input name="gastos_descripcion_edit" v-model="gastos_descripcion_edit" type="text"
                                class="form-control" placeholder="Descripcion">
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Monto del gasto </label>
                            <InputNumber class="form-control p-2 border border-success" v-model="monto_edit" name="monto_edit"
                                placeholder="Monto del gasto" inputId="locale-us" locale="en-US"
                                :minFractionDigits="2" />

                        </div>

                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label"> Tipo de gatos </label>
                            <select-tipo-gasto  :selected="get_gasto.tipo_gasto.urlapi" v-model="tipo_gasto_id"></select-tipo-gasto> 
                        </div>
                    </div>


                    <div id="basic-button" class="p-1 mt-4">
                        <div class="preview">
                            <button type="submit" class="btn btn-primary mr-1 mb-2">
                                <Icon icon="edit" class="ml-2" /> Editar gasto
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
            get_gasto: this.$attrs.get_gasto,
            gastos_descripcion_edit: "",
            monto_edit: "",
            tipo_gasto_id: ""
        }
    },
    methods: {
        // Your methods here
        editar_gasto() {
            const data = {
                urlapi: this.get_gasto.urlapi,
                gastos_descripcion_edit: this.gastos_descripcion_edit,
                monto_edit: this.monto_edit,
                tipo_gasto_id :  this.tipo_gasto_id
            };

            const headers = this.headers;

            this.loading_start();

            Axios
                .put("/gasto_edit", data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {
                        this.alert_success(response.data.message);
                        this.$emit('llamarEditarGasto', response.data.data);
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

        this.gastos_descripcion_edit = this.get_gasto.gastos_descripcion;
        this.monto_edit = this.get_gasto.monto;
 
        console.log(this.get_gasto);
        var self = this;

        $("#form_editar_gasto").validate({
            rules: {
                descripcion_edit: {
                    required: true,
                    noSpecial: true
                },
                nombre_edit: {
                    required: true,
                    noSpecial: true
                },
            },
            submitHandler: function (form) {
                try {
                    self.editar_gasto();
                } catch (error) {
                    this.alert_error_modal("Error en el servidor");
                }

                return false;
            }
        });

    }
}
</script>

<style></style>