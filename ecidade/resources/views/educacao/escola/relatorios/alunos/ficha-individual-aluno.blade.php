@extends("layouts.default")

@section("content")
    <ficha_individual_aluno escola="{{ db_getsession('DB_coddepto') }}"
                            usuario="{{ db_getsession('DB_id_usuario') }}"/>
@endsection
