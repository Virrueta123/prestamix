@extends('layouts.app')
@section('history')
<ol class="breadcrumb">
<li class="breadcrumb-item"><a href="#">Panel</a></li>
<li class="breadcrumb-item active" aria-current="page">Pos</li>
</ol>
@endsection
@section('body')
    <div id="app">
        <panel-pos> </panel-pos>
    </div>
@endsection