<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M27392AdicionaCampoGerafinanceiroCalculoiptutaxa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
-- DROP FUNCTION public.fc_calculoiptutaxa(int4, int4);

CREATE OR REPLACE FUNCTION public.fc_calculoiptutaxa(matricula integer, ano integer)
 RETURNS integer
 LANGUAGE plpgsql
AS \$function$

declare

    iptu_ou_taxa_quitado   boolean default false;
    iptu_ou_taxa_isencao   boolean default false;
    iptu_ou_taxa_cancelado boolean default false;
    taxa_receita_diferente boolean default false;
    lRaise                 boolean default false;
    lCriouDiversos         boolean default false;

    taxa_valor_anterior    numeric default 0;
    taxa_valor_agora       numeric default 0;

    iptucalv_record        record;

    numpre                 integer default null;
    iptutaxanump_codigo    integer default null;
    iptutaxacalv_codigo    integer default null;

begin

    lRaise         := ( case when fc_getsession('DB_debugon') is null then false else true end );
    lCriouDiversos := ( case when fc_getsession('DB_criou_diversos') is null then false else true end );

    if lCriouDiversos = true then
       return 1;
    end if;

    /**
     * +------+------------------+------------------+---------------------------------------------------------------------------------+
     * | CASO |       IPTU       |       TAXA       |            PROCEDIMENTO                                                         |
     * +------+------------------+------------------+---------------------------------------------------------------------------------+
     * |  01  | Igual            | Igual            | Delete em arrecad de receita de taxa / delete de taxa em estrutura de iptucalv  |
     * |  02  | Igual            | Diferente        | Update/Insert em estrutura de taxa e update/insert em arrecad de taxa           |
     * |  03  | Diferente        | Igual            | Delete em arrecad de receita de taxa / delete de taxa em estrutura de iptucalv  |
     * |  04  | Diferente        | Diferente        | Update/Insert em estrutura de taxa e update/insert em arrecad de taxa           |
     * |  05  | Quitado          | Quitado          | Sistema nao da suporte                                                          |
     * |  06  | Cancelado        | Cancelado        | Sistema nao da suporte                                                          |
     * |  07  | Isencao          | ---              | Sistema nao da suporte                                                          |
     * +------+------------------+------------------+---------------------------------------------------------------------------------+
     */

    /**
     *      Tabela de retornos
     * +------+-----------------------------+
     * | CASO |            SIGNIFICADO      |
     * +------+-----------------------------+
     * |   1  | Sucesso                     |
     * |   2  | Quitado                     |
     * |   3  | Isencao                     |
     * |   4  | Cancelado                   |
     * +------+-----------------------------+
     */

    perform fc_debug(' <calculoiptutaxa> Matricula: ' || matricula, lRaise);

    /**
     * 05 - Quitado
     */
    select true
      into iptu_ou_taxa_quitado
      from (
            select true
              from iptucalv
                   inner join iptunump on iptunump.j20_matric = iptucalv.j21_matric
             where iptucalv.j21_matric = matricula
               and iptunump.j20_anousu = ano
               and not exists (select *
                                 from arrecad
                                where arrecad.k00_numpre = iptunump.j20_numpre)
             union
            select true
              from iptutaxanump
                   inner join iptucadtaxaexe on iptucadtaxaexe.j08_iptucadtaxaexe = iptutaxanump.j151_iptucadtaxaexe
             where iptutaxanump.j151_matric = matricula
               and iptucadtaxaexe.j08_anousu = ano
               and not exists (select *
                                 from arrecad
                                where arrecad.k00_numpre = iptutaxanump.j151_numpre)
      ) as a;

    if iptu_ou_taxa_quitado then

        perform fc_debug(' <calculoiptutaxa> QUITADO', lRaise);
        return 2;
    end if;

    /**
     * 07 - Isencao
     */
    select x.isenta_taxas
      into iptu_ou_taxa_isencao
      from fc_iptu_verifica_isencao_competencia(matricula, ano, false) as x
     where x.isenta_taxas is true;

    if iptu_ou_taxa_isencao then

        perform fc_debug(' <calculoiptutaxa> ISENCAO', lRaise);
        return 3;
    end if;

    /**
     * 06 - Cancelado
     */
    select true
      into iptu_ou_taxa_cancelado
      from (
            select true
              from iptucalv
                   inner join iptunump on iptunump.j20_matric = iptucalv.j21_matric
                   inner join cancdebitosreg on cancdebitosreg.k21_numpre = iptunump.j20_numpre
                   inner join cancdebitos on cancdebitos.k20_codigo = cancdebitosreg.k21_codigo
             where iptucalv.j21_matric = matricula
               and iptunump.j20_anousu = ano
             union
            select true
              from iptutaxanump
                   inner join iptucadtaxaexe on iptucadtaxaexe.j08_iptucadtaxaexe = iptutaxanump.j151_iptucadtaxaexe
                   inner join cancdebitosreg on cancdebitosreg.k21_numpre = iptutaxanump.j151_numpre
                   inner join cancdebitos on cancdebitos.k20_codigo = cancdebitosreg.k21_codigo
             where iptutaxanump.j151_matric = matricula
               and iptucadtaxaexe.j08_anousu = ano
      ) as a;

    if iptu_ou_taxa_cancelado then
       perform fc_debug(' <calculoiptutaxa> CANCELADO', lRaise);
       return 4;
    end if;

    /**
     * Procedimento para carregamento de valor calculado anterior para taxa
     */
    select coalesce(sum(iptutaxacalv.j152_valor),0)
      into taxa_valor_anterior
      from iptutaxanump
           inner join iptucadtaxaexe on iptucadtaxaexe.j08_iptucadtaxaexe = iptutaxanump.j151_iptucadtaxaexe
           inner join iptutaxacalv on iptutaxacalv.j152_iptutaxanump = iptutaxanump.j151_codigo
     where iptutaxanump.j151_matric = matricula
       and iptucadtaxaexe.j08_anousu = ano;

    perform fc_debug(' <calculoiptutaxa> TAXA VALOR ANTERIOR: ' || taxa_valor_anterior, lRaise);

    /**
     * Procedimento para carregamento de valor calculado atualmente para taxa
     */
    select coalesce(sum(iptucalv.j21_valor),0)
      into taxa_valor_agora
      from iptucalv
     where iptucalv.j21_matric = matricula
       and iptucalv.j21_anousu = ano
       and iptucalv.j21_receit in (select j08_tabrec
                                     from iptucadtaxaexe
                                    where iptucadtaxaexe.j08_anousu = ano);

    perform fc_debug(' <calculoiptutaxa> TAXA VALOR AGORA: ' || taxa_valor_agora, lRaise);

    /**
    * 01 - Iptu igual && Taxa igual | 03 - Iptu Diferente && Taxa igual
    */
    if taxa_valor_anterior = taxa_valor_agora then

       perform fc_debug(' <calculoiptutaxa> 01 - Taxa igual', lRaise);

       perform fc_debug(' <calculoiptutaxa> TOTAL ARRECAD: '||(
            select sum(arrecad.k00_valor)
              from arrecad
             where arrecad.k00_numpre = (select j20_numpre
                                           from iptunump
                                          where j20_matric = matricula
                                            and j20_anousu = ano)), lRaise);

        perform fc_debug(' <calculoiptutaxa> TOTAL IPTUCALV: '||(select sum(j21_valor) from iptucalv where j21_matric = matricula and j21_anousu = ano), lRaise);

        perform fc_debug(' <calculoiptutaxa> DELETE ARRECAD DE RECEITA DE TAXA EM NUMPRE DE IPTU', lRaise);

        delete from arrecad
              where arrecad.k00_numpre = (select j20_numpre from iptunump where j20_matric = matricula and j20_anousu = ano)
                and arrecad.k00_receit in (select j08_tabrec from iptucadtaxaexe where j08_anousu = ano);

        perform fc_debug(' <calculoiptutaxa> DELETE IPTUCALV DE RECEITA DE TAXA EM IPTU', lRaise);

        delete from iptucalv
              where iptucalv.j21_receit in (select j08_tabrec from iptucadtaxaexe where j08_anousu = ano);

        perform fc_debug(' <calculoiptutaxa> TOTAL ARRECAD DEPOIS: '||(
            select sum(arrecad.k00_valor)
              from arrecad
             where arrecad.k00_numpre = (select j20_numpre
                                           from iptunump
                                          where j20_matric = matricula
                                            and j20_anousu = ano)), lRaise);

        perform fc_debug(' <calculoiptutaxa> TOTAL IPTUCALV: '||(select sum(j21_valor) from iptucalv where j21_matric = matricula and j21_anousu = ano), lRaise);

        return 1;

    end if;

    /**
    * 02 - Iptu igual && Taxa Diferente | 04 - Iptu Diferente && Taxa Diferente
    */
    if taxa_valor_anterior <> taxa_valor_agora then

        perform fc_debug(' <calculoiptutaxa> taxa_valor_anterior <> taxa_valor_agora', lRaise);

        /**
         * Antes não tinha taxa e agora tem.
         * Deve ser lancado estrutura de taxa e arrecad.
         * INSERT
         */
        if taxa_valor_anterior = 0 and taxa_valor_agora > 0 then

            perform fc_debug(' <calculoiptutaxa> taxa_valor_anterior = 0 and taxa_valor_agora > 0 -> INSERT', lRaise);

            for iptucalv_record in

                select iptucalv.*,
                       iptunump.*,
                       iptucadtaxaexe.*
                  from iptucalv
                       inner join iptucadtaxaexe on iptucadtaxaexe.j08_tabrec = iptucalv.j21_receit
                                                and iptucadtaxaexe.j08_anousu = iptucalv.j21_anousu
                       inner join iptunump on iptunump.j20_matric = iptucalv.j21_matric
                                          and iptunump.j20_anousu = iptucalv.j21_anousu
                 where iptucalv.j21_matric = matricula
                   and iptucalv.j21_anousu = ano

            loop

                select nextval('numpref_k03_numpre_seq') into numpre;

                perform fc_debug(' <calculoiptutaxa> numpre novo -> '||numpre, lRaise);
                perform fc_debug(' <calculoiptutaxa> numpre iptu -> '||iptucalv_record.j20_numpre, lRaise);
                perform fc_debug(' <calculoiptutaxa> receita -> '    ||iptucalv_record.j21_receit, lRaise);

                insert into arrecad
                  select numpre,
                     k00_numpar,
                     k00_numcgm,
                     k00_dtoper,
                     k00_receit,
                     k00_hist,
                     k00_valor,
                     k00_dtvenc,
                     k00_numtot,
                     k00_numdig,
                     k00_tipo,
                     k00_tipojm
                   from  arrecad
                  where k00_numpre = iptucalv_record.j20_numpre
                   and k00_receit = iptucalv_record.j21_receit;

                delete from arrecad where k00_numpre = iptucalv_record.j20_numpre and k00_receit = iptucalv_record.j21_receit;

                insert into arrematric (
                  k00_numpre,
                  k00_matric,
                  k00_perc
                )
                select numpre,
                       k00_matric,
                       k00_perc
                  from arrematric
                 where arrematric.k00_numpre = iptucalv_record.j20_numpre;

                perform fc_debug(' <calculoiptutaxa> inserido numpre '    ||iptucalv_record.j20_numpre|| ' na tabela arrematric', lRaise);

                select nextval('iptutaxanump_j151_codigo_seq') into iptutaxanump_codigo;

                insert into iptutaxanump (
                    j151_codigo,
                    j151_matric,
                    j151_numpre,
                    j151_iptucadtaxaexe
                ) values (
                    iptutaxanump_codigo,
                    iptucalv_record.j21_matric,
                    numpre,
                    iptucalv_record.j08_iptucadtaxaexe
                );

                select nextval('iptutaxacalv_j152_codigo_seq') into iptutaxacalv_codigo;

                insert into iptutaxacalv (
                    j152_codigo,
                    j152_iptutaxanump,
                    j152_codhis,
                    j152_receit,
                    j152_valor,
                    j152_quant
                ) values (
                    iptutaxacalv_codigo,
                    iptutaxanump_codigo,
                    iptucalv_record.j21_codhis,
                    iptucalv_record.j21_receit,
                    iptucalv_record.j21_valor,
                    iptucalv_record.j21_quant
                );

                delete from iptucalv
                 where j21_matric = iptucalv_record.j21_matric
                   and j21_anousu = iptucalv_record.j21_anousu
                   and j21_receit = iptucalv_record.j21_receit;

            end loop;

            return 1;

        end if;

        /**
         * Valor da taxa anterior existia e agora existe tbm so que diferente.
         * Nesse caso deve ser deletado os dados de taxa do numpre de iptu
         * E atualizado a estrutura e numpre da taxa
         * UPDATE
         */
        if taxa_valor_anterior > 0 and taxa_valor_agora > 0 then

            perform fc_debug(' <calculoiptutaxa> taxa_valor_anterior > 0 and taxa_valor_agora > 0 -> UPDATE', lRaise);

            /**
             * Procedimento para verificar receitas diferentes de taxa de antes e de agora.
             */
            select true
              into taxa_receita_diferente
              from iptucalv
             where iptucalv.j21_matric = matricula
               and iptucalv.j21_anousu = ano
               and iptucalv.j21_receit in (select j08_tabrec
                                             from iptucadtaxaexe
                                            where j08_anousu = ano)
               and iptucalv.j21_receit not in (select iptutaxacalv.j152_receit
                                                 from iptutaxacalv
                                                      inner join iptutaxanump on iptutaxanump.j151_codigo = iptutaxacalv.j152_iptutaxanump
                                                      inner join iptucadtaxaexe on iptucadtaxaexe.j08_iptucadtaxaexe = iptutaxanump.j151_iptucadtaxaexe
                                                where iptutaxanump.j151_matric = matricula
                                                  and iptucadtaxaexe.j08_anousu = ano
                                              );

            if taxa_receita_diferente then

                perform fc_debug(' <calculoiptutaxa> TAXA RECEITAS DIFERENTES', lRaise);
                return false;
            end if;

            for iptucalv_record in

                select iptucalv.*
                  from iptucalv
                       inner join iptucadtaxaexe on iptucadtaxaexe.j08_tabrec = iptucalv.j21_receit
                                                and iptucadtaxaexe.j08_anousu = ano
                 where iptucalv.j21_matric = matricula
                   and iptucalv.j21_anousu = ano

            loop

                perform fc_debug(' <calculoiptutaxa> dados tabela iptucalv ->>> % '||iptucalv_record, lRaise);
                -- Update em estrutura de taxa com o valor do calculo
                update iptutaxacalv
                   set j152_valor = iptucalv_record.j21_valor
                 where iptutaxacalv.j152_receit = iptucalv_record.j21_receit
                   and iptutaxacalv.j152_iptutaxanump = (
                       select iptutaxanump.j151_codigo
                         from iptutaxanump
                              inner join iptucadtaxaexe on iptucadtaxaexe.j08_iptucadtaxaexe = iptutaxanump.j151_iptucadtaxaexe
                                                       and iptucadtaxaexe.j08_anousu = iptucalv_record.j21_anousu
                                                       and iptucadtaxaexe.j08_tabrec = iptucalv_record.j21_receit
                        where iptutaxanump.j151_matric = iptucalv_record.j21_matric
                   );

                -- update arrecad de taxa
                update arrecad set k00_valor = ((
                    select iptutaxacalv.j152_valor
                      from iptutaxacalv
                     where iptutaxacalv.j152_receit = iptucalv_record.j21_receit
                       and iptutaxacalv.j152_iptutaxanump = (
                           select iptutaxanump.j151_codigo
                               from iptutaxanump
                                   inner join iptucadtaxaexe on iptucadtaxaexe.j08_iptucadtaxaexe = iptutaxanump.j151_iptucadtaxaexe
                                                           and iptucadtaxaexe.j08_anousu = iptucalv_record.j21_anousu
                                                           and iptucadtaxaexe.j08_tabrec = iptucalv_record.j21_receit
                               where iptutaxanump.j151_matric = iptucalv_record.j21_matric)
                ) / k00_numtot)::numeric(15, 2)
                 where k00_numpre = (
                     select iptutaxanump.j151_numpre
                       from iptutaxanump
                            inner join iptucadtaxaexe on iptucadtaxaexe.j08_iptucadtaxaexe = iptutaxanump.j151_iptucadtaxaexe
                                                     and iptucadtaxaexe.j08_anousu = iptucalv_record.j21_anousu
                                                     and iptucadtaxaexe.j08_tabrec = iptucalv_record.j21_receit
                      where iptutaxanump.j151_matric = iptucalv_record.j21_matric
                 );

                -- arredonda valor do arrecad
                update arrecad set k00_valor = k00_valor + ((
                    ((
                    select iptutaxacalv.j152_valor
                      from iptutaxacalv
                     where iptutaxacalv.j152_receit = iptucalv_record.j21_receit
                       and iptutaxacalv.j152_iptutaxanump = (
                           select iptutaxanump.j151_codigo
                               from iptutaxanump
                                   inner join iptucadtaxaexe on iptucadtaxaexe.j08_iptucadtaxaexe = iptutaxanump.j151_iptucadtaxaexe
                                                           and iptucadtaxaexe.j08_anousu = iptucalv_record.j21_anousu
                                                           and iptucadtaxaexe.j08_tabrec = iptucalv_record.j21_receit
                               where iptutaxanump.j151_matric = iptucalv_record.j21_matric)
                )/k00_numtot) -
                    ((
                    select iptutaxacalv.j152_valor
                      from iptutaxacalv
                     where iptutaxacalv.j152_receit = iptucalv_record.j21_receit
                       and iptutaxacalv.j152_iptutaxanump = (
                           select iptutaxanump.j151_codigo
                               from iptutaxanump
                                   inner join iptucadtaxaexe on iptucadtaxaexe.j08_iptucadtaxaexe = iptutaxanump.j151_iptucadtaxaexe
                                                           and iptucadtaxaexe.j08_anousu = iptucalv_record.j21_anousu
                                                           and iptucadtaxaexe.j08_tabrec = iptucalv_record.j21_receit
                               where iptutaxanump.j151_matric = iptucalv_record.j21_matric)
                )/k00_numtot)::numeric(15, 2)
                ) * k00_numtot)::numeric(15, 2)
                where k00_numpre = (
                     select iptutaxanump.j151_numpre
                       from iptutaxanump
                            inner join iptucadtaxaexe on iptucadtaxaexe.j08_iptucadtaxaexe = iptutaxanump.j151_iptucadtaxaexe
                                                     and iptucadtaxaexe.j08_anousu = iptucalv_record.j21_anousu
                                                     and iptucadtaxaexe.j08_tabrec = iptucalv_record.j21_receit
                      where iptutaxanump.j151_matric = iptucalv_record.j21_matric
                ) and k00_numpar = k00_numtot;

                -- delete receita de taxa de numpre do iptu
                delete from arrecad
                 where k00_receit = iptucalv_record.j21_receit
                   and k00_numpre = (
                       select iptunump.j20_numpre
                         from iptunump
                        where iptunump.j20_matric = iptucalv_record.j21_matric
                          and iptunump.j20_anousu = iptucalv_record.j21_anousu
                   );

                -- delete de registros de historico de calculo da taxa em estrutura de iptu
                delete from iptucalv
                 where j21_anousu = iptucalv_record.j21_anousu
                   and j21_matric = iptucalv_record.j21_matric
                   and j21_receit = iptucalv_record.j21_receit;

            end loop;

            return 1;

        end if;

        /**
         * Valor novo calculado eh igual a 0. Não damos suporte ao caso, sistema tem q gerar credito.
         */
        if taxa_valor_anterior > 0 and taxa_valor_agora = 0 then

            perform fc_debug(' <calculoiptutaxa> taxa_valor_anterior > 0 and taxa_valor_agora = 0', lRaise);
            perform fc_debug(' <calculoiptutaxa> TAXA NAO EXISTE MAIS', lRaise);
            return 5;
        end if;

    end if;

    perform fc_debug(' <calculoiptutaxa> nao entrou nessa condicao --> taxa_valor_anterior: '||taxa_valor_anterior||' <> taxa_valor_agora: '||taxa_valor_agora, lRaise);
    return 5;

end;
\$function$
;

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
-- DROP FUNCTION public.fc_calculoiptutaxa(int4, int4);

CREATE OR REPLACE FUNCTION public.fc_calculoiptutaxa(matricula integer, ano integer)
 RETURNS integer
 LANGUAGE plpgsql
AS \$function$
declare

    iptu_ou_taxa_quitado boolean default false;
    iptu_ou_taxa_isencao boolean default false;
    iptu_ou_taxa_cancelado boolean default false;
    taxa_receita_diferente boolean default false;
    taxa_valor_anterior numeric default 0;
    taxa_valor_agora numeric default 0;
    iptucalv_record record;
    numpre integer default null;
    iptutaxanump_codigo integer default null;
    iptutaxacalv_codigo integer default null;

begin

    /**
     * +------+------------------+------------------+---------------------------------------------------------------------------------+
     * | CASO |       IPTU       |       TAXA       |            PROCEDIMENTO                                                         |
     * +------+------------------+------------------+---------------------------------------------------------------------------------+
     * |  01  | Igual            | Igual            | Delete em arrecad de receita de taxa / delete de taxa em estrutura de iptucalv  |
     * |  02  | Igual            | Diferente        | Update/Insert em estrutura de taxa e update/insert em arrecad de taxa           |
     * |  03  | Diferente        | Igual            | Delete em arrecad de receita de taxa / delete de taxa em estrutura de iptucalv  |
     * |  04  | Diferente        | Diferente        | Update/Insert em estrutura de taxa e update/insert em arrecad de taxa           |
     * |  05  | Quitado          | Quitado          | Sistema nao da suporte                                                          |
     * |  06  | Cancelado        | Cancelado        | Sistema nao da suporte                                                          |
     * |  07  | Isencao          | ---              | Sistema nao da suporte                                                          |
     * +------+------------------+------------------+---------------------------------------------------------------------------------+
     */
    
    /**
     *      Tabela de retornos
     * +------+-----------------------------+
     * | CASO |            SIGNIFICADO      |
     * +------+-----------------------------+
     * |   1  | Sucesso                     |
     * |   2  | Quitado                     |
     * |   3  | Isencao                     |
     * |   4  | Cancelado                   |
     * +------+-----------------------------+
     */

    raise notice 'MATRICULA -> %', matricula;

    /**
     * 05 - Quitado
     */
    select true 
      into iptu_ou_taxa_quitado
      from (
            select true 
              from iptucalv 
                   inner join iptunump on iptunump.j20_matric = iptucalv.j21_matric 
             where iptucalv.j21_matric = matricula
               and iptunump.j20_anousu = ano
               and not exists (select * 
                                 from arrecad 
                                where arrecad.k00_numpre = iptunump.j20_numpre)
             union
            select true
              from iptutaxanump
                   inner join iptucadtaxaexe on iptucadtaxaexe.j08_iptucadtaxaexe = iptutaxanump.j151_iptucadtaxaexe
             where iptutaxanump.j151_matric = matricula
               and iptucadtaxaexe.j08_anousu = ano
               and not exists (select * 
                                 from arrecad 
                                where arrecad.k00_numpre = iptutaxanump.j151_numpre)
      ) as a;

    if iptu_ou_taxa_quitado then
    
        raise notice 'QUITADO';
        return 2;
    end if;

    /**
     * 07 - Isencao
     */
    select x.isenta_taxas 
      into iptu_ou_taxa_isencao 
      from fc_iptu_verifica_isencao_competencia(matricula, ano, false) as x 
     where x.isenta_taxas is true;

    if iptu_ou_taxa_isencao then
        
        raise notice 'ISENCAO';
        return 3;
    end if;

    /**
     * 06 - Cancelado
     */
    select true 
      into iptu_ou_taxa_cancelado
      from (
            select true 
              from iptucalv 
                   inner join iptunump on iptunump.j20_matric = iptucalv.j21_matric 
                   inner join cancdebitosreg on cancdebitosreg.k21_numpre = iptunump.j20_numpre
                   inner join cancdebitos on cancdebitos.k20_codigo = cancdebitosreg.k21_codigo
             where iptucalv.j21_matric = matricula
               and iptunump.j20_anousu = ano
             union
            select true
              from iptutaxanump
                   inner join iptucadtaxaexe on iptucadtaxaexe.j08_iptucadtaxaexe = iptutaxanump.j151_iptucadtaxaexe
                   inner join cancdebitosreg on cancdebitosreg.k21_numpre = iptutaxanump.j151_numpre
                   inner join cancdebitos on cancdebitos.k20_codigo = cancdebitosreg.k21_codigo
             where iptutaxanump.j151_matric = matricula
               and iptucadtaxaexe.j08_anousu = ano
      ) as a;

    if iptu_ou_taxa_cancelado then
    
        raise notice 'CANCELADO';
        return 4;
    end if;

    /**
     * Procedimento para carregamento de valor calculado anterior para taxa
     */
    select sum(iptutaxacalv.j152_valor)
      into taxa_valor_anterior
      from iptutaxanump
           inner join iptucadtaxaexe on iptucadtaxaexe.j08_iptucadtaxaexe = iptutaxanump.j151_iptucadtaxaexe
           inner join iptutaxacalv on iptutaxacalv.j152_iptutaxanump = iptutaxanump.j151_codigo
     where iptutaxanump.j151_matric = matricula
       and iptucadtaxaexe.j08_anousu = ano;

    raise notice 'TAXA VALOR ANTES -> %', taxa_valor_anterior;
    
    /**
     * Procedimento para carregamento de valor calculado atualmente para taxa
     */
    select sum(iptucalv.j21_valor) 
      into taxa_valor_agora 
      from iptucalv 
     where iptucalv.j21_matric = matricula
       and iptucalv.j21_anousu = ano
       and iptucalv.j21_receit in (select j08_tabrec
                                     from iptucadtaxaexe 
                                    where iptucadtaxaexe.j08_anousu = ano);

    raise notice 'TAXA VALOR AGORA -> %', taxa_valor_agora;

    /**
    * 01 - Iptu igual && Taxa igual | 03 - Iptu Diferente && Taxa igual
    */
    if taxa_valor_anterior = taxa_valor_agora then 

        raise notice '01 - Taxa igual';

        raise notice 'TOTAL ARRECAD -> %', (
            select sum(arrecad.k00_valor) 
              from arrecad 
             where arrecad.k00_numpre = (select j20_numpre 
                                           from iptunump 
                                          where j20_matric = matricula 
                                            and j20_anousu = ano));

        raise notice 'TOTAL IPTUCALV -> %', (select sum(j21_valor) from iptucalv where j21_matric = matricula and j21_anousu = ano);

        raise notice 'DELETE ARRECAD DE RECEITA DE TAXA EM NUMPRE DE IPTU';

        delete from arrecad 
              where arrecad.k00_numpre = (select j20_numpre from iptunump where j20_matric = matricula and j20_anousu = ano)
                and arrecad.k00_receit in (select j08_tabrec from iptucadtaxaexe where j08_anousu = ano);

        raise notice 'DELETE IPTUCALV DE RECEITA DE TAXA EM IPTU';

        delete from iptucalv 
              where iptucalv.j21_receit in (select j08_tabrec from iptucadtaxaexe where j08_anousu = ano);

        raise notice 'TOTAL ARRECAD DEPOIS -> %', (
            select sum(arrecad.k00_valor) 
              from arrecad 
             where arrecad.k00_numpre = (select j20_numpre 
                                           from iptunump 
                                          where j20_matric = matricula 
                                            and j20_anousu = ano));
        
        raise notice 'TOTAL IPTUCALV -> %', (select sum(j21_valor) from iptucalv where j21_matric = matricula and j21_anousu = ano);

        return 1;

    end if;

    /**
    * 02 - Iptu igual && Taxa Diferente | 04 - Iptu Diferente && Taxa Diferente
    */
    if taxa_valor_anterior <> taxa_valor_agora then
    
        raise notice 'taxa_valor_anterior <> taxa_valor_agora';

        /**
         * Antes não tinha taxa e agora tem.
         * Deve ser lancado estrutura de taxa e arrecad.
         * INSERT
         */
        if taxa_valor_anterior = 0 and taxa_valor_agora > 0 then 

            raise notice 'taxa_valor_anterior = 0 and taxa_valor_agora > 0 -> INSERT';

            for iptucalv_record in 

                select iptucalv.*,
                       iptunump.*,
                       iptucadtaxaexe.*
                  from iptucalv 
                       inner join iptucadtaxaexe on iptucadtaxaexe.j08_tabrec = iptucalv.j21_receit 
                                                and iptucadtaxaexe.j08_anousu = iptucalv.j21_anousu
                       inner join iptunump on iptunump.j20_matric = iptucalv.j21_matric
                                          and iptunump.j20_anousu = iptucalv.j21_anousu
                 where iptucalv.j21_matric = matricula 
                   and iptucalv.j21_anousu = ano

            loop

                select nextval('numpref_k03_numpre_seq') into numpre;

                raise notice 'numpre novo -> %', numpre;
                raise notice 'numpre iptu -> %', iptucalv_record.j20_numpre;
                raise notice 'receita -> %', iptucalv_record.j21_receit;

                update arrecad 
                   set k00_numpre = numpre
                 where k00_numpre = iptucalv_record.j20_numpre
                   and k00_receit = iptucalv_record.j21_receit;

                select nextval('iptutaxanump_j151_codigo_seq') into iptutaxanump_codigo;

                insert into iptutaxanump (
                    j151_codigo,
                    j151_matric,
                    j151_numpre,
                    j151_iptucadtaxaexe
                ) values (
                    iptutaxanump_codigo,
                    iptucalv_record.j21_matric,
                    numpre,
                    iptucalv_record.j08_iptucadtaxaexe 
                );

                select nextval('iptutaxacalv_j152_codigo_seq') into iptutaxacalv_codigo;

                insert into iptutaxacalv (
                    j152_codigo,
                    j152_iptutaxanump,
                    j152_codhis,
                    j152_receit,
                    j152_valor,
                    j152_quant
                ) values (
                    iptutaxacalv_codigo,
                    iptutaxanump_codigo,
                    iptucalv_record.j21_codhis,
                    iptucalv_record.j21_receit,
                    iptucalv_record.j21_valor,
                    iptucalv_record.j21_quant
                );

                delete from iptucalv 
                 where j21_matric = iptucalv_record.j21_matric
                   and j21_anousu = iptucalv_record.j21_anousu
                   and j21_receit = iptucalv_record.j21_receit;

            end loop;

            return 1;

        end if;

        /**
         * Valor da taxa anterior existia e agora existe tbm so que diferente.
         * Nesse caso deve ser deletado os dados de taxa do numpre de iptu 
         * E atualizado a estrutura e numpre da taxa
         * UPDATE
         */
        if taxa_valor_anterior > 0 and taxa_valor_agora > 0 then 

            raise notice 'taxa_valor_anterior > 0 and taxa_valor_agora > 0 -> UPDATE';

            /**
             * Procedimento para verificar receitas diferentes de taxa de antes e de agora.
             */
            select true
              into taxa_receita_diferente
              from iptucalv 
             where iptucalv.j21_matric = matricula
               and iptucalv.j21_anousu = ano 
               and iptucalv.j21_receit in (select j08_tabrec 
                                             from iptucadtaxaexe 
                                            where j08_anousu = ano) 
               and iptucalv.j21_receit not in (select iptutaxacalv.j152_receit 
                                                 from iptutaxacalv 
                                                      inner join iptutaxanump on iptutaxanump.j151_codigo = iptutaxacalv.j152_iptutaxanump
                                                      inner join iptucadtaxaexe on iptucadtaxaexe.j08_iptucadtaxaexe = iptutaxanump.j151_iptucadtaxaexe
                                                where iptutaxanump.j151_matric = matricula
                                                  and iptucadtaxaexe.j08_anousu = ano
                                              );

            if taxa_receita_diferente then 

                raise notice 'TAXA RECEITAS DIFERENTES';
                return false;
            end if;

            for iptucalv_record in 

                select iptucalv.* 
                  from iptucalv 
                       inner join iptucadtaxaexe on iptucadtaxaexe.j08_tabrec = iptucalv.j21_receit 
                                                and iptucadtaxaexe.j08_anousu = ano
                 where iptucalv.j21_matric = matricula 
                   and iptucalv.j21_anousu = ano

            loop

                raise notice '->>> %', iptucalv_record;
                -- Update em estrutura de taxa com o valor do calculo
                update iptutaxacalv 
                   set j152_valor = iptucalv_record.j21_valor
                 where iptutaxacalv.j152_receit = iptucalv_record.j21_receit
                   and iptutaxacalv.j152_iptutaxanump = (
                       select iptutaxanump.j151_codigo 
                         from iptutaxanump
                              inner join iptucadtaxaexe on iptucadtaxaexe.j08_iptucadtaxaexe = iptutaxanump.j151_iptucadtaxaexe
                                                       and iptucadtaxaexe.j08_anousu = iptucalv_record.j21_anousu
                                                       and iptucadtaxaexe.j08_tabrec = iptucalv_record.j21_receit
                        where iptutaxanump.j151_matric = iptucalv_record.j21_matric
                   );
                
                -- update arrecad de taxa
                update arrecad set k00_valor = ((
                    select iptutaxacalv.j152_valor 
                      from iptutaxacalv 
                     where iptutaxacalv.j152_receit = iptucalv_record.j21_receit
                       and iptutaxacalv.j152_iptutaxanump = (
                           select iptutaxanump.j151_codigo 
                               from iptutaxanump
                                   inner join iptucadtaxaexe on iptucadtaxaexe.j08_iptucadtaxaexe = iptutaxanump.j151_iptucadtaxaexe
                                                           and iptucadtaxaexe.j08_anousu = iptucalv_record.j21_anousu
                                                           and iptucadtaxaexe.j08_tabrec = iptucalv_record.j21_receit
                               where iptutaxanump.j151_matric = iptucalv_record.j21_matric)
                ) / k00_numtot)::numeric(15, 2)
                 where k00_numpre = (
                     select iptutaxanump.j151_numpre 
                       from iptutaxanump
                            inner join iptucadtaxaexe on iptucadtaxaexe.j08_iptucadtaxaexe = iptutaxanump.j151_iptucadtaxaexe
                                                     and iptucadtaxaexe.j08_anousu = iptucalv_record.j21_anousu
                                                     and iptucadtaxaexe.j08_tabrec = iptucalv_record.j21_receit
                      where iptutaxanump.j151_matric = iptucalv_record.j21_matric
                 );

                -- arredonda valor do arrecad
                update arrecad set k00_valor = k00_valor + ((
                    ((
                    select iptutaxacalv.j152_valor 
                      from iptutaxacalv 
                     where iptutaxacalv.j152_receit = iptucalv_record.j21_receit
                       and iptutaxacalv.j152_iptutaxanump = (
                           select iptutaxanump.j151_codigo 
                               from iptutaxanump
                                   inner join iptucadtaxaexe on iptucadtaxaexe.j08_iptucadtaxaexe = iptutaxanump.j151_iptucadtaxaexe
                                                           and iptucadtaxaexe.j08_anousu = iptucalv_record.j21_anousu
                                                           and iptucadtaxaexe.j08_tabrec = iptucalv_record.j21_receit
                               where iptutaxanump.j151_matric = iptucalv_record.j21_matric)
                )/k00_numtot) -
                    ((
                    select iptutaxacalv.j152_valor 
                      from iptutaxacalv 
                     where iptutaxacalv.j152_receit = iptucalv_record.j21_receit
                       and iptutaxacalv.j152_iptutaxanump = (
                           select iptutaxanump.j151_codigo 
                               from iptutaxanump
                                   inner join iptucadtaxaexe on iptucadtaxaexe.j08_iptucadtaxaexe = iptutaxanump.j151_iptucadtaxaexe
                                                           and iptucadtaxaexe.j08_anousu = iptucalv_record.j21_anousu
                                                           and iptucadtaxaexe.j08_tabrec = iptucalv_record.j21_receit
                               where iptutaxanump.j151_matric = iptucalv_record.j21_matric)
                )/k00_numtot)::numeric(15, 2)                    
                ) * k00_numtot)::numeric(15, 2)
                where k00_numpre = (
                     select iptutaxanump.j151_numpre 
                       from iptutaxanump
                            inner join iptucadtaxaexe on iptucadtaxaexe.j08_iptucadtaxaexe = iptutaxanump.j151_iptucadtaxaexe
                                                     and iptucadtaxaexe.j08_anousu = iptucalv_record.j21_anousu
                                                     and iptucadtaxaexe.j08_tabrec = iptucalv_record.j21_receit
                      where iptutaxanump.j151_matric = iptucalv_record.j21_matric
                ) and k00_numpar = k00_numtot;

                -- delete receita de taxa de numpre do iptu
                delete from arrecad 
                 where k00_receit = iptucalv_record.j21_receit
                   and k00_numpre = (
                       select iptunump.j20_numpre 
                         from iptunump 
                        where iptunump.j20_matric = iptucalv_record.j21_matric
                          and iptunump.j20_anousu = iptucalv_record.j21_anousu
                   );

                -- delete de registros de historico de calculo da taxa em estrutura de iptu
                delete from iptucalv 
                 where j21_anousu = iptucalv_record.j21_anousu 
                   and j21_matric = iptucalv_record.j21_matric
                   and j21_receit = iptucalv_record.j21_receit;

            end loop;

            return 1;

        end if;

        /**
         * Valor novo calculado eh igual a 0. Não damos suporte ao caso, sistema tem q gerar credito.
         */
        if taxa_valor_anterior > 0 and taxa_valor_agora = 0 then 

            raise notice 'taxa_valor_anterior > 0 and taxa_valor_agora = 0';
            raise notice 'TAXA NAO EXISTE MAIS';
            return 5;
        end if;

    end if;

    return 5;

end;
\$function$;
SQL
        );
    }
}
