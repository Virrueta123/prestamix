<template>
    <select ref="select_tipo_cliente" class="form-control"></select>
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
            tipo_cliente: [
                { "name":"particular" },
                { "name":"tarjeta privada" },
                { "name":"tarjeta estado" }
            ],
            select_tipo_cliente: null
        }
    },
    mounted() {
        this.initSelect();
    },
    watch: {
        tipo_cliente: function (newValue, oldValue) {
            this.select_tipo_cliente.clearOptions();
            this.select_tipo_cliente.addOptions(newValue);
        }
    },
    methods: {
        change_select_tipo_cliente(event) {
            this.$emit("comunicarTipoCliente", event);
        },
        initSelect() {

            this.select_tipo_cliente = new TomSelect(this.$refs.select_tipo_cliente, {
                valueField: 'name',
                labelField: 'name',
                searchField: 'name',
                options: this.tipo_cliente,
                persist: false,
                maxItems: 15,
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

            this.select_tipo_cliente.on('change', this.change_select_tipo_cliente);
        }
    }
}
</script>

<style></style>