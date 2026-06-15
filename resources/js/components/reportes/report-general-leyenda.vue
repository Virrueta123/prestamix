<template>
    <div class="col-span-12 mt-8">
        <div class="intro-y flex items-center h-10">
            <h2 class="text-lg font-medium truncate mr-5">
                Reporte Cuotas -- general
            </h2>
            <button href="" class="ml-auto flex btn btn-primary " @click="load_leyenda()">
                <Icon icon='rotate-right' class='mr-2' />
            </button>
        </div>
        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex mt-5">
                            <i class="fa fa-arrows-down-to-people fa-2xl fila-roja_font"></i>
                        </div>
                        <div class="text-3xl font-medium leading-8 mt-6">{{ get_leyenda.cliente_moroso }}</div>
                        <div class="text-base text-slate-500 mt-1">Clientes Morosos</div>
                    </div>
                </div>
            </div>
            <div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex mt-5">
                            <i class="fa fa-calendar-check fa-2xl fila-amarilla_font"></i>
                        </div>
                        <div class="text-3xl font-medium leading-8 mt-6">{{ get_leyenda.cuota_hoy }}</div>
                        <div class="text-base text-slate-500 mt-1">Cuotas vence hoy</div>
                    </div>
                </div>
            </div>
            <div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y">
                <div class="report-box zoom-in">
                    <div class="box p-5">
                        <div class="flex mt-5">
                            <i class="fa fa-calendar-days fa-2xl fila-pendiente_font"></i>
                        </div>
                        <div class="text-3xl font-medium leading-8 mt-6">{{ get_leyenda.cuota_pendientes }}</div>
                        <div class="text-base text-slate-500 mt-1">Cuotas Pendientes</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-span-12 lg:col-span-12 mt-8">
        <div class="intro-y block sm:flex items-center h-10">
            <h2 class="text-lg font-medium truncate mr-5">
                Reporte en barra
            </h2>
        </div>
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
            options: {
                // Data: Data to be displayed in the chart
                data: [
                    { asset: "cargando", amount: 10000 },
                    { asset: "cargando", amount: 10000 },
                ],
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
                theme: {
                    baseTheme: "ag-default",
                    palette: {
                        fills: ["#DD0506", "#FF8000", "#0083D0" ],
                        strokes: ["#2E3192"],
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
                                    color: "black",
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
            const data = {};

            const headers = this.headers;

            Axios
                .post("/load_leyenda", data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {
                        this.get_leyenda = response.data.data
                        this.options.data = [
                            { asset: "cliente_moroso", amount: this.get_leyenda.cliente_moroso, },
                            { asset: "cuota_hoy", amount: this.get_leyenda.cuota_hoy, },
                            { asset: "cuota_pendientes", amount: this.get_leyenda.cuota_pendientes, },
                        ];
                    } else {
                        this.alert_warning(response.data.message);
                    }

                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor, ingrese los nombres y apellidos manualmente");
                    console.error(error);
                });
        }
    },
    mounted() {
        this.load_leyenda();
    }
}
</script>

<style></style>