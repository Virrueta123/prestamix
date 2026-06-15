<template>
    <select ref="select_frecuencia_pago" class="form-control"></select>
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
            frecuencia_pago: [
                {"name":"Diario"},
                {"name":"Mensual"},
                {"name":"Semanal"},
            ],
            select_frecuencia_pago: null
        }
    },
    mounted() {
        this.initSelect();
    },
    watch: {
        frecuencia_pago: function (newValue, oldValue) {
            this.select_frecuencia_pago.clearOptions();
            this.select_frecuencia_pago.addOptions(newValue);
        }
    },
    methods: {
        change_select_frecuencia_pago(event) {
            this.$emit("comunicarFrecuenciaPago", event);
        },
        initSelect() {

            this.select_frecuencia_pago = new TomSelect(this.$refs.select_frecuencia_pago, {
                valueField: 'name',
                labelField: 'name',
                searchField: 'name',
                options: this.frecuencia_pago,
                persist: false,
                maxItems: 15,
                plugins: ['remove_button'],

                render: {
                    option: function (item, escape) {
                        return `<div>
                                                <div class="box px-4 py-4 mb-1 flex items-center ">
                                                    <div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">
                                                        <i class="fa fa-2x fa-calendar-days"></i>
                                                    </div>
                                                    <div class="ml-4 mr-auto">
                                                        <div class="font-medium"> ${item.name} </div> 
                                                    </div>
                                                    
                                                </div>
                                            </div>`;
                    },

                }
            });

            this.select_frecuencia_pago.on('change', this.change_select_frecuencia_pago);
        }
    }
}
</script>

<style></style>