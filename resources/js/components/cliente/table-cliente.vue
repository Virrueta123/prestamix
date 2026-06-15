<template>



    <div class="intro-y box mt-5">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
            <h2 class="font-medium text-base mr-auto">
               Tabla de clientes
            </h2>
        </div>
        <div class="p-5" id="small-table">
            <div class="preview" style="display: block;">
                <div class="overflow-x-auto">
                    <table class="table " id="table_solicitud_trabajador">
                        <thead>
                            <tr>
                                <th>T.Cliente</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Dni</th>
                                <th>Direccion</th>
                                <th>Edad</th>
                                <th>Fecha nacimiento</th> 
                                <th>Departamento</th>
                                <th>Distrito</th>
                                <th>Provincia</th>
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
            get_cliente: null
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

            var table = $('#table_solicitud_trabajador').DataTable({
                language: this.spanish_datatable,
                ajax: {
                    url: '/tabla_cliente_data',
                    type: 'post',
                    async: true,
                },
                columns: [
                    { data: "tipo_cliente" },
                    { data: "cli_nombre" },
                    { data: "cli_apellido" },
                    { data: "cli_dni" },
                    { data: "cli_domicilio" },
                    {
                        data: "fecha_nacimiento",
                        render: function (data, type, row) {
                           return self.calcularEdad( data );
                        }

                    },
                    {
                        data: "fecha_nacimiento",
                        render: function (data, type, row) {
                           return self.formatear_fecha( data );
                        }

                    },
                    { data: "cli_departamento" },
                    { data: "cli_distrito" },
                    { data: "cli_provincia" }, 
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
            $('#table_solicitud_trabajador tbody').on('dblclick', 'tr', function () {

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

        });



    },
}
</script>

<style></style>