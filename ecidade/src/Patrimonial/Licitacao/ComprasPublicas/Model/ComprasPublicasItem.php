<?php

namespace Ecidade\Patrimonial\Licitacao\ComprasPublicas\Model;

use Exception;
use licitacao;
use stdClass;
use cl_pcparam;
use db_utils;
use LicitacaoAtributosDinamicos;

class ComprasPublicasItem
{

   /**
    * @var Array
    */
    private $itens = [];
    private $codigoLicitacao;
    private $casasdecimais = 2;
    public function __construct($codigoLicitacao)
    {
        if ($codigoLicitacao == null) {
            throw new Exception("Código da licitação não informada");
        }

        $this->codigoLicitacao = $codigoLicitacao;
        $oLicitacao            = new licitacao($codigoLicitacao);
        $atributosLicitacao    = new LicitacaoAtributosDinamicos();
        $atributosLicitacao->setCodigoLicitacao($oLicitacao->getCodigo());
        $this->casasdecimais   = $atributosLicitacao->getAtributo('casas_decimais') == null
        ? 4
        : $atributosLicitacao->getAtributo('casas_decimais');
    }

    public function getItens($descricaoLote = null, $codigoitem = null)
    {
        $sSql   = "select l21_codigo,
                          l21_ordem,
                          pc01_codmater,
                          case
                          when pc11_resum is null or  trim(pc11_resum) = '' or pc11_resum = pc01_descrmater
                          then

                            pc01_descrmater
                          else

                            pc01_descrmater||'\n - '||pc11_resum
                          end as pc01_descrmater,
                          pc03_natureza,
                          l20_usaregistropreco,
                          coalesce(m61_abrev, 'SVC') as unid,
                          pc11_vlrun,
                          pc11_quant,
                          pc11_resum,
                          pc81_codprocitem,
                          coalesce((select pc11_quant
                            from licitacaoreservacotas
                           inner join liclicitem itemcota
                              on l19_liclicitemreserva = itemcota.l21_codigo
                           inner join pcprocitem procitemcota
                              on itemcota.l21_codpcprocitem = procitemcota.pc81_codprocitem
                           inner join solicitem solitemcota
                              on solitemcota.pc11_codigo    = procitemcota.pc81_solicitem
                           where l19_liclicitemorigem       = liclicitem.l21_codigo), 0)  as quantcota
                   from liclicitem
                  inner join pcprocitem
                     on liclicitem.l21_codpcprocitem = pcprocitem.pc81_codprocitem
                  inner join pcproc
                     on pcproc.pc80_codproc          = pcprocitem.pc81_codproc
                  inner join solicitem
                     on solicitem.pc11_codigo        = pcprocitem.pc81_solicitem
                  inner join solicitempcmater
                     on solicitempcmater.pc16_solicitem = solicitem.pc11_codigo
                  inner join pcmater
                     on pc01_codmater = pc16_codmater
                  inner join pcsubgrupo
                     on pc01_codsubgrupo = pc04_codsubgrupo
                  inner join pcgrupo
                     on pc04_codgrupo  = pc03_codgrupo
                   left join solicitemunid
                      on pc17_codigo  = pc11_codigo
                   left join matunid
                     on  m61_codmatunid =  pc17_unid
                  inner join solicita
                     on solicita.pc10_numero  = solicitem.pc11_numero
                  inner join liclicita
                     on liclicita.l20_codigo  = liclicitem.l21_codliclicita
                  inner join liclicitemlote
                     on l04_liclicitem = l21_codigo
                  where l21_codliclicita = {$this->codigoLicitacao}
                    and not exists (select 1
                                      from licitacaoreservacotas
                                     where l19_liclicitemreserva = l21_codigo)";

        if ($descricaoLote != null) {
            $sSql .= " and l04_descricao = '{$descricaoLote}'";
        }

        if ($codigoitem != null) {
            $sSql .= " and l21_codigo = '{$codigoitem}'";
        }

        $sSql .= " order by l21_ordem";
        $rsItensLicitacao = db_query($sSql);
        if (!$rsItensLicitacao) {
            throw new Exception("Sistema não conseguiu buscar os itens");
        }

        $iRegistros = pg_numrows($rsItensLicitacao);
        for ($iRegistro = 0; $iRegistro < $iRegistros; $iRegistro++) {
            $item                     =  new stdClass();
            $oDadosItem = db_utils::fieldsMemory($rsItensLicitacao, $iRegistro);
            $item->numeroCatalogo     = (int) $oDadosItem->pc01_codmater;
            $item->numero             = (int) $oDadosItem->l21_ordem;
            $item->numeroInterno      = (int) $oDadosItem->l21_codigo;
            $item->descricao          = pg_escape_string(utf8_encode($oDadosItem->pc01_descrmater));
            $item->natureza           = $oDadosItem->pc03_natureza;
            $item->siglaUnidade       = pg_escape_string(utf8_encode($oDadosItem->unid));
            $item->valorReferencia    = number_format($this->valorReferencia(
                $oDadosItem->pc81_codprocitem,
                $oDadosItem->l20_usaregistropreco,
                $oDadosItem->pc11_vlrun
            ), $this->casasdecimais, '.', '');

            $item->quantidadeTotal    = floatval($oDadosItem->pc11_quant + $oDadosItem->quantcota);
            if ($oDadosItem->quantcota > 0) {
                $item->quantidadeCota  = floatval($oDadosItem->quantcota);
            }

            $this->itens[]          = $item;
        }

        return $this->itens;
    }
    /*
     Criada método getItensRegraPRP  para contornar uma melhoria não implementada no Portal para a modalidade PRP
     quando é lote ou global, será enviado para o portal processar como item.
    */
    public function getItensRegraPRP($descricaoLote = null)
    {
        if ($descricaoLote == null || trim($descricaoLote) == "") {
            throw new Exception("Lote para licitação {$this->codigoLicitacao} não encontrada.");
        }

        $sSql   = "select l21_codliclicita                 as numero_interno,
                        l04_descricao                    as descricao,
                        1                                as natureza,
                        'UN'                             as siglaUnidade,
                        sum(pc23_valor)/sum(pc23_quant)  as valor_referencia,
                        sum(pc23_quant)                  as quantidade_total,
                        sum(coalesce((select pc11_quant
                          from licitacaoreservacotas
                         inner join liclicitem itemcota
                            on l19_liclicitemreserva      = itemcota.l21_codigo
                         inner join pcprocitem procitemcota
                            on itemcota.l21_codpcprocitem = procitemcota.pc81_codprocitem
                         inner join solicitem solitemcota
                            on solitemcota.pc11_codigo    = procitemcota.pc81_solicitem
                         where l19_liclicitemorigem       = liclicitem.l21_codigo), 0))  as quantcota
                 from liclicitem
                inner join pcprocitem
                   on liclicitem.l21_codpcprocitem    = pcprocitem.pc81_codprocitem
                inner join pcorcamitemproc
                   on pcorcamitemproc.pc31_pcprocitem = pcprocitem.pc81_codprocitem
                inner join pcorcamval
                   on pc23_orcamitem                  = pc31_orcamitem
                inner join pcorcamjulg
                   on pc24_orcamitem                  = pc23_orcamitem
                  and pc24_orcamforne                 = pc23_orcamforne
                  and pc24_pontuacao                  = 1
                inner join pcproc
                   on pcproc.pc80_codproc = pcprocitem.pc81_codproc
                inner join solicitem
                   on solicitem.pc11_codigo = pcprocitem.pc81_solicitem
                inner join solicitempcmater
                   on solicitempcmater.pc16_solicitem = solicitem.pc11_codigo
                inner join pcmater
                   on pc01_codmater                   = pc16_codmater
                inner join pcsubgrupo
                   on pc01_codsubgrupo                = pc04_codsubgrupo
                inner join pcgrupo
                   on pc04_codgrupo                   = pc03_codgrupo
                 left join solicitemunid
                    on pc17_codigo                    = pc11_codigo
                 left join matunid
                   on  m61_codmatunid                 = pc17_unid
                inner join solicita
                   on solicita.pc10_numero            = solicitem.pc11_numero
                inner join liclicita
                   on liclicita.l20_codigo            = liclicitem.l21_codliclicita
                inner join liclicitemlote
                   on l04_liclicitem = l21_codigo
                where l21_codliclicita = {$this->codigoLicitacao}
                  and not exists (select 1
                                    from licitacaoreservacotas
                                   where l19_liclicitemreserva = l21_codigo)
                  and l04_descricao = '{$descricaoLote}'
                group by l21_codliclicita,
                         l04_descricao
                order by l04_descricao asc";

        $rsItensLicitacao = db_query($sSql);
        if (!$rsItensLicitacao) {
            throw new Exception("Sistema não conseguiu buscar os itens");
        }

        $iRegistros = pg_numrows($rsItensLicitacao);
        $numero     = 1;
        for ($iRegistro = 0; $iRegistro < $iRegistros; $iRegistro++) {
            $item                      =  new stdClass();
            $oDadosItem = db_utils::fieldsMemory($rsItensLicitacao, $iRegistro);
            $item->numero              = (int) $numero;
            $numero ++;
            $item->numeroInterno       = (int) $oDadosItem->numero_interno;
            $item->descricao           = pg_escape_string(utf8_encode($oDadosItem->descricao));
            $item->natureza            = 1;
            $item->siglaUnidade        = "UN";
            $item->valorReferencia     = number_format($oDadosItem->valor_referencia, $this->casasdecimais, '.', '');
            $item->quantidadeTotal     = floatval($oDadosItem->quantidade_total + $oDadosItem->quantcota);
            if ($oDadosItem->quantcota > 0) {
                $item->quantidadeCota  = floatval($oDadosItem->quantcota);
            }

            $this->itens[]             = $item;
        }

        return $this->itens;
    }

    public function valorReferencia($itemProcessoCompra, $utilizaRegistroPreco = false, $valorUnitario = null)
    {

        if ($itemProcessoCompra == null) {
            throw new Exception("Processo de compra não encontrado");
        }

        $oDaoPcparam     = new cl_pcparam();
        $rsPcparam       = $oDaoPcparam->sql_record($oDaoPcparam->sql_query_file(
            db_getsession("DB_instit"),
            "pc30_tipojulgamentoorcamento"
        ));

        $tipoJulgamento = db_utils::fieldsMemory($rsPcparam, 0)->pc30_tipojulgamentoorcamento;
        if ($tipoJulgamento == 2) {
            $sqlValoreReferencia = "select sum(pc23_vlrun)/
                                           count(pc23_orcamforne) as valormediaorcada
                                      from pcorcamitemproc
                                     inner join pcorcamitem
                                        on pcorcamitem.pc22_orcamitem = pcorcamitemproc.pc31_orcamitem
                                     inner join pcorcamval
                                        on pcorcamitem.pc22_orcamitem   = pcorcamval.pc23_orcamitem
                                     inner join pcorcamforne
                                        on pcorcamval.pc23_orcamforne   = pcorcamforne.pc21_orcamforne
                                     inner join pcorcamjulg
                                        on pcorcamitem.pc22_orcamitem   = pcorcamjulg.pc24_orcamitem
                                       and pcorcamforne.pc21_orcamforne = pcorcamjulg.pc24_orcamforne
                                     where pc31_pcprocitem = {$itemProcessoCompra}";

            $rsValorReferencia  = db_query($sqlValoreReferencia);
            if ((!$rsValorReferencia || pg_numrows($rsValorReferencia) == 0) && !$valorUnitario) {
                throw new Exception("Não foi possível buscar o valor de referência.
                                     Verifique se existe orçamento para o processo de compra!");
            }

            $valorReferencia = db_utils::fieldsMemory($rsValorReferencia, 0);
            if ($valorReferencia->valormediaorcada == 0 && $valorUnitario == 0) {
                throw new Exception("A licitação requer um orçamento.
                                     Verifique se existe orçamento para o processo de compras da licitação");
            }
            if ($valorReferencia->valormediaorcada <= 0 && $valorUnitario <= 0) {
                throw new Exception("Valor de referência deve ser maior que zero");
            }
            return floatval($valorReferencia->valormediaorcada) ?: $valorUnitario;
        }

        $sqlValoreReferencia = "select pc23_vlrun as valormenorpreco
                                  from  pcorcamitemproc
                                 inner join pcorcamitem
                                    on pcorcamitem.pc22_orcamitem = pcorcamitemproc.pc31_orcamitem
                                 inner join pcorcamval
                                    on pcorcamitem.pc22_orcamitem   = pcorcamval.pc23_orcamitem
                                 inner join pcorcamforne
                                    on pcorcamval.pc23_orcamforne   = pcorcamforne.pc21_orcamforne
                                 inner join pcorcamjulg
                                    on pcorcamitem.pc22_orcamitem   = pcorcamjulg.pc24_orcamitem
                                   and pcorcamforne.pc21_orcamforne = pcorcamjulg.pc24_orcamforne
                                   and pc24_pontuacao  = 1
                                 where pc31_pcprocitem = {$itemProcessoCompra}";

        $rsValorReferencia  = db_query($sqlValoreReferencia);
        if ((!$rsValorReferencia || pg_numrows($rsValorReferencia) == 0) && !$valorUnitario) {
            throw new Exception("A licitação requer um orçamento.
                                 Verifique se existe orçamento para o processo de compra!");
        }

        $valorReferencia = db_utils::fieldsMemory($rsValorReferencia, 0);
        if ($valorReferencia->valormenorpreco <= 0 && $valorUnitario <= 0) {
            throw new Exception("Valor de referência deve ser maior zero");
        }

        return $valorReferencia->valormenorpreco ?: $valorUnitario;
    }
}
