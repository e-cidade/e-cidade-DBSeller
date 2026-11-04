<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2013  DBselller Servicos de Informatica
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

require_once modification("fpdf151/pdfwebseller.php");
require_once modification("libs/db_stdlibwebseller.php");
require_once modification("libs/db_utils.php");
require_once modification("libs/JSON.php");
require_once modification("libs/db_libdocumento.php");
require_once modification("std/db_stdClass.php");
require_once modification("dbforms/db_funcoes.php");
require_once(modification("model/educacao/ArredondamentoNota.model.php")); //2018-06-20 Wallace  Apenas para exportar
require_once(modification("model/educacao/DBEducacaoTermo.model.php"));//2018-06-20 Wallace  Apenas para exportar
require_once(modification("libs/db_libparagrafo.php"));//2018-06-20 Wallace  Apenas para exportar

// 2018-06-18 Wallace(ATMA) Esta página foi pega como template para a criação do relatório da página declaração, suas modificações serão feitas nos arquivos da CAMADA_FHSYS

$oDaoPeriodoEscola = new cl_periodoescola;

$oJson = new services_json();
$oParametros = new stdClass();
$oGet = db_utils::postMemory($_GET);



if(mb_detect_encoding($oGet->sDiretor.'x', 'UTF-8','ISO-8859-1') == 'UTF-8' ){
    $oGet->sDiretor =  utf8_decode($oGet->sDiretor);
}


$oParametros->aMatriculas = $oJson->decode(str_replace("\\", "", $oGet->aMatriculas));
$aDiretor = explode('|', $oGet->sDiretor);
$oParametros->sDiretor = '';
$oParametros->sCargo = '';
$oParametros->lTemDiretor = false;

$tamanhoFonte = 12;



/**
 * Verifica se foi informado diretor
 */
if (count($aDiretor) > 1) {

    $oParametros->sDiretor = $aDiretor[1];
    $oParametros->sCargo = $aDiretor[0];
    if (isset($aDiretor[2]) && !empty($aDiretor[2])) {
        $oParametros->sCargo .= "({$aDiretor[2]})";
    }
    $oParametros->lTemDiretor = true;
}

$oParametros->lExibeGradeAluno = $oGet->lExibeGradeAluno == 'S' ? true : false;
$oParametros->iAlturaLinha = 6;


$oParametros->sObservacao = "";

if (trim($oGet->sObservacao) != '') {

    $oParametros->sObservacao = trim(db_stdClass::db_stripTagsJsonSemEscape($oGet->sObservacao));

    if (mb_detect_encoding($oGet->sObservacao . 'x', 'UTF-8', 'ISO-8859-1') == 'UTF-8') {
        $oParametros->sObservacao = utf8_decode(trim(db_stdClass::db_stripTagsJsonSemEscape($oGet->sObservacao)));// Wallace 2018-06-18 Convertendo para ISO

    }


}

$oTurma = TurmaRepository::getTurmaByCodigo($oGet->iTurma);
$aTurno = array();

$aTurno[] = $oTurma->getTurno()->getCodigoTurno();
if ($oTurma->temTurnoAdicional() != "") {
    $aTurno[] = $oTurma->getTurnoAdicional()->getCodigoTurno();
}

$sCamposHorarioTurno = "min(ed17_h_inicio) as hora_inicio, max(ed17_h_fim) as hora_fim";
$sWhereHorarioTurno = "     ed17_i_escola = {$oTurma->getEscola()->getCodigo()}";
$sWhereHorarioTurno .= " and ed17_i_turno in(" . implode(',', $aTurno) . ")";
$sSqlHorarioTurno = $oDaoPeriodoEscola->sql_query(null, $sCamposHorarioTurno, null, $sWhereHorarioTurno);
$rsHorarioTurno = $oDaoPeriodoEscola->sql_record($sSqlHorarioTurno);

if ($oDaoPeriodoEscola->numrows == 0) {
    db_redireciona("db_erros.php?fechar=true&db_erro=" . _M('educacao.escola.edu2_atestadofrequencia.horario_turma_nao_encontrado'));
}
$oDadosHorarioTurno = db_utils::fieldsMemory($rsHorarioTurno, 0);


$aGradeHorario = array();

if ($oParametros->lExibeGradeAluno) {

    $sCamposGradeHorario = "ed17_i_turno, ed17_i_periodoaula, ed17_h_inicio, ed17_h_fim, ed15_c_nome ";
    $sSqlGradeHorario = $oDaoPeriodoEscola->sql_query("", $sCamposGradeHorario, "ed15_i_sequencia,ed08_i_sequencia", $sWhereHorarioTurno);
    $rsGradeHorario = $oDaoPeriodoEscola->sql_record($sSqlGradeHorario);
    $iLinhas = $oDaoPeriodoEscola->numrows;

    if ($iLinhas == 0) {
        db_redireciona("db_erros.php?fechar=true&db_erro=" . _M('educacao.escola.edu2_atestadofrequencia.grade_horario_nao_encontrada'));
    }

    for ($i = 0; $i < $iLinhas; $i++) {

        $oDadosGradeHorario = db_utils::fieldsMemory($rsGradeHorario, $i);
        $oGradeHorario = new stdClass();


        $oGradeHorario->iPeriodo = $oDadosGradeHorario->ed17_i_periodoaula;
        $oGradeHorario->sTurno = $oDadosGradeHorario->ed15_c_nome;
        $oGradeHorario->sHoraInicio = $oDadosGradeHorario->ed17_h_inicio;
        $oGradeHorario->sHoraFim = $oDadosGradeHorario->ed17_h_fim;
        $oGradeHorario->lPrincipal = true;

        if ($oTurma->getTurno()->getCodigoTurno() != $oDadosGradeHorario->ed17_i_turno) {
            $oGradeHorario->lPrincipal = false;
        }

        $aGradeHorario[] = $oGradeHorario;
    }
}


$aParagrafos = array();
$aDadosAlunos = array();

foreach ($oParametros->aMatriculas as $oMat) {


    $oParagrafo = new libdocumento(5028); // modelo Declaração de matrícula
//  $oParagrafo                         = new libdocumento(5009); // modelo atestado de Frequência

    $oMatricula = new Matricula($oMat->iMatricula);
    $oGrade = new GradeAproveitamentoAluno($oMatricula); //2018-06-20 Buscar todas as informações referentes ao aluno e seu diario de classe
    $oGrade->getResultadoFinalAluno(); //2018-06-20 Buscar a Inicial do Resultado Final, esta chamada é essencial para retornaAndamentoDaMatricula() funcionar
    $oGrade->getMatricula()->retornaAndamentoDaMatricula(); //2018-06-20 Retorna a situação so aluno de forma textual completa

    try {

        $oDataNascimento = new DBDate($oMatricula->getAluno()->getDataNascimento());
        $oParagrafo->dia_nascimento = $oDataNascimento->getDia();
        $oParagrafo->mes_extenso_nascimento = DBDate::getMesExtenso((int)$oDataNascimento->getMes());
        $oParagrafo->mes_numeral_nascimento = $oDataNascimento->getMes();
        $oParagrafo->ano_nascimento = $oDataNascimento->getAno();
    } catch (Exception $oErro) {

        $oParagrafo->dia_nascimento = "";
        $oParagrafo->mes_extenso_nascimento = "";
        $oParagrafo->mes_numeral_nascimento = "";
        $oParagrafo->ano_nascimento = "";
    }
    $aFiliacao = array();

    if ($oMatricula->getAluno()->getNomeMae() != '') {
        $aFiliacao[] = $oMatricula->getAluno()->getNomeMae();
    }
    if ($oMatricula->getAluno()->getNomePai() != '') {
        $aFiliacao[] = $oMatricula->getAluno()->getNomePai();
    }

    $oParagrafo->naturalidade         = $oMatricula->getAluno()->getNaturalidade()->getNome();
    $oParagrafo->estado_naturalidade  = "";
    $oParagrafo->uf_naturalidade      = "";

    if (!empty($oParagrafo->naturalidade)) {

        $oParagrafo->estado_naturalidade = $oMatricula->getAluno()->getNaturalidade()->getUF()->getNomeEstado();
        $oParagrafo->uf_naturalidade     = $oMatricula->getAluno()->getNaturalidade()->getUF()->getUF();
    }

    $oParagrafo->aluno        = $oMatricula->getAluno()->getNome();
    $oParagrafo->filiacao     = implode(' e ', $aFiliacao);
    $oParagrafo->etapa        = $oMatricula->getEtapaDeOrigem()->getNome();
    $oParagrafo->turma        = $oMatricula->getTurma()->getDescricao();
    $oParagrafo->curso        =  $oMatricula->getTurma()->getBaseCurricular()->getCurso()->getNome();
    $oParagrafo->turno        = $oMatricula->getTurma()->getTurno()->getDescricao();
    $oParagrafo->hora_inicial = $oDadosHorarioTurno->hora_inicio;
    $oParagrafo->hora_final   = $oDadosHorarioTurno->hora_fim;
    $oParagrafo->ano_declaracao        = $oMatricula->getTurma()->getCalendario()->getAnoExecucao();
    if(substr($oGrade->getMatricula()->retornaAndamentoDaMatricula(),0,11) == "TRANSFERIDO" ){ //2018-06-20 Se a primeira palavra for transferido entrar na condição
        $oParagrafo->situacao_aluno = " tendo sido  TRANSFERIDO(a)."; //2018-06-20 Criado para enviar a informação para a variável que será recebida como parâmetro no Sistema E-cidade
    }else {
        $oParagrafo->situacao_aluno = $oGrade->getMatricula()->retornaAndamentoDaMatricula() == "EM ANDAMENTO" ? "." : ", tendo sido " . $oGrade->getMatricula()->retornaAndamentoDaMatricula() . "(a)."; //2018-06-20 Se em andamento não imprimir este status
    }
    if(($oGrade->getMatricula()->retornaAndamentoDaMatricula() == "EM ANDAMENTO") && (substr($oGrade->getMatricula()->getSituacao(),0,10) == "DESISTENTE" )) {
        $oParagrafo->situacao_aluno = " tendo sido DESISTENTE.";
    }
    if(($oGrade->getMatricula()->retornaAndamentoDaMatricula() == "INFREQUENTE")) {
        $oParagrafo->situacao_aluno = " tendo sido INFREQUENTE";
    }

    $aParagrafos[]            = $oParagrafo->getDocParagrafos();

    $oDadosAlunos = new stdClass();
    $oDadosAlunos->aParagrafo           = $oParagrafo->getDocParagrafos();
    $oDadosAlunos->sObservacaoMatricula = $oMatricula->getObservacao();
    $aDadosAlunos[] = $oDadosAlunos;

}

if (count($aParagrafos) == 0) {
    db_redireciona("db_erros.php?fechar=true&db_erro=" . _M('educacao.escola.edu2_atestadofrequencia.matricula_nao_encontrada'));
}

$oPdf = new PDF();
$oPdf->AliasNbPages();
$oPdf->setFillColor(220);
$oPdf->Open();
$oPdf->SetAutoPageBreak(false, 10);
$head1 = "DECLARAÇÃO"; // 2018-06-18 Wallace (ATMA) Trocar titulo, Atestado de Frequência para Declaração

if (db_getsession("DB_modulo") != 1100747) {

    $aTelefones = $oTurma->getEscola()->getTelefones();
    $head2 = "Escola: {$oTurma->getEscola()->getNome()}";

    if (count($aTelefones) > 0) {

        $head3 = "Telefone: {$aTelefones[0]->iDDD} {$aTelefones[0]->iNumero}";
    }

}

$sObservacao = $oParametros->sObservacao;
foreach ($aDadosAlunos as $oDadosAlunos) {

    $oPdf->addpage("P");

    $sTexto = $oDadosAlunos->aParagrafo[1]->oParag->db02_texto;

    $oPdf->setfont('arial', 'b', $tamanhoFonte+1);
    $oPdf->SetY($oPdf->getY() + 10);

    $oPdf->Ln($oParametros->iAlturaLinha * 6); // 2018-06-19 Wallace (ATMA) Descer o título para o meio da página.
    $oPdf->Cell(192, $oParametros->iAlturaLinha, "Declaração", 0, 1, "C"); // 2018-06-19 Wallace Trocar titulo, Atestado de Frequência para Declaração
    $oPdf->Ln($oParametros->iAlturaLinha * 2);

    $oPdf->setfont('arial', '', $tamanhoFonte);
    $oPdf->setXY(16, $oPdf->GetY());
    $oPdf->multicell(180, $oParametros->iAlturaLinha, $sTexto, 0, "J", 0, 0);
    $oPdf->Ln($oParametros->iAlturaLinha * 2);
    $oPdf->setXY(16, $oPdf->GetY());

    $oPdf->setfont('arial','',$tamanhoFonte -2);

    $oParametros->sObservacao = '';
    if (!empty($oDadosAlunos->sObservacaoMatricula)) {
        $oParametros->sObservacao = "{$oDadosAlunos->sObservacaoMatricula}\n{$sObservacao}";
    } else if (empty($sObservacao)) {
        $oParametros->sObservacao = "..........................................................";
    } else {
        $oParametros->sObservacao = $sObservacao;
    }

    $oPdf->multicell(180, $oParametros->iAlturaLinha, "OBS.: {$oParametros->sObservacao}", 0, "J", 0, 0);

    $oPdf->Ln($oParametros->iAlturaLinha * 2);
    if ($oParametros->lExibeGradeAluno) {

        /**
         * Calculamos se a grade de de horário do aluno caberá na página atual.
         */
        if (((count($aGradeHorario) * $oParametros->iAlturaLinha) + $oPdf->GetY() + 10) > $oPdf->h - 20) {
            $oPdf->AddPage();
        }

        $oPdf->ln($oParametros->iAlturaLinha);
        $oPdf->setX(80);// 2018-06-18 Wallace Trocamos de 85 para 80
        $oPdf->setfont('arial','b',$tamanhoFonte)-2;
        $oPdf->Cell(50, $oParametros->iAlturaLinha-2, "TURNO PRINCIPAL", 1, 1, "C", 1);
        $lImprimeTurno = true;
        foreach ($aGradeHorario as $oGradeHorario) {

            $sString = "{$oGradeHorario->iPeriodo}º - {$oGradeHorario->sHoraInicio} / {$oGradeHorario->sHoraFim}";
            $oPdf->setfont('arial','',$tamanhoFonte-2);

            if ($oGradeHorario->lPrincipal) {

                if ($lImprimeTurno) {

                    $oPdf->setX(80);// 2018-06-18 Wallace Trocamos de 85 para 80
                    $oPdf->Cell(50, $oParametros->iAlturaLinha -2, $oGradeHorario->sTurno, 1, 1, "C", 1);
                    $lImprimeTurno = false;
                }
                $oPdf->setX(80);// 2018-06-18 Wallace Trocamos de 85 para 80
                $oPdf->Cell(50, $oParametros->iAlturaLinha-2, $sString, 1, 1, "C");
            }
        }

        $oPdf->Ln();

        /**
         * Verificamos se tem turno adicional
         */
        if ($oTurma->temTurnoAdicional() != "") {

            $oPdf->setX(85);
            $oPdf->setfont('arial','b',$tamanhoFonte-2);
            $oPdf->Cell(50, $oParametros->iAlturaLinha-2, "TURNO ADICIONAL", 1, 1, "C", 1);
            $lImprimeTurno = true;
            foreach ($aGradeHorario as $oGradeHorario) {

                $sString = "{$oGradeHorario->iPeriodo}º - {$oGradeHorario->sHoraInicio} / {$oGradeHorario->sHoraFim}";
                $oPdf->setfont('arial','',$tamanhoFonte-2);

                if (!$oGradeHorario->lPrincipal) {

                    if ($lImprimeTurno) {

                        $oPdf->setX(85);
                        $oPdf->Cell(50, $oParametros->iAlturaLinha-2, $oGradeHorario->sTurno, 1, 1, "C", 1);
                        $lImprimeTurno = false;
                    }
                    $oPdf->setX(85);
                    $oPdf->Cell(50, $oParametros->iAlturaLinha-2, $sString, 1, 1, "C");
                }
            }

        }
    }

    /**
     * Calculo para verificar se os dados da assinatura caberão na pagina atual
     */
    if ($oPdf->GetY() + 40 > $oPdf->h - 15) {
        $oPdf->AddPage();
    }

    $oPdf->SetY($oPdf->GetY()+5);
    $oDiaAtual  = new DBDate(date("Y-m-d"));
    $sMunicipio = $oTurma->getEscola()->getDepartamento()->getInstituicao()->getMunicipio();

    $DiaExtenso  = " {$sMunicipio}, " . $oDiaAtual->getDia() . " de " . DBDate::getMesExtenso((int)$oDiaAtual->getMes());
    $DiaExtenso .= "  de " . $oDiaAtual->getAno();

    $oPdf->Cell("192", $oParametros->iAlturaLinha, $DiaExtenso, 0, 1, "C");
    $oPdf->ln($oParametros->iAlturaLinha*2);
    $oPdf->Line(50, $oPdf->GetY(), 152, $oPdf->GetY());

    $oPdf->setfont('arial','',$tamanhoFonte-2);
    if ($oParametros->lTemDiretor) {
        $oPdf->Cell("192", $oParametros->iAlturaLinha, $oParametros->sDiretor, 0, 1, "C");
        $oPdf->Cell("192", $oParametros->iAlturaLinha-2, $oParametros->sCargo,   0, 1, "C");
    }

}

$oPdf->Output();
