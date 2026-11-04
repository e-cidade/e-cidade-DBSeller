@extends("layouts.default")

@section("content")
    <recurso-confere-vinculo exercicio="{{db_getsession('DB_anousu')}}" data-sistema="{{$dataSistema}}"></recurso-confere-vinculo>
@endsection
