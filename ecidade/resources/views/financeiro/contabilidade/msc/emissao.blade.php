@extends("layouts.default")

@section("head")

@section("content")
    <emissao-msc instituicao="{{$instituicao}}" exercicio="{{$exercicio}}"></emissao-msc>
@endsection
