@extends("layouts.default")

@section("content")
    <registro_aula user="{{ db_getsession("DB_id_usuario") }}"
                   escola="{{ db_getsession('DB_coddepto') }}"></registro_aula>
@endsection
