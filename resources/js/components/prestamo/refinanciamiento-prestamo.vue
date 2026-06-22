<template>
    <div class="card box p-3 ">
        <div class="flex flex-col sm:flex-row sm:items-end xl:items-start">
            <div class="xl:flex sm:mr-auto mt-2 pt-0">
                <div class="font-bold text-base  font-xl">
                    <h1 class=" font-xl">REFINANCIAMIENTO</h1>
                </div>
            </div>
            <div class="xl:flex sm:mr-auto mt-2 pt-0">
                <div class="font-bold text-base  font-xl"> SOLICITUD N° {{ get.code }} <span>Estado : </span> <span>{{
                    get.statusformated }}</span>
                </div>
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
                    Calcular Cronograma de la Reprogramacion
                </button>
            </div>
        </div>
    </div>

    <div class="card box p-3 mt-3">
        <div class="alert alert-pending show flex items-center mb-2" role="alert">
            <Icon icon='circle-info' class='mr-2' /> El Monto de amortizacion del prestamo vigente es igual la
            amortizacion restante
        </div>

        <div class="grid grid-cols-12 gap-6 mt-2">
            <div class="intro-y col-span-12 lg:col-span-4">
                <label for="vertical-form-2" class="form-label">Monto de amortizacion del prestamo vigente</label>
                <div class="input-group">
                    <div id="input-group-email" class="input-group-text">S/.</div>
                    <input class="form-control pl-4" placeholder="Monto del credito"
                        v-model="monto_credito_amortizacion" disabled name="monto_credito" data-type="monto_credito" />
                </div>
            </div>

            <div class="intro-y col-span-12 lg:col-span-4">
                <label for="vertical-form-2" class="form-label">Monto de credito para refinanciar</label>
                <div class="input-group">
                    <div id="input-group-email" class="input-group-text">S/.</div>
                    <input class="form-control pl-4" placeholder="Monto del credito" v-model="monto_credito_nueva"
                        name="monto_credito_nueva" data-type="monto_credito" />
                </div>
            </div>


            <div class="intro-y col-span-12 lg:col-span-4">
                <label for="vertical-form-2" class="form-label">Monto de credito </label>
                <div class="input-group">
                    <div id="input-group-email" class="input-group-text">S/.</div>
                    <input class="form-control pl-4" placeholder="Monto del credito" v-model="monto_credito"
                        name="monto_credito" data-type="monto_credito" />
                </div>
            </div>
        </div>



        <div class="grid grid-cols-12 gap-6 mt-2">

            <div class="intro-y col-span-12 lg:col-span-4">
                <label for="vertical-form-2" class="form-label"> Interes %</label>
                <div class="input-group">
                    <div id="input-group-email" class="input-group-text">%</div>
                    <input name="interes" v-model="interes" type="number" class="form-control" placeholder="0">
                </div>
            </div>

            <div class="intro-y col-span-12 mr-4 lg:col-span-4">

                <label for="vertical-form-2" class="form-label"> {{ frecuencia_pagos_a }} </label>
                <input name="intervalo" v-model="intervalo" type="number" class="form-control"
                    :placeholder="'2 ' + frecuencia_pagos_a">

            </div>
            <div class=" col-span-12 lg:col-span-4">
                <label>Frecuencia de los pagos </label>
                <div class="flex flex-col sm:flex-row mt-4">
                    <div class="form-check mr-2 sm:mt-0">
                        <input id="Diario" class="form-check-input" type="radio" name="frecuencia_pagos" checked
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

        </div>


    </div>

    <!-- Nuevo cronograma cronograma de prestamo -->
    <div class="card box p-3 mt-3" v-if="is_cronograma">
        <div class="report-box-1 intro-y mt-2">
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
                                                src="../../../../public/dist/images/Draw/schedule.svg" alt="">
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
    <!-- cronograma de prestamo -->

    <div class="box mt-4 p-2">
        <div class=" ">
            <div class="overflow-x-auto table-responsive">
                <table class="table table-responsive table-bordered" id="table_cronograma" style="font-size: 0.8rem;">
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


    <button v-if="is_boton" @click="crear_refinanciamiento()" type="submit"
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
            monto_credito_amortizacion: 0,
            monto_credito_nueva: 0,
            tasa_diaria: 0,
            cuotas: 0,
            cuota_final: 0,
            fecha_desembolso: null,
            frecuencia_pagos: "Diario",
            frecuencia_pagos_a: "Dias",
            interes: 10,
            intervalo: 30,
            is_cronograma: false,
            tabla_cronograma: null
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
        monto_credito_nueva: function (newValue) {
            this.monto_credito = newValue - this.monto_credito_amortizacion;
            return newValue;
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

                case "Diario":
                    this.frecuencia_pagos_a = "Dias";
                    break;

                case "Semanal":
                    this.frecuencia_pagos_a = "Semanas";
                    break;

                case "Quincenal":
                    this.frecuencia_pagos_a = "Quincenas";
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
        // crear la reprogramacion
        crear_refinanciamiento() {
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
                intervalo: this.intervalo
            };

            const headers = this.headers;

            this.loading_start();

            Axios
                .post("/crear_refinanciamiento", data, {
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

        calcular_cronograma() {


            switch (this.frecuencia_pagos) {

                case "Diario":
                    this.cronograma = this.calcularAmortizacionFrancesdiario(
                        this.monto_credito,
                        parseInt(this.intervalo),
                        parseInt(this.interes)
                    );
                    this.frecuencia_pagos_a = "Dias"; 
                    break;

                case "Semanal":
                    this.cronograma = this.calcularAmortizacionFrancesSemanal(
                        this.monto_credito,
                        parseInt(this.intervalo),
                        parseInt(this.interes)
                    );
                    this.frecuencia_pagos_a = "Semanas";
                    break;

                case "Quincenal":
                    this.cronograma = this.calcularAmortizacionFrancesQuincenal(
                        this.monto_credito,
                        parseInt(this.intervalo),
                        parseInt(this.interes)
                    );
                    this.frecuencia_pagos_a = "Quincenas";
                    break;

                case "Mensual":
                    this.cronograma = this.calcularAmortizacionFrancesMensual(
                        this.monto_credito,
                        parseInt(this.intervalo),
                        parseInt(this.interes)
                    );
                    this.frecuencia_pagos_a = "Mensuales";
                    break;

                case "Anual":

                    break;
            }
            this.is_cronograma = true;
            this.is_boton = true;

        },
        tabla_cronograma_data() {
            var self = this;
            this.tabla_cronograma = $('#table_cronograma').DataTable({
                paging: false,
                searching: false,
                processing: true,
                "infoFiltered": "",
                "processing": "<img src='~/Content/images/loadingNew.gif' />",
                autoWidth: true,
                responsive: true,
                data: self.get.prestamo.cronograma,
                "ordering": true,
                "language": this.spanish_datatable,
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
        }
    },
    mounted() {
        this.monto_credito_amortizacion = this.monto_credito_nuevo;
        this.monto_credito_nueva = this.get.prestamo.moto_credito;
        this.monto_credito = this.monto_credito_nueva - this.monto_credito_amortizacion;



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

            case "Quincenal":
                this.frecuencia_pagos_a = "Quincenas";
                break;

            case "Mensual":
                this.frecuencia_pagos_a = "Menses";
                break;

            case "Anual":

                break;
        }
        this.tabla_cronograma_data()

    }
}
</script>

<style></style>