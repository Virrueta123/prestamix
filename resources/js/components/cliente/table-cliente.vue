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
            <button @click="abrirEditar()" class="btn btn-warning mr-1 mb-2">
                <Icon icon='pen' class='mr-2' /> Editar cliente
            </button>
            <button @click="eliminarCliente()" class="btn btn-danger mr-1 mb-2">
                <Icon icon='trash' class='mr-2' /> Eliminar cliente
            </button>
        </div>
    </Sidebar>

    <Dialog v-model:visible="is_edit_modal" header="Editar cliente" :style="{ width: '90vw' }" modal>
        <edit-cliente v-if="cliente_edit" :get_cliente="cliente_edit"
            @comunicarEditarCliente="escucharEditarCliente"></edit-cliente>
    </Dialog>

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
            is_edit_modal: false,
            get_cliente: null,
            cliente_edit: null,
            table_cliente: null,
            indice_table: null
        }
    },
    methods: {
        abrirEditar() {
            const data = { urlapi: this.get_cliente.urlapi };
            const headers = this.headers;
            this.loading_start();
            Axios.post("/get_ciente", data, { headers })
                .then((response) => {
                    if (response.data.success) {
                        this.cliente_edit = response.data.data;
                        this.is_opciones_modal = false;
                        this.is_edit_modal = true;
                    } else {
                        this.alert_warning(response.data.message);
                    }
                    this.loading_end();
                })
                .catch(() => {
                    this.loading_end();
                    this.alert_error_modal("Error al cargar el cliente");
                });
        },
        escucharEditarCliente(cliente) {
            if (this.table_cliente && this.indice_table !== null) {
                this.table_cliente.row(this.indice_table).data(cliente);
            }
            this.is_edit_modal = false;
        },
        eliminarCliente() {
            const self = this;
            this.$swal({
                title: '¿Eliminar cliente?',
                text: `${this.get_cliente.cli_nombre} ${this.get_cliente.cli_apellido}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    const data = { urlapi: self.get_cliente.urlapi };
                    const headers = self.headers;
                    self.loading_start();
                    Axios.post("/eliminar_cliente", data, { headers })
                        .then((response) => {
                            if (response.data.success) {
                                self.alert_success(response.data.message);
                                self.is_opciones_modal = false;
                                self.table_cliente.ajax.reload(null, false);
                            } else {
                                self.alert_warning(response.data.message);
                            }
                            self.loading_end();
                        })
                        .catch(() => {
                            self.loading_end();
                            self.alert_error_modal("Error en el servidor");
                        });
                }
            });
        },
        async load_user_data() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            var self = this;

            this.table_cliente = $('#table_solicitud_trabajador').DataTable({
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
                var data = self.table_cliente.row(this).data();
                self.indice_table = self.table_cliente.row(this).index();
                self.is_opciones_modal = true;
                self.get_cliente = data;
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