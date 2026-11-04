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

$oGet = db_utils::postMemory($_GET);

define('BRASAO_REPUBLICA', 1);
define('BRASAO_MUNICIPIO', 2);
try {
    $turmaNome = base64_decode($oGet->turmaNome);
    $identificacaoAnoTurna = substr($turmaNome, 0, 2);
    $sqlGrade = "
    select ed65_i_codigo, ed232_c_descr, ed232_c_abrev, ed65_c_situacao, ed65_t_resultobtido,  
    case 
        when ed65_c_situacao = 'AMPARADO' OR ed65_c_situacao = 'NÃO OPTANTE' then null 
        else ed65_c_resultadofinal 
    end as ed65_c_resultadofinal,  
    ed65_i_qtdch, ed65_c_tiporesultado, ed65_i_historicomps, ed29_c_descr, ed11_c_descr, ed11_i_ensino, 
    ed11_i_sequencia, ed62_i_anoref, ed65_i_ordenacao, ed65_c_termofinal, ed65_basecomum, ed65_tipobase, ed47_v_nome, 
    ed47_i_codigo, ed232_i_codigo
    from histmpsdisc  
    left join justificativa on justificativa.ed06_i_codigo = histmpsdisc.ed65_i_justificativa  
    inner join disciplina on disciplina.ed12_i_codigo = histmpsdisc.ed65_i_disciplina  
    inner join historicomps on historicomps.ed62_i_codigo = histmpsdisc.ed65_i_historicomps  
    inner join escola on escola.ed18_i_codigo = historicomps.ed62_i_escola  
    inner join caddisciplina on caddisciplina.ed232_i_codigo = disciplina.ed12_i_caddisciplina  
    inner join ensino on ensino.ed10_i_codigo = disciplina.ed12_i_ensino  
    inner join serie on serie.ed11_i_codigo = historicomps.ed62_i_serie  
    inner join historico on historico.ed61_i_codigo = historicomps.ed62_i_historico
    inner join cursoedu on cursoedu.ed29_i_codigo = historico.ed61_i_curso  
    inner join aluno on aluno.ed47_i_codigo = historico.ed61_i_aluno
    where ed62_i_escola = {$oGet->escola}  and historico.ed61_i_aluno in ({$oGet->alunos}) 
        and ed62_i_turma = '{$turmaNome}'
    order by ed65_basecomum desc, ed65_i_ordenacao, ed47_v_nome
    ";

    $rsGrade = db_query($sqlGrade);
    $linhas = pg_num_rows($rsGrade);
    $listaDisciplinas = [];
    $resultadosDisciplinas = [];
    for ($i = 0; $i < pg_num_rows($rsGrade); $i++) {
        $dadosDisciplinas = db_utils::fieldsMemory($rsGrade, $i);
        $disciplinaTemp = new stdClass();
        if (!key_exists($dadosDisciplinas->ed232_i_codigo, $listaDisciplinas)) {
            $disciplinaTemp->codigo = $dadosDisciplinas->ed232_i_codigo;
            $disciplinaTemp->nome = $dadosDisciplinas->ed232_c_descr;
            $disciplinaTemp->nomeAbreviado = $dadosDisciplinas->ed232_c_abrev;
            $listaDisciplinas[$dadosDisciplinas->ed232_i_codigo] = $disciplinaTemp;
        }

        $chave = "{$dadosDisciplinas->ed47_i_codigo}#$dadosDisciplinas->ed232_i_codigo";
        $dadosTemp = new stdClass();
        $dadosTemp->codigoDisciplina = $dadosDisciplinas->ed232_i_codigo;
        $dadosTemp->nomeDisciplina = $dadosDisciplinas->ed232_c_descr;
        $dadosTemp->nomeAbreviadoDisciplina = $dadosDisciplinas->ed232_c_abrev;
        $dadosTemp->resultadoObtidoDisciplina = $dadosDisciplinas->ed65_t_resultobtido;
        $dadosTemp->resultadoFinalDisciplina = $dadosDisciplinas->ed65_c_resultadofinal;
        $dadosTemp->qtdChDisciplina = $dadosDisciplinas->ed65_i_qtdch;
        $dadosTemp->situacaoDisciplina = $dadosDisciplinas->ed65_c_situacao;
        $resultadosDisciplinas[$chave] = $dadosTemp;
    }

    usort($listaDisciplinas, function ($a, $b) {
        if ($a->nomeAbreviado < $b->nomeAbreviado) {
            return -1;
        } elseif ($a->nomeAbreviado > $b->nomeAbreviado) {
            return 1;
        }
        return 0;
    });
    
    $oDaoEduRelatModel  = new cl_edu_relatmodel;
    $sCamposRelatModel  = "
    ed217_t_cabecalho, ed217_t_rodape, ed217_t_obs, ed217_exibir_grade_alunos, 
    ed217_exibir_coluna_disciplinas, ed217_exibir_coluna_resultado_final, ed217_exibir_brasao,
    ed217_disposicao_brasao, ed217_disposicao_cabecalho, ed217_brasao
    ";
    $sSqlEduRelatModel  = $oDaoEduRelatModel->sql_query("", $sCamposRelatModel, "", "ed217_i_codigo = {$oGet->modeloAtaId}");
    $rsEduRelatModel    = $oDaoEduRelatModel->sql_record($sSqlEduRelatModel);

    if ($oDaoEduRelatModel->numrows > 0) {
        $oDadosRelatModel = db_utils::fieldsmemory($rsEduRelatModel, 0);
    }

    $meses = [
        "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "junho",
        "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
    ];
    $oInstit = new Instituicao(db_getsession("DB_instit"));
    $brasao = getBrasao($oDadosRelatModel->ed217_brasao, $oInstit);

    $localData = $oInstit->getMunicipio().", ".(new DBDate(date('d/m/Y')))->dataPorExtenso();

    $cabecalhoModelo = strlen($oDadosRelatModel->ed217_t_cabecalho) > 0
        ? base64_decode($oDadosRelatModel->ed217_t_cabecalho)
        : "";

    $observacaoTela = $oGet->sObs != "" ? urldecode($oGet->sObs) : "";
    $observacaoTela = mb_convert_encoding($observacaoTela, 'ISO-8859-1', 'UTF-8');
    $observacaoModelo = $oDadosRelatModel->ed217_t_obs != "" ? $oDadosRelatModel->ed217_t_obs : "";

    $pdf = new Pdf();
    $pdf->init(false);
    $pdf->exibeHeader(false);
    $pdf->setExibeBrasao(true);
    $pdf->setAutoPageBreak(true, 15);
    $pdf->addPage();
    $larguraUtil = ($pdf->getW() - $pdf->getRightMargin() - $pdf->getLeftMargin());
    
    $escolaInformada = EscolaRepository::getEscolaByCodigo($oGet->escola);
    $escolaNome = $escolaInformada->getNome();
    $secretarioNome = strlen($oGet->secretarioNome) > 0
        ? base64_decode($oGet->secretarioNome)
        : " ";
    $diretorNome = strlen($oGet->diretorNome) > 0
        ? base64_decode($oGet->diretorNome)
        : " ";
    $textoRelatorio = strlen($oGet->textoRelatorio) > 0 ? urldecode($oGet->textoRelatorio) : '' ;
    $textoRelatorio = mb_convert_encoding($textoRelatorio, 'ISO-8859-1', 'UTF-8');
    $escolaInformada = EscolaRepository::getEscolaByCodigo($oGet->escola);
    if (!empty($oGet->iAssinaturaAdicional) && $oGet->exibirAssinaturaAdicional == 't') {
        $sNomeDocente = "";
        $sFuncaoDocente = "Funcionário";
        if (!empty($oGet->iAssinaturaAdicional)) {
            $oDocente = DocenteRepository::getDocenteByCodigoRecursosHumano($oGet->iAssinaturaAdicional);
            $sNomeDocente = $oDocente->getNome();
            $sFuncaoDocente = '';

            foreach ($oDocente->getAtividades($escolaInformada) as $oAtividades) {
                if (isset($oGet->iAtividade) && $oAtividades->getAtividade()->getCodigo() == $oGet->iAtividade) {
                    $sFuncaoDocente = $oAtividades->getAtividade()->getDescricao();
                }
            }
        }

        $assAdicional = new stdClass();
        $assAdicional->nome = $sNomeDocente;
        $assAdicional->cargo = $sFuncaoDocente;
    }

    $TamFonteNome = 9;
    if (strlen($nomeInstituicao) > 42 || strlen($nomeEscola) > 42) {
        $TamFonteNome = 8;
    }
    $linha = "______________________________________________________";
    $dataPartes = [];
    $dataPartes = explode("/", $oGet->dataEmissao);
    $largura = ($pdf->getW()) / 2;

    $larguraCabecalho = null;
    $pdf->SetFillColor(223);
    if ($oDadosRelatModel->ed217_exibir_brasao == 't') {
        if ($oDadosRelatModel->ed217_disposicao_brasao == 'ACIMA') {
            $pdf->Image($brasao, $larguraUtil / 2, 10, 25, 25);
            $larguraCabecalho = $larguraUtil;
        } else {
            $pdf->Image($brasao, 10, 10, 25, 25);
            $larguraCabecalho = $larguraUtil - 30;
        }
    }
    
    $yImagem = $pdf->getY();
    
    $disposicoesCabecalho = [
        'ESQUERDA' => 'L',
        'DIREITA' => 'R',
        'CENTRALIZADO' => 'C'
    ];

    if ($oDadosRelatModel->ed217_exibir_brasao == 't' && $oDadosRelatModel->ed217_disposicao_brasao == 'LADO') {
        $pdf->SetX(36);
    }
    if ($oDadosRelatModel->ed217_disposicao_brasao == 'ACIMA' && $oDadosRelatModel->ed217_exibir_brasao == 't') {
        $pdf->setY($yImagem + 28);
    }
    $pdf->MultiCell(
        $larguraCabecalho,
        4,
        $oDadosRelatModel->ed217_t_cabecalho,
        0,
        $disposicoesCabecalho[$oDadosRelatModel->ed217_disposicao_cabecalho],
        0
    );
    $pdf->ln();
    if ($oDadosRelatModel->ed217_disposicao_brasao == 'LADO' && $oDadosRelatModel->ed217_exibir_brasao == 't') {
        $pdf->setY($yImagem + 28);
    }
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->MultiCell($larguraUtil, 7, "Ata Reunião do Ano Letivo de {$dataPartes[2]}.", 1, "C", 0);
    $pdf->ln(0.6);
    $pdf->SetFont('Arial', '', 8);
    $pdf->MultiCell($larguraUtil, 4, "Aos {$dataPartes[0]} dias do mês de {$meses[$dataPartes[1]-1]} do ano de {$dataPartes[2]} a {$escolaNome}, {$textoRelatorio}", 1, "J", 0);
    $pdf->ln(0.6);

    if ($oDadosRelatModel->ed217_exibir_coluna_resultado_final == 't') {
        $larguraColunaRF = 7;
    } else {
        $larguraColunaRF = 0;
    }

    $larguraColunaNum = 7;
    $larguraColunaNome = 80;
    $larguraColunaDisciplinas = 13;

    $qtdDisciplinas = count($listaDisciplinas);
    $complementoColunaNome = $qtdDisciplinas * $larguraColunaDisciplinas;
    if ($oDadosRelatModel->ed217_exibir_coluna_disciplinas == 'f') {
        $complementoColunaNome = 0;
    }
    $larguraColunaNome = $larguraUtil - $complementoColunaNome - $larguraColunaNum - $larguraColunaRF;

    if ($oDadosRelatModel->ed217_exibir_grade_alunos == 't') {
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->cell($larguraColunaNum, 4, "Nº", 1, 0, 'C', 0, '');
        $pdf->cell($larguraColunaNome, 4, "Nome do(a) aluno(a)", 1, 0, 'C', 0, '');
        foreach ($listaDisciplinas as $disciplina) {
            if ($oDadosRelatModel->ed217_exibir_coluna_disciplinas == 'f') {
                continue;
            }
            $pdf->cell($larguraColunaDisciplinas, 4, $disciplina->nomeAbreviado, 1, 0, 'C', 0, '');
        }

        if ($oDadosRelatModel->ed217_exibir_coluna_resultado_final == 't') {
            $pdf->cell($larguraColunaRF, 4, "RF", 1, 1, 'C', 0, '');
        } else {
            $pdf->ln();
        }
    }

    $contadorAlunos = 1;
    if ($oDadosRelatModel->ed217_exibir_grade_alunos == 't') {
        $alunosImpressos = [];
        $pdf->SetFont('Arial', '', 7);
        for ($i = 0; $i < pg_num_rows($rsGrade); $i++) {
            $dadosGrade = db_utils::fieldsMemory($rsGrade, $i);
            if (!in_array($dadosGrade->ed47_i_codigo, $alunosImpressos)) {
                $pdf->cell($larguraColunaNum, 4, $contadorAlunos, 1, 0, 'C', 0, '');
                $pdf->cell($larguraColunaNome, 4, $dadosGrade->ed47_v_nome, 1, 0, 'L', 0, '');
                
                foreach ($listaDisciplinas as $disciplina) {
                    if ($oDadosRelatModel->ed217_exibir_coluna_disciplinas == 'f') {
                        continue;
                    }
                    foreach ($resultadosDisciplinas as $key => $resultadoDisc) {
                        if ("{$dadosGrade->ed47_i_codigo}#{$disciplina->codigo}" == $key) {
                            $pdf->cell($larguraColunaDisciplinas, 4, $resultadoDisc->resultadoObtidoDisciplina, 1, 0, 'C', 0, '');
                        }
                    }
                }
                if ($oDadosRelatModel->ed217_exibir_coluna_resultado_final == 't') {
                    $pdf->cell($larguraColunaRF, 4, $dadosGrade->ed65_c_resultadofinal, 1, 1, 'C', 0, '');
                } else {
                    $pdf->ln();
                }
                $contadorAlunos++;
            }
            
            $alunosImpressos[] = $dadosGrade->ed47_i_codigo;
        }
        $pdf->ln(0.6);
    }

    $observacoes = "";
    if (strlen($observacaoModelo) > 0) {
        $observacoes = $observacaoModelo;
    }
    if (strlen($observacaoTela) > 0) {
        $quebra = "";
        if (strlen($observacaoModelo) > 0) {
            $quebra = "\n";
        }
        $observacoes .= $quebra.$observacaoTela;
    }
    if (strlen($observacoes) > 0) {
        $pdf->MultiCell($larguraUtil, 4, $observacoes, 1, "L", 0);
        $pdf->ln(0.6);
    }
    $pdf->MultiCell($larguraUtil, 4, "E para constar, lavrei a presente Ata, que vai por mim assinada e pelo(a) diretor(a) da Escola.", 1, "L", 0);
    $pdf->ln(0.6);
    $pdf->MultiCell($larguraUtil, 5, $localData, 1, "C", 0);
    $posYinicial = $pdf->getY();
    $posXinicial = $pdf->getX();
    $pdf->setXY($posXinicial, $posYinicial + 2);
    $assinaturaSecretario = "\n\n\n$linha\n$secretarioNome\nSecretário(a)\n";
    $assinaturaDiretor = "\n\n\n$linha\nDiretor(a)\n\n";
    if ($oGet->exibirAssinaturaSecretario == 'false') {
        $assinaturaSecretario = "\n\n \n \n \n\n";
    }
    $pdf->multiCell($largura-10, 4, $assinaturaSecretario, "LT", "C", 0, 0);
    $posYinicialPos = $pdf->getY();
    $posXinicialPos = $pdf->getX();
    $pdf->setXY($largura, $posYinicial + 2);
    $pdf->multiCell($largura-10, 4, "\n\n\n$linha\n$diretorNome\nDiretor(a)\n", "RT", "C", 0, 0);
    if ($oGet->exibirAssinaturaAdicional == 't' && !empty($oGet->iAssinaturaAdicional)) {
        $pdf->multiCell($larguraUtil, 4, "\n\n\n$linha\n{$assAdicional->nome}\n{$assAdicional->cargo}\n\n", "RLB", "C", 0, 0);
    } else {
        $pdf->multiCell($larguraUtil, 4, "\n", "RLB", "C", 0, 0);
    }

    $pdf->outPut();
} catch (Exception $e) {
    $sMsg = urlencode($e->getMessage());
    db_redireciona('db_erros.php?fechar=true&db_erro=' . $sMsg);
}

function getBrasao($iTipoBrasao, Instituicao $oInstituicao)
{
    $path = "";
    switch ($iTipoBrasao) {
        case BRASAO_REPUBLICA:
            $path = "imagens/brasaohistoricoescolar.jpeg";
            break;

        case BRASAO_MUNICIPIO:
            $path = "imagens/files/" . $oInstituicao->getImagemLogo();
            break;
    }

    if (!file_exists($path)) {
        $path = "imagens/brasaohistoricoescolar.jpeg";
    }

    return $path;
}
