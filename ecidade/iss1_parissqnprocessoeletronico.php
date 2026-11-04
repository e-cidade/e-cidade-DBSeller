<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_usuariosonline.php"));

?>
<html>
    <head>
        <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta http-equiv="Expires" CONTENT="0">
        <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
        <script rel="script" type="text/javascript" src="scripts/prototype.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/classes/http/http.js"></script>
        <link href="estilos.css" rel="stylesheet" type="text/css">
    </head>
    <body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr align="center">
            <td height="430" align="left" valign="top" bgcolor="#CCCCCC">
            <center>
                <form name="form1" method="post" action="">
                    <table style="margin-top: 20px;">
                        <tr>
                            <td>
                                <fieldset>
                                    <legend><b>Parâmetros do Processo de Alvará</b></legend>
                                    <fieldset>
                                        <legend><b>Alvará On-line</b></legend>
                                        <table border="0" >
                                            <tr>
                                                <td><b>Empresa:</b></td>
                                                <td>
                                                  <?= db_input("q150_alvaraempresa",20,'',true,"text",1);?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Mei:</b></td>
                                                <td>
                                                  <?= db_input("q150_alvaramei",20,'',true,"text",1);?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Autonomo:</b></td>
                                                <td>
                                                  <?= db_input("q150_alvaraautonomo",20,'',true,"text",1);?>
                                                </td>
                                            </tr>
                                        </table>
                                    </fieldset>
                                    <fieldset>
                                        <legend><b>Processo Eletrônico</b></legend>
                                        <table border="0" >
                                            <tr>
                                                <td><b>Empresa:</b></td>
                                                <td>
                                                  <?= db_input("q150_alvaraempresa_processoeletronico",20,'',true,"text",1);?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Mei:</b></td>
                                                <td>
                                                  <?= db_input("q150_alvaramei_processoeletronico",20,'',true,"text",1);?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Autonomo:</b></td>
                                                <td>
                                                  <?= db_input("q150_alvaraautonomo_processoeletronico",20,'',true,"text",1);?>
                                                </td>
                                            </tr>
                                        </table>
                                    </fieldset>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <fieldset>
                                    <legend><b>Parâmetros de Tipo de Alvará</b></legend>
                                    <table border="0" >
                                        <tr>
                                            <td><b>Baixo Risco:</b></td>
                                            <td>
                                              <?= db_input("q150_alvarabaixorisco",20,'',true,"text",1);?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Médio Risco:</b></td>
                                            <td>
                                              <?= db_input("q150_alvaramediorisco",20,'',true,"text",1);?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Alto Risco:</b></td>
                                            <td>
                                              <?= db_input("q150_alvaraaltorisco",20,'',true,"text",1);?>
                                            </td>
                                        </tr>
                                    </table>
                                </fieldset>
                            </td>
                        </tr>
                    </table>
                    <input name="salvar" type='button' id="salvar" value="Salvar">
                </form>
            </center>
            </td>
          </tr>
        </table>
    </body>
</html>
<script>
    const
        urlRpc                = 'iss1_parissqnprocessoeletronico.RPC.php',
        inputAlvaraEmpresa    = $('q150_alvaraempresa'),
        inputAlvaraMei        = $('q150_alvaramei'),
        inputAlvaraAutonomo   = $('q150_alvaraautonomo'),
        inputAlvaraBaixoRisco = $('q150_alvarabaixorisco'),
        inputAlvaraMedioRisco = $('q150_alvaramediorisco'),
        inputAlvaraAltoRisco  = $('q150_alvaraaltorisco'),
        inputAlvaraEmpresaProcessoEletronico   = $('q150_alvaraempresa_processoeletronico'),
        inputAlvaraMeiProcessoEletronico       = $('q150_alvaramei_processoeletronico'),
        inputAlvaraAutonomoProcessoEletronico  = $('q150_alvaraautonomo_processoeletronico'),
        btnSalvar             = $('salvar');

    carregaDados();

    salvar.addEventListener('click', () => {
        salvarDados();
    });

    function salvarDados(){
        var
            oParametros = {
                'exec'                  : 'salvar',
                'q150_alvaraempresa'    : inputAlvaraEmpresa.value,
                'q150_alvaramei'        : inputAlvaraMei.value,
                'q150_alvaraautonomo'   : inputAlvaraAutonomo.value,
                'q150_alvarabaixorisco' : inputAlvaraBaixoRisco.value,
                'q150_alvaramediorisco' : inputAlvaraMedioRisco.value,
                'q150_alvaraaltorisco'  : inputAlvaraAltoRisco.value,
                'q150_alvaraempresa_processoeletronico'  : inputAlvaraEmpresaProcessoEletronico.value,
                'q150_alvaramei_processoeletronico'      : inputAlvaraMeiProcessoEletronico.value,
                'q150_alvaraautonomo_processoeletronico' : inputAlvaraAutonomoProcessoEletronico.value
            },
            formData = createFormData(oParametros);

        HttpClient.post(urlRpc, {body: formData}).then(response => {
            console.log(response);
            alert(response.mensagem);
        });
    }

    function carregaDados(){
        var
            oParametros = {
                'exec' : 'carregarDados',
            },
            formData = createFormData(oParametros);

        HttpClient.post(urlRpc, {body: formData}).then(response => {
            if(!!response.dados){
                inputAlvaraEmpresa.value = response.dados.alvaraEmpresa || '';
                inputAlvaraMei.value = response.dados.alvaraMei || '';
                inputAlvaraAutonomo.value = response.dados.alvaraAutonomo || '';
                inputAlvaraBaixoRisco.value = response.dados.alvaraBaixoRisco || '';
                inputAlvaraMedioRisco.value = response.dados.alvaraMedioRisco || '';
                inputAlvaraAltoRisco.value = response.dados.alvaraAltoRisco || '';
                inputAlvaraEmpresaProcessoEletronico.value  = response.dados.alvaraEmpresaProcessoEletronico  || '';
                inputAlvaraMeiProcessoEletronico.value      = response.dados.alvaraMeiProcessoEletronico      || '';
                inputAlvaraAutonomoProcessoEletronico.value = response.dados.alvaraAutonomoProcessoEletronico || '';
            }
        });
    }

    function createFormData(oParametros){
        var formData = new FormData();
        for(parametro in oParametros){
            if(oParametros[parametro] instanceof Array){
                formData.append(`${parametro}[]`, oParametros[parametro]);
            } else {
                formData.append(parametro, oParametros[parametro]);
            }
        }
        return formData;
    }
</script>