<template>
    <form id="form_editar_cliente" method="POST" action="#">
        <div id="vertical-form" class="p-5">
            <div class="">
                <div>
                    <label for="regular-form-1" class="form-label">Buscar Dni</label>
                    <div class="input-group mt-2">

                        <input name="cli_dni" v-model="cli_dni" type="text" class="form-control"
                            placeholder="ejemplo.. 76234501">

                        <button type="button" class="btn btn-primary btn-select" @click="search_dni()">
                            <Icon icon="search" />
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-12 gap-6 mt-2">
                    <div class="intro-y col-span-12 lg:col-span-6">
                        <label for="vertical-form-2" class="form-label">Nombre </label>
                        <input name="cli_nombre" v-model="cli_nombre" type="text" class="form-control"
                            placeholder="Nombres">
                    </div>

                    <div class="intro-y col-span-12 lg:col-span-6">
                        <label for="vertical-form-2" class="form-label">Apellido </label>
                        <input name="cli_apellido" v-model="cli_apellido" type="text" class="form-control"
                            placeholder="Apellidos">
                    </div>
                </div>
                <div
                    class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                    <h2 class="font-medium text-base mr-auto">
                        Agregar Contactos
                    </h2>
                    <div class="form-check form-switch w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0">
                        <button type="button" class="btn btn-danger mr-1 mb-2" @click="agregar_contacto_cliente()">
                            <Icon icon="plus" /> Agregar
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-6 mt-2" v-for="(c_c, c_c_index) in contactos_cliente"
                    :key="c_c_index">

                    <div class="intro-y col-span-12 lg:col-span-4">
                        <label for="vertical-form-2" class="form-label">Contato {{ c_c_index + 1 }} </label>
                        <InputMask v-model="c_c.contacto" mask="999-999-999" placeholder="999-999-999"
                            class="form-control p-2" />
                    </div>
                    <div class="intro-y col-span-12 lg:col-span-6">
                        <label for="vertical-form-2" class="form-label">Descripcion Contato {{ c_c_index + 1 }}
                        </label>
                        <input v-model="c_c.descripcion" type="text" maxlength="35" class="form-control p-2" />
                    </div>

                    <div v-if="c_c_index != 0" class="intro-y col-span-12 lg:col-span-2">
                        <label for="vertical-form-2" class="form-label">Eliminar </label>
                        <button type="button" class="btn btn-danger ml-2 mb-2"
                            @click="eliminar_contacto_cliente(c_c_index)">
                            <Icon icon="trash" />
                        </button>
                    </div>
                </div>

                <div class=" col-span-12 lg:col-span-12 mt-3 ">
                    <label for="vertical-form-2" class="form-label"> Fecha de nacimiento </label>
                    <datepicker class="form-control col-span-12" v-model="fecha_nacimiento"
                        placeholder="hacer click para seleccionar" :styles="{ border: '2px solid #00ff00' }"
                        language="es"></datepicker>
                </div>

                <div class="grid grid-cols-12 gap-2">
                    <div class="mt-3 col-span-12">
                        <label for="vertical-form-2" class="form-label">Domicilio </label>
                        <input name="cli_domicilio" v-model="cli_domicilio" type="text" class="form-control"
                            placeholder="Domicilio">
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-2">
                    <div class="mt-3 col-span-12">
                        <label for="vertical-form-2" class="form-label">Direccion del trabajo</label>
                        <input v-model="cli_direccion_trabajo" type="text" class="form-control"
                            placeholder="Direccion del trabajo">
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-2 mt-2">
                    <div class="intro-y col-span-12 lg:col-span-6">
                        <label>Tipo de cliente</label>
                        <div class="flex flex-col sm:flex-row mt-5">
                            <div class="form-check mr-2 sm:mt-0">
                                <input id="particular" class="form-check-input" type="radio" name="tipo_cliente" checked
                                    v-model="tipo_cliente" value="particular">
                                <label class="form-check-label" for="particular">Particular</label>
                            </div>
                            <div class="form-check mr-2 mt-2 sm:mt-0">
                                <input id="privada" v-model="tipo_cliente" class="form-check-input" type="radio"
                                    name="tipo_cliente" value="tarjeta privada">
                                <label class="form-check-label" for="privada">Tarjeta privada</label>
                            </div>
                            <div class="form-check mr-2 mt-2 sm:mt-0">
                                <input id="estado" v-model="tipo_cliente" class="form-check-input" type="radio"
                                    name="tipo_cliente" value="tarjeta estado">
                                <label class="form-check-label" for="estado">Tarjeta estado</label>
                            </div>
                        </div>
                    </div>

                    <div class="intro-y col-span-12 lg:col-span-6">
                        <label>Sexo</label>
                        <div class="flex flex-col sm:flex-row mt-5">

                            <div class="form-check mr-2 sm:mt-0">
                                <input id="M" class="form-check-input" type="radio" name="cli_sexo" checked value="M"
                                    v-model="cli_sexo">
                                <label class="form-check-label" for="M">Masculino</label>
                            </div>
                            <div class="form-check mr-2 mt-2 sm:mt-0">
                                <input id="F" class="form-check-input" type="radio" name="cli_sexo" value="F"
                                    v-model="cli_sexo">
                                <label class="form-check-label" for="F">Femenino</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label for="inputEmail4">Buscar Ubigeo</label>
                        <select ref="select_ubigueo" class="form-control" style="width: 100%;" tabindex="2"
                            language="es" placeholder="seleccionar un ubigueo">
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="inputEmail4">Departamento</label>
                        <input v-model="cli_departamento" type="text" class="form-control" name="cli_departamento"
                            id="cli_departamento" placeholder="Departamento" />
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputEmail4">Provincia</label>
                        <input v-model="cli_provincia" type="text" class="form-control" name="cli_provincia"
                            id="cli_provincia" placeholder="Provincia" />
                    </div>
                    <div class="form-group col-md-4">
                        <label for="inputPassword4">Distrito</label>
                        <input v-model="cli_distrito" type="text" class="form-control" name="cli_distrito"
                            id="cli_distrito" placeholder="Distrito" />
                    </div>
                </div>

                <div id="basic-button" class="p-1 mt-4">
                    <div class="preview">
                        <button type="submit" class="btn btn-primary mr-1 mb-2">
                            <Icon icon="plus" /> Editar Cliente
                        </button>
                        <button type="button" class="btn btn-danger mr-1 mb-2" @click="is_actualizar_cliente = false">
                            <Icon icon="ban" /> Cancelar Edicion
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
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



// mixin
import {
    myMixin
} from "../../mixin.js";



export default {
    mixins: [myMixin],
    data() {
        return {
            get_cliente: this.$attrs.get_cliente,
            cli_dni: "",
            cli_nombre: "",
            cli_apellido: "",
            cli_celular: "",
            fecha_nacimiento: null,
            cli_domicilio: "",
            cli_direccion_trabajo: "",
            cli_sexo: "M",
            tipo_cliente: "particular",
            contactos_cliente: [],
            data_select_cliente: [],
            // ubigeo
            cli_departamento: "",
            cli_distrito: "",
            cli_provincia: "",
        }
    },
    methods: {
         change_select_ubigueo(data) {

            const datos = {
                urlapi: data
            };

            const headers = this.headers;

            this.loading_start();

            Axios
                .post("/get_ubigeo", datos, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {
                        var ubigeo = response.data.data;
                        this.cli_departamento = ubigeo.Departamento;
                        this.cli_distrito = ubigeo.Distrito;
                        this.cli_provincia = ubigeo.Provincia;
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
        // elimina un contacto
        eliminar_contacto_cliente(index) {
            this.contactos_cliente.splice(index, 1);
        },
        // funcion para agregar contactos
        agregar_contacto_cliente() {
            this.contactos_cliente.push({
                contacto: "",
                descripcion: ""
            })
        },
        editar_cliente() {
            const contactosConContactoVacio = this.contactos_cliente.filter(
                (contacto) => contacto.contacto.trim() === ""
            );

            if (contactosConContactoVacio.length > 0) {
                this.alert_warning("hay un contacto vacio revise el formulario");
                return;
            }

            const data = {
                urlapi: this.get_cliente.urlapi,
                cli_dni: this.cli_dni,
                cli_nombre: this.cli_nombre,
                cli_apellido: this.cli_apellido,
                cli_celular: this.contactos_cliente,
                fecha_nacimiento: this.fecha_nacimiento,
                cli_domicilio: this.cli_domicilio,
                cli_direccion_trabajo: this.cli_direccion_trabajo,
                cli_sexo: this.cli_sexo,
                tipo_cliente: this.tipo_cliente,
                cli_departamento: this.cli_departamento,
                cli_distrito: this.cli_distrito,
                cli_provincia: this.cli_provincia
            };


            const headers = this.headers;

            this.loading_start();

            Axios
                .put("/editar_cliente", data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {
                        this.alert_success(response.data.message);
                        this.$emit("comunicarEditarCliente",response.data.data); 
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
    },
    mounted() {

        this.cli_dni = this.get_cliente.cli_dni;
        this.cli_nombre = this.get_cliente.cli_nombre;
        this.cli_apellido = this.get_cliente.cli_apellido;
        this.cli_celular = this.get_cliente.cli_celular;
        this.fecha_nacimiento = this.get_cliente.fecha_nacimiento;
        this.cli_domicilio = this.get_cliente.cli_domicilio;
        this.cli_direccion_trabajo = this.get_cliente.cli_direccion_trabajo;
        this.cli_sexo = this.get_cliente.cli_sexo;
        this.tipo_cliente = this.get_cliente.tipo_cliente; 
        this.cli_departamento = this.get_cliente.cli_departamento;
        this.cli_distrito = this.get_cliente.cli_distrito;
        this.cli_provincia = this.get_cliente.cli_provincia;

        console.log(this.get_cliente.contactos_cliente);

        this.get_cliente.contactos_cliente.forEach(element => {
            this.contactos_cliente.push({
                contacto: element.ccliente_contacto,
                descripcion: element.ccliente_descripcion
            }) 
        });

        var self = this;
        this.$nextTick(() => {
            $("#form_editar_cliente").validate({
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
                    try {
                        self.editar_cliente();
                    } catch (error) {
                        console.log(error);
                    }
                    return false;
                }
            });
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
    },
}
</script>

<style></style>