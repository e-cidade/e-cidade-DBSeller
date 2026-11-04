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

use ECidade\Financeiro\Empenho\Mapper\TiposNotasParaiba;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_empnota_classe.php"));
require_once(modification("classes/db_empnotaitem_classe.php"));

$oGet = db_utils::postMemory($_GET);
$oDaoEmpNota = new cl_empnota();
$oDaoEmpNotaItem = new cl_empnotaitem();

$clrotulo = new rotulocampo;
$clrotulo->label("e69_numero");
$clrotulo->label("e69_codnota");
$clrotulo->label("e50_codord");
$clrotulo->label("e60_codemp");
$clrotulo->label("z01_nome");
$clrotulo->label("e70_valor");
$clrotulo->label("e70_vlrliq");
$clrotulo->label("e70_vlranu");
$clrotulo->label("e53_vlrpag");
if (isset($oGet->e69_codnota)) {
    $sSqlNota = "
    select e69_codnota,
           z01_nome,
           e69_dtnota,
           e69_numero,
           e60_codemp||'/'||e60_anousu as codemp,
           e70_valor,
           e70_vlrliq,
           e70_vlranu,
           e50_codord,
           e53_vlrpag,
           e69_outrosdados,
           e172_dados
      from empnota
            inner join empempenho on e69_numemp = e60_numemp
            inner join cgm on e60_numcgm = z01_numcgm
            inner join empnotaele on e69_codnota = e70_codnota
            left  join pagordemnota on e71_codnota = e69_codnota
                                   and e71_anulado is false
            left  join pagordem    on  e71_codord = e50_codord
            left join pagordemoutrosdados on e172_pagordem = e50_codord
            left  join pagordemele  on e53_codord = e50_codord
    where e69_codnota = {$oGet->e69_codnota}
  ";

    $tipoNota = '';
    $numeroSerie = '';
    $chave = '';

    $rsNota = $oDaoEmpNota->sql_record($sSqlNota);
    if ($oDaoEmpNota->numrows > 0) {
        $oNotas = db_utils::FieldsMemory($rsNota, 0);
        $e69_codnota = $oNotas->e69_codnota;
        $e69_numero = $oNotas->e69_numero;
        $codemp = $oNotas->codemp;
        $z01_nome = $oNotas->z01_nome;
        $e70_valor = $oNotas->e70_valor;
        $e70_vlrliq = $oNotas->e70_vlrliq;
        $e70_vlranu = $oNotas->e70_vlranu;
        $e53_vlrpag = $oNotas->e53_vlrpag;
        $e50_codord = $oNotas->e50_codord;

        if (isParaiba()) {
            if (!empty($oNotas->e69_outrosdados)) {
                $outrosDados = json_decode($oNotas->e69_outrosdados);
                $tipo = (new TiposNotasParaiba())->getTipoByID($outrosDados->tipo_nota);
                $tipoNota = sprintf('%s - %s', $tipo['id'], $tipo['label']);
                $numeroSerie = $outrosDados->serie_nota;
                $chave = $outrosDados->chave_nota;
            }
            if (!empty($oNotas->e172_dados)) {
                $outrosDadosPagOrdem = json_decode($oNotas->e172_dados);
                $codigoAgrupamento = $outrosDadosPagOrdem->codigo_agrupamento;
            }
        }
    }

    // Diarias
    $sqlDiarias = "
    select 
    e446_datainicio, e446_quantidade, e446_datafim, e446_tipodiaria, 
    e446_estadodestino, e446_destino, e446_paisdestino, e446_motivo, 
    e446_regist, cgm_pessoal.z01_nome
    from emppresta
    inner join empempenho  on  empempenho.e60_numemp = emppresta.e45_numemp
    inner join empprestatip  on  empprestatip.e44_tipo = emppresta.e45_tipo
    inner join cgm  on  cgm.z01_numcgm = empempenho.e60_numcgm
    inner join empprestaitem ON empprestaitem.e46_emppresta = emppresta.e45_sequencial
    inner join empprestaitemdiaria on empprestaitemdiaria.e446_empprestaitem = empprestaitem.e46_codigo
    inner join empnota on empnota.e69_numemp = empempenho.e60_numemp
    inner join rhpessoal on rhpessoal.rh01_regist = empprestaitemdiaria.e446_regist
    inner join cgm cgm_pessoal on cgm_pessoal.z01_numcgm = rhpessoal.rh01_numcgm
    where empnota.e69_codnota = {$oGet->e69_codnota}
    ";

    $estiloExibicaoInfoDiarias = "display: none";
    $rsDiarias = db_query($sqlDiarias);
    $linhasResult = pg_num_rows($rsDiarias);
    if ($linhasResult > 0) {
        $oDiaria = db_utils::FieldsMemory($rsDiarias, 0);
    }

    if ($linhasResult > 0 && isRioDeJaneiro() && db_getsession("DB_anousu") >= 2024) {
        $estiloExibicaoInfoDiarias = "";
    }

    if (isRioDeJaneiro()) {
        $oDaoEmpnotaatestador = new cl_empnotaatestador;
        $sCamposAtestador = "z01_nome, z01_numcgm";
        $sWhereAtestador = "e169_empnota = {$e69_codnota}";
        $sSqlAtestador = $oDaoEmpnotaatestador->sql_query(null, $sCamposAtestador, 'z01_nome', $sWhereAtestador);
        $rsAtestador = db_query($sSqlAtestador);

        if ($rsAtestador && pg_num_rows($rsAtestador) > 0) {
            $oAtestadores = db_utils::getCollectionByRecord($rsAtestador);
        }
    }
}
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
</head>
<body bgcolor="#CCCCCC">
<fieldset>
    <legend><b>Dados da nota</b></legend>
    <table>
        <tr>
            <td>
                <b>Código da Nota:</b>
            </td>
            <td>
                <?php
                db_input('e69_codnota', 13, $Ie69_codnota, true, 'text', 3);
                ?>
            </td>
            <td>
                <b>Número:</b>
            </td>
            <td>
                <?php
                db_input('e69_numero', 13, $Ie69_numero, true, 'text', 3);
                ?>
            </td>
            <td>
                <b><?php echo @$Le60_codemp; ?></b>
            </td>
            <td>
                <?php
                db_input('codemp', 13, $Ie60_codemp, true, 'text', 3);
                ?>
            </td>
            <td>
                <b>Nota de Liquidação:</b>
            </td>
            <td>
                <?php
                db_input('e50_codord', 13, $Ie50_codord, true, 'text', 3);
                ?>
            </td>
        </tr>
        <?php if (isParaiba()) : ?>
        <tr>
            <td>
                <b>Tipo:</b>
            </td>
            <td colspan="3" title="<?=$tipoNota?>">
                <input type="text" class="readonly field-size-max" readonly value="<?=$tipoNota?>">
            </td>
            <td>
                <b>Chave:</b>
            </td>
            <td colspan="2" title="<?=$chave?>">
                <input type="text" class="readonly field-size-max" readonly value="<?=$chave?>">
            </td>
            <td>
                <b>Série:</b>
            </td>
            <td title="<?=$numeroSerie?>">
                <input type="text" class="readonly field-size-max" readonly value="<?=$numeroSerie?>">
            </td>
        </tr>
            <?php if (isset($codigoAgrupamento)) : ?>
        <tr>
            <td>
                <b>Codigo Agrupamento:</b>
            </td>
            <td title="<?=$codigoAgrupamento?>">
                <input type="text" class="readonly field-size-max" readonly value="<?=$codigoAgrupamento?>">
            </td>
        </tr>
            <?php endif ?>
        <?php endif ?>
        <tr>
            <td>
                <b><?= $Lz01_nome ?></b>
            </td>
            <td colspan='8'>
                <?php
                db_input('z01_nome', 70, $Lz01_nome, true, 'text', 3);
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <b>Valor:</b>
            </td>
            <td>
                <?php
                db_input('e70_valor', 13, $Ie70_valor, true, 'text', 3);
                ?>
            </td>
            <td>
                <b>Valor Liquidado:</b>
            </td>
            <td>
                <?php
                db_input('e70_vlrliq', 13, $Ie70_vlrliq, true, 'text', 3);
                ?>
            </td>
            <td>
                <b>Valor Anulado: </b>
            </td>
            <td>
                <?php
                db_input('e70_vlranu', 13, $Ie70_vlranu, true, 'text', 3);
                ?>
            </td>
            <td>
                <b>Valor Pago:</b>
            </td>
            <td>
                <?php
                db_input('e53_vlrpag', 13, $Ie53_vlrpag, true, 'text', 3);
                ?>
            </td>
        </tr>
    </table>
</fieldset>
<fieldset style="<?=$estiloExibicaoInfoDiarias?>">
    <legend>
        <b>Diárias</b>
    </legend>
    <table>
        <tr>
            <td>
                <b>Matrícula:</b>
            </td>
            <td>
                <input type="text" name="diariaMatricula" class="readonly field-size-max" 
                readonly value="<?=$oDiaria->e446_regist?>"
                style="background-color:#DEB887;" size="13">
            </td>
            <td>
                <b>Nome:</b>
            </td>
            <td colspan="3">
                <input type="text" name="diariaNome" class="readonly field-size-max" 
                readonly value="<?=$oDiaria->z01_nome?>"
                style="background-color:#DEB887;" size="13">
            </td>
        </tr>
        <tr>
            <td>
                <b>Data de Saída:</b>
            </td>
            <td>
                <input type="text" name="diariaDataInicio" class="readonly field-size-max" 
                readonly value="<?=db_formatar($oDiaria->e446_datainicio, "d")?>"
                style="background-color:#DEB887;" size="13">
            </td>
            <td>
                <b>Qtde:</b>
            </td>
            <td>
                <input type="text" name="diariaQtde" class="readonly field-size-max" 
                readonly value="<?=$oDiaria->e446_quantidade?>"
                style="background-color:#DEB887;" size="13">
            </td>
            <td>
                <b>Data de Retorno:</b>
            </td>
            <td>
                <input type="text" name="diariaDataFim" class="readonly field-size-max" 
                readonly value="<?=db_formatar($oDiaria->e446_datafim, "d")?>"
                style="background-color:#DEB887;" size="13">
            </td>
        </tr>
        <tr>
            <td>
                <b>Destino:</b>
            </td>
            <td>
                <input type="text" name="diariaDestino" class="readonly field-size-max" 
                readonly value="<?=$oDiaria->e446_tipodiaria?>"
                style="background-color:#DEB887;" size="13">
            </td>
            <td>
                <b>Estado de Destino:</b>
            </td>
            <td colspan="3">
                <input type="text" name="diariaEstado" class="readonly field-size-max" 
                readonly value="<?=$oDiaria->e446_estadodestino?>"
                style="background-color:#DEB887;" size="13">
            </td>            
        </tr>
        <tr>
            <td>
                <b>Cidade de Destino:</b>
            </td>
            <td colspan="3">
                <input type="text" name="diariaCidade" class="readonly field-size-max" 
                readonly value="<?=$oDiaria->e446_destino?>"
                style="background-color:#DEB887;" size="13">
            </td>
            <td>
                <b>País de Destino:</b>
            </td>
            <td>
                <input type="text" name="diariaPais" class="readonly field-size-max" 
                readonly value="<?=$oDiaria->e446_paisdestino?>"
                style="background-color:#DEB887;" size="13">
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top;"><b>Objeto da Diária:</b></td>
            <td colspan="7">
                <textarea 
                    rows="8" 
                    cols="30" 
                    style="background-color:#DEB887;width:100%" 
                    name="diariaObjeto"
                    readonly><?=$oDiaria->e446_motivo?></textarea>
            </td>
        </tr>                                                                                                                                                                                                                                                                                                                       
    </table>
</fieldset>

<?php if (isset($oAtestadores)) : ?>
    <fieldset>
        <legend>Responsáveis pelo Atesto</legend>
        <table>
            <tr style="font-weight: bold;">
                <td>Número cgm</td>
                <td>Nome</td>
            </tr>
            <?php foreach ($oAtestadores as $atestador) : ?>
                <tr>
                    <td>
                        <input type="text" value=" <?= $atestador->z01_numcgm ?>" disabled size="13"/>
                    </td>
                    <td>
                        <input type="text" value=" <?= $atestador->z01_nome ?>" disabled size="50"/>
                    </td>
                </tr>
            <?php endforeach ?>
        </table>
    </fieldset>
<?php endif ?>

<fieldset>
    <legend>
        <b>Itens da Nota</b>
    </legend>
    <table>
        <form1 method='post' name='itens'>
            <?php
            if ($oDaoEmpNota->numrows > 0) {
                $sWhere = "e72_codnota = {$oNotas->e69_codnota}";
                $sCampos = "e62_sequen, pc01_descrmater, e72_valor, e72_qtd,e72_vlrliq,e72_vlranu";
                $sSqltensNota = $oDaoEmpNotaItem->sql_query(null, $sCampos, "e62_sequen", $sWhere);
                db_lovrot($sSqltensNota, 15, '', '', "", "", "Itens");
            }
            ?>
    </table>
    </form>
    <form name='form2' method='post'>
</fieldset>
<?php

if ($oNotas->e50_codord != null) {
    $dao = new cl_retencaoreceitas;

    $campos = "
      e21_sequencial,
      e21_descricao,
      e23_dtcalculo,
      e23_valor,
      e23_valorbase,
      e23_deducao,
      e23_valorretencao,
      e23_aliquota,
      e23_sequencial,
      case when e23_recolhido is true then 'Sim'  else 'Não' end as e23_recolhido,
      (select max(k105_data)
         from retencaocorgrupocorrente
              join corgrupocorrente on e47_corgrupocorrente = k105_sequencial
        where e47_retencaoreceita = e23_sequencial
          and e23_recolhido is true
      ) as k105_data,
      (select array_to_string(array_accum(k12_numpre), ' / ')
         from retencaocorgrupocorrente
              join corgrupocorrente on e47_corgrupocorrente = k105_sequencial
                   and k105_corgrupotipo  = 3
              join cornump on k105_id = k12_id
                   and k105_autent = k12_autent
                   and k105_data = k12_data
            where e47_retencaoreceita = e23_sequencial and e23_recolhido is true
      ) as k12_numpre,
      e81_cancelado,
      (select k108_slip
         from empagemovslips
         join slipempagemovslips on k108_empagemovslips = k107_sequencial
        where k107_empagemov = e81_codmov limit 1
      ) as slips
    ";
    $dbwhere = "
        e20_pagordem = {$oNotas->e50_codord} and e27_principal is true and (e23_ativo is true or e23_recolhido is true)
    ";
    $sSqlRetencoes = $dao->sql_query_consulta(null, $campos, "e21_sequencial, e23_sequencial", $dbwhere);
    $rsRetencoes = $dao->sql_record($sSqlRetencoes);

    $iNumRows = $dao->numrows;
    if ($iNumRows > 0) {
        $aRetencoes = db_utils::getCollectionByRecord($rsRetencoes, true);

        echo "<fieldset>";
        echo "  <legend><b>Retenções</b></legend>";
        echo "<table style='border: 2px inset white;' cellspacing='0'>";
        echo "  <tr>";
        echo "    <th class='table_header'>Código</th>";
        echo "    <th class='table_header'>Retenção</th>";
        echo "    <th class='table_header'>Data do Cálculo</th>";
        echo "    <th class='table_header'>Base de Calculo</th>";
        echo "    <th class='table_header'>Dedução</th>";
        echo "    <th class='table_header'>Valor</th>";
        echo "    <th class='table_header'>Aliquota</th>";
        echo "    <th class='table_header'>Recolhido</th>";
        echo "    <th class='table_header'>Data Autent.</th>";
        echo "    <th class='table_header'>Cod. Arrec</th>";
        echo "    <th class='table_header'>Situação</th>";
        echo "    <th class='table_header'>Slip</th>";
        echo "    <th class='table_header'>Ações</th>";
        echo "    <th class='table_header' width='17px'>&nbsp;</th>";
        echo "  </tr>";
        echo "<tbody style='height:150px;width:100%;overflow:scroll;overflow-x:hidden;background-color:white'>";
        foreach ($aRetencoes as $oRetencao) {
            $situacao = "Lançado";
            $classeLinha = '';
            if (!empty($oRetencao->k105_data)) {
                $situacao = "Apropriado / Pago";
                $classeLinha = 'alert-success';
            }

            if (!empty($oRetencao->e81_cancelado)) {
                $situacao = "Estornado";
                $classeLinha = 'alert-danger';
            }

            echo "<tr style='height:1em' class='{$classeLinha}'>";
            echo "  <td class='linhagrid' style='text-align:right'>{$oRetencao->e21_sequencial}</td>\n";
            echo "  <td class='linhagrid' style='text-align:left'>{$oRetencao->e21_descricao}</td>\n";
            echo "  <td class='linhagrid' style='text-align:center'>{$oRetencao->e23_dtcalculo}</td>\n";
            echo "  <td class='linhagrid' style='text-align:right'>" . db_formatar($oRetencao->e23_valorbase, "f") . "</td>\n";
            echo "  <td class='linhagrid' style='text-align:right'>" . db_formatar($oRetencao->e23_deducao, "f") . "</td>\n";
            echo "  <td class='linhagrid' style='text-align:right'>" . db_formatar($oRetencao->e23_valorretencao, "f") . "</td>\n";
            echo "  <td class='linhagrid' style='text-align:right'>{$oRetencao->e23_aliquota}%</td>\n";
            echo "  <td class='linhagrid' style='text-align:left'>{$oRetencao->e23_recolhido}</td>\n";
            echo "  <td class='linhagrid' style='text-align:center'>{$oRetencao->k105_data}&nbsp;</td>\n";
            echo "  <td class='linhagrid' style='text-align:center'>{$oRetencao->k12_numpre}&nbsp;</td>\n";
            echo "  <td class='linhagrid' style='text-align:center'>{$situacao}&nbsp;</td>\n";
            echo "  <td class='linhagrid' style='text-align:center'>{$oRetencao->slips}&nbsp;</td>\n";
            echo "  <td class='linhagrid' style='text-align:center'><input type='button' value='Detalhamento' onclick='js_detalhamento({$oRetencao->e23_sequencial})'>&nbsp;</td>\n";
            echo "  <td >&nbsp;</td>\n";
            echo "</tr>";
        }
        echo "<tr style='height:auto'><td>&nbsp;</td></tr>";
        echo "</tbody>";
        echo "</table>";
        echo "</fieldset>";
    }
}
?>
</form>
<script type="text/javascript" src="scripts/session.js"></script>
<script>
var urlApi;
window.addEventListener('load', () => {
    PHPSession.loadData().then(() => {
        urlApi = PHPSession.requestApi;
    });
});
function js_visualizarNota(codigosEstorage) {
    js_OpenJanelaIframe(
        'CurrentWindow.corpo',
        'db_visualizador_imagens',
        `db_visualizador_documentos.php?ids=${codigosEstorage}`,
        'Visualizador de documentos',
        true
    );
}

function js_detalhamento(seq){

    let sQuery = "";
    sQuery += "e21_sequencial="+seq;
    js_OpenJanelaIframe('','db_iframe_detalhamento',
        'emp2_detalhamento.php?'+sQuery,
        'Detalhamento',true);
}
</script>
</body>
</html>
