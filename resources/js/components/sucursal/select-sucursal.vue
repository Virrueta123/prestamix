<template>
    <select ref="select_sucursal" v-model="sucursal_id" name="sucursal_id"  class="form-control"></select>
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
            select_sucursal: null,
            sucursal_id: null,
        }
    },
    mounted() {
        this.initSelect();
    },
    watch: {
         
    },
    methods: {
        async cargar_sucursal() {
            const data = {};

            const headers = this.headers;

            return Axios
                .post("/load_sucursal", data, {
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
        change_select_sucursal(event) {
            this.sucursal_id = event;
            this.$emit("comunicarSucursal", event);
        },
        initSelect(data) {

            this.select_sucursal = new TomSelect(this.$refs.select_sucursal, {
                valueField: 'urlapi',
                labelField: 'sucursal_nombre',
                searchField: 'sucursal_nombre',
                options: data, 
                render: {
                    option: function (item, escape) {
                        return `<div class="box px-5 border border-primary  py-1 ml-2 mt-2 mr-2 flex-1  ">
                                                <div class="flex items-center">
                                                    <i class="fa fa-building mr-2 text-primary"></i>
                                                    <div class="font-medium text-primary">${item.sucursal_nombre}</div> 
                                                </div> 
                                            </div>`;
                    },
                    item: function (item, escape) {
                        return `<div class="box px-5   py-1 ml-1 flex-1 ">
                                                <div class="flex items-center">
                                                    <i class="fa fa-building mr-2 text-primary"></i>
                                                    <div class="font-medium text-primary">${item.sucursal_nombre}</div> 
                                                </div> 
                                            </div>`;
                    }
                }
            });

            if(this.selected != "" ){ 
                this.select_sucursal.setValue(this.selected);
            }

            this.select_sucursal.on('change', this.change_select_sucursal);
        }


    },
    mounted() {
        this.cargar_sucursal().then((response) => {
            if (response.success) {
                this.initSelect(response.data) 
            } else {
                this.alert_error_modal(response.message);
            }
        }).catch((err) => {
            this.alert_error_modal("Error al cargar los datos, recargar el navegador");
            console.log(err);
        });
    },
}
</script>

<style></style>