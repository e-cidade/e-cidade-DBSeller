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

use ECidade\Educacao\Escola\Service\ConteudoDesenvolvidoService;
use ECidade\Educacao\Secretaria\BNCC\Service\HabilidadeEducacaoInfantilService;
use ECidade\Educacao\Secretaria\BNCC\Service\HabilidadeEnsinoFundamentalService;
use ECidade\Educacao\Secretaria\Services\ParametrosGlobaisService;
use ECidade\Enum\Educacao\Escola\TipoEnsinoEnum;
use ECidade\Pdf\Pdf;

require_once(modification("libs/db_sql.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));

$configuracao = ParametrosGlobaisService::get();
$oGet = db_utils::postMemory($_GET);

$oPdf = new Pdf('L');
$oEscola = new Escola($oGet->escola);
$oTurma = TurmaRepository::getTurmaByCodigo($oGet->turma);
$oCalendario = new Calendario($oGet->calendario);
$etapa = EtapaRepository::getEtapaByCodigo($oGet->etapa);

$aDisciplinas = array();
$oGet->disciplinas = trim($oGet->disciplinas);
if (!empty($oGet->disciplinas)) {
    $aDisciplinas = explode(",", trim($oGet->disciplinas));
}

$oPeriodoAvaliacao = null;

$sNomeEscola = $oEscola->getNome();
$iCodigoReferencia = $oEscola->getCodigoReferencia();

if ($iCodigoReferencia != null) {
    $sNomeEscola = "{$iCodigoReferencia} - {$sNomeEscola}";
}
$mostrarHabilidades = false;
if ($oGet->mostrarHabilidades == 'true') {
    $mostrarHabilidades = true;
}

$exibirAnexo = false;
if ($oGet->exibirAnexo == 'true') {
    $exibirAnexo = true;
}

$todosProfessores = false;
if ($oGet->todosProfessores == 'true') {
    $todosProfessores = true;
}

$oDadosCabecalho = new stdClass();
$oDadosCabecalho->sEscola = $sNomeEscola;
$oDadosCabecalho->iAnoExecucao = $oCalendario->getAnoExecucao();
$oDadosCabecalho->sEtapa = $etapa->getNome();
$oDadosCabecalho->sTurma = $oTurma->getDescricao();
$oDadosCabecalho->iTurma = $oTurma->getCodigo();
$oDadosCabecalho->sTurno = $oTurma->getTurno()->getDescricao();
$oDadosCabecalho->sPeriodo = '';
$oDadosCabecalho->iPaginas = $oGet->paginas;
$oDadosCabecalho->lDisciplinas = 'false';
$oDadosCabecalho->sTitulo = "Registro de Aula";
$oDadosCabecalho->bTodosProfessores = $todosProfessores;
if ($oGet->preenchimento == "habilidades") {
    $oDadosCabecalho->sTitulo = "Descrição das Habilidades Desenvolvidas";
}

/**
 * Como o código recebido pela tela, no caso do registro de ocorrência era da AvaliacaoPeroidica
 * e não o próprio PeriodoAvaliação, foi necessário fazer esta validação para buscar o valor correto.
 */
if ($oGet->lRegistroOcorrencia == "true") {
    $oDadosCabecalho->sTitulo = "Registro de Ocorrências";
    $oAvaliacaoPeriodica = new AvaliacaoPeriodica($oGet->periodo);
    $oPeriodoAvaliacao = $oAvaliacaoPeriodica->getPeriodoAvaliacao();
} else {
    $oPeriodoAvaliacao = new PeriodoAvaliacao($oGet->periodo);
}

$oPeriodoCalendario = $oCalendario->getPeriodoCalendarioPorPeriodoAvaliacao($oPeriodoAvaliacao);
$oDadosCabecalho->sPeriodo = $oPeriodoAvaliacao->getDescricao();

$oPdf->init(false);
$oPdf->exibeHeader(false);
$oPdf->SetAutoPageBreak(false);
$oPdf->SetFillColor(230);
/**
 * Percorre as disciplinas selecionadas nos filtros.
 */
$oDadosCabecalho->sNomeProfessor = [];
$oDadosCabecalho->sNomeProfessorInativo = [];

/**
 * Se for selecnado a opçao de imprimir todas disciplinas, ele pega as informaçoes da primeira disciplina e imprime 1 pagina
 */
if (count($aDisciplinas) > 0 && $oDadosCabecalho->lDisciplinas == 'false') {
    foreach ($aDisciplinas as $iRegencia) {
        $oRegencia = RegenciaRepository::getRegenciaByCodigo($iRegencia);
        $oDadosCabecalho->sNomeDisciplina = $oRegencia->getDisciplina()->getNomeDisciplina();
        $oDadosCabecalho->ensinoInfantil = $oRegencia->getEtapa()->getEnsino()->getTipoEnsino()->isInfantil();

        if (count($oRegencia->getDocentesByDataAtual()) > 0) {
            if (count($oRegencia->getDocentes()) > 1) {
                $ind = '';
                $abrePar = '';
                $fechaPar = '';
                foreach ($oRegencia->getDocentes() as $iDocente) {
                    if (!empty($iDocente->getNomeSocial())) {
                        $ind = 1;
                        $abrePar = '(';
                        $fechaPar = ')';
                    }
                }
            }

            foreach ($oRegencia->getDocentesByDataAtual() as $oDocente) {
                if (!array_key_exists($oDadosCabecalho->sNomeDisciplina, $oDadosCabecalho->sNomeProfessor)) {
                    $oDadosCabecalho->sNomeProfessor[$oDadosCabecalho->sNomeDisciplina] = $oDocente->getNome() . $abrePar . $ind . $fechaPar;
                    $oDadosCabecalho->sNomeSocial[$oDadosCabecalho->sNomeDisciplina]    = !empty($oDocente->getNomeSocial()) ? $oDocente->getNomeSocial() . $abrePar . $ind . $fechaPar : '';
                    $ind != '' ? $ind++ : $ind = '';
                    continue;
                }
                // Verificação para saber se a diciplina tem mais de um docente para concatenar com vírgula ou não
                if (count($oRegencia->getDocentes()) > 1) {
                    $oDadosCabecalho->sNomeProfessor[$oDadosCabecalho->sNomeDisciplina] .= ", {$oDocente->getNome()}" . $abrePar . $ind . $fechaPar;
                    $oDadosCabecalho->sNomeSocial[$oDadosCabecalho->sNomeDisciplina]    .= !empty($oDocente->getNomeSocial()) ? ", {$oDocente->getNomeSocial()}" . $abrePar . $ind . $fechaPar : '';
                } else {
                    $oDadosCabecalho->sNomeProfessor[$oDadosCabecalho->sNomeDisciplina] = " {$oDocente->getNome()}";
                    $oDadosCabecalho->sNomeSocial[$oDadosCabecalho->sNomeDisciplina]    = " {$oDocente->getNomeSocial()}";
                }
            }
            // Se houver mais de um docente na disciplina e não tiverem nome social, manda a variável vazia
            if ($oDadosCabecalho->sNomeSocial[$oDadosCabecalho->sNomeDisciplina] == ', ') {
                $oDadosCabecalho->sNomeSocial[$oDadosCabecalho->sNomeDisciplina] = "";
            }
            // Caso não haja nome social em todos, retira a vírgula da concatenação e manda somente o nome
            $nomeSocial = $oDadosCabecalho->sNomeSocial[$oDadosCabecalho->sNomeDisciplina];
            if (strpos($nomeSocial, ',') !== false) {
                $posVirgula = strpos($nomeSocial, ',');
                $firstContent = substr($nomeSocial, 0, $posVirgula);
                $otherContent = substr($nomeSocial, $posVirgula + 1);

                if (empty($firstContent) || empty($otherContent)) {
                    $oDadosCabecalho->sNomeSocial[$oDadosCabecalho->sNomeDisciplina] = str_replace(',', '', $nomeSocial);
                }
            }
        }

        /**
         *  Pega professores Inativos da Regencia
         */
        if ($oDadosCabecalho->bTodosProfessores) {
            $nRegenciaAtual = $oRegencia->getCodigo();
            $daoRegencia = new cl_regenciahorario();
            $sSqlProfessores = $daoRegencia->sql_query_todosProfessoresDaTurma(
                '',
                'distinct  cgmpessoal.z01_nome ',
                '',
                "ed59_i_turma = $oDadosCabecalho->iTurma 
                 AND ed58_i_regencia = $nRegenciaAtual AND ed58_ativo = 'false' ");
            $rsSqlProfessores = db_query($sSqlProfessores);
            $professoresDaTurma = [];
            while ($iLinha = pg_fetch_array($rsSqlProfessores)) {
                $professoresDaTurma[] = $iLinha["z01_nome"];
            }
            $oDadosCabecalho->sNomeProfessorInativo = $professoresDaTurma;
        }

        /**
         * Busca os conteúdos desenvolvidos por disciplina quando for selecionado para ser lançado conforme diário.
         */
        $aConteudoDesenvolvido = array();
        if ($oGet->preenchimento == 'diario') {
            $aConteudoDesenvolvido = buscaConteudoDesenvolvidoDiario($oRegencia, $oPeriodoCalendario, $configuracao);
        }

        switch ($oGet->preenchimento) {
            case "manual":
                imprimeManual($oPdf, $oDadosCabecalho);
                break;
            case "diario":
                imprimeDiario($oPdf, $aConteudoDesenvolvido, $oDadosCabecalho);
                if ($exibirAnexo) {
                    $habilidadesDesenvolvidasPeriodo = buscarDescricaoHabilidades(
                        $oRegencia,
                        $oPeriodoCalendario,
                        $configuracao
                    );
                    imprimeDescritivoHabilidades($oPdf, $habilidadesDesenvolvidasPeriodo, $oDadosCabecalho);
                }
                break;
        }
    }
} else if ($oDadosCabecalho->lDisciplinas == 'true') {
        $iRegencia = $aDisciplinas[0];
        $oRegencia = RegenciaRepository::getRegenciaByCodigo($iRegencia);
        $oDadosCabecalho->sNomeDisciplina = $oRegencia->getDisciplina()->getNomeDisciplina();
        $oDadosCabecalho->ensinoInfantil = $oRegencia->getEtapa()->getEnsino()->getTipoEnsino()->isInfantil();
        if (count($oRegencia->getDocentes()) > 0) {
            foreach ($oRegencia->getDocentes() as $oDocente) {
                $oDadosCabecalho->sNomeProfessor[] = $oDocente->getNome();
                $oDadosCabecalho->sNomeSocial[]    = $oDocente->getNomeSocial();
        }
    }
    imprimeManual($oPdf, $oDadosCabecalho);

} else {
    imprimeManual($oPdf, $oDadosCabecalho);
}

/**
 * Imprime cabecalho do relatório.
 * @param Fpdf $oPdf
 * @param stdClass $oDadosCabecalho dados do cabecalho
 */
function imprimeCabecalho($oPdf, $oDadosCabecalho, $aConteudoDesenvolvido = null)
{
    $aulasDadas = 0;

    if (!is_null($aConteudoDesenvolvido) && !empty($aConteudoDesenvolvido)) {
        $ctd = \App\Domain\Educacao\Escola\Models\ConteudoDesenvolvido::find($aConteudoDesenvolvido[0]->codigoConteudo);
        $service = new \App\Domain\Educacao\Escola\Services\RegistroAulaService();
        $periodo = $service->getPeriodoByConteudo($ctd);
        $procAvaliacao = $service->getProcedimentoByConteudoPeriodo($ctd, $periodo);

        $resultado = \App\Domain\Educacao\Escola\Models\RegenciaPeriodo::where('ed78_i_regencia', $ctd->ed155_regencia)
            ->where('ed78_i_procavaliacao', $procAvaliacao->ed41_i_codigo)
            ->first();

        $aulasDadas = isset($resultado) ? $resultado->ed78_i_aulasdadas : $resultado;
    }


    $oPdf->AddPage();

    $oPdf->SetFont('arial', 'b', 10);
    $alturaInicial = $oPdf->GetY() - 5;
    $oPdf->SetY($alturaInicial);
    $oPdf->Cell(290, 4, mb_strtoupper($oDadosCabecalho->sTitulo) . " - {$oDadosCabecalho->sPeriodo}", 0, 1, "C");
    $oPdf->Cell(290, 4, $oDadosCabecalho->sEscola, 0, 1, "C");
    $alturaFinal = $oPdf->GetY();

    // Impressão da linha abaixo do Título
    $weightLine = 1;
    for ($i = $weightLine; $i <= 3; $i++) {
        $oPdf->Line(10, $alturaFinal + $i/10, 287, $alturaFinal + $i/10);
    }
    $oPdf->Ln();

    $oPdf->SetFont('arial', 'b', 9);
    $oPdf->Cell(20, 4, "Ano Letivo:", 0, 0, "L");
    $oPdf->SetFont('ARIAL', '', 9);
    $oPdf->Cell(20, 4, $oDadosCabecalho->iAnoExecucao, 0, 0, "L");
    $oPdf->SetFont('ARIAL', 'B', 9);
    $oPdf->Cell(12, 4, "Etapa:", 0, 0, "L");
    $oPdf->SetFont('ARIAL', '', 9);
    $oPdf->Cell(58, 4, $oDadosCabecalho->sEtapa, 0, 0, "L");
    $oPdf->SetFont('ARIAL', 'B', 9);
    $oPdf->Cell(12, 4, "Turma:", 0, 0, "L");
    $oPdf->SetFont('ARIAL', '', 9);
    $oPdf->Cell(58, 4, $oDadosCabecalho->sTurma, 0, 0, "L");
    $oPdf->SetFont('ARIAL', 'B', 9);
    $oPdf->Cell(12, 4, "Turno:", 0, 0, "L");
    $oPdf->SetFont('ARIAL', '', 9);
    $oPdf->Cell(58, 4, $oDadosCabecalho->sTurno, 0, 0, "L");
    $oPdf->SetFont('ARIAL', 'B', 9);
    $oPdf->Cell(20, 4, "Aulas Dadas: ", 0, 0, "L");
    if (!is_null($aConteudoDesenvolvido)) {
        $oPdf->SetFont('ARIAL', '', 9);
        $oPdf->Cell(20, 4, "  " .$aulasDadas, 0, 0, "L");
    }
    $oPdf->Ln();

    // Imprimir Disciplina(s)

    if (isset($oDadosCabecalho->sNomeDisciplina)) {
        $label = 'Disciplina';
        $tamanhos = [20, 100, 20, 30];
        if ($oDadosCabecalho->ensinoInfantil) {
            $label = 'Campos de Experiência';
            $tamanhos = [38, 100, 20, 80, 30];
        }
        $oPdf->SetFont('ARIAL', 'B', 9);
        $oPdf->Cell($tamanhos[0], 4, "{$label}:", 0, 0, "L");
        if (isset($oDadosCabecalho->lDisciplinas) && $oDadosCabecalho->lDisciplinas == 'true') {
            $oDadosCabecalho->sNomeDisciplina = "TODAS";
        }
        $oPdf->SetFont('ARIAL', '', 9);
        $oPdf->Cell(290 - $tamanhos[0], 4, $oDadosCabecalho->sNomeDisciplina, 0, 1, "L");
    }

    // Imprime nome do professor e nome social (se tiver)

    $nomeProfessor = isset($oDadosCabecalho->sNomeProfessor) && !empty($oDadosCabecalho->sNomeProfessor) &&
    isset($oDadosCabecalho->sNomeProfessor[$oDadosCabecalho->sNomeDisciplina]) ?
        mb_strimwidth($oDadosCabecalho->sNomeProfessor[$oDadosCabecalho->sNomeDisciplina], 0, 150) : '';

    $nomeProfessorSocial = isset($oDadosCabecalho->sNomeSocial) &&
    isset($oDadosCabecalho->sNomeSocial[$oDadosCabecalho->sNomeDisciplina]) ?
        mb_strimwidth($oDadosCabecalho->sNomeSocial[$oDadosCabecalho->sNomeDisciplina], 0, 150) : '';

    /**
     * Verifica se tem nome social e imprime embaixo do nome civil caso seja true
     */

    if (empty($nomeProfessorSocial)){
        $oPdf->SetFont('ARIAL', 'B', 9);
        $oPdf->Cell(36, 4, "Professor(a): ", 0, 0, "L");
        $oPdf->SetFont('ARIAL', '', 9);
        $oPdf->Cell(500, 4, $nomeProfessor, 0, 1, "L");
    } else {
        $oPdf->SetFont('ARIAL', 'B', 9);
        $oPdf->Cell(36, 4, "Professor(a): ", 0, 0, "L");
        $oPdf->SetFont('ARIAL', '', 9);
        $oPdf->Cell(500, 4, $nomeProfessor, 0, 1, "L");
        // Imprimindo Nome Social
        $oPdf->SetFont('ARIAL', 'B', 9);
        $oPdf->Cell(36, 4, "Nome Social: ", 0, 0, "L");
        $oPdf->SetFont('ARIAL', '', 9);
        $oPdf->Cell(460, 4, trim($nomeProfessorSocial), 0, 1, "L");
    }

    /**
     * Imprime no cabeçalho os nomes dos professores inativos, caso a opção
     * "Exibir Todos os Professores da Regência" esteja ativa.
     */
    if ($oDadosCabecalho->bTodosProfessores && count($oDadosCabecalho->sNomeProfessorInativo) > 0) {
        $aNomeProfessorInativo = $oDadosCabecalho->sNomeProfessorInativo;

        $oPdf->SetFont('ARIAL', 'B', 9);
        $oPdf->Cell(36, 4, "Professor(a) Inativo(a): ", 0, 0, "L");
        $oPdf->SetFont('ARIAL', '', 9);
        $oPdf->Cell(460, 4, implode(", ", $aNomeProfessorInativo), 0, 1, "L");
    }

    $oPdf->Ln();
    $alturaFinal = $oPdf->GetY();

    // Impressão da linha abaixo do Título
    $weightLine = 1;
    for ($i = $weightLine; $i <= 3; $i++) {
        $oPdf->Line(10, $alturaFinal + $i/10, 287, $alturaFinal + $i/10);
    }

    $oPdf->Ln();
}


/**
 * Imprime somente as linhas para lancamento manual.
 * @param Fpdf $oPdf
 * @param stdClass $oDadosCabecalho dados do cabecalho
 * @throws Exception
 */
function imprimeManual($oPdf, $oDadosCabecalho)
{
    global $mostrarHabilidades;
    for ($i = 0; $i < $oDadosCabecalho->iPaginas; $i++) {
        imprimeCabecalho($oPdf, $oDadosCabecalho);

        /**
         * Modifica a impressão do PDF por uma linha quando for true
         */
        $nomeProfessorSocial = $oDadosCabecalho->sNomeSocial[$oDadosCabecalho->sNomeDisciplina];

        /**
         * guarda as posicoes iniciais do eixo x e y antes de comecar a imprimir as linhas.
         */
        $iPosicaoYInicial = $oPdf->GetY();
        empty($nomeProfessorSocial) && $nomeProfessorSocial == ""
        ? $iMaximoLinha = 32
        : $iMaximoLinha = 31;
        /**
         * Conforme layout dividimos cada pagina em duas colunas.
         */
        for ($iColuna = 0; $iColuna < 2; $iColuna++) {
            if ($iColuna == 1) {
                $oPdf->SetY($iPosicaoYInicial);
                $oPdf->SetX(149);
            }

            $oPdf->Cell(14, 5, "Data", 1, 0, "C", 1);
            $oPdf->SetFont('ARIAL', '', 9);
            $oPdf->Cell(125, 5, $oDadosCabecalho->sTitulo, 1, 1, "C", 1);

            for ($iLinha = 0; $iLinha < $iMaximoLinha; $iLinha++) {
                if ($iColuna == 1) {
                    $oPdf->SetX(149);
                }

                $oPdf->Cell(14, 5, "", 1, 0);
                $oPdf->Cell(125, 5, "", 1, 1);
            }
        }
    }
}

/**
 * Imprime cabecalho do relatório.
 * @param Fpdf $oPdf
 * @param stdClass $oDadosCabecalho dados do cabecalho
 */
function imprimeRodape($oPdf, $oDadosCabecalho)
{
    $daoRegencia = new cl_regenciahorario();
    $sSqlProfessores = $daoRegencia->sql_query_todosProfessoresDaTurma('', 'distinct  cgmpessoal.z01_nome ', '', "ed59_i_turma = $oDadosCabecalho->iTurma");
    $rsSqlProfessores = db_query($sSqlProfessores);
    $professoresDaTurma = [];
    while ($iLinha = pg_fetch_array($rsSqlProfessores)) {
        $professoresDaTurma[] = $iLinha["z01_nome"];
    }
    if ($oDadosCabecalho->sTurno == "INTEGRAL") {
    $oPdf->Cell(1, 1, "", 0, 1);
    $oPdf->SetFont('arial', 'b', 6);
    $oPdf->Cell(12, 3, "Legenda:", 0, 0);

    $oPdf->SetFillColor(180);
    $oPdf->Cell(3, 3, "", 1, 0, '', 1);
    $oPdf->Cell(9, 3, " Manhã", 0, 0, '', 0);
    $oPdf->Cell(4, 3, "||", 0, 0, '', 0);

    $oPdf->SetFillColor(225);
    $oPdf->Cell(3, 3, "", 1, 0, '', 1, 1);
    $oPdf->Cell(7, 3, "Tarde", 0, 0, '', 0);
    $oPdf->Cell(4, 3, "||", 0, 0, '', 0);

    $oPdf->Cell(3, 3, "", 1, 0, '', 0);
    $oPdf->Cell(8, 3, "INTEGRAL E/OU SEM TURNO", 0, 0, '', 0);
    }

    $oPdf->Ln();
}

/**
 * Imprime os conteudos desenvolvidos que foram lancados no diario,
 * se nao houver conteudos lancados imprime linhas em branco.
 * @param Fpdf $oPdf
 * @param array $aConteudoDesenvolvido conteudos desenvolvidos lancado no diario
 * @param stdClass $oDadosCabecalho dados cabecalho
 * @throws Exception
 */
function imprimeDiario($oPdf, $aConteudoDesenvolvido, $oDadosCabecalho)
{
    global $mostrarHabilidades;
    $nomeProfessorSocial = isset($oDadosCabecalho->sNomeSocial) &&
        isset($oDadosCabecalho->sNomeSocial[$oDadosCabecalho->sNomeDisciplina]) ?
        $oDadosCabecalho->sNomeSocial[$oDadosCabecalho->sNomeDisciplina] : '';

    for ($i = 0; $i < $oDadosCabecalho->iPaginas; $i++) {
        imprimeCabecalho($oPdf, $oDadosCabecalho, $aConteudoDesenvolvido);

        /**
         * guarda as posicoes iniciais do eixo x e y antes de comecar a imprimir as linhas.
         */
        $iPosicaoYInicial = $oPdf->GetY();
        empty($nomeProfessorSocial) && $nomeProfessorSocial == ""
        ? $iMaximoLinha = 31
        : $iMaximoLinha = 30;
        $iAlturaLinha = 5;
        $iAlturaQuadroImpressao = 200;

        for ($iColuna = 0; $iColuna < 2; $iColuna++) {
            $lPrimeiraColuna = true;

            $iEixoYFimConteudo = 0;
            for ($iLinha = 0; $iLinha < $iMaximoLinha; $iLinha++) {
                if ($lPrimeiraColuna) {
                    $lPrimeiraColuna = false;
                    imprimeCabecalhoColunas($oPdf, $iColuna, $iPosicaoYInicial, $oDadosCabecalho->sTitulo);
                }
                /**
                 * Fazemos a contagem dos turnos, caso houver mais de um ele habilita a opção para colorir
                 */
                $turnos = [];
                foreach ($aConteudoDesenvolvido as $contagemTurno) {
                    if (empty($contagemTurno->turno)) {
                        continue;
                    }
                    $turnos[$contagemTurno->turno] = $contagemTurno->turno;
                }

                $lColorir = count($turnos) > 1;

                foreach ($aConteudoDesenvolvido as $iIndice => $oConteudo) {

                    if ($iColuna == 1) {
                        $oPdf->SetX(149);
                    }
                    $eixoXInicial = $oPdf->GetX();

                    $conteudo = $oConteudo->conteudo;
                    if (!is_null($oConteudo->tipoInstrumento)) {
                        $conteudo .= "\nTipo Instrumento Avaliativo: {$oConteudo->tipoInstrumento->nome}";
                    }
                    if ($mostrarHabilidades && !empty($oConteudo->habilidades)) {
                        $conteudo .= "\nHabilidades: {$oConteudo->habilidades}";
                    }
                    if ($oConteudo->aulasDadas && !empty($oConteudo->aulasDadas)) {
                        $conteudo .= "\nAulas Dadas: {$oConteudo->aulasDadas}";
                    }

                    $iLinhasUtilizadas = $oPdf->NbLines(125, $conteudo);

                    $iAlturaLinhaUtilizada = ($iLinhasUtilizadas * $iAlturaLinha);
                    $iEixoYFimConteudo = $iAlturaLinhaUtilizada + $oPdf->getY();

                    if ($iColuna == 0 && $iEixoYFimConteudo > $iAlturaQuadroImpressao) {
                        $oPdf->setY($iPosicaoYInicial);
                        $iColuna = 1;
                        $lPrimeiraColuna = true;
                        $iLinha = 0;
                        break;
                    }

                    if ($iColuna == 1 && $iEixoYFimConteudo > $iAlturaQuadroImpressao) {
                        $iColuna = 0;
                        $iLinha = 0;
                        imprimeCabecalho($oPdf, $oDadosCabecalho);
                        imprimeCabecalhoColunas($oPdf, $iColuna, $iPosicaoYInicial, $oDadosCabecalho->sTitulo);
                    }

                    $eixoXFinal = $eixoXInicial + 125;


                    $fillPadrao = 225;
                    $color = 255;
                    if ($oConteudo->turno == "MANHÃ") {
                        $color = 180;
                    } elseif ($oConteudo->turno == "TARDE") {
                        $color = 245;
                    }

                    $oPdf->SetFillColor($color);
                    $oPdf->Cell(14, $iAlturaLinhaUtilizada, $oConteudo->data, 1, 0, "C", $lColorir);
                    $oPdf->MultiCell(125, $iAlturaLinha, $conteudo, 1, "L", $lColorir);
                    $oPdf->SetFillColor($fillPadrao);
                    $oPdf->Line($eixoXInicial, $oPdf->GetY(), $eixoXFinal, $oPdf->GetY());
                    $iLinha += $iLinhasUtilizadas;

                    unset($aConteudoDesenvolvido[$iIndice]);
                }
                if ($iColuna == 1) {
                    $oPdf->SetX(149);
                }
                /**
                 * So ira imprimir linhas em branco se nao houver mais conteudo a ser impresso
                 */
                if (count($aConteudoDesenvolvido) == 0) {
                    $oPdf->Cell(14, 5, "", 1, 0);
                    $oPdf->Cell(125, 5, "", 1, 1);
                }
            }

                imprimeRodape($oPdf, $oDadosCabecalho);


        }

        if ($oPdf->GetY() < 199) {
            while ($oPdf->GetY() < 199) {
                $oPdf->SetX(149);
                $oPdf->Cell(14, 5, "", 1, 0);
                $oPdf->Cell(125, 5, "", 1, 1);
            }
        }
    }
}

/**
 * Busca conteudo desenvolvido lancado no Diario de Classe para a regencia
 * @param Regencia $oRegencia
 * @param PeriodoCalendario $oPeriodoCalendario
 * @param $configuracao
 * @return array:stdClass
 * @throws ParameterException
 */
function buscaConteudoDesenvolvidoDiario($oRegencia, $oPeriodoCalendario, $configuracao)
{
    $sDataInicial = $oPeriodoCalendario->getDataInicio()->convertTo(DBDate::DATA_EN);
    $sDataFinal = $oPeriodoCalendario->getDataTermino()->convertTo(DBDate::DATA_EN);


    $conteudoDesenvolvidoService = new ConteudoDesenvolvidoService();
    $conteudos = $conteudoDesenvolvidoService->buscarConteudoPeriodo($oRegencia, $sDataInicial, $sDataFinal);
    $conteudoDesenvolvido = array();
    foreach ($conteudos as $conteudo) {
        $habilidades = [];
        foreach ($conteudo->getHabilidades() as $habilidadeDesenvolvida) {
            if ($configuracao->isReferencialCurricularEstadual()) {
                $habilidadesDesenvolvidasReferencial = $habilidadeDesenvolvida->getHabilidadesReferencial();
                foreach ($habilidadesDesenvolvidasReferencial as $habilidadeDesenvolvidaReferencial) {
                    $habilidades[] = $habilidadeDesenvolvidaReferencial->getReferencialCurricular()
                        ->getCodigoReferencial();
                }
            } else {
                $habilidades[] = $habilidadeDesenvolvida->getCodigoHabilidade();
            }
        }

        // Pega o codigo do Turno referente e procura pela descrição do turno //
        $codigoTurno = $conteudo->getTurno();
        if ($codigoTurno != null) {
            $turnoReferente = new cl_turmaturnoreferente();
            $sSqlTurmaTurnoReferente = $turnoReferente->sql_query_file(null, '*', '', "ed336_codigo = $codigoTurno");
            $rsSqlTurmaTurnoReferente = $turnoReferente->sql_record($sSqlTurmaTurnoReferente);
            $iLinhas = pg_num_rows($rsSqlTurmaTurnoReferente);
            for ($i = 0; $i < $iLinhas; $i++) {
                $turmaTurnoReferente = db_utils::fieldsmemory($rsSqlTurmaTurnoReferente, $i);
            }
            $turnoTurma = TurnoRepository::getTurnoByCodigo($turmaTurnoReferente->ed336_turnoreferente);
            $turnoDescricao = $turnoTurma->getDescricao();
        } else {
            $turnoDescricao = '';
        }

        $conteudoDesenvolvido[] = (object)[
            'data' => $conteudo->getData()->format("d/m/Y"),
            'codigoConteudo' => $conteudo->getCodigo(),
            'conteudo' => $conteudo->getConteudo(),
            'habilidades' => implode(', ', array_unique($habilidades)),
            'turno' => $turnoDescricao,
            'tipoInstrumento' => $conteudo->getTipoInstrumentoAvaliativo(),
            'aulasDadas' => $conteudo->getAulasDadas(),
            'regencia' => $oRegencia->getCodigo()
        ];
    }

    return $conteudoDesenvolvido;
}

/**
 * Buscar habilidades lançadas no diário
 * @param Regencia $oRegencia
 * @param $oPeriodoCalendario
 * @param $configuracao
 * @return array []
 * @throws Exception
 */
function buscarDescricaoHabilidades($oRegencia, $oPeriodoCalendario, $configuracao)
{
    $sDataInicial = $oPeriodoCalendario->getDataInicio()->convertTo(DBDate::DATA_EN);
    $sDataFinal = $oPeriodoCalendario->getDataTermino()->convertTo(DBDate::DATA_EN);

    $conteudoDesenvolvidoService = new ConteudoDesenvolvidoService();
    $conteudos = $conteudoDesenvolvidoService->buscarConteudoPeriodo($oRegencia, $sDataInicial, $sDataFinal);

    $habilidadesDesenvolvidas = [];
    foreach ($conteudos as $conteudo) {
        foreach ($conteudo->getHabilidades() as $habilidadeDesenvolvida) {
            $data = $habilidadeDesenvolvida->getConteudoDesenvolvido()->getData();
            $anoHabilidade = $data->format('Y');
            $data = $data->format('d/m/Y');

            if ($configuracao->isReferencialCurricularEstadual()) {
                $habilidadesDesenvolvidasReferencial = $habilidadeDesenvolvida->getHabilidadesReferencial();
                foreach ($habilidadesDesenvolvidasReferencial as $habilidadeDesenvolvidaReferencial) {
                    $referencialCurricularEstadual = $habilidadeDesenvolvidaReferencial->getReferencialCurricular();
                    $habilidade = (object)[
                        'codigo' => $referencialCurricularEstadual->getCodigoReferencial(),
                        'descricao' => $referencialCurricularEstadual->getHabilidade()
                    ];

                    if (!array_key_exists($data, $habilidadesDesenvolvidas)) {
                        $habilidadesDesenvolvidas[$data] = [];
                    }
                    array_push($habilidadesDesenvolvidas[$data], $habilidade);
                }
            } else {
                $ensino = $habilidadeDesenvolvida->getDisciplina()->getEnsino()->getTipoEnsino()->getValue();
                if ($ensino === TipoEnsinoEnum::ENSINO_INFANTIL) {
                    $habilidadeEducacaoInfantilService = new HabilidadeEducacaoInfantilService(
                        $configuracao,
                        $anoHabilidade
                    );
                    $habilidadeBncc = $habilidadeEducacaoInfantilService->getHabilidade($habilidadeDesenvolvida);
                } else {
                    $habilidadeEnsinoFundamentalService = new HabilidadeEnsinoFundamentalService(
                        $configuracao,
                        $anoHabilidade
                    );

                    $habilidadeBncc = $habilidadeEnsinoFundamentalService->getHabilidade($habilidadeDesenvolvida);
                }

                $habilidade = null;
                if(!empty($habilidadeBncc)){
                    $habilidade = (object)[
                        'codigo' => $habilidadeBncc->getCodigo(),
                        'descricao' => $habilidadeBncc->getHabilidade()
                    ];
                }

                if (!array_key_exists($data, $habilidadesDesenvolvidas)) {
                    $habilidadesDesenvolvidas[$data] = [];
                }

                array_push($habilidadesDesenvolvidas[$data], $habilidade);
            }
        }
    }

    return $habilidadesDesenvolvidas;
}

function removerCodigo($codigo, $descricao)
{
    $string = "(" . $codigo . ") ";
    $descricao = str_replace($string, '', $descricao);
    return $descricao;
}

/**
 * @param $oPdf
 * @param $habilidadesDesenvolvidasPeriodo
 * @param $oDadosCabecalho
 * @throws Exception
 */
function imprimeDescritivoHabilidades($oPdf, $habilidadesDesenvolvidasPeriodo, $oDadosCabecalho)
{
    if (empty($habilidadesDesenvolvidasPeriodo)) {
        return;
    }

    $cabecalhoHabilidades = (object)[
        "sEscola" => $oDadosCabecalho->sEscola,
        "iAnoExecucao" => $oDadosCabecalho->iAnoExecucao,
        "sEtapa" => $oDadosCabecalho->sEtapa,
        "sTurma" => $oDadosCabecalho->sTurma,
        "sTurno" => $oDadosCabecalho->sTurno,
        "sPeriodo" => $oDadosCabecalho->sPeriodo,
        "iPaginas" => $oDadosCabecalho->iPaginas,
        "sTitulo" => "Descritivo de Habilidades/Objetivos de Aprendizagem",
        "sNomeDisciplina" => $oDadosCabecalho->sNomeDisciplina,
        "sNomeProfessor" => $oDadosCabecalho->sNomeProfessor,
        "ensinoInfantil" => $oDadosCabecalho->ensinoInfantil,
    ];

    imprimeCabecalho($oPdf, $cabecalhoHabilidades);
    imprimeCabecalhoHabilidades($oPdf);

    $maximoLinhas = 28;
    $linhasUtilizadas = 0;
    $posicaoInicial = $oPdf->getY();
    $posicaoFinal = $posicaoInicial;

    foreach ($habilidadesDesenvolvidasPeriodo as $data => $habilidades) {
        $habilidades = array_unique($habilidades, SORT_REGULAR);
        $linhasUtilizadasData = 0;

        if ($posicaoFinal > 179) {
            $oPdf->setY($posicaoInicial);
        }

        foreach ($habilidades as $habilidade) {
            $habilidade->descricao = removerCodigo($habilidade->codigo, $habilidade->descricao);
            $habilidade->linhasUtilizadas = $oPdf->NbLines(238, $habilidade->descricao);

            $linhasUtilizadas += $habilidade->linhasUtilizadas;
            $linhasUtilizadasData += $habilidade->linhasUtilizadas;

            // Impressão das habilidades
            $oPdf->setX(30);
            $oPdf->Cell(20, ($habilidade->linhasUtilizadas * 5), $habilidade->codigo, 1, 0, "C", 0);
            $oPdf->MultiCell(238, 5, $habilidade->descricao, 1, "L", 0);
            $posicaoFinal = $oPdf->getY();

            // Verifica se a altura máxima de linhas foi atingida
            if ($linhasUtilizadas >= $maximoLinhas) {
                // Impressão da data
                $oPdf->setY($posicaoFinal - ($linhasUtilizadasData * 5));
                $oPdf->setX(10);
                $oPdf->Cell(20, ($linhasUtilizadasData * 5), $data, 1, 0, "C", 0);

                // Reinicializa variáveis
                $linhasUtilizadas = 0;
                $linhasUtilizadasData = 0;

                imprimeCabecalho($oPdf, $oDadosCabecalho);
                imprimeCabecalhoHabilidades($oPdf);
                $oPdf->setY($posicaoInicial);
            }
        }

        if ($posicaoFinal < 179) {
            // Impressão da data para a última seção
            $oPdf->setY($posicaoFinal - ($linhasUtilizadasData * 5));
            $oPdf->setX(10);
            $oPdf->Cell(20, $linhasUtilizadasData * 5, $data, 1, 0, "C", 0);
            $oPdf->setY($posicaoFinal);
        }
    }
}

/**
 * Imprime cabecalho do relatório por colunas.
 * @param Fpdf $oPdf
 * @param int $iColuna
 * @param int $iPosicaoYInicial
 * @param string $sTitulo
 */
function imprimeCabecalhoColunas($oPdf, $iColuna, $iPosicaoYInicial, $sTitulo)
{
    global $mostrarHabilidades;
    if ($iColuna == 1) {
        $oPdf->SetY($iPosicaoYInicial);
        $oPdf->SetX(149);
    }

    $oPdf->SetFont('arial', 'b', 7);
    $oPdf->Cell(14, 5, "Data", 1, 0, "C", 1);
    $oPdf->Cell(125, 5, $sTitulo, 1, 1, "C", 1);
    $oPdf->SetFont('arial', '', 7);
}

/**
 * @param $oPdf
 */
function imprimeCabecalhoHabilidades($oPdf)
{
    $oPdf->SetFont('arial', 'b', 7);
    $oPdf->Cell(20, 5, "Data", 1, 0, "C", 1);
    $oPdf->Cell(20, 5, "Código", 1, 0, "C", 1);
    $oPdf->Cell(238, 5, "Descrição Habilidades / Objetivos de Aprendizagem", 1, 1, "C", 1);
    $oPdf->SetFont('arial', '', 7);
}

$oPdf->Output();
