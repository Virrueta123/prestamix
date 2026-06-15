@extends('layouts.app')
@section('history')
<ol class="breadcrumb">
<li class="breadcrumb-item"><a href="#">Panel</a></li>
<li class="breadcrumb-item active" aria-current="page"></li>
</ol>
@endsection
@section('body')
    <div id="app">
        <table-msj-prestamos></table-msj-prestamos>
    </div>
@endsection