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

/**
 * Classe modelo para estatística dos alunos matriculados
 * @package    Educacao
 * @subpackage Relatorio
 * @author     André Mello  - andre.mello@dbseller.com.br
 * @author     Andrio Costa - andrio.costa@dbseller.com.br
 * @version    $Revision: 1.3 $
 */
class RelatorioQuadroDeVagas extends EstatisticaAlunosMatriculados
{
    /**
     * valida se devemos exibir o percentual
     * @var boolean
     */
    private $lPercentual = false;

    const COR_ENSINO = '180';
    const COR_ETAPA = '225';
    const COR_TURMA = '255';

    /**
     * Instancia de FPDF
     * @var ECidade\Pdf\Pdf
     */
    private $oPdf;

    /**
     * @var array
     */
    private $aEtapas = [];

    /**
     * @var array
     */
    private $aTurmasMulti = [];

    /**
     * @var array
     */
    private $aTurmasRegulares = [];

    /**
     * @var int
     */
    private $contadorTurmasPorEscolaGlobal;

    /**
     * @var int
     */
    private $contadorVagasTurmasPorEscolaGlobal;

    /**
     * @var int
     */
    private $contadorMatriculasPorEscolaGlobal;

    /**
     * @var int
     */
    private $contadorMatriculasNeePorEscolaGlobal;

    /**
     * @var int
     */
    private $contadorVagasDisponiveisPorEscolaGlobal;

    /**
     * @var array
     */
    private $contadorVagasDisponiveisParciaisGlobal;

    /**
     * Construtor da classe.
     * @param Calendario $oCalendario Instancia do calendário
     * @param array $aEtapa Array com os códigos das etapas a serem filtradas
     * @param Escola $oEscola Instancia da escola
     * @param Boolean $lPercentual Valida se mostra ou não os percentuais
     */

    /**
     * RelatorioQuadroDeVagas constructor.
     * @param $aCalendario
     * @param array $aEtapa
     * @param $aEscola
     * @param bool $lPercentual
     * @param int $iTipoRelatorio
     */

    public function __construct($aCalendario, $aEtapa, $aEscola, $lPercentual = false, $iTipoRelatorio = 1)
    {
        $iModulo = db_getsession("DB_modulo");
        $contadorCalendario = 0;
        $this->instaciarPdf();

        // Ajuste no cabeçalho do relatório para que seja impresso todos os calendários selecionados
        if (sizeof($aEscola) > 1) {
            $this->oPdf->addTitulo('QUADRO DE VAGAS', 1);
            if ($iModulo == 7159) {
                $this->oPdf->addTitulo("Calendário : ", 3);

                $arrayCaledariosRepetidos = array();
                foreach ($aCalendario as $calendario) {// Percorre o array em busca de calendário
                    // os retira os repetidos.
                    $arrayCaledariosRepetidos[$calendario->getDescricao()] = $calendario->getDescricao();
                }

                $tamanhoCalendarioRepetidos = 1;
                foreach ($arrayCaledariosRepetidos as $calendarioRepetidos) { // Percorre os arrays sem repetição
                    // e seta o cabeçalho
                    $this->oPdf->editTitleByKey(3, "{$this->oPdf->getTitleByKey(3)} {$calendarioRepetidos}");
                    if ($tamanhoCalendarioRepetidos == sizeof($arrayCaledariosRepetidos)) {
                        $this->oPdf->editTitleByKey(3, "{$this->oPdf->getTitleByKey(3)} .");
                    } else {
                        $this->oPdf->editTitleByKey(3, "{$this->oPdf->getTitleByKey(3)} , ");
                    }
                    $tamanhoCalendarioRepetidos++;
                }
            }
        }

        if ($iTipoRelatorio === "1") {
            foreach ($aEscola as $oEscola) { // Percorre o array das onde estão os objetos escola
                $oCalendario = $aCalendario[$contadorCalendario]; // Seta o primeiro calendário

                parent::__construct($oCalendario, $aEtapa, $oEscola); // Seta os parametros no construtor PAI
                $contadorCalendario++;
                $this->getEstatisticaAlunosMatriculados(); // Reliza as busca dos dados na classe PAI
                $this->getPercentual(); // Não utilizado

                $this->lPercentual = $lPercentual;

                $oEtapa = EtapaRepository::getEtapaByCodigo($aEtapa[0]);

                $sDescricaoEtapa = $oEtapa->getNome();

                if (count($aEtapa) > 1) {
                    $sDescricaoEtapa = "TODOS";
                }

                // Ajuste no cabeçalho do relatório para exibiçao da escola quando for emitida pelo Módulo Secretaria
                $this->oPdf->addTitulo('QUADRO DE VAGAS', 1);

                if ($iModulo == 7159) {
                    $title2 = sizeof($aEscola) > 1
                        ? ""
                        : "Escola : {$oEscola->getCodigoReferencia()} - {$oEscola->getNome()}";
                    $title3 = sizeof($aEscola) > 1
                        ? ""
                        : "Calendário: {$oCalendario->getDescricao()}"; // Seta Calendário no cabeçalho e incrementa
                    $title4 = sizeof($aEscola) > 1
                        ? ""
                        : "Etapa : {$sDescricaoEtapa} ";
                        $this->oPdf->addTitulo($title2, 2);
                        $this->oPdf->addTitulo($title3, 3);
                        $this->oPdf->addTitulo($title4, 4);
                } else {
                    $this->oPdf->addTitulo("Calendário: {$oCalendario->getDescricao()}", 2);
                    $this->oPdf->addTitulo("Etapa : {$sDescricaoEtapa}", 3);
                }

                if ($contadorCalendario == 1) {
                    $this->oPdf->AddPage('P');
                }
                $this->imprimirPorEscola($oEscola, $oCalendario);
                $this->oPdf->CheckPageBreak(50);
            }

            $this->totalGeral(
                $this->contadorTurmasPorEscolaGlobal,
                $this->contadorVagasTurmasPorEscolaGlobal,
                $this->contadorMatriculasPorEscolaGlobal,
                $this->contadorMatriculasNeePorEscolaGlobal,
                $this->contadorVagasDisponiveisPorEscolaGlobal,
                $this->contadorVagasDisponiveisParciaisGlobal
            );
        } elseif ($iTipoRelatorio === "2") {
            foreach ($aEscola as $oEscola) {
                $oCalendario = $aCalendario[$contadorCalendario]; // Seta o primeiro calendário

                parent::__construct($oCalendario, $aEtapa, $oEscola); // Seta os parametros no construtor PAI
                $contadorCalendario++;
                $this->getEstatisticaAlunosMatriculados(); // Reliza as busca dos dados na classe PAI
                $this->getPercentual(); // Não utilizado

                $this->lPercentual = $lPercentual;

                $oEtapa = EtapaRepository::getEtapaByCodigo($aEtapa[0]);

                $sDescricaoEtapa = $oEtapa->getNome();

                if (count($aEtapa) > 1) {
                    $sDescricaoEtapa = "TODOS";
                }

                // Ajuste no cabeçalho do relatório para exibiçao da escola quando for emitida pelo Módulo Secretaria
                $this->oPdf->addTitulo('QUADRO DE VAGAS GERAL DA REDE', 1);

                if ($iModulo == 7159) {
                    $title2 = sizeof($aEscola) > 1
                        ? ""
                        : "Escola : {$oEscola->getCodigoReferencia()} - {$oEscola->getNome()}";
                    $title3 = sizeof($aEscola) > 1
                        ? ""
                        : "Calendário: {$oCalendario->getDescricao()}"; // Seta Calendário no cabeçalho e incrementa
                    $title4 = sizeof($aEscola) > 1
                        ? ""
                        : "Etapa : {$sDescricaoEtapa} ";
                        $this->oPdf->addTitulo($title2, 2);
                        $this->oPdf->addTitulo($title3, 3);
                        $this->oPdf->addTitulo($title4, 4);
                } else {
                    $this->oPdf->addTitulo("Calendário: {$oCalendario->getDescricao()}", 2);
                    $this->oPdf->addTitulo("Etapa : {$sDescricaoEtapa}", 3);
                }

                if ($contadorCalendario == 1) {
                    $this->oPdf->AddPage('P');
                }

                $this->montarDadosPorEtapa();
                $this->oPdf->CheckPageBreak(50);
            }
            $this->imprimirPorEtapa($this->aEtapas);
        }
    }

    public function instaciarPdf()
    {
        $this->oPdf = new ECidade\Pdf\Pdf();
        $this->oPdf->setfont('arial', '', 6);
        $this->oPdf->init(false);
        $this->oPdf->AliasNbPages();
        $this->oPdf->SetAutoPageBreak(true, 8);
        $this->oPdf->setfillcolor(223);
        $this->oPdf->SetMargins(15, 8, 8);
    }

    private function totalGeral(
        $contadorTurmas,
        $contadorVagasTurmas,
        $contadorMatriculas,
        $contadorMatriculasNee,
        $contadorVagasDisponiveis,
        $contadorVagasDisponiveisParciais
    )
    {
        // Rodapé Total Geral
        $altura = 4;
        $largura = 14;

        $total_geral_turmas = $contadorTurmas;
        $total_geral_vagas_turmas = $contadorVagasTurmas;
        $total_geral_matriculas = $contadorMatriculas;
        $total_geral_matriculas_nee = $contadorMatriculasNee;
        $total_geral_vagas_disponiveis = $contadorVagasDisponiveis;

        $this->oPdf->SetY($this->oPdf->GetY() + 8);
        $this->oPdf->setfillcolor(self::COR_ETAPA);
        $this->oPdf->setfont('arial', 'b', 7);
        $this->oPdf->cell($largura + 7, $altura, "TOTAL GERAL:", 'LBT', 0, 'C', 1);
        $this->oPdf->setfont('arial', 'b', 6);
        $this->oPdf->cell($largura + 54, $altura, "{$total_geral_turmas}", "LBT", 0, 'C', 1);
        $this->oPdf->cell($largura, $altura, "{$total_geral_vagas_turmas}", "LBT", 0, 'C', 1);
        $this->oPdf->cell($largura, $altura, "{$total_geral_matriculas}", "LBT", 0, 'C', 1);
        $this->oPdf->cell($largura, $altura, "{$total_geral_matriculas_nee}", "LBT", 0, 'C', 1);
        $this->oPdf->cell(($largura * 2) + 6, $altura, "", "LBT", 0, 'C', 1);

        if (!empty($contadorVagasDisponiveisParciais)) {
            $sDescricaoVagasParciaisGlobal = "";
            foreach ($contadorVagasDisponiveisParciais as $turno => $vagas) {
                $sDescricaoVagasParciaisGlobal .= $vagas . substr($turno, 0, 1);
                $sDescricaoVagasParciaisGlobal .= ",";
            }
            $sDescricaoVagasParciaisGlobal = rtrim($sDescricaoVagasParciaisGlobal, " ,");
            $this->oPdf->cell(
                $largura + 6,
                $altura,
                "{$total_geral_vagas_disponiveis} ({$sDescricaoVagasParciaisGlobal})*",
                1,
                1,
                'C',
                1
            );
            //Descritivo adicional caso tenha Vagas Parciais em Turno Integral
            $this->oPdf->setfont('arial', '', 6);
            $this->oPdf->cell($largura * 13, $altura,
                                "Vagas em (*) correspondem a Vagas Parciais em turmas com Turno Integral", 0, 1, "R", 0);
            $this->oPdf->cell(1, 0, "", 0, 1, "R", 0);
            $this->oPdf->cell($largura * 13, $altura - 1,
                                "T = Tarde / M = Manhã", 0, 1, "R", 0);
            $this->oPdf->setfont('arial', 'b', 6);
        } else {
            $this->oPdf->cell($largura + 6, $altura, "{$total_geral_vagas_disponiveis}", 1, 1, 'C', 1);
        }
    }

    public function montarDadosPorEtapa()
    {
        $aTurmasProcessadas = [];

        foreach ($this->aEnsino as $oEnsino) {
            foreach ($oEnsino->aEtapa as $oEtapa) {
                foreach ($oEtapa->aTurmas as $oTurma) {
                    $modelTurma = TurmaRepository::getTurmaByCodigo($oTurma->iCodigo);
                    $matriculasInclusao = 0;

                    $matriculasTurma = $modelTurma->getAlunosMatriculados();
                    foreach ($matriculasTurma as $matricula) {
                        if ($matricula->getSituacao() != 'MATRICULADO') {
                            continue;
                        }
                        if (count($matricula->getAluno()->getNecessidadesEspeciais()) > 0) {
                            $matriculasInclusao++;
                        }
                    }
                    $aModelTurmaEtapas = $modelTurma->getEtapas();
                    if (count($aModelTurmaEtapas) > 1) {
                        if (in_array($oTurma->iCodigo, $aTurmasProcessadas)) {
                            continue;
                        }
                        $aTurmasProcessadas[] = $oTurma->iCodigo;
                        $etapaCenso = $aModelTurmaEtapas[0]->getEtapa()->getEtapaCenso();
                     
                        if ($etapaCenso == 1) {
                            $key = 1; // multi_creche
                            $nomeEtapaCenso = "ED. INF. CRECHE";
                        } elseif ($etapaCenso == 2) {
                            $key = 2; // multi_pre
                            $nomeEtapaCenso = "ED. INF. PRÉ";
                        } elseif (in_array($etapaCenso, [14, 15, 16, 17, 18])) {
                            $key = 4; // multi_anos_iniciais
                            $nomeEtapaCenso = "ENS. FUND. INICIAIS";
                        } elseif (in_array($etapaCenso, [19, 20, 21, 22])) {
                            $key = 5; // multi_anos_finais
                            $nomeEtapaCenso = "ENS. FUND. FINAIS";
                        } elseif ($etapaCenso == 23) {
                            $key = 8; // multi_correcao_fluxo
                            $nomeEtapaCenso = "ENS. FUND. COR. FLUXO";
                        } elseif (in_array($etapaCenso, [25, 26, 27, 28])) {
                            $key = 9; // multi_ens_medio
                            $nomeEtapaCenso = "ENS. MÉD. NORMAL";
                        } elseif (in_array($etapaCenso, [35, 36, 37, 38])) {
                            $key = 10; // multi_ens_medio_magisterio
                            $nomeEtapaCenso = "ENS. MÉD. MAGISTÉRIO";
                        } elseif ($etapaCenso == 56) {
                            $key = 3; // multi_infantil_e_fundamental
                            $nomeEtapaCenso = "ED. INF. ENS. FUND.";
                        } elseif (in_array($etapaCenso, [43, 69])) {
                            $key = 6; // multi_eja_fundamental_iniciais
                            $nomeEtapaCenso = "EJA INICIAIS";
                        } elseif (in_array($etapaCenso, [44, 70])) {
                            $key = 7; // multi_eja_fundamental_finais
                            $nomeEtapaCenso = "EJA FINAIS";
                        } elseif ($etapaCenso == 65) {
                            $key = 11; // multi_ens_fund_eja_projovem
                            $nomeEtapaCenso = "EJA ED. ESPECIAL";
                        } else {
                            $key = "outros";
                            $nomeEtapaCenso = "OUTROS";
                        }

                        if (!array_key_exists($key, $this->aTurmasMulti)) {
                            $this->aTurmasMulti[$key] = (object)[
                                'nome_etapa_multi' => $nomeEtapaCenso,
                                'total_turmas' => 0,
                                'total_vagas_turmas' => 0,
                                'total_matriculas' => 0,
                                'total_matriculas_nee' => 0,
                                'total_vagas_disponiveis' => 0
                            ];
                        }

                        $this->aTurmasMulti[$key]->total_turmas++;
                        $this->aTurmasMulti[$key]->total_vagas_turmas += $oTurma->total_vagas;
                        $this->aTurmasMulti[$key]
                            ->total_matriculas += $modelTurma->getTotalAlunosMatriculados();
                        $this->aTurmasMulti[$key]
                            ->total_matriculas_nee += $matriculasInclusao;
                        $this->aTurmasMulti[$key]
                            ->total_vagas_disponiveis += $oTurma->total_disponiveis;
                    } else {
                        if (!array_key_exists($oEtapa->sNome, $this->aEtapas)) {
                            $etapaKey = $modelTurma->getEtapas()[0]->getEtapa();
                            $ordem = $etapaKey->getOrdem();
                            $codigoEnsino = $etapaKey->getEnsino()->getCodigo();
                            $keyOrdem = "{$codigoEnsino}#{$ordem}#{$oEtapa->sNome}";

                            $this->aEtapas[$oEtapa->sNome] = (object)[
                                'codigo_etaoa' => $keyOrdem,
                                'nome_etapa' => $oEtapa->sNome,
                                'total_turmas' => 0,
                                'total_vagas_turmas' => 0,
                                'total_matriculas' => 0,
                                'total_matriculas_nee' => 0,
                                'total_vagas_disponiveis' => 0
                            ];
                        }

                        $this->aEtapas[$oEtapa->sNome]->total_turmas++;
                        $this->aEtapas[$oEtapa->sNome]->total_vagas_turmas += $oTurma->total_vagas;
                        $this->aEtapas[$oEtapa->sNome]->total_matriculas += $modelTurma->getTotalAlunosMatriculados();
                        $this->aEtapas[$oEtapa->sNome]->total_matriculas_nee += $matriculasInclusao;
                        $this->aEtapas[$oEtapa->sNome]->total_vagas_disponiveis += $oTurma->total_disponiveis;
                    }
                }
            }
        }

        ksort($this->aTurmasMulti);
        asort($this->aEtapas);
    }

    public function cabecalhoPorEtapa($iPosicaoX, $altura, $largura, $larguraReduzida)
    {
        $this->oPdf->setfillcolor(self::COR_ETAPA);
        $this->oPdf->setfont('arial', 'b', 6);

        $this->oPdf->SetY($this->oPdf->GetY() + 6);
        $this->oPdf->cell(0, 0, "TURMAS REGULARES", 0, 0, "C", 0);
        $this->oPdf->SetY($this->oPdf->GetY() + 6);

        $this->oPdf->SetX($iPosicaoX);
        $this->oPdf->cell($largura, $altura, "SÉRIE", 1, 0, "C", 1);
        $this->oPdf->cell($larguraReduzida, $altura, "TURMAS", 1, 0, "C", 1);
        $this->oPdf->cell($larguraReduzida, $altura, "V. TURMA", 1, 0, "C", 1);
        $this->oPdf->cell($larguraReduzida, $altura, "MATRÍCULAS", 1, 0, "C", 1);
        $this->oPdf->cell($larguraReduzida, $altura, 'INCLUSÃO', 1, 0, "C", 1);
        $this->oPdf->cell($larguraReduzida, $altura, "V. DISP", 1, 1, "C", 1);

        $this->oPdf->setfillcolor(self::COR_TURMA);
        $this->oPdf->setfont('arial', '', 6);
    }

    public function imprimirPorEtapa($aEtapas)
    {
        $iPosicaoX = 15;
        $altura = 4;
        $largura = 30;
        $larguraReduzida = $largura - 0.8;

        $this->cabecalhoPorEtapa($iPosicaoX, $altura, $largura, $larguraReduzida);

        $contadorTurmas = 0;
        $contadorVagasTurmas = 0;
        $contadorMatriculas = 0;
        $contadorMatriculasNee = 0;
        $contadorVagasDisponiveis = 0;

        foreach ($aEtapas as $aEtapa) {
            if ($this->oPdf->getAvailHeight() <= 8) {
                $this->oPdf->AddPage('P');
                $this->cabecalhoPorEtapa($iPosicaoX, $altura, $largura, $larguraReduzida);
            }

            $this->oPdf->SetX($iPosicaoX);
            $this->oPdf->cell($largura, $altura, "{$aEtapa->nome_etapa}", "LB", 0, "C", 0);
            $this->oPdf->cell($larguraReduzida, $altura, "{$aEtapa->total_turmas}", "LB", 0, "C", 0);
            $this->oPdf->cell($larguraReduzida, $altura, "{$aEtapa->total_vagas_turmas}", "LB", 0, "C", 0);
            $this->oPdf->cell($larguraReduzida, $altura, "{$aEtapa->total_matriculas}", 1, "LB", "C", 0);
            $this->oPdf->cell($larguraReduzida, $altura, "{$aEtapa->total_matriculas_nee}", "LB", 0, "C", 0);
            $this->oPdf->setfont('arial', 'b', 6);
            $this->oPdf->cell($larguraReduzida, $altura, "{$aEtapa->total_vagas_disponiveis}", "LBR", 1, "C", 0);
            $this->oPdf->setfont('arial', '', 6);

            $contadorTurmas += $aEtapa->total_turmas;
            $contadorVagasTurmas += $aEtapa->total_vagas_turmas;
            $contadorMatriculas += $aEtapa->total_matriculas;
            $contadorMatriculasNee += $aEtapa->total_matriculas_nee;
            $contadorVagasDisponiveis += $aEtapa->total_vagas_disponiveis;
        }

        $this->oPdf->SetX($iPosicaoX);
        $this->oPdf->setfillcolor(self::COR_ETAPA);
        $this->oPdf->setfont('arial', 'b', 6);
        $this->oPdf->cell($largura, $altura, "SUBTOTAL", "LB", 0, 'C', 1);
        $this->oPdf->cell($larguraReduzida, $altura, "{$contadorTurmas}", "LB", 0, 'C', 1);
        $this->oPdf->cell($larguraReduzida, $altura, "{$contadorVagasTurmas}", "LB", 0, 'C', 1);
        $this->oPdf->cell($larguraReduzida, $altura, "{$contadorMatriculas}", "LB", 0, 'C', 1);
        $this->oPdf->cell($larguraReduzida, $altura, "{$contadorMatriculasNee}", "LB", 0, 'C', 1);
        $this->oPdf->cell($larguraReduzida, $altura, "{$contadorVagasDisponiveis}", "LBR", 0, 'C', 1);


        // Declarando variáveis para contar turmas multietapa
        $contadorTurmasMulti = 0;
        $contadorVagasTurmasMulti = 0;
        $contadorMatriculasMulti = 0;
        $contadorMatriculasNeeMulti = 0;
        $contadorVagasDisponiveisMulti = 0;

        // Turmas Multietapa - Cabeçalho

        $this->oPdf->SetY($this->oPdf->GetY() + 12);
        $this->oPdf->cell(0, 0, "TURMAS MULTIETAPA", 0, 0, "C", 0);
        $this->oPdf->SetY($this->oPdf->GetY() + 6);

        $this->oPdf->SetX($iPosicaoX);
        $this->oPdf->cell($largura, $altura, "SÉRIE", 1, 0, "C", 1);
        $this->oPdf->cell($larguraReduzida, $altura, "TURMAS", 1, 0, "C", 1);
        $this->oPdf->cell($larguraReduzida, $altura, "V. TURMA", 1, 0, "C", 1);
        $this->oPdf->cell($larguraReduzida, $altura, "MATRÍCULAS", 1, 0, "C", 1);
        $this->oPdf->cell($larguraReduzida, $altura, 'INCLUSÃO', 1, 0, "C", 1);
        $this->oPdf->cell($larguraReduzida, $altura, "V. DISP", 1, 1, "C", 1);

        $this->oPdf->setfillcolor(self::COR_TURMA);
        $this->oPdf->setfont('arial', '', 6);

        if (empty($this->aTurmasMulti)) {
            $this->oPdf->cell(176, $altura * 1.2, "Não foram encontradas turmas multietapa.", "LRB", 1, "C", 0);
        }

        foreach ($this->aTurmasMulti as $turmaMulti) {
            // Quebra de página e insere cabeçalho novamente se necessário
            if ($this->oPdf->getAvailHeight() <= 8) {
                $this->oPdf->AddPage('P');
                $this->oPdf->SetY($this->oPdf->GetY() + 12);
                $this->oPdf->cell(0, 0, "TURMAS MULTIETAPA", 0, 0, "C", 0);
                $this->oPdf->SetY($this->oPdf->GetY() + 6);

                $this->oPdf->SetX($iPosicaoX);
                $this->oPdf->cell($largura, $altura, "SÉRIE", 1, 0, "C", 1);
                $this->oPdf->cell($larguraReduzida, $altura, "TURMAS", 1, 0, "C", 1);
                $this->oPdf->cell($larguraReduzida, $altura, "V. TURMA", 1, 0, "C", 1);
                $this->oPdf->cell($larguraReduzida, $altura, "MATRÍCULAS", 1, 0, "C", 1);
                $this->oPdf->cell($larguraReduzida, $altura, 'INCLUSÃO', 1, 0, "C", 1);
                $this->oPdf->cell($larguraReduzida, $altura, "V. DISP", 1, 1, "C", 1);

                $this->oPdf->setfillcolor(self::COR_TURMA);
                $this->oPdf->setfont('arial', '', 6);
            }

            $this->oPdf->SetX($iPosicaoX);

            // Ajusta o tamanho da fonte dinamicamente para nome_etapa_multi
            $content = "{$turmaMulti->nome_etapa_multi}";
            $tamanhoString = $this->oPdf->GetStringWidth($content);

            if ($tamanhoString > $largura) {
                $tamanhoFonte = 7 * $largura / $tamanhoString;
                $this->oPdf->SetFontSize($tamanhoFonte);
            }

            $this->oPdf->cell($largura, $altura, "{$turmaMulti->nome_etapa_multi}", "LB", 0, "C", 0);
            // Retorna fonte ao tamanho original
            $this->oPdf->SetFontSize(6);
            $this->oPdf->cell($larguraReduzida, $altura, "{$turmaMulti->total_turmas}", "LB", 0, "C", 0);
            $this->oPdf->cell($larguraReduzida, $altura, "{$turmaMulti->total_vagas_turmas}", "LB", 0, "C", 0);
            $this->oPdf->cell($larguraReduzida, $altura, "{$turmaMulti->total_matriculas}", 1, "LB", "C", 0);
            $this->oPdf->cell($larguraReduzida, $altura, "{$turmaMulti->total_matriculas_nee}", "LB", 0, "C", 0);
            $this->oPdf->setfont('arial', 'b', 6);
            $this->oPdf->cell($larguraReduzida, $altura, "{$turmaMulti->total_vagas_disponiveis}", "LBR", 1, "C", 0);
            $this->oPdf->setfont('arial', '', 6);

            $contadorTurmasMulti += $turmaMulti->total_turmas;
            $contadorVagasTurmasMulti += $turmaMulti->total_vagas_turmas;
            $contadorMatriculasMulti += $turmaMulti->total_matriculas;
            $contadorMatriculasNeeMulti += $turmaMulti->total_matriculas_nee;
            $contadorVagasDisponiveisMulti += $turmaMulti->total_vagas_disponiveis;
        }

        // Turmas Multietapa Rodapé
        $this->oPdf->SetX($iPosicaoX);
        $this->oPdf->setfillcolor(self::COR_ETAPA);
        $this->oPdf->setfont('arial', 'b', 6);
        $this->oPdf->cell($largura, $altura, "SUBTOTAL", "LB", 0, 'C', 1);
        $this->oPdf->cell($larguraReduzida, $altura, "{$contadorTurmasMulti}", "LB", 0, 'C', 1);
        $this->oPdf->cell($larguraReduzida, $altura, "{$contadorVagasTurmasMulti}", "LB", 0, 'C', 1);
        $this->oPdf->cell($larguraReduzida, $altura, "{$contadorMatriculasMulti}", "LB", 0, 'C', 1);
        $this->oPdf->cell($larguraReduzida, $altura, "{$contadorMatriculasNeeMulti}", "LB", 0, 'C', 1);
        $this->oPdf->cell($larguraReduzida, $altura, "{$contadorVagasDisponiveisMulti}", "LBR", 0, 'C', 1);

        // Total Geral
        $total_geral_turmas = $contadorTurmas + $contadorTurmasMulti;
        $total_geral_vagas_turmas = $contadorVagasTurmas + $contadorVagasTurmasMulti;
        $total_geral_matriculas = $contadorMatriculas + $contadorMatriculasMulti;
        $total_geral_matriculas_nee = $contadorMatriculasNee + $contadorMatriculasNeeMulti;
        $total_geral_vagas_disponiveis = $contadorVagasDisponiveis + $contadorVagasDisponiveisMulti;

        $this->oPdf->SetY($this->oPdf->GetY() + 12);
        $this->oPdf->setfillcolor(self::COR_ETAPA);
        $this->oPdf->setfont('arial', 'b', 6);
        $this->oPdf->cell($largura, $altura, "TOTAL GERAL:", 'LBT', 0, 'C', 1);
        $this->oPdf->cell($larguraReduzida, $altura, "{$total_geral_turmas}", "LBT", 0, 'C', 1);
        $this->oPdf->cell($larguraReduzida, $altura, "{$total_geral_vagas_turmas}", "LBT", 0, 'C', 1);
        $this->oPdf->cell($larguraReduzida, $altura, "{$total_geral_matriculas}", "LBT", 0, 'C', 1);
        $this->oPdf->cell($larguraReduzida, $altura, "{$total_geral_matriculas_nee}", "LBT", 0, 'C', 1);
        $this->oPdf->cell($larguraReduzida, $altura, "{$total_geral_vagas_disponiveis}", 1, 1, 'C', 1);
    }

    // Parâmetros servem apenas para cabeçalho de cada tabela de escola
    public function imprimirPorEscola($escola, $calendario)
    {       
        $this->oPdf->setX(10);
        $altura = 4;
        $largura = 14;

        // Popula telefones de contato da escola
        $contatos = " ";
        for ($i = 0; $i < sizeof($escola->getTelefones()); $i++) {
            $contatos .= "(" . $escola->getTelefones()[$i]->iDDD . ") " . $escola->getTelefones()[$i]->iNumero . " ";
        }

        // Seta cabeçalho de cada tabela
        $this->oPdf->setfillcolor(self::COR_ETAPA);
        $this->oPdf->setfont('arial', 'b', 8);
        $this->oPdf->cell(100, $altura, "", 0, 0, "L", 0);
        $this->oPdf->cell(1, $altura, "", 0, 0, "R", 0);
        $this->oPdf->cell(74, $altura, "", 0, 1, "R", 0);
        $this->oPdf->cell($largura * 8, $altura, "{$escola->getCodigoReferencia()} - {$escola->getNome()}   ({$calendario->getDescricao()}) ", 0, 0, "L", 0);
        $this->oPdf->cell(1, $altura, "", 0, 1, "R", 0);
        $this->oPdf->cell($largura * 6, $altura, "BAIRRO: {$escola->getBairro()}", 0, 0, "L", 0);
        $this->oPdf->cell($largura * 6, $altura, "TELEFONE: $contatos", 0, 1, "R", 0);

        // Seta cabeçalho das colunas
        $this->oPdf->setfillcolor(self::COR_ETAPA);
        $this->oPdf->setfont('arial', 'b', 6);
        $this->oPdf->cell($largura + 7, $altura, "SÉRIE", 1, 0, "C", 1);
        $this->oPdf->cell($largura + 54, $altura, "TURMA", 1, 0, "C", 1);
        $this->oPdf->cell($largura, $altura, "V. TURMA", 1, 0, "C", 1);
        $this->oPdf->cell($largura, $altura, "MATRÍCULA", 1, 0, "C", 1);
        $this->oPdf->cell($largura, $altura, "INCLUSÃO", 1, 0, "C", 1);
        $this->oPdf->cell($largura, $altura, "TP TURMA", 1, 0, "C", 1);
        $this->oPdf->cell($largura + 6, $altura, "TURNO", 1, 0, "C", 1);
        $this->oPdf->cell($largura + 6, $altura, "V. DISP", 1, 1, "C", 1);

        // Array para guardar turmas multietapas únicas
        $aTurmasMultiProcessadas = [];
        $this->aTurmasRegulares = [];
        $this->aTurmasMulti = [];

        foreach ($this->aEnsino as $oEnsino) {
            foreach ($oEnsino->aEtapa as $oEtapa) {
                foreach ($oEtapa->aTurmas as $oTurma) {
                    $modelTurma = TurmaRepository::getTurmaByCodigo($oTurma->iCodigo);
                    $aModelTurmaEtapas = $modelTurma->getEtapas();
                    $totalVagasDisponiveis = 0;

                    // Monta as turmas multietapa
                    if (count($aModelTurmaEtapas) > 1 || $modelTurma->getTipoDaTurma() == 3 || $modelTurma->getTipoDaTurma() == 7) {
                        if (in_array($oTurma->iCodigo, $aTurmasMultiProcessadas)) {
                            continue;
                        }
                        $aTurmasMultiProcessadas[] = $oTurma->iCodigo;
                    } else {
                        if ($oTurma->lTurnoIntegral && $oTurma->lIsInfantil) {
                            $oTurma->total_vagas = ($oTurma->total_vagas / 2);
                            $totalVagasDisponiveis = $oTurma->total_vagas_inteiras;
                            ksort($oTurma->vagas_parciais, SORT_STRING);
                            if ($oTurma->total_vagas_inteiras > 0) {
                                $vagasParciaisPosCalculo = 0;
                                foreach ($oTurma->vagas_parciais as $turno => $vagas) {
                                    $oTurma->vagas_parciais[$turno] = $vagas - $oTurma->total_vagas_inteiras;
                                }
                                foreach ($oTurma->vagas_parciais as $turno => $vagas) {
                                    $vagasParciaisPosCalculo += (int)$vagas;
                                }
                                $oTurma->total_disponiveis = $vagasParciaisPosCalculo;
                            }
                        } else {
                            $totalVagasDisponiveis = $oTurma->total_disponiveis;
                        }
                        $this->aTurmasRegulares[$oTurma->iCodigo] = (object)[
                            'etapa_turma_regular' => $oEtapa->sNome,
                            'nome_turma_regular' => $oTurma->sTurma,
                            'vagas_turma_regular' => $oTurma->total_vagas,
                            'matriculas_turma_regular' => $oTurma->matriculas_efetivas,
                            'matriculas_nee_turma_regular' => $oTurma->matriculas_nee,
                            'tipo_turma_regular' => '',
                            'turno_turma_regular' => $oTurma->sTurno,
                            'vagas_disponiveis_turma_regular' => $totalVagasDisponiveis,
                            'vagas_disponiveis_parcial' => $oTurma->total_disponiveis,
                            'vagas_parciais_por_turno' => $oTurma->vagas_parciais
                        ];
                    }
                }
            }
        }

        foreach ($this->aEnsino as $oEnsino) {
            foreach ($oEnsino->aEtapa as $oEtapa) {
                foreach ($oEtapa->aTurmas as $oTurma) {
                    $multiEatapa = [];
                    $totalVagasDisponiveis = 0;
                    
                    if (in_array($oTurma->iCodigo, $aTurmasMultiProcessadas)) {
                        $modelTurma = TurmaRepository::getTurmaByCodigo($oTurma->iCodigo);
                        $aModelTurmaEtapas = $modelTurma->getEtapas();
                        $etapaCenso = $aModelTurmaEtapas[0]->getEtapa()->getEtapaCenso();
                        if (!isset($etapaCenso)) {
                            continue;
                        }

                        foreach ($aModelTurmaEtapas as $turmaEatapa) {
                            $multiEatapa[] = $turmaEatapa->getEtapa()->getNome();
                        }

                        if (!array_key_exists($oTurma->iCodigo, $this->aTurmasMulti)) {
                            if ($oTurma->lTurnoIntegral && $oTurma->lIsInfantil) {
                                $oTurma->total_vagas = ($oTurma->total_vagas / 2);
                                $totalVagasDisponiveis = $oTurma->total_vagas_inteiras;
                                ksort($oTurma->vagas_parciais, SORT_STRING);
                                if ($oTurma->total_vagas_inteiras > 0) {
                                    $vagasParciaisPosCalculo = 0;
                                    foreach ($oTurma->vagas_parciais as $turno => $vagas) {
                                        $oTurma->vagas_parciais[$turno] = $vagas - $oTurma->total_vagas_inteiras;
                                    }
                                    foreach ($oTurma->vagas_parciais as $turno => $vagas) {
                                        $vagasParciaisPosCalculo += (int)$vagas;
                                    }
                                    $oTurma->total_disponiveis = $vagasParciaisPosCalculo;
                                }
                            } else {
                                $totalVagasDisponiveis = $oTurma->total_disponiveis;
                            }
                            $this->aTurmasMulti[$oTurma->iCodigo] = (object)[
                                'etapa_turma_multi' => $multiEatapa,
                                'nome_turma_multi' => $oTurma->sTurma,
                                'vagas_turma_multi' => $oTurma->total_vagas,
                                'matriculas_turma_multi' => $oTurma->matriculas_efetivas,
                                'matriculas_nee_turma_multi' => $oTurma->matriculas_nee,
                                'tipo_turma_multi' => 'MULTI',
                                'turno_turma_multi' => $oTurma->sTurno,
                                'vagas_disponiveis_turma_multi' => $totalVagasDisponiveis,
                                'vagas_disponiveis_parcial' => $oTurma->total_disponiveis,
                                'vagas_parciais_por_turno' => $oTurma->vagas_parciais
                            ];
                        } else {
                            $this->aTurmasMulti[$oTurma->iCodigo]->matriculas_turma_multi +=
                                $oTurma->matriculas_efetivas;
                            $this->aTurmasMulti[$oTurma->iCodigo]->matriculas_nee_turma_multi +=
                                $oTurma->matriculas_nee;
                        }
                    }
                }
            }
        }

        // Declarando variáveis para contar turmas
        $contadorTurmas = 0;
        $contadorVagasTurmas = 0;
        $contadorMatriculas = 0;
        $contadorMatriculasNee = 0;
        $contadorVagasDisponiveis = 0;
        $contadorVagasParciais = [];

        // Imprime turmas regulares
        foreach ($this->aTurmasRegulares as $oTurmaRegular) {
            // Quebra página se não houver espaço e reimprime cabeçalho da tabela
            if ($this->oPdf->getAvailHeight() <= 8) {
                $this->oPdf->AddPage('P');
                $this->oPdf->setX(10);

                // Popula telefones de contato da escola
                $contatos = " ";
                for ($i = 0; $i < sizeof($escola->getTelefones()); $i++) {
                    $contatos .= "("
                        . $escola->getTelefones()[$i]->iDDD
                        . ") "
                        . $escola->getTelefones()[$i]->iNumero
                        . " ";
                }

                // Seta cabeçalho de cada tabela
                $this->oPdf->setfillcolor(self::COR_ETAPA);
                $this->oPdf->setfont('arial', 'b', 8);
                $this->oPdf->cell(100, $altura, "", 0, 0, "L", 0);
                $this->oPdf->cell(1, $altura, "", 0, 0, "R", 0);
                $this->oPdf->cell(74, $altura, "", 0, 1, "R", 0);
                $this->oPdf->cell($largura * 8, $altura, "{$escola->getCodigoReferencia()} - {$escola->getNome()}   ({$calendario->getDescricao()}) ", 0, 0, "L", 0);
                $this->oPdf->cell(1, $altura, "", 0, 1, "R", 0);
                $this->oPdf->cell($largura * 6, $altura, "BAIRRO: {$escola->getBairro()}", 0, 0, "L", 0);
                $this->oPdf->cell($largura * 6, $altura, "TELEFONE: $contatos", 0, 1, "R", 0);

                // Seta cabeçalho das colunas
                $this->oPdf->setfillcolor(self::COR_ETAPA);
                $this->oPdf->setfont('arial', 'b', 6);
                $this->oPdf->cell($largura + 7, $altura, "SÉRIE", 1, 0, "C", 1);
                $this->oPdf->cell($largura + 54, $altura, "TURMA", 1, 0, "C", 1);
                $this->oPdf->cell($largura, $altura, "V. TURMA", 1, 0, "C", 1);
                $this->oPdf->cell($largura, $altura, "MATRÍCULA", 1, 0, "C", 1);
                $this->oPdf->cell($largura, $altura, "INCLUSÃO", 1, 0, "C", 1);
                $this->oPdf->cell($largura, $altura, "TP TURMA", 1, 0, "C", 1);
                $this->oPdf->cell($largura + 6, $altura, "TURNO", 1, 0, "C", 1);
                $this->oPdf->cell($largura + 6, $altura, "V. DISP", 1, 1, "C", 1);
            }

            $this->oPdf->setfillcolor(self::COR_TURMA);
            $this->oPdf->setfont('arial', '', 6);

            $this->oPdf->cell($largura + 7, $altura, "{$oTurmaRegular->etapa_turma_regular}", "LB", 0, "C", 0);
            $this->oPdf->cell($largura + 54, $altura, "{$oTurmaRegular->nome_turma_regular}", "LB", 0, "L", 0);
            $this->oPdf->cell($largura, $altura, "{$oTurmaRegular->vagas_turma_regular}", "LB", 0, "C", 0);
            $this->oPdf->cell($largura, $altura, "{$oTurmaRegular->matriculas_turma_regular}", "LB", 0, "C", 0);
            $this->oPdf->cell($largura, $altura, "{$oTurmaRegular->matriculas_nee_turma_regular}", "LB", 0, "C", 0);
            $this->oPdf->cell($largura, $altura, "{$oTurmaRegular->tipo_turma_regular}", "LB", 0, "C", 0);

            // Verifica se o turno tem INTEGRAL MANHA E TARDE
            $content = "{$oTurmaRegular->turno_turma_regular} ";
            $verificaIntegral = stripos($content, 'INTEGRAL') !== false;
            $verificaManha = stripos($content, 'MANH') !== false;
            $verificaTarde = stripos($content, 'TARDE') !== false;
            if ($verificaIntegral && $verificaManha && $verificaTarde) {
                $content = 'INTEGRAL';
            }

            $this->oPdf->cell($largura + 6, $altura, "{$content}", "LB", 0, "C", 0);

            $this->oPdf->setfont('arial', 'b', 6);
            if ($oTurmaRegular->vagas_disponiveis_parcial > 0) {
                $sDescricaoVagas = "";
                foreach ($oTurmaRegular->vagas_parciais_por_turno as $turno => $vagas) {
                    $sDescricaoVagas .= $vagas . substr($turno, 0, 1);
                    $sDescricaoVagas .= ",";
                }
                $sDescricaoVagas = rtrim($sDescricaoVagas, " ,");
                if ($sDescricaoVagas != '') {
                    $this->oPdf->cell(
                        $largura + 6,
                        $altura,
                        "{$oTurmaRegular->vagas_disponiveis_turma_regular} ({$sDescricaoVagas})*",
                        "LBR",
                        1,
                        "C",
                        0
                    );
                } else {
                    $this->oPdf->cell($largura + 6, $altura, "{$oTurmaRegular->vagas_disponiveis_turma_regular}", "LBR", 1, "C", 0);
                }
            } else {
                $this->oPdf->cell($largura + 6, $altura, "{$oTurmaRegular->vagas_disponiveis_turma_regular}", "LBR", 1, "C", 0);
            }
            $this->oPdf->setfont('arial', '', 6);

            $contadorTurmas++;
            $contadorVagasTurmas += $oTurmaRegular->vagas_turma_regular;
            $contadorMatriculas += $oTurmaRegular->matriculas_turma_regular;
            $contadorMatriculasNee += $oTurmaRegular->matriculas_nee_turma_regular;
            $contadorVagasDisponiveis += $oTurmaRegular->vagas_disponiveis_turma_regular;

            if ($oTurmaRegular->vagas_disponiveis_parcial > 0) {
                foreach ($oTurmaRegular->vagas_parciais_por_turno as $turno => $vagas) {
                    $contadorVagasParciais[$turno] += $vagas;
                }
            }
        }

        // Imprime turmas multietapa
        foreach ($this->aTurmasMulti as $oTurmaMulti) {
            $this->oPdf->setfont('arial', '', 6);

            // Ajusta o tamanho da fonte dinamicamente
            $content = "{$oTurmaMulti->etapa_turma_multi[0]} ";
            $tamanhoString = $this->oPdf->GetStringWidth($content);

            if ($tamanhoString > $largura) {
                $tamanhoFonte = 7 * $largura / $tamanhoString;
                $this->oPdf->SetFontSize($tamanhoFonte);
            }
            $acAltura = 0;
            if (is_array($oTurmaMulti->etapa_turma_multi)) {
                
                foreach ($oTurmaMulti->etapa_turma_multi as $value) {
                    $acAltura += $altura;
                    $this->oPdf->cell($largura + 7, $altura, "{$value}", 1, 1, "L", 0);
                }
                $this->oPdf->setY($this->oPdf->getY() - $acAltura);
                $this->oPdf->setX($this->oPdf->getX() + $largura + 7);
                $this->oPdf->setfont('arial', '', 6);
                $this->oPdf->cell($largura + 54, $acAltura, "{$oTurmaMulti->nome_turma_multi}", "LB", 0, "L", 0);
                $this->oPdf->cell($largura, $acAltura, "{$oTurmaMulti->vagas_turma_multi}", "LB", 0, "C", 0);
                $this->oPdf->cell($largura, $acAltura, "{$oTurmaMulti->matriculas_turma_multi}", "LB", 0, "C", 0);
                $this->oPdf->cell($largura, $acAltura, "{$oTurmaMulti->matriculas_nee_turma_multi}", "LB", 0, "C", 0);
                $this->oPdf->cell($largura, $acAltura, "{$oTurmaMulti->tipo_turma_multi}", "LB", 0, "C", 0);
                
                // Verifica se o turno tem INTEGRAL MANHA E TARDE
                $content = "{$oTurmaMulti->turno_turma_multi} ";
                $verificaIntegral = stripos($content, 'INTEGRAL') !== false;
                $verificaManha = stripos($content, 'MANH') !== false;
                $verificaTarde = stripos($content, 'TARDE') !== false;
                if ($verificaIntegral && $verificaManha && $verificaTarde) {
                    $content = 'INTEGRAL';
                }

                $this->oPdf->cell($largura + 6, $acAltura, "{$content}", "LB", 0, "C", 0);

                $this->oPdf->setfont('arial', 'b', 6);
                if ($oTurmaMulti->vagas_disponiveis_parcial > 0) {
                    $sDescricaoVagas = "";
                    foreach ($oTurmaMulti->vagas_parciais_por_turno as $turno => $vagas) {
                        $sDescricaoVagas .= $vagas . substr($turno, 0, 1);
                        $sDescricaoVagas .= ",";
                    }
                    $sDescricaoVagas = rtrim($sDescricaoVagas, " ,");
                    if ($sDescricaoVagas != '') {
                        $this->oPdf->cell(
                            $largura + 6,
                            $acAltura,
                            "{$oTurmaMulti->vagas_disponiveis_turma_multi} ({$sDescricaoVagas})*",
                            "LBR",
                            1,
                            "C",
                            0
                        );
                    } else {
                        $this->oPdf->cell($largura + 6, $acAltura, "{$oTurmaMulti->vagas_disponiveis_turma_multi}", "LBR", 1, "C", 0);
                    }
                } else {
                    $this->oPdf->cell($largura + 6, $acAltura, "{$oTurmaMulti->vagas_disponiveis_turma_multi}", "LBR", 1, "C", 0);
                }
                $this->oPdf->setfont('arial', '', 6);

                $contadorTurmas++;
                $contadorVagasTurmas += $oTurmaMulti->vagas_turma_multi;
                $contadorMatriculas += $oTurmaMulti->matriculas_turma_multi;
                $contadorMatriculasNee += $oTurmaMulti->matriculas_nee_turma_multi;
                $contadorVagasDisponiveis += $oTurmaMulti->vagas_disponiveis_turma_multi;

                if ($oTurmaMulti->vagas_disponiveis_parcial > 0) {
                    foreach ($oTurmaMulti->vagas_parciais_por_turno as $turno => $vagas) {
                        $contadorVagasParciais[$turno] += $vagas;
                    }
                }
            }
        }

        // Rodapé
        $this->oPdf->setfillcolor(self::COR_ETAPA);
        $this->oPdf->setfont('arial', 'b', 6);
        $this->oPdf->cell($largura + 7, $altura, "SUBTOTAL", "LB", 0, 'C', 1);
        $this->oPdf->cell($largura + 54, $altura, "{$contadorTurmas}", "LB", 0, 'C', 1);
        $this->oPdf->cell($largura, $altura, "{$contadorVagasTurmas}", "LB", 0, 'C', 1);
        $this->oPdf->cell($largura, $altura, "{$contadorMatriculas}", "LB", 0, 'C', 1);
        $this->oPdf->cell($largura, $altura, "{$contadorMatriculasNee}", "LBR", 0, 'C', 1);
        $this->oPdf->cell($largura, $altura, "", "B", 0, "C", 1);
        $this->oPdf->cell($largura + 6 , $altura, "", "B", 0, "C", 1);

        if (!empty($contadorVagasParciais)) {
            $sDescricaoVagasParcialContador = "";
            foreach ($contadorVagasParciais as $turno => $vagas) {
                $sDescricaoVagasParcialContador .= $vagas . substr($turno, 0, 1);
                $sDescricaoVagasParcialContador .= ",";
            }
            $sDescricaoVagasParcialContador = rtrim($sDescricaoVagasParcialContador, " ,");
            $this->oPdf->cell(
                $largura + 6,
                $altura,
                "{$contadorVagasDisponiveis} ({$sDescricaoVagasParcialContador})*",
                "LBR",
                0,
                'C',
                1
            );
        } else {
            $this->oPdf->cell($largura + 6, $altura, "{$contadorVagasDisponiveis}", "LBR", 0, 'C', 1);
        }

        $this->contadorTurmasPorEscolaGlobal += $contadorTurmas;
        $this->contadorVagasTurmasPorEscolaGlobal += $contadorVagasTurmas;
        $this->contadorMatriculasPorEscolaGlobal += $contadorMatriculas;
        $this->contadorMatriculasNeePorEscolaGlobal += $contadorMatriculasNee;
        $this->contadorVagasDisponiveisPorEscolaGlobal += $contadorVagasDisponiveis;

        if (!empty($contadorVagasParciais)) {
            foreach ($contadorVagasParciais as $turno => $vagas) {
                $this->contadorVagasDisponiveisParciaisGlobal[$turno] += $vagas;
            }
        }

        //Condicao caso não tenha turma integral e infantil
        if (!empty($contadorVagasParciais)) {
            $this->oPdf->cell(1, $altura, "", 0, 1, "R", 0);
            $this->oPdf->setfont('arial', '', 6);
            $this->oPdf->cell($largura * 13, $altura,
                                "Vagas em (*) correspondem a Vagas Parciais em turmas com Turno Integral", 0, 1, "R", 0);
            $this->oPdf->cell(1, 0, "", 0, 1, "R", 0);
            $this->oPdf->cell($largura * 13, $altura - 1,
                                "T = Tarde / M = Manhã", 0, 1, "R", 0);
            $this->oPdf->setfont('arial', 'b', 6);

            // Insere linha em branco para dar um espaço entre cada tabela de escola
            $this->oPdf->cell($largura, $altura / 4, "", 0, 1, "C", 0);
        } else {
            // Insere linha em branco para dar um espaço entre cada tabela de escola
            $this->oPdf->cell($largura, $altura, "", 0, 1, "C", 0);
        }
    }

    public function gerarRelatorio() // Função para gerar pdf chamado no edu2_QuadroDeVagas002.php
    {
        $this->oPdf->Output();
    }



    public function ordenarString($a, $b)
    {
        $aString = substr($a, strpos($a, " "));
        $bString = substr($b, strpos($b, " "));

        $aNumber = substr($a, 0, strpos($a, " "));
        $bNumber = substr($b, 0, strpos($b, " "));

        $a = romanoParaInteiro(trim($aString)) > 0
            ? romanoParaInteiro(trim($aString)) . " " . $aNumber
            : $aString . " " . $aNumber;
        $b = romanoParaInteiro(trim($bString)) > 0
            ? romanoParaInteiro(trim($bString)) . " " . $bNumber
            : $bString . " " . $bNumber;

        if ($a == $b) {
            return 0;
        }

        return ($a < $b) ? -1 : 1;
    }

    public function ordenarStringMultisseriada($a, $b)
    {
        $aString = substr(key($a), strpos(key($a), " "));
        $bString = substr(key($b), strpos(key($b), " "));

        $aNumber = substr(key($a), 0, strpos(key($a), " "));
        $bNumber = substr(key($b), 0, strpos(key($b), " "));

        $a = romanoParaInteiro(trim($aString)) > 0
            ? romanoParaInteiro(trim($aString)) . " " . $aNumber
            : $aString . " " . $aNumber;
        $b = romanoParaInteiro(trim($bString)) > 0
            ? romanoParaInteiro(trim($bString)) . " " . $bNumber
            : $bString . " " . $bNumber;

        if ($a == $b) {
            return 0;
        }

        return ($a < $b) ? -1 : 1;
    }

    public function romanoParaInteiro($numRoman, $debug = false)
    {
        $nRoman = $numRoman;
        $default = array(
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1,
        );

        $int = 0;
        foreach ($default as $key => $value) {
            while (strpos($numRoman, $key) === 0) {
                $int += $value;
                $numRoman = substr($numRoman, strlen($key));
            }
        }

        if ($debug) {
            return sprintf('%s = %s', $nRoman, $int);
        }

        return $int;
    }
}
