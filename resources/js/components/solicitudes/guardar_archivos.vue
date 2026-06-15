<template>
    <div class="intro-y inbox box mt-5">

        <div class="overflow-x-auto sm:overflow-x-visible">
            <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
                <table class="table table-report -mt-2">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">Tipo</th>
                            <th class="whitespace-nowrap">Descripcion</th>
                            <th class="text-center whitespace-nowrap">Pezo</th>
                            <th class="text-center whitespace-nowrap">Fecha creacion</th>
                            <th class="text-center whitespace-nowrap">ususario</th>
                            <th class="text-center whitespace-nowrap">acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="intro-x" v-for="(g_a, index_g_a) in get.guardar_documento" :key="index_g_a">
                            <td class="w-40">
                                PDF
                            </td>

                            <td class="text-center">{{ g_a.descripcion }}</td>
                            <td class="text-center">{{ g_a.size }} bytes</td>

                            <td>
                                <div class="text-slate-500 text-sm whitespace-nowrap mt-0.5 text-center"> {{
                            g_a.fechacreacion }}</div>
                                <div class="text-slate-500 text-sm whitespace-nowrap mt-0.5 text-center"> {{
                            g_a.horacreacion }}</div>
                            </td>
                            <td>
                                <div class="text-slate-500 text-sm whitespace-nowrap mt-0.5 text-center"> {{
                            g_a.usuario.name }}</div>
                                <div class="text-slate-500 text-sm whitespace-nowrap mt-0.5 text-center"> {{
                            g_a.usuario.lastname }}</div>
                            </td>
                            <td class="table-report__action w-56">
                                <div class="flex justify-center items-center">
                                    <a @click=" view_archivo = g_a.url_destino; is_modal_view = true;"
                                        class="flex items-center mr-3" href="javascript:;">
                                        <Icon icon='eye' class='mr-2' /> Ver
                                    </a>
                                    <a @click="is_modal_edit=true; descripcion_edit = g_a.descripcion;urlapi = g_a.urlapi; index_edit = index_g_a;"
                                        class="flex items-center mr-3" href="javascript:;">
                                        <Icon icon='edit' class='mr-2' /> Editar
                                    </a>
                                    <a class="flex items-center text-danger" href="javascript:;" data-tw-toggle="modal"
                                        data-tw-target="#delete-confirmation-modal">
                                        <Icon icon='trash' class='mr-2' /> Eliminar
                                    </a>
                                </div>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

        </div>
        <div class="p-5 flex flex-col sm:flex-row items-center text-center sm:text-left text-slate-500">
            <div>-</div>
            <div class="sm:ml-auto mt-2 sm:mt-0"> </div>
        </div>



    </div>

    <div class="card box p-3 mt-3">
        <div class="flex flex-col sm:flex-row sm:items-end xl:items-start">
            <div class="xl:flex sm:mr-auto mt-2 pt-0">
                <div class="font-bold text-base font-xl"> Formulario para insertar documentos </div>
            </div>
        </div>
    </div>

    <div class="card box p-3 mt-3">
        <div class="">
            <div class="grid grid-cols-12 gap-12 mt-2 mb-2">
                <div class="intro-y col-span-12 lg:col-span-12">
                    <label for="vertical-form-2" class="form-label">Escribar una descripcion de este documento </label>
                    <input name="descripcion" v-model="descripcion" type="text" class="form-control"
                        placeholder="ejemplo: contrato scaneado">
                </div>
            </div>
        </div>
        <div ref="guardar_archivos" id="guardar_archivos" class="border border-4 border-primary rounded">

        </div>
        <button type="button" class="btn btn-primary w-24 mr-1 mt-2" style="width: 100%;"
            v-on:click="upload_document()">
            <Icon icon="floppy-disk" class="mr-2" /> Guardar Documento
        </button>
    </div>

    <!-- modal para visualizar  el archivo -->
    <Dialog v-model:visible="is_modal_view" maximizable modal header="Visualizar documento" :style="{ width: '90rem' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
        <iframe style="width: 100%;" :src="'../../../../' + view_archivo" width="500" height="800"></iframe>
    </Dialog>

    <!-- modal para editar  el archivo -->
    <Dialog v-model:visible="is_modal_edit" maximizable modal header="Formulario para editar un archivo"
        :style="{ width: '90rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">

        <editar_archivos @comunicarGuardarArchivos="escucharGuardarArchivos" :descripcion_edit="descripcion_edit" :urlapi="urlapi"></editar_archivos>

    </Dialog>




</template>

<script>
import Uppy from '@uppy/core';
import Webcam from '@uppy/webcam';
import Dashboard from '@uppy/dashboard';
import es from '@uppy/locales/lib/es_ES';
import ImageEditor from '@uppy/image-editor';
import '@uppy/image-editor/dist/style.min.css';
import XHRUpload from '@uppy/xhr-upload';
import Swal from "sweetalert2";
import axios from "axios";
import $ from "jquery";
import "jquery-validation";
import "jquery-validation/dist/localization/messages_es"
import '@uppy/core/dist/style.min.css';
import '@uppy/dashboard/dist/style.min.css';
// mixin
import {
    myMixin
} from "../../mixin.js";

import Axios from 'axios';
import { get } from 'jquery';

export default {
    mixins: [myMixin],
    data() {
        return {
            is_modal_view: false,
            view_archivo: "",
            // ediccion
            is_modal_edit: false,
            urlapi: null,
            index_edit:null,
            descripcion_edit: null,
            //
            get: this.$attrs.solicitud,
            descripcion: "",
            headers: {
                "Content-Type": "application/json",
            },
        } 
    },
    methods: {
        escucharGuardarArchivos(send){
            this.get.guardar_documento[this.index_edit] = send;
            this.is_modal_edit = false;
        },
        modal_edit(id, descripcion) {
            this.urlapi = id;
            this.descripcion_edit = descripcion;
            var self = this;  
        }, 
        upload_document() {
            this.uppy.getFiles().forEach((file) => {
                const reader = new FileReader();

                reader.onload = () => {
                    const pdf_code = reader.result.split(",")[1];

                    //insertar imagenes
                    var self = this;
                    const data = {
                        urlapi: this.get.urlapi,
                        pdf_code: pdf_code,
                        descripcion: this.descripcion
                    };

                    const headers = this.headers;

                    this.loading_start();

                    Axios
                        .post("/guardar_documento", data, {
                            headers,
                        })
                        .then((response) => {
                            console.log(response.data);
                            if (response.data.success) {
                                this.alert_success(response.data.message);
                                this.get.guardar_documento.push( response.data.data );
                                this.descripcion = "";
                         
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
                };

                reader.readAsDataURL(file.data);
            });
        }
    },
    created() {

    },
    mounted() {
  
        this.uppy = new Uppy({
            debug: true,
            locale: es,
            autoProceed: false,
            restrictions: {
                allowedFileTypes: ['application/pdf'],
                maxFileSize: 1500000000,
                maxNumberOfFiles: 1
            },
        })
            .use(Dashboard, {
                target: '#guardar_archivos',
                inline: true,
                width: '100%',
                proudlyDisplayPoweredByUppy: false,
                hideUploadButton: true,
            }).use(Webcam, {
                target: Dashboard
            })
            .use(ImageEditor, {
                target: Dashboard
            }).on("fileAdded", (file) => {
                // Obtener el contenido del archivo en forma de string
                const fileData = file.getData();

                // Imprimir el contenido del archivo
                console.log(fileData);
            });
    },
}
</script>

<style></style>