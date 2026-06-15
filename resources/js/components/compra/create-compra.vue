<template>
    <div class="intro-y box  mt-5">
        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60">
            <h2 class="font-medium text-base mr-auto text-primary">
                <Icon icon='table-list' class='mr-2' /> Formulario para crear un compra
            </h2>
        </div>
        <div class="p-5 ">
            <div class="card-body">

                <form id="form_crear_compra" method="POST" action="#">
                    <div id="vertical-form" class="p-5">
                        <div class="">
                            <div class="grid grid-cols-12 gap-6 mt-2">
                                <div class="intro-y col-span-12 lg:col-span-6">
                                    <label for="vertical-form-2" class="form-label">Ruc </label>
                                    <input name="ruc" v-model="ruc" type="text" class="form-control" placeholder="Ruc">
                                </div>

                            </div>

                            <div class="grid grid-cols-12 gap-6 mt-2">
                                <div class="intro-y col-span-12 lg:col-span-6">
                                    <label for="vertical-form-2" class="form-label">Tipo comprobante </label>

                                    <select class="form-control" name="" v-model="t_comprobante">
                                        <option value="B">Boleta </option>
                                        <option value="F">Factura</option>
                                    </select>

                                </div>
                                <div class="intro-y col-span-12 lg:col-span-3">
                                    <label for="vertical-form-2" class="form-label">Serie </label>
                                    <input name="compra_serie" v-model="compra_serie" type="text" class="form-control"
                                        placeholder="Serie">
                                </div>
                                <div class="intro-y col-span-12 lg:col-span-3">
                                    <label for="vertical-form-2" class="form-label">Correlativo </label>
                                    <input name="compra_correlativo" v-model="compra_correlativo" type="text"
                                        class="form-control" placeholder="Correlativo">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="invoice-footer">
                                    <div class="text-end mt-3">
                                        <button type="submit" v-if="is_created" class="btn btn-primary ms-1">Crear compra</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- Row start -->
                <div class="row">
                    <div class="col-12">
                        <div class="">
                            <div class="table-responsive">
                                <table class="table table-responsive" id="table-compra">
                                    <thead>
                                        <tr>
                                            <th colspan="7" class="pt-3 pb-3">
                                                Items / <button @click="add_item()" type="button" name="" id=""
                                                    class="btn btn-primary  btn-xs btn-block">
                                                    <Icon icon='plus' />
                                                </button>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>Decripcion</th>
                                            <th width="120px">Cantidad</th>
                                            <th width="120px">Precio</th>
                                            <th width="120px">Importe</th>
                                            <th width="120px">T.Gasto</th>
                                            <th width="120px">
                                                <Icon icon='cog' class='mr-2' />
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index_item) in items" :key="index_item">
                                            <td>
                                                <!-- Form group start -->
                                                <input type="text" v-model="items[index_item].descripcion" class="form-control" placeholder="Descripcion">
                                                <!-- Form group end -->
                                            </td>
                                            <td>
                                                <!-- Form group start -->
                                                <InputNumber @input="keyup_cantidad(index_item, $event)"
                                                    v-model="items[index_item].cantidad"
                                                    class="form-control border border-primary text-center"
                                                    inputId="minmax-buttons" mode="decimal" showButtons :min="0"
                                                    :max="100" />
                                                <!-- Form group end -->
                                            </td>
                                            <td>
                                                <InputNumber v-model="items[index_item].precio"
                                                    @input="keyup_precio(index_item, $event)"
                                                    class="form-control p-2 border border-success" name="monto"
                                                    placeholder="Monto del gasto" inputId="locale-us" locale="en-US"
                                                    :minFractionDigits="2" />
                                                <!-- Form group end -->
                                            </td>
                                            <td>
                                                S/. {{ item.importe }}
                                            </td>
                                            <td>
                                                <select-tipo-gasto v-on:change="change_tipo_id(index_item, $event)"></select-tipo-gasto>
                                                <!-- Form group end -->
                                            </td>
                                            <td>
                                                <!-- Form group start -->
                                                <button type="button" @click="delete_item(index_item)"
                                                    class="btn btn-primary btn-xs btn-block btn-danger">
                                                    <Icon icon='trash' />
                                                </button>
                                                <!-- Form group end -->
                                            </td>

                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="px-5 sm:px-20 pb-10  flex flex-col-reverse sm:flex-row">

                        <div class="text-center sm:text-right sm:ml-auto">
                            <div class="text-base text-slate-500">Importe total</div>
                            <div class="text-xl text-primary font-medium mt-2">{{ total }}</div>
                        </div>
                    </div>

                    <div class="preview" v-if="pagos.length">
                        <button @click="agregar_cuenta()" v-if="is_btn_pagos" type="button"
                            class="btn btn-primary mr-1 mt-2 mb-2">
                            <Icon icon="plus" />
                        </button>
                        <div>
                            <table class="table table-bordered table-pago table-hover" style="font-size: 11px;">
                                <thead>
                                    <tr>
                                        <th class="whitespace-nowrap">#</th>
                                        <th class="whitespace-nowrap">Cuenta</th>
                                        <th class="whitespace-nowrap">Monto</th>
                                        <th class="whitespace-nowrap text-center">Eliminar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(pago, index_pago) in pagos" :key="index_pago">
                                        <td>{{ pago.numero }}</td>
                                        <td> <select-cuenta v-on:change="change_cuenta(index_pago, $event)"
                                                @comunicarCuenta="escucharCuenta" v-model="cuentas_id"></select-cuenta>
                                        </td>
                                        <td>
                                            <InputNumber class="form-control p-2 border border-success" type="number"
                                                v-model="pagos[index_pago].monto"
                                                @input="keyup_cuenta(index_pago, $event)" name="monto"
                                                placeholder="Monto del gasto" inputId="locale-us" locale="en-US"
                                                :minFractionDigits="2" />

                                        </td>
                                        <td class="m-auto">
                                            <button @click="deleted_pago(index_pago)" v-if="index_pago == 1"
                                                type="button" name="" id="" class="btn btn-danger m-auto btn-xs">
                                                <Icon icon='trash' class='' />
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>


                </div>
                <!-- Row end -->

            </div>
        </div>
    </div>
</template>

<script>

import $ from 'jquery';

import Axios from 'axios';

import 'jquery-validation';
import 'jquery-validation/dist/localization/messages_es';
// mixin
import {
    myMixin
} from "../../mixin.js";

export default {
    mixins: [myMixin],
    data() {
        return {
            t_comprobante: "B",
            compra_correlativo: "",
            compra_serie: "",
            items: [],
            pagos: [],
            ruc: 0,
            is_btn_pagos: false,
            is_created: false
        }
    },
    computed: {
        total() {
            return this.items.reduce((sum, item) => {
                return sum + parseFloat(item.importe);
            }, 0);
        },
        totalCuota_pagos() {
            return this.pagos.reduce((sum, pa) => sum + parseFloat(pa.monto), 0);
        },
    },
    watch: {
        totalCuota_pagos: function (newValue) {
            if (this.total == newValue) {
                this.is_created = true;
            } else {
                this.is_created = false;
            }
        },
        total: function (newValue) {
            if (newValue > 0) {
                this.pagos[0] = {
                    numero: 1,
                    cuentas_id: "MFNhZTlXUkhTSmpWNDQ5ZUc2YUhldz09",
                    monto: parseFloat(newValue)
                };
            }
        },

    },
    methods: {
        deleted_pago(index) {
            this.pagos[0].monto = parseFloat(this.pagos[0].monto) + parseFloat(this.pagos[index].monto);
            this.pagos.splice(index, 1);
        },
        agregar_cuenta() {

            if (this.pagos.length < 2) {
                this.is_btn_pagos = false;
                this.pagos.push({
                    numero: 2,
                    cuentas_id: "MFNhZTlXUkhTSmpWNDQ5ZUc2YUhldz09",
                    monto: this.total - this.pagos[0].monto
                });
            }

        },
        keyup_cuenta(index, evento) {
            if (this.totalCuota_pagos != this.total) {
                this.is_btn_pagos = false;
            } else {
                this.is_btn_pagos = true;
            }
        },
        change_cuenta(index, evento) {
            this.pagos[index].cuentas_id = evento.target.value;
        },
        change_tipo_id(index, evento){
            console.log(evento);
             this.items[index].tipo_gasto_id = evento.target.value;
        },
        keyup_cantidad(index, event) {
            this.items[index].cantidad = event.value
            if (this.items[index].cantidad == 0) {
                this.items[index].cantidad = 1
            }
            this.items[index].importe = this.items[index].cantidad * this.items[index].precio;

        },
        keyup_precio(index, event) {
            this.items[index].precio = event.value
            this.items[index].importe = this.items[index].cantidad * this.items[index].precio;
        },
        delete_item(index) {
            this.items.splice(index, 1);
        },
        add_item() {
            this.items.push({
                descripcion: "",
                tipo_gasto_id: "",
                cantidad: 1,
                precio: 0,
                importe: 0,
            });
        },
        crear_compra() {
            if (this.items.length == 0) {
                this.alert_warning("debe haber items para poder continuar")
            }

            const data = {
                pagos: this.pagos,
                compra_serie: this.compra_serie,
                ruc: this.ruc,
                compra_correlativo: this.compra_correlativo,
                items: this.items,
                total: this.total,
                t_comprobante: this.t_comprobante
            };

            const headers = this.headers;

            this.loading_start();

            Axios
                .post("/compra", data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {
                        window.location.assign(response.data.data);
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
    },
    mounted() {
        this.items.push({
            descripcion: "",
            tipo_gasto_id: "",
            cantidad: 1,
            precio: 0,
            importe: 0,
        });


        var self = this;

        $("#form_crear_compra").validate({
            rules: {
                compra_serie: {
                    required: true,
                },
                ruc: {
                    required: true,
                    number: true,
                    min: 11
                },
                compra_correlativo: {
                    required: true,
                }
            },
            submitHandler: function (form) {
                try {
                    self.crear_compra();
                } catch (error) {
                    console.log(error);
                }
                return false;
            }
        });


    },
}
</script>

<style></style>