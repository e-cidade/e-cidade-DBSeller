@extends("layouts.default")

@section("head")
<style>
    .p-dialog{
        border: 1px solid #a19f9d !important;
    }
    .p-dialog-content{
        background: #e1dede !important;
    }
    .p-datatable-header {
        background: #e1dede !important;
        border : none !important;
    }
</style>
@endsection
@section("content")
    <lancamento_ajuda instituicao="{{ db_getsession('DB_instit') }}"/>
@endsection
