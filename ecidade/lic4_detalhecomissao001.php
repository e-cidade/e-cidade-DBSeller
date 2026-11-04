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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("std/db_stdClass.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

$request = db_utils::postMemory($_GET);
if (empty($request->l20_codigo)) {
    throw new Exception('Código da licitação não fornecido!');
}

$licitacao = new licitacao($request->l20_codigo);
$comissao = $licitacao->getComissao();

$descricaoTipoComissao = $comissao->l30_tipo;
switch ($comissao->l30_tipo) {
    case '1':
        $descricaoTipoComissao = 'Permanente';
        break;
    case '2':
        $descricaoTipoComissao = 'Especial';
        break;
    case '3':
        $descricaoTipoComissao = 'Pregão';
        break;
    case '4':
        $descricaoTipoComissao = 'Servidor Designado';
        break;
    case '5':
        $descricaoTipoComissao = 'Leiloeiro Oficial';
        break;
    case '6':
        $descricaoTipoComissao = 'Agente de Contratação';
        break;
}
?>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <?php
    db_app::load("scripts.js, strings.js, prototype.js, datagrid.widget.js, widgets/windowAux.widget.js");
    db_app::load("estilos.css, grid.style.css,tab.style.css");
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
    <style>
        .tdWidth {
            width: 150px;
        }

        .tdBgColor {
            background-color: #FFFFFF;
            color: #000000;
            width: 100%;
        }
    </style>
</head>

<form name="form1" method="post" action="">
    <table style="margin-top:15px;">
        <tr>
            <td nowrap="nowrap" class="tdWidth" align="left" width="10%">
                <b>Código Comissão:</b>
            </td>
            <td class="tdBgColor"><?= $comissao->l30_codigo; ?></td>

            <td class="tdWidth" nowrap="nowrap">
                <b>Portaria:</b>
            </td>
            <td class="tdBgColor"><?= $comissao->l30_portaria; ?></td>
        </tr>

        <tr>
            <td nowrap="nowrap">
                <b>Validade:</b>
            </td>
            <td class="tdBgColor"><?= db_formatar($comissao->l30_datavalid, 'd') ?></td>

            <td nowrap="nowrap">
                <b>Tipo:</b>
            </td>
            <td class="tdBgColor"><?= $descricaoTipoComissao; ?></td>
        </tr>

        <tr>
            <td colspan="4">
                <fieldset style="margin-top:10px;">
                    <legend><b>Membros Cadastrados</b></legend>
                    <div id='cntGridMembros'></div>
                </fieldset>
            </td>
        </tr>
    </table>
</form>
<script>
    const gridMembrosComissao = new DBGrid("gridMembros");
    gridMembrosComissao.nameInstance = "gridMembrosComissao";
    gridMembrosComissao.setCellWidth(['100px', '100px', '300px', '150px']);
    gridMembrosComissao.setCellAlign(["center", "center", "left", "left"]);
    gridMembrosComissao.setHeader(["Número Cgm", "Documento", "Membro", "Responsabilidade"]);
    gridMembrosComissao.show($('cntGridMembros'));

    function js_consultaMembros() {
        js_divCarregando('Consultando membros da Comissão...', 'msgBox');
        const parametros = {};
        parametros.exec = 'getComissao';
        parametros.licitacao = <?= $licitacao->getCodigo() ?>

        new Ajax.Request('lic4_licitacao.RPC.php', {
                method: 'post',
                parameters: 'json=' + JSON.stringify(parametros),
                onComplete: js_completaGrid
            }
        );
    }

    function js_completaGrid(request) {
        js_removeObj("msgBox");

        const retorno = JSON.parse(request.responseText);
        const aMembros = retorno.comissao.participantes;

        gridMembrosComissao.clearAll(true);
        aMembros.each(function (participante) {
            let linha = [];

            linha[0] = participante.z01_numcgm;
            linha[1] = participante.z01_cgccpf;
            linha[2] = participante.z01_nome;

            let descricaoParticipante = '';
            switch (participante.l31_tipo) {
                case 'P':
                    descricaoParticipante = 'Presidente'
                    break;
                case 'M':
                    descricaoParticipante = 'Membro/Suplente'
                    break;
                case 'G':
                    descricaoParticipante = 'Pregoeiro'
                    break;
                case 'A':
                    descricaoParticipante = 'Equipe de Apoio'
                    break;
                case 'D':
                    descricaoParticipante = 'Servidor Designado'
                    break;
                case 'L':
                    descricaoParticipante = 'Leiloeiro'
                    break;
                case 'S':
                    descricaoParticipante = 'Secretario'
                    break;
                case 'C':
                    descricaoParticipante = 'Agente de Contratação'
                    break;
            }

            linha[3] = descricaoParticipante;
            gridMembrosComissao.addRow(linha);

        });

        gridMembrosComissao.renderRows();
    }

    js_consultaMembros();
</script>
