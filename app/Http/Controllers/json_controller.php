<?php

namespace App\Http\Controllers;

use App\Models\caja;
use App\Models\Cliente;
use App\Models\ContactosCliente;
use App\Models\cronograma;
use App\Models\ingreso;
use App\Models\pagos;
use App\Models\Prestamo;
use App\Models\Solicitud;
use App\Utils\Formulas;
use App\Utils\funciones;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class json_controller extends Controller
{
    public function semana_rafael($item)
    {
        $cliente = new Cliente();
        $cliente->cli_nombre = $item["CLIENTE"];
        $cliente->tipo_cliente = "particular";
        $cliente->cli_dni = isset($item["DNI"]) ? $item["DNI"] : "00000000";
        $cliente->cli_domicilio = isset($item["DOMICILIO"]) ? $item["DOMICILIO"] : "no tiene";
        $cliente->save();

        $fecha = $item["FE"];

        if (isset($item["TELEFONO"])) {
            if (strpos($item["TELEFONO"], '-') !== false) {
                $elementos = explode("-", $item["TELEFONO"]);

                // Recorrer el array con un bucle foreach
                foreach ($elementos as $elemento) {
                    $ContactosCliente = new ContactosCliente();
                    $ContactosCliente->ccliente_contacto = $elemento;
                    $ContactosCliente->ccliente_descripcion = "";
                    $ContactosCliente->cli_id = $cliente->cli_id;
                    $ContactosCliente->save();
                }
            } else {
                $ContactosCliente = new ContactosCliente();
                $ContactosCliente->ccliente_contacto = $item["TELEFONO"];
                $ContactosCliente->ccliente_descripcion = "";
                $ContactosCliente->cli_id = $cliente->cli_id;
                $ContactosCliente->save();
            }
        }


        $Solicitud = new Solicitud();
        $Solicitud->tipo_solicitud = "Nuevo";
        $Solicitud->solicitud_nombre = $cliente->cli_nombre;
        $Solicitud->analista_id = 3;
        $Solicitud->destino = "no refiere";
        $Solicitud->tipo_vivienda = "Propia";
        $Solicitud->solicitud_antiguedad = "no refiere";
        $Solicitud->solicitud_direccion_negocio = $item["DOMICILIO"];
        $Solicitud->solicitud_referencia_negocio = $item["DOMICILIO"];
        $Solicitud->solicitud_giro = "no refiere";
        $Solicitud->solicitud_lugar =  isset($item["ZONA"]) ? $item["ZONA"] : "no refiere";
        $Solicitud->solicitud_referencia_cliente = "REFERENCIA";
        $Solicitud->status = "G";
        $Solicitud->solicitud_documento =  isset($item["DNI"]) ? $item["DNI"] : "00000000";
        $Solicitud->cli_id = $cliente->cli_id;
        $Solicitud->created_user = 1;
        $Solicitud->sucursal_id = 1;
        $Solicitud->created_at = Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d h:i:s");
        $Solicitud->updated_at = Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d h:i:s");
        $Solicitud->save();



        $interes =  $item["PORCENTAJE"];

        $monto_credito = number_format($item["CREDITO"], 2);

        $frecuencia_pagos = "Semanal";

        $cuota = 0;
        switch ($frecuencia_pagos) {
            case 'Diario':
                $intervalo = 30;
                $cuota = Formulas::calcularAmortizacionFrancesDiaria($monto_credito, $intervalo, $interes);
                break;

            case 'Semanal':
                $intervalo = 4;
                $cuota = Formulas::calcularAmortizacionFrancesDiaria($monto_credito, $intervalo, $interes);
                break;
        }

        $monto_credito_sin_comas = $monto_credito;
        switch ($frecuencia_pagos) {
            case 'Diario':


                break;
            case 'Mensual':

                $cronogramasData = Formulas::calcularAmortizacionFrancesMensual_cronograma($monto_credito_sin_comas, $intervalo, $interes);
                break;
            case 'Semanal':

                $cronogramasData = Formulas::calcularAmortizacionFrancesSemanal_cronograma(Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d"), $monto_credito_sin_comas, $intervalo, $interes);
                break;
        }


        $prestamo = new Prestamo();
        $prestamo->cli_id = $cliente->cli_id;
        $prestamo->analista_id = 3;
        $prestamo->solicitud_id = $Solicitud->solicitud_id;
        $prestamo->status = "A";
        $prestamo->moto_credito = floatval($item["CREDITO"]);
        $prestamo->interes = $interes;
        $prestamo->cuotas = $cuota;
        $prestamo->frecuencia_pagos = $frecuencia_pagos;
        $prestamo->user_id = 1;
        $prestamo->sucursal_id = 1;
        $prestamo->intervalo = $intervalo;
        $prestamo->tasa_diaria = number_format(($interes / 30), 2);
        $Solicitud->created_at = Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d h:i:s");
        $Solicitud->updated_at = Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d h:i:s");
        $prestamo->save();

        $Solicitud->prestamo_id =$prestamo->prestamo_id;
        $Solicitud->save();

        $cronogramasData = array_map(function ($item, $key) use ($prestamo) {
            $item['prestamo_id'] = $prestamo->prestamo_id;
            return $item;
        }, $cronogramasData, array_keys($cronogramasData));


        $get_prestamo = Prestamo::find($prestamo->prestamo_id);
        $get_prestamo->d_t = array_sum(array_column($cronogramasData, 'cuota'));
        $get_prestamo->save();

        cronograma::insert($cronogramasData);


        if (isset($item["PAGOS"])) {
            foreach ($item["PAGOS"] as $key => $value) {

                $ingreso = new ingreso();
                $ingreso->monto = number_format(floatval($value), 2);
                $ingreso->descripcion = "amortizacion de la solicitud n° {$Solicitud->code}";
                $ingreso->created_user = 2;
                $ingreso->sucursal_id = 1;
                $ingreso->yes_office = "Y";
                $ingreso->caja_chica_id = 4;
                $ingreso->codigo = funciones::generar_codigo(number_format(floatval($value), 2));
                $ingreso->created_at = Carbon::parse($key)->format("Y-m-d h:i:s");
                $ingreso->is_amortizacion = "Y";
                $ingreso->prestamo_id = $prestamo->prestamo_id;
                $ingreso->updated_at =  Carbon::parse($key)->format("Y-m-d h:i:s");
                $ingreso->save();

                $pagos = new pagos();
                $pagos->ingreso_id = $ingreso->ingreso_id;
                $pagos->monto =  number_format(floatval($value), 2);
                $pagos->cuentas_id = 1;
                $pagos->tipo = "I";
                $pagos->caja_chica_id = 4;
                $pagos->created_user  = 2;
                $pagos->sucursal_id  = 1;
                $ingreso->created_at = Carbon::parse($key)->format("Y-m-d h:i:s");
                $ingreso->updated_at =  Carbon::parse($key)->format("Y-m-d h:i:s");
                $pagos->save();
            }
        }

        // $caja = new caja();
        // $caja->referencia = "levantamiento de datos";
        // $caja->user_id = 1;  

    }
 
    public function diario_rafael($item)
    {
        $cliente = new Cliente();
        $cliente->cli_nombre = $item["CLIENTE"];
        $cliente->tipo_cliente = "particular";
        $cliente->cli_dni = isset($item["DNI"]) ? $item["DNI"] : "00000000";
        $cliente->cli_domicilio = isset($item["DOMICILIO"]) ? $item["DOMICILIO"] : "no tiene";
        $cliente->save();

        if (isset($item["TELEFONO"])) {
            if (strpos($item["TELEFONO"], '-') !== false) {
                $elementos = explode("-", $item["TELEFONO"]);

                // Recorrer el array con un bucle foreach
                foreach ($elementos as $elemento) {
                    $ContactosCliente = new ContactosCliente();
                    $ContactosCliente->ccliente_contacto = $elemento;
                    $ContactosCliente->ccliente_descripcion = "";
                    $ContactosCliente->cli_id = $cliente->cli_id;
                    $ContactosCliente->save();
                }
            } else {
                $ContactosCliente = new ContactosCliente();
                $ContactosCliente->ccliente_contacto = $item["TELEFONO"];
                $ContactosCliente->ccliente_descripcion = "";
                $ContactosCliente->cli_id = $cliente->cli_id;
                $ContactosCliente->save();
            }
        }

        $fecha = $item["FE"];


        $Solicitud = new Solicitud();
        $Solicitud->tipo_solicitud = "Nuevo";
        $Solicitud->solicitud_nombre = $cliente->cli_nombre;
        $Solicitud->analista_id = 3;
        $Solicitud->destino = "no refiere";
        $Solicitud->tipo_vivienda = "Propia";
        $Solicitud->solicitud_antiguedad = "no refiere";
        $Solicitud->solicitud_direccion_negocio =$cliente->cli_domicilio;
        $Solicitud->solicitud_referencia_negocio =$cliente->cli_domicilio;
        $Solicitud->solicitud_giro =  isset($item["NEGOCIO"]) ? $item["NEGOCIO"] : "no refiere";;
        $Solicitud->solicitud_lugar =  isset($item["ZONA"]) ? $item["ZONA"] : "no refiere";
        $Solicitud->solicitud_referencia_cliente = "REFERENCIA";
        $Solicitud->status = "G";
        $Solicitud->solicitud_documento =  isset($item["DNI"]) ? $item["DNI"] : "00000000";
        $Solicitud->cli_id = $cliente->cli_id;
        $Solicitud->created_user = 1;
        $Solicitud->sucursal_id = 1;
        $Solicitud->created_at = Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d h:i:s");
        $Solicitud->updated_at = Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d h:i:s");
        $Solicitud->save();



        $interes =  $item["PORCENTAJE"];

        $monto_credito = number_format($item["CREDITO"], 2);

        $frecuencia_pagos = "Diario";

        $cuota = 0;
        switch ($frecuencia_pagos) {
            case 'Diario':
                $intervalo = 30;
                $cuota = Formulas::calcularAmortizacionFrancesDiaria($monto_credito, $intervalo, $interes);
                break;

            case 'Semanal':
                $intervalo = 4;
                $cuota = Formulas::calcularAmortizacionFrancesDiaria($monto_credito, $intervalo, $interes);
                break;
        }

        $monto_credito_sin_comas = $monto_credito;
        switch ($frecuencia_pagos) {
            case 'Diario': 
                $cronogramasData = Formulas::calcularAmortizacionFrancesDiaria_cronograma(Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d"), $monto_credito_sin_comas, $intervalo, $interes);
                break;
            case 'Mensual':

                $cronogramasData = Formulas::calcularAmortizacionFrancesMensual_cronograma($monto_credito_sin_comas, $intervalo, $interes);
                break;
            case 'Semanal':

                $cronogramasData = Formulas::calcularAmortizacionFrancesSemanal_cronograma(Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d"), $monto_credito_sin_comas, $intervalo, $interes);
                break;
        }

     

        $prestamo = new Prestamo();
        $prestamo->cli_id = $cliente->cli_id;
        $prestamo->analista_id = 3;
        $prestamo->solicitud_id = $Solicitud->solicitud_id;
        $prestamo->status = "A";
        $prestamo->moto_credito = floatval($item["CREDITO"]);
        $prestamo->interes = $interes;
        $prestamo->cuotas = $cuota;
        $prestamo->frecuencia_pagos = $frecuencia_pagos;
        $prestamo->user_id = 1;
        $prestamo->sucursal_id = 1;
        $prestamo->intervalo = $intervalo;
        $prestamo->tasa_diaria = number_format(($interes / 30), 2);
        $Solicitud->created_at = Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d h:i:s");
        $Solicitud->updated_at = Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d h:i:s");
        $prestamo->save();

        $Solicitud->prestamo_id =$prestamo->prestamo_id;
        $Solicitud->save();

        $cronogramasData = array_map(function ($item, $key) use ($prestamo) {
            $item['prestamo_id'] = $prestamo->prestamo_id;
            return $item;
        }, $cronogramasData, array_keys($cronogramasData));


        $get_prestamo = Prestamo::find($prestamo->prestamo_id);
        $get_prestamo->d_t = array_sum(array_column($cronogramasData, 'cuota'));
        $get_prestamo->save();

        cronograma::insert($cronogramasData);


        if (isset($item["PAGOS"])) {
            foreach ($item["PAGOS"] as $key => $value) {
                
                if ($value != 0) { 
                    $ingreso = new ingreso();
                    $ingreso->monto = floatval($value);
                    $ingreso->descripcion = "amortizacion de la solicitud n° {$Solicitud->code}";
                    $ingreso->created_user = 2;
                    $ingreso->sucursal_id = 1;
                    $ingreso->yes_office = "Y";
                    $ingreso->caja_chica_id = 4;
                    $ingreso->codigo = funciones::generar_codigo(floatval($value));
                    $ingreso->created_at = Carbon::parse($key)->format("Y-m-d h:i:s");
                    $ingreso->is_amortizacion = "Y";
                    $ingreso->prestamo_id = $prestamo->prestamo_id;
                    $ingreso->updated_at =  Carbon::parse($key)->format("Y-m-d h:i:s");
                    $ingreso->save();
    
                    $pagos = new pagos();
                    $pagos->ingreso_id = $ingreso->ingreso_id;
                    $pagos->monto =  floatval($value);
                    $pagos->cuentas_id = 1;
                    $pagos->tipo = "I";
                    $pagos->caja_chica_id = 4;
                    $pagos->created_user  = 2;
                    $pagos->sucursal_id  = 1;
                    $ingreso->created_at = Carbon::parse($key)->format("Y-m-d h:i:s");
                    $ingreso->updated_at =  Carbon::parse($key)->format("Y-m-d h:i:s");
                    $pagos->save();
                }
                
            }
        }

        // $caja = new caja();
        // $caja->referencia = "levantamiento de datos";
        // $caja->user_id = 1;  

    }

    public function semana_alejandro($item)
    {
        $cliente = new Cliente();
        $cliente->cli_nombre = $item["CLIENTE"];
        $cliente->tipo_cliente = "particular";
        $cliente->cli_dni = isset($item["DNI"]) ? $item["DNI"] : "00000000";
        $cliente->cli_domicilio = isset($item["DOMICILIO"]) ? $item["DOMICILIO"] : "no tiene";
        $cliente->save();

        $fecha = $item["FE"];

        if (isset($item["TELEFONO"])) {
            if (strpos($item["TELEFONO"], '-') !== false) {
                $elementos = explode("-", $item["TELEFONO"]);

                // Recorrer el array con un bucle foreach
                foreach ($elementos as $elemento) {
                    $ContactosCliente = new ContactosCliente();
                    $ContactosCliente->ccliente_contacto = $elemento;
                    $ContactosCliente->ccliente_descripcion = "";
                    $ContactosCliente->cli_id = $cliente->cli_id;
                    $ContactosCliente->save();
                }
            } else {
                $ContactosCliente = new ContactosCliente();
                $ContactosCliente->ccliente_contacto = $item["TELEFONO"];
                $ContactosCliente->ccliente_descripcion = "";
                $ContactosCliente->cli_id = $cliente->cli_id;
                $ContactosCliente->save();
            }
        }


        $Solicitud = new Solicitud();
        $Solicitud->tipo_solicitud = "Nuevo";
        $Solicitud->solicitud_nombre = $cliente->cli_nombre;
        $Solicitud->analista_id = 6;
        $Solicitud->destino = "no refiere";
        $Solicitud->tipo_vivienda = "Propia";
        $Solicitud->solicitud_antiguedad = "no refiere";
        $Solicitud->solicitud_direccion_negocio = $item["DOMICILIO"];
        $Solicitud->solicitud_referencia_negocio = $item["DOMICILIO"];
        $Solicitud->solicitud_giro = "no refiere";
        $Solicitud->solicitud_lugar =  isset($item["ZONA"]) ? $item["ZONA"] : "no refiere";
        $Solicitud->solicitud_referencia_cliente = "REFERENCIA";
        $Solicitud->status = "G";
        $Solicitud->solicitud_documento =  isset($item["DNI"]) ? $item["DNI"] : "00000000";
        $Solicitud->cli_id = $cliente->cli_id;
        $Solicitud->created_user = 1;
        $Solicitud->sucursal_id = 1;
        $Solicitud->created_at = Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d h:i:s");
        $Solicitud->updated_at = Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d h:i:s");
        $Solicitud->save();



        $interes =  $item["PORCENTAJE"];

        $monto_credito = number_format($item["CREDITO"], 2);

        $frecuencia_pagos = "Semanal";

        $cuota = 0;
        switch ($frecuencia_pagos) {
            case 'Diario':
                $intervalo = 30;
                $cuota = Formulas::calcularAmortizacionFrancesDiaria($monto_credito, $intervalo, $interes);
                break;

            case 'Semanal':
                $intervalo = 4;
                $cuota = Formulas::calcularAmortizacionFrancesDiaria($monto_credito, $intervalo, $interes);
                break;
        }

        $monto_credito_sin_comas = $monto_credito;
        switch ($frecuencia_pagos) {
            case 'Diario':


                break;
            case 'Mensual':

                $cronogramasData = Formulas::calcularAmortizacionFrancesMensual_cronograma($monto_credito_sin_comas, $intervalo, $interes);
                break;
            case 'Semanal':

                $cronogramasData = Formulas::calcularAmortizacionFrancesSemanal_cronograma(Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d"), $monto_credito_sin_comas, $intervalo, $interes);
                break;
        }


        $prestamo = new Prestamo();
        $prestamo->cli_id = $cliente->cli_id;
        $prestamo->analista_id = 6;
        $prestamo->solicitud_id = $Solicitud->solicitud_id;
        $prestamo->status = "A";
        $prestamo->moto_credito = floatval($item["CREDITO"]);
        $prestamo->interes = $interes;
        $prestamo->cuotas = $cuota;
        $prestamo->frecuencia_pagos = $frecuencia_pagos;
        $prestamo->user_id = 1;
        $prestamo->sucursal_id = 1;
        $prestamo->intervalo = $intervalo;
        $prestamo->tasa_diaria = number_format(($interes / 30), 2);
        $Solicitud->created_at = Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d h:i:s");
        $Solicitud->updated_at = Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d h:i:s");
        $prestamo->save();

        $Solicitud->prestamo_id =$prestamo->prestamo_id;
        $Solicitud->save();

        $cronogramasData = array_map(function ($item, $key) use ($prestamo) {
            $item['prestamo_id'] = $prestamo->prestamo_id;
            return $item;
        }, $cronogramasData, array_keys($cronogramasData));


        $get_prestamo = Prestamo::find($prestamo->prestamo_id);
        $get_prestamo->d_t = array_sum(array_column($cronogramasData, 'cuota'));
        $get_prestamo->save();

        cronograma::insert($cronogramasData);


        if (isset($item["PAGOS"])) {
            foreach ($item["PAGOS"] as $key => $value) {

                $ingreso = new ingreso();
                $ingreso->monto = number_format(floatval($value), 2);
                $ingreso->descripcion = "amortizacion de la solicitud n° {$Solicitud->code}";
                $ingreso->created_user = 2;
                $ingreso->sucursal_id = 1;
                $ingreso->yes_office = "Y";
                $ingreso->caja_chica_id = 4;
                $ingreso->codigo = funciones::generar_codigo(number_format(floatval($value), 2));
                $ingreso->created_at = Carbon::parse($key)->format("Y-m-d h:i:s");
                $ingreso->is_amortizacion = "Y";
                $ingreso->prestamo_id = $prestamo->prestamo_id;
                $ingreso->updated_at =  Carbon::parse($key)->format("Y-m-d h:i:s");
                $ingreso->save();

                $pagos = new pagos();
                $pagos->ingreso_id = $ingreso->ingreso_id;
                $pagos->monto =  number_format(floatval($value), 2);
                $pagos->cuentas_id = 1;
                $pagos->tipo = "I";
                $pagos->caja_chica_id = 4;
                $pagos->created_user  = 2;
                $pagos->sucursal_id  = 1;
                $ingreso->created_at = Carbon::parse($key)->format("Y-m-d h:i:s");
                $ingreso->updated_at =  Carbon::parse($key)->format("Y-m-d h:i:s");
                $pagos->save();
            }
        }

        // $caja = new caja();
        // $caja->referencia = "levantamiento de datos";
        // $caja->user_id = 1;  

    }
 
    public function diario_alejandro($item)
    {
        $cliente = new Cliente();
        $cliente->cli_nombre = $item["CLIENTE"];
        $cliente->tipo_cliente = "particular";
        $cliente->cli_dni = isset($item["DNI"]) ? $item["DNI"] : "00000000";
        $cliente->cli_domicilio = isset($item["DOMICILIO"]) ? $item["DOMICILIO"] : "no tiene";
        $cliente->save();

        if (isset($item["TELEFONO"])) {
            if (strpos($item["TELEFONO"], '-') !== false) {
                $elementos = explode("-", $item["TELEFONO"]);

                // Recorrer el array con un bucle foreach
                foreach ($elementos as $elemento) {
                    $ContactosCliente = new ContactosCliente();
                    $ContactosCliente->ccliente_contacto = $elemento;
                    $ContactosCliente->ccliente_descripcion = "";
                    $ContactosCliente->cli_id = $cliente->cli_id;
                    $ContactosCliente->save();
                }
            } else {
                $ContactosCliente = new ContactosCliente();
                $ContactosCliente->ccliente_contacto = $item["TELEFONO"];
                $ContactosCliente->ccliente_descripcion = "";
                $ContactosCliente->cli_id = $cliente->cli_id;
                $ContactosCliente->save();
            }
        }

        $fecha = $item["FE"];


        $Solicitud = new Solicitud();
        $Solicitud->tipo_solicitud = "Nuevo";
        $Solicitud->solicitud_nombre = $cliente->cli_nombre;
        $Solicitud->analista_id = 6;
        $Solicitud->destino = "no refiere";
        $Solicitud->tipo_vivienda = "Propia";
        $Solicitud->solicitud_antiguedad = "no refiere";
        $Solicitud->solicitud_direccion_negocio =$cliente->cli_domicilio;
        $Solicitud->solicitud_referencia_negocio =$cliente->cli_domicilio;
        $Solicitud->solicitud_giro =  isset($item["NEGOCIO"]) ? $item["NEGOCIO"] : "no refiere";;
        $Solicitud->solicitud_lugar =  isset($item["ZONA"]) ? $item["ZONA"] : "no refiere";
        $Solicitud->solicitud_referencia_cliente = "REFERENCIA";
        $Solicitud->status = "G";
        $Solicitud->solicitud_documento =  isset($item["DNI"]) ? $item["DNI"] : "00000000";
        $Solicitud->cli_id = $cliente->cli_id;
        $Solicitud->created_user = 1;
        $Solicitud->sucursal_id = 1;
        $Solicitud->created_at = Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d h:i:s");
        $Solicitud->updated_at = Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d h:i:s");
        $Solicitud->save();



        $interes =  $item["PORCENTAJE"];

        $monto_credito = number_format($item["CREDITO"], 2);

        $frecuencia_pagos = "Diario";

        $cuota = 0;
        switch ($frecuencia_pagos) {
            case 'Diario':
                $intervalo = 30;
                $cuota = Formulas::calcularAmortizacionFrancesDiaria($monto_credito, $intervalo, $interes);
                break;

            case 'Semanal':
                $intervalo = 4;
                $cuota = Formulas::calcularAmortizacionFrancesDiaria($monto_credito, $intervalo, $interes);
                break;
        }

        $monto_credito_sin_comas = $monto_credito;
        switch ($frecuencia_pagos) {
            case 'Diario': 
                $cronogramasData = Formulas::calcularAmortizacionFrancesDiaria_cronograma(Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d"), $monto_credito_sin_comas, $intervalo, $interes);
                break;
            case 'Mensual':

                $cronogramasData = Formulas::calcularAmortizacionFrancesMensual_cronograma($monto_credito_sin_comas, $intervalo, $interes);
                break;
            case 'Semanal':

                $cronogramasData = Formulas::calcularAmortizacionFrancesSemanal_cronograma(Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d"), $monto_credito_sin_comas, $intervalo, $interes);
                break;
        }


        $prestamo = new Prestamo();
        $prestamo->cli_id = $cliente->cli_id;
        $prestamo->analista_id = 6;
        $prestamo->solicitud_id = $Solicitud->solicitud_id;
        $prestamo->status = "A";
        $prestamo->moto_credito = floatval($item["CREDITO"]);
        $prestamo->interes = $interes;
        $prestamo->cuotas = $cuota;
        $prestamo->frecuencia_pagos = $frecuencia_pagos;
        $prestamo->user_id = 1;
        $prestamo->sucursal_id = 1;
        $prestamo->intervalo = $intervalo;
        $prestamo->tasa_diaria = number_format(($interes / 30), 2);
        $Solicitud->created_at = Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d h:i:s");
        $Solicitud->updated_at = Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d h:i:s");
        $prestamo->save();

        $Solicitud->prestamo_id =$prestamo->prestamo_id;
        $Solicitud->save();

        $cronogramasData = array_map(function ($item, $key) use ($prestamo) {
            $item['prestamo_id'] = $prestamo->prestamo_id;
            return $item;
        }, $cronogramasData, array_keys($cronogramasData));


        $get_prestamo = Prestamo::find($prestamo->prestamo_id);
        $get_prestamo->d_t = array_sum(array_column($cronogramasData, 'cuota'));
        $get_prestamo->save();

        cronograma::insert($cronogramasData);


        if (isset($item["PAGOS"])) {
            foreach ($item["PAGOS"] as $key => $value) {
                if ($value != 0) {
                    $ingreso = new ingreso();
                    $ingreso->monto = number_format(floatval($value), 2);
                    $ingreso->descripcion = "amortizacion de la solicitud n° {$Solicitud->code}";
                    $ingreso->created_user = 2;
                    $ingreso->sucursal_id = 1;
                    $ingreso->yes_office = "Y";
                    $ingreso->caja_chica_id = 4;
                    $ingreso->codigo = funciones::generar_codigo(number_format(floatval($value), 2));
                    $ingreso->created_at = Carbon::parse($key)->format("Y-m-d h:i:s");
                    $ingreso->is_amortizacion = "Y";
                    $ingreso->prestamo_id = $prestamo->prestamo_id;
                    $ingreso->updated_at =  Carbon::parse($key)->format("Y-m-d h:i:s");
                    $ingreso->save();
    
                    $pagos = new pagos();
                    $pagos->ingreso_id = $ingreso->ingreso_id;
                    $pagos->monto =  number_format(floatval($value), 2);
                    $pagos->cuentas_id = 1;
                    $pagos->tipo = "I";
                    $pagos->caja_chica_id = 4;
                    $pagos->created_user  = 2;
                    $pagos->sucursal_id  = 1;
                    $ingreso->created_at = Carbon::parse($key)->format("Y-m-d h:i:s");
                    $ingreso->updated_at =  Carbon::parse($key)->format("Y-m-d h:i:s");
                    $pagos->save();
                }
                
            }
        }

        // $caja = new caja();
        // $caja->referencia = "levantamiento de datos";
        // $caja->user_id = 1;  

    }

    public function diario_oficina_profesor($item)
    {
        $cliente = new Cliente();
        $cliente->cli_nombre = $item["CLIENTE"];
        $cliente->tipo_cliente = "tarjeta estado";
        $cliente->cli_dni = isset($item["DNI"]) ? $item["DNI"] : "00000000";
        $cliente->cli_domicilio = isset($item["DOMICILIO"]) ? $item["DOMICILIO"] : "no tiene";
        $cliente->save();

        if (isset($item["TELEFONO"])) {
            if (strpos($item["TELEFONO"], '-') !== false) {
                $elementos = explode("-", $item["TELEFONO"]);

                // Recorrer el array con un bucle foreach
                foreach ($elementos as $elemento) {
                    $ContactosCliente = new ContactosCliente();
                    $ContactosCliente->ccliente_contacto = $elemento;
                    $ContactosCliente->ccliente_descripcion = "";
                    $ContactosCliente->cli_id = $cliente->cli_id;
                    $ContactosCliente->save();
                }
            } else {
                $ContactosCliente = new ContactosCliente();
                $ContactosCliente->ccliente_contacto = $item["TELEFONO"];
                $ContactosCliente->ccliente_descripcion = "";
                $ContactosCliente->cli_id = $cliente->cli_id;
                $ContactosCliente->save();
            }
        }

        $fecha = $item["FE"];
 
        $Solicitud = new Solicitud();
        $Solicitud->tipo_solicitud = "Nuevo";
        $Solicitud->solicitud_nombre = $cliente->cli_nombre;
        $Solicitud->analista_id = 17;
        $Solicitud->destino = "no refiere";
        $Solicitud->tipo_vivienda = "Propia";
        $Solicitud->solicitud_antiguedad = "no refiere";
        $Solicitud->solicitud_direccion_negocio =$cliente->cli_domicilio;
        $Solicitud->solicitud_referencia_negocio =$cliente->cli_domicilio;
        $Solicitud->solicitud_giro =  isset($item["NEGOCIO"]) ? $item["NEGOCIO"] : "no refiere";;
        $Solicitud->solicitud_lugar =  isset($item["ZONA"]) ? $item["ZONA"] : "no refiere";
        $Solicitud->solicitud_referencia_cliente = "REFERENCIA";
        $Solicitud->status = "G";
        $Solicitud->solicitud_documento =  isset($item["DNI"]) ? $item["DNI"] : "00000000";
        $Solicitud->cli_id = $cliente->cli_id;
        $Solicitud->created_user = 1;
        $Solicitud->sucursal_id = 1;
        $Solicitud->created_at = Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d h:i:s");
        $Solicitud->updated_at = Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d h:i:s");
        $Solicitud->save();



        $interes =  $item["PORCENTAJE"];

        $monto_credito = number_format($item["CREDITO"], 2);

        $frecuencia_pagos = "Diario";

        $cuota = 0;
        switch ($frecuencia_pagos) {
            case 'Diario':
                $intervalo = 30;
                $cuota = Formulas::calcularAmortizacionFrancesDiaria($monto_credito, $intervalo, $interes);
                break;

            case 'Semanal':
                $intervalo = 4;
                $cuota = Formulas::calcularAmortizacionFrancesDiaria($monto_credito, $intervalo, $interes);
                break;
        }

        $monto_credito_sin_comas = $monto_credito;
        switch ($frecuencia_pagos) {
            case 'Diario': 
                $cronogramasData = Formulas::calcularAmortizacionFrancesDiaria_cronograma(Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d"), $monto_credito_sin_comas, $intervalo, $interes);
                break;
            case 'Mensual':

                $cronogramasData = Formulas::calcularAmortizacionFrancesMensual_cronograma($monto_credito_sin_comas, $intervalo, $interes);
                break;
            case 'Semanal':

                $cronogramasData = Formulas::calcularAmortizacionFrancesSemanal_cronograma(Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d"), $monto_credito_sin_comas, $intervalo, $interes);
                break;
        }


        $prestamo = new Prestamo();
        $prestamo->cli_id = $cliente->cli_id;
        $prestamo->analista_id = 17;
        $prestamo->solicitud_id = $Solicitud->solicitud_id;
        $prestamo->status = "A";
        $prestamo->moto_credito = floatval($item["CREDITO"]);
        $prestamo->interes = $interes;
        $prestamo->cuotas = $cuota;
        $prestamo->frecuencia_pagos = $frecuencia_pagos;
        $prestamo->user_id = 1;
        $prestamo->sucursal_id = 1;
        $prestamo->intervalo = $intervalo;
        $prestamo->tasa_diaria = number_format(($interes / 30), 2);
        $Solicitud->created_at = Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d h:i:s");
        $Solicitud->updated_at = Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d h:i:s");
        $prestamo->save();

        $Solicitud->prestamo_id =$prestamo->prestamo_id;
        $Solicitud->save();

        $cronogramasData = array_map(function ($item, $key) use ($prestamo) {
            $item['prestamo_id'] = $prestamo->prestamo_id;
            return $item;
        }, $cronogramasData, array_keys($cronogramasData));


        $get_prestamo = Prestamo::find($prestamo->prestamo_id);
        $get_prestamo->d_t = array_sum(array_column($cronogramasData, 'cuota'));
        $get_prestamo->save();

        cronograma::insert($cronogramasData);


        if (isset($item["PAGOS"])) {
            foreach ($item["PAGOS"] as $key => $value) {
                if ($value != 0) {
                    $ingreso = new ingreso();
                    $ingreso->monto = number_format(floatval($value), 2);
                    $ingreso->descripcion = "amortizacion de la solicitud n° {$Solicitud->code}";
                    $ingreso->created_user = 2;
                    $ingreso->sucursal_id = 1;
                    $ingreso->yes_office = "Y";
                    $ingreso->caja_chica_id = 4;
                    $ingreso->codigo = funciones::generar_codigo(number_format(floatval($value), 2));
                    $ingreso->created_at = Carbon::parse($key)->format("Y-m-d h:i:s");
                    $ingreso->is_amortizacion = "Y";
                    $ingreso->prestamo_id = $prestamo->prestamo_id;
                    $ingreso->updated_at =  Carbon::parse($key)->format("Y-m-d h:i:s");
                    $ingreso->save();
    
                    $pagos = new pagos();
                    $pagos->ingreso_id = $ingreso->ingreso_id;
                    $pagos->monto =  number_format(floatval($value), 2);
                    $pagos->cuentas_id = 1;
                    $pagos->tipo = "I";
                    $pagos->caja_chica_id = 4;
                    $pagos->created_user  = 2;
                    $pagos->sucursal_id  = 1;
                    $ingreso->created_at = Carbon::parse($key)->format("Y-m-d h:i:s");
                    $ingreso->updated_at =  Carbon::parse($key)->format("Y-m-d h:i:s");
                    $pagos->save();
                }
                
            }
        }

        // $caja = new caja();
        // $caja->referencia = "levantamiento de datos";
        // $caja->user_id = 1;  

    }

    public function diario_oficina_tarjeta_varias($item)
    {
        $cliente = new Cliente();
        $cliente->cli_nombre = $item["CLIENTE"];
        $cliente->tipo_cliente = "tarjeta privada";
        $cliente->cli_dni = isset($item["DNI"]) ? $item["DNI"] : "00000000";
        $cliente->cli_domicilio = isset($item["DOMICILIO"]) ? $item["DOMICILIO"] : "no tiene";
        $cliente->save();

        if (isset($item["TELEFONO"])) {
            if (strpos($item["TELEFONO"], '-') !== false) {
                $elementos = explode("-", $item["TELEFONO"]);

                // Recorrer el array con un bucle foreach
                foreach ($elementos as $elemento) {
                    $ContactosCliente = new ContactosCliente();
                    $ContactosCliente->ccliente_contacto = $elemento;
                    $ContactosCliente->ccliente_descripcion = "";
                    $ContactosCliente->cli_id = $cliente->cli_id;
                    $ContactosCliente->save();
                }
            } else {
                $ContactosCliente = new ContactosCliente();
                $ContactosCliente->ccliente_contacto = $item["TELEFONO"];
                $ContactosCliente->ccliente_descripcion = "";
                $ContactosCliente->cli_id = $cliente->cli_id;
                $ContactosCliente->save();
            }
        }

        $fecha = $item["FE"];
 
        $Solicitud = new Solicitud();
        $Solicitud->tipo_solicitud = "Nuevo";
        $Solicitud->solicitud_nombre = $cliente->cli_nombre;
        $Solicitud->analista_id = 17;
        $Solicitud->destino = "no refiere";
        $Solicitud->tipo_vivienda = "Propia";
        $Solicitud->solicitud_antiguedad = "no refiere";
        $Solicitud->solicitud_direccion_negocio =$cliente->cli_domicilio;
        $Solicitud->solicitud_referencia_negocio =$cliente->cli_domicilio;
        $Solicitud->solicitud_giro =  isset($item["NEGOCIO"]) ? $item["NEGOCIO"] : "no refiere";;
        $Solicitud->solicitud_lugar =  isset($item["ZONA"]) ? $item["ZONA"] : "no refiere";
        $Solicitud->solicitud_referencia_cliente = "REFERENCIA";
        $Solicitud->status = "G";
        $Solicitud->solicitud_documento =  isset($item["DNI"]) ? $item["DNI"] : "00000000";
        $Solicitud->cli_id = $cliente->cli_id;
        $Solicitud->created_user = 1;
        $Solicitud->sucursal_id = 1;
        $Solicitud->created_at = Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d h:i:s");
        $Solicitud->updated_at = Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d h:i:s");
        $Solicitud->save();



        $interes =  $item["PORCENTAJE"];

        $monto_credito = number_format($item["CREDITO"], 2);

        $frecuencia_pagos = "Diario";

        $cuota = 0;
        switch ($frecuencia_pagos) {
            case 'Diario':
                $intervalo = 30;
                $cuota = Formulas::calcularAmortizacionFrancesDiaria($monto_credito, $intervalo, $interes);
                break;

            case 'Semanal':
                $intervalo = 4;
                $cuota = Formulas::calcularAmortizacionFrancesDiaria($monto_credito, $intervalo, $interes);
                break;
        }

        $monto_credito_sin_comas = $monto_credito;
        switch ($frecuencia_pagos) {
            case 'Diario': 
                $cronogramasData = Formulas::calcularAmortizacionFrancesDiaria_cronograma(Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d"), $monto_credito_sin_comas, $intervalo, $interes);
                break;
            case 'Mensual':

                $cronogramasData = Formulas::calcularAmortizacionFrancesMensual_cronograma($monto_credito_sin_comas, $intervalo, $interes);
                break;
            case 'Semanal':

                $cronogramasData = Formulas::calcularAmortizacionFrancesSemanal_cronograma(Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d"), $monto_credito_sin_comas, $intervalo, $interes);
                break;
        }


        $prestamo = new Prestamo();
        $prestamo->cli_id = $cliente->cli_id;
        $prestamo->analista_id = 17;
        $prestamo->solicitud_id = $Solicitud->solicitud_id;
        $prestamo->status = "A";
        $prestamo->moto_credito = floatval($item["CREDITO"]);
        $prestamo->interes = $interes;
        $prestamo->cuotas = $cuota;
        $prestamo->frecuencia_pagos = $frecuencia_pagos;
        $prestamo->user_id = 1;
        $prestamo->sucursal_id = 1;
        $prestamo->intervalo = $intervalo;
        $prestamo->tasa_diaria = number_format(($interes / 30), 2);
        $Solicitud->created_at = Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d h:i:s");
        $Solicitud->updated_at = Carbon::createFromFormat('d-m-y', $fecha)->format("Y-m-d h:i:s");
        $prestamo->save();

        $Solicitud->prestamo_id =$prestamo->prestamo_id;
        $Solicitud->save();

        $cronogramasData = array_map(function ($item, $key) use ($prestamo) {
            $item['prestamo_id'] = $prestamo->prestamo_id;
            return $item;
        }, $cronogramasData, array_keys($cronogramasData));


        $get_prestamo = Prestamo::find($prestamo->prestamo_id);
        $get_prestamo->d_t = array_sum(array_column($cronogramasData, 'cuota'));
        $get_prestamo->save();

        cronograma::insert($cronogramasData);


        if (isset($item["PAGOS"])) {
            foreach ($item["PAGOS"] as $key => $value) {
                if ($value != 0) {
                    $ingreso = new ingreso();
                    $ingreso->monto = number_format(floatval($value), 2);
                    $ingreso->descripcion = "amortizacion de la solicitud n° {$Solicitud->code}";
                    $ingreso->created_user = 2;
                    $ingreso->sucursal_id = 1;
                    $ingreso->yes_office = "Y";
                    $ingreso->caja_chica_id = 4;
                    $ingreso->codigo = funciones::generar_codigo(number_format(floatval($value), 2));
                    $ingreso->created_at = Carbon::parse($key)->format("Y-m-d h:i:s");
                    $ingreso->is_amortizacion = "Y";
                    $ingreso->prestamo_id = $prestamo->prestamo_id;
                    $ingreso->updated_at =  Carbon::parse($key)->format("Y-m-d h:i:s");
                    $ingreso->save();
    
                    $pagos = new pagos();
                    $pagos->ingreso_id = $ingreso->ingreso_id;
                    $pagos->monto = $value;
                    $pagos->cuentas_id = 1;
                    $pagos->tipo = "I";
                    $pagos->caja_chica_id = 4;
                    $pagos->created_user  = 2;
                    $pagos->sucursal_id  = 1;
                    $ingreso->created_at = Carbon::parse($key)->format("Y-m-d h:i:s");
                    $ingreso->updated_at =  Carbon::parse($key)->format("Y-m-d h:i:s");
                    $pagos->save();
                }
                
            }
        }

        // $caja = new caja();
        // $caja->referencia = "levantamiento de datos";
        // $caja->user_id = 1;  

    }

    public function leer_json()
    {

        // Ruta al archivo JSON
        $jsonFilePath = storage_path('app/public/planilla_semana_rafael.json');

        // Leer el contenido del archivo JSON
        $jsonContent = File::get($jsonFilePath);

        // Decodificar el JSON a un array de PHP
        $dataArray = json_decode($jsonContent, true);

        // Realizar un bucle foreach para procesar cada elemento
        foreach ($dataArray as $item) {
            $this->semana_rafael($item);
        }
        dd("datos cargados exitosamente");
           // Ruta al archivo JSON
           $json_diario_alejandra = storage_path('app/public/diario_alejandro.json');

           // Leer el contenido del archivo JSON
           $jsonContent_diario_alejandra = File::get($json_diario_alejandra);
   
           // Decodificar el JSON a un array de PHP
           $dataArray_diario_alejandra = json_decode($jsonContent_diario_alejandra, true);
   
           // Realizar un bucle foreach para procesar cada elemento
           foreach ($dataArray_diario_alejandra as $item) {
               $this->diario_alejandro($item);
           }
       
         // Ruta al archivo JSON
         $jsonFilePath = storage_path('app/public/diario_rafael.json');

         // Leer el contenido del archivo JSON
         $jsonContent = File::get($jsonFilePath);
 
         // Decodificar el JSON a un array de PHP
         $dataArray = json_decode($jsonContent, true);
 
         // Realizar un bucle foreach para procesar cada elemento
         foreach ($dataArray as $item) {
             $this->diario_rafael($item);
         }
       
        

        // Ruta al archivo JSON
        $json_semana_alejandra = storage_path('app/public/semanero_alejandro.json');

        // Leer el contenido del archivo JSON
        $jsonContent_semana_alejandra = File::get($json_semana_alejandra);

        // Decodificar el JSON a un array de PHP
        $dataArray_semana_alejandro = json_decode($jsonContent_semana_alejandra, true);

        // Realizar un bucle foreach para procesar cada elemento
        foreach ($dataArray_semana_alejandro as $item) {
            $this->semana_alejandro($item);
        }
 

       

     

        // Ruta al archivo JSON
        $json_oficina_trabajadora_diario = storage_path('app/public/oficina_trabajadora_diario.json');

        // Leer el contenido del archivo JSON
        $jsonContent_oficina_trabajadora_diario = File::get($json_oficina_trabajadora_diario);

        // Decodificar el JSON a un array de PHP
        $dataArray_oficina_trabajadora_diario = json_decode($jsonContent_oficina_trabajadora_diario, true);

        // Realizar un bucle foreach para procesar cada elemento
        foreach ($dataArray_oficina_trabajadora_diario as $item) {
            $this->diario_oficina_profesor($item);
        }


        // Ruta al archivo JSON
        $json_oficina_tarjeta_varios = storage_path('app/public/oficina_tarjeta_varios.json');

        // Leer el contenido del archivo JSON
        $jsonContent_oficina_tarjeta_varios = File::get($json_oficina_tarjeta_varios);

        // Decodificar el JSON a un array de PHP
        $dataArray_oficina_tarjeta_varios = json_decode($jsonContent_oficina_tarjeta_varios, true);

        // Realizar un bucle foreach para procesar cada elemento
        foreach ($dataArray_oficina_tarjeta_varios as $item) {
            $this->diario_oficina_tarjeta_varias($item);
        }


      


    }
}
