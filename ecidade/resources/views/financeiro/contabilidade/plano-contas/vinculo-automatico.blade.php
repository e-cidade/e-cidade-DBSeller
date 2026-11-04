@extends("layouts.default")

@section("content")
    <plano-contas-vinculo-automatico exercicio="{{db_getsession('DB_anousu')}}" ></plano-contas-vinculo-automatico>
@endsection
