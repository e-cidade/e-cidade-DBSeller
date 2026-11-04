@extends('layouts.default')

@section('content')
    <incluir_obra_licitacon instituicao="{{ db_getsession('DB_instit') }}" departamento="{{ db_getsession('DB_coddepto')  }}" />
@endsection
