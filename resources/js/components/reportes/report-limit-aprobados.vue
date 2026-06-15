<template>
   <div class="col-span-12 md:col-span-6 xl:col-span-4 2xl:col-span-12 mt-3 2xl:mt-8">
                        <div class="intro-x flex items-center h-10">
                            <h2 class="text-lg font-medium truncate mr-5">
                              solicitudes aprobadas
                            </h2>
                        </div>
                        <div class="mt-5">
                            
                            <a class="intro-x" v-for="(g_s, index_g_s) in get_solicitudes" :key="index_g_s" :href="'/solicitud/'+g_s.urlapi">
                                <div class="box px-5 py-3 mb-3 flex items-center zoom-in">
                                    <div class="w-10 h-10 flex-none image-fit rounded-full overflow-hidden">
                                         <Icon icon='hand-holding-dollar' class='mr-2' /> 
                                    </div>
                                    <div class="ml-4 mr-auto">
                                        <div class="font-medium">{{ g_s.solicitud_nombre }}</div>
                                        <div class="text-slate-500 text-xs mt-0.5"> {{ g_s.analista.name }} {{ g_s.analista.lastname }}</div>
                                    </div>
                                    <div class="text-success">{{ formatear_dinero_soles(g_s.prestamo.moto_credito) }}</div>
                                </div>
                            </a>
                            
                            <a href="/tabla_solicitud_aprobado" class="intro-x w-full block text-center rounded-md py-3 border border-dotted border-slate-400 dark:border-darkmode-300 text-slate-500">Ver Mas</a>
                        </div>
                    </div>
</template>

<script>
import $ from 'jquery';

import 'tom-select/dist/css/tom-select.css';
import TomSelect from 'tom-select';
import Axios from 'axios';

// mixin
import {
    myMixin
} from "../../mixin.js";

export default {
    mixins:[myMixin],
    data() {
        return {
            get_solicitudes: [], 
             
        };
    },
    mounted() {
        
        this.get_solicitudes_data().then((response) => {
            this.get_solicitudes =  response; 
            console.log(response);
        }).catch((err) => {
            
        }); 
    },
    methods: {
       async get_solicitudes_data() {
            const data = {
                 
            };
            
            const headers = this.headers;
            
            this.loading_start();
            
            return Axios
                .post("/report_limit_aprobados", data, {
                    headers,
                })
                .then((response) => {
                    
                    console.log(response.data);
                    if (response.data.success) {
                        console.log("solicitudes");
                        this.loading_end();
                        return response.data.data;
                    } else {
                        this.loading_end();
                        this.alert_warning(response.data.message);
                    }
                   
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor");
                    console.error(error);
                });
        }   
    }
}
</script>

<style></style>