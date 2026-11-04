@extends("layouts.default")

@section("content")
    <acompanhamento_alunos_pcd escola="{{ db_getsession('DB_coddepto') }}"
                               modulo="{{ db_getsession("DB_modulo") }}"> </acompanhamento_alunos_pcd>
@endsection