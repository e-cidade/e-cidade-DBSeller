@extends("layouts.default")

@section("content")
    <parametros_cemiterio ano="{{ db_getsession('DB_anousu') }}"/>
@endsection
