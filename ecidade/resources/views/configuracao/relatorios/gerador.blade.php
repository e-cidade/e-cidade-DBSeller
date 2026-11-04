@extends('layouts.default')

@section('content')
    <gerador_relatorios usuario="{{ $usuario }}" departamento="{{ $departamento }}">
@endsection
