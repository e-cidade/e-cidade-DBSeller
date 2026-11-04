<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2023;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2023\RDExtra2023Builder;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\PadService;

class RDExtra2023Service extends PadService
{
    protected $fileName = 'RD_EXTRA.TXT';

    public function __construct($exercicio, $instituicoes, $dataInicio, $dataFim)
    {
        $this->instituicoes = $instituicoes;
        $this->ano = $exercicio;
        $this->dataInicial = $dataInicio;
        $this->dataFinal = $dataFim;
    }

    protected function getDados()
    {
        $rs = $this->dados();
        while ($state = pg_fetch_assoc($rs)) {
            $builder = $this->getBuilder();
            yield $builder->addDados($state)
                ->build();
        }
    }

    protected function getBuilder()
    {
        return new RDExtra2023Builder();
    }

    private function dados()
    {
        $where = implode(' and ', [
            "c60_anousu = {$this->ano}",
            "c60_codsis = 7",
            sprintf('c61_instit in (%s)', $this->getListaInstituicoes())
        ]);

        $sql = "
        with contas as (
         select c60_estrut as estrutural,
                case
                  when substring(c60_estrut,1 ,4)::int = 2188 then '03'
                  else '07'
                end as classificacao,
                codtrib AS orgao_unidade,
                c61_anousu,
                c61_reduz
           from contabilidade.conplano
           join contabilidade.conplanoreduz on (c61_codcon, c61_anousu) = (c60_codcon, c60_anousu)
           join configuracoes.db_config on db_config.codigo = conplanoreduz.c61_instit
          where $where
          order by 1
        ), lancamentos_debito as (
          select contas.*,
                 c69_valor::numeric(17,2) as valor,
                 'D' as identificador
            from contas
            join conlancamval on c69_debito = c61_reduz
                 and c69_anousu = c61_anousu
           where c69_data between '{$this->dataInicial}' and '{$this->dataFinal}'
        ), lancamentos_credito as (
          select contas.*,
                 c69_valor::numeric(17,2) as valor,
                 'R' as identificador
            from contas
            join conlancamval on c69_credito = c61_reduz
                 and c69_anousu = c61_anousu
           where c69_data between '{$this->dataInicial}' and '{$this->dataFinal}'
        ), agrupa as (
          select estrutural, orgao_unidade, classificacao, identificador, valor
            from lancamentos_debito
          union all
          select estrutural, orgao_unidade, classificacao, identificador, valor
            from lancamentos_credito
        ),totaliza as (
          select estrutural,
                 orgao_unidade,
                 classificacao,
                 identificador,
                 sum(valor) as valor
             from agrupa
           group by estrutural, orgao_unidade, classificacao, identificador
        ) select * from totaliza order by orgao_unidade, estrutural";

        $rs = db_query($sql);
        if (!$rs) {
            throw new \Exception('Erro ao buscar dados do arquivo Receitas e Despesas Extra-Orçamentária.', 400);
        }
        return $rs;
    }
}
