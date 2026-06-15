<template>

    <div class="intro-y box mt-5">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
            <h2 v-if="get_id == ''" class="font-medium text-base mr-auto">
                Tabla de registro de los gastos
            </h2>
            <h2 v-else class="font-medium text-base mr-auto">
                Tabla de registro de los gastos de esta caja
            </h2>

            <div class="form-check form-switch w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0">
                <a :href="'/gastos/create'" type="button" class="btn btn-primary btn-block">
                    <Icon icon='plus' class='mr-2' /> Crear un gasto
                </a>
            </div>
        </div>



        <div class="p-5" id="small-table">
            <div class="preview" style="display: block;">
                <div class="overflow-x-auto">
                    <table class="table table-striped" id="table_gasto">
                        <thead>
                            <tr>
                                <th>Descripcion</th>
                                <th>Tipo gasto</th>
                                <th>Pagos</th>
                                <th>Monto</th>
                                <th>Usuario</th>
                                <th>Surcursal</th>
                                <th>Fecha de Creacion</th>
                                <th>Fecha de Edicion</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </div>


    <Sidebar v-model:visible="is_edit_modal" header="Formulario para editar un gasto" position="bottom"
        style="height: auto">

        <edit-gasto @llamarEditarGasto="escucharEditarGasto" :get_gasto="get_gasto"></edit-gasto>

    </Sidebar>

    <Sidebar v-model:visible="is_edit_pago_gasto" header="Formulario para editar un gasto" position="top"
        style="height: auto">

        <edit-pago-gasto @llamarEditarPagoGasto="escucharEditarPagoGasto" :get_gasto="get_gasto"></edit-pago-gasto>

    </Sidebar>

    <Sidebar v-model:visible="is_opciones_modal" header="Opciones para este registro" position="bottom"
        style="height: auto">

        <div id="link-button" class="p-5">
            <!-- <button @click="is_edit_modal = true;" class="btn btn-primary  mr-1 mb-2">
                <Icon icon='edit' class='mr-2' /> Editar Gasto
            </button>  -->
            <button @click="eliminar_gasto()" class="btn btn-primary  mr-1 mb-2">
                <Icon icon='edit' class='mr-2' /> Eliminar Gasto
            </button> 

            <button  @click="modal_edit_pago_gasto()" class="btn btn-pending  mr-1 mb-2">
                <Icon icon='edit' class='mr-2' /> Editar pago
            </button> 

            <button v-if="user.rol=='gerente'" @click="print_voucher_gasto()" class="btn btn-pending  mr-1 mb-2">
                <Icon icon='print' class='mr-2' /> Imprimir Voucher
            </button> 
        </div>

    </Sidebar> 

</template>

<script>


// mixin
import Axios from "axios";
import {
    myMixin
} from "../../mixin.js";

import moment from 'moment';



export default {
    mixins: [myMixin],
    data() {
        return {
            table_gasto: null,
            is_opciones_modal: false,
            is_edit_modal: false,
            get_gasto: null,
            indice_table: null,
            get_id: this.$attrs.get_id || '',
            is_edit_pago_gasto:false, 
        }
    },
    methods: {
        modal_edit_pago_gasto() {  
            
            this.is_edit_pago_gasto = true;
            this.is_opciones_modal = false;
            this.is_edit_modal = false;
  
            if( !this.is_now( this.get_gasto.created_at ) ){
                this.alert_warning("No puede editar este pago, por estar fuera de fecha");
                this.is_edit_pago_gasto = false;
                return;
            }
            
        },
        print_voucher_gasto(){
            const data = {
                urlapi: this.get_gasto.urlapi
            };
            
            const headers = this.headers;
            
            this.loading_start();
            
            Axios
                .post(
                    "/print_voucher_gasto", 
                    data, {
                    headers,
                })
                .then((response) => {
                   
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
        eliminar_gasto(){
            const data = {
                urlapi: this.get_gasto.urlapi
            };
            
            const headers = this.headers;
            
            this.loading_start();
            
            Axios.post("/delete_gasto", data, {
                    headers,
                })
                .then((response) => {
             
                    if (response.data.success) {
                        this.alert_success(response.data.message);
                        this.table_gasto.ajax.reload(null, false);
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
            
            this.table_gasto.row(this.indice_table).data(evento)
            this.is_opciones_modal = false;
            this.is_edit_modal = false;
        },
        escucharEditarPagoGasto(evento){
            console.log(evento); 
        },  
        async load_data_cuentas() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            var self = this;

            this.table_gasto = $('#table_gasto').DataTable({

                ajax: {
                    url: '/load_table_gasto',
                    type: 'post',
                    async: true,
                    "data": function (d) {
                        // Aquí puedes definir los parámetros personalizados que deseas enviar al servidor
                        d.get_id = self.get_id;  
                    },
                },
                columns: [
                    { "data": "gastos_descripcion" },
                    { "data": "tipo_gasto.nombre" },
                    {
                        "data": "pago",
                        render: function (data, type, row) { 
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
                    { "data": "usuario.name" },
                    { "data": "sucursal.sucursal_nombre" },
                    {
                        "data": "created_at",
                        render: function (data, type, row) { 
                            return moment(data).format("L LT a");
                        }
                    },
                    {
                        "data": "updated_at",
                        render: function (data, type, row) {

                            return moment(data).format("L LT a");
                        }
                    }
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
            $('#table_gasto tbody').on('dblclick', 'tr', function () { 
                var data = self.table_gasto.row(this).data();

                self.is_opciones_modal = true;
                self.get_gasto = data;
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