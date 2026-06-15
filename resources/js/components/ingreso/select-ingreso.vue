<template>
    <ProgressSpinner id="pre_select_ingresos" style="width: 50px; height: 50px" strokeWidth="8"
        fill="var(--surface-ground)" animationDuration=".5s" aria-label="Custom ProgressSpinner" />

    <select style="display: none;" id="select_ingresos" ref="select_ingresos" name="ingreso_id"
        class="form-control"></select>
</template>

<script>
import $ from 'jquery';
import 'tom-select/dist/css/tom-select.css';
import TomSelect from 'tom-select';
// mixin
import {
    myMixin
} from "../../mixin.js";

import Axios from 'axios';


export default {
    mixins: [myMixin],
    data() {
        return {
            selected: this.$attrs.selected || '',
            select_ingresos: null,
            ingreso_id: null,
        }
    },
    mounted() {
        this.initSelect();
    },
    watch: {

    },
    methods: {
        async cargar_ingresos() {
            const data = {};

            const headers = this.headers;

            return Axios
                .post("/load_ingresos_cliente", data, {
                    headers,
                })
                .then((response) => {
                    return response.data;
                })
                .catch((error) => {
                    this.alert_error_modal("Error en el servidor");
                    console.error(error);
                    return {
                        message: "Error al cargar los datos, recargar el navegador",
                        success: false
                    }
                });
        },
        change_select_ingresos(event) {
            this.ingreso_id = event;
            this.$emit("comunicarTrabajador", event);
        },
        initSelect(data) {

            this.select_ingresos = new TomSelect(this.$refs.select_ingresos, {
                valueField: 'urlapi',
                labelField: 'descripcion',
                searchField: 'descripcion',
                options: data,
                persist: false,
                plugins: {
                    'clear_button': {
                        'title': 'Eliminar la seleccion',
                    }
                },
                render: {
                    option: function (item, escape) {
                        return `<div class="box px-4 py-4 mb-3 flex items-center zoom-in ">
                                            <div class="w-10 h-10 flex-none mt-4  ">
                                                <i class="fa-solid fa-piggy-bank text-primary mt-2 fa-2xl"></i>
                                            </div> 
                                            <div class="ml-4 mr-auto">
                                                <div class="font-medium">${item.descripcion} - ${item.monto}</div>
                                                <div class="text-slate-500 text-xs mt-0.5">${item.cliente.cli_nombre} ${item.cliente.cli_apellido}</div>
                                            </div>
                                            `;
                    },
                    item: function (item, escape) {
                        return `<div class="box px-5 border border-primary  py-1 ml-2 mt-2 mr-2 flex-1  ">
                                                <i class="fa-solid fa-piggy-bank text-primary mt-2 fa-1xl"></i>
                                                <div class="flex items-center">
                                                    <div class="font-medium text-primary">${item.descripcion} - ${item.monto}</div> 
                                                </div>
                                                <div class="text-slate-500 mt-1 ">${item.cliente.cli_nombre} ${item.cliente.cli_apellido}</div>
                                            </div>`;
                    }
                }
            });

            if (this.selected != "") {
                this.select_ingresos.setValue(this.selected);
            }

            this.select_ingresos.on('change', this.change_select_ingresos);
        }


    },
    mounted() {
        this.cargar_ingresos().then((response) => {
            if (response.success) {
                this.initSelect(response.data)
                $("#pre_select_ingresos").fadeOut();
                $("#select_ingresos").fadeIn();
            } else {
                console.log(response.message);
                this.alert_error_modal("Error en el servidor");
            }
        }).catch((err) => {
            this.alert_error_modal("Error al cargar los datos, recargar el navegador");
            console.log(err);
        });
    },
}
</script>

<style></style>