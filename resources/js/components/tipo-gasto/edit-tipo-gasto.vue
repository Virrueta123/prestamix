<template>

    <div class="intro-y box mt-5">

        <form id="form_editar_tipo_gasto" method="POST" action="#">
            <div id="vertical-form" class="p-5">
                <div class="">

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-12">
                            <label for="vertical-form-2" class="form-label">Nombre </label>
                            <input name="nombre_edit" v-model="nombre_edit" type="text" class="form-control"
                                placeholder="Nombre">
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-12">
                            <label for="vertical-form-2" class="form-label">Descripcion </label>

                            <Textarea name="descripcion_edit" v-model="descripcion_edit" class="form-control"
                                placeholder="Descripcion" rows="5" cols="30" />
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-12">
                            <label for="vertical-form-2" class="form-label" id="switch1">Estado del tipo de egreso
                            </label>

                            <div class="form-check form-switch">
                                <label class="form-check-label mr-2" for="checkbox-switch-7">Desactivado</label>
                                <input id="checkbox-switch-7" v-model="status" class="form-check-input" type="checkbox">
                                <label class="form-check-label ml-2" for="checkbox-switch-7">Activado </label>
                            </div>

                        </div>
                    </div>


                    <div id="basic-button" class="p-1 mt-4">
                        <div class="preview">
                            <button type="submit" class="btn btn-primary mr-1 mb-2">
                                <Icon icon="edit" class="ml-2" /> Editar tipo de gasto
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
            get_tipo_gasto: this.$attrs.get_tipo_gasto,
            nombre_edit: "",
            descripcion_edit: "",
            status: false
        }
    },
    methods: {
        // Your methods here
        editar_tipo_gasto() {
            const data = {
                urlapi: this.get_tipo_gasto.urlapi,
                nombre_edit: this.nombre_edit,
                descripcion_edit: this.descripcion_edit,
                status :  this.status
            };

            const headers = this.headers;

            this.loading_start();

            Axios
                .put("/tipo_gasto_edit", data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {
                        this.alert_success(response.data.message);
                        this.$emit('llamarEditarTipoGasto', response.data.data);
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

        this.nombre_edit = this.get_tipo_gasto.nombre;
        this.descripcion_edit = this.get_tipo_gasto.descripcion;
        this.status = this.get_tipo_gasto.status == "A" ? true : false;

        var self = this;

        $("#form_editar_tipo_gasto").validate({
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
                    self.editar_tipo_gasto();
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