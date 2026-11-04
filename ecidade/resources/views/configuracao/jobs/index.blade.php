@extends('layouts.default')

@section('head')
    <style>
        .p-button .p-button-icon-only {
            width: 2.357rem;
            padding: 0.5rem 0;
            min-width: 2.357rem;
        }

        .p-button {
            min-width: 2.357rem;
        }

        .p-tabview .p-tabview-nav li.p-highlight .p-tabview-nav-link {
            margin: 0 !important;
        }

        .p-tabview .p-tabview-nav li .p-tabview-nav-link {
            margin: 0 !important;
        }

        .p-tabview .p-tabview-panels {
            padding: 3px;
        }

        .p-tabview-nav {
            flex-grow: 1;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <jobs></jobs>
    </div>
@endsection

