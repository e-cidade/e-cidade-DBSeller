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
use ECidade\Pdf\Pdf;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_sql.php"));
require_once(modification("libs/db_utils.php"));


$clAlunoCadeirante = new cl_necessidadealunocadeirante;
$clAlunoBPC = new cl_necessidadealunobpc;

$oGet = db_utils::postMemory($_GET);

$head1 = "RELATÓRIO ALUNOS PcD / ALTAS HABILIDADES";
$head2 = "Escola: TODAS";
$head3 = "Ano: {$oGet->iAno}";
$head4 = "Etapa: TODAS";
$head5 = "NECESSIDADE: TODAS";
$head6 = "SUBDIVISÃO: TODAS";
$head7 = "CADEIRANTE: NÃO";
$head8 = "BPC: NÃO";
$head9 = "TIPO ATENDIMENTO: TODOS";

/**
 * Array com os tipos de Apoio para necessidade especial
 */
$aApoio = array(""=>"",
                "1"=>"SEM APOIO PEDAGÓGICO",
                "2"=>"COM APOIO PEDAGÓGICO",
                "3"=>"COM APOIO PEDAGÓGICO (OUTRO ESTABELECIMENTO)"
               );

/**
 * Array com os tipos de diagnóstico necessidade especial
 */
$aTipo  = array(""=>"",
                "1"=>"SEM DIAGNÓSTICO",
                "2"=>"FICHA DE AVALIAÇÃO",
                "3"=>"LAUDO TÉCNICO"
               );
           

/**
 * Array com os filtros que montaram a clausula where
 */
$aFiltros   = array();
$aFiltros[] = " calendario.ed52_i_ano = {$oGet->iAno} ";
$aFiltros[] = " ed60_c_situacao       = 'MATRICULADO' ";
$aFiltros[] = " ed60_c_ativa          = 'S' ";
  
if ($oGet->aEscola != 0) {
    $head2 = "ESCOLA: ".substr($oGet->aEscola, 0, 40).((strlen($oGet->aEscola)>40)?' ...':'');
    $aFiltros[] = "turma.ed57_i_escola in ({$oGet->aEscola})";
}

if ($oGet->aSerie != 0) {
    $head4      = "ETAPA: ".$oGet->aSerie;
    $aFiltros[] = "serie.ed11_i_codigo in ({$oGet->aSerie})";
}

if ($oGet->iSemestre != 0) {
    $aFiltros[] = "calendario.ed52_i_periodo in (0, {$oGet->iSemestre})";
}

if ($oGet->aNecessidade != 0) {
    $head5 = "NECESSIDADE: ".$oGet->aNecessidade;
  
  // CASO OCORRA NECESSIDADE MÚLTIPLA
    if (in_array("108", explode(",", $oGet->aNecessidade))) {
        // FILTRA TODOS OS ALUNOS COM MAIS DE UMA DEFICIENCIA
        $aFiltros[] = "alunonecessidade.ed214_i_aluno in (select ed214_i_aluno from alunonecessidade GROUP BY ed214_i_aluno HAVING COUNT(*) >1)";
        // REMOVE TODOS OS REGISTROS COM DEFICIENCIAS REPETIDAS PARA O MESMO ALUNO
        $aFiltros[] = "alunonecessidade.ed214_i_aluno not in (select  ed214_i_aluno from alunonecessidade  GROUP BY ed214_i_aluno,ed214_i_necessidade HAVING COUNT(*) >1)";
    } else {
        // ALUNOS COM PELO MENOS UMA OCORRENCIA DA DEFICIENCIA ESCOLHIDA
        $aFiltros[] = "alunonecessidade.ed214_i_aluno in 
        (select ed214_i_aluno from alunonecessidade 
        where alunonecessidade.ed214_i_necessidade in ({$oGet->aNecessidade}) 
        GROUP BY ed214_i_aluno HAVING COUNT(*) > 0)";
    }
}


$sJoin = "";
if ($oGet->aSubdivisao != 0) {
    $head6 = "SUBDIVISÃO: ".$oGet->aSubdivisao;
    $sJoin .= " LEFT JOIN necessidadesubdivisao ON necessidadesubdivisao.ed185_necessidade = necessidade.ed48_i_codigo ";
    $sJoin .= " LEFT JOIN necessidadesubdivisaoaluno ON necessidadesubdivisaoaluno.ed187_necessidadesubdivisao = necessidadesubdivisao.ed185_sequencial ";
    $sJoin .= " LEFT JOIN necessidadealunocadeirante ON necessidadealunocadeirante.ed189_aluno = alunonecessidade.ed214_i_aluno ";
    $sJoin .= " LEFT JOIN necessidadealunobpc ON necessidadealunobpc.ed190_aluno = alunonecessidade.ed214_i_aluno ";
    $aFiltros[] = "necessidadesubdivisao.ed185_sequencial in ({$oGet->aSubdivisao}) AND necessidadesubdivisaoaluno.ed187_aluno = aluno.ed47_i_codigo";
}

if ($oGet->iCadeirante != 0) {
    $head7 = "CADEIRANTE: SIM";
    if ($oGet->iCadeirante == 1) {
        $sJoin .= " LEFT JOIN necessidadealunocadeirante as cadeirante ON cadeirante.ed189_aluno = aluno.ed47_i_codigo ";
        $aFiltros[] = "cadeirante.ed189_aluno is not null";
    } else {
        $sJoin .= " LEFT JOIN necessidadealunocadeirante as cadeirante ON cadeirante.ed189_aluno = aluno.ed47_i_codigo ";
        $aFiltros[] = "cadeirante.ed189_aluno is null";
    }
}

if ($oGet->iBPC != 0) {
    $head8 = "BPC: SIM";
    if ($oGet->iBPC == 1) {
        $sJoin .= " LEFT JOIN necessidadealunobpc as bpc ON bpc.ed190_aluno = aluno.ed47_i_codigo ";
        $aFiltros[] = "bpc.ed190_aluno is not null";
    } else {
        $sJoin .= " LEFT JOIN necessidadealunobpc as bpc ON bpc.ed190_aluno = aluno.ed47_i_codigo ";
        $aFiltros[] = "bpc.ed190_aluno is null";
    }
}

if ($oGet->tipoAtendimento != 0) {
    $aWhere = array();
    $sCampos = "ed186_sequencial, ed186_descricao";

    $aWhere[] = "ed186_sequencial = {$oGet->tipoAtendimento}";
    $oTipoAtendimento = new cl_necessidadetipoatendimento;
    $sWhere = implode(' and ', $aWhere);
    $sSql = $oTipoAtendimento->sql_query('', $sCampos, null, $sWhere);
    $rTipoAtendimento = $oTipoAtendimento->sql_record($sSql);

    if ($oTipoAtendimento->numrows > 0) {
        $tipoAtendimento = db_utils::fieldsMemory($rTipoAtendimento, 0);
        $head9 = "TIPO ATENDIMENTO: ".substr($tipoAtendimento->ed186_descricao, 0, 25);
    }

    $aFiltros[] = "
    (
        select count(distinct necessidadetipoatendimento.ed186_sequencial)
        from necessidadetipoatendimentoaluno
        join necessidadetipoatendimento ON necessidadetipoatendimento.ed186_sequencial = necessidadetipoatendimentoaluno.ed188_necessidadetipoatendimento
        where necessidadetipoatendimentoaluno.ed188_aluno = aluno.ed47_i_codigo and necessidadetipoatendimento.ed186_sequencial = {$oGet->tipoAtendimento}
    
    ) > 0
    ";
}

$aFiltros[] .= " matriculaserie.ed221_c_origem = 'S'";
$sWhere  = implode(" and ", $aFiltros);

/**
 * Busca todos os alunos alunos que possuem necessidades especiáis
 */
$sSql  = " SELECT ";
$sSql .= "       turma.ed57_i_escola,                                 ";
$sSql .= "       trim(serie.ed11_c_descr) as ed11_c_descr,            ";
$sSql .= "       trim(aluno.ed47_v_nome) as ed47_v_nome,              ";
$sSql .= "       turma.ed57_i_codigo,                                 ";
$sSql .= "       escola.ed18_c_nome,                                  ";
$sSql .= "       trim(aluno.ed47_c_codigoinep) as ed47_c_codigoinep,  ";
$sSql .= "       aluno.ed47_i_codigo,                                 ";
$sSql .= "       serie.ed11_i_codigo,                                 ";
$sSql .= "       ensino.ed10_i_codigo,                                ";
$sSql .= "       trim(ensino.ed10_c_descr) as ed10_c_descr,           ";
$sSql .= "       trim(turma.ed57_c_descr) as ed57_c_descr,            ";
$sSql .= "       trim(calendario.ed52_c_descr) as ed52_c_descr,       ";
$sSql .= "       serie.ed11_i_sequencia,                              ";
$sSql .= "       necessidade.ed48_i_codigo,                           ";
$sSql .= "       trim(necessidade.ed48_c_descr) as ed48_c_descr,      ";
$sSql .= "       alunonecessidade.ed214_i_apoio,                      ";
$sSql .= "       alunonecessidade.ed214_i_tipo,                        ";
$sSql .= "       case when ed214_i_anexo_estorage is null then 'NÃO' else 'SIM' end as laudo_anexo,";
$sSql .= "(";
$sSql .= "    select string_agg(distinct necessidadetipoatendimento.ed186_descricao, ', ') ";
$sSql .= "    from necessidadetipoatendimentoaluno";
$sSql .= "    join necessidadetipoatendimento ";
$sSql .= "        ON necessidadetipoatendimento.ed186_sequencial = necessidadetipoatendimentoaluno.ed188_necessidadetipoatendimento";
$sSql .= "    where necessidadetipoatendimentoaluno.ed188_aluno = aluno.ed47_i_codigo";
$sSql .= ") as tipos_atendimentos";
$sSql .= "  FROM matricula ";
$sSql .= " INNER JOIN aluno            ON aluno.ed47_i_codigo              = matricula.ed60_i_aluno ";
$sSql .= " INNER JOIN turma            ON turma.ed57_i_codigo              = matricula.ed60_i_turma ";
$sSql .= " INNER JOIN escola           ON escola.ed18_i_codigo             = turma.ed57_i_escola ";
$sSql .= " INNER JOIN calendario       ON calendario.ed52_i_codigo         = turma.ed57_i_calendario ";
$sSql .= " INNER JOIN base             ON base.ed31_i_codigo               = turma.ed57_i_base ";
$sSql .= " INNER JOIN cursoedu         ON cursoedu.ed29_i_codigo           = base.ed31_i_curso ";
$sSql .= " INNER JOIN ensino           ON ensino.ed10_i_codigo             = cursoedu.ed29_i_ensino ";
$sSql .= " INNER JOIN matriculaserie   ON matriculaserie.ed221_i_matricula = matricula.ed60_i_codigo ";
$sSql .= " INNER JOIN serie            ON serie.ed11_i_codigo              = matriculaserie.ed221_i_serie ";
$sSql .= " INNER JOIN alunonecessidade ON alunonecessidade.ed214_i_aluno   = aluno.ed47_i_codigo ";
$sSql .= " INNER JOIN necessidade      ON necessidade.ed48_i_codigo        = alunonecessidade.ed214_i_necessidade ";
$sSql .= " {$sJoin} ";
$sSql .= " WHERE {$sWhere} ";
$sSql .= " ORDER BY ed57_i_escola,";
$sSql .= " ed11_c_descr,";
$sSql .= " ed47_v_nome,ed57_i_codigo";
$rsAlunos = db_query($sSql);
$iLinhasMatricula = pg_num_rows($rsAlunos);

if ($iLinhasMatricula == 0) {
    db_redireciona("db_erros.php?fechar=true&db_erro=Nenhum registro encontrado para os filtros selecionados.");
}

/**
 * Organiza os dados retornado pela query
 */
$aTotalAlunoEscola  = array();
$aAlunosNecessidade = array();
for ($i = 0; $i < $iLinhasMatricula; $i++) {
    $oDadosMatricula = db_utils::fieldsMemory($rsAlunos, $i);
  
    $iEscola = $oDadosMatricula->ed57_i_escola;
    $iEnsino = $oDadosMatricula->ed10_i_codigo;
    $iEtapa  = $oDadosMatricula->ed11_i_codigo;
    $iTurma  = $oDadosMatricula->ed57_i_codigo;
  
    if (!array_key_exists($iEscola, $aAlunosNecessidade)) {
        $oEscola          = new stdClass();
        $oEscola->iCodigo = $oDadosMatricula->ed57_i_escola;
        $oEscola->sEscola = $oDadosMatricula->ed18_c_nome;
        $oEscola->aEtapa  = array();
  
        $aAlunosNecessidade[$iEscola] = $oEscola;
    }
  
    $iChaveEnsinoEtapa = "{$iEscola}#{$iEnsino}#{$iEtapa}#{$iTurma}";
  
    if (!array_key_exists($iChaveEnsinoEtapa, $aAlunosNecessidade[$iEscola]->aEtapa)) {
        $oEnsino                = new stdClass();
        $oEnsino->iCodigoEnsino = $oDadosMatricula->ed10_i_codigo;
        $oEnsino->sEnsino       = $oDadosMatricula->ed10_c_descr;
        $oEnsino->iCodigoEtapa  = $oDadosMatricula->ed11_i_codigo;
        $oEnsino->sEtapa        = $oDadosMatricula->ed11_c_descr;
        $oEnsino->aAlunos       = array();
  
        $aAlunosNecessidade[$iEscola]->aEtapa[$iChaveEnsinoEtapa] = $oEnsino;
    }
  
    $oAluno                = new stdClass();
    $oAluno->iCodigo       = $oDadosMatricula->ed47_i_codigo;
    $oAluno->sNome         = $oDadosMatricula->ed47_v_nome;
    $oAluno->sTurma        = $oDadosMatricula->ed57_c_descr;
    $oAluno->sEtapa        = $oDadosMatricula->ed11_c_descr;
    $oAluno->sInep         = $oDadosMatricula->ed47_c_codigoinep;
    $oAluno->iEscola       = $oDadosMatricula->ed57_i_escola;
    $oAluno->laudo_anexo   = $oDadosMatricula->laudo_anexo;
    $oAluno->aNecessidades = array();
  
    $oNecessidade               = new stdClass();
    $oNecessidade->iNecessidade = $oDadosMatricula->ed48_i_codigo;
    $oNecessidade->sNecessidade = $oDadosMatricula->ed48_c_descr;
    $oNecessidade->sApoio       = $aApoio[$oDadosMatricula->ed214_i_apoio];
    $oNecessidade->sTipo        = $aTipo[$oDadosMatricula->ed214_i_tipo];

    if (!array_key_exists($oDadosMatricula->ed48_i_codigo, $oAluno->aNecessidades)) {
        $oAluno->aNecessidades[$oDadosMatricula->ed48_i_codigo] = $oNecessidade;
    }

    $oAluno->necessidadeTipoAtendimentos = $oDadosMatricula->tipos_atendimentos;

    $sSqlSubdivisao = "select ed185_sequencial as subdivisao, ed185_descricao as descricao
                       from necessidadesubdivisaoaluno as subdivisaoaluno
                      inner join necessidadesubdivisao as subdivisao
                         on subdivisao.ed185_sequencial = subdivisaoaluno.ed187_necessidadesubdivisao
                      where subdivisao.ed185_necessidade = $oDadosMatricula->ed48_i_codigo
                        and subdivisaoaluno.ed187_aluno = $oDadosMatricula->ed47_i_codigo";

    $rsSubdivisao = db_query($sSqlSubdivisao);
    $iLinhasSubdivisao = pg_num_rows($rsSubdivisao);
    $oNecessidade->aSubdivisoes = array();

    if ($iLinhasSubdivisao != 0) {
        for ($j = 0; $j < $iLinhasSubdivisao; $j++) {
            $oDadosSubdivisao = db_utils::fieldsMemory($rsSubdivisao, $j);
            if (!array_key_exists($oDadosSubdivisao->subdivisao, $oNecessidade->aSubdivisoes)) {
                 $oNecessidade->aSubdivisoes[$oDadosSubdivisao->subdivisao] = $oDadosSubdivisao;
            }
        }
    }

    if (array_key_exists($oDadosMatricula->ed47_i_codigo, $aAlunosNecessidade[$iEscola]->aEtapa[$iChaveEnsinoEtapa]->aAlunos)) {
        if (!array_key_exists($oDadosMatricula->ed48_i_codigo, $aAlunosNecessidade[$iEscola]->aEtapa[$iChaveEnsinoEtapa]->aAlunos[$oAluno->iCodigo]->aNecessidades)) {
            $aAlunosNecessidade[$iEscola]->aEtapa[$iChaveEnsinoEtapa]->aAlunos[$oAluno->iCodigo]->aNecessidades[] = $oNecessidade;
        }
    } else {
        $aTotalAlunoEscola[$iEscola][] = true;
        $aAlunosNecessidade[$iEscola]->aEtapa[$iChaveEnsinoEtapa]->aAlunos[$oAluno->iCodigo] = $oAluno;
    }
}

$oPdf = new Pdf("P");

$oPdf->addTitulo($head1, 1);
$oPdf->addTitulo($head2, 2);
$oPdf->addTitulo($head3, 3);
$oPdf->addTitulo($head4, 4);
$oPdf->addTitulo($head5, 5);
$oPdf->addTitulo($head6, 6);
$oPdf->addTitulo($head7, 7);
$oPdf->addTitulo($head8, 8);
$oPdf->addTitulo($head9, 9);

$oPdf->AliasNbPages();
$oPdf->SetAutoPageBreak(true);
$oPdf->SetFillColor(225, 225, 225);
$oPdf->init(false);

$iHeight = 4;

/**
 * Percorre os dados e imprime o relatório
 */
$totalGeral = 0;
$qtdEscola = 0;

foreach ($aAlunosNecessidade as $oEscola) {
    $qtdEscola +=1;
  
    $oPdf->AddPage();
    validaQuebraPagina($oPdf);
    $oPdf->SetFont('arial', 'b', 8);
    $oPdf->Cell(15, $iHeight, "Escola: ", 0, 0, "L");
    $oPdf->SetFont('arial', '', 7);
    $oPdf->Cell(165, $iHeight, "{$oEscola->iCodigo} - {$oEscola->sEscola}", 0, 1, "L");
    $iCodigoEtapa = 0;
    $primeiroPrintEtapa = true;
    $iTotalAlunosEtapa = 0;
    foreach ($oEscola->aEtapa as $oEtapa) {
        if ($iCodigoEtapa != $oEtapa->iCodigoEtapa) {
            $iCodigoEtapa = $oEtapa->iCodigoEtapa;
            $lPrimeiraPagina   = true;
            $bEtapaMudou = true;

            if ($bEtapaMudou && !$primeiroPrintEtapa) {
                $oPdf->SetFont('arial', 'b', 8);
                $oPdf->Cell(160, $iHeight, "Total de alunos para etapa: {$sNomeEtapa}", "TBR", 0, "R");
                $oPdf->Cell(30, $iHeight, $iTotalAlunosEtapa, "TBL", 1, "R");
                $oPdf->Ln(2);
                $sNomeEtapa = '';
                $iTotalAlunosEtapa = 0;
            }
        }

        if ($primeiroPrintEtapa || $bEtapaMudou) {
            validaQuebraPagina($oPdf);
            $oPdf->SetFont('arial', 'b', 8);
            $oPdf->Cell(15, $iHeight, "Etapa: ", 0, 0, "L");
            $oPdf->SetFont('arial', '', 7);
            $oPdf->Cell(165, $iHeight, $oEtapa->sEtapa, 0, 1, "L");
            $primeiroPrintEtapa = false;
            $bEtapaMudou = false;
        }
        $sNomeEtapa = $oEtapa->sEtapa;
        foreach ($oEtapa->aAlunos as $oAluno) {
            if ($lPrimeiraPagina) {
                $lPrimeiraPagina = false;
                imprimeCabecalho($oPdf, $iHeight);
                $sNomeTurma = $oAluno->sTurma;
            }
            $iTotalAlunosEtapa+=1;
            validaQuebraPagina($oPdf);
      
          //verifica se cadeirante
            $sCadeirante = "NÃO";
            $clAlunoCadeirante->sql_record($clAlunoCadeirante->sql_query_file(
                null,
                "ed189_sequencial",
                null,
                "ed189_aluno = {$oAluno->iCodigo}"
            ));
            if ($clAlunoCadeirante->numrows > 0) {
                  $sCadeirante = "SIM";
            }
      
          //verifica se bpc
            $sBPC = "NÃO";
            $clAlunoBPC->sql_record($clAlunoBPC->sql_query_file(
                null,
                "ed190_sequencial",
                null,
                "ed190_aluno = {$oAluno->iCodigo}"
            ));
            if ($clAlunoBPC->numrows) {
                  $sBPC = "SIM";
            }
      
            $oPdf->SetFont('arial', '', 7);
            $oPdf->cell(11, $iHeight, $oAluno->iCodigo, "T", 0, "C", 0);
            $oPdf->cell(60, $iHeight, $oAluno->sNome, "T", 0, "L", 0);
            $oPdf->cell(44, $iHeight, $oAluno->sTurma, "T", 0, "C", 0);
            $oPdf->cell(30, $iHeight, $oAluno->sEtapa, "T", 0, "C", 0);
            $oPdf->cell(18, $iHeight, $sCadeirante, "T", 0, "C", 0);
            $oPdf->cell(10, $iHeight, $sBPC, "T", 0, "C", 0);
            $oPdf->cell(20, $iHeight, $oAluno->laudo_anexo, "T", 1, "C", 0);

      
            $oPdf->setfont('arial', 'b', 6);
            $oPdf->cell(10, $iHeight, "", 0, 0, "L", 0);
            $oPdf->cell(75, $iHeight, "Necessidade", 0, 0, "L", 0);
            $oPdf->cell(70, $iHeight, "Apoio Pedagógico", 0, 0, "L", 0);
            $oPdf->cell(35, $iHeight, "Tipo de Diagnóstico", 0, 1, "L", 0);

            foreach ($oAluno->aNecessidades as $oNecessidade) {
                $oPdf->setfont('arial', '', 6);
                $oPdf->cell(10, $iHeight, "", "T", 0, "L", 0);
                $oPdf->cell(75, $iHeight, "{$oNecessidade->iNecessidade} - {$oNecessidade->sNecessidade}", "T", 0, "L", 0);
                $oPdf->cell(70, $iHeight, $oNecessidade->sApoio, "T", 0, "L", 0);
                $oPdf->cell(35, $iHeight, $oNecessidade->sTipo, "T", 1, "L", 0);

                if ($oNecessidade->aSubdivisoes > 0) {
                    $subdivisoes = array();
                    foreach ($oNecessidade->aSubdivisoes as $oSubdivisao) {
                        $subdivisoes[] = "{$oSubdivisao->descricao}";
                    }
                    $subdivisoes = implode(" / ", $subdivisoes);
                    $oPdf->setfont('arial', '', 6);
                    $oPdf->cell(15, $iHeight, "", "0", 0, "L", 0);
                    $oPdf->MultiCell(177, $iHeight, $subdivisoes, "C", "L");
                }
            }

            $oPdf->setfont('arial', 'b', 6);
            $oPdf->cell(177, $iHeight, "TIPOS DE ATENDIMENTOS: ", 0, 1, "L", 0);
            $oPdf->setfont('arial', '', 6);
            if (strlen($oAluno->necessidadeTipoAtendimentos) > 0) {
                $oPdf->MultiCell(177, $iHeight, $oAluno->necessidadeTipoAtendimentos, "C", "L");
                $oPdf->Ln(0.6);
            }
        }
        validaQuebraPagina($oPdf);
    }
    $oPdf->SetFont('arial', 'b', 8);
    $oPdf->Cell(160, $iHeight, "Total de alunos para etapa: {$sNomeEtapa}", "TBR", 0, "R");
    $oPdf->Cell(30, $iHeight, $iTotalAlunosEtapa, "TBL", 1, "R");
    $oPdf->Ln(2);

    $oPdf->SetFont('arial', 'b', 8);
    $oPdf->Cell(160, $iHeight, "Total de alunos na Escola ", "TBR", 0, "R");
    $oPdf->Cell(30, $iHeight, count($aTotalAlunoEscola[$oEscola->iCodigo]), "TBL", 1, "R");
    $oPdf->Ln();
  

    $totalGeral += count($aTotalAlunoEscola[$oEscola->iCodigo]);

    $oPdf->SetFont('arial', 'b', 8);
    $oPdf->Cell(160, $iHeight, "Total Geral de Alunos em ".str_pad($qtdEscola, 2, 0, STR_PAD_LEFT).
    " Escola".($qtdEscola>1?'s':'')." ", "TBR", 0, "R");
    $oPdf->Cell(30, $iHeight, $totalGeral, "TBL", 1, "R");
    $oPdf->Ln();
}


/**
 * imprime cabeçalho
 * @param FPDF $oPdf
 * @param integer $iHeight
 */
function imprimeCabecalho(Pdf $oPdf, $iHeight)
{

    $oPdf->SetFont('arial', 'b', 8);
    $oPdf->cell(11, $iHeight, "Código", "TBR", 0, "C", 1);
    $oPdf->cell(60, $iHeight, "Aluno", 1, 0, "L", 1);
    $oPdf->cell(44, $iHeight, "Turma", 1, 0, "C", 1);
    $oPdf->cell(30, $iHeight, "Etapa", 1, 0, "C", 1);
    $oPdf->cell(18, $iHeight, "Cadeirante", 1, 0, "C", 1);
    $oPdf->cell(10, $iHeight, "BPC", 1, 0, "C", 1);
    $oPdf->cell(20, $iHeight, "Anexo Laudo", 1, 1, "C", 1);
}



/**
 * Valida se deve ser quebrado pagina
 * @param FPDF $oPdf
 */
function validaQuebraPagina(Pdf $oPdf)
{

    if ($oPdf->GetY() > $oPdf->getH() - 30) {
        $oPdf->AddPage();
    }
}

$oPdf->Output();
