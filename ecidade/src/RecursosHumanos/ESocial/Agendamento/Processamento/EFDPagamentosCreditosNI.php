<?php

namespace ECidade\RecursosHumanos\ESocial\Agendamento\Processamento;

use App\Domain\Integracoes\EFDReinf\Retencao\RetencaoR4040;
use App\Domain\Integracoes\EFDReinf\Services\ConfiguracaoService;
use CgmRepository;
use ECidade\RecursosHumanos\ESocial\Agendamento\Evento;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use stdClass;

class EFDPagamentosCreditosNI extends ProcessamentoAbstract implements ProcessamentoInterface
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
            $eventoFila = new Evento(Tipo::R4040, $this->cgm, $dadosEvento->referencia, $dadosEvento);
            if ($eventoFila->adicionarFila(false, $validaMd5)) {
                $alteracaoDados = true;
            }
        }

        return $alteracaoDados;
    }

    private function preProcessar()
    {
        $retencao           = new RetencaoR4040;
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
            $contri = ($filtroOrgaoUnidade) ? $retencao->unidade : $retencao->contribuinte;

            if (!array_key_exists($contri, $dados)) {
                $std = new stdClass;

                $std->referencia = "{$this->ano}-{$this->mes} {$contri}";
                $std->perApur = "{$this->ano}-{$this->mes}";
                $std->inscricao_contribuinte = $contri;

                $std->tpinscestab = "1";
                $std->nrinscestab = $contri;
                $std->idenat = [];

                $dados[$contri] = $std;
            }

            $natrend = $retencao->codnatureza_rendimento;
            if (!array_key_exists($natrend, $dados[$contri]->idenat)) {
                $idenat = new stdClass;
                $idenat->natrend = $natrend;
                $idenat->infopgto = [];

                $dados[$contri]->idenat[$natrend] = $idenat;
            }

            $infopgto = new stdClass();
            $infopgto->dtfg = $retencao->fato_gerador;

            $vlrliq = round(floatval($retencao->valor_bruto) - floatval($retencao->valor_retencao), 2);
            $infopgto->vlrliq = $vlrliq;

            $infopgto->vlrbaseir = floatval($retencao->valor_base);
            $infopgto->vlrir = floatval($retencao->valor_retencao);

            if ($retencao->codnatureza_rendimento == 12052) {
                $infopgto->dtescrcont = $retencao->fato_gerador;
            }

            $infopgto->descr = $retencao->benef;

            // agrupa propriedades
            $idenat = $dados[$contri]->idenat[$natrend];
            $idenat->infopgto[] = $infopgto;
        }

        return $dados;
    }
}
