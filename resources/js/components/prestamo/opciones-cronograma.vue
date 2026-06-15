<template>
    <ScrollPanel style="width: 100%; height: 400px">

        <div class="p-2">
            <div class="preview">
                <div class=" ">
                    <table class="table table-sm table-bordered" id="table_cronograma" style="font-size: 0.8rem;">
                        <thead>
                            <tr>
                                <th class="whitespace-nowrap">Periodo</th>
                                <th class="whitespace-nowrap">Fecha Vencimiento</th>
                                <th class="whitespace-nowrap">Saldo capital</th>
                                <th class="whitespace-nowrap">Amortizacion</th>
                                <th class="whitespace-nowrap">Interes</th>
                                <th class="whitespace-nowrap">Cuota</th>
                                <th class="whitespace-nowrap">Monto cancelado</th>
                                <th class="whitespace-nowrap">Saldo</th>
                                <th class="whitespace-nowrap">Mora</th>
                                <th class="whitespace-nowrap">Estado</th>
                                <th class="whitespace-nowrap">Cobrar mora</th>
                                <th class="whitespace-nowrap no_mostrar_print">Cobrar interes</th>
                                <th class="whitespace-nowrap">Pagar</th>
                                <th class="whitespace-nowrap">-</th>
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>
        <div class="text-primary font-semibold text-3xl">Ingresos</div>


        <table class="table table-sm table-bordered" style="font-size: 0.8rem;">
            <thead>
                <tr>
                    <th class="whitespace-nowrap">Descripcion</th>
                    <th class="whitespace-nowrap">Monto</th>
                    <th class="whitespace-nowrap">cuenta</th>
                    <th class="whitespace-nowrap">Codigo</th>
                    <th class="whitespace-nowrap">fecha</th>
                    <th class="whitespace-nowrap">Oficina</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(g_i, index_g_i) in get_ingresos" :key="index_g_i">
                    <td>{{ g_i.descripcion }}</td>
                    <td>{{ g_i.monto }}</td>
                    <td><button type="button" name="" id="" class="btn btn-primary btn-xs btn-block">
                            <Icon icon='eye' class='' />
                        </button></td>
                    <td>{{ g_i.codigo }}</td>
                    <td>{{ formatear_fecha(g_i.created_at) }}</td>
                    <td v-if="g_i.yes_office == 'Y'">Si</td>
                    <td v-else>No</td>
                </tr>
                <tr v-if="get_ingresos.length == 0">
                    <td colspan="7" class="text-center">
                        <Icon icon='eye' class='mr-2' /> no hay datos que mostrar
                    </td>
                </tr>
            </tbody>
        </table>

        <div id="cronogram_print" style="display: none;">
            <center>
                <img class="card-img-top" width="28%" src="../../../../public/dist/images/logo/logo.svg" alt="">
            </center>

            <div class="box p-5 rounded-md mt-5">

                <div class="flex items-center mt-5 border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
                    <div class="font-medium text-base truncate">
                        <Icon icon="user" class="mr-2 text-primary" />Información del cliente
                    </div>
                </div>
                <div class="flex items-center">
                    <Icon icon="check" class="mr-2 text-primary" /> Nombres y apellidos:
                    <div class="ml-auto">{{ get_cuota.solicitud.cliente.cli_nombre }} {{
                        get_cuota.solicitud.cliente.cli_apellido }}</div>
                </div>
                <hr>

                <div class="flex items-center">
                    <Icon icon="check" class="mr-2 text-primary" /> Dni:
                    <div class="ml-auto">{{ get_cuota.solicitud.cliente.cli_dni }}</div>
                </div>
                <hr>

                <hr>

            </div>

        </div>

    </ScrollPanel>


    <Sidebar v-model:visible="is_opciones_modal_cronograma" header="Opciones para la cuota" position="bottom"
        style="height: auto">

        <div id="link-button" class="p-5">
            <button @click="is_opciones_modal_cronograma_pago = true;" class="btn btn-success text-white mr-1 p-4 mb-2">
                <Icon icon='coins' class='mr-2' /> Pagar cuota
            </button>

        </div>
    </Sidebar>

    <Sidebar v-model:visible="is_opciones_modal_cronograma_pago" header="Opciones para la cuota" position="bottom"
        style="height: auto">
        <pagar-cuota :get_cuota="get_cuota" :select_cronograma="select_cronograma"></pagar-cuota>
    </Sidebar>

    <Sidebar v-model:visible="is_opciones_modal_cronograma_pago_grupal" header="Formulario para pagar cuotas"
        position="top" style="height: auto">
        <pagar-cuota-grupal :get_cuota="get_cuota" :pago_grupal="pago_grupal"></pagar-cuota-grupal>
    </Sidebar>

    <button v-if="pago_grupal.length != 0" type="button" class="btn btn-primary btn-flotante" label="Image"
        @click="is_opciones_modal_cronograma_pago_grupal = true;">
        <Icon icon='edit' class='mr-2' /> Pagar ( {{ pago_grupal.length }} ) cuotas
    </button>
</template>

<script>
// mixin
import {
    myMixin
} from "../../mixin.js";
import TomSelect from 'tom-select';
import Axios from 'axios';

import moment from "moment";

export default {
    mixins: [myMixin],
    data() {
        return {
            get_cuota: this.$attrs.get_cuota,
            cronogramas: null,
            pago_grupal: [],
            contador: "disabled",
            tabla_cronograma: null,
            reprogramacion: false,
            select_cronograma: null,
            get_ingresos: [],
            is_opciones_modal_cronograma: false,
            is_opciones_modal_cronograma_pago: false,
            is_opciones_modal_cronograma_pago_grupal: false
        };
    },
    methods: {
        // cargar todo los ingresos de este prestamo
        load_ingresos() {
            const data = {
                urlapi: this.get_cuota.solicitud.urlapi
            }

            const headers = this.headers;

            this.loading_start();

            return Axios
                .post("/load_ingresos_prestamo", data, {
                    headers,
                })
                .then((response) => {
                    console.log("ingresos",response);
                    
                    if (response.data.success) {
                        // this.alert_success(response.data.message);
                        return response.data.data;
                        
                    } else {
                        this.alert_warning(response.data.message);
                    }
                })
                .catch((error) => {
                    this.alert_error_modal("Error en el servidor, recargue la pagina");
                    console.error(error);
                });
        },
        pagar_cuota() {

        },
        async check_interes(yes_interes, urlapi) {
            const data = {
                urlapi: urlapi,
                yes_interes: yes_interes
            };

            const headers = this.headers;

            this.loading_start();

            return Axios
                .put("/updated_yes_interes", data, {
                    headers,
                })
                .then((response) => {
                    if (response.data.success) {
                        this.alert_success(response.data.message);
                        this.loading_end();
                        return response.data.data;
                    } else {
                        this.alert_warning(response.data.message);
                    }

                })
                .catch((error) => {

                    this.alert_error_modal("Error en el servidor");
                    console.error(error);
                });
        },
        async check_mora(yes_mora, urlapi) {
            const data = {
                urlapi: urlapi,
                yes_mora: yes_mora
            };

            const headers = this.headers;

            this.loading_start();

            return Axios
                .put("/updated_yes_mora", data, {
                    headers,
                })
                .then((response) => {
                    if (response.data.success) {

                        this.loading_end();
                        return response.data.data;
                    } else {
                        this.alert_warning(response.data.message);
                    }

                })
                .catch((error) => {

                    this.alert_error_modal("Error en el servidor");
                    console.error(error);
                });
        },
        load_cronogramas() {

            const data = {
                urlapi: this.get_cuota.urlapi
            }

            const headers = this.headers;

            this.loading_start();

            return Axios
                .post("/get_cuotas", data, {
                    headers,
                })
                .then((response) => {

                    if (response.data.success) {
                        // this.alert_success(response.data.message);
                        return response.data.data;

                    } else {
                        this.alert_warning(response.data.message);
                    }

                })
                .catch((error) => {

                    this.alert_error_modal("Error en el servidor, recargue la pagina");
                    console.error(error);
                });
        },
        // esta funcion se encarga para mostrar los cronogramas en una tabla y lo cancelara siempre y cuando aun no este pagado
        cancelar_cronograma(prestamo_id, ncuota, periodo, cronograma_id) {
            const data = {
                prestamo_id: prestamo_id,
                ncuota: ncuota,
                periodo: periodo,
                cronograma_id: cronograma_id
            }

            const headers = this.headers;

            this.loading_start();

            return Axios
                .post("/cancelar_cronograma", data, {
                    headers,
                })
                .then((response) => {

                    if (response.data.success) {

                        window.location.reload();

                    } else {
                        this.alert_warning(response.data.message);
                    }
                    this.loading_end();
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor, recargue la pagina");
                });
        },
        load_cronogramas_datatable(result) {

            this.$nextTick(() => {
                if ($.fn.DataTable.isDataTable("#table_cronograma")) {
                    $("#table_cronograma").DataTable().destroy();
                }

                var self = this;
                this.tabla_cronograma = $('#table_cronograma').DataTable({
                    "columnDefs": [
                        {
                            "targets": 11,  // Índice de la columna donde quieres agregar la clase
                            "className": "no_mostrar_print"
                        }
                    ],
                    paging: false,
                    searching: false,
                    scrollCollapse: true,
                    scrollY: '280px',
                    processing: true,
                    "infoFiltered": "",
                    "processing": "<img src='~/Content/images/loadingNew.gif' />",
                    dom: 'Bfrtip',
                    autoWidth: true,
                    responsive: true,
                    data: result,
                    "ordering": true,
                    "language": this.spanish_datatable,
                    "buttons": [{
                        text: '<i class="fa fa-bars"></i> columnas visibles',
                        extend: 'colvis',
                    }
                        , {
                        extend: 'print',
                        customize: function (win) {
                            $(win.document.body)
                                .css('font-size', '10pt')
                                .prepend(
                                    $("#cronogram_print").html()
                                );
                            ;

                            $(win.document.body).find('table thead th:nth-child(11)').remove();
                            $(win.document.body).find('table thead th:nth-child(12)').remove();
                            $(win.document.body).find('table thead th:nth-child(13)').remove();
                            $(win.document.body).find('table thead th:nth-child(14)').remove();
                            $(win.document.body).find('table thead th:nth-child(15)').remove();
                            $(win.document.body).find('table thead th:nth-child(16)').remove();
                            $(win.document.body).find('table thead th:nth-child(17)').remove();
                            $(win.document.body).find('table thead th:nth-child(18)').remove();
                            $(win.document.body).find('table thead th:nth-child(19)').remove();



                            $(".no_mostrar_print").css('background', 'red');



                            $(".no_mostrar_print").remove();

                            $(win.document.body).find('table tbody tr').each(function () {

                                $(this).find('td:nth-child(11)').remove();
                                $(this).find('td:nth-child(12)').remove();
                                $(this).find('td:nth-child(13)').remove();
                                $(this).find('td:nth-child(14)').remove();
                                $(this).find('td:nth-child(15)').remove();
                                $(this).find('td:nth-child(16)').remove();
                                $(this).find('td:nth-child(17)').remove();
                                $(this).find('td:nth-child(18)').remove();
                                $(this).find('td:nth-child(19)').remove();
                            });


                            $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');

                        }
                    },
                    {
                        text: '<i class="fa fa-calendar-days"></i> Reprogramacion', // Texto del botón extra
                        action: function (e, dt, node, config) {

                            if (self.user.rol == "gerente") {
                                window.location.href = `/reprogramacion/${self.get_cuota.solicitud.urlapi}`;
                            } else {
                                self.alert_warning("esta opcion solo es para los gerentes")
                            }
                        }
                    },
                    {
                        text: '<i class="fa fa-calendar-minus"></i> Reprogramacion Cuota', // Texto del botón extra
                        action: function (e, dt, node, config) {
                            if (self.user.rol == "gerente") {
                                window.location.href = `/reprogramacion_cuota/${self.get_cuota.solicitud.urlapi}`;
                            } else {
                                self.alert_warning("esta opcion solo es para los gerentes")
                            }
                        }
                    },
                    {
                        text: '<i class="fa-solid fa-eye"></i> Ver solicitud', // Texto del botón extra
                        action: function (e, dt, node, config) {

                            window.location.href = `/solicitud/${self.get_cuota.solicitud.urlapi}`;

                        }
                    },
                    {
                        text: '<i class="fa-solid fa-arrow-up-right-dots"></i> Ampliacion', // Texto del botón extra
                        action: function (e, dt, node, config) {
                            if (self.user.rol == "gerente") {
                                window.location.href = `/ampliacion/${self.get_cuota.solicitud.urlapi}`;
                            } else {
                                self.alert_warning("esta opcion solo es para los gerentes")
                            }
                        }
                    },
                    {
                        text: '<i class="fa-solid fa-xmarks-lines"></i> Paralelo', // Texto del botón extra
                        action: function (e, dt, node, config) { 
                            window.location.href = `/solicitud/create/${self.get_cuota.solicitud.urlapi}`; 
                        }
                    },
                    {
                        text: '<i class="fa-solid fa-xmarks-lines"></i> Cancelar prestamo', // Texto del botón extra
                        action: function (e, dt, node, config) { 
                            window.location.href = `/cancelar_prestamo/${self.get_cuota.solicitud.urlapi}`; 
                        }
                    },
                    {
                        text: '<i class="fa-solid fa-trash"></i> ', // Texto del botón extra
                        action: function (e, dt, node, config) {

                            if (self.user.rol == "gerente") {
                                window.location.href = `/desaparecer_prestamo/${self.get_cuota.solicitud.urlapi}`;
                            } else {
                                self.alert_warning("esta opcion solo es para los gerentes")
                            }
                        }
                    }

                    ],
                    columns: [
                        { "data": "periodo" },
                        {
                            "data": "fechaVencimiento",
                            render: function (data, type, row) {
                                if (row.yes_pago == "N") {
                                    const fechaDada = moment(data);
                                    const fechaActual = moment();

                                    if (fechaDada.isBefore(fechaActual, 'day')) {
                                        return `<div class="alert fila-roja  p-0 text-center text-white show "> ${data} </div>`;
                                    } else if (fechaDada.isSame(fechaActual, 'day')) {
                                        return `<div class="alert  fila-amarilla  p-0 text-center text-white show "> ${data} </div>`;

                                    } else {
                                        return `<div class="alert fila-pendiente  p-0 text-center text-white show "> ${data} </div>`;

                                    }

                                } else {
                                    return `<div class="alert fila-pagada  p-0 text-center text-white show "> ${data} </div>`;

                                }
                            }
                        },
                        { "data": "saldoCapital" },
                        { "data": "amortizacion" },
                        {
                            "data": "interes",
                            render: function (data, type, row) {

                                if (row.yes_interes == "N") {
                                    row.cuota = row.amortizacion;
                                    return `<div "> 0 </div>`;
                                } else {
                                    return `<div "> ${data} </div>`;
                                }

                            }
                        },
                        {
                            "data": "cuota",
                            render: function (data, type, row) {
                                if (row.yes_interes == "Y") {
                                    return `${data}`;
                                } else {
                                    return `${row.amortizacion}`;
                                }
                            }
                        },
                        {
                            "data": "cuota",
                            render: function (data, type, row) {
                                return row.pagado;
                            }
                        },
                        {
                            "data": "cuota",
                            render: function (data, type, row) {

                                let mora = 0;
                                if (row.yes_pago == "N") {

                                    switch (row.yes_mora) {
                                        case "N":
                                            const fechaDada = moment(row.fechaVencimiento);
                                            const fechaActual = moment();
                                            // Comparar la fecha actual con la fecha dada 
                                            if (fechaDada.isBefore(fechaActual, 'day')) {
                                                var diferencia = fechaActual.diff(fechaDada, 'days');

                                                self.check_mora("Y", row.urlapi);
                                                row.yes_mora = "Y";
                                                row.monto_mora = row.interes;

                                                var interes_by_days = row.interes / 30;

                                                var interes_cuota_actual = 0;

                                                if (diferencia >= 30) {
                                                    interes_cuota_actual = interes_by_days * 30;
                                                } else {
                                                    interes_cuota_actual = interes_by_days * diferencia;
                                                }

                                                mora = self.redondear(interes_cuota_actual);

                                            } else if (fechaDada.isSame(fechaActual, 'day')) {
                                                mora = 0;
                                            } else {
                                                mora = 0;
                                            }
                                            break;

                                        case "Y":
                                            const fecha_vencimiento = moment(row.fechaVencimiento);
                                            const fecha_actual = moment();

                                            var diferencia = fecha_actual.diff(fecha_vencimiento, 'days');

                                            var interes_by_days = row.interes / 30;

                                            var interes_cuota_actual = 0;

                                            if (diferencia >= 30) {
                                                interes_cuota_actual = interes_by_days * 30;
                                            } else {
                                                interes_cuota_actual = interes_by_days * diferencia;
                                            }

                                            mora = self.redondear(interes_cuota_actual);
                                            break;

                                        case "S":
                                        mora = 0;
                                            break;
                                    }
                                } else {
                                    return `<div "> Pagado </div>`;
                                }


                                let cuota = Number(row.cuota) || 0; // Si row.cuota es null o undefined, asignar 0
                                let montoMora = Number(mora) || 0;
                                let pagado = Number(row.pagado) || 0;

                                if (row.yes_mora == "Y") {

                                    return self.redondear((cuota + montoMora) - pagado);

                                } else {
                                    let monto = cuota - pagado;
                                    if (monto < 0) {
                                        return 0;
                                    } else {
                                        return self.redondear(monto);
                                    }

                                }

                            }
                        },
                        {
                            "data": "monto_mora",
                            render: function (data, type, row) {

                                if (row.yes_pago == "N") {

                                    switch (row.yes_mora) {
                                        case "N":
                                            const fechaDada = moment(row.fechaVencimiento);
                                            const fechaActual = moment();
                                            // Comparar la fecha actual con la fecha dada 
                                            if (fechaDada.isBefore(fechaActual, 'day')) {
                                                var diferencia = fechaActual.diff(fechaDada, 'days');

                                                self.check_mora("Y", row.urlapi);
                                                row.yes_mora = "Y";
                                                row.monto_mora = row.interes;

                                                var interes_by_days = row.interes / 30;

                                                var interes_cuota_actual = 0;

                                                if (diferencia >= 30) {
                                                    interes_cuota_actual = interes_by_days * 30;
                                                } else {
                                                    interes_cuota_actual = interes_by_days * diferencia;
                                                }

                                                return `<div "> ${self.redondear(interes_cuota_actual)} </div>`;

                                            } else if (fechaDada.isSame(fechaActual, 'day')) {
                                                return `<div "> NO </div>`;
                                            } else {
                                                return `<div "> NO </div>`;
                                            }
                                            break;

                                        case "Y":
                                            const fecha_vencimiento = moment(row.fechaVencimiento);
                                            const fecha_actual = moment();

                                            var diferencia = fecha_actual.diff(fecha_vencimiento, 'days');

                                            var interes_by_days = row.interes / 30;

                                            var interes_cuota_actual = 0;

                                            if (diferencia >= 30) {
                                                interes_cuota_actual = interes_by_days * 30;
                                            } else {
                                                interes_cuota_actual = interes_by_days * diferencia;
                                            }

                                            return `<div "> ${self.redondear(interes_cuota_actual)} </div>`;
                                            break;

                                        case "S":
                                            return `<div "> 0 </div>`;
                                            break;
                                    }
                                } else {
                                    return `<div "> Pagado </div>`;
                                }
                            }
                        },
                        {
                            "data": "status",
                            render: function (data, type, row) {

                                if (row.yes_pago == "Y") {
                                    return "Pagado";
                                }
                                const fechaDada = moment(row.fechaVencimiento);
                                const fechaActual = moment();
                                // Comparar la fecha actual con la fecha dada 
                                switch (data) {
                                    case "A":
                                        return `<div "> Pagado </div>`;
                                        break;
                                    case "P":
                                        if (fechaDada.isBefore(fechaActual, 'day')) {
                                            return `<div ">  Vencido </div>`;
                                        } else if (fechaDada.isSame(fechaActual, 'day')) {

                                            return `<div "> Pendiente / Hoy </div>`;
                                        } else {
                                            return `<div "> Pendiente </div>`;
                                        }

                                        break;
                                    case "N":
                                        if (fechaDada.isBefore(fechaActual, 'day')) {
                                            return `<div ">  Vencido </div>`;
                                        } else if (fechaDada.isSame(fechaActual, 'day')) {

                                            return `<div "> Pendiente / Hoy </div>`;
                                        } else {
                                            return `<div "> Pendiente </div>`;
                                        }
                                        break;
                                }
                            }
                        },

                        {
                            data: 'yes_mora',
                            render: function (data, type, row, meta) {
                                // Crea una casilla de verificación con el ID de la fila
                                if (row.yes_pago == "N") {

                                    switch (row.yes_mora) {
                                        case "N":
                                            const fechaDada = moment(row.fechaVencimiento);
                                            const fechaActual = moment();
                                            // Comparar la fecha actual con la fecha dada 
                                            if (fechaDada.isBefore(fechaActual, 'day')) {

                                                return `<div class="form-check form-switch">

                                                        <input  class="form-check-input chk-mora" checked index="${meta.row}" type="checkbox">

                                                     </div> `;

                                            } else if (fechaDada.isSame(fechaActual, 'day')) {
                                                return `<div "> NO </div>`;
                                            } else {
                                                return `<div "> NO </div>`;
                                            }

                                            break;

                                        case "Y":
                                            return `<div class="form-check form-switch">

                                                        <input  class="form-check-input chk-mora" checked index="${meta.row}" type="checkbox">

                                                        </div> `;
                                            break;

                                        case "S":
                                            return ` <div class="form-check form-switch">

                                                    <input  class="form-check-input chk-mora" index="${meta.row}" type="checkbox">

                                                    </div> `;
                                            break;
                                    }


                                } else {
                                    return "Pagado"
                                }

                            }
                        },
                        {
                            data: 'yes_interes',
                            render: function (data, type, row, meta) {
                                // Crea una casilla de verificación con el ID de la fila
                                if (row.yes_pago == "N") {
                                    var yes_interes = row.yes_interes == "Y" ? "checked" : "";

                                    return ` <div class="form-check form-switch no_mostrar_print">

                                                <input  ${yes_interes} class="form-check-input chk-select" index="${meta.row}" type="checkbox">

                                            </div> `;
                                } else {
                                    return "<div class='no_mostrar_print'> Pagado </div>"
                                }

                            }
                        },

                        {
                            data: 'yes_pago',
                            render: function (data, type, row, meta) {

                                if (row.urlapi == self.get_cuota.cuota_actual.urlapi) {
                                    return `<div class="form-check form-switch"> <input class="form-check-input chk-pago " index="${meta.row}" type="checkbox"> </div> `;
                                } else if (row.yes_pago == "N") {
                                    return "<div class=''>Pendiente</div>"; // or return some default value
                                } else {
                                    return "<div class=''>Pagado</div>";
                                }
                            }
                        },
                        {
                            data: 'yes_pago',
                            render: function (data, type, row, meta) {

                                if (data == "Y") {
                                   if(row.yes_reprogramacion == "Y"){
                                        return `<div class="text-success">Pagado (Reprogramación) </div>`;
                                    } else {
                                        return `<div class="text-success">Pagado</div>`;
                                    }
                                   
                                } else {
                                    return `<button class="btn btn-primary btn-sm click-cancelar" index="${meta.row}">Cancelar</button>`;
                                }
                            }
                        }

                    ],
                })

                //funciones para pagar varias cuotas
                $('#table_cronograma').on('change', '.chk-pago', function () {
                    const index = $(this).attr('index'); // Obtén el ID de la fila
                    const isChecked = $(this).prop('checked'); // Verifica si está marcado o no

                    var data = self.tabla_cronograma.row(index).data();

                    if (isChecked) {
                        self.pago_grupal.push(data)
                    } else {

                        let index = self.pago_grupal.findIndex(item => item.urlapi === data.urlapi);

                        // Si el elemento existe, elimínalo
                        if (index !== -1) {
                            self.pago_grupal.splice(index, 1);
                        }
                    }
                });

                //evento para check select  mora
                $('#table_cronograma').on('change', '.chk-mora', function () {
                    const index = $(this).attr('index'); // Obtén el ID de la fila
                    const isChecked = $(this).prop('checked'); // Verifica si está marcado o no 
                    var data = self.tabla_cronograma.row(index).data();

                    if (isChecked) {
                        self.check_mora("Y", data.urlapi).then((response) => {
                            var rowData = self.tabla_cronograma.row(index).data(); // Datos de la fila seleccionada 

                            rowData.monto_mora = response.interes;
                            rowData.yes_mora = response.yes_mora;
                            self.tabla_cronograma.row(index).data(rowData)
                            self.loading_end();
                        }).catch((err) => {
                            console.log(err);
                        });
                    } else {
                        self.check_mora("S", data.urlapi).then((response) => {

                            var rowData = self.tabla_cronograma.row(index).data(); // Datos de la fila seleccionada

                            rowData.monto_mora = 0;
                            rowData.yes_mora = response.yes_mora;

                            self.tabla_cronograma.row(index).data(rowData)
                            self.loading_end(); // Cambia el valor de la columna "Nombre" 
                        }).catch((err) => {
                            console.log(err);
                        });
                    }
                });

                // evento click para cancelar un cronograma
                $('#table_cronograma').on('click', '.click-cancelar', async function () {
                    const index = $(this).attr('index'); // Obtén el ID de la fila 
                    var data = self.tabla_cronograma.row(index).data();
  
                    await self.$swal.fire({
                        title: "Estas seguro que desea cancelar este cronograma?",
                        showDenyButton: true,
                        showCancelButton: false,
                        confirmButtonText: "Si, estoy seguro",
                        denyButtonText: `Cancelar`
                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                            self.cancelar_cronograma(
                                self.get_cuota.urlapi,
                                self.get_cuota.ncuotas,
                                data.periodo,
                                data.urlapi
                            );
                        }
                    });

                });

                //evento para check select no interes
                $('#table_cronograma').on('change', '.chk-select', function () {
                    const index = $(this).attr('index'); // Obtén el ID de la fila
                    const isChecked = $(this).prop('checked'); // Verifica si está marcado o no 
                    var data = self.tabla_cronograma.row(index).data();

                    if (isChecked) {
                        self.check_interes("Y", data.urlapi).then((response) => {
                            var rowData = self.tabla_cronograma.row(index).data(); // Datos de la fila seleccionada 
                            rowData.yes_interes = 'Y';
                            rowData.cuota = response.cuota;
                            rowData.interes = response.interes;
                            self.tabla_cronograma.row(index).data(rowData)
                            self.loading_end();
                        }).catch((err) => {
                            console.log(err);
                        });
                    } else {
                        self.check_interes("N", data.urlapi).then((response) => {

                            var rowData = self.tabla_cronograma.row(index).data(); // Datos de la fila seleccionada
                            rowData.cuota = response.amortizacion;
                            rowData.interes = 0;
                            rowData.yes_interes = 'N';

                            self.tabla_cronograma.row(index).data(rowData)
                            self.loading_end(); // Cambia el valor de la columna "Nombre" 
                        }).catch((err) => {
                            console.log(err);
                        });
                    }
                });

                // $('#table_cronograma tbody').on('dblclick', 'tr', function () {
                //     var data = self.tabla_cronograma.row(this).data();
                //     self.select_cronograma = data;
                //     self.is_opciones_modal_cronograma = true;
                // });

            });

        }
    },
    mounted() {
        var self = this;

        this.load_ingresos().then((result) => {
            this.get_ingresos = result;
        }).catch((err) => {
            console.log(err);
        });

        this.load_cronogramas().then((result) => {

            this.load_cronogramas_datatable(result);
            var ultimo_periodo = result[result.length - 1];


            const fechaDada = moment(ultimo_periodo.fechaVencimiento);
            const fechaActual = moment();

            if (fechaDada.isBefore(fechaActual, 'day')) {
                this.reprogramacion = true;
            }

            this.loading_end();

        }).catch((err) => {

        });



    }
}
</script>

<style></style>