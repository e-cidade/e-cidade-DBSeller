<?php

namespace ECidade\RecursosHumanos\ESocial\Integracao\Formatter;

use CalculoFolha;
use CgmJuridico;
use DBCompetencia;
use ECidade\RecursosHumanos\ESocial\Repository\ESocialRubricasRepository;
use ECidade\RecursosHumanos\ESocial\Repository\LotacaoServidor;
use ECidade\RecursosHumanos\ESocial\Repository\PagamentosRendimentosTrabalho as PagamentosRendTrabalhoRepository;
use Servidor;
use ServidorRepository;
use stdClass;

/**
 * Class DesligamentoServidorFormatter
 */
class DesligamentoServidorFormatter extends Formatter
{

    private $codigosNaoEnviaDemissaoVoluntaria = [
        '10' => '10',
        '11' => '11',
        '12' => '12',
        '13' => '13',
        '28' => '28',
        '29' => '29',
        '30' => '30',
        '34' => '34',
        '36' => '36',
        '37' => '37',
        '40' => '40',
        '43' => '43',
        '44' => '44'
    ];

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

    private $deParaAgNocivo = [
        0 => 1,
        1 => 1,
        2 => 3,
        3 => 3,
        4 => 4,
        5 => 1
    ];

    /**
     * @param  array $dados
     * @return mixed|stdClass[]
     * @throws \BusinessException
     * @throws \DBException
     */
    public function formatar($servidores)
    {
        $dadosServidor = [];
        $this->rubricasRepository = new ESocialRubricasRepository();
        $this->rubricasValidas = $this->rubricasRepository->validarRubricas('2299');

        foreach ($servidores as $servidor) {
            if ($servidor->temVinculoEmpregaticio() &&  $servidor->isRescindido()) {
                $dadosServidor[] = $this->processamento($servidor);
            }
        }
        return $dadosServidor;
    }

    /**
     * @param  $dadosFormatado
     * @return mixed
     * @throws \BusinessException
     * @throws \DBException
     */
    private function processamento($servidor)
    {
        $dadoServidor = new stdClass();

        $this->servidorAtual = $servidor;
        $dadoServidor->referencia = $this->servidorAtual->getDadosRescisao()->rh05_codigorescisao;
        $dadoServidor->inscricao_empregador = $this->getEmpregador()->getCnpj();
        $dadoServidor->ideVinculo = new stdClass();
        $dadoServidor->ideVinculo->cpfTrab = $this->servidorAtual->getCgm()->getCpf();
        $dadoServidor->ideVinculo->matricula = $this->servidorAtual->getMatricula();
        if ($this->servidorAtual->getMatriculaAnterior() != "") {
            $dadoServidor->ideVinculo->matricula = $this->servidorAtual->getMatriculaAnterior();
        }
        $dadoServidor->infoDeslig = $this->dadosRecisaoServidor();

        return $dadoServidor;
    }

    private function dadosRecisaoServidor()
    {
        $infoDeslig = new stdClass();

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
                || $this->servidorAtual->getMesCompetencia() != $mes
            ) {
                $this->servidorAtual = ServidorRepository::getInstanciaByCodigo(
                    $this->servidorAtual->getMatricula(),
                    $ano,
                    $mes,
                    $this->servidorAtual->getCodigoInstituicao()
                );
            }
        }
        // Inicializamos a rubrica de pensao alimenticia pela competencia do servidor
        $this->inicializaRubricaPensaoAlimenticia();
        // Pegamos o calculo de rescisao
        $this->inicializaEventosFinanceiros();

        $infoDeslig->mtvDeslig = str_pad(
            $this->servidorAtual->getDadosRescisao()->r59_motivoesocial,
            2,
            '0',
            STR_PAD_LEFT
        );

        $motivoEsocial = $this->servidorAtual->getDadosRescisao()->r59_motivoesocial;
        if (!in_array($motivoEsocial, $this->codigosNaoEnviaDemissaoVoluntaria)) {
            if (!empty($this->servidorAtual->getDadosRescisao()->demissaovoluntaria)
                && $this->servidorAtual->getDadosRescisao()->demissaovoluntaria == 't') {
                $infoDeslig->indPDV = 'S';
            }
        }

        $infoDeslig->dtDeslig = $this->servidorAtual->getDadosRescisao()->rh05_recis;

        if (!empty($this->servidorAtual->getDadosRescisao()->rh05_aviso)) {
            if ($this->servidorAtual->getDadosRescisao()->rh05_aviso >=
                $this->servidorAtual->getDadosRescisao()->rh01_admiss
            ) {
                $infoDeslig->dtAvPrv = $this->servidorAtual->getDadosRescisao()->rh05_aviso;
                $infoDeslig->indPagtoAPI = 'S';
                $infoDeslig->dtProjFimAPI = $this->servidorAtual->getDadosRescisao()->rh05_aviso;
            }
        } else {
            $infoDeslig->indPagtoAPI = 'N';
        }

        if ($this->servidorAtual->isCeletista()) {
            if ($this->servidorPossuiPensaoAlimenticia()) {
                $infoDeslig->pensAlim = 2;
                foreach ($this->eventosRescisao as $evento) {
                    if (in_array($evento->getRubrica()->getCodigo(), $this->rubricaPensaoAlimenticia)) {
                        $infoDeslig->vrAlim = $this->truncar($evento->getValor());
                        break;
                    }
                }
            } else {
                $infoDeslig->pensAlim = 0;
            }
        }

        $verbasResc = $this->verbasRescisao();
        if ($verbasResc) {
            $infoDeslig->verbasResc = $verbasResc;
        }
        return $infoDeslig;
    }

    private function verbasRescisao()
    {
        $processamento = false;
        if ($this->servidorAtual->validacaoEnvioRescisaoEsocialComVinculo()) {
            return $processamento;
        }
        $verbasResc = new stdClass();

        $dmDev = new stdClass();
        $anoRescisao = (int) substr($this->servidorAtual->getDadosRescisao()->rh05_recis, 0, 4);
        $mesRescisao = (int) substr($this->servidorAtual->getDadosRescisao()->rh05_recis, 5, 2);
        $dmDev->ideDmDev = $this->servidorAtual->getMatricula() . 'RESC' . $anoRescisao
            . str_pad($mesRescisao, 2, 0, STR_PAD_LEFT);
        $dmDev->infoPerApur = new stdClass();
        $dmDev->infoPerApur->ideEstabLot = [];
        $anoAtual = \DBPessoal::getAnoFolha();
        $mesAtual = \DBPessoal::getMesFolha();
        $lotacoes = LotacaoServidor::buscarLotacaoTributaria(
            $this->servidorAtual,
            $anoRescisao,
            $mesRescisao,
            $anoAtual,
            $mesAtual
        );

        if (count($lotacoes) == 0) {
            $lotacao = new \stdClass();
            $lotacao->dataInicio = null;
            $lotacao->dataFim = null;
            $lotacao->lotacaoTributaria = "";
            $lotacao->dias = 30;
            $lotacao->ultimalotacao = true;
            $lotacoes[] = $lotacao;
        }
        // array de controle de valores pagos das rubricas
        $rubricas = [];
        foreach ($lotacoes as $indice => $lotacao) {
            $ideEstabLot = new stdClass();
            $ideEstabLot->codLotacao = $lotacao->lotacaoTributaria;
            $ideEstabLot->tpInsc = 1;
            $ideEstabLot->nrInsc = $this->getEmpregador()->getCnpj();

            $infoRubrica = [];
            $retorno = false;

            foreach ($this->eventosRescisao as $eventoRescisao) {
                // validamos se a rubrica possui recibo no esocial
                if (!empty($this->rubricasValidas[$eventoRescisao->getRubrica()->getCodigo()])) {
                    // validamos se a rubrica existe no array, caso nao existe inicializamos ela com valor zerado
                    if (empty($rubricas[$eventoRescisao->getRubrica()->getCodigo()])) {
                        $rubricas[$eventoRescisao->getRubrica()->getCodigo()] = 0;
                    }
                    $detVerba = new stdClass();
                    $detVerba->codRubr = $eventoRescisao->getRubrica()->getCodigo();
                    $detVerba->ideTabRubr = $eventoRescisao->getRubrica()->getCodigo();
                    $detVerba->qtdRubr = $this->truncar($eventoRescisao->getQuantidade());
                    $detVerba->fatorRubr = $this->truncar($eventoRescisao->getQuantidade());
                    $valorFinanceiroRescisao = $this->truncar($eventoRescisao->getValor());
                    if (($anoRescisao == 2021 && $mesRescisao >= 7) or $anoRescisao >= 2022) {
                        $detVerba->indApurIR = 0;
                    }
                    $valorRubrica = round((float) $valorFinanceiroRescisao*($lotacao->dias/30), 2);
                    $detVerba->vrRubr = $valorRubrica;
                    if (count($lotacoes) > 1 && $indice == (count($lotacoes)-1)) {
                        $valor = $valorFinanceiroRescisao - $rubricas[$eventoRescisao->getRubrica()->getCodigo()];
                        $detVerba->vrRubr = $valor;
                    }
                    $rubricas[$eventoRescisao->getRubrica()->getCodigo()] += $valorRubrica;
                    $infoRubrica[] = $detVerba;
                }
            }

            // Validamos se realmente vai enviar alguma rubrica
            if (sizeof($infoRubrica) > 0) {
                $retorno = true;
                $ideEstabLot->detVerbas = $infoRubrica;
            }

            $agNocivo = (int) $this->servidorAtual->getTipoExposicaoAgentesNocivos();

            if ((!empty($agNocivo) or $agNocivo === 0) && $this->servidorAtual->isRgps()) {
                $ideEstabLot->infoAgNocivo = new stdClass();
                $ideEstabLot->infoAgNocivo->grauExp = $this->deParaAgNocivo[$agNocivo];
            }
            if ($retorno) {
                $dmDev->infoPerApur->ideEstabLot[] = $ideEstabLot;
                $processamento = true;
            }
        }

        if ($processamento) {
            $verbasResc->dmDev = [];
            $verbasResc->dmDev[] = $dmDev;
            return $verbasResc;
        }
        return $verbasResc;
    }

    private function inicializaEventosFinanceiros()
    {
        $this->eventosRescisao = $this->servidorAtual
            ->getCalculoFinanceiro(CalculoFolha::CALCULO_RESCISAO)
            ->getEventosFinanceiros();
    }

    /**
     * Metodo com a finalidade de verificar se o servidor possui
     * pagamento de pensao alimenticia na competencia
     *
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

    /**
     * @param  int $ano
     * @param  int $mes
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
     * Get the value of empregador
     *
     * @return CgmJuridico
     */
    public function getEmpregador()
    {
        return $this->empregador;
    }

    /**
     * Set the value of empregador
     *
     * @param CgmJuridico $empregador
     *
     * @return self
     */
    public function setEmpregador(CgmJuridico $empregador)
    {
        $this->empregador = $empregador;
    }
}
