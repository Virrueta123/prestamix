<template>

    <div class="intro-y box mt-5">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
            <h2 class="font-medium text-base mr-auto text-primary">
                <Icon icon='table-list' class='mr-2' /> Formulario para crear un usuario
            </h2>
        </div>

        <form id="form_crear_usuario" method="POST" action="#">
            <div id="vertical-form" class="p-5">
                <div class="">

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Nombre </label>
                            <input name="name" v-model="name" type="text" class="form-control" placeholder="Nombre">
                        </div>
                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Apellido </label>
                            <input name="lastname" v-model="lastname" type="text" class="form-control"
                                placeholder="Apellido">
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Celular </label>
                            <input name="celular" v-model="celular" type="number" class="form-control"
                                placeholder="Celular">
                        </div>
                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Dni </label>
                            <input name="dni" v-model="dni" type="number" class="form-control" placeholder="Dni">
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label>Sexo</label>
                            <div class="flex flex-col sm:flex-row mt-5">

                                <div class="form-check mr-2 sm:mt-0">
                                    <input id="M" class="form-check-input" type="radio" name="sexo" checked value="M"
                                        v-model="sexo">
                                    <label class="form-check-label" for="M">Masculino</label>
                                </div>
                                <div class="form-check mr-2 mt-2 sm:mt-0">
                                    <input id="F" class="form-check-input" type="radio" name="sexo" value="F"
                                        v-model="sexo">
                                    <label class="form-check-label" for="F">Femenino</label>
                                </div>
                            </div>
                        </div>
                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Email </label>
                            <input name="email" v-model="email" type="email" class="form-control" placeholder="Email">
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Email </label>
                            <select class="form-select sm:mr-2" v-model="rol" name="rol">
                                <option value="gerente">gerente</option>
                                <option value="recepcionista" selected>recepcionista</option>
                                <option value="analista">analista</option>
                            </select>
                        </div>
                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Sucursal </label>
                            <select-sucursal @comunicarSucursal="escucharSucursal"></select-sucursal>
                        </div>
                    </div>

                    <div id="basic-button" class="p-1 mt-4">
                        <div class="preview">
                            <button type="submit" class="btn btn-primary mr-1 mb-2">
                                <Icon icon="plus" class="ml-2" /> Registrar usuario
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
            name: "",
            lastname: "",
            celular: "",
            dni: "",
            sexo: "M",
            email: "",
            sucursal_id: null,
            rol: "recepcionista"
        }
    },
    methods: {
        escucharSucursal(data) {
            this.sucursal_id = data;
        },
        // Your methods here
        crear_usuario() {
            const data = {
                name: this.name,
                lastname: this.lastname,
                celular: this.celular,
                dni: this.dni,
                sexo: this.sexo,
                email: this.email,
                sucursal_id: this.sucursal_id,
                rol: this.rol
            };

            const headers = this.headers;

            this.loading_start();

            Axios
                .post("/usuario", data, {
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

        $("#form_crear_usuario").validate({
            rules: {
                name: {
                    required: true,
                    noSpecial: true
                },
                lastname: {
                    required: true,
                    noSpecial: true
                },
                celular: {
                    required: true
                },
                dni: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                sucursal_id: {
                    required: true
                }
            },
            submitHandler: function (form) {
                self.crear_usuario();
                return false;
            }
        });

    }
}
</script>

<style></style>