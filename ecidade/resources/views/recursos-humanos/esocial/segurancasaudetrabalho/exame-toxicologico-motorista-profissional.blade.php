@extends("layouts.default")

@section("content")
    <evento_s2221 instituicao="{{db_getsession('DB_instit')}}"/>
@endsection
