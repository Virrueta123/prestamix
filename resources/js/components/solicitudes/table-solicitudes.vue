<template>
    <div class="intro-y box p-5 mt-5">
        <div class="flex flex-col sm:flex-row sm:items-end xl:items-start">
            <form id="tabulator-html-filter-form" class="xl:flex sm:mr-auto">

                <div class="sm:flex items-center sm:mr-12 mt-2 xl:mt-0">
                    <label class="w-12 flex-none xl:w-auto xl:flex-initial mr-2">Value </label>
                    <input type="text" class="form-control " placeholder="Busquedas...">
                </div>

            </form>
            <div class="flex mt-5 sm:mt-0">
                <button id="tabulator-print" v-on:click="imprimir()" class="btn btn-primary w-1/2 sm:w-auto mr-2">
                    <Icon icon='print' class='mr-2' /> Imprimir
                </button>
                <div class="dropdown w-1/2 sm:w-auto">
                    <button class="dropdown-toggle btn btn-primary w-full sm:w-auto" aria-expanded="false"
                        data-tw-toggle="dropdown">
                        <Icon icon='download' class='mr-2' /> Exportar
                        <Icon icon='chevron-down' class='mr-2' />
                    </button>
                    <div class="dropdown-menu w-40">
                        <ul class="dropdown-content">
                            <li>
                                <button class="dropdown-item" v-on:click="exportar_xlsx()">
                                    <Icon icon='file-excel' class='mr-2' /> Export Excel
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item" v-on:click="exportar_pdf()">
                                    <Icon icon='file-pdf' class='mr-2' /> Export Pdf
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto scrollbar-hidden">
            <div ref="tabulator" class="mt-5 table-report table-report--tabulator"></div>
        </div>

    </div>

</template>

<script>
import { TabulatorFull as Tabulator } from 'tabulator-tables';
import {
    Canvg
} from 'canvg';
import html2canvas from 'html2canvas';

import Axios from "axios";

// mixin
import {
    myMixin
} from "../../mixin.js";


export default {
    mixins: [myMixin],
    data() {
        return {
            tableData: this.$attrs.users,
            tabulator: null,

        }
    },
    methods: {
        editar_registro() {
            alert("este evento  es de prueba");
        },
        exportar_xlsx() {
            this.tabulator.download("xlsx", "excel-usuarios.xlsx"); 
        },
        exportar_pdf() {
            this.tabulator.downloadToTab("pdf","usuarios.pdf");
        },
        imprimir() {
            this.tabulator.print(false, true);
        },
        desactivar_usuario(e, cell) {
 
            var row = this.tabulator.getRow(cell.getRow());
  
            var get = row.getData();

            row.getCells()[6].getElement()
            this.$swal.fire({
                imageUrl: "../../../../dist/images/Draw/denied.svg",
                imageHeight: 150,
                imageAlt: "--",
                title: "Estas seguro que deseas desactivar a este usuario?",
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: "Si estoy segur@",
                denyButtonText: `Salir`
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    const data = {
                        urlapi: get.urlapi
                    };

                    const headers = this.headers;

                    this.loading_start();

                    Axios
                        .put("/desactivar_usuario", data, {
                            headers,
                        })
                        .then((response) => {
                            console.log(response.data);
                            if (response.data.success) {
                                this.tableData[row.getPosition() - 1].status = response.data.data;
                                if (this.tableData[row.getPosition() - 1].status == "A") {
                                    row.getCells()[6].getElement().innerHTML = "<button class='btn btn-sm btn-success text-white p-1 m-1'> Desactivar usuario</button>";
                                } else {
                                    row.getCells()[6].getElement().innerHTML = "<button class='btn btn-sm btn-danger text-white p-1 m-1'> Activar usuario</button>";
                                }
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
            });
        },

        load_user_table() {
            var self = this;

            this.tabulator = new Tabulator(this.$refs.tabulator, {
                downloadConfig: {
                    columnHeaders: false, //do not include column headers in downloaded table
                    columnGroups: false, //do not include column groups in column headers for downloaded table
                    rowGroups: false, //do not include row groups in downloaded table
                    columnCalcs: false, //do not include column calcs in downloaded table
                    dataTree: false, //do not include data tree in downloaded table
                },
                printHeader:"<h3 class='text-center'>Lista de Usuarios</h3>",
                reactiveData: true,
                edit: true,
                layout: "fitDataFill",
                movableColumns: true,
                paginationCounter: "rows", pagination: "local",
                paginationSize: 6,
                paginationSizeSelector: [3, 6, 8, 10],
                responsiveLayout: "collapse",
                columns: [
                    { title: "NOMBRE", field: "name", headerFilter: "input" },
                    { title: "APELLIDO", field: "lastname", align: "center", headerSort: true, reponsive: true, headerFilter: "input" },
                    { title: "DNI", field: "dni", headerFilter: "input" },
                    { title: "CELULAR", field: "celular", headerFilter: "input" },
                    { title: "CORREO", field: "email", headerFilter: "input" },
                    {
                        formatter: function (cell, formatterParams, onRendered) {

                            return "<button class='btn btn-sm btn-primary p-1 m-1'  > editar</button>";
                        }
                        , hozAlign: "center", responsive: 0, cellClick: this.editar_registro
                    },
                    {
                        formatter: function (cell, formatterParams, onRendered) {
                            var usuario = cell.getData();

                            if (usuario.urlapi != self.user.urlapi) {
                                if (usuario.status == "A") {
                                    return "<button class='btn btn-sm btn-success text-white p-1 m-1'> Desactivar usuario</button>";
                                } else {
                                    return "<button class='btn btn-sm btn-danger text-white p-1 m-1'> Activar usuario</button>";
                                }
                            } else {
                                return "";
                            }


                        }
                        , title: "Btn", hozAlign: "center", responsive: 0, cellClick: this.desactivar_usuario
                    },


                ],
                data: this.tableData,
            });
            return true;
        }
    },
    created() {

    },
    mounted() {
        this.cargar_usuario().then((result) => {
            this.user = result;
            console.log(this.user);
            this.load_user_table()
        }).catch((err) => {

        });



    },
}
</script>

<style></style>