@extends("layouts.default")

@section("content")
    <cobertura_recurso_extra exercicio="{{db_getsession('DB_anousu')}}"></cobertura_recurso_extra>
@endsection
