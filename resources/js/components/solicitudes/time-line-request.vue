<template>

    <div v-if="solicitudes_data.length > 1">

        <div class="intro-y box mt-4 mb-4">
            <div
                class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                <h2 class="font-medium text-base mr-auto">
                    Linea de tiempo de esta solicitud
                </h2>
            </div>
        </div>
 
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="main-timeline">
                        <div class="timeline" v-for="(sd, index) in solicitudes_data" :key="index">
                            <a href="#" class="timeline-content">
                                <div class="timeline-icon">  <Icon icon='money-bills'/>  </div>
                                <div  style="font-size: 1.1rem;">{{ formatear_fecha_normal(sd.fecha_creacion)  }}</div>
                                <h3 class="title">Solicitud N° {{ sd.serie }} / {{ sd.tipo_solicitud }}</h3> 
                                <div v-if="sd.estado_desembolso=='Y'">
                                    <h4> Solicitud con desembolso </h4> 
                                    <p>Fecha : {{ formatear_fecha_normal(sd.fecha_desembolso)  }}</p>
                                    <p>Monto : {{ sd.desemboloso }}</p>
                                </div>
                              
                            </a>
                        </div> 
                    </div>
                </div>
            </div>
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

import * as echarts from 'echarts';


export default {
    mixins: [myMixin],

    data() {
        return {
            solicitud_id: this.$attrs.solicitud_id,
            month_format: null,
            month_format_data: null,
            solicitudes_data: []
        }
    },
    watch: {
        month: function (newValue) {
            console.log("ejecutando un watch" + newValue);
        },
    },
    methods: {
        load_time_line_request() {
            return new Promise((resolve, reject) => {
                const data = {
                    solicitud_id: this.solicitud_id
                };
                const headers = this.headers;

                this.loading_start();

                Axios
                    .post("/time_line_request", data, { headers })
                    .then((response) => {
                        console.log(response.data);
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
        
    },
    created() {
        
    },
    mounted() {
 
        this.load_time_line_request().then((response) => {
            this.solicitudes_data = response;
        });
    }
}
</script>

<style></style>