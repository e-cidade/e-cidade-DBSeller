@extends("layouts.default")

@section("content")
    <configuracao_ajuda instituicao="{{ db_getsession('DB_instit') }}"/>
@endsection
