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

use ECidade\Tributario\Cadastro\Repository\HistoricoCalculoAnualRepository;

  require_once(modification("libs/db_stdlib.php"));
  require_once(modification("libs/db_utils.php"));
  require_once(modification("libs/db_conecta.php"));
  require_once(modification("libs/db_sessoes.php"));
  require_once(modification("libs/db_usuariosonline.php"));
  require_once(modification("dbforms/db_funcoes.php"));
  require_once(modification("libs/db_app.utils.php"));

  db_postmemory($HTTP_GET_VARS);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="ISO-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body>

<?php
    try {
        $historicoCalculoAnualRepository = HistoricoCalculoAnualRepository::getInstance();

        $historicoCalculoAnualRepository->setMatricula($matricula)
                                        ->setExercicio($exercicio);

        $oCalculos = $historicoCalculoAnualRepository->organizaCalculos();

        $cl_cfiptu = new \cl_cfiptu();

        $rCfiptu = $cl_cfiptu->sql_record($cl_cfiptu->sql_query_file($exercicio, "j18_vlrref"));
    
        if (!$rCfiptu) {
            throw new \DBException("Erro ao buscar os dados da cfiptu \n\n Erro: ".preg_last_error());
        }

        $oCfiptu = \db_utils::fieldsMemory($rCfiptu, 0);


    } catch (\DBException $e) {
        db_msgbox($e->getMessage());
        exit;
    }
?>
<script>
    function imprimeHistorico(exercicio, matricula) {
        window.open("cad3_histcalculoanualimp.php?exercicio=" + exercicio + "&matricula=" + matricula, "","fullscreen=1");
    }
</script>

    <table width="95%" border="0" align="center" cellpadding="0" cellspacing="2">
        <?php if (count($oCalculos) > 0) : ?>
            <tr align="center">
                <td colspan="7">
                    <u>Cálculo por Matrícula</u>
                </td>
            </tr>
            <!--
            <tr align="center">
                <td colspan="7">
                    <button onclick='imprimeHistorico(<?php //echo $exercicio ?>, <?php //echo $matricula ?>)'>Imprimir</button>
                </td>
            </tr>
            -->
            <?php foreach($oCalculos as $key => $oCalculoAgrupado) : 
                $j162_areaed = $oCalculoAgrupado["iptucalc"]->j223_areaed;
                $j162_iptucalclog = $oCalculoAgrupado["iptucalc"]->iptucalclog;
                ?>

            <tr align="left">
                <td colspan="7" nowrap>
                    <table width="100%" border="0" cellspacing="2" cellpadding="0" >
                        <tr>
                            <td width="21%" align="left" nowrap bgcolor="#CCCCCC"><div align="right">&nbsp;Testada
                                do C&aacute;lculo</div></td>
                            <td width="23%" align="right">
                                <?=$oCalculoAgrupado["iptucalc"]->j223_testad?>
                            </td>
                            <td width="25%" align="left" nowrap bgcolor="#CCCCCC"><div align="right">Area
                                do lote para calculo</div></td>
                            <td width="31%" align="right">
                                <?=db_formatar($oCalculoAgrupado["iptucalc"]->j223_arealo,'f')?>
                            </td>
                        </tr>
                        <tr>
                            <td align="left" nowrap bgcolor="#CCCCCC"><div align="right">Fração de Cálculo</div></td>
                            <td align="right">
                                <?=$oCalculoAgrupado["iptucalc"]->j223_areafr?>
                            </td>
                            <td align="left" nowrap bgcolor="#CCCCCC"><div align="right">Area edificada</div></td>
                            <td align="right">
                                <?=$oCalculoAgrupado["iptucalc"]->j223_areaed?>
                            </td>
                        </tr>
                        <tr>
                            <td align="left" nowrap bgcolor="#CCCCCC"><div align="right">Valor m2 Terreno</div></td>
                            <td align="right">
                                <?=db_formatar($oCalculoAgrupado["iptucalc"]->j223_m2terr,'f')?>
                            </td>
                            <td align="left" nowrap bgcolor="#CCCCCC"><div align="right">Valor Venal Terreno</div></td>
                            <td align="right">
                                <?=db_formatar($oCalculoAgrupado["iptucalc"]->j223_vlrter,'f')?>
                            </td>
                        </tr>
                        <tr>
                            <td align="left" nowrap bgcolor="#CCCCCC"><div align="right">Aliquota</div></td>
                            <td align="right">
                                <?=db_formatar($oCalculoAgrupado["iptucalc"]->j223_aliq,'f')?>
                            </td>
                            <td align="left" nowrap bgcolor="#CCCCCC"><div align="right">Valor Venal Edific.</div></td>
                            <td align="right">
                                <?=db_formatar($oCalculoAgrupado["iptucalc"]->j162_valor,'f')?>
                            </td>
                        </tr>
                        <tr>
                            <td align="left" nowrap bgcolor="#CCCCCC"><div align="right">Valor Venal:</div></td>
                            <td align="right">
                                <?=db_formatar(($oCalculoAgrupado["iptucalc"]->j223_vlrter+$oCalculoAgrupado["iptucalc"]->j162_valor),'f')?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr align="left">
                <td colspan="7" nowrap>
                    <table width="100%" border="0" cellspacing="2" cellpadding="0" class='tab_cinza'>
                        <tr>
                            <th width="5%"  nowrap bgcolor="#CCCCCC"> Lançamento</th>
                            <th width="9%"  nowrap bgcolor="#CCCCCC"> Receita</th>
                            <th width="20%" nowrap bgcolor="#CCCCCC"> Descrição</th>
                            <th width="5%"  nowrap bgcolor="#CCCCCC"> Histórico</th>
                            <th width="25%" nowrap bgcolor="#CCCCCC"> Descrição</th>
                            <th width="12%" nowrap bgcolor="#CCCCCC"> Valor Calculado</th>
                            <th width="12%" nowrap bgcolor="#CCCCCC"> Valor Isenção</th>
                            <th width="12%" nowrap bgcolor="#CCCCCC"> Saldo a pagar</th>
                        </tr>

                        <?php
                            $soma     = 0;
                            $somacalc = 0;
                            $somaisen = 0;

                            $iptucalclog = $oCalculoAgrupado["iptucalclog"];
                            $aCalculoIptucale = $oCalculoAgrupado["iptucale"];

                            unset($oCalculoAgrupado["iptucalclog"]);
                            unset($oCalculoAgrupado["iptucalc"]);
                            unset($oCalculoAgrupado["iptucale"]);
                            unset($oCalculoAgrupado["areaEdTaxa"]);
                            unset($oCalculoAgrupado["aliqTaxa"]);
                            $gerafinanc = $oCalculoAgrupado[0]->gerafinanc;
                            $habilitaGeraFinac = false;
                            $oDaocfiptu = new cl_cfiptu;
                            $sqlcfiptu = $oDaocfiptu->sql_query_file(db_getsession("DB_anousu"),'j18_taxaseparada, j18_simulacaocalculo');
                            $rs = pg_fetch_object(db_query($sqlcfiptu));

                            if($rs->j18_taxaseparada == 0 && $rs->j18_simulacaocalculo == 't'){
                            $habilitaGeraFinac = true;
                            }
                            foreach($oCalculoAgrupado as $oCalculo) :

                                $soma     = $soma     + ($oCalculo->j157_valor - abs($oCalculo->j157_valorisen));
                                $somacalc = $somacalc + $oCalculo->j157_valor;
                                $somaisen = $somaisen + $oCalculo->j157_valorisen;
                        ?>
                            <tr>
                                <td width="9%" nowrap align="center"><?=$oCalculo->numpre?></td>
                                <td width="9%" nowrap align="center"><?=$oCalculo->k02_codigo?></td>
                                <td width="21%" nowrap bgcolor="#FFFFFF"><?=substr($oCalculo->k02_descr,0,20)?></td>
                                <td width="5%" nowrap align="center"><?=$oCalculo->j17_codhis?></td>
                                <td width="30%" nowrap bgcolor="#FFFFFF"><?= substr($oCalculo->j17_descr, 0, 30) ?></td>
                                <td width="6%"  align="right" nowrap bgcolor="#FFFFFF"><?=db_formatar($oCalculo->valor,'f')?></td>
                                <td width="6%"  align="right" nowrap bgcolor="#FFFFFF"><?=db_formatar($oCalculo->valorisen,'f')?></td>
                                <td width="16%" align="right" nowrap bgcolor="#FFFFFF"><?=db_formatar(($oCalculo->valor - abs($oCalculo->valorisen)),'f')?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr align="left">
                            <th align="right" nowrap bgcolor="#CCCCCC"><?= $habilitaGeraFinac ? 'Calcular Financeiro' : ''; ?></th>
                            <th align="center" nowrap bgcolor="#CCCCCC"><?= $habilitaGeraFinac ? $gerafinanc : ''; ?></th>
                            <th colspan="3" align="right" nowrap bgcolor="#CCCCCC">TOTAL :</th>
                            <td width="12%" align="right" nowrap bgcolor="#CCCCCC"><strong><?=db_formatar($somacalc,'f')?></strong></td>
                            <td width="12%" align="right" nowrap bgcolor="#CCCCCC"><strong><?=db_formatar($somaisen,'f')?></strong></td>
                            <td width="12%" align="right" nowrap bgcolor="#CCCCCC"><strong><?=db_formatar($soma,'f')?></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr align="left">
                <td colspan="7">&nbsp;</td>
            </tr>
            <tr align="left">
                <td colspan="7">&nbsp;</td>
            </tr>

            <?php if(!empty($aCalculoIptucale)) : ?>
                <?php
                    //Verifica se o plugin de calculo proporcional esta ativo.
                    $sqlPluginAtivo = "SELECT *
                                         FROM db_plugin
                                        WHERE db145_nome = 'calculo-de-iptu-proporcional';";

                    $resultPluginAtivo = db_query($sqlPluginAtivo);
                ?>
                <tr align="center">
                    <td colspan="6" align="center" bgcolor="#CCCCCC">C&aacute;lculos para cada edifica&ccedil;&atilde;o&nbsp; &nbsp; &nbsp;</td>
                </tr>
                <tr border="1" align="center">
                    <td colspan="6" align="center" nowrap bgcolor="#FFFFFF">
                        <table width="100%" border="0" cellspacing="0">
                            <tr>
                                <td width="4%">N&deg;</td>
                                <td width="16%" align="center">&Aacute;rea</td>
                                <td width="12%" align="center">Exerc&iacute;cio</td>
                                <td width="19%" align="center">Valor m&sup2;</td>
                                <td width="15%" align="center">Pontua&ccedil;&atilde;o</td>
                                <td width="34%" align="right">Valor Venal</td>
                            </tr>
                            <?php
                            foreach($aCalculoIptucale as $oCalculoIptucale) :
                                $areaExibida = $oCalculoIptucale->j162_areaed;

                                /*
                                * Este if, serve para quando o plugin
                                * iptu calculo proporcional estiver instalado
                                * levar em consideração a area filtrado por vigencias
                                * respectiva salva na tabela de historico que é adicionada pelo mesmo.
                                */

                                if(pg_numrows($resultPluginAtivo) > 0) {
                                    $sql = "SELECT SUM(area) AS area
                                              FROM plugins.iptuconstrareahistorico
                                             WHERE plugins.iptuconstrareahistorico.matricula = {$matricula}
                                               AND plugins.iptuconstrareahistorico.datainicio <= ({$oCalculoIptucale->j162_anousu}||'-'||'01'||'-01')::date
                                               AND ({$oCalculoIptucale->j162_anousu}||'-'||'01'||'-01')::date <= plugins.iptuconstrareahistorico.data
                                               AND id_constr = {$oCalculoIptucale->j162_idcons}";

                                    $result = db_query($sql);
                                    $area = \db_utils::fieldsMemory($result, 0)->area;
                                    $areaExibida = $area ? $area : $areaExibida;
                                }
                            ?>
                                <tr>
                                    <td><?= $oCalculoIptucale->j162_idcons ?></td>
                                    <td align="center"><?=db_formatar($areaExibida,'f')?></td>
                                    <td align="center"><?=$oCalculoIptucale->j162_anousu?></td>
                                    <td align="center"><?=$oCalculoIptucale->j162_vm2?></td>
                                    <td align="center"><?=$oCalculoIptucale->j162_pontos?></td>
                                    <td align="right"><?=trim(db_formatar($oCalculoIptucale->j162_valor, "f"))?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </td>
                </tr>
            <?php endif; ?>

            <?php if ($iptucalclog->j27_codigo != 0) : ?>
                <tr>
                    <td style="width: 400px;">
                        Código: <?= $iptucalclog->j27_codigo ?>
                    </td>
                    <td>
                        Tipo: <?= ($iptucalclog->j27_parcial == "t" ? "PARCIAL" : "GERAL") ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        Data: <?= db_formatar($iptucalclog->j27_data,'d') ?>
                    </td>
                    <td>
                        Hora: <?= $iptucalclog->j27_hora ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        Resultado: <?= $iptucalclog->j28_tipologcalc ?> - <?= $iptucalclog->j62_descr ?>
                    </td>
                    <td>
                        Tipo: <?= ($iptucalclog->j62_erro == "t" ? "ERRO" : "NORMAL") ?>
                    </td>
                </tr>
                <?php if ($iptucalclog->j27_parcial == "t" AND !empty($iptucalclog->j27_observacao)) : ?>
                    <tr>
                        <td>
                            Observação: <?= $iptucalclog->j27_observacao ?>
                        </td>
                        <td>
                            &nbsp;
                        </td>
                    </tr>
                <?php endif; ?>

                <tr>
                    <td>
                        Usuário: <?= $iptucalclog->login ?> - <?= $iptucalclog->nome ?>
                    </td>
                    <td>
                        &nbsp;
                    </td>
                </tr>
            <?php endif; ?>
            <tr align="left">
                <td colspan="7">&nbsp;</td>
            </tr>
            <tr align="left">
                <td colspan="7">&nbsp;</td>
            </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr align="center">
                <td colspan="7"><u>Sem histórico</u></td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>
