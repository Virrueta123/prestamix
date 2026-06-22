<template>


    <div class="intro-y box mt-5">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
            <h2 class="font-medium text-base mr-auto">
                Tabla (Planilla)
            </h2>
        </div>

        <div id="basic-accordion" class="p-5">
            <div class="preview">
                <div id="faq-accordion-1" class="accordion">
                    <div class="accordion-item">
                        <div id="faq-accordion-content-1" class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-tw-toggle="collapse"
                                data-tw-target="#faq-accordion-collapse-1" aria-expanded="false"
                                aria-controls="faq-accordion-collapse-1"> Mas opciones de busqueda </button>
                        </div>
                        <div id="faq-accordion-collapse-1" class="accordion-collapse collapse"
                            aria-labelledby="faq-accordion-content-1" data-tw-parent="#faq-accordion-1"
                            style="display: none;">

                            <div class="grid grid-cols-12 gap-6 mt-2">
                                <div class="intro-y col-span-12 lg:col-span-6">
                                    <!-- BEGIN: Form Layout -->
                                    <div class="intro-y box p-5">
                                        <div class="mt-3">
                                            <label class="form-label">Analista</label>

                                            <div class="sm:grid grid-cols-1 gap-1 ">
                                                <div class="input-group">
                                                    <div id="input-group-5" class="input-group-text">
                                                        <Icon icon='users' class='text-primary' />
                                                    </div>
                                                    <analista-multiple @comunicarAnalista="eschuchar_analista"
                                                        :analistas="analistas"></analista-multiple>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END: Form Layout -->
                                </div>

                                <div class="intro-y col-span-12 lg:col-span-6">
                                    <!-- BEGIN: Form Layout -->
                                    <div class="intro-y box p-5">
                                        <div class="mt-3">
                                            <label class="form-label">Frecuencia de pago</label>

                                            <div class="sm:grid grid-cols-1 gap-2">
                                                <div class="input-group">
                                                    <div id="input-group-5" class="input-group-text">
                                                        <Icon icon='calendar-week' class='text-primary' />
                                                    </div>
                                                    <filtrar-by-frecuencia-pago
                                                        @comunicarFrecuenciaPago="eschucharFrecuenciaPago"></filtrar-by-frecuencia-pago>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END: Form Layout -->
                                </div>
                            </div>

                            <div class="grid grid-cols-12 gap-12 mt-2">
                                <div class="col-span-12 lg:col-span-6">
                                    <!-- BEGIN: Form Layout -->
                                    <div class="box p-5">
                                        <div class="mt-3">
                                            <label class="form-label">Tipo cliente</label>

                                            <div class="sm:grid grid-cols-1 gap-2">
                                                <div class="input-group">
                                                    <div id="input-group-5" class="input-group-text">
                                                        <Icon icon='users' class='text-primary' />
                                                    </div>
                                                    <tipo-cliente-multiple
                                                        @comunicarTipoCliente="escucharTipoCliente"></tipo-cliente-multiple>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END: Form Layout -->
                                </div>

                                <div class="col-span-12 lg:col-span-6">
                                    <!-- BEGIN: Form Layout -->
                                    <div class="box p-5">
                                        <div class="mt-3">
                                            <label class="form-label">Vencido/Hoy/Pendiente</label>

                                            <div class="sm:grid grid-cols-1 gap-2">
                                                <div class="input-group">
                                                    <div id="input-group-5" class="input-group-text">
                                                        <Icon icon='bars' class='text-primary' />
                                                    </div>
                                                    <select-estado
                                                        @comunicarSelectEstado="escucharSelectEstado"></select-estado>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END: Form Layout -->
                                </div>
                            </div>

                            <!-- <div class="intro-y col-span-12 lg:col-span-6">
                                <div class="intro-y box p-5">
                                    <div class="form-check mt-5">
                                        <input id="vertical-form-3" @click="by_date_change()" :checked="is_by_date"
                                            class="form-check-input" type="checkbox" value="">
                                        <label class="form-check-label" for="vertical-form-3">Buscar por fecha</label>
                                    </div>
                                </div>
                            </div>
                            <div class="intro-y col-span-12 lg:col-span-6" v-if="is_by_date">
                                <div class="intro-y box p-5">
                                    <div class="grid grid-cols-12 gap-6 mt-2">

                                        <div class=" col-span-6 lg:col-span-12 mt-3 ">
                                            <label for="vertical-form-2" class="form-label"> Fecha Inicio
                                            </label>
                                            <datepicker class="form-control col-span-12" v-model="fecha_inicio"
                                                placeholder="hacer click para seleccionar"
                                                :styles="{ border: '2px solid #00ff00' }" language="es"></datepicker>
                                        </div>

                                        <div class=" col-span-6 lg:col-span-12 mt-3 ">
                                            <label for="vertical-form-2" class="form-label"> Fecha Final
                                            </label>
                                            <datepicker class="form-control col-span-12" v-model="fecha_final"
                                                placeholder="hacer click para seleccionar"
                                                :styles="{ border: '2px solid #00ff00' }" language="es"></datepicker>
                                        </div>
                                    </div>
                                </div>
                            </div>   -->
                        </div>
                    </div>

                </div>
            </div>

        </div>


        <div class="box p-5 mt-4">
            <div class="flex text-primary">
                <Icon icon='list' class='mr-2 mt-2' />
                <div class="font-medium text-base mx-auto">Leyenda</div>

            </div>
            <div class="border-t border-slate-200/60 dark:border-darkmode-400 pt-5 mt-5">
                <div class="flex items-center">
                    <div class="w-5 h-4 bg-pending fila-roja  rounded mr-3"></div>
                    <span class="truncate">Cliente morosos</span>
                    <div class="h-px flex-1 border border-r border-dashed border-slate-200 mx-3 xl:hidden"></div>
                    <span class="font-medium xl:ml-auto">{{ cliente_moroso }}</span>
                </div>
                <div class="flex items-center mt-4">
                    <div class="w-5 h-4 bg-primary fila-amarilla rounded mr-3"></div>
                    <span class="truncate">Cuotas Pendiente</span>
                    <div class="h-px flex-1 border border-r border-dashed border-slate-200 mx-3 xl:hidden"></div>
                    <span class="font-medium xl:ml-auto">{{ conteo_pendientes }}</span>
                </div>
                <div class="flex items-center mt-4">
                    <div class="w-5 h-4 bg-primary fila-pendiente rounded  mr-3"></div>
                    <span class="truncate">Cuotas de hoy</span>
                    <div class="h-px flex-1 border border-r border-dashed border-slate-200 mx-3 xl:hidden"></div>
                    <span class="font-medium xl:ml-auto">{{ conteo_hoy }}</span>
                </div>
            </div>
        </div>

        <div class="box p-5 mt-4">
            <div class="form-check form-switch">
                <label class="form-check-label" for="vertical-form-3">
                    <span class="text-primary">Permitir seleccionar lo prestamos para imprimir de manera
                        personalizada</span>
                </label>
                <br>
                <input class="form-check-input chk-pago" type="checkbox" v-model="is_select_print"
                    v-on:change="change_select_print()">
            </div>
        </div>

        <div class="card box p-3 mt-3">
            <div class="preview" style="display: block;">
                <div class="overflow-x-auto">
                    <table class="table table-striped  table-bordered table-planilla" id="table_solicitud_aprobada">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Cliente</th>
                                <th>Domicilio</th>
                                <th>Telefono</th>
                                <th>Analista</th>
                                <th>T.Cliente</th>
                                <th>F.P</th>
                                <th>Interes</th>
                                <th>F.C</th>
                                <th>M.Crédito</th>
                                <th>Amortizacion</th>
                                <th>M.interes</th>
                                <th>Cuota</th>
                                <th>Mora</th>
                                <th>D.T</th>
                                <th>P.A</th>
                                <th>Saldo</th>
                                <th>Periodo</th>
                                <th>Fecha V</th>
                                <th>Estado</th>
                                <th><i class="fa-regular fa-message"></i></th>
                                <th>{{ fecha_actual }}</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th> </th>
                                <th> </th>
                                <th> </th>
                                <th> </th>
                                <th> </th>
                                <th> </th>
                                <th> </th>
                                <th>{{ suma_interes }} </th>
                                <th></th>
                                <th>{{ formatear_dinero_soles(suma_monto_credito) }}</th>
                                <th> </th>
                                <th> </th>
                                <th> {{ formatear_dinero_soles(suma_cuotas) }} </th>
                                <th> </th>
                                <th> </th>
                                <th> </th>
                                <th> </th>
                                <th> </th>
                                <th> </th>
                                <th> </th>
                                <th> </th>
                                <th> </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
 

    </div>

    <Sidebar v-model:visible="is_opciones_modal" header="Opciones para la tabla de planilla" position="bottom"
        style="height: auto">

        <opciones-cronograma v-if="get_cuota.frecuencia_pagos == 'Mensual'"
            :get_cuota="get_cuota"></opciones-cronograma>
        <opciones-cronograma-por-parte v-else :get_cuota="get_cuota"></opciones-cronograma-por-parte>

    </Sidebar>

    <Sidebar v-model:visible="is_modal_msj_create" header="Formulario para crear mensaje" position="bottom"
        style="height: auto">

        <create-msj-prestamo @llamarCrearMsj="escucharCrearMsj" :prestamo_urlapi="prestamo_urlapi">
        </create-msj-prestamo>

    </Sidebar>

    <Sidebar v-model:visible="is_modal_msj_show" position="bottom" style="height: auto">

        <show-msj-prestamo :prestamo_urlapi="prestamo_urlapi">
        </show-msj-prestamo>

    </Sidebar>

</template>

<script>
import { TabulatorFull as Tabulator } from 'tabulator-tables';

import {
    Canvg
} from 'canvg';
import html2canvas from 'html2canvas';

import Axios from "axios";


// mixin
import {
    myMixin
} from "../../mixin.js";

import moment from 'moment';
import OpcionesCronogramaPorParte from './opciones-cronograma-por-parte.vue';

export default {
    mixins: [myMixin],
    components: {
        OpcionesCronogramaPorParte

    },
    data() {
        return {
            tableData: this.$attrs.users,
            nombre: this.$attrs.nombre,
            urlapi: this.$attrs.urlapi,
            tabulator: null,
            table: null,
            data_solicitud: [],
            select_name: "",
            search_input: "",
            is_opciones_modal: false,
            get_cuota: null,
            analistas_seleted: [],
            frecuencia_seleted: [],
            data_table_by_count: [],
            // variable fechas 
            is_by_date: false,
            fecha_inicio: null,
            fecha_final: null,
            between_date: [],
            cliente_moroso: 0,
            conteo_hoy: 0,
            conteo_pendientes: 0,
            is_loading: false,
            suma_monto_credito: 0,
            suma_interes: 0,
            suma_cuotas: 0,
            is_modal_msj_create: false,
            prestamo_urlapi: null,
            index_planilla: null,
            get_prestamo: null,
            is_modal_msj_show: false,
            fecha_actual: null,
            montos_reporte: [],
            is_loading_montos: false,
            is_select_print: false
        }
    },
    computed: {
    },
    watch: {

        'data_table_by_count': function (newValue) {

            if (this.is_loading) {
                const fechaActual = moment();
                let conteo_morosos = 0;
                let conteo_hoy = 0;
                let conteo_pendientes = 0;

                this.montos_reporte.sum_moroso = 0;
                this.montos_reporte.sum_hoy = 0;
                this.montos_reporte.sum_pendientes = 0;

                // Iterar sobre los datos almacenados en tableData
                this.data_table_by_count.data.forEach(data => {
                    const fechaDada = moment(data.cuota_actual.fechaVencimiento);

                    if (fechaDada.isBefore(fechaActual, 'day')) {
                        this.montos_reporte.sum_moroso += parseFloat(parseFloat(data.d_t) - parseFloat(data.pa));
                        conteo_morosos++;
                    } else if (fechaDada.isSame(fechaActual, 'day')) {
                        this.montos_reporte.sum_hoy += parseFloat(parseFloat(data.d_t) - parseFloat(data.pa));
                        conteo_hoy++;
                    } else {
                        this.montos_reporte.sum_pendientes += parseFloat(parseFloat(data.d_t) - parseFloat(data.pa));
                        conteo_pendientes++;
                    }
                });

                this.cliente_moroso = conteo_morosos;
                this.conteo_hoy = conteo_hoy;
                this.conteo_pendientes = conteo_pendientes;

                const importe_monto_credito = this.data_table_by_count.data.reduce((acumulador, res) => {
                    return acumulador + parseFloat(res.moto_credito);
                }, 0);

                const importe_interes = this.data_table_by_count.data.reduce((acumulador, res) => {
                    return acumulador + parseFloat(res.interes);
                }, 0);

                const importe_cuotas = this.data_table_by_count.data.reduce((acumulador, res) => {
                    if (res.cuota_actual.yes_interes == "Y") {
                        return acumulador + parseFloat(res.cuota_actual.cuota);
                    } else {
                        return acumulador + parseFloat(res.cuota_actual.amortizacion);
                    }

                }, 0);

                this.suma_monto_credito = importe_monto_credito;
                this.suma_interes = importe_interes;
                this.suma_cuotas = importe_cuotas;
                this.is_loading_montos = true;

            } else {

            }
        },
        'fecha_inicio': function (newValue) {
            this.fecha_final = newValue;
            this.by_date_table();
        },
        'fecha_final': function (newValue) {
            this.by_date_table();
        }
    },
    methods: {
        change_select_print() {
            if (this.is_select_print) {
                this.table.select.style('multi');  // Selección múltiple
            } else {
                // Deshabilitar selección de filas
                this.table.select.style('api');  // Cambia a modo "API", es decir, sin selección visible
                this.table.rows().deselect();  // Des-selecciona cualquier fila previamente seleccionada
            }
        },
        escucharSelectEstado(event) {
            var estados = event;
            var dataFiltrada = this.data_table_by_count.data.filter(item => {


                const fechaDada = moment(item.cuota_actual.fechaVencimiento);
                const fechaActual = moment();
                // Comparar la fecha actual con la fecha dada 

                if (estados == "Vencido") {

                    if (fechaDada.isBefore(fechaActual, 'day')) {
                        return item;
                    }
                }

                if (estados == "Hoy") {
                    if (fechaDada.isSame(fechaActual, 'day')) {
                        return item;
                    }
                }

                if (estados == "Pendiente") {

                    if (fechaDada.isBefore(fechaActual, 'day')) {


                    } else if (fechaDada.isSame(fechaActual, 'day')) {

                    } else {
                        return item;
                    }


                }
            });

            // Eliminar las filas existentes
            this.table.rows().remove();

            // Agregar las nuevas filas
            this.table.rows.add(dataFiltrada);

            // Volver a dibujar la tabla
            this.table.draw();

        },
        escucharCrearMsj(event) {
            var rowData = this.table.row(this.index_planilla).data(); // Datos de la fila seleccionada
            rowData.is_mensaje = event;

            this.table.row(this.index_planilla).data(rowData);
            this.is_modal_msj_create = false;

        },
        eschucharFrecuenciaPago(event) {
            this.frecuencia_seleted = event;
            this.table.ajax.reload(null, false);
        },
        escucharTipoCliente(event) {
            this.tipo_cliente_seleted = event;
            this.table.ajax.reload(null, false);
        },
        by_date_table() {
            this.between_date = [
                moment(this.fecha_inicio, "ddd MMM DD YYYY HH:mm:ss ZZ").format("Y-MM-DD"),
                moment(this.fecha_final, "ddd MMM DD YYYY HH:mm:ss ZZ").format("Y-MM-DD")
            ]
            this.table.ajax.reload(null, false);
        },
        by_date_change() {
            this.is_by_date = !this.is_by_date;
        },
        eschuchar_analista(event) {
            this.analistas_seleted = event;
            this.table.ajax.reload(null, false);
        },
        async load_user_data() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            var self = this;

            this.table = $('#table_solicitud_aprobada').DataTable({

                ajax: {
                    url: '/prestamo_por_cliente',
                    type: 'post',
                    async: true,
                    "data": function (d) {
                        d.analistas_seleted = self.analistas_seleted;
                        d.between_date = self.between_date;
                        d.frecuencia_seleted = self.frecuencia_seleted;
                        d.tipo_cliente_seleted = self.tipo_cliente_seleted;
                        // Aquí puedes definir los parámetros personalizados que deseas enviar al servidor
                        d.urlapi = self.urlapi;
                        // Agrega más parámetros según sea necesario
                    },
                },
                columns: [
                    { "data": "solicitud.code" },
                    { "data": "cliente" },
                    { "data": "domicilio" },
                    { "data": "telefono" },
                    {
                        "data": "analista", render: function (data, type, row) {
                            return `<div class="text-center">${data}</div>`;
                        }
                    },
                    {
                        "data": "solicitud.cliente.tipo_cliente",
                        render: function (data, type, row) {
                            return `<div class="text-center"> ${data}</div>`;
                        }
                    },
                    {
                        "data": "frecuencia_pagos",
                        render: function (data, type, row) {
                            switch (data) {
                                case "Semanal":
                                    return `<div class="text-center"> S</div>`;
                                    break;

                                case "Diario":
                                    return `<div class="text-center"> D</div>`;
                                    break;

                                case "Mensual":
                                    return `<div class="text-center"> M</div>`;
                                    break;

                                case "Quincenal":
                                    return `<div class="text-center"> Q</div>`;
                                    break;

                            }

                            return `<div class="text-center"> ${data} %</div>`;
                        }
                    },
                    {
                        "data": "interes", render: function (data, type, row) {

                            return `<div class="text-center"> ${data} %</div>`;
                        }
                    },
                    {
                        "data": "fechainicio", render: function (data, type, row) {
                            ;
                            return `<div class="text-center"> ${self.formatear_fecha(data.date)}</div>`;
                        }
                    },
                    {
                        "data": "moto_credito", render: function (data, type, row) {

                            return `${data} `;
                        }
                    },

                    {
                        "data": "cuota_actual.amortizacion", render: function (data, type, row) {

                            return self.formatear_dinero_soles(data);

                        }
                    },
                    {
                        "data": "cuota_actual", render: function (data, type, row) {
                    
                            if (row.cuota_actual.yes_interes == "Y") {
                                return self.formatear_dinero_soles(data);
                            } else {
                                return `0.00`;
                            }
                        }
                    },
                    {
                        "data": "cuota_actual.cuota", render: function (data, type, row) {
                            if (row.cuota_actual.yes_interes == "Y") {
                                return self.formatear_dinero_soles(data);
                            } else {
                                return self.formatear_dinero_soles(row.amortizacion);
                            }
                        }
                    },
                    {
                        "data": "cuota_actual.interes", render: function (data, type, row) {
                            switch (row.cuota_actual.yes_mora) {
                                case "N":
                                    const fechaDada = moment(row.cuota_actual.fechaVencimiento);
                                    const fechaActual = moment();
                                    // Comparar la fecha actual con la fecha dada 
                                    if (fechaDada.isBefore(fechaActual, 'day')) {
                                        return self.formatear_dinero_soles(row.cuota_actual.interes);
                                    } else if (fechaDada.isSame(fechaActual, 'day')) {
                                        return `0.00`;
                                    } else {
                                        return `0.00`;
                                    }

                                    break;

                                case "Y":
                                    return self.formatear_dinero_soles(row.cuota_actual.monto_mora);
                                    break;

                                case "S":
                                    return `0.00`;
                                    break;
                            }
                        }
                    },
                    {
                        "data": "d_t", render: function (data, type, row) {
                            return `<div > ${self.formatear_dinero_soles(data)} </div>`;
                        }
                    },
                    {
                        "data": "pa", render: function (data, type, row) {
                            return `<div > ${self.formatear_dinero_soles(self.redondear(data))} </div>`;
                        }
                    },
                    {
                        "data": "pa", render: function (data, type, row) {
                            return `<div > ${self.formatear_dinero_soles(self.redondear(row.d_t - data))} </div>`;
                        }
                    },
                    {
                        "data": "cuota_actual.periodo", render: function (data, type, row) {
                            const fechaDada = moment(row.cuota_actual.fechaVencimiento);
                            const fechaActual = moment();
                            // Comparar la fecha actual con la fecha dada 
                            if (fechaDada.isBefore(fechaActual, 'day')) {

                                var css = `fila-roja`;

                            } else if (fechaDada.isSame(fechaActual, 'day')) {
                                var css = `fila-amarilla`;
                            } else {
                                var css = `fila-pendiente`;
                            }

                            return `<div class="alert ${css} p-0 text-center text-white show "> ${data}/${row.ncuotas} </div>`;
                        }
                    },
                    {
                        "data": "cuota_actual.fechaVencimiento", render: function (data, type, row) {

                            const fechaDada = moment(row.cuota_actual.fechaVencimiento);
                            const fechaActual = moment();
                            // Comparar la fecha actual con la fecha dada 
                            if (fechaDada.isBefore(fechaActual, 'day')) {

                                var css = `fila-roja`;

                            } else if (fechaDada.isSame(fechaActual, 'day')) {
                                var css = `fila-amarilla`;
                            } else {
                                var css = `fila-pendiente`;
                            }
                            return `<div class="${css} p-0 text-center text-white show"> ${data} </div>`;
                        }
                    },
                    {
                        "data": "status", render: function (data, type, row) {

                            const fechaDada = moment(row.cuota_actual.fechaVencimiento);
                            const fechaActual = moment();
                            // Comparar la fecha actual con la fecha dada 
                            if (fechaDada.isBefore(fechaActual, 'day')) {

                                return `<div> Vencido </div>`;

                            } else if (fechaDada.isSame(fechaActual, 'day')) {
                                return `<div> Pendiente / hoy </div>`;
                            } else {
                                return `<div> Pendiente </div>`;
                            }

                        }
                    },
                    {
                        "data": "status", render: function (data, type, row, meta) {

                            if (row.is_mensaje === "N") {
                                return `<div class="text-center"><button class="btn btn-primary click-msj" index="${meta.row}"><i class="fa-regular fa-message"></i></button></div>`;

                            } else {
                                return `<div class="text-center"><button class="btn btn-success click-msj" index="${meta.row}"><i class="fa fa-message text-white"></i></button></div>`;
                            }

                        }
                    },
                    {
                        "data": "status", render: function (data, type, row, meta) {

                            return "<div> </div>";

                        }
                    }
                ],
                searching: true,
                paging: true,
                processing: true,
                serverSide: false,
                autoWidth: true,
                "infoFiltered": "",
                "processing": "<img src='~/Content/images/loadingNew.gif' />",
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
                    orientation: 'landscape',
                }, {
                    text: '<i class="fa fa-print"></i> Imprimir',
                    extend: "print",
                    title: 'tabla planilla',
                    pageSize: 'LEGAL',
                    exportOptions: {
                        orientation: 'landscape',
                        columns: [1, 2, 3, 4, 5, 6, 7, 8, 14, 15, 16, 18, 21],
                        modifier: {
                            page: 'all',
                        },
                        format: {
                            body: function (data, row, column, node) {
                                // Strip $ from salary column to make it numeric
                                return column === 5 ? data.replace(/[$,]/g, '') : data;
                            }
                        },

                    },
                    customize: function (win) {
                        $(win.document.body)
                            .css('font-size', '10pt')
                            .prepend(
                                '<img src="../../../../dist/images/logo/logo.png" style="width:120px;" />'
                            );

                        $(win.document.body)
                            .find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                    }
                },
                ],
                "drawCallback": function (settings) {
                    // Almacenar los datos de la tabla en una variable del componente Vue 

                    self.data_table_by_count = settings.json;
                }
            })
 

            //funciones para pagar varias cuotas
            $('#table_solicitud_aprobada').on('click', '.click-msj', function () {
                const index = $(this).attr('index'); // Obtén el ID de la fila  


                var data = self.table.row(index).data();

                if (data.is_mensaje == "N") {
                    self.is_modal_msj_create = true;
                    self.index_planilla = index;
                    self.prestamo_urlapi = data.urlapi;
                } else {
                    self.is_modal_msj_show = true;
                    self.prestamo_urlapi = data.urlapi;
                }

            });

            var self = this;
            $('#table_solicitud_aprobada tbody').on('dblclick', 'tr', function () {

                var data = self.table.row(this).data();
                self.is_opciones_modal = true;
                self.get_cuota = data;

            });

            this.table.on('init', function (e) {
                self.is_loading = true;
            });
        },


    },
    created() {

    },
    mounted() {



        this.fecha_actual = this.formatear_fecha(moment());

        this.load_user_data().then((result) => {
            $("#loading").fadeOut(200);
            $("#table").fadeIn(50);
        }).catch((err) => {

        });


        //maxItems: 3
    },
}
</script>

<style scoped>
/* Estilos para impresión */
@media print {

    /* Ocultar el botón de impresión */
    button {
        display: none;
    }

    /* Estilos específicos para el DataTable al imprimir */
    .datatable {
        /* Agrega tus estilos específicos para la tabla al imprimir */
        /* Por ejemplo: */
        font-size: 12px;
        color: black;
        /* etc. */
    }

    .datatable {
        /* Agrega tus estilos específicos para la tabla al imprimir */
        /* Por ejemplo: */
        font-size: 12px;
        color: black;
        /* etc. */
    }
}
</style>