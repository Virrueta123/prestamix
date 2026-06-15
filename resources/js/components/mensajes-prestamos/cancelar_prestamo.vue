<template>
    <div class="intro-y box overflow-hidden mt-5">
        <div class="border-b border-slate-200/60 dark:border-darkmode-400 text-center sm:text-left">
            <div class="px-5 py-10 sm:px-20 sm:py-20">
                <div class="text-primary font-semibold text-3xl">Cancelar el prestamo de la solicitud N {{
                    solicitud.code }}</div>
            </div>
        </div>
        <div class="intro-y box px-5 pt-5 mt-1">
            <div class="flex flex-col lg:flex-row border-b border-slate-200/60 dark:border-darkmode-400 pb-5 -mx-5">
                <div class="flex flex-1 px-5 items-center justify-center lg:justify-start">
                    <div class="w-20 h-20 sm:w-20 sm:h-24 flex-none lg:w-32 lg:h-32 relative">
                        <img alt="Imagen no encontrado" class=" " width="300"
                            src="../../../../public/dist/images/Draw/solicitud.svg">
                    </div>
                    <div class="ml-5">
                        <div class="w-24 sm:w-40 sm:whitespace-normal font-medium text-lg">{{
                            solicitud.cliente.cli_nombre }} | {{ solicitud.cliente.cli_apellido }}</div>
                        <div class="text-slate-500">{{ solicitud.solicitud_giro }}</div>
                    </div>
                </div>
                <div
                    class="mt-6 lg:mt-0 flex-1 px-5 border-l border-r border-slate-200/60 dark:border-darkmode-400 border-t lg:border-t-0 pt-5 lg:pt-0">

                    <div class="box p-5 rounded-md mt-5">

                        <div
                            class="flex items-center mt-5 border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
                            <div class="font-medium text-base truncate">
                                <Icon icon="user" class="mr-2 text-primary" />Información de la solicitud
                            </div>
                        </div>

                        <div class="flex items-center">
                            <Icon icon="check" class="mr-2 text-primary" /> Tipo solicitud:
                            <div class="ml-auto">{{ solicitud.tipo_solicitud }}</div>
                        </div>


                        <div class="flex items-center">
                            <Icon icon="check" class="mr-2 text-primary" /> Fecha de solicitud:
                            <div class="ml-auto">{{ this.formatear_fecha(solicitud.created_at) }}</div>
                        </div>
                        <hr>


                        <div class="flex items-center">
                            <Icon icon="check" class="mr-2 text-primary" /> Entidad Bancaria:
                            <div class="ml-auto"
                                v-if="solicitud.solicitud_entidad_tarjeta == '' || solicitud.solicitud_entidad_tarjeta === null">
                                No
                                refiere</div>
                            <div class="ml-auto" v-else>{{ solicitud.solicitud_entidad_tarjeta }}</div>
                        </div>
                        <div class="flex items-center">
                            <Icon icon="check" class="mr-2 text-primary" /> Numero de cuenta de la entidad bancaria:
                            <div class="ml-auto"
                                v-if="solicitud.solicitud_tarjeta == '' || solicitud.solicitud_tarjeta === null">No
                                refiere</div>
                            <div class="ml-auto" v-else>{{ solicitud.solicitud_tarjeta }}</div>
                        </div>
                        <hr>

                        <div
                            class="flex mt-5 items-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
                            <div class="font-medium text-base truncate">
                                <Icon icon="phone" class="mr-2 text-primary" />Contactos
                            </div>
                        </div>

                        <div class="flex items-center"
                            v-for="(contacto, ct_index) in solicitud.cliente.contactos_cliente" :key="ct_index">
                            {{ contacto.ccliente_contacto }}
                            <div v-if="contacto.ccliente_descripcion" class="ml-auto">{{ contacto.ccliente_descripcion
                            }}
                            </div>
                            <div v-else class="ml-auto">No tiene descripcion</div>
                        </div>

                        <div
                            class="flex items-center mt-5 border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
                            <div class="font-medium text-base truncate">
                                <Icon icon="user" class="mr-2 text-primary" />Información del cliente
                            </div>
                        </div>

                        <div class="flex items-center">
                            <Icon icon="check" class="mr-2 text-primary" /> Dni:
                            <div class="ml-auto">{{ solicitud.cliente.cli_dni }}</div>
                        </div>
                        <hr>
                        <div class="flex items-center">
                            <Icon icon="check" class="mr-2 text-primary" /> Direccion domicilio:
                            <div class="ml-auto">{{ solicitud.cliente.cli_domicilio }}</div>
                        </div>
                        <hr>
                        <div class="flex items-center">
                            <Icon icon="check" class="mr-2 text-primary" /> Direccion de su trabajo:
                            <div class="ml-auto">{{ solicitud.cliente.cli_direccion_trabajo }}</div>
                        </div>
                        <hr>
                        <div class="flex items-center">
                            <Icon icon="check" class="mr-2 text-primary" /> Edad:
                            <div class="ml-auto">{{ calcularEdad(solicitud.cliente.fecha_nacimiento) }} </div>
                        </div>
                        <hr>
                        <div class="flex items-center">
                            <Icon icon="check" class="mr-2 text-primary" /> fecha de nacimiento:
                            <div class="ml-auto">{{ formatear_fecha(solicitud.cliente.fecha_nacimiento) }} </div>
                        </div>
                        <div
                            class="flex  mt-5 items-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
                            <div class="font-medium text-base truncate">
                                <Icon icon="credit-card" class="mr-2 text-primary" />Datos del Prestamo
                            </div>
                        </div>
                        <div class="flex items-center">
                            <Icon icon="check" class="mr-2 text-primary" /> Monto solicitado:
                            <div class="ml-auto text-success">{{ formatear_dinero(solicitud.prestamo.moto_credito) }}
                            </div>
                        </div>
                        <div class="flex items-center">
                            <Icon icon="check" class="mr-2 text-primary" /> Cuotas:
                            <div class="ml-auto text-success">{{ formatear_dinero(solicitud.prestamo.cuotas) }} </div>
                        </div>
                        <div class="flex items-center">
                            <Icon icon="check" class="mr-2 text-primary" /> {{
                                nomenclatura_frecuencia_pago(solicitud.prestamo.frecuencia_pagos) }}:
                            <div class="ml-auto text-success">{{ solicitud.prestamo.intervalo }} </div>
                        </div>
                        <div class="flex items-center">
                            <Icon icon="check" class="mr-2 text-primary" /> Tasa diaria:
                            <div class="ml-auto text-success">{{ solicitud.prestamo.tasa_diaria }} </div>
                        </div>
                        <div class="flex items-center">
                            <Icon icon="check" class="mr-2 text-primary" /> D.T:
                            <div class="ml-auto text-success">{{ solicitud.prestamo.d_t }} </div>
                        </div>
                        <div class="flex items-center">
                            <Icon icon="check" class="mr-2 text-primary" /> Interes:
                            <div class="ml-auto text-success">{{ solicitud.prestamo.interes }}
                                <Icon icon="percent" class="mr-2 text-success" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class=" col-span-12 lg:col-span-12 mt-3">
            <label for="vertical-form-2" class="form-label"> Fecha de la operacion de la cancelación del prestamo
            </label>
            <datepicker class="form-control col-span-12" v-on:change="change_fecha_actual()"
                v-model="fecha_actual_input" placeholder="hacer click para seleccionar"
                :styles="{ border: '2px solid #00ff00' }" :disabled-dates="rango_maximo" language="es">
            </datepicker>
        </div>


        <div class="px-5 sm:px-16 py-10 sm:py-20">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="border-b-2 dark:border-darkmode-400 whitespace-nowrap">Descripcion</th>

                            <th class="border-b-2 dark:border-darkmode-400 text-right whitespace-nowrap">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border-b dark:border-darkmode-400">
                                <div class="font-medium whitespace-nowrap">Interes diario </div>
                            </td>
                            <td class="text-right border-b dark:border-darkmode-400 w-32">
                                <InputNumber class="form-control p-2 border border-success" v-model="interespordia"
                                    name="monto_credito" placeholder="Monto del credito" inputId="locale-us"
                                    locale="en-US" :minFractionDigits="2" />
                            </td>
                        </tr>

                        <tr>
                            <td class="border-b dark:border-darkmode-400">
                                <div class="font-medium whitespace-nowrap"> Dias trancurridos del {{ fechainicio }} al
                                    {{ fecha_actual }} </div>
                            </td>
                            <td class="text-right border-b dark:border-darkmode-400 w-32">
                                <InputNumber class="form-control p-2 border border-success" v-model="diferenciadias"
                                    name="monto_credito" placeholder="Monto del credito" inputId="locale-us"
                                    locale="en-US" :minFractionDigits="2" />
                            </td>
                        </tr>


                        <tr v-if="isingresos">
                            <td class="border-b dark:border-darkmode-400">
                                <div class="font-medium whitespace-nowrap"> ingresos de la cuota actual periodo ({{
                                    cuotaactual.periodo }}) </div>
                            </td>
                            <td class="text-right border-b dark:border-darkmode-400 w-32">({{ amortizacion }}) </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
        <div class="px-5 sm:px-20 pb-10 sm:pb-20 flex flex-col-reverse sm:flex-row">

            <div class="text-center sm:text-right sm:ml-auto">
                <div class="text-xl text-primary font-medium mt-2">Interes total</div>
                <div class="mt-1">{{ formatear_dinero(montointeres) }}</div>

                <div class="text-xl text-primary font-medium mt-2">Monto restante de amortizacion</div>
                <div class="mt-1">{{ formatear_dinero(saldocapital) }}</div>


                <div class="text-xl text-primary font-medium mt-2">Total</div>
                <div class="mt-1">{{ formatear_dinero(total) }}</div>
            </div>


        </div>
        <div class="intro-y box mt-5">

            <h2 class="font-medium mt-2 text-base mr-auto text-primary">
                <Icon icon='coins' class='mr-2' /> Pagos a este Ingreso
            </h2>

            <div class="preview" v-if="pagos.length">
                <button @click="agregar_cuenta()" v-if="is_btn_pagos" type="button"
                    class="btn btn-primary mr-1 mt-2 mb-2">
                    <Icon icon="plus" />
                </button>
                <div>
                    <table class="table table-bordered table-pago table-hover" style="font-size: 11px;">
                        <thead>
                            <tr>
                                <th class="whitespace-nowrap">#</th>
                                <th class="whitespace-nowrap">Cuenta</th>
                                <th class="whitespace-nowrap">Monto</th>
                                <th class="whitespace-nowrap text-center">Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(pago, index_pago) in pagos" :key="index_pago">
                                <td>{{ pago.numero }}</td>
                                <td> <select-cuenta @change="change_cuenta(index_pago, $event)"
                                        @comunicarCuenta="escucharCuenta" :default="pago.cuentas_id"></select-cuenta>
                                </td>
                                <td>
                                    <InputNumber class="form-control p-2 border border-success" type="number"
                                        v-model="pagos[index_pago].monto" @input="keyup_cuenta(index_pago, $event)"
                                        name="monto" placeholder="Monto del gasto" inputId="locale-us" locale="en-US"
                                        :minFractionDigits="2" />

                                </td>
                                <td class="m-auto">
                                    <buteton @click="deleted_pago(index_pago)" v-if="index_pago == 1" type="button"
                                        name="" id="" class="btn btn-danger m-auto btn-xs">
                                        <Icon icon='trash' class='' />
                                    </buteton>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>



        </div>

        <div class="row" style="height: 200px; width: 100%;">

        </div>

        <button v-if="is_created && !isLoading" @click="cancelar_prestamo()" type="button"
            class="btn btn-primary btn-block btn-flotante btn-xs  ">
            Cancelar prestamo</button>
        <btn-loading v-if="isLoading"></btn-loading>
    </div>

    <!-- fin del modal para crear un cliente nuevo -->
</template>

<script>
import $ from 'jquery';
import { createApp, h } from 'vue';

import 'tom-select/dist/css/tom-select.css';
import TomSelect from 'tom-select';
import Axios from 'axios';

import "jquery-validation";
import "jquery-validation/dist/localization/messages_es"

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
            solicitud: this.$attrs.solicitud,
            prestamo: this.$attrs.prestamo,
            interespordia: this.$attrs.interespordia,
            diferenciadias: this.$attrs.diferenciadias,
            montointeres: this.$attrs.montointeres,
            isingresos: this.$attrs.isingresos,
            amortizacion: this.$attrs.amortizacion,
            cuotaactual: this.$attrs.cuotaactual,
            saldocapital: this.$attrs.saldocapital,
            total: this.$attrs.total,
            cuotaanterior: this.$attrs.cuotaanterior,
            fecha_actual: null,
            fechainicio: null,
            fecha_actual_input: null,
            fechainicio_input: null,
            rango_maximo: "",
            monto: "",
            pagos: [],
            delete_array: [],
            is_btn_pagos: false,
            is_created: false,
            isLoading: false
        }
    },

    computed: {
        totalCuota_pagos() {
            return this.pagos.reduce((sum, pa) => sum + parseFloat(pa.monto), 0);
        },
    },
    watch: {

        total: function (newValue) {
            if (newValue > 0) {
                this.pagos[0] = {
                    numero: 1,
                    cuentas_id: "MFNhZTlXUkhTSmpWNDQ5ZUc2YUhldz09",
                    monto: parseFloat(newValue)
                };
            }
        },
        fecha_actual_input: function (newValue) {

            console.log(newValue);
            this.fecha_actual = moment(this.fecha_actual_input).format('DD/MM/YYYY');


            this.diferenciadias = moment(this.fecha_actual_input).diff(moment(this.fechainicio_input), 'days');
            console.log(this.diferenciadias);


        },
        diferenciadias(newVal, oldVal) {
            this.montointeres = this.interespordia * this.diferenciadias
            this.calcular_total();
        },
        interespordia(newVal, oldVal) {
            this.montointeres = this.interespordia * this.diferenciadias
            this.calcular_total();
        },
        totalCuota_pagos: function (newValue) {
            if (this.total == newValue) {
                this.is_created = true;
            } else {
                this.is_created = false;
            }
        },
    },
    methods: {
        change_fecha_actual(e) {
        },
        calcular_total() {
            this.total = this.montointeres + this.saldocapital;
            if (this.isingresos) {
                this.total = this.total - this.amortizacion;
            }
            this.total = this.redondear(this.total);
        },
        cancelar_prestamo() {
            const data = {
                "prestamo": this.prestamo.urlapi,
                "pagos": this.pagos,
                "nombre_cliente": this.solicitud.solicitud_nombre,
                "nsolicitud": this.solicitud.code,
                "fecha_actual": this.fecha_actual,
                "fechainicio": this.fechainicio,
                "amortizacion": this.saldocapital,
                "amortizacion_ingresos": this.amortizacion,
                "interespordia": this.interespordia,
                "diferenciadias": this.diferenciadias,
                "montointeres": this.montointeres,
                "isingresos": this.isingresos,
                "periodo_anterior": this.cuotaanterior.periodo,
                "periodo_actual": this.cuotaactual.periodo,
                "total": this.total,
                "fecha_inicio": this.fechainicio_input,
                "fecha_final": this.fecha_actual_input,
            };

            let headers = this.headers;

            Axios
                .post("/procesar_cancelar_prestamo", data, {
                    headers,
                })
                .then((response) => {

                    if (response.data.success) {
 
                        this.alert_success(response.data.message);
                        window.location.href = `/planilla_prestamos`;
                    } else {
                        this.alert_warning(response.data.message);
                    }
                    this.loading_end();
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor, ingrese los nombres y apellidos manualmente");
                });
        },
        change_tipo_gasto_id() {
            const data = {
                tipo_gasto_id: this.tipo_gasto_id
            };

            console.log(this.tipo_gasto_id);

            const headers = this.headers;

            this.loading_start();

            Axios
                .post("/get_tipo_gasto", data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {
                        if (response.data.data.yes_gasolina == "Y") {
                            this.yes_gasolina = true;
                        } else {
                            this.yes_gasolina = false;
                        }
                    } else {
                        this.alert_warning(response.data.message);
                    }
                    this.loading_end();
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor, el campo de estar seleccionado");
                    console.error(error);
                });
        },
        deleted_pago(index) {
            this.pagos[0].monto = parseFloat(this.pagos[0].monto) + parseFloat(this.pagos[index].monto);

            if (this.pagos[index].urlapi) {
                this.delete_array.push(this.pagos[index].urlapi);
            }
            this.pagos.splice(index, 1);
        },
        agregar_cuenta() {
            if (this.pagos.length < 2) {
                this.is_btn_pagos = false;
                this.pagos.push({
                    numero: 2,
                    cuentas_id: "MFNhZTlXUkhTSmpWNDQ5ZUc2YUhldz09",
                    monto: this.total - this.pagos[0].monto
                });
            }

        },
        keyup_cuenta(index, evento) {



            if (this.totalCuota_pagos == this.redondear(this.total)) {


                this.is_btn_pagos = false;
            } else {
                this.is_btn_pagos = true;
            }
        },
        change_cuenta(index, evento) {
            this.pagos[index].cuentas_id = evento.target.value;
        },
    },
    mounted() {
        this.diferenciadias = this.diferenciadias <= 0 ? 0 : this.diferenciadias;
        console.log("diferenciadias");
        console.log(this.diferenciadias);


    },
    created() {

        this.interespordia = this.formatear_dinero_soles(this.interespordia)

        this.calcular_total();

        this.fecha_actual = moment().format('DD/MM/YYYY');



        this.fechainicio = moment(this.cuotaanterior.fechaVencimiento).format('DD/MM/YYYY');

        this.fecha_actual_input = moment(this.fecha_actual, 'DD/MM/YYYY');
        this.fechainicio_input = moment(this.fechainicio, 'DD/MM/YYYY');

        this.rango_maximo = {
            to: new Date(this.fechainicio_input.format("Y"), this.fechainicio_input.format("M") - 1, this.fechainicio_input.format("DD")),
        }


        let init_pago = {
            numero: 1,
            cuentas_id: "MFNhZTlXUkhTSmpWNDQ5ZUc2YUhldz09",
            monto: this.redondear(this.total)
        }

        this.pagos[0] = init_pago;

    },
}  
</script>

<style>
.thead-fixed {
    position: sticky;
    top: 0;
    z-index: 999;
    background-color: white;
    /* Cambia esto al color de fondo deseado */
}
</style>