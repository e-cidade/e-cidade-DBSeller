@extends("layouts.default")

@section("content")
    <decendio-relatorio exercicio="{{$exercicio}}"
                        instituicao="{{$instituicao}}"
                        data-sistema="{{$dataSistema}}">
    </decendio-relatorio>
@endsection
