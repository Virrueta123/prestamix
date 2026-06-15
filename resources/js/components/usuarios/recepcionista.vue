<template>
    <select ref="select_recepcionista" v-model="recepcionista" name="recepcionista"  class="form-control"></select>
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
            select_recepcionista: null,
            recepcionista: null,
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
                .post("/load_recepcionista", data, {
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
        change_select_recepcionista(event) {
            this.recepcionista = event;
            this.$emit("comunicarTipoGasto", event);
        },
        initSelect(data) {

            this.select_recepcionista = new TomSelect(this.$refs.select_recepcionista, {
                valueField: 'urlapi',
                labelField: 'name',
                searchField: 'name',
                options: data,
                persist: false,
                plugins: {
                    'clear_button': {
                        'title': 'Eliminar la seleccion',
                    }
                },
                render: {
                    option: function (item, escape) {
                        return `<div class="intro-x">
                                                <div class="box px-4 py-4 mb-1 flex items-center ">
                                                    <div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">
                                                        <img alt="Midone - HTML Admin Template" src="../../dist/images/avatar/${item.sexo == "M" ? "masculino" : "femenino"}.png">
                                                    </div>
                                                    <div class="ml-4 mr-auto">
                                                        <div class="font-medium">${item.name} | ${item.lastname}</div>
                                                        <div class="text-slate-500 text-xs mt-0.5">Dni  ${item.dni} </div>
                                                    </div>
                                                    
                                                </div>
                                            </div>`;
                    },
                    item: function (item, escape) {
                        return `<div class="intro-x">
                                                <div class="box px-4 py-4 mb-1 flex items-center ">
                                                    <div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">
                                                        <img alt="Midone - HTML Admin Template" src="../../dist/images/avatar/${item.sexo == "M" ? "masculino" : "femenino"}.png">
                                                    </div>
                                                    <div class="ml-4 mr-auto">
                                                        <div class="font-medium">${item.name} | ${item.lastname}</div>
                                                        <div class="text-slate-500 text-xs mt-0.5">Dni ${item.dni} </div>
                                                    </div>
                                                    
                                                </div>
                                            </div>`;
                    }
                }
            });

            if(this.selected != "" ){ 
                this.select_recepcionista.setValue(this.selected);
            }

            this.select_recepcionista.on('change', this.change_select_recepcionista);
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