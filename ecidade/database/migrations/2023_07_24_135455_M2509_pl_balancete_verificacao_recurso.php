<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M2509PlBalanceteVerificacaoRecurso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upIndex();

        DB::connection()->getPdo()->exec(<<<SQL
drop function if exists contabilidade.balancete_verificacao_por_recurso(int, date, date, boolean);

--
-- Atencao, principios que devemos entender ao trabalhar com essa pl
--
-- - Todo o valor a Credito sera representado negativo.
-- - Todo o valor a Debito esta positivo.
-- Isso foi feito para simplificar os calculos. No final realizamos as validacoes setando o sinal de cada conta
--
create or replace function contabilidade.balancete_verificacao_por_recurso(
    f_ano integer,
    f_dataInicio date,
    f_dataFim date,
    f_encerramento boolean
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
                   c60_identificadorfinanceiro
              from contabilidade.conplano
              join contabilidade.conplanoreduz on (c61_codcon, c61_anousu) = (c60_codcon, c60_anousu)
             where c60_anousu = f_ano
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
                    sum(si.saldo) as saldo
            from saldo_inicial si
            group by si.estrutural, si.classe, si.nome, si.codigo_conplano, si.instituicao, si.reduzido,
                     si.id_recurso, si.siconfi, si.gestao, si.subrecurso, si.complemento, si.principal,
                     si.c60_consistemaconta, si.c60_identificadorfinanceiro
        ), lancamentos_exercicio as (
           select c70_codlan
           from contabilidade.conlancam
           join contabilidade.conlancamdoc on conlancamdoc.c71_codlan = conlancam.c70_codlan
           where c70_anousu = f_ano
             and (case
                   when f_encerramento is false and c71_coddoc not in (1024, 1025, 1026, 2030, 2031, 1011, 1012, 1013, 1014, 1015, 1016, 1017, 1018, 1009, 1010, 1020, 1021, 1019, 1022, 1023)
                     then true
                   else true
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
                      lancamentos.principal, lancamentos.c60_consistemaconta, lancamentos.c60_identificadorfinanceiro
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
                     x.c60_consistemaconta, x.c60_identificadorfinanceiro
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

--
-- definimos a natureza em que a conta se encontra com base na natureza padrao da conta e o valor informado
-- No contexto dessa PL o valor informado encontra-se negativo quando origem do saldo esta CREDORA
--
drop function if exists contabilidade.fc_sinal_saldo_conta(int, numeric);

create or replace function contabilidade.fc_sinal_saldo_conta(classe int, valor numeric)
    returns char
    language plpgsql
as $$
begin
    -- a natureza das contas de classe 1,3,5,7 e D.
    if classe in (1,3,5,7) then
        if ( valor < 0) then
            return 'C';
        else return 'D';
        end if;
    end if;

    -- a natureza das contas de classe 2,4,6,8 e C.
    if classe in (2,4,6,8) then
        if ( valor < 0) then
            return 'D';
        else return 'C';
        end if;
    end if;

end;
$$;

drop function if exists contabilidade.fc_recurso_conta_lancamento(int, int, char);

create or replace function contabilidade.fc_recurso_conta_lancamento(lancamento int, conta int, natureza char(1) )
    returns int
    language plpgsql
as $$
declare
    id_recurso int;
begin

    select
        case
            when o201_orctiporec is not null
                 and c53_tipo in (10, 11, 20, 21, 30, 31, 40, 41, 50, 51, 60, 61, 70, 71, 90, 91, 92, 100, 101, 110, 111, 112, 113, 200, 201, 414, 415, 900, 901, 1000, 1500, 2000, 2001)
                then o201_orctiporec
            else c130_orctiporec
            end as codigo_recurso
    into id_recurso
    from conlancam
             join conlancamdoc on conlancamdoc.c71_codlan = c70_codlan
             join conhistdoc on conhistdoc.c53_coddoc = conlancamdoc.c71_coddoc
             join contabilidade.conlancamrecurso on c130_conlancam = c70_codlan
        and c130_conta = conta
        and c130_natureza = natureza
             left join conlancamcomplementorecurso on o201_codlan = c70_codlan
    where c70_codlan = lancamento;

    return id_recurso;
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
        $this->downIndex();
    }

    /**
     * @return void
     */
    public function upIndex()
    {
        $this->downIndex();
        DB::connection()->getPdo()->exec(<<<SQL
create index conplanoexecontacorrenteatributo_atributo_in on contabilidade.conplanoexecontacorrenteatributo (c144_valor);
create index conplanoatributolancamentos_data_in on contabilidade.conplanoatributolancamentos (c124_data);
create index conplanoexecontacorrenteatributo_conplanoexecontacorrente_in on conplanoexecontacorrenteatributo (c144_conplanoexecontacorrente)
SQL
        );
    }

    public function downIndex()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop index if exists conplanoexecontacorrenteatributo_atributo_in;
drop index if exists conplanoatributolancamentos_data_in;
drop index if exists conplanoexecontacorrenteatributo_conplanoexecontacorrente_in;
SQL
        );
    }
}
