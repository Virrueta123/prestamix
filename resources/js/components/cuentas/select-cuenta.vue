<template>
    <select ref="select_cuenta" name="cuentas_id" class="form-control"></select>
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
            select_cuenta: null,
            default: this.$attrs.default || null
        }
    },
    watch: {

    },
    methods: {
        async cargar_tipo_gasto() {
            const data = {};

            const headers = this.headers;

            return Axios
                .post("/load_cuentas", data, {
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
        change_select_cuenta(event) {
            this.$emit("comunicarCuenta", event);
        },
        html_item(item) {
            return `<div class="ml-3"> 
                     ${item.cuentas_entidad} 
                    </div>`;
        },
        initSelect(data) {
            var self = this;
            this.select_cuenta = new TomSelect(this.$refs.select_cuenta, {
                valueField: 'urlapi',
                labelField: 'nombre',
                searchField: 'nombre',
                options: data,
                persist: false,
                plugins: {
                    'clear_button': {
                        'title': 'Eliminar la seleccion',
                    }
                },
                render: {
                    option: function (item, escape) {
                        return self.html_item(item);
                    },
                    item: function (item, escape) {
                        return self.html_item(item);
                    }
                }
            });

            if (this.default) {
                this.select_cuenta.setValue(this.default);
                this.$emit("comunicarCuenta", this.default);
            } else if (data.length > 0) {
                this.select_cuenta.setValue(data[0].urlapi);
                this.$emit("comunicarCuenta", data[0].urlapi);
            }
            this.select_cuenta.on('change', this.change_select_cuenta);
        }


    },
    mounted() {
       console.log(this.default);
       
        this.cargar_tipo_gasto().then((response) => {
            if (response.success) {
                this.initSelect(response.data)
            } else {
                this.alert_error_modal("Error en el servidor");
            }
        }).catch((err) => {
            this.alert_error_modal("Error al cargar los datos, recargar el navegador");
            console.log(err);
        });
    },
}
</script>

<style></style>