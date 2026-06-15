@extends('layouts.app')
@section('history')
<ol class="breadcrumb">
<li class="breadcrumb-item"><a href="#">Panel</a></li>
<li class="breadcrumb-item active" aria-current="page"></li>
</ol>
@endsection
@section('body')
 
<div id="app">
    <table-planilla :nombre="'{{ $nombre }}'" >  </table-planilla>
</div>

@endsection

@section('js')
    
@endsection