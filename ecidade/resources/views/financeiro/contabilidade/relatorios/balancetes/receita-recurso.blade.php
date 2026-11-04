@extends("layouts.default")

@section("content")
    <relatorio-balancete-receita-recurso exercicio="{{$exercicio}}" data-sistema="{{$dataSistema}}"></relatorio-balancete-receita-recurso>
@endsection
