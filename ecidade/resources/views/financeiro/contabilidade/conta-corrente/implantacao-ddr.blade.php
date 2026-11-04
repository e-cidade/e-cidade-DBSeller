@extends("layouts.default")

@section("head")
<style>
    .p-fileupload-file-thumbnail {
        display: none !important;
    }
</style>
@endsection

@section("content")
    <implantacao_ddr exercicio="{{db_getsession('DB_anousu')}}"></implantacao_ddr>
@endsection
