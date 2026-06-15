<template>

    <div id="select-options" class="p-5">

        <div class="col-span-12 ">
            <div class="intro-y flex items-center h-10">
                <h2 class="text-lg font-medium truncate mr-5">
                    Reporte -- general
                </h2>
                <button href="" class="ml-auto flex btn btn-primary " @click="load_leyenda()">
                    <Icon icon='rotate-right' class='mr-2' />
                </button>
            </div>

        </div>

        <div class="grid grid-cols-12 gap-6 mt-2">

            <div class="intro-y col-span-12 lg:col-span-6">
                <label for="vertical-form-2" class="form-label"> Intereses </label>
                <div class="input-group">
                    <select class="form-select  sm:mr-2" v-model="tipo_reporte">
                        <option value="S"> Seleccione un año </option>
                        <option value="A"> Anual </option>
                        <option value="M"> Mensual </option>
                        <option value="R"> Personalizado </option>
                    </select>
                </div>
            </div>

            <div v-if="tipo_reporte == 'A'" class="intro-y col-span-12 lg:col-span-6">
                <label for="vertical-form-2" class="form-label"> Año </label>
                <div class="input-group">
                    <div style="display: none;" id="p_loading_r_a_c" class="card flex justify-content-center">
                        <ProgressSpinner style="width: 35px; height: 35px" strokeWidth="8" fill="var(--surface-ground)"
                            animationDuration=".2s" aria-label="Custom ProgressSpinner" />
                    </div>
                    <select style="display: none;" id="e_loading_r_a_c" class="form-select  sm:mr-2"
                        v-model="cbx_anual">
                        <option v-for="(c_a_d, index_c_a_d) in cbx_anual_data" :key="index_c_a_d" :value="c_a_d.value">
                            {{ c_a_d.text }}</option>

                    </select>
                </div>
            </div>

            <div v-if="tipo_reporte == 'M'" class="intro-y col-span-12 lg:col-span-6">
                <label for="vertical-form-2" class="form-label"> Meses </label>
                <div class="input-group">
                    <div style="display: none;" id="p_loading_mensual" class="card flex justify-content-center">
                        <ProgressSpinner style="width: 35px; height: 35px" strokeWidth="8" fill="var(--surface-ground)"
                            animationDuration=".2s" aria-label="Custom ProgressSpinner" />
                    </div>
                    <select style="display: none;" id="e_loading_mensual" class="form-select  sm:mr-2"
                        v-model="cbx_mensual" v-on:change="change_cbx_mensual()">
                        <option v-for="(c_m_d, index_c_m_d) in cbx_mensual_data" :key="index_c_m_d"
                            :value="c_m_d.value">
                            {{ c_m_d.text }}</option>

                    </select>
                </div>
            </div>
            <!-- <div class="intro-y col-span-12 lg:col-span-6">
                <label for="vertical-form-2" class="form-label"> Destino </label> 
                <button type="button" name="" id="" class="btn btn-primary btn-lg btn-block form-control"> + </button>
            </div> -->
        </div>

        <hr class="mb-4 mt-4">

        <div v-if="tipo_reporte == 'S'" class="grid grid-cols-12 gap-6 mt-2">
            <div class="intro-y col-span-12 lg:col-span-12">
                <center>
                    <img class="card-img-top" width="58%" src="../../../../public/dist/images/Draw/chart.svg" alt="">
                    <label for="vertical-form-2" class="form-label"> Seleciona las opciones para poder mostrar reportes
                    </label>
                </center>
            </div>
        </div>

        <!-- graficos -->

        <div v-if="tipo_reporte == 'A' && cbx_anual != 'S'" class="box">

            <by_year :year="cbx_anual"></by_year>

        </div>

        <div v-if="tipo_reporte == 'M' && cbx_mensual != 'S'" class="box">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">mensuales</h1>
            <by_months id="by_month" :key="Key_mensual" :by_month="cbx_mensual"></by_months> 
        </div>

    </div>
</template>

<script>
import Axios from 'axios'

// mixin
import {
    myMixin
} from "../../mixin.js";
import { forEach } from 'jszip';
import moment from 'moment';
import by_year from './reporte_balance/by_year.vue';



export default {
    components: { by_year },
    mixins: [myMixin],
    data() {
        return {
            get_leyenda: [],
            options: {
                // Data: Data to be displayed in the chart
                data: [
                    { asset: "cargando", amount: 10000 },
                    { asset: "cargando", amount: 10000 },
                ],
                // Series: Defines which chart type and data to use
                series: [
                    {
                        type: "donut",
                        calloutLabelKey: "asset",
                        angleKey: "amount",
                        innerRadiusRatio: 0.4,
                        innerLabels: [
                            {
                                text: "Total Investment",
                                fontWeight: "bold",
                            },
                            {
                                text: "$100,000",
                                margin: 4,
                                fontSize: 48,
                                color: "green",
                            },
                        ],
                        innerCircle: {
                            fill: "#3AB3FF",
                        },
                    },
                ],
                theme: {
                    baseTheme: "ag-default",
                    palette: {
                        fills: ["#DD0506", "#FF8000", "#0083D0"],
                        strokes: ["#204484"],
                    },
                    overrides: {
                        common: {
                            title: {
                                fontSize: 24,
                            },
                        },
                        bar: {
                            series: {
                                label: {
                                    enabled: true,
                                    color: "#204484",
                                },
                            },
                        },
                    },
                }
            },
            tipo_reporte: "S",
            cbx_anual: "S",
            cbx_anual_data: [],
            // para la parte mensual
            cbx_mensual: "S",
            cbx_mensual_data: [],
            Key_mensual:1
        }
    },
    watch: {
        tipo_reporte: function (newValue) {
            switch (
            newValue
            ) {
                case "A":

                    this.cbx_anual_data = [];
                    this.cbx_anual_data.push({
                        value: "S",
                        text: "Seleccione un año"
                    });

                    $("#e_loading_r_a_c").fadeOut()
                    $("#p_loading_r_a_c").fadeIn()

                    this.load_anual().then((response) => {
                        response.forEach(element => {
                            this.cbx_anual_data.push({
                                value: element.value,
                                text: element.text
                            });
                        });
                        this.cbx_anual = "S";
                        $("#p_loading_r_a_c").fadeOut()
                        $("#e_loading_r_a_c").fadeIn()
                    });

                    break;

                case "M":

                    this.cbx_mensual_data = [];
                    this.cbx_mensual_data.push({
                        value: "S",
                        text: "Seleccione un mes"
                    });

                    $("#e_loading_mensual").fadeOut()
                    $("#p_loading_mensual").fadeIn()

                    this.mensual().then((response) => {
                        response.forEach(element => {
                            this.cbx_mensual_data.push({
                                value: element.mes + "-" + element.anio,
                                text: moment(element.mes + "/" + element.anio, 'MM/YYYY').format('MMMM/YYYY') + " | " + element.mes + " / " + element.anio
                            });
                        });
                        this.cbx_mensual = "S";
                        $("#p_loading_mensual").fadeOut()
                        $("#e_loading_mensual").fadeIn()
                    });

                    break;

                case "R":

                    break;

            }
        },
        cbx_anual: function (newValue) {
            if (newValue != "S") { 
                this.cbx_anual = newValue; 
            }
        },
         
    },
    methods: {
        load_leyenda() {
        },
        load_anual() {
            return new Promise((resolve, reject) => {
                const data = {};
                const headers = this.headers;

                this.loading_start();

                Axios
                    .post("/load_anual", data, { headers })
                    .then((response) => {
                        
                        this.loading_end();
                        if (response.data.success) {
                            this.alert_success(response.data.message);
                            resolve(response.data.data);
                        } else {
                            this.alert_warning(response.data.message);
                            reject(new Error(response.data.message));
                        }
                    })
                    .catch((error) => {
                        this.loading_end();
                        this.alert_error_modal("Error en el servidor, ingrese los nombres y apellidos manualmente");
                        console.error(error);
                        reject(error);
                    });
            });
        },
        mensual() {
            return new Promise((resolve, reject) => {
                const data = {};
                const headers = this.headers;

                this.loading_start();

                Axios
                    .post("/load_mensual", data, { headers })
                    .then((response) => {
                         
                        this.loading_end();
                        if (response.data.success) {
                            this.alert_success(response.data.message);
                            resolve(response.data.data);
                        } else {
                            this.alert_warning(response.data.message);
                            reject(new Error(response.data.message));
                        }
                    })
                    .catch((error) => {
                        this.loading_end();
                        this.alert_error_modal("Error en el servidor, ingrese los nombres y apellidos manualmente");
                        console.error(error);
                        reject(error);
                    });
            });
        },
        rango_fecha() {
        },
        change_cbx_mensual(){
         
            if (event.target.value != "S") { 
                this.cbx_mensual = "S";
                this.cbx_mensual = event.target.value;
                this.Key_mensual += 1;
            }
        }
    },
    created() {

    },
    mounted() {

    }
}
</script>

<style></style>