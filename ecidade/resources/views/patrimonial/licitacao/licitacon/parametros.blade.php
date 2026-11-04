@extends('layouts.default')

@section('content')
    <parametros_licitacon instituicao="{{ db_getsession('DB_instit') }}" departamento="{{ db_getsession('DB_coddepto')  }}" />
@endsection
