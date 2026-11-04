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

        .section-processo-dados {
            width: 500px;
            margin: 50px auto auto auto;

        }

        legend {
            font-size: 12px;
        }

        .dados-requerente {
            width: 100%;
            min-height: 150px;
        }

        .dados-solicitacao {
            width: 100%;
            min-height: 80px;
        }

        .departamento {
            width: 100%;
            height: 30px;
            text-align: center;
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

<table class="section-processo-dados">
    <tr>
        <td>
            <fieldset style="border:1px solid;text-align:center;width:250px">
                <legend style="margin-left:60px;width:150px;">NÚMERO DO PROCESSO</legend>
                @if(isset($numero_processo))
                    {{$numero_processo}}
                @endif
            </fieldset>
        </td>
        <td>
            <fieldset style="border:1px solid; text-align:center;width:250px">
                <legend style="margin-left:95px;width:50px">DATA</legend>
                @if(isset($data_processo))
                    {{$data_processo}}
                @endif
            </fieldset>
        </td>
    </tr>
</table>

@if(isset($tipo_processo_descricao))
    <h3 style="text-align: center">{{$tipo_processo_descricao}}</h3>
@endif

<fieldset class="departamento">
    <legend>DEPARTAMENTO</legend>
    @if(isset($departamento_nome))
        {{$departamento_nome}}
    @endif

</fieldset>
<fieldset class="dados-requerente">
    <legend>DADOS DO TITULAR</legend>
    <table>
        <tr>
            <td style="text-align:right"><b>CGM: </b></td>
            <td>
                @if(isset($titular_cgm))
                    {{$titular_cgm}}
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align:right"><b>NOME / RAZÃO SOCIAL: </b></td>
            <td>
                @if(isset($titular_nome))
                    {{$titular_nome}}
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align:right"><b>CPF/CNPJ: </b></td>
            <td>   @if(isset($titular_cpfcnpj))
                    {{$titular_cpfcnpj}}
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align:right"><b>LOGRADOURO: </b></td>
            <td>@if(isset($titular_logradouro))
                    {{$titular_logradouro}}
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align:right"><b>BAIRRO: </b></td>
            <td>
                @if(isset($titular_bairro))
                    {{$titular_bairro}}
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align:right"><b>MUNICÍPIO: </b></td>
            <td>@if(isset($titular_municipio))
                    {{$titular_municipio}}
                @endif</td>
        </tr>
    </table>
</fieldset>

<fieldset class="dados-solicitacao">
    <legend>DADOS DA SOLICITAÇÃO</legend>
    <table>
        <tr>
            <td style="text-align:right"><b>REQUERENTE: </b></td>
            <td>
                @if(isset($requerente))
                    {{$requerente}}
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align:right"><b>APROVADO POR: </b></td>
            <td>
                @if(isset($nome_usuario_aprovou))
                    {{$nome_usuario_aprovou}}
                @endif
            </td>
        </tr>
    </table>
</fieldset>

<h4 style="text-align:center">OBSERVAÇÕES</h4>
<hr/>
<div style="width:100%">
    @if(isset($observacao))
        {!! $observacao !!}
    @endif
</div>
<hr/>
</body>
</html>
