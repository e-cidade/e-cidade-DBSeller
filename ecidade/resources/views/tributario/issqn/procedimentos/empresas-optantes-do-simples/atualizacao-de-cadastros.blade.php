@extends("layouts.default")

@section("content")
    <atualizacao_de_cadastros_simples_nacional usuario="{{ db_getsession('DB_id_usuario') }}" />
@endsection
