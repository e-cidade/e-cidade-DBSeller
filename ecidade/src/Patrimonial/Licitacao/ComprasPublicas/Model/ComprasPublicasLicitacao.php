<?php

namespace ECidade\Patrimonial\Licitacao\ComprasPublicas\Model;

use ECidade\Patrimonial\Licitacao\ComprasPublicas\Model\ComprasPublicasLote;
use licitacao;
use stdClass;
use LicitacaoAtributosDinamicos;

/**
 * Definição do objeto que será enviado para a API do Compras Públicas
 */
class ComprasPublicasLicitacao
{

    public $dados;

    public function __construct($licitacao, $documentos = [])
    {

        $this->dados                        = new stdClass();
        $this->dados->id                    = (string) $licitacao;
        $this->dados->documentosHabilitacao = $documentos;
    }

    public function processarDados($configuracao = [])
    {
        $licitacaoPregao = new licitacao($this->dados->id);
        if ($licitacaoPregao == null) {
            throw new \Exception("Dados licitação não encontrada");
        }

        $licitacaoPregao->getFase();
        $licitacaoPregao->getSituacao();
        $licitacaoPregao->getItens();
        $licitacaoPregao->getProcessoProtocolo();
        $atributosLicitacao = new LicitacaoAtributosDinamicos();
        $atributosLicitacao->setCodigoLicitacao($licitacaoPregao->getCodigo());
        $this->dados->objeto                = utf8_encode($licitacaoPregao->getObjeto());
        if ($licitacaoPregao->getModalidade()->getSiglaTipoCompraTribunal() == "PRE" ||
            $licitacaoPregao->getModalidade()->getSiglaTipoCompraTribunal() == "PCE") {
            $this->dados->tipoRealizacao    = 1;
        }

        if ($licitacaoPregao->getModalidade()->getSiglaTipoCompraTribunal() == "PRP" ||
            $licitacaoPregao->getModalidade()->getSiglaTipoCompraTribunal() == "PCP") {
            $this->dados->tipoRealizacao    = 2;
        }


        /**
         * 1 = Menor Preço; 2 = Maior Preço; 3 = Maior Desconto -> se tipoJulgamento = 2,
         * então tipoRealizacao não pode ser 3 (Não há Pregão Presencial por Maior Preço)
         */
        switch ($atributosLicitacao->getAtributo('tipolicitacao')) {
            case 'MPR':
                $tipoJulgamento = 1;
                break;

            case 'MOP':
                $tipoJulgamento = 2;
                break;

            case 'MDE':
                $tipoJulgamento = 3;
                break;

            default:
                $tipoJulgamento = 1;
                break;
        }

        $this->dados->tipoJulgamento        = $tipoJulgamento;
        $numeroProcesso                     = $licitacaoPregao->getProcesso();

        if ($licitacaoPregao->getProcessoProtocolo() != null) {
            $numeroProcesso                  = $licitacaoPregao->getProcessoProtocolo()->getNumeroProcesso() . "/" .
                $licitacaoPregao->getProcessoProtocolo()->getAnoProcesso();
        }

        $this->dados->numeroProcessoInterno = (string) pg_escape_string($numeroProcesso);
        $this->dados->numeroProcesso        = (int) $licitacaoPregao->getEdital(); //Verificar nome variavel
        $this->dados->anoProcesso           = (int) $licitacaoPregao->getAno();
        $this->dados->dataAberturaPropostas = $licitacaoPregao->getDataAbertura()
            ->getDate() . "T" .
            $licitacaoPregao->getHoraAbertura(); //l20_dataaber:l20_horaaber
        $this->dados->orcamentoSigiloso     = $atributosLicitacao->getAtributo('orcamentosigiloso') == null
            ? false
            : $atributosLicitacao->getAtributo('orcamentosigiloso');
        $this->dados->aplicar147            = $atributosLicitacao->getAtributo('tipobeneficiomicroepp') == "R" ||
            $atributosLicitacao->getAtributo('tipobeneficiomicroepp') == "C" ||
            $atributosLicitacao->getAtributo('tipobeneficiomicroepp') == "T"
            ? true
            : false;
        $this->dados->exigeGarantia         = true;
        $this->dados->permiteCadastroReserva = false;
        $this->dados->exclusivoMPE          = $atributosLicitacao->getAtributo('tipobeneficiomicroepp') == "L"
            ? true
            : false;
        $this->dados->beneficioLocal        = false;
        $this->dados->casasDecimais         = $atributosLicitacao->getAtributo('casas_decimais') == null
            ? "4"
            : $atributosLicitacao->getAtributo('casas_decimais');
        if ($atributosLicitacao->getAtributo('legislacao_aplicada') != null) {
            $this->dados->legislacaoAplicavel = $atributosLicitacao->getAtributo('legislacao_aplicada');
        } else {
            throw new \Exception('Selecione a legislação aplicada nas informações complementares.');
        }

        if ($licitacaoPregao->getModalidade()->getSiglaTipoCompraTribunal() == "PRE") {
            $this->dados->aplicar10024      = true;
        }

        if ($licitacaoPregao->getModalidade()->getSiglaTipoCompraTribunal() == "PRE") {
            $this->dados->tratamentoFaseLance   = 1;
            $this->dados->tipoIntervaloLance    = 1;
            $this->dados->valorIntervaloLance   = 1;
        }

        /*
          1 | Por item - lote é a ordem do item
          2 | Global   - Agrupa pela descrição(l04_descricao)
          3 | Por lote - Agrupa pela descrição(l04_descricao)
        */
        $this->dados->separarPorLotes       = false;
        $this->dados->operacaoLote          = 1;
        /*
          Criada a condição $licitacaoPregao->getModalidade()->getSiglaTipoCompraTribunal() == "PRE" para contornar
          uma melhoria não implementada no Portal para a modalidade PRP quando é lote ou global, será enviado para o
          portal processar como item.
         */
        if ($licitacaoPregao->getTipoJulgamento() == 2 &&
            ($licitacaoPregao->getModalidade()->getSiglaTipoCompraTribunal() == "PRE" ||
                $licitacaoPregao->getModalidade()->getSiglaTipoCompraTribunal() == "PCE")) {
            $this->dados->separarPorLotes   = true;
        }
        if ($licitacaoPregao->getTipoJulgamento() == 3 &&
            ($licitacaoPregao->getModalidade()->getSiglaTipoCompraTribunal() == "PRE" ||
                $licitacaoPregao->getModalidade()->getSiglaTipoCompraTribunal() == "PCE")) {
            $this->dados->separarPorLotes   = true;
            $this->dados->operacaoLote      = 2;
        }
        $cotaReservada = false;
        if ($atributosLicitacao->getAtributo('tipobeneficiomicroepp') == "R" ||
            $atributosLicitacao->getAtributo('tipobeneficiomicroepp') == "C" ||
            $atributosLicitacao->getAtributo('tipobeneficiomicroepp') == "T") {
            $cotaReservada = true;
        }
        $lote                = new ComprasPublicasLote($licitacaoPregao);
        $this->dados->lotes  = $lote->getLote($cotaReservada, $this->dados->exclusivoMPE, $configuracao);

        return $this->dados;
    }
}
