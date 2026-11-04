<html>
<head>
    <style>
        .section-logo {
            width: 100%;
            height: 90px;
            text-align: center;
        }

        #img-logo {
            width: 100px;
            height: 100px;
        }

        .section-instituicao-nome {
            margin-top: 10px;
            width: 100%;
            text-align: center;
        }

        .section-instituicao-nome h3, .section-instituicao-nome h2, .section-instituicao-nome h5 {
            margin: 0;
        }

        .section-memoria-dados {
            width: 100%;
            margin: 10px auto auto auto;
        }

        legend {
            font-size: 12px;
        }

        .departamento {
            width: 100%;
            margin-top: 50px;
            text-align: left;
            padding: 5px;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            background-color: lightblue;
            height: 50px;
        }

    </style>
    <title></title>
</head>
<body>
<div class="section-logo">
    @if(!empty($instituicao_logo))
        <img id="img-logo"
             src="{{$instituicao_logo}}"/>
    @endif
</div>
<div class="section-instituicao-nome">
    @if(!empty($instituicao_estado))
        <h5>ESTADO {{$instituicao_estado}}</h5>
    @endif
    @if(isset($instituicao_nome))
        <h2>
            {{strtoupper($instituicao_nome)}}
        </h2>
    @endif

    @if(isset($instituicao_telefone))
        <h5>TELEFONE:
            {{$instituicao_telefone}}
        </h5>
    @endif
</div>

<hr style="border:1px solid #000"/>

<table class="section-memoria-dados">
    <tr>
        <td>
            <b>MEMORANDO: </b>
            @if(isset($numero_processo))
                {{$numero_processo}}
            @endif
        </td>
        <td style="text-align: right">
            @if(isset($instituicao_municipio))
                {{$instituicao_municipio}}
            @endif
            @if(isset($instituicao_uf))
                - {{$instituicao_uf}} ,
            @endif
            @if(isset($data_processo))
                {{DBDate::converterDataParaTexto(DBDate::converter($data_processo))}}
            @endif
        </td>
    </tr>
</table>


<div class="departamento">
    @if(isset($departamento_nome))
        <b>DE: </b>{{$departamento_nome}}
    @endif
    <br>
    @if(isset($departamento_transferencia_nome))
        <b>PARA: </b>{{$departamento_transferencia_nome}}
    @endif
</div>

@if(isset($tipo_processo_descricao))
    <h5 style="text-align: left;margin-top:30px">ASSUNTO: {{$tipo_processo_descricao}}</h5>
@endif
<div style="width:100%;margin-top:20px">
    @if(isset($observacao))
        {!! $observacao !!}
    @endif
</div>
</body>
</html>
