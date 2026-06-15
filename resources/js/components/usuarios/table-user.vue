<template>
    <div class="intro-y box p-5 mt-5">
         
        <div class="overflow-x-auto scrollbar-hidden">
            <div ref="tabulator" class="mt-5 table-report table-report--tabulator"></div>
        </div>

    </div>

    <Sidebar v-model:visible="is_opciones_modal" header="Opciones para este registro" position="bottom"
        style="height: auto">

        <div id="link-button" class="p-5">
            <button @click="is_edit_modal = true;" class="btn btn-primary  mr-1 mb-2">
                <Icon icon='edit' class='mr-2' /> Editar usuario
            </button> 
        </div>

    </Sidebar>

    <Sidebar v-model:visible="is_opciones_modal" header="Editar este usuario" position="bottom"
        style="height: auto">

        <edit-usuario  :get_data="get_data"></edit-usuario>
    </Sidebar>

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
            user :null,
            is_opciones_modal:false,
            is_edit_modal:false,
            get_data:null
        }
    },
    methods: {
        editar_registro(e,cell) {
            var row = this.tabulator.getRow(cell.getRow()); 
            var get = row.getData(); 
            this.get_data = get ;
            this.is_opciones_modal = true;
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
                    { title: "Tipo usuario", field: "rol", headerFilter: "input" },
                    {
                        formatter: function (cell, formatterParams, onRendered) {

                            return "<button class='btn btn-sm btn-primary p-1 m-1'  > editar</button>";
                        }
                        , hozAlign: "center", responsive: 0, cellClick: this.editar_registro
                    },
                    {
                        formatter: function (cell, formatterParams, onRendered) {
                            var usuario = cell.getData();
                            return `<a href="/crear_salario/${usuario.urlapi}" class='btn btn-sm btn-success p-1 m-1'> Salario</a>`;
                        }
                        , hozAlign: "center", responsive: 0
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
            this.load_user_table()
        }).catch((err) => {

        });
         
 

    },
}
</script>

<style></style>