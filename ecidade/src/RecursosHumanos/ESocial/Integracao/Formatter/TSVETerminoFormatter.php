<?php

/**
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
 *  junto com este programa; se nao, escreva para a Free Softwareb
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace ECidade\RecursosHumanos\ESocial\Integracao\Formatter;

use CalculoFolha;
use CgmJuridico;
use DBCompetencia;
use ECidade\RecursosHumanos\ESocial\Repository\ESocialRubricasRepository;
use ECidade\RecursosHumanos\ESocial\Repository\PagamentosRendimentosTrabalho as PagamentosRendTrabalhoRepository;
use Servidor;
use ServidorRepository;
use stdClass;
use ECidade\RecursosHumanos\ESocial\Repository\TrabalhadorSemVinculoInicio;

use DBPessoal;

/**
 * Formata os dados do Cargo
 *
 * @package ECidade\RecursosHumanos\ESocial\Integracao\Formatter
 * @author Augusto Oliveira <augusto.oliveira@dbseller.com.br>
 */
class TSVETerminoFormatter extends Formatter
{
    /**
     * Realiza a formatação dos dados para envio da API
     *
     * @param array $dados
     * @return array
     */

    /**
     * @var Servidor
     */
    private $servidorAtual;

    /**
     * @var EventoFinanceiroFolha[]
     */
    private $eventosRescisao = [];
    /**
     * @var CgmJuridico
     */
    private $empregador;
    private $rubricaPensaoAlimenticia;
    private $rubricasRepository;
    private $rubricasValidas;

    public function formatar($dados)
    {
        $dadosServidor = [];
        foreach ($dados as $servidor) {
            if (!$servidor->temVinculoEmpregaticio() && $servidor->isRescindido()) {
                if (\DBPessoal::getDataFaseEsocial(2)->getDate() <= $servidor->getDadosRescisao()->rh05_recis) {
                    $dadosServidor[] = $this->processamento($servidor);
                }
            }
        }
        return $dadosServidor;
    }

    public function processamento($servidor)
    {
        /**
         * Preenche dados das rúbricas relacionada ao Evento.
         */

        $this->rubricasRepository = new ESocialRubricasRepository();
        $this->rubricasValidas = $this->rubricasRepository->validarRubricas('2399');

        $this->servidorAtual = $servidor;
        $dadoServidor = new stdClass();
        $dadoServidor->referencia = $this->servidorAtual->getDadosRescisao()->rh05_codigorescisao;
        $dadoServidor->inscricao_empregador = $this->getEmpregador()->getCnpj();
        $this->atualizarDadosServidor($dadoServidor);
        return $dadoServidor;
    }

    private function atualizarDadosServidor(&$dadoServidor)
    {
        $this->getDadosTrabSemVinculo($dadoServidor);
        $this->getDadosRescisao($dadoServidor);
        $this->getDadosVerbasResc($dadoServidor);
        $this->getDadosQuarentena($dadoServidor);
    }

    private function getDadosTrabSemVinculo(&$dadoServidor)
    {
        $dadoServidor->ideTrabSemVinculo = new stdClass();
        $dadoServidor->ideTrabSemVinculo->cpfTrab = $this->servidorAtual->getCgm()->getCpf();
        if ($this->validaEnvioMatricula($this->servidorAtual->getMatricula(), $this->getEmpregador())) {
            $dadoServidor->ideTrabSemVinculo->matricula = $this->servidorAtual->getMatricula();
        }
        if ($this->servidorAtual->getMatriculaAnterior() != "") {
            $dadoServidor->ideTrabSemVinculo->matricula = $this->servidorAtual->getMatriculaAnterior();
        }
        $dadoServidor->ideTrabSemVinculo->codCateg = (int) $this->servidorAtual->getVinculo()->getCodigoCategoria();

        if (isset($dadoServidor->ideTrabSemVinculo->matricula) && !empty($dadoServidor->ideTrabSemVinculo->matricula)) {
            unset($dadoServidor->ideTrabSemVinculo->codCateg);
        }
        if (isset($dadoServidor->ideTrabSemVinculo->codCateg) && !empty($dadoServidor->ideTrabSemVinculo->codCateg)) {
            unset($dadoServidor->ideTrabSemVinculo->matricula);
        }
    }

    private function getDadosRescisao(&$dadoServidor)
    {
        $dadoServidor->infoTSVTermino = new stdClass();

        $dadoServidor->infoTSVTermino->dtTerm = $this->servidorAtual->getDadosRescisao()->rh05_recis;

        /* Não necessita Preenchimento.
        $dadoServidor->infoTSVTermino->mtvDesligTSV = str_pad(
            $this->servidorAtual->getDadosRescisao()->r59_motivoesocial,
            2,
            '0',
            STR_PAD_LEFT
        );*/

        $sql = "
            select
                r20_anousu as ano, r20_mesusu as mes
            from
                pessoal.gerfres
            where
                r20_regist = {$this->servidorAtual->getMatricula()}
                and r20_instit = {$this->servidorAtual->getCodigoInstituicao()}
            limit 1
            ";
        $rs = db_query($sql);

        if (!$rs) {
            $msg = "Ocorreu um erro ao buscar informações da competência de pagamento de rescisão da "
                . "matrícula: {$this->servidorAtual->getMatricula()}.";
            throw new \DBException($msg);
        }

        if (pg_num_rows($rs) > 0) {
            $ano = \db_utils::fieldsMemory($rs, 0)->ano;
            $mes = \db_utils::fieldsMemory($rs, 0)->mes;

            if ($this->servidorAtual->getAnoCompetencia() != $ano
                || $this->servidorAtual->getMesCompetencia() != $mes) {
                $this->servidorAtual = ServidorRepository::getInstanciaByCodigo(
                    $this->servidorAtual->getMatricula(),
                    $ano,
                    $mes,
                    $this->servidorAtual->getCodigoInstituicao()
                );
            }
        }

        $this->inicializaRubricaPensaoAlimenticia();

        $this->inicializaEventosFinanceiros();
        if ($this->servidorAtual->isCeletista() ||
            $this->servidorAtual->validarEnvioPensaoAlimenticia()) {
            if ($this->servidorPossuiPensaoAlimenticia()) {
                $dadoServidor->infoTSVTermino->pensAlim = 2;
                foreach ($this->eventosRescisao as $evento) {
                    if (in_array($evento->getRubrica()->getCodigo(), $this->rubricaPensaoAlimenticia)) {
                        $dadoServidor->infoTSVTermino->vrAlim = $this->truncar($evento->getValor());
                        break;
                    }
                }
            } else {
                $dadoServidor->infoTSVTermino->pensAlim = 0;
            }

            /**
             * Informação obrigatória e exclusiva se pensAlim = [1, 3]
             */
            if ($dadoServidor->infoTSVTermino->pensAlim >= 1 && $dadoServidor->infoTSVTermino->pensAlim <= 3) {
                $dadoServidor->infoTSVTermino->percAliment = '';
            }
        }

        $dadoServidor->infoTSVTermino->nrProcTrab = '';
        $mudancaCPF = new stdClass();
        $dadoServidor->infoTSVTermino->mudancaCPF = $mudancaCPF;
        $dadoServidor->infoTSVTermino->mudancaCPF->novoCPF = '';
        $this->regraDadosRescisao($dadoServidor);
    }

    private function getDadosVerbasResc(&$dadoServidor)
    {
        $verbasResc = $this->verbasRescisao();
        if ($verbasResc) {
            $dadoServidor->infoTSVTermino->verbasResc = $verbasResc;
        }
        $this->regraVerbasResc($dadoServidor);
    }

    private function verbasRescisao()
    {
        $retorno = false;
        if ($this->servidorAtual->validacaoEnvioRescisaoEsocialSemVinculo()) {
            return $retorno;
        }

        $verbasResc = new stdClass();

        $dmDev = new stdClass();
        $ideEstabLot = new stdClass();
        $ideEstabLot->tpInsc = 1;
        $ideEstabLot->nrInsc = $this->getEmpregador()->getCnpj();
        $ideEstabLot->codLotacao = '01';
        $anoRescisao = (int) substr($this->servidorAtual->getDadosRescisao()->rh05_recis, 0, 4);
        $mesRescisao = (int) substr($this->servidorAtual->getDadosRescisao()->rh05_recis, 5, 2);
        $dmDev->ideDmDev = $this->servidorAtual->getMatricula() . 'RESC' . $anoRescisao
            . str_pad($mesRescisao, 2, 0, STR_PAD_LEFT);
        $dmDev->ideEstabLot = [];
        $infoRubrica = [];

        foreach ($this->eventosRescisao as $eventoRescisao) {
            if (!empty($this->rubricasValidas[$eventoRescisao->getRubrica()->getCodigo()])) {
                $detVerba = new stdClass();
                $detVerba->codRubr = $eventoRescisao->getRubrica()->getCodigo();
                $detVerba->ideTabRubr = $eventoRescisao->getRubrica()->getCodigo();
                $detVerba->qtdRubr = $this->truncar($eventoRescisao->getQuantidade());
                $detVerba->fatorRubr = $this->truncar($eventoRescisao->getQuantidade());
                $detVerba->vrRubr = $this->truncar($eventoRescisao->getValor());
                if (($anoRescisao == 2021 && $mesRescisao >= 7) or $anoRescisao >= 2022) {
                    $detVerba->indApurIR = 0;
                }
                $infoRubrica[] = $detVerba;
            }
        }
        // Validamos se realmente vai enviar alguma rubrica
        if (sizeof($infoRubrica) > 0) {
            $retorno = true;
            $ideEstabLot->detVerbas = $infoRubrica;
        }

        if ($retorno) {
            $verbasResc->dmDev = [];
            $dmDev->ideEstabLot[] = $ideEstabLot;
            $verbasResc->dmDev[] = $dmDev;
            return $verbasResc;
        }
    }

    private function getDadosQuarentena(&$dadoServidor)
    {
        $dadoServidor->infoTSVTermino->quarentena = new stdClass();
        $dadoServidor->infoTSVTermino->quarentena->dtFimQuar = '';
        $this->regraDadosQuarentena($dadoServidor);
    }

    private function inicializaEventosFinanceiros()
    {
        $this->eventosRescisao = $this->servidorAtual
            ->getCalculoFinanceiro(CalculoFolha::CALCULO_RESCISAO)
            ->getEventosFinanceiros();
    }

    /**
     * @param int $ano
     * @param int $mes
     * @return array
     * @throws \DBException
     */

    private function inicializaRubricaPensaoAlimenticia()
    {
        $competencia = new DBCompetencia(
            $this->servidorAtual->getAnoCompetencia(),
            $this->servidorAtual->getMesCompetencia()
        );

        $this->rubricaPensaoAlimenticia[] = PagamentosRendTrabalhoRepository::buscarParametroRubricaPensaoAlimenticia(
            $competencia
        );
        $this->rubricaPensaoAlimenticia[] = "4" . substr($this->rubricaPensaoAlimenticia[0], 1, 3);
    }

    /**
     * Metodo com a finalidade de verificar se o servidor possui
     * pagamento de pensao alimenticia na competencia
     * @return bool
     */
    private function servidorPossuiPensaoAlimenticia()
    {
        $retorno = false;
        foreach ($this->eventosRescisao as $evento) {
            if (in_array($evento->getRubrica()->getCodigo(), $this->rubricaPensaoAlimenticia)) {
                $retorno = true;
                break;
            }
        }
        return $retorno;
    }

    private function regraDadosRescisao(&$dadoServidor)
    {
        if (empty($dadoServidor->infoTSVTermino->nrProcTrab)) {
            unset($dadoServidor->infoTSVTermino->nrProcTrab);
        }

        if (empty($dadoServidor->infoTSVTermino->mudancaCPF->novoCPF)) {
            unset($dadoServidor->infoTSVTermino->mudancaCPF);
        }
    }

    private function regraVerbasResc(&$dadoServidor)
    {
        if (!isset($dadoServidor->infoTSVTermino->verbasResc) && !empty($dadoServidor->infoTSVTermino->verbasResc)) {
            if (empty($dadoServidor->infoTSVTermino->verbasResc->procJudTrab->tpTrib) &&
                empty($dadoServidor->infoTSVTermino->verbasResc->procJudTrab->nrProcJud) &&
                empty($dadoServidor->infoTSVTermino->verbasResc->procJudTrab->codSusp)) {
                unset($dadoServidor->infoTSVTermino->verbasResc->procJudTrab);
            }

            if (empty($dadoServidor->infoTSVTermino->verbasResc->infoMV->indMV) &&
                empty($dadoServidor->infoTSVTermino->verbasResc->infoMV->remunOutrEmpr->tpInsc) &&
                empty($dadoServidor->infoTSVTermino->verbasResc->infoMV->remunOutrEmpr->nrInsc) &&
                empty($dadoServidor->infoTSVTermino->verbasResc->infoMV->remunOutrEmpr->codCateg) &&
                empty($dadoServidor->infoTSVTermino->verbasResc->infoMV->remunOutrEmpr->vlrRemunOE)) {
                unset($dadoServidor->infoTSVTermino->verbasResc->infoMV);
            }
        }
    }

    private function regraDadosQuarentena(&$dadoServidor)
    {
        if (empty($dadoServidor->infoTSVTermino->quarentena->dtFimQuar)) {
            unset($dadoServidor->infoTSVTermino->quarentena);
        }
    }

    /**
     * Get the value of empregador
     *
     * @return  CgmJuridico
     */
    public function getEmpregador()
    {
        return $this->empregador;
    }

    /**
     * Set the value of empregador
     *
     * @param  CgmJuridico  $empregador
     *
     * @return  self
     */
    public function setEmpregador(CgmJuridico $empregador)
    {
        $this->empregador = $empregador;
    }
}
