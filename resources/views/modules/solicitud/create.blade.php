@extends('layouts.app')
@section('history')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Panel</a></li>
        <li class="breadcrumb-item active" aria-current="page"></li>
    </ol>
@endsection
@section('body')
    <div id="app">
        @if ($solicitud == '')
        <h1>Nulo</h1>
        <create-solicitud :solicitudparalelo="''"></create-solicitud>
        @else  
        <create-solicitud :solicitudparalelo="{{ $solicitud }}" ></create-solicitud>
        @endif

  
      
    </div>
@endsection
