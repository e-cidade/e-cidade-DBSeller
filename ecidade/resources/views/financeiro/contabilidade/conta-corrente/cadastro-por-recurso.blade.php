@extends("layouts.default")

@section("head")
<style>
    .p-fileupload-file-thumbnail {
        display: none !important;
    }
</style>
@endsection

@section("content")
    <cadastro_por_recurso instituicao="{{$instituicao}}" exercicio="{{$exercicio}}"></cadastro_por_recurso>
@endsection
