<template>
    <div class="tab-content mt-6">
        <div class="card flex justify-content-center">
            <ProgressSpinner id="p_loading_r_a_c" style="width: 50px; height: 50px" strokeWidth="8"
                fill="var(--surface-ground)" animationDuration=".5s" aria-label="Custom ProgressSpinner" />
        </div>
        <div style="display: none;" id="e_loading_r_a_c" class="tab-pane active" role="tabpanel"
            aria-labelledby="active-users-tab">
            <div class="box  ml-4 mr-4"><ag-charts-vue :options="options"> </ag-charts-vue></div>
            
            <div class="mx-auto w-10/12 2xl:w-2/3 mt-8">
                <div v-for="(d_f, index_d_f) in data_office" :key="index_d_f" class="flex items-center">
                    <div class="w-2 h-2 bg-primary rounded-full mr-3"></div>
                    <span class="truncate">{{ d_f.asset }}</span> <span class="font-medium ml-auto"> {{
                formatear_dinero_soles(d_f.amount) }}</span>
                </div>
            </div>
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
    mixins: [myMixin],
    data() {
        return {
            get_caja: this.$attrs.get_caja,
            data_office: null,
            options: {
                // Data: Data to be displayed in the chart
                data: [
                    { asset: "cargando", amount: 60000 },
                    { asset: "cargando", amount: 40000 },
                ],
                zoom: {
                    enabled: true,
                    axes: "xy",
                    anchorPointX: "pointer",
                    anchorPointY: "pointer",
                },
                // Series: Defines which chart type and data to use
                series: [

                    {
                        type: 'pie',
                        angleKey: 'amount',
                        calloutLabelKey: 'asset',
                        sectorLabelKey: 'amount',
                        sectorLabel: {
                            color: 'white',
                            fontWeight: 'bold',
                        },
                    },
                ],
            },
        };
    },
    mounted() {
        this.load_analista_caja().then((response) => {
            this.options.data = response.response_analistas;
            this.data_office = response.response_analistas_office;
            $("#e_loading_r_a_c").fadeIn()
            $("#p_loading_r_a_c").fadeOut()
        }).catch((err) => {

        });
        // this.options.data = [{ asset: "Stocks3", amount: 160000 },
        // { asset: "Bonds2", amount: 430000 }]
    },
    methods: {
        async load_analista_caja() {
            const data = {
                urlapi: this.get_caja.urlapi
            };

            const headers = this.headers;

            this.loading_start();

            return Axios
                .post("/report_analista_caja", data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {
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