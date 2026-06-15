<template>
    <select ref="select_tipo_cliente_select_estado" class="form-control"></select>
</template>

<script>
import 'tom-select/dist/css/tom-select.css';
import TomSelect from 'tom-select';
// mixin
import {
    myMixin
} from "../../mixin.js";


export default {
    mixins: [myMixin],
    data() {
        return { 
            tipo_cliente_select_estado: [
                { "name":"Vencido" },
                { "name":"Hoy" },
                { "name":"Pendiente" }
            ],
            select_tipo_cliente_select_estado: null
        }
    },
    mounted() {
        this.initSelect();
    },
    watch: {
        tipo_cliente_select_estado: function (newValue, oldValue) {
            this.select_tipo_cliente_select_estado.clearOptions();
            this.select_tipo_cliente_select_estado.addOptions(newValue);
        }
    },
    methods: {
        change_select_tipo_cliente_select_estado(event) {
            this.$emit("comunicarSelectEstado", event);
        },
        initSelect() {

            this.select_tipo_cliente_select_estado = new TomSelect(this.$refs.select_tipo_cliente_select_estado, {
                valueField: 'name',
                labelField: 'name',
                searchField: 'name',
                options: this.tipo_cliente_select_estado,
               
                plugins: ['remove_button'],

                render: {
                    option: function (item, escape) {
                        return `<div  >
                                                <div class="box px-4 py-4 mb-1 flex items-center "> 
                                                    <div class="ml-4 mr-auto">
                                                        <div class="font-medium">${item.name}</div> 
                                                    </div> 
                                                </div>
                                            </div>`;
                    },

                }
            });

            this.select_tipo_cliente_select_estado.on('change', this.change_select_tipo_cliente_select_estado);
        }
    }
}
</script>

<style></style>