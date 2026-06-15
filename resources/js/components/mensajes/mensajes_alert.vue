<template>
     
 
    <Dialog v-model:visible="is_modal_msj" maximizable modal header="Imprimir solicitud y contrato"
        :style="{ width: '90rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
        mensaje
    </Dialog>
 
 
</template>

<script>
import $ from 'jquery';
import { createApp, h } from 'vue';
import TomSelect from 'tom-select';
import Axios from 'axios';

import "jquery-validation";
import "jquery-validation/dist/localization/messages_es";

import moment from 'moment';
import 'moment/locale/es';

// plugins para la creacion de pdf de una manera elegante
import {
    Canvg
} from 'canvg';
import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';
import 'jspdf-autotable';
import * as svg2pdf from 'svg2pdf.js';


// mixin
import {
    myMixin
} from "../../mixin.js";


import { renderToString } from '@vue/server-renderer';

//svg   
import documentInf from '../solicitudes/svg/documento-inf.vue';


import { ref } from "vue";
import { get } from 'jquery';

export default {
    mixins: [myMixin],
    data() {
        return {
            is_modal_msj: true 
        }
    },
    computed: {
         
    },
    watch: {
        
    },
    methods: {
        
    },
    mounted() {

        console.log("iniciando mensajes");
 
    }
}
</script>

<style></style>