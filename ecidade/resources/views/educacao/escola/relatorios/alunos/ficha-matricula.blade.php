@extends("layouts.default")

@section("content")
    <ficha_matricula escola="{{ db_getsession('DB_coddepto') }}" />
@endsection
