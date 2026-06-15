@extends('layouts.app')
@section('history')
<ol class="breadcrumb">
<li class="breadcrumb-item"><a href="#">Panel</a></li>
<li class="breadcrumb-item active" aria-current="page">Vista del cliente</li>
</ol>
@endsection
@section('body')
    <div id="app">
        <show-cliente :get_cliente="{{ $cliente }}" ></show-cliente>
    </div>
@endsection