@extends("layouts.default")
@section("content")
    <relatorio_ajuda instituicao="{{ db_getsession('DB_instit') }}"/>
@endsection
