<?php

namespace ECidade\RecursosHumanos\ESocial\Agendamento\Processamento;

use App\Domain\Integracoes\EFDReinf\Retencao\RetencaoR4010;
use App\Domain\Integracoes\EFDReinf\Services\ConfiguracaoService;
use CgmRepository;
use ECidade\RecursosHumanos\ESocial\Agendamento\Evento;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use stdClass;

class EFDPagamentosCreditosPF extends ProcessamentoAbstract implements ProcessamentoInterface
{
    private $cgm;
    private $instituicao;
    private $ano;
    private $mes;
    private $config;

    public function __construct($cgm, $instituicao = null, $ano = null, $mes = null)
    {
        $this->cgm = $cgm;
        $this->instituicao = $instituicao;
        $this->ano = $ano;
        $this->mes = $mes;
        $this->config = ConfiguracaoService::getInstance($instituicao);
    }

    public function processar()
    {
        if (empty($this->ano) && empty($this->mes)) {
            throw new \Exception("Você deve informar a competência");
        }

        $alteracaoDados = false;
        $dados = $this->preProcessar();
        $validaMd5 = true;

        if ($this->envioForcado) {
            $validaMd5 = false;
        }

        if (!$dados) {
            return false;
        }

        foreach ($dados as $dadosEvento) {
            $eventoFila = new Evento(Tipo::R4010, $this->cgm, $dadosEvento->referencia, $dadosEvento);
            if ($eventoFila->adicionarFila(false, $validaMd5)) {
                $alteracaoDados = true;
            }
        }

        return $alteracaoDados;
    }

    public function preProcessar()
    {
        $retencao           = new RetencaoR4010;
        $filtroOrgaoUnidade = false;
        $unidadeCnpjbase    = null;
        $dados              = [];

        if ($this->config->filtraOrgaoUnidade()) {
            $filtroOrgaoUnidade = true;
            $unidade = CgmRepository::getByCodigo($this->cgm);
            $unidadeCnpjbase = substr($unidade->getCnpj(), 0, 8);
        }

        $retencoes = $retencao->procesamento(
            $this->instituicao,
            $this->ano,
            $this->mes,
            $filtroOrgaoUnidade,
            $unidadeCnpjbase
        );

        if (!$retencoes) {
            return false;
        }

        foreach ($retencoes as $retencao) {
            $cgm = $retencao->cgm;

            if (!array_key_exists($cgm, $dados)) {
                $contribuinte = ($filtroOrgaoUnidade) ? $retencao->unidade : $retencao->contribuinte;

                $benef = new stdClass;
                $benef->referencia = "{$this->ano}-{$this->mes} {$contribuinte}-{$retencao->cpfbenef}";
                $benef->perApur = "{$this->ano}-{$this->mes}";
                $benef->inscricao_contribuinte = $contribuinte;

                $benef->tpinscestab = '1';
                $benef->nrinscestab = $contribuinte;
                $benef->idebenef = new stdClass;
                $benef->idebenef->cpfbenef = $retencao->cpfbenef;
                $benef->idepgto = [];

                $dados[$cgm] = $benef;
            }

            // identificador do pagamento
            $natrend = $retencao->codnatureza_rendimento;
            if (!array_key_exists($natrend, $dados[$cgm]->idepgto)) {
                $idepgto = new stdClass;
                $idepgto->natrend = $retencao->codnatureza_rendimento;
                $idepgto->infopgto = [];

                $dados[$cgm]->idepgto[$natrend] = $idepgto;
            }

            $idepgto = $dados[$cgm]->idepgto[$natrend];

            // informacoes do pagamento
            $valorBruto = bcadd($retencao->valor_base, $retencao->deducao, 2);
            $infopagto = new stdClass;
            $infopagto->dtfg         = $retencao->fato_gerador;
            $infopagto->vlrrendbruto = floatval($valorBruto);
            $infopagto->vlrrendtrib  = floatval($retencao->valor_base);
            $infopagto->vlrir        = floatval($retencao->valor_retencao);

            $idepgto->infopgto[] = $infopagto;
        }

        return $dados;
    }
}
