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
            margin: 5px auto auto auto;
        }

        legend {
            font-size: 12px;
        }

        .dados-requerente {
            width: 100%;
            padding:0;
        }

        .dados-solicitacao {
            width: 100%;
            padding: 3px;

        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            background-color: lightblue;
            height: 50px;
        }

        .field-label {
            font-size: 10px;
        }

        .observacao-div {
            word-wrap: break-word !important;
            word-break: break-all !important;
            overflow-wrap: break-word !important;
            width: 100%;
        }

        table {
            width: 100% !important;
        }
    </style>
    <title></title>
</head>
<body>
<div class="section-logo">
    @if(!empty($instituicao_logo))
        <img id="img-logo"
             src="{{$instituicao_logo}}" alt=""/>
    @endif
</div>
<div class="section-instituicao-nome">
    @if(!empty($instituicao_estado))
        <h5>ESTADO {{$instituicao_estado}}</h5>
    @endif
    @if(isset($instituicao_nome))
        <h3>
            {{strtoupper($instituicao_nome)}}
        </h3>
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
            <fieldset style="border:1px solid;text-align:center;width:250px;padding: 5px;font-size: 11px">
                <legend style="margin-left:60px;width:150px;font-size: 10px">NÚMERO DO PROCESSO</legend>
                @if(isset($numero_processo))
                    {{$numero_processo}}
                @endif
            </fieldset>
        </td>
        <td>
            <fieldset style="border:1px solid; text-align:center;width:250px;padding: 5px;font-size: 11px">
                <legend style="margin-left:80px;width:100px;font-size: 10px">HORA / DATA</legend>
                @if(isset($data_processo) && isset($hora_processo))
                    {{$hora_processo}} {{$data_processo}}
                @endif
            </fieldset>
        </td>
    </tr>
</table>

@if(isset($tipo_processo_descricao))
    <h5 style="text-align: center;margin:0">{{$tipo_processo_descricao}}</h5>
@endif

<fieldset class="dados-requerente">
    <legend>DADOS DO TITULAR</legend>
    <table style="width: 100%;padding: 0">
        <tr>
            <td style="width: 50%">
               <table >
                   <tr>
                       <td style="text-align:right"><b class="field-label">CGM: </b></td>
                       <td class="field-label">
                           @if(isset($titular_cgm))
                               {{$titular_cgm}}
                           @endif
                       </td>
                   </tr>
                   <tr>
                       <td style="text-align:right"><b class="field-label">NOME / RAZÃO SOCIAL: </b></td>
                       <td class="field-label">
                           @if(isset($titular_nome))
                               {{$titular_nome}}
                           @endif
                       </td>
                   </tr>
               </table>
            </td>
            <td style="width: 50%">
              <table style="width: 100%">
                  <tr>
                      <td style="text-align:right"><b class="field-label">LOGRADOURO: </b></td>
                      <td class="field-label">@if(isset($titular_logradouro))
                              {{$titular_logradouro}}
                          @endif
                      </td>
                  </tr>
                  <tr>
                      <td style="text-align:right"><b class="field-label">BAIRRO: </b></td>
                      <td class="field-label">
                          @if(isset($titular_bairro))
                              {{$titular_bairro}}
                          @endif
                      </td>
                  </tr>
                  <tr>
                      <td style="text-align:right"><b class="field-label">MUNICÍPIO: </b></td>
                      <td class="field-label">@if(isset($titular_municipio))
                              {{$titular_municipio}}
                          @endif</td>
                  </tr>
                  <tr>
                      <td style="text-align:right"></td>
                      <td class="field-label"></td>
                  </tr>
              </table>
            </td>
        </tr>
    </table>
</fieldset>

<fieldset class="dados-solicitacao">
    <legend>DADOS DA SOLICITAÇÃO</legend>
    <table>
        <tr>
            <td style="text-align:right"><b class="field-label">REQUERENTE: </b></td>
            <td class="field-label">
                @if(isset($requerente))
                    {{$requerente}}
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align:right"><b class="field-label">ATENDENTE: </b></td>
            <td class="field-label">
                @if(isset($nome_atendente))
                    {{$nome_atendente}}
                @endif
            </td>
        </tr>
    </table>
</fieldset>

<h3 style="text-align:center;margin: 0">DESPACHO </h3>
<hr/>
<div class="observacao-div">
    @if(isset($observacao))
        {!! $observacao !!}
    @endif
</div>
<hr/>
</body>
</html>
