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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_sql.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));

$oGet  = db_utils::postMemory($_GET);
$iInstituicao = db_getsession("DB_instit");

$tipo = null;
$situacao = null;
$data_lancamento_inicio_ano = '';
$data_lancamento_inicio_mes = '';
$data_lancamento_inicio_dia = '';
$data_lancamento_final_ano  = '';
$data_lancamento_final_mes  = '';
$data_lancamento_final_dia  = '';

?>
<html>
    <head>
        <title>Documento sem t&iacute;tulo</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <?php
        db_app::load('scripts.js');
        db_app::load('prototype.js');
        db_app::load('estilos.css');
        ?>
    </head>
    <body bgcolor=#CCCCCC bgcolor="#CCCCCC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="parent.document.getElementById('processando').style.visibility = 'hidden'">
        <center>
            <?php
            if (isset($oGet->numcgm)) {
                $sInnerCredito = "inner join arrenumcgm on arrenumcgm.k00_numpre = abatimentorecibo.k127_numprerecibo ";
                $sWhereCredito = "and arrenumcgm.k00_numcgm = ".$oGet->numcgm;
            } else if (isset($oGet->matric)) {
                $sInnerCredito = "inner join arrematric on arrematric.k00_numpre = abatimentorecibo.k127_numprerecibo ";
                $sWhereCredito = "and arrematric.k00_matric = ".$oGet->matric;
            } else if (isset($oGet->inscr)) {
                $sInnerCredito = "inner join arreinscr on arreinscr.k00_numpre = abatimentorecibo.k127_numprerecibo ";
                $sWhereCredito = "and arreinscr.k00_inscr = ".$oGet->inscr;
            } else {
                $sInnerCredito = "";
                $sWhereCredito = "and abatimentorecibo.k127_numprerecibo = ".$oGet->numpre;
            }

            $where = '';
            if(!empty($oGet->data_lancamento_inicio)){
                $datafim = empty($oGet->data_lancamento_final) ? date("d/m/Y") : $oGet->data_lancamento_final;
                $where .= " and DATE( k125_datalanc ) between '$oGet->data_lancamento_inicio' and '$datafim' ";
            }
                
            if(!empty($oGet->situacao)){
                $where .= " and status = '$oGet->situacao' ";
            }

            if(!empty($oGet->tipo)){
                if($oGet->tipo == 1){
                    $where .= " and k156_regracompensacao = 7 ";
                }else{
                    $where .= " and k156_regracompensacao is null or k156_regracompensacao != 7";
                }
            }
            $dDataSistema = date('Y-m-d', db_getsession('DB_datausu'));
            $iAnoUsu  = db_getsession("DB_anousu");
            $sSqlCreditosDisponiveis  = " select k125_sequencial,                                                                                                                           \n";
            $sSqlCreditosDisponiveis .= "        k125_valordisponivel as abatimento_valordisponivel,                                                                                        \n";
            $sSqlCreditosDisponiveis .= "        recibo.k00_numpre,                                                                                                                         \n";
            $sSqlCreditosDisponiveis .= "        recibo.k00_receit,                                                                                                                         \n";
            $sSqlCreditosDisponiveis .= "        recibo.k00_hist,                                                                                                                           \n";
            $sSqlCreditosDisponiveis .= "        tabrec.k02_descr,                                                                                                                          \n";
            $sSqlCreditosDisponiveis .= "        histcalc.k01_descr,                                                                                                                        \n";
            $sSqlCreditosDisponiveis .= "        recibo.k00_valor,                                                                                                                                \n";
            $sSqlCreditosDisponiveis .= "        case                                                                                                                                       \n";
            $sSqlCreditosDisponiveis .= "          when (k125_datalanc + ((select coalesce(min(case when k155_tempovalidade = '' then null else k155_tempovalidade end::integer), 99999999) \n";
            $sSqlCreditosDisponiveis .= "                                     from abatimentoregracompensacao                                                                               \n";
            $sSqlCreditosDisponiveis .= "                                    inner join regracompensacao on k155_sequencial = k156_regracompensacao                                         \n";
            $sSqlCreditosDisponiveis .= "                                   where k156_abatimento = abatimento.k125_sequencial)::integer||' days')::interval) >= '{$dDataSistema}'          \n";
            $sSqlCreditosDisponiveis .= "           and k125_valordisponivel > 0                                                                                                            \n";
            $sSqlCreditosDisponiveis .= "          then 'ATIVO'::varchar                                                                                                                    \n";
            $sSqlCreditosDisponiveis .= "                                                                                                                                                   \n";
            $sSqlCreditosDisponiveis .= "          else 'INATIVO'::varchar                                                                                                                  \n";
            $sSqlCreditosDisponiveis .= "        end as status,                                                                                                                             \n";
            $sSqlCreditosDisponiveis .= "        coalesce(
                                                    (select sum(k157_valor) from abatimentoutilizacao where k157_abatimento = abatimento.k125_sequencial), 0
                                                 ) as valor_utilizado,                                                                                                                      \n";
            $sSqlCreditosDisponiveis .= "        coalesce(
                                                    (select k167_data from abatimentocorrecao where k167_abatimento = k125_sequencial order by k167_data desc limit 1), k125_datalanc
                                                 ) as data_correcao,                                                                                                                        \n";
            $sSqlCreditosDisponiveis .= "       coalesce(
                                                  (select sum(k167_valorcorrigido - k167_valorantigo) from abatimentocorrecao where k167_abatimento = abatimento.k125_sequencial), 0
                                                ) as valor_corrigido,                                                                                                                       \n";
            $sSqlCreditosDisponiveis .= "       to_char(k125_datalanc,'DD/MM/YYYY') as k125_datalanc    ,                                                                                    \n";
            $sSqlCreditosDisponiveis .= "coalesce(
                (select count(*) 
                   from abatimentorecibo
                  inner join recibo
                     on k00_numpre = k127_numprerecibo
                  where k127_abatimento = abatimento.k125_sequencial),
                0
            ) as qtd_registros
            ";
            $sSqlCreditosDisponiveis .= "   from abatimentorecibo                                                                                                                           \n";
            $sSqlCreditosDisponiveis .= "        inner join abatimento              on abatimento.k125_sequencial = abatimentorecibo.k127_abatimento                                        \n";
            $sSqlCreditosDisponiveis .= "        inner join recibo                  on recibo.k00_numpre          = abatimentorecibo.k127_numprerecibo                                      \n";
            $sSqlCreditosDisponiveis .= "        inner join arreinstit              on arreinstit.k00_numpre      = recibo.k00_numpre                                                       \n";
            $sSqlCreditosDisponiveis .= "        inner join arretipo                on arretipo.k00_tipo          = recibo.k00_tipo                                                         \n";
            $sSqlCreditosDisponiveis .= "        inner join tabrec                  on tabrec.k02_codigo          = recibo.k00_receit                                                       \n";
            $sSqlCreditosDisponiveis .= "        inner join histcalc                on histcalc.k01_codigo        = recibo.k00_hist                                                         \n";
            $sSqlCreditosDisponiveis .= "        left  join abatimentotransferencia on k158_abatimentoorigem      = k125_sequencial                                                         \n";
            $sSqlCreditosDisponiveis .= "        left  join abatimentoutilizacao    on k157_sequencial            = k158_abatimentoutilizacao                                               \n";
            $sSqlCreditosDisponiveis .= "        {$sInnerCredito}                                                                                                                           \n";
            $sSqlCreditosDisponiveis .= "  where abatimento.k125_tipoabatimento = 3                                                                                                         \n";
            $sSqlCreditosDisponiveis .= "    and arreinstit.k00_instit = {$iInstituicao}                                                                                                    \n";
            $sSqlCreditosDisponiveis .= "        {$sWhereCredito}                                                                                                                           \n";
            $sSqlCreditosDisponiveis .= "  group by k125_sequencial, recibo.k00_numpre, recibo.k00_valor, recibo.k00_receit, recibo.k00_hist, tabrec.k02_descr, histcalc.k01_descr,         \n";
            $sSqlCreditosDisponiveis .= "           k125_datalanc                                                                                                                           \n";
            $sSqlCreditosDisponiveis .= "  order by k125_sequencial desc                                                                                                                    \n";

            $sSqlCreditosDisponiveis  = " select *, fc_corre( k00_receit, data_correcao, k00_valor, current_date, {$iAnoUsu}, data_correcao ) as valor_disponivel,
                                                    fc_corre( k00_receit, data_correcao, abatimento_valordisponivel, current_date, {$iAnoUsu}, data_correcao ) as valor_disponivel_corrigido
                                          from ({$sSqlCreditosDisponiveis}) as creditos;";

            $sSqlCreditosDisponiveis .= "drop table if exists w_abatimento_credito; create temp table w_abatimento_credito as {$sSqlCreditosDisponiveis}                                    \n";
            $sSqlCreditosDisponiveis .= "select k125_sequencial,
                                                abatimento_valordisponivel ,
                                                k00_numpre                 ,
                                                array_to_string(array_agg(k00_receit), ', ') codrecs         ,
                                                k00_hist                   , 
                                                array_to_string(array_agg(trim(k02_descr)), ', ') descr_receitas   , 
                                                k01_descr                  ,
                                                sum(k00_valor) total_recibo,
                                                status                     ,
                                                valor_utilizado            ,
                                                data_correcao              ,
                                                valor_corrigido            ,
                                                k125_datalanc              ,
                                                1 qtd_registros            ,
                                                sum(valor_disponivel) valor_disponivel,
                                                valor_disponivel_corrigido 
                                           from w_abatimento_credito
                                           left join  abatimentoregracompensacao on  k125_sequencial = k156_abatimento
                                           where
                                                1=1
                                                $where
                                       group by k125_sequencial            ,
                                                abatimento_valordisponivel ,
                                                k00_numpre                 ,
                                                k00_hist                   ,
                                                k01_descr                  ,
                                                status                     ,
                                                valor_utilizado            ,
                                                data_correcao              ,
                                                valor_corrigido            ,
                                                k125_datalanc              ,
                                                qtd_registros              ,
                                                valor_disponivel_corrigido"; 
            
            //echo $sSqlCreditosDisponiveis; die;
            $rsCreditosDisponiveis    = db_query($sSqlCreditosDisponiveis);
            $iLinhasCreditos          = pg_num_rows($rsCreditosDisponiveis);

            ?>
            <form action="" method="GET">
                <input type="hidden" name="numcgm" value="<?= $oGet->numcgm ?>">
                <input type="hidden" name="matric" value="<?= $oGet->matric ?>">
                <input type="hidden" name="inscr" value="<?= $oGet->inscr ?>">

                <table style="margin-bottom: 0.5em">
                    <tr>
                        <td>Data de lançamento:</td>
                        <td style="position: relative;">
                            <?= 
                                db_inputdata(
                                    "data_lancamento_inicio",
                                    $data_lancamento_inicio_dia,
                                    $data_lancamento_inicio_mes,
                                    $data_lancamento_inicio_ano,
                                    false,
                                    'text',
                                    1
                                ) 
                            ?>
                        </td>
                        <td> até </td>
                        <td>
                            <?= 
                                db_inputdata(
                                    "data_lancamento_final",
                                    $data_lancamento_final_dia,
                                    $data_lancamento_final_mes,
                                    $data_lancamento_final_ano,
                                    false,
                                    'text',
                                    1
                                )
                            ?>    
                        </td>
                        <td>Tipo:</td>
                        <td>
                            <select name="tipo">
                                <option selected  value="">Todos</option>
                                <option value="1">Manual</option>
                                <option value="2">Automática</option>
                            </select>
                        </td>
                        <td>Situação:</td>
                        <td>
                            <select name="situacao">
                                <option selected  value="">Todos</option>
                                <option value="ATIVO">Ativos</option>
                                <option value="INATIVO">Inativos</option>
                            </select>
                        </td>
                        <td>
                        <input type="submit" value="Pesquisar">
                        </td>
                    </tr>
                </table>
            </form>
            <?php

            if ($iLinhasCreditos > 0) {            
             ?>
                <table border="1" cellspacing="0" cellpadding="3">
                    <tr bgcolor="#FFCC66">
                        <th nowrap>MI                  </th>
                        <th nowrap>Status              </th>
                        <th nowrap>Código              </th>
                        <th nowrap>Origem              </th>
                        <th nowrap>Data Lançamento     </th>
                        <th nowrap>Numpre              </th>
                        <!--
                        <th nowrap>Receita             </th>
                        <th nowrap>Descrição Receita   </th>
                        -->
                        <th nowrap>Histórico           </th>
                        <th nowrap>Descrição Histórico </th>
                        <th nowrap>Valor Original      </th>
                        <th nowrap>Valor Corrigido     </th>
                        <th nowrap>Valor Utilizado     </th>
                        <th nowrap>Valor Disponível    </th>
                        <th nowrap>Valor Disponível Corrigido</th>
                    </tr>
                    <?php
                    $sCor1   = "#EFE029";
                    $sCor2   = "#E4F471";
                    $sCorRow = $sCor1;

                    $totValorCredito    = 0;
                    $totValorCorrigido  = 0;
                    $totValorUtilizado  = 0;
                    $totValorDisponivel = 0;

                    $abatimentos = array();
                    for ( $iInd=0; $iInd < $iLinhasCreditos; $iInd++ ) {
                        $oCredito = db_utils::fieldsMemory($rsCreditosDisponiveis,$iInd);
                        
                        $oCredito->valor_corrigido = ($oCredito->valor_corrigido / $oCredito->qtd_registros);
                                             
                        if ($sCorRow == $sCor1) {
                            $sCorRow = $sCor2;
                        } else {
                            $sCorRow = $sCor1;
                        }

                        if(empty($abatimentos[$oCredito->k00_numpre])) {
                            $abatimentos[$oCredito->k00_numpre]['valordisponivel'] = $oCredito->valor_utilizado;
                        }


                        if (!empty($oCredito->valor_corrigido)) {
                            $nValorCorrigido = ($oCredito->total_recibo + $oCredito->valor_corrigido);                            
                        } else {
                            $nValorCorrigido = $oCredito->valor_disponivel;
                        }

                        if ($abatimentos[$oCredito->k00_numpre]['valordisponivel'] >= $nValorCorrigido) {
                            $abatimentos[$oCredito->k00_numpre]['valordisponivel'] -= $nValorCorrigido ;
                            $oCredito->valor_utilizado  = $nValorCorrigido;
                            $oCredito->valor_disponivel = 0;
                        } elseif ($abatimentos[$oCredito->k00_numpre]['valordisponivel'] > 0) {
                            $oCredito->valor_utilizado  = $abatimentos[$oCredito->k00_numpre]['valordisponivel'];
                            $oCredito->valor_disponivel = $nValorCorrigido - $abatimentos[$oCredito->k00_numpre]['valordisponivel'];
                            $abatimentos[$oCredito->k00_numpre]['valordisponivel'] -= $oCredito->valor_disponivel;
                        }

                        //Verifica origem de crédito do CGM
                        $sqlOrigemCredito = "SELECT m.k00_matric, i.k00_inscr, c.k00_numcgm
                                                FROM arrenumcgm as c
                                                    LEFT JOIN arrematric as m on m.k00_numpre = c.k00_numpre
                                                    LEFT JOIN arreinscr as i on i.k00_numpre = c.k00_numpre
                                                WHERE c.k00_numpre = {$oCredito->k00_numpre}";
                        $rsSqlOrigemCredito = db_query($sqlOrigemCredito);
                        if($rsSqlOrigemCredito) {
                            $iLinhasOrigemCredito = pg_num_rows($rsSqlOrigemCredito);
                            $aCreditos = array();
                            for ( $xInd=0; $xInd < $iLinhasOrigemCredito; $xInd++ ) {
                                $oOrigemCredito = db_utils::fieldsMemory($rsSqlOrigemCredito,$xInd);
                                if(!empty($oOrigemCredito->k00_matric)) {
                                    $aCreditos[] = "M - " . $oOrigemCredito->k00_matric;
                                }
                                if(!empty($oOrigemCredito->k00_inscr)) {
                                    $aCreditos[] = "I - " . $oOrigemCredito->k00_inscr;
                                }
                                if(empty($oOrigemCredito->k00_inscr) && empty($oOrigemCredito->k00_matric)) {
                                    $aCreditos[] = "C - " . $oOrigemCredito->k00_numcgm;
                                }
                            }
                        }
                        $aCreditos = array_unique($aCreditos);
                        $oCredito->origemCredito = implode(" / ", $aCreditos);
                        ?>
                        <tr bgcolor="<?php echo $sCorRow; ?>">
                            <td align="center" nowrap >
                                <?php db_ancora('MI',"js_consultaOrigemCredito({$oCredito->k125_sequencial})",1,''); ?>
                            </td>
                            <td align="center" nowrap ><?php echo $oCredito->status; ?></td>
                            <td align="center" nowrap ><?php echo $oCredito->k125_sequencial; ?></td>
                            <td align="center" nowrap ><?php echo $oCredito->origemCredito ?></td>
                            <td align="center" nowrap ><?php echo $oCredito->k125_datalanc; ?></td>
                            <td align="center" nowrap ><?php echo $oCredito->k00_numpre; ?></td>
                            <!--
                            <td align="center" nowrap ><?php //echo $oCredito->codrecs; ?></td>
                            <td align="center" nowrap ><?php //echo $oCredito->descr_receitas;  ?></td>
                            -->
                            <td align="center" nowrap ><?php echo $oCredito->k00_hist;   ?></td>
                            <td align="center" nowrap ><?php echo $oCredito->k01_descr;  ?></td>
                            <td align="right"  nowrap ><?php echo db_formatar($oCredito->total_recibo,'f'); // valor credito?></td>
                            <td align="right"  nowrap ><?php echo db_formatar($nValorCorrigido,'f'); ?></td>
                            <td align="right"  nowrap ><?php echo db_formatar($oCredito->valor_utilizado,'f'); // valor utilizado ?></td>
                            <td align="right"  nowrap ><?php echo db_formatar($oCredito->abatimento_valordisponivel,'f'); // valor disponivel no abatimento ?></td>
                            <td align="right"  nowrap ><?php echo db_formatar($oCredito->valor_disponivel_corrigido,'f'); // valor abatimento corrigido?></td>
                        </tr>
                        <?php
                        $totValorCredito    += $oCredito->total_recibo;
                        $totValorCorrigido  += $nValorCorrigido;
                        $totValorUtilizado  += $oCredito->valor_utilizado;
                        $totValorDisponivel += $oCredito->abatimento_valordisponivel;
                        $totValorDisponivelCorrigido += $oCredito->valor_disponivel_corrigido;                        
                    }?>
                        <tr bgcolor="#FFCC66">
                            <td align="center" nowrap colspan="8"><b>TOTAL</b></td>
                            <td align="right"  nowrap ><b><?php echo db_formatar($totValorCredito,'f');    // total valor credito?>   </b></td>
                            <td align="right"  nowrap ><b><?php echo db_formatar($totValorCorrigido,'f');  // total valor corrigido?> </b></td>
                            <td align="right"  nowrap ><b><?php echo db_formatar($totValorUtilizado,'f');  // total valor utilizado?> </b></td>
                            <td align="right"  nowrap ><b><?php echo db_formatar($totValorDisponivel,'f'); // total valor disponivel?></b></td>
                            <td align="right"  nowrap ><b><?php echo db_formatar($totValorDisponivelCorrigido,'f'); // total valor disponivel?></b></td>
                        </tr>
                </table>
                <input type="button" name="imprimir" value="Imprimir" onclick="js_imprime()">
                <?php 
            }?>
        </center>
    </body>
</html>
<script>
  function js_consultaOrigemCredito(iAbatimento) {

    var sUrl = 'func_origemabatimento.php?iAbatimento='+iAbatimento;
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_abatimento',sUrl,'Origem Cr\E9dito',true);

  }

  function js_imprime() {
      jandb = window.open('cai3_gerfinanc076.php?<?php
        if(!empty($oGet->matric)){
           echo "matric=$oGet->matric";
        }else if(!empty($oGet->inscr)){
           echo "inscr=$oGet->inscr";
        }else if(!empty($oGet->numcgm)){
           echo "numcgm=$oGet->numcgm";
        }else {
           echo "numpre=$oGet->numpre";
        }

        if(!empty($oGet->data_lancamento_inicio)) {
           echo "&data_lancamento_inicio=".$oGet->data_lancamento_inicio;
           echo "&data_lancamento_final=".$datafim;
        }
        if(!empty($oGet->situacao)) {
            echo "&situacao=".$oGet->situacao;
        }
        if(!empty($oGet->tipo)) {
           echo "&tipo=".$oGet->tipo;
        }

      ?>','','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
      jandb.moveTo(0,0);
   }
</script>
