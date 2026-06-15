<template>

    <div>
        <div class="col-span-12 ">
            <div class="intro-y flex items-center h-10">
                <h2 class="text-lg font-medium truncate mr-5">
                    Reporte -- {{ month_format }}
                </h2>
                <button href="" class="ml-auto flex btn btn-primary " @click="load()">
                    <Icon icon='rotate-right' class='mr-2' />
                </button>
            </div>

        </div>

        <hr class="mb-4 mt-4">

        <h3>Reporte general del mes</h3>
        <div id="month" style="width: 100%; height: 450px;"></div>

        <h3>Reporte por dia</h3>
        <div id="main" style="width: 100%; height: 800px;"></div>

        <h3>Reporte de categorias</h3>
        <div id="bar_categories" style="width: 100%; height: 800px;"></div>
    </div>
</template>

<script>
import Axios from 'axios'

// mixin
import {
    myMixin
} from "../../../mixin.js";
import { forEach } from 'jszip';
import moment from 'moment';

import * as echarts from 'echarts';


export default {
    mixins: [myMixin],

    data() {
        return {
            month: this.$attrs.by_month,
            month_format: null,
            month_format_data: null
        }
    },
    watch: {
        month: function (newValue) {
            console.log("ejecutando un watch" + newValue);
        },
    },
    methods: {

        load_data_by_month() {
            return new Promise((resolve, reject) => {
                const data = {
                    month: this.month
                };
                const headers = this.headers;

                this.loading_start();

                Axios
                    .post("/load_data_by_month", data, { headers })
                    .then((response) => {
                        console.log("reporte general", response.data);
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
        load_chart_general(data) {
            var dom = document.getElementById('month');
            var myChart = echarts.init(dom, null, {
                renderer: 'canvas',
                useDirtyRect: false
            });
            var app = {};

            var option;

            // This example requires ECharts v5.5.0 or later
            option = {
                tooltip: {
                    trigger: 'item'
                },
                legend: {
                    top: '5%',
                    left: 'center'
                },
                series: [
                    {
                        name: 'Access From',
                        type: 'pie',
                        radius: ['40%', '70%'],
                        center: ['50%', '70%'],
                        // adjust the start and end angle
                        startAngle: 180,
                        endAngle: 360,
                        data: [
                            data.data_ingresos_general,
                            data.data_egresos_general,
                            data.data_desembolso_general
                        ]

                    }
                ]
            };

            myChart.on('updateAxisPointer', function (event) {
                const xAxisInfo = event.axesInfo[0];
                if (xAxisInfo) {
                    const dimension = xAxisInfo.value + 1;
                    myChart.setOption({
                        series: {
                            id: 'pie',
                            label: {
                                formatter: '{b}: {@[' + dimension + ']} ({d}%)'
                            },
                            encode: {
                                value: dimension,
                                tooltip: dimension
                            }
                        }
                    });
                }
            });
            myChart.setOption(option);

            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }

            window.addEventListener('resize', myChart.resize);
        },
        load_chart(data) {
            var dom = document.getElementById('main');
            var myChart = echarts.init(dom, null, {
                renderer: 'canvas',
                useDirtyRect: false
            });
            var app = {};

            var option;

            setTimeout(function () {
                option = {
                    legend: {},
                    tooltip: {
                        trigger: 'axis',
                        showContent: true
                    },
                    dataset: {
                        source: [
                            data.fechas,
                            data.data_ingresos,
                            data.data_egresos,
                            data.data_desembolso
                        ]
                    },
                    xAxis: { type: 'category' },
                    yAxis: { gridIndex: 0 },
                    grid: { top: '55%' },
                    series: [
                        {
                            type: 'line',
                            smooth: true,
                            seriesLayoutBy: 'row',
                            emphasis: { focus: 'series' }
                        },
                        {
                            type: 'line',
                            smooth: true,
                            seriesLayoutBy: 'row',
                            emphasis: { focus: 'series' }
                        },

                        {
                            type: 'line',
                            smooth: true,
                            seriesLayoutBy: 'row',
                            emphasis: { focus: 'series' }
                        },

                        {
                            type: 'pie',
                            id: 'pie',
                            radius: '30%',
                            center: ['50%', '25%'],
                            emphasis: {
                                focus: 'self'
                            },
                            label: {
                                formatter: '{b}: {@2012} ({d}%)'
                            },
                            encode: {
                                itemName: 'product',
                                value: '2012',
                                tooltip: '2012'
                            }
                        }
                    ]
                };
                myChart.on('updateAxisPointer', function (event) {
                    const xAxisInfo = event.axesInfo[0];
                    if (xAxisInfo) {
                        const dimension = xAxisInfo.value + 1;
                        myChart.setOption({
                            series: {
                                id: 'pie',
                                label: {
                                    formatter: '{b}: {@[' + dimension + ']} ({d}%)'
                                },
                                encode: {
                                    value: dimension,
                                    tooltip: dimension
                                }
                            }
                        });
                    }
                });
                myChart.setOption(option);
                myChart.on('click', 'series', function () {
                    console.log("evento series");

                });
                myChart.on('click', 'series.line', function () {
                    console.log("evento series.line");

                });
                myChart.on('click', 'dataZoom', function () {
                    console.log("evento dataZoom");

                });
                myChart.on('click', 'xAxis.category', function () {
                    console.log("evento category");

                });
            });

            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }

            window.addEventListener('resize', myChart.resize);
        },
        load_bar_categories(data) {
            var dom = document.getElementById('bar_categories');
            var myChart = echarts.init(dom, null, {
                renderer: 'canvas',
                useDirtyRect: false
            });
            var app = {};

            var option;

            const builderJson = {
                all: 10887,
                charts: {
                    bar: 7561,
                }
            };
            const downloadJson = {
                'echarts.min.js': 17365,
                'echarts.simple.min.js': 4079,
                'echarts.common.min.js': 6929,
                'echarts.js': 14890
            };
        
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            canvas.width = canvas.height = 100;
        
            option = {
                backgroundColor: {
                    type: 'pattern',
                    image: canvas,
                    repeat: 'repeat'
                },
                tooltip: {},
                title: [
                    {
                        text: 'Reporte general del mes de ',
                        left: 'center',
                        textAlign: 'center'
                    }
                ],
                grid: {
                    top: 50,
                    bottom: '50%',
                    left: 10,
                    containLabel: true
                },
                xAxis: {
                    type: 'value',
                    max: builderJson.all,
                    splitLine: {
                        show: false
                    }
                },
                yAxis: {
                    type: 'category',
                    data: Object.keys(data.data_gastos_por_tipo),
                    axisLabel: {
                        interval: 0,
                        rotate: 30
                    },
                    splitLine: {
                        show: false
                    }
                },
                series: [
                    {
                        type: 'bar',
                        label: {
                            position: 'right',
                            show: true
                        },
                        data: Object.keys(data.data_gastos_por_tipo).map(function (key) {
                            return data.data_gastos_por_tipo[key];
                        })
                    } 
                ]
            };

            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }

            window.addEventListener('resize', myChart.resize);
        }
    },
    created() {
        this.month_format = moment(this.month, 'MM-YYYY').format('MMMM/YYYY');
        this.month_format_data = moment(this.month, 'MM-YYYY');
    },
    mounted() {

        this.load_chart();

        this.load_data_by_month().then((response) => {
            console.log("reporte general: ", response);

            this.load_chart_general(response);
            this.load_chart(response);
            this.load_bar_categories(response);
        });
    }
}
</script>

<style></style>