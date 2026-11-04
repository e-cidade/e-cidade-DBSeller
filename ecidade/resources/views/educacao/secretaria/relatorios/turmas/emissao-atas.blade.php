@extends("layouts.default")

@section("content")
    <emissao_atas escola="{{ db_getsession('DB_coddepto') }}" 
        modulo="{{ db_getsession('DB_modulo') }}" 
    />
@endsection
