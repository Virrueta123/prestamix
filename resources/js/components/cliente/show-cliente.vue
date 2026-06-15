<template>

    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Vista del cliente
        </h2>
    </div>

    <!-- <div class="intro-y box px-5 pt-5 mt-5">
                    <div class="flex flex-col lg:flex-row border-b border-slate-200/60 dark:border-darkmode-400 pb-5 -mx-5">
                        <div class="flex flex-1 px-5 items-center justify-center lg:justify-start">
                            <div class="w-20 h-20 sm:w-24 sm:h-24 flex-none lg:w-32 lg:h-32 image-fit relative">
                                <img alt="Midone - HTML Admin Template" class="rounded-full" src="../../../../public/dist/images/avatar/masculino.png">
                               
                            </div>
                            <div class="ml-5">
                                <div class="w-24 sm:w-40 truncate sm:whitespace-normal font-medium text-lg">Denzel Washington</div>
                                <div class="text-slate-500">Frontend Engineer</div>
                            </div>
                        </div>
                        <div class="mt-6 lg:mt-0 flex-1 px-5 border-l border-r border-slate-200/60 dark:border-darkmode-400 border-t lg:border-t-0 pt-5 lg:pt-0">
                            <div class="font-medium text-center lg:text-left lg:mt-3">Contact Details</div>
                            <div class="flex flex-col justify-center items-center lg:items-start mt-4">
                                <div class="truncate sm:whitespace-normal flex items-center"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="mail" data-lucide="mail" class="lucide lucide-mail w-4 h-4 mr-2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg> denzelwashington@left4code.com </div>
                                <div class="truncate sm:whitespace-normal flex items-center mt-3"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="instagram" data-lucide="instagram" class="lucide lucide-instagram w-4 h-4 mr-2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg> Instagram Denzel Washington </div>
                                <div class="truncate sm:whitespace-normal flex items-center mt-3"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="twitter" data-lucide="twitter" class="lucide lucide-twitter w-4 h-4 mr-2"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5 0-.28-.03-.56-.08-.83A7.72 7.72 0 0023 3z"></path></svg> Twitter Denzel Washington </div>
                            </div>
                        </div>
                        <div class="mt-6 lg:mt-0 flex-1 px-5 border-t lg:border-0 border-slate-200/60 dark:border-darkmode-400 pt-5 lg:pt-0">
                            <div class="font-medium text-center lg:text-left lg:mt-5">Sales Growth</div>
                            <div class="flex items-center justify-center lg:justify-start mt-2">
                                <div class="mr-2 w-20 flex"> USP: <span class="ml-3 font-medium text-success">+23%</span> </div>
                                <div class="w-3/4">
                                    <div class="h-[55px]">
                                        <canvas class="simple-line-chart-1 -mr-5" width="284" height="55" style="display: block; box-sizing: border-box; height: 55px; width: 284px;"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-center lg:justify-start">
                                <div class="mr-2 w-20 flex"> STP: <span class="ml-3 font-medium text-danger">-2%</span> </div>
                                <div class="w-3/4">
                                    <div class="h-[55px]">
                                        <canvas class="simple-line-chart-2 -mr-5" width="284" height="55" style="display: block; box-sizing: border-box; height: 55px; width: 284px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <ul class="nav nav-link-tabs flex-col sm:flex-row justify-center lg:justify-start text-center" role="tablist">
                        <li id="dashboard-tab" class="nav-item" role="presentation"> <a href="javascript:;" class="nav-link py-4 active" data-tw-target="#dashboard" aria-controls="dashboard" aria-selected="true" role="tab"> Dashboard </a> </li>
                        <li id="account-and-profile-tab" class="nav-item" role="presentation"> <a href="javascript:;" class="nav-link py-4" data-tw-target="#account-and-profile" aria-selected="false" role="tab"> Account &amp; Profile </a> </li>
                        <li id="activities-tab" class="nav-item" role="presentation"> <a href="javascript:;" class="nav-link py-4" data-tw-target="#activities" aria-selected="false" role="tab"> Activities </a> </li>
                        <li id="tasks-tab" class="nav-item" role="presentation"> <a href="javascript:;" class="nav-link py-4" data-tw-target="#tasks" aria-selected="false" role="tab"> Tasks </a> </li>
                    </ul>
                </div> -->

    <div class="intro-y box mt-5">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
            <h2 class="font-medium text-base mr-auto">
                Table de ingresos de este cliente
            </h2>
        </div>
        <div class="p-5" id="small-table">
            <div class="preview" style="display: block;">
                <div class="overflow-x-auto">
                    <table class="table " id="tabla_ingresos_cliente">
                        <thead>
                            <tr>
                                <th>N° solicitud</th>
                                <th>T.solicitud</th>
                                <th>Monto de prestamo</th>
                                <th>DT</th>
                                <th>F.pago</th>
                                <th>Monto</th> 
                                <th>Fecha</th> 
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>

    <Sidebar v-model:visible="is_opciones_modal" header="Opciones para la tabla de clientes" position="bottom"
        style="height: auto">
        <div id="link-button" class="p-5">

            <a :href="'/cliente/' + get_cliente.urlapi" class="btn btn-eye  mr-1 mb-2">
                <Icon icon='eye' class='mr-2 text-info' /> Ver cliente
            </a>
        </div>


    </Sidebar>

</template>

<script>
import { TabulatorFull as Tabulator } from 'tabulator-tables';

import {
    Canvg
} from 'canvg';
import html2canvas from 'html2canvas';

import Axios from "axios";

import jszip from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-dt';
import 'datatables.net-autofill-dt';
import 'datatables.net-buttons-dt';
import 'datatables.net-buttons/js/buttons.colVis.mjs';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';
import 'datatables.net-colreorder-dt';
import DateTime from 'datatables.net-datetime';
import 'datatables.net-fixedcolumns-dt';
import 'datatables.net-fixedheader-dt';
import 'datatables.net-keytable-dt';
import 'datatables.net-responsive-dt';
import 'datatables.net-rowgroup-dt';
import 'datatables.net-rowreorder-dt';
import 'datatables.net-searchbuilder-dt';
import 'datatables.net-searchpanes-dt';
import 'datatables.net-select-dt';
import 'datatables.net-staterestore-dt';
// mixin
import {
    myMixin
} from "../../mixin.js";



export default {
    mixins: [myMixin],
    components: {

    },
    data() {
        return {
            tableData: this.$attrs.users,
            tabulator: null,
            data_solicitud: [],
            select_name: "",
            search_input: "",
            is_opciones_modal: false,
            get_cliente: this.$attrs.get_cliente, 
        }
    },
    methods: {
        async load_user_data() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            var self = this;

            var table = $('#tabla_ingresos_cliente').DataTable({
                language: this.spanish_datatable,
                ajax: {
                    url: '/tabla_ingresos_cliente_data',
                    type: 'post',
                    async: true,
                    "data": function (d) { 
                        d.cliente_id = self.get_cliente.urlapi;
                    },
                },
                columns: [
                    { data: "monto",
                        render: function (data, type, row) {
                            console.log(row);
                              
                            return row.prestamo.solicitud.code;
                        }

                    },
                    { data: "monto",
                        render: function (data, type, row) {
                            console.log(row);
                              
                            return row.prestamo.solicitud.tipo_solicitud;
                        }

                    },
                    { data: "monto",
                        render: function (data, type, row) {
                            console.log(row);
                              
                            return row.prestamo.moto_credito;
                        }

                    },
                    { data: "monto",
                        render: function (data, type, row) {
                            console.log(row);
                              
                            return row.prestamo.d_t;
                        }

                    },
                    { data: "monto",
                        render: function (data, type, row) {
                            console.log(row);
                              
                            return row.codigo;
                        }

                    }, 
                    { 
                        data: "monto" 
                    }, 
                    { data: "created_at" },
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

            })

            var self = this;
            $('#tabla_ingresos_cliente tbody').on('dblclick', 'tr', function () {
                console.log(data);
                var data = table.row(this).data();
                self.is_opciones_modal = true;
                self.get_cliente = data
            });
        },


    },
    created() {

    },
    mounted() {

      

         this.load_user_data().then((result) => {
             $("#loading").fadeOut(200);
             $("#table").fadeIn(50);
         }).catch((err) => {
            console.log(err);
            
         });



    },
}
</script>

<style></style>