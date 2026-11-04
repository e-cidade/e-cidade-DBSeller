<?php

namespace Ecidade\Patrimonial\Licitacao\ComprasPublicas\Model;

use ECidade\Patrimonial\Licitacao\ComprasPublicas\Model\ComprasPublicasItem;
use Exception;
use licitacao;
use stdClass;
use db_utils;
use ItemLicitacao;

class ComprasPublicasLote
{

    /**
     * @var Integer
     */
    private $licitacao;
    /**
     * @var Array
     */
    private $aItensLicitacao = [];
    /**
     * @var Array
     */
    private $aItensLote = [];

    public function __construct(licitacao $licitacao = null)
    {

        if ($licitacao == null) {
            throw new Exception("Licitação não foi informada");
        }

        $this->licitacao   = $licitacao;
    }

    public function getLote($cotaReservada = false, $exclusivoMPE = false, $configuracao = [])
    {

        $lotes      = [];
        $loteDescr  = [];
        $dadosItens = $this->getItens($this->licitacao->getCodigo());
        if (count($dadosItens) == 0) {
            throw new Exception("Não existem itens");
        }

        if ($this->licitacao->getModalidade()->getSiglaTipoCompraTribunal() == "PRE" ||
            $this->licitacao->getModalidade()->getSiglaTipoCompraTribunal() == "PCE") {
            switch ($this->licitacao->getTipoJulgamento()) {
                case 1:
                    foreach ($dadosItens as $itensLicitacao) {
                        $item                = new ComprasPublicasItem($this->licitacao->getCodigo());
                        $lote                = new stdClass();
                        $lote->numero        = $itensLicitacao->getOrdem();
                        $lote->descricao     = utf8_encode($itensLicitacao->getLoteLicitacao()->getDescricao());
                        $lote->exclusivoMPE  = $exclusivoMPE;
                        foreach ($configuracao as $configuracaoLote) {
                            if ($itensLicitacao->getLoteLicitacao()->getDescricao() == $configuracaoLote->descricao) {
                                $lote->exclusivoMPE  = $configuracaoLote->exclusivo;
                                break;
                            }
                        }

                        $lote->cotaReservada = $itensLicitacao->hasCota();
                        $lote->justificativa = utf8_encode("Lote único");
                        $lote->itens         = $item->getItens(null, $itensLicitacao->getCodigo());
                        $lotes[]             = $lote;
                    }

                    break;

                case 2:
                case 3:
                    foreach ($dadosItens as $iIndice => $item) {
                        $loteDescr[] = $item->getLoteLicitacao()->getDescricao();
                    }

                    $numeroLote     = 0;
                    $loteDescrUnica = array_unique($loteDescr);
                    foreach ($loteDescrUnica as $indice => $descricao) {
                        $numeroLote++;
                        $lote                = new stdClass();
                        $lote->numero        = $numeroLote;
                        $lote->descricao     = utf8_encode($descricao);
                        $lote->exclusivoMPE  = $exclusivoMPE;
                        foreach ($configuracao as $configuracaoLote) {
                            if ($descricao == $configuracaoLote->descricao) {
                                $lote->exclusivoMPE  = $configuracaoLote->exclusivo;
                                break;
                            }
                        }

                        $lote->cotaReservada = false;
                        $lote->justificativa = utf8_encode($descricao);
                        $item                = new ComprasPublicasItem($this->licitacao->getCodigo());
                        $lote->itens         = $item->getItens($descricao);
                        $lotes[]             = $lote;
                    }

                    break;

                default:
                    $mensagem  = "Tipo de Julgamento {$this->licitacao->getTipoJulgamento()} ";
                    $mensagem .= "não reconhecido para integração";
                    throw new Exception($mensagem);
                    break;
            }
        }

        if ($this->licitacao->getModalidade()->getSiglaTipoCompraTribunal() == "PRP" ||
            $this->licitacao->getModalidade()->getSiglaTipoCompraTribunal() == "PCP") {
            switch ($this->licitacao->getTipoJulgamento()) {
                case 1:
                    foreach ($dadosItens as $itensLicitacao) {
                        $item                = new ComprasPublicasItem($this->licitacao->getCodigo());
                        $lote                = new stdClass();
                        $lote->numero        = $itensLicitacao->getOrdem();
                        $lote->descricao     = utf8_encode($itensLicitacao->getLoteLicitacao()->getDescricao());
                        $lote->exclusivoMPE  = $exclusivoMPE;
                        foreach ($configuracao as $configuracaoLote) {
                            if ($itensLicitacao->getLoteLicitacao()->getDescricao() == $configuracaoLote->descricao) {
                                $lote->exclusivoMPE  = $configuracaoLote->exclusivo;
                                break;
                            }
                        }
                        $lote->cotaReservada = $cotaReservada;
                        $lote->justificativa = utf8_encode("Lote único");
                        $lote->itens         = $item->getItens(null, $itensLicitacao->getCodigo());
                        $lotes[]             = $lote;
                    }

                    break;

                case 2:
                    foreach ($dadosItens as $itensLicitacao) {
                        $item                = new ComprasPublicasItem($this->licitacao->getCodigo());
                        $lote                = new stdClass();
                        $lote->numero        = $itensLicitacao->getOrdem();
                        $lote->descricao     = utf8_encode($itensLicitacao->getLoteLicitacao()->getDescricao());
                        $lote->exclusivoMPE  = $exclusivoMPE;
                        foreach ($configuracao as $configuracaoLote) {
                            if ($itensLicitacao->getLoteLicitacao()->getDescricao() == $configuracaoLote->descricao) {
                                $lote->exclusivoMPE  = $configuracaoLote->exclusivo;
                                break;
                            }
                        }
                        $lote->cotaReservada = $cotaReservada;
                        $lote->justificativa = utf8_encode("Lote único");
                        $lote->itens         = $item->getItens(null, $itensLicitacao->getCodigo());
                        $lotes[]             = $lote;
                    }

                    break;

                case 3:
                    foreach ($dadosItens as $iIndice => $item) {
                        $loteDescr[] = $item->getLoteLicitacao()->getDescricao();
                    }

                    $numeroLote     = 0;
                    $loteDescrUnica = array_unique($loteDescr);
                    foreach ($loteDescrUnica as $indice => $descricao) {
                        $numeroLote++;
                        $lote                = new stdClass();
                        $lote->numero        = $numeroLote;
                        $lote->descricao     = utf8_encode($descricao);
                        $lote->exclusivoMPE  = $exclusivoMPE;
                        foreach ($configuracao as $configuracaoLote) {
                            if ($descricao == $configuracaoLote->descricao) {
                                $lote->exclusivoMPE  = $configuracaoLote->exclusivo;
                                break;
                            }
                        }
                        $lote->cotaReservada = $cotaReservada;
                        $lote->justificativa = utf8_encode($descricao);
                        $item                = new ComprasPublicasItem($this->licitacao->getCodigo());
                        $lote->itens         = $item->getItensRegraPRP($descricao);
                        $lotes[]             = $lote;
                    }

                    break;

                default:
                    $mensagem  = "Tipo de Julgamento {$this->licitacao->getTipoJulgamento()}";
                    $mensagem .= "não reconhecido para integração";
                    throw new Exception($mensagem);
                    break;
            }
        }

        return $lotes;
    }

    public function getItens($iCodLicitacao)
    {

        if (count($this->aItensLicitacao) == 0) {
            $oDaoLicLicitem = db_utils::getDao("liclicitem");
            $sSqlLicLicitem = $oDaoLicLicitem->sql_query(
                null,
                "l21_codigo",
                "l21_ordem",
                "l21_codliclicita = {$iCodLicitacao}
                 and not exists (select 1
                                   from licitacaoreservacotas
                                  where l19_liclicitemreserva = l21_codigo)"
            );
            $rsLicLicitem = $oDaoLicLicitem->sql_record($sSqlLicLicitem);
            $iNumRowsLiclicitem = $oDaoLicLicitem->numrows;
            for ($iRow = 0; $iRow < $iNumRowsLiclicitem; $iRow++) {
                $oDadoLicLicitem = db_utils::fieldsMemory($rsLicLicitem, $iRow);
                $oItemLicitacao  = new ItemLicitacao($oDadoLicLicitem->l21_codigo);
                $this->aItensLicitacao[] = $oItemLicitacao;
            }
        }
        return $this->aItensLicitacao;
    }

    public function getItensLote($iCodLicitacao, $descricao)
    {
        $oDaoLicLicitemLote = db_utils::getDao("liclicitemlote");
        $sSqlLicLicitemLote = $oDaoLicLicitemLote->sql_query(
            null,
            "l21_codigo",
            "l21_ordem",
            "l21_codliclicita  = {$iCodLicitacao}
                 and l04_descricao = '{$descricao}'"
        );
        $rsLicLicitem        = $oDaoLicLicitemLote->sql_record($sSqlLicLicitemLote);
        if (!$rsLicLicitem) {
            throw new Exception("{$oDaoLicLicitemLote->erro_msg}");
        }
        $iNumRowsLiclicitem  = $oDaoLicLicitemLote->numrows;
        for ($iRow = 0; $iRow < $iNumRowsLiclicitem; $iRow++) {
            $oDadoLicLicitem = db_utils::fieldsMemory($rsLicLicitem, $iRow);
            $oItemLicitacao  = new ItemLicitacao($oDadoLicLicitem->l21_codigo);
            $this->aItensLote[] = $oItemLicitacao;
        }

        return $this->aItensLote;
    }
}
