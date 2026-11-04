@extends("layouts.default")

@section("content")
    <boletim_desempenho_pcd escola="{{ db_getsession('DB_coddepto') }}" 
        modulo="{{ db_getsession('DB_modulo') }}" 
    />
@endsection
