<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2024;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2023\BalanceteVerificacao2023Service;

class BalanceteVerificacao2024Service extends BalanceteVerificacao2023Service
{
    protected function buscaValoresBalancete()
    {
        $where = sprintf('instituicao in (%s)', $this->getListaInstituicoes());

        $encerramento = $this->encerramento ? 'true' : 'false';

        $sql = "
         with contas as (
            select estrutural,
                   reduzido,
                   exercicio,
                   codigo_conplano,
                   classe,
                   nome,
                   instituicao,
                   substring(siconfi, 2) as siconfi,
                   0 as subrecurso,
                   indicador_superavit,
                   natureza_informacao,
                   saldo_anterior,
                   saldo_debito,
                   saldo_credito,
                   saldo_final,
                   case when o200_tribunal is true then complemento else 0 end as complemento,
                   codtrib as orgao_unidade
              from contabilidade.balancete_verificacao_por_recurso(
                     $this->ano, '$this->dataInicial', '$this->dataFinal', $encerramento, null
                   )
              join configuracoes.db_config on db_config.codigo = instituicao
              join orcamento.complementofonterecurso on o200_sequencial = complemento
            where $where
        ), agrupa as (
            select  estrutural,
                    reduzido,
                    exercicio,
                    nome,
                    siconfi,
                    subrecurso,
                    complemento,
                    indicador_superavit,
                    natureza_informacao,
                    orgao_unidade,
                    sum(saldo_anterior) as saldo_anterior,
                    sum(saldo_debito) as saldo_debito,
                    sum(saldo_credito) as saldo_credito
            from contas
            group by estrutural, reduzido, exercicio, nome, siconfi, subrecurso, complemento, indicador_superavit,
                     natureza_informacao, orgao_unidade
        ) select *
           from (
              select agrupa.*,
                     (saldo_anterior + saldo_debito + saldo_credito) as saldo_final
                from agrupa
            ) as x
            where saldo_anterior != 0
               or saldo_debito != 0
               or saldo_credito != 0
               or saldo_final != 0
        ";

        $rs = db_query($sql);
        if (!$rs) {
            throw new \Exception('Erro ao buscar lançamentos do balancete de verificação.', 400);
        }

        if (pg_num_rows($rs) === 0) {
            throw new \Exception('Sem registros no balancete de verificação.', 400);
        }

        return \db_utils::getCollectionByRecord($rs);
    }
}
