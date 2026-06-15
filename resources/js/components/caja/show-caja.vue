<template>

    <div v-if="get_caja.status == 'C'" class="alert alert-success text-white show flex items-center mb-2" role="alert">
        ESTADO DE LA CAJA = CERRADO
    </div>

    <div v-else class="alert alert-warning show flex items-center mb-2" role="alert">
        ESTADO DE LA CAJA = ABIERTA
    </div>

    <div class="report-box-2 intro-y mt-12 sm:mt-5">
        <div class="box sm:flex">
            <div class="px-8 py-12 flex flex-col justify-center flex-1">

                <Icon icon='cash-register' class='mr-2 fa-4x text-success' />
                <div class="relative text-4xl text-center font-medium mt-12 pl-4 ml-0.5"> {{
                    formatear_dinero_soles(total_caja) }} </div>
                <report-analista-caja :get_caja="get_caja"></report-analista-caja>
            </div>
            <div
                class="px-8 py-12 flex flex-col justify-center flex-1 border-t sm:border-t-0 sm:border-l border-slate-200 dark:border-darkmode-300 border-dashed">
                <a type="button" :href=" '/caja_reporte/'+ get_caja.urlapi " class="btn btn-primary btn-lg btn-block">  <Icon icon='print' class='mr-2' />  Descargar pdf </a>
                <div class="text-slate-500 text-xs">Monto de apertura de caja</div>
                <div class="mt-1.5 flex items-center">
                    <div class="text-base">{{ formatear_dinero_soles(get_caja.saldo_inicial) }}</div>

                </div>
                <div class="text-slate-500 text-xs mt-5">Ingresos</div>
                <div class="mt-1.5 flex items-center">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="whitespace-nowrap">Ingresos general</th>
                                <th class="whitespace-nowrap">{{ formatear_dinero_soles(get_caja.ingresosefectivo +
                                    get_caja.ingresoscuenta) }}</th>
                            </tr>
                            <tr>
                                <th class="whitespace-nowrap bg-success text-white">Ingresos efectivo</th>
                                <th class="whitespace-nowrap bg-success text-white">{{
                                    formatear_dinero_soles(get_caja.ingresosefectivo) }}
                                </th>
                            </tr>
                            <tr>
                                <th class="whitespace-nowrap">Ingresos cuentas</th>
                                <th class="whitespace-nowrap">{{ formatear_dinero_soles(get_caja.ingresoscuenta) }}</th>
                            </tr>
                            <tr>
                                <th class="whitespace-nowrap">Informacion de las cuentas</th>
                                <th class="mr-0 pr-0 m-sm-1 p-sm-1 p-0">
                                    <table class="table table-bordered mr-0 pr-0">
                                        <thead>
                                            <tr>
                                                <th class="whitespace-nowrap">Nombre de la cuenta</th>
                                                <th class="whitespace-nowrap"> Monto </th>
                                            </tr>
                                            <tr v-for="(g_t, index_g_t) in get_tabla_tarjeta_ingresos" :key="index_g_t">
                                                <td> {{ g_t.cuenta.cuentas_entidad }} </td>
                                                <td> {{ g_t.total }} </td>
                                            </tr>
                                            
                                        </thead>
                                    </table>
                                </th>
                            </tr>
                        </thead>
                    </table>

                </div>
                <div class="text-slate-500 text-xs mt-5">Gastos</div>
                <div class="mt-1.5 flex items-center">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="whitespace-nowrap">Gasto general</th>
                                <th class="whitespace-nowrap">{{ formatear_dinero_soles(get_caja.gastosefectivo +
                                    get_caja.gastoscuenta) }}</th>
                            </tr>
                            <tr>
                                <th class="whitespace-nowrap bg-success text-white">Gasto efectivo</th>
                                <th class="whitespace-nowrap bg-success text-white">{{
                                    formatear_dinero_soles(get_caja.gastosefectivo) }}
                                </th>
                            </tr>
                            <tr>
                                <th class="whitespace-nowrap ">Gasto cuentas</th>
                                <th class="whitespace-nowrap">{{ formatear_dinero_soles(get_caja.gastoscuenta) }}</th>
                            </tr>
                        </thead>
                    </table>

                </div>
                <div class="text-slate-500 text-xs mt-5">Monto Finales</div>
                <div class="mt-1.5 flex items-center">

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="whitespace-nowrap bg-success text-white">Monto de cierre de caja efectivo
                                </th>
                                <th class="whitespace-nowrap bg-success text-white">{{
                                    formatear_dinero_soles(total_caja) }}</th>
                            </tr>
                            <tr>
                                <th class="whitespace-nowrap">Monto total en las cuentas</th>
                                <th class="whitespace-nowrap">{{ formatear_dinero_soles(get_caja.ingresoscuenta -
                                    get_caja.gastoscuenta) }}
                                </th>
                            </tr>

                        </thead>
                    </table>

                </div>



            </div>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 md:col-span-6">
            <div class="box">
                <table-ingreso :get_id="get_caja.urlapi"></table-ingreso>
            </div>
        </div>

        <div class="intro-x col-span-12 md:col-span-6">
            <div class="box">
                <table-gastos :get_id="get_caja.urlapi"></table-gastos>
            </div>
        </div>
    </div>
</template>

<script>
// mixin
import {
    myMixin
} from "../../mixin.js";
export default {
    mixins: [myMixin],
    data() {
        return {
            get_caja: this.$attrs.get_caja,
            total_caja: 0,
            get_tabla_tarjeta_ingresos: this.$attrs.get_tabla_tarjeta_ingresos
        }
    },
    mounted() {
        this.total_caja = ((parseFloat(this.get_caja.saldo_inicial) + parseFloat(this.get_caja.ingresosefectivo)) - parseFloat(this.get_caja.gastosefectivo));
    },
}
</script>

<style></style>