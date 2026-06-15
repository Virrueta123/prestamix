@extends('layouts.app')
@section('history')
<ol class="breadcrumb">
<li class="breadcrumb-item"><a href="#">Panel</a></li>
<li class="breadcrumb-item active" aria-current="page">REFINANCIAMIENTO</li>
</ol>
@endsection
@section('body')
    <div id="app"> 
        <refinanciamiento-prestamo :get_solicitud="{{$solicitud}}" :monto_credito_nuevo="{{$monto_credito_nuevo}}"></-prestamo>
    </div>
@endsection