<template>
    <div class="intro-y box px-5 pt-5 mt-1">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
            <h2 class="font-medium text-base mr-auto">
                Tabla de salario
            </h2>
        </div>
        <div class="p-5" id="small-table">
            <div class="preview" style="display: block;">
                <div class="overflow-x-auto">
                    <table class="table " id="table-sueldo">
                        <thead>
                            <tr>
                                <th>Mes</th>
                                <th>Fecha de inicio</th>
                                <th>Fecha de vencimiento</th>
                                <th>Monto</th>
                                <th>Pagado</th>
                                <th>Restante</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>

                    </table>
                </div>
            </div>
        </div>


        <Sidebar v-model:visible="is_opciones_modal" header="Opciones para este registro" position="bottom"
            style="height: auto">

            <div id="link-button" class="p-5">
            
                <a :href="'/salario/'+selected.urlapi" class="btn btn-primary  mr-1 mb-2">
                    <Icon icon='eyes' class='mr-2' /> ver salario
                </a> 
                
            </div>

        </Sidebar>
    </div>

</template>

<script>
import $ from 'jquery';

import 'tom-select/dist/css/tom-select.css';
import TomSelect from 'tom-select';
import Axios from 'axios';

import 'jquery-validation';
import 'jquery-validation/dist/localization/messages_es';

import moment from 'moment';
import 'moment/locale/es';

// mixin
import {
    myMixin
} from "../../mixin.js";

export default {
    mixins: [myMixin],
    data() {
        return {
            trabajador: this.$attrs.trabajador,
            data_table_by_sum: [],
            is_opciones_modal:false,
            selected:null,
            table:null
        }
    },
    watch: {
        'data_table_by_sum': function (newValue) {
            if (this.is_loading) {
                const importe_monto_credito = this.data_table_by_sum.data.reduce((acumulador, res) => {
                    return acumulador + parseFloat(res.monto);
                }, 0);
                this.suma_monto_adelanto = importe_monto_credito;
            } else {

            }

        },
    },
    methods: {
        // evento para cambiar el tipo de solicitud
        change_tipo_solicitud(event) {
            this.tipo = event.target.value;

            if (this.tipo == "S") {
                if (this.salario_id != null) {
                    this.start = true;
                    this.buscar_salario();
                }

            }
        },
        buscar_salario() {

            if (this.start) {
                this.start = false;
            } else {
                $("#table-sueldo").DataTable().clear().draw();
            }

            this.$nextTick(() => {

            });

        },
        // evento para cambiar el salario del trabajador
        change_salario(event) {
            this.salario_id = event.target.value;

            if (this.tipo == "S") {
                this.buscar_salario();
            }
        },
        // Your methods here
        solicitud_trabajador() {
            const data = {
                salario_id: this.salario_id,
                monto: this.monto,
                tipo: this.tipo,
                descripcion: this.descripcion,
            };

            const headers = this.headers;

            this.loading_start();

            Axios
                .post("/solicitud_trabajador", data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {
                        window.location.assign(response.data.data);
                    } else {
                        this.alert_warning(response.data.message);
                    }
                    this.loading_end();
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor");
                    console.error(error);
                });
        }
    },
    mounted() {


        var self = this;

        console.log(self.trabajador_id);

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });

        this.table = $('#table-sueldo').DataTable({
            language: this.spanish_datatable,
            retrieve: true,
            paging: false,
            ajax: {
                url: '/table_sueldo',
                type: 'post',
                "data": function (d) {
                    // Aquí puedes definir los parámetros personalizados que deseas enviar al servidor
                    d.trabajador_id = self.trabajador;

                },
            },
            "order": [[1, "desc"]],
            columns: [
                { data: "mes" },
                {
                    data: "fecha_inicio",
                    render: function (data, type, row) {

                        return `<div> ${self.formatear_fecha(data)} </div>`;

                    }
                },
                {
                    data: "fecha_final",
                    render: function (data, type, row) {

                        return `<div> ${self.formatear_fecha(data)} </div>`;

                    }
                },
                {
                    data: "monto",
                    render: function (data, type, row) {


                        return self.formatear_dinero_soles(data);
                    }
                },
                {
                    data: "pagado",
                    render: function (data, type, row) {
                        return self.formatear_dinero_soles(data);
                    }
                },
                {
                    data: "restante",
                    render: function (data, type, row) {
                        return self.formatear_dinero_soles(data);
                    }
                },
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
            "drawCallback": function (settings) {
                // Almacenar los datos de la tabla en una variable del componente Vue  
                self.data_table_by_sum = settings.json;
            }
        })

        

        $('#table-sueldo tbody').on('dblclick', 'tr', function () {

            var data =  self.table.row(this).data();
           
            self.is_opciones_modal = true;
            self.selected = data;

        });

    }
}
</script>

<style></style>