@extends('layouts.app')
@section('history')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Panel</a></li>
        <li class="breadcrumb-item active" aria-current="page"></li>
    </ol>
@endsection
@section('body')
    <div id="app">
        {{-- <img width="200" src="data:image/png;base64,{{DNS1D::getBarcodePNG('2024', 'C39E')}}" alt="barcode" /> --}}
        <home-principal></home-principal>
    </div>
@endsection
