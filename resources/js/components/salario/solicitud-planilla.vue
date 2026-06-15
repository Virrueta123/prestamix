<template>

    <div class="intro-y box mt-5">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
            <h2 class="font-medium text-base mr-auto text-primary">
                <Icon icon='table-list' class='mr-2' /> Formulario para crear una solicitud de trabajadores
            </h2>
        </div>

        <form id="form_solicitud_trabajador" method="POST" action="#">
            <div id="vertical-form" class="p-5">
                <div class="">

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-12">
                            <label for="vertical-form-2" class="form-label">Motivo</label>
                            <input name="descripcion" v-model="descripcion" type="text" class="form-control"
                                placeholder="Descripcion">
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Monto solicitado</label>
                            <InputNumber class="form-control p-2 border border-success" v-model="monto" name="monto"
                                placeholder="Monto del gasto" inputId="locale-us" locale="en-US"
                                :minFractionDigits="2" />
                        </div>

                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label"> Tipo de solicitud </label>

                            <select class="form-control" v-on:change="change_tipo_solicitud($event)" name="tipo" id=""
                                v-model="tipo">
                                <option value="D"> Descuentos </option>
                                <option value="A"> Adelanto</option>
                                <option value="S"> Pagar salario restante</option>
                            </select>

                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-12">
                            <label for="vertical-form-2" class="form-label"> Salario </label>
                            <select-salario @change="change_salario($event)" name="salario"></select-salario>
                        </div>
                    </div>



                    <div id="basic-button" class="p-1 mt-4">
                        <div class="preview">
                            <button type="submit" class="btn btn-primary mr-1 mb-2">
                                <Icon icon="plus" class="ml-2" /> Registrar Solicitud
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </form>

        <div class="intro-y box px-5 pt-5 mt-1" v-if="tipo == 'S'">

            <div class="intro-y flex-1 box py-16 lg:ml-5 mb-5 lg:mb-0" v-if="get_salario_data != null">
                <div class="flex justify-center bg-success">
                    <div class="relative text-5xl p-2 font-semibold text-white  mx-auto">
                        {{ formatear_mes(get_salario_data.fecha_inicio) }}
                    </div>
                </div>
                <div class="flex justify-center">
                    <div class="relative text-5xl font-semibold mx-auto">
                        Salario de {{ get_salario_data.trabajador.name }} {{ get_salario_data.trabajador.lastname }}
                    </div>
                </div>

                <div class="text-base text-slate-500 mt-8 text-center">Salario</div>
                <div class="flex justify-center">
                    <div class="relative text-5xl font-semibold   mx-auto">
                        {{ formatear_dinero_soles(get_salario_data.monto) }}
                    </div>
                </div>

                <div class="text-base text-slate-500 mt-8 text-center">Salario adelantado</div>
                <div class="flex justify-center">
                    <div class="relative text-5xl font-semibold   mx-auto">
                        {{ formatear_dinero_soles(get_salario_data.pagado) }}
                    </div>
                </div>

                <div class="text-base text-slate-500 mt-8 text-center">Salario Restante</div>
                <div class="flex justify-center">
                    <div v-if="get_salario_data.monto - get_salario_data.pagado < 0"
                        class="relative text-5xl font-semibold text-danger  mx-auto">
                        {{ formatear_dinero_soles(get_salario_data.monto - get_salario_data.pagado) }}
                    </div>
                    <div v-else class="relative text-5xl font-semibold   mx-auto">
                        {{ formatear_dinero_soles(get_salario_data.monto - get_salario_data.pagado) }}
                    </div>
                </div>
                <center>
                    <button v-on:click="pagar_sueldo()" class="btn btn-success w-32 mr-2 mt-4 p-2 text-white"
                        v-if="(get_salario_data.monto - get_salario_data.pagado) > 0">
                        <Icon icon='coins' class='mr-2' /> Pagar sueldo
                    </button>
                </center>
            </div>


            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
                <h2 class="font-medium text-base mr-auto">
                    Tabla de solicitudes de este salario ( procesados )
                </h2>
            </div>


            <div class="p-5" id="small-table">
                <div class="preview" style="display: block;">
                    <div class="overflow-x-auto">
                        <table class="table " id="table_solicitud_trabajador_procesado">
                            <thead>
                                <tr>
                                    <th>Descripcion</th>
                                    <th>Tipo solicitud</th>
                                    <th>Trabajador</th>
                                    <th>Monto</th>
                                    <th>Pagos</th>
                                    <th>Fecha creacion</th>
                                    <th>Fecha de respuesta</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>

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
            gastos_descripcion: "",
            monto: "",
            tipo: "A",
            salario_id: null,
            start: true,
            is_loading: false,
            suma_monto_adelanto: 0,
            get_salario_data: null
        }
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
        // pagar sueldo restante a un trabajador
        pagar_sueldo() {
            console.log("pagando sueldo...");
            const data = {
                salario_id: this.salario_id,
                monto: this.get_salario_data.monto - this.get_salario_data.pagado
            };

            const headers = this.headers;

            this.loading_start();

            return Axios
                .post("/pagar_salario", data, {
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
                    return {
                        message: "Error al cargar los datos, recargar el navegador",
                        success: false
                    }
                });
        },
        buscar_salario() {

            this.get_salario().then((result) => {
                this.get_salario_data = result.data;
            }).catch((err) => {
                this.message.error("Error al cargar los datos del salario");
            });

            if (this.start) {
                this.start = false;
            } else {
                $("#table_solicitud_trabajador_procesado").DataTable().clear().draw();
            }

            this.$nextTick(() => {
                var self = this;

                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });



                var table = $('#table_solicitud_trabajador_procesado').DataTable({
                    language: this.spanish_datatable,
                    retrieve: true,
                    paging: false,
                    ajax: {
                        url: '/tabla_solicitud_trabajadores_procesado_by_trabajador',
                        type: 'post',
                        "data": function (d) {
                            // Aquí puedes definir los parámetros personalizados que deseas enviar al servidor
                            d.salario_id = self.salario_id;
                            d.is_created_solicitud = true;
                            // Agrega más parámetros según sea necesario
                        },
                    },
                    "order": [[5, "desc"]],
                    columns: [
                        { data: "descripcion" },
                        {
                            data: "tipo",
                            render: function (data, type, row) {
                                switch (data) {
                                    case "A":
                                        return "<div> Adelanto </div>";
                                        break;
                                    case "D":
                                        return "<div> Descuento </div>";
                                        break;
                                    case "S":
                                        return "<div> Salario cancelado </div>";
                                        break;
                                }
                            }

                        },
                        { data: "trabajador" },
                        {
                            data: "monto",
                            render: function (data, type, row) {

                                return `<div> ${self.formatear_dinero_soles(data)} </div>`;

                            }
                        },
                        {
                            data: "monto",
                            render: function (data, type, row) {
                                console.log(row);
                                var string = ``;

                                row.gasto.pago.map(element => {
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
                            data: "created_at",
                            render: function (data, type, row) {

                                return `<div> ${self.formatear_fecha(data)} </div>`;

                            }
                        },
                        {
                            data: "updated_at",
                            render: function (data, type, row) {

                                return `<div> ${self.formatear_fecha(data)} </div>`;

                            }
                        },
                        {
                            data: "status",
                            render: function (data, type, row) {
                                switch (data) {
                                    case "P":
                                        return "<div> Pendiente </div>";
                                        break;
                                    case "R":
                                        return "<div> Rechazado </div>";
                                        break;
                                    case "A":
                                        return "<div> Aprobado </div>";
                                        break;
                                    case "G":
                                        return "<div> Procesado </div>";
                                        break;
                                }
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
                })

                table.on('init', function (e) {
                    self.is_loading = true;
                });
            });

        },
        // evento para cambiar el salario del trabajador
        change_salario(event) {
            this.salario_id = event.target.value;

            if (this.tipo == "S") {
                this.buscar_salario();
            }
        },
        async get_salario() {
            const data = {
                salario_id: this.salario_id,
            };

            const headers = this.headers;

            return Axios
                .post("/get_salario", data, {
                    headers,
                })
                .then((response) => {
                    return response.data;
                })
                .catch((error) => {
                    this.alert_error_modal("Error en el servidor");
                    console.error(error);
                    return {
                        message: "Error al cargar los datos, recargar el navegador",
                        success: false
                    }
                });
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

        $("#form_solicitud_trabajador").validate({
            rules: {
                descripcion: {
                    required: true,
                    noSpecial: true
                },
                monto: {
                    required: true
                },
                salario: {
                    required: true
                }

            },
            submitHandler: function (form) {
                if (self.monto == "0") {
                    self.alert_warning("tienes que agregar un a esta solicitud");
                    return false;
                }
                self.solicitud_trabajador();
                return false;
            }
        });

    }
}
</script>

<style></style>