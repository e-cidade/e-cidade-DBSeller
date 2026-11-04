@extends("layouts.default")

@section("content")
    <bases_curriculares_crud escola="{{ db_getsession('DB_coddepto') }}"
                             modulo="{{ db_getsession("DB_modulo") }}"
    ></bases_curriculares_crud>
@endsection
