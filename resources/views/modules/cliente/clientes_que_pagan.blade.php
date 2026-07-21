@extends('layouts.app')
@section('history')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Panel</a></li>
    <li class="breadcrumb-item"><a href="{{ route('cliente.index') }}">Cliente</a></li>
    <li class="breadcrumb-item active" aria-current="page">Clientes que pagan</li>
</ol>
@endsection
@section('body')
    <div id="app">
        <table-clientes-que-pagan></table-clientes-que-pagan>
    </div>
@endsection
