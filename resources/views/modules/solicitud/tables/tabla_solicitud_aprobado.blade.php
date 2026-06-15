@extends('layouts.app')
@section('history')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Panel</a></li>
        <li class="breadcrumb-item " aria-current="page"><a href="{{ route('solicitud.index') }}">Solicitud</a></li>
        <li class="breadcrumb-item active" aria-current="page"> Solicitudes Aprobadas </li>
    </ol>
@endsection
@section('body')
    <div id="app">
        <table-solicitudes-aprobado></table-solicitudes-aprobado>    
    </div>
@endsection

@section('js')
    <script>
        
    </script>
@endsection
