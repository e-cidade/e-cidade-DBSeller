@extends("layouts.default")

@section("content")
    <lrf-emissao
        tipo="{{$tipo}}"
        anexo="{{$anexo}}"
        exercicio="{{$exercicio}}"
        instituicao="{{$instituicao}}"
        login="{{$login}}"
        consolidado="{{$consolidado}}"
    >
    </lrf-emissao>
@endsection
