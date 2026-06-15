<template>

    <form id="form_crear_mensaje_prestamo" method="POST" action="#">
        <div id="vertical-form" class="p-5">
            <div class="">

                <div class="grid grid-cols-12 gap-6 mt-2">
                    <div class="intro-y col-span-12 lg:col-span-12">

                        <label for="regular-form-3" class="form-label">Descripcion del mensaje</label>
                        <textarea class="form-control" v-model="msj_descripcion" name="msj_descripcion" maxlength="250" id="" cols="2"
                            rows="3" />
                        <div class="form-help text-right text-primary font-bold  "> {{ msj_descripcion.length }}/250
                        </div>
                    </div>
                </div>

 
                <div class="grid grid-cols-12 gap-6 mt-2"> 
                    <div class="col-span-12 lg:col-span-12">
                        <label for="vertical-form-2" class="form-label"> Fecha de nacimiento </label>
                        <datepicker class="form-control col-span-12" v-model="fecha_programada" name="fecha_programada"
                            placeholder="hacer click para seleccionar" :styles="{ border: '2px solid #00ff00' }"
                            language="es"></datepicker>
                    </div>
                </div>

                <div id="basic-button" class="p-1 mt-4">
                    <div class="preview">
                        <button type="submit" class="btn btn-primary mr-1 mb-2">
                            <Icon icon="plus" class="ml-2" /> Registrar mensaje
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </form>

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
            msj_descripcion: "",
            fecha_programada: null,  
            prestamo_urlapi: this.$attrs.prestamo_urlapi
        }
    },
    methods: {
        // Your methods here
        crear_mensaje_prestamo() {
            const data = {
                prestamo_urlapi:this.prestamo_urlapi,
                msj_descripcion: this.msj_descripcion,
                fecha_programada: this.fecha_programada, 
            };

            const headers = this.headers;

            this.loading_start();
 
            Axios
                .post("/mensaje_prestamo", data, {
                    headers,
                })
                .then((response) => { 
                    if (response.data.success) { 
                        this.$emit('llamarCrearMsj', response.data.data);
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

        $("#form_crear_mensaje_prestamo").validate({
            rules: {
                msj_descripcion: {
                    required: true,
                    noSpecial: true
                },
                fecha_programada: {
                    required: true
                }  
            },
            submitHandler: function (form) {
                self.crear_mensaje_prestamo();
                return false;
            }
        });

    }
}
</script>

<style></style>