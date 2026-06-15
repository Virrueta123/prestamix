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
                                <th>pagos</th>
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

    <Sidebar v-model:visible="is_edit_pago_ingreso" header="Formulario para editar el pago de un ingreso" position="top"
        style="height: auto">

        <edit-pago-ingreso @llamarEditarPagoGasto="escucharEditarPagoGasto" :get_ingreso="get_ingreso"></edit-pago-ingreso>

    </Sidebar>

    <Sidebar v-model:visible="is_opciones_modal" header="Opciones para este registro" position="bottom"
        style="height: auto">

        <div id="link-button" class="p-5">
            <button @click="modal_edit_pago_ingreso()" class="btn btn-primary  mr-1 mb-2">
                <Icon icon='edit' class='mr-2' /> Editar Pago
            </button> 

            <button @click="eliminar_ingreso()" class="btn btn-primary  mr-1 mb-2">
                <Icon icon='edit' class='mr-2' /> Eliminar Ingreso
            </button> 
        
            <button v-if="user.rol=='gerente'" @click="print_voucher_ingreso()" class="btn btn-pending  mr-1 mb-2">
                <Icon icon='print' class='mr-2' /> Imprimir Voucher
            </button> 
        </div>

    </Sidebar> 
 

</template>

<script>

import Axios from "axios";
 
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
            get_ingreso: null,
            indice_table: null,
            get_caja_id: this.$attrs.get_id || '',
            is_edit_pago_ingreso:false
        }
    },
    methods: {
        modal_edit_pago_ingreso(){

            this.is_edit_pago_ingreso= true;
            this.is_opciones_modal = false;
            this.is_edit_modal = false;
  
            console.log("get_ingreso",this.get_ingreso);
            

            if( !this.is_now( this.get_ingreso.created_at ) ){
                this.alert_warning("No puede editar este pago, por estar fuera de fecha");
                this.is_edit_pago_ingreso = false;
                return;
            }

        },
        eliminar_ingreso(){
            const data = {
                urlapi: this.get_ingreso.urlapi
            };
            
            const headers = this.headers;
            
            this.loading_start();
            
            Axios.post("/delete_ingreso", data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {
                        this.alert_success(response.data.message);
                        this.table_ingresos.ajax.reload(null, false);
                    } else {
                        this.alert_warning(response.data.message);
                    }
                    this.loading_end();
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor, ingrese los nombres y apellidos manualmente");
                    console.error(error);
                });
        },
        print_voucher_ingreso(){
            const data = {
                urlapi: this.get_ingreso.urlapi
            };
            
            const headers = this.headers;
            
            this.loading_start();
            
            Axios
                .post(
                    "/print_voucher_ingreso", 
                    data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {
                        this.alert_success(response.data.message);
                    } else {
                        this.alert_warning(response.data.message);
                    }
                    this.loading_end();
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor, ingrese los nombres y apellidos manualmente");
                    console.error(error);
                });
        },
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
                        "data": "pago",
                        render: function (data, type, row) {

                            console.log(data);

                            var string = ``;

                            data.map(element => {
                                string += `<div class="flex items-center">
                                                    <div class="w-2 h-2 bg-primary rounded-full mr-3"></div>
                                                    <span class="truncate">${element.cuenta.cuentas_entidad} <i class="fa-solid fa-arrow-right"></i></span> 
                                                    <span class="font-medium xl:ml-auto">${element.monto}</span> 
                                           </div>`;
                            });

                            return string;
                        }
                    },
                    {
                        "data": "monto",
                        render: function (data, type, row) {

                            return self.formatear_dinero_soles(data);
                        }
                    },
                    { "data": "analista" },
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

            var self = this;
            $('#table_ingresos tbody').on('dblclick', 'tr', function () { 
                var data = self.table_ingresos.row(this).data();

                self.is_opciones_modal = true;
                self.get_ingreso = data;
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