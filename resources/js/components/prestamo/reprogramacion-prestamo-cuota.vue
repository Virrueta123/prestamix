<template>
    <div class="card box p-3 ">
        <div class="flex flex-col sm:flex-row sm:items-end xl:items-start">
            <div class="xl:flex sm:mr-auto mt-2 pt-0">
                <div class="font-bold text-base  font-xl">
                    REPROGRAMACIÓN DE CUOTA - CASHTIME
                </div>
            </div>
            <div class="xl:flex sm:mr-auto mt-2 pt-0">
                <div class="font-bold text-base  font-xl"> SOLICITUD N° {{ get.code }} <span>Estado : </span> <span>{{
                    get.statusformated }}</span>
                </div>
            </div>
        </div>
    </div>


    <div v-if="get.status == 'R'" class="alert alert-danger show mb-2 mt-2" role="alert">
        <div class="flex items-center">
            <div class="font-medium text-lg" v-if="user.rol == 'recepcionista'">Esta solicitud fue rechazada por
                GERENCIA
            </div>
            <div class="font-medium text-lg" v-if="user.rol == 'gerente'">Esta solicitud fue rechazada</div>
            <div class="text-xs bg-white px-1 rounded-md text-slate-100 ml-auto">
                <Icon icon='ban' class='text-danger' />
            </div>
        </div>
        <div class="mt-3">
            MOTIVO = {{ get.descripcion_rechazo }}
        </div>
    </div>

    <div v-if="get.status == 'A'" class="alert alert-success show  mb-2 mt-2 intro-x text-white" role="alert">
        <div class="flex items-center">
            <div class="font-medium text-lg" v-if="user.rol == 'recepcionista'">Esta solicitud fue aprobada por
                GERENCIA
                <button type="button" name="" v-on:click="generar_contrato()" id=""
                    class="btn btn-info btn-lg btn-block mt-4">
                    <Icon icon='check-to-slot' class='mr-2' />
                    Generar Contrato
                </button>
            </div>
            <div class="font-medium text-lg" v-if="user.rol == 'gerente'">
                Esta solicitud fue aprobada
                <hr>
                <button type="button" name="" id="" v-on:click="generar_contrato()"
                    class="btn btn-info btn-lg btn-block mt-4">
                    <Icon icon='check-to-slot' class='mr-2' />
                    Generar Contrato
                </button>
            </div>
            <div class="text-xs bg-white px-1 rounded-md text-slate-700 ml-auto">
                <Icon icon='check' class='mr-2' />
            </div>
        </div>
    </div>



    <div class="intro-y box px-5 pt-5 mt-1">
        <div class="flex flex-col lg:flex-row border-b border-slate-200/60 dark:border-darkmode-400 pb-5 -mx-5">
            <div class="flex flex-1 px-5 items-center justify-center lg:justify-start">
                <div class="w-20 h-20 sm:w-20 sm:h-24 flex-none lg:w-32 lg:h-32 relative">
                    <img alt="Midone - HTML Admin Template" class=" " width="300"
                        src="../../../../public/dist/images/Draw/solicitud.svg">
                </div>
                <div class="ml-5">
                    <div class="w-24 sm:w-40 sm:whitespace-normal font-medium text-lg">{{
                        get.cliente.cli_nombre }} | {{ get.cliente.cli_apellido }}</div>
                    <div class="text-slate-500">{{ get.solicitud_giro }}</div>
                </div>
            </div>
            <div
                class="mt-6 lg:mt-0 flex-1 px-5 border-l border-r border-slate-200/60 dark:border-darkmode-400 border-t lg:border-t-0 pt-5 lg:pt-0">

                <div class="box p-5 rounded-md mt-5">

                    <div class="flex items-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
                        <div class="font-medium text-base truncate">Contactos</div>
                    </div>

                    <div class="flex items-center" v-for="(contacto, ct_index) in get.cliente.contactos_cliente"
                        :key="ct_index">
                        <Icon icon="phone" class="mr-2 text-primary" /> {{ contacto.ccliente_contacto }}
                        <div v-if="contacto.ccliente_descripcion" class="ml-auto">{{ contacto.ccliente_descripcion }}
                        </div>
                        <div v-else class="ml-auto">No tiene descripcion</div>
                    </div>

                    <div class="flex items-center mt-5 border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
                        <div class="font-medium text-base truncate">
                            <Icon icon="user" class="mr-2 text-primary" />Información del cliente
                        </div>
                    </div>

                    <div class="flex items-center">
                        <Icon icon="check" class="mr-2 text-primary" /> Dni:
                        <div class="ml-auto">{{ get.cliente.cli_dni }}</div>
                    </div>
                    <hr>
                    <div class="flex items-center">
                        <Icon icon="check" class="mr-2 text-primary" /> Direccion domicilio:
                        <div class="ml-auto">{{ get.cliente.cli_domicilio }}</div>
                    </div>
                    <hr>
                    <div class="flex items-center">
                        <Icon icon="check" class="mr-2 text-primary" /> Direccion de su trabajo:
                        <div class="ml-auto">{{ get.cliente.cli_direccion_trabajo }}</div>
                    </div>
                    <hr>
                    <div class="flex items-center">
                        <Icon icon="check" class="mr-2 text-primary" /> Edad:
                        <div class="ml-auto">{{ calcularEdad(get.cliente.fecha_nacimiento) }} </div>
                    </div>
                    <hr>
                    <div class="flex items-center">
                        <Icon icon="check" class="mr-2 text-primary" /> fecha de nacimiento:
                        <div class="ml-auto">{{ formatear_fecha(get.cliente.fecha_nacimiento) }} </div>
                    </div>
                    <div
                        class="flex  mt-5 items-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
                        <div class="font-medium text-base truncate">
                            <Icon icon="credit-card" class="mr-2 text-primary" />Datos del Prestamo
                        </div>
                    </div>
                    <div class="flex items-center">
                        <Icon icon="check" class="mr-2 text-primary" /> Monto solicitado:
                        <div class="ml-auto text-success">{{ formatear_dinero(get.prestamo.moto_credito) }} </div>
                    </div>
                    <div class="flex items-center">
                        <Icon icon="check" class="mr-2 text-primary" /> Cuotas:
                        <div class="ml-auto text-success">{{ formatear_dinero(get.prestamo.cuotas) }} </div>
                    </div>
                    <div class="flex items-center">
                        <Icon icon="check" class="mr-2 text-primary" /> {{
                            nomenclatura_frecuencia_pago(get.prestamo.frecuencia_pagos) }}:
                        <div class="ml-auto text-success">{{ get.prestamo.intervalo }} </div>
                    </div>
                    <div class="flex items-center">
                        <Icon icon="check" class="mr-2 text-primary" /> Interes:
                        <div class="ml-auto text-success">{{ get.prestamo.interes }}
                            <Icon icon="percent" class="mr-2 text-success" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card box p-3 mt-3">
        <div class="flex flex-col sm:flex-row sm:items-end xl:items-start">
            <div class="xl:flex sm:mr-auto mt-2 pt-0">
                <div class="font-bold text-base  font-xl"> Reprogramar prestamo </div>
            </div>
            <div class="flex mt-5 sm:mt-0">
                <button @click="calcular_cronograma()" type="button" name="" id=""
                    class="btn btn-primary btn-sm btn-block">
                    <i class="fa fa-calendar-days mr-2"></i>
                    Calcular Cronograma de la reprogramacion
                </button>
            </div>
        </div>
    </div>

    <div class="card box p-3 mt-3">
        <div class="alert alert-pending show flex items-center mb-2" role="alert">
            <Icon icon='circle-info' class='mr-2' /> El monto de credito es igual la amortizacion restante
        </div>


    </div>

    <!-- Nuevo cronograma cronograma de prestamo -->
    <div class="card box p-3 mt-3">
        <div class="grid grid-cols-12 gap-6 mt-2">
            <div class="intro-y col-span-12 lg:col-span-12">
                <div class="intro-y flex flex-col sm:flex-row items-center mt-8">
                    <h2 class="text-lg font-medium mr-auto">
                        Cronograma
                        <Icon icon="hourglass-half" />
                        <div
                            class="py-1 px-2 rounded-full text-xs bg-success text-white text-center cursor-pointer font-medium">
                            {{ frecuencia_pagos }} </div>
                    </h2>
                    <div class="w-full sm:w-auto flex mt-4 sm:mt-0" v-if="is_cronograma">
                        <button type="button" v-on:click="generarPDF()"
                            class="btn btn-outline-primary w-1/2 sm:w-auto mr-2">
                            <Icon icon="print" /> Descargar Cronograma Simulacion
                        </button>

                    </div>
                </div>

                <div class="overflow-x-auto mt-4">
                    <table class="table table-sm table-bordered table-cronograma" style="font-size: 0.8rem;">
                        <thead class="thead-fixed">
                            <tr v-if="cronograma.length != 0" class="sticky top-0 z-10 bg-white">
                                <th class="whitespace-nowrap">-</th>
                                <th class="whitespace-nowrap">Fecha Desebolso</th>
                                <th class="whitespace-nowrap">Monto de credito</th>
                                <th class="whitespace-nowrap">-</th>
                                <th class="whitespace-nowrap">-</th>
                                <th class="whitespace-nowrap">-</th>
                            </tr>
                            <tr v-if="cronograma.length != 0">
                                <td> - </td>
                                <td> {{ formatear_fecha_normal(fecha_desembolso) }} </td>
                                <td> S/. {{ numeralFormat(monto_credito, '0,0.00') }} </td>
                                <td> - </td>
                                <td> - </td>
                                <td> - </td>
                            </tr>
                            <tr class="sticky top-0 z-10 bg-white">
                                <th class="whitespace-nowrap">Periodo</th>
                                <th class="whitespace-nowrap">Fecha vencimiento</th>
                                <th class="whitespace-nowrap">Saldo capital</th>
                                <th class="whitespace-nowrap">Amortizacion</th>
                                <th class="whitespace-nowrap">Interes</th>
                                <th class="whitespace-nowrap">Cuota</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(c_g, c_g_index) in cronograma" :key="c_g_index" :class="{
                                cuota_reprogramada_seleccionada: c_g.periodo === cuota_actual.periodo,
                                cuota_adicional_reprogramada: c_g.is_reprogrado
                            }">
                                <td>{{ c_g.periodo }}</td>
                                <td>{{ c_g.fechaVencimiento }}</td>
                                <td>S/. {{ numeralFormat(c_g.saldoCapital, '0,0.00') }}</td>
                                <td>S/. {{ numeralFormat(c_g.amortizacion, '0,0.00') }}</td>
                                <td>S/. {{ numeralFormat(c_g.interes, '0,0.00') }}</td>
                                <td class="bg-blue-200">S/. {{ numeralFormat(c_g.cuota, '0,0.00') }}
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>S/. {{ numeralFormat(monto_credito, '0,0.00') }}</td>
                                <td>S/. {{ numeralFormat(sumar_interes, '0,0.00') }}</td>
                                <td>S/. {{ numeralFormat(sumar_cuota, '0,0.00') }}</td>
                            </tr>
                            <tr>
                                <th colspan="8" class="text-center" v-if="!is_reprogramacion"> No se prodra realizar la
                                    reprogramacion de la cuota
                                    seleccionada
                                    primero es necesario
                                    pagar el interes y la mora si en el caso lo tubiera </th>
                                <th colspan="8" class="justify-center" v-else> <button type="button"
                                        v-on:click="reprogramar_cuota()" class="btn btn-primary btn-sm">Reprogramar
                                        cuota</button> </th>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- cronograma de prestamo -->

    <div class="card box p-3 mt-3">
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
                        <tr>
                            <th class="whitespace-nowrap"> </th>
                            <th class="whitespace-nowrap">{{ formatear_fecha(get.prestamo.updated_at) }}</th>
                            <th class="whitespace-nowrap">{{ formatear_dinero_soles(get.prestamo.moto_credito) }}</th>
                            <th class="whitespace-nowrap"> </th>
                            <th class="whitespace-nowrap"> </th>
                            <th class="whitespace-nowrap"> </th>
                            <th class="whitespace-nowrap"> </th>
                            <th class="whitespace-nowrap"> </th>
                        </tr>

                    </thead>

                </table>
            </div>
        </div>
    </div>





    <OverlayPanel ref="op">
        <div class="flex flex-column gap-3 w-25rem">
            <div>
                <div class="grid grid-cols-12 gap-6 mt-2">
                    <div class="intro-y col-span-12 lg:col-span-3">
                        <label for="vertical-form-2" class="form-label">Monto de credito</label>
                        <div class="input-group">
                            <div id="input-group-email" class="input-group-text">S/.</div>
                            <input class="form-control pl-2" placeholder="Monto del credito"
                                v-model="get.prestamo.moto_credito" name="monto_credito" data-type="monto_credito" />
                        </div>
                    </div>

                    <div class="intro-y col-span-12 lg:col-span-3">
                        <label for="vertical-form-2" class="form-label"> Intereses </label>

                        <div class="input-group">
                            <div id="input-group-email" class="input-group-text">%</div>
                            <input name="interes" v-model="get.prestamo.interes" type="number" class="form-control"
                                placeholder="0">
                        </div>
                    </div>

                    <div class="intro-y col-span-12 lg:col-span-6">
                        <label for="vertical-form-2" class="form-label"> Destino </label>
                        <input name="destino" v-model="get.prestamo.destino" type="text" class="form-control"
                            placeholder="Destino">
                    </div>

                </div>

                <div class="grid grid-cols-12 gap-2 mt-2">
                    <div class=" col-span-12 lg:col-span-6">
                        <label>Frecuencia de los pagos </label>
                        <div class="flex flex-col sm:flex-row mt-4">
                            <div class="form-check mr-2 sm:mt-0">
                                <input id="Diario" class="form-check-input" type="radio" name="frecuencia_pagos" checked
                                    v-model="get.prestamo.frecuencia_pagos" value="Diario">
                                <label class="form-check-label" for="Diario">Diario</label>
                            </div>
                            <div class="form-check mr-2 mt-2 sm:mt-0">
                                <input id="Semanal" v-model="get.prestamo.frecuencia_pagos" class="form-check-input"
                                    type="radio" name="frecuencia_pagos" value="Semanal">
                                <label class="form-check-label" for="Semanal">Semanal</label>
                            </div>
                            <div class="form-check mr-2 mt-2 sm:mt-0">
                                <input id="Mensual" v-model="get.prestamo.frecuencia_pagos" class="form-check-input"
                                    type="radio" name="frecuencia_pagos" value="Mensual">
                                <label class="form-check-label" for="Mensual">Mensual</label>
                            </div>
                            <div class="form-check mr-2 mt-2 sm:mt-0">
                                <input id="Anual" v-model="get.prestamo.frecuencia_pagos" class="form-check-input"
                                    type="radio" name="frecuencia_pagos" value="Anual">
                                <label class="form-check-label" for="Anual">Anual</label>
                            </div>
                            <div class="flex items-center">
                                <Icon icon="check" class="mr-2 text-primary" /> D.T:
                                <div class="ml-auto text-success">{{ get.prestamo.d_t }} </div>
                            </div>
                            <div class="flex items-center">
                                <Icon icon="check" class="mr-2 text-primary" /> Interes:
                                <div class="ml-auto text-success">{{ get.prestamo.interes }}
                                    <Icon icon="percent" class="mr-2 text-success" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="intro-y col-span-12 mr-4 lg:col-span-6">

                        <label for="vertical-form-2" class="form-label"> {{ frecuencia_pagos_a }} </label>
                        <input name="intervalo" v-model="get.prestamo.intervalo" type="number" class="form-control">

                    </div>
                </div>

            </div>

        </div>
    </OverlayPanel>


    <!-- modal para rechazar la solicitud -->
    <Dialog v-model:visible="is_modal_rechazo" maximizable modal header="Formulario para rechazar una solicitud"
        :style="{ width: '50rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
        <form id="form_rechazar_solicitud" method="POST" action="#">
            <div id="vertical-form" class="p-5">
                <div class="">
                    <div class="grid grid-cols-12 gap-12 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-12">
                            <label for="vertical-form-2" class="form-label">Escribar el motivo de por que rechaza esta
                                solicitud </label>
                            <input name="descripcion_solicitud" v-model="descripcion_solicitud" type="text"
                                class="form-control" placeholder="Motivo de rechazo">
                        </div>
                    </div>

                </div>
                <div id="basic-button" class="p-1 mt-4">
                    <div class="preview">
                        <button type="button" class="btn btn-primary mr-1 mb-2" @click="send_rechazo_solicitud()">
                            <Icon icon="plus" /> Rechazar Solicitud
                        </button>
                        <button type="button" class="btn btn-danger mr-1 mb-2" @click="is_modal_add_cliente = false">
                            <Icon icon="ban" /> Cancelar operacion
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </Dialog>

    <!-- Imprimir solicitud y contrato -->
    <Dialog v-model:visible="is_modal_imprimir" maximizable modal header="Imprimir solicitud y contrato"
        :style="{ width: '90rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
        <iframe style="width: 100%;" :src="'/solicitdu/' + get.urlapi + '/imprimir'" width="500" height="800"></iframe>
    </Dialog>


    <Dialog v-model:visible="is_modal_guardar_documento" maximizable modal header="Guardar documentos"
        :style="{ width: '80rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
        <guardar_archivos :solicitud="get"></guardar_archivos>
    </Dialog>

    <button v-if="get.status != 'G'" type="button" class="btn  btn-primary btn-flotante" label="Image" @click="toggle">
        <Icon icon='edit' class='mr-2' /> Editar Cronograma
    </button>


    <button v-if="is_boton" @click="crear_reporgramacion()" type="submit"
        class="btn btn-primary btn-flotante btn-block btn-flotante btn-xs ">Procesar
        Reprogramacion</button>

</template>

<script>
import $ from 'jquery';
import { createApp, h } from 'vue';
import { TabulatorFull as Tabulator } from 'tabulator-tables';
import 'tom-select/dist/css/tom-select.css';
import TomSelect from 'tom-select';
import Axios from 'axios';

import "jquery-validation";
import "jquery-validation/dist/localization/messages_es"

import moment from 'moment';
import 'moment/locale/es';

// plugins para la creacion de pdf de una manera elegante
import {
    Canvg
} from 'canvg';
import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';
import 'jspdf-autotable';
import * as svg2pdf from 'svg2pdf.js';


// mixin
import {
    myMixin
} from "../../mixin.js";


import { renderToString } from '@vue/server-renderer';

//svg   
import documentInf from '../solicitudes/svg/documento-inf.vue';


import { ref } from "vue";
import { get } from 'jquery';

export default {
    mixins: [myMixin],
    data() {
        return {
            is_reprogramacion: false,
            cuota_actual: null,
            is_modal_rechazo: false,
            is_modal_imprimir: false,
            is_modal_guardar_documento: false,
            is_boton: false,
            get: this.$attrs.get_solicitud,
            monto_credito_nuevo: this.$attrs.monto_credito_nuevo,
            fecha_nacimiento: "",
            edad: "",
            cronograma_headers: null,
            //
            // variables de rechazo de solicitud
            descripcion_solicitud: "",
            // variables para la solicitud   
            cronograma: [],
            cuotas: null,
            fecha_desembolso: null,
            loading: false,
            monto_credito: 1500.00,
            tasa_diaria: 0,
            cuotas: 0,
            cuota_final: 0,
            fecha_desembolso: null,
            frecuencia_pagos: "Diario",
            frecuencia_pagos_a: "Dias",
            interes: 10,
            intervalo: 30,
            is_cronograma: false,
            tabla_cronograma: null,
            get_ingresos: []
        }
    },
    computed: {
        sumar_interes() {
            if (this.cronograma != 0) {
                const importe = this.cronograma.reduce((acumulador, res) => {
                    return acumulador + parseFloat(res.interes);
                }, 0);
                return this.redondear(importe);
            } else {
                return 0;
            }

        },
        sumar_cuota() {
            if (this.cronograma != 0) {
                const importe = this.cronograma.reduce((acumulador, res) => {
                    return acumulador + parseFloat(res.cuota);
                }, 0);
                return this.redondear(importe);

            } else {
                return 0;
            }
        }
    },
    watch: {
        'moto_credito': function (newValue) {
            this.is_boton = false;
        },
        'interes': function (newValue) {
            this.is_boton = false;
        },
        'intervalo': function (newValue) {
            this.is_boton = false;
        },
        'frecuencia_pagos': function (newValue) {
            this.is_boton = false;
            switch (newValue) {

                case "Diario":
                    this.frecuencia_pagos_a = "Dias";
                    break;

                case "Semanal":
                    this.frecuencia_pagos_a = "Semanas";
                    break;

                case "Mensual":
                    this.frecuencia_pagos_a = "Menses";
                    break;

                case "Anual":

                    break;
            }
        },
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
        // crear la reprogramacion
        crear_reporgramacion() {
            const data = {
                urlapi: this.get.urlapi,
                cronograma: this.cronograma,
                monto_credito: this.monto_credito,
                tasa_diaria: this.tasa_diaria,
                cuotas: this.cuotas,
                cuota_final: this.cuota_final,
                fecha_desembolso: this.fecha_desembolso,
                frecuencia_pagos: this.frecuencia_pagos,
                interes: this.interes,
                intervalo: this.intervalo,
                d_t: this.sumar_cuota
            };

            const headers = this.headers;

            this.loading_start();

            Axios
                .post("/crear_reporgramacion", data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {
                        this.alert_success(response.data.message);
                        window.location.href = response.data.data;
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
        generar_contrato() {
            const data = {
                solicitud: this.get.urlapi,
                cronograma: this.cronograma,
                cuotas: this.cuotas
            };

            const headers = this.headers;

            this.loading_start();

            Axios
                .put("/send_procesar_solicitud", data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {
                        window.location.reload();
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

        toggle(event) {
            console.log(this.op);
            this.$refs.op.toggle(event);
        },
        // rechazar solicitud via axios
        send_rechazo_solicitud() {

            if (this.descripcion_solicitud == "") {
                this.alert_warning("Ingrese una descripción a la solicitud");
                return;
            }

            const data = {
                solicitud: this.get.urlapi,
                descripcion_solicitud: this.descripcion_solicitud
            };

            const headers = this.headers;

            this.loading_start();

            Axios
                .put("/send_rechazo_solicitud", data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {

                        window.location.reload();
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

        send_solicitud_aprobada() {
            console.log("dsada");
            this.$swal.fire({
                imageUrl: "../../../../dist/images/Draw/questions.svg",
                imageHeight: 150,
                imageAlt: "--",
                title: "Estas seguro que deseas aprobar esta solicitud?",
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: "Si estoy segur@",
                denyButtonText: `Salir`
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    const data = {
                        solicitud: this.get.urlapi
                    };

                    const headers = this.headers;

                    this.loading_start();

                    Axios
                        .put("/send_solicitud_aprobada", data, {
                            headers,
                        })
                        .then((response) => {
                            console.log(response.data);
                            if (response.data.success) {
                                window.location.reload();
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
                }
            });
        },
        calcular_cronograma() {
            var self = this;

            switch (this.frecuencia_pagos) {

                case "Diario":

                    this.cronograma = this.calcularAmortizacionFrancesdiario(
                        this.monto_credito,
                        parseInt(this.intervalo),
                        parseInt(this.interes),
                        this.fecha_desembolso
                    );

                    this.frecuencia_pagos_a = "Dias";

                    break;

                case "Semanal":

                    this.cronograma = this.calcularAmortizacionFrancesSemanal(
                        this.monto_credito,
                        parseInt(this.intervalo),
                        parseInt(this.interes),
                        this.fecha_desembolso
                    );

                    this.frecuencia_pagos_a = "Semanas";
                    break;

                case "Mensual":

                    this.cronograma = this.calcularAmortizacionFrancesMensual(
                        this.monto_credito,
                        parseInt(this.intervalo),
                        parseInt(this.interes),
                        this.fecha_desembolso
                    );

                    this.frecuencia_pagos_a = "Mensuales";
                    break;

                case "Anual":

                    break;
            }
            this.is_cronograma = true;
            this.is_boton = true;
            this.fecha_desembolso = moment().format("YYYY-MM-DD");

        },
        tabla_cronograma_data() {
            var self = this;
            this.tabla_cronograma = $('#table_cronograma').DataTable({
                paging: false,
                searching: false,
                processing: true,
                "infoFiltered": "",
                "processing": "<img src='~/Content/images/loadingNew.gif' />",
                dom: 'Bfrtip',
                autoWidth: true,
                responsive: true,
                data: self.get.prestamo.cronograma,
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
                    title: 'tabla_cliente_fecha_'
                },
                {
                    text: '<i class="fa fa-money-bill-trend-up"></i> Refinanciar', // Texto del botón extra
                    action: function (e, dt, node, config) {

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
                                    return `<div class="alert fila-roja  p-0 text-center text-white show "> ${self.formatear_fecha(data)} </div>`;
                                } else if (fechaDada.isSame(fechaActual, 'day')) {
                                    return `<div class="alert  fila-amarilla  p-0 text-center text-white show "> ${self.formatear_fecha(data)} </div>`;

                                } else {
                                    return `<div class="alert fila-pendiente  p-0 text-center text-white show "> ${self.formatear_fecha(data)} </div>`;

                                }

                            } else {
                                return `<div class="alert fila-pagada  p-0 text-center text-white show "> ${self.formatear_fecha(data)} </div>`;

                            }
                        }
                    },
                    {
                        "data": "saldoCapital"
                        ,
                        render: function (data, type, row) {


                            return `<div "> ${self.formatear_dinero_soles(data)} </div>`;

                        }
                    },
                    {
                        "data": "amortizacion",
                        render: function (data, type, row) {

                            return `<div "> ${self.formatear_dinero_soles(data)} </div>`;

                        }
                    },
                    {
                        "data": "interes",
                        render: function (data, type, row) {

                            if (row.yes_interes == "N") {
                                row.cuota = row.amortizacion;
                                return `<div "> 0.00 </div>`;
                            } else {
                                return `<div "> ${self.formatear_dinero_soles(data)} </div>`;
                            }

                        }
                    },
                    {
                        "data": "cuota",
                        render: function (data, type, row) {
                            if (row.yes_interes == "Y") {
                                return ` ${self.formatear_dinero_soles(data)}`;
                            } else {
                                return ` ${self.formatear_dinero_soles(row.amortizacion)}`;
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
                                            return `<div "> ${self.formatear_dinero_soles(row.interes)} </div>`;

                                        } else if (fechaDada.isSame(fechaActual, 'day')) {
                                            return `<div "> NO </div>`;
                                        } else {
                                            return `<div "> NO </div>`;
                                        }
                                        break;

                                    case "Y":
                                        return `<div "> ${self.formatear_dinero_soles(row.monto_mora)} </div>`;
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

                                        return `<div "> Pendiente hoy </div>`;
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
                                        return `<div ">  Pendiente </div>`;
                                    }
                                    break;
                                case "R":
                                    if (fechaDada.isBefore(fechaActual, 'day')) {
                                        return `<div ">  Vencido </div>`;
                                    } else if (fechaDada.isSame(fechaActual, 'day')) {

                                        return `<div "> Pendiente / Hoy </div>`;
                                    } else {
                                        return `<div ">  Pendiente </div>`;
                                    }
                                    break;
                            }
                        }
                    }
                ],
            })
        },
        async generar_documentos() {
            const doc = new jsPDF();
            var Params = {
                // variables del prestamo
                monto_credito: 1000,
            }
            const componente = h(documentInf,
                Params
            );
            console.log(Params);
            var html = await renderToString(componente);

            const svg = html;


            doc.addSvgAsImage(html, 'SVG', 10, 10, 20, 0);

            // Renderizar el SVG en el canvas
            await v.render();
            // Agregar la imagen de datos al documento PDF
            doc.addImage(canvas.toDataURL('image/png', 1), 'PNG', 5, 5, 655, 150);
            doc.autoPrint({ variant: 'non-conform' });

            // Guardar el documento PDF
            doc.save('Cronograma-.pdf');


        },
        load_ingresos() {
            const data = {
                urlapi: this.get.urlapi
            }

            const headers = this.headers;

            this.loading_start();

            return Axios
                .post("/load_ingresos_prestamo", data, {
                    headers,
                })
                .then((response) => {
                    console.error(response);
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
        //  comprobar si el prestamo tiene cuotas vencidas, comprobar sin son mas de uno, comprobar si pago el interes de la cuota
        comprobar_pago_cuotas() {
            const data = {
                urlapi: this.get.urlapi
            };

            const headers = this.headers;

            this.loading_start();

            return Axios
                .post("/comprobar_pago_cuotas_reprogramacion", data, {
                    headers,
                })
                .then((response) => {
                    if (response.data.success) {

                        this.cronograma = response.data.data.cuotas;
                        this.cuota_actual = response.data.data.cuota_actual;
                        this.verficar_interes(response.data.data.ingresos_cuota_actual)
                        this.alert_success(response.data.message);
                        this.loading_end();

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
        verficar_interes(ingresos_cuota_actual) {


            const interes = this.get.prestamo.cuota_actual.interes;
 
                this.is_reprogramacion = true;

                let index_cambiado = 0;

                // Paso 1: Encontrar e insertar la nueva cuota
                this.cronograma.forEach((c_g, index) => {
                    if (c_g.periodo == this.cuota_actual.periodo) { 
                        const nueva_cuota = {
                            periodo: c_g.periodo + 1,
                            fechaVencimiento: moment(c_g.fechaVencimiento).add(1, 'months').format('YYYY-MM-DD'),
                            saldoCapital: c_g.saldoCapital,
                            amortizacion: c_g.amortizacion,
                            interes: interes,
                            cuota: c_g.cuota,
                            is_reprogrado: true
                        };

                        this.cronograma.splice(index + 1, 0, nueva_cuota);
                        index_cambiado = index + 1;
                    }
                });

                // Paso 2: Actualizar los períodos y fechas de las cuotas posteriores
                // IMPORTANTE: Empezar desde index_cambiado + 1 para evitar modificar la cuota recién insertada
                for (let i = index_cambiado + 1; i < this.cronograma.length; i++) {
                    // Calcular el desplazamiento desde la cuota actual
                    const desplazamiento = i - index_cambiado;

                    // Incrementar el período basado en la cuota actual + desplazamiento
                    this.cronograma[i].periodo = this.cuota_actual.periodo + 1 + desplazamiento;

                    // Actualizar la fecha de vencimiento basándose en la fecha de la cuota actual
                    this.cronograma[i].fechaVencimiento = moment(this.cuota_actual.fechaVencimiento)
                        .add(1 + desplazamiento, 'months')
                        .format('YYYY-MM-DD');
                }
           


        },
        reprogramar_cuota() {
            const data = {
                urlapi: this.get.urlapi
            };
            const headers = this.headers;

            // antes de proceder a reprogramar se necesita preguntar si se quiere reprogramar la cuota con sweet alert

            this.$swal({
                title: "¿Estás seguro?",
                text: "Esta acción no se puede deshacer, ¿Estás seguro?",
                icon: "warning",
                showCancelButton: true,  // Muestra botón "Cancelar"
                confirmButtonText: "Aceptar", // Texto del botón de confirmación
                cancelButtonText: "Cancelar", // Texto del botón de cancelar
                dangerMode: true,
            }).then((result) => {
                if (result.isConfirmed) { // Solo ejecuta si se confirma
                    this.loading_start();
                    Axios.post("/comprobar_pago_cuotas_reprogramacion_store", data, { headers })
                        .then((response) => {
                            console.log(response.data);
                            if (response.data.success) {
                                this.alert_success(response.data.message);
                                window.location.href = response.data.data;
                            } else {
                                this.alert_warning(response.data.message);
                            }
                            this.loading_end();
                        })
                        .catch((error) => {
                            this.loading_end();
                            this.alert_error_modal("Error en el servidor, intentelo de nuevo");
                            console.error(error);
                        });
                }
            });
        }
    },
    created() {

    },
    mounted() {

        this.fecha_desembolso = this.get.prestamo.cronograma[this.get.prestamo.cronograma.length - 1].fechaVencimiento;

        if (this.get.prestamo.frecuencia_pagos == "Mensual") {
            const cronograma = this.get.prestamo.cronograma;

            // Filtrar los elementos donde yes_pago es "N"
            const filtrado = cronograma.filter(item => item.yes_pago === "N");

            // Ordenar el array por el campo 'periodo' en orden ascendente
            const ordenado = filtrado.sort((a, b) => a.periodo - b.periodo);

            // Obtener el primer elemento del array ordenado
            const primerElemento = ordenado.length > 0 ? ordenado[0] : null;

            // Obtener la fecha de vencimiento del primer elemento
            this.fecha_desembolso = primerElemento ? primerElemento.fechaVencimiento : null;
        }

        this.monto_credito = this.monto_credito_nuevo;
        this.interes = this.get.prestamo.interes;
        this.intervalo = this.get.prestamo.intervalo;
        this.frecuencia_pagos = this.get.prestamo.frecuencia_pagos;

        switch (this.frecuencia_pagos) {

            case "Diario":
                this.frecuencia_pagos_a = "Dias";
                break;

            case "Semanal":
                this.frecuencia_pagos_a = "Semanas";
                break;

            case "Mensual":
                this.frecuencia_pagos_a = "Menses";
                break;

            case "Anual":

                break;
        }

        this.tabla_cronograma_data()
        this.comprobar_pago_cuotas();
        this.load_ingresos().then((result) => {
            this.get_ingresos = result;
        }).catch((err) => {
            console.log(err);
        });
    }
}
</script>

<style>
.cuota_reprogramada_seleccionada {
    background: #f8472b;
    color: white;
}

.cuota_adicional_reprogramada {
    background: #068454;
    color: white;
}
</style>