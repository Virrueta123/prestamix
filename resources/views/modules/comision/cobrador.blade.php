@extends('layouts.app')
@section('history')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('comision.index') }}">Comisiones</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $trabajador->name }} {{ $trabajador->lastname }}</li>
</ol>
@endsection
@section('body')
    <div id="app">
        <comision-cobrador-view
            :trabajador='@json($trabajador)'
            :mes-inicial="{{ $mes }}"
            :anio-inicial="{{ $anio }}"
        ></comision-cobrador-view>
    </div>
@endsection