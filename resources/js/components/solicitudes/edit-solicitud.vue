<template>
    <div class=" box">

        <form id="editar_solicitud" action="#" method="POST">


            <div class="grid grid-cols-12 gap-2 mt-2">
                <div class=" col-span-12 lg:col-span-12">
                    <label>Tipo de Vivienda</label>
                    <div class="flex flex-col sm:flex-row mt-5">
                        <div class="form-check mr-2 sm:mt-0">
                            <input id="Propia" class="form-check-input" type="radio" name="tipo_vivienda" checked
                                v-model="tipo_vivienda" value="Propia">
                            <label class="form-check-label" for="Propia">Propia</label>
                        </div>

                        <div class="form-check mr-2 sm:mt-0">
                            <input id="Alquilada" class="form-check-input" type="radio" name="tipo_vivienda"
                                v-model="tipo_vivienda" value="Alquilada">
                            <label class="form-check-label" for="Alquilada">Alquilada</label>
                        </div>

                        <div class="form-check mr-2 sm:mt-0">
                            <input id="Familiar" class="form-check-input" type="radio" name="tipo_vivienda"
                                v-model="tipo_vivienda" value="Familiar">
                            <label class="form-check-label" for="Familiar">Familiar</label>
                        </div>

                        <div class="form-check mr-2 sm:mt-0">
                            <input id="Noble" class="form-check-input" type="radio" name="tipo_vivienda"
                                v-model="tipo_vivienda" value="Noble">
                            <label class="form-check-label" for="Noble">Noble</label>
                        </div>

                        <div class="form-check mr-2 sm:mt-0">
                            <input id="Rustico" class="form-check-input" type="radio" name="tipo_vivienda"
                                v-model="tipo_vivienda" value="Rustico">
                            <label class="form-check-label" for="Rustico">Rustico</label>
                        </div>

                    </div>
                </div>
            </div>


            <div class="mt-2">
                <label for="regular-form-1" class="form-label">Dni/Ruc</label>
                <div class="input-group ">

                    <input name="solicitud_documento" style="z-index: 1;" v-model="solicitud_documento" type="text"
                        class="form-control" placeholder="ejemplo.. Dni : 76234501 o Ruc 10214579281">

                    <button type="button" class="btn btn-primary btn-select" @click="search_solicitud_documento()">
                        <Icon icon="search" />
                    </button>
                </div>
            </div>


            <div class="grid grid-cols-12 gap-6 mt-2">
                <div class="col-span-12 lg:col-span-12">
                    <label for="vertical-form-2" class="form-label">Nombre y apellido / Razón Social ( <Span
                            class="text-success"> {{ estado_ruc }} </Span> ) </label>
                    <input name="solicitud_nombre" v-model="solicitud_nombre" type="text" class="form-control"
                        placeholder="Nombre y apellido / Razón Social">
                </div>
            </div>

            <div class="grid grid-cols-12 gap-6 mt-2">
                <div class="col-span-12 lg:col-span-6">
                    <label for="vertical-form-2" class="form-label">Giro de empresa o negocio </label>
                    <input name="solicitud_giro" v-model="solicitud_giro" type="text" class="form-control"
                        placeholder="Giro de empresa o negocio">
                </div>

                <div class="col-span-12 lg:col-span-6">
                    <label for="vertical-form-2" class="form-label">Antiguedad </label>
                    <input name="solicitud_antiguedad" v-model="solicitud_antiguedad" type="text" class="form-control"
                        placeholder="Antiguedad">
                </div>
            </div>


            <div class="grid grid-cols-12 gap-6 mt-2">
                <div class="col-span-12 lg:col-span-6">
                    <label for="vertical-form-2" class="form-label">Dirección del negocio </label>
                    <input name="solicitud_direccion_negocio" v-model="solicitud_direccion_negocio" type="text"
                        class="form-control" placeholder="Dirección del negocio">
                </div>

                <div class="col-span-12 lg:col-span-6">
                    <label for="vertical-form-2" class="form-label">Lugar </label>
                    <input name="solicitud_lugar" v-model="solicitud_lugar" type="text" class="form-control"
                        placeholder="Lugar">
                </div>
            </div>


            <div class="grid grid-cols-12 gap-6 mt-2">
                <div class="col-span-12 lg:col-span-6">
                    <label for="vertical-form-2" class="form-label">Referencia de ubicación del
                        negocio</label>
                    <input name="solicitud_referencia_negocio" v-model="solicitud_referencia_negocio" type="text"
                        class="form-control" placeholder="Referencia de ubicación del negocio">
                </div>

                <div class="col-span-12 lg:col-span-6">
                    <label for="vertical-form-2" class="form-label">Referencia de cliente </label>
                    <input name="solicitud_referencia_cliente" v-model="solicitud_referencia_cliente" type="text"
                        class="form-control" placeholder="Referencia de cliente">
                </div>
            </div>

            <div class="grid grid-cols-12 gap-6 mt-2">
                <div class="col-span-12 lg:col-span-12">
                    <label for="vertical-form-2" class="form-label">Dirección del domicilio</label>
                    <input name="solicitud_domicilio" v-model="solicitud_domicilio" type="text" class="form-control"
                        placeholder="Dirección del domicilio">
                </div>
            </div>

            <div class="grid grid-cols-12 gap-6 mt-2">
                <div class="intro-y col-span-12 lg:col-span-6">
                    <label for="vertical-form-2" class="form-label"> Destino </label>
                    <input name="destino" v-model="destino" type="text" class="form-control" placeholder="Destino">
                </div>
            </div>

            <div class="grid grid-cols-12 gap-6 mt-2">
                <div class="col-span-12 lg:col-span-6">
                    <label for="vertical-form-2" class="form-label">Nombre del conyugue </label>
                    <input name="solicitud_nombre_conyugue" v-model="solicitud_nombre_conyugue" type="text"
                        class="form-control" placeholder="Nombre del conyugue">
                </div>


                <div class="col-span-12 lg:col-span-3">
                    <label for="regular-form-1" class="form-label">Dni conyugue</label>
                    <div class="input-group mt-1">

                        <input name="solicitud_conyugue_dni" v-model="solicitud_conyugue_dni" type="text"
                            class="form-control" placeholder="ejemplo.. 76234501">

                        <button type="button" class="btn btn-primary btn-select" @click="search_dni_conyugue()">
                            <Icon icon="search" />
                        </button>
                    </div>
                </div>

                <div class="intro-y col-span-12 lg:col-span-3">
                    <label for="vertical-form-2" class="form-label">Ruc conyugue</label>
                    <input name="solicitud_conyugue_ruc" v-model="solicitud_conyugue_ruc" type="text"
                        class="form-control" placeholder="Ruc conyuque">
                </div>
            </div>

            <hr>

            <div class="grid grid-cols-12 gap-6 mt-2">
                <div class="intro-y col-span-12 lg:col-span-6">
                    <label for="vertical-form-2" class="form-label">Nombre de la entidad bancaria de esta
                        <strong>solicitud</strong> </label>
                    <input name=" solicitud_entidad_tarjeta" v-model="solicitud_entidad_tarjeta" type="text"
                        class="form-control" placeholder="Nombre de la tarjeta">
                </div>
                <div class="intro-y col-span-12 lg:col-span-6">
                    <label for="vertical-form-2" class="form-label">Numero de cuenta para esta
                        <strong>solicitud</strong> </label>
                    <input name="solicitud_tarjeta" v-model="solicitud_tarjeta" type="text" class="form-control"
                        placeholder="Numero de la tarjeta">
                </div>
            </div>




            <button type="submit" class="btn btn-primary btn-block btn-flotante btn-xs  ">Editar
                Solicitud</button>


        </form>

    </div>

</template>

<script>
import $ from 'jquery';
import { createApp, h } from 'vue';

import 'tom-select/dist/css/tom-select.css';
import TomSelect from 'tom-select';
import Axios from 'axios';

import "jquery-validation";
import "jquery-validation/dist/localization/messages_es"

import moment from 'moment';
import 'moment/locale/es';

// plugins para la creacion de pdf de una manera elegante
import {
    Canvg
} from 'canvg';
import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';
import 'jspdf-autotable';

import { CurrencyInput } from 'vue-currency-input'


// mixin
import {
    myMixin
} from "../../mixin.js";

import { renderToString } from '@vue/server-renderer';

//svg   
import cabezeraVue from './svg/cabezera.vue';

export default {
    mixins: [myMixin],
    components: {
        cabezeraVue,
        renderToString,
    },
    data() {
        return {
            headers: {
                "Content-Type": "application/json",
            },
            // variables para la solicitud
            get_solicitud: this.$attrs.get_solicitud,
            is_solicitud: false,
            tipo_solicitud: "Nuevo",
            tipo_vivienda: "Propia",
            analista_id: null,
            destino: "",
            solicitud_nombre: "",
            solicitud_documento: "",
            estado_ruc: "",
            solicitud_giro: "",
            solicitud_antiguedad: "",
            solicitud_direccion_negocio: "",
            solicitud_lugar: "",
            solicitud_referencia_negocio: "",
            solicitud_referencia_cliente: "",
            solicitud_domicilio: "",
            solicitud_nombre_conyugue: "",
            solicitud_conyugue_dni: "",
            solicitud_conyugue_ruc: "",
        }
    },
    watch: {

    },
    computed: {

    },
    methods: {
        //escuchar mensaje del componente analista para obtener el id de la misma
        escucharAnalista(sent) {
            this.analista_id = sent;
        },
        // activar modal de creacion de un cliente
        modal_add_cliente() {
            this.is_modal_add_cliente = true;
            var self = this;
            this.contactos_cliente.push({
                contacto: "",
                descripcion: ""
            })

            // validation cliente 
            this.$nextTick(() => {
                $("#form_crear_cliente").validate({
                    rules: {
                        cli_dni: {
                            required: true,
                        },
                        cli_nombre: {
                            required: true,
                        },
                        cli_apellido: {
                            required: true,
                        },
                        cli_domicilio: {
                            required: true,
                        }
                    },
                    submitHandler: function (form) {
                        self.crear_cliente();
                        return false;
                    }
                });

                this.select_ubigueo = new TomSelect(this.$refs.select_ubigueo, {
                    valueField: 'urlapi',
                    labelField: 'Distrito',
                    searchField: 'Distrito',
                    options: [],
                    render: {
                        option: function (item, escape) {

                            return `<div class="intro-x">
                                            <div class="box px-4 py-4 mb-1 flex items-center ">
                                                <div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">
                                                    <img alt="Midone - HTML Admin Template" src="../../dist/images/Draw/map.svg">
                                                </div>
                                                <div class="ml-4 mr-auto">
                                                    <div class="font-medium">${item.Departamento + " / " + item.Provincia + "/" + item.Distrito}</div> 
                                                </div>
                                                
                                            </div>
                            </div>`;
                        },
                        item: function (item, escape) {
                            return `<div class="intro-x">
                                            <div class="box px-4 py-4 mb-1 flex items-center ">
                                                <div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">
                                                    <img alt="Midone - HTML Admin Template" src="../../dist/images/Draw/map.svg">
                                                </div>
                                                <div class="ml-4 mr-auto">
                                                    <div class="font-medium">${item.Departamento + " / " + item.Provincia + "/" + item.Distrito}</div> 
                                                </div>
                                                
                                            </div>
                            </div>`;
                        }
                    },
                    load: function (query, callback) {
                        const data = {
                            search: query,
                        };

                        const headers = this.headers;

                        Axios
                            .post("/search_ubigeo_select", data, {
                                headers,
                            })
                            .then((response) => {

                                if (response.data.success) {
                                    console.log(response.data);
                                    callback(response.data.data);
                                }
                            })
                            .catch((error) => {
                                callback();
                                this.loading_end();
                                this.alert_error_modal("Error en el servidor, recargue el navegador");
                                console.error(error);
                            });
                    },
                });

                this.select_ubigueo.on('change', this.change_select_ubigueo);

            });
        },
        // buscar dni en el api de la reniec
        search_dni() {

            if (this.cli_dni.length != 8) {
                this.alert_warning("Tienes que digitar 8 dígitos para buscar el <strong>DNI</strong>");
                return;
            }

            const data = { dni: this.cli_dni };

            const headers = this.headers;

            this.loading_start();

            Axios
                .post("/search_dni", data, {
                    headers,
                })
                .then((response) => {

                    if (response.data.success) {

                        var Params = response.data.data;

                        this.cli_nombre = Params.nombres;
                        this.cli_apellido = Params.apellidoPaterno + " " + Params.apellidoMaterno;

                        this.alert_success("Dni cargando exitosamente");

                    } else {
                        this.cli_nombre = "";
                        this.cli_apellido = "";
                        this.alert_warning(response.data.message);
                    }
                    this.loading_end();
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor, ingrese los nombres y apellidos manualmente");
                    console.error(error);
                });

        },
        editar_solicitud() {
            const data = {
                urlapi: this.get_solicitud.urlapi,
                tipo_solicitud: this.tipo_solicitud,
                tipo_vivienda: this.tipo_vivienda,
                solicitud_nombre: this.solicitud_nombre,
                solicitud_documento: this.solicitud_documento,
                solicitud_giro: this.solicitud_giro,
                solicitud_antiguedad: this.solicitud_antiguedad,
                solicitud_direccion_negocio: this.solicitud_direccion_negocio,
                solicitud_lugar: this.solicitud_lugar,
                solicitud_referencia_negocio: this.solicitud_referencia_negocio,
                solicitud_referencia_cliente: this.solicitud_referencia_cliente,
                solicitud_domicilio: this.solicitud_domicilio,
                solicitud_nombre_conyuge: this.solicitud_nombre_conyugue,
                solicitud_conyugue_dni: this.solicitud_conyugue_dni,
                solicitud_conyugue_ruc: this.solicitud_conyugue_ruc,
                destino: this.destino,
                solicitud_tarjeta: this.solicitud_tarjeta,
                solicitud_entidad_tarjeta: this.solicitud_entidad_tarjeta
            };

            const headers = this.headers;

            this.loading_start();

            Axios
                .put("/editar_solicitud", data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {
                        this.$emit("comunicarEditarSolicitud", response.data.data)
                    } else {
                        this.alert_warning(response.data.message);
                    }
                    this.loading_end();
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor, ingrese los nombres y apellidos manualmente");
                    console.error(error);
                });
        },
        // buscar el dni de la conyugue o apoderado
        search_dni_conyugue() {

            if (this.solicitud_conyugue_dni.length != 8) {
                this.alert_warning("Tienes que digitar 8 dígitos para buscar el <strong>DNI</strong>");
                return;
            }

            const data = { dni: this.solicitud_conyugue_dni };

            const headers = this.headers;

            this.loading_start();

            Axios
                .post("/search_dni", data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {
                        console.log(response.data);
                        var Params = response.data.data;

                        this.solicitud_nombre_conyugue = Params.nombres + " " + Params.apellidoPaterno + " " + Params.apellidoMaterno;


                        this.alert_success("Dni cargando exitosamente");

                    } else {
                        this.solicitud_nombre_conyugue = "";
                        this.alert_warning(response.data.message);
                    }
                    this.loading_end();
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor, ingrese los nombres y apellidos manualmente");
                    console.error(error);
                });
        },
        // buscar por dni y por ruc dependiendo de la longitud de  los datos ingresados
        search_solicitud_documento() {

            var ruta = "";
            var tipo_documento = "";
            var data = {};
            switch (this.solicitud_documento.length) {
                case 8:
                    ruta = "/search_dni";
                    tipo_documento = "dni";
                    data = { dni: this.solicitud_documento };
                    break;

                case 11:
                    ruta = "/search_ruc";
                    tipo_documento = "ruc";
                    data = { ruc: this.solicitud_documento };
                    break;

                default:
                    this.alert_warning("Tienes que digitar 8 o 11 dígitos para buscar el <strong>DNI o RUC</strong>");
                    break;
            }

            if (ruta == "") {
                return;
            }

            const headers = this.headers;

            this.loading_start();

            Axios
                .post(ruta, data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {
                        var Params = response.data.data;
                        if (tipo_documento == "ruc") {
                            this.solicitud_nombre = Params.razonSocial
                            this.estado_ruc = "Condicion de este ruc : " + Params.condicion
                        } else {
                            this.solicitud_nombre = `${Params.nombres} ${Params.apellidoPaterno} ${Params.apellidoMaterno}`;
                            this.estado_ruc = "";
                        }

                    } else {

                        this.alert_warning(response.data.message);
                    }
                    this.loading_end();
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor, ingrese los nombres y apellidos manualmente");
                    console.error(error);
                });
        },
        valitation_add_prestamo() {


        },
    },
    mounted() {

        const self = this;
        $("#editar_solicitud").validate({
            rules: {
                solicitud_nombre: {
                    required: true,
                },
                solicitud_documento: {
                    required: true,
                },
                solicitud_giro: {
                    required: true,
                },
                solicitud_antiguedad: {
                    required: true,
                },
                solicitud_direccion_negocio: {
                    required: true,
                },
                solicitud_lugar: {
                    required: true,
                },
                solicitud_referencia_negocio: {
                    required: true,
                },
                solicitud_referencia_cliente: {
                    required: true,
                },
                solicitud_domicilio: {
                    required: true,
                },
                destino: {
                    required: true,
                },
                solicitud_tarjeta: {
                    minlength: 2,
                    maxlength: 60,
                    digits: true,
                },
                solicitud_entidad_tarjeta: {
                    minlength: 1,
                    maxlength: 60
                }
            },
            submitHandler: function (form) {
                try {
                    self.editar_solicitud();
                    return false;
                } catch (error) {
                    console.log(error);
                    return false;
                }

            }
        });


        this.tipo_solicitud = this.get_solicitud.tipo_solicitud;
        this.tipo_vivienda = this.get_solicitud.tipo_vivienda;
        this.destino = this.get_solicitud.destino;
        this.solicitud_nombre = this.get_solicitud.solicitud_nombre;
        this.solicitud_documento = this.get_solicitud.solicitud_documento;
        this.estado_ruc = this.get_solicitud.estado_ruc;
        this.solicitud_giro = this.get_solicitud.solicitud_giro;
        this.solicitud_antiguedad = this.get_solicitud.solicitud_antiguedad;
        this.solicitud_direccion_negocio = this.get_solicitud.solicitud_direccion_negocio;
        this.solicitud_lugar = this.get_solicitud.solicitud_lugar;
        this.solicitud_referencia_negocio = this.get_solicitud.solicitud_referencia_negocio;
        this.solicitud_referencia_cliente = this.get_solicitud.solicitud_referencia_cliente;
        this.solicitud_domicilio = this.get_solicitud.solicitud_domicilio;
        this.solicitud_nombre_conyugue = this.get_solicitud.solicitud_nombre_conyugue;
        this.solicitud_conyugue_dni = this.get_solicitud.solicitud_conyugue_dni;
        this.solicitud_conyugue_ruc = this.get_solicitud.solicitud_conyugue_ruc;
        this.solicitud_entidad_tarjeta = this.get_solicitud.solicitud_entidad_tarjeta;
        this.solicitud_tarjeta = this.get_solicitud.solicitud_tarjeta;
    },
}  
</script>

<style>
.thead-fixed {
    position: sticky;
    top: 0;
    z-index: 999;
    background-color: white;
    /* Cambia esto al color de fondo deseado */
}
</style>