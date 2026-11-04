@extends("layouts.default")

@section("content")
    <atualizador_aulas_dadas user="{{ db_getsession("DB_id_usuario") }}"
                   escola="{{ db_getsession('DB_coddepto') }}"></atualizador_aulas_dadas>
@endsection
