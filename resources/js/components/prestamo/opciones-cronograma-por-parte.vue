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
                                <th class="whitespace-nowrap">Mora</th>
                                <th class="whitespace-nowrap">Estado</th>
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


    </ScrollPanel>


    <Sidebar v-model:visible="is_opciones_modal_cronograma" header="Opciones para la cuota" position="bottom"
        style="height: auto">

        <div id="link-button" class="p-5">
            <button @click="is_opciones_modal_cronograma_pago = true;" class="btn btn-success text-white mr-1 p-4 mb-2">
                <Icon icon='coins' class='mr-2' /> Pagar cuota
            </button> 
        </div>
    </Sidebar>



    <Sidebar v-model:visible="is_opciones_modal_cronograma_pago_por_parte" header="Formulario para pagar cuotas"
        position="top" style="height: auto">
        <pagar-por-parte :sumar_ingresos="sumar_ingresos" :get_cuota="get_cuota"></pagar-por-parte>
    </Sidebar>

    <button type="button" class="btn btn-primary btn-flotante" label="Image"
        @click="is_opciones_modal_cronograma_pago_por_parte = true;">
        <Icon icon='edit' class='mr-2' /> Pagar Prestamo
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
            tabla_cronograma: null,
            reprogramacion: false,
            select_cronograma: null,
            is_opciones_modal_cronograma: false,
            is_opciones_modal_cronograma_pago: false,
            is_opciones_modal_cronograma_pago_por_parte: false,
            get_ingresos: []
        };
    },
    computed: {
        sumar_ingresos() {
            if (this.get_ingresos.length != 0) {
                const importe = this.get_ingresos.reduce((acumulador, res) => {
                    return acumulador + parseFloat(res.monto);
                }, 0);
                return this.redondear(importe);

            } else {
                return 0;
            }
        }
    },
    methods: { 
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
        load_cronogramas_datatable(result) {

            this.$nextTick(() => {
                if ($.fn.DataTable.isDataTable("#table_cronograma")) {
                    $("#table_cronograma").DataTable().destroy();
                }
                var self = this;
                this.tabla_cronograma = $('#table_cronograma').DataTable({
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
                    },  /*{
                        text: '<i class="fa fa-file-pdf"></i> Pdf',
                        extend: 'pdfHtml5',
                        title: 'tabla_cliente_fecha_',

                    },*/ {
                        text: '<i class="fa fa-print"></i> Imprimir',
                        extend: "print",
                        title: 'tabla_cliente_fecha_'
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
                        text: '<i class="fa fa-money-bill-trend-up"></i> Refinanciar', // Texto del botón extra
                        action: function (e, dt, node, config) {
                            if (self.user.rol == "gerente") {
                                window.location.href = `/refinanciamiento/${self.get_cuota.solicitud.urlapi}`;
                            } else {
                                self.alert_warning("esta opcion solo es para los gerentes")
                            }

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
                        text: '<i class="fa-solid fa-eye"></i> Ver solicitud', // Texto del botón extra
                        action: function (e, dt, node, config) {

                            window.location.href = `/solicitud/${self.get_cuota.solicitud.urlapi}`;

                        }
                    },
                    {
                        text: '<i class="fa-solid fa-xmarks-lines"></i> Paralelo', // Texto del botón extra
                        action: function (e, dt, node, config) {
               
                                window.location.href = `/solicitud/create/${self.get_cuota.solicitud.urlapi}`;
                       
                            
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
                            "data": "monto_mora",
                            render: function (data, type, row) { 

                                if (row.yes_pago == "N") {

                                    switch (row.yes_mora) {
                                        case "N":
                                            const fechaDada = moment(row.fechaVencimiento);
                                            const fechaActual = moment();
                                            // Comparar la fecha actual con la fecha dada 
                                            if (fechaDada.isBefore(fechaActual, 'day')) {

                                                self.check_mora("Y", row.urlapi);
                                                row.yes_mora = "Y";
                                                row.monto_mora = row.interes;
                                                return `<div "> ${row.interes} </div>`;

                                            } else if (fechaDada.isSame(fechaActual, 'day')) {
                                                return `<div "> NO </div>`;
                                            } else {
                                                return `<div "> NO </div>`;
                                            }
                                            break;

                                        case "Y":
                                            return `<div "> ${row.monto_mora} </div>`;
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
                            // console.log(err);
                        });
                    } else {
                        self.check_mora("S", data.urlapi).then((response) => {

                            var rowData = self.tabla_cronograma.row(index).data(); // Datos de la fila seleccionada

                            rowData.monto_mora = 0;
                            rowData.yes_mora = response.yes_mora;

                            self.tabla_cronograma.row(index).data(rowData)
                            self.loading_end(); // Cambia el valor de la columna "Nombre" 
                        }).catch((err) => {
                            // console.log(err);
                        });
                    }
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
                          
                        });
                    }
                });



            });

        }
    },
    mounted() {
        var self = this;

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

        this.load_ingresos().then((result) => {
            this.get_ingresos = result;
        }).catch((err) => {
        
        });
    }
}
</script>

<style></style>