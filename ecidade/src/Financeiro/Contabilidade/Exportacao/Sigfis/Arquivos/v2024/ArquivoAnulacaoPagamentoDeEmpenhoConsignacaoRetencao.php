<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use BusinessException;
use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common\Helper;
use Illuminate\Database\Capsule\Manager as DB;
use stdClass;

class ArquivoAnulacaoPagamentoDeEmpenhoConsignacaoRetencao extends ArquivoBase
{
    protected $sNomeArquivo = 'AnulacaoPagamentoDeEmpenhoConsignacaoRetencao';

    public function gerarDados()
    {
        $retencoes = (array)$this->getRetencoes();

        if (empty($retencoes)) {
            throw new BusinessException('Não há retenções para a competência informada.');
        }

        $EmpenhoAnuConsignacaoRetencao = new stdClass();
        $EmpenhoAnuConsignacaoRetencao->AnulacoesPagamentosDeEmpenhoConsignacoesRetencoes = [];

        foreach ($retencoes as $retencao) {
            /*
             * Adicionado o codigo da retencao (retencaotiporec) ao numero da nota pois uma nota pode ter varias
             * retenções lançadas e estava gerando critica pelo validador pois identiicava mais de um pagamento para
             * a mesma nota de liquidacao
             */
            $idNotaPegamentoRetencao = $retencao->identificadorliquidacao . $retencao->codigoretencao;

            $AnuConsigRet = new stdClass();
            $AnuConsigRet->Identificador = $retencao->identificador;
            $AnuConsigRet->CodigoUnidadeGestora = $this->sCodigoTribunal;
            $AnuConsigRet->Competencia = $this->competencia;
            $AnuConsigRet->NumeroEmpenho = $retencao->numeroempenho;
            $AnuConsigRet->AnoEmpenho = $retencao->anoempenho;
            $AnuConsigRet->NumeroNotaPagamentoDeEmpenhoConsignacaoRetencao = $idNotaPegamentoRetencao;
            $AnuConsigRet->AnoNotaPagamentoDeEmpenhoConsignacaoRetencao = $retencao->anoliquidacaoempenho;
            $AnuConsigRet->NumeroNotaAnulacao = substr($retencao->identificadorliquidacao, 0, 6);
            $AnuConsigRet->DataAnulacao = $retencao->dataanulacao;
            $AnuConsigRet->TipoConsignacaoRetencaoPaga = $this->convertTipoRetencao($retencao->tipo);
            $AnuConsigRet->Valor = $retencao->valor;
            $AnuConsigRet->CpfResponsavel = $retencao->cpfresponsavel;
            $AnuConsigRet->Justificativa = Helper::convertAndLimit("<![CDATA[".$retencao->justificativa);
            $AnuConsigRet->TipoAnulacaoMovimento = 2;

            $EmpenhoAnuConsignacaoRetencao->AnulacoesPagamentosDeEmpenhoConsignacoesRetencoes[] = (object) [
                'AnulacaoPagamentoDeEmpenhoConsignacaoRetencao' => $AnuConsigRet
            ];
        }

        $this->aDados = $EmpenhoAnuConsignacaoRetencao;
    }

    private function getRetencoes()
    {
        $instituicao = implode(', ', $this->getListaInstituicoes());

        $sql = <<<SQL
WITH dados_anulacao_pagamento as (select c70_codlan
                                       , e21_sequencial
                                       , empempenho.e60_codemp
                                       , empempenho.e60_anousu
                                       , orcdotacao.o58_orgao
                                       , orcdotacao.o58_unidade
                                       , empnota.e69_numero
                                       , c70_data
                                       , e32_sequencial
                                       , cgmusu.z01_cgccpf
                                       , pagordem.e50_codord
                                       , empnota.e69_anousu
                                       , c72_complem
                                       , c70_valor
                                  from empempenho
                                           inner join orcdotacao on o58_anousu = e60_anousu and o58_coddot = e60_coddot
                                           inner join empnota on e69_numemp = e60_numemp
                                           inner join pagordemnota on e71_codnota = e69_codnota
                                           inner join pagordem on e50_codord = e71_codord
                                           inner join db_usuacgm on db_usuacgm.id_usuario = e50_id_usuario
                                           inner join cgm as cgmusu on cgmlogin = cgmusu.z01_numcgm
                                           inner join conlancamemp on c75_numemp = e60_numemp
                                           inner join conlancam on c75_codlan = c70_codlan
                                           inner join conlancamcompl on c72_codlan = c70_codlan
                                           inner join conlancamdoc on c71_codlan = c70_codlan
                                           inner join conlancamord as ord_ret
                                          on ord_ret.c80_codord = e71_codord and ord_ret.c80_codlan = c70_codlan
                                           inner join conlancamretencao on c127_conlancam = c70_codlan
                                           inner join retencaotiporec on e21_sequencial = c127_retencaotiporec
                                           inner join retencaotipocalc on e32_sequencial = e21_retencaotipocalc
                                  where c71_coddoc in (6003, 6005)
                                    and e60_instit in ({$instituicao})
                                    and c70_data between '{$this->dtDataInicial}' and '{$this->dtDataFinal}' )
SELECT MIN(c70_codlan)                        as Identificador
     , string_agg(DISTINCT c72_complem, ', ') AS Justificativa
     , SUM(c70_valor)                         as Valor
     , e21_sequencial                         as CodigoRetencao
     , e60_codemp                             as NumeroEmpenho
     , e60_anousu                             as AnoEmpenho
     , o58_orgao                              as CodigoOrgao
     , o58_unidade                            as CodigoUnidadeOrcamentaria
     , e69_numero                             as NumeroNota
     , c70_data                               as DataAnulacao
     , e32_sequencial                         as Tipo
     , z01_cgccpf                             as CPFResponsavel
     , e50_codord                             as IdentificadorLiquidacao
     , e69_anousu                             as AnoLiquidacaoEmpenho
FROM dados_anulacao_pagamento
group by CodigoRetencao
       , NumeroEmpenho
       , AnoEmpenho
       , CodigoOrgao
       , CodigoUnidadeOrcamentaria
       , NumeroNota
       , DataAnulacao
       , Tipo
       , CPFResponsavel
       , IdentificadorLiquidacao
       , AnoLiquidacaoEmpenho
order by NumeroEmpenho
SQL;
        return DB::select($sql);
    }

    private function convertTipoRetencao($tipo)
    {
        switch ($tipo) {
            // Imposto de Renda Retido na Fonte (IRRF)
            case '1':
            case '2':
                return 6;
                // Contribuições Previdenciárias
            case '3':
            case '4':
            case '7':
                return 3;
                // Imposto Sobre Serviços
            case '5':
                return 7;
                // Outras Retenções
            default:
                return 8;
        }
    }
}
