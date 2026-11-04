@extends("layouts.default")

@section("head")

@section("content")

    <lancamento-manual instituicao="{{$instituicao}}"
                       exercicio="{{$exercicio}}"
                       data-sistema="{{$dataSistema}}"
                       data-encerramento="{{$dataEncerramento}}">
    </lancamento-manual>
@endsection
