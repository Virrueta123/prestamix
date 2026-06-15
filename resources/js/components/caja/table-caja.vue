<template>



    <div class="intro-y box mt-5">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
            <h2 class="font-medium text-base mr-auto">
                Toda las cajas
            </h2>
            <div v-if="user.rol == 'gerente'" class="form-check form-switch w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0">
                <a :href="'/caja/create'" type="button" class="btn btn-primary btn-block">
                    <Icon icon='plus' class='mr-2' /> Crear una caja nueva
                </a>
            </div>
        </div>

        <div class="alert alert-warning show flex items-center m-4" role="alert">
            <Icon icon='bell' class='mr-2' />
            Una vez que la caja tenga ingresos y
            egresos no se prodra eliminar ni editar
        </div>

        <div class="p-5" id="small-table">
            <div class="preview" style="display: block;">
                <div class="overflow-x-auto">
                    <table class="table table-striped" id="table_cuentas">
                        <thead>
                            <tr>
                                <th>Numero</th>
                                <th>Referencia</th>
                                <th>Fecha Apertura</th>
                                <th>Fecha Cierre</th>
                                <th>Monto apertura </th>
                                <th>Ingresos</th>
                                <th>Egresos</th>
                                <th>Monto cierre</th>
                                <th>Usuario</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </div>



    <div class="intro-y box mt-5">
        <reporte-general-balance></reporte-general-balance>
    </div>

    <Sidebar v-model:visible="is_edit_modal" header="Formulario para editar una cuenta" position="bottom"
        style="height: auto">

        <edit-cuentas @llamarEditarCuenta="escucharEditarCuenta" :get_caja="get_caja"></edit-cuentas>

    </Sidebar>



    <Sidebar v-model:visible="is_opciones_modal" header="Opciones para la tabla de cuentas" position="bottom"
        style="height: auto">

        <div id="link-button" class="p-5">

            <a :href="'/caja/' + get_caja.urlapi" class="btn btn-warning  mr-1 mb-2">
                <Icon icon='eye' class='mr-2' /> Ver Caja
            </a>
            <!-- <button v-if="get_caja.ingresos== 0 && get_caja.gastos == 0" class="btn btn-primary  mr-1 mb-2">
                <Icon icon='circle-xmark' class='mr-2' /> Editar Caja
            </button> -->
            <button v-if="get_caja.status == 'A' && user.rol == 'gerente'" @click="cerrar_caja()"
                class="btn btn-primary  mr-1 mb-2">
                <Icon icon='edit' class='mr-2' /> Cerrar caja
            </button>
            <!-- <button v-if="get_caja.status=='A'" @click="cerrar_caja()" class="btn btn-danger mr-1 mb-2">
                <Icon icon='trash' class='mr-2' /> Eliminar Caja
            </button> -->
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
            get_caja: null,
            indice_table: null,
        }
    },
    methods: {
        cerrar_caja() {
            this.$swal.fire({
                title: "Estas segur@ que deseas cerrar esta caja?",
                text: "Si cierras esta caja no podras agregar mas ingresos y gastos hasta crear una nueva caja",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, cerra esta caja"
            }).then((result) => {
                if (result.isConfirmed) {
                    const data = {
                        urlapi: this.get_caja.urlapi,
                    };

                    const headers = this.headers;

                    this.loading_start();

                    Axios
                        .put("/cerrar_caja", data, {
                            headers,
                        })
                        .then((response) => {
                            if (response.data.success) {
                                window.location.href = response.data.data;
                            } else {
                                this.alert_warning(response.data.message);
                            }
                            this.loading_end();
                        })
                        .catch((error) => {
                            this.loading_end();
                            this.alert_error_modal("Error en el servidor, recargue la pagina");
                            console.error(error);
                        });

                }
            });

        },
        escucharEditarCuenta(evento) { 
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
                    url: '/load_table_caja',
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
                    { "data": "code" },
                    { "data": "referencia" },
                    { "data": "created_at" },
                    { "data": "fecha_cierre" },
                    { "data": "saldo_inicial" },
                    { "data": "ingresosefectivo" },
                    { "data": "gastosefectivo" },
                    { "data": "saldo_final" },
                    { "data": "usuario" },
                ],
                paging: true,
                processing: true,
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
            $('#table_cuentas tbody').on('dblclick', 'tr', function () {

                var data = self.table_cuentas.row(this).data();

                self.is_opciones_modal = true;
                self.get_caja = data;
                self.indice_table = this;

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