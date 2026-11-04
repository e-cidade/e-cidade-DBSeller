<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23208FcVistoriasCharqueadas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
CREATE OR REPLACE FUNCTION public.fc_vistorias_charqueadas(integer)
 RETURNS character varying
 LANGUAGE plpgsql
AS \$function$
  declare

  v_vistoria             alias for $1;

  v_ativtipo             integer;
  v_numpre               integer;
  v_numpreinscr          integer;
  v_arrecant             integer;
  v_arrecad              integer;
  v_data                 date;

  v_diasgeral            integer;
  v_mesgeral             integer;
  v_parcial              boolean;

  v_achou                boolean default false;
  v_calculou             boolean default false;
  v_datavenc             date;
  v_y74_codsani          integer;
  v_y74_inscrsani        integer;
  v_y80_numcgm           integer;
  v_y69_numpre           integer;
  v_q81_recexe           integer;
  v_q92_hist             integer;
  v_q81_valexe           float8;
  v_q92_tipo             integer;
  v_y71_inscr            integer;
  v_q02_numcgm           integer;
  v_ativ                 integer;
  v_anousu               integer;
  v_ativprinc            integer;
  v_diasvenc             integer;

  iFormaCalculoAtividade integer;
  icodvencimento         integer default 0;

  lCalculaVistoriaMei    boolean;
  lContribuinteMei       boolean;

  lVistoriaSanitario     boolean;
  lVistoriaLocalizacao   boolean;

  v_tabmult              boolean;
  v_cadmult              boolean;
  v_area                 float8;
  v_multitab             float8 := 1;
  v_multicad             float8 := 1;
  v_valinflator          float8;
  v_base                 float8;
  v_valalancar           float8;
  v_text                 text default '';
  v_excedente            float8;
  v_quantativ            integer default 0;

  iFormulaCalculo        integer;

  v_claspont             integer default null;
  v_zonapont             integer;
  v_empreg               integer;
  v_empregpont           integer;
  v_areapont             integer;
  v_pontuacaogeral       integer;

  iorigemdados           integer;
  itipovist1             integer;
  itipovist2             integer;
  v_tipo_quant           integer;
  iinstit                integer;
  ddatavistoria          date;
  dDataAtual             date;

  lencontrouquantidadecalculo boolean default false;
  lraise                      boolean default true;

  lCalculaPorPorteAtividade   boolean default false;

  CALCULO_ATIVIDADE_PRINCIPAL   CONSTANT integer := 1;
  CALCULO_ATIVIDADE_MAIOR_VALOR CONSTANT integer := 2;

  TIPO_CALCULO_POR_ATIVIDADE    CONSTANT integer := 1;
  CALCULO_POR_PONTUACAO         CONSTANT integer := 2;

  v_record_vistsanitario  record;
  v_record_ativtipo       record;
  v_record_saniatividade  record;
  v_record_arrecad        record;

  rfinanceiro             record;

  begin


  --raise notice ' INICIO >>>>>> VISTORIA: % ', v_vistoria;

    lraise     := ( case when fc_getsession('db_debugon') is null then false else true end );
    iinstit    := fc_getsession('db_instit');
    dDataAtual := fc_getsession('DB_datausu');

    -- select substr(current_date,0,5)
    -- into v_anousu;

    select extract(year from y70_data), y70_data, y70_tipovist
           into v_anousu, ddatavistoria, iorigemdados
      from vistorias
     where y70_codvist = v_vistoria;

    if lraise is true then
      raise notice 'v_anousu: % ', v_anousu;
    end if;

-- verifica se a vistoria eh parcial ou geral, para montar a data de vencimento a ser gravada no arrecad --
    if iorigemdados = 1 then
      itipovist1 := 3;
      itipovist2 := 5;
    elsif iorigemdados = 2 then
      itipovist1 := 5;
      itipovist2 := 6;
    elsif iorigemdados = 10 then
      itipovist1 := 5;
    else
      return '21-erro ao selecionar origem dos dados (inscrição ou sanitário)!';
    end if;

     begin

     -- create temp table w_origemdados as select itipovist1 as q81_codigo union select itipovist2;



     drop table if exists w_origemdados;

      if exists(select 1 from vistorias where y70_codvist = v_vistoria and y70_tipovist = 10)
        then

            create temp table w_origemdados as select 5 as q81_codigo ; --union select itipovist2;

          --create temp table w_tipos_localizacao as select 5 as codigo;
          --create temp table w_tipos_sanitario   as select 5 as codigo;

        elsif  exists(select 1 from vistorias where y70_codvist = v_vistoria and y70_tipovist = 1)
          then 
            create temp table w_origemdados as select 3 as q81_codigo ;

        elsif     exists(select 1 from vistorias where y70_codvist = v_vistoria and y70_tipovist = 2)
          then
            create temp table w_origemdados as select 6 as q81_codigo;
        --  create temp table w_tipos_localizacao as select 3 as codigo;
        --  create temp table w_tipos_sanitario   as select 6 as codigo;
        end if;





      -- raise notice '>>>>>> VISTORIA origem: % ', iorigemdados;
      --return '';

       exception
         when duplicate_table then
         -- truncate w_origemdados;
          --insert into w_origemdados select itipovist1 as q81_codigo union select itipovist2;
     end;

    select y70_parcial
      from vistorias
     where y70_codvist = v_vistoria
      into v_parcial;

    if v_parcial is not null and v_parcial = false then
      if lraise is true then
        raise notice 'geral ';
      end if;
      select y77_diasgeral, y77_mesgeral, y70_data from tipovistorias
      inner join vistorias on y77_codtipo = y70_tipovist
      where y70_codvist = v_vistoria
      into  v_diasgeral,v_mesgeral,v_data;
      if v_diasgeral is null or v_diasgeral = 0 or v_mesgeral is null or v_mesgeral = 0 then
        return '01- tipo de vistoria sem dia ou mes para vencimento configurado!';
      end if;
      v_datavenc = v_anousu||'-'||v_mesgeral||'-'||v_diasgeral;
      if lraise is true then
        raise notice 'v_diasvenc: % - v_datavenc: %', v_diasvenc, v_datavenc;
      end if;
    else
      if lraise is true then
        raise notice 'parcial ';
      end if;
      select y77_dias, y70_data, y70_data, y77_diasgeral, y77_mesgeral
        into v_diasvenc, v_data, v_datavenc, v_diasgeral, v_mesgeral
        from tipovistorias
             inner join vistorias on y77_codtipo = y70_tipovist
       where y70_codvist = v_vistoria ;
      if v_diasvenc is null or v_diasvenc = 0 then
        if v_diasgeral is null then
          return '02- tipo de vistoria sem dias para vencimento configurado!';
        else
          v_datavenc = v_anousu||'-'||v_mesgeral||'-'||v_diasgeral;
        end if;

      end if;
      if lraise is true then
        raise notice 'v_diasvenc: % - v_datavenc: % - v_data: %', v_diasvenc, v_datavenc, v_data;
      end if;
--v_data = v_datavenc;
      if lraise is true then
        raise notice 'v_datavenc: %', v_datavenc;
      end if;
      if v_diasvenc is null then
        v_diasvenc = 0;
      end if;
      select v_datavenc + v_diasvenc
      into v_datavenc;
    end if;

--*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*--

    if lraise is true then
      raise notice 'v_datavenc: %', v_datavenc;
    end if;

    select y32_formvist,
           y32_utilizacalculoporteatividade,
           y32_calculavistoriamei
      into iFormulaCalculo,
           lCalculaPorPorteAtividade,
           lCalculaVistoriaMei
      from parfiscal
     where y32_instit = iinstit ;

    select q04_vbase
      into v_base
      from cissqn
     where cissqn.q04_anousu = v_anousu;

    if v_base = 0 or v_base is null then
      return '03- sem valor base cadastrado nos parametros ';
    end if;

    select distinct i02_valor
      into v_valinflator
      from cissqn
           inner join infla on q04_inflat = i02_codigo
     where cissqn.q04_anousu = v_anousu
       and date_part('y',i02_data) = v_anousu;

    if v_valinflator is null then
      v_valinflator = 1;
      --   return 'valor do inflator nao configurado corretamente';
    end if;

    select y74_codsani,y80_numcgm,y69_numpre
      into v_y74_codsani,v_y80_numcgm,v_y69_numpre
      from vistsanitario
           inner join sanitario on y74_codsani = y80_codsani
           left outer join vistorianumpre on y69_codvist = v_vistoria
     where y74_codvist = v_vistoria;

    if lraise is true then
      raise notice 'v_y74_codsani: % - v_vistoria: %', v_y74_codsani, v_vistoria;
    end if;

    if v_y74_codsani = 0 or v_y74_codsani is null then

      lVistoriaSanitario = false;

      select y71_inscr,q02_numcgm,y69_numpre
        into v_y71_inscr,v_q02_numcgm,v_y69_numpre
        from vistinscr
             inner join issbase on q02_inscr = y71_inscr
             left outer join vistorianumpre on y69_codvist = v_vistoria
       where y71_codvist = v_vistoria;

      if v_y71_inscr is null then
        lVistoriaLocalizacao = false;
      else
        lVistoriaLocalizacao = true;
      end if;

    else
      lVistoriaSanitario = true;
    end if;


    if exists(  select 1 from  issbase where q02_inscr= v_y71_inscr and  q02_formalocalvara = 2 ) then

         lVistoriaLocalizacao = false;
         lVistoriaSanitario = false;
    end if;

    if iorigemdados = 10  then 

         -- lVistoriaLocalizacao =  true;
        --  lVistoriaSanitario = true;
    end if;

   raise notice '>> Sanit %', lVistoriaSanitario;


    if lraise is true then
      raise notice 'lVistoriaSanitario: % - iFormulaCalculo: %', lVistoriaSanitario, iFormulaCalculo;
    end if;

    if lVistoriaLocalizacao = false and lVistoriaSanitario = false then
      return '10- calculo nao configurado para a vistoria numero ' || v_vistoria;
    end if;


    --
    -- Neste ponto ja esta definido qual o tipo de vistoria
    --   sanitario   - lVistoriaSanitario
    --   localizacao - lVistoriaLocalizacao
    --
    if iFormulaCalculo <> TIPO_CALCULO_POR_ATIVIDADE then
      return '20-procedimento nao preparado para calculo por forma diferente de 1 (normal)';
    end if;

    -- Se nao utilizar integracao com sanitario nao entrara aqui
    -- Necessario que tenha registros nas tabelas do sanitario
    if lVistoriaSanitario is true then

      v_achou = false;

      if lraise is true then
        raise notice 'v_y74_codsani: %', v_y74_codsani;
        raise notice 'antes for...';
      end if;

      if lCalculaPorPorteAtividade then

        -- Pega o primeiro tipo de calculo encontrado na tabela de ligação com o porte
        select q85_forcal
          into iFormaCalculoAtividade
          from saniatividade
               inner join sanitarioinscr      on sanitarioinscr.y18_codsani = saniatividade.y83_codsani
               inner join issbaseporte        on issbaseporte.q45_inscr     = sanitarioinscr.y18_inscr
               inner join tabativportetipcalc on tabativportetipcalc.q143_ativid = saniatividade.y83_ativ
                                             and tabativportetipcalc.q143_issporte = issbaseporte.q45_codporte
               inner join tipcalc             on tipcalc.q81_codigo = tabativportetipcalc.q143_tipcalc
               inner join cadcalc             on tipcalc.q81_cadcalc = cadcalc.q85_codigo
         where saniatividade.y83_codsani = v_y74_codsani
           and saniatividade.y83_dtfim is null
           and saniatividade.y83_ativprinc is true
           and tipcalc.q81_tipo in ( select q81_codigo from w_origemdados );

      else

        select min(q85_forcal)
          into iFormaCalculoAtividade
          from tipcalc
               inner join cadcalc        on tipcalc.q81_cadcalc = cadcalc.q85_codigo
               inner join ativtipo       on ativtipo.q80_tipcal = tipcalc.q81_codigo
               inner join saniatividade  on saniatividade.y83_ativ = ativtipo.q80_ativ
         where saniatividade.y83_codsani = v_y74_codsani
           and saniatividade.y83_dtfim is null
           and tipcalc.q81_tipo in ( select q81_codigo from w_origemdados );
      end if;

      if iFormaCalculoAtividade is null then
        return '11-sem forma de calculo encontrada (sani)!';
      end if;

      if lraise is true then
        raise notice 'iFormaCalculoAtividade: %', iFormaCalculoAtividade;
      end if;

      if iFormaCalculoAtividade = CALCULO_ATIVIDADE_PRINCIPAL then

        if lCalculaPorPorteAtividade then

          select y83_ativ
            into v_ativprinc
            from saniatividade
                 inner join sanitarioinscr      on sanitarioinscr.y18_codsani = saniatividade.y83_codsani
                 inner join issbaseporte        on issbaseporte.q45_inscr     = sanitarioinscr.y18_inscr
                 inner join tabativportetipcalc on tabativportetipcalc.q143_ativid = saniatividade.y83_ativ
                                               and tabativportetipcalc.q143_issporte = issbaseporte.q45_codporte
                 inner join tipcalc on tipcalc.q81_codigo = tabativportetipcalc.q143_tipcalc
           where y83_codsani = v_y74_codsani
             and y83_dtfim is null
             and q81_tipo in ( select q81_codigo from w_origemdados )
             and y83_ativprinc is true;
        else

          select q80_ativ
            into v_ativprinc
            from saniatividade
                 inner join ativtipo on saniatividade.y83_ativ = ativtipo.q80_ativ
                 inner join tipcalc on tipcalc.q81_codigo = ativtipo.q80_tipcal
           where y83_codsani = v_y74_codsani
             and y83_dtfim is null
             and q81_tipo in ( select q81_codigo from w_origemdados )
             and y83_ativprinc is true;
        end if;

        if v_ativprinc is not null then
          v_achou = true;
        end if;

      elsif iFormaCalculoAtividade = CALCULO_ATIVIDADE_MAIOR_VALOR then

        if lCalculaPorPorteAtividade then

          select y83_ativ
            into v_ativprinc
            from saniatividade
                 inner join sanitarioinscr      on sanitarioinscr.y18_codsani = saniatividade.y83_codsani
                 inner join issbaseporte        on issbaseporte.q45_inscr     = sanitarioinscr.y18_inscr
                 inner join tabativportetipcalc on tabativportetipcalc.q143_ativid = saniatividade.y83_ativ
                                               and tabativportetipcalc.q143_issporte = issbaseporte.q45_codporte
                 inner join tipcalc on tipcalc.q81_codigo = tabativportetipcalc.q143_tipcalc
           where y83_codsani = v_y74_codsani
             and y83_dtfim is null
             and q81_tipo in ( select q81_codigo from w_origemdados )
           order by q81_valexe desc
           limit 1;
        else

          select q80_ativ
            into v_ativprinc
            from saniatividade
                 inner join ativtipo on saniatividade.y83_ativ = ativtipo.q80_ativ
                 inner join tipcalc on tipcalc.q81_codigo = ativtipo.q80_tipcal
           where y83_codsani = v_y74_codsani
             and y83_dtfim is null
             and q81_tipo in ( select q81_codigo from w_origemdados )
           order by q81_valexe desc
           limit 1;
        end if;

        if v_ativprinc is not null then
          v_achou = true;
        end if;

      end if;

      if v_achou is false then
        return '04- nenhuma atividade com tipo 6 cadastrada';
      end if;
    end if;

    if lVistoriaLocalizacao = true then

      if lCalculaPorPorteAtividade then

        -- Pega o primeiro tipo de calculo encontrado na tabela de ligação com o porte
        -- Faz ligação com tipcalc para verificar se é sanitario ou localização
        select q85_forcal
          into iFormaCalculoAtividade
          from tabativ
               left join ativprinc            on ativprinc.q88_inscr = tabativ.q07_inscr
                                             and ativprinc.q88_seq = tabativ.q07_seq
               inner join issbaseporte        on issbaseporte.q45_inscr = tabativ.q07_inscr
               inner join tabativportetipcalc on tabativportetipcalc.q143_ativid = tabativ.q07_ativ
                                             and tabativportetipcalc.q143_issporte = issbaseporte.q45_codporte
               inner join tipcalc             on tipcalc.q81_codigo = tabativportetipcalc.q143_tipcalc
               inner join cadcalc             on tipcalc.q81_cadcalc = cadcalc.q85_codigo
         where q07_inscr = v_y71_inscr
           and tabativ.q07_datafi is null
           and tipcalc.q81_tipo in ( select q81_codigo from w_origemdados );
      else

        select min(q85_forcal)
          into iFormaCalculoAtividade
          from tipcalc
               inner join cadcalc        on tipcalc.q81_cadcalc = cadcalc.q85_codigo
               inner join ativtipo       on ativtipo.q80_tipcal = tipcalc.q81_codigo
               inner join tabativ        on tabativ.q07_ativ = ativtipo.q80_ativ
         where q07_inscr = v_y71_inscr and
        tabativ.q07_datafi is null and
        tipcalc.q81_tipo in ( select q81_codigo from w_origemdados );
      end if;

      if iFormaCalculoAtividade is null then

        select min(q85_forcal)
          into iFormaCalculoAtividade
          from issportetipo
               inner join issbaseporte on q45_inscr    = v_y71_inscr
                                      and q45_codporte = q41_codporte
               inner join tipcalc      on q41_codtipcalc = q81_codigo
               inner join cadcalc      on cadcalc.q85_codigo = tipcalc.q81_cadcalc
               inner join clasativ     on q82_classe = q41_codclasse
               inner join tabativ      on q82_ativ = q07_ativ
                                      and q07_inscr = v_y71_inscr
               inner join ativprinc    on ativprinc.q88_inscr = tabativ.q07_inscr
                                      and ativprinc.q88_seq = tabativ.q07_seq
         where q45_codporte = q41_codporte
           and q81_tipo in ( select q81_codigo from w_origemdados )
           and case
                 when q07_datafi is null then
                   true
                 else q07_datafi >= v_data
               end
           and q07_databx is null;


         raise notice 'DATA>>> : %' , v_y71_inscr;


         if   iFormaCalculoAtividade is null and  exists(select 1 from db_config where prefeitura is true and cgc = '88489786000101') then

           iFormaCalculoAtividade = 2;
         end if;



        if iFormaCalculoAtividade is null then 
          return '17-sem forma de calculo encontrada (inscr)!';
        end if;

      end if;

      if lraise is true then
        raise notice 'iFormaCalculoAtividade: %', iFormaCalculoAtividade;
      end if;

      -- pontuacao das classes
      select  q82_ativ,
              max(q25_pontuacao)
          into v_ativprinc,
               v_claspont
          from tabativ
               inner join clasativ   on q82_ativ = q07_ativ
               inner join classepont on q25_classe = q82_classe
         where q07_inscr = v_y71_inscr
           and case
                 when q07_datafi is null then true
                 else q07_datafi >= v_data
               end
           and q07_databx is null
         group by q82_ativ
         order by max(q25_pontuacao) desc
         limit 1;

      if v_claspont is not null then
      --  return '11-pontuacao da classe nao encontrada';

        -- pontuacao zona fiscal
        select q26_pontuacao
          into v_zonapont
          from zonapont
               inner join isszona on q26_zona = q35_zona
         where q35_inscr = v_y71_inscr;

        if v_zonapont is null then
          return '12-pontuacao da zona nao encontrada';
        end if;

        -- pontuacao empregados/area
        -- multiplicador para localizacao e sanitario
        select q30_quant,
               q30_area
          into v_empreg,
               v_area
          from issquant
         where issquant.q30_inscr = v_y71_inscr
           and issquant.q30_anousu = v_anousu;

        if v_empreg is null then

          select q30_quant,
                 q30_area
            into v_empreg,
                 v_area
            from issquant
           where issquant.q30_inscr = v_y71_inscr
             and issquant.q30_anousu = (v_anousu - 1);

          if v_empreg is null then

            select q30_quant,
                   q30_area
              into v_empreg,
                   v_area
              from issquant
             where issquant.q30_inscr = v_y71_inscr
               and issquant.q30_anousu = (v_anousu + 1);

            if v_empreg is null then

              insert into issquant
              select *
                from issquant
               where issquant.q30_inscr = v_y71_inscr
                 and issquant.q30_anousu = (v_anousu - 1);
            end if;
          end if;
        end if;

        -- pontuacao pelos empregados
        select q27_pontuacao
               into v_empregpont
          from empregpont
         where v_empreg >= q27_quantini
           and v_empreg <= q27_quantfim;

        if v_empregpont is null then
          return '13-pontuacao do numero de empregados nao encontrada';
        end if;


        if lraise is true then
          raise notice 'v_area: %', v_area;
        end if;

        -- pontuacao pela area
        select q28_pontuacao
          into v_areapont
          from areapont
         where v_area >= q28_quantini
           and v_area <= q28_quantfim;

        if v_areapont is null then
          return '14-pontuacao da area nao encontrada';
        end if;

        if lraise is true then
          raise notice 'v_claspont: % - v_zonapont: %  - v_empregpont: %  - v_areapont: %', v_claspont, v_zonapont, v_empregpont, v_areapont;
        end if;

        v_pontuacaogeral = v_claspont + v_zonapont + v_empregpont + v_areapont;

        if lraise is true then
          raise notice 'v_pontuacaogeral: %', v_pontuacaogeral;
        end if;

        select q81_codigo,
               q81_recexe,
               q92_hist,
               q81_valexe,
               q92_tipo
          into v_ativtipo,
               v_q81_recexe,
               v_q92_hist,
               v_q81_valexe,
               v_q92_tipo
          from tipcalc
              inner join tipcalcexe  on tipcalcexe.q83_anousu = v_anousu
                                    and tipcalcexe.q83_tipcalc = tipcalc.q81_codigo
              inner join cadvencdesc on q92_codigo = tipcalcexe.q83_codven
        where v_pontuacaogeral >= q81_qiexe
          and v_pontuacaogeral <= q81_qfexe
          and q81_tipo in ( select q81_codigo from w_origemdados );

      -- por ativtipo
      else

        if iFormaCalculoAtividade = CALCULO_ATIVIDADE_PRINCIPAL then

          if lCalculaPorPorteAtividade then

            select q07_ativ
              into v_ativprinc
              from tabativ
                   inner join ativprinc           on ativprinc.q88_inscr = tabativ.q07_inscr
                                                 and ativprinc.q88_seq = tabativ.q07_seq
                   inner join issbaseporte        on issbaseporte.q45_inscr = tabativ.q07_inscr
                   inner join tabativportetipcalc on tabativportetipcalc.q143_ativid = tabativ.q07_ativ
                                                 and tabativportetipcalc.q143_issporte = issbaseporte.q45_codporte
                   inner join tipcalc             on tipcalc.q81_codigo = tabativportetipcalc.q143_tipcalc
             where q07_inscr = v_y71_inscr
               and tabativ.q07_datafi is null
               and q81_tipo in ( select q81_codigo from w_origemdados );

          else

            select q80_ativ
              into v_ativprinc
              from tabativ
                   inner join ativprinc on ativprinc.q88_inscr = tabativ.q07_inscr and ativprinc.q88_seq = tabativ.q07_seq
                   inner join ativtipo on tabativ.q07_ativ = ativtipo.q80_ativ
                   inner join tipcalc on tipcalc.q81_codigo = ativtipo.q80_tipcal
             where q07_inscr = v_y71_inscr
               and tabativ.q07_datafi is null
               and q81_tipo in ( select q81_codigo from w_origemdados );
          end if;

          if v_ativprinc is not null then
            v_achou = true;
          else

            select q07_ativ
              into v_ativprinc
              from issportetipo
                   inner join issbaseporte on q45_inscr = v_y71_inscr
                                          and q45_codporte = q41_codporte
                   inner join tipcalc on q41_codtipcalc = q81_codigo
                   inner join cadcalc on cadcalc.q85_codigo = tipcalc.q81_cadcalc
                   inner join clasativ on q82_classe = q41_codclasse
                   inner join tabativ on q82_ativ = q07_ativ
                                     and q07_inscr = v_y71_inscr
                   inner join ativprinc on ativprinc.q88_inscr = tabativ.q07_inscr
                                       and ativprinc.q88_seq = tabativ.q07_seq
             where q45_codporte = q41_codporte
               and q81_tipo in ( select q81_codigo from w_origemdados )
               and case
                     when q07_datafi is null then
                       true
                     else q07_datafi >= v_data
                   end
               and q07_databx is null;

            if v_ativprinc is not null then
              v_achou = true;
            end if;

          end if;


        elsif iFormaCalculoAtividade = CALCULO_ATIVIDADE_MAIOR_VALOR then

          if lraise is true then
            raise notice 'forcal 2...';
          end if;

          if lCalculaPorPorteAtividade then

            select q07_ativ
              into v_ativprinc
              from tabativ
                   inner join issbaseporte        on issbaseporte.q45_inscr = tabativ.q07_inscr
                   inner join tabativportetipcalc on tabativportetipcalc.q143_ativid = tabativ.q07_ativ
                                                 and tabativportetipcalc.q143_issporte = issbaseporte.q45_codporte
                   inner join tipcalc             on tipcalc.q81_codigo = tabativportetipcalc.q143_tipcalc
             where q07_inscr = v_y71_inscr
               and tabativ.q07_datafi is null
               and tipcalc.q81_tipo in ( select q81_codigo from w_origemdados )
             order by q81_valexe desc
             limit 1;
          else

            select q80_ativ
              into v_ativprinc
              from tabativ
                   inner join ativtipo on tabativ.q07_ativ = ativtipo.q80_ativ
                   inner join tipcalc on tipcalc.q81_codigo = ativtipo.q80_tipcal
             where q07_inscr = v_y71_inscr
               and tabativ.q07_datafi is null
               and q81_tipo in ( select q81_codigo from w_origemdados )
             order by q81_valexe desc
             limit 1;
          end if;


raise notice ' >>>>> ATIVIDADE PRINC: %', v_ativprinc;

          if v_ativprinc is not null then
            v_achou = true;
          end if;

        end if;

        if v_achou is false then
          return '16 - sem atividade principal';
        end if;

        if lCalculaPorPorteAtividade then

          select tipcalc.q81_codigo
            into v_ativtipo
            from tabativ
                 inner join issbaseporte        on q45_inscr    = q07_inscr
                 inner join tabativportetipcalc on q143_issporte = q45_codporte
                                               and q143_ativid   = q07_ativ
                 inner join tipcalc on q81_codigo = q143_tipcalc
                 inner join cadcalc on cadcalc.q85_codigo = tipcalc.q81_cadcalc
           where q81_tipo in ( select q81_codigo
                                 from w_origemdados )
             and q07_inscr = v_y71_inscr
             and q07_ativ = v_ativprinc
             and case
                   when q07_datafi is null
                     then true
                   else q07_datafi >= v_data
                 end
             and q07_databx is null ;
        else

          select tipcalc.q81_codigo
            into v_ativtipo
            from ativtipo
                 inner join tabativ on q07_ativ = q80_ativ
                 inner join tipcalc on q80_tipcal = q81_codigo
                 inner join cadcalc on cadcalc.q85_codigo = tipcalc.q81_cadcalc
           where q81_tipo in ( select q81_codigo from w_origemdados )
             and q07_inscr = v_y71_inscr
             and case
                   when q07_datafi is null then true else q07_datafi >= v_data
                 end
             and q07_databx is null
             and q07_ativ = v_ativprinc;
        end if;

        if lraise is true then
          raise notice 'v_ativtipo: % - v_y71_inscr: % - v_ativprinc: %', v_ativtipo, v_y71_inscr, v_ativprinc;
        end if;

        if v_ativtipo is null then

          if lraise is true then
            raise notice 'ativtipo is null - data: % - inscr: % - ativprinc: %', v_data, v_y71_inscr, v_ativprinc;
          end if;

          select tipcalc.q81_codigo
            into v_ativtipo
            from issportetipo
                 inner join issbaseporte on q45_inscr = v_y71_inscr and q45_codporte = q41_codporte
                 inner join tipcalc on q41_codtipcalc = q81_codigo
                 inner join cadcalc on cadcalc.q85_codigo = tipcalc.q81_cadcalc
                 inner join clasativ on q82_classe = q41_codclasse
                 inner join tabativ on q82_ativ = q07_ativ and q07_inscr = v_y71_inscr
           where q45_codporte = q41_codporte
            and q81_tipo in ( select q81_codigo from w_origemdados )
            and case
                  when q07_datafi is null then
                    true
                  else q07_datafi >= v_data
                end
            and q07_databx is null
            and q82_ativ = v_ativprinc;

          if v_ativtipo is null then
            return '06-sem tipo de calculo configurado!';
          end if;
        end if;
      end if;
    end if;

    if v_y69_numpre = 0 or v_y69_numpre is null then

      select nextval('numpref_k03_numpre_seq')
        into v_numpre;

      insert into vistorianumpre values(v_vistoria,v_numpre);
    else

      v_numpre = v_y69_numpre;

      select k00_numpre
        into v_arrecant
        from arrecant
       where k00_numpre = v_numpre;

      if v_arrecant != 0 or v_arrecant is not null then
        return '07- vistoria ja paga ou cancelada ';
      end if;

      select k00_numpre
        into v_arrecad
        from arrecad
       where k00_numpre = v_numpre;

      if v_arrecad != 0 or v_arrecad is not null then
        delete from arrecad where k00_numpre = v_numpre;
      end if;
    end if;

      --se for por sanitario segue aqui
      if lVistoriaSanitario = true then

        for v_record_saniatividade in
          select y83_ativ,
                 y80_area,
                 q45_codporte
            from saniatividade
                 inner join sanitario      on sanitario.y80_codsani      = y83_codsani
                 left  join sanitarioinscr on sanitarioinscr.y18_codsani = y83_codsani
                 left  join issbaseporte   on issbaseporte.q45_inscr     = sanitarioinscr.y18_inscr
           where y83_codsani = v_y74_codsani
             and y83_ativ    = v_ativprinc
        loop

          select y18_inscr
            into v_y74_inscrsani
            from sanitarioinscr
           where y18_codsani = v_y74_codsani;

          if lCalculaVistoriaMei is false then

            lContribuinteMei := fc_verifica_contribuinte_mei(v_y74_inscrsani, dDataAtual);

            if lContribuinteMei then

              delete from vistorianumpre where y69_codvist = v_vistoria and y69_numpre = v_numpre;
              return '25 - CONTRIBUINTE OPTANTE PELO SIMPLES NACIONAL NA CATEGORIA MEI';
            end if;
          end if;

          if lraise is true then
            raise notice 'y83_ativ (2): % - anousu: %', v_record_saniatividade.y83_ativ, v_anousu;
          end if;

          if lCalculaPorPorteAtividade then

            if v_record_saniatividade.q45_codporte is null then
              return '24- Porte não encontrado no cadastro da empresa. Alvara sanitario : '||v_y74_codsani;
            end if;

            select q81_recexe,
                   q92_hist,
                   q81_valexe,
                   q92_tipo,
                   ( select distinct
                            q83_codven
                       from tipcalcexe
                      where q83_tipcalc = q81_codigo
                       and q83_anousu = v_anousu )
              into v_q81_recexe,
                   v_q92_hist,
                   v_q81_valexe,
                   v_q92_tipo,
                   icodvencimento
              from tabativportetipcalc
                   inner join tipcalc     on tipcalc.q81_codigo    = tabativportetipcalc.q143_tipcalc
                   inner join tipcalcexe  on tipcalcexe.q83_anousu = v_anousu
                                         and tipcalcexe.q83_tipcalc = tipcalc.q81_codigo
                   inner join cadvencdesc on q92_codigo = tipcalcexe.q83_codven
             where q143_ativid   = v_record_saniatividade.y83_ativ
               and q143_issporte = v_record_saniatividade.q45_codporte
               and v_record_saniatividade.y80_area between q81_qiexe and q81_qfexe
               and q81_tipo in ( select q81_codigo
                                   from w_origemdados );
          else

            select q81_recexe,
                   q92_hist,
                   q81_valexe,
                   q92_tipo,
                   ( select distinct
                            q83_codven
                       from tipcalcexe
                      where q83_tipcalc = q81_codigo
                       and q83_anousu = v_anousu )
              into v_q81_recexe,
                   v_q92_hist,
                   v_q81_valexe,
                   v_q92_tipo,
                   icodvencimento
              from ativtipo
                   inner join tipcalc     on q80_tipcal = q81_codigo
                   inner join tipcalcexe  on tipcalcexe.q83_anousu = v_anousu and tipcalcexe.q83_tipcalc = tipcalc.q81_codigo
                   inner join cadvencdesc on q92_codigo = tipcalcexe.q83_codven
             where q80_ativ = v_record_saniatividade.y83_ativ
               and ( select y80_area from sanitario where y80_codsani = v_y74_codsani) between q81_qiexe and q81_qfexe
               and q81_tipo in ( select q81_codigo from w_origemdados );
          end if;

 raise notice '>>>>> inserindo no arrecad... v_valalancar (2): % - icodvencimento: %', v_valalancar, icodvencimento;
 raise notice '>>>>>>>>>executando fc_gerafinanceiro(%,%,%,%,%,%,%)', v_numpre,v_valalancar,icodvencimento,v_y80_numcgm,v_data,v_q81_recexe,ddatavistoria;

          if v_q81_recexe is not null then


            v_valalancar = round(v_q81_valexe * v_valinflator * v_base * v_multitab * v_multicad,2);

            if lraise is true then
              raise notice 'inserindo no arrecad... v_valalancar (2): % - icodvencimento: %', v_valalancar, icodvencimento;
            end if;

            v_calculou = true;
            --
            -- inserindo por sanitario
            --
            -- funcao para gerar o financeiro
            --

            if icodvencimento is null then
              return '18-sem vencimento configurado para o exercicio!';
            end if;

            if lraise is true then
              raise notice 'executando fc_gerafinanceiro(%,%,%,%,%,%,%)', v_numpre,v_valalancar,icodvencimento,v_y80_numcgm,v_data,v_q81_recexe,ddatavistoria;
            end if;

            select *
              into rfinanceiro
              from fc_gerafinanceiro(v_numpre,v_valalancar,icodvencimento,v_y80_numcgm,v_data,v_q81_recexe,ddatavistoria);

            if v_y74_inscrsani is not null then

              if lraise is true then
                raise notice 'xxxxxxxxxxxxxxxxxxxxxxxxx: inscricao: %', v_y74_inscrsani;
              end if;

              select k00_numpre
                into v_numpreinscr
                from arreinscr
               where k00_numpre = v_numpre;

              if v_numpreinscr != 0 or v_numpreinscr is not null then
                delete from arreinscr where k00_numpre = v_numpreinscr;
              end if;

              insert into arreinscr (k00_numpre, k00_inscr)
                             values (v_numpre, v_y74_inscrsani);

            end if;
          end if;
        end loop;

        if v_calculou is true and v_y74_inscrsani != null then
          return '09-ok inscricao numero ' || v_y74_inscrsani;
        elsif v_calculou is true then
          return '09-ok inscricao numero ';
        else
          return '15-ocorreu algum erro durante o calculo (1)!!!';
        end if;
--fim do if do sanitario
--se for por inscricao segue aqui
      elsif lVistoriaLocalizacao = true then

        if lraise is true then
          raise notice 'acessou via inscricao... v_claspont: % - v_ativprinc: %', v_claspont, v_ativprinc;
        end if;

        if lCalculaVistoriaMei is false then

          lContribuinteMei := fc_verifica_contribuinte_mei(v_y71_inscr, dDataAtual);

          if lContribuinteMei then

            delete from vistorianumpre where y69_codvist = v_vistoria and y69_numpre = v_numpre;
            return '25 - CONTRIBUINTE OPTANTE PELO SIMPLES NACIONAL NA CATEGORIA MEI';
          end if;
        end if;

        if v_claspont is null then

          if lCalculaPorPorteAtividade then

            v_text = v_text || ' select q81_qiexe,q81_gera,q81_qfexe,q81_codigo,q81_uqtab,q81_uqcad,q07_ativ as q80_ativ 
';
            v_text = v_text || '   from tabativ 
';
            v_text = v_text || '        inner join issbaseporte        on q45_inscr    = q07_inscr 
';
            v_text = v_text || '        inner join tabativportetipcalc on q143_issporte = q45_codporte 
';
            v_text = v_text || '                                      and q143_ativid   = q07_ativ 
';
            v_text = v_text || '        inner join tipcalc on q81_codigo = q143_tipcalc 
';
            v_text = v_text || '  where q07_ativ = ' || v_ativprinc || '
';
            v_text = v_text || '    and q45_inscr = ' || v_y71_inscr || '
';
            v_text = v_text || '    and q81_tipo in ( select q81_codigo from w_origemdados )
';
            v_text = v_text || ' union 
';
            v_text = v_text || ' select q81_qiexe,q81_gera,q81_qfexe,q81_codigo,q81_uqtab,q81_uqcad,q82_ativ as q80_ativ 
';
            v_text = v_text || '   from issportetipo 
';
            v_text = v_text || '        inner join issbaseporte on q45_inscr          = ' || v_y71_inscr || '
';
            v_text = v_text || '        inner join tipcalc      on q41_codtipcalc     = q81_codigo 
';
            v_text = v_text || '        inner join cadcalc      on cadcalc.q85_codigo = tipcalc.q81_cadcalc 
';
            v_text = v_text || '        inner join clasativ     on q82_classe         = q41_codclasse 
';
            v_text = v_text || '  where q45_codporte = q41_codporte 
';
            v_text = v_text || '    and q81_tipo in ( select q81_codigo from w_origemdados ) 
';
            v_text = v_text || '    and q82_ativ = ' || v_ativprinc || '
';

          else

            v_text = v_text || ' select q81_qiexe,q81_gera,q81_qfexe,q81_codigo,q81_uqtab,q81_uqcad,q80_ativ ';
            v_text = v_text || '   from ativtipo ';
            v_text = v_text || '        inner join tipcalc on q81_codigo = q80_tipcal ';
            v_text = v_text || '  where q80_ativ = ' || v_ativprinc ;
            v_text = v_text || '    and q81_tipo in ( select q81_codigo from w_origemdados )';
            v_text = v_text || ' union ';
            v_text = v_text || ' select q81_qiexe,q81_gera,q81_qfexe,q81_codigo,q81_uqtab,q81_uqcad,q82_ativ as q80_ativ ';
            v_text = v_text || '   from issportetipo ';
            v_text = v_text || '        inner join issbaseporte on q45_inscr          = ' || v_y71_inscr;
            v_text = v_text || '        inner join tipcalc      on q41_codtipcalc     = q81_codigo ';
            v_text = v_text || '        inner join cadcalc      on cadcalc.q85_codigo = tipcalc.q81_cadcalc ';
            v_text = v_text || '        inner join clasativ     on q82_classe         = q41_codclasse ';
            v_text = v_text || '  where q45_codporte = q41_codporte ';
            v_text = v_text || '    and q81_tipo in ( select q81_codigo from w_origemdados ) ';
            v_text = v_text || '    and q82_ativ = ' || v_ativprinc;
          end if;

          select q60_campoutilcalc
            into v_tipo_quant
            from parissqn;

          if v_tipo_quant = 2 then
            select q30_quant from issquant into v_area where q30_inscr = v_y71_inscr and q30_anousu = v_anousu;
          else
            select q30_area from issquant into v_area where q30_inscr = v_y71_inscr and q30_anousu = v_anousu;
          end if;

          if lraise is true then
            raise notice ' 1- v_area - % inscr - % anousu - %',v_area,v_y71_inscr,v_anousu;
          end if;

          if v_area is null then
            v_area = 0;
          end if;

        else

          v_text = v_text || ' select q81_codigo, ';
          v_text = v_text || '        q81_recexe, ';
          v_text = v_text || '        q81_gera,   ';
          v_text = v_text || '        q92_hist,   ';
          v_text = v_text || '        q81_valexe, ';
          v_text = v_text || '        q92_tipo,   ';
          v_text = v_text || '        q81_qiexe,  ';
          v_text = v_text || '        q81_qfexe,  ';
          v_text = v_text || '        q81_uqtab,  ';
          v_text = v_text || '        q81_uqcad,  ';
          v_text = v_text || '        q80_ativ    ';
          v_text = v_text || '   from ativtipo      ';
          v_text = v_text || '        inner join tipcalc on tipcalc.q81_codigo = ativtipo.q80_tipcal ';
          v_text = v_text || '        inner join tipcalcexe on tipcalcexe.q83_anousu = ' || v_anousu || ' and tipcalcexe.q83_tipcalc = tipcalc.q81_codigo ';
          v_text = v_text || '        inner join cadcalc on q81_cadcalc = q85_codigo ';
          v_text = v_text || '        inner join cadvencdesc on q92_codigo = tipcalcexe.q83_codven ';
          v_text = v_text || ' where ' || v_pontuacaogeral || ' >= q81_qiexe and ';
          v_text = v_text ||              v_pontuacaogeral || ' <= q81_qfexe and ';
          v_text = v_text || '       q81_tipo in ( select q81_codigo from w_origemdados ) and ativtipo.q80_ativ = ' || v_ativprinc;

          v_area = v_pontuacaogeral;

          if lraise is true then
            raise notice 'v_area - % ',v_area;
          end if;

        end if;

        if lCalculaPorPorteAtividade then

          select array_upper(array_accum(distinct substr(q71_estrutural,2,5)::integer),1)
            into v_quantativ
            from tabativ
                 inner join issbaseporte        on q45_inscr    = q07_inscr
                 inner join tabativportetipcalc on q143_issporte = q45_codporte
                                               and q143_ativid   = q07_ativ
                 inner join tipcalc             on q81_codigo   = q143_tipcalc
                 inner join atividcnae          on atividcnae.q74_ativid        = tabativ.q07_ativ
                 inner join cnaeanalitica       on cnaeanalitica.q72_sequencial = atividcnae.q74_cnaeanalitica
                 inner join cnae                on cnae.q71_sequencial          = cnaeanalitica.q72_cnae
           where q07_inscr = v_y71_inscr
             and q81_tipo in (3,5,6)
             and (q07_datafi is null or q07_datafi >= current_date)
             and (q07_databx is null or q07_databx >= current_date);

        else

          select count(*)
            into v_quantativ
            from ( select distinct
                          q07_seq
                     from tabativ
                          inner join ativtipo on ativtipo.q80_ativ = tabativ.q07_ativ
                          inner join tipcalc on q81_codigo = q80_tipcal
                    where q81_tipo in (3,5,6)
                      and q07_inscr = v_y71_inscr
                      and (q07_datafi is null or q07_datafi >= current_date)
                      and (q07_databx is null or q07_databx >= current_date) ) as x;
        end if;

        if lraise is true then
          raise notice 'v_quantativ: %', v_quantativ;
        end if;

        if lraise is true then
          --raise notice 'v_text: %', v_text;
          raise notice 'antes do for...';
        end if;

        for v_record_ativtipo in execute v_text loop

          if lraise is true then
            raise notice 'dentro do for... vcalculou : % - tipcalc: % - area: % - qiexe: % - qfexe: %',v_calculou, v_record_ativtipo.q81_codigo, v_area, v_record_ativtipo.q81_qiexe, v_record_ativtipo.q81_qfexe;
          end if;

          if lraise is true then
            raise notice '   antes do if... area - % q81_qiexe - % q81_qfexe - %',v_area,v_record_ativtipo.q81_qiexe,v_record_ativtipo.q81_qfexe;
          end if;

          if v_area >= v_record_ativtipo.q81_qiexe and v_area <= v_record_ativtipo.q81_qfexe and 2 = v_record_ativtipo.q81_gera then

            lencontrouquantidadecalculo := true;
            if lraise is true then
              raise notice '      processando tipcalc: %', v_record_ativtipo.q81_codigo;
            end if;

            select q81_recexe,
                   q92_hist,
                   q81_valexe,
                   q92_tipo,
                   q81_excedenteativ,
                   (select distinct
                           q83_codven
                      from tipcalcexe
                     where q83_tipcalc = q81_codigo
                       and q83_anousu = v_anousu )
              into v_q81_recexe,
                   v_q92_hist,
                   v_q81_valexe,
                   v_q92_tipo,
                   v_excedente,
                   icodvencimento
              from tipcalc
                   inner join tipcalcexe  on tipcalcexe.q83_anousu = v_anousu
                                         and tipcalcexe.q83_tipcalc = tipcalc.q81_codigo
                   inner join cadcalc     on q81_cadcalc = q85_codigo
                   inner join cadvencdesc on q92_codigo = tipcalcexe.q83_codven
             where q81_codigo = v_record_ativtipo.q81_codigo;

            if icodvencimento is null then
              return '18-sem vencimento configurado para o exercicio!';
            end if;

            /*
               verifica se eh para calcular pela issquant ou tabativ
            */
            if v_record_ativtipo.q81_uqcad is true then
               select q30_mult from issquant into v_multicad where q30_inscr = v_y71_inscr and q30_anousu = v_anousu;
               if not found then
                  return '22 - inscricao sem multiplicador cadastrado na issquant';
               end if;

               if v_multicad is null or v_multicad = 0 then
                  v_multicad := 1;
               end if;
            end if;

            if v_record_ativtipo.q81_uqtab is true then
               select q07_quant
               into v_multitab
               from tabativ
               where q07_inscr = v_y71_inscr and q07_ativ = v_record_ativtipo.q80_ativ and q07_databx is null;
               if not found then
                  return '23 - inscricao sem atividade cadastrada na tabativ ou atividade baixada';
               end if;

               if v_multitab is null or v_multitab = 0 then
                  v_multitab := 1;
               end if;
            end if;

            v_valalancar = round(v_q81_valexe * v_valinflator * v_base * v_multicad * v_multitab,2);
            v_calculou = true;

            if lraise is true then
              raise notice 'v_valalancar (1): % - k00_numpre: % - v_valinflator: % - v_base: %', v_valalancar, v_numpre, v_valinflator, v_base;
            end if;

            if v_excedente > 0 then
              if lraise is true then
                raise notice 'valor antes: %', v_valalancar;
              end if;
              v_valalancar = v_valalancar + (v_valalancar * v_excedente * (v_quantativ - 1));
              if lraise is true then
                raise notice 'valor depois: %', v_valalancar;
              end if;
            end if;

            --
            -- inserindo pelo issbase
            --
            -- funcao para gerar o financeiro
            --
            select *
              into rfinanceiro
              from fc_gerafinanceiro(v_numpre,v_valalancar,icodvencimento,v_q02_numcgm,v_data,v_q81_recexe,ddatavistoria);

            select k00_numpre
              into v_numpreinscr
              from arreinscr
             where k00_numpre = v_numpre;

            if v_numpreinscr != 0 or v_numpreinscr is not null then
              delete from arreinscr where k00_numpre = v_numpreinscr;
            end if;
            insert into arreinscr (k00_numpre, k00_inscr)
                           values (v_numpre, v_y71_inscr);
          end if;

        end loop;


        if lencontrouquantidadecalculo is false then
          return '24-area/empregados nao enquadrada no tipo de calculo.';
        end if;

        if lraise is true then
          raise notice 'fora do for... v_calculou: %', v_calculou;
        end if;

        if v_calculou is true then
          return '09-ok inscricao numero ' || v_y71_inscr;
        else
          return '19-ocorreu algum erro durante o calculo (2)!!!';
        end if;

      end if;
  end;

\$function$
;


SQL;

    DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = <<<SQL

CREATE OR REPLACE FUNCTION public.fc_vistorias_charqueadas(integer)
 RETURNS character varying
 LANGUAGE plpgsql
AS \$function$
  declare

  v_vistoria             alias for $1;

  v_ativtipo             integer;
  v_numpre               integer;
  v_numpreinscr          integer;
  v_arrecant             integer;
  v_arrecad              integer;
  v_data                 date;

  v_diasgeral            integer;
  v_mesgeral             integer;
  v_parcial              boolean;

  v_achou                boolean default false;
  v_calculou             boolean default false;
  v_datavenc             date;
  v_y74_codsani          integer;
  v_y74_inscrsani        integer;
  v_y80_numcgm           integer;
  v_y69_numpre           integer;
  v_q81_recexe           integer;
  v_q92_hist             integer;
  v_q81_valexe           float8;
  v_q92_tipo             integer;
  v_y71_inscr            integer;
  v_q02_numcgm           integer;
  v_ativ                 integer;
  v_anousu               integer;
  v_ativprinc            integer;
  v_diasvenc             integer;

  iFormaCalculoAtividade integer;
  icodvencimento         integer default 0;

  lCalculaVistoriaMei    boolean;
  lContribuinteMei       boolean;

  lVistoriaSanitario     boolean;
  lVistoriaLocalizacao   boolean;

  v_tabmult              boolean;
  v_cadmult              boolean;
  v_area                 float8;
  v_multitab             float8 := 1;
  v_multicad             float8 := 1;
  v_valinflator          float8;
  v_base                 float8;
  v_valalancar           float8;
  v_text                 text default '';
  v_excedente            float8;
  v_quantativ            integer default 0;

  iFormulaCalculo        integer;

  v_claspont             integer default null;
  v_zonapont             integer;
  v_empreg               integer;
  v_empregpont           integer;
  v_areapont             integer;
  v_pontuacaogeral       integer;

  iorigemdados           integer;
  itipovist1             integer;
  itipovist2             integer;
  v_tipo_quant           integer;
  iinstit                integer;
  ddatavistoria          date;
  dDataAtual             date;

  lencontrouquantidadecalculo boolean default false;
  lraise                      boolean default true;

  lCalculaPorPorteAtividade   boolean default false;

  CALCULO_ATIVIDADE_PRINCIPAL   CONSTANT integer := 1;
  CALCULO_ATIVIDADE_MAIOR_VALOR CONSTANT integer := 2;

  TIPO_CALCULO_POR_ATIVIDADE    CONSTANT integer := 1;
  CALCULO_POR_PONTUACAO         CONSTANT integer := 2;

  v_record_vistsanitario  record;
  v_record_ativtipo       record;
  v_record_saniatividade  record;
  v_record_arrecad        record;

  rfinanceiro             record;

  begin


  --raise notice ' INICIO >>>>>> VISTORIA: % ', v_vistoria;

    lraise     := ( case when fc_getsession('db_debugon') is null then false else true end );
    iinstit    := fc_getsession('db_instit');
    dDataAtual := fc_getsession('DB_datausu');

    -- select substr(current_date,0,5)
    -- into v_anousu;

    select extract(year from y70_data), y70_data, y70_tipovist
           into v_anousu, ddatavistoria, iorigemdados
      from vistorias
     where y70_codvist = v_vistoria;

    if lraise is true then
      raise notice 'v_anousu: % ', v_anousu;
    end if;

-- verifica se a vistoria eh parcial ou geral, para montar a data de vencimento a ser gravada no arrecad --
    if iorigemdados = 1 then
      itipovist1 := 3;
      itipovist2 := 5;
    elsif iorigemdados = 2 then
      itipovist1 := 5;
      itipovist2 := 6;
    elsif iorigemdados = 10 then
      itipovist1 := 5;
    else
      return '21-erro ao selecionar origem dos dados (inscrição ou sanitário)!';
    end if;

     begin

     -- create temp table w_origemdados as select itipovist1 as q81_codigo union select itipovist2;



     drop table if exists w_origemdados;

      if exists(select 1 from vistorias where y70_codvist = v_vistoria and y70_tipovist = 10)
        then

            create temp table w_origemdados as select 5 as q81_codigo ; --union select itipovist2;

          --create temp table w_tipos_localizacao as select 5 as codigo;
          --create temp table w_tipos_sanitario   as select 5 as codigo;

        elsif  exists(select 1 from vistorias where y70_codvist = v_vistoria and y70_tipovist = 1)
          then 
            create temp table w_origemdados as select 3 as q81_codigo ;

        elsif     exists(select 1 from vistorias where y70_codvist = v_vistoria and y70_tipovist = 2)
          then
            create temp table w_origemdados as select 6 as q81_codigo;
        --  create temp table w_tipos_localizacao as select 3 as codigo;
        --  create temp table w_tipos_sanitario   as select 6 as codigo;
        end if;





      -- raise notice '>>>>>> VISTORIA origem: % ', iorigemdados;
      --return '';

       exception
         when duplicate_table then
         -- truncate w_origemdados;
          --insert into w_origemdados select itipovist1 as q81_codigo union select itipovist2;
     end;

    select y70_parcial
      from vistorias
     where y70_codvist = v_vistoria
      into v_parcial;

    if v_parcial is not null and v_parcial = false then
      if lraise is true then
        raise notice 'geral ';
      end if;
      select y77_diasgeral, y77_mesgeral, y70_data from tipovistorias
      inner join vistorias on y77_codtipo = y70_tipovist
      where y70_codvist = v_vistoria
      into  v_diasgeral,v_mesgeral,v_data;
      if v_diasgeral is null or v_diasgeral = 0 or v_mesgeral is null or v_mesgeral = 0 then
        return '01- tipo de vistoria sem dia ou mes para vencimento configurado!';
      end if;
      v_datavenc = v_anousu||'-'||v_mesgeral||'-'||v_diasgeral;
      if lraise is true then
        raise notice 'v_diasvenc: % - v_datavenc: %', v_diasvenc, v_datavenc;
      end if;
    else
      if lraise is true then
        raise notice 'parcial ';
      end if;
      select y77_dias, y70_data, y70_data, y77_diasgeral, y77_mesgeral
        into v_diasvenc, v_data, v_datavenc, v_diasgeral, v_mesgeral
        from tipovistorias
             inner join vistorias on y77_codtipo = y70_tipovist
       where y70_codvist = v_vistoria ;
      if v_diasvenc is null or v_diasvenc = 0 then
        if v_diasgeral is null then
          return '02- tipo de vistoria sem dias para vencimento configurado!';
        else
          v_datavenc = v_anousu||'-'||v_mesgeral||'-'||v_diasgeral;
        end if;

      end if;
      if lraise is true then
        raise notice 'v_diasvenc: % - v_datavenc: % - v_data: %', v_diasvenc, v_datavenc, v_data;
      end if;
--v_data = v_datavenc;
      if lraise is true then
        raise notice 'v_datavenc: %', v_datavenc;
      end if;
      if v_diasvenc is null then
        v_diasvenc = 0;
      end if;
      select v_datavenc + v_diasvenc
      into v_datavenc;
    end if;

--*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*--

    if lraise is true then
      raise notice 'v_datavenc: %', v_datavenc;
    end if;

    select y32_formvist,
           y32_utilizacalculoporteatividade,
           y32_calculavistoriamei
      into iFormulaCalculo,
           lCalculaPorPorteAtividade,
           lCalculaVistoriaMei
      from parfiscal
     where y32_instit = iinstit ;

    select q04_vbase
      into v_base
      from cissqn
     where cissqn.q04_anousu = v_anousu;

    if v_base = 0 or v_base is null then
      return '03- sem valor base cadastrado nos parametros ';
    end if;

    select distinct i02_valor
      into v_valinflator
      from cissqn
           inner join infla on q04_inflat = i02_codigo
     where cissqn.q04_anousu = v_anousu
       and date_part('y',i02_data) = v_anousu;

    if v_valinflator is null then
      v_valinflator = 1;
      --   return 'valor do inflator nao configurado corretamente';
    end if;

    select y74_codsani,y80_numcgm,y69_numpre
      into v_y74_codsani,v_y80_numcgm,v_y69_numpre
      from vistsanitario
           inner join sanitario on y74_codsani = y80_codsani
           left outer join vistorianumpre on y69_codvist = v_vistoria
     where y74_codvist = v_vistoria;

    if lraise is true then
      raise notice 'v_y74_codsani: % - v_vistoria: %', v_y74_codsani, v_vistoria;
    end if;

    if v_y74_codsani = 0 or v_y74_codsani is null then

      lVistoriaSanitario = false;

      select y71_inscr,q02_numcgm,y69_numpre
        into v_y71_inscr,v_q02_numcgm,v_y69_numpre
        from vistinscr
             inner join issbase on q02_inscr = y71_inscr
             left outer join vistorianumpre on y69_codvist = v_vistoria
       where y71_codvist = v_vistoria;

      if v_y71_inscr is null then
        lVistoriaLocalizacao = false;
      else
        lVistoriaLocalizacao = true;
      end if;

    else
      lVistoriaSanitario = true;
    end if;


    if exists(  select 1 from  issbase where q02_inscr= v_y71_inscr and  q02_formalocalvara = 2 ) then

         lVistoriaLocalizacao = false;
         lVistoriaSanitario = false;
    end if;

    if iorigemdados = 10  then 

         -- lVistoriaLocalizacao =  true;
        --  lVistoriaSanitario = true;
    end if;

   raise notice '>> Sanit %', lVistoriaSanitario;


    if lraise is true then
      raise notice 'lVistoriaSanitario: % - iFormulaCalculo: %', lVistoriaSanitario, iFormulaCalculo;
    end if;

    if lVistoriaLocalizacao = false and lVistoriaSanitario = false then
      return '10- calculo nao configurado para a vistoria numero ' || v_vistoria;
    end if;


    --
    -- Neste ponto ja esta definido qual o tipo de vistoria
    --   sanitario   - lVistoriaSanitario
    --   localizacao - lVistoriaLocalizacao
    --
    if iFormulaCalculo <> TIPO_CALCULO_POR_ATIVIDADE then
      return '20-procedimento nao preparado para calculo por forma diferente de 1 (normal)';
    end if;

    -- Se nao utilizar integracao com sanitario nao entrara aqui
    -- Necessario que tenha registros nas tabelas do sanitario
    if lVistoriaSanitario is true then

      v_achou = false;

      if lraise is true then
        raise notice 'v_y74_codsani: %', v_y74_codsani;
        raise notice 'antes for...';
      end if;

      if lCalculaPorPorteAtividade then

        -- Pega o primeiro tipo de calculo encontrado na tabela de ligação com o porte
        select q85_forcal
          into iFormaCalculoAtividade
          from saniatividade
               inner join sanitarioinscr      on sanitarioinscr.y18_codsani = saniatividade.y83_codsani
               inner join issbaseporte        on issbaseporte.q45_inscr     = sanitarioinscr.y18_inscr
               inner join tabativportetipcalc on tabativportetipcalc.q143_ativid = saniatividade.y83_ativ
                                             and tabativportetipcalc.q143_issporte = issbaseporte.q45_codporte
               inner join tipcalc             on tipcalc.q81_codigo = tabativportetipcalc.q143_tipcalc
               inner join cadcalc             on tipcalc.q81_cadcalc = cadcalc.q85_codigo
         where saniatividade.y83_codsani = v_y74_codsani
           and saniatividade.y83_dtfim is null
           and saniatividade.y83_ativprinc is true
           and tipcalc.q81_tipo in ( select q81_codigo from w_origemdados );

      else

        select min(q85_forcal)
          into iFormaCalculoAtividade
          from tipcalc
               inner join cadcalc        on tipcalc.q81_cadcalc = cadcalc.q85_codigo
               inner join ativtipo       on ativtipo.q80_tipcal = tipcalc.q81_codigo
               inner join saniatividade  on saniatividade.y83_ativ = ativtipo.q80_ativ
         where saniatividade.y83_codsani = v_y74_codsani
           and saniatividade.y83_dtfim is null
           and tipcalc.q81_tipo in ( select q81_codigo from w_origemdados );
      end if;

      if iFormaCalculoAtividade is null then
        return '11-sem forma de calculo encontrada (sani)!';
      end if;

      if lraise is true then
        raise notice 'iFormaCalculoAtividade: %', iFormaCalculoAtividade;
      end if;

      if iFormaCalculoAtividade = CALCULO_ATIVIDADE_PRINCIPAL then

        if lCalculaPorPorteAtividade then

          select y83_ativ
            into v_ativprinc
            from saniatividade
                 inner join sanitarioinscr      on sanitarioinscr.y18_codsani = saniatividade.y83_codsani
                 inner join issbaseporte        on issbaseporte.q45_inscr     = sanitarioinscr.y18_inscr
                 inner join tabativportetipcalc on tabativportetipcalc.q143_ativid = saniatividade.y83_ativ
                                               and tabativportetipcalc.q143_issporte = issbaseporte.q45_codporte
                 inner join tipcalc on tipcalc.q81_codigo = tabativportetipcalc.q143_tipcalc
           where y83_codsani = v_y74_codsani
             and y83_dtfim is null
             and q81_tipo in ( select q81_codigo from w_origemdados )
             and y83_ativprinc is true;
        else

          select q80_ativ
            into v_ativprinc
            from saniatividade
                 inner join ativtipo on saniatividade.y83_ativ = ativtipo.q80_ativ
                 inner join tipcalc on tipcalc.q81_codigo = ativtipo.q80_tipcal
           where y83_codsani = v_y74_codsani
             and y83_dtfim is null
             and q81_tipo in ( select q81_codigo from w_origemdados )
             and y83_ativprinc is true;
        end if;

        if v_ativprinc is not null then
          v_achou = true;
        end if;

      elsif iFormaCalculoAtividade = CALCULO_ATIVIDADE_MAIOR_VALOR then

        if lCalculaPorPorteAtividade then

          select y83_ativ
            into v_ativprinc
            from saniatividade
                 inner join sanitarioinscr      on sanitarioinscr.y18_codsani = saniatividade.y83_codsani
                 inner join issbaseporte        on issbaseporte.q45_inscr     = sanitarioinscr.y18_inscr
                 inner join tabativportetipcalc on tabativportetipcalc.q143_ativid = saniatividade.y83_ativ
                                               and tabativportetipcalc.q143_issporte = issbaseporte.q45_codporte
                 inner join tipcalc on tipcalc.q81_codigo = tabativportetipcalc.q143_tipcalc
           where y83_codsani = v_y74_codsani
             and y83_dtfim is null
             and q81_tipo in ( select q81_codigo from w_origemdados )
           order by q81_valexe desc
           limit 1;
        else

          select q80_ativ
            into v_ativprinc
            from saniatividade
                 inner join ativtipo on saniatividade.y83_ativ = ativtipo.q80_ativ
                 inner join tipcalc on tipcalc.q81_codigo = ativtipo.q80_tipcal
           where y83_codsani = v_y74_codsani
             and y83_dtfim is null
             and q81_tipo in ( select q81_codigo from w_origemdados )
           order by q81_valexe desc
           limit 1;
        end if;

        if v_ativprinc is not null then
          v_achou = true;
        end if;

      end if;

      if v_achou is false then
        return '04- nenhuma atividade com tipo 6 cadastrada';
      end if;
    end if;

    if lVistoriaLocalizacao = true then

      if lCalculaPorPorteAtividade then

        -- Pega o primeiro tipo de calculo encontrado na tabela de ligação com o porte
        -- Faz ligação com tipcalc para verificar se é sanitario ou localização
        select q85_forcal
          into iFormaCalculoAtividade
          from tabativ
               left join ativprinc            on ativprinc.q88_inscr = tabativ.q07_inscr
                                             and ativprinc.q88_seq = tabativ.q07_seq
               inner join issbaseporte        on issbaseporte.q45_inscr = tabativ.q07_inscr
               inner join tabativportetipcalc on tabativportetipcalc.q143_ativid = tabativ.q07_ativ
                                             and tabativportetipcalc.q143_issporte = issbaseporte.q45_codporte
               inner join tipcalc             on tipcalc.q81_codigo = tabativportetipcalc.q143_tipcalc
               inner join cadcalc             on tipcalc.q81_cadcalc = cadcalc.q85_codigo
         where q07_inscr = v_y71_inscr
           and tabativ.q07_datafi is null
           and tipcalc.q81_tipo in ( select q81_codigo from w_origemdados );
      else

        select min(q85_forcal)
          into iFormaCalculoAtividade
          from tipcalc
               inner join cadcalc        on tipcalc.q81_cadcalc = cadcalc.q85_codigo
               inner join ativtipo       on ativtipo.q80_tipcal = tipcalc.q81_codigo
               inner join tabativ        on tabativ.q07_ativ = ativtipo.q80_ativ
         where q07_inscr = v_y71_inscr and
        tabativ.q07_datafi is null and
        tipcalc.q81_tipo in ( select q81_codigo from w_origemdados );
      end if;

      if iFormaCalculoAtividade is null then

        select min(q85_forcal)
          into iFormaCalculoAtividade
          from issportetipo
               inner join issbaseporte on q45_inscr    = v_y71_inscr
                                      and q45_codporte = q41_codporte
               inner join tipcalc      on q41_codtipcalc = q81_codigo
               inner join cadcalc      on cadcalc.q85_codigo = tipcalc.q81_cadcalc
               inner join clasativ     on q82_classe = q41_codclasse
               inner join tabativ      on q82_ativ = q07_ativ
                                      and q07_inscr = v_y71_inscr
               inner join ativprinc    on ativprinc.q88_inscr = tabativ.q07_inscr
                                      and ativprinc.q88_seq = tabativ.q07_seq
         where q45_codporte = q41_codporte
           and q81_tipo in ( select q81_codigo from w_origemdados )
           and case
                 when q07_datafi is null then
                   true
                 else q07_datafi >= v_data
               end
           and q07_databx is null;


         raise notice 'DATA>>> : %' , v_y71_inscr;


         if   iFormaCalculoAtividade is null and  exists(select 1 from db_config where prefeitura is true and cgc = '88489786000101') then

           iFormaCalculoAtividade = 2;
         end if;



        if iFormaCalculoAtividade is null then 
          return '17-sem forma de calculo encontrada (inscr)!';
        end if;

      end if;

      if lraise is true then
        raise notice 'iFormaCalculoAtividade: %', iFormaCalculoAtividade;
      end if;

      -- pontuacao das classes
      select  q82_ativ,
              max(q25_pontuacao)
          into v_ativprinc,
               v_claspont
          from tabativ
               inner join clasativ   on q82_ativ = q07_ativ
               inner join classepont on q25_classe = q82_classe
         where q07_inscr = v_y71_inscr
           and case
                 when q07_datafi is null then true
                 else q07_datafi >= v_data
               end
           and q07_databx is null
         group by q82_ativ
         order by max(q25_pontuacao) desc
         limit 1;

      if v_claspont is not null then
      --  return '11-pontuacao da classe nao encontrada';

        -- pontuacao zona fiscal
        select q26_pontuacao
          into v_zonapont
          from zonapont
               inner join isszona on q26_zona = q35_zona
         where q35_inscr = v_y71_inscr;

        if v_zonapont is null then
          return '12-pontuacao da zona nao encontrada';
        end if;

        -- pontuacao empregados/area
        -- multiplicador para localizacao e sanitario
        select q30_quant,
               q30_area
          into v_empreg,
               v_area
          from issquant
         where issquant.q30_inscr = v_y71_inscr
           and issquant.q30_anousu = v_anousu;

        if v_empreg is null then

          select q30_quant,
                 q30_area
            into v_empreg,
                 v_area
            from issquant
           where issquant.q30_inscr = v_y71_inscr
             and issquant.q30_anousu = (v_anousu - 1);

          if v_empreg is null then

            select q30_quant,
                   q30_area
              into v_empreg,
                   v_area
              from issquant
             where issquant.q30_inscr = v_y71_inscr
               and issquant.q30_anousu = (v_anousu + 1);

            if v_empreg is null then

              insert into issquant
              select *
                from issquant
               where issquant.q30_inscr = v_y71_inscr
                 and issquant.q30_anousu = (v_anousu - 1);
            end if;
          end if;
        end if;

        -- pontuacao pelos empregados
        select q27_pontuacao
               into v_empregpont
          from empregpont
         where v_empreg >= q27_quantini
           and v_empreg <= q27_quantfim;

        if v_empregpont is null then
          return '13-pontuacao do numero de empregados nao encontrada';
        end if;


        if lraise is true then
          raise notice 'v_area: %', v_area;
        end if;

        -- pontuacao pela area
        select q28_pontuacao
          into v_areapont
          from areapont
         where v_area >= q28_quantini
           and v_area <= q28_quantfim;

        if v_areapont is null then
          return '14-pontuacao da area nao encontrada';
        end if;

        if lraise is true then
          raise notice 'v_claspont: % - v_zonapont: %  - v_empregpont: %  - v_areapont: %', v_claspont, v_zonapont, v_empregpont, v_areapont;
        end if;

        v_pontuacaogeral = v_claspont + v_zonapont + v_empregpont + v_areapont;

        if lraise is true then
          raise notice 'v_pontuacaogeral: %', v_pontuacaogeral;
        end if;

        select q81_codigo,
               q81_recexe,
               q92_hist,
               q81_valexe,
               q92_tipo
          into v_ativtipo,
               v_q81_recexe,
               v_q92_hist,
               v_q81_valexe,
               v_q92_tipo
          from tipcalc
              inner join tipcalcexe  on tipcalcexe.q83_anousu = v_anousu
                                    and tipcalcexe.q83_tipcalc = tipcalc.q81_codigo
              inner join cadvencdesc on q92_codigo = tipcalcexe.q83_codven
        where v_pontuacaogeral >= q81_qiexe
          and v_pontuacaogeral <= q81_qfexe
          and q81_tipo in ( select q81_codigo from w_origemdados );

      -- por ativtipo
      else

        if iFormaCalculoAtividade = CALCULO_ATIVIDADE_PRINCIPAL then

          if lCalculaPorPorteAtividade then

            select q07_ativ
              into v_ativprinc
              from tabativ
                   inner join ativprinc           on ativprinc.q88_inscr = tabativ.q07_inscr
                                                 and ativprinc.q88_seq = tabativ.q07_seq
                   inner join issbaseporte        on issbaseporte.q45_inscr = tabativ.q07_inscr
                   inner join tabativportetipcalc on tabativportetipcalc.q143_ativid = tabativ.q07_ativ
                                                 and tabativportetipcalc.q143_issporte = issbaseporte.q45_codporte
                   inner join tipcalc             on tipcalc.q81_codigo = tabativportetipcalc.q143_tipcalc
             where q07_inscr = v_y71_inscr
               and tabativ.q07_datafi is null
               and q81_tipo in ( select q81_codigo from w_origemdados );

          else

            select q80_ativ
              into v_ativprinc
              from tabativ
                   inner join ativprinc on ativprinc.q88_inscr = tabativ.q07_inscr and ativprinc.q88_seq = tabativ.q07_seq
                   inner join ativtipo on tabativ.q07_ativ = ativtipo.q80_ativ
                   inner join tipcalc on tipcalc.q81_codigo = ativtipo.q80_tipcal
             where q07_inscr = v_y71_inscr
               and tabativ.q07_datafi is null
               and q81_tipo in ( select q81_codigo from w_origemdados );
          end if;

          if v_ativprinc is not null then
            v_achou = true;
          else

            select q07_ativ
              into v_ativprinc
              from issportetipo
                   inner join issbaseporte on q45_inscr = v_y71_inscr
                                          and q45_codporte = q41_codporte
                   inner join tipcalc on q41_codtipcalc = q81_codigo
                   inner join cadcalc on cadcalc.q85_codigo = tipcalc.q81_cadcalc
                   inner join clasativ on q82_classe = q41_codclasse
                   inner join tabativ on q82_ativ = q07_ativ
                                     and q07_inscr = v_y71_inscr
                   inner join ativprinc on ativprinc.q88_inscr = tabativ.q07_inscr
                                       and ativprinc.q88_seq = tabativ.q07_seq
             where q45_codporte = q41_codporte
               and q81_tipo in ( select q81_codigo from w_origemdados )
               and case
                     when q07_datafi is null then
                       true
                     else q07_datafi >= v_data
                   end
               and q07_databx is null;

            if v_ativprinc is not null then
              v_achou = true;
            end if;

          end if;


        elsif iFormaCalculoAtividade = CALCULO_ATIVIDADE_MAIOR_VALOR then

          if lraise is true then
            raise notice 'forcal 2...';
          end if;

          if lCalculaPorPorteAtividade then

            select q07_ativ
              into v_ativprinc
              from tabativ
                   inner join issbaseporte        on issbaseporte.q45_inscr = tabativ.q07_inscr
                   inner join tabativportetipcalc on tabativportetipcalc.q143_ativid = tabativ.q07_ativ
                                                 and tabativportetipcalc.q143_issporte = issbaseporte.q45_codporte
                   inner join tipcalc             on tipcalc.q81_codigo = tabativportetipcalc.q143_tipcalc
             where q07_inscr = v_y71_inscr
               and tabativ.q07_datafi is null
               and tipcalc.q81_tipo in ( select q81_codigo from w_origemdados )
             order by q81_valexe desc
             limit 1;
          else

            select q80_ativ
              into v_ativprinc
              from tabativ
                   inner join ativtipo on tabativ.q07_ativ = ativtipo.q80_ativ
                   inner join tipcalc on tipcalc.q81_codigo = ativtipo.q80_tipcal
             where q07_inscr = v_y71_inscr
               and tabativ.q07_datafi is null
               and q81_tipo in ( select q81_codigo from w_origemdados )
             order by q81_valexe desc
             limit 1;
          end if;


raise notice ' >>>>> ATIVIDADE PRINC: %', v_ativprinc;

          if v_ativprinc is not null then
            v_achou = true;
          end if;

        end if;

        if v_achou is false then
          return '16 - sem atividade principal';
        end if;

        if lCalculaPorPorteAtividade then

          select tipcalc.q81_codigo
            into v_ativtipo
            from tabativ
                 inner join issbaseporte        on q45_inscr    = q07_inscr
                 inner join tabativportetipcalc on q143_issporte = q45_codporte
                                               and q143_ativid   = q07_ativ
                 inner join tipcalc on q81_codigo = q143_tipcalc
                 inner join cadcalc on cadcalc.q85_codigo = tipcalc.q81_cadcalc
           where q81_tipo in ( select q81_codigo
                                 from w_origemdados )
             and q07_inscr = v_y71_inscr
             and q07_ativ = v_ativprinc
             and case
                   when q07_datafi is null
                     then true
                   else q07_datafi >= v_data
                 end
             and q07_databx is null ;
        else

          select tipcalc.q81_codigo
            into v_ativtipo
            from ativtipo
                 inner join tabativ on q07_ativ = q80_ativ
                 inner join tipcalc on q80_tipcal = q81_codigo
                 inner join cadcalc on cadcalc.q85_codigo = tipcalc.q81_cadcalc
           where q81_tipo in ( select q81_codigo from w_origemdados )
             and q07_inscr = v_y71_inscr
             and case
                   when q07_datafi is null then true else q07_datafi >= v_data
                 end
             and q07_databx is null
             and q07_ativ = v_ativprinc;
        end if;

        if lraise is true then
          raise notice 'v_ativtipo: % - v_y71_inscr: % - v_ativprinc: %', v_ativtipo, v_y71_inscr, v_ativprinc;
        end if;

        if v_ativtipo is null then

          if lraise is true then
            raise notice 'ativtipo is null - data: % - inscr: % - ativprinc: %', v_data, v_y71_inscr, v_ativprinc;
          end if;

          select tipcalc.q81_codigo
            into v_ativtipo
            from issportetipo
                 inner join issbaseporte on q45_inscr = v_y71_inscr and q45_codporte = q41_codporte
                 inner join tipcalc on q41_codtipcalc = q81_codigo
                 inner join cadcalc on cadcalc.q85_codigo = tipcalc.q81_cadcalc
                 inner join clasativ on q82_classe = q41_codclasse
                 inner join tabativ on q82_ativ = q07_ativ and q07_inscr = v_y71_inscr
           where q45_codporte = q41_codporte
            and q81_tipo in ( select q81_codigo from w_origemdados )
            and case
                  when q07_datafi is null then
                    true
                  else q07_datafi >= v_data
                end
            and q07_databx is null
            and q82_ativ = v_ativprinc;

          if v_ativtipo is null then
            return '06-sem tipo de calculo configurado!';
          end if;
        end if;
      end if;
    end if;

    if v_y69_numpre = 0 or v_y69_numpre is null then

      select nextval('numpref_k03_numpre_seq')
        into v_numpre;

      insert into vistorianumpre values(v_vistoria,v_numpre);
    else

      v_numpre = v_y69_numpre;

      select k00_numpre
        into v_arrecant
        from arrecant
       where k00_numpre = v_numpre;

      if v_arrecant != 0 or v_arrecant is not null then
        return '07- vistoria ja paga ou cancelada ';
      end if;

      select k00_numpre
        into v_arrecad
        from arrecad
       where k00_numpre = v_numpre;

      if v_arrecad != 0 or v_arrecad is not null then
        delete from arrecad where k00_numpre = v_numpre;
      end if;
    end if;

      --se for por sanitario segue aqui
      if lVistoriaSanitario = true then

        for v_record_saniatividade in
          select y83_ativ,
                 y80_area,
                 q45_codporte
            from saniatividade
                 inner join sanitario      on sanitario.y80_codsani      = y83_codsani
                 left  join sanitarioinscr on sanitarioinscr.y18_codsani = y83_codsani
                 left  join issbaseporte   on issbaseporte.q45_inscr     = sanitarioinscr.y18_inscr
           where y83_codsani = v_y74_codsani
             and y83_ativ    = v_ativprinc
        loop

          select y18_inscr
            into v_y74_inscrsani
            from sanitarioinscr
           where y18_codsani = v_y74_codsani;

          if lCalculaVistoriaMei is false then

            lContribuinteMei := fc_verifica_contribuinte_mei(v_y74_inscrsani, dDataAtual);

            if lContribuinteMei then

              delete from vistorianumpre where y69_codvist = v_vistoria and y69_numpre = v_numpre;
              return '25 - CONTRIBUINTE OPTANTE PELO SIMPLES NACIONAL NA CATEGORIA MEI';
            end if;
          end if;

          if lraise is true then
            raise notice 'y83_ativ (2): % - anousu: %', v_record_saniatividade.y83_ativ, v_anousu;
          end if;

          if lCalculaPorPorteAtividade then

            if v_record_saniatividade.q45_codporte is null then
              return '24- Porte não encontrado no cadastro da empresa. Alvara sanitario : '||v_y74_codsani;
            end if;

            select q81_recexe,
                   q92_hist,
                   q81_valexe,
                   q92_tipo,
                   ( select distinct
                            q83_codven
                       from tipcalcexe
                      where q83_tipcalc = q81_codigo
                       and q83_anousu = v_anousu )
              into v_q81_recexe,
                   v_q92_hist,
                   v_q81_valexe,
                   v_q92_tipo,
                   icodvencimento
              from tabativportetipcalc
                   inner join tipcalc     on tipcalc.q81_codigo    = tabativportetipcalc.q143_tipcalc
                   inner join tipcalcexe  on tipcalcexe.q83_anousu = v_anousu
                                         and tipcalcexe.q83_tipcalc = tipcalc.q81_codigo
                   inner join cadvencdesc on q92_codigo = tipcalcexe.q83_codven
             where q143_ativid   = v_record_saniatividade.y83_ativ
               and q143_issporte = v_record_saniatividade.q45_codporte
               and v_record_saniatividade.y80_area between q81_qiexe and q81_qfexe
               and q81_tipo in ( select q81_codigo
                                   from w_origemdados );
          else

            select q81_recexe,
                   q92_hist,
                   q81_valexe,
                   q92_tipo,
                   ( select distinct
                            q83_codven
                       from tipcalcexe
                      where q83_tipcalc = q81_codigo
                       and q83_anousu = v_anousu )
              into v_q81_recexe,
                   v_q92_hist,
                   v_q81_valexe,
                   v_q92_tipo,
                   icodvencimento
              from ativtipo
                   inner join tipcalc     on q80_tipcal = q81_codigo
                   inner join tipcalcexe  on tipcalcexe.q83_anousu = v_anousu and tipcalcexe.q83_tipcalc = tipcalc.q81_codigo
                   inner join cadvencdesc on q92_codigo = tipcalcexe.q83_codven
             where q80_ativ = v_record_saniatividade.y83_ativ
               and ( select y80_area from sanitario where y80_codsani = v_y74_codsani) between q81_qiexe and q81_qfexe
               and q81_tipo in ( select q81_codigo from w_origemdados );
          end if;

 raise notice '>>>>> inserindo no arrecad... v_valalancar (2): % - icodvencimento: %', v_valalancar, icodvencimento;
 raise notice '>>>>>>>>>executando fc_gerafinanceiro(%,%,%,%,%,%,%)', v_numpre,v_valalancar,icodvencimento,v_y80_numcgm,v_data,v_q81_recexe,ddatavistoria;

          if v_q81_recexe is not null then


            v_valalancar = round(v_q81_valexe * v_valinflator * v_base * v_multitab * v_multicad,2);

            if lraise is true then
              raise notice 'inserindo no arrecad... v_valalancar (2): % - icodvencimento: %', v_valalancar, icodvencimento;
            end if;

            v_calculou = true;
            --
            -- inserindo por sanitario
            --
            -- funcao para gerar o financeiro
            --

            if icodvencimento is null then
              return '18-sem vencimento configurado para o exercicio!';
            end if;

            if lraise is true then
              raise notice 'executando fc_gerafinanceiro(%,%,%,%,%,%,%)', v_numpre,v_valalancar,icodvencimento,v_y80_numcgm,v_data,v_q81_recexe,ddatavistoria;
            end if;

            select *
              into rfinanceiro
              from fc_gerafinanceiro(v_numpre,v_valalancar,icodvencimento,v_y80_numcgm,v_data,v_q81_recexe,ddatavistoria);

            if v_y74_inscrsani is not null then

              if lraise is true then
                raise notice 'xxxxxxxxxxxxxxxxxxxxxxxxx: inscricao: %', v_y74_inscrsani;
              end if;

              select k00_numpre
                into v_numpreinscr
                from arreinscr
               where k00_numpre = v_numpre;

              if v_numpreinscr != 0 or v_numpreinscr is not null then
                delete from arreinscr where k00_numpre = v_numpreinscr;
              end if;

              insert into arreinscr (k00_numpre, k00_inscr)
                             values (v_numpre, v_y74_inscrsani);

            end if;
          end if;
        end loop;

        if v_calculou is true and v_y74_inscrsani != null then
          return '09-ok inscricao numero ' || v_y74_inscrsani;
        elsif v_calculou is true then
          return '09-ok inscricao numero ';
        else
          return '15-ocorreu algum erro durante o calculo (1)!!!';
        end if;
--fim do if do sanitario
--se for por inscricao segue aqui
      elsif lVistoriaLocalizacao = true then

        if lraise is true then
          raise notice 'acessou via inscricao... v_claspont: % - v_ativprinc: %', v_claspont, v_ativprinc;
        end if;

        if lCalculaVistoriaMei is false then

          lContribuinteMei := fc_verifica_contribuinte_mei(v_y71_inscr, dDataAtual);

          if lContribuinteMei then

            delete from vistorianumpre where y69_codvist = v_vistoria and y69_numpre = v_numpre;
            return '25 - CONTRIBUINTE OPTANTE PELO SIMPLES NACIONAL NA CATEGORIA MEI';
          end if;
        end if;

        if v_claspont is null then

          if lCalculaPorPorteAtividade then

            v_text = v_text || ' select q81_qiexe,q81_qfexe,q81_codigo,q81_uqtab,q81_uqcad,q07_ativ as q80_ativ 
';
            v_text = v_text || '   from tabativ 
';
            v_text = v_text || '        inner join issbaseporte        on q45_inscr    = q07_inscr 
';
            v_text = v_text || '        inner join tabativportetipcalc on q143_issporte = q45_codporte 
';
            v_text = v_text || '                                      and q143_ativid   = q07_ativ 
';
            v_text = v_text || '        inner join tipcalc on q81_codigo = q143_tipcalc 
';
            v_text = v_text || '  where q07_ativ = ' || v_ativprinc || '
';
            v_text = v_text || '    and q45_inscr = ' || v_y71_inscr || '
';
            v_text = v_text || '    and q81_tipo in ( select q81_codigo from w_origemdados )
';
            v_text = v_text || ' union 
';
            v_text = v_text || ' select q81_qiexe,q81_qfexe,q81_codigo,q81_uqtab,q81_uqcad,q82_ativ as q80_ativ 
';
            v_text = v_text || '   from issportetipo 
';
            v_text = v_text || '        inner join issbaseporte on q45_inscr          = ' || v_y71_inscr || '
';
            v_text = v_text || '        inner join tipcalc      on q41_codtipcalc     = q81_codigo 
';
            v_text = v_text || '        inner join cadcalc      on cadcalc.q85_codigo = tipcalc.q81_cadcalc 
';
            v_text = v_text || '        inner join clasativ     on q82_classe         = q41_codclasse 
';
            v_text = v_text || '  where q45_codporte = q41_codporte 
';
            v_text = v_text || '    and q81_tipo in ( select q81_codigo from w_origemdados ) 
';
            v_text = v_text || '    and q82_ativ = ' || v_ativprinc || '
';

          else

            v_text = v_text || ' select q81_qiexe,q81_qfexe,q81_codigo,q81_uqtab,q81_uqcad,q80_ativ ';
            v_text = v_text || '   from ativtipo ';
            v_text = v_text || '        inner join tipcalc on q81_codigo = q80_tipcal ';
            v_text = v_text || '  where q80_ativ = ' || v_ativprinc ;
            v_text = v_text || '    and q81_tipo in ( select q81_codigo from w_origemdados )';
            v_text = v_text || ' union ';
            v_text = v_text || ' select q81_qiexe,q81_qfexe,q81_codigo,q81_uqtab,q81_uqcad,q82_ativ as q80_ativ ';
            v_text = v_text || '   from issportetipo ';
            v_text = v_text || '        inner join issbaseporte on q45_inscr          = ' || v_y71_inscr;
            v_text = v_text || '        inner join tipcalc      on q41_codtipcalc     = q81_codigo ';
            v_text = v_text || '        inner join cadcalc      on cadcalc.q85_codigo = tipcalc.q81_cadcalc ';
            v_text = v_text || '        inner join clasativ     on q82_classe         = q41_codclasse ';
            v_text = v_text || '  where q45_codporte = q41_codporte ';
            v_text = v_text || '    and q81_tipo in ( select q81_codigo from w_origemdados ) ';
            v_text = v_text || '    and q82_ativ = ' || v_ativprinc;
          end if;

          select q60_campoutilcalc
            into v_tipo_quant
            from parissqn;

          if v_tipo_quant = 2 then
            select q30_quant from issquant into v_area where q30_inscr = v_y71_inscr and q30_anousu = v_anousu;
          else
            select q30_area from issquant into v_area where q30_inscr = v_y71_inscr and q30_anousu = v_anousu;
          end if;

          if lraise is true then
            raise notice ' 1- v_area - % inscr - % anousu - %',v_area,v_y71_inscr,v_anousu;
          end if;

          if v_area is null then
            v_area = 0;
          end if;

        else

          v_text = v_text || ' select q81_codigo, ';
          v_text = v_text || '        q81_recexe, ';
          v_text = v_text || '        q92_hist,   ';
          v_text = v_text || '        q81_valexe, ';
          v_text = v_text || '        q92_tipo,   ';
          v_text = v_text || '        q81_qiexe,  ';
          v_text = v_text || '        q81_qfexe,  ';
          v_text = v_text || '        q81_uqtab,  ';
          v_text = v_text || '        q81_uqcad,  ';
          v_text = v_text || '        q80_ativ    ';
          v_text = v_text || '   from ativtipo      ';
          v_text = v_text || '        inner join tipcalc on tipcalc.q81_codigo = ativtipo.q80_tipcal ';
          v_text = v_text || '        inner join tipcalcexe on tipcalcexe.q83_anousu = ' || v_anousu || ' and tipcalcexe.q83_tipcalc = tipcalc.q81_codigo ';
          v_text = v_text || '        inner join cadcalc on q81_cadcalc = q85_codigo ';
          v_text = v_text || '        inner join cadvencdesc on q92_codigo = tipcalcexe.q83_codven ';
          v_text = v_text || ' where ' || v_pontuacaogeral || ' >= q81_qiexe and ';
          v_text = v_text ||              v_pontuacaogeral || ' <= q81_qfexe and ';
          v_text = v_text || '       q81_tipo in ( select q81_codigo from w_origemdados ) and ativtipo.q80_ativ = ' || v_ativprinc;

          v_area = v_pontuacaogeral;

          if lraise is true then
            raise notice 'v_area - % ',v_area;
          end if;

        end if;

        if lCalculaPorPorteAtividade then

          select array_upper(array_accum(distinct substr(q71_estrutural,2,5)::integer),1)
            into v_quantativ
            from tabativ
                 inner join issbaseporte        on q45_inscr    = q07_inscr
                 inner join tabativportetipcalc on q143_issporte = q45_codporte
                                               and q143_ativid   = q07_ativ
                 inner join tipcalc             on q81_codigo   = q143_tipcalc
                 inner join atividcnae          on atividcnae.q74_ativid        = tabativ.q07_ativ
                 inner join cnaeanalitica       on cnaeanalitica.q72_sequencial = atividcnae.q74_cnaeanalitica
                 inner join cnae                on cnae.q71_sequencial          = cnaeanalitica.q72_cnae
           where q07_inscr = v_y71_inscr
             and q81_tipo in (3,5,6)
             and (q07_datafi is null or q07_datafi >= current_date)
             and (q07_databx is null or q07_databx >= current_date);

        else

          select count(*)
            into v_quantativ
            from ( select distinct
                          q07_seq
                     from tabativ
                          inner join ativtipo on ativtipo.q80_ativ = tabativ.q07_ativ
                          inner join tipcalc on q81_codigo = q80_tipcal
                    where q81_tipo in (3,5,6)
                      and q07_inscr = v_y71_inscr
                      and (q07_datafi is null or q07_datafi >= current_date)
                      and (q07_databx is null or q07_databx >= current_date) ) as x;
        end if;

        if lraise is true then
          raise notice 'v_quantativ: %', v_quantativ;
        end if;

        if lraise is true then
          --raise notice 'v_text: %', v_text;
          raise notice 'antes do for...';
        end if;

        for v_record_ativtipo in execute v_text loop

          if lraise is true then
            raise notice 'dentro do for... vcalculou : % - tipcalc: % - area: % - qiexe: % - qfexe: %',v_calculou, v_record_ativtipo.q81_codigo, v_area, v_record_ativtipo.q81_qiexe, v_record_ativtipo.q81_qfexe;
          end if;

          if lraise is true then
            raise notice '   antes do if... area - % q81_qiexe - % q81_qfexe - %',v_area,v_record_ativtipo.q81_qiexe,v_record_ativtipo.q81_qfexe;
          end if;

          if v_area >= v_record_ativtipo.q81_qiexe and v_area <= v_record_ativtipo.q81_qfexe then

            lencontrouquantidadecalculo := true;
            if lraise is true then
              raise notice '      processando tipcalc: %', v_record_ativtipo.q81_codigo;
            end if;

            select q81_recexe,
                   q92_hist,
                   q81_valexe,
                   q92_tipo,
                   q81_excedenteativ,
                   (select distinct
                           q83_codven
                      from tipcalcexe
                     where q83_tipcalc = q81_codigo
                       and q83_anousu = v_anousu )
              into v_q81_recexe,
                   v_q92_hist,
                   v_q81_valexe,
                   v_q92_tipo,
                   v_excedente,
                   icodvencimento
              from tipcalc
                   inner join tipcalcexe  on tipcalcexe.q83_anousu = v_anousu
                                         and tipcalcexe.q83_tipcalc = tipcalc.q81_codigo
                   inner join cadcalc     on q81_cadcalc = q85_codigo
                   inner join cadvencdesc on q92_codigo = tipcalcexe.q83_codven
             where q81_codigo = v_record_ativtipo.q81_codigo;

            if icodvencimento is null then
              return '18-sem vencimento configurado para o exercicio!';
            end if;

            /*
               verifica se eh para calcular pela issquant ou tabativ
            */
            if v_record_ativtipo.q81_uqcad is true then
               select q30_mult from issquant into v_multicad where q30_inscr = v_y71_inscr and q30_anousu = v_anousu;
               if not found then
                  return '22 - inscricao sem multiplicador cadastrado na issquant';
               end if;

               if v_multicad is null or v_multicad = 0 then
                  v_multicad := 1;
               end if;
            end if;

            if v_record_ativtipo.q81_uqtab is true then
               select q07_quant
               into v_multitab
               from tabativ
               where q07_inscr = v_y71_inscr and q07_ativ = v_record_ativtipo.q80_ativ and q07_databx is null;
               if not found then
                  return '23 - inscricao sem atividade cadastrada na tabativ ou atividade baixada';
               end if;

               if v_multitab is null or v_multitab = 0 then
                  v_multitab := 1;
               end if;
            end if;

            v_valalancar = round(v_q81_valexe * v_valinflator * v_base * v_multicad * v_multitab,2);
            v_calculou = true;

            if lraise is true then
              raise notice 'v_valalancar (1): % - k00_numpre: % - v_valinflator: % - v_base: %', v_valalancar, v_numpre, v_valinflator, v_base;
            end if;

            if v_excedente > 0 then
              if lraise is true then
                raise notice 'valor antes: %', v_valalancar;
              end if;
              v_valalancar = v_valalancar + (v_valalancar * v_excedente * (v_quantativ - 1));
              if lraise is true then
                raise notice 'valor depois: %', v_valalancar;
              end if;
            end if;

            --
            -- inserindo pelo issbase
            --
            -- funcao para gerar o financeiro
            --
            select *
              into rfinanceiro
              from fc_gerafinanceiro(v_numpre,v_valalancar,icodvencimento,v_q02_numcgm,v_data,v_q81_recexe,ddatavistoria);

            select k00_numpre
              into v_numpreinscr
              from arreinscr
             where k00_numpre = v_numpre;

            if v_numpreinscr != 0 or v_numpreinscr is not null then
              delete from arreinscr where k00_numpre = v_numpreinscr;
            end if;
            insert into arreinscr (k00_numpre, k00_inscr)
                           values (v_numpre, v_y71_inscr);
          end if;

        end loop;


        if lencontrouquantidadecalculo is false then
          return '24-area/empregados nao enquadrada no tipo de calculo.';
        end if;

        if lraise is true then
          raise notice 'fora do for... v_calculou: %', v_calculou;
        end if;

        if v_calculou is true then
          return '09-ok inscricao numero ' || v_y71_inscr;
        else
          return '19-ocorreu algum erro durante o calculo (2)!!!';
        end if;

      end if;
  end;

\$function$
;


SQL;

    DB::connection()->getPdo()->exec($sql);
    }
}
