@extends('layouts.app')
@section('history')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Panel</a></li>
        <li class="breadcrumb-item active" aria-current="page"></li>
    </ol>
@endsection
@section('body')
<div class="intro-y flex flex-col sm:flex-row items-center mt-8">
    <h2 class="text-lg font-medium mr-auto">
       Tabla de todo los usuarios
    </h2>
    <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <a href="{{ route('usuario.create') }}" class="btn btn-primary shadow-md mr-2"> <i data-lucide='user-plus'></i> agregar usuario</a> 
    </div>
</div>


 

<div id="app">
    <table-users :users="{{ $users }}"></table-users>
</div>

@endsection
