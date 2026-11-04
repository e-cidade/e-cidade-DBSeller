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
class RelatorioQuadroDeVagasBairro extends EstatisticaAlunosMatriculados
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
    private $aTurmasMulti = [];

    /**
     * @var array
     */
    private $aTurmasRegulares = [];

    /**
     * @var int
     */
    private $contadorTurmasPorBairroGlobal;

    /**
     * @var int
     */
    private $contadorVagasTurmasPorBairroGlobal;

    /**
     * @var int
     */
    private $contadorMatriculasPorBairroGlobal;

    /**
     * @var int
     */
    private $contadorMatriculasNeePorBairroGlobal;

    /**
     * @var int
     */
    private $contadorVagasDisponiveisPorBairroGlobal;

    /**
     * Construtor da classe.
     * @param Calendario $oCalendario Instancia do calendário
     * @param array $aEtapa Array com os códigos das etapas a serem filtradas
     * @param Escola $oEscola Instancia da escola
     * @param Bairros $nBairros Nomes do Bairro
     * @param Etapas $nEtapas Dados da Etapa
     * @param Calendarios $iCalendario Dados do Calendario
     * @param Boolean $lPercentual Valida se mostra ou não os percentuais
     */

    public function __construct($aCalendario, $aEtapa, $aEscola, $nBairros, $nEtapas, $iCalendario, $lPercentual = false)
    {
        $iModulo = db_getsession("DB_modulo");
        $contadorCalendario = 0;
        $this->instaciarPdf(); // 2018-06-14  Instancia o PDF

        // 2018-06-14  Ajuste no cabeçalho do relatório para que seja impresso todos os calendários selecionados

        $title3 = "Bairros: {$this->limitaHeaders($nBairros, 35)}";
        $title4 = "Calendários: {$this->limitaHeaders($iCalendario, 60)}";
        $title5 = "Etapas: {$this->limitaHeaders($nEtapas, 60)}";

        $this->oPdf->addTitulo('QUADRO DE VAGAS POR BAIRROS', 1);
        $this->oPdf->addTitulo($title3, 3);
        $this->oPdf->addTitulo($title4, 4);
        $this->oPdf->addTitulo($title5, 5);

        foreach ($aEscola as $oEscola) { // 2018-06-14  Percorre o array das onde estão os objetos escola
            $oCalendario = $aCalendario[$contadorCalendario]; // 2018-06-14  seta o primeiro calendário

            parent::__construct($oCalendario, $aEtapa, $oEscola); // 2018-06-14  Seta os parametros no construtor PAI
            $contadorCalendario++;
            $this->getEstatisticaAlunosMatriculados(); // 2018-06-14  Reliza as busca dos dados na classe PAI
            $this->getPercentual(); // 2018-06-14  não utilizad0
            $this->lPercentual = $lPercentual;

            if(isset($aEtapa[0])){
                $oEtapa = EtapaRepository::getEtapaByCodigo($aEtapa[0]);

                $sDescricaoEtapa = $oEtapa->getNome();
            }

            if (count($aEtapa) > 1) {
                $sDescricaoEtapa = "TODOS";
            }
            if ($contadorCalendario == 1) {
                $this->oPdf->AddPage('P');
            }

            $this->imprimir($oEscola, $oCalendario); // 2018-06-14  Monta as tabelas com os dados
            $this->oPdf->CheckPageBreak(50);
        }

        $this->totalGeral(
            $this->contadorTurmasPorBairroGlobal,
            $this->contadorVagasTurmasPorBairroGlobal,
            $this->contadorMatriculasPorBairroGlobal,
            $this->contadorMatriculasNeePorBairroGlobal,
            $this->contadorVagasDisponiveisPorBairroGlobal
        );
    }

    /**
     * @param string $header
     * @param int $limiter
     * @return string
     */
    private function limitaHeaders($header, $limiter)
    {
        $sHeader = '';
        $aHeaders = explode(",", $header);
        sort($aHeaders);

        if (mb_strlen($header) > $limiter) {
            $aArrayKeys = array_keys($aHeaders);
            $iLastArrayKey = array_pop($aArrayKeys);
            foreach ($aHeaders as $key => $value) {
                if ($key === $iLastArrayKey || mb_strlen($sHeader) > $limiter) {
                    $sHeader .= array_shift($aHeaders);
                    empty($aHeaders)
                        ? $sHeader .= "."
                        : $sHeader .= " [...+" . count($aHeaders) . "]";

                    break;
                }

                $sHeader .= array_shift($aHeaders) . ", ";
            }

            return $sHeader;
        }

        return implode(", ", $aHeaders) . ".";
    }

    public function instaciarPdf()
    {
        $this->oPdf = new ECidade\Pdf\Pdf();
        $this->oPdf->setfont('arial', '', 2);
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
        $contadorVagasDisponiveis
    ) {
        // Rodapé Total Geral
        $altura = 4;
        $largura = 17;

        $total_geral_turmas = $contadorTurmas;
        $total_geral_vagas_turmas = $contadorVagasTurmas;
        $total_geral_matriculas = $contadorMatriculas;
        $total_geral_matriculas_nee = $contadorMatriculasNee;
        $total_geral_vagas_disponiveis = $contadorVagasDisponiveis;

        $this->oPdf->SetY($this->oPdf->GetY() + 8);
        $this->oPdf->setfillcolor(self::COR_ETAPA);
        $this->oPdf->setfont('arial', 'b', 7);
        $this->oPdf->cell($largura + 2, $altura, "TOTAL GERAL", 'LBT', 0, 'C', 1);
        $this->oPdf->setfont('arial', 'b', 7);
        $this->oPdf->cell($largura * 3.2, $altura, "{$total_geral_turmas}", "LBT", 0, 'C', 1);
        $this->oPdf->cell($largura + 3, $altura, "{$total_geral_vagas_turmas}", "LBT", 0, 'C', 1);
        $this->oPdf->cell($largura, $altura, "{$total_geral_matriculas}", "LBT", 0, 'C', 1);
        $this->oPdf->cell($largura, $altura, "{$total_geral_matriculas_nee}", "LBT", 0, 'C', 1);
        $this->oPdf->cell($largura * 2, $altura, "", "LBT", 0, 'C', 1);
        $this->oPdf->cell($largura - 5, $altura, "{$total_geral_vagas_disponiveis}", 1, 1, 'C', 1);
    }

    /**
     * Método responsável por montar a linhas do pdf de acordo com as Turmas de cada Etapa por Ensino
     */

    public function imprimir($escola, $calendario)
    {
        $this->oPdf->setX(10);
        $altura = 4;
        $largura = 17;

        $contatos = " ";
        for ($i = 0; $i < sizeof($escola->getTelefones()); $i++) {
            $contatos .= "(" . $escola->getTelefones()[$i]->iDDD . ") " . $escola->getTelefones()[$i]->iNumero . " ";
        }

        $this->oPdf->setfillcolor(self::COR_ETAPA);
        $this->oPdf->setfont('arial', 'b', 7);
        $this->oPdf->cell(100, $altura, "", 0, 0, "L", 0);
        $this->oPdf->cell(1, $altura, "", 0, 0, "R", 0);
        $this->oPdf->cell(74, $altura, "", 0, 1, "R", 0);
        $this->oPdf->cell($largura * 8, $altura, "{$escola->getCodigo()} - {$escola->getNome()}   ({$calendario->getDescricao()}) ", 0, 0, "L", 0);
        $this->oPdf->cell(1, $altura, "", 0, 1, "R", 0);
        $this->oPdf->cell($largura * 4, $altura, "BAIRRO: {$escola->getBairro()}", 0, 0, "L", 0);
        $this->oPdf->cell($largura * 4, $altura, "TELEFONE: $contatos", 0, 1, "R", 0);

        // 2018-06-11 Seta cabeçalho das colunas
        $this->oPdf->setfillcolor(self::COR_ETAPA);
        $this->oPdf->setfont('arial', 'b', 7);
        $this->oPdf->cell($largura + 2, $altura, "SÉRIE", 1, 0, "C", 1);
        $this->oPdf->cell($largura * 3.2, $altura, "TURMA", 1, 0, "C", 1);
        $this->oPdf->cell($largura + 3, $altura, "VAGAS TURMA", 1, 0, "C", 1);
        $this->oPdf->cell($largura, $altura, "MATRÍCULA", 1, 0, "C", 1);
        $this->oPdf->cell($largura, $altura, 'INCLUSÃO', 1, 0, "C", 1);
        $this->oPdf->cell($largura, $altura, 'TP TURMA', 1, 0, "C", 1);
        $this->oPdf->cell($largura, $altura, 'TURNO', 1, 0, "C", 1);
        $this->oPdf->cell($largura - 5, $altura, "V. DISP", 1, 1, "C", 1);

        // Array para guardar turmas multietapas únicas
        $aTurmasMultiProcessadas = [];
        $this->aTurmasRegulares = [];
        $this->aTurmasMulti = [];

        foreach ($this->aEnsino as $oEnsino) {
            foreach ($oEnsino->aEtapa as $oEtapa) {
                foreach ($oEtapa->aTurmas as $oTurma) {
                    $modelTurma = TurmaRepository::getTurmaByCodigo($oTurma->iCodigo);
                    $aModelTurmaEtapas = $modelTurma->getEtapas();

                    // Monta as turmas regulares (que não são multietapas)
                    if (count($aModelTurmaEtapas) == 1) {
                        $this->aTurmasRegulares[$oTurma->iCodigo] = (object)[
                            'etapa_turma_regular' => $oEtapa->sNome,
                            'nome_turma_regular' => $oTurma->sTurma,
                            'vagas_turma_regular' => $oTurma->total_vagas,
                            'matriculas_turma_regular' => $oTurma->matriculas_efetivas,
                            'matriculas_nee_turma_regular' => $oTurma->matriculas_nee,
                            'tipo_turma_regular' => '',
                            'turno_turma_regular' => $oTurma->sTurno,
                            'vagas_disponiveis_turma_regular' => $oTurma->total_disponiveis
                        ];
                    }

                    // Monta as turmas multietapa
                    if (count($aModelTurmaEtapas) > 1) {
                        if (in_array($oTurma->iCodigo, $aTurmasMultiProcessadas)) {
                            continue;
                        }

                        $aTurmasMultiProcessadas[] = $oTurma->iCodigo;
                    }
                }
            }
        }

        foreach ($this->aEnsino as $oEnsino) {
            foreach ($oEnsino->aEtapa as $oEtapa) {
                foreach ($oEtapa->aTurmas as $oTurma) {
                    if (in_array($oTurma->iCodigo, $aTurmasMultiProcessadas)) {
                        $sql = "select ed132_censoetapa from turmacensoetapa where ed132_turma = {$oTurma->iCodigo}";
                        $etapaCenso = pg_query($sql);
                        $etapaCenso = pg_fetch_assoc($etapaCenso);
                        $etapaCenso = $etapaCenso['ed132_censoetapa'];
                        $modelTurma = TurmaRepository::getTurmaByCodigo($oTurma->iCodigo);
                        $aModelTurmaEtapas = $modelTurma->getEtapas();

                        if (!isset($etapaCenso)) {
                            continue;
                        }

                        if ($etapaCenso == 1) {
//                            $key = 1; // multi_creche
                            $nomeEtapaCenso = "ED. INF. CRECHE";
                        } elseif ($etapaCenso == 2) {
//                            $key = 2; // multi_pre
                            $nomeEtapaCenso = "ED. INF. PRÉ";
                        } elseif (in_array($etapaCenso, [14, 15, 16, 17, 18, 19, 20, 21, 22])) {
//                            $key = 4; // multi_anos_iniciais
                            $nomeEtapaCenso = "ENS. FUND. 9 ANOS";
                        } elseif ($etapaCenso == 23) {
//                            $key = 8; // multi_correcao_fluxo
                            $nomeEtapaCenso = "ENS. FUND. COR. FLUXO";
                        } elseif (in_array($etapaCenso, [25, 26, 27, 28])) {
//                            $key = 9; // multi_ens_medio
                            $nomeEtapaCenso = "ENS. MÉD. NORMAL";
                        } elseif (in_array($etapaCenso, [35, 36, 37, 38])) {
//                            $key = 10; // multi_ens_medio_magisterio
                            $nomeEtapaCenso = "ENS. MÉD. MAGISTÉRIO";
                        } elseif ($etapaCenso == 56) {
//                            $key = 3; // multi_infantil_e_fundamental
                            $nomeEtapaCenso = "ED. INF. ENS. FUND.";
                        } elseif (in_array($etapaCenso, [43, 69])) {
//                            $key = 6; // multi_eja_fundamental_iniciais
                            $nomeEtapaCenso = "EJA INICIAIS";
                        } elseif (in_array($etapaCenso, [44, 70])) {
//                            $key = 7; // multi_eja_fundamental_finais
                            $nomeEtapaCenso = "EJA FINAIS";
                        } elseif ($etapaCenso == 65) {
//                            $key = 11; // multi_ens_fund_eja_projovem
                            $nomeEtapaCenso = "EJA PROJOVEM";
                        } else {
//                            $key = "outros";
                            $nomeEtapaCenso = "OUTROS";
                        }

                        if (!array_key_exists($oTurma->iCodigo, $this->aTurmasMulti)) {
                            $this->aTurmasMulti[$oTurma->iCodigo] = (object)[
                                'etapa_turma_multi' => $nomeEtapaCenso,
                                'nome_turma_multi' => $oTurma->sTurma,
                                'vagas_turma_multi' => $oTurma->total_vagas,
                                'matriculas_turma_multi' => $oTurma->matriculas_efetivas,
                                'matriculas_nee_turma_multi' => $oTurma->matriculas_nee,
                                'tipo_turma_multi' => 'MULTI',
                                'turno_turma_multi' => $oTurma->sTurno,
                                'vagas_disponiveis_turma_multi' => $oTurma->total_disponiveis
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
        asort($this->aTurmasMulti);
        $contadorTurmas = 0;
        $contadorVagasTurmas = 0;
        $contadorMatriculas = 0;
        $contadorMatriculasNee = 0;
        $contadorVagasDisponiveis = 0;

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
                $this->oPdf->setfont('arial', 'b', 7);
                $this->oPdf->cell(100, $altura, "", 0, 0, "L", 0);
                $this->oPdf->cell(1, $altura, "", 0, 0, "R", 0);
                $this->oPdf->cell(74, $altura, "", 0, 1, "R", 0);
                $this->oPdf->cell($largura * 8, $altura, "{$escola->getCodigo()} - {$escola->getNome()}   ({$calendario->getDescricao()}) ", 0, 0, "L", 0);
                $this->oPdf->cell(1, $altura, "", 0, 1, "R", 0);
                $this->oPdf->cell($largura * 4, $altura, "BAIRRO: {$escola->getBairro()}", 0, 0, "L", 0);
                $this->oPdf->cell($largura * 4, $altura, "TELEFONE: $contatos", 0, 1, "R", 0);

                // Seta cabeçalho das colunas
                $this->oPdf->setfillcolor(self::COR_ETAPA);
                $this->oPdf->setfont('arial', 'b', 7);
                $this->oPdf->cell($largura + 2, $altura, "SÉRIE", 1, 0, "C", 1);
                $this->oPdf->cell($largura * 3.2, $altura, "TURMA", 1, 0, "C", 1);
                $this->oPdf->cell($largura + 3, $altura, "VAGAS TURMA", 1, 0, "C", 1);
                $this->oPdf->cell($largura, $altura, "MATRÍCULA", 1, 0, "C", 1);
                $this->oPdf->cell($largura, $altura, "INCLUSÃO", 1, 0, "C", 1);
                $this->oPdf->cell($largura, $altura, "TP TURMA", 1, 0, "C", 1);
                $this->oPdf->cell($largura, $altura, "TURNO", 1, 0, "C", 1);
                $this->oPdf->cell($largura - 5, $altura, "V. DISP", 1, 1, "C", 1);
            }

            $this->oPdf->setfillcolor(self::COR_TURMA);
            $this->oPdf->setfont('arial', '', 6);

            $this->oPdf->cell($largura + 2, $altura, "{$oTurmaRegular->etapa_turma_regular}", "LB", 0, "L", 0);
            $this->oPdf->cell($largura * 3.2, $altura, "{$oTurmaRegular->nome_turma_regular}", "LB", 0, "C", 0);
            $this->oPdf->cell($largura + 3, $altura, "{$oTurmaRegular->vagas_turma_regular}", "LB", 0, "C", 0);
            $this->oPdf->cell($largura, $altura, "{$oTurmaRegular->matriculas_turma_regular}", "LB", 0, "C", 0);
            $this->oPdf->cell($largura, $altura, "{$oTurmaRegular->matriculas_nee_turma_regular}", "LB", 0, "C", 0);
            $this->oPdf->cell($largura, $altura, "{$oTurmaRegular->tipo_turma_regular}", "LB", 0, "C", 0);

            // Ajusta o tamanho da fonte dinamicamente
            $content = "{$oTurmaRegular->turno_turma_regular} ";
            $tamanhoString = $this->oPdf->GetStringWidth($content);

            if ($tamanhoString > $largura) {
                $tamanhoFonte = 8 * $largura / $tamanhoString;
                $this->oPdf->SetFontSize($tamanhoFonte);
            }

            $this->oPdf->cell($largura, $altura, "{$oTurmaRegular->turno_turma_regular}", "LB", 0, "C", 0);

            $this->oPdf->setfont('arial', 'b', 7);
            $this->oPdf->cell($largura - 5, $altura, "{$oTurmaRegular->vagas_disponiveis_turma_regular}", "LBR", 1, "C", 0);
            $this->oPdf->setfont('arial', '', 6);

            $contadorTurmas++;
            $contadorVagasTurmas += $oTurmaRegular->vagas_turma_regular;
            $contadorMatriculas += $oTurmaRegular->matriculas_turma_regular;
            $contadorMatriculasNee += $oTurmaRegular->matriculas_nee_turma_regular;
            $contadorVagasDisponiveis += $oTurmaRegular->vagas_disponiveis_turma_regular;
        }

        // Imprime turmas multietapa
        foreach ($this->aTurmasMulti as $oTurmaMulti) {
            $this->oPdf->setfont('arial', '', 6);

            // Ajusta o tamanho da fonte dinamicamente
            $content = "{$oTurmaMulti->etapa_turma_multi} ";
            $tamanhoString = $this->oPdf->GetStringWidth($content);

            if ($tamanhoString > $largura) {
                $tamanhoFonte = 7 * $largura / $tamanhoString;
                $this->oPdf->SetFontSize($tamanhoFonte);
            }

            $this->oPdf->cell($largura + 2, $altura, "{$oTurmaMulti->etapa_turma_multi}", "LB", 0, "L", 0);
            $this->oPdf->setfont('arial', '', 6);
            $this->oPdf->cell($largura * 3.2, $altura, "{$oTurmaMulti->nome_turma_multi}", "LB", 0, "C", 0);
            $this->oPdf->cell($largura + 3, $altura, "{$oTurmaMulti->vagas_turma_multi}", "LB", 0, "C", 0);
            $this->oPdf->cell($largura, $altura, "{$oTurmaMulti->matriculas_turma_multi}", "LB", 0, "C", 0);
            $this->oPdf->cell($largura, $altura, "{$oTurmaMulti->matriculas_nee_turma_multi}", "LB", 0, "C", 0);
            $this->oPdf->cell($largura, $altura, "{$oTurmaMulti->tipo_turma_multi}", "LB", 0, "C", 0);

            // Ajusta o tamanho da fonte dinamicamente
            $content = "{$oTurmaMulti->turno_turma_multi} ";
            $tamanhoString = $this->oPdf->GetStringWidth($content);

            if ($tamanhoString > $largura) {
                $tamanhoFonte = 8 * $largura / $tamanhoString;
                $this->oPdf->SetFontSize($tamanhoFonte);
            }

            $this->oPdf->cell($largura, $altura, "{$oTurmaMulti->turno_turma_multi}", "LB", 0, "C", 0);

            $this->oPdf->setfont('arial', 'b', 7);
            $this->oPdf->cell($largura - 5, $altura, "{$oTurmaMulti->vagas_disponiveis_turma_multi}", "LBR", 1, "C", 0);
            $this->oPdf->setfont('arial', '', 6);

            $contadorTurmas++;
            $contadorVagasTurmas += $oTurmaMulti->vagas_turma_multi;
            $contadorMatriculas += $oTurmaMulti->matriculas_turma_multi;
            $contadorMatriculasNee += $oTurmaMulti->matriculas_nee_turma_multi;
            $contadorVagasDisponiveis += $oTurmaMulti->vagas_disponiveis_turma_multi;
        }

        // Rodapé
        $this->oPdf->setfillcolor(self::COR_ETAPA);
        $this->oPdf->setfont('arial', 'b', 7);
        $this->oPdf->cell($largura + 2, $altura, "SUBTOTAL", "LB", 0, 'C', 1);
        $this->oPdf->cell($largura * 3.2, $altura, "{$contadorTurmas}", "LB", 0, 'C', 1);
        $this->oPdf->cell($largura + 3, $altura, "{$contadorVagasTurmas}", "LB", 0, 'C', 1);
        $this->oPdf->cell($largura, $altura, "{$contadorMatriculas}", "LB", 0, 'C', 1);
        $this->oPdf->cell($largura, $altura, "{$contadorMatriculasNee}", "LBR", 0, 'C', 1);
        $this->oPdf->cell($largura, $altura, "", "B", 0, "C", 1);
        $this->oPdf->cell($largura, $altura, "", "B", 0, "C", 1);
        $this->oPdf->cell($largura - 5, $altura, "{$contadorVagasDisponiveis}", "LBR", 0, 'C', 1);

        $this->contadorTurmasPorBairroGlobal += $contadorTurmas;
        $this->contadorVagasTurmasPorBairroGlobal += $contadorVagasTurmas;
        $this->contadorMatriculasPorBairroGlobal += $contadorMatriculas;
        $this->contadorMatriculasNeePorBairroGlobal += $contadorMatriculasNee;
        $this->contadorVagasDisponiveisPorBairroGlobal += $contadorVagasDisponiveis;

        // Insere linha em branco para dar um espaço entre cada tabela de escola
        $this->oPdf->cell($largura, $altura, "", 0, 1, "C", 0);
    }

    public function gerarRelatorio() // Função para gerar pdf chamado no edu2_QuadroDeVagas002.php
    {
        $this->oPdf->Output('I', 'Relatório Quadro de Vagas Geral por Bairros', false);
    }
}

function ordenarString($a, $b)
{
    $aString = substr($a, strpos($a, " "));
    $bString = substr($b, strpos($b, " "));

    $aNumber = substr($a, 0, strpos($a, " "));
    $bNumber = substr($b, 0, strpos($b, " "));


    $a = romanoParaInteiro(trim($aString)) > 0 ? romanoParaInteiro(trim($aString)) . " " . $aNumber : $aString . " " . $aNumber;
    $b = romanoParaInteiro(trim($bString)) > 0 ? romanoParaInteiro(trim($bString)) . " " . $bNumber : $bString . " " . $bNumber;

    if ($a == $b) {
        return 0;
    }

    return ($a < $b) ? -1 : 1;
}

function ordenarStringMultisseriada($a, $b)
{
    $aString = substr(key($a), strpos(key($a), " "));
    $bString = substr(key($b), strpos(key($b), " "));

    $aNumber = substr(key($a), 0, strpos(key($a), " "));
    $bNumber = substr(key($b), 0, strpos(key($b), " "));

    $a = romanoParaInteiro(trim($aString)) > 0 ? romanoParaInteiro(trim($aString)) . " " . $aNumber : $aString . " " . $aNumber;
    $b = romanoParaInteiro(trim($bString)) > 0 ? romanoParaInteiro(trim($bString)) . " " . $bNumber : $bString . " " . $bNumber;

    if ($a == $b) {
        return 0;
    }

    return ($a < $b) ? -1 : 1;
}

function romanoParaInteiro($numRoman, $debug = false)
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
