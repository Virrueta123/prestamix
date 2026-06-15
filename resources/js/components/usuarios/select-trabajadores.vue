<template>
    <select ref="select_trabajadores"   name="trabajador_id"  class="form-control"></select>
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
            select_trabajadores: null,
            trabajador_id: null,
        }
    },
    mounted() {
        this.initSelect();
    },
    watch: {
         
    },
    methods: {
        async cargar_trabajadores() {
            const data = {};

            const headers = this.headers;

            return Axios
                .post("/load_trabajadores", data, {
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
        change_select_trabajadores(event) {
            this.trabajador_id = event;
            this.$emit("comunicarTrabajador", event);
        },
        initSelect(data) {

            this.select_trabajadores = new TomSelect(this.$refs.select_trabajadores, {
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
                        return `<div class="box px-5 border border-primary  py-1 ml-2 mt-2 mr-2 flex-1  ">
                                                <div class="flex items-center">
                                                    <div class="font-medium text-primary">${item.name} - ${item.lastname}</div> 
                                                </div>
                                                <div class="text-slate-500 mt-1 ">${item.rol}</div>
                                            </div>`;
                    },
                    item: function (item, escape) {
                        return `<div class="box px-5   py-1 ml-1 flex-1 ">
                                                <div class="flex items-center">
                                                    <div class="font-medium text-primary">${item.name} - ${item.lastname}</div> 
                                                </div>
                                               
                                            </div>`;
                    }
                }
            });

            if(this.selected != "" ){ 
                this.select_trabajadores.setValue(this.selected);
            }

            this.select_trabajadores.on('change', this.change_select_trabajadores);
        }


    },
    mounted() {
        this.cargar_trabajadores().then((response) => {
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