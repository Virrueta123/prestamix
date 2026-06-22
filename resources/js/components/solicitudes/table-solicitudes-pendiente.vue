<template>



    <div class="intro-y box mt-5">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
            <h2 class="font-medium text-base mr-auto">
                Tablas de solicitudes pendientes
            </h2>
        </div>
        <div class="p-5" id="small-table">
            <div class="preview" style="display: block;">
                <div class="overflow-x-auto">
                    <table class="table " id="table_solicitud_aprobada">
                        <thead>
                            <tr>
                                <th>Serie</th>
                                <th>Tipo solicitud</th>
                                <th>Nombre</th>
                                <th>Analista</th>
                                <th>Usuario</th>
                                <th>Documento</th>
                                <th>Tipo vivienda</th>
                                <th>Domicilio</th>
                                <th>D.negocio</th>
                                <th>Monto Credito</th>
                                <th>Frecuencia Pago</th>
                                <th>Interes</th>
                                <th>D/S/M/Q/A</th>
                                <th>Fecha S</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>

                    </table>
                </div>
            </div>

        </div>
    </div>

    <Sidebar v-model:visible="is_opciones_modal" header="Opciones para la tabla de solicitudes" position="bottom"
        style="height: auto">
        <div id="link-button" class="p-5">
            <a :href="'/solicitud/' + get_solicitud.urlapi + '/edit'" class="btn btn-primary  mr-1 mb-2">
                <Icon icon='edit' class='mr-2' /> Editar Solicitud
            </a>
            <a :href="'/solicitud/' + get_solicitud.urlapi" class="btn btn-eye  mr-1 mb-2">
                <Icon icon='eye' class='mr-2' /> Ver Solicitud
            </a>
            <button @click="eliminar_solicitud_tabla(get_solicitud, table_solicitud)" class="btn btn-danger mr-1 mb-2">
                <Icon icon='trash' class='mr-2' /> Eliminar solicitud
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
            tableData: this.$attrs.users,
            tabulator: null,
            data_solicitud: [],
            select_name: "",
            search_input: "",
            is_opciones_modal: false,
            get_solicitud: null,
            table_solicitud: null
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

            this.table_solicitud = $('#table_solicitud_aprobada').DataTable({
                language: this.spanish_datatable,
                ajax: {
                    url: '/tabla_solicitud_pendiente_data',
                    type: 'post',
                    async: true,
                },
                columns: [
                    { data: "code" },
                    { data: "tipo_solicitud" },
                    { data: "solicitud_nombre" },
                    { data: "analista.name" },
                    { data: "usuario.name" },
                    { data: "solicitud_documento" },
                    { data: "tipo_vivienda" },
                    { data: "solicitud_domicilio" },
                    { data: "solicitud_direccion_negocio" },
                    { data: "prestamo.moto_credito" },
                    { data: "prestamo.frecuencia_pagos" },
                    { data: "prestamo.interes" },
                    { data: "prestamo.intervalo" },
                    {
                        data: "created_at",
                        render: function (data, type, row) {

                            return moment(data).format("L LT a");
                            
                        }
                    },
                ],
                paging: true,
                processing: true,
                serverSide: false,
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
            $('#table_solicitud_aprobada tbody').on('dblclick', 'tr', function () {

                var data = self.table_solicitud.row(this).data();
                self.is_opciones_modal = true;
                self.get_solicitud = data
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