<?php

use Illuminate\Database\Migrations\Migration;

class M26817PlMatriz extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDadosAuxiliarEmpenho();
        $this->upValoresAtributoEmpenhoExercicio();
        $this->upValoresAtributoEmpenhoRP();
        $this->upValoresAtributoReceita();
        $this->upValoresAtributoRecurso();
        $this->upValoresAtributoDc();
        $this->upMatriz();
    }

    private function upMatriz()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop function if exists contabilidade.matriz;

create or replace function contabilidade.matriz(
    f_ano integer,
    f_instituicao integer,
    f_dataInicio date,
    f_dataFim date,
    f_encerramento boolean
) returns table (
    estrutural varchar(13),
    estrutural_padrao varchar(13),
    reduzido integer,
    exercicio integer,
    instituicao integer,
    poder_ordao varchar(6),
    siconfi varchar(4),
    complemento integer,
    indicador_superavit char(1),
    divida_consolidada char(1),
    nr varchar(8),
    nd varchar(8),
    ai integer,
    funcao varchar(2),
    subfuncao varchar(3),
    informacoescomplementares varchar(20),
    saldo_anterior numeric(17,2),
    saldo_debito numeric(17,2),
    saldo_credito numeric(17,2),
    saldo_final numeric(17,2)
) language plpgsql
as $$
declare
    primeiroDiaAno date;
    recordContas record;
    recordDados record;
begin

    primeiroDiaAno = (f_ano || '-01-01')::date;

    for recordContas in (
        SELECT c60_estrut,
               c60_codigo,
               c60_consistemaconta,
               c60_identificadorfinanceiro,
               c61_instit,
               c61_reduz,
               c61_anousu,
               c61_codigo,
               db21_codigosiconfi,
               pcasp.conta,
               pcasp.informacoescomplementares
        from contabilidade.conplano
        join contabilidade.conplanoreduz ON (c61_codcon,c61_anousu) = (c60_codcon, c60_anousu)
        join contabilidade.pcaspconplano ON conplano_codigo = c60_codigo
        join contabilidade.pcasp ON pcasp.id = pcasp_id
        join configuracoes.db_config ON codigo = c61_instit
        join configuracoes.db_tipoinstit ON db21_tipoinstit = db21_codtipo
        WHERE c61_anousu = f_ano
          and c61_instit = f_instituicao
          and uniao = 't'
    ) loop
        for recordDados in (
            select at.reduzido,
                   at.saldo_final_anterior,
                   at.saldo_debito,
                   at.saldo_credito,
                   at.saldo_final,
                   null as siconfi,
                   null as complemento,
                   null as nr,
                   null as nd,
                   null as funcao,
                   null as subfuncao,
                   null::integer as ano_empenho
            from contabilidade.valores_atributo_dc(recordContas.c61_reduz,recordContas.c61_anousu,f_dataInicio,f_dataFim, f_encerramento) as at
            where recordContas.informacoescomplementares in ('PO', 'PO,FP', 'PO,FP,DC')

            union all

            select at.reduzido,
                   at.saldo_final_anterior,
                   at.saldo_debito,
                   at.saldo_credito,
                   at.saldo_final,
                   at.siconfi,
                   at.complemento,
                   null as nr,
                   null as nd,
                   null as funcao,
                   null as subfuncao,
                   null::integer as ano_empenho
            from contabilidade.valores_atributo_recurso(recordContas.c61_reduz,recordContas.c61_anousu,f_dataInicio,f_dataFim, f_encerramento) as at
            where recordContas.informacoescomplementares in ('PO,FP,DC,FR', 'PO,FP,FR,CO', 'PO,FR,CO')

            union all

            select at.reduzido,
                   at.saldo_final_anterior,
                   at.saldo_debito,
                   at.saldo_credito,
                   at.saldo_final,
                   at.siconfi,
                   at.complemento,
                   at.nr,
                   null as nd,
                   null as funcao,
                   null as subfuncao,
                   null::integer as ano_empenho
            from contabilidade.valores_atributo_receita (recordContas.c61_reduz,recordContas.c61_anousu,f_dataInicio,f_dataFim, f_encerramento) as at
            where recordContas.informacoescomplementares in ('PO,FR,CO,NR')

            union all

            select at.reduzido,
                   at.saldo_final_anterior,
                   at.saldo_debito,
                   at.saldo_credito,
                   at.saldo_final,
                   at.siconfi,
                   at.complemento,
                   null as nr,
                   at.nd,
                   at.funcao,
                   at.subfuncao,
                   null::integer as ano_empenho
            from contabilidade.valores_atributo_empenho_exercicio(recordContas.c61_reduz,recordContas.c61_anousu,f_dataInicio,f_dataFim, f_encerramento) as at
            where recordContas.informacoescomplementares in ('PO,FS,FR,CO,ND')

            union all

            select at.reduzido,
                   at.saldo_final_anterior,
                   at.saldo_debito,
                   at.saldo_credito,
                   at.saldo_final,
                   at.siconfi,
                   at.complemento,
                   null as nr,
                   at.nd,
                   at.funcao,
                   at.subfuncao,
                   at.ano_empenho::integer
            from contabilidade.valores_atributo_empenho_rp(recordContas.c61_reduz, recordContas.c61_anousu, f_dataInicio, f_dataFim, f_encerramento) as at
            where recordContas.informacoescomplementares in ('PO,FS,FR,CO,ND,AI')
        ) loop
            estrutural = recordContas.c60_estrut;
            estrutural_padrao = recordContas.conta;
            reduzido = recordContas.c61_reduz;
            exercicio = recordContas.c61_anousu;
            instituicao = recordContas.c61_instit;
            poder_ordao = recordContas.db21_codigosiconfi;

            -- Atributo do Superavit Financeiro (Financeiro/Permanente)
            if strpos(recordContas.informacoescomplementares, 'FP') = 0 or
               (strpos(recordContas.informacoescomplementares, 'FP') != 0 and recordContas.c60_identificadorfinanceiro = 'N') then
                indicador_superavit = null;
            elsif recordContas.c60_identificadorfinanceiro = 'F' then
                indicador_superavit = 1;
            else
                indicador_superavit = 2;
            end if;

            -- Divida Consolidada: 1 - não compoem a DC
            divida_consolidada = case
                 when recordContas.c60_consistemaconta != 9 and strpos(recordContas.informacoescomplementares, 'DC') != 0
                     then 1
                 else null
            end;

            informacoescomplementares = recordContas.informacoescomplementares;
            siconfi = recordDados.siconfi;
            complemento = recordDados.complemento;
            nr = recordDados.nr;
            nd = recordDados.nd;
            ai = recordDados.ano_empenho;
            funcao = recordDados.funcao;
            subfuncao = recordDados.subfuncao;

            saldo_anterior = recordDados.saldo_final_anterior;
            saldo_debito = recordDados.saldo_debito;
            saldo_credito = recordDados.saldo_credito;
            saldo_final = recordDados.saldo_final;

            return next;
        end loop;
    end loop;
end;
$$;
SQL
        );
    }

    /**
     * Busca os valores dos atributos: 'PO', 'PO,FP', 'PO,FP,DC'
     */
    private function upValoresAtributoDc()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop function if exists contabilidade.valores_atributo_dc;
create or replace function contabilidade.valores_atributo_dc(f_reduz int, f_ano integer, f_dataInicio date, f_dataFim date, f_encerramento boolean)
returns table (
    reduzido int,
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
            select c62_reduz as reduzido,
                   case when c62_vlrcre != 0 then c62_vlrcre *-1
                        else c62_vlrdeb
                       end as saldo_inicial,
                   (f_ano || '-01-01')::date as data,
                   null::int as tipo_documento
            from contabilidade.conplanoexe
            where c62_anousu = f_ano
              and c62_reduz = f_reduz
        ), debitos as (
            select c69_debito as reduzido,
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
        ), creditos as (
            select c69_credito as reduzido,
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
        ), unifica as (
            select si.reduzido, si.data, si.saldo_inicial, 0 as debito, 0 as credito, tipo_documento
            from saldo_inicial si
            union all
            select d.reduzido, d.data, 0 as saldo_inicial, d.debito, 0 as credito, tipo_documento
            from debitos d
            union all
            select c.reduzido, c.data, 0 as saldo_inicial, 0 as debito, c.credito, tipo_documento
            from creditos c
        ), totaliza as (
            select x.reduzido,
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
                       else 0 end)::numeric(17, 2) as saldo_credito
            from unifica as x
            group by 1
        ) select * from totaliza
    ) loop
        reduzido = recordDados.reduzido;
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
SQL
        );
    }

    /**
     * Busca os valores dos atributos: 'PO,FP,DC,FR', 'PO,FP,FR,CO', 'PO,FR,CO'
     */
    private function upValoresAtributoRecurso()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop function if exists contabilidade.valores_atributo_recurso;
create or replace function contabilidade.valores_atributo_recurso(f_reduz int, f_ano integer, f_dataInicio date, f_dataFim date, f_encerramento boolean)
returns table (
    reduzido int,
    siconfi varchar(4),
    complemento int,
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
            select c143_conplanoreduz  as reduzido,
                   c143_exercicio as exercicio,
                   c144_valor::int as id_recurso,
                   coalesce((case WHEN c143_natureza = 'C' THEN c143_saldo * -1 else c143_saldo end), 0)::numeric(17, 2) as saldo_inicial,
                   (f_ano || '-01-01')::date as data,
                   null::int as tipo_documento
            from contabilidade.conplanoexecontacorrente
            join contabilidade.conplanoexecontacorrenteatributo on c144_conplanoexecontacorrente = conplanoexecontacorrente.id
            where c143_conplanoreduz = f_reduz
              and c143_exercicio = f_ano
              and c143_conplanosistema = 100
        ), debitos as (
            select c69_debito as reduzido,
                   c69_anousu as exercicio,
                   contabilidade.fc_recurso_conta_lancamento(c69_codlan, c69_debito, 'D') as id_recurso,
                   c69_valor as debito,
                   c69_data as data,
                   c53_tipo::int as tipo_documento
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
        ), creditos as (
            select c69_credito as reduzido,
                   c69_anousu as exercicio,
                   contabilidade.fc_recurso_conta_lancamento(c69_codlan, c69_credito, 'C') as id_recurso,
                   c69_valor *-1 as credito,
                   c69_data as data,
                   c53_tipo::int as tipo_documento
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
        ), unifica as (
            select si.reduzido, si.exercicio, si.id_recurso, si.data, si.saldo_inicial, 0 as debito, 0 as credito, tipo_documento
            from saldo_inicial si
            union all
            select d.reduzido, d.exercicio, d.id_recurso, d.data, 0 as saldo_inicial, debito, 0 as credito, tipo_documento
            from debitos d
            union all
            select c.reduzido, c.exercicio, c.id_recurso, c.data, 0 as saldo_inicial, 0 as debito, credito, tipo_documento
            from creditos c
        ), totaliza as (
            select x.reduzido,  fr.codigo_siconfi, o.o15_complemento,
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
            group by 1,2,3
        ) select * from totaliza
    ) loop
        reduzido = recordDados.reduzido;
        siconfi = recordDados.codigo_siconfi;
        complemento = recordDados.o15_complemento;
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
SQL
        );
    }

    /**
     * Busca os valores dos atributos da receita 'PO,FR,CO,NR'
     */
    private function upValoresAtributoReceita()
    {
        DB::connection()->getPdo()->exec(<<<SQL
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
        with debitos as (
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
            select d.reduzido, d.exercicio, d.id_recurso, conta, d.data, 0 as saldo_inicial, debito, 0 as credito, tipo_documento
            from debitos d
            union all
            select c.reduzido, c.exercicio, c.id_recurso, conta, c.data, 0 as saldo_inicial, 0 as debito, credito, tipo_documento
            from creditos c
        ), totaliza as (
            select x.reduzido,  fr.codigo_siconfi, o.o15_complemento, conta,
                   0 as saldo_inicial,
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
SQL
        );
    }

    /**
     * Busca os valores dos atributos da receita 'PO,FS,FR,CO,ND'
     */
    private function upValoresAtributoEmpenhoExercicio()
    {
        DB::connection()->getPdo()->exec(<<<SQL
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
        with debitos as (
            select c69_debito as reduzido,
                   c69_anousu as exercicio,
                   contabilidade.fc_recurso_conta_lancamento(c69_codlan, c69_debito, 'D') as id_recurso,
                   conta,
                   o58_funcao as funcao,
                   o58_subfuncao as subfuncao,
                   c69_valor as debito,
                   c69_data as data,
                   c53_tipo as tipo_documento
            from conlancamval
            join contabilidade.conlancamdoc on conlancamdoc.c71_codlan = conlancamval.c69_codlan
            join contabilidade.conhistdoc on conhistdoc.c53_coddoc = conlancamdoc.c71_coddoc
            join contabilidade.conlancamdot on c73_codlan = c69_codlan
            join orcamento.orcdotacao on (o58_anousu, o58_coddot) = (c73_anousu, c73_coddot)
            join contabilidade.conplanoorcamento on (c60_codcon, c60_anousu) = (o58_codele, o58_anousu)
            join contabilidade.planodespesaconplanoorcamento on conplanoorcamento_codigo = c60_codigo
            join contabilidade.planodespesa on planodespesa.id = planodespesaconplanoorcamento.planodespesa_id
            where c69_debito = f_reduz
              and c69_anousu = f_ano
              and c69_data <= f_dataFim
              and planodespesa.uniao is true
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
                   o58_funcao as funcao,
                   o58_subfuncao as subfuncao,
                   c69_valor *-1 as credito,
                   c69_data as data,
                   c53_tipo as tipo_documento
            from conlancamval
            join contabilidade.conlancamdoc on conlancamdoc.c71_codlan = conlancamval.c69_codlan
            join contabilidade.conhistdoc on conhistdoc.c53_coddoc = conlancamdoc.c71_coddoc
            join contabilidade.conlancamdot on c73_codlan = c69_codlan
            join orcamento.orcdotacao on (o58_anousu, o58_coddot) = (c73_anousu, c73_coddot)
            join contabilidade.conplanoorcamento on (c60_codcon, c60_anousu) = (o58_codele, o58_anousu)
            join contabilidade.planodespesaconplanoorcamento on conplanoorcamento_codigo = c60_codigo
            join contabilidade.planodespesa on planodespesa.id = planodespesaconplanoorcamento.planodespesa_id
            where c69_credito = f_reduz
              and c69_anousu = f_ano
              and c69_data <= f_dataFim
              and planodespesa.uniao is true
              and (case
                       when f_encerramento is true then true
                       when f_encerramento is false and c53_tipo not in (1000, 1500) then true
                       else false
                   end)
        ), unifica as (
            select d.reduzido, d.exercicio, d.id_recurso, conta, d.funcao, d.subfuncao, d.data, 0 as saldo_inicial, debito, 0 as credito, tipo_documento
            from debitos d
            union all
            select c.reduzido, c.exercicio, c.id_recurso, conta, c.funcao, c.subfuncao, c.data, 0 as saldo_inicial, 0 as debito, credito, tipo_documento
            from creditos c
        ), totaliza as (
            select x.reduzido,  fr.codigo_siconfi, o.o15_complemento, x.conta, x.funcao, x.subfuncao,
                   0 as saldo_inicial,
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
SQL
        );
    }

    /**
     * Busca os valores dos atributos da receita 'PO - FS - FR - CO - ND - AI'
     */
    private function upValoresAtributoEmpenhoRP()
    {
        DB::connection()->getPdo()->exec(<<<SQL
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
            join contabilidade.dados_auxiliar_empenho_matriz(c151_empenho, c151_exercicio, true) as e
                 on e.numemp = c151_empenho
            where c151_reduzido = f_reduz
              and c151_exercicio = f_ano
        ), debitos as (
            select c69_debito as reduzido,
                   c69_anousu as exercicio,
                   contabilidade.fc_recurso_conta_lancamento(c69_codlan, c69_debito, 'D') as id_recurso,
                   e.conta,
                   e.funcao,
                   e.subfuncao,
                   e.ano_empenho,
                   e.anousu_rp,
                   c69_valor as debito,
                   c69_data as data,
                   c53_tipo as tipo_documento
            from conlancamval
            join contabilidade.conlancamdoc on conlancamdoc.c71_codlan = conlancamval.c69_codlan
            join contabilidade.conhistdoc on conhistdoc.c53_coddoc = conlancamdoc.c71_coddoc
            join contabilidade.conlancamemp on c75_codlan = c69_codlan
            join contabilidade.dados_auxiliar_empenho_matriz(c75_numemp, c69_anousu, true) as e
                 on e.numemp = conlancamemp.c75_numemp
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
                   e.conta,
                   e.funcao,
                   e.subfuncao,
                   e.ano_empenho,
                   e.anousu_rp,
                   c69_valor *-1 as credito,
                   c69_data as data,
                   c53_tipo as tipo_documento
            from conlancamval
            join contabilidade.conlancamdoc on conlancamdoc.c71_codlan = conlancamval.c69_codlan
            join contabilidade.conhistdoc on conhistdoc.c53_coddoc = conlancamdoc.c71_coddoc
            join contabilidade.conlancamemp on c75_codlan = c69_codlan
            join contabilidade.dados_auxiliar_empenho_matriz(c75_numemp, c69_anousu, true) as e
                 on e.numemp = conlancamemp.c75_numemp
            where c69_credito = f_reduz
              and c69_anousu = f_ano
              and c69_data <= f_dataFim
              and (case
                      when f_encerramento is true then true
                      when f_encerramento is false and c53_tipo not in (1000, 1500) then true
                      else false
                   end)
        ), unifica as (
            select s.reduzido, s.exercicio, s.id_recurso, s.conta, s.funcao, s.subfuncao, s.ano_empenho, s.anousu_rp, s.data, s.saldo_inicial, 0 as debito, 0 as credito, tipo_documento
            from saldo_inicial s
            union all
            select d.reduzido, d.exercicio, d.id_recurso, conta, d.funcao, d.subfuncao, d.ano_empenho, d.anousu_rp, d.data, 0 as saldo_inicial, debito, 0 as credito, tipo_documento
            from debitos d
            union all
            select c.reduzido, c.exercicio, c.id_recurso, conta, c.funcao, c.subfuncao, c.ano_empenho, c.anousu_rp, c.data, 0 as saldo_inicial, 0 as debito, credito, tipo_documento
            from creditos c
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
SQL
        );
    }

    private function upDadosAuxiliarEmpenho()
    {
        DB::connection()->getPdo()->exec(<<<SQL

drop function if exists contabilidade.dados_auxiliar_empenho_matriz;
create or replace function contabilidade.dados_auxiliar_empenho_matriz(f_empenho int, f_ano integer, f_rp boolean)
returns table (
    numemp integer,
    codemp varchar,
    ano_empenho integer,
    anousu_rp integer,
    id_recurso integer,
    conta varchar,
    funcao varchar,
    subfuncao varchar
) language plpgsql
as $$
declare
    recordDados record;
begin

    for recordDados in (
        select e60_numemp, e60_codemp, e60_anousu, e91_anousu,
               o206_recurso as id_recurso,
               planodespesa.conta,
               o58_funcao as funcao,
               o58_subfuncao as subfuncao
        from empenho.empempenho
                 left join empenho.empresto on empresto.e91_numemp = e60_numemp and e91_anousu = f_ano
                 join orcamento.orcdotacao on (o58_anousu, o58_coddot) = (e60_anousu, e60_coddot)
                 join contabilidade.conplanoorcamento on c60_codcon = o58_codele
            and c60_anousu = (case when o58_anousu < 2023 then 2023 else o58_anousu end)
                 join contabilidade.planodespesaconplanoorcamento on conplanoorcamento_codigo = c60_codigo
                 join contabilidade.planodespesa on planodespesa.id = planodespesaconplanoorcamento.planodespesa_id
                 join orcamento.origemcomplementorecurso on o206_numero = e60_numemp
            and o206_origem = (case when e91_numemp is null then 1 else 10 end)
        where uniao is true
          and e60_numemp = f_empenho
          and (case when f_rp is true then e91_anousu = f_ano else e60_anousu = f_ano end)
    ) loop
            numemp = recordDados.e60_numemp;
            codemp = recordDados.e60_codemp;
            ano_empenho = recordDados.e60_anousu;
            anousu_rp = recordDados.e91_anousu;
            id_recurso = recordDados.id_recurso;
            conta = recordDados.conta;
            funcao = recordDados.funcao;
            subfuncao = recordDados.subfuncao;
            return next ;
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
        DB::connection()->getPdo()->exec(<<<SQL
drop function if exists contabilidade.dados_auxiliar_empenho_matriz;
drop function if exists contabilidade.valores_atributo_empenho_rp;
drop function if exists contabilidade.valores_atributo_empenho_exercicio;
drop function if exists contabilidade.valores_atributo_receita;
drop function if exists contabilidade.valores_atributo_recurso;
drop function if exists contabilidade.valores_atributo_dc;
drop function if exists contabilidade.matriz;;
SQL
        );
    }
}
