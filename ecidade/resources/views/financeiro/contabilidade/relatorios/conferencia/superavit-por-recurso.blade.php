@extends("layouts.default")

@section("content")
    <superavit-por-recurso instituicao="{{$instituicao}}" exercicio="{{$exercicio}}" data-sistema="{{$dataSistema}}"></superavit-por-recurso>
@endsection
