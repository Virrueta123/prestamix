<template>
    <div class=" box">
        <Toast />

        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
            <h2 class="font-medium text-base mr-auto">
                Simulacion de prestamo
            </h2>

        </div>
        <form id="add_solicitud" action="#" method="POST">
            <div id="input" class="p-5">

                <div>


                    <div class=" flex flex-col sm:flex-row items-center mt-6">
                        <h2 class="text-lg font-medium mr-auto">
                            <Icon icon="sack-dollar" /> Datos del prestamo
                        </h2>
                        <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
                            <button v-on:click="calcular_cronograma()" type="button"
                                class="btn btn-outline-primary w-1/2 sm:w-auto mr-2">
                                <Icon icon="sack-dollar" /> Calcular Cronograma
                            </button>

                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-3">
                            <label for="vertical-form-2" class="form-label">Monto de credito</label>
                            <div class="input-group">
                                <div id="input-group-email" class="input-group-text">S/.</div>
                                <InputNumber class="form-control p-2 border border-success" v-model="monto_credito"
                                    name="monto_credito" placeholder="Monto del credito" inputId="locale-us"
                                    locale="en-US" :minFractionDigits="2" />
                            </div>
                        </div>

                        <div class="intro-y col-span-12 lg:col-span-3">
                            <label for="vertical-form-2" class="form-label"> Intereses </label>
                            <div class="input-group">
                                <div id="input-group-email" class="input-group-text">%</div>
                                <input name="interes" v-model="interes" type="number" class="form-control"
                                    placeholder="0">
                            </div>
                        </div>

                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label"> Fecha de desembolso </label>
                            <div class="input-group">
                                <div id="input-group-email" class="input-group-text">
                                    <Icon icon="calendar" />
                                </div>
                                <input name="interes" v-model="fecha" type="date" class="form-control" placeholder="0">
                            </div>
                        </div>


                    </div>

                    <div class="grid grid-cols-12 gap-2 mt-2">
                        <div class=" col-span-12 lg:col-span-6">
                            <label>Frecuencia de los pagos </label>
                            <div class="flex flex-col sm:flex-row mt-4">
                                <div class="form-check sm:mt-0">
                                    <input id="Quincenal" class="form-check-input" type="radio" name="frecuencia_pagos"
                                        checked v-model="frecuencia_pagos" value="Quincenal">
                                    <label class="form-check-label" for="Quincenal">Quincenal</label>
                                </div>
                                <div class="form-check mr-2 mt-2 sm:mt-0">
                                    <input id="Diario" class="form-check-input" type="radio" name="frecuencia_pagos"
                                        v-model="frecuencia_pagos" value="Diario">
                                    <label class="form-check-label" for="Diario">Diario</label>
                                </div>
                                <div class="form-check mr-2 mt-2 sm:mt-0">
                                    <input id="Semanal" v-model="frecuencia_pagos" class="form-check-input" type="radio"
                                        name="frecuencia_pagos" value="Semanal">
                                    <label class="form-check-label" for="Semanal">Semanal</label>
                                </div>
                                <div class="form-check mr-2 mt-2 sm:mt-0">
                                    <input id="Mensual" v-model="frecuencia_pagos" class="form-check-input" type="radio"
                                        name="frecuencia_pagos" value="Mensual">
                                    <label class="form-check-label" for="Mensual">Mensual</label>
                                </div>
                                <div class="form-check mr-2 mt-2 sm:mt-0">
                                    <input id="Anual" v-model="frecuencia_pagos" class="form-check-input" type="radio"
                                        name="frecuencia_pagos" value="Anual">
                                    <label class="form-check-label" for="Anual">Anual</label>
                                </div>
                            </div>
                        </div>
                        <div class="intro-y col-span-12 mr-4 lg:col-span-6">

                            <label for="vertical-form-2" class="form-label"> {{ frecuencia_pagos_a }} </label>
                            <input name="intervalo" v-model="intervalo" type="number" class="form-control"
                                :placeholder="'2 ' + frecuencia_pagos_a">

                        </div>
                    </div>

                    <div v-if="frecuencia_pagos == 'Mensual'">
                        <div class="grid grid-cols-12 gap-12 mt-4">
                            <div class=" col-span-12 lg:col-span-6">
                                <label for="vertical-form-2" class="form-label" id="switch1">Este pago es en
                                    <strong>Deseas cambiar la fecha de las cuotas del prestamo?</strong>
                                    <br>
                                    <span class="text-danger">
                                        <Icon icon="info-circle" /> Ojo la fecha como maximo son 20 dias de la fecha de desembolso
                                    </span>
                                </label>
                                <div class="form-check form-switch">
                                    <label class="form-check-label mr-2" for="checkbox-switch-7">No </label>
                                    <input id="checkbox-switch-7" v-model="is_fecha_pago" checked
                                        class="form-check-input" type="checkbox">
                                    <label class="form-check-label ml-2" for="checkbox-switch-7">Si </label>
                                </div>
                            </div>
                        </div>

                        <div class=" col-span-12 lg:col-span-12 mt-3" v-if="is_fecha_pago">
                            <label for="vertical-form-2" class="form-label"> Fecha de donde empezara las cuotas </label>
                            <datepicker class="form-control col-span-12" v-on:change="change_fecha_de_pago_cuota()"
                                v-model="fecha_de_pago_cuota" placeholder="hacer click para seleccionar"
                                :styles="{ border: '2px solid #00ff00' }" :disabled-dates="rango_maximo" language="es">
                            </datepicker>
                        </div>
                    </div>



                    <div class="report-box-1 intro-y mt-2" v-if="is_cronograma">
                        <div class="box sm:flex">
                            <div class="px-2 py-2 flex flex-col justify-center flex-1">

                                <div class="intro-y flex-1 px-2 py-2">
                                    <center> <img class="card-img-top" width="38%"
                                            src="../../../../public/dist/images/Draw/savings.svg" alt="">
                                    </center>
                                    <div class="text-xl font-medium text-center mt-10">Monto credito</div>

                                    <div class="flex justify-center">
                                        <div class="relative text-5xl font-semibold mt-8 mx-auto text-success">
                                            <span class="absolute text-2xl top-0 left-0 -ml-5">S/</span> {{
                                                numeralFormat(monto_credito, '0,0.00') }}
                                        </div>
                                    </div>
                                    <!-- <button type="button" class="btn btn-rounded-primary py-3 px-4 block mx-auto mt-8">PURCHASE NOW</button> -->
                                </div>
                            </div>
                            <div
                                class="px-8 py-12 flex flex-col justify-center flex-1 border-t sm:border-t-0 sm:border-l border-slate-200 dark:border-darkmode-300 border-dashed">
                                <div class="text-slate-500 text-xs">Plazo en {{ frecuencia_pagos_a }}</div>
                                <div class="mt-1.5 flex items-center">
                                    <div class="text-base">{{ intervalo }} {{ frecuencia_pagos_a }}</div>

                                </div>
                                <div class="text-slate-500 text-xs mt-5">TASA MENSUAL</div>
                                <div class="mt-1.5 flex items-center">
                                    <div class="text-base">{{ interes }} %</div>

                                </div>
                                <div class="text-slate-500 text-xs mt-5">TASA DIARIA</div>
                                <div class="mt-1.5 flex items-center">
                                    <div class="text-base"> {{ tasa_diaria }} %</div>

                                </div>
                                <div class="text-slate-500 text-xs mt-5">CUOTA</div>
                                <div class="mt-1.5 flex items-center">
                                    <div class="text-base">S/. {{ cuotas }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

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
                                <table class="table table-bordered ">
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
                                            <td> {{ fecha_desembolso }} </td>
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


                                        <tr v-for="(c_g, c_g_index) in cronograma" :key="c_g_index">
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
                                        <tr v-if="cronograma.length == 0">
                                            <td colspan="6">

                                                <div class="card ">
                                                    <center> <img class="card-img-top" width="20%"
                                                            src="../../../../public/dist/images/Draw/schedule.svg"
                                                            alt="">
                                                    </center>

                                                    <div class="card-body">
                                                        <h4 class="card-title text-center">Rellene los datos del
                                                            prestamo
                                                            para
                                                            generar un cronograma de pago</h4>

                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>



            </div>

        </form>


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

// plugins para la creacion de pdf de una manera elegante
import {
    Canvg
} from 'canvg';
import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';
import 'jspdf-autotable';

import { CurrencyInput } from 'vue-currency-input'


// mixin
import {
    myMixin
} from "../../mixin.js";

import { renderToString } from '@vue/server-renderer';

//svg   
import cabezeraVueSimulacion from '../solicitudes/svg/cabezera_simulacion.vue';

export default {
    mixins: [myMixin],
    components: {
        cabezeraVueSimulacion,
        renderToString,
    },
    data() {
        return {
            headers: {
                "Content-Type": "application/json",
            },
            select_cliente: null,
            // variables modal
            is_modal_add_cliente: false,
            // variable crear cliente
            cli_dni: "",
            cli_nombre: "",
            cli_apellido: "",
            cli_celular: "",
            fecha_nacimiento: null,
            cli_domicilio: "",
            cli_direccion_trabajo: "",
            cli_sexo: "M",
            tipo_cliente: "particular",
            contactos_cliente: [],
            data_select_cliente: [],
            get_cliente: null,
            // variables para la solicitud
            is_solicitud: false,
            tipo_solicitud: "Nuevo",
            tipo_vivienda: "Propia",
            analista_id: null,
            destino: "",
            solicitud_nombre: "",
            solicitud_documento: "",
            estado_ruc: "",
            solicitud_giro: "",
            solicitud_antiguedad: "",
            solicitud_direccion_negocio: "",
            solicitud_lugar: "",
            solicitud_referencia_negocio: "",
            solicitud_referencia_cliente: "",
            solicitud_domicilio: "",
            solicitud_nombre_conyugue: "",
            solicitud_conyugue_dni: "",
            solicitud_conyugue_ruc: "",
            monto_credito: 1000,
            tasa_diaria: 0,
            cuotas: 0,
            cuota_final: 0,
            fecha_desembolso: null,
            frecuencia_pagos: "Quincenal",
            frecuencia_pagos_a: "Quincenas",
            interes: 15,
            intervalo: 3,
            is_cronograma: false,
            cronograma: [],

            fecha: null,
            // para la creacion de una cuota
            is_fecha_pago: false,
            fecha_de_pago_cuota: null,
            rango_maximo: ""
        }
    },

    watch: {
        monto_credito_nueva: function (newValue) {
            var monto = newValue + this.monto_credito_amortizacion;
            this.monto_credito = monto;

            return newValue;
        },
        'fecha': function (newValue) {
            this.fecha_desembolso = this.fecha; 
            var fecha_actual = moment(this.fecha_desembolso);
            var fechas_mas_veinte = moment(this.fecha_desembolso).add(20, 'days');

            this.rango_maximo = {
                to: new Date(fecha_actual.format("Y"), fecha_actual.format("M") - 1, fecha_actual.format("DD")),
                from: new Date(fechas_mas_veinte.format("Y"), fechas_mas_veinte.format("M") - 1, fechas_mas_veinte.format("DD"))
            }
        },
        'is_fecha_pago': function (newValue) {
            this.is_boton = false;
            if (newValue == true) {
                if (this.is_reprogramacion == true) {
                    this.is_reprogramacion = false;
                }
            } else {
                if (this.is_reprogramacion == false) {
                    this.is_reprogramacion = true;
                }
            }
        },
        'fecha_de_pago_cuota': function (newValue) {
            this.is_boton = false;
      
            this.alert_warning("nueva fecha de pago");
            if (this.fecha_de_pago_cuota == null) {
                if (this.is_reprogramacion == true) {
                    this.is_reprogramacion = false;
                }
            } else {
                if (this.is_reprogramacion == false) {
                    this.is_reprogramacion = true;
                }
            }
        },
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
                case "Quincenal":
                    this.frecuencia_pagos_a = "Quincenas";
                    break;
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
    computed: {
        string_contactos() {

            var contactosUnidos = this.contactos_cliente.reduce((acc, contacto) => {
                const contactoFormateado = contacto.map(c => `${c.contacto}`).join(' / ');
                return `${acc} / ${contactoFormateado}`;
            }, '');
            return contactosUnidos;

        },
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
    methods: {

        //calcular cronograma del prestamo
        calcular_cronograma() {
           
            switch (this.frecuencia_pagos) {

                case "Diario":
                    this.cronograma = this.calcularAmortizacionFrancesdiarioByDate(
                        this.monto_credito,
                        parseInt(this.intervalo),
                        parseInt(this.interes),
                        this.fecha
                    );
                    break;

                case "Semanal":
                    this.cronograma = this.calcularAmortizacionFrancesSemanalByDate(
                        this.monto_credito,
                        parseInt(this.intervalo),
                        parseInt(this.interes),
                        this.fecha
                    )
                    break;

                case "Quincenal":
                    this.cronograma = this.calcularAmortizacionFrancesQuincenal(
                        this.monto_credito,
                        parseInt(this.intervalo),
                        parseInt(this.interes),
                        this.fecha
                    );
                    break;

                case "Mensual":

                    if (this.is_fecha_pago) {
                        this.alert_warning(this.fecha_de_pago_cuota);


                        if (moment(this.fecha_desembolso).isSame(this.fecha_actual(), 'day')) {
                            this.cronograma = this.calcularAmortizacionFrancesMensual(
                                this.monto_credito,
                                parseInt(this.intervalo),
                                parseInt(this.interes),
                                this.fecha_de_pago_cuota
                            );
                        } else {
                            this.cronograma = this.calcularAmortizacionFrancesMensualFechaDesembolsoCambiada(
                                this.monto_credito,
                                parseInt(this.intervalo),
                                parseInt(this.interes),
                                this.fecha_desembolso,
                                this.fecha_de_pago_cuota);
                        }
                    } else {
                   
                        
                        if (!moment(this.fecha_desembolso).isSame(this.fecha_actual(), 'day')) {
                            this.cronograma = this.calcularAmortizacionFrancesMensual(
                                this.monto_credito,
                                parseInt(this.intervalo),
                                parseInt(this.interes),
                                this.fecha_desembolso
                            );
                     
                        }else{
                            this.cronograma = this.calcularAmortizacionFrancesMensual(
                                this.monto_credito,
                                parseInt(this.intervalo),
                                parseInt(this.interes)
                            );
                        }  
                    }

                    this.fecha_desembolso = this.fecha;
                    break;

                case "Anual":

                    break;
            }

            if (this.is_fecha_pago) {

                // Define las dos fechas
                var startDate = moment(this.fecha);
                var endDate = moment(this.fecha_de_pago_cuota);

                // Calcula la diferencia en días
                var differenceInDays = endDate.diff(startDate, 'days');
                var dias_antes_cuota = differenceInDays + 1

                var monto_interes_mes = this.monto_credito * this.interes / 100;
                var monto_por_dia = monto_interes_mes / 30;
                var monto_del_intervalo = monto_por_dia * dias_antes_cuota;

                // console.log(this.cronograma[this.cronograma.length - 1].fechaVencimiento);

                // var fecha_ultima_cuota = moment(this.cronograma[this.cronograma.length - 1].fechaVencimiento, "DD/MM/YYYY");

                // var fechaVencimiento = fecha_ultima_cuota.clone().add(1, 'months');

                // // Si la fecha de vencimiento cae en domingo (0), ajustar al siguiente día
                // if (fechaVencimiento.day() === 0) {
                //     fechaVencimiento.add(1, 'days');
                // }


                // console.log(fechaVencimiento);

                this.d_t = this.d_t + monto_del_intervalo;

                const pago = {
                    periodo: 0,
                    fechaVencimiento: moment(this.fecha_de_pago_cuota).format("D/M/YYYY"),
                    saldoCapital: 0,
                    amortizacion: 0,
                    interes: monto_del_intervalo,
                    cuota: monto_del_intervalo
                };

                this.cronograma.unshift(pago);

            }

            this.is_cronograma = true;
            this.is_boton = true;



        },
        //generar un pdf  con el cronograma de pagos
        async generarPDF() {
            const doc = new jsPDF();
            var Params = {

            }
            const componente = h(cabezeraVueSimulacion,
                Params
            );
           
            var html = await renderToString(componente);

            const svg = html;

            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            const v = Canvg.fromString(context, svg);

            // Ajustar la escala para mejorar la calidad
            const scaleFactor = 3; // Ajusta este valor según tus necesidades
            const originalWidth = 1800; // Ancho original del SVG
            const originalHeight = 378.8; // Altura original del SVG

            canvas.width = originalWidth * scaleFactor;
            canvas.height = originalHeight * scaleFactor;
            context.scale(scaleFactor, scaleFactor);


            // Datos de la tabla
            const cronograma_headers = [
                ['-', 'Fecha Desembolso', 'Monto de credito', '-', "-", "-"],
                ["-",
                    this.fecha_desembolso,
                    "S/. " + this.formatear_monto(this.monto_credito),
                    "-",
                    "-",
                    "-"
                ],
                ['Periodo', 'Fecha vencimiento', 'Saldo capital', 'Amortizacion', "Interes", "Cuota"]
            ];
            const cronograma_data = [];


            this.cronograma.forEach((element, index) => {
                cronograma_data.push([element.periodo, element.fechaVencimiento,
                "S/. " + this.formatear_monto(element.saldoCapital),
                "S/. " + this.formatear_monto(element.amortizacion),
                "S/. " + this.formatear_monto(element.interes),
                "S/. " + this.formatear_monto(element.cuota)
                ])
            });


            cronograma_data.push([
                "",
                "",
                "",
                "S/. " + this.formatear_monto(this.monto_credito),
                "S/. " + this.formatear_monto(this.sumar_interes),
                "S/. " + this.formatear_monto(this.sumar_cuota)
            ])

            // Configuración de la tabla
            const tableConfig = {
                startY: 90, // Posición vertical de inicio
                head: cronograma_headers,
                body: cronograma_data,
                tableWidth: 'auto',
                theme: 'grid', // Estilo de la tabla
                margin: {
                    top: 0,
                    left: 6,
                    right: 6
                },
                headStyles: {
                    fillColor: [46, 49, 146],
                },
                styles: {
                    cellPadding: 2, // Relleno de celda
                    fontSize: 10, // Tamaño de fuente
                    // Color de fondo de las celdas
                    // Color del texto de las celdas
                    fontStyle: 'normal', // Estilo de fuente (normal, bold, italic)
                    overflow: 'linebreak', // Manejo de desbordamiento de texto
                    halign: 'middle', // Alineación horizontal (left, center, right)
                    valign: 'middle',
                    cellPadding: 0.5 // Alineación vertical (top, middle, bottom)
                },
                columnStyles: { // Estilos específicos de columna

                    0: {
                        fontStyle: 'bold',
                        textColor: [0, 0, 255]
                    }, // Primera columna en negrita y azul
                    1: {
                        halign: 'left'
                    }, // Segunda columna centrada horizontalmente
                },
            };

            doc.autoTable(tableConfig);

            // Renderizar el SVG en el canvas
            await v.render();
            // Agregar la imagen de datos al documento PDF
            doc.addImage(canvas.toDataURL('image/png', 1), 'PNG', 5, 5, 655, 150);
            doc.autoPrint({ variant: 'non-conform' });

            // Guardar el documento PDF
            doc.save('Cronograma-' + this.solicitud_nombre + "-" + this.fecha_desembolso + '.pdf');

        },
    },
    mounted() {
        // formatear monto credito
        this.currency("monto_credito");

        var fecha_actual = moment();
        var fechas_mas_veinte = moment().add(20, 'days');

        this.fecha_desembolso = moment().format("YYYY-MM-DD");
        this.fecha = moment().format("YYYY-MM-DD");


        this.rango_maximo = {
            to: new Date(fecha_actual.format("Y"), fecha_actual.format("M") - 1, fecha_actual.format("DD")),
            from: new Date(fechas_mas_veinte.format("Y"), fechas_mas_veinte.format("M") - 1, fechas_mas_veinte.format("DD"))
        }
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