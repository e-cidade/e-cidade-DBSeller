<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Fix;

use Illuminate\Support\Facades\DB;

class CorrigirLancamentosSuplementacoes
{

    /**
     * @var mixed
     */
    private $exercicio;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var string
     */
    private $tableDespesa;

    public function arrumar($exercicio)
    {
        $this->exercicio = $exercicio;
        $this->tableName = 'w_suplementacoes_recursos_' . time();
        $this->bucaLancamento();

        $this->corrigeLancamentos();
        $this->criaRecursoLancamento();
    }

    private function bucaLancamento()
    {
        DB::statement("drop table if exists {$this->tableName}");
        DB::statement(<<<SQL
create table $this->tableName as
with despesas as (
  select c70_codlan as lancamento,
         c70_anousu as exercicio,
         o58_codigo as id_recurso
    from contabilidade.conlancam
    join contabilidade.conlancamdoc on c71_codlan = c70_codlan
    join contabilidade.conlancamdot on c73_codlan = c70_codlan
    join orcamento.orcdotacao on (o58_anousu, o58_coddot) = (c73_anousu, c73_coddot)
   where c70_anousu = 2023
     and c71_coddoc in (7,  9, 11, 51, 53, 56, 57, 60, 64, 65, 71, 72, 73, 74, 76, 77)
), receitas as (
  select c70_codlan as lancamento,
         c70_anousu as exercicio,
         o70_codigo as id_recurso
    from contabilidade.conlancam
    join contabilidade.conlancamdoc on c71_codlan = c70_codlan
    join contabilidade.conlancamrec on c74_codlan = c70_codlan
    join orcamento.orcreceita on (o70_anousu, o70_codrec) = (c74_anousu, c74_codrec)
   where c70_anousu = 2023
     and c71_coddoc in (52,54,55,63,75,59,61,62,8)
), unifica as (
  select x.*,
         o15_complemento as complemento
   from (
      select * from despesas
      union
      select * from receitas
   ) as x
   join orctiporec on (o15_codigo) = (id_recurso)
) select * from unifica
SQL
        );
    }

    private function corrigeLancamentos()
    {
        DB::statement(<<<SQL
update contabilidade.conlancamrecurso
   set c130_orctiporec = x.id_recurso
  from $this->tableName as x
 where c130_conlancam = x.lancamento;
SQL
        );

        DB::statement(<<<SQL
update contabilidade.conlancamcomplementorecurso
   set o201_orctiporec = id_recurso, o201_complemento = complemento
  from $this->tableName
 where o201_codlan = lancamento;
SQL
        );
    }

    /**
     * Nesse método não deve cair... mas implementei por segurança
     * @return void
     */
    private function criaRecursoLancamento()
    {
        DB::statement(<<<SQL
create temp table incluir_conlancamrecurso as
with incluir as (
    select *
      from {$this->tableName}
     where not exists(
      select 1 from conlancamrecurso where c130_conlancam = lancamento
     )
), creditos as (
   select lancamento, id_recurso, c69_credito as conta, exercicio, 'C' as natureza
   from incluir
      join contabilidade.conlancamval on lancamento = c69_codlan
), debitos as (
   select lancamento, id_recurso, c69_debito as conta, exercicio, 'D' as natureza
   from incluir
   join contabilidade.conlancamval on lancamento = c69_codlan
), unifica as (
    select * from creditos
    union all
    select * from debitos
) select nextval('conlancamrecurso_c130_sequencial_seq'::regclass) as codigo,
         lancamento,
         id_recurso,
         conta,
         exercicio,
         natureza
    from unifica
SQL
        );
        $dados = DB::select('select * from incluir_conlancamrecurso');

        if (count($dados)) {
            DB::statement('insert into conlancamrecurso select * from incluir_conlancamrecurso');
        }

        DB::statement(<<<SQL
insert into contabilidade.conlancamcomplementorecurso
select nextval('conlancamcomplementorecurso_o201_sequencial_seq'::regclass),
       lancamento,
       complemento,
       id_recurso
  from {$this->tableName}
 where not exists(
  select 1 from conlancamrecurso where c130_conlancam = lancamento
 )
SQL
        );
    }
}
