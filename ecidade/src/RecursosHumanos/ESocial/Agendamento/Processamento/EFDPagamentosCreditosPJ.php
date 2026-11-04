<?php

namespace ECidade\RecursosHumanos\ESocial\Agendamento\Processamento;

use App\Domain\Integracoes\EFDReinf\Retencao\RetencaoR4020;
use App\Domain\Integracoes\EFDReinf\Services\ConfiguracaoService;
use BusinessException;
use CgmRepository;
use ECidade\RecursosHumanos\ESocial\Agendamento\Evento;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use stdClass;

class EFDPagamentosCreditosPJ extends ProcessamentoAbstract implements ProcessamentoInterface
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
            $eventoFila = new Evento(Tipo::R4020, $this->cgm, $dadosEvento->referencia, $dadosEvento);
            if ($eventoFila->adicionarFila(false, $validaMd5)) {
                $alteracaoDados = true;
            }
        }

        return $alteracaoDados;
    }

    public function preProcessar()
    {
        $retencao           = new RetencaoR4020;
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
                $benef->referencia = "{$this->ano}-{$this->mes} {$contribuinte}-{$retencao->cnpjbenef}";
                $benef->perApur = "{$this->ano}-{$this->mes}";
                $benef->inscricao_contribuinte = $contribuinte;

                $benef->tpinscestab = '1';
                $benef->nrinscestab = $contribuinte;
                $benef->idebenef = new stdClass;
                $benef->idebenef->cnpjbenef = $retencao->cnpjbenef;
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
            $infopagto = new stdClass;
            $infopagto->dtfg = $retencao->fato_gerador;
            $infopagto->vlrbruto = floatval($retencao->valor_bruto);

            // informacoes retencao
            $reten = new stdClass;
            switch ($retencao->tipo_calculo) {
                case '1':
                case '2':
                    $reten->vlrbaseir     = floatval($retencao->valor_base);
                    $reten->vlrir         = floatval($retencao->valor_retencao);
                    break;
                case '8':
                    $reten->vlrbasepp     = floatval($retencao->valor_base);
                    $reten->vlrpp         = floatval($retencao->valor_retencao);
                    break;
                case '9':
                    $reten->vlrbasecsll   = floatval($retencao->valor_base);
                    $reten->vlrcsll       = floatval($retencao->valor_retencao);
                    break;
                case '10':
                    $reten->vlrbasecofins = floatval($retencao->valor_base);
                    $reten->vlrcofins     = floatval($retencao->valor_retencao);
                    break;
                case '11':
                    $reten->vlrbaseagreg  = floatval($retencao->valor_base);
                    $reten->vlragreg      = floatval($retencao->valor_retencao);
                    break;
                default:
                    throw new BusinessException('Tipo de Cálculo não aceito');
                    break;
            }

            // agrupa propriedades
            $infopagto->retencoes   = $reten;
            $idepgto->infopgto[]    = $infopagto;
        }

        return $dados;
    }
}
