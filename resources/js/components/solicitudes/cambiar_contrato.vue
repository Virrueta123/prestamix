<template>

    <div class="intro-y box mt-5">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
            <h2 class="font-medium text-base mr-auto text-primary">
                <Icon icon='table-list' class='mr-2' /> Formulario para crear un gasto
            </h2>
        </div>

        <form id="form_cambiar_contrato" method="POST" action="#">
            <div id="vertical-form" class="p-5">
                <div class="">


                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-4">
                            <label for="vertical-form-2" class="form-label">Nombre del cliente </label>
                            <input name="nombre" v-model="nombre" type="text" class="form-control"
                                placeholder="Nombre del cliente">
                        </div>

                        <div class="intro-y col-span-12 lg:col-span-4">
                            <label for="vertical-form-2" class="form-label"> Interes </label>
                            <input name="interes" v-model="interes" type="number" class="form-control"
                                placeholder="Interes del contrato">
                        </div>

                        <div class="intro-y col-span-12 lg:col-span-4">
                            <label for="vertical-form-2" class="form-label"> Documento </label>
                            <input name="documento" v-model="documento" type="number" class="form-control"
                                placeholder="Documento del cliente">
                        </div>
                    </div>




                    <div id="basic-button" class="p-1 mt-4">
                        <div class="preview">
                            <button type="submit" class="btn btn-primary mr-1 mb-2">
                                <Icon icon="print" class="ml-2" />Generar consrato
                            </button>
                        </div>
                    </div>

                    
                    <iframe :src="pdfUrl" style="width: 100%; height: 500px;"></iframe>

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
            pdfUrl: "",
            interes: 10,
            nombre: "JUAN ALEX VIRRUETA CORAL",
            documento: 76827876
        }
    },
    methods: {
        generar_parte_contrato() {

            const data = {
                interes: this.interes,
                nombre: this.nombre,
                documento: this.documento
            };

            const headers = this.headers;

            this.loading_start();

            Axios
                .post("/print_parte_contrato", data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {
                        let pdfData = response.data.data;
                        let pdfUrl = 'data:application/pdf;base64,' + pdfData;
                        // Puedes usar una biblioteca de visor de PDF como PDF.js o simplemente abrir el PDF en una nueva pestaña
                        // window.open(pdfUrl, '_blank');

                        this.pdfUrl = pdfUrl;
                        
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

        $("#form_cambiar_contrato").validate({
            rules: {
                nombre: {
                    required: true,
                    noSpecial: true
                },
                interes: {
                    required: true
                },
                documento: {
                    required: true
                }
            },
            submitHandler: function (form) {
                try {
                    self.generar_parte_contrato();
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