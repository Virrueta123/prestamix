<template>
    <select ref="select_salario" name="salario_id" class="form-control"></select>
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
            select_salario: null,
            salario_id: null,
        }
    },
    mounted() {
        this.initSelect();
    },
    watch: {

    },
    methods: {
        async cargar_salario() {
            const data = {};

            const headers = this.headers;

            return Axios
                .post("/load_salario", data, {
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
        change_select_salario(event) {
            this.salario_id = event;
            this.$emit("comunicarSalario", event);
        },
        initSelect(data) {
            var self = this;
            this.select_salario = new TomSelect(this.$refs.select_salario, {
                valueField: 'urlapi',
                labelField: 'fecha_inicial',
                searchField: 'fecha_inicial',
                options: data,
                persist: false,
                plugins: {
                    'clear_button': {
                        'title': 'Eliminar la seleccion',
                    }
                },
                render: {

                    option: function (item, escape) {
                        return `<div class="box px-5 border border-primary  py-1 ml-2 mt-2 mr-2 flex-1  ">
                                                <div class="flex items-center">
                                                    <div class="font-medium text-primary"> 
                                                        Mes => ${ self.formatear_mes(item.fecha_inicio) }
                                                    </div> 
                                                </div>
                                                <div class="flex items-center">
                                                    <div class="font-medium text-primary">Salario : Fecha inicial => ${self.formatear_fecha(item.fecha_inicio)} |
                                                        Fecha final => ${self.formatear_fecha(item.fecha_final)}
                                                         </div> 
                                                </div>
                                                <div class="text-slate-500 mt-1 ">${item.monto}</div>
                                                <div class="text-slate-500 mt-1 ">${item.trabajador.name} - ${item.trabajador.lastname}</div>
                                                <div class="text-slate-500 mt-1 ">${item.trabajador.rol}</div>
                                            </div>`;
                    },
                    item: function (item, escape) {
                                            return `<div class="box px-5 border border-primary  py-1 ml-2 mt-2 mr-2 flex-1  ">
                                                <div class="flex items-center">
                                                    <div class="font-medium text-primary"> 
                                                        Mes => ${ self.formatear_mes(item.fecha_inicio) }
                                                    </div> 
                                                </div>
                                                <div class="flex items-center">
                                                    <div class="font-medium text-primary">Salario : Fecha inicial => ${self.formatear_fecha(item.fecha_inicio)} |
                                                        Fecha final => ${self.formatear_fecha(item.fecha_final)}
                                                         </div> 
                                                </div>
                                                <div class="text-slate-500 mt-1 ">${item.monto}</div>
                                                <div class="text-slate-500 mt-1 ">${item.trabajador.name} - ${item.trabajador.lastname}</div>
                                                <div class="text-slate-500 mt-1 ">${item.trabajador.rol}</div>
                                            </div>`;
                    }
                }
            });

            if (this.selected != "") {
                this.select_salario.setValue(this.selected);
            }

            this.select_salario.on('change', this.change_select_salario);
        }


    },
    mounted() {
        this.cargar_salario().then((response) => {
            if (response.success) {
                this.initSelect(response.data)
            } else {
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