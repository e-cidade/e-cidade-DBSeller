<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29037AtualizacaoPlsMsc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
drop function if exists contabilidade.valores_atributo_receita;
create or replace function contabilidade.valores_atributo_receita(f_reduz int, f_ano integer, f_dataInicio date, f_dataFim date, f_encerramento boolean)
returns table (
    reduzido int,
    siconfi varchar(4),
    complemento int,
    nr varchar(8),
    saldo_inicial numeric(17,2),
    debito_anterior numeric(17,2),
    credito_anterior numeric(17,2),
    saldo_final_anterior numeric(17,2),
    saldo_debito numeric(17,2),
    saldo_credito numeric(17,2),
    saldo_final numeric(17,2)
) language plpgsql
as $$
declare
    recordDados record;
    primeiroDiaAno date;
begin
    primeiroDiaAno = (f_ano || '-01-01')::date;
    for recordDados in (
        with saldo_inicial as (
            select c157_reduzido as reduzido,
                   c157_exercicio as exercicio,
                   c157_recurso as id_recurso,
                   planoreceita.conta,
                   case when c157_natureza = 'C' then c157_valor *-1 else c157_valor end valor,
                   (f_ano || '-01-01')::date as data
            from contabilidade.complanoexe_receita_ajuste_saldo_msc
                     join contabilidade.conplanoorcamento on c60_codcon = c157_receita
                and c60_anousu = c157_exercicio
                     join contabilidade.planoreceitaconplanoorcamento on conplanoorcamento_codigo = c60_codigo
                     join contabilidade.planoreceita on planoreceita.id = planoreceitaconplanoorcamento.planoreceita_id
                and uniao is true
            where c157_reduzido = f_reduz
              and c157_exercicio = f_ano
        ), debitos as (
            select c69_debito as reduzido,
                   c69_anousu as exercicio,
                   contabilidade.fc_recurso_conta_lancamento(c69_codlan, c69_debito, 'D') as id_recurso,
                   conta,
                   c69_valor as debito,
                   c69_data as data,
                   c53_tipo as tipo_documento
            from conlancamval
            join contabilidade.conlancamdoc on conlancamdoc.c71_codlan = conlancamval.c69_codlan
            join contabilidade.conhistdoc on conhistdoc.c53_coddoc = conlancamdoc.c71_coddoc
            join contabilidade.conlancamrec on c74_codlan = c69_codlan
            join orcamento.orcreceita on o70_codrec = c74_codrec
                 and o70_anousu = conlancamrec.c74_anousu
            join contabilidade.conplanoorcamento on c60_codcon = o70_codfon and c60_anousu = o70_anousu
            join contabilidade.planoreceitaconplanoorcamento on conplanoorcamento_codigo = c60_codigo
            join contabilidade.planoreceita on planoreceita.id = planoreceitaconplanoorcamento.planoreceita_id
                 and planoreceita.uniao is true
            where c69_debito = f_reduz
              and c69_anousu = f_ano
              and c69_data <= f_dataFim
              and planoreceita.uniao is true
              and (case
                       when f_encerramento is true then true
                       when f_encerramento is false and c53_tipo not in (1000, 1500) then true
                       else false
                end)
        ), creditos as (
            select c69_credito as reduzido,
                   c69_anousu as exercicio,
                   contabilidade.fc_recurso_conta_lancamento(c69_codlan, c69_credito, 'C') as id_recurso,
                   conta,
                   c69_valor *-1 as credito,
                   c69_data as data,
                   c53_tipo as tipo_documento
            from conlancamval
            join contabilidade.conlancamdoc on conlancamdoc.c71_codlan = conlancamval.c69_codlan
            join contabilidade.conhistdoc on conhistdoc.c53_coddoc = conlancamdoc.c71_coddoc
            join contabilidade.conlancamrec on conlancamrec.c74_codlan = conlancamval.c69_codlan
            join orcamento.orcreceita on (o70_codrec, o70_anousu) = (c74_codrec, c74_anousu)
            join contabilidade.conplanoorcamento on (c60_codcon, c60_anousu) = (o70_codfon, o70_anousu)
            join contabilidade.planoreceitaconplanoorcamento on conplanoorcamento_codigo = c60_codigo
            join contabilidade.planoreceita on planoreceita.id = planoreceitaconplanoorcamento.planoreceita_id
                 and planoreceita.uniao is true
            where c69_credito = f_reduz
              and c69_anousu = f_ano
              and c69_data <= f_dataFim
              and planoreceita.uniao is true
              and (case
                       when f_encerramento is true then true
                       when f_encerramento is false and c53_tipo not in (1000, 1500) then true
                       else false
                end)
        ), unifica as (
            select si.reduzido, si.exercicio, si.id_recurso, conta, si.data, valor as saldo_inicial, 0 as debito, 0 as credito, null as tipo_documento
            from saldo_inicial si
            union all
            select d.reduzido, d.exercicio, d.id_recurso, conta, d.data, 0 as saldo_inicial, debito, 0 as credito, tipo_documento
            from debitos d
            union all
            select c.reduzido, c.exercicio, c.id_recurso, conta, c.data, 0 as saldo_inicial, 0 as debito, credito, tipo_documento
            from creditos c
        ), totaliza as (
            select x.reduzido,  fr.codigo_siconfi, o.o15_complemento, conta,
                   sum(x.saldo_inicial) as saldo_inicial,
                   sum(case
                           when f_encerramento is true and tipo_documento not in (1000, 1500)
                               then x.debito
                           when f_encerramento is false and f_dataInicio > primeiroDiaAno and x.data < f_dataInicio
                               then x.debito
                           else 0 end
                   )::numeric(17, 2) as debito_anterior,
                   sum(case
                           when f_encerramento is true and tipo_documento not in (1000, 1500)
                               then x.credito
                           when f_encerramento is false and f_dataInicio > primeiroDiaAno and x.data < f_dataInicio
                               then x.credito
                           else 0 end
                   )::numeric(17, 2) as credito_anterior,
                   sum(case
                           when f_encerramento is true and tipo_documento in (1000, 1500)
                               then x.debito
                           when f_encerramento is false and x.data >= f_dataInicio
                               then x.debito
                           else 0 end
                   )::numeric(17, 2) as saldo_debito,
                   sum(case
                           when f_encerramento is true and tipo_documento in (1000, 1500)
                               then x.credito
                           when f_encerramento is false and x.data >= f_dataInicio
                               then x.credito
                           else 0 end
                   )::numeric(17, 2) as saldo_credito
            from unifica as x
            join orcamento.orctiporec o on o.o15_codigo = x.id_recurso
            join orcamento.fonterecurso fr on fr.orctiporec_id = o.o15_codigo
                 and fr.exercicio = x.exercicio
            group by 1,2,3,4
        ) select * from totaliza
    ) loop
        reduzido = recordDados.reduzido;
        siconfi = recordDados.codigo_siconfi;
        complemento = recordDados.o15_complemento;
        nr = recordDados.conta;
        saldo_inicial = recordDados.saldo_inicial;
        debito_anterior = recordDados.debito_anterior;
        credito_anterior = recordDados.credito_anterior;
        saldo_final_anterior = recordDados.saldo_inicial + recordDados.debito_anterior + recordDados.credito_anterior;
        saldo_debito = recordDados.saldo_debito;
        saldo_credito = recordDados.saldo_credito;
        saldo_final = saldo_final_anterior + recordDados.saldo_debito + recordDados.saldo_credito;
        return next;
    end loop;
end;
$$;

drop function if exists contabilidade.valores_atributo_empenho_exercicio;
create or replace function contabilidade.valores_atributo_empenho_exercicio(f_reduz int, f_ano integer, f_dataInicio date, f_dataFim date, f_encerramento boolean)
returns table (
    reduzido int,
    siconfi varchar(4),
    complemento int,
    nd varchar(8),
    funcao char(2),
    subfuncao char(3),
    saldo_inicial numeric(17,2),
    debito_anterior numeric(17,2),
    credito_anterior numeric(17,2),
    saldo_final_anterior numeric(17,2),
    saldo_debito numeric(17,2),
    saldo_credito numeric(17,2),
    saldo_final numeric(17,2)
) language plpgsql
as $$
declare
    recordDados record;
    primeiroDiaAno date;
begin
    primeiroDiaAno = (f_ano || '-01-01')::date;
    for recordDados in (
        with saldo_inicial as (
            select c156_reduzido as reduzido,
                   c156_exercicio as exercicio,
                   o52_siconfi as funcao,
                   o53_siconfi as subfuncao,
                   planodespesa.conta,
                   c156_recurso as id_recurso,
                   case when c156_natureza = 'C' then c156_valor *-1 else c156_valor end valor,
                   (f_ano || '-01-01')::date as data
              from complanoexe_despesa_ajuste_saldo_msc
              join orcamento.orcfuncao on o52_funcao = c156_funcao
              join orcamento.orcsubfuncao on o53_subfuncao= c156_subfuncao
              join contabilidade.conplanoorcamento on c60_codcon = c156_elemento
                  and c60_anousu = c156_exercicio
              join contabilidade.planodespesaconplanoorcamento on conplanoorcamento_codigo = c60_codigo
              join contabilidade.planodespesa on planodespesa.id = planodespesaconplanoorcamento.planodespesa_id
                  and uniao is true
             where c156_reduzido = f_reduz
               and c156_exercicio = f_ano
        ), debitos as (
            select c69_debito as reduzido,
                   c69_anousu as exercicio,
                   contabilidade.fc_recurso_conta_lancamento(c69_codlan, c69_debito, 'D') as id_recurso,
                   contabilidade.retorna_conta_despesa(c69_codlan, c53_tipo) as conta,
                   o52_siconfi as funcao,
                   o53_siconfi as subfuncao,
                   c69_valor as debito,
                   c69_data as data,
                   c53_tipo as tipo_documento
            from conlancamval
            join contabilidade.conlancamdoc on conlancamdoc.c71_codlan = conlancamval.c69_codlan
            join contabilidade.conhistdoc on conhistdoc.c53_coddoc = conlancamdoc.c71_coddoc
            join contabilidade.conlancamdot on c73_codlan = c69_codlan
            join orcamento.orcdotacao on (o58_anousu, o58_coddot) = (c73_anousu, c73_coddot)
            join orcamento.orcfuncao on o52_funcao = o58_funcao
            join orcamento.orcsubfuncao on o53_subfuncao= o58_subfuncao
            where c69_debito = f_reduz
              and c69_anousu = f_ano
              and c69_data <= f_dataFim
              and (case
                       when f_encerramento is true then true
                       when f_encerramento is false and c53_tipo not in (1000, 1500) then true
                       else false
                   end)
        ), creditos as (
            select c69_credito as reduzido,
                   c69_anousu as exercicio,
                   contabilidade.fc_recurso_conta_lancamento(c69_codlan, c69_credito, 'C') as id_recurso,
                   contabilidade.retorna_conta_despesa(c69_codlan, c53_tipo) as conta,
                   o52_siconfi as funcao,
                   o53_siconfi as subfuncao,
                   c69_valor *-1 as credito,
                   c69_data as data,
                   c53_tipo as tipo_documento
            from conlancamval
            join contabilidade.conlancamdoc on conlancamdoc.c71_codlan = conlancamval.c69_codlan
            join contabilidade.conhistdoc on conhistdoc.c53_coddoc = conlancamdoc.c71_coddoc
            join contabilidade.conlancamdot on c73_codlan = c69_codlan
            join orcamento.orcdotacao on (o58_anousu, o58_coddot) = (c73_anousu, c73_coddot)
            join orcamento.orcfuncao on o52_funcao = o58_funcao
            join orcamento.orcsubfuncao on o53_subfuncao= o58_subfuncao
            where c69_credito = f_reduz
              and c69_anousu = f_ano
              and c69_data <= f_dataFim
              and (case
                       when f_encerramento is true then true
                       when f_encerramento is false and c53_tipo not in (1000, 1500) then true
                       else false
                   end)
        ), unifica as (
            select si.reduzido, si.exercicio, si.id_recurso, conta, si.funcao, si.subfuncao, si.data, valor as saldo_inicial, 0 as debito, 0 as credito, null as tipo_documento
            from saldo_inicial si
            union all
            select d.reduzido, d.exercicio, d.id_recurso, conta, d.funcao, d.subfuncao, d.data, 0 as saldo_inicial, debito, 0 as credito, tipo_documento
            from debitos d
            union all
            select c.reduzido, c.exercicio, c.id_recurso, conta, c.funcao, c.subfuncao, c.data, 0 as saldo_inicial, 0 as debito, credito, tipo_documento
            from creditos c
        ), totaliza as (
            select x.reduzido,  fr.codigo_siconfi, o.o15_complemento, x.conta, x.funcao, x.subfuncao,
                   sum(x.saldo_inicial) as saldo_inicial,
                   sum(case
                           when f_encerramento is true and tipo_documento not in (1000, 1500)
                               then x.debito
                           when f_encerramento is false and f_dataInicio > primeiroDiaAno and x.data < f_dataInicio
                               then x.debito
                           else 0 end
                   )::numeric(17, 2) as debito_anterior,
                   sum(case
                           when f_encerramento is true and tipo_documento not in (1000, 1500)
                               then x.credito
                           when f_encerramento is false and f_dataInicio > primeiroDiaAno and x.data < f_dataInicio
                               then x.credito
                           else 0 end
                   )::numeric(17, 2) as credito_anterior,
                   sum(case
                           when f_encerramento is true and tipo_documento in (1000, 1500)
                               then x.debito
                           when f_encerramento is false and x.data >= f_dataInicio
                               then x.debito
                           else 0 end
                   )::numeric(17, 2) as saldo_debito,
                   sum(case
                           when f_encerramento is true and tipo_documento in (1000, 1500)
                               then x.credito
                           when f_encerramento is false and x.data >= f_dataInicio
                               then x.credito
                           else 0 end
                   )::numeric(17, 2) as saldo_credito
            from unifica as x
            join orcamento.orctiporec o on o.o15_codigo = x.id_recurso
            join orcamento.fonterecurso fr on fr.orctiporec_id = o.o15_codigo
                 and fr.exercicio = x.exercicio
            group by 1,2,3,4,5,6
        ) select * from totaliza
    ) loop
        reduzido = recordDados.reduzido;
        siconfi = recordDados.codigo_siconfi;
        complemento = recordDados.o15_complemento;
        nd = recordDados.conta;
        funcao = lpad(recordDados.funcao, 2, '0');
        subfuncao = lpad(recordDados.subfuncao, 3, '0');
        saldo_inicial = recordDados.saldo_inicial;
        debito_anterior = recordDados.debito_anterior;
        credito_anterior = recordDados.credito_anterior;
        saldo_final_anterior = recordDados.saldo_inicial + recordDados.debito_anterior + recordDados.credito_anterior;
        saldo_debito = recordDados.saldo_debito;
        saldo_credito = recordDados.saldo_credito;
        saldo_final = saldo_final_anterior + recordDados.saldo_debito + recordDados.saldo_credito;

        return next;
    end loop;
end;
$$;

drop function if exists contabilidade.valores_atributo_empenho_rp;
create or replace function contabilidade.valores_atributo_empenho_rp(f_reduz int, f_ano integer, f_dataInicio date, f_dataFim date, f_encerramento boolean)
returns table (
    reduzido int,
    siconfi varchar(4),
    complemento int,
    nd varchar(8),
    funcao char(2),
    subfuncao char(3),
    ano_empenho integer,
    anousu_rp integer,
    saldo_inicial numeric(17,2),
    debito_anterior numeric(17,2),
    credito_anterior numeric(17,2),
    saldo_final_anterior numeric(17,2),
    saldo_debito numeric(17,2),
    saldo_credito numeric(17,2),
    saldo_final numeric(17,2)
) language plpgsql
as $$
declare
    recordDados record;
    primeiroDiaAno date;
begin
    primeiroDiaAno = (f_ano || '-01-01')::date;
    for recordDados in (
        with saldo_inicial as (
            select c151_reduzido  as reduzido,
                   c151_exercicio as exercicio,
                   e.id_recurso,
                   e.conta,
                   e.funcao,
                   e.subfuncao,
                   e.ano_empenho,
                   e.anousu_rp,
                   coalesce((case WHEN c151_natureza = 'C' THEN c151_valor * -1 else c151_valor end), 0)::numeric(17, 2) as saldo_inicial,
                   (f_ano || '-01-01')::date as data,
                   null::int as tipo_documento
            from contabilidade.conplanoexeempenho
            join contabilidade.dados_auxiliar_empenho_matriz(c151_empenho, c151_exercicio) as e
                 on e.numemp = c151_empenho
            where c151_reduzido = f_reduz
              and c151_exercicio = f_ano

            union all

            select c162_reduzido  as reduzido,
                   c162_exercicio as exercicio,
                   c162_recurso as id_recurso,
                   planodespesa.conta,
                   o52_siconfi::varchar as funcao,
                   o53_siconfi::varchar as subfuncao,
                   c162_ai as ano_empenho,
                   c162_exercicio as anousu_rp,
                   coalesce((case WHEN c162_natureza = 'C' THEN c162_valor * -1 else c162_valor end), 0)::numeric(17, 2) as saldo_inicial,
                   (f_ano || '-01-01')::date as data,
                   null::int as tipo_documento
            from contabilidade.conplanoexeconciliarp
            join orcamento.orcfuncao on o52_funcao = c162_funcao
            join orcamento.orcsubfuncao on o53_subfuncao= c162_subfuncao
            join contabilidade.conplanoorcamento on c60_codcon = c162_elemento
                 and c60_anousu = (case when c162_ai < 2023 then 2023 else c162_ai end)
            join contabilidade.planodespesaconplanoorcamento on conplanoorcamento_codigo = c60_codigo
            join contabilidade.planodespesa on planodespesa.id = planodespesaconplanoorcamento.planodespesa_id
                 and uniao is true
            where c162_reduzido = f_reduz
              and c162_exercicio = f_ano
        ), debitos as (
            select c69_codlan as lancamento,
                   c69_debito as reduzido,
                   c69_anousu as exercicio,
                   contabilidade.fc_recurso_conta_lancamento(c69_codlan, c69_debito, 'D') as id_recurso,
                   c69_valor as debito,
                   c69_data as data,
                   c53_tipo as tipo_documento
            from conlancamval
            join contabilidade.conlancamdoc on conlancamdoc.c71_codlan = conlancamval.c69_codlan
            join contabilidade.conhistdoc on conhistdoc.c53_coddoc = conlancamdoc.c71_coddoc
            where c69_debito = f_reduz
              and c69_anousu = f_ano
              and c69_data <= f_dataFim
              and (case
                       when f_encerramento is true then true
                       when f_encerramento is false and c53_tipo not in (1000, 1500) then true
                       else false
                end)
        ), atributos_debitos as (
            select debitos.*,
                   e.conta,
                   e.funcao,
                   e.subfuncao,
                   e.ano_empenho,
                   e.anousu_rp
            from debitos
            join contabilidade.conlancamemp on c75_codlan = lancamento
            join contabilidade.dados_auxiliar_empenho_matriz(c75_numemp, exercicio) as e
                 on e.numemp = conlancamemp.c75_numemp

            union all

            select debitos.*,
                   planodespesa.conta,
                   o52_siconfi as funcao,
                   o53_siconfi as subfuncao,
                   c155_ai as ano_empenho,
                   debitos.exercicio as anousu_rp
            from debitos
            join contabilidade.conlancamajustesaldoconta on c155_conlancam = lancamento
            join orcamento.orcfuncao on o52_funcao = c155_funcao
            join orcamento.orcsubfuncao on o53_subfuncao= c155_subfuncao
            join contabilidade.conplanoorcamento on c60_codcon = c155_elemento and c60_anousu = exercicio
            join contabilidade.planodespesaconplanoorcamento on conplanoorcamento_codigo = c60_codigo
            join contabilidade.planodespesa on planodespesa.id = planodespesaconplanoorcamento.planodespesa_id
                 and uniao is true
            where c155_ai is not null
        ), creditos as (
            select c69_codlan as lancamento,
                   c69_credito as reduzido,
                   c69_anousu as exercicio,
                   contabilidade.fc_recurso_conta_lancamento(c69_codlan, c69_credito, 'C') as id_recurso,
                   c69_valor *-1 as credito,
                   c69_data as data,
                   c53_tipo as tipo_documento
            from conlancamval
            join contabilidade.conlancamdoc on conlancamdoc.c71_codlan = conlancamval.c69_codlan
            join contabilidade.conhistdoc on conhistdoc.c53_coddoc = conlancamdoc.c71_coddoc
            where c69_credito = f_reduz
              and c69_anousu = f_ano
              and c69_data <= f_dataFim
              and (case
                       when f_encerramento is true then true
                       when f_encerramento is false and c53_tipo not in (1000, 1500) then true
                       else false
                end)
        ), atributos_creditos as (
            select creditos.*,
                   e.conta,
                   e.funcao,
                   e.subfuncao,
                   e.ano_empenho,
                   e.anousu_rp
            from creditos
            join contabilidade.conlancamemp on c75_codlan = lancamento
            join contabilidade.dados_auxiliar_empenho_matriz(c75_numemp, exercicio) as e
                 on e.numemp = conlancamemp.c75_numemp

            union all

            select creditos.*,
                   planodespesa.conta,
                   o52_siconfi as funcao,
                   o53_siconfi as subfuncao,
                   c155_ai as ano_empenho,
                   creditos.exercicio as anousu_rp
            from creditos
            join contabilidade.conlancamajustesaldoconta on c155_conlancam = lancamento
            join orcamento.orcfuncao on o52_funcao = c155_funcao
            join orcamento.orcsubfuncao on o53_subfuncao= c155_subfuncao
            join contabilidade.conplanoorcamento on c60_codcon = c155_elemento
                 and c60_anousu = exercicio
            join contabilidade.planodespesaconplanoorcamento on conplanoorcamento_codigo = c60_codigo
            join contabilidade.planodespesa on planodespesa.id = planodespesaconplanoorcamento.planodespesa_id
                 and uniao is true
            where c155_ai is not null
        ), unifica as (
            select s.reduzido, s.exercicio, s.id_recurso, s.conta, s.funcao, s.subfuncao, s.ano_empenho, s.anousu_rp, s.data, s.saldo_inicial, 0 as debito, 0 as credito, tipo_documento
            from saldo_inicial s
            union all
            select d.reduzido, d.exercicio, d.id_recurso, conta, d.funcao, d.subfuncao, d.ano_empenho, d.anousu_rp, d.data, 0 as saldo_inicial, debito, 0 as credito, tipo_documento
            from atributos_debitos d
            union all
            select c.reduzido, c.exercicio, c.id_recurso, conta, c.funcao, c.subfuncao, c.ano_empenho, c.anousu_rp, c.data, 0 as saldo_inicial, 0 as debito, credito, tipo_documento
            from atributos_creditos c
        ), totaliza as (
            select x.reduzido,  fr.codigo_siconfi, o.o15_complemento, x.conta, x.funcao, x.subfuncao, x.ano_empenho, x.anousu_rp,
                   sum(x.saldo_inicial) as saldo_inicial,
                   sum(case
                           when f_encerramento is true and tipo_documento not in (1000, 1500)
                               then x.debito
                           when f_encerramento is false and f_dataInicio > primeiroDiaAno and x.data < f_dataInicio
                               then x.debito
                           else 0 end
                   )::numeric(17, 2) as debito_anterior,
                   sum(case
                           when f_encerramento is true and tipo_documento not in (1000, 1500)
                               then x.credito
                           when f_encerramento is false and f_dataInicio > primeiroDiaAno and x.data < f_dataInicio
                               then x.credito
                           else 0 end
                   )::numeric(17, 2) as credito_anterior,
                   sum(case
                           when f_encerramento is true and tipo_documento in (1000, 1500)
                               then x.debito
                           when f_encerramento is false and x.data >= f_dataInicio
                               then x.debito
                           else 0 end
                   )::numeric(17, 2) as saldo_debito,
                   sum(case
                           when f_encerramento is true and tipo_documento in (1000, 1500)
                               then x.credito
                           when f_encerramento is false and x.data >= f_dataInicio
                               then x.credito
                           else 0 end
                   )::numeric(17, 2) as saldo_credito
            from unifica as x
            join orcamento.orctiporec o on o.o15_codigo = x.id_recurso
            join orcamento.fonterecurso fr on fr.orctiporec_id = o.o15_codigo
                 and fr.exercicio = x.exercicio
            group by 1,2,3,4,5,6,7,8
        ) select * from totaliza
    ) loop
            reduzido = recordDados.reduzido;
            siconfi = recordDados.codigo_siconfi;
            complemento = recordDados.o15_complemento;
            nd = recordDados.conta;
            funcao = lpad(recordDados.funcao, 2, '0');
            subfuncao = lpad(recordDados.subfuncao, 3, '0');
            ano_empenho = recordDados.ano_empenho;
            anousu_rp = recordDados.anousu_rp;
            saldo_inicial = recordDados.saldo_inicial;
            debito_anterior = recordDados.debito_anterior;
            credito_anterior = recordDados.credito_anterior;
            saldo_final_anterior = recordDados.saldo_inicial + recordDados.debito_anterior + recordDados.credito_anterior;
            saldo_debito = recordDados.saldo_debito;
            saldo_credito = recordDados.saldo_credito;
            saldo_final = saldo_final_anterior + recordDados.saldo_debito + recordDados.saldo_credito;
            return next;
        end loop;
end;
$$;

drop function if exists contabilidade.retorna_conta_despesa;
create function contabilidade.retorna_conta_despesa(lancamento integer, tipo integer) returns varchar
    language plpgsql
as
$$
declare
    retorno varchar;
begin

    select case
           when tipo in (1000, 1500, 2000, 40, 50, 60)
               then contabilidade.conta_elemento_dotacao(lancamento)
           when tipo in (3000) and not exists(select 1 from conlancamemp where c75_codlan = lancamento)
               then contabilidade.conta_elemento_dotacao(lancamento)
           else  contabilidade.conta_desdobramento_empenho(lancamento)
           end as conta
    into retorno;

    return retorno;
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
