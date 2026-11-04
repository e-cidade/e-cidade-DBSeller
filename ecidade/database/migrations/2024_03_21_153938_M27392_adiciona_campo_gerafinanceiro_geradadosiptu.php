<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M27392AdicionaCampoGerafinanceiroGeradadosiptu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
-- DROP FUNCTION public.fc_iptu_geradadosiptu(int4, int4, int4, numeric, bool, bool);

CREATE OR REPLACE FUNCTION public.fc_iptu_geradadosiptu(integer, integer, integer, numeric, boolean, boolean)
 RETURNS boolean
 LANGUAGE plpgsql
AS \$function$
declare

    iMatricula          alias for $1;
    iIdbql              alias for $2;
    iAnousu             alias for $3;
    nAliqIsen           alias for $4;
    bRaise              alias for $6;

    nTestada            numeric       default 0;
    nBase               numeric(15,2) default 0;

    nViptu              numeric(15,2) default 0;
    nVlrisen            numeric(15,2) default 0;
    nAreatrib           numeric       default 0;
    nAreal              numeric       default 0;

    iReciptu            integer;
    iVencim             integer;
    iHistiptu           integer;
    iHistIsenIptu       integer;
    iHistIsenI          integer;
    iTipoIsen           integer;
    iEspReciptu         integer;
    iTipoCalculo        integer;

    dDtoper             date;
    bErro               boolean;
    bIsenTaxas          boolean;
    lTemDigitacaoManual boolean;
    tSql                text    default '';

    rDadosIptu          record;
    rIptucale           record;
    rIptucalv           record;
    rIptuHistorico      record;

    lRaise              boolean;
    lGerafinanc         boolean    default true;

begin

  lRaise              := bRaise;
  lTemDigitacaoManual := fc_getsession('DB_iptumanual');

  if fc_getsession('DB_Gerafinanc') = '1' then
    lGerafinanc         := false;
  end if;

  perform fc_debug(' <iptu_geradadosiptu> Gerando Dados Iptu', lRaise);
  perform fc_debug(' <iptu_geradadosiptu> nAliqIsen: ' || nAliqIsen, lRaise);

  iTipoCalculo := 1;
  if lTemDigitacaoManual is not null and lTemDigitacaoManual is true then
    iTipoCalculo := 2;
  end if;

  select tipoisen, isentaxas
    into iTipoIsen, bIsenTaxas
    from tmpdadosiptu;

  /**
   * Insere os dados na iptucale, dados q foram manipulados na tmpiptucale durante o calculo
   */
  insert into iptucale
       select anousu, matric, idcons, round(areaed, 2),
              round(vm2, 2), pontos, round(valor, 2), lGerafinanc
         from tmpiptucale;

  select * into rDadosIptu from tmpdadosiptu;

  select sum(areaed) as areaed
    into rIptucale
    from tmpiptucale;

    /**
     * Grava os dados do iptu na iptucalc, iptucalv(onde fica os dados referente aos valores)
     */
   select case when j36_testle = 0
               then j36_testad
               else j36_testle end as j36_testle,
          case when j34_areal  = 0
               then j34_area
              else j34_areal   end as j34_areal
     into nTestada, nAreal
     from testpri
          inner join lote    on j34_idbql = j49_idbql
          inner join face    on j49_face  = j37_face
          inner join testada on j49_face  = j36_face
                            and j49_idbql = j36_idbql
    where j49_idbql = iIdbql;

    select case when rDadosIptu.predial is false
						    then j18_rterri
							  else j18_rpredi
					 end,
					 j18_vencim,
					 j18_dtoper,
					 j18_vlrref,
					 j18_iptuhistisen
      into iReciptu, iVencim, dDtoper, nBase, iHistIsenIptu
      from cfiptu
     where j18_anousu = iAnousu;

   /**
    *  Calcula a area tributada
    */
   begin

     /**
      * Verifica se tem receita especifica por matricula pre-configurada
      * troca a receita default(cfiptu) pela receita especifica( iptucalcconfrec)
      */
     select j23_recdst
       into iEspReciptu
       from iptucalcconfrec
      where j23_matric = iMatricula
        and j23_anousu = iAnousu
        and j23_recorg = iReciptu
        and j23_tipo   = 1;

     if found then

       perform fc_debug(' <iptu_geradadosiptu> Alterando receita: ' || iReciptu || ' por receita especifica: ' || iEspReciptu, lRaise);

       /**
        * Troca a receita da tmprec para seguir a mesma logica na hora de gerar o financeiro
        */
       update tmprecval
          set receita = iEspReciptu
        where receita = iReciptu
          and taxa is false;

       update tmptaxapercisen
          set rectaxaisen = iEspReciptu
        where rectaxaisen = iReciptu;

       iReciptu := iEspReciptu;

     end if;

   exception

     when undefined_table then
     when others then
   end;

  perform *
  from db_plugin
  where db145_nome = 'calculo-de-iptu-proporcional'
    and db145_situacao is true;

  -- Caso o plugin de cálculo de IPTU proporcional esteja instalado e ativo
  -- efetua o cálculo de forma proporcional de acordo com as mudanças nas construções
  if found then
    select sum(z.valor) as areaed into rIptuHistorico
    from (select
              case when x.areadohistorico > 0 then
                       areadohistorico
                   else
                       areaprincipal
                  end as valor
          from (
               select j39_idcons,
                      (
                          select sum(area) as areadohistorico from plugins.iptuconstrareahistorico
                          where plugins.iptuconstrareahistorico.matricula = iMatricula
                            and plugins.iptuconstrareahistorico.datainicio <= (iAnousu||'-'||'01'||'-01')::date
                            and (iAnousu||'-'||'01'||'-01')::date <= plugins.iptuconstrareahistorico.data
                            and plugins.iptuconstrareahistorico.id_constr = j39_idcons
                      ),
                      (
                          select sum(j22_areaed) as areaprincipal from iptucale
                          where iptucale.j22_anousu = iAnousu
                            and iptucale.j22_matric = iMatricula
                            and iptucale.j22_idcons = j39_idcons
                      )
               from iptuconstr
               where j39_matric = iMatricula
                 and (iptuconstr.j39_dtdemo > (iAnousu||'-'||'01'||'-01')::date or iptuconstr.j39_dtdemo is null)
          ) as x
    ) as z;

      if rIptuHistorico.areaed is not null then
          rIptucale.areaed := rIptuHistorico.areaed;
      end if;
  end if;

  perform fc_debug(' <iptu_geradadosiptu> WHUIDHASD!! UI!: ' || rIptucale.areaed, lRaise);
   nAreatrib := rIptucale.areaed * (rDadosIptu.fracao / 100);

   perform fc_debug(' <iptu_geradadosiptu> Area tributada: ' || coalesce( nAreatrib, 0 ), lRaise);

   insert into iptucalc
               ( j23_anousu,
                 j23_matric,
                 j23_testad,
                 j23_arealo,
                 j23_areafr,
                 j23_areaed,
                 j23_m2terr,
                 j23_vlrter,
                 j23_aliq  ,
                 j23_vlrisen,
                 j23_tipoim,
                 j23_manual,
                 j23_tipocalculo,
                 j23_gerafinanc)
        values ( iAnousu,
                 iMatricula,
                 round(case when rDadosIptu.testada is null or rDadosIptu.testada = 0 then nTestada else rDadosIptu.testada end,2),
                 round(rDadosIptu.areat, 2),
                 round(rDadosIptu.fracao,2),
                 round(rIptucale.areaed, 2),
                 round(rDadosIptu.vm2t,  2),
                 round(rDadosIptu.vvt,   2),
                 round(rDadosIptu.aliq,  2),
                 round(nVlrisen,         2),
                 (case when rDadosIptu.predial is true then 'P' else 'T' end),
                 '',
                 iTipoCalculo,
                 lGerafinanc) ;

    /**
     * Incluindo com taxa false
     */
    for rIptucalv in select *
                       from tmprecval
                            left join tmptaxapercisen on tmprecval.receita = tmptaxapercisen.rectaxaisen
                      where taxa is false
    loop

      perform fc_debug(' <iptu_geradadosiptu> Receita: '           || iReciptu, lRaise);
      perform fc_debug(' <iptu_geradadosiptu> Valor: '             || coalesce( round(rIptucalv.valor,2), 0 ), lRaise);
      perform fc_debug(' <iptu_geradadosiptu> Historico: '         || rIptucalv.hist, lRaise);
      perform fc_debug(' <iptu_geradadosiptu> Historico Insecao: ' || rIptucalv.histcalcisen, lRaise);

      if rIptucalv.hist = 1 then

        iHistiptu  := 1;
        iHistIsenI := iHistIsenIptu;
      else

        iHistiptu  := rIptucalv.hist;
        iHistIsenI := rIptucalv.histcalcisen;
      end if;

      if rIptucalv.valor > 0 then

        insert into iptucalv ( j21_anousu,
                               j21_matric,
                               j21_receit,
                               j21_valor,
                               j21_quant,
                               j21_codhis,
                               j21_gerafinanc )
                      values ( iAnousu,
                               iMatricula,
                               iReciptu,
                               round(rIptucalv.valor, 2),
                               0,
                               iHistiptu,
                               lGerafinanc );
      end if;

      if iTipoIsen = 1 and rIptucalv.valor <> 0 then

         nVlrisen  := rIptucalv.valor * ( 100 / 100);
         perform fc_debug(' <iptu_geradadosiptu> Valor da Isencao: ' || coalesce( nVlrisen, 0 ), lRaise);

         insert into iptucalv ( j21_anousu, j21_matric, j21_receit, j21_valor, j21_quant, j21_codhis, j21_gerafinanc )
                       values ( iAnousu, iMatricula, iReciptu, round( ( nVlrisen *-1),2) , 0, iHistIsenI, lGerafinanc );

      elsif nAliqIsen is not null and nAliqIsen > 0 then

         nVlrisen  := rIptucalv.valor * ( nAliqIsen / 100);
         perform fc_debug(' <iptu_geradadosiptu> Valor da Isencao (Utilizando Aliquota): ' || coalesce( nVlrisen, 0 ), lRaise);

         insert into iptucalv ( j21_anousu, j21_matric, j21_receit, j21_valor, j21_quant, j21_codhis, j21_gerafinanc )
                       values ( iAnousu, iMatricula, iReciptu,  round( ( nVlrisen *-1),2) , 0, iHistIsenI, lGerafinanc );
      end if;

    end loop;

    for rIptucalv in select *
                       from tmprecval
                            inner join tmptaxapercisen on tmprecval.receita = tmptaxapercisen.rectaxaisen
                      where taxa is true
    loop

      perform fc_debug(' <iptu_geradadosiptu> Receita Isencao de Taxa: '    || rIptucalv.rectaxaisen, lRaise);
      perform fc_debug(' <iptu_geradadosiptu> Percentual Isencao de Taxa: ' || rIptucalv.percisen,    lRaise);
      perform fc_debug(' <iptu_geradadosiptu> Taxa: '                        || rIptucalv.taxa,        lRaise);

      /**
       * Grava o valor da isencao na iptucalv
       */
      if rIptucalv.rectaxaisen is not null then

        if rIptucalv.histcalcisen is not null then

          perform fc_debug(' <iptu_geradadosiptu> Incluindo valores', lRaise);

          if rIptucalv.valsemisen <> 0 then

          insert into iptucalv (j21_anousu, j21_matric, j21_receit, j21_valor, j21_quant, j21_codhis, j21_gerafinanc)
               values (iAnousu, iMatricula, rIptucalv.receita, round( rIptucalv.valsemisen, 2), 0, rIptucalv.hist, lGerafinanc);

          end if;

          if rIptucalv.percisen > 0 then

            perform fc_debug(' <iptu_geradadosiptu> Incluindo valor de isencao', lRaise);

            insert into iptucalv (j21_anousu, j21_matric, j21_receit, j21_valor, j21_quant, j21_codhis, j21_gerafinanc)
                 values (iAnousu, iMatricula, rIptucalv.receita, round( ((rIptucalv.valsemisen * rIptucalv.percisen) / 100),2) * -1, 0, rIptucalv.histcalcisen, lGerafinanc);

				  end if;

          update tmprecval
             set valor = ( select round(sum(coalesce(j21_valor, 0)), 2)
                             from iptucalv
                            where j21_matric = iMatricula
                              and j21_receit = receita
                              and j21_anousu = iAnousu )
           where receita = receita;

			  end if;

      end if;

    end loop;

    perform fc_debug(' <iptu_geradadosiptu> Valor Iptu SEM isencao' || rDadosIptu.viptu, lRaise);

    nViptu := rDadosIptu.viptu - ( rDadosIptu.viptu * ( nAliqIsen / 100) );

    perform fc_debug(' <iptu_geradadosiptu> Valor Iptu:'          || nViptu,   lRaise);
    perform fc_debug(' <iptu_geradadosiptu> Valor Iptu isencao: ' || nVlrisen, lRaise);

     update tmpdadosiptu set viptu = nViptu;
     update tmprecval    set valor = nViptu where taxa is false and hist = 1 ;

    return true;

end;
\$function$;
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
        DB::statement(<<<SQL
-- DROP FUNCTION public.fc_iptu_geradadosiptu(int4, int4, int4, numeric, bool, bool);

CREATE OR REPLACE FUNCTION public.fc_iptu_geradadosiptu(integer, integer, integer, numeric, boolean, boolean)
 RETURNS boolean
 LANGUAGE plpgsql
AS \$function$
declare

    iMatricula          alias for $1;
    iIdbql              alias for $2;
    iAnousu             alias for $3;
    nAliqIsen           alias for $4;
    bRaise              alias for $6;

    nTestada            numeric       default 0;
    nBase               numeric(15,2) default 0;

    nViptu              numeric(15,2) default 0;
    nVlrisen            numeric(15,2) default 0;
    nAreatrib           numeric       default 0;
    nAreal              numeric       default 0;

    iReciptu            integer;
    iVencim             integer;
    iHistiptu           integer;
    iHistIsenIptu       integer;
    iHistIsenI          integer;
    iTipoIsen           integer;
    iEspReciptu         integer;
    iTipoCalculo        integer;

    dDtoper             date;
    bErro               boolean;
    bIsenTaxas          boolean;
    lTemDigitacaoManual boolean;
    tSql                text    default '';

    rDadosIptu          record;
    rIptucale           record;
    rIptucalv           record;
    rIptuHistorico      record;

    lRaise              boolean;

begin

  lRaise              := bRaise;
  lTemDigitacaoManual := fc_getsession('DB_iptumanual');

  perform fc_debug(' <iptu_geradadosiptu> Gerando Dados Iptu', lRaise);
  perform fc_debug(' <iptu_geradadosiptu> nAliqIsen: ' || nAliqIsen, lRaise);

  iTipoCalculo := 1;
  if lTemDigitacaoManual is not null and lTemDigitacaoManual is true then
    iTipoCalculo := 2;
  end if;

  select tipoisen, isentaxas
    into iTipoIsen, bIsenTaxas
    from tmpdadosiptu;

  /**
   * Insere os dados na iptucale, dados q foram manipulados na tmpiptucale durante o calculo
   */
  insert into iptucale
       select anousu, matric, idcons, round(areaed, 2),
              round(vm2, 2), pontos, round(valor, 2)
         from tmpiptucale;

  select * into rDadosIptu from tmpdadosiptu;

  select sum(areaed) as areaed
    into rIptucale
    from tmpiptucale;

    /**
     * Grava os dados do iptu na iptucalc, iptucalv(onde fica os dados referente aos valores)
     */
   select case when j36_testle = 0
               then j36_testad
               else j36_testle end as j36_testle,
          case when j34_areal  = 0
               then j34_area
              else j34_areal   end as j34_areal
     into nTestada, nAreal
     from testpri
          inner join lote    on j34_idbql = j49_idbql
          inner join face    on j49_face  = j37_face
          inner join testada on j49_face  = j36_face
                            and j49_idbql = j36_idbql
    where j49_idbql = iIdbql;

    select case when rDadosIptu.predial is false
						    then j18_rterri
							  else j18_rpredi
					 end,
					 j18_vencim,
					 j18_dtoper,
					 j18_vlrref,
					 j18_iptuhistisen
      into iReciptu, iVencim, dDtoper, nBase, iHistIsenIptu
      from cfiptu
     where j18_anousu = iAnousu;

   /**
    *  Calcula a area tributada
    */
   begin

     /**
      * Verifica se tem receita especifica por matricula pre-configurada
      * troca a receita default(cfiptu) pela receita especifica( iptucalcconfrec)
      */
     select j23_recdst
       into iEspReciptu
       from iptucalcconfrec
      where j23_matric = iMatricula
        and j23_anousu = iAnousu
        and j23_recorg = iReciptu
        and j23_tipo   = 1;

     if found then

       perform fc_debug(' <iptu_geradadosiptu> Alterando receita: ' || iReciptu || ' por receita especifica: ' || iEspReciptu, lRaise);

       /**
        * Troca a receita da tmprec para seguir a mesma logica na hora de gerar o financeiro
        */
       update tmprecval
          set receita = iEspReciptu
        where receita = iReciptu
          and taxa is false;

       update tmptaxapercisen
          set rectaxaisen = iEspReciptu
        where rectaxaisen = iReciptu;

       iReciptu := iEspReciptu;

     end if;

   exception

     when undefined_table then
     when others then
   end;

  perform *
  from db_plugin
  where db145_nome = 'calculo-de-iptu-proporcional'
    and db145_situacao is true;

  -- Caso o plugin de cálculo de IPTU proporcional esteja instalado e ativo
  -- efetua o cálculo de forma proporcional de acordo com as mudanças nas construções
  if found then
    select sum(z.valor) as areaed into rIptuHistorico
    from (select
              case when x.areadohistorico > 0 then
                       areadohistorico
                   else
                       areaprincipal
                  end as valor
          from (
               select j39_idcons,
                      (
                          select sum(area) as areadohistorico from plugins.iptuconstrareahistorico
                          where plugins.iptuconstrareahistorico.matricula = iMatricula
                            and plugins.iptuconstrareahistorico.datainicio <= (iAnousu||'-'||'01'||'-01')::date
                            and (iAnousu||'-'||'01'||'-01')::date <= plugins.iptuconstrareahistorico.data
                            and plugins.iptuconstrareahistorico.id_constr = j39_idcons
                      ),
                      (
                          select sum(j22_areaed) as areaprincipal from iptucale
                          where iptucale.j22_anousu = iAnousu
                            and iptucale.j22_matric = iMatricula
                            and iptucale.j22_idcons = j39_idcons
                      )
               from iptuconstr
               where j39_matric = iMatricula
                 and (iptuconstr.j39_dtdemo > (iAnousu||'-'||'01'||'-01')::date or iptuconstr.j39_dtdemo is null)
          ) as x
    ) as z;

      if rIptuHistorico.areaed is not null then
          rIptucale.areaed := rIptuHistorico.areaed;
      end if;
  end if;

  perform fc_debug(' <iptu_geradadosiptu> WHUIDHASD!! UI!: ' || rIptucale.areaed, lRaise);
   nAreatrib := rIptucale.areaed * (rDadosIptu.fracao / 100);

   perform fc_debug(' <iptu_geradadosiptu> Area tributada: ' || coalesce( nAreatrib, 0 ), lRaise);

   insert into iptucalc
               ( j23_anousu,
                 j23_matric,
                 j23_testad,
                 j23_arealo,
                 j23_areafr,
                 j23_areaed,
                 j23_m2terr,
                 j23_vlrter,
                 j23_aliq  ,
                 j23_vlrisen,
                 j23_tipoim,
                 j23_manual,
                 j23_tipocalculo )
        values ( iAnousu,
                 iMatricula,
                 round(case when rDadosIptu.testada is null or rDadosIptu.testada = 0 then nTestada else rDadosIptu.testada end,2),
                 round(rDadosIptu.areat, 2),
                 round(rDadosIptu.fracao,2),
                 round(rIptucale.areaed, 2),
                 round(rDadosIptu.vm2t,  2),
                 round(rDadosIptu.vvt,   2),
                 round(rDadosIptu.aliq,  2),
                 round(nVlrisen,         2),
                 (case when rDadosIptu.predial is true then 'P' else 'T' end),
                 '',
                 iTipoCalculo ) ;

    /**
     * Incluindo com taxa false
     */
    for rIptucalv in select *
                       from tmprecval
                            left join tmptaxapercisen on tmprecval.receita = tmptaxapercisen.rectaxaisen
                      where taxa is false
    loop

      perform fc_debug(' <iptu_geradadosiptu> Receita: '           || iReciptu, lRaise);
      perform fc_debug(' <iptu_geradadosiptu> Valor: '             || coalesce( round(rIptucalv.valor,2), 0 ), lRaise);
      perform fc_debug(' <iptu_geradadosiptu> Historico: '         || rIptucalv.hist, lRaise);
      perform fc_debug(' <iptu_geradadosiptu> Historico Insecao: ' || rIptucalv.histcalcisen, lRaise);

      if rIptucalv.hist = 1 then

        iHistiptu  := 1;
        iHistIsenI := iHistIsenIptu;
      else

        iHistiptu  := rIptucalv.hist;
        iHistIsenI := rIptucalv.histcalcisen;
      end if;

      if rIptucalv.valor > 0 then

        insert into iptucalv ( j21_anousu,
                               j21_matric,
                               j21_receit,
                               j21_valor,
                               j21_quant,
                               j21_codhis )
                      values ( iAnousu,
                               iMatricula,
                               iReciptu,
                               round(rIptucalv.valor, 2),
                               0,
                               iHistiptu );
      end if;

      if iTipoIsen = 1 and rIptucalv.valor <> 0 then

         nVlrisen  := rIptucalv.valor * ( 100 / 100);
         perform fc_debug(' <iptu_geradadosiptu> Valor da Isencao: ' || coalesce( nVlrisen, 0 ), lRaise);

         insert into iptucalv ( j21_anousu, j21_matric, j21_receit, j21_valor, j21_quant, j21_codhis )
                       values ( iAnousu, iMatricula, iReciptu, round( ( nVlrisen *-1),2) , 0, iHistIsenI );

      elsif nAliqIsen is not null and nAliqIsen > 0 then

         nVlrisen  := rIptucalv.valor * ( nAliqIsen / 100);
         perform fc_debug(' <iptu_geradadosiptu> Valor da Isencao (Utilizando Aliquota): ' || coalesce( nVlrisen, 0 ), lRaise);

         insert into iptucalv ( j21_anousu, j21_matric, j21_receit, j21_valor, j21_quant, j21_codhis )
                       values ( iAnousu, iMatricula, iReciptu,  round( ( nVlrisen *-1),2) , 0, iHistIsenI );
      end if;

    end loop;

    for rIptucalv in select *
                       from tmprecval
                            inner join tmptaxapercisen on tmprecval.receita = tmptaxapercisen.rectaxaisen
                      where taxa is true
    loop

      perform fc_debug(' <iptu_geradadosiptu> Receita Isencao de Taxa: '    || rIptucalv.rectaxaisen, lRaise);
      perform fc_debug(' <iptu_geradadosiptu> Percentual Isencao de Taxa: ' || rIptucalv.percisen,    lRaise);
      perform fc_debug(' <iptu_geradadosiptu> Taxa: '                        || rIptucalv.taxa,        lRaise);

      /**
       * Grava o valor da isencao na iptucalv
       */
      if rIptucalv.rectaxaisen is not null then

        if rIptucalv.histcalcisen is not null then

          perform fc_debug(' <iptu_geradadosiptu> Incluindo valores', lRaise);

          if rIptucalv.valsemisen <> 0 then

          insert into iptucalv (j21_anousu, j21_matric, j21_receit, j21_valor, j21_quant, j21_codhis)
               values (iAnousu, iMatricula, rIptucalv.receita, round( rIptucalv.valsemisen, 2), 0, rIptucalv.hist);

          end if;

          if rIptucalv.percisen > 0 then

            perform fc_debug(' <iptu_geradadosiptu> Incluindo valor de isencao', lRaise);

            insert into iptucalv (j21_anousu, j21_matric, j21_receit, j21_valor, j21_quant, j21_codhis)
                 values (iAnousu, iMatricula, rIptucalv.receita, round( ((rIptucalv.valsemisen * rIptucalv.percisen) / 100),2) * -1, 0, rIptucalv.histcalcisen);

				  end if;

          update tmprecval
             set valor = ( select round(sum(coalesce(j21_valor, 0)), 2)
                             from iptucalv
                            where j21_matric = iMatricula
                              and j21_receit = receita
                              and j21_anousu = iAnousu )
           where receita = receita;

			  end if;

      end if;

    end loop;

    perform fc_debug(' <iptu_geradadosiptu> Valor Iptu SEM isencao' || rDadosIptu.viptu, lRaise);

    nViptu := rDadosIptu.viptu - ( rDadosIptu.viptu * ( nAliqIsen / 100) );

    perform fc_debug(' <iptu_geradadosiptu> Valor Iptu:'          || nViptu,   lRaise);
    perform fc_debug(' <iptu_geradadosiptu> Valor Iptu isencao: ' || nVlrisen, lRaise);

     update tmpdadosiptu set viptu = nViptu;
     update tmprecval    set valor = nViptu where taxa is false and hist = 1 ;

    return true;

end;
\$function$;

SQL
        );
    }
}
