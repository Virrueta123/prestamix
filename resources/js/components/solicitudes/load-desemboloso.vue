<template>

    <div v-if="get_desemboloso_request.length != 0">

        <div class="intro-y box mt-4 mb-4">
            <div
                class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                <h2 class="font-medium text-base mr-auto">
                    Esta solicitud tiene un desemboloso
                </h2>
            </div>
        </div>

        <div class="box sm:flex">
            <div class="px-8 py-12 flex flex-col justify-center flex-1">
                <Icon icon='money-bills' class='mr-2 fa-10x text-success' />
                <center>
                    <div class="relative text-3xl font-medium mt-12 pl-4 ml-0.5"> <span class="absolute text-8xl font-medium top-0 left-0 -ml-0.5"></span> S/. {{ get_desemboloso_request.monto }} </div>
                </center>

            </div>
            <div 
                class="px-8 py-12 flex flex-col justify-center flex-1 border-t sm:border-t-0 sm:border-l border-slate-200 dark:border-darkmode-300 border-dashed">
                <div class="text-slate-500 text-xs">Fecha del desemboloso</div>
                <div class="mt-1.5 flex items-center">
                    <div class="text-base">{{ formatear_fecha(get_desemboloso_request.created_at) }}</div> 
                </div>
                <div class="text-slate-500 text-xs mt-5">Codigo</div>
                <div class="mt-1.5 flex items-center">
                    <div class="text-base">{{ get_desemboloso_request.codigo }}</div> 
                </div> 
                <div class="mt-1.5 flex items-center">
                    <a name="" id="" class="btn btn-primary" :href="'/caja/'+get_desemboloso_request.urlcaja" role="button">  <Icon icon='link' class='mr-2' />  Ver caja </a>
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
            get_desemboloso_request: []
        }
    },
    watch: {
        month: function (newValue) {
            console.log("ejecutando un watch" + newValue);
        },
    },
    methods: {
        load_desemboloso() {
            return new Promise((resolve, reject) => {
                const data = {
                    solicitud_id: this.solicitud_id
                };
                const headers = this.headers;

                this.loading_start();

                Axios
                    .post("/load_desemboloso", data, { headers })
                    .then((response) => {
                        console.log(response.data);
                        this.loading_end();
                        if (response.data.success) {
                            this.alert_success(response.data.message);
                            resolve(response.data.data);
                        } else {
                            this.alert_warning(response.data.message);
                            reject(new Error(response.data.error));
                        }
                    })
                    .catch((error) => {
                        this.loading_end();
                        this.alert_error_modal("");
                        console.error(error);
                        reject(error);
                    });
            });
        },

    },
    created() {
        this.month_format = moment(this.month, 'MM-YYYY').format('MMMM/YYYY');
        this.month_format_data = moment(this.month, 'MM-YYYY');
    },
    mounted() {

        this.load_desemboloso().then((response) => {
            this.get_desemboloso_request = response;
        });
    }
}
</script>

<style></style>