<!doctype html>
<html lang="pt-BR" style="background-color: #efefef;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <style>
        .cabecalho {
            display: flex;
            flex-direction: row;
            align-items: center;
            width: 100%;
            height: 80px;
            background-color: #7cb4df;
            border-radius: 10px;
        }

        .logo-div {
            display: flex;
            justify-content: flex-end;
            flex-grow: 1;
            margin: 10px;
        }

        .corpo {
            display: flex;
            flex-direction: column;
            margin-left: 50px;
            margin-top: 50px;
        }

        b {
            font-family: sans-serif;
        }
    </style>
</head>
<body>
    <div class="cabecalho">
        <b style="margin-left: 10px;font-size: 25px; color: white">{{ config('app.name') }}</b>
        <div class="logo-div">
            <img src="{{$logo}}" alt="logo">
        </div>
    </div>
    <div class="corpo">
        <b style="font-size: 25px;">Olá, Vanderson Duarte Maciel</b>
        <p>{{$mensagem}}</p>
    </div>
</body>
</html>
