<template>
    <div class="col-span-12 mt-8">
        <div class="intro-y flex items-center h-10">
            <h2 class="text-lg font-medium truncate mr-5">
                Reporte -- general
            </h2>
            <button href="" class="ml-auto flex btn btn-primary " @click="load_leyenda()">
                <Icon icon='rotate-right' class='mr-2' />
            </button>
        </div>

    </div>

    <div class="col-span-12 lg:col-span-12 mt-8">
        <div class="intro-y box p-5 mt-12 sm:mt-5">
            <ag-charts-vue :options="options"> </ag-charts-vue>
        </div>
    </div>
</template>

<script>
import Axios from 'axios'
export default {
    data() {
        return {
            get_leyenda: [],
            montos_reporte: this.$attrs.montos_reporte,
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
        }
    },
    methods: {
        load_leyenda() {
            this.options.data = [
                { asset: "cliente moroso", amount: this.montos_reporte.sum_moroso, },
                { asset: "cuota hoy", amount: this.montos_reporte.sum_hoy, },
                { asset: "cuota pendientes", amount: this.montos_reporte.sum_pendientes, },
            ];
        }
    },
    created() {

        this.load_leyenda();
    },
    mounted() {

    }
}
</script>

<style></style>