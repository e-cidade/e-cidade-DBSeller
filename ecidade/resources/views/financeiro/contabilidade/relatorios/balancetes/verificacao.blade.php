@extends("layouts.default")

@section("content")
    <relatorio-balancete-verificacao exercicio="{{$exercicio}}" data-sistema="{{$dataSistema}}"></relatorio-balancete-verificacao>
@endsection
