<template>
    <div class="box">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
            <div class="mr-auto">
                <h2 class="font-medium text-base">
                    Simulación de préstamo
                </h2>
                <p class="text-slate-500 text-xs mt-1">
                    Calcule el cronograma de financiamiento y exporte un PDF con la marca Horizon Finance
                </p>
            </div>
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
                                    <input id="Semanal" v-model="frecuencia_pagos" class="form-check-input" type="radio"
                                        name="frecuencia_pagos" value="Semanal">
                                    <label class="form-check-label" for="Semanal">Semanal</label>
                                </div>
                                <div class="form-check mr-2 mt-2 sm:mt-0">
                                    <input id="Mensual" v-model="frecuencia_pagos" class="form-check-input" type="radio"
                                        name="frecuencia_pagos" value="Mensual">
                                    <label class="form-check-label" for="Mensual">Mensual</label>
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



                    <!-- Resumen de simulación -->
                    <div class="sim-resumen intro-y mt-4" v-if="is_cronograma">
                        <div class="sim-resumen__grid">
                            <div class="sim-resumen__monto">
                                <span class="sim-resumen__label">Monto del crédito</span>
                                <div class="sim-resumen__amount">
                                    <span class="sim-resumen__currency">S/</span>
                                    {{ numeralFormat(monto_credito, '0,0.00') }}
                                </div>
                                <span class="sim-resumen__hint">Fecha desembolso: {{ fechaDesembolsoFmt }}</span>
                            </div>
                            <div class="sim-resumen__stats">
                                <div class="sim-stat">
                                    <span class="sim-stat__label">Plazo</span>
                                    <span class="sim-stat__value">{{ intervalo }} {{ frecuencia_pagos_a }}</span>
                                </div>
                                <div class="sim-stat">
                                    <span class="sim-stat__label">Tasa de interés</span>
                                    <span class="sim-stat__value">{{ interes }} %</span>
                                </div>
                                <div class="sim-stat">
                                    <span class="sim-stat__label">Frecuencia</span>
                                    <span class="sim-stat__value">{{ frecuencia_pagos }}</span>
                                </div>
                                <div class="sim-stat sim-stat--highlight">
                                    <span class="sim-stat__label">Cuota</span>
                                    <span class="sim-stat__value">S/ {{ numeralFormat(cuotas, '0,0.00') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-12">
                            <div class="intro-y flex flex-col sm:flex-row items-center mt-8">
                                <h2 class="text-lg font-medium mr-auto flex items-center gap-2 flex-wrap">
                                    Cronograma de financiamiento
                                    <Icon icon="hourglass-half" />
                                    <span
                                        v-if="is_cronograma"
                                        class="py-1 px-2 rounded-full text-xs bg-primary text-white font-medium"
                                    >
                                        {{ frecuencia_pagos }}
                                    </span>
                                </h2>
                                <div class="w-full sm:w-auto flex mt-4 sm:mt-0" v-if="is_cronograma">
                                    <button
                                        type="button"
                                        :disabled="exportando"
                                        v-on:click="generarPDF()"
                                        class="btn btn-primary w-full sm:w-auto"
                                    >
                                        <Icon icon="file-pdf" class="mr-2" />
                                        {{ exportando ? 'Generando PDF...' : 'Exportar PDF del cronograma' }}
                                    </button>
                                </div>
                            </div>

                            <div class="overflow-x-auto mt-4 sim-table-wrap">
                                <table class="table table-bordered sim-table">
                                    <thead class="thead-fixed">
                                        <tr v-if="cronograma.length != 0">
                                            <th colspan="2" class="sim-table__meta">Fecha desembolso</th>
                                            <th colspan="2" class="sim-table__meta">Monto de crédito</th>
                                            <th colspan="2" class="sim-table__meta">Total a pagar</th>
                                        </tr>
                                        <tr v-if="cronograma.length != 0">
                                            <td colspan="2">{{ fechaDesembolsoFmt }}</td>
                                            <td colspan="2">S/ {{ numeralFormat(monto_credito, '0,0.00') }}</td>
                                            <td colspan="2" class="font-semibold text-primary">
                                                S/ {{ numeralFormat(sumar_cuota, '0,0.00') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="whitespace-nowrap">Periodo</th>
                                            <th class="whitespace-nowrap">Fecha vencimiento</th>
                                            <th class="whitespace-nowrap">Saldo capital</th>
                                            <th class="whitespace-nowrap">Amortización</th>
                                            <th class="whitespace-nowrap">Interés</th>
                                            <th class="whitespace-nowrap">Cuota</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(c_g, c_g_index) in cronograma" :key="c_g_index">
                                            <td class="font-semibold">{{ c_g.periodo }}</td>
                                            <td>{{ c_g.fechaVencimiento }}</td>
                                            <td>S/ {{ numeralFormat(c_g.saldoCapital, '0,0.00') }}</td>
                                            <td>S/ {{ numeralFormat(c_g.amortizacion, '0,0.00') }}</td>
                                            <td>S/ {{ numeralFormat(c_g.interes, '0,0.00') }}</td>
                                            <td class="sim-table__cuota">
                                                S/ {{ numeralFormat(c_g.cuota, '0,0.00') }}
                                            </td>
                                        </tr>
                                        <tr v-if="cronograma.length != 0" class="sim-table__totals">
                                            <td colspan="3" class="text-right font-semibold">Totales</td>
                                            <td class="font-semibold">S/ {{ numeralFormat(monto_credito, '0,0.00') }}</td>
                                            <td class="font-semibold">S/ {{ numeralFormat(sumar_interes, '0,0.00') }}</td>
                                            <td class="font-semibold">S/ {{ numeralFormat(sumar_cuota, '0,0.00') }}</td>
                                        </tr>
                                        <tr v-if="cronograma.length == 0">
                                            <td colspan="6">
                                                <div class="sim-empty">
                                                    <Icon icon="calendar-days" class="sim-empty__icon" />
                                                    <h4 class="sim-empty__title">
                                                        Complete los datos del préstamo y pulse
                                                        <strong>Calcular Cronograma</strong>
                                                    </h4>
                                                    <p class="sim-empty__sub">
                                                        Luego podrá exportar el cronograma de financiamiento en PDF
                                                    </p>
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

import 'tom-select/dist/css/tom-select.css';
import Axios from 'axios';

import "jquery-validation";
import "jquery-validation/dist/localization/messages_es"

import moment from 'moment';
import 'moment/locale/es';

import jsPDF from 'jspdf';
import 'jspdf-autotable';

// mixin
import {
    myMixin
} from "../../mixin.js";

// Colores Horizon Finance — primary = mismo azul del menú (bg-primary / blue-800)
const BRAND = {
    primary: [30, 64, 175],       // blue-800 = --color-primary del menú
    primaryDark: [30, 58, 138],   // blue-900
    horizonGreen: [5, 190, 80],   // #05be50 del logo
    slate900: [15, 23, 42],
    slate600: [71, 85, 105],
    slate400: [148, 163, 184],
    slate200: [226, 232, 240],
    slate100: [241, 245, 249],
    slate50: [248, 250, 252],
    white: [255, 255, 255],
    success: [5, 150, 105],
};

// Mismo logo del menú lateral / login (icono + HORIZON en blanco)
const LOGO_DASHBOARD = '/images/logo_horizon.png';

export default {
    mixins: [myMixin],
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
            cuotas: 0,
            cuota_final: 0,
            fecha_desembolso: null,
            frecuencia_pagos: "Quincenal",
            frecuencia_pagos_a: "Quincenas",
            interes: 15,
            intervalo: 3,
            is_cronograma: false,
            cronograma: [],
            exportando: false,

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

                case "Semanal":
                    this.frecuencia_pagos_a = "Semanas";
                    break;

                case "Mensual":
                    this.frecuencia_pagos_a = "Menses";
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
        fechaDesembolsoFmt() {
            if (!this.fecha_desembolso) return '—';
            const m = moment(this.fecha_desembolso);
            return m.isValid() ? m.format('DD/MM/YYYY') : this.fecha_desembolso;
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

            // Cuota representativa (primera cuota regular con periodo > 0, o la primera fila)
            const cuotaRef = this.cronograma.find((r) => parseInt(r.periodo, 10) > 0) || this.cronograma[0];
            this.cuotas = cuotaRef ? parseFloat(cuotaRef.cuota) || 0 : 0;

            this.is_cronograma = true;
            this.is_boton = true;
        },

        money(value) {
            const n = parseFloat(value);
            if (Number.isNaN(n)) return '0.00';
            return n.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        /**
         * Carga un PNG del sistema y devuelve { dataUrl, width, height }.
         */
        async loadImageBase64(url) {
            try {
                const res = await fetch(url, { cache: 'force-cache' });
                if (!res.ok) throw new Error('Imagen no encontrada: ' + url);
                const blob = await res.blob();
                const dataUrl = await new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onload = () => resolve(reader.result);
                    reader.onerror = reject;
                    reader.readAsDataURL(blob);
                });

                const dims = await new Promise((resolve) => {
                    const img = new Image();
                    img.onload = () => resolve({ width: img.naturalWidth || 1, height: img.naturalHeight || 1 });
                    img.onerror = () => resolve({ width: 100, height: 100 });
                    img.src = dataUrl;
                });

                return { dataUrl, ...dims };
            } catch (e) {
                console.warn('No se pudo cargar', url, e);
                return null;
            }
        },

        /**
         * PDF profesional del cronograma de simulación — branding Horizon Finance
         */
        async generarPDF() {
            if (!this.cronograma || this.cronograma.length === 0) {
                this.alert_warning?.('Primero calcule el cronograma para exportar el PDF.');
                return;
            }

            this.exportando = true;
            try {
                const doc = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
                const pageW = doc.internal.pageSize.getWidth();
                const pageH = doc.internal.pageSize.getHeight();
                const marginX = 14;
                let y = 12;

                // Cabecera = mismo azul del menú del sistema (bg-primary)
                // logo_horizon.png tiene texto HORIZON en blanco → se ve bien sobre primary
                const logo = await this.loadImageBase64(LOGO_DASHBOARD);

                const headerH = 32;
                doc.setFillColor(...BRAND.primary);
                doc.rect(0, 0, pageW, headerH, 'F');
                // Franja verde marca Horizon
                doc.setFillColor(...BRAND.horizonGreen);
                doc.rect(0, headerH, pageW, 1.8, 'F');

                if (logo && logo.dataUrl) {
                    try {
                        // Proporción real del logo del menú (icono + HORIZON)
                        const maxH = 16;
                        const maxW = 68;
                        const ratio = logo.width / logo.height;
                        let logoH = maxH;
                        let logoW = logoH * ratio;
                        if (logoW > maxW) {
                            logoW = maxW;
                            logoH = logoW / ratio;
                        }
                        const logoY = (headerH - logoH) / 2;
                        doc.addImage(logo.dataUrl, 'PNG', marginX, logoY, logoW, logoH);
                    } catch (e) {
                        doc.setTextColor(...BRAND.white);
                        doc.setFont('helvetica', 'bold');
                        doc.setFontSize(15);
                        doc.text('HORIZON', marginX, 19);
                    }
                } else {
                    doc.setTextColor(...BRAND.white);
                    doc.setFont('helvetica', 'bold');
                    doc.setFontSize(15);
                    doc.text('HORIZON', marginX, 19);
                }

                doc.setTextColor(...BRAND.white);
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(12);
                doc.text('Simulación de préstamo', pageW - marginX, 13, { align: 'right' });
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(9);
                doc.setTextColor(191, 219, 254); // blue-200, legible sobre primary
                doc.text('Cronograma de financiamiento', pageW - marginX, 19, { align: 'right' });
                doc.setFontSize(8);
                doc.setTextColor(147, 197, 253); // blue-300
                doc.text(moment().format('DD/MM/YYYY HH:mm'), pageW - marginX, 25, { align: 'right' });

                y = 42;

                // ── Título ────────────────────────────────────────────
                doc.setTextColor(...BRAND.slate900);
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(12);
                doc.text('Resumen de la simulación', marginX, y);
                y += 4;

                // ── Cajas de resumen ──────────────────────────────────
                const boxW = (pageW - marginX * 2 - 6) / 4;
                const boxH = 18;
                const resumenItems = [
                    { label: 'Monto crédito', value: 'S/ ' + this.money(this.monto_credito) },
                    { label: 'Plazo', value: `${this.intervalo} ${this.frecuencia_pagos_a}` },
                    { label: 'Tasa de interés', value: `${this.interes} %` },
                    { label: 'Cuota', value: 'S/ ' + this.money(this.cuotas) },
                ];

                resumenItems.forEach((item, i) => {
                    const x = marginX + i * (boxW + 2);
                    doc.setFillColor(...BRAND.slate50);
                    doc.setDrawColor(...BRAND.slate100);
                    doc.roundedRect(x, y, boxW, boxH, 1.5, 1.5, 'FD');
                    doc.setTextColor(...BRAND.slate600);
                    doc.setFont('helvetica', 'normal');
                    doc.setFontSize(7);
                    doc.text(item.label.toUpperCase(), x + 3, y + 6);
                    doc.setTextColor(...BRAND.primary);
                    doc.setFont('helvetica', 'bold');
                    doc.setFontSize(10);
                    doc.text(String(item.value), x + 3, y + 13);
                });

                y += boxH + 6;

                // ── Datos adicionales ─────────────────────────────────
                doc.setFillColor(...BRAND.slate50);
                doc.roundedRect(marginX, y, pageW - marginX * 2, 12, 1.5, 1.5, 'F');
                doc.setTextColor(...BRAND.slate600);
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(8);
                const metaY = y + 7.5;
                doc.text(`Frecuencia: ${this.frecuencia_pagos}`, marginX + 4, metaY);
                doc.text(`Desembolso: ${this.fechaDesembolsoFmt}`, marginX + 55, metaY);
                doc.text(`Total a pagar: S/ ${this.money(this.sumar_cuota)}`, marginX + 110, metaY);
                doc.setTextColor(...BRAND.success);
                doc.setFont('helvetica', 'bold');
                doc.text(`Intereses: S/ ${this.money(this.sumar_interes)}`, pageW - marginX - 4, metaY, { align: 'right' });

                y += 18;

                // ── Tabla del cronograma ──────────────────────────────
                const head = [['#', 'Fecha vencimiento', 'Saldo capital', 'Amortización', 'Interés', 'Cuota']];
                const body = this.cronograma.map((row) => [
                    String(row.periodo ?? ''),
                    String(row.fechaVencimiento ?? ''),
                    'S/ ' + this.money(row.saldoCapital),
                    'S/ ' + this.money(row.amortizacion),
                    'S/ ' + this.money(row.interes),
                    'S/ ' + this.money(row.cuota),
                ]);

                body.push([
                    { content: 'TOTALES', colSpan: 3, styles: { halign: 'right', fontStyle: 'bold' } },
                    { content: 'S/ ' + this.money(this.monto_credito), styles: { fontStyle: 'bold' } },
                    { content: 'S/ ' + this.money(this.sumar_interes), styles: { fontStyle: 'bold' } },
                    { content: 'S/ ' + this.money(this.sumar_cuota), styles: { fontStyle: 'bold', textColor: BRAND.primary } },
                ]);

                doc.autoTable({
                    startY: y,
                    head,
                    body,
                    theme: 'grid',
                    margin: { left: marginX, right: marginX, bottom: 22 },
                    styles: {
                        font: 'helvetica',
                        fontSize: 8.5,
                        cellPadding: 2.2,
                        valign: 'middle',
                        textColor: BRAND.slate900,
                        lineColor: [226, 232, 240],
                        lineWidth: 0.2,
                    },
                    headStyles: {
                        fillColor: BRAND.primary,
                        textColor: BRAND.white,
                        fontStyle: 'bold',
                        fontSize: 8,
                        halign: 'center',
                    },
                    alternateRowStyles: {
                        fillColor: BRAND.slate50,
                    },
                    columnStyles: {
                        0: { halign: 'center', fontStyle: 'bold', cellWidth: 14 },
                        1: { halign: 'center', cellWidth: 32 },
                        2: { halign: 'right' },
                        3: { halign: 'right' },
                        4: { halign: 'right' },
                        5: { halign: 'right', fontStyle: 'bold', textColor: BRAND.primary },
                    },
                    didParseCell: (data) => {
                        if (data.section === 'body' && data.row.index === body.length - 1) {
                            data.cell.styles.fillColor = [219, 234, 254]; // blue-100
                        }
                    },
                    didDrawPage: (data) => {
                        // Pie de página en cada hoja
                        const footerY = pageH - 12;
                        doc.setDrawColor(...BRAND.primary);
                        doc.setLineWidth(0.4);
                        doc.line(marginX, footerY - 4, pageW - marginX, footerY - 4);

                        doc.setFont('helvetica', 'normal');
                        doc.setFontSize(7.5);
                        doc.setTextColor(...BRAND.slate400);
                        doc.text(
                            'Documento informativo de simulación · HORIZON',
                            marginX,
                            footerY
                        );
                        doc.text(
                            `Página ${doc.internal.getNumberOfPages()}`,
                            pageW - marginX,
                            footerY,
                            { align: 'right' }
                        );
                        doc.setFontSize(7);
                        doc.text(
                            'Los montos son referenciales y no constituyen un contrato de crédito.',
                            marginX,
                            footerY + 4
                        );
                    },
                });

                const nombre = `Cronograma-Simulacion-${this.fechaDesembolsoFmt.replace(/\//g, '-')}.pdf`;
                doc.save(nombre);

                this.alert_success?.('PDF del cronograma generado correctamente');
            } catch (err) {
                console.error(err);
                this.alert_error_modal?.('No se pudo generar el PDF del cronograma');
            } finally {
                this.exportando = false;
            }
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

<style scoped>
.sim-resumen {
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    overflow: hidden;
    background: linear-gradient(135deg, #eff6ff 0%, #ffffff 50%, #f8fafc 100%);
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
}

.sim-resumen__grid {
    display: grid;
    grid-template-columns: 1fr;
}

@media (min-width: 768px) {
    .sim-resumen__grid {
        grid-template-columns: 1.1fr 1.4fr;
    }
}

.sim-resumen__monto {
    padding: 1.5rem 1.75rem;
    border-bottom: 1px solid #e2e8f0;
}

@media (min-width: 768px) {
    .sim-resumen__monto {
        border-bottom: none;
        border-right: 1px dashed #cbd5e1;
    }
}

.sim-resumen__label {
    display: block;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #64748b;
}

.sim-resumen__amount {
    margin-top: 0.5rem;
    font-size: 2.25rem;
    font-weight: 700;
    color: #1e40af;
    line-height: 1.1;
}

.sim-resumen__currency {
    font-size: 1.25rem;
    margin-right: 0.25rem;
    color: #64748b;
    font-weight: 600;
}

.sim-resumen__hint {
    display: block;
    margin-top: 0.65rem;
    font-size: 0.85rem;
    color: #64748b;
}

.sim-resumen__stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
    padding: 1.25rem 1.5rem;
}

.sim-stat {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 0.75rem 0.9rem;
}

.sim-stat--highlight {
    border-color: #bfdbfe;
    background: #eff6ff;
}

.sim-stat__label {
    display: block;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    color: #64748b;
    margin-bottom: 0.25rem;
}

.sim-stat__value {
    font-size: 1.05rem;
    font-weight: 700;
    color: #0f172a;
}

.sim-table-wrap {
    border: 1px solid #e2e8f0;
    border-radius: 0.85rem;
    background: #fff;
}

.sim-table thead th {
    background: #1e40af !important;
    color: #fff !important;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    border-color: #1e3a8a !important;
}

.sim-table__meta {
    background: #1e3a8a !important;
}

.sim-table tbody td {
    font-size: 0.9rem;
    vertical-align: middle;
}

.sim-table__cuota {
    font-weight: 700;
    color: #1e40af;
    background: #eff6ff;
}

.sim-table__totals td {
    background: #dbeafe !important;
    border-top: 2px solid #1e40af !important;
}

.sim-empty {
    text-align: center;
    padding: 2.5rem 1rem;
    color: #64748b;
}

.sim-empty__icon {
    font-size: 2rem;
    margin-bottom: 0.75rem;
    color: #94a3b8;
}

.sim-empty__title {
    font-size: 1rem;
    font-weight: 600;
    color: #334155;
    margin: 0 0 0.35rem;
}

.sim-empty__sub {
    margin: 0;
    font-size: 0.875rem;
    color: #94a3b8;
}

.thead-fixed {
    position: sticky;
    top: 0;
    z-index: 10;
}
</style>