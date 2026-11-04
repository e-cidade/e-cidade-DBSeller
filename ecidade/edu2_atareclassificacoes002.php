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
    select 
    trocaserie.ed101_i_codigo as codigo_reclassificacao,  
    aluno.ed47_i_codigo, ed47_v_nome,
    trim(ed47_v_nome) as nome_completo_aluno, 
    disciplina.ed12_i_codigo as codigo, 
    trim(ed232_c_descr) as nome_disciplina,
    ed232_c_abrev,
    ed335_avaliacao as resultado, avaliacaoclassificacao.ed335_sequencial
    from trocaserie  
    inner join turma as origem  on origem.ed57_i_codigo = trocaserie.ed101_i_turmaorig  
    inner join escola on escola.ed18_i_codigo = origem.ed57_i_escola  
    inner join db_depart on db_depart.coddepto = escola.ed18_i_codigo  
    inner join db_config on db_config.codigo = db_depart.instit  
    inner join aluno on aluno.ed47_i_codigo = trocaserie.ed101_i_aluno  
    inner join turma as destino on destino.ed57_i_codigo = trocaserie.ed101_i_turmadest  
    inner join base on base.ed31_i_codigo = destino.ed57_i_base  
    inner join cursoedu on cursoedu.ed29_i_codigo = base.ed31_i_curso  
    inner join ensino on ensino.ed10_i_codigo = cursoedu.ed29_i_ensino
    left join avaliacaoclassificacao on avaliacaoclassificacao.ed335_trocaserie = trocaserie.ed101_i_codigo
    left join disciplina on disciplina.ed12_i_codigo = avaliacaoclassificacao.ed335_disciplina
    left join caddisciplina on caddisciplina.ed232_i_codigo = disciplina.ed12_i_caddisciplina
    left join regencia on regencia.ed59_i_turma = origem.ed57_i_codigo and regencia.ed59_i_disciplina = disciplina.ed12_i_codigo
    where escola.ed18_i_codigo = {$oGet->escola} and origem.ed57_i_calendario = {$oGet->calendario} and aluno.ed47_i_codigo in ({$oGet->alunos})
    and trocaserie.ed101_i_codigo = trocaserie.ed101_i_codigo
    order by trim(ed47_v_nome)
    ";

    $rsGrade = db_query($sqlGrade);
    $linhas = pg_num_rows($rsGrade);
    $listaDisciplinas = [];
    $resultadosDisciplinas = [];
    for ($i = 0; $i < pg_num_rows($rsGrade); $i++) {
        $dadosDisciplinas = db_utils::fieldsMemory($rsGrade, $i);
        $disciplinaTemp = new stdClass();
        if (!key_exists($dadosDisciplinas->codigo, $listaDisciplinas) && !empty($dadosDisciplinas->codigo)) {
            $disciplinaTemp->codigo = $dadosDisciplinas->codigo;
            $disciplinaTemp->nome = $dadosDisciplinas->nome_disciplina;
            $disciplinaTemp->nomeAbreviado = $dadosDisciplinas->ed232_c_abrev;
            $listaDisciplinas[$dadosDisciplinas->codigo] = $disciplinaTemp;
        }

        $chave = "{$dadosDisciplinas->ed47_i_codigo}#$dadosDisciplinas->codigo";
        $dadosTemp = new stdClass();
        $dadosTemp->codigoDisciplina = $dadosDisciplinas->codigo;
        $dadosTemp->nomeDisciplina = $dadosDisciplinas->nome_disciplina;
        $dadosTemp->nomeAbreviadoDisciplina = $dadosDisciplinas->ed232_c_abrev;
        $dadosTemp->resultadoObtidoDisciplina = $dadosDisciplinas->resultado;
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
    ed217_exibir_coluna_disciplinas, ed217_exibir_coluna_resultado_final, 
    ed217_exibir_brasao, ed217_disposicao_brasao, ed217_disposicao_cabecalho, 
    ed217_brasao
    ";
    $sSqlEduRelatModel  = $oDaoEduRelatModel->sql_query(
        "",
        $sCamposRelatModel,
        "",
        "ed217_i_codigo = {$oGet->modeloAtaId}"
    );
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
    $observacaoModelo = $oDadosRelatModel->ed217_t_obs != ""
        ? $oDadosRelatModel->ed217_t_obs
        : "";

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
    $espacamentoLn = 1.3;
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
    $pdf->MultiCell($larguraUtil, 7, "Ata de Resultados dos Exames de Reclassificação do Ano Letivo de {$dataPartes[2]}.", 1, "C", 0);
    $pdf->ln($espacamentoLn);
    $pdf->SetFont('Arial', '', 8);
    $pdf->MultiCell($larguraUtil, 4, "Aos {$dataPartes[0]} dias do mês de {$meses[$dataPartes[1]-1]} do ano de {$dataPartes[2]} a {$escolaNome}, amparada pela LDB nº 9.394/96, art. 24, inciso II, alínea 'C', pela Resolução nº 01/CME-06 e pela Portaria nº 48/2023/ ASTEC/GAB/SEMED, realizou os Exames de Reclassificação referentes ao {$identificacaoAnoTurna} ano do Ensino Fundamental, dos(a) alunos(a) abaixo relacionado(s) .", 1, "J", 0);
    $pdf->ln($espacamentoLn);

    $larguraColunaNum = 7;
    $larguraColunaRF = 7;
    $larguraColunaNome = 80;
    $larguraColunaDisciplinas = 13;

    $qtdDisciplinas = count($listaDisciplinas);
    $complementoColunaNome = $qtdDisciplinas * $larguraColunaDisciplinas;
    $larguraColunaNome = $larguraUtil - $complementoColunaNome - $larguraColunaNum - $larguraColunaRF;

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($larguraColunaNum, 4, "Nº", 1, 0, 'C', 0, '');
    $pdf->cell($larguraColunaNome, 4, "Nome do(a) aluno(a)", 1, 0, 'C', 0, '');
    foreach ($listaDisciplinas as $disciplina) {
        $pdf->cell($larguraColunaDisciplinas, 4, $disciplina->nomeAbreviado, 1, 0, 'C', 0, '');
    }
    $pdf->cell($larguraColunaRF, 4, "RF", 1, 1, 'C', 0, '');

    $contadorAlunos = 1;
    $alunosImpressos = [];
    $pdf->SetFont('Arial', '', 7);
    for ($i = 0; $i < pg_num_rows($rsGrade); $i++) {
        $dadosGrade = db_utils::fieldsMemory($rsGrade, $i);
        if (!in_array($dadosGrade->ed47_i_codigo, $alunosImpressos)) {
            $pdf->cell($larguraColunaNum, 4, $contadorAlunos, 1, 0, 'C', 0, '');
            $pdf->cell($larguraColunaNome, 4, substr($dadosGrade->nome_completo_aluno, 0, 45), 1, 0, 'L', 0, '');
            
            foreach ($listaDisciplinas as $disciplina) {
                if ($dadosGrade->ed335_sequencial == null) {
                    $pdf->cell($larguraColunaDisciplinas, 4, "RECLASS", 1, 0, 'C', 0, '');
                    continue;
                }
                foreach ($resultadosDisciplinas as $key => $resultadoDisc) {
                    if ("{$dadosGrade->ed47_i_codigo}#{$disciplina->codigo}" == $key) {
                        $pdf->cell($larguraColunaDisciplinas, 4, $resultadoDisc->resultadoObtidoDisciplina, 1, 0, 'C', 0, '');
                    }
                }
            }
            $pdf->cell($larguraColunaRF, 4, "A", 1, 1, 'C', 0, '');
            $contadorAlunos++;
        }
        
        $alunosImpressos[] = $dadosGrade->ed47_i_codigo;
    }
    $pdf->ln($espacamentoLn);

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
        $pdf->ln($espacamentoLn);
    }
    $pdf->MultiCell($larguraUtil, 4, "E para constar, lavrei a presente Ata, que vai por mim assinada e pelo(a) diretor(a) da Escola.", 1, "L", 0);
    $pdf->ln($espacamentoLn);
    $pdf->MultiCell($larguraUtil, 5, $localData, 1, "C", 0);
    $posYinicial = $pdf->getY();
    $posXinicial = $pdf->getX();
    $pdf->setXY($posXinicial, $posYinicial + 2);
    $assinaturaSecretario = "\n\n$linha\n$secretarioNome\nSecretário(a)\n\n";
    $assinaturaDiretor = "\n\n$linha\nDiretor(a)\n\n";
    if ($oGet->exibirAssinaturaSecretario == 'false') {
        $assinaturaSecretario = "\n\n \n \n \n\n";
    }
    $pdf->multiCell($largura-10, 4, $assinaturaSecretario, "LT", "C", 0, 0);
    $posYinicialPos = $pdf->getY();
    $posXinicialPos = $pdf->getX();
    $pdf->setXY($largura, $posYinicial + 2);
    $pdf->multiCell($largura-10, 4, "\n\n$linha\n$diretorNome\nDiretor(a)\n\n", "RT", "C", 0, 0);
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
