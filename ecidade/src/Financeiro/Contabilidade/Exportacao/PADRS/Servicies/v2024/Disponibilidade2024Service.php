<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2024;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2024\Disponibilidade2024Builder;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2023\Disponibilidade2023Service;

class Disponibilidade2024Service extends Disponibilidade2023Service
{
    protected function getSql()
    {
        $listaEstruturais = [
            "c60_estrut like '11111%'",
            "c60_estrut like '114%'",
            "c60_estrut like '11131%'",
            "c60_estrut like '11132%'",
        ];

        $filtros = [
            "c61_anousu = {$this->ano}",
            sprintf('c61_instit in (%s)', $this->getListaInstituicoes()),
            sprintf('(%s)', implode(' or ', $listaEstruturais)),
            "c60_identificadorfinanceiro = 'F'"
        ];

        $where = implode(' and ', $filtros);

        $sqlFiltraReduzidos = "
        select c61_reduz
          from contabilidade.conplano
          join contabilidade.conplanoreduz on (c61_codcon, c61_anousu) = (c60_codcon, c60_anousu)
         where {$where}
        ";
        $sqlReduz = "(select array_agg(c61_reduz) from ({$sqlFiltraReduzidos}) as x )::int[]";

        $sql = "
         with contas as (
            select estrutural,
                   reduzido,
                   exercicio,
                   codigo_conplano,
                   classe,
                   nome,
                   substring(siconfi, 2) as siconfi,
                   indicador_superavit,
                   case when c63_banco is null then '000' else c63_banco end as banco,
				   case when c63_agencia is null then '000' else c63_agencia end as agencia,
				   case when c63_conta is null then '0000' else c63_conta end as conta,
                   c63_dvagencia as digito_agencia,
                   c63_dvconta as digito_conta,
                   c63_codigooperacao as codigo_operacao,
                   case when o200_tribunal is true then complemento else 0 end as complemento,
                   codtrib as orgao_unidade,
                   db21_tipoinstit as tipo_instituicao,
                   saldo_anterior,
                   saldo_debito,
                   saldo_credito,
                   saldo_final
              from contabilidade.balancete_verificacao_por_recurso(
                     $this->ano, '$this->dataInicial', '$this->dataFinal', false, $sqlReduz
                   )
              join configuracoes.db_config on db_config.codigo = instituicao
              join orcamento.complementofonterecurso on o200_sequencial = complemento
              join contabilidade.conplanoreduz on c61_reduz = reduzido and c61_anousu = exercicio
              left join conplanoconta on c63_codcon = c61_codcon
                                     and c63_anousu = c61_anousu
                                     and c63_reduz = c61_reduz
        ), agrupa as (
            select  distinct estrutural,
                    reduzido,
                    exercicio,
                    nome,
                    siconfi,
                    complemento,
                    indicador_superavit,
                    banco,
                    agencia,
                    conta,
                    digito_agencia,
                    digito_conta,
                    codigo_operacao,
                    orgao_unidade,
                    tipo_instituicao,
                    sum(saldo_anterior) as saldo_anterior,
                    sum(saldo_debito) as saldo_debito,
                    sum(saldo_credito) as saldo_credito,
                    sum(saldo_final) as saldo_final
            from contas
            group by 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15
            order by estrutural, siconfi, complemento
        ) select agrupa.*
            from agrupa
           where (saldo_anterior != 0 or saldo_debito != 0 or saldo_credito != 0)
        ";

        return $sql;
    }

    protected function getBuilder()
    {
        return new Disponibilidade2024Builder();
    }
}
