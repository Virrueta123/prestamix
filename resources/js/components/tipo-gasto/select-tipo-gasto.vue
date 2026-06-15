<template>
    <select ref="select_gasto" v-model="tipo_gasto_id" name="tipo_gasto_id"  class="form-control"></select>
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
            select_gasto: null,
            tipo_gasto_id: null,
        }
    },
    mounted() {
        this.initSelect();
    },
    watch: {
         
    },
    methods: {
        async cargar_tipo_gasto() {
            const data = {};

            const headers = this.headers;

            return Axios
                .post("/load_tipo_de_gato", data, {
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
        change_select_gasto(event) {
            this.tipo_gasto_id = event;
            
            this.$emit("comunicarTipoGasto", event);
        },
        initSelect(data) {

            this.select_gasto = new TomSelect(this.$refs.select_gasto, {
                valueField: 'urlapi',
                labelField: 'nombre',
                searchField: 'nombre',
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
                                                    <div class="font-medium text-primary">${item.nombre}</div> 
                                                </div>
                                                <div class="text-slate-500 mt-1 ">${item.descripcion}</div>
                                            </div>`;
                    },
                    item: function (item, escape) {
                        return `<div class="box px-5   py-1 ml-1 flex-1 ">
                                                <div class="flex items-center">
                                                    <div class="font-medium text-primary">${item.nombre}</div> 
                                                </div>
                                                <div class="text-slate-500 mt-1">${item.descripcion}</div>
                                            </div>`;
                    }
                }
            });

            if(this.selected != "" ){ 
                this.select_gasto.setValue(this.selected);
            }

            this.select_gasto.on('change', this.change_select_gasto);
        }


    },
    mounted() {
        this.cargar_tipo_gasto().then((response) => {
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