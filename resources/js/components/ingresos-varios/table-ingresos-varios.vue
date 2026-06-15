<template>

    <div class="intro-y box mt-5">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
            <h2 v-if="get_caja_id == ''" class="font-medium text-base mr-auto">
                Tabla de registro de los ingresos
            </h2>
            <h2 v-else class="font-medium text-base mr-auto">
                Tabla de registro de los ingresos de esta caja
            </h2>
        </div>



        <div class="p-5" id="small-table">
            <div class="preview" style="display: block;">
                <div class="overflow-x-auto">
                    <table class="table table-striped" id="table_ingresos">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Descripcion</th>
                                <th>Monto</th>
                                <th>
                                    <Icon icon='user' class='mr-2' />Analista
                                </th> 
                                <th>Usuario</th>
                                <th>Fecha de Creacion</th> 
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </div>




</template>

<script>


// mixin
import {
    myMixin
} from "../../mixin.js";

import moment from 'moment';

export default {
    mixins: [myMixin],
    data() {
        return {
            table_ingresos: null,
            is_opciones_modal: false,
            is_edit_modal: false,
            get_gasto: null,
            indice_table: null,
            get_caja_id: this.$attrs.get_id || ''
        }
    },
    methods: {
        escucharEditarGasto(evento) {
            console.log(evento);
            this.table_ingresos.row(this.indice_table).data(evento)
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

            this.table_ingresos = $('#table_ingresos').DataTable({

                ajax: {
                    url: '/load_table_ingresos',
                    type: 'post',
                    async: true,
                    "data": function (d) {
                        // Aquí puedes definir los parámetros personalizados que deseas enviar al servidor
                        d.get_caja_id = self.get_caja_id;
                    },
                },
                columns: [
                    { "data": "cliente" },
                    { "data": "descripcion" },

                    {
                        "data": "monto",
                        render: function (data, type, row) {

                            return self.formatear_dinero_soles(data);
                        }
                    },
                    { "data": "prestamo.analista.name" }, 
                    { "data": "usuario.name" },
                    {
                        "data": "created_at",
                        render: function (data, type, row) {

                            return moment(data).format("L LT a");
                        }
                    } 
                ],
                paging: true,
                processing: true,
                serverSide: false,
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