@extends("layouts.default")

@section("content")
   <atividades
        departamento="{{ db_getsession('DB_coddepto') }}"
        modulo="{{ db_getsession('DB_modulo') }}">
   </atividades>
@endsection
