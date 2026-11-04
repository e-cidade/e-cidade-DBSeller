<!doctype html>
<html>
<head>
    <title>DBSeller Informática Ltda - Página Inicial</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="ECIDADE_REQUEST_PATH" content="{{ ECIDADE_REQUEST_PATH }}">
    <script src="{{ asset("scripts/scripts.js") }}"></script>
    @if(config("app.env") == "local")
        <script type="text/javascript" src="http://localhost:35729/livereload.js"></script>
        <script src="http://localhost:8098"></script>
    @endif
    <link href="{{ asset("public" . mix("primevue.css")) }}" rel="stylesheet" type="text/css">
    <link href="{{ asset("public" . mix("vue/css/style.css")) }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shepherd.js@10.0.1/dist/css/shepherd.css"/>
    @yield("head")
</head>
<body>
<div id="app">
    <div>
        <toast/>
    </div>
    @yield("content")
</div>
<script src="{{ asset("public" . mix("vue/js/app.js")) }}" ></script>
@yield("scripts")
</body>
</html>
