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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("dbforms/db_classesgenericas.php"));

$clrotulo  = new rotulocampo;
$clrotulo->label('DBtxt23');
$clrotulo->label('DBtxt25');
$clrotulo->label('DBtxt27');
$clrotulo->label('DBtxt28');

db_postmemory($_POST);
?>
<html>

<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>

<body>

    <div class="container">
        <form name="form1">
            <fieldset>
                <legend>Folha Analítica / Sintética (CSV) </legend>

                <table class="form-container">
                    <?php
                    if (!isset($tipo)) {
                        $tipo = "l";
                    }
                    if (!isset($filtro)) {
                        $filtro = "i";
                    }
                    if (!isset($anofolha) || (isset($anofolha) && trim($anofolha) == "")) {
                        $anofolha = DBPessoal::getAnoFolha();
                    }
                    if (!isset($mesfolha) || (isset($mesfolha) && trim($mesfolha) == "")) {
                        $mesfolha = DBPessoal::getMesFolha();
                    }
              
              
                    $geraform = new cl_formulario_rel_pes;
            
                    $geraform->usaregi = true;                // PERMITIR SELEÇÃO DE MATRÍCULAS
                    $geraform->usalota = true;                // PERMITIR SELEÇÃO DE LOTAÇÕES
                    $geraform->usaorga = true;                // PERMITIR SELEÇÃO DE ÓRGÃO
                    $geraform->usaloca = true;                // PERMITIR SELEÇÃO DE LOCAL DE TRABALHO
                    $geraform->re1nome = "regisi";            // NOME DO CAMPO DA MATRÍCULA INICIAL
                    $geraform->re2nome = "regisf";            // NOME DO CAMPO DA MATRÍCULA FINAL
                    $geraform->re3nome = "selreg";            // NOME DO CAMPO DE SELEÇÃO DE MATRÍCULAS
                    $geraform->lo1nome = "lotaci";            // NOME DO CAMPO DA LOTAÇÃO INICIAL
                    $geraform->lo2nome = "lotacf";            // NOME DO CAMPO DA LOTAÇÃO FINAL
                    $geraform->lo3nome = "sellot";            // NOME DO CAMPO DE SELEÇÃO DE LOTAÇÕES
                    $geraform->or1nome = "orgaoi";            // NOME DO CAMPO DO ÓRGÃO INICIAL
                    $geraform->or2nome = "orgaof";            // NOME DO CAMPO DO ÓRGÃO FINAL
                    $geraform->or3nome = "selorg";            // NOME DO CAMPO DE SELEÇÃO DE ÓRGÃOS
                    $geraform->tr1nome = "locali";            // NOME DO CAMPO DO LOCAL INICIAL
                    $geraform->tr2nome = "localf";            // NOME DO CAMPO DO LOCAL FINAL
                    $geraform->tr3nome = "selloc";            // NOME DO CAMPO DE SELEÇÃO DE LOCAIS
                    $geraform->trenome = "tipo";              // NOME DO CAMPO TIPO DE RESUMO
                    $geraform->tfinome = "filtro";            // NOME DO CAMPO TIPO DE FILTRO
                    $geraform->resumopadrao = "l";            // NOME DO DAS LOTAÇÕES SELECIONADAS
                    $geraform->filtropadrao = "i";            // NOME DO DAS LOTAÇÕES SELECIONADAS
                    $geraform->strngtipores = "glomt";        // OPÇÕES PARA MOSTRAR NO TIPO DE RESUMO
                                                              // g - geral,
                                                              // l - lotação,
                                                              // o - órgão,
                                                              // m - matrícula,
                                                              // t - local de trabalho
            
                    $geraform->tipofol = true;            // MOSTRAR DO CAMPO PARA TIPO DE FOLHA
                    $geraform->arr_tipofol = [
                                         "r14"=>"Salário",
                                         "r48"=>"Complementar",
                                         "r20"=>"Rescisão",
                                         "r35"=>"13o. Salário",
                                         "r22"=>"Adiantamento",
                                         "r99"=>"Salário + Rescisão"
                    ];
                    $geraform->complementar = "r48";                // VALUE DA COMPLEMENTAR PARA BUSCAR SEMEST
        
                    $geraform->campo_auxilio_regi = "faixa_regis";  // NOME DO DAS MATRÍCULAS SELECIONADAS
                    $geraform->campo_auxilio_lota = "faixa_lotac";  // NOME DO DAS LOTAÇÕES SELECIONADAS
                    $geraform->campo_auxilio_orga = "faixa_orgao";  // NOME DO DOS ÓRGÃOS SELECIONADOS
                    $geraform->campo_auxilio_loca = "faixa_local";  // NOME DO DOS LOCAIS SELECIONADOS
        
                    $geraform->selecao = true;                      // CAMPO PARA ESCOLHA DA SELEÇÃO
                    $geraform->selregime = true;                    // CAMPO PARA ESCOLHA DO REGIME
        
                    $geraform->onchpad = true;                      // MUDAR AS OPÇÕES AO SELECIONAR
                                                                    // OS TIPOS DE FILTRO OU RESUMO
                    $geraform->gera_form($anofolha, $mesfolha);
                    ?>
                    <tr>
                        <td title="Tipo de impressão">
                            Tipo de impressão:
                        </td>
                        <td>
                            <?php
                                $aTipoFolha = ['a' => 'Analítica','s'=>'Sintética'];
                                db_select("ansin", $aTipoFolha, true, 1, "");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td title="Tipo de impressão">
                            Imprimir Afastados:
                        </td>
                        <td>
                            <?php
                                $aProcessaAfastados = ['s' => 'Sim','n'=>'Não'];
                                db_select("afastado", $aProcessaAfastados, true, 1, "");
                            ?>
                        </td>
                    </tr>

                </table>

            </fieldset>

            <input name="geraCSV" id="geraCSV" type="button" value="Processar" onclick="js_geraCsv();">

        </form>
    </div>
    <?php
    db_menu();
    ?>
</body>

</html>

<script>
var sUrlRPC = "pes2_analiticacsv.RPC.php";

function js_geraCsv() {
    var sFolha = $F("tipofol");
    var sTipo = $F("tipo");
    var iAno = $F("anofolha");
    var iMes = $F("mesfolha");
    var sAnsin = $F("ansin");
    var sAfastado = $F("afastado");
    var sSel = $F("selecao");
    var sReg = $F("regime");
    var sSemest = null;
    var sFaixareg = null;
    var iRegini = null;
    var iRegfim = null;
    var sFaixalot = null;
    var iLotini = null;
    var iLotfim = null;
    var sFaixaloc = null;
    var iLocini = null;
    var iLocfim = null;
    var sFaixaorg = null;
    var iOrgini = null;
    var iOrgfim = null;

    if (document.form1.complementar) {
        sSemest = $F("complementar");
    }
    if (document.form1.selreg) {
        if (document.form1.selreg.length > 0) {
            sFaixareg = js_campo_recebe_valores();
        }

    } else if (document.form1.regisi) {
        iRegini = $F("regisi");
        iRegfim = $F("regisf");
    }

    if (document.form1.sellot) {
        if (document.form1.sellot.length > 0) {
            sFaixalot = js_campo_recebe_valores();
        }
    } else if (document.form1.lotaci) {
        iLotini = document.form1.lotaci.value;
        iLotfim = document.form1.lotacf.value;
    }

    if (document.form1.selloc) {
        if (document.form1.selloc.length > 0) {
            sFaixaloc = js_campo_recebe_valores();
        }
    } else if (document.form1.locali) {
        iLocini = document.form1.locali.value;
        iLocfim = document.form1.localf.value;
    }

    if (document.form1.selorg) {
        if (document.form1.selorg.length > 0) {
            sFaixaorg = js_campo_recebe_valores();
        }
    } else if (document.form1.orgaoi) {
        iOrgini = document.form1.orgaoi.value;
        iOrgfim = document.form1.orgaof.value;
    }

    var oParametros = new Object();
    var msgDiv = "Gerando arquivo CSV \n Aguarde ...";

    oParametros.exec = 'gerarCsv';
    oParametros.sFolha = sFolha;
    oParametros.sTipo = sTipo;
    oParametros.iAno = iAno;
    oParametros.iMes = iMes;
    oParametros.sAnsin = sAnsin;
    oParametros.sAfastado = sAfastado;
    oParametros.sSel = sSel;
    oParametros.sReg = sReg;
    oParametros.sSemest = sSemest;
    oParametros.sFaixareg = sFaixareg;
    oParametros.iRegini = iRegini;
    oParametros.iRegfim = iRegfim;
    oParametros.sFaixalot = sFaixalot;
    oParametros.iLotini = iLotini;
    oParametros.iLotfim = iLotfim;
    oParametros.sFaixaloc = sFaixaloc;
    oParametros.iLocini = iLocini;
    oParametros.iLocfim = iLocfim;
    oParametros.sFaixaorg = sFaixaorg;
    oParametros.iOrgini = iOrgini;
    oParametros.iOrgfim = iOrgfim;

    js_divCarregando(msgDiv, 'msgBox');

    var oAjaxLista = new Ajax.Request(sUrlRPC, {
        method: "post",
        parameters: 'json=' + Object.toJSON(oParametros),
        onComplete: js_retornoCsv
    });
}

function js_retornoCsv(oAjax) {

    js_removeObj('msgBox');
    var oRetorno = JSON.parse(oAjax.responseText);

    // se o retorno do csv "status" for 1, significa que nao ocorreram erros e exibimos a opção de download

    if (oRetorno.status == 1) {

        var listagem = oRetorno.sArquivo + "# Download do Arquivo - relatorioFolhaPagamentoAnalitico.csv";
        js_montarlista(listagem, 'form1');

    } else { // senão  Exibimos o erro ocorriodo na geração do CSV

        alert(oRetorno.message);
        return false;

    }
}
</script>