<template>

    <div>
        <div class="intro-y box overflow-hidden mt-5">
            <div class="border-b border-slate-200/60 dark:border-darkmode-400 text-center sm:text-left">
                <div class="px-5 py-10 sm:px-20 sm:py-20">
                    <div class="text-primary font-semibold text-3xl">Panel de venta</div>
                </div>
            </div>
            <div class="box">
                <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
                    <button class="btn btn-primary shadow-md mr-2">Add New Category</button>
                    <div class="dropdown">
                        <button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">
                            <span class="w-5 h-5 flex items-center justify-center"> <svg
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" icon-name="plus" class="lucide lucide-plus w-4 h-4"
                                    data-lucide="plus">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg> </span>
                        </button>
                        <div class="dropdown-menu w-40">
                            <ul class="dropdown-content">
                                <li>
                                    <a href="" class="dropdown-item"> <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            icon-name="printer" data-lucide="printer"
                                            class="lucide lucide-printer w-4 h-4 mr-2">
                                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                            <path
                                                d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2">
                                            </path>
                                            <rect x="6" y="14" width="12" height="8"></rect>
                                        </svg> Print </a>
                                </li>
                                <li>
                                    <a href="" class="dropdown-item"> <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            icon-name="file-text" data-lucide="file-text"
                                            class="lucide lucide-file-text w-4 h-4 mr-2">
                                            <path d="M14.5 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V7.5L14.5 2z">
                                            </path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                            <line x1="16" y1="13" x2="8" y2="13"></line>
                                            <line x1="16" y1="17" x2="8" y2="17"></line>
                                            <line x1="10" y1="9" x2="8" y2="9"></line>
                                        </svg> Export to Excel </a>
                                </li>
                                <li>
                                    <a href="" class="dropdown-item"> <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            icon-name="file-text" data-lucide="file-text"
                                            class="lucide lucide-file-text w-4 h-4 mr-2">
                                            <path d="M14.5 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V7.5L14.5 2z">
                                            </path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                            <line x1="16" y1="13" x2="8" y2="13"></line>
                                            <line x1="16" y1="17" x2="8" y2="17"></line>
                                            <line x1="10" y1="9" x2="8" y2="9"></line>
                                        </svg> Export to PDF </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="hidden md:block mx-auto text-slate-500">Showing 1 to 10 of 150 entries</div>
                    <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                        <div class="w-56 relative text-slate-500">
                            <input type="text" class="form-control w-56 box pr-10" placeholder="Search...">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" icon-name="search"
                                class="lucide lucide-search w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0"
                                data-lucide="search">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-5 sm:px-16 py-10 sm:py-20">
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="border-b-2 dark:border-darkmode-400 whitespace-nowrap">Descripcion</th>
                                <th class="border-b-2 dark:border-darkmode-400 text-right whitespace-nowrap">Cantidad
                                </th>
                                <th class="border-b-2 dark:border-darkmode-400 text-right whitespace-nowrap">Precio</th>
                                <th class="border-b-2 dark:border-darkmode-400 text-right whitespace-nowrap">Subtotal
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in items" :key="index">
                                <td class="border-b dark:border-darkmode-400">
                                    <div class="font-medium whitespace-nowrap">{{ item.descripcion }}</div>
                                    <div class="text-slate-500 text-sm mt-0.5 whitespace-nowrap"
                                        v-if="item.genero == 'M'">Masculino</div>
                                    <div class="text-slate-500 text-sm mt-0.5 whitespace-nowrap" v-else>Femenino</div>
                                </td>
                                <td class="text-right border-b dark:border-darkmode-400 w-32">{{ item.cantidad }}</td>
                                <td class="text-right border-b dark:border-darkmode-400 w-32">{{ item.precio }}</td>
                                <td class="text-right border-b dark:border-darkmode-400 w-32 font-medium">{{
                                    item.subtotal }}</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>


</template>

<script>
import $ from 'jquery';

import 'tom-select/dist/css/tom-select.css';
import TomSelect from 'tom-select';
import Axios from 'axios';

import 'jquery-validation';
import 'jquery-validation/dist/localization/messages_es';

import moment from 'moment';
import 'moment/locale/es';

// mixin
import {
    myMixin
} from "../../../mixin.js";

export default {
    mixins: [myMixin],
    data() {
        return {
            items: [],
            response:[]
        }
    },
    methods: {
        // Your methods here
        agregar_item() {
            console.log("datos cargados correctamente",this.response);
            if(this.response.success==true){
                var item = this.response.data;
                this.items.push({
                    descripcion: item.descripcion,
                    cantidad: 1,
                    precio: item.precio_venta,
                    subtotal: item.precio_venta,
                    genero: item.genero 
                });
                this.alert_success("Producto agregado correctamente.");
            }else{
                this.alert_error_modal("No se pudo agregar el item, intentelo de nuevo.");
            }
        }
    },
    created() {
      

    },
    mounted() {

        var self = this;

        var pusher = new window.Pusher('d531de99a6a41804aaa3', {
            cluster: 'mt1'
        });

        var channel = pusher.subscribe('channel-pos-article');
        channel.bind('pos-article', function (data) {
            console.log("este mensaje fue ejecutado con exito en pos", data);
            self.response = data;
            self.agregar_item();
        });

        $("#form_crear_cuenta").validate({
            rules: {
                saldo_inicial: {
                    required: true
                },
                referencia: {
                    required: true
                },
                recepcionista: {
                    required: true
                }
            },
            submitHandler: function (form) {
                self.crear_cuenta();
                return false;
            }
        });

    }
}
</script>

<style></style>