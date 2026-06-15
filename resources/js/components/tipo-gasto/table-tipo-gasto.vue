<template>

    <div class="intro-y box mt-5">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
            <h2 class="font-medium text-base mr-auto">
                Toda las tablas
            </h2>

            <div class="form-check form-switch w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0">
                <a :href="'/tipo_gasto/create'" type="button" class="btn btn-primary btn-block">
                    <Icon icon='plus' class='mr-2' /> Crear un tipo de gasto
                </a>
            </div>
        </div>



        <div class="p-5" id="small-table">
            <div class="preview" style="display: block;">
                <div class="overflow-x-auto">
                    <table class="table table-striped" id="table_tipo_gasto">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripcion</th>
                                <th>Usuario</th>
                                <th>Fecha de Creacion</th>
                                <th>Activo</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </div>


    <Sidebar v-model:visible="is_edit_modal" header="Formulario para editar un tipo de gasto" position="bottom"
        style="height: auto">

        <edit-tipo-gasto @llamarEditarTipoGasto="escucharEditarTipoGasto"
            :get_tipo_gasto="get_tipo_gasto"></edit-tipo-gasto>

    </Sidebar>

    <Sidebar v-model:visible="is_opciones_modal" header="Opciones para la tabla de cuentas" position="bottom"
        style="height: auto">

        <div id="link-button" class="p-5">
            <button @click="is_edit_modal = true;" class="btn btn-primary  mr-1 mb-2">
                <Icon icon='edit' class='mr-2' /> Editar Cuenta
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
            table_tipo_gasto: null,
            is_opciones_modal: false,
            is_edit_modal: false,
            get_tipo_gasto: null,
            indice_table: null,
        }
    },
    methods: { 
        escucharEditarTipoGasto(evento) {
            console.log(evento);
            this.table_tipo_gasto.row(this.indice_table).data(evento)
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

            this.table_tipo_gasto = $('#table_tipo_gasto').DataTable({

                ajax: {
                    url: '/load_table_tipo_gasto',
                    type: 'post',
                    async: true,
                },
                columns: [
                    { "data": "nombre" },
                    { "data": "descripcion" },
                    { "data": "usuario.name" },
                    {
                        "data": "created_at",
                        render: function (data, type, row) {
                          
                            return moment(data).format("L LT a");
                        }
                    },
                    {
                        "data": "status",
                        render: function (data, type, row) {
                            if (row.status == "A") {
                                return `<div class="rounded-full py-1 px-2 text-xs bg-success text-white text-center cursor-pointer font-medium"> <i class="fa fa-check"></i> </div>`
                            }
                            return `<div class="rounded-full py-1 px-2 text-xs bg-danger text-white text-center cursor-pointer font-medium"> <i class="fa fa-x"></i> </div>`;
                        }
                    },
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
                },
                ],

            })


            var self = this;
            $('#table_tipo_gasto tbody').on('dblclick', 'tr', function () {

                var data = self.table_tipo_gasto.row(this).data();

                console.log(data.DT_RowIndex);

                self.is_opciones_modal = true;
                self.get_tipo_gasto = data;
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