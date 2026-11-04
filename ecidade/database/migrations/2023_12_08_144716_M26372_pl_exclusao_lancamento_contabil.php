<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26372PlExclusaoLancamentoContabil extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
create or replace function contabilidade.excluir_lancamento_contabil(f_lancamento integer)
returns boolean language plpgsql
as $$
declare

begin

    create table w_excluir_autenticacao as
    select distinct *
    from (
             select k105_id as id, k105_data as data, k105_autent as autent
             from caixa.corgrupocorrente
              join contabilidade.conlancamcorgrupocorrente on conlancamcorgrupocorrente.c23_corgrupocorrente = k105_sequencial
             where conlancamcorgrupocorrente.c23_conlancam = f_lancamento

             union all

             select c86_id, c86_data, c86_autent
             from contabilidade.conlancamcorrente
             where conlancamcorrente.c86_conlancam = f_lancamento
    ) as x;

    create table w_excluir_numpre as
    select k12_numpre ,
           k12_numpar
    from caixa.cornump
    inner join w_excluir_autenticacao on w_excluir_autenticacao.id = cornump.k12_id
               and w_excluir_autenticacao.data = cornump.k12_data
               and w_excluir_autenticacao.autent = cornump.k12_autent;

    delete from contabilidade.conlancamdoc   where c71_codlan = f_lancamento;
    delete from contabilidade.conlancamcgm   where c76_codlan = f_lancamento;
    delete from contabilidade.conlancamemp   where c75_codlan = f_lancamento;
    delete from contabilidade.conlancamdig   where c78_codlan = f_lancamento;
    delete from contabilidade.conlancamdot   where c73_codlan = f_lancamento;
    delete from contabilidade.conlancamord   where c80_codlan = f_lancamento;
    delete from contabilidade.conlancamrec   where c74_codlan = f_lancamento;
    delete from contabilidade.conlancamretif where c79_codlan = f_lancamento;
    delete from contabilidade.conlancamsup   where c79_codlan = f_lancamento;

    delete from contabilidade.conlancamlr
     where c81_sequen in (select c69_sequen from conlancamval where c69_codlan = f_lancamento);

    delete from contabilidade.contacorrentedetalheconlancamval
     where c28_conlancamval in (select c69_sequen from conlancamval where c69_codlan = f_lancamento);

    delete from contabilidade.conlancamval where c69_codlan = f_lancamento;

    delete from caixa.corconf
     using w_excluir_autenticacao
    where corconf.k12_id     = w_excluir_autenticacao.id
      and corconf.k12_data   = w_excluir_autenticacao.data
      and corconf.k12_autent = w_excluir_autenticacao.autent;

    delete from empenho.retencaocorgrupocorrente
      where e47_sequencial in (
        select e47_sequencial
          from w_excluir_autenticacao
           join caixa.corgrupocorrente cg on cg.k105_id = w_excluir_autenticacao.id
                and cg.k105_data = w_excluir_autenticacao.data
                and cg.k105_autent = w_excluir_autenticacao.autent
           join empenho.retencaocorgrupocorrente r on r.e47_corgrupocorrente = cg.k105_sequencial
        );

    delete from contabilidade.conlancamcorgrupocorrente where c23_conlancam = f_lancamento;
    delete from contabilidade.conlancamcorrente where c86_conlancam = f_lancamento;
    delete from caixa.corgrupocorrente using w_excluir_autenticacao where k105_id = id and k105_data = data and k105_autent = autent;
    delete from caixa.corlanc using w_excluir_autenticacao where k12_id = id and k12_data = data and k12_autent = autent;
    delete from caixa.corempagemov using w_excluir_autenticacao where k12_id = id and k12_data = data and k12_autent = autent;
    delete from caixa.coremp using w_excluir_autenticacao where k12_id = id and k12_data = data and k12_autent = autent;
    delete from caixa.corautent using w_excluir_autenticacao where k12_id = id and k12_data = data and k12_autent = autent;
    delete from caixa.corempagemovestorno using w_excluir_autenticacao where k12_id = id and k12_data = data and k12_autent = autent;
    delete from caixa.corempagemovpagamento using w_excluir_autenticacao where k12_id = id and k12_data = data and k12_autent = autent;
    delete from caixa.conciliapendcorrente using w_excluir_autenticacao where k89_id = id and k89_data = data and k89_autent = autent;
    delete from caixa.cornump using w_excluir_autenticacao where k12_id = id and k12_data = data and k12_autent = autent;
    delete from caixa.corhist using w_excluir_autenticacao where k12_id = id and k12_data = data and k12_autent = autent;
    delete from caixa.corrente using w_excluir_autenticacao where k12_id = id and k12_data = data and k12_autent = autent;

    delete from empenho.pagordemdescontoempanulado
    where e06_pagordemdesconto in (
       select e33_pagordemdesconto
         from empenho.pagordemdescontolanc
        where e33_conlancam = f_lancamento
    );

    delete from empenho.pagordemdesconto
    where e34_sequencial in (
        select e33_pagordemdesconto
         from empenho.pagordemdescontolanc
        where e33_conlancam = f_lancamento
    );
    delete from empenho.pagordemdescontolanc where e33_conlancam = f_lancamento;

    delete from contabilidade.conlancampag   where c82_codlan = f_lancamento;
    delete from contabilidade.conlancamele   where c67_codlan = f_lancamento;
    delete from contabilidade.conlancamcompl where c72_codlan = f_lancamento;
    delete from contabilidade.conlancamnota  where c66_codlan = f_lancamento;
    delete from contabilidade.conlancamcorrente where c86_conlancam = f_lancamento;
    delete from contabilidade.conlancamslip  where c84_conlancam = f_lancamento;
    delete from contabilidade.conlancambol where c77_codlan = f_lancamento;
    delete from contabilidade.conlancaminstit where c02_codlan = f_lancamento;
    delete from contabilidade.conlancamordem  where c03_codlan = f_lancamento;
    delete from contabilidade.conlancamconcarpeculiar where c08_codlan = f_lancamento;
    delete from contabilidade.conlancamcomplementorecurso where o201_codlan = f_lancamento;
    delete from contabilidade.conlancamdepartamento where c128_conlancam = f_lancamento;
    delete from contabilidade.conlancamlogatributos where c134_codlan = f_lancamento;
    delete from contabilidade.conlancamrecurso where c130_conlancam = f_lancamento;
    delete from contabilidade.conlancamacordo where c87_codlan = f_lancamento;

    delete from contabilidade.infocomplementarvalor
     using contabilidade.conplanoatributolancamentos
     where c124_lancamento = f_lancamento
       and c123_conplanoatributolancamentos = c124_sequencial;

    delete from contabilidade.conplanoatributolancamentos where c124_lancamento = f_lancamento;
    delete from contabilidade.conlancammatestoqueinimei where c103_conlancam = f_lancamento;
    delete from contabilidade.lotelancamentoconlancam where c161_conlancam = f_lancamento;
    delete from contabilidade.conlancam where c70_codlan = f_lancamento;
    delete from contabilidade.lancamentoscontabeislog where codlan = f_lancamento;

    delete from caixa.arrecant
     using w_excluir_numpre
     where w_excluir_numpre.k12_numpre = arrecant.k00_numpre
       and w_excluir_numpre.k12_numpar = arrecant.k00_numpar;

    delete from caixa.arrepaga
     using w_excluir_numpre
     where w_excluir_numpre.k12_numpre = arrepaga.k00_numpre
       and w_excluir_numpre.k12_numpar = arrepaga.k00_numpar;

    drop table w_excluir_autenticacao;
    drop table w_excluir_numpre;

    return true;
end;
$$;
SQL
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop function if exists contabilidade.excluir_lancamento_contabil;
SQL
        );
    }
}
