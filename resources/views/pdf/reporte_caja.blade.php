<!DOCTYPE html>
<html>

<head>
    <title>Chart PDF</title>

    <style>
        body {
            padding: 0px;
            margin: 0px;
        }

        .texto-color {
            color: #2E3192;
        }

        .texto-title {
            width: 100%;
            border-bottom: 2px solid #2E3192;
        }

        table {
            width: 100%;
            margin: 0px;
            border-collapse: none;
        }

        table tr td {
            background: rgb(239, 237, 237);
            font-size: 10px;
            border: 0.7px solid rgb(88, 88, 88);
        }


        table tr th {
            font-size: 12px;
            border: 1px solid rgb(88, 88, 88);
        }

        .texto-color-black {
            color: rgb(57, 57, 57);
        }

        .texto-center {
            text-align: center;
        }
    </style>
</head>

<body>

    <h2 class="texto-color texto-center" style="">Reporte general de caja N° 1 | 12/02/2024</h2>


    <div class="report-box-2 intro-y mt-12 sm:mt-5">
        <div class="box sm:flex">
            <div class="px-8 py-12 flex flex-col justify-center flex-1">

                <Icon icon='cash-register' class='mr-2 fa-4x text-success' />
                {{-- <div class="relative text-4xl text-center font-medium mt-12 pl-4 ml-0.5"> {{
                    funciones::formatear_dinero_soles(total_caja) }} </div> --}}

            </div>
            <div
                class="px-8 py-12 flex flex-col justify-center flex-1 border-t sm:border-t-0 sm:border-l border-slate-200 dark:border-darkmode-300 border-dashed">

                <div class="text-slate-500 text-xs">Monto de apertura de caja</div>
                <div class="mt-1.5 flex items-center">
                    <div class="text-base">{{ $funciones::formatear_dinero_soles($get_caja->saldo_inicial) }}</div>

                </div>
                <div class="text-slate-500 text-xs mt-5">Ingresos</div>
                <div class="mt-1.5 flex items-center">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="whitespace-nowrap">Ingresos general</th>
                                <th class="whitespace-nowrap">
                                    {{ $funciones::formatear_dinero_soles($get_caja->ingresosefectivo + $get_caja->ingresoscuenta) }}
                                </th>
                            </tr>
                            <tr>
                                <th class="whitespace-nowrap bg-success text-white">Ingresos efectivo</th>
                                <th class="whitespace-nowrap bg-success text-white">
                                    {{ $funciones::formatear_dinero_soles($get_caja->ingresosefectivo) }}
                                </th>
                            </tr>
                            <tr>
                                <th class="whitespace-nowrap">Ingresos cuentas</th>
                                <th class="whitespace-nowrap">
                                    {{ $funciones::formatear_dinero_soles($get_caja->ingresoscuenta) }}</th>
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
                                            @foreach ($get_tabla_tarjeta_ingresos as $g_t)
                                                <tr>
                                                    <td> {{ $g_t->cuenta->cuentas_entidad }} </td>
                                                    <td> {{ $g_t->total }} </td>
                                                </tr>
                                            @endforeach


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
                                <th class="whitespace-nowrap">
                                    {{ $funciones::formatear_dinero_soles($get_caja->gastosefectivo + $get_caja->gastoscuenta) }}
                                </th>
                            </tr>
                            <tr>
                                <th class="whitespace-nowrap bg-success text-white">Gasto efectivo</th>
                                <th class="whitespace-nowrap bg-success text-white">
                                    {{ $funciones::formatear_dinero_soles($get_caja->gastosefectivo) }}
                                </th>
                            </tr>
                            <tr>
                                <th class="whitespace-nowrap ">Gasto cuentas</th>
                                <th class="whitespace-nowrap">
                                    {{ $funciones::formatear_dinero_soles($get_caja->gastoscuenta) }}</th>
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
                                <th class="whitespace-nowrap bg-success text-white">
                                    {{ $funciones::formatear_dinero_soles(0) }}</th>
                            </tr>
                            <tr>
                                <th class="whitespace-nowrap">Monto total en las cuentas</th>
                                <th class="whitespace-nowrap">
                                    {{ $funciones::formatear_dinero_soles($get_caja->ingresoscuenta - $get_caja->gastoscuenta) }}
                                </th>
                            </tr>

                        </thead>
                    </table>

                </div>



            </div>
        </div>
    </div>

    <h3 class="texto-color-black texto-title" style="">Tabla de Ingresos</h3>

    <table>
        <tr>
            <th>Cliente</th>
            <th>Descripcion</th>
            <th>pagos</th>
            <th>Monto</th>
            <th>
                <Icon icon='user' class='mr-2' />Analista
            </th>
            <th>
                codigo
            </th>
            <th>Usuario</th>
            <th>Fecha de Creacion</th>
        </tr>
        @foreach ($ingresos as $i_g)
            <tr>
                <td>{{ $i_g['cliente'] }}</td>
                <td>{{ $i_g['descripcion'] }}</td>
                <td>
                    @foreach ($i_g['pago'] as $pago)
                        
                        <table>
                            <tr>
                                <td>{{ $pago['cuenta']['cuentas_entidad'] }}</td>
                                <td>{{ $pago['monto'] }}</td>
                            </tr>
                        </table>
                    @endforeach
                </td>
                <td>{{ $i_g['monto'] }}</td>
                <td>{{ $i_g['analista'] }}</td>
                <td>{{ $i_g['codigo'] }}</td>
                <td>{{ $i_g['usuario']["name"] }}</td>
                <td>{{ $carbon::parse($i_g['created_at'])->isoFormat('L LT a')  }}</td>
            </tr>
        @endforeach
    </table>

    <h3 class="texto-color-black texto-title" style="">Tabla de gastos</h3>

    <table>
        <tr>
            <th>Descripcion</th>
            <th>Tipo gasto</th>
            <th>Pagos</th>
            <th>Monto</th>
            <th>Usuario</th>
            <th>Surcursal</th>
            <th>Fecha de Creacion</th>
            <th>Fecha de Edicion</th>
        </tr>
        @foreach ($ingresos as $i_g)
            <tr>
                <td>{{ $i_g['gastos_descripcion'] }}</td>
                <td>{{ $i_g['tipo_gasto']["nombre"] }}</td>
                <td>
                    @foreach ($i_g['pago'] as $pago)
                        
                        <table>
                            <tr>
                                <td>{{ $pago['cuenta']['cuentas_entidad'] }}</td>
                                <td>{{ $pago['monto'] }}</td>
                            </tr>
                        </table>
                    @endforeach
                </td>
                <td>{{ $i_g['monto'] }}</td>
                <td>{{ $i_g['usuario']["name"] }}</td>
                <td>{{ $i_g['codigo'] }}</td>
                <td>{{ $i_g['sucursal']["sucursal_nombre"] }}</td>
                <td>{{ $carbon::parse($i_g['created_at'])->isoFormat('L LT a')  }}</td>
            </tr>
        @endforeach
    </table>


</body>

</html>
