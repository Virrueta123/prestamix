@extends('layouts.app')
@section('history')
<ol class="breadcrumb">
<li class="breadcrumb-item"><a href="#">Panel</a></li>
<li class="breadcrumb-item active" aria-current="page"></li>
</ol>
@endsection
@section('body')
    <div id="app">
        <show-caja :get_caja="{{ $get_caja }}" :get_tabla_tarjeta_ingresos="{{ $get_tabla_tarjeta_ingresos }}"></show-caja>
    </div>
@endsection