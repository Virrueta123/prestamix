<template>
    <div class="card box p-3 mt-3">
        <div class="">
            <div class="grid grid-cols-12 gap-12 mt-2 mb-2">
                <div class="intro-y col-span-12 lg:col-span-12">
                    <label for="vertical-form-2" class="form-label">Escribar una descripcion de este documento
                    </label>
                    <input name="descripcion" v-model="descripcion_edit"  type="text" class="form-control"
                        placeholder="ejemplo: contrato scaneado">
                </div>
            </div>
        </div>
        <div ref="edit_archivos" id="edit_archivos" class="border border-4 border-primary rounded">

        </div>
        <button type="button" class="btn btn-primary w-24 mr-1 mt-2" style="width: 100%;" v-on:click="edit_document()">
            <Icon icon="floppy-disk" class="mr-2" /> Editar Documento
        </button>
    </div>

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
            descripcion_edit: this.$attrs.descripcion_edit,
            urlapi: this.$attrs.urlapi,
        }
    },
    methods: { 
        edit_document() {
            this.uppy_edit.getFiles().forEach((file) => {
                const reader = new FileReader();

                reader.onload = () => {
                    const pdf_code = reader.result.split(",")[1];

                    //insertar imagenes
                    var self = this;

                    console.log(this.urlapi);

                    const data = {
                        urlapi: this.urlapi,
                        pdf_code: pdf_code,
                        descripcion: this.descripcion_edit
                    };

                    const headers = this.headers;

                    this.loading_start();

                    Axios
                        .put("/edit_documento", data, {
                            headers,
                        })
                        .then((response) => {
                            console.log(response.data);
                            if (response.data.success) {
                                this.alert_success(response.data.message);
                                this.$emit("comunicarGuardarArchivos",response.data.data);
                            } else {
                                this.alert_warning(response.data.message);
                            }
                            this.loading_end();
                        })
                        .catch((error) => {
                            this.loading_end();
                            this.alert_error_modal("Error en el servidor,recargar la pagina");
                            console.error(error);
                        });
                };

                reader.readAsDataURL(file.data);
            });
        },

    },
    created() {

    },
    mounted() {
        console.log(this.urlapi);    
        this.uppy_edit = new Uppy({
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
                target: '#edit_archivos',
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