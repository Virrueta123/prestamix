<template>

    <div class="intro-y box mt-5">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
            <h2 class="font-medium text-base mr-auto text-primary">
                <Icon icon='table-list' class='mr-2' /> Formulario para crear una caja </h2>
        </div>

        <form id="form_crear_cuenta" method="POST" action="#">
            <div id="vertical-form" class="p-5">
                <div class="">

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Apertura caja </label>
                            <InputNumber  class="form-control p-2 border border-success" v-model="saldo_inicial" placeholder="Saldo inicial" inputId="locale-us" locale="en-US" :minFractionDigits="2" />
                                
                        </div>

                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label"> Referencia </label>
                            <input name="referencia" v-model="referencia" type="text" class="form-control"
                                placeholder="Referencia">
                        </div>
                    </div> 
                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Recepcionista </label>
                            <recepcionista v-on:change="change_recepcionista($event)"></recepcionista>
                                
                        </div> 
                    </div> 


                    
                    <div id="basic-button" class="p-1 mt-4">
                        <div class="preview">
                            <button type="submit" class="btn btn-primary mr-1 mb-2">
                                <Icon icon="plus" class="ml-2" /> Crear una caja
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>

</template>

<script>
import $ from 'jquery';

import 'tom-select/dist/css/tom-select.css';
import TomSelect from 'tom-select';
import Axios from 'axios';

import 'jquery-validation';
import 'jquery-validation/dist/localization/messages_es';

import moment from 'moment';
import 'moment/locale/es';

// mixin
import {
    myMixin
} from "../../mixin.js";

export default {
    mixins: [myMixin], 
    data() {
        return {
            saldo_inicial: "",  
            referencia: "",
            recepcionista_id:""
        }
    },
    methods: {
        change_recepcionista(event){
            this.recepcionista_id = event.target.value;
        },
        // Your methods here
        crear_cuenta() {
            const data = {
                saldo_inicial: this.saldo_inicial,
                referencia: this.referencia, 
                recepcionista_id: this.recepcionista_id, 
            };

            const headers = this.headers;

            this.loading_start();

            Axios
                .post("/caja", data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) { 
                        window.location.assign( response.data.data );
                    } else {
                        this.alert_warning(response.data.message);
                    }
                    this.loading_end();
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor ");
                    console.error(error);
                });
        }
    },
    mounted() {

        var self = this;

        $("#form_crear_cuenta").validate({
            rules: {
                saldo_inicial: {
                    required: true
                },
                referencia: {
                    required: true
                },
                recepcionista: {
                    required: true
                }
            },
            submitHandler: function (form) {
                self.crear_cuenta();
                return false;
            }
        });

    }
}
</script>

<style></style>