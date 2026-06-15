<template>
    <div class="card box p-3 ">
        <div class="flex flex-col sm:flex-row sm:items-end xl:items-start">
            <div class="xl:flex sm:mr-auto mt-2 pt-0">
                <div class="font-bold text-base  font-xl"> Vista del salario

                </div>
            </div>

        </div>
    </div>

    <div class="intro-y box px-5 pt-5 mt-1">
        <div class="intro-y flex-1 box py-16 lg:ml-5 mb-5 lg:mb-0">



            <div class="text-base text-slate-500 mt-8 text-center">Salario</div>
            <div class="flex justify-center">
                <div class="relative text-5xl font-semibold   mx-auto">
                    {{ formatear_dinero_soles(salario.monto) }}
                </div>
            </div>

            <div class="text-base text-slate-500 mt-8 text-center">Salario adelantado</div>
            <div class="flex justify-center">
                <div class="relative text-5xl font-semibold   mx-auto">
                    {{ formatear_dinero_soles(salario.pagado) }}
                </div>
            </div>

            <div class="text-base text-slate-500 mt-8 text-center">Salario Restante</div>
            <div class="flex justify-center">
                <div v-if="salario.monto - salario.pagado < 0"
                    class="relative text-5xl font-semibold text-danger  mx-auto">
                    {{ formatear_dinero_soles(salario.monto - salario.pagado) }}
                </div>
                <div v-else class="relative text-5xl font-semibold   mx-auto">
                    {{ formatear_dinero_soles(salario.monto - salario.pagado) }}
                </div>

            </div>


        </div>
    </div>


    <div class="intro-y box px-5 pt-5 mt-1">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
            <h2 class="font-medium text-base mr-auto">
                Tabla de solicitudes de este salario ( procesados )
            </h2>
        </div>
        <div class="p-5" id="small-table">
            <div class="preview" style="display: block;">
                <div class="overflow-x-auto">
                    <table class="table " id="table_solicitud_trabajador_procesado">
                        <thead>
                            <tr>
                                <th>Descripcion</th>
                                <th>Tipo solicitud</th>
                                <th>Trabajador</th>
                                <th>Monto</th>
                                <th>Pagos</th>
                                <th>Fecha creacion</th>
                                <th>Fecha de respuesta</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>

                    </table>
                </div>
            </div>

        </div>
    </div>




</template>

<script>
import $ from 'jquery';
import { createApp, h } from 'vue';
import TomSelect from 'tom-select';
import Axios from 'axios';

import "jquery-validation";
import "jquery-validation/dist/localization/messages_es"

import moment from 'moment';
import 'moment/locale/es';

// plugins para la creacion de pdf de una manera elegante
import {
    Canvg
} from 'canvg';
import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';
import 'jspdf-autotable';
import * as svg2pdf from 'svg2pdf.js';


// mixin
import {
    myMixin
} from "../../mixin.js";


import { renderToString } from '@vue/server-renderer';

//svg   
import documentInf from '../solicitudes/svg/documento-inf.vue';


import { ref } from "vue";
import { get } from 'jquery';

export default {
    mixins: [myMixin],
    data() {
        return {
            salario: this.$attrs.salario,
            is_loading: false,
            suma_monto_adelanto: 0,
            data_table_by_sum: []
        }
    },
    computed: {

    },
    watch: {

        'data_table_by_sum': function (newValue) {

            if (this.is_loading) {



                const importe_monto_credito = this.data_table_by_sum.data.reduce((acumulador, res) => {
                    return acumulador + parseFloat(res.monto);
                }, 0);


                this.suma_monto_adelanto = importe_monto_credito;


            } else {

            }


        },
        'fecha_inicio': function (newValue) {
            this.fecha_final = newValue;
            this.by_date_table();
        },
        'fecha_final': function (newValue) {
            this.by_date_table();
        }
    },
    methods: {


        table_solicitud_trabajador() {
            var self = this;

            console.log(self.salario);

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            var table = $('#table_solicitud_trabajador_procesado').DataTable({
                language: this.spanish_datatable,
                ajax: {
                    url: '/tabla_solicitud_trabajadores_procesado_by_trabajador',
                    type: 'post',
                    "data": function (d) {
                        // Aquí puedes definir los parámetros personalizados que deseas enviar al servidor
                        d.salario_id = self.salario.urlapi;
                        d.is_created_solicitud = "Y"
                        // Agrega más parámetros según sea necesario
                    },
                },
                "order": [[5, "desc"]],
                columns: [
                    { data: "descripcion" },
                    {
                        data: "tipo",
                        render: function (data, type, row) {
                            switch (data) {
                                case "A":
                                    return "<div> Adelanto </div>";
                                    break;
                                case "D":
                                    return "<div> Descuento </div>";
                                    break;


                            }
                        }
                    },
                    { data: "trabajador" },
                    {
                        data: "monto",
                        render: function (data, type, row) {

                            return `<div> ${self.formatear_dinero_soles(data)} </div>`;

                        }
                    },
                    {
                        data: "monto",
                        render: function (data, type, row) {

                            var string = ``;


                            if (row.gasto === null) {
                                string += `<div> Sin pagos </div>`;
                            } else {
                                row.gasto.pago.map(element => {
                                    string += `<div class="flex items-center">
                                                    <div class="w-2 h-2 bg-primary rounded-full mr-3"></div>
                                                    <span class="truncate">${element.cuenta.cuentas_entidad} <i class="fa-solid fa-arrow-right"></i></span> 
                                                    <span class="font-medium xl:ml-auto">${element.monto}</span> 
                                           </div>`;
                                });
                            }
 
                            return string;

                        }
                    },
                    {
                        data: "created_at",
                        render: function (data, type, row) {

                            return `<div> ${self.formatear_fecha(data)} </div>`;

                        }
                    },
                    {
                        data: "updated_at",
                        render: function (data, type, row) {

                            return `<div> ${self.formatear_fecha(data)} </div>`;

                        }
                    },
                    {
                        data: "status",
                        render: function (data, type, row) {
                            switch (data) {
                                case "P":
                                    return "<div> Pendiente </div>";
                                    break;
                                case "R":
                                    return "<div> Rechazado </div>";
                                    break;
                                case "A":
                                    return "<div> Aprobado </div>";
                                    break;
                                case "G":
                                    return "<div> Procesado </div>";
                                    break;
                            }
                        }

                    },
                ],
                paging: true,
                processing: true,
                serverSide: true,
                "infoFiltered": "",
                "processing": "<img src='~/Content/images/loadingNew.gif' />",
                dom: 'Bfrtip',
                keys: true,
                responsive: true,
                "ordering": true,
                "buttons": [{
                    text: '<i class="fa fa-bars"></i> columnas visibles',
                    extend: 'colvis',
                },
                {
                    text: '<i class="fa fa-copy"></i> Copiar',
                    extend: 'copy',
                    title: 'tabla_cliente_fecha_'
                }, {
                    text: '<i class="fa fa-file-csv"></i> Csv',
                    extend: 'csvHtml5',
                    title: 'tabla_cliente_fecha_'
                }, {
                    text: '<i class="fa fa-file-excel"></i> Excel',
                    extend: 'excelHtml5',
                    title: 'tabla_cliente_fecha_'
                }, {
                    text: '<i class="fa fa-file-pdf"></i> Pdf',
                    extend: 'pdfHtml5',
                    title: 'tabla_cliente_fecha_',

                }, {
                    text: '<i class="fa fa-print"></i> Imprimir',
                    extend: "print",
                    title: 'tabla_cliente_fecha_',
                },
                ],
                "drawCallback": function (settings) {
                    // Almacenar los datos de la tabla en una variable del componente Vue 

                    self.data_table_by_sum = settings.json;
                }
            })

            table.on('init', function (e) {
                self.is_loading = true;
            });
        }
    },
    mounted() {
        console.log(this.solicitud_trabajador);

        this.table_solicitud_trabajador();
    }
}
</script>

<style></style>