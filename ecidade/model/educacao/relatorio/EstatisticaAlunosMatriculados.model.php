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
 *
 * @package    Educacao
 * @subpackage Relatorio
 * @author     André Mello - andre.mello@dbseller.com.br
 * @version    $Revision: 1.8 $
 */
class EstatisticaAlunosMatriculados
{

    /**
     * Recebe o objeto de Calendário
     *
     * @var Calendario
     */
    protected $oCalendario;

    /**
     * Código das Etapas Selecionadas
     *
     * @var array
     */
    private $aEtapa;

    /**
     * Array todas as informações para montar a tela.
     * Contém um array contendo os Ensinos.
     * Contém um array contendo os dados da turma.
     * Contém um array com os resultado dos cálculos feito por turma.
     *
     * @var array
     */
    protected $aEnsino = array();


    /**
     * Recebe o objeto de Escola
     *
     * @var Escola
     */
    protected $oEscola;


    /**
     *
     * @param Calendario $oCalendario
     * @param array      $aEtapa
     * @param Escola     $oEscola
     */
    protected function __construct( Calendario $oCalendario, $aEtapa, Escola $oEscola )
    {

        $this->oCalendario = $oCalendario;
        $this->aEtapa      = $aEtapa;
        $this->oEscola     = $oEscola;
    }

    /**
     * Método responsável por buscar as turmas que possuem o calendário, a escola e a etapa setadas e adicioná-las
     * há um array de turmas.
     *
     * @param integer $iEtapa
     */
    private function getTurmas(Etapa $oEtapa)
    {

        $aTurmaCalendarioEscola = TurmaRepository::getTurmaPorCalendarioEscola($this->oEscola, $this->oCalendario);
        $aTurmasSelecionadas    = array();

        foreach ( $aTurmaCalendarioEscola as $oTurma ) {

            foreach ($oTurma->getEtapas() as $oEtapaTurma) {

                if (count($oTurma->getAlunosMatriculadosNaTurmaPorSerie($oEtapaTurma->getEtapa())) == 0 ) {
                    continue;
                }

                if ($oEtapaTurma->getEtapa()->getCodigo() == $oEtapa->getCodigo() ) {

                    $aTurmasSelecionadas[] = $this->getCalculosTurma($oTurma, $oEtapa->getCodigo());
                }
            }
        }

        ksort($aTurmasSelecionadas);

        return $aTurmasSelecionadas;
    }

    /**
     * Executa os cálculos para buscar os totais de:
     *   - matricula_inicial
     *   - matriculas_evadidas
     *   - matriculas_canceladas
     *   - matriculas_transferidas
     *   - matriculas_progredidas
     *   - matriculas_falecidas
     *   - matriculas_efetivas
     *   - total_vagas
     *   - total_disponiveis
     *   - total_vagas_inteiras (em caso de Integral e Infantil)
     * Em uma turma e busca o nome da turma e o turno da mesma.
     *
     * @param  Turma $oTurma
     * @param  int   $iCodigoEtapa
     * @return array Calculos Por Turma
     */
    private function getCalculosTurma( Turma $oTurma, $iCodigoEtapa )
    {

        /* FHSYS Capacidade da Turma (Sala) */
        $sSqlCapac = "select ed16_i_capacidade from turma inner join sala on ed57_i_sala = ed16_i_codigo where ed57_i_codigo = ".$oTurma->getCodigo();
        $rSqlCapac = db_query($sSqlCapac);
        $capac     = pg_result($rSqlCapac, 0, 0);
        /* FHSYS Capacidade da Turma (Sala) */

        $oEtapa       = EtapaRepository::getEtapaByCodigo($iCodigoEtapa);

        $oDadosTurma                          = new stdClass();
        $oDadosTurma->sTurma                  = $oTurma->getDescricao();
        $oDadosTurma->sTurno                  = $oTurma->getTurno()->getDescricao();
        $oDadosTurma->iCodigo                 = $oTurma->getCodigo();
        $oDadosTurma->lTurnoIntegral          = $oTurma->getTurno()->isIntegral();
        $oDadosTurma->lIsInfantil             = $oTurma->getBaseCurricular()->getCurso()->getEnsino()->isInfantil();
        $oDadosTurma->sAbreviaturaEnsino      = $oTurma->getBaseCurricular()->getCurso()->getEnsino()->getAbreviatura();
        $oDadosTurma->sBaseCurricular         = $oTurma->getBaseCurricular()->getDescricao();
        $oDadosTurma->matricula_inicial       = "0";
        $oDadosTurma->matriculas_evadidas     = "0";
        $oDadosTurma->matriculas_canceladas   = "0";
        $oDadosTurma->matriculas_transferidas = "0";
        $oDadosTurma->matriculas_trocas       = "0";
        $oDadosTurma->matriculas_progredidas  = "0";
        $oDadosTurma->matriculas_falecidas    = "0";
        $oDadosTurma->matriculas_nee          = "0"; /* FHSYS */
        $oDadosTurma->matriculas_efetivas     = "0";
        $oDadosTurma->total_vagas             = "0";
        $oDadosTurma->total_disponiveis       = "0";
        $oDadosTurma->total_capacidade        = "0"; /* FHSYS */
        $oDadosTurma->total_vagas_inteiras    = "0";
        $oDadosTurma->vagas_parciais          = [];

        $aVagas            = $oTurma->getVagas();
        $aVagasDisponiveis = $oTurma->getVagasDisponiveis();
        /* FHSYS Capacidade da Turma (Sala) */
        $aCapacidade       = $capac;
        /* FHSYS Capacidade da Turma (Sala) */

        $oRetorno          = new stdClass();
        $oRetorno->status  = 1;
        $oRetorno->message = '';
        /**
         * Valida se o ensino é infantil de turno integral.
         * Se for verifica as vagas da turma, vagas disponiveis e turnos da turma de acordo com os turnos de referencia
         */
        if ($oEtapa->getEnsino()->isInfantil() && $oTurma->getTurno()->isIntegral() ) {

            $aTurnoReferente = array();

            foreach ($aVagas as $iReferencia => $iVagas) {

                $sTurnoReferente = "";
                switch ($iReferencia) {

                case 1:

                    $sTurnoReferente = "MANHÃ";
                    break;

                case 2:

                    $sTurnoReferente = "TARDE";
                    break;

                case 3:

                      $sTurnoReferente = "NOITE";
                    break;
                }
                $aTurnoReferente[] = $sTurnoReferente;
                $oDadosTurma->total_vagas += $iVagas;
            }
            $oDadosTurma->sTurno .= " - ". implode(" / ", $aTurnoReferente);

            $nTurnoAtual = 0;
            foreach ($aVagasDisponiveis as $iVagasDisponiveis) {
                $oDadosTurma->total_disponiveis += "$iVagasDisponiveis";
                $oDadosTurma->vagas_parciais[$aTurnoReferente[$nTurnoAtual]] = $iVagasDisponiveis;
                $nTurnoAtual++;
            }
            /* FHSYS Capacidade da Turma (Sala) */
            foreach ($aCapacidade as $iCapacidade) {
                $oDadosTurma->total_capacidade += "$iCapacidade";
            }
            $oDadosTurma->total_vagas_inteiras = min($aVagasDisponiveis);
            /* FHSYS Capacidade da Turma (Sala) */
        } else {

            /**
             * Se o ensino não foi configurado como infantil, mesmo o turno sendo integrel só vamos pegar o número de vagas do
             * primeiro turno de referencia
             */
            foreach ($aVagas as $iVagas) {
                $oDadosTurma->total_vagas = "$iVagas";
                break;
            }
            foreach ($aVagasDisponiveis as $iVagasDisponiveis) {
                $oDadosTurma->total_disponiveis = "$iVagasDisponiveis";
                break;
            }

            /* FHSYS Capacidade da Turma (Sala) */
            $oDadosTurma->total_capacidade = $aCapacidade;

            /* FHSYS Capacidade da Turma (Sala) */
        }


        foreach ($oTurma->getAlunosMatriculadosNaTurmaPorSerie($oEtapa) as $oMatricula) {

            $oDadosTurma->matricula_inicial ++;

            switch ($oMatricula->getSituacao()) {
            case 'ABANDONO ESCOLAR':
            case 'EVADIDO':
                $oDadosTurma->matriculas_evadidas ++;
                break;

            case 'CANCELADO':
                $oDadosTurma->matriculas_canceladas ++;
                break;

            case 'TRANSFERIDO REDE':
            case 'TRANSFERIDO FORA':
            case 'TROCA DE MODALIDADE':
                $oDadosTurma->matriculas_transferidas++;
                break;

            /* FHSYS - CONTABILIZA TROCA DE TURMA */
            case 'TROCA DE TURMA':
                $oDadosTurma->matriculas_trocas++;
                break;

            case 'AVANÇADO':
            case 'RECLASSIFICADO':
            case 'CLASSIFICADO':
                $oDadosTurma->matriculas_progredidas++;
                break;

            case 'FALECIDO':
                $oDadosTurma->matriculas_falecidas ++;
                break;

            case 'MATRICULADO':
                $oDadosTurma->matriculas_efetivas ++;
                /* FHSYS - CONTABILIZA NEE */
                $oAluno = AlunoRepository::getAlunoByCodigo($oMatricula->getAluno()->getCodigoAluno());
                if (count($oAluno->getNecessidadesEspeciais()) > 0) {
                    $oDadosTurma->matriculas_nee ++;
                }
                break;
            }
        }

        return $oDadosTurma;
    }

    /**
     * Percorre as turmas de determinado calendário, escola e etapa e executa os cálculos de totais, retornando
     * um array contendo informações de ensino, etapa, turmas.
     *
     * @return array aEnsino:
     */
    protected function getEstatisticaAlunosMatriculados()
    {

        $aTurmasPercorridas = array();

        foreach ($this->aEtapa as $iEtapa ) {

            $oEtapa        = EtapaRepository::getEtapaByCodigo($iEtapa);

            $iCodigoEnsino = $oEtapa->getEnsino()->getCodigo();

            if (!array_key_exists($iCodigoEnsino, $this->aEnsino) ) {

                $oEnsino                         = new stdClass();
                $oEnsino->iCodigo                = $oEtapa->getEnsino()->getCodigo();;
                $oEnsino->sNome                  = $oEtapa->getEnsino()->getNome();
                $oEnsino->aEtapa                 = array();
                $oEnsino->iTotalMatriculaInicial = 0;
                $oEnsino->iTotalEvadidos         = 0;
                $oEnsino->iTotalCancelados       = 0;
                $oEnsino->iTotalTransferidos     = 0;
                $oEnsino->iTotalTrocas           = 0; /* FHSYS */
                $oEnsino->iTotalProgredidos      = 0;
                $oEnsino->iTotalObitos           = 0;
                $oEnsino->iTotalMatriculaEfetiva = 0;
                $oEnsino->iTotalMatriculaNEE     = 0; /* FHSYS */
                $oEnsino->iTotalVagas            = 0;
                $oEnsino->iTotalVagasDisponiveis = 0;
                $oEnsino->iTotalCapacidade       = 0; /* FHSYS */
                $this->aEnsino[$iCodigoEnsino]   = $oEnsino;
            }

            $oEtapaDados                         = new stdClass();
            $oEtapaDados->iCodigo                = $oEtapa->getCodigo();
            $oEtapaDados->sNome                  = $oEtapa->getNome();
            $oEtapaDados->aTurmas                = array();
            $oEtapaDados->iTotalMatriculaInicial = 0;
            $oEtapaDados->iTotalEvadidos         = 0;
            $oEtapaDados->iTotalCancelados       = 0;
            $oEtapaDados->iTotalTransferidos     = 0;
            $oEtapaDados->iTotalTrocas           = 0; /* FHSYS */
            $oEtapaDados->iTotalProgredidos      = 0;
            $oEtapaDados->iTotalObitos           = 0;
            $oEtapaDados->iTotalMatriculaEfetiva = 0;
            $oEtapaDados->iTotalMatriculaNEE     = 0; /* FHSYS */
            $oEtapaDados->iTotalVagas            = 0;
            $oEtapaDados->iTotalVagasDisponiveis = 0;
            $oEtapaDados->iTotalCapacidade       = 0; /* FHSYS */

            $this->aEnsino[$iCodigoEnsino]->aEtapa[$oEtapa->getOrdem()] = $oEtapaDados;

            ksort($this->aEnsino[$iCodigoEnsino]->aEtapa);

            $this->aEnsino[$iCodigoEnsino]->aEtapa[$oEtapa->getOrdem()]->aTurmas = $this->getTurmas($oEtapa);

            if (count($this->aEnsino[$iCodigoEnsino]->aEtapa[$oEtapa->getOrdem()]->aTurmas) == 0) {

                unset($this->aEnsino[$iCodigoEnsino]->aEtapa[$oEtapa->getOrdem()]);
                if (count($this->aEnsino[$iCodigoEnsino]->aEtapa) == 0) {
                    unset($this->aEnsino[$iCodigoEnsino]);
                }
                continue;
            }

            foreach ($this->aEnsino[$iCodigoEnsino]->aEtapa[$oEtapa->getOrdem()]->aTurmas as $oTurma) {

                $oEtapaDados->iTotalMatriculaInicial += $oTurma->matricula_inicial;
                $oEtapaDados->iTotalEvadidos         += $oTurma->matriculas_evadidas;
                $oEtapaDados->iTotalCancelados       += $oTurma->matriculas_canceladas;
                $oEtapaDados->iTotalTransferidos     += $oTurma->matriculas_transferidas;
                $oEtapaDados->iTotalTrocas           += $oTurma->matriculas_trocas; /* FHSYS */
                $oEtapaDados->iTotalProgredidos      += $oTurma->matriculas_progredidas;
                $oEtapaDados->iTotalObitos           += $oTurma->matriculas_falecidas;
                $oEtapaDados->iTotalMatriculaEfetiva += $oTurma->matriculas_efetivas;
                $oEtapaDados->iTotalMatriculaNEE     += $oTurma->matriculas_nee; /* FHSYS */

                if(!in_array($oTurma->iCodigo, $aTurmasPercorridas) ) {

                    $oEtapaDados->iTotalVagas            += $oTurma->total_vagas;
                    $oEtapaDados->iTotalVagasDisponiveis += $oTurma->total_disponiveis;
                    $oEtapaDados->iTotalCapacidade       += $oTurma->total_capacidade; /* FHSYS */
                    $aTurmasPercorridas[] = $oTurma->iCodigo;
                }
            }

        }

        /**
         * Calcula o total das matrículas dos aluno por ensino
         */
        foreach ($this->aEnsino as $iCodigoEnsino => $oEnsino ) {

            foreach ($oEnsino->aEtapa as $oEtapa) {

                $oEnsino->iTotalMatriculaInicial += $oEtapa->iTotalMatriculaInicial;
                $oEnsino->iTotalEvadidos         += $oEtapa->iTotalEvadidos        ;
                $oEnsino->iTotalCancelados       += $oEtapa->iTotalCancelados      ;
                $oEnsino->iTotalTransferidos     += $oEtapa->iTotalTransferidos    ;
                $oEnsino->iTotalTrocas           += $oEtapa->iTotalTrocas          ; /* FHSYS */
                $oEnsino->iTotalProgredidos      += $oEtapa->iTotalProgredidos     ;
                $oEnsino->iTotalObitos           += $oEtapa->iTotalObitos          ;
                $oEnsino->iTotalMatriculaEfetiva += $oEtapa->iTotalMatriculaEfetiva;
                $oEnsino->iTotalMatriculaNEE     += $oEtapa->iTotalMatriculaNEE    ; /* FHSYS */
                $oEnsino->iTotalVagas            += $oEtapa->iTotalVagas           ;
                $oEnsino->iTotalVagasDisponiveis += $oEtapa->iTotalVagasDisponiveis;
                $oEnsino->iTotalCapacidade       += $oEtapa->iTotalCapacidade      ; /* FHSYS */
            }
        }

        return $this->aEnsino;
    }

    /**
     * Método responsável por executar os cálculos de porcentagem das matriculas dos alunos tanto para o Ensino quanto
     * para a Etapa
     */
    protected function getPercentual()
    {

        foreach ($this->aEnsino as $iCodigoEnsino => $oEnsino ) {

            $oEnsino->iPercentualEvadidos         = 0;
            $oEnsino->iPercentualCancelados       = 0;
            $oEnsino->iPercentualTransferidos     = 0;
            $oEnsino->iPercentualTrocas           = 0; /* FHSYS */
            $oEnsino->iPercentualProgredidos      = 0;
            $oEnsino->iPercentualObitos           = 0;
            $oEnsino->iPercentualMatriculaEfetiva = 0;
            $oEnsino->iPercentualMatriculaNEE     = 0; /* FHSYS */
            $oEnsino->iPercentualVagasDisponiveis = 0;

            if ($oEnsino->iTotalMatriculaInicial != 0 ) {

                $oEnsino->iPercentualEvadidos         = round(($oEnsino->iTotalEvadidos / $oEnsino->iTotalMatriculaInicial) * 100, 2);
                $oEnsino->iPercentualCancelados       = round(($oEnsino->iTotalCancelados / $oEnsino->iTotalMatriculaInicial) * 100, 2);
                $oEnsino->iPercentualTransferidos     = round(($oEnsino->iTotalTransferidos / $oEnsino->iTotalMatriculaInicial) * 100, 2);
                $oEnsino->iPercentualTrocas           = round(($oEnsino->iTotalTrocas / $oEnsino->iTotalMatriculaInicial) * 100, 2); /* FHSYS */
                $oEnsino->iPercentualProgredidos      = round(($oEnsino->iTotalProgredidos / $oEnsino->iTotalMatriculaInicial) * 100, 2);
                $oEnsino->iPercentualObitos           = round(($oEnsino->iTotalObitos / $oEnsino->iTotalMatriculaInicial) * 100, 2);
                $oEnsino->iPercentualMatriculaEfetiva = round(($oEnsino->iTotalMatriculaEfetiva / $oEnsino->iTotalMatriculaInicial) * 100, 2);
                $oEnsino->iPercentualMatriculaNEE     = round(($oEnsino->iTotalMatriculaNEE / $oEnsino->iTotalMatriculaInicial) * 100, 2); /* FHSYS */
                $oEnsino->iPercentualVagasDisponiveis = round(($oEnsino->iTotalVagasDisponiveis / $oEnsino->iTotalVagas) * 100, 2);
            }

            foreach ($oEnsino->aEtapa as $oEtapa) {

                $oEtapa->iPercentualEvadidos         = 0;
                $oEtapa->iPercentualCancelados       = 0;
                $oEtapa->iPercentualTransferidos     = 0;
                $oEtapa->iPercentualTrocas           = 0; /* FHSYS */
                $oEtapa->iPercentualProgredidos      = 0;
                $oEtapa->iPercentualObitos           = 0;
                $oEtapa->iPercentualMatriculaEfetiva = 0;
                $oEtapa->iPercentualMatriculaNEE     = 0; /* FHSYS */
                $oEtapa->iPercentualVagasDisponiveis = 0;

                if ($oEtapa->iTotalMatriculaInicial != 0 ) {

                    $oEtapa->iPercentualEvadidos         = round(($oEtapa->iTotalEvadidos / $oEtapa->iTotalMatriculaInicial) * 100, 2);
                    $oEtapa->iPercentualCancelados       = round(($oEtapa->iTotalCancelados / $oEtapa->iTotalMatriculaInicial) * 100, 2);
                    $oEtapa->iPercentualTransferidos     = round(($oEtapa->iTotalTransferidos / $oEtapa->iTotalMatriculaInicial) * 100, 2);
                    $oEtapa->iPercentualTrocas           = round(($oEtapa->iTotalTrocas / $oEtapa->iTotalMatriculaInicial) * 100, 2); /* FHSYS */
                    $oEtapa->iPercentualProgredidos      = round(($oEtapa->iTotalProgredidos / $oEtapa->iTotalMatriculaInicial) * 100, 2);
                    $oEtapa->iPercentualObitos           = round(($oEtapa->iTotalObitos / $oEtapa->iTotalMatriculaInicial) * 100, 2);
                    $oEtapa->iPercentualMatriculaEfetiva = round(($oEtapa->iTotalMatriculaEfetiva / $oEtapa->iTotalMatriculaInicial) * 100, 2);
                    $oEtapa->iPercentualMatriculaNEE     = round(($oEtapa->iTotalMatriculaNEE / $oEtapa->iTotalMatriculaInicial) * 100, 2); /* FHSYS */

                    if(!empty($oEtapa->iTotalVagasDisponiveis) && !empty($oEtapa->iTotalVagas) ) {
                        $oEtapa->iPercentualVagasDisponiveis = round(($oEtapa->iTotalVagasDisponiveis / $oEtapa->iTotalVagas)  * 100, 2);
                    }
                }
            }
        }
    }

    /**
     * Calcula os totais e as porcentagens das matrículas dos alunos por Ensino
     *
     * @return stdClass $oTotalGeral
     */
    protected function getTotalGeral()
    {

        $oTotalGeral = new stdClass();
        $oTotalGeral->iTotalMatriculaInicial = 0;
        $oTotalGeral->iTotalEvadidos         = 0;
        $oTotalGeral->iTotalCancelados       = 0;
        $oTotalGeral->iTotalTransferidos     = 0;
        $oTotalGeral->iTotalTrocas           = 0; /* FHSYS */
        $oTotalGeral->iTotalProgredidos      = 0;
        $oTotalGeral->iTotalObitos           = 0;
        $oTotalGeral->iTotalMatriculaEfetiva = 0;
        $oTotalGeral->iTotalMatriculaNEE     = 0; /* FHSYS */
        $oTotalGeral->iTotalVagas            = 0;
        $oTotalGeral->iTotalVagasDisponiveis = 0;
        $oTotalGeral->iTotalCapacidade       = 0; /* FHSYS */
        foreach ( $this->aEnsino as $oEnsino ) {

            $oTotalGeral->iTotalMatriculaInicial += $oEnsino->iTotalMatriculaInicial;
            $oTotalGeral->iTotalEvadidos         += $oEnsino->iTotalEvadidos        ;
            $oTotalGeral->iTotalCancelados       += $oEnsino->iTotalCancelados      ;
            $oTotalGeral->iTotalTransferidos     += $oEnsino->iTotalTransferidos    ;
            $oTotalGeral->iTotalTrocas           += $oEnsino->iTotalTrocas          ; /* FHSYS */
            $oTotalGeral->iTotalProgredidos      += $oEnsino->iTotalProgredidos     ;
            $oTotalGeral->iTotalObitos           += $oEnsino->iTotalObitos          ;
            $oTotalGeral->iTotalMatriculaEfetiva += $oEnsino->iTotalMatriculaEfetiva;
            $oTotalGeral->iTotalMatriculaNEE     += $oEnsino->iTotalMatriculaNEE    ; /* FHSYS */
            $oTotalGeral->iTotalVagas            += $oEnsino->iTotalVagas           ;
            $oTotalGeral->iTotalVagasDisponiveis += $oEnsino->iTotalVagasDisponiveis;
            $oTotalGeral->iTotalCapacidade       += $oEnsino->iTotalCapacidade      ; /* FHSYS */
        }

        $oTotalGeral->iPercentualEvadidos         = round(($oTotalGeral->iTotalEvadidos         / $oTotalGeral->iTotalMatriculaInicial) * 100, 2);
        $oTotalGeral->iPercentualCancelados       = round(($oTotalGeral->iTotalCancelados       / $oTotalGeral->iTotalMatriculaInicial) * 100, 2);
        $oTotalGeral->iPercentualTransferidos     = round(($oTotalGeral->iTotalTransferidos     / $oTotalGeral->iTotalMatriculaInicial) * 100, 2);
        $oTotalGeral->iPercentualTrocas           = round(($oTotalGeral->iTotalTrocas           / $oTotalGeral->iTotalMatriculaInicial) * 100, 2);
        $oTotalGeral->iPercentualProgredidos      = round(($oTotalGeral->iTotalProgredidos      / $oTotalGeral->iTotalMatriculaInicial) * 100, 2);
        $oTotalGeral->iPercentualObitos           = round(($oTotalGeral->iTotalObitos           / $oTotalGeral->iTotalMatriculaInicial) * 100, 2);
        $oTotalGeral->iPercentualMatriculaEfetiva = round(($oTotalGeral->iTotalMatriculaEfetiva / $oTotalGeral->iTotalMatriculaInicial) * 100, 2);
        $oTotalGeral->iPercentualMatriculaNEE     = round(($oTotalGeral->iTotalMatriculaNEE     / $oTotalGeral->iTotalMatriculaInicial) * 100, 2); /* FHSYS */
        $oTotalGeral->iPercentualVagasDisponiveis = round(($oTotalGeral->iTotalVagasDisponiveis / $oTotalGeral->iTotalVagas)            * 100, 2);

        return $oTotalGeral;
    }

}

