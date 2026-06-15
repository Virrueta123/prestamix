<template>
    <div class="card flex justify-content-center">
        <ProgressSpinner id="p_loading_r_a_c" style="width: 50px; height: 50px" strokeWidth="8"
            fill="var(--surface-ground)" animationDuration=".5s" aria-label="Custom ProgressSpinner" />
    </div>
    <div style="display: none;" id="e_loading_r_a_c" class="box px-5 py-3 ml-4 flex-1">
        <div class="flex items-center">
            <div class="font-medium">Mensaje de este prestamo</div>
            <div class="text-xs text-slate-500 ml-auto">Fecha: {{  get_fecha_programada }}</div>
        </div>
        <div class="text-slate-500 mt-1"> {{  get_msj_descripcion }} </div>
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
            prestamo_urlapi: this.$attrs.prestamo_urlapi,
            get_msj_prestamo: null,
            get_fecha_programada:"",
            get_msj_descripcion :""
        }
    },
    methods: {
        // Your methods here
        async load_mensaje_prestamo() {
            const data = {
                prestamo_urlapi: this.prestamo_urlapi,
            };

            const headers = this.headers;

            this.loading_start();

            return Axios
                .post("/load_mensaje_prestamo", data, {
                    headers,
                })
                .then((response) => {
                    if (response.data.success) {
                        this.get_msj_prestamo = response.data.data;
                        return response.data;
                    } else {
                        return response.data;
                    }
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor");
                    console.error(error);
                });
        }
    },
    mounted() {

        this.load_mensaje_prestamo().then((result) => {
            if (result.success) { 
                this.alert_success(result.message);
                this.get_fecha_programada = result.data.fecha_programada
                this.get_msj_descripcion = result.data.msj_descripcion 
            } else {
                this.alert_success(result.message);
            } 
            
            $("#e_loading_r_a_c").fadeIn()
            $("#p_loading_r_a_c").fadeOut()
            this.loading_end();
        }).catch((err) => {

        });

    }
}
</script>

<style></style>