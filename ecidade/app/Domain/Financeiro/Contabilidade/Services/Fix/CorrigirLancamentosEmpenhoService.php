<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Fix;

use Illuminate\Support\Facades\DB;

class CorrigirLancamentosEmpenhoService
{
    public function arrumar($exercicio)
    {
        $this->lancamentoComRecursosDiferentes($exercicio);
        $this->empenhoComDoisRecursos($exercicio);
    }

    /**
     * Ajusta os recursos de empenhos na conlancamrecurso, não identificamos onde esta realizando a troca do recurso
     *
     * Foi identificado que a tabela conlancamcomplementorecurso estava gravando o recurso correto, mas na tabela
     * conlancamrecurso tinha um recurso diferente para o memso lançamento. Para despesa e receita isso não era para
     * acontecer.
     *
     * @param $exercicio
     * @return void
     */
    private function lancamentoComRecursosDiferentes($exercicio)
    {
        $sql = <<<SQL
drop table if exists lancamento_empenho_arrumar;

create table lancamento_empenho_arrumar as
with lancamentos as (
  select distinct c70_codlan, c70_anousu, c75_numemp, o201_orctiporec
    from conlancam
    join conlancamemp on c75_codlan = c70_codlan
    join conlancamdoc on c71_codlan = c70_codlan
    join conhistdoc on c53_coddoc = c71_coddoc
    join conlancamrecurso on c130_conlancam = c70_codlan
    join conlancamcomplementorecurso on o201_codlan = c70_codlan
  where c70_anousu = $exercicio
    and c53_tipo in (10, 11, 20, 21, 30, 31, 40, 41, 50, 51, 60, 61, 70, 71, 90, 91, 92, 100, 101, 110, 111, 112, 113,
                     200, 201, 414, 415, 900, 901, 1000, 1500, 2000, 2001)
    and c130_orctiporec != o201_orctiporec
), empenho_exercicio as (

    select lancamentos.*,
           case when o206_recurso is null then o58_codigo else o206_recurso end id_recurso,
           false as rp
      from lancamentos
      join empempenho  on e60_numemp = c75_numemp
      join orcdotacao on (o58_anousu, o58_coddot) = (e60_anousu, e60_coddot)
      left join origemcomplementorecurso on o206_numero = e60_numemp
                and o206_origem = 1
     where not exists(select 1 from empresto where e91_numemp = e60_numemp)
), empenho_rp as (
    select lancamentos.*,
            case when o206_recurso is null then o58_codigo else o206_recurso end id_recurso,
            true as rp
    from lancamentos
    join empempenho on e60_numemp = c75_numemp
    join empresto on e91_numemp = e60_numemp and e91_anousu = c70_anousu
    join orcdotacao on (o58_anousu, o58_coddot) = (e60_anousu, e60_coddot)
    left join origemcomplementorecurso on o206_numero = e60_numemp
              and o206_origem = 10
)
select * from empenho_exercicio
union
select * from empenho_rp;

update conlancamrecurso set c130_orctiporec = o201_orctiporec
  from lancamento_empenho_arrumar
 where c130_conlancam = c70_codlan;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Foi identificado em capivari, registros de lançamentos de empenhos com recuros diferentes ao da dotação.
     * Segue abaixo, um exemplo de como estava alguns casos
     * o15_codigo | o15_recurso | o15_complemento | gestao | codigo_siconfi
     * -----------+-------------+-----------------+--------+---------------
     *    1       | 0001        |               0 | 1500   | 1500
     * 8833       | 0001        |               0 | 2500   | 2500
     *
     * @param $exercicio
     * @return void
     */
    private function empenhoComDoisRecursos($exercicio)
    {
        DB::statement('drop table if exists w_empenhos_arrumar');
        $sql = <<<SQL
create table w_empenhos_arrumar as
with empenhos_problema as (
    select distinct c75_numemp, c70_anousu
   from conlancam
   join conlancamemp on c75_codlan = c70_codlan
   join conlancamrecurso on c130_conlancam = c70_codlan
   where c70_anousu = {$exercicio}
   group by 1, 2
   having count(distinct c130_orctiporec) > 1
), empenho_exercicio as (
    select empenhos_problema.*,
           o206_complementorecurso,
           o206_recurso as recurso_origem,
           o58_codigo as recurso_dotacao,
           o15_complemento,
           false as rp
      from empenhos_problema
      join empempenho on e60_numemp = c75_numemp
      join orcdotacao on (o58_anousu, o58_coddot) = (e60_anousu, e60_coddot)
      join orctiporec on o15_codigo = o58_codigo
      left join origemcomplementorecurso on o206_numero = e60_numemp
    and o206_origem = 1
     where not exists(select 1 from empresto where e91_numemp = e60_numemp)
), empenho_rp as (
    select empenhos_problema.*,
           o206_complementorecurso,
           o206_recurso as recurso_origem,
           o58_codigo as recurso_dotacao,
           o15_complemento,
           true as rp
    from empenhos_problema
    join empempenho on e60_numemp = c75_numemp
    join empresto on e91_numemp = e60_numemp and e91_anousu = c70_anousu
    join orcdotacao on (o58_anousu, o58_coddot) = (e60_anousu, e60_coddot)
    join orctiporec on o15_codigo = o58_codigo
    left join origemcomplementorecurso on o206_numero = e60_numemp
    and o206_origem = 10
)
select distinct x.*, o201_orctiporec
from (
    select * from empenho_exercicio
    union
    select * from empenho_rp
) as x
join conlancamemp on conlancamemp.c75_numemp = x.c75_numemp
join conlancamcomplementorecurso on o201_codlan = c75_codlan
SQL;
        DB::statement($sql);

        DB::statement('
        update orcamento.origemcomplementorecurso
        set o206_recurso = recurso_dotacao, o206_complementorecurso = o15_complemento
        from w_empenhos_arrumar
        where o201_orctiporec = recurso_dotacao
          and o206_numero = c75_numemp
          and o206_origem = 1
          and rp is false
        ');

        DB::statement('
        update orcamento.origemcomplementorecurso
        set o206_recurso = recurso_dotacao, o206_complementorecurso = o15_complemento
        from w_empenhos_arrumar
        where o201_orctiporec = recurso_dotacao
          and o206_numero = c75_numemp
          and o206_origem = 10
          and rp is true
        ');

        DB::statement('
        update contabilidade.conlancamrecurso set c130_orctiporec = recurso_dotacao
        from w_empenhos_arrumar
        join contabilidade.conlancamemp on conlancamemp.c75_numemp = w_empenhos_arrumar.c75_numemp
        where c130_conlancam = c75_codlan
        ');

        DB::statement('
        update contabilidade.conlancamcomplementorecurso
        set o201_orctiporec = recurso_dotacao, o201_complemento = o15_complemento
        from w_empenhos_arrumar
        join contabilidade.conlancamemp on conlancamemp.c75_numemp = w_empenhos_arrumar.c75_numemp
        where o201_codlan = c75_codlan
        ');
    }
}
