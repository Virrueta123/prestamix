<template>



    <div class="intro-y box mt-5">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
            <h2 class="font-medium text-base mr-auto">
                Toda las tablas
            </h2>
            <div class="form-check form-switch w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0">
                <a :href="'/cuentas/create'" type="button" class="btn btn-primary btn-block">
                    <Icon icon='plus' class='mr-2' /> Crear una cuenta
                </a>
            </div>
        </div>



        <div class="p-5" id="small-table">
            <div class="preview" style="display: block;">
                <div class="overflow-x-auto">
                    <table class="table table-striped" id="table_cuentas">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Entidad</th>
                                <th>Numero de cuenta</th>
                                <th>Numero cci</th>
                                <th>Estado</th>
                                <th>Usuario</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </div>


    <Sidebar v-model:visible="is_edit_modal" header="Formulario para editar una cuenta" position="bottom"
        style="height: auto">

        <edit-cuentas @llamarEditarCuenta="escucharEditarCuenta" :get_cuentas="get_cuentas"></edit-cuentas>

    </Sidebar>

    <Sidebar v-model:visible="is_opciones_modal" header="Opciones para la tabla de cuentas" position="bottom"
        style="height: auto">

        <div id="link-button" class="p-5">
            <button @click="is_edit_modal = true;" class="btn btn-primary  mr-1 mb-2">
                <Icon icon='edit' class='mr-2' /> Editar Cuenta
            </button>
            <button class="btn btn-danger mr-1 mb-2">
                <Icon icon='trash' class='mr-2' /> Eliminar Cuenta
            </button>
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

import moment from 'moment';

export default {
    mixins: [myMixin],
    components: {

    },
    data() {
        return {
            table_cuentas: null,
            data_solicitud: [],
            select_name: "",
            search_input: "",
            is_opciones_modal: false,
            is_edit_modal: false,
            get_cuentas: null,
            indice_table: null,
        }
    },
    methods: {
        escucharEditarCuenta(evento) {
            console.log(evento);
            this.table_cuentas.row(this.indice_table).data(evento)
            this.is_opciones_modal = false;
            this.is_edit_modal = false;
        },
        async load_data_cuentas() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            var self = this;

            this.table_cuentas = $('#table_cuentas').DataTable({

                ajax: {
                    url: '/load_table_cuentas',
                    type: 'post',
                    async: true,
                    "data": function (d) {
                        // Aquí puedes definir los parámetros personalizados que deseas enviar al servidor
                        d.analistas_seleted = self.analistas_seleted;
                        d.between_date = self.between_date;
                        // Agrega más parámetros según sea necesario
                    },
                },
                columns: [
                    { "data": "cuentas_nombre" },
                    { "data": "cuentas_entidad" },
                    { "data": "cuentas_numero" },
                    { "data": "cuentas_cci" },
                    { "data": "status" },
                    { "data": "usuario.name" },
                ],
                paging: true,
                processing: true,
                serverSide: true,
                "infoFiltered": "",
                "processing": "Cargando.....",
                dom: 'Bfrtip',
                keys: true,
                lengthChange: false,
                responsive: true,
                "ordering": true,
                "language": this.spanish_datatable,
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

                    exportOptions: {
                        modifier: {
                            page: 'all'
                        },
                        format: {
                            body: function (data, row, column, node) {
                                // Strip $ from salary column to make it numeric
                                return column === 5 ? data.replace(/[$,]/g, '') : data;
                            }
                        }
                    }
                },
                ],

            })

          
            var self = this;
            $('#table_cuentas tbody').on('dblclick', 'tr', function () {

                var data = self.table_cuentas.row(this).data();

                console.log(data.DT_RowIndex);

                self.is_opciones_modal = true;
                self.get_cuentas = data;
                self.indice_table = this;

                //  data.cuentas_nombre = "123123123";
                console.log(data.cuentas_nombre);

            });
        },


    },
    created() {

    },
    mounted() {


        this.load_data_cuentas().then((result) => {
            $("#loading").fadeOut(200);
            $("#table").fadeIn(50);
        }).catch((err) => {

        });


        //maxItems: 3
    },
}
</script>

<style></style>