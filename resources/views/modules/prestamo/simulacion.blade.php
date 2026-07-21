@extends('layouts.app')
@section('history')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Panel</a></li>
    <li class="breadcrumb-item active" aria-current="page">Simulación de préstamo</li>
</ol>
@endsection
@section('body')
    <div id="app">
        <simulacion></simulacion>
    </div>
@endsection