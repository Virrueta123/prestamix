<template>

    <div class="intro-y box mt-5">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
            <h2 class="font-medium text-base mr-auto text-primary">
                <Icon icon='table-list' class='mr-2' /> Formulario para crear un salario
            </h2>
            <h3 class="font-medium text-base mr-auto text-primary">
                Salario del usuario {{ trabajador.name }} {{ trabajador.lastname }}
            </h3>
        </div>

        <form id="form_crear_salario" method="POST" action="#">
            <div id="vertical-form" class="p-5">
                <div class="">

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-12">
                            <label for="vertical-form-2" class="form-label">Monto del salario </label>
                            <InputNumber class="form-control p-2 border border-success" v-model="monto" name="monto"
                                placeholder="Monto del gasto" inputId="locale-us" locale="en-US"
                                :minFractionDigits="2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-12">
                            <label for="vertical-form-2" class="form-label">fecha del salario</label>
                            <datepicker class="form-control col-span-12" v-model="fecha_inicio" name="fecha_inicio"
                                @input="change_fecha_inicio()" placeholder="hacer click para seleccionar"
                                :styles="{ border: '2px solid #00ff00' }" language="es"></datepicker>
                        </div>
                    </div>


                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label"> Proxima fecha del sueldo : {{
                                formatear_fecha_select(fecha_final) }} </label>
                        </div>
                    </div>


                    <div id="basic-button" class="p-1 mt-4">
                        <div class="preview">
                            <button type="submit" class="btn btn-primary mr-1 mb-2">
                                <Icon icon="plus" class="mr-2" /> Registrar salario
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </form>
 
        <table-sueldo :trabajador="trabajador.urlapi"></table-sueldo>

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
            trabajador_id: this.$attrs.trabajador_id,
            fecha_inicio: null,
            monto: null,
            fecha_final: null,
            trabajador: this.$attrs.user
        }
    },
    methods: {
        change_fecha_inicio() {
            console.log(this.fecha_inicio);
            this.fecha_final = moment(this.fecha_inicio).add(1, 'months');
            const fechaVencimiento = new Date(moment(this.fecha_final).year(), moment(this.fecha_final).month(), moment(this.fecha_final).date());
       
            if (fechaVencimiento.getDay() === 0) {
                fechaVencimiento.setDate(fechaVencimiento.getDate() - 1); 
            }
            this.fecha_final = fechaVencimiento;
        },
        // Your methods here
        crear_salario() {
            const data = {
                urlapi: this.trabajador.urlapi,
                monto: this.monto,
                fecha_inicio: this.fecha_inicio,
                fecha_final: this.fecha_final
            };

            const headers = this.headers;

            this.loading_start();

            Axios
                .post("/salario", data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {
                        window.location.assign("/usuario");
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
          
        $("#form_crear_salario").validate({
            rules: {
                fecha_inicio: {
                    required: true
                },
                monto: {
                    required: true
                }
            },
            submitHandler: function (form) {
                if (self.monto === null) {
                    self.alert_error_modal("Necesitas agregar un monto a este salario")
                    return false;
                }

                if (self.fecha_inicio === null) {
                    self.alert_error_modal("Necesitas agregar una fecha a este salario")
                    return false;
                }
                self.crear_salario(); 
                return false;
            }
        });

    }
}
</script>

<style></style>