<template>
    <select ref="select_analistas" class="form-control pb-2"></select>
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
            analistas: this.$attrs.analistas,
            select_analistas: null
        }
    },
    mounted() {
        this.initSelect();
    },
    watch: {
        analistas: function (newValue, oldValue) {
            this.select_analistas.clearOptions();
            this.select_analistas.addOptions(newValue);
        }
    },
    methods: {
        change_select_analistas(event) {
            this.$emit("comunicarAnalista", event);
        },
        initSelect() {

            this.select_analistas = new TomSelect(this.$refs.select_analistas, {
                valueField: 'urlapi',
                labelField: 'name',
                searchField: 'name',
                options: this.analistas,
                persist: false,
                maxItems: 15,
                plugins: ['remove_button'],

                render: {
                    option: function (item, escape) {
                        return `<div  >
                                                <div class="box px-4 py-4 mb-1 flex items-center ">
                                                    <div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">
                                                        <img alt="Midone - HTML Admin Template" src="../../dist/images/avatar/${item.sexo == "M" ? "masculino" : "femenino"}.png">
                                                    </div>
                                                    <div class="ml-4 mr-auto">
                                                        <div class="font-medium">${item.name} | ${item.lastname}</div> 
                                                    </div>
                                                    
                                                </div>
                                            </div>`;
                    },

                }
            });

            this.select_analistas.on('change', this.change_select_analistas);
        }
    }
}
</script>

<style></style>