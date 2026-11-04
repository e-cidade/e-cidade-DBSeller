<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29769TriggerEmpresto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
create or replace function fc_conlancamemp_inc_alt_del() returns trigger as
$$
DECLARE

    iAnousu        integer;
    iNumemp        integer;
    iNivel         integer;
    iTipoRP        integer default 999;
    cEstrutural    varchar;
    cEstruturalOri varchar;
    cEstruturalNew text;
    nSaldoProc     numeric(15, 2) default 0;
    nSaldoNProc    numeric(15, 2) default 0;
    sNomeTabela    varchar;
    sOperacao      varchar;
    record_tiporp  record;
    aEstrutural    text[];

begin

    sNomeTabela := lower(TG_RELNAME);
    sOperacao := upper(TG_OP);

    iAnousu = fc_getsession('DB_anousu');
    if iAnousu is null then
        raise exception 'Variavel de sessao [DB_anousu] nao declarada!';
    end if;

    if sOperacao = 'UPDATE' or sOperacao = 'INSERT' then
        iNumemp = new.c75_numemp;
    else
        iNumemp = old.c75_numemp;
    end if;

    perform c30_anodestino
    from db_virada
             inner join db_viradaitem on c31_db_virada = c30_sequencial
    where c30_anodestino = 2025
      and c31_db_viradacaditem = 13
      and c31_situacao = 1;

    if found then
        create table w_empresto_virada_new as

        select iAnousu + 1                                                                                            as e91_anousu,
               e60_numemp,
               e64_codele,
               c60_estrut,
               round(vlremp, 2)::float8                                                                               as e60_vlremp,
               round(coalesce(vlranu, 0), 2)::float8                                                                  as e60_vlranu,
               round(coalesce(vlrliq, 0), 2)::float8                                                                  as e60_vlrliq,
               round(coalesce(vlrpag, 0), 2)::float8                                                                  as e60_vlrpag,
               (select o206_recurso
                from origemcomplementorecurso
                where o206_numero = e60_numemp
                  and o206_origem = 1)                                                                                as o58_codigo,
               e60_instit
        from empempenho
                 inner join orcdotacao on o58_anousu = e60_anousu and o58_coddot = e60_coddot
                 inner join empelemento on e60_numemp = e64_numemp
                 inner join conplanoorcamento on c60_codcon = e64_codele and c60_anousu = e60_anousu
                 inner join (select c75_numemp,
                                    round(sum(case when c53_tipo in (10) then round(c70_valor, 2) end), 2)     as vlremp,
                                    round(sum(case when c53_tipo in (11) then round(c70_valor, 2) end),
                                          2)                                                                   as vlranu,
                                    round(sum(case
                                                  when c53_tipo in (20) then round(c70_valor, 2)
                                                  when c53_tipo in (21) then round(c70_valor, 2) * -1 end),
                                          2)                                                                   as vlrliq,
                                    round(sum(case
                                                  when c53_tipo in (30) then round(c70_valor, 2)
                                                  when c53_tipo in (31) then round(c70_valor, 2) * -1 end), 2) as vlrpag
                             from conlancamemp
                                      inner join conlancam on c70_codlan = c75_codlan
                                      inner join conlancamdoc on c70_codlan = c71_codlan
                                      inner join conhistdoc on c53_coddoc = c71_coddoc
                             where c53_tipo in (10, 11, 20, 21, 30, 31)
                               and c70_anousu = iAnousu
                               and c71_data <= (iAnousu::varchar || '-12-31')::date
                             group by c75_numemp) as vlremp on vlremp.c75_numemp = e60_numemp
        where e60_numemp = iNumemp;
        --             round(round(round(vlremp,2) - round(coalesce(vlranu,0),2),2)::float8 - round(coalesce(vlrliq,0),2)::float8,2) > 0
--	     and
--             round(round(round(vlremp,2) - round(coalesce(vlranu,0),2),2)::float8 - round(coalesce(vlrpag,0),2)::float8,2) > 0
--            )

        perform * from w_empresto_virada_new;

        if found then

            select round(e60_vlremp - e60_vlranu - e60_vlrliq, 2)::numeric(15, 2)
            into nSaldoProc
            from w_empresto_virada_new;

            select round(e60_vlrliq - e60_vlrpag, 2)::numeric(15, 2)
            into nSaldoNProc
            from w_empresto_virada_new;

            if nSaldoProc > 0 or nSaldoNProc > 0 then

                perform e91_numemp from empresto where e91_numemp = iNumemp and e91_anousu = iAnousu + 1;

                if found then

                    update empresto
                    set e91_vlremp = w_empresto_virada_new.e60_vlremp,
                        e91_vlranu = w_empresto_virada_new.e60_vlranu,
                        e91_vlrliq = w_empresto_virada_new.e60_vlrliq,
                        e91_vlrpag = w_empresto_virada_new.e60_vlrpag
                    from w_empresto_virada_new
                    where empresto.e91_numemp = w_empresto_virada_new.e60_numemp
                      and empresto.e91_anousu = w_empresto_virada_new.e91_anousu;

                else

                    select fc_estruturaldespesa(c60_estrut) from w_empresto_virada_new into cEstrutural;
                    select fc_estruturaldespesa(c60_estrut) from w_empresto_virada_new into cEstruturalOri;

                    select fc_estrutural_nivel(cEstrutural)
                    into iNivel;

                    --    	      raise notice 'estrutural ori: % - nivel: %', cEstruturalOri, iNivel;
--    	      raise notice '';

                    while iNivel > 2
                        loop

                            aEstrutural = string_to_array(cEstruturalOri::text, '.');

                            for record_tiporp in
                                select e90_codigo, e90_estrut
                                from emprestotipo
                                where e90_estrut != ''
                                order by length(e90_estrut) desc
                                loop

                                    cEstruturalNew = '';

                                    for iContador in 1..iNivel
                                        loop
                                            cEstruturalNew = cEstruturalNew || aEstrutural[iContador];
--                   raise notice 'iContador: % - % - cEstruturalNew: % - cEstruturalNew: % - e90_estrut: % - nivel: %', iContador, aEstrutural[iContador], cEstrutural, cEstruturalNew, record_tiporp.e90_estrut, iNivel;
                                        end loop;

                                    if record_tiporp.e90_estrut = cEstruturalNew then
                                        iTipoRP = record_tiporp.e90_codigo;
                                    end if;

--                  raise notice '';

                                end loop;

                            select fc_estrutural_pai(cEstrutural) into cEstrutural;
                            select fc_estrutural_nivel(cEstrutural) into iNivel;
--                raise notice '';

                        end loop;

--	      raise notice 'estrutural: % - iTipoRP: %', cEstruturalOri, iTipoRP;

                    insert into empresto
                    (e91_anousu,
                     e91_numemp,
                     e91_vlremp,
                     e91_vlranu,
                     e91_vlrliq,
                     e91_vlrpag,
                     e91_elemento,
                     e91_recurso,
                     e91_codtipo,
                     e91_rpcorreto)
                    select iAnousu + 1,
                           e60_numemp,
                           e60_vlremp,
                           e60_vlranu,
                           e60_vlrliq,
                           e60_vlrpag,
                           c60_estrut,
                           o58_codigo,
                           iTipoRP,
                           'false'
                    from w_empresto_virada_new;

                end if;

            elsif nSaldoProc < 0 or nSaldoNProc < 0 then
                raise exception 'Saldo do empenho inconsistente: saldo processado: % - saldo nao processado: %', nSaldoProc, nSaldoNProc;
            else

                delete
                from empresto
                where empresto.e91_numemp = iNumemp
                  and empresto.e91_anousu = iAnousu + 1;

            end if;

        else

            perform e91_numemp from empresto where e91_numemp = iNumemp and e91_anousu = iAnousu + 1;

            if found then
                delete
                from empresto
                where empresto.e91_numemp = iNumemp
                  and empresto.e91_anousu = iAnousu + 1;
            end if;

        end if;

    end if;

    drop table w_empresto_virada_new;

    if sOperacao = 'UPDATE' or sOperacao = 'INSERT' then
        return new;
    else
        return old;
    end if;

end;
$$ language 'plpgsql';
SQL
        );

        DB::unprepared(<<<SQL

drop trigger if exists tg_conlancamemp_inc_alt_del on contabilidade.conlancamemp;
create trigger tg_conlancamemp_inc_alt_del
    after insert or update or delete
    on contabilidade.conlancamemp
    for each row
execute procedure fc_conlancamemp_inc_alt_del();
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
        DB::unprepared(<<<SQL
drop trigger tg_conlancamemp_inc_alt_del on conlancamemp;
drop function fc_conlancamemp_inc_alt_del;
SQL
        );
    }
}
