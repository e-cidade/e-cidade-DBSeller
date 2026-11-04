<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27658AjustaFcIptuDemonstrativo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL

CREATE OR REPLACE FUNCTION public.fc_iptu_demonstrativo(integer, integer, integer, boolean)
 RETURNS text
 LANGUAGE plpgsql
AS \$function$
declare

   iMatricula      alias for $1;
   iAnousu         alias for $2;
   iIdql           alias for $3;
   bRaise          alias for $4;

   tDemonstrativo  text        default '
';
   tSqlConstr      text        default '';
   tSqlIsencao       text        default '';
   nTotal          numeric(15,2) default 0;
   nVm2            numeric       default 0;

   iTotalPontos    integer default 0;
   nAreaEdificada  numeric;
   iNumpreVerifica integer default 0;

   rValores        record;
   rDadosIptu      record;
   rProprietario   record;
   rEndereco       record;
   rConstr         record;
   rIsenc      record;
   rCaract         record;
   rLoteCaract     record;

   lAbatimento     boolean default false;

begin

   if bRaise then
      raise notice ' GERANDO DEMONSTRATIVO DE CALCULO ...';
   end if;

    -- Verifica se existe Pagamento Parcial para o débito informado
    select j20_numpre
      from iptunump
      into iNumpreVerifica
     where j20_matric = iMatricula
       and j20_anousu = iAnousu limit 1;

    if found then
      select fc_verifica_abatimento(1,( select j20_numpre
                                          from iptunump
                                         where j20_matric = iMatricula
                                           and j20_anousu = iAnousu
                                         limit 1 ))::boolean into lAbatimento;

      if lAbatimento then
--        raise exception '<erro>Operação Cancelada, Débito com Pagamento Parcial!</erro>';
      end if;
    end if;
------------------------------- dados do proprietario -------------------------
   select cgm.z01_cgccpf,
    cgm.z01_nome,
          cgm.z01_ident,
          cgm.z01_ender,
          cgm.z01_numero,
          cgm.z01_bairro,
          cgm.z01_cep,
          cgm.z01_munic,
          cgm.z01_uf,
          cgm.z01_telef,
          cgm.z01_cadast
     into rProprietario
     from cgm
          inner join iptubase on iptubase.j01_numcgm = cgm.z01_numcgm
    where j01_matric = iMatricula;

   tDemonstrativo := tDemonstrativo||LPAD('[ PROPRIETÁRIO ]--',90,'-')||'
';
   tDemonstrativo := tDemonstrativo||'
';
   tDemonstrativo := tDemonstrativo||RPAD(' MATRICULA '           ,55,'.')||': '|| iMatricula ||' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' NOME/RAZAO SOCIAL '   ,55,'.')||': '|| trim(coalesce(rProprietario.z01_nome,'')) ||' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' CGC/CPF '             ,55,'.')||': '|| trim(coalesce(rProprietario.z01_cgccpf,'')) ||' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' IDENTIDADE/INSC.EST ' ,55,'.')||': '|| trim(coalesce(rProprietario.z01_ident,''))  ||' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' ENDERECO '            ,55,'.')||': '|| trim(coalesce(rProprietario.z01_ender,''))  ||' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' NUMERO '              ,55,'.')||': '|| trim(coalesce(rProprietario.z01_numero,0)::varchar)  ||' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' BAIRRO '              ,55,'.')||': '|| trim(coalesce(rProprietario.z01_bairro,'')) ||' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' CEP '                 ,55,'.')||': '|| trim(coalesce(rProprietario.z01_cep,''))    ||' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' MUNICIPIO '           ,55,'.')||': '|| trim(coalesce(rProprietario.z01_munic,''))  ||' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' UF '                  ,55,'.')||': '|| trim(coalesce(rProprietario.z01_uf,''))     ||' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' TELEFONE '            ,55,'.')||': '|| trim(coalesce(rProprietario.z01_telef,''))  ||' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' DATA DO CADASTRO '    ,55,'.')||': '|| trim(coalesce(cast(rProprietario.z01_cadast as text),'')) ||' 
';
   tDemonstrativo := tDemonstrativo||'
';

------------------------------ endereco do imovel ------------------------------
   select distinct
          iptuconstr.j39_numero,
          iptuconstr.j39_compl,
          ruas.j14_nome,
          bairro.j13_descr,
          lote.j34_setor,
          lote.j34_quadra,
          lote.j34_lote,
          lote.j34_area
     into rEndereco
     from iptubase
          left  join iptuconstr on j01_matric = j39_matric
          inner join lote       on j34_idbql  = j01_idbql
          inner join bairro     on j34_bairro = j13_codi
          inner join testpri    on j01_idbql  = j49_idbql
          inner join ruas       on j49_codigo = j14_codigo
    where iptuconstr.j39_dtdemo is null
      and j01_matric = iMatricula;


   tDemonstrativo := tDemonstrativo||LPAD('[ ENDERECO DO IMÓVEL ]--',90,'-')||'
';
   tDemonstrativo := tDemonstrativo||'
';
   tDemonstrativo := tDemonstrativo||RPAD(' LOGRADOURO '            ,55,'.')||': '|| trim(coalesce(rEndereco.j14_nome,''))   ||' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' NUMERO '                ,55,'.')||': '|| trim(coalesce(rEndereco.j39_numero::varchar,'')) ||' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' COMPLEMENTO '           ,55,'.')||': '|| trim(coalesce(rEndereco.j39_compl,''))  ||' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' BAIRRO '                ,55,'.')||': '|| trim(coalesce(rEndereco.j13_descr,''))  ||' 
';
   tDemonstrativo := tDemonstrativo||'
';

--------------------------------- dados do lote ---------------------------------

   tDemonstrativo := tDemonstrativo||LPAD('[ DADOS DO LOTE ]--' ,90,'-')||'
';
   tDemonstrativo := tDemonstrativo||'
';
   tDemonstrativo := tDemonstrativo||RPAD(' SETOR/QUADRA/LOTE ' ,55,'.')||': '|| trim(coalesce(rEndereco.j34_setor,'')||'/'||coalesce(rEndereco.j34_quadra,'')||'/'||coalesce(rEndereco.j34_lote,'')) || ' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' AREA '              ,55,'.')||': '|| trim(coalesce(rEndereco.j34_area::varchar,'')) || ' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' TESTADA PRINCIPAL ' ,55,'.')||': '|| trim(coalesce(rEndereco.j14_nome,'')) || ' 
';
   tDemonstrativo := tDemonstrativo||' CARACTERISTICAS DO LOTE : 
';
   for rLoteCaract in select j31_codigo,j31_descr,j31_grupo from carlote
                      inner join caracter on j35_caract = j31_codigo
                      where j35_idbql = iIdql
   loop
       tDemonstrativo := tDemonstrativo||LPAD(' '||coalesce(rLoteCaract.j31_codigo::varchar,'') ,40,'.')||' - '||coalesce(rLoteCaract.j31_descr,'')||' - GRUPO : '||rLoteCaract.j31_grupo||'
';
   end loop;
   tDemonstrativo := tDemonstrativo||'
';


------------------------------ dados das construcoes ------------------------------
   tDemonstrativo := tDemonstrativo||LPAD('[ DADOS DAS CONSTRUÇÕES ]--' ,90,'-')||'
';
   
   tSqlConstr := 'select distinct j39_idcons,j39_area,j39_ano,valor,j39_matric,coalesce(pontos,0) as pontos from iptuconstr
                            inner join tmpiptucale on matric = j39_matric and idcons = j39_idcons
                  where j39_matric = '||iMatricula;
   for rConstr in execute tSqlConstr
   loop
      tDemonstrativo := tDemonstrativo||'
';
      tDemonstrativo := tDemonstrativo||RPAD(' CONSTRUÇÃO '           ,55,'.')||': '|| coalesce(rConstr.j39_idcons::varchar,'')          || ' 
';
      tDemonstrativo := tDemonstrativo||RPAD(' PONTUAÇÃO '            ,55,'.')||': '|| coalesce(rConstr.pontos::varchar,'')              || ' 
';
      tDemonstrativo := tDemonstrativo||RPAD(' AREA '                 ,55,'.')||': '|| coalesce(round(rConstr.j39_area,2)::varchar,'')   || ' 
';
      tDemonstrativo := tDemonstrativo||RPAD(' ANO DA CONSTRUÇÃO '    ,55,'.')||': '|| coalesce(rConstr.j39_ano::varchar,'')             || ' 
';
      tDemonstrativo := tDemonstrativo||RPAD(' VLR VENAL CONSTRUÇÃO ' ,55,'.')||': '|| coalesce(round(rConstr.valor,2)::varchar,'')      || ' 
';

      tDemonstrativo := tDemonstrativo||' CARACTERISTICAS DA CONSTRUÇÃO : 
';
      for rCaract in select * from carconstr
                     inner join caracter  on j48_caract = j31_codigo
                     where j48_matric = rConstr.j39_matric
                       and j48_idcons = rConstr.j39_idcons
      loop
           tDemonstrativo := tDemonstrativo||LPAD(' '||rCaract.j31_codigo ,40,'.')||' - '||coalesce(rCaract.j31_descr,'')||' - GRUPO : '||rCaract.j31_grupo||'
';
      end loop;

   end loop;
   tDemonstrativo := tDemonstrativo||'
';


------------------------------ dados do financeiro ------------------------------

   select * from tmpdadosiptu into rDadosIptu;

   select sum(coalesce(pontos,0)), sum(areaed)
     into iTotalPontos
     from tmpiptucale;

   select sum(areaed)
     into nAreaEdificada
     from tmpiptucale;

   tDemonstrativo := tDemonstrativo||LPAD('[ CALCULO '||coalesce(IAnousu::varchar,'')||' ]--',90,'-')||'
';
   tDemonstrativo := tDemonstrativo||'
';
   tDemonstrativo := tDemonstrativo||RPAD(' PONTUAÇÃO '            ,55,'.')||': '|| coalesce(iTotalPontos::varchar,'')||'  
';
   tDemonstrativo := tDemonstrativo||RPAD(' AREA P/ CALCULO '      ,55,'.')||': '|| coalesce(round(rDadosIptu.areat,2)::varchar,'') || '
';
   tDemonstrativo := tDemonstrativo||RPAD(' FRACAO '               ,55,'.')||': '|| coalesce(round( rDadosIptu.fracao,2)::varchar,'') || '% 
';
   tDemonstrativo := tDemonstrativo||RPAD(' ALIQUOTA '             ,55,'.')||': '|| coalesce(round( rDadosIptu.aliq,2)::varchar,'')   || '% 
';
   tDemonstrativo := tDemonstrativo||RPAD(' VALOR VENAL TERRENO '  ,55,'.')||': '|| coalesce(round( rDadosIptu.vvt,2)::varchar,'')    || ' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' VALOR VENAL EDIFIC '   ,55,'.')||': '|| coalesce(round( rDadosIptu.vvc,2)::varchar,'')    || ' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' VALOR VENAL TOTAL '   ,55,'.')||': '|| coalesce(round( rDadosIptu.vvc,2),0) + coalesce(round( rDadosIptu.vvt,2),0)    || ' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' AREA DO TERRENO '       ,55,'.')||': '|| coalesce(round( rDadosIptu.areat,2)::varchar,'')  || ' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' AREA EDIFICADA '       ,55,'.')||': '|| coalesce(round( nAreaEdificada,2)::varchar,'')  || ' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' VALOR M2 DO TERRENO '  ,55,'.')||': '|| coalesce(round( rDadosIptu.vm2t,2)::varchar,'')   || ' 
';

 for rValores in select * from tmprecval inner join tabrec on receita = k02_codigo loop
     tDemonstrativo := tDemonstrativo||RPAD(' VALOR '||coalesce(rValores.k02_descr::varchar,'0'),55,'.')||': '|| coalesce(round(rValores.valor,2)::varchar,'0') || '
';
     nTotal         := nTotal + coalesce(rValores.valor,0);
 end loop;
 
 tSqlIsencao := 'SELECT k02_descr, j17_descr,  CASE
              WHEN iptucalhconf.j89_codhis IS NOT NULL THEN
                  (SELECT sum(x.j21_valor)
                   FROM iptucalv x
                   WHERE x.j21_anousu = iptucalv.j21_anousu
                     AND x.j21_matric = iptucalv.j21_matric
                     AND x.j21_receit = iptucalv.j21_receit
                     AND x.j21_codhis = iptucalhconf.j89_codhis)
                ELSE 0
            END AS j21_valorisen
      FROM iptucalv
      INNER JOIN iptucalh ON iptucalh.j17_codhis = j21_codhis
      LEFT JOIN iptucalhconf ON iptucalhconf.j89_codhispai = j21_codhis
      INNER JOIN tabrec ON tabrec.k02_codigo = j21_receit
      WHERE j21_matric = '||iMatricula||' AND j21_anousu = '||iAnousu||'
        AND j17_codhis NOT IN  (SELECT j89_codhis FROM iptucalhconf) ORDER BY iptucalh.j17_codhis';
    for rIsenc in execute tSqlIsencao
    loop
  if rIsenc.j21_valorisen is not null or rIsenc.j21_valorisen <> 0 then 
   tDemonstrativo := tDemonstrativo||RPAD(' VALOR  ISENCAO '||coalesce(rIsenc.j17_descr::varchar,''),55,'.')||': '|| coalesce(round(rIsenc.j21_valorisen,2)::varchar,'') || '
';
   nTotal         := nTotal + rIsenc.j21_valorisen;
  end if;
     end loop;

 tDemonstrativo := tDemonstrativo||RPAD(' TOTAL A PAGAR ',55,'.')||': '||coalesce(nTotal,0)||'  
';

 return tDemonstrativo;

end;
\$function$
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
        DB::connection()->getPdo()->exec(<<<SQLD

CREATE OR REPLACE FUNCTION public.fc_iptu_demonstrativo(integer, integer, integer, boolean)
 RETURNS text
 LANGUAGE plpgsql
AS \$function$
declare

   iMatricula      alias for $1;
   iAnousu         alias for $2;
   iIdql           alias for $3;
   bRaise          alias for $4;

   tDemonstrativo  text        default '
';
   tSqlConstr      text        default '';
   tSqlIsencao       text        default '';
   nTotal          numeric(15,2) default 0;
   nVm2            numeric       default 0;

   iTotalPontos    integer default 0;
   nAreaEdificada  numeric;
   iNumpreVerifica integer default 0;

   rValores        record;
   rDadosIptu      record;
   rProprietario   record;
   rEndereco       record;
   rConstr         record;
   rIsenc      record;
   rCaract         record;
   rLoteCaract     record;

   lAbatimento     boolean default false;

begin

   if bRaise then
      raise notice ' GERANDO DEMONSTRATIVO DE CALCULO ...';
   end if;

    -- Verifica se existe Pagamento Parcial para o débito informado
    select j20_numpre
      from iptunump
      into iNumpreVerifica
     where j20_matric = iMatricula
       and j20_anousu = iAnousu limit 1;

    if found then
      select fc_verifica_abatimento(1,( select j20_numpre
                                          from iptunump
                                         where j20_matric = iMatricula
                                           and j20_anousu = iAnousu
                                         limit 1 ))::boolean into lAbatimento;

      if lAbatimento then
--        raise exception '<erro>Operação Cancelada, Débito com Pagamento Parcial!</erro>';
      end if;
    end if;
------------------------------- dados do proprietario -------------------------
   select cgm.z01_cgccpf,
    cgm.z01_nome,
          cgm.z01_ident,
          cgm.z01_ender,
          cgm.z01_numero,
          cgm.z01_bairro,
          cgm.z01_cep,
          cgm.z01_munic,
          cgm.z01_uf,
          cgm.z01_telef,
          cgm.z01_cadast
     into rProprietario
     from cgm
          inner join iptubase on iptubase.j01_numcgm = cgm.z01_numcgm
    where j01_matric = iMatricula;

   tDemonstrativo := tDemonstrativo||LPAD('[ PROPRIETÁRIO ]--',90,'-')||'
';
   tDemonstrativo := tDemonstrativo||'
';
   tDemonstrativo := tDemonstrativo||RPAD(' MATRICULA '           ,55,'.')||': '|| iMatricula ||' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' NOME/RAZAO SOCIAL '   ,55,'.')||': '|| trim(coalesce(rProprietario.z01_nome,'')) ||' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' CGC/CPF '             ,55,'.')||': '|| trim(coalesce(rProprietario.z01_cgccpf,'')) ||' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' IDENTIDADE/INSC.EST ' ,55,'.')||': '|| trim(coalesce(rProprietario.z01_ident,''))  ||' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' ENDERECO '            ,55,'.')||': '|| trim(coalesce(rProprietario.z01_ender,''))  ||' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' NUMERO '              ,55,'.')||': '|| trim(coalesce(rProprietario.z01_numero,0)::varchar)  ||' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' BAIRRO '              ,55,'.')||': '|| trim(coalesce(rProprietario.z01_bairro,'')) ||' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' CEP '                 ,55,'.')||': '|| trim(coalesce(rProprietario.z01_cep,''))    ||' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' MUNICIPIO '           ,55,'.')||': '|| trim(coalesce(rProprietario.z01_munic,''))  ||' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' UF '                  ,55,'.')||': '|| trim(coalesce(rProprietario.z01_uf,''))     ||' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' TELEFONE '            ,55,'.')||': '|| trim(coalesce(rProprietario.z01_telef,''))  ||' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' DATA DO CADASTRO '    ,55,'.')||': '|| trim(coalesce(cast(rProprietario.z01_cadast as text),'')) ||' 
';
   tDemonstrativo := tDemonstrativo||'
';

------------------------------ endereco do imovel ------------------------------
   select distinct
          iptuconstr.j39_numero,
          iptuconstr.j39_compl,
          ruas.j14_nome,
          bairro.j13_descr,
          lote.j34_setor,
          lote.j34_quadra,
          lote.j34_lote,
          lote.j34_area
     into rEndereco
     from iptubase
          left  join iptuconstr on j01_matric = j39_matric
          inner join lote       on j34_idbql  = j01_idbql
          inner join bairro     on j34_bairro = j13_codi
          inner join testpri    on j01_idbql  = j49_idbql
          inner join ruas       on j49_codigo = j14_codigo
    where iptuconstr.j39_dtdemo is null
      and j01_matric = iMatricula;


   tDemonstrativo := tDemonstrativo||LPAD('[ ENDERECO DO IMÓVEL ]--',90,'-')||'
';
   tDemonstrativo := tDemonstrativo||'
';
   tDemonstrativo := tDemonstrativo||RPAD(' LOGRADOURO '            ,55,'.')||': '|| trim(coalesce(rEndereco.j14_nome,''))   ||' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' NUMERO '                ,55,'.')||': '|| trim(coalesce(rEndereco.j39_numero::varchar,'')) ||' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' COMPLEMENTO '           ,55,'.')||': '|| trim(coalesce(rEndereco.j39_compl,''))  ||' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' BAIRRO '                ,55,'.')||': '|| trim(coalesce(rEndereco.j13_descr,''))  ||' 
';
   tDemonstrativo := tDemonstrativo||'
';

--------------------------------- dados do lote ---------------------------------

   tDemonstrativo := tDemonstrativo||LPAD('[ DADOS DO LOTE ]--' ,90,'-')||'
';
   tDemonstrativo := tDemonstrativo||'
';
   tDemonstrativo := tDemonstrativo||RPAD(' SETOR/QUADRA/LOTE ' ,55,'.')||': '|| trim(coalesce(rEndereco.j34_setor,'')||'/'||coalesce(rEndereco.j34_quadra,'')||'/'||coalesce(rEndereco.j34_lote,'')) || ' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' AREA '              ,55,'.')||': '|| trim(coalesce(rEndereco.j34_area::varchar,'')) || ' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' TESTADA PRINCIPAL ' ,55,'.')||': '|| trim(coalesce(rEndereco.j14_nome,'')) || ' 
';
   tDemonstrativo := tDemonstrativo||' CARACTERISTICAS DO LOTE : 
';
   for rLoteCaract in select j31_codigo,j31_descr,j31_grupo from carlote
                      inner join caracter on j35_caract = j31_codigo
                      where j35_idbql = iIdql
   loop
       tDemonstrativo := tDemonstrativo||LPAD(' '||coalesce(rLoteCaract.j31_codigo::varchar,'') ,40,'.')||' - '||coalesce(rLoteCaract.j31_descr,'')||' - GRUPO : '||rLoteCaract.j31_grupo||'
';
   end loop;
   tDemonstrativo := tDemonstrativo||'
';


------------------------------ dados das construcoes ------------------------------
   tDemonstrativo := tDemonstrativo||LPAD('[ DADOS DAS CONSTRUÇÕES ]--' ,90,'-')||'
';
   
   tSqlConstr := 'select distinct j39_idcons,j39_area,j39_ano,valor,j39_matric,coalesce(pontos,0) as pontos from iptuconstr
                            inner join tmpiptucale on matric = j39_matric and idcons = j39_idcons
                  where j39_matric = '||iMatricula;
   for rConstr in execute tSqlConstr
   loop
      tDemonstrativo := tDemonstrativo||'
';
      tDemonstrativo := tDemonstrativo||RPAD(' CONSTRUÇÃO '           ,55,'.')||': '|| coalesce(rConstr.j39_idcons::varchar,'')          || ' 
';
      tDemonstrativo := tDemonstrativo||RPAD(' PONTUAÇÃO '            ,55,'.')||': '|| coalesce(rConstr.pontos::varchar,'')              || ' 
';
      tDemonstrativo := tDemonstrativo||RPAD(' AREA '                 ,55,'.')||': '|| coalesce(round(rConstr.j39_area,2)::varchar,'')   || ' 
';
      tDemonstrativo := tDemonstrativo||RPAD(' ANO DA CONSTRUÇÃO '    ,55,'.')||': '|| coalesce(rConstr.j39_ano::varchar,'')             || ' 
';
      tDemonstrativo := tDemonstrativo||RPAD(' VLR VENAL CONSTRUÇÃO ' ,55,'.')||': '|| coalesce(round(rConstr.valor,2)::varchar,'')      || ' 
';

      tDemonstrativo := tDemonstrativo||' CARACTERISTICAS DA CONSTRUÇÃO : 
';
      for rCaract in select * from carconstr
                     inner join caracter  on j48_caract = j31_codigo
                     where j48_matric = rConstr.j39_matric
                       and j48_idcons = rConstr.j39_idcons
      loop
           tDemonstrativo := tDemonstrativo||LPAD(' '||rCaract.j31_codigo ,40,'.')||' - '||coalesce(rCaract.j31_descr,'')||' - GRUPO : '||rCaract.j31_grupo||'
';
      end loop;

   end loop;
   tDemonstrativo := tDemonstrativo||'
';


------------------------------ dados do financeiro ------------------------------

   select * from tmpdadosiptu into rDadosIptu;

   select sum(coalesce(pontos,0)), sum(areaed)
     into iTotalPontos
     from tmpiptucale;

   select sum(areaed)
     into nAreaEdificada
     from tmpiptucale;

   tDemonstrativo := tDemonstrativo||LPAD('[ CALCULO '||coalesce(IAnousu::varchar,'')||' ]--',90,'-')||'
';
   tDemonstrativo := tDemonstrativo||'
';
   tDemonstrativo := tDemonstrativo||RPAD(' PONTUAÇÃO '            ,55,'.')||': '|| coalesce(iTotalPontos::varchar,'')||'  
';
   tDemonstrativo := tDemonstrativo||RPAD(' AREA P/ CALCULO '      ,55,'.')||': '|| coalesce(round( (rDadosIptu.areat*rDadosIptu.fracao)/100 ,2)::varchar,'') || '
';
   tDemonstrativo := tDemonstrativo||RPAD(' FRACAO '               ,55,'.')||': '|| coalesce(round( rDadosIptu.fracao,2)::varchar,'') || '% 
';
   tDemonstrativo := tDemonstrativo||RPAD(' ALIQUOTA '             ,55,'.')||': '|| coalesce(round( rDadosIptu.aliq,2)::varchar,'')   || '% 
';
   tDemonstrativo := tDemonstrativo||RPAD(' VALOR VENAL TERRENO '  ,55,'.')||': '|| coalesce(round( rDadosIptu.vvt,2)::varchar,'')    || ' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' VALOR VENAL EDIFIC '   ,55,'.')||': '|| coalesce(round( rDadosIptu.vvc,2)::varchar,'')    || ' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' VALOR VENAL TOTAL '   ,55,'.')||': '|| coalesce(round( rDadosIptu.vvc,2),0) + coalesce(round( rDadosIptu.vvt,2),0)    || ' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' AREA DO TERRENO '       ,55,'.')||': '|| coalesce(round( rDadosIptu.areat,2)::varchar,'')  || ' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' AREA EDIFICADA '       ,55,'.')||': '|| coalesce(round( nAreaEdificada,2)::varchar,'')  || ' 
';
   tDemonstrativo := tDemonstrativo||RPAD(' VALOR M2 DO TERRENO '  ,55,'.')||': '|| coalesce(round( rDadosIptu.vm2t,2)::varchar,'')   || ' 
';

 for rValores in select * from tmprecval inner join tabrec on receita = k02_codigo loop
     tDemonstrativo := tDemonstrativo||RPAD(' VALOR '||coalesce(rValores.k02_descr::varchar,'0'),55,'.')||': '|| coalesce(round(rValores.valor,2)::varchar,'0') || '
';
     nTotal         := nTotal + coalesce(rValores.valor,0);
 end loop;
 
 tSqlIsencao := 'SELECT k02_descr, j17_descr,  CASE
              WHEN iptucalhconf.j89_codhis IS NOT NULL THEN
                  (SELECT sum(x.j21_valor)
                   FROM iptucalv x
                   WHERE x.j21_anousu = iptucalv.j21_anousu
                     AND x.j21_matric = iptucalv.j21_matric
                     AND x.j21_receit = iptucalv.j21_receit
                     AND x.j21_codhis = iptucalhconf.j89_codhis)
                ELSE 0
            END AS j21_valorisen
      FROM iptucalv
      INNER JOIN iptucalh ON iptucalh.j17_codhis = j21_codhis
      LEFT JOIN iptucalhconf ON iptucalhconf.j89_codhispai = j21_codhis
      INNER JOIN tabrec ON tabrec.k02_codigo = j21_receit
      WHERE j21_matric = '||iMatricula||' AND j21_anousu = '||iAnousu||'
        AND j17_codhis NOT IN  (SELECT j89_codhis FROM iptucalhconf) ORDER BY iptucalh.j17_codhis';
    for rIsenc in execute tSqlIsencao
    loop
  if rIsenc.j21_valorisen is not null or rIsenc.j21_valorisen <> 0 then 
   tDemonstrativo := tDemonstrativo||RPAD(' VALOR  ISENCAO '||coalesce(rIsenc.j17_descr::varchar,''),55,'.')||': '|| coalesce(round(rIsenc.j21_valorisen,2)::varchar,'') || '
';
   nTotal         := nTotal + rIsenc.j21_valorisen;
  end if;
     end loop;

 tDemonstrativo := tDemonstrativo||RPAD(' TOTAL A PAGAR ',55,'.')||': '||coalesce(nTotal,0)||'  
';

 return tDemonstrativo;

end;
\$function$
SQLD
        );
    }
}
