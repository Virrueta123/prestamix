<template>

    <div class="intro-y box mt-5">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
            <h2 class="font-medium text-base mr-auto text-primary">
                <Icon icon='table-list' class='mr-2' /> Formulario para crear un tipo de gasto
            </h2>
        </div>

        <form id="form_crear_tipo_gasto" method="POST" action="#">
            <div id="vertical-form" class="p-5">
                <div class="">

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-12">
                            <label for="vertical-form-2" class="form-label">Nombre </label>
                            <input name="nombre" v-model="nombre" type="text" class="form-control"
                                placeholder="Nombre">
                        </div> 
                    </div>

                     <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-12">
                            <label for="vertical-form-2" class="form-label">Descripcion </label>
                            
                                <Textarea name="descripcion" v-model="descripcion"  class="form-control"
                                placeholder="Descripcion" rows="5" cols="30" />
                        </div> 
                    </div>

                 
                    <div id="basic-button" class="p-1 mt-4">
                        <div class="preview">
                            <button type="submit" class="btn btn-primary mr-1 mb-2">
                                <Icon icon="plus" class="ml-2" /> Registrar tipo de gasto
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
            nombre: "",
            descripcion: "",  
        }
    },
    methods: {
        // Your methods here
        crear_tipo_gasto() {
            const data = {
                nombre: this.nombre, 
                descripcion: this.descripcion, 
            };

            const headers = this.headers;

            this.loading_start();

            Axios
                .post("/tipo_gasto", data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {
                        window.location.assign(response.data.data);
                    } else {
                        this.alert_warning(response.data.message);
                    }
                    this.loading_end();
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor");
                    console.error(error);
                });
        }
    },
    mounted() {

        var self = this;

        $("#form_crear_tipo_gasto").validate({
            rules: {
                descripcion: {
                    required: true,
                    noSpecial:true
                },
                nombre: {
                    required: true,
                    noSpecial:true
                },
            },
            submitHandler: function (form) {
                 self.crear_tipo_gasto();
                return false;
            }
        });

    }
}
</script>

<style></style>