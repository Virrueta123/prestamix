@extends('layouts.app')
@section('history')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Panel</a></li>
        <li class="breadcrumb-item active" aria-current="page"></li>
    </ol>
@endsection
@section('body')
    <div id="app">
         <cancelar_prestamo 
         :solicitud="{{$solicitud}}"
         :prestamo="{{$prestamo}}"
         :interespordia="{{$interes_por_dia}}"
         :diferenciadias="{{$diferenciaDias}}"
         :montointeres="{{$monto_interes}}"
         :isingresos="{{$is_ingresos}}"
         :amortizacion="{{$amortizacion}}"
         :cuotaactual="{{$cuota_actual}}"
         :saldocapital="{{$saldo_capital}}"
         :cuotaanterior="{{$cuota_anterior}}"
         >
         
        </cancelar_prestamo>
    </div>
@endsection
