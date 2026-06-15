@extends('layouts.app')
@section('history')
<ol class="breadcrumb">
<li class="breadcrumb-item"><a href="#">Panel</a></li>
<li class="breadcrumb-item active" aria-current="page">Ampliacion</li>
</ol>
@endsection
@section('body')
    <div id="app"> 
        <ampliacion-prestamo :get_solicitud="{{$solicitud}}" :monto_credito_nuevo="{{$monto_credito_nuevo}}"></ampliacion-prestamo>
    </div>
@endsection