@extends('layouts.app')
@section('history')
<ol class="breadcrumb">
<li class="breadcrumb-item"><a href="#">Panel</a></li>
<li class="breadcrumb-item active" aria-current="page"></li>
</ol>
@endsection
@section('body')
    <div id="app">
        <show-solicitud-trabajador :solicitud_trabajador="{{ $solicitud_trabajador }}"></show-solicitud-trabajador>
    </div>
@endsection