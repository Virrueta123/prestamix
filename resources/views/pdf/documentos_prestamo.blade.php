<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ public_path('dist/images/logo/logo.svg') }}" rel="shortcut icon">
    <title>Horizon Finance</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&display=swap');

        .text-negrita-italic {
            font-weight: bold;
            font-style: italic;
            text-decoration: underline;
            background: red;
            font-family: "Nunito Sans", sans-serif;
        }

        .text-sub {
            font-weight: bold;
            text-decoration: underline;
            font-family: "Nunito Sans", sans-serif;
            font-style: italic;
            margin: 2px;
        }

        .separador {
            margin: 3px;
        }

        .separador-mayor {
            margin: 8px;
        }

        .text-negrita {
            font-weight: bold;
            font-family: "Nunito Sans", sans-serif;

        }

        .text-negrita-2 {
            font-weight: bold;
            font-size: 16px;
            text-align: center;
            font-family: "arial";
        }

        .text-normal {
            font-size: 12px;
            font-family: "Nunito Sans", sans-serif;
        }

        .text-normal-2 {
            font-size: 16px;
            width: 600px;
            margin: auto;
            font-family: "arial";
        }

        .text-negrita-titulo {
            font-size: 20px;
            font-weight: bold;
            margin: 2px;
            font-family: "Nunito Sans", sans-serif;
            text-align: center;
        }

        .text-negrita-titulo-2 {
            font-family: "arial";
            font-size: 18px;
            text-decoration: underline;
            font-weight: bold;
            text-align: center;
        }

        .text-negrita-titulo2 {
            font-weight: bold;
            margin: 2px;
            font-family: "Nunito Sans", sans-serif;
            text-align: center;
        }

        /* table cronograma */
        .table-cronograma {
            width: 100%;
            border-collapse: collapse;
        }

        .table-cronograma thead tr {
            background: #2E3192;
            color: white;
            border: 1px solid #2E3192;
        }

        .table-cronograma thead tr th {
            border: 1px solid white;
        }

        .table-cronograma tbody tr td {
            border: 1px solid #2E3192;
        }

        .table-cronograma tbody tr:nth-of-type(even) {
            background-color: #f3f3f3;
        }

        .marca_agua {
            position: fixed;
            left: 23%;
            width: 340px;
            top: 34%;
            opacity: 0.18;
        }

        .solicitud_documento {
            width: 100%;
            height: 100%;
            margin: 0px;
            padding: 0px;
        }
    </style>
</head>

<body>
    <img src="data:image/svg+xml;base64,{{ base64_encode($marca_agua) }}" class="marca_agua" />

    <img src="data:image/svg+xml;base64,{{ base64_encode($solicitud_documento) }}" class="solicitud_documento" />

    <center><img src="{{ public_path('dist/images/logo/logo.png') }}" alt="Mi Imagen"></center>
    <div class="separador"></div>
    <p class="text-negrita-titulo-2 ">CONSTANCIA DE ENTREGA – RECEPCION DE DINERO PRESTADO</p>

    <p class="text-negrita-titulo2 "> </p>

    <foreignObject transform="matrix(1 0 0 1 37.0645 365)" width="508" height="97">
        <div class="text-normal-2" style="text-align: justify;">

            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita-2">EL MUTUANTE</tspan>
                Mediante el presente documento, <tspan class="text-negrita-2">yo, {{ $nombre }}</tspan> con DNI Nº
                <tspan class="text-negrita-2">{{ $documento }}</tspan> con domicilio en el {{ $direccion }},
                {{ $distrito }}, Distrito,
                Provincia de {{ $provincia }}, Departamento de {{ $departamento }}, en este acto,
                recibo en calidad de prestado, de la Empresa <tspan class="text-negrita-2">HORIZON FINANCE E.I.R.L</tspan>,
                representada por la señora Olga Panduro
                Pinedo, con un poder inscrito en registros públicos con partida N° 11179962 la suma de <tspan
                    class="text-negrita-2">S/. {{ $monto_credito }}</tspan> ({{ $monto_credito_deletreado }}), dinero
                entregado en efectivo, lo mismo indefectiblemente serán
                devueltos en su integridad, caso contrario se someten a las acciones legales, ante las Autoridades
                Judiciales de su Distrito y/o Provincia de San Martín, Región san Martín para su devolución respectiva,
                aceptando el deudor.
            </text>
        </div>
    </foreignObject>
    <div class="separador-mayor"></div>
    <foreignObject transform="matrix(1 0 0 1 37.0645 365)" width="508" height="97">
        <div class="text-normal-2" style="text-align: justify;">

            <text x="0" y="10" textAnchor="start">
                <tspan style="margin-left: 16px"> </tspan> Leída y encontrándose conforme el contenido, de ambas partes,
                suscriben sus firmas, el deudor
                adicionalmente, estampa su impresión digital de su dedo índice derecho en señal de conformidad.
            </text>
        </div>
    </foreignObject>
    <div class="separador-mayor"></div>
    <foreignObject transform="matrix(1 0 0 1 37.0645 365)" width="508" height="97">
        <div class="text-normal" style="text-align: justify;">

            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita-2" style="position: absolute; right: 7%;">Tarapoto, {{ $dia }} de
                    {{ $mes }} del {{ $ano }}.
                </tspan>
            </text>
            <div class="separador-mayor" style="margin-bottom: 15px;"></div>
        </div>
    </foreignObject>
    <div class="separador-mayor" style="margin-bottom: 100px;"></div>



    <div class="separador-mayor" style="margin-bottom: 160px;"></div>

    <foreignObject transform="matrix(1 0 0 1 37.0645 365)" width="508" height="97">
        <div class="text-normal" style="text-align: justify;">
            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita-2" style="position: absolute; left: 13%;">
                    ......................................................</tspan>
            </text>
            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita-2" style="position: absolute; right: 12%;">
                    ......................................................</tspan>
            </text>
        </div>
    </foreignObject>



    <div class="separador-mayor" style="margin-bottom: 20px;"></div>

    <foreignObject transform="matrix(1 0 0 1 37.0645 365)" width="508" height="130">
        <div class="text-normal" style="text-align: justify;">
            <text x="10" y="10" textAnchor="start">
                <tspan class="text-negrita-2" style="position: absolute; left: 12%;">{{ $nombre }}</tspan>
            </text>
            <text x="10" y="10" textAnchor="start">
                <tspan class="text-negrita-2" style="position: absolute; right: 14%;">OLGA PANDURO PINEDO</tspan>
            </text>
        </div>
    </foreignObject>
 

    <div class="separador-mayor" style="margin-bottom: 10px;"></div>

    <foreignObject transform="matrix(1 0 0 1 37.0645 500)" width="508" height="130">
        <div class="text-normal" style="text-align: justify;">
            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita-2" style="position: absolute; left: 19%;">DNI Nº {{ $documento }}</tspan>
            </text>
            <text x="0" y="9" textAnchor="start">
                <tspan class="text-negrita-2" style="position: absolute; right: 19%;">DNI Nº 80210814</tspan>
            </text>
        </div>
    </foreignObject>

    <div class="separador-mayor" style="margin-bottom: 20px;"></div>
    <foreignObject transform="matrix(1 0 0 1 37.0645 365)" width="508" height="97">
        <div class="text-normal" style="text-align: justify;">
            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita-2" style="position: absolute; left: 18%;"> RECIBI CONFORME </tspan>
            </text>
            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita-2" style="position: absolute; right: 14%;">ENTREGUE CONFORME </tspan>
            </text>
        </div>
    </foreignObject>

    <div class="separador-mayor" style="margin-bottom: 220px;"></div>


    <center><img src="{{ public_path('dist/images/logo/logo.png') }}" alt="Mi Imagen"></center>
    <div class="separador-mayor" style="margin-bottom: 447px;"></div>




    <div class="separador-mayor" style="margin-bottom: 160px;"></div>

    <foreignObject transform="matrix(1 0 0 1 37.0645 365)" width="508" height="97">
        <div class="text-normal" style="text-align: justify;">
            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita-2" style="position: absolute; left: 13%;">
                    ......................................................</tspan>
            </text>
            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita-2" style="position: absolute; right: 12%;">
                    ......................................................</tspan>
            </text>
        </div>
    </foreignObject>

    <div class="separador-mayor" style="margin-bottom: 20px;"></div>

    <foreignObject transform="matrix(1 0 0 1 37.0645 365)" width="508" height="130">
        <div class="text-normal" style="text-align: justify;">
            <text x="10" y="10" textAnchor="start">
                <tspan class="text-negrita-2" style="position: absolute; left: 12%;">{{ $nombre }}</tspan>
            </text>
            <text x="10" y="10" textAnchor="start">
                <tspan class="text-negrita-2" style="position: absolute; right: 14%;">OLGA PANDURO PINEDO</tspan>
            </text>
        </div>
    </foreignObject>


    <div class="separador-mayor" style="margin-bottom: 10px;"></div>

    <foreignObject transform="matrix(1 0 0 1 37.0645 500)" width="508" height="130">
        <div class="text-normal" style="text-align: justify;">
            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita-2" style="position: absolute; left: 19%;">DNI Nº {{ $documento }}
                </tspan>
            </text>
            <text x="0" y="9" textAnchor="start">
                <tspan class="text-negrita-2" style="position: absolute; right: 19%;">DNI Nº 80210814</tspan>
            </text>
        </div>
    </foreignObject>

    <div class="separador-mayor" style="margin-bottom: 20px;"></div>

    <foreignObject transform="matrix(1 0 0 1 37.0645 365)" width="508" height="97">
        <div class="text-normal" style="text-align: justify;">
            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita-2" style="position: absolute; left: 18%;"> RECIBI CONFORME </tspan>
            </text>
            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita-2" style="position: absolute; right: 14%;">ENTREGUE CONFORME </tspan>
            </text>
        </div>
    </foreignObject>





    <div class="separador-mayor" style="margin-bottom: 220px;"></div>


    <foreignObject transform="matrix(1 0 0 1 37.0645 365)" width="508" height="97">
        <div class="text-normal" style="text-align: justify;">

            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita">UNDÉCIMA. - </tspan>
                Queda convenido que la mora por retraso en el pago de alguna de las cuotas equivale al diez por ciento
                (10%) del importe cedido en calidad de préstamo, es decir que se calcula sobre el íntegro del capital
                realizado en el presente mutuo.
                Cumplido el requisito de mora, <tspan class="text-negrita">EL MUTUANTE</tspan> se encuentra facultado a
                ejercer las acciones legales
                correspondientes para hacer cumplir el compromiso de la deuda asumida por <tspan class="text-negrita">
                    EL MUTUATARIO</tspan>, dentro de dicho
                acto puede ejecutar los actos relacionados con la retención del título de propiedad, ejecutoriedad de
                los títulos valores, ejecución de medidas forzosas relacionados con demandas de entrega de dar suma de
                dinero, medidas de embargo para lo cual deja su número de cuenta N° {{ $solicitud_tarjeta }}
                {{ $solicitud_entidad_tarjeta }} y otras de
                naturaleza similar.
                Queda entendido que existe mora automática al siguiente día del no pago de la cuota del préstamo en la
                fecha indicada en el cronograma de pagos.

            </text>
        </div>
    </foreignObject>

    <div class="separador-mayor"></div>

    <p class="text-sub">
        MORA
    </p>

    <div class="separador"></div>

    <foreignObject transform="matrix(1 0 0 1 37.0645 365)" width="508" height="97">
        <div class="text-normal" style="text-align: justify;">

            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita">DUODÉCIMA.</tspan>
                - Queda convenido que la mora por retraso en el pago de alguna de las cuotas equivale al
                {{ $interes_deletreado }} por ciento ({{ $interes }}%) del importe cedido en calidad de préstamo,
                es decir que se calcula sobre el íntegro del
                capital realizado en el presente mutuo.
                Cumplido el requisito de mora, <tspan class="text-negrita">EL MUTUANTE</tspan> se encuentra facultado a
                ejercer las acciones legales
                correspondientes para hacer cumplir el compromiso de la deuda asumida por <tspan class="text-negrita">
                    EL MUTUATARIO,</tspan> dentro de dicho
                acto puede ejecutar los actos relacionados con la retención del título de propiedad, ejecutoriedad de
                los títulos valores, ejecución de medidas forzosas relacionados con demandas de entrega de dar suma de
                dinero, medidas de embargo y otras de naturaleza similar.
                Queda entendido que existe mora automática al siguiente día del no pago de la cuota del préstamo en la
                fecha indicada en el cronograma de pagos.
            </text>
        </div>
    </foreignObject>

    <div class="separador-mayor"></div>

    <p class="text-sub">
        REPRESENTACION LEGAL:
    </p>

    <div class="separador"></div>

    <foreignObject transform="matrix(1 0 0 1 37.0645 365)" width="508" height="97">
        <div class="text-normal" style="text-align: justify;">

            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita">DÉCIMO TERCERA.</tspan>
                - Queda entendido que la representación legal para realizar la gestión de cobranza,
                cobrar penalidades por mora y ejecutar las acciones legales correspondientes recaen en nombre de la
                señora <tspan class="text-negrita">Olga Panduro Pinedo,</tspan> las mismas que tiene todas las
                prerrogativas legales, administrativas,
                financieras, operativas para gestionar y hacer cumplir los acuerdos que emanan del presente acuerdo, por
                ende puede ejecutar a sola firma los títulos valores (letras de cambio), título de propiedad y algún
                tipo de bien dejado en prenda en la medida que EL MUTUATARIO no cumpla con cancelar las cuotas del
                préstamo dentro del plazo y las formas señaladas para tal fín.

            </text>
        </div>
    </foreignObject>

    <div class="separador-mayor"></div>

    <p class="text-sub">
        GASTOS Y TRIBUTOS DEL CONTRATO:
    </p>

    <div class="separador"></div>

    <foreignObject transform="matrix(1 0 0 1 37.0645 365)" width="508" height="97">
        <div class="text-normal" style="text-align: justify;">

            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita">DÉCIMO CUARTA.</tspan>
                - Las partes acuerdan que todos los gastos y tributos que originen la celebración y
                ejecución de este contrato serán asumidos por EL MUTUATARIO.

            </text>
        </div>
    </foreignObject>

    <div class="separador-mayor"></div>

    <p class="text-sub">
        COMPETENCIA TERRITORIAL:
    </p>

    <div class="separador"></div>

    <foreignObject transform="matrix(1 0 0 1 37.0645 365)" width="508" height="97">
        <div class="text-normal" style="text-align: justify;">

            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita">DÉCIMO QUINTA. -</tspan>
                Para efectos de cualquier controversia que se genere con motivo de la celebración y
                ejecución de este contrato, las partes se someten a la competencia territorial de los jueces y
                tribunales de San Martín
            </text>
        </div>
    </foreignObject>

    <div class="separador-mayor"></div>

    <p class="text-sub">
        DOMICILIO:
    </p>

    <div class="separador"></div>

    <foreignObject transform="matrix(1 0 0 1 37.0645 365)" width="508" height="97">
        <div class="text-normal" style="text-align: justify;">

            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita">DÉCIMO SEXTA.</tspan>
                - Para la validez de todas las comunicaciones y notificaciones a las partes, con motivo de
                la ejecución de este contrato, ambas señalan como sus respectivos domicilios los indicados en la
                introducción de este documento. El cambio de domicilio de cualquiera de las partes surtirá efecto desde
                la fecha de comunicación de dicho cambio a la otra parte, por cualquier medio escrito.
            </text>
        </div>
    </foreignObject>

    <div class="separador-mayor"></div>

    <p class="text-sub">
        APLICACIÓN SUPLETORIA DE LA LEY:
    </p>

    <div class="separador"></div>

    <foreignObject transform="matrix(1 0 0 1 37.0645 365)" width="508" height="97">
        <div class="text-normal" style="text-align: justify;">

            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita">DÉCIMO SÉTIMA.</tspan>
                - En lo no previsto por las partes en el presente contrato, ambas se someten a lo
                establecido por las normas del Código Civil y demás del sistema jurídico que resulten aplicables.
            </text>
        </div>
    </foreignObject>
    <div class="separador-mayor"></div>
    <div class="separador-mayor"></div>

    <foreignObject transform="matrix(1 0 0 1 37.0645 365)" width="508" height="97">
        <div class="text-normal" style="text-align: justify;">

            <text x="0" y="10" textAnchor="start">
                En señal de conformidad las partes suscriben este documento en la ciudad de , a
                los ....... días del mes de ........... de ..............
            </text>
        </div>
    </foreignObject>
    <div class="separador-mayor"></div>
    <div class="separador-mayor"></div>
    <div class="separador-mayor"></div>
    <div class="separador-mayor"></div>


    <foreignObject transform="matrix(1 0 0 1 37.0645 365)" width="508" height="97">
        <div class="text-normal" style="text-align: justify;">

            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita" style="position: absolute; left: 6%;">
                    .............................................................</tspan>

                <tspan class="text-negrita" style="position: absolute; right: 6%;">
                    .............................................................</tspan>
            </text>
        </div>
    </foreignObject>

    <div class="separador-mayor"></div>

    <foreignObject transform="matrix(1 0 0 1 37.0645 365)" width="508" height="97">
        <div class="text-normal" style="text-align: justify; ">

            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita" style="position: absolute; left: 6%;">EL MUTUANTE</tspan>

                <tspan class="text-negrita" style="position: absolute; right: 6%;">{{ $nombre }}</tspan>
            </text>
        </div>
    </foreignObject>

    <div class="separador-mayor"></div>

    <foreignObject transform="matrix(1 0 0 1 37.0645 365)" width="508" height="97">
        <div class="text-normal" style="text-align: justify; ">

            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita" style="position: absolute; left: 6%;">80210814</tspan>

                <tspan class="text-negrita" style="position: absolute; right: 6%;">{{ $documento }}</tspan>
            </text>
        </div>
    </foreignObject>


    <div class="separador-mayor" style="margin-bottom: 150px;"></div>

    <center><img src="{{ public_path('dist/images/logo/logo.png') }}" width="200" alt="Mi Imagen"></center>




    <p class="text-negrita-titulo ">CONTRATO
        DE MUTUO DE DINERO</p>

    <p class="text-negrita-titulo2 "> </p>
    <div class="separador-mayor"></div>

    <foreignObject transform="matrix(1 0 0 1 37.0645 120)" font-family="'ArialMT'" font-size="10px" width="508"
        height="97">
        <div class="text-normal" style="text-align: justify;">
            <text x="0" y="0" textAnchor="start">
                Constes por el presente documents el contrato de mutuo que celebran de una parte <tspan
                    class="text-negrita">HORIZON FINANCE EIRL</tspan>, con RUC
                N° <tspan class="text-negrita">20608330284</tspan>, con domicilio fiscal en el Jirón Bolognesi N°
                523
                del distrito de Tarapoto – San Martín
                e inscrita en la partida registral N° 11179962 de los registros públicos de Tarapoto, debidamente
                representada por su titular gerente señora Olga Panduro Pinedo , a quien en lo sucesivo se
                denominará
                <tspan class="text-negrita">EL
                    MUTUANTE</tspan>; y de otra parte {{ $nombre2 }} identificado con
                DNI N°
                {{ $documento }},
                con domicilio real en {{ $direccion }}, del distrito de --{{ $distrito }}, provincia de
                {{ $distrito }},
                departamento de {{ $departamento }}, a quien en lo sucesivo se denominará <tspan class="text-negrita">
                    EL
                    MUTUATARIO;</tspan> en los términos
                contenidos en las cláusulas siguientes:
            </text>
        </div>
    </foreignObject>

    <div class="separador-mayor"></div>

    <p class="text-sub">
        ANTECEDENTES:
    </p>

    <div class="separador"></div>

    <foreignObject transform="matrix(1 0 0 1 37.0645 220)" font-family="'ArialMT'" font-size="10px" width="508"
        height="97">
        <div class="text-normal" style="text-align: justify;">

            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita">PRIMERA. - EL MUTUATARIO </tspan>es una persona natural, de ocupación
                {{ $ocupacion }} que requiere capital de
                trabajo
                y/o financiamiento con motivo de realizar una determinada inversión y/o actividad propia de sus
                necesidades actuales y futuras.
            </text>
        </div>
    </foreignObject>

    <div class="separador"></div>

    <foreignObject transform="matrix(1 0 0 1 37.0645 250)" font-family="'ArialMT'" font-size="10px" width="508"
        height="97">
        <div class="text-normal" style="text-align: justify;">

            <text x="0" y="10" class="text-normal" textAnchor="start">
                <tspan class="text-negrita">SEGUNDA.</tspan>- Por lo expresado en la cláusula primera <tspan
                    class="text-negrita">EL MUTUATARIO</tspan>,
                requiere contar con un capital de trabajo ascendente a la suma de S/. {{ $monto_credito }}
                ({{ $monto_credito_deletreado }})
                que
                <tspan class="text-negrita">EL MUTUANTE</tspan> está dispuesto a entregarle en calidad de préstamo.
            </text>
        </div>
    </foreignObject>

    <div class="separador-mayor"></div>

    <p class="text-sub">
        OBJETO DEL CONTRATO:
    </p>

    <div class="separador"></div>
    <foreignObject transform="matrix(1 0 0 1 37.0645 306)" width="508" height="97">
        <div class="text-normal" style="text-align: justify;">

            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita">TERCERA.</tspan>
                - Por el presente contrato, <tspan class="text-negrita">EL MUTUANTE</tspan> se obliga a entregar en
                mutuo, en favor de EL MUTUATARIO,
                la suma de dinero ascendente a S/. {{ $monto_credito }} ({{ $monto_credito_deletreado }}).<tspan
                    class="text-negrita">EL
                    MUTUATARIO</tspan> , a su turno, se obliga a
                devolver a <tspan class="text-negrita">EL MUTUANTE</tspan> la referida suma de dinero en la forma y
                oportunidad pactadas en las cláusulas siguientes.
            </text>
        </div>
    </foreignObject>

    <div class="separador-mayor"></div>

    <p class="text-sub">
        OBLIGACIONES DE LAS PARTES:
    </p>

    <div class="separador"></div>

    <foreignObject transform="matrix(1 0 0 1 37.0645 365)" width="508" height="97">
        <div class="text-normal" style="text-align: justify;">

            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita">CUARTA. - EL MUTUANTE</tspan>
                se obliga a entregar la suma de dinero objeto de la prestación a su cargo en el momento de la
                firma de este documento, sin más constancia que las firmas de las partes puestas en él.
            </text>
        </div>
    </foreignObject>

    <foreignObject transform="matrix(1 0 0 1 37.0645 395)" width="508" height="97">
        <div class="text-normal" style="text-align: justify;">

            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita">QUINTA. - EL MUTUATARIO</tspan>
                declara haber recibido conforme la referida suma mutuada,
                en dinero en efectivo, en moneda nacional y en la cantidad a que se refiere la cláusula tercera.
            </text>
        </div>
    </foreignObject>

    <foreignObject transform="matrix(1 0 0 1 37.0645 425)" width="508" height="97">
        <div class="text-normal" style="text-align: justify;">

            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita">SEXTA. - EL MUTUATARIO</tspan>
                se obliga a devolver el íntegro del dinero objeto del mutuo, según el cronograma adjunto.
            </text>
        </div>
    </foreignObject>

    <div class="separador-mayor"></div>

    <table class="table-cronograma text-normal">
        <thead>
            <tr>
                <th>Periodo</th>
                <th>Fecha Vencimiento</th>
                <th>Saldo capital</th>
                <th>Amortización</th>
                <th>Interes</th>
                <th>Cuota</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td> </td>
                <td>{{ \Carbon\Carbon::parse($solicitud->prestamo->fechainicio)->format('d/m/Y') }}</td>
                <td>{{ $solicitud->prestamo->moto_credito }}</td>
                <td> </td>
                <td> </td>
                <td> </td>
            </tr>
            @foreach ($solicitud->prestamo->cronograma as $cronograma)
                <tr>
                    <td>{{ $cronograma->periodo }}</td>
                    <td>{{ \Carbon\Carbon::parse($cronograma->fechaVencimiento)->format('d/m/Y') }}</td>
                    <td>{{ $cronograma->saldoCapital }}</td>
                    <td>{{ $cronograma->amortizacion }}</td>
                    <td>{{ $cronograma->interes }}</td>
                    <td>{{ $cronograma->cuota }}</td>
                </tr>
            @endforeach
        </tbody>

        {{-- mas texto --}}


    </table>

    <div class="separador-mayor"></div>

    <foreignObject transform="matrix(1 0 0 1 37.0645 395)" width="508" height="97">
        <div class="text-normal" style="text-align: justify;">
            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita">SÉTIMA. - EL MUTUATARIO</tspan>
                se obliga a cumplir fielmente con el cronograma de pagos descrito en la cláusula anterior.
                En caso de incumplimiento en el pago de una de las armadas, cualquiera que sea, quedarán vencidas todas
                las demás,
                y en consecuencia EL MUTUANTE estará facultado para exigir el pago íntegro de la suma de dinero mutuada.
            </text>
        </div>
    </foreignObject>

    <div class="separador"></div>

    <foreignObject transform="matrix(1 0 0 1 37.0645 395)" width="508" height="97">
        <div class="text-normal" style="text-align: justify;">
            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita">OCTAVA.</tspan>
                - Las partes acuerdan que EL MUTUATARIO devolverá la suma de dinero objeto del mutuo, en la misma moneda
                y cantidad recibida, debiendo efectuar el pago de cada armada con dinero en efectivo.
            </text>
        </div>
    </foreignObject>

    <div class="separador-mayor"></div>
    <p class="text-sub">
        LUGAR DE PAGO:
    </p>

    <div class="separador"></div>

    <foreignObject transform="matrix(1 0 0 1 37.0645 365)" width="508" height="97">
        <div class="text-normal" style="text-align: justify;">

            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita">NOVENA.</tspan>
                - Las partes dejan constancia de que el lugar de pago será el domicilio de <tspan class="text-negrita">
                    EL MUTUANTE;</tspan>
                y cuando no fuere posible realizarlo en dicho lugar, se realizará en el domicilio y/o lugar donde
                se encuentre <tspan class="text-negrita">EL MUTUATARIO.</tspan>
            </text>
        </div>
    </foreignObject>

    <div class="separador-mayor"></div>

    <p class="text-sub">
        GARANTÍA:
    </p>

    <div class="separador"></div>

    <foreignObject transform="matrix(1 0 0 1 37.0645 365)" width="508" height="97">
        <div class="text-normal" style="text-align: justify;">

            <text x="0" y="10" textAnchor="start">
                <tspan class="text-negrita">DÉCIMA. - EL MUTUATARIO</tspan>
                deja en calidad de garantía un título valor <tspan class="text-negrita">(letra de cambio)</tspan>
                debidamente
                firmado en favor de <tspan class="text-negrita">EL MUTUANTE,</tspan> por el monto total del importe
                cedido en calidad de mutuo. Asimismo,
                para garantizar el compromiso de la deuda contraída deja en custodia su tarjeta multired.
            </text>
        </div>
    </foreignObject>

    <div class="separador-mayor"></div>

    <p class="text-sub">
        PAGO DE INTERESES:
    </p>

    <div class="separador"></div>








</body>

</html>
