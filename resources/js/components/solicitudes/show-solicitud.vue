<template>
    <div class="card box p-3 ">
        <div class="flex flex-col sm:flex-row sm:items-end xl:items-start">
            <div class="xl:flex sm:mr-auto mt-2 pt-0">
                <div class="font-bold text-base  font-xl"> SOLICITUD N° {{ get.code }} <span>Estado : </span> <span>{{
                    get.statusformated }}</span>
                </div>
            </div>
            <div class="flex mt-5 sm:mt-0">
                <div class="dropdown w-1/2 sm:w-auto" style="position: relative;">
                    <button class="dropdown-toggle btn btn-outline-primary w-full sm:w-auto" aria-expanded="false"
                        data-tw-toggle="dropdown">
                        <Icon icon="gears" class="mr-2" /> Opciones
                        <Icon icon="chevron-down" class="ml-2" />
                    </button>

                    <div class="dropdown-menu w-40" id="_x9lz84wx3" data-popper-placement="bottom-end"
                        style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate(0px, 38px);">
                        <ul class="dropdown-content">
                            <li>
                                <a v-if="!get.status == 'P'" href="javascript:;" class="dropdown-item text-1xs">
                                    <Icon icon="file-pen" class="mr-2" /> Modificar Solicitud
                                </a>
                                <button class="dropdown-item text-1xs mb-1 "
                                    v-if="get.status == 'G' || get.status == 'M'" @click="is_modal_imprimir = true">
                                    <Icon icon="print" class="mr-2" /> Imprimir documentos
                                </button>

                                <button class="dropdown-item text-1xs mb-1 "
                                    v-if="get.status == 'G' || get.status == 'M'"
                                    @click="is_modal_guardar_documento = true">
                                    <Icon icon="floppy-disk" class="mr-2" /> Guardar documentos
                                </button>
                                <a v-if="get.urlapiantigua != 0" :href="'/solicitud/' + get.urlapiantigua"
                                    class="dropdown-item text-1xs">
                                    <Icon icon="eye" class="mr-2" />Solicitud anterior
                                </a>
                                <button @click="is_actualizar_cliente = true;" type="button"
                                    class="dropdown-item text-1xs mb-1 bg-info ">
                                    <Icon icon="pen-to-square" class="mr-2" /> Actualizar cliente
                                </button>


                                <button v-if="get_ingresos.length == 0 && get.solicitud_id_relacionado != 0"
                                    @click="restablecer_solicitud()" type="button"
                                    class="dropdown-item text-1xs mb-1 bg-info ">
                                    <Icon icon="trash-can-arrow-up" class="mr-2" />
                                    Restablecer a un anterior solicitud
                                </button>

                                <!-- <button @click="is_actualizar_cliente = true;" type="button"
                                    class="dropdown-item text-1xs mb-1 bg-info ">
                                    <Icon icon="credit-card" class="mr-2" /> Agregar tarjetas
                                </button> -->

                                <button @click="is_actualizar_solicitud = true;" type="button"
                                    class="dropdown-item text-1xs mb-1 bg-info ">
                                    <Icon icon="user-pen" class="mr-2" /> Editar datos solicitud
                                </button>

                                <div v-if="user.rol == 'gerente'">
                                    <button v-if="!(get.status == 'G' || get.status == 'M')"
                                        @click="send_solicitud_aprobada()" type="button"
                                        class="dropdown-item text-1xs mb-1 bg-success text-white">
                                        <Icon icon="file-circle-check" class="mr-2" /> Aprobar Solicitud
                                    </button>

                                    <a v-if="!(get.status == 'G' || get.status == 'M')" href="javascript:;"
                                        @click="is_modal_rechazo = true"
                                        class="dropdown-item text-1xs bg-danger text-white">
                                        <Icon icon="ban" class="mr-2" /> Rechazar Solicitud
                                    </a>
                                </div>

                            </li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div v-if="get.status == 'R'" class="alert alert-danger show mb-2 mt-2" role="alert">
        <div class="flex items-center">
            <div class="font-medium text-lg" v-if="user.rol == 'recepcionista'">Esta solicitud fue rechazada por
                GERENCIA
            </div>
            <div class="font-medium text-lg" v-if="user.rol == 'gerente'">Esta solicitud fue rechazada</div>
            <div class="text-xs bg-white px-1 rounded-md text-slate-100 ml-auto">
                <Icon icon='ban' class='text-danger' />
            </div>
        </div>
        <div class="mt-3">
            MOTIVO = {{ get.descripcion_rechazo }}
        </div>
    </div>

    <div v-if="get.status == 'A'" class="alert alert-success show  mb-2 mt-2 intro-x text-white" role="alert">
        <div class="flex items-center">
            <div class="font-medium text-lg" v-if="user.rol == 'recepcionista'">Esta solicitud fue aprobada por
                GERENCIA
                <button type="button" name="" v-on:click="generar_contrato()" id=""
                    class="btn btn-info btn-lg btn-block mt-4">
                    <Icon icon='check-to-slot' class='mr-2' />
                    Generar Contrato
                </button>
            </div>
            <div class="font-medium text-lg" v-if="user.rol == 'gerente'">
                Esta solicitud fue aprobada
                <hr>
                <button type="button" name="" id="" v-on:click="generar_contrato()"
                    class="btn btn-info btn-lg btn-block mt-4">
                    <Icon icon='check-to-slot' class='mr-2' />
                    Generar Contrato
                </button>
            </div>
            <div class="text-xs bg-white px-1 rounded-md text-slate-700 ml-auto">
                <Icon icon='check' class='mr-2' />
            </div>
        </div>
    </div>



    <div class="intro-y box px-5 pt-5 mt-1">
        <div class="flex flex-col lg:flex-row border-b border-slate-200/60 dark:border-darkmode-400 pb-5 -mx-5">
            <div class="flex flex-1 px-5 items-center justify-center lg:justify-start">
                <div class="w-20 h-20 sm:w-20 sm:h-24 flex-none lg:w-32 lg:h-32 relative">
                    <img alt="Midone - HTML Admin Template" class=" " width="300"
                        src="../../../../public/dist/images/Draw/solicitud.svg">
                </div>
                <div class="ml-5">
                    <div class="w-24 sm:w-40 sm:whitespace-normal font-medium text-lg">{{
                        get.cliente.cli_nombre }} | {{ get.cliente.cli_apellido }}</div>
                    <div class="text-slate-500">{{ get.solicitud_giro }}</div>
                </div>
            </div>
            <div
                class="mt-6 lg:mt-0 flex-1 px-5 border-l border-r border-slate-200/60 dark:border-darkmode-400 border-t lg:border-t-0 pt-5 lg:pt-0">

                <div class="box p-5 rounded-md mt-5">

                    <div class="flex items-center mt-5 border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
                        <div class="font-medium text-base truncate">
                            <Icon icon="user" class="mr-2 text-primary" />Información de la solicitud
                        </div>
                    </div>

                    <div class="flex items-center">
                        <Icon icon="check" class="mr-2 text-primary" /> Tipo solicitud:
                        <div class="ml-auto">{{ get.tipo_solicitud }}</div>
                    </div>


                    <div class="flex items-center">
                        <Icon icon="check" class="mr-2 text-primary" /> Fecha de solicitud:
                        <div class="ml-auto">{{ this.formatear_fecha(get.created_at) }}</div>
                    </div>
                    <hr>


                    <div class="flex items-center">
                        <Icon icon="check" class="mr-2 text-primary" /> Entidad Bancaria:
                        <div class="ml-auto"
                            v-if="get.solicitud_entidad_tarjeta == '' || get.solicitud_entidad_tarjeta === null">No
                            refiere</div>
                        <div class="ml-auto" v-else>{{ get.solicitud_entidad_tarjeta }}</div>
                    </div>
                    <div class="flex items-center">
                        <Icon icon="check" class="mr-2 text-primary" /> Numero de cuenta de la entidad bancaria:
                        <div class="ml-auto" v-if="get.solicitud_tarjeta == '' || get.solicitud_tarjeta === null">No
                            refiere</div>
                        <div class="ml-auto" v-else>{{ get.solicitud_tarjeta }}</div>
                    </div>
                    <hr>

                    <div class="flex mt-5 items-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
                        <div class="font-medium text-base truncate">
                            <Icon icon="phone" class="mr-2 text-primary" />Contactos
                        </div>
                    </div>

                    <div class="flex items-center" v-for="(contacto, ct_index) in get.cliente.contactos_cliente"
                        :key="ct_index">
                        {{ contacto.ccliente_contacto }}
                        <div v-if="contacto.ccliente_descripcion" class="ml-auto">{{ contacto.ccliente_descripcion }}
                        </div>
                        <div v-else class="ml-auto">No tiene descripcion</div>
                    </div>

                    <div class="flex items-center mt-5 border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
                        <div class="font-medium text-base truncate">
                            <Icon icon="user" class="mr-2 text-primary" />Información del cliente
                        </div>
                    </div>

                    <div class="flex items-center">
                        <Icon icon="check" class="mr-2 text-primary" /> Dni:
                        <div class="ml-auto">{{ get.cliente.cli_dni }}</div>
                    </div>
                    <hr>
                    <div class="flex items-center">
                        <Icon icon="check" class="mr-2 text-primary" /> Direccion domicilio:
                        <div class="ml-auto">{{ get.cliente.cli_domicilio }}</div>
                    </div>
                    <hr>
                    <div class="flex items-center">
                        <Icon icon="check" class="mr-2 text-primary" /> Direccion de su trabajo:
                        <div class="ml-auto">{{ get.cliente.cli_direccion_trabajo }}</div>
                    </div>
                    <hr>
                    <div class="flex items-center">
                        <Icon icon="check" class="mr-2 text-primary" /> Edad:
                        <div class="ml-auto">{{ calcularEdad(get.cliente.fecha_nacimiento) }} </div>
                    </div>
                    <hr>
                    <div class="flex items-center">
                        <Icon icon="check" class="mr-2 text-primary" /> fecha de nacimiento:
                        <div class="ml-auto">{{ formatear_fecha(get.cliente.fecha_nacimiento) }} </div>
                    </div>
                    <div
                        class="flex  mt-5 items-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5 mb-5">
                        <div class="font-medium text-base truncate">
                            <Icon icon="credit-card" class="mr-2 text-primary" />Datos del Prestamo
                        </div>
                    </div>
                    <div class="flex items-center">
                        <Icon icon="check" class="mr-2 text-primary" /> Monto solicitado:
                        <div class="ml-auto text-success">{{ formatear_dinero(get.prestamo.moto_credito) }} </div>
                    </div>
                    <div class="flex items-center">
                        <Icon icon="check" class="mr-2 text-primary" /> Cuotas:
                        <div class="ml-auto text-success">{{ formatear_dinero(get.prestamo.cuotas) }} </div>
                    </div>
                    <div class="flex items-center">
                        <Icon icon="check" class="mr-2 text-primary" /> {{
                            nomenclatura_frecuencia_pago(get.prestamo.frecuencia_pagos) }}:
                        <div class="ml-auto text-success">{{ get.prestamo.intervalo }} </div>
                    </div>
                    <div class="flex items-center">
                        <Icon icon="check" class="mr-2 text-primary" /> Tasa diaria:
                        <div class="ml-auto text-success">{{ get.prestamo.tasa_diaria }} </div>
                    </div>
                    <div class="flex items-center">
                        <Icon icon="check" class="mr-2 text-primary" /> D.T:
                        <div class="ml-auto text-success">{{ get.prestamo.d_t }} </div>
                    </div>
                    <div class="flex items-center">
                        <Icon icon="check" class="mr-2 text-primary" /> Interes:
                        <div class="ml-auto text-success">{{ get.prestamo.interes }}
                            <Icon icon="percent" class="mr-2 text-success" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <table-planilla-por-cliente :urlapi="get.cliente.urlapi"></table-planilla-por-cliente>


    <div class="card box p-3 mt-3">
        <div class="flex flex-col sm:flex-row sm:items-end xl:items-start">
            <div class="xl:flex sm:mr-auto mt-2 pt-0">
                <div class="font-bold text-base  font-xl"> Cronograma de pago </div>
            </div>
        </div>
    </div>


    <!-- cronograma de prestamo -->

    <div class="box">
        <div v-if="get.status != 'G'" class="grid grid-cols-12 gap-6 mt-2">
            <div class="intro-y col-span-12 lg:col-span-12">
                <div class="intro-y flex flex-col sm:flex-row items-center ml-2 mt-8">
                    <h2 class="text-lg font-medium mr-auto">
                        Cronograma
                        <Icon icon="hourglass-half" />
                        <div
                            class="py-1 px-2 rounded-full text-xs bg-success text-white text-center cursor-pointer font-medium">
                            {{ frecuencia_pagos }} </div>
                    </h2>
                    <div class="w-full sm:w-auto flex mt-4 sm:mt-0">


                    </div>
                </div>

                <div class="overflow-x-auto mt-4">
                    <table class="table table-sm table-bordered table-cronograma" style="font-size: 0.8rem;">
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
                                <td> S/. {{ numeralFormat(get.prestamo.moto_credito, '0,0.00') }} </td>
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
                                <td>S/. {{ numeralFormat(get.prestamo.moto_credito, '0,0.00') }}</td>
                                <td>S/. {{ numeralFormat(sumar_interes, '0,0.00') }}</td>
                                <td>S/. {{ numeralFormat(sumar_cuota, '0,0.00') }}</td>
                            </tr>
                            <tr v-if="cronograma.length == 0">
                                <td colspan="6">

                                    <div class="card ">
                                        <center> <img class="card-img-top" width="20%"
                                                src="../../../../public/dist/images/Draw/schedule.svg" alt="">
                                        </center>

                                        <div class="card-body">
                                            <h4 class="card-title text-center">Rellene los datos del prestamo para
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

        <div v-else class="card box p-3 mt-3">
            <div class="preview">
                <div class=" ">
                    <table class="table table-sm table-bordered" id="table_cronograma" style="font-size: 0.8rem;">
                        <thead>
                            <tr>
                                <th class="whitespace-nowrap">Periodo</th>
                                <th class="whitespace-nowrap">Fecha Vencimiento</th>
                                <th class="whitespace-nowrap">Saldo capital</th>
                                <th class="whitespace-nowrap">Amortizacion</th>
                                <th class="whitespace-nowrap">Interes</th>
                                <th class="whitespace-nowrap">Cuota</th>
                                <th class="whitespace-nowrap">Mora</th>
                                <th class="whitespace-nowrap">Estado</th>
                                <th class="whitespace-nowrap">Cobrar mora</th>
                                <th class="whitespace-nowrap">Cobrar interes</th>
                                <th class="whitespace-nowrap">Pagar</th>
                            </tr>
                            <tr>
                                <th class="whitespace-nowrap"> </th>
                                <th class="whitespace-nowrap">{{ formatear_fecha(get.prestamo.updated_at) }}</th>
                                <th class="whitespace-nowrap">{{ formatear_dinero_soles(get.prestamo.moto_credito) }}
                                </th>
                                <th class="whitespace-nowrap"> </th>
                                <th class="whitespace-nowrap"> </th>
                                <th class="whitespace-nowrap"> </th>
                                <th class="whitespace-nowrap"> </th>
                                <th class="whitespace-nowrap"> </th>
                                <th class="whitespace-nowrap"> </th>
                                <th class="whitespace-nowrap"> </th>
                                <th class="whitespace-nowrap"> </th>
                            </tr>
                        </thead>

                        <tfoot>
                            <th class="whitespace-nowrap"> </th>
                            <th class="whitespace-nowrap"> </th>
                            <th class="whitespace-nowrap">Saldo capital</th>
                            <th class="whitespace-nowrap">{{ get.prestamo.moto_credito }}</th>
                            <th class="whitespace-nowrap">{{ formatear_dinero_soles(get.prestamo.d_t -
                                get.prestamo.moto_credito) }}</th>
                            <th class="whitespace-nowrap">{{ formatear_dinero_soles(get.prestamo.d_t) }}</th>
                            <th class="whitespace-nowrap"> </th>
                            <th class="whitespace-nowrap"> </th>
                            <th class="whitespace-nowrap"> </th>
                            <th class="whitespace-nowrap"> </th>
                            <th class="whitespace-nowrap"> </th>
                        </tfoot>

                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- 
    <load-desemboloso :solicitud_id="get.urlapi"></load-desemboloso>
 
    <time-line-request :solicitud_id="get.urlapi"></time-line-request> -->


    <div class="card box p-3 mt-3">
        <div class="flex flex-col sm:flex-row sm:items-end xl:items-start">
            <div class="xl:flex sm:mr-auto mt-2 pt-0">
                <div class="font-bold text-base  font-xl"> Ingresos </div>
            </div>
        </div>
    </div>

    <div class="box">
        <table class="table table-sm table-bordered" style="font-size: 0.8rem;">
            <thead>
                <tr>
                    <th class="whitespace-nowrap">Descripcion</th>
                    <th class="whitespace-nowrap">Monto</th>
                    <th class="whitespace-nowrap">cuenta</th>
                    <th class="whitespace-nowrap">Codigo</th>
                    <th class="whitespace-nowrap">fecha</th>
                    <th class="whitespace-nowrap">Oficina</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(g_i, index_g_i) in get_ingresos" :key="index_g_i">
                    <td>{{ g_i.descripcion }}</td>
                    <td>{{ g_i.monto }}</td>
                    <td><button type="button" name="" id="" class="btn btn-primary btn-xs btn-block">
                            <Icon icon='eye' class='' />
                        </button></td>
                    <td>{{ g_i.codigo }}</td>
                    <td>{{ formatear_fecha(g_i.created_at) }}</td>
                    <td v-if="g_i.yes_office == 'Y'">Si</td>
                    <td v-else>No</td>
                </tr>
                <tr v-if="get_ingresos.length == 0">
                    <td colspan="7" class="text-center">
                        <Icon icon='eye' class='mr-2' /> no hay datos que mostrar
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <OverlayPanel ref="op">
        <div class="flex flex-column gap-3 w-25rem">
            <div>
                <div class="grid grid-cols-12 gap-6 mt-2">
                    <div class="intro-y col-span-12 lg:col-span-4">
                        <label for="vertical-form-2" class="form-label">Monto de credito</label>
                        <div class="input-group">
                            <div id="input-group-email" class="input-group-text">S/.</div>
                            <input class="form-control pl-2" placeholder="Monto del credito"
                                v-model="get.prestamo.moto_credito" name="monto_credito" data-type="monto_credito" />
                        </div>
                    </div>

                    <div class="intro-y col-span-12 lg:col-span-4">
                        <label for="vertical-form-2" class="form-label"> Intereses </label>

                        <div class="input-group">
                            <div id="input-group-email" class="input-group-text">%</div>
                            <input name="interes" v-model="get.prestamo.interes" type="number" class="form-control"
                                placeholder="0">
                        </div>
                    </div>

                    <div class="intro-y col-span-12 mr-4 lg:col-span-4">

                        <label for="vertical-form-2" class="form-label"> {{ frecuencia_pagos_a }} </label>
                        <input name="intervalo" v-model="get.prestamo.intervalo" type="number" class="form-control">

                    </div>
                </div>

                <div class="grid grid-cols-12 gap-2 mt-2">
                    <div class=" col-span-12 lg:col-span-6">
                        <label>Frecuencia de los pagos </label>
                        <div class="flex flex-col sm:flex-row mt-4">
                            <div class="form-check mr-2 sm:mt-0">
                                <input id="Diario" class="form-check-input" type="radio" name="frecuencia_pagos" checked
                                    v-model="get.prestamo.frecuencia_pagos" value="Diario">
                                <label class="form-check-label" for="Diario">Diario</label>
                            </div>
                            <div class="form-check mr-2 mt-2 sm:mt-0">
                                <input id="Semanal" v-model="get.prestamo.frecuencia_pagos" class="form-check-input"
                                    type="radio" name="frecuencia_pagos" value="Semanal">
                                <label class="form-check-label" for="Semanal">Semanal</label>
                            </div>
                            <div class="form-check mr-2 mt-2 sm:mt-0">
                                <input id="Mensual" v-model="get.prestamo.frecuencia_pagos" class="form-check-input"
                                    type="radio" name="frecuencia_pagos" value="Mensual">
                                <label class="form-check-label" for="Mensual">Mensual</label>
                            </div>

                        </div>
                    </div>

                </div>

                <button type="button" @click="guardar_edicion()" class="btn mt-4 btn-primary " style="width: 100%;"
                    label="Image">
                    <Icon icon='edit' class='mr-2' /> Guardar cambio
                </button>

            </div>

        </div>
    </OverlayPanel>


    <!-- modal para rechazar la solicitud -->
    <Dialog v-model:visible="is_modal_rechazo" maximizable modal header="Formulario para rechazar una solicitud"
        :style="{ width: '50rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
        <form id="form_rechazar_solicitud" method="POST" action="#">
            <div id="vertical-form" class="p-5">
                <div class="">
                    <div class="grid grid-cols-12 gap-12 mt-2">
                        <div class="intro-y col-span-12 lg:col-span-12">
                            <label for="vertical-form-2" class="form-label">Escribar el motivo de por que rechaza esta
                                solicitud </label>
                            <input name="descripcion_solicitud" v-model="descripcion_solicitud" type="text"
                                class="form-control" placeholder="Motivo de rechazo">
                        </div>
                    </div>

                </div>
                <div id="basic-button" class="p-1 mt-4">
                    <div class="preview">
                        <button type="button" class="btn btn-primary mr-1 mb-2" @click="send_rechazo_solicitud()">
                            <Icon icon="plus" /> Rechazar Solicitud
                        </button>
                        <button type="button" class="btn btn-danger mr-1 mb-2" @click="is_modal_add_cliente = false">
                            <Icon icon="ban" /> Cancelar operacion
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </Dialog>

    <!-- Imprimir solicitud y contrato -->
    <Dialog v-model:visible="is_modal_imprimir" maximizable modal header="Imprimir solicitud y contrato"
        :style="{ width: '90rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
        <iframe style="width: 100%;" :src="'/solicitdu/' + get.urlapi + '/imprimir'" width="500" height="800"></iframe>
    </Dialog>


    <Dialog v-model:visible="is_modal_guardar_documento" maximizable modal header="Guardar documentos"
        :style="{ width: '80rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
        <guardar_archivos :solicitud="get"></guardar_archivos>
    </Dialog>

    <button v-if="get.status == 'P'" type="button" class="btn  btn-primary btn-flotante" label="Image" @click="toggle">
        <Icon icon='edit' class='mr-2' /> Editar Cronograma
    </button>

    <Dialog v-model:visible="is_actualizar_cliente" maximizable modal header="Actualizar cliente"
        :style="{ width: '80rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
        <edit-cliente @comunicarEditarCliente="escucharEditarCliente" :get_cliente="get.cliente"></edit-cliente>
    </Dialog>

    <Dialog v-model:visible="is_actualizar_solicitud" maximizable modal header="Editar Solicitud"
        :style="{ width: '80rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
        <edit-solicitud @comunicarEditarSolicitud="escucharEditarSolicitud" :get_solicitud="get"></edit-solicitud>
    </Dialog>

    <!-- modal para crear una tarjeta de los clientes -->


</template>

<script>
import $ from 'jquery';
import { createApp, h } from 'vue';
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
import timeLineRequest from './time-line-request.vue';

export default {
    components: { timeLineRequest },
    mixins: [myMixin],
    data() {
        return {
            is_modal_rechazo: false,
            is_modal_imprimir: false,
            is_modal_guardar_documento: false,
            get: this.$attrs.solicitud,
            fecha_nacimiento: "",
            edad: "",
            cronograma_headers: null,
            //
            // variables de rechazo de solicitud
            descripcion_solicitud: "",
            // variables para la solicitud   
            cronograma: [],
            cuotas: null,
            fecha_desembolso: null,
            loading: false,
            frecuencia_pagos_a: "",
            cuota: 0,
            tasa_diaria: 0,
            is_actualizar_solicitud: false,
            is_actualizar_cliente: false,
            get_ingresos: []
        }
    },
    computed: {
        sumar_interes() {
            if (this.cronograma != 0) {
                const importe = this.cronograma.reduce((acumulador, res) => {
                    return acumulador + parseFloat(res.interes);
                }, 0);
                return Math.round(importe).toFixed(2);
            } else {
                return 0;
            }

        },
        sumar_cuota() {
            if (this.cronograma != 0) {
                const importe = this.cronograma.reduce((acumulador, res) => {
                    return acumulador + parseFloat(res.cuota);
                }, 0);
                return Math.round(importe).toFixed(2);

            } else {
                return 0;
            }
        }
    },
    watch: {
        cuotas: function (newValue) {
            this.get.prestamo.cuotas = newValue;
            this.get.prestamo.d_t = this.sumar_cuota;
            this.get.prestamo.tasa_diaria = this.tasa_diaria
        },
        'get.prestamo.frecuencia_pagos': function (newValue) {
            // Se ejecuta cuando el valor de "get.prestamo.moto_credito" cambia
            console.log('El nuevo valor del préstamo moto crédito es:', newValue);
            switch (newValue) {

                case "Diario":
                    this.frecuencia_pagos_a = "Dias";
                    break;

                case "Semanal":
                    this.frecuencia_pagos_a = "Semanas";
                    break;

                case "Mensual":
                    this.frecuencia_pagos_a = "Meses";
                    break;

                case "Anual":

                    break;
            }
            this.calcular_cronograma();


        },
        'get.prestamo.moto_credito': function (newValue) {
            // Se ejecuta cuando el valor de "get.prestamo.moto_credito" cambia
            console.log('El nuevo valor del préstamo moto crédito es:', newValue);

            this.calcular_cronograma();


        },
        'get.prestamo.interes': function (newValue) {
            // Se ejecuta cuando el valor de "get.prestamo.interes" cambia
            console.log('El nuevo valor del préstamo moto crédito es:', newValue);

            this.calcular_cronograma();


        },
        'get.prestamo.intervalo': function (newValue) {
            // Se ejecuta cuando el valor de "get.prestamo.intervalo" cambia 
            this.calcular_cronograma();
        },
    },
    methods: {
        load_ingresos() {
            const data = {
                urlapi: this.get.urlapi
            }

            const headers = this.headers;

            this.loading_start();

            return Axios
                .post("/load_ingresos_prestamo", data, {
                    headers,
                })
                .then((response) => {
                    if (response.data.success) {
                        // this.alert_success(response.data.message);
                        return response.data.data;

                    } else {
                        this.alert_warning(response.data.message);
                    }

                })
                .catch((error) => {

                    this.alert_error_modal("Error en el servidor, recargue la pagina");
                    console.error(error);
                });
        },
        escucharEditarSolicitud(event) {
            this.get = event;
            this.is_actualizar_solicitud = false;
        },
        escucharEditarCliente(event) {
            console.log(event);
            this.is_actualizar_cliente = false;
            get.cliente = event;
        },
        async check_interes(yes_interes, urlapi) {
            const data = {
                urlapi: urlapi,
                yes_interes: yes_interes
            };

            const headers = this.headers;

            this.loading_start();

            return Axios
                .put("/updated_yes_interes", data, {
                    headers,
                })
                .then((response) => {
                    if (response.data.success) {
                        this.alert_success(response.data.message);
                        this.loading_end();
                        return response.data.data;
                    } else {
                        this.alert_warning(response.data.message);
                    }

                })
                .catch((error) => {

                    this.alert_error_modal("Error en el servidor");
                    console.error(error);
                });
        },
        async check_mora(yes_mora, urlapi) {
            const data = {
                urlapi: urlapi,
                yes_mora: yes_mora
            };

            const headers = this.headers;

            this.loading_start();

            return Axios
                .put("/updated_yes_mora", data, {
                    headers,
                })
                .then((response) => {
                    if (response.data.success) {

                        this.loading_end();
                        return response.data.data;
                    } else {
                        this.alert_warning(response.data.message);
                    }

                })
                .catch((error) => {

                    this.alert_error_modal("Error en el servidor");
                    console.error(error);
                });
        },
        generar_contrato() {
            var mensaje = '';
            console.log(this.get.tipo_solicitud);
            if (this.get.tipo_solicitud == "Ampliacion") {
                mensaje = "Este es una ampliacion se desembolsara" + this.formatear_dinero_soles(this.get.prestamo.monto_ampliacion);
            } else {
                mensaje = "Cuando generes el contrator se desembolsara " + this.formatear_dinero_soles(this.get.prestamo.moto_credito);
            }

            this.$swal.fire({
                title: "Estas seguro de generar contrato?",
                text: mensaje,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, desembolsar"
            }).then((result) => {
                if (result.isConfirmed) {
                    const data = {
                        solicitud: this.get.urlapi,
                        cronograma: this.cronograma,
                        cuotas: this.cuotas
                    };

                    const headers = this.headers;

                    this.loading_start();

                    Axios
                        .put("/send_procesar_solicitud", data, {
                            headers,
                        })
                        .then((response) => {
                            console.log(response.data);
                            if (response.data.success) {
                                window.location.reload();
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
            });

        },

        toggle(event) {
            console.log(this.op);
            this.$refs.op.toggle(event);
        },
        // rechazar solicitud via axios
        send_rechazo_solicitud() {

            if (this.descripcion_solicitud == "") {
                this.alert_warning("Ingrese una descripción a la solicitud");
                return;
            }

            const data = {
                solicitud: this.get.urlapi,
                descripcion_solicitud: this.descripcion_solicitud
            };

            const headers = this.headers;

            this.loading_start();

            Axios
                .put("/send_rechazo_solicitud", data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {

                        window.location.reload();
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

        send_solicitud_aprobada() {
            console.log("dsada");
            this.$swal.fire({
                imageUrl: "../../../../dist/images/Draw/questions.svg",
                imageHeight: 150,
                imageAlt: "--",
                title: "Estas seguro que deseas aprobar esta solicitud?",
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: "Si estoy segur@",
                denyButtonText: `Salir`
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    const data = {
                        solicitud: this.get.urlapi
                    };

                    const headers = this.headers;

                    this.loading_start();

                    Axios
                        .put("/send_solicitud_aprobada", data, {
                            headers,
                        })
                        .then((response) => {
                            console.log(response.data);
                            if (response.data.success) {
                                window.location.reload();
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
            });
        },
        calcular_cronograma() {


            switch (this.get.prestamo.frecuencia_pagos) {

                case "Diario":
                    this.cronograma = this.calcularAmortizacionFrancesdiario(
                        parseInt(this.get.prestamo.moto_credito),
                        parseInt(this.get.prestamo.intervalo),
                        parseInt(this.get.prestamo.interes)
                    );
                    break;

                case "Semanal":
                    this.cronograma = this.calcularAmortizacionFrancesSemanal(
                        parseInt(String(this.get.prestamo.moto_credito).replace(/,/g, '')),
                        parseInt(this.get.prestamo.intervalo),
                        parseInt(this.get.prestamo.interes),
                    );
                    break;

                case "Mensual":

                    if (this.get.prestamo.is_fecha_pago == "A") {
                        this.cronograma = this.calcularAmortizacionFrancesMensual(
                            parseInt(this.get.prestamo.moto_credito),
                            parseInt(this.get.prestamo.intervalo),
                            parseInt(this.get.prestamo.interes),
                            this.get.prestamo.fecha_de_pago_cuota

                        );
                    } else {
                        this.cronograma = this.calcularAmortizacionFrancesMensual(
                            parseInt(this.get.prestamo.moto_credito),
                            parseInt(this.get.prestamo.intervalo),
                            parseInt(this.get.prestamo.interes)
                        );
                    }

                    break;

                case "Anual":

                    break;
            }

            if (this.get.prestamo.is_fecha_pago == "A") {

                // Define las dos fechas
                var startDate = moment();
                var endDate = moment(this.get.prestamo.fecha_de_pago_cuota, 'YYYY-MM-DD');

                // Calcula la diferencia en días
                var differenceInDays = endDate.diff(startDate, 'days');
                var dias_antes_cuota = differenceInDays + 1

                var monto_interes_mes = this.get.prestamo.moto_credito * this.get.prestamo.interes / 100;
                var monto_por_dia = monto_interes_mes / 30;
                var monto_del_intervalo = monto_por_dia * dias_antes_cuota;

                // console.log(fechaVencimiento);

                this.d_t = this.d_t + monto_del_intervalo;

                const pago = {
                    periodo: 0,
                    fechaVencimiento: moment(this.get.prestamo.fecha_de_pago_cuota, 'YYYY-MM-DD').format("D/M/YYYY"),
                    saldoCapital: 0,
                    amortizacion: 0,
                    interes: monto_del_intervalo,
                    cuota: monto_del_intervalo
                };

                this.cronograma.unshift(pago);

            }

        },
        // esta funcion sirve para restablecer a una solicitud anterior siempre y cuando no se haya generado ningun pago
        restablecer_solicitud() {
            this.$swal.fire({
                title: "Deseas restablecer esta solicitud a una anterior?",
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: "Si",
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {

                    const data = {
                        urlapi: this.get.urlapi,
                    };

                    const headers = this.headers;

                    this.loading_start();

                    Axios
                        .post("/restablecer_solicitud", data, {
                            headers,
                        })
                        .then((response) => {
                            console.log(response.data);
                            if (response.data.success) {
                                window.location.href = "/solicitud/" + response.data.data;
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
            });

        },

        async generar_documentos() {
            const doc = new jsPDF();
            var Params = {
                // variables del prestamo
                monto_credito: 1000,
            }
            const componente = h(documentInf,
                Params
            );
            console.log(Params);
            var html = await renderToString(componente);

            const svg = html;


            doc.addSvgAsImage(html, 'SVG', 10, 10, 20, 0);

            // Renderizar el SVG en el canvas
            await v.render();
            // Agregar la imagen de datos al documento PDF
            doc.addImage(canvas.toDataURL('image/png', 1), 'PNG', 5, 5, 655, 150);
            doc.autoPrint({ variant: 'non-conform' });

            // Guardar el documento PDF
            doc.save('Cronograma-.pdf');


        },
        // guardar los datos del prestamo
        guardar_edicion() {
            const data = {
                urlapi: this.get.urlapi,
                prestamo: this.get.prestamo,
            };

            const headers = this.headers;

            this.loading_start();

            Axios
                .post("/guardar_edicion_solicitud", data, {
                    headers,
                })
                .then((response) => {
                    console.log(response.data);
                    if (response.data.success) {
                        this.alert_success(response.data.message);
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
        }
    },
    mounted() {
      

        this.load_ingresos().then((result) => {
            this.get_ingresos = result;
        }).catch((err) => {
            console.log(err);
        });
        console.log(this.get);

        if (this.get.status == "G") {
            this.fecha_desembolso = moment(this.get.updated_at).format("YYYY-MM-DD");
            this.cronograma_headers = [
                {
                    periodo: "-",
                    fechaVencimiento: this.fecha_desembolso,
                    saldoCapital: this.get.prestamo.moto_credito,
                    amortizacion: "--",
                    interes: "--",
                    cuota: "--",

                }
            ];
            this.cronograma = this.get.prestamo.cronograma;

            var self = this;
            this.tabla_cronograma = $('#table_cronograma').DataTable({
                paging: false,
                searching: false,

                processing: true,
                "infoFiltered": "",
                "processing": "<img src='~/Content/images/loadingNew.gif' />",
                dom: 'Bfrtip',
                autoWidth: true,
                responsive: true,
                data: self.get.prestamo.cronograma,
                "ordering": true,
                "language": this.spanish_datatable,
                "buttons": [{
                    text: '<i class="fa fa-bars"></i> columnas visibles',
                    extend: 'colvis',
                },
                {
                    text: '<i class="fa fa-copy"></i> Copiar',
                    extend: 'copy',
                    title: 'tabla_cliente_fecha_'
                }, {
                    text: '<i class="fa fa-file-csv"></i> Csv',
                    extend: 'csvHtml5',
                    title: 'tabla_cliente_fecha_'
                }, {
                    text: '<i class="fa fa-file-excel"></i> Excel',
                    extend: 'excelHtml5',
                    title: 'tabla_cliente_fecha_'
                }, {
                    text: '<i class="fa fa-file-pdf"></i> Pdf',
                    extend: 'pdfHtml5',
                    title: 'tabla_cliente_fecha_',

                }, {
                    text: '<i class="fa fa-print"></i> Imprimir',
                    extend: "print",
                    title: 'tabla_cliente_fecha_'
                },
                {
                    text: '<i class="fa fa-money-bill-trend-up"></i> Refinanciar', // Texto del botón extra
                    action: function (e, dt, node, config) {

                    }
                }
                ],
                columns: [
                    { "data": "periodo" },
                    {
                        "data": "fechaVencimiento",
                        render: function (data, type, row) {
                            if (row.yes_pago == "N") {
                                const fechaDada = moment(data);
                                const fechaActual = moment();

                                if (fechaDada.isBefore(fechaActual, 'day')) {
                                    return `<div class="alert fila-roja  p-0 text-center text-white show "> ${self.formatear_fecha(data)} </div>`;
                                } else if (fechaDada.isSame(fechaActual, 'day')) {
                                    return `<div class="alert  fila-amarilla  p-0 text-center text-white show "> ${self.formatear_fecha(data)} </div>`;

                                } else {
                                    return `<div class="alert fila-pendiente  p-0 text-center text-white show "> ${self.formatear_fecha(data)} </div>`;

                                }

                            } else {
                                return `<div class="alert fila-pagada  p-0 text-center text-white show "> ${self.formatear_fecha(data)} </div>`;

                            }
                        }
                    },
                    {
                        "data": "saldoCapital"
                        ,
                        render: function (data, type, row) {

                            return `<div "> ${self.formatear_dinero_soles(data)} </div>`;

                        }
                    },
                    {
                        "data": "amortizacion",
                        render: function (data, type, row) {

                            return `<div "> ${self.formatear_dinero_soles(data)} </div>`;

                        }
                    },
                    {
                        "data": "interes",
                        render: function (data, type, row) {

                            if (row.yes_interes == "N") {
                                row.cuota = row.amortizacion;
                                return `<div "> 0.00 </div>`;
                            } else {
                                return `<div "> ${self.formatear_dinero_soles(data)} </div>`;
                            }

                        }
                    },
                    {
                        "data": "cuota",
                        render: function (data, type, row) {
                            if (row.yes_interes == "Y") {
                                return ` ${self.formatear_dinero_soles(data)}`;
                            } else {
                                return ` ${self.formatear_dinero_soles(row.amortizacion)}`;
                            }
                        }
                    },
                    {
                        "data": "monto_mora",
                        render: function (data, type, row) {
                            console.log(row);

                            if (row.yes_pago == "N") {

                                switch (row.yes_mora) {
                                    case "N":
                                        const fechaDada = moment(row.fechaVencimiento);
                                        const fechaActual = moment();
                                        // Comparar la fecha actual con la fecha dada 
                                        if (fechaDada.isBefore(fechaActual, 'day')) {

                                            self.check_mora("Y", row.urlapi);
                                            row.yes_mora = "Y";
                                            row.monto_mora = row.interes;
                                            return `<div "> ${self.formatear_dinero_soles(row.interes)} </div>`;

                                        } else if (fechaDada.isSame(fechaActual, 'day')) {
                                            return `<div "> NO </div>`;
                                        } else {
                                            return `<div "> NO </div>`;
                                        }
                                        break;

                                    case "Y":
                                        return `<div "> ${self.formatear_dinero_soles(row.monto_mora)} </div>`;
                                        break;

                                    case "S":
                                        return `<div "> 0 </div>`;
                                        break;
                                }
                            } else {
                                return `<div "> Pagado </div>`;
                            }
                        }
                    },
                    {
                        "data": "status",
                        render: function (data, type, row) {

                            if (row.yes_pago == "Y") {
                                return "Pagado";
                            }
                            const fechaDada = moment(row.fechaVencimiento);
                            const fechaActual = moment();
                            // Comparar la fecha actual con la fecha dada 
                            switch (data) {
                                case "A":
                                    return `<div "> Pagado </div>`;
                                    break;
                                case "P":
                                    if (fechaDada.isBefore(fechaActual, 'day')) {
                                        return `<div ">  Vencido </div>`;
                                    } else if (fechaDada.isSame(fechaActual, 'day')) {

                                        return `<div "> Pendiente hoy </div>`;
                                    } else {
                                        return `<div "> Pendiente </div>`;
                                    }

                                    break;
                                case "N":
                                    if (fechaDada.isBefore(fechaActual, 'day')) {
                                        return `<div ">  Vencido </div>`;
                                    } else if (fechaDada.isSame(fechaActual, 'day')) {

                                        return `<div "> Pendiente / Hoy </div>`;
                                    } else {
                                        return `<div ">  Pendiente </div>`;
                                    }
                                    break;
                            }
                        }
                    },
                    {
                        data: 'yes_mora',
                        render: function (data, type, row, meta) {
                            // Crea una casilla de verificación con el ID de la fila
                            if (row.yes_pago == "N") {

                                switch (row.yes_mora) {
                                    case "N":
                                        const fechaDada = moment(row.fechaVencimiento);
                                        const fechaActual = moment();
                                        // Comparar la fecha actual con la fecha dada 
                                        if (fechaDada.isBefore(fechaActual, 'day')) {

                                            return `<div class="form-check form-switch">

                                                        <input  class="form-check-input chk-mora" checked index="${meta.row}" type="checkbox">

                                                     </div> `;

                                        } else if (fechaDada.isSame(fechaActual, 'day')) {
                                            return `<div "> NO </div>`;
                                        } else {
                                            return `<div "> NO </div>`;
                                        }

                                        break;

                                    case "Y":
                                        return `<div class="form-check form-switch">

                                                        <input  class="form-check-input chk-mora" checked index="${meta.row}" type="checkbox">

                                                        </div> `;
                                        break;

                                    case "S":
                                        return ` <div class="form-check form-switch">

                                                    <input  class="form-check-input chk-mora" index="${meta.row}" type="checkbox">

                                                    </div> `;
                                        break;
                                }


                            } else {
                                return "Pagado"
                            }

                        }
                    },
                    {
                        data: 'yes_interes',
                        render: function (data, type, row, meta) {
                            // Crea una casilla de verificación con el ID de la fila
                            if (row.yes_pago == "N") {
                                var yes_interes = row.yes_interes == "Y" ? "checked" : "";

                                return ` <div class="form-check form-switch">

                                                <input  ${yes_interes} class="form-check-input chk-select" index="${meta.row}" type="checkbox">

                                            </div> `;
                            } else {
                                return "Pagado"
                            }

                        }
                    },
                    {
                        data: 'yes_pago',
                        render: function (data, type, row, meta) {

                            if (row.yes_pago == "N") {
                                return `<div class="form-check form-switch"> 
                                                <input class="form-check-input chk-pago" index="${meta.row}" type="checkbox">
                                            </div>
                                   `;
                            } else {
                                return "Pagado";
                            }

                        }
                    }
                ],
            })

        } else {

            this.calcular_cronograma();


            switch (this.get.prestamo.frecuencia_pagos) {

                case "Diario":
                    this.frecuencia_pagos_a = "Dias";
                    break;

                case "Semanal":
                    this.frecuencia_pagos_a = "Semanas";
                    break;

                case "Mensual":
                    this.frecuencia_pagos_a = "Meses";
                    break;

                case "Anual":

                    break;
            }

        }




    }
}
</script>

<style></style>