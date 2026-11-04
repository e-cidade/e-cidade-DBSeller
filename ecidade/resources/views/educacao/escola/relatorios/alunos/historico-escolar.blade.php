@extends("layouts.default")

@section("content")
    <historico_escolar aluno="{{ isset($aluno) ? $aluno : '' }}" departamento="{{ db_getsession('DB_coddepto') }}"
                       modulo="{{ db_getsession("DB_modulo") }}"/>
@endsection
