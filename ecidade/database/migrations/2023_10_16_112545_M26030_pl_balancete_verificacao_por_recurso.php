<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26030PlBalanceteVerificacaoPorRecurso extends Migration
{
    /**
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop function if exists contabilidade.balancete_verificacao_por_recurso;
create or replace function contabilidade.balancete_verificacao_por_recurso(
    f_ano integer,
    f_dataInicio date,
    f_dataFim date,
    f_encerramento boolean,
    f_reduzidos int[]
)
    returns table (
          estrutural varchar(13),
          reduzido integer,
          exercicio integer,
          codigo_conplano integer,
          classe integer,
          nome varchar,
          instituicao integer,
          principal boolean,
          id_recurso integer,
          siconfi varchar(4),
          gestao varchar(4),
          subrecurso varchar(4),
          complemento integer,
          indicador_superavit char(1),
          natureza_informacao char(1),
          conta_bancaria_id int,
          saldo_anterior numeric(17,2),
          saldo_debito numeric(17,2),
          saldo_credito numeric(17,2),
          saldo_final numeric(17,2),
          sinal_anterior char(1),
          sinal_final char(1)
    )
    language plpgsql
as $$
declare
    primeiroDiaAno date;
    recordDados record;
    testeRecord record;
begin

    primeiroDiaAno = (f_ano || '-01-01')::date;

    for recordDados in (

        with contas as (
            select c60_estrut as estrutural,
                   c60_descr as nome,
                   c60_codigo as codigo_conplano,
                   c61_instit as instituicao,
                   c61_reduz as reduzido,
                   substring(c60_estrut,1 ,1)::int classe,
                   c61_anousu,
                   c61_codigo as recurso_principal,
                   c60_consistemaconta,
                   c60_identificadorfinanceiro,
                   c56_contabancaria
              from contabilidade.conplano
              join contabilidade.conplanoreduz on (c61_codcon, c61_anousu) = (c60_codcon, c60_anousu)
              left join contabilidade.conplanocontabancaria on conplanocontabancaria.c56_anousu = c61_anousu
                   and conplanocontabancaria.c56_reduz = c61_reduz
             where c60_anousu = f_ano
               and (case
                        when (array_length(f_reduzidos, 1) is null) then true
                        when array_length(f_reduzidos, 1) > 0 and array[c61_reduz] <@ f_reduzidos then true
                        else false
                 end)
        ), saldo_inicial as (
            select contas.*,
                   (case WHEN c143_natureza = 'C' THEN c143_saldo * -1 else c143_saldo end)::numeric(17, 2) as saldo,
                   o15_codigo as id_recurso,
                   codigo_siconfi as siconfi,
                   fr.gestao,
                   o15_recurso as subrecurso,
                   o15_complemento as complemento,
                   case when recurso_principal = o15_codigo then true else false end as principal
            from contas
            join contabilidade.conplanoexecontacorrente on c143_conplanoreduz = contas.reduzido
                 and c143_exercicio = contas.c61_anousu
            join contabilidade.conplanoexecontacorrenteatributo on c144_conplanoexecontacorrente = conplanoexecontacorrente.id
            join contabilidade.conplanosistemaatributos on c129_conplanosistema  = c143_conplanosistema
                 and c129_conplanoinfocomplementar = c144_conplanoinfocomplementar
            join orcamento.orctiporec on o15_codigo = c144_valor::int
            join orcamento.fonterecurso fr on fr.orctiporec_id = o15_codigo
                 and fr.exercicio = c143_exercicio
            where c143_conplanosistema = 100
        ), totaliza_si as (
            select  si.estrutural,
                    si.classe,
                    si.nome,
                    si.codigo_conplano,
                    si.instituicao,
                    si.reduzido,
                    si.id_recurso,
                    si.siconfi,
                    si.gestao,
                    si.subrecurso,
                    si.complemento,
                    si.principal,
                    si.c60_consistemaconta,
                    si.c60_identificadorfinanceiro,
                    si.c56_contabancaria,
                    sum(si.saldo) as saldo
            from saldo_inicial si
            group by si.estrutural, si.classe, si.nome, si.codigo_conplano, si.instituicao, si.reduzido,
                     si.id_recurso, si.siconfi, si.gestao, si.subrecurso, si.complemento, si.principal,
                     si.c60_consistemaconta, si.c60_identificadorfinanceiro, si.c56_contabancaria
        ), lancamentos_exercicio as (
           select c70_codlan
           from contabilidade.conlancam
           join contabilidade.conlancamdoc on conlancamdoc.c71_codlan = conlancam.c70_codlan
           join contabilidade.conhistdoc on conhistdoc.c53_coddoc = conlancamdoc.c71_coddoc
           where c70_anousu = f_ano
             and (case
                   when f_encerramento is true then true
                   when f_encerramento is false and c53_tipo not in (1000, 1500)
                     then true
                   else false
                 end)
        ), lancamentos as (
            select x.*,
                   codigo_siconfi as siconfi,
                   fr.gestao,
                   o15_recurso as subrecurso,
                   o15_complemento as complemento,
                   case when recurso_principal = x.id_recurso then true else false end as principal
            from (
                     select contas.*,
                            c69_codlan,
                            c69_valor *-1 as saldo,
                            'C' as natureza,
                            c69_data as c124_data,
                            fc_recurso_conta_lancamento(c69_codlan, contas.reduzido, 'C') as id_recurso
                     from contas
                     join contabilidade.conlancamval on c69_credito = contas.reduzido
                          and c69_anousu = contas.c61_anousu
                     join lancamentos_exercicio on c70_codlan = c69_codlan
                     where c69_data between primeiroDiaAno and f_dataFim
                     union all
                     select contas.*,
                         c69_codlan,
                         c69_valor as saldo,
                         'D' as natureza,
                         c69_data as c124_data,
                         fc_recurso_conta_lancamento(c69_codlan, contas.reduzido, 'D') as id_recurso
                     from contas
                     join contabilidade.conlancamval on c69_debito = contas.reduzido
                         and c69_anousu = contas.c61_anousu
                     join lancamentos_exercicio on c70_codlan = c69_codlan
                     where c69_data between primeiroDiaAno and f_dataFim
                 ) as x
            join orcamento.orctiporec on o15_codigo = x.id_recurso
            join orcamento.fonterecurso fr on fr.orctiporec_id = o15_codigo
                 and fr.exercicio = x.c61_anousu
        ), totaliza_lancamentos as (
           select lancamentos.estrutural,
                  lancamentos.classe,
                  lancamentos.nome,
                  lancamentos.codigo_conplano,
                  lancamentos.instituicao,
                  lancamentos.reduzido,
                  lancamentos.id_recurso,
                  lancamentos.siconfi,
                  lancamentos.gestao,
                  lancamentos.subrecurso,
                  lancamentos.complemento,
                  lancamentos.principal,
                  lancamentos.c60_consistemaconta,
                  lancamentos.c60_identificadorfinanceiro,
                  lancamentos.c56_contabancaria,
                  0 as saldo,
                  sum(case when natureza = 'D' and f_dataInicio > primeiroDiaAno and c124_data < f_dataInicio
                      then saldo else 0 end
                  )::numeric(17, 2) as saldo_anterior_debito,
                  sum(case when natureza = 'C' and f_dataInicio > primeiroDiaAno and c124_data < f_dataInicio
                      then saldo else 0
                  end)::numeric(17, 2) as saldo_anterior_credito,
                  sum(case when natureza = 'D' and c124_data >= f_dataInicio then saldo else 0 end)::numeric(17, 2) as saldo_debito,
                  sum(case when natureza = 'C' and c124_data >= f_dataInicio then saldo else 0 end)::numeric(17, 2) as saldo_credito
             from lancamentos
             group by lancamentos.estrutural, lancamentos.classe, lancamentos.nome, lancamentos.codigo_conplano,
                      lancamentos.instituicao, lancamentos.reduzido, lancamentos.id_recurso,
                      lancamentos.siconfi, lancamentos.gestao, lancamentos.subrecurso, lancamentos.complemento,
                      lancamentos.principal, lancamentos.c60_consistemaconta, lancamentos.c60_identificadorfinanceiro,
                      lancamentos.c56_contabancaria
        ), unifica_dados as (
            select x.estrutural,
                   x.classe,
                   x.nome,
                   x.codigo_conplano,
                   x.instituicao,
                   x.reduzido,
                   x.id_recurso,
                   x.siconfi,
                   x.gestao,
                   x.subrecurso,
                   x.complemento,
                   x.principal,
                   x.c60_consistemaconta,
                   x.c60_identificadorfinanceiro,
                   x.c56_contabancaria,
                   sum(x.saldo)::numeric(17,2) as saldo,
                   sum(x.saldo_anterior_debito)::numeric(17,2) as saldo_anterior_debito,
                   sum(x.saldo_anterior_credito)::numeric(17,2) as saldo_anterior_credito,
                   sum(x.saldo_debito)::numeric(17,2) as saldo_debito,
                   sum(x.saldo_credito)::numeric(17,2) as saldo_credito
            from (
               select totaliza_si.*,
                      0 as saldo_anterior_debito,
                      0 as saldo_anterior_credito,
                      0 as saldo_debito,
                      0 as saldo_credito
                 from totaliza_si
                union all
               select totaliza_lancamentos.* from totaliza_lancamentos
            ) as x
            group by x.estrutural, x.classe, x.nome, x.codigo_conplano, x.instituicao, x.reduzido,
                     x.id_recurso, x.siconfi, x.gestao, x.subrecurso, x.complemento, x.principal,
                     x.c60_consistemaconta, x.c60_identificadorfinanceiro, x.c56_contabancaria
        )

        select unifica_dados.*
            from unifica_dados
    ) loop
            estrutural = recordDados.estrutural ;
            reduzido = recordDados.reduzido ;
            codigo_conplano = recordDados.codigo_conplano ;
            classe = recordDados.classe ;
            nome = recordDados.nome ;
            instituicao = recordDados.instituicao ;
            id_recurso = recordDados.id_recurso ;
            siconfi = recordDados.siconfi ;
            gestao = recordDados.gestao ;
            subrecurso = recordDados.subrecurso ;
            complemento = recordDados.complemento ;
            principal = recordDados.principal;
            indicador_superavit = recordDados.c60_identificadorfinanceiro;

            natureza_informacao = case
                    when recordDados.c60_consistemaconta = 1 then 'O'
                    when recordDados.c60_consistemaconta = 2 then 'P'
                    when recordDados.c60_consistemaconta = 3 then 'C'
                    else 'X'
                end;

            conta_bancaria_id = recordDados.c56_contabancaria;
            saldo_anterior = recordDados.saldo + recordDados.saldo_anterior_debito + recordDados.saldo_anterior_credito;
            saldo_debito = recordDados.saldo_debito;
            saldo_credito = recordDados.saldo_credito;
            saldo_final = (saldo_anterior + saldo_debito + saldo_credito)::numeric(17,2);

            sinal_anterior = fc_sinal_saldo_conta(classe, saldo_anterior);
            sinal_final = fc_sinal_saldo_conta(classe, saldo_final);

            exercicio = f_ano;
        return next;
    end loop;
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
        //
    }
}
