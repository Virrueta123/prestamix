<?php

namespace App\Utils;

use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class ticketera
{

    public static function imprimir_ingreso($saldo_anterior, $saldo_cancelado, $codigo, $cliente, $n_solicitud, $analista, $recepcionista, $total, $para = "cliente")
    {

        $fecha_impresion = date('d/m/Y h:ia');


        // URL a la que deseas hacer la solicitud
        $url = 'https://fowl-sacred-strangely.ngrok-free.app/print_script/public/';



        // Datos que deseas enviar en la solicitud POST
        $postData = array(
            'saldo_pendiente' => $saldo_anterior,
            'monto_cancelado' => $saldo_cancelado,
            'codigo' =>  $codigo,
            'cliente' => $cliente,
            'numerosolicitud' => $n_solicitud,
            'analista' => $analista,
            'recepcionista' => $recepcionista,
            'total' => $total,
            "para" =>  $para,
            "fecha_impresion" => $fecha_impresion,
        );

        // Inicializar cURL
        $ch = curl_init();

        // Establecer la URL de la solicitud
        curl_setopt($ch, CURLOPT_URL, $url);

        // Establecer el método de la solicitud como POST
        curl_setopt($ch, CURLOPT_POST, true);

        // Convertir los datos a formato de cadena y establecerlos como datos de POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));

        // Si necesitas agregar encabezados u otros datos a la solicitud, aquí es donde lo harías
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer TOKEN'));

        // Establecer que deseas recibir la respuesta como una cadena en lugar de imprimirla directamente
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($ch);

        // Verificar si hubo errores
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        }

        // Cerrar la conexión cURL
        curl_close($ch);

        //  echo($response );
    }


    public static function imprimir_ingreso_grupal($descripcion, $cuota, $saldo_restante, $saldo_cancelado, $codigo, $cliente, $n_solicitud, $analista, $recepcionista, $total, $para = "cliente")
    {

        $fecha_impresion = date('d/m/Y h:ia');

        // URL a la que deseas hacer la solicitud
        $url = 'https://fowl-sacred-strangely.ngrok-free.app/print_script/public/_grupal';

        // Datos que deseas enviar en la solicitud POST
        $postData = array(
            'descripcion' => $descripcion,
            'cuota' => $cuota,
            'saldo_restante' => $saldo_restante,
            'monto_cancelado' => $saldo_cancelado,
            'codigo' =>  $codigo,
            'cliente' => $cliente,
            'numerosolicitud' => $n_solicitud,
            'analista' => $analista,
            'recepcionista' => $recepcionista,
            'total' => $total,
            "para" =>  $para,
            "fecha_impresion" => $fecha_impresion,
        );


        // Inicializar cURL
        $ch = curl_init();

        // Establecer la URL de la solicitud
        curl_setopt($ch, CURLOPT_URL, $url);

        // Establecer el método de la solicitud como POST
        curl_setopt($ch, CURLOPT_POST, true);

        // Convertir los datos a formato de cadena y establecerlos como datos de POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));

        // Si necesitas agregar encabezados u otros datos a la solicitud, aquí es donde lo harías
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer TOKEN'));

        // Establecer que deseas recibir la respuesta como una cadena en lugar de imprimirla directamente
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($ch);

        // Verificar si hubo errores
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        }


        // Cerrar la conexión cURL
        curl_close($ch);
    }


    public static function imprimir_gasto($descripcion, $tipo_cancelado, $codigo, $recepcionista, $total)
    {

        $fecha_impresion = date('d/m/Y h:ia');


        // URL a la que deseas hacer la solicitud
        $url = 'https://fowl-sacred-strangely.ngrok-free.app/print_script/public/impresion_gastos';



        // Datos que deseas enviar en la solicitud POST
        $postData = array(
            'descripcion' => $descripcion,
            'tipo_gasto' => $tipo_cancelado,
            'code' => $codigo,
            'recepcionista' => $recepcionista,
            'monto' => $total,
            'fecha_impresion' => $fecha_impresion,
        );


        // Inicializar cURL
        $ch = curl_init();

        // Establecer la URL de la solicitud
        curl_setopt($ch, CURLOPT_URL, $url);

        // Establecer el método de la solicitud como POST
        curl_setopt($ch, CURLOPT_POST, true);

        // Convertir los datos a formato de cadena y establecerlos como datos de POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));

        // Si necesitas agregar encabezados u otros datos a la solicitud, aquí es donde lo harías
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer TOKEN'));

        // Establecer que deseas recibir la respuesta como una cadena en lugar de imprimirla directamente
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($ch);

        // Verificar si hubo errores
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        }

        // Cerrar la conexión cURL
        curl_close($ch);
    }


    public static function imprimir_desembolso($descripcion, $tipo_cancelado, $codigo, $recepcionista, $total, $copia, $cliente, $para)
    {

        $fecha_impresion = date('d/m/Y h:ia');

        // URL a la que deseas hacer la solicitud
        $url = 'https://fowl-sacred-strangely.ngrok-free.app/print_script/public/imprimir_desembolso';



        // Datos que deseas enviar en la solicitud POST
        $postData = array(
            'descripcion' => $descripcion,
            'tipo_gasto' => $tipo_cancelado,
            'code' => $codigo,
            'recepcionista' => $recepcionista,
            'monto' => $total,
            "copia" => $copia,
            "para" => $para,
            'fecha_impresion' => $fecha_impresion,
            "cliente" => $cliente
        );


        // Inicializar cURL
        $ch = curl_init();

        // Establecer la URL de la solicitud
        curl_setopt($ch, CURLOPT_URL, $url);

        // Establecer el método de la solicitud como POST
        curl_setopt($ch, CURLOPT_POST, true);

        // Convertir los datos a formato de cadena y establecerlos como datos de POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));

        // Si necesitas agregar encabezados u otros datos a la solicitud, aquí es donde lo harías
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer TOKEN'));

        // Establecer que deseas recibir la respuesta como una cadena en lugar de imprimirla directamente
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($ch);

        // Verificar si hubo errores
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        }


        // Cerrar la conexión cURL
        curl_close($ch);

        // echo($response );

    }


    public static function imprimir_gasto_duplicado($fecha_impresion, $descripcion, $tipo_cancelado, $codigo, $recepcionista, $total)
    {

        // URL a la que deseas hacer la solicitud
        $url = 'https://fowl-sacred-strangely.ngrok-free.app/print_script/public/impresion_gastos';

        // Datos que deseas enviar en la solicitud POST
        $postData = array(
            'descripcion' => $descripcion,
            'tipo_gasto' => $tipo_cancelado,
            'code' => $codigo,
            'recepcionista' => $recepcionista,
            'monto' => $total,
            'fecha_impresion' => $fecha_impresion,
        );

        // Inicializar cURL
        $ch = curl_init();

        // Establecer la URL de la solicitud
        curl_setopt($ch, CURLOPT_URL, $url);

        // Establecer el método de la solicitud como POST
        curl_setopt($ch, CURLOPT_POST, true);

        // Convertir los datos a formato de cadena y establecerlos como datos de POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));

        // Si necesitas agregar encabezados u otros datos a la solicitud, aquí es donde lo harías
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer TOKEN'));

        // Establecer que deseas recibir la respuesta como una cadena en lugar de imprimirla directamente
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($ch);

        // Verificar si hubo errores
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        }


        // Cerrar la conexión cURL
        curl_close($ch);

        // // echo($response ); 
    }

    public static function impresion_voucher_prestamo_cancelado(
        $FechaCreacion,
        $Nombres,
        $Dni,
        $NumeroSolicitud,
        $MCredito,
        $Fpago,
        $Cuotas,
        $Interes,
        $InteresDiario,
        $DiasTranscurrido,
        $InteresTotal,
        $MontoRestante,
        $Total,
        $fecha_inicio,
        $fecha_final,
    ) {

        // Base URL a la que deseas hacer la solicitud
        $baseUrl = 'https://fowl-sacred-strangely.ngrok-free.app/print_script/public/impresion_voucher_prestamo_cancelado';

        // Datos que deseas enviar como parámetros en la solicitud GET
        $getData = array(
            "Nombres" => $Nombres,
            "Dni" => $Dni,
            "NumeroSolicitud" => $NumeroSolicitud,
            "FechaCreacion" => $FechaCreacion,
            "MCredito" => $MCredito,
            "Fpago" => $Fpago,
            "Cuotas" => $Cuotas,
            "Interes" => $Interes,
            "InteresDiario" => $InteresDiario,
            "DiasTranscurrido" => $DiasTranscurrido,
            "InteresTotal" => $InteresTotal,
            "MontoRestante" => $MontoRestante,
            "Total" => $Total,
            "fecha_inicio" => $fecha_inicio,
            "fecha_final" => $fecha_final,
        );

        // Construir la URL completa con los parámetros GET
        $url = $baseUrl . '?' . http_build_query($getData);

        // Inicializar cURL
        $ch = curl_init();

        // Establecer la URL completa
        curl_setopt($ch, CURLOPT_URL, $url);

        // Establecer que deseas recibir la respuesta como una cadena en lugar de imprimirla directamente
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($ch);

        // Verificar si hubo errores
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        } else {
            // Imprimir la respuesta (puedes procesarla según sea necesario)
            
        }

        // Cerrar la conexión cURL
        curl_close($ch);
    }
}
