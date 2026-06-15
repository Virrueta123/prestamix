<template>

    <div id="select-options" class="p-5">

        <div class="col-span-12 ">
            <div class="intro-y flex items-center h-10">
                <h2 class="text-lg font-medium truncate mr-5">
                    Reporte -- general
                </h2>
                <button href="" class="ml-auto flex btn btn-primary " @click="load()">
                    <Icon icon='rotate-right' class='mr-2' />
                </button>
            </div>

        </div>

        <hr class="mb-4 mt-4">

        <div id="main" style="width: 100%; height: 800px;"></div>

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
            get_leyenda: [], 
            year: this.$attrs.year
        }
    },
    watch: {

    },
    methods: {
        load() {
            this.load_data().then((response) => {
                console.log(response);
                this.load_chart(response);
            });
        },
        load_chart(data) {
            var dom = document.getElementById('main');
            var myChart = echarts.init(dom, null, {
                renderer: 'canvas',
                useDirtyRect: false
            });
            var app = {};

            var option;

            const posList = [
                'left',
                'right',
                'top',
                'bottom',
                'inside',
                'insideTop',
                'insideLeft',
                'insideRight',
                'insideBottom',
                'insideTopLeft',
                'insideTopRight',
                'insideBottomLeft',
                'insideBottomRight'
            ];
            app.configParameters = {
                rotate: {
                    min: -90,
                    max: 90
                },
                align: {
                    options: {
                        left: 'left',
                        center: 'center',
                        right: 'right'
                    }
                },
                verticalAlign: {
                    options: {
                        top: 'top',
                        middle: 'middle',
                        bottom: 'bottom'
                    }
                },
                position: {
                    options: posList.reduce(function (map, pos) {
                        map[pos] = pos;
                        return map;
                    }, {})
                },
                distance: {
                    min: 0,
                    max: 100
                }
            };
            app.config = {
                rotate: 90,
                align: 'left',
                verticalAlign: 'middle',
                position: 'insideBottom',
                distance: 15,
                onChange: function () {
                    const labelOption = {
                        rotate: app.config.rotate,
                        align: app.config.align,
                        verticalAlign: app.config.verticalAlign,
                        position: app.config.position,
                        distance: app.config.distance
                    };
                    myChart.setOption({
                        series: [
                            {
                                label: labelOption
                            },
                            {
                                label: labelOption
                            },
                            {
                                label: labelOption
                            },
                            {
                                label: labelOption
                            }
                        ]
                    });
                }
            };
            const labelOption = {
                show: true,
                position: app.config.position,
                distance: app.config.distance,
                align: app.config.align,
                verticalAlign: app.config.verticalAlign,
                rotate: app.config.rotate,
                formatter: '{c}  {name|{a}}',
                fontSize: 16,
                rich: {
                    name: {}
                }
            };

            option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                legend: {
                    data: ['Ingresos', 'Gastos', 'Desembolsos']
                },
                toolbox: {
                    show: true,
                    orient: 'vertical',
                    left: 'right',
                    top: 'center',
                    feature: {
                        mark: { show: true },
                        dataView: { show: true, readOnly: false },
                        magicType: { show: true, type: ['line', 'bar', 'stack'] },
                        restore: { show: true },
                        saveAsImage: { show: true }
                    }
                },
                xAxis: [
                    {
                        type: 'category',
                        axisTick: { show: false },
                        data: data.meses
                    }
                ],
                yAxis: [
                    {
                        type: 'value'
                    }
                ],
                series: [
                    {
                        name: 'Ingresos',
                        type: 'bar',
                        barGap: 0,
                        label: labelOption,
                        emphasis: {
                            focus: 'series'
                        },itemStyle: {
                            color: '#105198' // Personaliza el color de las barras de "Ingresos"
                        },
                        data: data.data_ingresos
                    },
                    {
                        name: 'Gastos',
                        type: 'bar',
                        label: labelOption,
                        emphasis: {
                            focus: 'series'
                        },
                        itemStyle: {
                            color: '#FF0000' // Personaliza el color de las barras de "Ingresos"
                        },
                        data: data.data_egresos
                    },
                    {
                        name: 'Desembolsos',
                        type: 'bar',
                        label: labelOption,
                        emphasis: {
                            focus: 'series'
                        },
                        itemStyle: {
                            color: '#FC9A03' // Personaliza el color de las barras de "Ingresos"
                        },
                        data: data.data_desembolso
                    }
                ]
            };


            if (option && typeof option === 'object') {
                myChart.setOption(option);
            }

            window.addEventListener('resize', myChart.resize);

        },
        load_data() {
            return new Promise((resolve, reject) => {
                const data = {
                    year: this.year
                };
                const headers = this.headers;

                this.loading_start();

                Axios
                    .post("/load_data_by_year", data, { headers })
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
        }
    },
    created() {

    },
    mounted() {
        this.load();
    }
}
</script>

<style></style>