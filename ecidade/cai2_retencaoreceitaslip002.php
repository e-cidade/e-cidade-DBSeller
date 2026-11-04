<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2014  DBSeller Servicos de Informatica
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
use ECidade\Pdf\Pdf;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta".".php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("std/db_stdClass.php"));

$oGet = db_utils::postMemory($_GET);

$sWhere = "";
if (isset($oGet->sMovimentos)){
    $sWhere  = " and  (    retencaoempagemov.e27_empagemov in ({$oGet->sMovimentos})
                      or empagemovretencoes.e145_movimento_original in ({$oGet->sMovimentos})
                      or empagemovretencoes.e145_movimento_retencao in ({$oGet->sMovimentos})
                    )";
} else  if (isset($oGet->pagordem)) {
    $sWhere  = " and pagordem.e50_codord = {$oGet->pagordem}";
} else if (isset($oGet->sSlips)) {
    $sWhere  = " and slip.k17_codigo in ({$oGet->sSlips})";
}

$sSqlDados = "select orcorgao.o40_orgao              as orgao,
		             orcorgao.o40_descr              as orgao_descricao,
		             orcunidade.o41_unidade          as unidade,
		             orcunidade.o41_descr            as unidade_descricao,
		             retencaoreceitas.e23_sequencial as retencaoreceitas,
		             retencaotiporec.e21_descricao   as retencaotiporec,
		             cgm_retencao.z01_nome           as retencao_credor,
		             slip.k17_codigo                 as slip,
		             cgm_slip.z01_nome               as slip_credor,
		             slip.k17_valor                  as slip_valor,
		             slip.k17_debito                 as slip_debito,
		             conplano.c60_descr              as descr_conta_debito,
		             slip.k17_credito                as slip_credito,
		             saltes_credito.k13_descr        as descr_conta_credito,
		             pagordem.e50_codord             as op,
		             pagordemele.e53_valor           as op_valor,
		             case when pagordemconta.e49_codord is null then cgm_empenho.z01_nome else cgm_ordem.z01_nome end as op_credor,
		             empagetipo.e83_conta as op_conta_pagadora,
		             empagetipo.e83_descr as op_conta_pagadora_descr,
                     pc63_banco      ,
                     pc63_agencia    ,
                     pc63_conta      ,
                     pc63_agencia_dig,
                     pc63_conta_dig
		        from slip
		             inner join slipnum                     on slipnum.k17_codigo                     = slip.k17_codigo
		             inner join cgm as cgm_slip             on cgm_slip.z01_numcgm                    = slipnum.k17_numcgm
		             inner join saltes as saltes_credito    on saltes_credito.k13_conta               = slip.k17_credito
		             inner join conplanoreduz               on conplanoreduz.c61_reduz                = slip.k17_debito
		                                                   and conplanoreduz.c61_anousu               = extract(year from slip.k17_data)
		                                                   and conplanoreduz.c61_instit               = slip.k17_instit
		             inner join conplano                    on conplano.c60_codcon                    = conplanoreduz.c61_codcon
		                                                   and conplano.c60_anousu                    = conplanoreduz.c61_anousu
		             inner join slipdepartamento            on slipdepartamento.k211_slip             = slip.k17_codigo
		             inner join db_departorg                on db_departorg.db01_coddepto             = slipdepartamento.k211_depart
		                                                   and db_departorg.db01_anousu               = extract(year from k17_data)
		             inner join orcorgao                    on orcorgao.o40_orgao                     = db_departorg.db01_orgao
		                                                   and orcorgao.o40_anousu                    = db_departorg.db01_anousu
		             inner join orcunidade                  on orcunidade.o41_anousu                  = db_departorg.db01_anousu
                                                           and orcunidade.o41_orgao                   = db_departorg.db01_orgao
                                                           and orcunidade.o41_unidade                 = db_departorg.db01_unidade
		             inner join slipretencaoreceitas        on slipretencaoreceitas.k206_slip         = slip.k17_codigo
		             inner join retencaoreceitas            on retencaoreceitas.e23_sequencial        = slipretencaoreceitas.k206_retencaoreceitas
		             inner join retencaopagordem            on retencaopagordem.e20_sequencial        = retencaoreceitas.e23_retencaopagordem
		             inner join pagordem                    on pagordem.e50_codord                    = retencaopagordem.e20_pagordem
		             inner join empempenho                  on empempenho.e60_numemp                  = pagordem.e50_numemp
		             inner join cgm as cgm_empenho          on cgm_empenho.z01_numcgm                 = empempenho.e60_numcgm
		             inner join pagordemele                 on pagordemele.e53_codord                 = pagordem.e50_codord
		              left join pagordemconta               on pagordemconta.e49_codord               = pagordem.e50_codord
		              left join cgm as cgm_ordem            on cgm_ordem.z01_numcgm                   = pagordemconta.e49_numcgm
		             inner join retencaoempagemov           on retencaoempagemov.e27_retencaoreceitas = slipretencaoreceitas.k206_retencaoreceitas
		             inner join retencaotiporec             on retencaotiporec.e21_sequencial         = retencaoreceitas.e23_retencaotiporec
		             inner join retencaotiporeccgm          on retencaotiporeccgm.e48_retencaotiporec = retencaoreceitas.e23_retencaotiporec
		             inner join cgm as cgm_retencao         on cgm_retencao.z01_numcgm                = retencaotiporeccgm.e48_cgm
		             inner join empagepag                   on empagepag.e85_codmov                   = retencaoempagemov.e27_empagemov
		             inner join empagetipo                  on empagetipo.e83_codtipo                 = empagepag.e85_codtipo
		              left join empagemovconta              on empagemovconta.e98_codmov              = retencaoempagemov.e27_empagemov
		             inner join pcfornecon                  on pcfornecon.pc63_contabanco             = empagemovconta.e98_contabanco
		       where retencaoreceitas.e23_ativo = true
		         and retencaoempagemov.e27_principal = true
		         $sWhere
		       order by orcorgao.o40_orgao, orcunidade.o41_unidade, pagordem.e50_codord";
$rsDados = db_query($sSqlDados);
$iQtdRegistros = pg_num_rows($rsDados);
if ($iQtdRegistros == "0") {
    
    $sMsgErro = "Não foi possível localizar ou não existem slips gerados para as retenções";
    db_redireciona("db_erros.php?fechar=true&db_erro={$sMsgErro}");
    exit;
}

$aDados = db_utils::getCollectionByRecord($rsDados);

$oPdf = new Pdf("L");
$oPdf->AliasNbPages();
$oPdf->SetAutoPageBreak(false);
$oPdf->setfillcolor(235);
$oPdf->setfont('arial', 'b', 6);
$oPdf->addTitulo("RELATÓRIO DE SLIPS GERADOS",2);
$oPdf->init(false);

$iAlturalinha = 4;
$iFonte       = 6;

$oPdf->AddPage("L");

$sHashOP = "";
$lCor    = 0;

foreach ($aDados as $oDados) {
    
    if ( $oPdf->GetY() > $oPdf->GetH() - 25 ) {
        $oPdf->SetFont('arial', 'b', 6);
        $oPdf->AddPage("L");
    }
    
    if ($oDados->op != $sHashOP) {
        $oPdf->ln(4);
        imprimirDadosOrdemPagamento($oPdf, $oDados);
        $lCor = 0;
        $sHashOP = $oDados->op;
    }
    
    $oPdf->cell(12,  4, $oDados->retencaoreceitas, 1,  0, "C", $lCor);
    $oPdf->cell(45,  4, $oDados->retencaotiporec,  1,  0, "L", $lCor);
    $oPdf->cell(12,  4, $oDados->slip,             1,  0, "C", $lCor);
    $oPdf->cell(70,  4, $oDados->slip_credor,      1,  0, "L", $lCor);
    $oPdf->cell(60,  4, $oDados->pc63_banco." | ".$oDados->pc63_agencia."-".$oDados->pc63_agencia_dig." | ".$oDados->pc63_conta."-".$oDados->pc63_conta_dig ,     1,  0, "L", $lCor);
    $oPdf->cell(20,  4, db_formatar($oDados->slip_valor,'f'),       1,  0, "R", $lCor);
    $oPdf->cell(61,  4, "$oDados->slip_credito - $oDados->descr_conta_credito",     1,  1, "L", $lCor);
    
    $lCor = ($lCor==0?1:0);
    
}
$oPdf->output();


function imprimirDadosOrdemPagamento($oPdf, $oDados) {
    
    $oPdf->setfont('arial','b',6);
    $oPdf->cell(40, 4, "Orgão/Unidade",             "T", 0, "L", 0);
    $oPdf->cell(240, 4, "{$oDados->orgao}/".str_pad($oDados->unidade, 2, "0", STR_PAD_LEFT)." - {$oDados->unidade_descricao}",      "T", 1, "L", 0);
    
    $oPdf->cell(40, 4, "Ordem de Pagamento",        0, 0, "L", 0);
    $oPdf->cell(60, 4, $oDados->op,                 0, 1, "L", 0);
    
    $oPdf->cell(40, 4, "Valor",                     0, 0, "L", 0);
    $oPdf->cell(60, 4, $oDados->op_valor,           0, 1, "L", 0);
    
    $oPdf->cell(40, 4, "Credor",                    0, 0, "L", 0);
    $oPdf->cell(60, 4, $oDados->op_credor,          0, 1, "L", 0);
    
    $oPdf->cell(40, 4, "Conta Pagadora",            0, 0, "L", 0);
    $oPdf->cell(60, 4, "$oDados->op_conta_pagadora - $oDados->op_conta_pagadora_descr",  0, 1, "L", 0);
    
    $oPdf->cell(280, 4, "Slip's Gerados",         1,  1, "C", 1);
    $oPdf->cell(12,  4, "Cod. Ret.",              1,  0, "C", 1);
    $oPdf->cell(45,  4, "Retenção",               1,  0, "L", 1);
    $oPdf->cell(12,  4, "Slip",                   1,  0, "C", 1);
    $oPdf->cell(70,  4, "Credor",                 1,  0, "L", 1);
    $oPdf->cell(60,  4, "Credor dados bancários", 1,  0, "L", 1);
    $oPdf->cell(20,  4, "Valor",                  1,  0, "C", 1);
    $oPdf->cell(61,  4, "Conta Pagadora",         1,  1, "L", 1);
    
}

