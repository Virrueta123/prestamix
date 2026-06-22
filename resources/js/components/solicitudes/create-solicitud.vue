<template>
    <div class="box w-full max-w-full mx-auto solicitud-form-mobile">
        <Toast />

        <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
            <h2 class="font-medium text-base mr-auto" v-if="solicitudparalelo == ''">
                Formulario para la creaccion de una solicitud de un prestamo
            </h2>
            <h2 class="font-medium text-base mr-auto" v-else>
                Formulario para la creaccion de una solicitud de paralelo para la solicitud {{ solicitudparalelo.code }}
            </h2>
        </div>
        <div class="intro-y box px-5 pt-5 mt-5" v-if="solicitudparalelo != ''">
            <div class="flex flex-col lg:flex-row border-b border-slate-200/60 dark:border-darkmode-400 pb-5 -mx-5">
                <div class="flex flex-1 px-5 items-center justify-center lg:justify-start">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 flex-none lg:w-32 lg:h-32  relative">
                        <img alt="Midone - HTML Admin Template" src="../../../../public/dist/images/Draw/paralelo.svg">
                    </div>
                    <div class="ml-5">
                        <div class="w-24 sm:w-40 truncate sm:whitespace-normal font-medium text-lg">
                            {{ solicitudparalelo.cliente.cli_nombre }}</div>
                        <div class="text-slate-500">{{ solicitudparalelo.cliente.cli_apellido }}</div>
                    </div>
                </div>
                <div
                    class="mt-6 lg:mt-0 flex-1 px-5 border-l border-r border-slate-200/60 dark:border-darkmode-400 border-t lg:border-t-0 pt-5 lg:pt-0">
                    <div class="font-medium text-center lg:text-left lg:mt-3">Descripcion de esta solicitud</div>
                    <div class="flex flex-col justify-center items-center lg:items-start mt-4">
                        <div class="truncate sm:whitespace-normal flex items-center"> <strong>Tipo de solicitud
                            </strong>
                            <Icon icon='play' class='mr-2' /> {{ solicitudparalelo.tipo_solicitud }}
                        </div>
                        <div class="truncate sm:whitespace-normal flex items-center mt-3"> <strong>Frecuencia de pago
                            </strong>
                            <Icon icon='play' class='mr-2' /> {{ solicitudparalelo.prestamo.frecuencia_pagos }}
                        </div>

                    </div>
                </div>
                <div
                    class="mt-6 lg:mt-0 flex-1 flex items-center justify-center px-5 border-t lg:border-0 border-slate-200/60 dark:border-darkmode-400 pt-5 lg:pt-0">
                    <div class="text-center rounded-md w-20 py-3">
                        <div class="font-medium text-primary text-xl">{{ solicitudparalelo.prestamo.moto_credito }}
                        </div>
                        <div class="text-slate-500">M.Credito</div>
                    </div>
                    <div class="text-center rounded-md w-20 py-3">
                        <div class="font-medium text-primary text-xl">{{ solicitudparalelo.prestamo.intervalo }}</div>
                        <div class="text-slate-500">Intervalo</div>
                    </div>
                    <div class="text-center rounded-md w-20 py-3">
                        <div class="font-medium text-primary text-xl"> {{ solicitudparalelo.prestamo.d_t }}</div>
                        <div class="text-slate-500">D.T</div>
                    </div>
                </div>
            </div>
        </div>
        <form id="add_solicitud" action="#" method="POST">
            <div id="input" class="p-5" :class="{ 'cliente-dropdown-open': clienteDropdownOpen }">

                <div class="cliente-select-wrapper">
                    <label for="regular-form-1" class="form-label">Buscar Cliente</label>
                    <div class="input-group mt-2">

                        <select ref="select_cliente" class="form-control">
                        </select>

                        <button type="button" class="btn btn-primary btn-sm btn-select" @click="modal_add_cliente()">
                            <Icon icon="plus" />
                        </button>
                    </div>
                </div>

                <div v-if="!is_solicitud" class="solicitud-empty-state box mt-4 pt-5 pb-5">
                    <center>
                        <img class="card-img-top" width="38%" src="../../../../public/dist/images/Draw/schedule.svg" alt="">
                    </center>
                </div>

                <div class="solicitud-form-content" v-if="is_solicitud">
                    <div
                        class="flex flex-col sm:flex-row items-center pt-5 pb-5 border-b border-b border-slate-200/60 dark:border-darkmode-400">
                        <h2 class="font-medium text-base mr-auto">
                            Solicitud de credito
                        </h2>

                    </div>


                    <div class="grid grid-cols-12 gap-2 mt-2">
                        <div class=" col-span-12 lg:col-span-12">
                            <label>Tipo de Vivienda</label>
                            <div class="flex flex-col sm:flex-row mt-5">
                                <div class="form-check mr-2 sm:mt-0">
                                    <input id="Propia" class="form-check-input" type="radio" name="tipo_vivienda"
                                        checked v-model="tipo_vivienda" value="Propia">
                                    <label class="form-check-label" for="Propia">Propia</label>
                                </div>

                                <div class="form-check mr-2 sm:mt-0">
                                    <input id="Alquilada" class="form-check-input" type="radio" name="tipo_vivienda"
                                        v-model="tipo_vivienda" value="Alquilada">
                                    <label class="form-check-label" for="Alquilada">Alquilada</label>
                                </div>

                                <div class="form-check mr-2 sm:mt-0">
                                    <input id="Familiar" class="form-check-input" type="radio" name="tipo_vivienda"
                                        v-model="tipo_vivienda" value="Familiar">
                                    <label class="form-check-label" for="Familiar">Familiar</label>
                                </div>

                                <div class="form-check mr-2 sm:mt-0">
                                    <input id="Noble" class="form-check-input" type="radio" name="tipo_vivienda"
                                        v-model="tipo_vivienda" value="Noble">
                                    <label class="form-check-label" for="Noble">Noble</label>
                                </div>

                                <div class="form-check mr-2 sm:mt-0">
                                    <input id="Rustico" class="form-check-input" type="radio" name="tipo_vivienda"
                                        v-model="tipo_vivienda" value="Rustico">
                                    <label class="form-check-label" for="Rustico">Rustico</label>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="col-span-12 lg:col-span-12">
                            <label for="vertical-form-2" class="form-label">Analistas </label>
                            <analistas @comunicarAnalista="escucharAnalista" :analistas="analistas" name="analista_id"
                                v-model="analista_id"></analistas>
                        </div>
                    </div>

                    <div class="mt-2">
                        <label for="regular-form-1" class="form-label">Dni/Ruc</label>
                        <div class="input-group ">

                            <input name="solicitud_documento" style="z-index: 1;" v-model="solicitud_documento"
                                type="text" class="form-control"
                                placeholder="ejemplo.. Dni : 76234501 o Ruc 10214579281">

                            <button type="button" class="btn btn-primary btn-select"
                                @click="search_solicitud_documento()">
                                <Icon icon="search" />
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="col-span-12 lg:col-span-12">
                            <label for="vertical-form-2" class="form-label">Nombre y apellido / Razón Social ( <Span
                                    class="text-success"> {{ estado_ruc }} </Span> ) </label>
                            <input name="solicitud_nombre" v-model="solicitud_nombre" type="text" class="form-control"
                                placeholder="Nombre y apellido / Razón Social">
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Giro de empresa o negocio </label>
                            <input name="solicitud_giro" v-model="solicitud_giro" type="text" class="form-control"
                                placeholder="Giro de empresa o negocio">
                        </div>

                        <div class="col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Antiguedad </label>
                            <input name="solicitud_antiguedad" v-model="solicitud_antiguedad" type="text"
                                class="form-control" placeholder="Antiguedad">
                        </div>
                    </div>




                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Dirección del negocio </label>
                            <input name="solicitud_direccion_negocio" v-model="solicitud_direccion_negocio" type="text"
                                class="form-control" placeholder="Dirección del negocio">
                        </div>

                        <div class="col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Lugar </label>
                            <input name="solicitud_lugar" v-model="solicitud_lugar" type="text" class="form-control"
                                placeholder="Lugar">
                        </div>
                    </div>


                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Referencia de ubicación del
                                negocio</label>
                            <input name="solicitud_referencia_negocio" v-model="solicitud_referencia_negocio"
                                type="text" class="form-control" placeholder="Referencia de ubicación del negocio">
                        </div>

                        <div class="col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Referencia de cliente </label>
                            <input name="solicitud_referencia_cliente" v-model="solicitud_referencia_cliente"
                                type="text" class="form-control" placeholder="Referencia de cliente">
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="col-span-12 lg:col-span-12">
                            <label for="vertical-form-2" class="form-label">Dirección del domicilio</label>
                            <input name="solicitud_domicilio" v-model="solicitud_domicilio" type="text"
                                class="form-control" placeholder="Dirección del domicilio">
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6 mt-2">

                        <div class="col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Nombre del conyugue </label>
                            <input name="solicitud_nombre_conyugue" v-model="solicitud_nombre_conyugue" type="text"
                                class="form-control" placeholder="Nombre del conyugue">
                        </div>


                        <div class="col-span-12 lg:col-span-3">
                            <label for="regular-form-1" class="form-label">Dni conyugue</label>
                            <div class="input-group mt-1">

                                <input name="solicitud_conyugue_dni" v-model="solicitud_conyugue_dni" type="text"
                                    class="form-control" placeholder="ejemplo.. 76234501">

                                <button type="button" class="btn btn-primary btn-select" @click="search_dni_conyugue()">
                                    <Icon icon="search" />
                                </button>
                            </div>
                        </div>

                        <div class="intro-y col-span-12 lg:col-span-3">
                            <label for="vertical-form-2" class="form-label">Ruc conyugue</label>
                            <input name="solicitud_conyugue_ruc" v-model="solicitud_conyugue_ruc" type="text"
                                class="form-control" placeholder="Ruc conyuque">
                        </div>


                    </div>
                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Nombre de la entidad bancaria de esta
                                <strong>solicitud</strong> </label>
                            <input name=" solicitud_entidad_tarjeta" v-model="solicitud_entidad_tarjeta" type="text"
                                class="form-control" placeholder="Nombre de la tarjeta">
                        </div>
                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Numero de cuenta para esta
                                <strong>solicitud</strong> </label>
                            <input name="solicitud_tarjeta" v-model="solicitud_tarjeta" type="text" class="form-control"
                                placeholder="Numero de la tarjeta">
                        </div>
                    </div>

                    <div class=" flex flex-col sm:flex-row items-center mt-6">
                        <h2 class="text-lg font-medium mr-auto">
                            <Icon icon="sack-dollar" /> Datos del prestamo
                        </h2>
                        <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
                            <button v-on:click="calcular_cronograma()" type="button"
                                class="btn btn-outline-primary w-1/2 sm:w-auto mr-2">
                                <Icon icon="sack-dollar" /> Calcular Cronograma
                            </button>

                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-3">
                            <label for="vertical-form-2" class="form-label">Monto de credito</label>
                            <div class="input-group">
                                <div id="input-group-email" class="input-group-text">S/.</div>

                                <InputNumber class="form-control p-2 border border-success" v-model="monto_credito"
                                    name="monto_credito" placeholder="Monto del credito" inputId="locale-us"
                                    locale="en-US" :minFractionDigits="2" />
                            </div>
                        </div>

                        <div class="intro-y col-span-12 lg:col-span-3">
                            <label for="vertical-form-2" class="form-label"> Intereses </label>
                            <div class="input-group">
                                <div id="input-group-email" class="input-group-text">%</div>
                                <input name="interes" v-model="interes" type="number" class="form-control"
                                    placeholder="0">
                            </div>
                        </div>

                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label"> Destino </label>
                            <input name="destino" v-model="destino" type="text" class="form-control"
                                placeholder="Destino">
                        </div>

                    </div>

                    <div class="grid grid-cols-12 gap-2 mt-2">
                        <div class=" col-span-12 lg:col-span-6">
                            <label>Frecuencia de los pagos </label>
                            <div class="flex flex-col sm:flex-row mt-4">
                                <div class="form-check mr-2 sm:mt-0">
                                    <input id="Quincenal" class="form-check-input" type="radio" name="frecuencia_pagos"
                                        checked v-model="frecuencia_pagos" value="Quincenal">
                                    <label class="form-check-label" for="Quincenal">Quincenal</label>
                                </div>
                                <div class="form-check mr-2 mt-2 sm:mt-0">
                                    <input id="Diario" class="form-check-input" type="radio" name="frecuencia_pagos"
                                        v-model="frecuencia_pagos" value="Diario">
                                    <label class="form-check-label" for="Diario">Diario</label>
                                </div>
                                <div class="form-check mr-2 mt-2 sm:mt-0">
                                    <input id="Semanal" v-model="frecuencia_pagos" class="form-check-input" type="radio"
                                        name="frecuencia_pagos" value="Semanal">
                                    <label class="form-check-label" for="Semanal">Semanal</label>
                                </div>
                                <div class="form-check mr-2 mt-2 sm:mt-0">
                                    <input id="Mensual" v-model="frecuencia_pagos" class="form-check-input" type="radio"
                                        name="frecuencia_pagos" value="Mensual">
                                    <label class="form-check-label" for="Mensual">Mensual</label>
                                </div>

                            </div>
                        </div>
                        <div class="intro-y col-span-12 mr-4 lg:col-span-6">

                            <label for="vertical-form-2" class="form-label"> {{ frecuencia_pagos_a }} </label>
                            <input name="intervalo" v-model="intervalo" type="number" class="form-control"
                                :placeholder="'2 ' + frecuencia_pagos_a">

                        </div>
                    </div>

                    <div class="mt-4"></div>
                    <hr>

                    <div class="grid grid-cols-12 gap-6 mt-2">

                        <div class=" col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label" id="switch1">Este pago es en
                                <strong>Deseas cambiar la fecha de las cuotas del prestamo?</strong>
                                <br>
                                <span class="text-danger">
                                    <Icon icon="info-circle" /> Ojo la fecha debe ser menor a 20 dias de la fecha de hoy
                                </span>
                            </label>

                            <div class="form-check form-switch">
                                <label class="form-check-label mr-2" for="checkbox-switch-7">No </label>
                                <input id="checkbox-switch-7" v-model="is_fecha_pago" checked class="form-check-input"
                                    type="checkbox">
                                <label class="form-check-label ml-2" for="checkbox-switch-7">Si </label>
                            </div>
                        </div>
                    </div>


                    <div class=" col-span-12 lg:col-span-12 mt-3" v-if="is_fecha_pago">
                        <label for="vertical-form-2" class="form-label"> Fecha de nacimiento </label>
                        <datepicker class="form-control col-span-12" v-model="fecha_de_pago_cuota"
                            placeholder="hacer click para seleccionar" :styles="{ border: '2px solid #00ff00' }"
                            :disabled-dates="rango_maximo" language="es"></datepicker>
                    </div>


                    <div class="report-box-1 intro-y mt-2" v-if="is_cronograma">
                        <div class="box sm:flex">
                            <div class="px-2 py-2 flex flex-col justify-center flex-1">

                                <div class="intro-y flex-1 px-2 py-2">
                                    <center> <img class="card-img-top" width="38%"
                                            src="../../../../public/dist/images/Draw/savings.svg" alt="">
                                    </center>
                                    <div class="text-xl font-medium text-center mt-10">Monto credito</div>

                                    <div class="flex justify-center">
                                        <div class="relative text-5xl font-semibold mt-8 mx-auto text-success">
                                            <span class="absolute text-2xl top-0 left-0 -ml-5">S/</span> {{
                                                numeralFormat(monto_credito, '0,0.00') }}
                                        </div>
                                    </div>
                                    <!-- <button type="button" class="btn btn-rounded-primary py-3 px-4 block mx-auto mt-8">PURCHASE NOW</button> -->
                                </div>
                            </div>
                            <div
                                class="px-8 py-12 flex flex-col justify-center flex-1 border-t sm:border-t-0 sm:border-l border-slate-200 dark:border-darkmode-300 border-dashed">
                                <div class="text-slate-500 text-xs">Plazo en {{ frecuencia_pagos_a }}</div>
                                <div class="mt-1.5 flex items-center">
                                    <div class="text-base">{{ intervalo }} {{ frecuencia_pagos_a }}</div>

                                </div>
                                <div class="text-slate-500 text-xs mt-5">TASA MENSUAL</div>
                                <div class="mt-1.5 flex items-center">
                                    <div class="text-base">{{ interes }} %</div>

                                </div>
                                <div class="text-slate-500 text-xs mt-5">TASA DIARIA</div>
                                <div class="mt-1.5 flex items-center">
                                    <div class="text-base"> {{ tasa_diaria }} %</div>

                                </div>
                                <div class="text-slate-500 text-xs mt-5">CUOTA</div>
                                <div class="mt-1.5 flex items-center">
                                    <div class="text-base">S/. {{ cuotas }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-12">
                            <div class="intro-y flex flex-col sm:flex-row items-center mt-8">
                                <h2 class="text-lg font-medium mr-auto">
                                    Cronograma
                                    <Icon icon="hourglass-half" />
                                    <div
                                        class="py-1 px-2 rounded-full text-xs bg-success text-white text-center cursor-pointer font-medium">
                                        {{ frecuencia_pagos }} </div>
                                </h2>
                                <div class="w-full sm:w-auto flex mt-4 sm:mt-0" v-if="is_cronograma">
                                    <button type="button" v-on:click="generarPDF()"
                                        class="btn btn-outline-primary w-1/2 sm:w-auto mr-2">
                                        <Icon icon="print" /> Descargar Cronograma Simulacion
                                    </button>

                                </div>
                            </div>

                            <div class="overflow-x-auto mt-4">
                                <table class="table table-bordered ">
                                    <thead class="thead-fixed">
                                        <tr v-if="cronograma.length != 0" class="sticky top-0 z-10 bg-white">
                                            <th class="whitespace-nowrap">-</th>
                                            <th class="whitespace-nowrap">Fecha Desebolso</th>
                                            <th class="whitespace-nowrap">Monto de credito</th>
                                            <th class="whitespace-nowrap">-</th>
                                            <th class="whitespace-nowrap">-</th>
                                            <th class="whitespace-nowrap">-</th>
                                        </tr>
                                        <tr v-if="cronograma.length != 0">
                                            <td> - </td>
                                            <td> {{ fecha_desembolso }} </td>
                                            <td> S/. {{ numeralFormat(monto_credito, '0,0.00') }} </td>
                                            <td> - </td>
                                            <td> - </td>
                                            <td> - </td>
                                        </tr>
                                        <tr class="sticky top-0 z-10 bg-white">
                                            <th class="whitespace-nowrap">Periodo</th>
                                            <th class="whitespace-nowrap">Fecha vencimiento</th>
                                            <th class="whitespace-nowrap">Saldo capital</th>
                                            <th class="whitespace-nowrap">Amortizacion</th>
                                            <th class="whitespace-nowrap">Interes</th>
                                            <th class="whitespace-nowrap">Cuota</th>
                                        </tr>
                                    </thead>
                                    <tbody>


                                        <tr v-for="(c_g, c_g_index) in cronograma" :key="c_g_index">
                                            <td>{{ c_g.periodo }}</td>
                                            <td>{{ c_g.fechaVencimiento }}</td>
                                            <td>S/. {{ numeralFormat(c_g.saldoCapital, '0,0.00') }}</td>
                                            <td>S/. {{ numeralFormat(c_g.amortizacion, '0,0.00') }}</td>
                                            <td>S/. {{ numeralFormat(c_g.interes, '0,0.00') }}</td>
                                            <td class="bg-blue-200">S/. {{ numeralFormat(c_g.cuota, '0,0.00') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>S/. {{ numeralFormat(monto_credito, '0,0.00') }}</td>
                                            <td>S/. {{ numeralFormat(sumar_interes, '0,0.00') }}</td>
                                            <td>S/. {{ numeralFormat(sumar_cuota, '0,0.00') }}</td>
                                        </tr>
                                        <tr v-if="cronograma.length == 0">
                                            <td colspan="6">

                                                <div class="card ">
                                                    <center> <img class="card-img-top" width="20%"
                                                            src="../../../../public/dist/images/Draw/schedule.svg"
                                                            alt="">
                                                    </center>

                                                    <div class="card-body">
                                                        <h4 class="card-title text-center">Rellene los datos del
                                                            prestamo
                                                            para
                                                            generar un cronograma de pago</h4>

                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <button v-if="is_cronograma" type="submit" class="btn btn-primary btn-block btn-flotante btn-xs">Crear préstamo</button>

            </div>

        </form>


    </div>

    <!-- Modal para crear un cliente nuevo -->

    <Dialog v-model:visible="is_modal_add_cliente" maximizable modal header="Formulario para crear cliente"
        :style="{ width: '50rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
        <form id="form_crear_cliente" method="POST" action="#">
            <div id="vertical-form" class="p-5">
                <div class="">
                    <div>
                        <label for="regular-form-1" class="form-label">Buscar Dni</label>
                        <div class="input-group mt-2">

                            <input name="cli_dni" v-model="cli_dni" type="text" class="form-control"
                                placeholder="ejemplo.. 76234501">

                            <button type="button" class="btn btn-primary btn-select" @click="search_dni()">
                                <Icon icon="search" />
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-12 gap-6 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Nombre </label>
                            <input name="cli_nombre" v-model="cli_nombre" type="text" class="form-control"
                                placeholder="Nombres">
                        </div>

                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Apellido </label>
                            <input name="cli_apellido" v-model="cli_apellido" type="text" class="form-control"
                                placeholder="Apellidos">
                        </div>
                    </div>
                    <div
                        class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                        <h2 class="font-medium text-base mr-auto">
                            Agregar Contactos
                        </h2>
                        <div class="form-check form-switch w-full sm:w-auto sm:ml-auto mt-3 sm:mt-0">
                            <button type="button" class="btn btn-danger mr-1 mb-2" @click="agregar_contacto_cliente()">
                                <Icon icon="plus" /> Agregar
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-6 mt-2" v-for="(c_c, c_c_index) in contactos_cliente"
                        :key="c_c_index">

                        <div class="intro-y col-span-12 lg:col-span-4">
                            <label for="vertical-form-2" class="form-label">Contato {{ c_c_index + 1 }} </label>
                            <InputMask v-model="c_c.contacto" mask="999-999-999" placeholder="999-999-999"
                                class="form-control p-2" />
                        </div>
                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label for="vertical-form-2" class="form-label">Descripcion Contato {{ c_c_index + 1 }}
                            </label>
                            <input v-model="c_c.descripcion" type="text" maxlength="35" class="form-control p-2" />
                        </div>

                        <div v-if="c_c_index != 0" class="intro-y col-span-12 lg:col-span-2">
                            <label for="vertical-form-2" class="form-label">Eliminar </label>
                            <button type="button" class="btn btn-danger ml-2 mb-2"
                                @click="eliminar_contacto_cliente(c_c_index)">
                                <Icon icon="trash" />
                            </button>
                        </div>
                    </div>

                    <div class=" col-span-12 lg:col-span-12 mt-3 ">
                        <label for="vertical-form-2" class="form-label"> Fecha de nacimiento </label>
                        <datepicker class="form-control col-span-12" v-model="fecha_nacimiento"
                            placeholder="hacer click para seleccionar" :styles="{ border: '2px solid #00ff00' }"
                            language="es"></datepicker>
                    </div>

                    <div class="grid grid-cols-12 gap-2">
                        <div class="mt-3 col-span-12">
                            <label for="vertical-form-2" class="form-label">Domicilio </label>
                            <input name="cli_domicilio" v-model="cli_domicilio" type="text" class="form-control"
                                placeholder="Domicilio">
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-2">
                        <div class="mt-3 col-span-12">
                            <label for="vertical-form-2" class="form-label">Direccion del trabajo</label>
                            <input v-model="cli_direccion_trabajo" type="text" class="form-control"
                                placeholder="Direccion del trabajo">
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-2 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label>Tipo de cliente</label>
                            <div class="flex flex-col sm:flex-row mt-5">
                                <div class="form-check mr-2 sm:mt-0">
                                    <input id="particular" class="form-check-input" type="radio" name="tipo_cliente"
                                        checked v-model="tipo_cliente" value="particular">
                                    <label class="form-check-label" for="particular">Particular</label>
                                </div>
                                <div class="form-check mr-2 mt-2 sm:mt-0">
                                    <input id="privada" v-model="tipo_cliente" class="form-check-input" type="radio"
                                        name="tipo_cliente" value="Tarjeta privada">
                                    <label class="form-check-label" for="privada">Tarjeta privada</label>
                                </div>
                                <div class="form-check mr-2 mt-2 sm:mt-0">
                                    <input id="estado" v-model="tipo_cliente" class="form-check-input" type="radio"
                                        name="tipo_cliente" value="Tarjeta estado">
                                    <label class="form-check-label" for="estado">Tarjeta estado</label>
                                </div>
                            </div>
                        </div>

                        <div class="intro-y col-span-12 lg:col-span-6">
                            <label>Sexo</label>
                            <div class="flex flex-col sm:flex-row mt-5">

                                <div class="form-check mr-2 sm:mt-0">
                                    <input id="M" class="form-check-input" type="radio" name="cli_sexo" checked
                                        value="M" v-model="cli_sexo">
                                    <label class="form-check-label" for="M">Masculino</label>
                                </div>
                                <div class="form-check mr-2 mt-2 sm:mt-0">
                                    <input id="F" class="form-check-input" type="radio" name="cli_sexo" value="F"
                                        v-model="cli_sexo">
                                    <label class="form-check-label" for="F">Femenino</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="inputEmail4">Buscar Ubigeo</label>
                            <select ref="select_ubigueo" class="form-control" style="width: 100%;" tabindex="2"
                                language="es" placeholder="seleccionar un ubigueo">
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="inputEmail4">Departamento</label>
                            <input v-model="cli_departamento" type="text" class="form-control" name="cli_departamento"
                                id="cli_departamento" placeholder="Departamento" />
                        </div>
                        <div class="form-group col-md-4">
                            <label for="inputEmail4">Provincia</label>
                            <input v-model="cli_provincia" type="text" class="form-control" name="cli_provincia"
                                id="cli_provincia" placeholder="Provincia" />
                        </div>
                        <div class="form-group col-md-4">
                            <label for="inputPassword4">Distrito</label>
                            <input v-model="cli_distrito" type="text" class="form-control" name="cli_distrito"
                                id="cli_distrito" placeholder="Distrito" />
                        </div>
                    </div>

                    <div id="basic-button" class="p-1 mt-4">
                        <div class="preview">
                            <button type="submit" class="btn btn-primary mr-1 mb-2">
                                <Icon icon="plus" /> Registrar Cliente
                            </button>
                            <button type="button" class="btn btn-danger mr-1 mb-2"
                                @click="is_modal_add_cliente = false">
                                <Icon icon="ban" /> Cancelar Registro
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </Dialog>


    <!-- fin del modal para crear un cliente nuevo -->
</template>

<script>
import $ from 'jquery';
import { createApp, h } from 'vue';

import 'tom-select/dist/css/tom-select.css';
import TomSelect from 'tom-select';
import Axios from 'axios';

import "jquery-validation";
import "jquery-validation/dist/localization/messages_es"

import moment from 'moment';
import 'moment/locale/es';

// plugins para la creacion de pdf de una manera elegante
import {
    Canvg
} from 'canvg';
import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';
import 'jspdf-autotable';

import { CurrencyInput } from 'vue-currency-input'


// mixin
import {
    myMixin
} from "../../mixin.js";

import { renderToString } from '@vue/server-renderer';

//svg
import cabezeraVue from '../solicitudes/svg/cabezera.vue';

export default {
    mixins: [myMixin],
    components: {
        cabezeraVue,
        renderToString,
    },
    data() {
        return {
            headers: {
                "Content-Type": "application/json",
            },
            select_cliente: null,
            // variables modal
            is_modal_add_cliente: false,
            // variable crear cliente
            cli_dni: "",
            cli_nombre: "",
            cli_apellido: "",
            cli_celular: "",
            fecha_nacimiento: null,
            cli_domicilio: "",
            cli_direccion_trabajo: "",
            cli_sexo: "M",
            tipo_cliente: "particular",
            contactos_cliente: [],
            data_select_cliente: [],
            // ubigeo
            cli_departamento: "",
            cli_distrito: "",
            cli_provincia: "",
            get_cliente: null,
            // variables para la solicitud
            is_solicitud: false,
            clienteDropdownOpen: false,
            tipo_solicitud: "Nuevo",
            tipo_vivienda: "Propia",
            analista_id: null,
            destino: "",
            solicitud_nombre: "",
            solicitud_documento: "",
            estado_ruc: "",
            solicitud_giro: "",
            solicitud_antiguedad: "",
            solicitud_direccion_negocio: "",
            solicitud_lugar: "",
            solicitud_referencia_negocio: "",
            solicitud_referencia_cliente: "",
            solicitud_domicilio: "",
            solicitud_nombre_conyugue: "",
            solicitud_conyugue_dni: "",
            solicitud_conyugue_ruc: "",
            solicitud_tarjeta: '',
            solicitud_entidad_tarjeta: '',
            monto_credito: 1000,
            tasa_diaria: 0,
            cuotas: 0,
            cuota_final: 0,
            fecha_desembolso: null,
            frecuencia_pagos: "Quincenal",
            frecuencia_pagos_a: "Quincenas",
            interes: 20,
            intervalo: 0,
            is_cronograma: false,
            cronograma: [],
            is_fecha_pago: false,
            fecha_de_pago_cuota: null,
            rango_maximo: "",
            solicitudparalelo: this.$attrs.solicitudparalelo,
        }
    },
    watch: {
        frecuencia_pagos(newVal, oldVal) {
            switch (newVal) {
                case "Quincenal":
                    this.frecuencia_pagos_a = "Quincenas";
                    break;
                case "Diario":
                    this.frecuencia_pagos_a = "Dias";
                    break;

                case "Semanal":
                    this.frecuencia_pagos_a = "Semanas";
                    break;

                case "Mensual":
                    this.frecuencia_pagos_a = "Meses";
                    break;
            }
        }
    },
    computed: {
        string_contactos() {

            var contactosUnidos = this.contactos_cliente.reduce((acc, contacto) => {
                const contactoFormateado = contacto.map(c => `${c.contacto}`).join(' / ');
                return `${acc} / ${contactoFormateado}`;
            }, '');
            return contactosUnidos;

        },
        sumar_interes() {
            if (this.cronograma != 0) {
                const importe = this.cronograma.reduce((acumulador, res) => {
                    return acumulador + parseFloat(res.interes);
                }, 0);
                return this.redondear(importe);
            } else {
                return 0;
            }

        },
        sumar_cuota() {
            if (this.cronograma != 0) {
                const importe = this.cronograma.reduce((acumulador, res) => {
                    return acumulador + parseFloat(res.cuota);
                }, 0);
                return this.redondear(importe);

            } else {
                return 0;
            }
        }
    },
    methods: {
        //escuchar mensaje del componente analista para obtener el id de la misma
        escucharAnalista(sent) {
            this.analista_id = sent;
        },
        // elimina un contacto
        eliminar_contacto_cliente(index) {
            this.contactos_cliente.splice(index, 1);
        },
        // funcion para agregar contactos
        agregar_contacto_cliente() {
            this.contactos_cliente.push({
                contacto: "",
                descripcion: ""
            })
        },
        // activar modal de creacion de un cliente
        modal_add_cliente() {
            this.is_modal_add_cliente = true;
            var self = this;
            this.contactos_cliente.push({
                contacto: "",
                descripcion: ""
            })

            // validation cliente
            this.$nextTick(() => {
                $("#form_crear_cliente").validate({
                    rules: {
                        cli_dni: {
                            required: true,
                        },
                        cli_nombre: {
                            required: true,
                        },
                        cli_apellido: {
                            required: true,
                        },
                        cli_domicilio: {
                            required: true,
                        }
                    },
                    submitHandler: function (form) {
                        self.crear_cliente();
                        return false;
                    }
                });

                this.select_ubigueo = new TomSelect(this.$refs.select_ubigueo, {
                    valueField: 'urlapi',
                    labelField: 'Distrito',
                    searchField: 'Distrito',
                    options: [],
                    render: {
                        option: function (item, escape) {

                            return `<div class="intro-x">
                                            <div class="box px-4 py-4 mb-1 flex items-center ">
                                                <div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">
                                                    <img alt="Midone - HTML Admin Template" src="../../dist/images/Draw/map.svg">
                                                </div>
                                                <div class="ml-4 mr-auto">
                                                    <div class="font-medium">${item.Departamento + " / " + item.Provincia + "/" + item.Distrito}</div>
                                                </div>

                                            </div>
                            </div>`;
                        },
                        item: function (item, escape) {
                            return `<div class="intro-x">
                                            <div class="box px-4 py-4 mb-1 flex items-center ">
                                                <div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">
                                                    <img alt="Midone - HTML Admin Template" src="../../dist/images/Draw/map.svg">
                                                </div>
                                                <div class="ml-4 mr-auto">
                                                    <div class="font-medium">${item.Departamento + " / " + item.Provincia + "/" + item.Distrito}</div>
                                                </div>

                                            </div>
                            </div>`;
                        }
                    },
                    load: function (query, callback) {
                        const data = {
                            search: query,
                        };

                        const headers = this.headers;

                        Axios
                            .post("/search_ubigeo_select", data, {
                                headers,
                            })
                            .then((response) => {

                                if (response.data.success) {
                                    callback(response.data.data);
                                }
                            })
                            .catch((error) => {
                                callback();
                                this.loading_end();
                                this.alert_error_modal("Error en el servidor, recargue el navegador");
                                console.error(error);
                            });
                    },
                });

                this.select_ubigueo.on('change', this.change_select_ubigueo);

            });
        },
        // buscar dni en el api de la reniec
        search_dni() {

            if (this.cli_dni.length != 8) {
                this.alert_warning("Tienes que digitar 8 dígitos para buscar el <strong>DNI</strong>");
                return;
            }

            const data = { dni: this.cli_dni };

            const headers = this.headers;

            this.loading_start();

            Axios
                .post("/search_dni", data, {
                    headers,
                })
                .then((response) => {

                    if (response.data.success) {

                        var Params = response.data.data;

                        this.cli_nombre = Params.nombres;
                        this.cli_apellido = Params.apellido_paterno + " " + Params.apellido_materno;
                        this.cli_departamento = Params.departamento;
                        this.cli_provincia = Params.provincia;
                        this.cli_distrito = Params.distrito;
                        this.cli_domicilio = Params.direccion

                        this.alert_success("Dni cargando exitosamente");

                    } else {
                        this.cli_nombre = "";
                        this.cli_apellido = "";
                        this.alert_warning(response.data.message);
                    }
                    this.loading_end();
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor, ingrese los nombres y apellidos manualmente");
                    console.error(error);
                });

        },
        crear_cliente() {
            const contactosConContactoVacio = this.contactos_cliente.filter(
                (contacto) => contacto.contacto.trim() === ""
            );

            if (contactosConContactoVacio.length > 0) {
                this.alert_warning("hay un contacto vacio revise el formulario");
                return;
            }

            const data = {
                cli_dni: this.cli_dni,
                cli_nombre: this.cli_nombre,
                cli_apellido: this.cli_apellido,
                cli_celular: this.contactos_cliente,
                fecha_nacimiento: this.fecha_nacimiento,
                cli_domicilio: this.cli_domicilio,
                cli_direccion_trabajo: this.cli_direccion_trabajo,
                cli_sexo: this.cli_sexo,
                tipo_cliente: this.tipo_cliente,
                cli_departamento: this.cli_departamento,
                cli_distrito: this.cli_distrito,
                cli_provincia: this.cli_provincia
            };


            const headers = this.headers;

            this.loading_start();

            Axios
                .post("/crear_cliente", data, {
                    headers,
                })
                .then((response) => {
                    if (response.data.success) {
                        var cliente = response.data.data;
                        this.alert_success(response.data.message);

                        this.select_cliente.addOption(cliente);
                        this.select_cliente.setValue(cliente.urlapi);
                        this.select_cliente.inputState();

                        this.change_select_cliente(cliente.urlapi)

                        this.is_solicitud = true;
                        this.is_modal_add_cliente = false;

                        this.tipo_solicitud = "Nuevo";

                    } else {
                        this.alert_warning(response.data.message);
                    }
                    this.loading_end();
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor, ingrese los nombres y apellidos manualmente");
                    console.error(error);
                });

        },
        search_cliente(search) {
            const data = {
                search: search,
            };

            const headers = this.headers;

            Axios
                .post("/search_cliente_select", data, {
                    headers,
                })
                .then((response) => {

                    if (response.data.success) {
                        this.data_select_cliente = response.data.data;
                    }
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor, recargue el navegador");
                    console.error(error);
                });
        },
        // funciones de la solicitud
        // -----------------------------
        // crear el prestamo
        valitation_add_prestamo() {
            const self = this;
            $("#add_solicitud").validate({
                rules: {
                    analista_id: {
                        required: true,
                    },
                    solicitud_nombre: {
                        required: true,
                    },
                    solicitud_documento: {
                        required: true,
                    },
                    solicitud_giro: {
                        required: true,
                    },
                    solicitud_antiguedad: {
                        required: true,
                    },
                    solicitud_direccion_negocio: {
                        required: true,
                    },
                    solicitud_lugar: {
                        required: true,
                    },
                    solicitud_referencia_negocio: {
                        required: true,
                    },
                    solicitud_referencia_cliente: {
                        required: true,
                    },
                    solicitud_domicilio: {
                        required: true,
                    },
                    destino: {
                        required: true,
                    },
                    solicitud_tarjeta: {
                        minlength: 11,
                        maxlength: 24,
                        digits: true,
                    },
                    solicitud_entidad_tarjeta: {
                        minlength: 4,
                        maxlength: 42
                    }
                },
                submitHandler: function (form) {
                    try {
                        self.send_solicitud();
                        return false;
                    } catch (error) {
                        return false;
                    }

                }
            });


        },
        send_solicitud() {
            if (!this.is_cronograma || this.cronograma.length === 0) {
                this.alert_warning("Primero debe calcular el cronograma de pagos");
                return;
            }

            const data = {
                cliente: this.get_cliente.urlapi,
                tipo_solicitud: this.tipo_solicitud,
                tipo_vivienda: this.tipo_vivienda,
                analista: this.analista_id,
                solicitud_nombre: this.solicitud_nombre,
                solicitud_documento: this.solicitud_documento,
                solicitud_giro: this.solicitud_giro,
                solicitud_antiguedad: this.solicitud_antiguedad,
                solicitud_direccion_negocio: this.solicitud_direccion_negocio,
                solicitud_lugar: this.solicitud_lugar,
                solicitud_referencia_negocio: this.solicitud_referencia_negocio,
                solicitud_referencia_cliente: this.solicitud_referencia_cliente,
                solicitud_domicilio: this.solicitud_domicilio,
                solicitud_nombre_conyuge: this.solicitud_nombre_conyugue,
                solicitud_conyugue_dni: this.solicitud_conyugue_dni,
                solicitud_conyugue_ruc: this.solicitud_conyugue_ruc,
                destino: this.destino,
                monto_credito: this.monto_credito,
                tasa_diaria: this.tasa_diaria,
                cuotas: this.cuotas,
                cuota_final: this.cuota_final,
                fecha_desembolso: this.fecha_desembolso,
                frecuencia_pagos: this.frecuencia_pagos,
                interes: this.interes,
                intervalo: this.intervalo,
                is_fecha_pago: this.is_fecha_pago,
                fecha_de_pago_cuota: moment(this.fecha_de_pago_cuota).format("YYYY-MM-DD"),
                d_t: this.redondear(this.sumar_cuota),
                solicitud_tarjeta: this.solicitud_tarjeta,
                solicitud_entidad_tarjeta: this.solicitud_entidad_tarjeta,
                solicitud_paralelo: this.solicitudparalelo == '' ? '' : this.solicitudparalelo.urlapi,
                cronograma: this.cronograma,
            };

            const headers = this.headers;

            this.loading_start();

            Axios
                .post("/crear_solicitud", data, {
                    headers,
                })
                .then((response) => {
                    if (response.data.success) {
                        window.location.href = response.data.data;
                    } else {
                        this.alert_warning(response.data.message);
                    }
                    this.loading_end();
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor, ingrese los nombres y apellidos manualmente");
                    console.error(error);
                });
        },
        // buscar el dni de la conyugue o apoderado
        search_dni_conyugue() {

            if (this.solicitud_conyugue_dni.length != 8) {
                this.alert_warning("Tienes que digitar 8 dígitos para buscar el <strong>DNI</strong>");
                return;
            }

            const data = { dni: this.solicitud_conyugue_dni };

            const headers = this.headers;

            this.loading_start();

            Axios
                .post("/search_dni", data, {
                    headers,
                })
                .then((response) => {
                    if (response.data.success) {
                        var Params = response.data.data;

                        this.solicitud_nombre_conyugue = Params.nombres + " " + Params.apellidoPaterno + " " + Params.apellidoMaterno;


                        this.alert_success("Dni cargando exitosamente");

                    } else {
                        this.solicitud_nombre_conyugue = "";
                        this.alert_warning(response.data.message);
                    }
                    this.loading_end();
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor, ingrese los nombres y apellidos manualmente");
                    console.error(error);
                });
        },
        verificarPrestamosActivos(urlapi) {
            Axios.post("/prestamos_activos_cliente", { urlapi }, { headers: this.headers })
                .then((response) => {
                    if (response.data.success && response.data.data.total > 0) {
                        const lista = response.data.data.prestamos.map((p) =>
                            `${p.code} - S/. ${p.monto} (${p.estado})`
                        ).join('<br>');
                        this.$swal.fire({
                            icon: 'warning',
                            title: 'Este cliente ya tiene préstamos activos',
                            html: `<p>Tiene <strong>${response.data.data.total}</strong> préstamo(s) activo(s):</p><div class="text-left text-sm">${lista}</div>`,
                            confirmButtonText: 'Entendido',
                        });
                    }
                });
        },
        change_select_cliente(urlapi) {
            if (!urlapi) {
                return;
            }
            const data = {
                urlapi: urlapi,
            };
            const headers = this.headers;
            this.loading_start()
            Axios
                .post("/get_ciente", data, {
                    headers,
                })
                .then((response) => {

                    if (response.data.success) {
                        this.get_cliente = response.data.data;



                        this.solicitud_nombre = this.get_cliente.cli_nombre + " " + this.get_cliente.cli_apellido;
                        this.solicitud_documento = this.get_cliente.cli_dni;
                        this.contactos_cliente = [];



                        this.get_cliente.contactos_cliente.forEach((element, index) => {
                            this.contactos_cliente.push([
                                {
                                    contacto: element.ccliente_contacto,
                                    descripcion: element.ccliente_descripcion
                                }
                            ])
                        });

                        this.tipo_solicitud = "Recurrente";

                        if (this.solicitudparalelo != '') {
                            this.tipo_solicitud = "Paralelo";
                        }

                        this.is_solicitud = true;
                        this.is_modal_add_cliente = false;

                        this.verificarPrestamosActivos(urlapi);

                        if (this.get_cliente.ultimasolicitud != null) {
                            this.tipo_vivienda = this.get_cliente.ultimasolicitud.tipo_vivienda ?? "Propia";
                            this.destino = this.get_cliente.ultimasolicitud.destino ?? "NO REFIERE";
                            this.solicitud_nombre = this.get_cliente.ultimasolicitud.solicitud_nombre ?? "NO REFIERE";
                            this.solicitud_documento = this.get_cliente.ultimasolicitud.solicitud_documento ?? "NO REFIERE";
                            this.estado_ruc = this.get_cliente.ultimasolicitud.estado_ruc ?? "NO REFIERE";
                            this.solicitud_giro = this.get_cliente.ultimasolicitud.solicitud_giro ?? "NO REFIERE";
                            this.solicitud_antiguedad = this.get_cliente.ultimasolicitud.solicitud_antiguedad ?? "NO REFIERE";
                            this.solicitud_direccion_negocio = this.get_cliente.ultimasolicitud.solicitud_direccion_negocio ?? "NO REFIERE";
                            this.solicitud_lugar = this.get_cliente.ultimasolicitud.solicitud_lugar ?? "NO REFIERE";
                            this.solicitud_referencia_negocio = this.get_cliente.ultimasolicitud.solicitud_referencia_negocio ?? "NO REFIERE";
                            this.solicitud_referencia_cliente = this.get_cliente.ultimasolicitud.solicitud_referencia_cliente ?? "NO REFIERE";
                            this.solicitud_domicilio = this.get_cliente.ultimasolicitud.solicitud_domicilio ?? "NO REFIERE";
                            this.solicitud_nombre_conyugue = this.get_cliente.ultimasolicitud.solicitud_nombre_conyugue ?? "NO REFIERE";
                            this.solicitud_conyugue_dni = "";
                            this.solicitud_conyugue_ruc = "";
                        } else {
                            this.tipo_vivienda = "Propia";
                            this.destino = "NO REFIERE";

                            this.estado_ruc = "NO REFIERE";
                            this.solicitud_giro = "NO REFIERE";
                            this.solicitud_antiguedad = "NO REFIERE";
                            this.solicitud_direccion_negocio = "NO REFIERE";
                            this.solicitud_lugar = "NO REFIERE";
                            this.solicitud_referencia_negocio = "NO REFIERE";
                            this.solicitud_referencia_cliente = "NO REFIERE";
                            this.solicitud_domicilio = "NO REFIERE";
                            this.solicitud_nombre_conyugue = "";
                            this.solicitud_conyugue_dni = "";
                            this.solicitud_conyugue_ruc = "";
                        }
                    }

                    this.loading_end();
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor, recargue el navegador");
                    console.error(error);
                });


        },
        // buscar por dni y por ruc dependiendo de la longitud de  los datos ingresados
        search_solicitud_documento() {

            var ruta = "";
            var tipo_documento = "";
            var data = {};
            switch (this.solicitud_documento.length) {
                case 8:
                    ruta = "/search_dni";
                    tipo_documento = "dni";
                    data = { dni: this.solicitud_documento };
                    break;

                case 11:
                    ruta = "/search_ruc";
                    tipo_documento = "ruc";
                    data = { ruc: this.solicitud_documento };
                    break;

                default:
                    this.alert_warning("Tienes que digitar 8 o 11 dígitos para buscar el <strong>DNI o RUC</strong>");
                    break;
            }

            if (ruta == "") {
                return;
            }

            const headers = this.headers;

            this.loading_start();

            Axios
                .post(ruta, data, {
                    headers,
                })
                .then((response) => {
                    if (response.data.success) {
                        var Params = response.data.data;
                        if (tipo_documento == "ruc") {
                            this.solicitud_nombre = Params.razonSocial
                            this.estado_ruc = "Condicion de este ruc : " + Params.condicion
                        } else {
                            this.solicitud_nombre = `${Params.nombres} ${Params.apellidoPaterno} ${Params.apellidoMaterno}`;
                            this.estado_ruc = "";
                        }

                    } else {

                        this.alert_warning(response.data.message);
                    }
                    this.loading_end();
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor, ingrese los nombres y apellidos manualmente");
                    console.error(error);
                });
        },
        //calcular cronograma del prestamo
        calcular_cronograma() {


            switch (this.frecuencia_pagos) {

                case "Diario":
                    this.cronograma = this.calcularAmortizacionFrancesdiario(
                        this.monto_credito,
                        parseInt(this.intervalo),
                        parseInt(this.interes)
                    );

                    break;

                case "Semanal":
                    this.cronograma = this.calcularAmortizacionFrancesSemanal(
                        this.monto_credito,
                        parseInt(this.intervalo),
                        parseInt(this.interes)
                    )
                    break;

                case "Quincenal":
                    this.cronograma = this.calcularAmortizacionFrancesQuincenal(
                        this.monto_credito,
                        parseInt(this.intervalo),
                        parseInt(this.interes)
                    );
                    break;

                case "Mensual":

                    if (this.is_fecha_pago) {

                        this.cronograma = this.calcularAmortizacionFrancesMensual(
                            this.monto_credito,
                            parseInt(this.intervalo),
                            parseInt(this.interes),
                            this.fecha_de_pago_cuota
                        );

                    } else {

                        this.cronograma = this.calcularAmortizacionFrancesMensual(
                            this.monto_credito,
                            parseInt(this.intervalo),
                            parseInt(this.interes)
                        );

                    }

                    break;

                case "Anual":

                    break;
            }

            if (this.is_fecha_pago) {

                // Define las dos fechas
                var startDate = moment();
                var endDate = moment(this.fecha_de_pago_cuota);

                // Calcula la diferencia en días
                var differenceInDays = endDate.diff(startDate, 'days');
                var dias_antes_cuota = differenceInDays + 1

                var monto_interes_mes = this.monto_credito * this.interes / 100;
                var monto_por_dia = monto_interes_mes / 30;
                var monto_del_intervalo = monto_por_dia * dias_antes_cuota;

                // console.log(this.cronograma[this.cronograma.length - 1].fechaVencimiento);

                // var fecha_ultima_cuota = moment(this.cronograma[this.cronograma.length - 1].fechaVencimiento, "DD/MM/YYYY");

                // var fechaVencimiento = fecha_ultima_cuota.clone().add(1, 'months');

                // // Si la fecha de vencimiento cae en domingo (0), ajustar al siguiente día
                // if (fechaVencimiento.day() === 0) {
                //     fechaVencimiento.add(1, 'days');
                // }


                // console.log(fechaVencimiento);

                this.d_t = this.d_t + monto_del_intervalo;

                const pago = {
                    periodo: 0,
                    fechaVencimiento: moment(this.fecha_de_pago_cuota).format("D/M/YYYY"),
                    saldoCapital: 0,
                    amortizacion: 0,
                    interes: monto_del_intervalo,
                    cuota: monto_del_intervalo
                };

                this.cronograma.unshift(pago);

            }

            this.valitation_add_prestamo();
            this.is_cronograma = true;
        },
        //generar un pdf  con el cronograma de pagos
        async generarPDF() {
            const doc = new jsPDF();
            var Params = {
                // variables del prestamo
                monto_credito: this.formatear_monto(this.monto_credito),
                fecha_desembolso: this.formatear_monto(this.fecha_desembolso),
                intervalo: this.intervalo,
                frecuencia_pagos_a: this.frecuencia_pagos_a.toUpperCase(),
                interes: this.formatear_monto(this.interes),
                tasa_diaria: this.formatear_monto(this.tasa_diaria),
                cuotas: this.formatear_monto(this.cuotas),
                cuota_final: this.formatear_monto(this.cuota_final),
                // variables del cliente
                nombres: this.solicitud_nombre,
                dni: this.solicitud_documento,
                cel: this.string_contactos,
                tipo_cliente: this.tipo_cliente
            }
            const componente = h(cabezeraVue,
                Params
            );
            var html = await renderToString(componente);

            const svg = html;

            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            const v = Canvg.fromString(context, svg);

            // Ajustar la escala para mejorar la calidad
            const scaleFactor = 3; // Ajusta este valor según tus necesidades
            const originalWidth = 1800; // Ancho original del SVG
            const originalHeight = 378.8; // Altura original del SVG

            canvas.width = originalWidth * scaleFactor;
            canvas.height = originalHeight * scaleFactor;
            context.scale(scaleFactor, scaleFactor);


            // Datos de la tabla
            const cronograma_headers = [
                ['-', 'Fecha Desembolso', 'Monto de credito', '-', "-", "-"],
                ["-",
                    this.fecha_desembolso,
                    "S/. " + this.formatear_monto(this.monto_credito),
                    "-",
                    "-",
                    "-"
                ],
                ['Periodo', 'Fecha vencimiento', 'Saldo capital', 'Amortizacion', "Interes", "Cuota"]
            ];
            const cronograma_data = [];


            this.cronograma.forEach((element, index) => {
                cronograma_data.push([element.periodo, element.fechaVencimiento,
                "S/. " + this.formatear_monto(element.saldoCapital),
                "S/. " + this.formatear_monto(element.amortizacion),
                "S/. " + this.formatear_monto(element.interes),
                "S/. " + this.formatear_monto(element.cuota)
                ])
            });


            cronograma_data.push([
                "",
                "",
                "",
                "S/. " + this.formatear_monto(this.monto_credito),
                "S/. " + this.formatear_monto(this.sumar_interes),
                "S/. " + this.formatear_monto(this.sumar_cuota)
            ])

            // Configuración de la tabla
            const tableConfig = {
                startY: 90, // Posición vertical de inicio
                head: cronograma_headers,
                body: cronograma_data,
                tableWidth: 'auto',
                theme: 'grid', // Estilo de la tabla
                margin: {
                    top: 0,
                    left: 6,
                    right: 6
                },
                headStyles: {
                    fillColor: [46, 49, 146],
                },
                styles: {
                    cellPadding: 2, // Relleno de celda
                    fontSize: 10, // Tamaño de fuente
                    // Color de fondo de las celdas
                    // Color del texto de las celdas
                    fontStyle: 'normal', // Estilo de fuente (normal, bold, italic)
                    overflow: 'linebreak', // Manejo de desbordamiento de texto
                    halign: 'middle', // Alineación horizontal (left, center, right)
                    valign: 'middle',
                    cellPadding: 0.5 // Alineación vertical (top, middle, bottom)
                },
                columnStyles: { // Estilos específicos de columna

                    0: {
                        fontStyle: 'bold',
                        textColor: [0, 0, 255]
                    }, // Primera columna en negrita y azul
                    1: {
                        halign: 'left'
                    }, // Segunda columna centrada horizontalmente
                },
            };

            doc.autoTable(tableConfig);

            // Renderizar el SVG en el canvas
            await v.render();
            // Agregar la imagen de datos al documento PDF
            doc.addImage(canvas.toDataURL('image/png', 1), 'PNG', 5, 5, 655, 150);
            doc.autoPrint({ variant: 'non-conform' });

            // Guardar el documento PDF
            doc.save('Cronograma-' + this.solicitud_nombre + "-" + this.fecha_desembolso + '.pdf');

        },
        change_select_ubigueo(data) {
            const datos = {
                urlapi: data
            };

            const headers = this.headers;

            this.loading_start();

            Axios
                .post("/get_ubigeo", datos, {
                    headers,
                })
                .then((response) => {
                    if (response.data.success) {
                        var ubigeo = response.data.data;
                        this.cli_departamento = ubigeo.Departamento;
                        this.cli_distrito = ubigeo.Distrito;
                        this.cli_provincia = ubigeo.Provincia;
                    } else {
                        this.alert_warning(response.data.message);
                    }
                    this.loading_end();
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor, ingrese los nombres y apellidos manualmente");
                    console.error(error);
                });
        }
    },
    mounted() {

        var self = this;



        // formatear monto credito
        this.currency("monto_credito");

        this.valitation_add_prestamo();

        // configurar fechas para poder ponerlo en el rango permitido de la programacion de la cuota

        var fecha_actual = moment();
        var fechas_mas_veinte = moment().add(20, 'days');

        this.rango_maximo = {
            to: new Date(fecha_actual.format("Y"), fecha_actual.format("M") - 1, fecha_actual.format("DD")),
            from: new Date(fechas_mas_veinte.format("Y"), fechas_mas_veinte.format("M") - 1, fechas_mas_veinte.format("DD"))
        }

        this.select_cliente = new TomSelect(this.$refs.select_cliente, {
            valueField: 'urlapi',
            labelField: 'name',
            searchField: 'name',
            options: [],
            dropdownParent: 'body',
            maxOptions: 10,
            render: {
                option: function (item, escape) {

                    return `<div class="intro-x">
                                            <div class="box px-4 py-4 mb-1 flex items-center ">
                                                <div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">
                                                    <img alt="Midone - HTML Admin Template" src="../../dist/images/avatar/${item.cli_sexo == "M" ? "masculino" : "femenino"}.png">
                                                </div>
                                                <div class="ml-4 mr-auto">
                                                    <div class="font-medium">${item.cli_nombre + "-" + item.cli_apellido}</div>
                                                    <div class="text-slate-500 text-xs mt-0.5">Dni ( ${item.cli_dni} ) </div>
                                                </div>

                                            </div>
                            </div>`;
                },
                item: function (item, escape) {
                    return `<div class="intro-x" style="width:100%;">
                                            <div class="box px-4 py-4 mb-1 flex items-center ">
                                                <div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">
                                                    <img alt="Midone - HTML Admin Template" src="../../dist/images/avatar/${item.cli_sexo == "M" ? "masculino" : "femenino"}.png">
                                                </div>
                                                <div class="ml-4 mr-auto">
                                                    <div class="font-medium">${item.cli_nombre + "-" + item.cli_apellido}</div>
                                                    <div class="text-slate-500 text-xs mt-0.5">Dni ( ${item.cli_dni} ) </div>
                                                </div>

                                            </div>
                            </div>`;
                }
            },
            load: function (query, callback) {
                const data = {
                    search: query,
                };

                const headers = this.headers;

                Axios
                    .post("/search_cliente_select", data, {
                        headers,
                    })
                    .then((response) => {

                        if (response.data.success) {
                            callback(response.data.data);
                        }
                    })
                    .catch((error) => {
                        callback();
                        this.loading_end();
                        this.alert_error_modal("Error en el servidor, recargue el navegador");
                        console.error(error);
                    });
            },
        });

        this.select_cliente.on('change', this.change_select_cliente);
        this.select_cliente.on('dropdown_open', () => {
            this.clienteDropdownOpen = true;
        });
        this.select_cliente.on('dropdown_close', () => {
            this.clienteDropdownOpen = false;
        });

        Axios.post("/search_cliente_select", { search: '' }, { headers: this.headers })
            .then((response) => {
                if (response.data.success) {
                    response.data.data.forEach((cliente) => {
                        this.select_cliente.addOption(cliente);
                    });
                }
            });

        if (self.solicitudparalelo != '') {
            this.select_cliente.addOption(self.solicitudparalelo.cliente);
            this.select_cliente.setValue(self.solicitudparalelo.cliente.urlapi);
            this.select_cliente.inputState();
        }


    },
}
</script>

<style>
.cliente-select-wrapper {
    position: relative;
    overflow: visible;
}
.cliente-dropdown-open {
    padding-bottom: 18rem;
}
.solicitud-empty-state,
.solicitud-form-content {
    position: relative;
    z-index: 1;
}
.solicitud-form-mobile .ts-wrapper,
.solicitud-form-mobile .ts-control,
.solicitud-form-mobile .input-group,
.solicitud-form-mobile .table {
    max-width: 100%;
}
.solicitud-form-mobile .ts-wrapper.dropdown-active {
    z-index: 60;
}
@media (max-width: 640px) {
    .solicitud-form-mobile .report-box-1 .box {
        flex-direction: column;
    }
    .solicitud-form-mobile .input-group {
        flex-wrap: nowrap;
    }
    .solicitud-form-mobile .btn-flotante {
        width: calc(100% - 2rem);
        left: 50%;
        transform: translateX(-50%);
    }
}
.thead-fixed {
    position: sticky;
    top: 0;
    z-index: 999;
    background-color: white;
    /* Cambia esto al color de fondo deseado */
}
</style>
