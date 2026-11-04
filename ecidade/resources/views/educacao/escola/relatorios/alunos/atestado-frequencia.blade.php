@extends("layouts.default")

@section("content")
    <atestado_frequencia departamento="{{ db_getsession('DB_coddepto') }}"
                       modulo="{{ db_getsession("DB_modulo") }}"/>
@endsection
