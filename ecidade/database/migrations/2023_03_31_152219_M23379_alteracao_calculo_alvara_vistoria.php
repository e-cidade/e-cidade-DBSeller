<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M23379AlteracaoCalculoAlvaraVistoria extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upEstrutura();
        $this->upDicionario();
        $this->upFcVistoriasAlterada();
        $this->upFcVistoriasCharqueadasAlterada();
        $this->upFcIssqnAlterada();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downDicionario();
        $this->downEstrutura();
        $this->downFcVistoriasAlterada();
        $this->downFcVistoriasCharqueadasAlterada();
        $this->downFcIssqnAlterada();
    }

    private function upDicionario()
    {
        //parissqn
        DB::statement("insert into db_syscampo values(1014949 ,'q60_calcbaixorisco' ,'bool' ,'Campo para gerar c·lculo de alvar· e vistorias para baixo risco' ,'1' ,'C·lculo alvar· e vistoria baixo risco' ,1 ,'false' ,'false' ,'false' ,5 ,'text' ,'C·lculo alvar· e vistoria baixo risco');");

        //parfiscal
        DB::statement("insert into db_syscampo values(1014960 ,'y32_calcprovisorio' ,'bool' ,'Calcula vistoria de alvar· sanit·rio provisÛrio, se n„o È usada a funÁ„o de vistoria atual, se sim È usada a func„o de vistoria alterada' ,'0' ,'Calcula vist. alvar· sanit·rio prov.' ,1 ,'false' ,'false' ,'false' ,5 ,'text' ,'Calcula vist. alvar· sanit·rio prov.');");

         //organiza campos 
         $dados = [
            [664,1014949,34,0],
            [1103,1014960,27,0],
        ];

        DB::table('configuracoes.db_sysarqcamp')->insert($this->dbSysArqCamp($dados));
    }

    private function downDicionario()
    {
        //parissqn
        DB::table('db_sysarqcamp')->where('codcam', 1014949)->delete();
        DB::table('db_syscampo')->where('codcam', 1014949)->delete();

        //parfiscal
        DB::table('db_sysarqcamp')->where('codcam', 1014960)->delete();
        DB::table('db_syscampo')->where('codcam', 1014960)->delete();
    }

    private function dbSysArqCamp($linhas)
    {
        $dados = [];

        foreach ($linhas as $values) {
            $dados[] = [
                'codarq' => $values[0],
                'codcam' => $values[1],
                'seqarq' => $values[2],
                'codsequencia' => $values[3]
            ];
        }

        return $dados;
    }

    private function dbSysForKey($linhas)
    {
        $dados = [];

        foreach ($linhas as $values) {
            $dados[] = [
                'codarq' => $values[0],
                'codcam' => $values[1],
                'sequen' => $values[2],
                'referen' => $values[3],
                'tipoobjrel' => $values[4]
            ];
        }

        return $dados;
    }

    public function upEstrutura()
    {
        DB::statement("ALTER TABLE issqn.parissqn ADD q60_calcbaixorisco bool NOT NULL DEFAULT '1';");
        DB::statement("ALTER TABLE fiscal.parfiscal ADD y32_calcprovisorio bool NOT NULL DEFAULT '0';");
        DB::statement("INSERT INTO grupocaracteristica
                                    (db139_sequencial, db139_grupoutilizacao, db139_descricao)
                                SELECT 7, 1, 'VISTORIAS'
                                WHERE
                                    NOT EXISTS (
                                        SELECT db139_sequencial FROM grupocaracteristica WHERE db139_sequencial = 7
                                    ); ");
        DB::statement("INSERT INTO caracteristica
                                    (db140_sequencial, db140_grupocaracteristica, db140_descricao)
                                SELECT 32, 7, 'NORMAL'
                                WHERE
                                    NOT EXISTS (
                                        SELECT db140_sequencial FROM caracteristica WHERE db140_sequencial = 32
                                    ); ");
        DB::statement("INSERT INTO caracteristica
                                    (db140_sequencial, db140_grupocaracteristica, db140_descricao)
                                SELECT 33, 7, 'ISENTO'
                                WHERE
                                    NOT EXISTS (
                                        SELECT db140_sequencial FROM caracteristica WHERE db140_sequencial = 33
                                    ); ");
    }

    public function downEstrutura()
    {
        DB::statement("ALTER TABLE issqn.parissqn  DROP COLUMN q60_calcbaixorisco;");
        DB::statement("ALTER TABLE fiscal.parfiscal DROP COLUMN y32_calcprovisorio;");
    }

    public function upFcVistoriasAlterada()
    {
    DB::statement(<<<SQL1

    CREATE OR REPLACE FUNCTION public.fc_vistorias(integer)
 RETURNS character varying
AS $$
DECLARE

  iCodigoVistoria             alias for $1;

  V_ATIVTIPO                  integer;
  iNumpreGerado               integer;
  iNumpreArreinscr            integer;
  iNumpreArrecant             integer;
  iNumpreArrecad              integer;
  V_DATA                      date;

  V_DIASGERAL                 integer;
  V_MESGERAL                  integer;
  V_PARCIAL                   boolean;

  V_ACHOU                     boolean default false;
  lCalculou                   boolean default false;
  V_DATAVENC                  date;
  V_Y74_CODSANI               integer;
  V_Y74_INSCRSANI             integer;
  V_Y80_NUMCGM                integer;
  V_Y69_NUMPRE                integer;
  V_Q138_CARACTERISTICA       integer;
  iCodigoReceitaExercicio     integer;
  iCodigoHistoricoCalculo     integer;
  nValorExercicio             float8;
  iCodigoArretipo             integer;
  V_Y71_INSCR                 integer;
  V_INSCR_BAIXO_RISCO         integer;
  V_Q02_NUMCGM                integer;
  V_ATIV                      integer;
  iAnousu                     integer;
  iCodigoAtividade            integer;
  V_DIASVENC                  integer;
  iFormaCalculo               integer;
  iCodVencimento              integer default 0;

  lIsSanitario                boolean;
  V_INSCR                     boolean;
  V_AREA                      float8;
  nValorInflator              float8;
  nValorBase                  float8;
  nValorVistoria              float8;
  sSql                        text default '';
  nValorExcedente             float8;
  iQuantidadeAtividades       integer default 0;

  iFormulaCalculoVistoria     integer;

  nPontuacaoClasse            integer default NULL;
  iPontuacaoPorZonaFiscal     integer;
  iPontuacaoPorEmpregadosArea integer;
  iPontuacaoPorEmpregados     integer;
  nPontuacaoPorArea           float8;
  nPontuacaoGeral             float8;

  lPorArea                    boolean default false;

  v_tipo_quant                integer;

  calc_baixo_risco            boolean default true;

  iInstit                     integer;
  iCodCli                     integer;

  dDataVistoria               date;
  dDataAtual                  date;

  rAtivtipo                   record;
  rSaniAtividade              record;
  rFinanceiro                 record;

  sTipoVistoriaSanitario      text;

  lRaise                      boolean default true;

  lCalculaVistoriaMei         boolean default true;
  lCalcProvisorio             boolean default false;
  lContribuinteMei            boolean default false;
  lVistoriaAtrasada           boolean default false;

  nQuantidadeAtividade        float8 default 1; -- Quantidade informada no campo q07_quant da tabativ
  nMultiplicadorIssQuantidade float8 default 1; -- Quantidade informada no campo q30_multi da issquant

  BEGIN

    /**
     * Verifica pl por Cliente Especifico
     */
    select db21_codcli
      into iCodCli
      from db_config
     where prefeitura is true;

    lRaise     := ( case when fc_getsession('DB_debugon') is null then false else true end );
    iInstit    := fc_getsession('DB_instit');
    dDataAtual := fc_getsession('DB_datausu');


    /**
     * 3 = VISTORIA LOCALIZACAO
     * 5 = TAXA
     * 6 = VISTORIA SANITARIO
     */
    begin

      if iCodCli = 4 then

        create temp table w_tipos_localizacao as select 3 as codigo;
        create temp table w_tipos_sanitario   as select 6 as codigo;

      elsif exists(select 1 from db_config where prefeitura is true and cgc = '88489786000101') then
      
     
        return fc_vistorias_charqueadas(iCodigoVistoria);

      

      if exists(select 1 from vistorias where y70_codvist = iCodigoVistoria and y70_tipovist = 10)
        then

          create temp table w_tipos_localizacao as select 5 as codigo;
          create temp table w_tipos_sanitario   as select 5 as codigo;
        else

          create temp table w_tipos_localizacao as select 3 as codigo;
          create temp table w_tipos_sanitario   as select 6 as codigo;
        end if;

      else
  
        create temp table w_tipos_localizacao as select 3 as codigo union select 5;
        create temp table w_tipos_sanitario   as select 5 as codigo union select 6;
      end if;
    exception

         when duplicate_table then

           truncate w_tipos_localizacao;
           truncate w_tipos_sanitario;
           if iCodCli = 4 then

             insert into w_tipos_localizacao values (3);
             insert into w_tipos_sanitario   values (6);
           else

             insert into w_tipos_localizacao values (3),(5);
             insert into w_tipos_sanitario   values (5),(6);
           end if;
    end;

    select fc_getsession('DB_funcao_vistorias_sanitario')
      into sTipoVistoriaSanitario;

    if sTipoVistoriaSanitario is not null and sTipoVistoriaSanitario <> '' then

      delete from w_tipos_sanitario;
      insert into w_tipos_sanitario select unnest(string_to_array(sTipoVistoriaSanitario, ','))::integer;
    end if;
    
    select extract(year from y70_data), y70_data
      into iAnousu, dDataVistoria
      from vistorias
     where y70_codvist = iCodigoVistoria;

    /**
     *  Verifica se a vistoria eh parcial ou geral, para montar a data de vencimento a ser gravada no arrecad
     */
    select y70_parcial
      into V_PARCIAL
      from vistorias
     where y70_codvist = iCodigoVistoria;

    if V_PARCIAL is not null AND V_PARCIAL = false then

      perform fc_debug('<fc_vistorias> GERAL', lRaise);

      select y77_diasgeral, y77_mesgeral, y70_data
        into  V_DIASGERAL,V_MESGERAL,V_DATA
        from tipovistorias
             inner join vistorias on y77_codtipo = y70_tipovist
       where y70_codvist = iCodigoVistoria;


      if V_DIASGERAL is null OR
         V_DIASGERAL = 0     OR
         V_MESGERAL is null  OR
         V_MESGERAL = 0      then

        return '01- TIPO DE VISTORIA SEM DIA OU MES PARA VENCIMENTO CONFIGURADO!';
      end if;

      V_DATAVENC = iAnousu||'-'||V_MESGERAL||'-'||V_DIASGERAL;

    else

      perform fc_debug('<fc_vistorias> PARCIAL', lRaise);

      select y77_dias, y70_data, y70_data, y77_diasgeral, y77_mesgeral
        into V_DIASVENC, V_DATA, V_DATAVENC, V_DIASGERAL, V_MESGERAL
        from tipovistorias
             inner join vistorias on y77_codtipo = y70_tipovist
       where y70_codvist = iCodigoVistoria;

      if V_DIASVENC is null OR V_DIASVENC = 0 then

        if V_DIASGERAL is null then
          return '02- TIPO DE VISTORIA SEM DIAS PARA VENCIMENTO CONFIGURADO!';
        else
          V_DATAVENC = iAnousu||'-'||V_MESGERAL||'-'||V_DIASGERAL;
        end if;
      end if;

      perform fc_debug('<fc_vistorias> V_DIASVENC: ' || V_DIASVENC || ' V_DATAVENC: ' || V_DATAVENC, lRaise);

      /**
       * V_DATA = V_DATAVENC;
       */
      if V_DIASVENC is null then
        V_DIASVENC = 0;
      end if;

      select V_DATAVENC + V_DIASVENC
        into V_DATAVENC;

    end if;

    perform fc_debug('<fc_vistorias> V_DATAVENC: ' || V_DATAVENC, lRaise);

    select y32_formvist,
           y32_calculavistoriamei,
           y32_calcprovisorio
      into iFormulaCalculoVistoria,
           lCalculaVistoriaMei,
           lCalcProvisorio
      from parfiscal
     where y32_instit = iInstit ;

    if iFormulaCalculoVistoria = 1 then
      
       select q60_campoutilcalc
         into v_tipo_quant
         from parissqn;
       
    end if;

    if iFormulaCalculoVistoria =  1 and v_tipo_quant = 1 then /* Normal / por ·rea */
       lPorArea = true;
    end if;

    select q04_vbase
      into nValorBase
      from cissqn
     where cissqn.q04_anousu = iAnousu;

    if nValorBase = 0 OR nValorBase is null then
      return '03- SEM VALOR BASE CADASTRADO NOS PARAMETROS ';
    end if;

    select distinct i02_valor
      into nValorInflator
      from cissqn
           inner join infla on q04_inflat = i02_codigo
      where cissqn.q04_anousu             = iAnousu
        and date_part('year',i02_data)    = iAnousu
        and date_part('month',i02_data)   = date_part('month', fc_getsession('DB_datausu')::date);

    perform fc_debug('<fc_vistorias> Inflator: ' || nValorInflator, lRaise);

    if nValorInflator is null then
      nValorInflator = 1;
    end if;

    select y74_codsani,y80_numcgm,y69_numpre
      into V_Y74_CODSANI, V_Y80_NUMCGM, V_Y69_NUMPRE
      from vistsanitario
           inner join sanitario      on y74_codsani = y80_codsani
           left  join vistorianumpre on y69_codvist = iCodigoVistoria
     where Y74_CODVIST = iCodigoVistoria;

    perform fc_debug('<fc_vistorias> V_Y74_CODSANI: ' || V_Y74_CODSANI || ' iCodigoVistoria: ' || iCodigoVistoria, lRaise);

    if V_Y74_CODSANI = 0 OR V_Y74_CODSANI is null then

      lIsSanitario = false;
      select y71_inscr,q02_numcgm,y69_numpre,q138_caracteristica
        into V_Y71_INSCR,V_Q02_NUMCGM,V_Y69_NUMPRE,V_Q138_CARACTERISTICA
        from vistinscr
             inner join issbase               on q02_inscr   = y71_inscr
             left  join vistorianumpre        on y69_codvist = iCodigoVistoria
             left  join issbasecaracteristica on q02_inscr = q138_inscr
      where Y71_CODVIST = iCodigoVistoria;

      if V_Y71_INSCR is null then
        V_INSCR = false;
      else
        V_INSCR = true;
      end if;

      if  V_Q138_CARACTERISTICA = 33 then
        return 'INSCRI«√O COM CARACTERÕSTICA VISTORIA: ISENTO';
      end if;

    else
      lIsSanitario = true;
    end if;

    perform fc_debug('<fc_vistorias> lIsSanitario: ' || lIsSanitario || ' iFormulaCalculoVistoria: ' || iFormulaCalculoVistoria, lRaise);
    
    if iFormulaCalculoVistoria = 1 then

      if lIsSanitario = true OR V_INSCR = true then
        
        if lIsSanitario is true then
          
          if lCalcProvisorio is true then
            
            V_ACHOU = false;
            perform fc_debug('<fc_vistorias> V_Y74_CODSANI: ' || V_Y74_CODSANI, lRaise);

            select min(q85_forcal)
              into iFormaCalculo
              from tipcalc
                  inner join cadcalc       on tipcalc.q81_cadcalc    = cadcalc.q85_codigo
                  inner join ativtipo      on ativtipo.q80_tipcal    = tipcalc.q81_codigo
                  inner join saniatividade on saniatividade.y83_ativ = ativtipo.q80_ativ
            where saniatividade.y83_codsani = V_Y74_CODSANI
              and saniatividade.y83_databx is null
              and date_part('year', saniatividade.y83_dtfim) >= date_part('year', CURRENT_DATE) 
              and tipcalc.q81_tipo in ( select codigo from w_tipos_sanitario );
            
            if iFormaCalculo is null then
              return '11-SEM FORMA DE CALCULO ENCONTRADA (SANI)!';
            end if;

            perform fc_debug('<fc_vistorias> iFormaCalculo: ' || iFormaCalculo, lRaise);

            if iFormaCalculo = 1 then

              select q80_ativ
                into iCodigoAtividade
                from saniatividade
                    inner join ativtipo on saniatividade.y83_ativ = ativtipo.q80_ativ
                    inner join tipcalc  on tipcalc.q81_codigo     = ativtipo.q80_tipcal
              where y83_codsani = V_Y74_CODSANI
                and y83_databx is null
                and date_part('year', saniatividade.y83_dtfim) >= date_part('year', CURRENT_DATE)
                and q81_tipo in ( select codigo from w_tipos_sanitario )
                and y83_ativprinc is true;

              if iCodigoAtividade is not null then
                V_ACHOU = true;
              end if;

            elsif iFormaCalculo = 2 then

                select  Q80_ATIV
                  into iCodigoAtividade
                  from saniatividade
                      inner join ativtipo on saniatividade.y83_ativ = ativtipo.q80_ativ
                      inner join tipcalc  on tipcalc.q81_codigo     = ativtipo.q80_tipcal
                where y83_codsani = V_Y74_CODSANI
                  and y83_databx is null
                  and date_part('year', saniatividade.y83_dtfim) >= date_part('year', CURRENT_DATE)
                  and q81_tipo in ( select codigo from w_tipos_sanitario )
              order by q81_valexe desc
                limit 1;

              if iCodigoAtividade is not null then
                V_ACHOU = true;
              end if;

            end if;

            perform fc_debug('<fc_vistorias> iCodigoAtividade: ' || iCodigoAtividade, lRaise);

            if V_ACHOU is false then
              return '04- NENHUMA ATIVIDADE COM TIPO 6 CADASTRADA';
            end if;
          else
            
            V_ACHOU = false;
            perform fc_debug('<fc_vistorias> V_Y74_CODSANI: ' || V_Y74_CODSANI, lRaise);

            select min(q85_forcal)
              into iFormaCalculo
              from tipcalc
                  inner join cadcalc       on tipcalc.q81_cadcalc    = cadcalc.q85_codigo
                  inner join ativtipo      on ativtipo.q80_tipcal    = tipcalc.q81_codigo
                  inner join saniatividade on saniatividade.y83_ativ = ativtipo.q80_ativ
            where saniatividade.y83_codsani = V_Y74_CODSANI
              and saniatividade.y83_dtfim is null
              and tipcalc.q81_tipo in ( select codigo from w_tipos_sanitario );

            if iFormaCalculo is null then
              return '11-SEM FORMA DE CALCULO ENCONTRADA (SANI)!';
            end if;

            perform fc_debug('<fc_vistorias> iFormaCalculo: ' || iFormaCalculo, lRaise);

            if iFormaCalculo = 1 then

              select q80_ativ
                into iCodigoAtividade
                from saniatividade
                    inner join ativtipo on saniatividade.y83_ativ = ativtipo.q80_ativ
                    inner join tipcalc  on tipcalc.q81_codigo     = ativtipo.q80_tipcal
              where y83_codsani = V_Y74_CODSANI
                and saniatividade.y83_dtfim is null
                and q81_tipo in ( select codigo from w_tipos_sanitario )
                and y83_ativprinc is true;

              if iCodigoAtividade is not null then
                V_ACHOU = true;
              end if;

            elsif iFormaCalculo = 2 then

                select  Q80_ATIV
                  into iCodigoAtividade
                  from saniatividade
                      inner join ativtipo on saniatividade.y83_ativ = ativtipo.q80_ativ
                      inner join tipcalc  on tipcalc.q81_codigo     = ativtipo.q80_tipcal
                where y83_codsani = V_Y74_CODSANI
                  and saniatividade.y83_dtfim is null
                  and q81_tipo in ( select codigo from w_tipos_sanitario )
              order by q81_valexe desc
                limit 1;

              if iCodigoAtividade is not null then
                V_ACHOU = true;
              end if;

            end if;

            perform fc_debug('<fc_vistorias> iCodigoAtividade: ' || iCodigoAtividade, lRaise);

            if V_ACHOU is false then
              return '04- NENHUMA ATIVIDADE COM TIPO 6 CADASTRADA';
            end if;
          end if;   
        end if;

        if V_INSCR = true then

          select MIN(Q85_FORCAL)
          into iFormaCalculo
          from TIPCALC
          INNER JOIN CADCALC                ON TIPCALC.Q81_CADCALC = CADCALC.Q85_CODIGO
          INNER JOIN ATIVTIPO               ON ATIVTIPO.Q80_TIPCAL = TIPCALC.Q81_CODIGO
          INNER JOIN TABATIV          ON TABATIV.Q07_ATIV = ATIVTIPO.Q80_ATIV
          where Q07_INSCR = V_Y71_INSCR AND
          TABATIV.Q07_DATAFI is null AND
          TIPCALC.Q81_TIPO IN ( select codigo from w_tipos_localizacao );

          if iFormaCalculo is null then

            select MIN(Q85_FORCAL)
            into iFormaCalculo
            from ISSPORTETIPO
            INNER JOIN ISSBASEPORTE ON Q45_INSCR = V_Y71_INSCR
                                   and q45_codporte = q41_codporte
            INNER JOIN TIPCALC ON Q41_CODTIPCALC = Q81_CODIGO
            INNER JOIN CADCALC ON CADCALC.Q85_CODIGO = TIPCALC.Q81_CADCALC
            INNER JOIN CLASATIV ON Q82_CLASSE = Q41_CODCLASSE
            INNER JOIN TABATIV ON Q82_ATIV = Q07_ATIV AND Q07_INSCR = V_Y71_INSCR
            INNER JOIN ATIVPRINC ON ATIVPRINC.q88_inscr = TABATIV.q07_inscr and ATIVPRINC.q88_seq = TABATIV.q07_seq
            where Q45_CODPORTE = Q41_CODPORTE AND Q81_TIPO IN ( select codigo from w_tipos_localizacao ) and
            case when q07_datafi is null then true else q07_datafi >= V_DATA end AND
            q07_databx is null;

            if iFormaCalculo is null then
              return '17-SEM FORMA DE CALCULO ENCONTRADA (INSCR)!';
            end if;

          end if;

          perform fc_debug('Forma de Calculo encontrada: ' || iFormaCalculo, lRaise);
          perform fc_debug('v_data: ' ||v_data ||' V_Y71_INSCR: ' || V_Y71_INSCR, lRaise);

          /**
           * Pontuacao das classes
           */
            select Q82_ATIV, MAX(Q25_PONTUACAO)
              into iCodigoAtividade, nPontuacaoClasse
              from TABATIV
                   INNER JOIN CLASATIV   ON Q82_ATIV   = Q07_ATIV
                   INNER JOIN CLASSEPONT ON Q25_CLASSE = Q82_CLASSE
             where Q07_INSCR = V_Y71_INSCR
               AND case when q07_datafi is null
                        then true
                        else q07_datafi >= V_DATA end
               AND q07_databx is null
          GROUP BY Q82_ATIV
          ORDER BY MAX(Q25_PONTUACAO) DESC
             limit 1;

          if nPontuacaoClasse is not null then

            /**
             * Pontuacao zona fiscal
             */
            select Q26_PONTUACAO
              into iPontuacaoPorZonaFiscal
              from ZONAPONT
                   INNER JOIN ISSZONA ON Q26_ZONA = Q35_ZONA
             where Q35_INSCR = V_Y71_INSCR;

            if lPorArea then
               iPontuacaoPorZonaFiscal = 0;
            elsif iPontuacaoPorZonaFiscal is null then
              return '12-PONTUACAO DA ZONA NAO ENCONTRADA';
            end if;

            /**
             * Pontuacao empregados/area
             */
            select Q30_QUANT, Q30_AREA
              into iPontuacaoPorEmpregadosArea, V_AREA
              from ISSQUANT
             where ISSQUANT.Q30_INSCR  = V_Y71_INSCR
               AND ISSQUANT.Q30_ANOUSU = iAnousu;

            if iPontuacaoPorEmpregadosArea is null then
              select Q30_QUANT, Q30_AREA
              from ISSQUANT
              into iPontuacaoPorEmpregadosArea, V_AREA
              where ISSQUANT.Q30_INSCR = V_Y71_INSCR AND ISSQUANT.Q30_ANOUSU = (iAnousu - 1);
              if iPontuacaoPorEmpregadosArea is null then
                select Q30_QUANT, Q30_AREA
                from ISSQUANT
                into iPontuacaoPorEmpregadosArea, V_AREA
                where ISSQUANT.Q30_INSCR = V_Y71_INSCR AND ISSQUANT.Q30_ANOUSU = (iAnousu + 1);
                if iPontuacaoPorEmpregadosArea is null then
                  INSERT into ISSQUANT select * from ISSQUANT where ISSQUANT.Q30_INSCR = V_Y71_INSCR AND ISSQUANT.Q30_ANOUSU = (iAnousu - 1);
                end if;
              end if;
            end if;

            /**
             * Pontuacao pelos empregados
             */
            select Q27_PONTUACAO
              into iPontuacaoPorEmpregados
              from EMPREGPONT
             where iPontuacaoPorEmpregadosArea >= Q27_QUANTINI AND
                   iPontuacaoPorEmpregadosArea <= Q27_QUANTFIM;

            if lPorArea then
               iPontuacaoPorEmpregados = 0; 
            elsif iPontuacaoPorEmpregados is null then
              return '13-PONTUACAO DO NUMERO DE EMPREGADOS NAO ENCONTRADA';
            end if;

            if lRaise is true then
              raise notice 'V_AREA: %', V_AREA;
            end if;

            /**
             * Pontuacao pela area
             */
            if lPorArea then 

               nPontuacaoPorArea = V_AREA;

            else 
               select Q28_PONTUACAO
                 into nPontuacaoPorArea
                 from AREAPONT
                where V_AREA >= Q28_QUANTINI
                  AND V_AREA <= Q28_QUANTFIM;

            end if;

            if nPontuacaoPorArea is null then
              return '14-PONTUACAO DA AREA NAO ENCONTRADA';
            end if;

            if lRaise is true then
              raise notice 'nPontuacaoClasse: % - iPontuacaoPorZonaFiscal: %  - iPontuacaoPorEmpregados: %  - nPontuacaoPorArea: %', nPontuacaoClasse, iPontuacaoPorZonaFiscal, iPontuacaoPorEmpregados, nPontuacaoPorArea;
            end if;

            if lPorArea is false then 
               nPontuacaoGeral = nPontuacaoClasse + iPontuacaoPorZonaFiscal + iPontuacaoPorEmpregados + nPontuacaoPorArea;
               V_AREA = nPontuacaoGeral;
            else
               nPontuacaoGeral = nPontuacaoPorArea;
            end if;

            perform fc_debug('Pontuacaogeral: ' || nPontuacaoGeral ,lRaise);

            select Q81_CODIGO, Q81_RECEXE, Q92_HIST, Q81_VALEXE, Q92_TIPO
              into V_ATIVTIPO, iCodigoReceitaExercicio, iCodigoHistoricoCalculo, nValorExercicio, iCodigoArretipo
              from TIPCALC
                   inner join tipcalcexe on tipcalcexe.q83_anousu = iAnousu
                                        and tipcalcexe.q83_tipcalc = tipcalc.q81_codigo
                   INNER JOIN CADVENCDESC ON Q92_CODIGO = tipcalcexe.Q83_CODVEN
             where nPontuacaoGeral >= Q81_QIEXE
               AND nPontuacaoGeral <= Q81_QFEXE
               AND Q81_TIPO IN ( select codigo from w_tipos_localizacao );

         /**
          * Por ativtipo
          */
          else

            if iFormaCalculo = 1 then

              select Q80_ATIV
                into iCodigoAtividade
                from TABATIV
                     INNER JOIN ATIVPRINC ON ATIVPRINC.q88_inscr = TABATIV.q07_inscr
                                         and ATIVPRINC.q88_seq = TABATIV.q07_seq
                     INNER JOIN ATIVTIPO  ON TABATIV.Q07_ativ = ATIVTIPO.q80_ativ
                     INNER JOIN TIPCALC   ON TIPCALC.Q81_CODIGO = ATIVTIPO.Q80_TIPCAL
              where Q07_INSCR = V_Y71_INSCR
                AND TABATIV.Q07_DATAFI is null
                AND Q81_TIPO IN ( select codigo from w_tipos_localizacao );

              if iCodigoAtividade is not null then
                V_ACHOU = true;
              else

                select Q07_ATIV
                  into iCodigoAtividade
                  from ISSPORTETIPO
                       INNER JOIN ISSBASEPORTE ON Q45_INSCR = V_Y71_INSCR and q45_codporte = q41_codporte
                       INNER JOIN TIPCALC ON Q41_CODTIPCALC = Q81_CODIGO
                       INNER JOIN CADCALC ON CADCALC.Q85_CODIGO = TIPCALC.Q81_CADCALC
                       INNER JOIN CLASATIV ON Q82_CLASSE = Q41_CODCLASSE
                       INNER JOIN TABATIV ON Q82_ATIV = Q07_ATIV AND Q07_INSCR = V_Y71_INSCR
                       INNER JOIN ATIVPRINC ON ATIVPRINC.q88_inscr = TABATIV.q07_inscr
                                           and ATIVPRINC.q88_seq = TABATIV.q07_seq
                where Q45_CODPORTE = Q41_CODPORTE
                  AND Q81_TIPO IN ( select codigo from w_tipos_localizacao )
                  and case when q07_datafi is null then true else q07_datafi >= V_DATA end
                  AND q07_databx is null;

                if iCodigoAtividade is not null then
                  V_ACHOU = true;
                end if;

              end if;

            elsif iFormaCalculo = 2 then

                select Q80_ATIV
                  into iCodigoAtividade
                  from TABATIV
                       INNER JOIN ATIVTIPO ON TABATIV.Q07_ativ = ATIVTIPO.q80_ativ
                       INNER JOIN TIPCALC ON TIPCALC.Q81_CODIGO = ATIVTIPO.Q80_TIPCAL
                 where Q07_INSCR = V_Y71_INSCR
                   AND TABATIV.Q07_DATAFI is null
                   AND Q81_TIPO IN ( select codigo from w_tipos_localizacao )
              ORDER BY Q81_VALEXE DESC
                 LIMIT 1;

              if iCodigoAtividade is not null then
                V_ACHOU = true;
              end if;

            end if;

            if V_ACHOU is false then
              return '16 - SEM ATIVIDADE PRINCIPAL';
            end if;

            select TIPCALC.Q81_CODIGO
              into V_ATIVTIPO
              from ATIVTIPO
                   INNER JOIN TABATIV ON Q07_ATIV = q80_ativ
                   INNER JOIN TIPCALC ON Q80_TIPCAL = Q81_CODIGO
                   INNER JOIN CADCALC ON CADCALC.Q85_CODIGO = TIPCALC.Q81_CADCALC
             where Q81_TIPO IN ( select codigo from w_tipos_localizacao )
               AND Q07_INSCR = V_Y71_INSCR
               AND case when q07_datafi is null then true else q07_datafi >= V_DATA end
               AND q07_databx is null
               AND Q07_ATIV = iCodigoAtividade;

            if V_ATIVTIPO is null then

              select TIPCALC.Q81_CODIGO
                into V_ATIVTIPO
                from ISSPORTETIPO
                     INNER JOIN ISSBASEPORTE ON Q45_INSCR = V_Y71_INSCR and q45_codporte = q41_codporte
                     INNER JOIN TIPCALC ON Q41_CODTIPCALC = Q81_CODIGO
                     INNER JOIN CADCALC ON CADCALC.Q85_CODIGO = TIPCALC.Q81_CADCALC
                     INNER JOIN CLASATIV ON Q82_CLASSE = Q41_CODCLASSE
                     INNER JOIN TABATIV ON Q82_ATIV = Q07_ATIV AND Q07_INSCR = V_Y71_INSCR
               where Q45_CODPORTE = Q41_CODPORTE
                 AND Q81_TIPO IN ( select codigo from w_tipos_localizacao )
                 and case when q07_datafi is null then true else q07_datafi >= V_DATA end
                 AND q07_databx is null
                 AND Q82_ATIV = iCodigoAtividade;

              if V_ATIVTIPO is null then
                return '06-SEM TIPO DE CALCULO CONFIGURADO!';
              end if;
            end if;

          end if;

        end if;

        if V_Y69_NUMPRE = 0 OR V_Y69_NUMPRE is null then

          select NEXTVAL('numpref_k03_numpre_seq')
            into iNumpreGerado;

          INSERT into VISTORIANUMPRE VALUES(iCodigoVistoria, iNumpreGerado);
        else

          iNumpreGerado = V_Y69_NUMPRE;
          select k00_numpre
            into iNumpreArrecant
            from arrecant
           where k00_numpre = iNumpreGerado;

          if iNumpreArrecant != 0 OR iNumpreArrecant is not null then
            return '07- VISTORIA JA PAGA OU CANCELADA ';
          end if;

          select k00_numpre
            into iNumpreArrecad
            from arrecad
           where k00_numpre = iNumpreGerado;

          if iNumpreArrecad != 0 OR iNumpreArrecad is not null then
            delete from arrecad where k00_numpre = iNumpreGerado;
          end if;

        end if;
      end if;

      perform fc_debug('Verifica se sanitario lIsSanitario: ' || lIsSanitario,  lRaise);
      perform fc_debug('                           V_INSCR: ' || V_INSCR, lRaise);

      /**
       * SE FOR POR SANITARIO SEGUE AQUI
       */
      if lIsSanitario = true then

        FOR rSaniAtividade IN
          select Y83_ATIV
            from SANIATIVIDADE
           where Y83_CODSANI = V_Y74_CODSANI
             AND Y83_ATIV    = iCodigoAtividade

          LOOP

          if lRaise is true then
            raise notice 'Y83_ATIV (2): % - anousu: %', rSaniAtividade.Y83_ATIV, iAnousu;
          end if;

          select Y18_INSCR
            into V_Y74_INSCRSANI
            from sanitarioinscr
           where y18_codsani = V_Y74_CODSANI;

          select q60_calcbaixorisco
           into calc_baixo_risco
           from parissqn;
 
          if calc_baixo_risco is false then
              
            select q30_inscr
              into V_INSCR_BAIXO_RISCO
              from issquant
              where q30_inscr  = V_Y71_INSCR
                and q30_graurisco = 'B';
  
            if V_INSCR_BAIXO_RISCO != 0 OR V_INSCR_BAIXO_RISCO is not null then
  
              delete from vistorianumpre where y69_codvist = iCodigoVistoria and y69_numpre = iNumpreGerado;
              return '26 - INSCRI«√O COM GRAU DE RISCO BAIXO, SE DESEJA CALCULAR ALTERE PARA SIM O PAR¬METRO GERAR C¡LCULO PARA BAIXO RISCO';
            end if;
          end if;

          if lCalculaVistoriaMei is false then
            lContribuinteMei := fc_verifica_contribuinte_mei(V_Y74_INSCRSANI, dDataAtual);

            if lContribuinteMei then

              delete from vistorianumpre where y69_codvist = iCodigoVistoria and y69_numpre = iNumpreGerado;
              return '25 - CONTRIBUINTE OPTANTE PELO SIMPLES NACIONAL NA CATEGORIA MEI';
            end if;
          end if;

          select q81_recexe, q92_hist, q81_valexe, q92_tipo,
                 (select distinct q83_codven
                    from tipcalcexe
                   where q83_tipcalc = q81_codigo
                     and q83_anousu  = iAnousu)
            into iCodigoReceitaExercicio,
                 iCodigoHistoricoCalculo,
                 nValorExercicio,
                 iCodigoArretipo,
                 iCodVencimento
            from ATIVTIPO
                 inner join tipcalc     on q80_tipcal             = q81_codigo
                 inner join tipcalcexe  on tipcalcexe.q83_anousu  = iAnousu
                                       and tipcalcexe.q83_tipcalc = tipcalc.q81_codigo
                 inner join cadvencdesc on q92_codigo             = tipcalcexe.q83_codven
           where q80_ativ = rsaniatividade.y83_ativ
             and ( select y80_area from sanitario where y80_codsani = V_Y74_CODSANI) >= q81_qiexe
             and ( select y80_area from sanitario where y80_codsani = V_Y74_CODSANI) <= q81_qfexe
             and q81_tipo in ( select codigo from w_tipos_sanitario );

          if iCodigoReceitaExercicio is not null then

            nValorVistoria = round(nValorExercicio * nValorInflator * nValorBase,2);

            -- Verifica se esta instalado o plugin para calcular a vistoria atrasada em dobro.
            perform *
               from db_plugin
              where db145_nome = 'PluginVistoriasAtrasadas'
                and db145_situacao is true;

            if found then

              lVistoriaAtrasada :=  fc_verifica_vistoria_atrasada(V_Y74_CODSANI, iCodigoVistoria, iAnousu);

              if lVistoriaAtrasada is true then
                nValorVistoria := nValorVistoria * 2;
              end if ;
            end if;


            if lRaise is true then
              raise notice 'inserindo no arrecad... nValorVistoria (2): % - iCodVencimento: %', nValorVistoria, iCodVencimento;
            end if;

            lCalculou = true;
            --
            -- Inserindo por sanitario
            --

            --
            -- Funcao para gerar o financeiro
            --
            if iCodVencimento is null then
              return '18-SEM VENCIMENTO CONFIGURADO PARA O EXERCICIO!';
            end if;

            if lRaise is true then
              raise notice 'executando fc_gerafinanceiro(%,%,%,%,%,%,%)', iNumpreGerado,nValorVistoria,iCodVencimento,V_Y80_NUMCGM,V_DATA,iCodigoReceitaExercicio,dDataVistoria;
            end if;

            select *
              into rFinanceiro
              from fc_gerafinanceiro(iNumpreGerado,nValorVistoria,iCodVencimento,V_Y80_NUMCGM,V_DATA,iCodigoReceitaExercicio,dDataVistoria);

            if V_Y74_INSCRSANI is not null then

              select k00_numpre
                into iNumpreArreinscr
                from arreinscr
               where k00_numpre = iNumpreGerado;

              if iNumpreArreinscr != 0 OR iNumpreArreinscr is not null then
                DELETE from ARREINSCR where K00_NUMPRE = iNumpreArreinscr;
              end if;

              INSERT into ARREINSCR (k00_numpre, k00_inscr)
                             VALUES (iNumpreGerado, V_Y74_INSCRSANI);

            end if;
          end if;
        end loop;

        if lCalculou IS true then
          return '08 - OK ';
        else
          return '15 - ERRO DURANTE O CALCULO';
        end if;
      /**
       * FIM DO if DO SANITARIO
       */

      /**
       * SE FOR POR INSCRICAO SEGUE AQUI
       */
      elsif V_INSCR = true then

        perform fc_debug('<fc_vistorias> Inscricao - nPontuacaoClasse: ' || nPontuacaoClasse || ' iCodigoAtividade: ' || iCodigoAtividade, lRaise);

        select q60_calcbaixorisco
          into calc_baixo_risco
          from parissqn;

        if calc_baixo_risco is false then
            
          select q30_inscr
            into V_INSCR_BAIXO_RISCO
            from issquant
            where q30_inscr  = V_Y71_INSCR
              and q30_graurisco = 'B';

          if V_INSCR_BAIXO_RISCO != 0 OR V_INSCR_BAIXO_RISCO is not null then

            delete from vistorianumpre where y69_codvist = iCodigoVistoria and y69_numpre = iNumpreGerado;
            return '26 - INSCRI«√O COM GRAU DE RISCO BAIXO, SE DESEJA CALCULAR ALTERE PARA SIM O PAR¬METRO GERAR C¡LCULO PARA BAIXO RISCO';
          end if;
        end if;

        if lCalculaVistoriaMei is false then

          lContribuinteMei := fc_verifica_contribuinte_mei(V_Y71_INSCR, dDataAtual);

          if lContribuinteMei then

            delete from vistorianumpre where y69_codvist = iCodigoVistoria and y69_numpre = iNumpreGerado;
            return '25 - CONTRIBUINTE OPTANTE PELO SIMPLES NACIONAL NA CATEGORIA MEI';
          end if;
        end if;

        if nPontuacaoClasse is null then

          sSql = sSql || ' select q81_qiexe, q81_qfexe, q81_codigo, q81_uqcad, q81_uqtab                ';
          sSql = sSql || '   from ativtipo                                                              ';
          sSql = sSql || '          inner join tipcalc on q81_codigo = q80_tipcal                       ';
          sSql = sSql || '  where q80_ativ = ' || iCodigoAtividade;
          sSql = sSql || '    and q81_tipo in ( select codigo from w_tipos_localizacao )                ';
          sSql = sSql || ' UNION                                                                        ';
          sSql = sSql || ' select q81_qiexe, q81_qfexe, q81_codigo, q81_uqcad, q81_uqtab                ';
          sSql = sSql || '   from issportetipo                                                          ';
          sSql = sSql || '        inner join issbaseporte on q45_inscr          = ' || V_Y71_INSCR;
          sSql = sSql || '        inner join tipcalc      on q41_codtipcalc     = q81_codigo            ';
          sSql = sSql || '        inner join cadcalc      on cadcalc.q85_codigo = tipcalc.q81_cadcalc   ';
          sSql = sSql || '        inner join clasativ     on q82_classe         = q41_codclasse         ';
          sSql = sSql || '  where q45_codporte = q41_codporte                                           ';
          sSql = sSql || '    and q81_tipo in (select codigo from w_tipos_localizacao )                 ';
          sSql = sSql || '    and q82_ativ = ' || iCodigoAtividade;

          select q60_campoutilcalc
            into v_tipo_quant
            from parissqn;

          if v_tipo_quant = 2 then

            select q30_quant
              into V_AREA
              from issquant
             where q30_inscr  = V_Y71_INSCR
               and q30_anousu = iAnousu;
          else

            select q30_area
              into V_AREA
              from issquant
             where q30_inscr  = V_Y71_INSCR
               and q30_anousu = iAnousu;
          end if;

          perform fc_debug('<fc_vistorias> V_AREA: ' || V_AREA, lRaise);
          perform fc_debug('<fc_vistorias> inscr : ' || V_Y71_INSCR, lRaise);
          perform fc_debug('<fc_vistorias> anousu: ' || iAnousu, lRaise);

          if V_AREA is null then
            V_AREA = 0;
          end if;

        else

          sSql = sSql || ' select q81_codigo,                                                              ';
          sSql = sSql || '        q81_recexe,                                                              ';
          sSql = sSql || '        q92_hist,                                                                ';
          sSql = sSql || '        q81_valexe,                                                              ';
          sSql = sSql || '        q92_tipo,                                                                ';
          sSql = sSql || '        q81_qiexe,                                                               ';
          sSql = sSql || '        q81_qfexe,                                                               ';
          sSql = sSql || '        q81_uqcad,                                                               ';
          sSql = sSql || '        q81_uqtab                                                                ';
          sSql = sSql || '   from ativtipo                                                                 ';
          sSql = sSql || '        inner join tipcalc     on tipcalc.q81_codigo     = ativtipo.q80_tipcal   ';
          sSql = sSql || '        inner join tipcalcexe  on tipcalcexe.q83_anousu  = ' || iAnousu;
          sSql = sSql || '                              and tipcalcexe.q83_tipcalc = tipcalc.q81_codigo    ';
          sSql = sSql || '        inner join cadcalc     on q81_cadcalc            = q85_codigo            ';
          ssql = ssql || '        inner join cadvencdesc on q92_codigo             = tipcalcexe.q83_codven ';
          sSql = sSql || '      where ' || nPontuacaoGeral || ' >= q81_qiexe and                          ';
          sSql = sSql ||                nPontuacaoGeral || '    <= q81_qfexe and                          ';
          sSql = sSql || '        Q81_TIPO IN ( select codigo from w_tipos_localizacao ) AND ATIVTIPO.Q80_ATIV = ' || iCodigoAtividade;

          perform fc_debug('<fc_vistorias> V_AREA: ' || V_AREA, lRaise);
        end if;

        select count(*)
          into iQuantidadeAtividades
          from ( select distinct
                        q07_seq
                   from tabativ
                        inner join ativtipo on ativtipo.q80_ativ = tabativ.q07_ativ
                        inner join tipcalc on q81_codigo = q80_tipcal
                  where q81_tipo in (3,5,6)
                    and q07_inscr = V_Y71_INSCR
                    and (q07_datafi is null or q07_datafi >= current_date)
                    and (q07_databx is null or q07_databx >= current_date) ) as x;

        perform fc_debug('<fc_vistorias> iQuantidadeAtividades: ' || iQuantidadeAtividades, lRaise);

        FOR rAtivtipo IN EXECUTE sSql LOOP

          if lRaise is true then
            raise notice 'dentro do for... vcalculou : % - tipcalc: % - area: % - qiexe: % - qfexe: %',lCalculou, rAtivtipo.Q81_CODIGO, V_AREA, rAtivtipo.Q81_QIEXE, rAtivtipo.Q81_QFEXE;
          end if;

          if lRaise is true then
            raise notice 'antes do if... area - % q81_qiexe - % q81_qfexe - %',V_AREA,rAtivtipo.Q81_QIEXE,rAtivtipo.Q81_QFEXE;
          end if;

          if lPorArea then
             V_AREA = nPontuacaoPorArea;
          end if;

          if V_AREA >= rAtivtipo.Q81_QIEXE and V_AREA <= rAtivtipo.Q81_QFEXE then

            select q81_recexe, q92_hist, q81_valexe, q92_tipo, q81_excedenteativ,
                   (select distinct q83_codven from tipcalcexe where q83_tipcalc = q81_codigo and q83_anousu = iAnousu)
              into iCodigoReceitaExercicio,
                   iCodigoHistoricoCalculo,
                   nValorExercicio,
                   iCodigoArretipo,
                   nValorExcedente, -- Valor excedente configurado na atividade
                   iCodVencimento
              from tipcalc
                   inner join tipcalcexe  on tipcalcexe.q83_anousu  = ianousu
                                         and tipcalcexe.q83_tipcalc = tipcalc.q81_codigo
                   inner join cadcalc     on q81_cadcalc            = q85_codigo
                   inner join cadvencdesc on q92_codigo             = tipcalcexe.q83_codven
            where Q81_CODIGO = rAtivtipo.Q81_CODIGO;


            if iCodVencimento is null then
              return '18-SEM VENCIMENTO CONFIGURADO PARA O EXERCICIO!';
            end if;

            perform fc_debug('Calculando valor do debito:',        lRaise);
            perform fc_debug('nValorExercicio  : ' || nValorExercicio,   lRaise);
            perform fc_debug('nValorInflator: ' || nValorInflator, lRaise);
            perform fc_debug('nValorBase    : ' || nValorBase,     lRaise);

            /**
             * Verifica se o parametro Utiliza quantidade do cadastro configurado no tipo de calculo esta setado
             */
            if rAtivtipo.q81_uqcad is true then

              select q30_mult
                into nMultiplicadorIssQuantidade
                from issquant
               where q30_inscr  = v_y71_inscr
                 and q30_anousu = iAnousu;
              if not found then
                  return '22 - inscricao sem multiplicador cadastrado na issquant';
              end if;

              if nMultiplicadorIssQuantidade is null or nMultiplicadorIssQuantidade = 0 then
                  nMultiplicadorIssQuantidade := 1;
              end if;
            end if;

            /**
             * Verifica se o parametro Utiliza quantidade da tabela de atividades configurado no
             * tipo de calculo esta setado
             */
            if rAtivtipo.q81_uqtab is true then

               select q07_quant
                 into nQuantidadeAtividade
                 from tabativ
                where q07_inscr = v_y71_inscr
                  and q07_ativ = iCodigoAtividade
                  and q07_databx is null;
               if not found then
                  return '23 - inscricao sem atividade cadastrada na tabativ ou atividade baixada';
               end if;

               if nQuantidadeAtividade is null or nQuantidadeAtividade = 0 then
                  nQuantidadeAtividade := 1;
               end if;
            end if;

            nValorVistoria = round(nValorExercicio * nValorInflator * nValorBase * nMultiplicadorIssQuantidade * nQuantidadeAtividade, 2);
            perform fc_debug('Resultado -> nValorVistoria: ' || nValorVistoria,  lRaise);

            lCalculou = true;

            if lRaise is true then
              raise notice 'nValorExcedente-: % nValorVistoria (1): % - k00_numpre: % - nValorInflator: % - nValorBase: %', nValorExcedente, nValorVistoria, iNumpreGerado, nValorInflator, nValorBase;
            end if;

            if nValorExcedente > 0 then

              raise notice 'valor antes: %', nValorVistoria;
              nValorVistoria = nValorVistoria + (nValorVistoria * 0.3 * (iQuantidadeAtividades - 1));
              raise notice 'valor depois: %', nValorVistoria;
            end if;

            --
            -- Funcao para gerar o financeiro
            --
            select *
              into rFinanceiro
              from fc_gerafinanceiro(iNumpreGerado, nValorVistoria, iCodVencimento, V_q02_NUMCGM, V_DATA, iCodigoReceitaExercicio, dDataVistoria);

            select k00_numpre
              into iNumpreArreinscr
              from arreinscr
             where k00_numpre = iNumpreGerado;

            if iNumpreArreinscr != 0 OR iNumpreArreinscr is not null then
              DELETE from ARREINSCR where K00_NUMPRE = iNumpreArreinscr;
            end if;
            insert into arreinscr (k00_numpre, k00_inscr)
                           values (iNumpreGerado, V_Y71_INSCR);
          end if;

        end loop;

        if lRaise is true then
          raise notice 'fora do for... lCalculou: %', lCalculou;
        end if;

        if lCalculou IS true then
          return '09-OK INSCRICAO NUMERO ' || V_Y71_INSCR;
        else
          return '19-OCORREU ALGUM ERRO DURANTE O CALCULO (2)!!!';
        end if;

      end if;

      if V_INSCR = false AND lIsSanitario = false then
          return '10- CALCULO NAO CONFIGURADO PARA A VISTORIA NUMERO ' || iCodigoVistoria;
      end if;

    else
          return '20-PROCEDIMENTO N√ÉO PREPARADO PARA CALCULO POR FORMA DIFERENTE DE 1 (NORMAL)';
    end if;

  end;

$$ language 'plpgsql';   

SQL1
    );
    }

    public function downFcVistoriasAlterada()
    {
    DB::statement(<<<SQL1

    CREATE OR REPLACE FUNCTION public.fc_vistorias(integer)
 RETURNS character varying
AS $$
DECLARE

  iCodigoVistoria             alias for $1;

  V_ATIVTIPO                  integer;
  iNumpreGerado               integer;
  iNumpreArreinscr            integer;
  iNumpreArrecant             integer;
  iNumpreArrecad              integer;
  V_DATA                      date;

  V_DIASGERAL                 integer;
  V_MESGERAL                  integer;
  V_PARCIAL                   boolean;

  V_ACHOU                     boolean default false;
  lCalculou                   boolean default false;
  V_DATAVENC                  date;
  V_Y74_CODSANI               integer;
  V_Y74_INSCRSANI             integer;
  V_Y80_NUMCGM                integer;
  V_Y69_NUMPRE                integer;
  iCodigoReceitaExercicio     integer;
  iCodigoHistoricoCalculo     integer;
  nValorExercicio             float8;
  iCodigoArretipo             integer;
  V_Y71_INSCR                 integer;
  V_Q02_NUMCGM                integer;
  V_ATIV                      integer;
  iAnousu                     integer;
  iCodigoAtividade            integer;
  V_DIASVENC                  integer;
  iFormaCalculo               integer;
  iCodVencimento              integer default 0;

  lIsSanitario                boolean;
  V_INSCR                     boolean;
  V_AREA                      float8;
  nValorInflator              float8;
  nValorBase                  float8;
  nValorVistoria              float8;
  sSql                        text default '';
  nValorExcedente             float8;
  iQuantidadeAtividades       integer default 0;

  iFormulaCalculoVistoria     integer;

  nPontuacaoClasse            integer default NULL;
  iPontuacaoPorZonaFiscal     integer;
  iPontuacaoPorEmpregadosArea integer;
  iPontuacaoPorEmpregados     integer;
  nPontuacaoPorArea           float8;
  nPontuacaoGeral             float8;

  lPorArea                    boolean default false;

  v_tipo_quant                integer;

  iInstit                     integer;
  iCodCli                     integer;

  dDataVistoria               date;
  dDataAtual                  date;

  rAtivtipo                   record;
  rSaniAtividade              record;
  rFinanceiro                 record;

  sTipoVistoriaSanitario      text;

  lRaise                      boolean default true;

  lCalculaVistoriaMei         boolean default true;
  lContribuinteMei            boolean default false;
  lVistoriaAtrasada           boolean default false;

  nQuantidadeAtividade        float8 default 1; -- Quantidade informada no campo q07_quant da tabativ
  nMultiplicadorIssQuantidade float8 default 1; -- Quantidade informada no campo q30_multi da issquant

  BEGIN

    /**
     * Verifica pl por Cliente Especifico
     */
    select db21_codcli
      into iCodCli
      from db_config
     where prefeitura is true;

    lRaise     := ( case when fc_getsession('DB_debugon') is null then false else true end );
    iInstit    := fc_getsession('DB_instit');
    dDataAtual := fc_getsession('DB_datausu');


    /**
     * 3 = VISTORIA LOCALIZACAO
     * 5 = TAXA
     * 6 = VISTORIA SANITARIO
     */
    begin

      if iCodCli = 4 then

        create temp table w_tipos_localizacao as select 3 as codigo;
        create temp table w_tipos_sanitario   as select 6 as codigo;

      elsif exists(select 1 from db_config where prefeitura is true and cgc = '88489786000101') then
      
     
        return fc_vistorias_charqueadas(iCodigoVistoria);

      

      if exists(select 1 from vistorias where y70_codvist = iCodigoVistoria and y70_tipovist = 10)
        then

          create temp table w_tipos_localizacao as select 5 as codigo;
          create temp table w_tipos_sanitario   as select 5 as codigo;
        else

          create temp table w_tipos_localizacao as select 3 as codigo;
          create temp table w_tipos_sanitario   as select 6 as codigo;
        end if;

      else
  
        create temp table w_tipos_localizacao as select 3 as codigo union select 5;
        create temp table w_tipos_sanitario   as select 5 as codigo union select 6;
      end if;
    exception

         when duplicate_table then

           truncate w_tipos_localizacao;
           truncate w_tipos_sanitario;
           if iCodCli = 4 then

             insert into w_tipos_localizacao values (3);
             insert into w_tipos_sanitario   values (6);
           else

             insert into w_tipos_localizacao values (3),(5);
             insert into w_tipos_sanitario   values (5),(6);
           end if;
    end;

    select fc_getsession('DB_funcao_vistorias_sanitario')
      into sTipoVistoriaSanitario;

    if sTipoVistoriaSanitario is not null and sTipoVistoriaSanitario <> '' then

      delete from w_tipos_sanitario;
      insert into w_tipos_sanitario select unnest(string_to_array(sTipoVistoriaSanitario, ','))::integer;
    end if;

    select extract(year from y70_data), y70_data
      into iAnousu, dDataVistoria
      from vistorias
     where y70_codvist = iCodigoVistoria;

    /**
     *  Verifica se a vistoria eh parcial ou geral, para montar a data de vencimento a ser gravada no arrecad
     */
    select y70_parcial
      into V_PARCIAL
      from vistorias
     where y70_codvist = iCodigoVistoria;

    if V_PARCIAL is not null AND V_PARCIAL = false then

      perform fc_debug('<fc_vistorias> GERAL', lRaise);

      select y77_diasgeral, y77_mesgeral, y70_data
        into  V_DIASGERAL,V_MESGERAL,V_DATA
        from tipovistorias
             inner join vistorias on y77_codtipo = y70_tipovist
       where y70_codvist = iCodigoVistoria;


      if V_DIASGERAL is null OR
         V_DIASGERAL = 0     OR
         V_MESGERAL is null  OR
         V_MESGERAL = 0      then

        return '01- TIPO DE VISTORIA SEM DIA OU MES PARA VENCIMENTO CONFIGURADO!';
      end if;

      V_DATAVENC = iAnousu||'-'||V_MESGERAL||'-'||V_DIASGERAL;

    else

      perform fc_debug('<fc_vistorias> PARCIAL', lRaise);

      select y77_dias, y70_data, y70_data, y77_diasgeral, y77_mesgeral
        into V_DIASVENC, V_DATA, V_DATAVENC, V_DIASGERAL, V_MESGERAL
        from tipovistorias
             inner join vistorias on y77_codtipo = y70_tipovist
       where y70_codvist = iCodigoVistoria;

      if V_DIASVENC is null OR V_DIASVENC = 0 then

        if V_DIASGERAL is null then
          return '02- TIPO DE VISTORIA SEM DIAS PARA VENCIMENTO CONFIGURADO!';
        else
          V_DATAVENC = iAnousu||'-'||V_MESGERAL||'-'||V_DIASGERAL;
        end if;
      end if;

      perform fc_debug('<fc_vistorias> V_DIASVENC: ' || V_DIASVENC || ' V_DATAVENC: ' || V_DATAVENC, lRaise);

      /**
       * V_DATA = V_DATAVENC;
       */
      if V_DIASVENC is null then
        V_DIASVENC = 0;
      end if;

      select V_DATAVENC + V_DIASVENC
        into V_DATAVENC;

    end if;

    perform fc_debug('<fc_vistorias> V_DATAVENC: ' || V_DATAVENC, lRaise);

    select y32_formvist,
           y32_calculavistoriamei
      into iFormulaCalculoVistoria,
           lCalculaVistoriaMei
      from parfiscal
     where y32_instit = iInstit ;

    if iFormulaCalculoVistoria = 1 then
      
       select q60_campoutilcalc
         into v_tipo_quant
         from parissqn;
       
    end if;

    if iFormulaCalculoVistoria =  1 and v_tipo_quant = 1 then /* Normal / por ·rea */
       lPorArea = true;
    end if;

    select q04_vbase
      into nValorBase
      from cissqn
     where cissqn.q04_anousu = iAnousu;

    if nValorBase = 0 OR nValorBase is null then
      return '03- SEM VALOR BASE CADASTRADO NOS PARAMETROS ';
    end if;

    select distinct i02_valor
      into nValorInflator
      from cissqn
           inner join infla on q04_inflat = i02_codigo
      where cissqn.q04_anousu             = iAnousu
        and date_part('year',i02_data)    = iAnousu
        and date_part('month',i02_data)   = date_part('month', fc_getsession('DB_datausu')::date);

    perform fc_debug('<fc_vistorias> Inflator: ' || nValorInflator, lRaise);

    if nValorInflator is null then
      nValorInflator = 1;
    end if;

    select y74_codsani,y80_numcgm,y69_numpre
      into V_Y74_CODSANI, V_Y80_NUMCGM, V_Y69_NUMPRE
      from vistsanitario
           inner join sanitario      on y74_codsani = y80_codsani
           left  join vistorianumpre on y69_codvist = iCodigoVistoria
     where Y74_CODVIST = iCodigoVistoria;

    perform fc_debug('<fc_vistorias> V_Y74_CODSANI: ' || V_Y74_CODSANI || ' iCodigoVistoria: ' || iCodigoVistoria, lRaise);

    if V_Y74_CODSANI = 0 OR V_Y74_CODSANI is null then

      lIsSanitario = false;
      select y71_inscr,q02_numcgm,y69_numpre
        into V_Y71_INSCR,V_Q02_NUMCGM,V_Y69_NUMPRE
        from vistinscr
             inner join issbase        on q02_inscr   = y71_inscr
             left  join vistorianumpre on y69_codvist = iCodigoVistoria
      where Y71_CODVIST = iCodigoVistoria;

      if V_Y71_INSCR is null then
        V_INSCR = false;
      else
        V_INSCR = true;
      end if;

    else
      lIsSanitario = true;
    end if;

    perform fc_debug('<fc_vistorias> lIsSanitario: ' || lIsSanitario || ' iFormulaCalculoVistoria: ' || iFormulaCalculoVistoria, lRaise);

    if iFormulaCalculoVistoria = 1 then

      if lIsSanitario = true OR V_INSCR = true then

        if lIsSanitario is true then

          V_ACHOU = false;
          perform fc_debug('<fc_vistorias> V_Y74_CODSANI: ' || V_Y74_CODSANI, lRaise);

          select min(q85_forcal)
            into iFormaCalculo
            from tipcalc
                 inner join cadcalc       on tipcalc.q81_cadcalc    = cadcalc.q85_codigo
                 inner join ativtipo      on ativtipo.q80_tipcal    = tipcalc.q81_codigo
                 inner join saniatividade on saniatividade.y83_ativ = ativtipo.q80_ativ
           where saniatividade.y83_codsani = V_Y74_CODSANI
             and saniatividade.y83_dtfim is null
             and tipcalc.q81_tipo in ( select codigo from w_tipos_sanitario );

          if iFormaCalculo is null then
            return '11-SEM FORMA DE CALCULO ENCONTRADA (SANI)!';
          end if;

          perform fc_debug('<fc_vistorias> iFormaCalculo: ' || iFormaCalculo, lRaise);

          if iFormaCalculo = 1 then

            select q80_ativ
              into iCodigoAtividade
              from saniatividade
                   inner join ativtipo on saniatividade.y83_ativ = ativtipo.q80_ativ
                   inner join tipcalc  on tipcalc.q81_codigo     = ativtipo.q80_tipcal
             where y83_codsani = V_Y74_CODSANI
               and y83_dtfim is null
               and q81_tipo in ( select codigo from w_tipos_sanitario )
               and y83_ativprinc is true;

            if iCodigoAtividade is not null then
              V_ACHOU = true;
            end if;

          elsif iFormaCalculo = 2 then

              select  Q80_ATIV
                into iCodigoAtividade
                from saniatividade
                     inner join ativtipo on saniatividade.y83_ativ = ativtipo.q80_ativ
                     inner join tipcalc  on tipcalc.q81_codigo     = ativtipo.q80_tipcal
               where y83_codsani = V_Y74_CODSANI
                 and y83_dtfim is null
                 and q81_tipo in ( select codigo from w_tipos_sanitario )
            order by q81_valexe desc
               limit 1;

            if iCodigoAtividade is not null then
              V_ACHOU = true;
            end if;

          end if;

          perform fc_debug('<fc_vistorias> iCodigoAtividade: ' || iCodigoAtividade, lRaise);

          if V_ACHOU is false then
            return '04- NENHUMA ATIVIDADE COM TIPO 6 CADASTRADA';
          end if;
        end if;

        if V_INSCR = true then

          select MIN(Q85_FORCAL)
          into iFormaCalculo
          from TIPCALC
          INNER JOIN CADCALC                ON TIPCALC.Q81_CADCALC = CADCALC.Q85_CODIGO
          INNER JOIN ATIVTIPO               ON ATIVTIPO.Q80_TIPCAL = TIPCALC.Q81_CODIGO
          INNER JOIN TABATIV          ON TABATIV.Q07_ATIV = ATIVTIPO.Q80_ATIV
          where Q07_INSCR = V_Y71_INSCR AND
          TABATIV.Q07_DATAFI is null AND
          TIPCALC.Q81_TIPO IN ( select codigo from w_tipos_localizacao );

          if iFormaCalculo is null then

            select MIN(Q85_FORCAL)
            into iFormaCalculo
            from ISSPORTETIPO
            INNER JOIN ISSBASEPORTE ON Q45_INSCR = V_Y71_INSCR
                                   and q45_codporte = q41_codporte
            INNER JOIN TIPCALC ON Q41_CODTIPCALC = Q81_CODIGO
            INNER JOIN CADCALC ON CADCALC.Q85_CODIGO = TIPCALC.Q81_CADCALC
            INNER JOIN CLASATIV ON Q82_CLASSE = Q41_CODCLASSE
            INNER JOIN TABATIV ON Q82_ATIV = Q07_ATIV AND Q07_INSCR = V_Y71_INSCR
            INNER JOIN ATIVPRINC ON ATIVPRINC.q88_inscr = TABATIV.q07_inscr and ATIVPRINC.q88_seq = TABATIV.q07_seq
            where Q45_CODPORTE = Q41_CODPORTE AND Q81_TIPO IN ( select codigo from w_tipos_localizacao ) and
            case when q07_datafi is null then true else q07_datafi >= V_DATA end AND
            q07_databx is null;

            if iFormaCalculo is null then
              return '17-SEM FORMA DE CALCULO ENCONTRADA (INSCR)!';
            end if;

          end if;

          perform fc_debug('Forma de Calculo encontrada: ' || iFormaCalculo, lRaise);
          perform fc_debug('v_data: ' ||v_data ||' V_Y71_INSCR: ' || V_Y71_INSCR, lRaise);

          /**
           * Pontuacao das classes
           */
            select Q82_ATIV, MAX(Q25_PONTUACAO)
              into iCodigoAtividade, nPontuacaoClasse
              from TABATIV
                   INNER JOIN CLASATIV   ON Q82_ATIV   = Q07_ATIV
                   INNER JOIN CLASSEPONT ON Q25_CLASSE = Q82_CLASSE
             where Q07_INSCR = V_Y71_INSCR
               AND case when q07_datafi is null
                        then true
                        else q07_datafi >= V_DATA end
               AND q07_databx is null
          GROUP BY Q82_ATIV
          ORDER BY MAX(Q25_PONTUACAO) DESC
             limit 1;

          if nPontuacaoClasse is not null then

            /**
             * Pontuacao zona fiscal
             */
            select Q26_PONTUACAO
              into iPontuacaoPorZonaFiscal
              from ZONAPONT
                   INNER JOIN ISSZONA ON Q26_ZONA = Q35_ZONA
             where Q35_INSCR = V_Y71_INSCR;

            if lPorArea then
               iPontuacaoPorZonaFiscal = 0;
            elsif iPontuacaoPorZonaFiscal is null then
              return '12-PONTUACAO DA ZONA NAO ENCONTRADA';
            end if;

            /**
             * Pontuacao empregados/area
             */
            select Q30_QUANT, Q30_AREA
              into iPontuacaoPorEmpregadosArea, V_AREA
              from ISSQUANT
             where ISSQUANT.Q30_INSCR  = V_Y71_INSCR
               AND ISSQUANT.Q30_ANOUSU = iAnousu;

            if iPontuacaoPorEmpregadosArea is null then
              select Q30_QUANT, Q30_AREA
              from ISSQUANT
              into iPontuacaoPorEmpregadosArea, V_AREA
              where ISSQUANT.Q30_INSCR = V_Y71_INSCR AND ISSQUANT.Q30_ANOUSU = (iAnousu - 1);
              if iPontuacaoPorEmpregadosArea is null then
                select Q30_QUANT, Q30_AREA
                from ISSQUANT
                into iPontuacaoPorEmpregadosArea, V_AREA
                where ISSQUANT.Q30_INSCR = V_Y71_INSCR AND ISSQUANT.Q30_ANOUSU = (iAnousu + 1);
                if iPontuacaoPorEmpregadosArea is null then
                  INSERT into ISSQUANT select * from ISSQUANT where ISSQUANT.Q30_INSCR = V_Y71_INSCR AND ISSQUANT.Q30_ANOUSU = (iAnousu - 1);
                end if;
              end if;
            end if;

            /**
             * Pontuacao pelos empregados
             */
            select Q27_PONTUACAO
              into iPontuacaoPorEmpregados
              from EMPREGPONT
             where iPontuacaoPorEmpregadosArea >= Q27_QUANTINI AND
                   iPontuacaoPorEmpregadosArea <= Q27_QUANTFIM;

            if lPorArea then
               iPontuacaoPorEmpregados = 0; 
            elsif iPontuacaoPorEmpregados is null then
              return '13-PONTUACAO DO NUMERO DE EMPREGADOS NAO ENCONTRADA';
            end if;

            if lRaise is true then
              raise notice 'V_AREA: %', V_AREA;
            end if;

            /**
             * Pontuacao pela area
             */
            if lPorArea then 

               nPontuacaoPorArea = V_AREA;

            else 
               select Q28_PONTUACAO
                 into nPontuacaoPorArea
                 from AREAPONT
                where V_AREA >= Q28_QUANTINI
                  AND V_AREA <= Q28_QUANTFIM;

            end if;

            if nPontuacaoPorArea is null then
              return '14-PONTUACAO DA AREA NAO ENCONTRADA';
            end if;

            if lRaise is true then
              raise notice 'nPontuacaoClasse: % - iPontuacaoPorZonaFiscal: %  - iPontuacaoPorEmpregados: %  - nPontuacaoPorArea: %', nPontuacaoClasse, iPontuacaoPorZonaFiscal, iPontuacaoPorEmpregados, nPontuacaoPorArea;
            end if;

            if lPorArea is false then 
               nPontuacaoGeral = nPontuacaoClasse + iPontuacaoPorZonaFiscal + iPontuacaoPorEmpregados + nPontuacaoPorArea;
               V_AREA = nPontuacaoGeral;
            else
               nPontuacaoGeral = nPontuacaoPorArea;
            end if;

            perform fc_debug('Pontuacaogeral: ' || nPontuacaoGeral ,lRaise);

            select Q81_CODIGO, Q81_RECEXE, Q92_HIST, Q81_VALEXE, Q92_TIPO
              into V_ATIVTIPO, iCodigoReceitaExercicio, iCodigoHistoricoCalculo, nValorExercicio, iCodigoArretipo
              from TIPCALC
                   inner join tipcalcexe on tipcalcexe.q83_anousu = iAnousu
                                        and tipcalcexe.q83_tipcalc = tipcalc.q81_codigo
                   INNER JOIN CADVENCDESC ON Q92_CODIGO = tipcalcexe.Q83_CODVEN
             where nPontuacaoGeral >= Q81_QIEXE
               AND nPontuacaoGeral <= Q81_QFEXE
               AND Q81_TIPO IN ( select codigo from w_tipos_localizacao );

         /**
          * Por ativtipo
          */
          else

            if iFormaCalculo = 1 then

              select Q80_ATIV
                into iCodigoAtividade
                from TABATIV
                     INNER JOIN ATIVPRINC ON ATIVPRINC.q88_inscr = TABATIV.q07_inscr
                                         and ATIVPRINC.q88_seq = TABATIV.q07_seq
                     INNER JOIN ATIVTIPO  ON TABATIV.Q07_ativ = ATIVTIPO.q80_ativ
                     INNER JOIN TIPCALC   ON TIPCALC.Q81_CODIGO = ATIVTIPO.Q80_TIPCAL
              where Q07_INSCR = V_Y71_INSCR
                AND TABATIV.Q07_DATAFI is null
                AND Q81_TIPO IN ( select codigo from w_tipos_localizacao );

              if iCodigoAtividade is not null then
                V_ACHOU = true;
              else

                select Q07_ATIV
                  into iCodigoAtividade
                  from ISSPORTETIPO
                       INNER JOIN ISSBASEPORTE ON Q45_INSCR = V_Y71_INSCR and q45_codporte = q41_codporte
                       INNER JOIN TIPCALC ON Q41_CODTIPCALC = Q81_CODIGO
                       INNER JOIN CADCALC ON CADCALC.Q85_CODIGO = TIPCALC.Q81_CADCALC
                       INNER JOIN CLASATIV ON Q82_CLASSE = Q41_CODCLASSE
                       INNER JOIN TABATIV ON Q82_ATIV = Q07_ATIV AND Q07_INSCR = V_Y71_INSCR
                       INNER JOIN ATIVPRINC ON ATIVPRINC.q88_inscr = TABATIV.q07_inscr
                                           and ATIVPRINC.q88_seq = TABATIV.q07_seq
                where Q45_CODPORTE = Q41_CODPORTE
                  AND Q81_TIPO IN ( select codigo from w_tipos_localizacao )
                  and case when q07_datafi is null then true else q07_datafi >= V_DATA end
                  AND q07_databx is null;

                if iCodigoAtividade is not null then
                  V_ACHOU = true;
                end if;

              end if;

            elsif iFormaCalculo = 2 then

                select Q80_ATIV
                  into iCodigoAtividade
                  from TABATIV
                       INNER JOIN ATIVTIPO ON TABATIV.Q07_ativ = ATIVTIPO.q80_ativ
                       INNER JOIN TIPCALC ON TIPCALC.Q81_CODIGO = ATIVTIPO.Q80_TIPCAL
                 where Q07_INSCR = V_Y71_INSCR
                   AND TABATIV.Q07_DATAFI is null
                   AND Q81_TIPO IN ( select codigo from w_tipos_localizacao )
              ORDER BY Q81_VALEXE DESC
                 LIMIT 1;

              if iCodigoAtividade is not null then
                V_ACHOU = true;
              end if;

            end if;

            if V_ACHOU is false then
              return '16 - SEM ATIVIDADE PRINCIPAL';
            end if;

            select TIPCALC.Q81_CODIGO
              into V_ATIVTIPO
              from ATIVTIPO
                   INNER JOIN TABATIV ON Q07_ATIV = q80_ativ
                   INNER JOIN TIPCALC ON Q80_TIPCAL = Q81_CODIGO
                   INNER JOIN CADCALC ON CADCALC.Q85_CODIGO = TIPCALC.Q81_CADCALC
             where Q81_TIPO IN ( select codigo from w_tipos_localizacao )
               AND Q07_INSCR = V_Y71_INSCR
               AND case when q07_datafi is null then true else q07_datafi >= V_DATA end
               AND q07_databx is null
               AND Q07_ATIV = iCodigoAtividade;

            if V_ATIVTIPO is null then

              select TIPCALC.Q81_CODIGO
                into V_ATIVTIPO
                from ISSPORTETIPO
                     INNER JOIN ISSBASEPORTE ON Q45_INSCR = V_Y71_INSCR and q45_codporte = q41_codporte
                     INNER JOIN TIPCALC ON Q41_CODTIPCALC = Q81_CODIGO
                     INNER JOIN CADCALC ON CADCALC.Q85_CODIGO = TIPCALC.Q81_CADCALC
                     INNER JOIN CLASATIV ON Q82_CLASSE = Q41_CODCLASSE
                     INNER JOIN TABATIV ON Q82_ATIV = Q07_ATIV AND Q07_INSCR = V_Y71_INSCR
               where Q45_CODPORTE = Q41_CODPORTE
                 AND Q81_TIPO IN ( select codigo from w_tipos_localizacao )
                 and case when q07_datafi is null then true else q07_datafi >= V_DATA end
                 AND q07_databx is null
                 AND Q82_ATIV = iCodigoAtividade;

              if V_ATIVTIPO is null then
                return '06-SEM TIPO DE CALCULO CONFIGURADO!';
              end if;
            end if;

          end if;

        end if;

        if V_Y69_NUMPRE = 0 OR V_Y69_NUMPRE is null then

          select NEXTVAL('numpref_k03_numpre_seq')
            into iNumpreGerado;

          INSERT into VISTORIANUMPRE VALUES(iCodigoVistoria, iNumpreGerado);
        else

          iNumpreGerado = V_Y69_NUMPRE;
          select k00_numpre
            into iNumpreArrecant
            from arrecant
           where k00_numpre = iNumpreGerado;

          if iNumpreArrecant != 0 OR iNumpreArrecant is not null then
            return '07- VISTORIA JA PAGA OU CANCELADA ';
          end if;

          select k00_numpre
            into iNumpreArrecad
            from arrecad
           where k00_numpre = iNumpreGerado;

          if iNumpreArrecad != 0 OR iNumpreArrecad is not null then
            delete from arrecad where k00_numpre = iNumpreGerado;
          end if;

        end if;
      end if;

      perform fc_debug('Verifica se sanitario lIsSanitario: ' || lIsSanitario,  lRaise);
      perform fc_debug('                           V_INSCR: ' || V_INSCR, lRaise);

      /**
       * SE FOR POR SANITARIO SEGUE AQUI
       */
      if lIsSanitario = true then

        FOR rSaniAtividade IN
          select Y83_ATIV
            from SANIATIVIDADE
           where Y83_CODSANI = V_Y74_CODSANI
             AND Y83_ATIV    = iCodigoAtividade

          LOOP

          if lRaise is true then
            raise notice 'Y83_ATIV (2): % - anousu: %', rSaniAtividade.Y83_ATIV, iAnousu;
          end if;

          select Y18_INSCR
            into V_Y74_INSCRSANI
            from sanitarioinscr
           where y18_codsani = V_Y74_CODSANI;

          if lCalculaVistoriaMei is false then
            lContribuinteMei := fc_verifica_contribuinte_mei(V_Y74_INSCRSANI, dDataAtual);

            if lContribuinteMei then

              delete from vistorianumpre where y69_codvist = iCodigoVistoria and y69_numpre = iNumpreGerado;
              return '25 - CONTRIBUINTE OPTANTE PELO SIMPLES NACIONAL NA CATEGORIA MEI';
            end if;
          end if;

          select q81_recexe, q92_hist, q81_valexe, q92_tipo,
                 (select distinct q83_codven
                    from tipcalcexe
                   where q83_tipcalc = q81_codigo
                     and q83_anousu  = iAnousu)
            into iCodigoReceitaExercicio,
                 iCodigoHistoricoCalculo,
                 nValorExercicio,
                 iCodigoArretipo,
                 iCodVencimento
            from ATIVTIPO
                 inner join tipcalc     on q80_tipcal             = q81_codigo
                 inner join tipcalcexe  on tipcalcexe.q83_anousu  = iAnousu
                                       and tipcalcexe.q83_tipcalc = tipcalc.q81_codigo
                 inner join cadvencdesc on q92_codigo             = tipcalcexe.q83_codven
           where q80_ativ = rsaniatividade.y83_ativ
             and ( select y80_area from sanitario where y80_codsani = V_Y74_CODSANI) >= q81_qiexe
             and ( select y80_area from sanitario where y80_codsani = V_Y74_CODSANI) <= q81_qfexe
             and q81_tipo in ( select codigo from w_tipos_sanitario );

          if iCodigoReceitaExercicio is not null then

            nValorVistoria = round(nValorExercicio * nValorInflator * nValorBase,2);

            -- Verifica se esta instalado o plugin para calcular a vistoria atrasada em dobro.
            perform *
               from db_plugin
              where db145_nome = 'PluginVistoriasAtrasadas'
                and db145_situacao is true;

            if found then

              lVistoriaAtrasada :=  fc_verifica_vistoria_atrasada(V_Y74_CODSANI, iCodigoVistoria, iAnousu);

              if lVistoriaAtrasada is true then
                nValorVistoria := nValorVistoria * 2;
              end if ;
            end if;


            if lRaise is true then
              raise notice 'inserindo no arrecad... nValorVistoria (2): % - iCodVencimento: %', nValorVistoria, iCodVencimento;
            end if;

            lCalculou = true;
            --
            -- Inserindo por sanitario
            --

            --
            -- Funcao para gerar o financeiro
            --
            if iCodVencimento is null then
              return '18-SEM VENCIMENTO CONFIGURADO PARA O EXERCICIO!';
            end if;

            if lRaise is true then
              raise notice 'executando fc_gerafinanceiro(%,%,%,%,%,%,%)', iNumpreGerado,nValorVistoria,iCodVencimento,V_Y80_NUMCGM,V_DATA,iCodigoReceitaExercicio,dDataVistoria;
            end if;

            select *
              into rFinanceiro
              from fc_gerafinanceiro(iNumpreGerado,nValorVistoria,iCodVencimento,V_Y80_NUMCGM,V_DATA,iCodigoReceitaExercicio,dDataVistoria);

            if V_Y74_INSCRSANI is not null then

              select k00_numpre
                into iNumpreArreinscr
                from arreinscr
               where k00_numpre = iNumpreGerado;

              if iNumpreArreinscr != 0 OR iNumpreArreinscr is not null then
                DELETE from ARREINSCR where K00_NUMPRE = iNumpreArreinscr;
              end if;

              INSERT into ARREINSCR (k00_numpre, k00_inscr)
                             VALUES (iNumpreGerado, V_Y74_INSCRSANI);

            end if;
          end if;
        end loop;

        if lCalculou IS true then
          return '08 - OK ';
        else
          return '15 - ERRO DURANTE O CALCULO';
        end if;
      /**
       * FIM DO if DO SANITARIO
       */

      /**
       * SE FOR POR INSCRICAO SEGUE AQUI
       */
      elsif V_INSCR = true then

        perform fc_debug('<fc_vistorias> Inscricao - nPontuacaoClasse: ' || nPontuacaoClasse || ' iCodigoAtividade: ' || iCodigoAtividade, lRaise);

        if lCalculaVistoriaMei is false then

          lContribuinteMei := fc_verifica_contribuinte_mei(V_Y71_INSCR, dDataAtual);

          if lContribuinteMei then

            delete from vistorianumpre where y69_codvist = iCodigoVistoria and y69_numpre = iNumpreGerado;
            return '25 - CONTRIBUINTE OPTANTE PELO SIMPLES NACIONAL NA CATEGORIA MEI';
          end if;
        end if;

        if nPontuacaoClasse is null then

          sSql = sSql || ' select q81_qiexe, q81_qfexe, q81_codigo, q81_uqcad, q81_uqtab                ';
          sSql = sSql || '   from ativtipo                                                              ';
          sSql = sSql || '          inner join tipcalc on q81_codigo = q80_tipcal                       ';
          sSql = sSql || '  where q80_ativ = ' || iCodigoAtividade;
          sSql = sSql || '    and q81_tipo in ( select codigo from w_tipos_localizacao )                ';
          sSql = sSql || ' UNION                                                                        ';
          sSql = sSql || ' select q81_qiexe, q81_qfexe, q81_codigo, q81_uqcad, q81_uqtab                ';
          sSql = sSql || '   from issportetipo                                                          ';
          sSql = sSql || '        inner join issbaseporte on q45_inscr          = ' || V_Y71_INSCR;
          sSql = sSql || '        inner join tipcalc      on q41_codtipcalc     = q81_codigo            ';
          sSql = sSql || '        inner join cadcalc      on cadcalc.q85_codigo = tipcalc.q81_cadcalc   ';
          sSql = sSql || '        inner join clasativ     on q82_classe         = q41_codclasse         ';
          sSql = sSql || '  where q45_codporte = q41_codporte                                           ';
          sSql = sSql || '    and q81_tipo in (select codigo from w_tipos_localizacao )                 ';
          sSql = sSql || '    and q82_ativ = ' || iCodigoAtividade;

          select q60_campoutilcalc
            into v_tipo_quant
            from parissqn;

          if v_tipo_quant = 2 then

            select q30_quant
              into V_AREA
              from issquant
             where q30_inscr  = V_Y71_INSCR
               and q30_anousu = iAnousu;
          else

            select q30_area
              into V_AREA
              from issquant
             where q30_inscr  = V_Y71_INSCR
               and q30_anousu = iAnousu;
          end if;

          perform fc_debug('<fc_vistorias> V_AREA: ' || V_AREA, lRaise);
          perform fc_debug('<fc_vistorias> inscr : ' || V_Y71_INSCR, lRaise);
          perform fc_debug('<fc_vistorias> anousu: ' || iAnousu, lRaise);

          if V_AREA is null then
            V_AREA = 0;
          end if;

        else

          sSql = sSql || ' select q81_codigo,                                                              ';
          sSql = sSql || '        q81_recexe,                                                              ';
          sSql = sSql || '        q92_hist,                                                                ';
          sSql = sSql || '        q81_valexe,                                                              ';
          sSql = sSql || '        q92_tipo,                                                                ';
          sSql = sSql || '        q81_qiexe,                                                               ';
          sSql = sSql || '        q81_qfexe,                                                               ';
          sSql = sSql || '        q81_uqcad,                                                               ';
          sSql = sSql || '        q81_uqtab                                                                ';
          sSql = sSql || '   from ativtipo                                                                 ';
          sSql = sSql || '        inner join tipcalc     on tipcalc.q81_codigo     = ativtipo.q80_tipcal   ';
          sSql = sSql || '        inner join tipcalcexe  on tipcalcexe.q83_anousu  = ' || iAnousu;
          sSql = sSql || '                              and tipcalcexe.q83_tipcalc = tipcalc.q81_codigo    ';
          sSql = sSql || '        inner join cadcalc     on q81_cadcalc            = q85_codigo            ';
          ssql = ssql || '        inner join cadvencdesc on q92_codigo             = tipcalcexe.q83_codven ';
          sSql = sSql || '      where ' || nPontuacaoGeral || ' >= q81_qiexe and                          ';
          sSql = sSql ||                nPontuacaoGeral || '    <= q81_qfexe and                          ';
          sSql = sSql || '        Q81_TIPO IN ( select codigo from w_tipos_localizacao ) AND ATIVTIPO.Q80_ATIV = ' || iCodigoAtividade;

          perform fc_debug('<fc_vistorias> V_AREA: ' || V_AREA, lRaise);
        end if;

        select count(*)
          into iQuantidadeAtividades
          from ( select distinct
                        q07_seq
                   from tabativ
                        inner join ativtipo on ativtipo.q80_ativ = tabativ.q07_ativ
                        inner join tipcalc on q81_codigo = q80_tipcal
                  where q81_tipo in (3,5,6)
                    and q07_inscr = V_Y71_INSCR
                    and (q07_datafi is null or q07_datafi >= current_date)
                    and (q07_databx is null or q07_databx >= current_date) ) as x;

        perform fc_debug('<fc_vistorias> iQuantidadeAtividades: ' || iQuantidadeAtividades, lRaise);

        FOR rAtivtipo IN EXECUTE sSql LOOP

          if lRaise is true then
            raise notice 'dentro do for... vcalculou : % - tipcalc: % - area: % - qiexe: % - qfexe: %',lCalculou, rAtivtipo.Q81_CODIGO, V_AREA, rAtivtipo.Q81_QIEXE, rAtivtipo.Q81_QFEXE;
          end if;

          if lRaise is true then
            raise notice 'antes do if... area - % q81_qiexe - % q81_qfexe - %',V_AREA,rAtivtipo.Q81_QIEXE,rAtivtipo.Q81_QFEXE;
          end if;

          if lPorArea then
             V_AREA = nPontuacaoPorArea;
          end if;

          if V_AREA >= rAtivtipo.Q81_QIEXE and V_AREA <= rAtivtipo.Q81_QFEXE then

            select q81_recexe, q92_hist, q81_valexe, q92_tipo, q81_excedenteativ,
                   (select distinct q83_codven from tipcalcexe where q83_tipcalc = q81_codigo and q83_anousu = iAnousu)
              into iCodigoReceitaExercicio,
                   iCodigoHistoricoCalculo,
                   nValorExercicio,
                   iCodigoArretipo,
                   nValorExcedente, -- Valor excedente configurado na atividade
                   iCodVencimento
              from tipcalc
                   inner join tipcalcexe  on tipcalcexe.q83_anousu  = ianousu
                                         and tipcalcexe.q83_tipcalc = tipcalc.q81_codigo
                   inner join cadcalc     on q81_cadcalc            = q85_codigo
                   inner join cadvencdesc on q92_codigo             = tipcalcexe.q83_codven
            where Q81_CODIGO = rAtivtipo.Q81_CODIGO;


            if iCodVencimento is null then
              return '18-SEM VENCIMENTO CONFIGURADO PARA O EXERCICIO!';
            end if;

            perform fc_debug('Calculando valor do debito:',        lRaise);
            perform fc_debug('nValorExercicio  : ' || nValorExercicio,   lRaise);
            perform fc_debug('nValorInflator: ' || nValorInflator, lRaise);
            perform fc_debug('nValorBase    : ' || nValorBase,     lRaise);

            /**
             * Verifica se o parametro Utiliza quantidade do cadastro configurado no tipo de calculo esta setado
             */
            if rAtivtipo.q81_uqcad is true then

              select q30_mult
                into nMultiplicadorIssQuantidade
                from issquant
               where q30_inscr  = v_y71_inscr
                 and q30_anousu = iAnousu;
              if not found then
                  return '22 - inscricao sem multiplicador cadastrado na issquant';
              end if;

              if nMultiplicadorIssQuantidade is null or nMultiplicadorIssQuantidade = 0 then
                  nMultiplicadorIssQuantidade := 1;
              end if;
            end if;

            /**
             * Verifica se o parametro Utiliza quantidade da tabela de atividades configurado no
             * tipo de calculo esta setado
             */
            if rAtivtipo.q81_uqtab is true then

               select q07_quant
                 into nQuantidadeAtividade
                 from tabativ
                where q07_inscr = v_y71_inscr
                  and q07_ativ = iCodigoAtividade
                  and q07_databx is null;
               if not found then
                  return '23 - inscricao sem atividade cadastrada na tabativ ou atividade baixada';
               end if;

               if nQuantidadeAtividade is null or nQuantidadeAtividade = 0 then
                  nQuantidadeAtividade := 1;
               end if;
            end if;

            nValorVistoria = round(nValorExercicio * nValorInflator * nValorBase * nMultiplicadorIssQuantidade * nQuantidadeAtividade, 2);
            perform fc_debug('Resultado -> nValorVistoria: ' || nValorVistoria,  lRaise);

            lCalculou = true;

            if lRaise is true then
              raise notice 'nValorExcedente-: % nValorVistoria (1): % - k00_numpre: % - nValorInflator: % - nValorBase: %', nValorExcedente, nValorVistoria, iNumpreGerado, nValorInflator, nValorBase;
            end if;

            if nValorExcedente > 0 then

              raise notice 'valor antes: %', nValorVistoria;
              nValorVistoria = nValorVistoria + (nValorVistoria * 0.3 * (iQuantidadeAtividades - 1));
              raise notice 'valor depois: %', nValorVistoria;
            end if;

            --
            -- Funcao para gerar o financeiro
            --
            select *
              into rFinanceiro
              from fc_gerafinanceiro(iNumpreGerado, nValorVistoria, iCodVencimento, V_q02_NUMCGM, V_DATA, iCodigoReceitaExercicio, dDataVistoria);

            select k00_numpre
              into iNumpreArreinscr
              from arreinscr
             where k00_numpre = iNumpreGerado;

            if iNumpreArreinscr != 0 OR iNumpreArreinscr is not null then
              DELETE from ARREINSCR where K00_NUMPRE = iNumpreArreinscr;
            end if;
            insert into arreinscr (k00_numpre, k00_inscr)
                           values (iNumpreGerado, V_Y71_INSCR);
          end if;

        end loop;

        if lRaise is true then
          raise notice 'fora do for... lCalculou: %', lCalculou;
        end if;

        if lCalculou IS true then
          return '09-OK INSCRICAO NUMERO ' || V_Y71_INSCR;
        else
          return '19-OCORREU ALGUM ERRO DURANTE O CALCULO (2)!!!';
        end if;

      end if;

      if V_INSCR = false AND lIsSanitario = false then
          return '10- CALCULO NAO CONFIGURADO PARA A VISTORIA NUMERO ' || iCodigoVistoria;
      end if;

    else
          return '20-PROCEDIMENTO N√ÉO PREPARADO PARA CALCULO POR FORMA DIFERENTE DE 1 (NORMAL)';
    end if;

  end;

$$ language 'plpgsql';    

SQL1
    );
    }

    public function upFcIssqnAlterada()
    {
    DB::statement(<<<SQL1

create or replace function fc_issqn(integer,date,integer,date,boolean,boolean,integer,varchar,integer,integer, integer default 0)
returns varchar(200)
as $$
declare

iInscr                    alias   for $1; -- inscricao que esta sendo calculada
dDatahj                   alias   for $2; -- data do sistema
iAnousu                   alias   for $3; -- ano do sistema
dDtbaixa                  alias   for $4; -- data da baixa se estiver calculando baixa
bRecalc                   alias   for $5; -- se e um recalculo
bGeral                    alias   for $6; -- se e um calculo geral
iInstit                   alias   for $7; -- instituicao
sAtivs                    alias   for $8; -- relacao das atividades separadas por virgula. Exemplo: 1,2,3,4,5
iTipoCalculo              alias   for $9; -- Tipo de calculo: 0 - Todos,
                            --                                1 - ISSQN,
                            --                                2 - Alvara
iNumeroParcelasAlvara     alias   for $10;-- Quantidade de Parcelas em que o Alvare deve ser dividido
iNumeroParcelasIssqn      alias   for $11;-- Quantidade de Parcelas em que o ISSQN deve ser dividido

iCalcfixvar               integer default 0;
iDiasvctoCissqn           integer default 0;
iConsiderarMesInicio      integer default 0;
iMesFinal                 integer default 0;
iDiaInicio                integer default 0;

v_vencproc                integer default 0;
v_quantexcluido           integer default 0;
v_diasjasomados           integer default 0;
v_anoatualservidor        integer;
v_tipo_quant              integer;
iTotalVencimentosCad      integer;
v_anoiniciocalc           integer;
v_mesinicio               integer;
v_numprejapago            integer;
v_uqtab                   integer;
v_uqcad                   float8;
v_ativprinc               integer;
v_codvencadcalc           integer;
v_codven                  integer;
v_sequencia               integer;
v_tabativ                 integer;
v_ativtipo                integer;
v_tipcalc                 integer;
iInscrexiste              integer;
v_quantativ               integer;
v_numpre                  integer;
v_numpar                  integer;
v_numtot                  integer;
v_numdig                  integer;
v_numcgm                  integer;
v_receita                 integer;
v_codbco                  integer;
iAux                      integer;
v_diasdesdeinicio         integer;
v_diasdestevcto           integer;
iTotalDiasAno             integer;
v_q81_rec                 integer;
iQtdeVencProcessar        integer;
v_totvenc                 integer default 0;
iQtdParcelas              integer;
iQtdParcelasPagas         integer;
v_forcal                  integer;
iDiasParaVencimento       integer default 0;
iAnoCadastroEmpresa       integer default 0;
iNumTot                   integer default 1;

v_qprovisorio             float8 default 1;
nValorParcela             float8;
v_valor                   float8;
v_valorgrav               float8 default 0;
v_quant                   float8;
v_base                    float8;
v_valinflator             float8;
v_q81_qini                float8;
v_q81_qfim                float8;
v_q81_val                 float8;
nPercentualParcela        float8;

nValorTotalPago           float8 default 0; -- total pago usado no caso de um recalculo
nDescontoPagamentoParcela float8 default 0; -- desconto por parcela, e o valor a ser descontado por parcela no caso de um recalculo com pagamentos
nPercPagoNovo             float8 default 0; -- percentual pago referente ao total do calculo, usado para calcular o desconto por parcela no caso de um recalculo com pagamento

dDataCadastro             date;
v_venc                    date;
v_vencvar                 date;
v_venccalc                date;
iPrimeiroDiaAno           date;
iUltimoDiaAno             date;
v_dtvencano               date;
dMaiorVencimentoCadvenc   date;
dInicioAtividade          date;
dInicioAtividadecalc      date;
v_dtbase                  date;
v_databaixa               date;
dDtProporcionalidade      date;
dVencimentoAtual          date;
dDataInicialCadSimples    date;

q81Tipos                  varchar;

v_cep                     char(8);
v_cepinstit               char(8);
v_integr                  char(1);
v_var                     char(1);
v_codage                  char(5);
v_numbco                  char(15);

v_descrvariavel           varchar(50);
v_descrtipcalc            varchar(40);
v_descrcadcalc            varchar(40);
sDescrProporcionalidade   varchar;

v_text                    text;
v_textexclui              text;
v_textexclui2             text;
v_manual                  text default '\n';
tManual                   text;
sSqlInsert                text;
sManualCorrecao           text;
sManualText               text;

v_provisorio              boolean default false;
v_cadcalc_var             boolean default false;
v_cadcalc_fix             boolean default false;
v_comcalculo              boolean default false;
v_continuar               boolean default true;
v_prim                    boolean;
v_jagravou                boolean;
lJaPassouUltVenc          boolean;
bTemFixado                boolean default false; -- se tem valor fixado(varfixval)
lProcessaParcVencidas     boolean default false;
lProcessaParcela          boolean;
lTabelasCriadas           boolean;
lInscricaoMei             boolean default false;
lOptanteSimples           boolean default false;

v_record_ativ             record;
v_record_tipcalc          record;
v_record_cadvenc          record;
v_record_cadcalc          record;
v_record_ativprinc        record;
v_record_maiorvalor       record;
v_record_somatodos        record;
v_record_variavel         record;
v_numprejacalculado       record;
v_record_excluir          record;
rDebitos                  record;
rParcelas           record;

dtOperacao                varchar;
iNumpreArquivoSimples     integer;

lRaise                    boolean default false; -- variavel para debug
lAbatimento               boolean default false;

lCalculaVistoriasMEI      boolean default false;

iNumeroParcelas           integer; -- Vari√°vel auxiliar para o n√∫mero de parcelas

arrecadAuxiliar           record; -- Vari√°vel para juntar parcelas iguais

calc_baixo_risco          boolean default true;

v_inscr_baixo_risco       integer;

begin

select to_char(fc_getsession('DB_datausu')::date, 'DD/MM/YYYY') into dtOperacao;

    if  dtOperacao is null then
        RAISE EXCEPTION 'Erro: variavel de sessao DB_datausu nao declarado!';
    end if;

if     iTipoCalculo = 1 then
    v_manual :=  '  ======================= Calculo de ISSQN          | Data de Calculo: ' || dtOperacao ||'  =========== \n';
elsif  iTipoCalculo = 2 then
    v_manual :=  '  ======================= Calculo de Alvara         | Data de Calculo: ' || dtOperacao ||'  =========== \n';
else
    v_manual :=  '  ======================= Calculo de ISSQN e Alvara | Data de Calculo: ' || dtOperacao ||'  =========== \n';
end if;

lRaise  := ( case when fc_getsession('DB_debugon') is null then false else true end );

perform fc_debug('INICIANDO CALCULO PARA INSCRICAO : '||iInscr||' EXERCICIO : '||iAnousu,lRaise,true,false);

perform fc_debug('DATA DE BAIXA - '||dDtbaixa,lRaise,false,false);

perform *
   from tabativ
  inner join clasativ
     on q82_ativ      = q07_ativ
  inner join issbaseporte
     on q45_inscr     = q07_inscr
  where q07_inscr = iInscr
    and (exists(select 1
                  from ativtipo
                 where q80_ativ = q07_ativ)
        or exists(select 1
                    from issportetipo
                   where q41_codclasse = q82_classe
                     and q41_codporte  = q45_codporte));

if not found then
    return '24-Empresa sem tipo de calculo configurado!';
end if;

--
-- Realizamos a confer√™ncia dos dados para sabermos se a inscri√ß√£o √© optante pelo SIMPLES
--
-- Caso seja optante n√£o ser√° calculado a taxa de alvar√° da empresa.
--
-- Deve possui cadastro na tabela meicgm...
-- OU
-- A empresa deve ser optante com:
--  - A categoria 3 - MEI;
--  - Data de in√≠cio do cadastro no simples menor ou igual a data de calculo;
--  - N√£o pode estar com o cadastro do simples baixado (isscadsimplesbaixa)
--

-- perform *
--     from meicgm
--         inner join issbase on issbase.q02_numcgm = meicgm.q115_numcgm
--     where issbase.q02_inscr = iInscr;

-- if found then
--     lInscricaoMei = true;
-- end if;

perform *
    from isscadsimples
    where isscadsimples.q38_inscr     = iInscr
    and isscadsimples.q38_categoria = 3
    and isscadsimples.q38_dtinicial <= dDatahj
    and not exists ( select 1
                        from isscadsimplesbaixa
                        where isscadsimplesbaixa.q39_isscadsimples = isscadsimples.q38_sequencial
                        and q39_dtbaixa <= dDatahj );
if found then
    lInscricaoMei = true;
end if;
--
-- FIM DA CONFER√äNCIA DE CALCULO DO MEI
--

-- Realizamos a confer√™ncia dos dados para sabermos se a inscri√ß√£o √© optante pelo SIMPLES
-- Caso seja optante, a data de vencimento ser√° sempre o dia 20 do m√™s.
select q38_dtinicial
    into dDataInicialCadSimples
    from isscadsimples
    where isscadsimples.q38_inscr     = iInscr
    and isscadsimples.q38_dtinicial <= dDatahj
    and not exists ( select 1
                        from isscadsimplesbaixa
                        where isscadsimplesbaixa.q39_isscadsimples = isscadsimples.q38_sequencial
                        and q39_dtbaixa <= dDatahj );
if found then
    lOptanteSimples = true;
end if;
-- FIM DA CONFER√äNCIA DE EMPRESAS OPTANTE PELO SIMPLES

--
-- Verificando par√¢metro Calcula Vistorias para MEI:
--
select y32_calculavistoriamei::boolean
    into lCalculaVistoriasMEI
    from parfiscal;

select extract(year from q02_dtinic)
    into iAnoCadastroEmpresa
    from issbase
    where q02_inscr = iInscr;

if iAnousu < iAnoCadastroEmpresa then
    return '24-N√£o pode ser feito calculo para exercicio menor que o ano de cadastramento da empresa.';
end if;

select fc_issqn_criatemptable(lRaise)
    into lTabelasCriadas;
if lTabelasCriadas is false then
    return '24-Problema ao criar as tabelas temporarias. ';
end if;

for rDebitos in
    select q01_numpre
    from isscalc
    where q01_inscr  = iInscr
        and q01_anousu = iAnousu
loop

    -- Verifica se existe Pagamento Parcial para o d√©bito informado
    select fc_verifica_abatimento(1,rDebitos.q01_numpre)::boolean into lAbatimento;

    if lAbatimento then
    return '24-Opera√ß√£o Cancelada, D√©bito com Pagamento Parcial!';
    end if;

end loop;


sSqlInsert := '
insert into ativs (q07_inscr,q07_perman,q07_seq,q07_calcula,q07_ativ,q03_descr,q07_datain,q07_datafi,q07_databx,q07_quant,q11_tipcalc)
        select distinct
                q07_inscr,
                q07_perman,
                q07_seq, \'*\'::char(1) as q07_calcula,
                q07_ativ,
                q03_descr,
                q07_datain,
                q07_datafi,
                q07_databx,
                q07_quant,
                q11_tipcalc
            from tabativ
                left outer join tabativtipcalc on q11_inscr   = q07_inscr
                                            and q11_seq     = q07_seq
                inner join ativtipo            on q07_ativ    = q80_ativ
                inner join tipcalc             on q80_tipcal  = q81_codigo
                inner join ativid              on q07_ativ    = q03_ativ
                inner join cadcalc             on q81_cadcalc = q85_codigo
        where q07_inscr ='||  iInscr ||'
            and q07_seq in ('||sAtivs||')
    union
        select distinct
                q07_inscr,
                q07_perman,
                q07_seq, \'*\'::char(1) as q07_calcula,
                q07_ativ,
                q03_descr,
                q07_datain,
                q07_datafi,
                q07_databx,
                q07_quant,
                q11_tipcalc
            from tabativ
                left outer join tabativtipcalc on q11_inscr     = q07_inscr
                                            and q11_seq       = q07_seq
                inner join clasativ            on q82_ativ      = q07_ativ
                inner join issbaseporte        on q45_inscr     = q07_inscr
                inner join issportetipo        on q41_codclasse = q82_classe
                                            and q41_codporte  = q45_codporte
                inner join tipcalc             on q81_codigo    = q41_codtipcalc
                inner join ativid              on q07_ativ      = q03_ativ
                inner join cadcalc             on q81_cadcalc   = q85_codigo
        where q07_inscr =  '|| iInscr ||'
            and q07_seq in ('||sAtivs||')' ;

execute sSqlInsert;

select count(*) from ativs into v_sequencia;

v_sequencia = 1;
--
-- Primeiro dia do ano para calculo
--
iPrimeiroDiaAno = to_char(iAnousu, '9999') || '-01-01';

--
-- Ultimo dia do ano para calculo
--
iUltimoDiaAno = to_char(iAnousu, '9999') || '-12-31';

--
-- Total de dias do ano
--
iTotalDiasAno  = iUltimoDiaAno::date - iPrimeiroDiaAno::date + 1;

select q02_inscr
    from issbase
    into iInscrexiste
    where q02_inscr = iInscr;
if iInscrexiste is null then
    return '11-INSCRICAO NAO CADASTRADA ';
end if;

select q02_dtinic,
        q02_dtinic,
        extract (month from q02_dtinic)
    from issbase
    into dInicioAtividade,
        dDataCadastro,
        v_mesinicio
    where q02_inscr = iInscr;

if dInicioAtividade is null then

    select q02_dtinic,
            extract (month from q02_dtinic)
    from issbase
    into dInicioAtividade,
            v_mesinicio
    where q02_inscr = iInscr;

    if dInicioAtividade is null then
    select min(q07_datain),
            extract (month from min(q07_datain))
        into dInicioAtividade,
            v_mesinicio
        from tabativ
        where (q07_datafi is null or q07_datafi >= dDatahj)
        and (q07_databx is null or q07_databx >= dDatahj);

    if dInicioAtividade is null then
        return '12-INSCRICAO SEM DATA DE INICIO CONFIGURADA! ';
    else
        v_manual = v_manual || 'data de inicio da inscricao (baseada na menor data de inicio das atividades): ' || dInicioAtividade || '\n';
    end if;
    else

    v_manual = v_manual || 'data de inicio da inscricao (baseada na data de cadastramento): ' || dInicioAtividade || '\n';

    end if;

else
    v_manual = v_manual || 'data de inicio da inscricao: ' || dInicioAtividade || '\n';
end if;

select extract (year from dDatahj) into v_anoatualservidor;

perform fc_debug('inscr: '||iInscr||' - inicio: '||dInicioAtividade,lRaise,false,false);

--*********************************** se calcula por area ou por quantidade de funcionarios ********************************************************
--
-- grande bloco que verifica a variavel v_quant, que ser√° utilizada para encontrar posteriormente o tipcalc a ser utilizado no calculo
--
select q60_campoutilcalc
    into v_tipo_quant
    from parissqn;

--  se o campo q60_campoutilcalc for igual a 1 calcula pelo campo q30_area

if v_tipo_quant is null then
    return '13-PARAMETRO DE QUANTIDADE PARA O CALCULO NAO CONFIGURADO NA TABELA PARISSQN';
end if;

--**************************************************************************************************************************************************

select q04_vbase,
        q04_calfixvar,
        q04_diasvcto
    from cissqn
    into v_base,
        iCalcfixvar,
        iDiasvctoCissqn
    where cissqn.q04_anousu = iAnousu;

if v_base = 0 or v_base is null then
    return '14-SEM VALOR BASE CADASTRADO NOS PARAMETROS ';
end if;

v_manual = v_manual || 'valor base configurado nos parametros: ' || v_base || '\n';

select q04_dtbase
    from cissqn
    into v_dtbase
where cissqn.q04_anousu = iAnousu;
if v_dtbase is null then
    return '15-SEM DATA BASE CADASTRADA NOS PARAMETROS ';
end if;
v_manual = v_manual || 'data base configurada nos parametros: ' || v_dtbase || '\n';

select distinct i02_valor
    from cissqn
    into v_valinflator
        inner join infla on q04_inflat = i02_codigo
    where cissqn.q04_anousu = iAnousu
    and date_part('year',i02_data)     = iAnousu
    and date_part('month',i02_data)    = date_part('month', fc_getsession('DB_datausu')::date);

if v_valinflator is null then
    v_valinflator = 1;
--      return 'valor do inflator nao configurado corretamente';
end if;
v_manual = v_manual || 'valor do inflator configurada nos parametros: ' || v_valinflator || '\n';

perform fc_debug('valor do inflator : '||v_valinflator,lRaise,false,false);

select q02_dtbaix
    from issbase
    into v_databaixa
    where q02_inscr = iInscr;
if v_databaixa is not null then
    return '16-INSCRICAO JA BAIXADA ';
end if;

-- Q81_TIPO:
-- 1 issqn
-- 4 alvara
-- 5 taxa de expediente


drop table if exists w_q81tipos;
create temp table w_q81tipos on commit drop as select 1 as q81_tipo union select 4  union select 5;


-- validacao para saoborja cgc = '88489786000101'
if exists(  select 1 from  issbase where q02_inscr = iInscr and  q02_formalocalvara = 2 ) and  exists(select 1 from db_config where prefeitura is true and cgc = '88489786000101') then

  delete from w_q81tipos where q81_tipo <> 1;
end if;


        select count(*) into v_quantativ from (
        select distinct q07_seq
            from tabativ
                inner join ativtipo on ativtipo.q80_ativ = tabativ.q07_ativ
                inner join tipcalc on q81_codigo = q80_tipcal
            where q81_tipo in (select q81_tipo from  w_q81tipos)
            and q07_inscr = iInscr
            and (q07_datafi is null or q07_datafi >= dDatahj)
            and (q07_databx is null or q07_databx >= dDatahj)) as x;


perform fc_debug('Total de atividades : '||v_quantativ,lRaise,false,false);

select q07_inscr
    from tabativ
    into v_tabativ
        inner join ativtipo on ativtipo.q80_ativ = tabativ.q07_ativ
    where q07_inscr = iInscr
    and q07_datain <= dDatahj;

select q07_inscr
    from tabativ
    into v_ativtipo
        inner join ativtipo on ativtipo.q80_ativ = tabativ.q07_ativ
    where q07_inscr = iInscr
    and (q07_datafi is null or q07_datafi >= dDatahj)
    and (q07_databx is null or q07_databx >= dDatahj);

select q07_inscr
    from tabativ
    into v_tipcalc
        inner join ativtipo on ativtipo.q80_ativ = tabativ.q07_ativ
        inner join tipcalc  on q81_codigo = q80_tipcal
    where q81_tipo in (select q81_tipo from  w_q81tipos)
    and q07_inscr = iInscr
    and q07_datain <= dDatahj;

perform fc_debug('Data da baixa : '||dDtbaixa,lRaise,false,false);

-- verifica quais os tipos de calculo para as atividades cadastradas para a inscricao

select cep
    from db_config
    into v_cepinstit
    where codigo = iInstit;

if v_cepinstit is null then
    return '17-PROBLEMAS COM A TABELA DB_CONFIG';
end if;
v_manual = v_manual || 'cep da instituicao: ' || v_cepinstit || '\n';

select q02_cep
    from issbase
    into v_cep
where q02_inscr = iInscr;

v_manual = v_manual || 'cep da inscricao: ' || v_cep || '\n';

v_manual = v_manual || '\n--- p r i m e i r a   e t a p a  -----\n';

select distinct
        q07_inscr
    into iAux
    from ativs;

perform fc_debug('Inscricao tabela temporaria (ativs) : '||iAux,lRaise,false,false);

-- ativs √© uma tabela tempor√°ria criada antes de chamar a funcao

for v_record_ativ in execute 'select * from ativs where q07_inscr = ' || iInscr loop


    v_manual = v_manual || '\nprocessando atividade: ' || v_record_ativ.q07_ativ || ' - ' || v_record_ativ.q03_descr || ' - sequencia: ' || v_record_ativ.q07_seq || ' - inicio: ' || v_record_ativ.q07_datain || '\n';
    --
    -- sempre entra aqui porque ninguem utiliza o esquema da tabela
    -- tabativtipcalc (tipo de calculo especifico para aquela atividade daquela inscricao)
    --
    if v_record_ativ.q11_tipcalc is null then
    v_text = 'select distinct
                        tipcalc.*,
                        cadcalc.q85_outromun,
                        cadcalc.q85_var,
                        case
                        when q81_tipo = 4 then
                            ( select q83_codven
                                from ativtipo ativtipo2
                                    inner join tipcalc    on ativtipo2.q80_tipcal = q81_codigo
                                    left  join tipcalcexe on q83_tipcalc = q81_codigo
                                                        and q83_anousu  = (select extract(year from q02_dtinic)
                                                                            from issbase
                                                                            where q02_inscr = '||iInscr||')
                            where ativtipo2.q80_ativ = ativtipo.q80_ativ and ativtipo2.q80_tipcal = ativtipo.q80_tipcal )
                        else
                            q83_codven
                        end as q83_codven
                from ativtipo
                        inner join tipcalc    on q80_tipcal = q81_codigo
                        left join tipcalcexe  on q83_tipcalc = q81_codigo
                                            and q83_anousu = ' || iAnousu || '
                        inner join cadcalc    on cadcalc.q85_codigo = tipcalc.q81_cadcalc
                where q81_tipo in (select q81_tipo from  w_q81tipos)
                    and q80_ativ = ' || v_record_ativ.q07_ativ || '
                union
                select tipcalc.*,
                        cadcalc.q85_outromun,
                        cadcalc.q85_var,
                        case
                        when q81_tipo = 4 then
                            ( select q83_codven
                                from tipcalc tipcalc2
                                    left  join tipcalcexe tipcalcexe2   on tipcalcexe2.q83_tipcalc = tipcalc2.q81_codigo
                                                            and tipcalcexe2.q83_anousu  = (select extract(year from issbase2.q02_dtinic)
                                                                                from issbase issbase2
                                                                                where issbase2.q02_inscr = '||iInscr||')
                                    inner join cadcalc cadcalc2  on cadcalc2.q85_codigo = tipcalc2.q81_cadcalc
                                    inner join clasativ clasativ2  on clasativ2.q82_classe = issportetipo.q41_codclasse and clasativ2.q82_ativ = ' || v_record_ativ.q07_ativ || '
                            where tipcalc2.q81_codigo = issportetipo.q41_codtipcalc )
                        else
                            q83_codven
                        end as q83_codven
                from issportetipo
                        inner join issbaseporte on q45_inscr = ' || iInscr || '
                        inner join tipcalc      on q41_codtipcalc = q81_codigo
                        left  join tipcalcexe   on q83_tipcalc = q81_codigo
                                            and q83_anousu = ' || iAnousu || '
                        inner join cadcalc      on cadcalc.q85_codigo = tipcalc.q81_cadcalc
                        inner join clasativ     on q82_classe = q41_codclasse
                where q45_codporte = q41_codporte
                    and q81_tipo in (select q81_tipo from  w_q81tipos)
                    and q82_ativ = ' || v_record_ativ.q07_ativ;

    v_textexclui = 'select distinct
                            tipcalc.q81_cadcalc
                        from ativtipo
                            inner join tipcalc on q80_tipcal = q81_codigo
                            inner join cadcalc on cadcalc.q85_codigo = tipcalc.q81_cadcalc
                        where q81_tipo in (select q81_tipo from  w_q81tipos)
                        and q80_ativ = ' || v_record_ativ.q07_ativ || '
                    union
                    select tipcalc.q81_cadcalc
                        from issportetipo
                            inner join issbaseporte on q45_inscr = ' || iInscr || '
                            inner join tipcalc      on q41_codtipcalc = q81_codigo
                            inner join cadcalc      on cadcalc.q85_codigo = tipcalc.q81_cadcalc
                            inner join clasativ     on q82_classe = q41_codclasse
                        where q45_codporte = q41_codporte
                        and q81_tipo in (select q81_tipo from  w_q81tipos)
                        and q82_ativ = ' || v_record_ativ.q07_ativ;
    else
    v_text = 'select tipcalc.*,
                        q85_outromun,
                        cadcalc.q85_var
                from tipcalc
                        left outer join cadcalc on cadcalc.q85_codigo = tipcalc.q81_cadcalc
                where q81_tipo in (select q81_tipo from  w_q81tipos)
                    and q81_codigo = ' || v_record_ativ.q11_tipcalc;
    end if;

    -- deletando calculo atual da inscricao do ano
    v_manual = v_manual || 'deletando os calculos antigos da inscricao no ano - apenas os que nao serao recalculados neste calculo\n';

    v_textexclui2 = 'select q01_numpre,
                            q01_cadcal
                        from isscalc
                    where q01_cadcal not in ( ' || v_textexclui || ')
                        and q01_inscr  = ' || iInscr || '
                        and q01_anousu = ' || iAnousu;

    -- limpa o financeiro do que foi selecionado pelo usuario antes de calcular

    for v_record_excluir in execute v_textexclui2 loop
        perform fc_debug('Numpre : '||v_record_excluir.q01_numpre,lRaise,false,false);
        -- verifica se n√£o esta no arrecant
        select k00_numpre
            from arrecant
            into v_numprejapago
            where k00_numpre = v_record_excluir.q01_numpre;
        if v_numprejapago is null then

            v_manual = v_manual || '     deletando numpre ' || v_record_excluir.q01_numpre || ' - calculo: ' || v_record_excluir.q01_cadcal || '\n';
            delete from arrecad where k00_numpre = v_record_excluir.q01_numpre;
            delete from isscalc where q01_numpre = v_record_excluir.q01_numpre;
            v_quantexcluido = v_quantexcluido + 1;

        else
            perform fc_debug('Numpre ja pago : '||v_record_excluir.q01_numpre,lRaise,false,false);
        end if;
    end loop;

    v_manual = v_manual || 'quantidade de calculos excluidos: ' || v_quantexcluido || ' \n';

    perform fc_debug('Atividade : '||v_record_ativ.q07_ativ,lRaise,false,false);

    perform fc_debug('',lRaise,false,false);
    perform fc_debug('Sql buscando os tipos de calculo : '||v_text,lRaise,false,false);
    perform fc_debug('',lRaise,false,false);

    -- para cada atividade retornada com seu tipo de calculo...


    for v_record_tipcalc in execute v_text loop


        select *
        into v_quant, tManual
        from fc_buscaquantidadeempresa( cast(iInscr                 as integer),
                                        cast(iAnousu                as integer),
                                        cast(v_tipo_quant           as integer),
                                        cast(v_record_ativ.q07_ativ as integer)
                                        );
        perform fc_debug('========== Excluindo registros da porcalculo e "do": '||tManual,lRaise,false,false);


        perform fc_debug('========== QUANTIDADES : '||tManual,lRaise,false,false);

        v_manual = v_manual||tManual;

        if v_quant = 0 and v_tipo_quant = 3 then
            return '30 - Erro buscando as quantidades para o calculo por pontua√ß√£o. Verifique o cadastro de pontua√ß√£o das Classes, Areas, Zonas e Empregados';
        end if;


    perform fc_debug('========== Tipo de calculo : '||v_record_tipcalc.q81_codigo||'-'||v_record_tipcalc.q81_descr||' - Quantidade : '||v_quant,lRaise,false,false);

    v_continuar = true;

    perform fc_debug('p r o c e s s a n d o  tipo de calculo: ' || v_record_tipcalc.q81_codigo || ' - ' || v_record_tipcalc.q81_descr || ' - gera: ' || v_record_tipcalc.q81_gera,lRaise,false,false);

    if  extract(year from dInicioAtividade )::integer = iAnousu then
        v_q81_rec  = v_record_tipcalc.q81_recexe;
        v_q81_qini = v_record_tipcalc.q81_qiexe;
        v_q81_qfim = v_record_tipcalc.q81_qfexe;
        v_q81_val  = v_record_tipcalc.q81_valexe;
    else
        v_q81_rec  = v_record_tipcalc.q81_recpro;
        v_q81_qini = v_record_tipcalc.q81_qipro;
        v_q81_qfim = v_record_tipcalc.q81_qfpro;
        v_q81_val  = v_record_tipcalc.q81_valpro;
    end if;

    perform fc_debug('Quantidade : '||coalesce(v_quant,0)||' Entre : '||coalesce(v_q81_qini,0)||' e '||coalesce(v_q81_qfim,0),lRaise,false,false);

    if coalesce(v_quant,0) >= coalesce(v_q81_qini,0) and coalesce(v_quant,0) <= coalesce(v_q81_qfim,999999) then

        perform fc_debug('Dentro do if da quantidade gera : '||v_record_tipcalc.q81_gera,lRaise,false,false);
        perform fc_debug('Ano de inicio de atividades : '||to_number(substr(dInicioAtividade,1,4),'99999'),lRaise,false,false);

        if v_record_tipcalc.q81_gera = 1 and to_number(substr(dInicioAtividade,1,4),'99999') < iAnousu then
        perform fc_debug('nao vai processar...',lRaise,false,false);
        v_manual = v_manual || '\n';
        else

        perform fc_debug('entrou no gera 2 do if - verificando pagamentos ',lRaise,false,false);

        v_manual = v_manual || 'verificando pagamentos\n';

        for v_numprejacalculado in
            select q01_numpre
            from isscalc
            where q01_anousu = iAnousu
                and q01_inscr  = iInscr
                and q01_cadcal = v_record_tipcalc.q81_cadcalc
                and q01_recei  = v_q81_rec
        loop

            perform fc_debug('procurando numpre : '||v_numprejacalculado.q01_numpre,lRaise,false,false);
            v_manual = v_manual || 'processando numpre: ' || v_numprejacalculado.q01_numpre || '\n';

            -- trocado ordem de verifica√ß√£o de arrecad, arrecant para arrecant, arrecad
            select k00_numpre
            from arrecant
            into v_numprejapago
            where k00_numpre = v_numprejacalculado.q01_numpre;

            perform fc_debug('q01_numpre: '|| v_numprejacalculado.q01_numpre,lRaise,false,false);

            if v_numprejacalculado.q01_numpre is not null then

            perform fc_debug('v_numprejacalculado.q01_numpre is not null - v_numprejapago: '||v_numprejapago,lRaise,false,false);

            if v_numprejapago is null then

                v_manual = v_manual || 'numpre em aberto\n';

                if dDtbaixa is not null and v_record_tipcalc.q85_var is true then
                v_manual = v_manual || 'se data da baixa preenchido e variavel, deleta do arrecad e issvar\n';

                perform fc_debug('Deletando do arrecad e issvar',lRaise,false,false);

                delete from arrecad where k00_numpre = v_numprejacalculado.q01_numpre and k00_numpar >= to_number(substr(dDtbaixa,6,2),'999') + 1;

                perform *
                    from issvarlev
                        inner join issvar on issvar.q05_codigo = issvarlev.q18_codigo
                    where q05_numpre = v_numprejacalculado.q01_numpre
                    and q05_numpar >= to_number(substr(dDtbaixa,6,2),'999') + 1;
                if found then
                    return '28 - EMPRESA JA POSSUI LEVANTAMENTO FISCAL PARA COMPETENCIA : '||(to_number(substr(dDtbaixa,6,2),'999') + 1)||'/'||iAnousu ;
                end if;

                delete from issvarnotas
                    using issvar
                    where issvar.q05_codigo  = issvarnotas.q06_codigo
                    and issvar.q05_numpre  = v_numprejacalculado.q01_numpre
                    and issvar.q05_numpar >= to_number(substr(dDtbaixa,6,2),'999') + 1;

                delete from issvar
                    where q05_numpre = v_numprejacalculado.q01_numpre
                    and q05_numpar >= to_number(substr(dDtbaixa,6,2),'999') + 1;

                elsif dDtbaixa is not null then

                v_manual = v_manual || 'se data da baixa preenchido e nao variavel, insere no isscalcant e deleta do isscalc e arrecad\n';

                perform fc_debug('Deletando do arrecad e isscalv',lRaise,false,false);

                insert into isscalcant select * from isscalc where q01_numpre = v_numprejacalculado.q01_numpre;
                delete from isscalc where q01_numpre = v_numprejacalculado.q01_numpre;
                delete from arrecad where k00_numpre = v_numprejacalculado.q01_numpre;
                end if;

            else

                perform fc_debug('Numpre pago.',lRaise,false,false);

                v_manual = v_manual || 'numpre pago\n';
                v_comcalculo = true;

            end if;

            if v_numprejapago is null then

                select k00_numpre
                from arrecad
                into v_numprejapago
                where k00_numpre = v_numprejacalculado.q01_numpre;

                if v_numprejapago is not null then

                if bRecalc is false then
                    return '18-INSCRICAO JA CALCULADA E RECALCULO NAO PASSADO COMO PARAMETRO';
                end if;

                perform fc_debug('recalculo',lRaise,false,false);

                v_manual = v_manual || 'recalculo\n';
                v_comcalculo = true;
                end if;

            end if;

            end if;

        end loop;

        perform fc_debug('saiu do procura pagamentos - continuar : '||( case when v_continuar is true then 'true' else 'false' end ),lRaise,false,false);

        if v_continuar then

            -- se true, busca quantidade do tabativ, senao default 1
            if v_record_tipcalc.q81_uqtab is false then

            perform fc_debug('uqtab false',lRaise,false,false);

            v_uqtab = 1;
            v_manual = v_manual || 'quantidade utilizada da tabela para calculo (utilizada default do sistema):' || v_uqtab || '\n';
            else

            perform fc_debug('uqtab true',lRaise,false,false);

            if v_record_ativ.q07_quant = 0 then
                v_uqtab = 1;
            else
                v_uqtab = v_record_ativ.q07_quant;
            end if;
            v_manual = v_manual || 'quantidade utilizada para calculo (baseada na quantidade da atividade lancada): ' || v_uqtab || '\n';
            end if;

            -- se true, busca quantidade do issquant, senao default 1
            if v_record_tipcalc.q81_uqcad is false then
            v_uqcad = 1;
            else
            select q30_mult
                from issquant
                into v_uqcad
                where issquant.q30_inscr = iInscr
                and issquant.q30_anousu = iAnousu;
            if v_uqcad is null then
                return '19-MULTIPLICADOR NAO LANCADO PARA ESTA INSCRICAO';
            end if;

            end if;

            if v_record_tipcalc.q81_integr is true then
            v_integr = '1';
            else
            v_integr = '0';
            end if;
            v_manual = v_manual || 'integral: (1 = sim - 0 = nao): ' || v_integr || '\n';

            select case when q85_var is true then '1' else '0' end as q85_var
            from cadcalc
            into v_var
            where cadcalc.q85_codigo = v_record_tipcalc.q81_cadcalc;
            if v_var is null then
            return '20-NAO DEFINIDO NO CADASTRO DE CALCULO SE VARIAVEL OU NAO';
            end if;

            select q85_forcal
            from cadcalc
            into v_forcal
            where cadcalc.q85_codigo = v_record_tipcalc.q81_cadcalc;
            if v_forcal is null then
            return '21-NAO DEFINIDO NO CADASTRO DE CALCULO A FORMA DE CALCULO';
            end if;
            v_manual = v_manual || 'forma de calculo (1 = atividade principal - 2 = atividade com maior valor - 3 = soma do valor das atividades):' || v_forcal || '\n';

            select q85_perman
            from cadcalc
            into v_provisorio
            where cadcalc.q85_codigo = v_record_tipcalc.q81_cadcalc;

            if v_provisorio is true and v_record_ativ.q07_perman is false then

            v_qprovisorio = v_record_tipcalc.q81_percprovis;
            v_manual = v_manual || 'provisorio: vai acrescer ' || v_qprovisorio || ' por centro no valor calculado\n';

            perform fc_debug('Provisorio -- '||v_qprovisorio,lRaise,false,false);

            else

            v_qprovisorio = 1;

            perform fc_debug('Nao provisorio',lRaise,false,false);

            end if;

            perform fc_debug('inserindo na tabela tudo... sequencia:'||v_sequencia,lRaise,false,false);
            perform fc_debug('q81_val: '||v_q81_val||' - v_base: '|| v_base||' - uqtab: '||v_uqtab||' - uqcad: '||v_uqcad||' - qprovisorio: '||v_qprovisorio||' - valinflator: '||v_valinflator,lRaise,false,false);

            if v_record_tipcalc.q83_codven is null then
            return '25-VENCIMENTO NAO ENCONTRADO NO CADASTRO DE TIPO DE CALCULO';
            else
            v_codven = v_record_tipcalc.q83_codven;
            end if;
            perform fc_debug('q81_codigo: '||v_record_tipcalc.q81_codigo||' - q81_cadcalc: '||v_record_tipcalc.q81_cadcalc||' - vencimento: '||v_codven,lRaise,false,false);

            --
            -- Se empresa for optante pelo simples nao deve ser efetuado
            -- calculo de alvara nem de taxa de expediente
            --
            if lInscricaoMei then

            if v_record_tipcalc.q81_cadcalc = 1 then
                perform fc_debug('Inscricao optante pelo MEI, nao calculando alvara.',lRaise,false,false);
                continue;
            end if;

            -- Se par√¢metro Calcula Vistorias para MEI: estiver N√ÉO ent√£o ignora taxa de expediente

            if lCalculaVistoriasMEI = false then

                if v_record_tipcalc.q81_tipo = 5 then -- tipo de c√°lculo for taxa

                perform fc_debug('Inscricao optante pelo MEI, nao calculando TAXA DE EXPEDIENTE.',lRaise,false,false);
                continue;
                end if;

            end if;

            end if;


            insert into
            tudo values (iAnousu,
                            iInscr,
                            v_record_ativ.q07_ativ,
                            v_record_tipcalc.q81_codigo,
                            v_record_tipcalc.q81_cadcalc,
                            v_base,
                            v_record_tipcalc.q81_recexe,
                            v_record_tipcalc.q81_recpro,
                            coalesce(v_quant,0),
                            v_record_tipcalc.q81_qiexe,
                            v_record_tipcalc.q81_qfexe,
                            v_uqtab,
                            v_uqcad,
                            v_forcal,
                            v_codven,
                            v_integr,
                            v_record_tipcalc.q81_tippro,
                            v_q81_val * v_base * v_uqtab * v_uqcad * v_qprovisorio * v_valinflator,
                            v_q81_val * v_qprovisorio * v_valinflator,
                            v_record_ativ.q07_datain,
                            coalesce( v_record_ativ.q07_datafi,(iAnousu||'-12-31')::date ),
                            v_var,
                            v_record_tipcalc.q81_gera,
                            v_sequencia);

                v_sequencia = v_sequencia + 1;

        end if;

        end if;

    end if;

    end loop;
    perform fc_debug('Terminou atividade',lRaise,false,false);

end loop;

--
-- SEGUNDA FASE DO CALCULO
-- na fase anterior a rotina insere registros na tabela tudo e nela √© que se baseia daqui para frente para saber o que calcular
--

perform fc_debug('--------------------------------------------------------------------------------------------------------',lRaise,false,false);
perform fc_debug('',lRaise,false,false);
perform fc_debug('SEGUNDA FASE DO CALCULO',lRaise,false,false);
perform fc_debug('',lRaise,false,false);
perform fc_debug('Na fase anterior a rotina insere registros na tabela tudo e nela √© que se baseia daqui para frente para saber o que calcular',lRaise,false,false);

if iTipoCalculo = 0  then

    select q60_calcbaixorisco
          into calc_baixo_risco
          from parissqn;

    if calc_baixo_risco is false then
              
        select q30_inscr
            into v_inscr_baixo_risco
            from issquant
            where q30_inscr = iInscr
            and q30_graurisco = 'B';

        if v_inscr_baixo_risco != 0 OR v_inscr_baixo_risco is not null then

            perform fc_debug('Excluindo registros de Alvar√°', lRaise, false, false);
            DELETE FROM tudo where cadcalc not in (2,3);
        end if;
    end if;

elseif iTipoCalculo = 1  then

    perform fc_debug('Excluindo registros de Alvar√°', lRaise, false, false);
    DELETE FROM tudo where cadcalc not in (2,3);
elseif iTipoCalculo = 2  then

    perform fc_debug('Excluindo registros de ISSQN', lRaise, false, false);
    DELETE FROM tudo where cadcalc not in (1,4,9);
end if;


select count(*) into iAux from tudo;

v_manual = v_manual || '\nregistros processados na etapa de tipos de calculo: ' || iAux || '\n';
v_manual = v_manual || '\n---- s e g u n d a  e t a p a  -----\n';
v_manual = v_manual || 'agrupando por cadastro de calculo' || '\n';

perform fc_debug('Quantidade de registros tabela tudo : '||iAux,lRaise,false,false);


--
-- for na tabela tudo com os tipos de calculo a utilizar
-- veja o detalhe do group by, que faz com que apenas um cadcalc (ALVARA/ISSQN FIXO/ISSQN VARIAVEL) seja utilizado por calculo
-- nessa fase o sistema cria registros na tabela porcalculo que ser√° utilizada nessa fase
--
for v_record_cadcalc in select cadcalc, forcal, var from tudo group by cadcalc, forcal, var loop

    select q85_descr
    into v_descrcadcalc
    from cadcalc
    where q85_codigo = v_record_cadcalc.cadcalc;

    v_manual = v_manual || '   processando calculo ' || v_record_cadcalc.cadcalc || ' - ' || v_descrcadcalc || '\n';

    perform fc_debug('Var : '||v_record_cadcalc.var||' cadcalc : '||v_record_cadcalc.cadcalc,lRaise,false,false);

    -- se for variavel
    if v_record_cadcalc.var = '1' then

    v_manual = v_manual || '      variavel\n';

    -- pode ser fixado por inscricao
    for v_record_variavel in select * from tudo where tudo.cadcalc = v_record_cadcalc.cadcalc limit 1 loop

        perform fc_debug('Inserindo na tabela tudo fixado por inscricao',lRaise,false,false);

        execute 'insert into porcalculo values ('
        || iAnousu || ','
        || iInscr  || ','
        || v_base || ','
        || v_record_variavel.tipcalc || ','
        || v_record_variavel.cadcalc || ','
        || v_record_variavel.forcal  || ','
        || v_record_variavel.codven  || ','
        || v_record_variavel.integr  || ','
        || '''' || v_record_variavel.tipopro || '''' || ','
        || '''' || v_record_variavel.inicio  || '''' || ','
        || '''' || coalesce( v_record_variavel.final,(iAnousu||'-12-31')::date )   || '''' || ','
        || 0 || ','
        || v_record_variavel.valori || ','
        || 0 || ','
        || '''' || v_record_variavel.var     || '''' || ','
        || v_record_variavel.gera || ','
        || v_record_variavel.seq
        || ');';

    end loop;

    -- se NAO for variavel
    else

    perform fc_debug('Inserindo na tabela tudo pela atividade principal',lRaise,false,false);

    v_manual = v_manual || '      nao variavel\n';

    -- se for pela atividade principal
    if v_record_cadcalc.forcal = 1 then
        v_manual = v_manual || '         calculando pela atividade principal\n';

        select q07_ativ
        into v_ativprinc
        from ativprinc
                inner join tabativ on q07_inscr = q88_inscr and q07_seq = q88_seq
        where q88_inscr = iInscr;
        if v_ativprinc is null then
        return '22-SEM ATIVIDADE PRINCIPAL CADASTRADA PARA ESTA INSCRICAO ';
        end if;

--        for v_record_ativprinc in select * from tudo where tudo.cadcalc = v_record_cadcalc.cadcalc and tudo.ativ = v_ativprinc loop
--        DESCOBRIR QUEM E PORQUE COMENTARAM A LINHA ACIMA
--        PORQUE PELA LOGICA DEVERIA UTILIZAR A LINHA COMENTADA

        for v_record_ativprinc in select * from tudo where tudo.cadcalc = v_record_cadcalc.cadcalc limit 1 loop

        v_manual = v_manual || '            inserindo na tabela de tipos de calculo a processar - tipcalc: ' || v_record_ativprinc.tipcalc || ' - cadcalc: ' || v_record_ativprinc.cadcalc || '\n';
        execute 'insert into porcalculo values ('
        || iAnousu || ','
        || iInscr  || ','
        || v_base || ','
        || v_record_ativprinc.tipcalc || ','
        || v_record_ativprinc.cadcalc || ','
        || v_record_ativprinc.forcal  || ','
        || v_record_ativprinc.codven  || ','
        || v_record_ativprinc.integr  || ','
        || '''' || v_record_ativprinc.tipopro || '''' || ','
        || '''' || v_record_ativprinc.inicio  || '''' || ','
        || '''' || coalesce( v_record_ativprinc.final, (iAnousu||'-12-31')::date )   || '''' || ','
        || v_record_ativprinc.valor   || ','
        || v_record_ativprinc.valori  || ','
        || 0 || ','
        || '''' || v_record_ativprinc.var     || '''' || ','
        || v_record_ativprinc.gera    || ','
        || v_record_ativprinc.seq
        || ');';

        end loop;

    end if;

    -- pela atividade que gerou o maior valor
    if v_record_cadcalc.forcal = 2 then
        v_manual = v_manual || 'calculando pela atividade que gerou o maior valor\n';

        for v_record_maiorvalor in select * from tudo where tudo.cadcalc = v_record_cadcalc.cadcalc order by valor desc limit 1 loop

        perform fc_debug('Inserindo na tabela porcalculo pela atividade de maior valor valor : '||v_record_maiorvalor.valor||' - tipo de calculo: '||v_record_maiorvalor.tipcalc||' - vencimento: '||v_record_maiorvalor.codven,lRaise,false,false);

        execute 'insert into porcalculo values ('
        || iAnousu || ','
        || iInscr  || ','
        || v_base || ','
        || v_record_maiorvalor.tipcalc || ','
        || v_record_maiorvalor.cadcalc || ','
        || v_record_maiorvalor.forcal  || ','
        || v_record_maiorvalor.codven  || ','
        || v_record_maiorvalor.integr  || ','
        || '''' || v_record_maiorvalor.tipopro || '''' || ','
        || '''' || v_record_maiorvalor.inicio  || '''' || ','
        || '''' || coalesce( v_record_maiorvalor.final, (iAnousu||'-12-31')::date )   || '''' || ','
        || v_record_maiorvalor.valor   || ','
        || v_record_maiorvalor.valori  || ','
        || 0 || ','
        || '''' || v_record_maiorvalor.var     || '''' || ','
        || v_record_maiorvalor.gera    || ','
        || v_record_maiorvalor.seq
        || ');';

        end loop;

    end if;

    -- pela soma de todos os valores calculados
    -- ainda nao implementado totalmente
    -- ou seja, nao funciona ainda...
    -- TESTADORES DEVEM UTILIZAR ESSA FORMULA DE CALCULO NOS TESTES
    if v_record_cadcalc.forcal = 3 then
        v_manual = v_manual || 'calculando pela soma de todos os valores\n';

        for v_record_somatodos in select * ,
                                        (select sum(valor) as somatotal
                                            from tudo
                                            where tudo.cadcalc = v_record_cadcalc.cadcalc) as somatotal
                                    from tudo
                                    where tudo.cadcalc = v_record_cadcalc.cadcalc loop

        select q85_codven
        from cadcalc
        into v_codvencadcalc
        where q85_codigo = v_record_cadcalc.cadcalc;
        if v_codvencadcalc is null then
            return '23-SEM VENCIMENTO PADRAO NO CADASTRO DE VENCIMENTOS ';
        end if;

        execute 'insert into porcalculo values ('
        || iAnousu || ','
        || iInscr  || ','
        || v_base || ','
        || v_record_somatodos.tipcalc || ','
        || v_record_cadcalc.cadcalc || ','
        || v_record_cadcalc.forcal  || ','
        || v_codvencadcalc            || ','
        || v_record_somatodos.integr  || ','
        || '''' || v_record_somatodos.tipopro || '''' || ','
        || '''' || v_record_somatodos.inicio  || '''' || ','
        || '''' || coalesce( v_record_somatodos.final, (iAnousu||'-12-31')::date)   || '''' || ','
        || v_record_somatodos.somatotal || ','
        || v_record_somatodos.valori  || ','
        || 0 || ','
        || '''' || v_record_somatodos.var     || '''' || ','
        || v_record_somatodos.gera    || ','
        || v_record_somatodos.seq
        || ');';

        end loop;

    end if;

    end if;

end loop;
-- fim do for do select na tabela tudo, que gera os registros na porcalculo

v_manual = v_manual || '\n---- t e r c e i r a  e t a p a  -----\n';
v_manual = v_manual || 'agrupando por tipo de vencimento e preparando para calcular\n';

perform fc_debug('--------------------------------------------------------------------------------------------------------',lRaise,false,false);
perform fc_debug('',lRaise,false,false);
perform fc_debug('TERCEIRA ETAPA DO CALCULO ',lRaise,false,false);
perform fc_debug('',lRaise,false,false);
perform fc_debug('agrupando por tipo de vencimento e preparando para calcular',lRaise,false,false);

for v_record_cadcalc in select porcalculo.*,
                                tipcalc.q81_excedenteativ
                            from porcalculo
                                inner join tipcalc on q81_codigo = porcalculo.tipcalc
loop

    if v_record_cadcalc.q81_excedenteativ > 0 then
    v_valor = v_record_cadcalc.valor;

    perform fc_debug('Seq : '||v_record_cadcalc.seq||' Valor : '||v_valor,lRaise,false,false);

    v_valor = v_valor + (v_valor * v_record_cadcalc.q81_excedenteativ * (v_quantativ - 1));
    update porcalculo set valor = v_valor where seq = v_record_cadcalc.seq ;

    perform fc_debug('Apos calcular 30% por atividade excedente - Seq : '||v_record_cadcalc.seq||' Valor : '||v_valor,lRaise,false,false);

    end if;
end loop;

perform fc_debug('Descobrindo de calcula fixo ou variavel ',lRaise,false,false);

--
-- Verifica se calcula fixo ou variavel
--
for v_record_cadvenc in select distinct cadcalc from porcalculo
loop

    if v_record_cadvenc.cadcalc = 2 then

    v_cadcalc_fix = true;

    elsif v_record_cadvenc.cadcalc = 3 then

    v_cadcalc_var = true;

    end if;

end loop;

if v_cadcalc_fix = true and v_cadcalc_var = true and iCalcfixvar = 1 then

    perform fc_debug('Inscricao com dois (2) calculos fixo/var, excluido calculo fixo',lRaise,false,false);
    delete from porcalculo where cadcalc = 2;
    v_manual = v_manual || '\n inscricao com dois (2) calculos fixo/variavel, calculado somente variavel \n';

end if;

if v_cadcalc_fix = true and v_cadcalc_var = true and iCalcfixvar = 2 then

    perform fc_debug('Inscricao com dois (2) calculos fixo/var, excluido calculo variavel',lRaise,false,false);

    delete from porcalculo where cadcalc = 3;
    v_manual = v_manual || '\n inscricao com dois (2) calculos fixo/variavel, calculado somente fixo \n';

end if;

perform fc_debug('select trazendo os vencimentos para gerar as proporcionalidades',lRaise,false,false);

for v_record_cadvenc in select codven from porcalculo group by codven
loop

    v_manual = v_manual || 'processando vencimento ' || v_record_cadvenc.codven || '\n';

    perform fc_debug('Codigo do cadastro de vencimentos : '||v_record_cadvenc.codven,lRaise,false,false);

    for v_record_cadcalc in select distinct tipcalc, cadcalc, valor, integr, inicio, final, tipopro, seq from porcalculo where codven = v_record_cadvenc.codven loop

    perform fc_debug('Tipo de calulo : '||v_record_cadcalc.tipcalc||' Cadcalc : '||v_record_cadcalc.cadcalc||'seq :'||v_record_cadcalc.seq,lRaise,false,false);

    select q81_abrev
    into v_descrtipcalc
        from tipcalc
        where q81_codigo = v_record_cadcalc.tipcalc;

    select q85_descr
        into v_descrcadcalc
        from cadcalc
        where q85_codigo = v_record_cadcalc.cadcalc;

    v_manual = v_manual || 'processando tipcalc: ' || v_record_cadcalc.tipcalc || ' - ' || v_descrtipcalc || ' - cadcalc: ' || v_record_cadcalc.cadcalc || ' - ' || v_descrcadcalc || '\n';

    v_valor = v_record_cadcalc.valor;
    v_manual = v_manual || 'valor: ' || v_valor || '\n';

    -- se √© para calcular com proporcionalidade
    if v_record_cadcalc.integr = '0' then

        perform fc_debug('Valor sem proporcionalidade : '||v_valor,lRaise,false,false);

        v_manual = v_manual || '   nao integral = proporcional ' || ' inicio: ' || v_record_cadcalc.inicio || ' - ano atual ' || iAnousu || '\n';

        -- soh calcula integralidade se ano do inicio da atividade for igual ao atual ou for calculo de baixa
        if extract(year from v_record_cadcalc.inicio)::integer = iAnousu or dDtbaixa is not null then
        --
        -- Calculo da proporcionalidade
        --
        if dDtbaixa is null then
            dDtProporcionalidade := v_record_cadcalc.final;
        else
            dDtProporcionalidade := dDtbaixa;
        end if;

        perform fc_debug('Tipo   - '||v_record_cadcalc.tipopro,lRaise,false,false);
        perform fc_debug('Inicio - '||v_record_cadcalc.inicio,lRaise,false,false);
        perform fc_debug('Final  - '||dDtProporcionalidade,lRaise,false,false);

        --
        -- Funcao fc_issqn_proporcionalidade
        --   retorna o valor proporcional ao periodo de atividade do exercio e a descricao do tipo de proporcionalidade
        --
        select rnValorProporcional,rsTipoProporcionalidade
            into v_valor,sDescrProporcionalidade
            from fc_issqn_proporcionalidade(v_record_cadcalc.valor::numeric,v_record_cadcalc.tipopro::varchar,v_record_cadcalc.inicio::date,dDtProporcionalidade::date,iAnousu::integer,dDtbaixa::date);

        v_manual = v_manual ||sDescrProporcionalidade||' \n';

        end if;

        perform fc_debug('fc_issqn_proporcionalidade(' || v_record_cadcalc.valor::numeric || ',' || v_record_cadcalc.tipopro::varchar || ',' || v_record_cadcalc.inicio::date || ',' || dDtProporcionalidade::date || ',' || iAnousu::integer || ',' || dDtbaixa::date || ')');

        perform fc_debug('Valor com a proporcionalide : '||v_valor,lRaise,false,false);

        v_manual = v_manual || 'valor ja calculado a proporcionalidade: ' || v_valor || '\n';

    else

        v_manual = v_manual || 'integral\n';

    end if;

    update porcalculo set valorintegr = v_valor  where seq = v_record_cadcalc.seq ;

    end loop;

end loop;

select count(*)
    into iAux
    from porcalculo;

v_manual = v_manual || '\n---- q u a r t a  e t a p a  -----\n';
v_manual = v_manual || 'total de calculos que o sistema vai processar: ' || iAux || '\n';

if iAux = 0 then
    return '24-NENHUM CALCULO EFETUADO!';
end if;

--
-- QUARTA FASE - GERANDO FINANCEIRO
--
perform fc_debug('--------------------------------------------------------------------------------------------------------',lRaise,false,false);
perform fc_debug('',lRaise,false,false);
perform fc_debug('QUARTA FASE DO CALCULO (GERANDO FINANCEIRO) ',lRaise,false,false);
perform fc_debug('Quantidade de registros tabela porcalculo : '||iAux,lRaise,false,false);
perform fc_debug('',lRaise,false,false);
perform fc_debug('--------------------------------------------------------------------------------------------------------',lRaise,false,false);

/* for nos calculo da inscricao que esta sendo calculada */

perform fc_debug('Percorrendo os calculo da inscricao que esta sendo calculada',lRaise,false,false);

dInicioAtividade := dDataCadastro;

for v_record_cadcalc in select * from porcalculo loop

    select q81_abrev
    from tipcalc
    into v_descrtipcalc
    where q81_codigo = v_record_cadcalc.tipcalc;

    select q85_descr
    from cadcalc
    into v_descrcadcalc
    where q85_codigo = v_record_cadcalc.cadcalc;

    v_manual = v_manual || '\nprocessando calculo ' || v_record_cadcalc.cadcalc || ' - ' || v_descrcadcalc ||  ' - tipo de calculo: ' || v_record_cadcalc.tipcalc || ' - ' || v_descrtipcalc || '\n';

    -- data de baixa preenchida e √© variavel
    -- resumindo, √© para nao fazer nada se for vari√°vel e calculo para baixa

    if dDtbaixa is not null and v_record_cadcalc.var = '1' then

       perform fc_debug('Com data de baixa passada como parametro: ' || dDtbaixa || ' e variavel: nao calcula',lRaise,false,false);
       v_manual = v_manual || 'com data de baixa passada como parametro: ' || dDtbaixa || ' e variavel: nao calcula\n';

        /* Quando eh baixa de MEI deve apagar arrecad posterior a competencia da baixa */
        if lInscricaoMei and v_numprejacalculado.q01_numpre is not null then
            perform fc_debug('Apagando arrecad de inscricao MEI com data de baixa passada como parametro: ' || dDtbaixa ,lRaise,false,false);
            delete from arrecad where k00_numpre = v_numprejacalculado.q01_numpre and k00_dtvenc > dDtbaixa and k00_numpar >= to_number(substr(dDtbaixa,6,2),'999');

        end if;

    -- senao
    else

    select q01_numpre
        from isscalc
        into v_numpre
        where q01_anousu = iAnousu
        and q01_inscr = iInscr
        and q01_cadcal = v_record_cadcalc.cadcalc;

    if v_numpre is null then

        perform fc_debug('Sem calculo para o exercicio ',lRaise,false,false);
        v_comcalculo = false;

    else

        perform fc_debug('Com calculo para o exercicio numpre : '||v_numpre,lRaise,false,false);
        v_comcalculo = true;

    end if;

    if v_comcalculo is false then
        v_manual = v_manual || 'calculo novo\n';

        select nextval('numpref_k03_numpre_seq')
        into v_numpre;

        perform fc_debug('Calculo Novo ',lRaise,false,false);
        perform fc_debug('Novo numpre : '||v_numpre,lRaise,false,false);

        if v_numpre is null then
        return '25-ERRO DO PROCESSAR SEQUENCIA DO NUMPRE';
        end if;

    else

        v_manual = v_manual || 'calculo ja existe... utilizar o mesmo numpre\n';
        select q01_numpre
        from isscalc
        into v_numpre
        where q01_anousu = iAnousu
            and q01_inscr = iInscr
            and q01_cadcal = v_record_cadcalc.cadcalc;

    end if;

    v_numpar = 1;

    select max(q82_parc)
        into v_numtot
        from cadvenc
            inner join cadvencdesc on q82_codigo = q92_codigo
        where cadvenc.q82_codigo = v_record_cadcalc.codven;

    if v_record_cadcalc.var = '1' then
        v_numtot = 12;
    else

        if v_numtot is null then
        v_numtot = 0;
        end if;

        if bGeral is false then
        v_numtot = 1;
        end if;
    end if;

    v_manual = v_manual || 'total de parcelas baseado no vencimento: ' || v_numtot || '\n';

    perform fc_debug('Codigo do Vencimento encontrado : '||v_record_cadcalc.codven,lRaise,false,false);
    perform fc_debug('Numpre : '||v_numpre||' Parcela : '||v_numpar||' Numtot : '||v_numtot||' Cadcalc : '||v_record_cadcalc.cadcalc,lRaise,false,false);

    if dDtbaixa is not null then 
       v_numpar = 1;
       v_numtot = 1;
    end if;

    select fc_digito(v_numpre,v_numpar,v_numtot)
        into v_numdig;

    if v_numdig is null then
        return '26-ERRO DO PROCESSAR FUNCAO DE CALCULO DO DIGITO VERIFICADOR';
    end if;

    select k15_codbco,
            k15_codage
        into v_codbco,
            v_codage
        from cadvencdescban
            inner join cadban on cadvencdescban.q93_cadban = cadban.k15_codigo
    where q93_codigo = v_record_cadcalc.codven;

    if v_codbco is null then
        v_codbco = 0;
    end if;

    if v_codage is null then
        v_codage = 0;
    end if;

    select q02_numcgm
        into v_numcgm
        from issbase
        where q02_inscr = iInscr;
    if v_numcgm is null then
        return '27-CGM DA INSCRICAO : '||iInscr||' NAO ENCONTRADO DO CADASTRO DO CGM';
    end if;

    perform fc_debug('Inicio : '||v_record_cadcalc.inicio||' Anousu : '||iAnousu,lRaise,false,false);

    if iAnousu = to_number(substr(v_record_cadcalc.inicio,1,4),'99999') then

        v_manual = v_manual || 'Ano atual igual ao ano de inicio da atividade: ' || iAnousu || '\n';

        perform fc_debug('Ano atual igual ao ano de inicio',lRaise,false,false);

        select recexe
        into v_receita
        from tudo
        where tudo.seq = v_record_cadcalc.seq;
    else

        v_manual = v_manual||'Ano atual diferente do ano de inicio da atividade: ' || iAnousu || '\n';
        perform fc_debug('Ano atual diferente ao ano de inicio',lRaise,false,false);
        select recpro
        into v_receita
        from tudo
        where tudo.seq = v_record_cadcalc.seq;

    end if;

    v_prim = false;

    /**
        * Se for variavel
        */
    if v_record_cadcalc.var = '1' then
        v_manual = v_manual || 'variavel\n';

        perform fc_debug('Calculando variavel -- vencimento : '||v_record_cadcalc.codven,lRaise,false,false);

        -- se for baixa
        perform fc_debug('',lRaise,false,false);
        perform fc_debug('COMECANDO A PROCESSAR OS VENCIMENTOS (PELO CADASTRO DE VENCIMENTOS CADVENC) ',lRaise,false,false);
        perform fc_debug('',lRaise,false,false);

        /* este for calcula pelo cadvenc todas as parcelas pelo select do porcalculo(todos os calculos da inscricao) */
        for v_record_cadvenc in select * from cadvenc
                                        inner join cadvencdesc on q82_codigo = q92_codigo
                                    where cadvenc.q82_codigo = v_record_cadcalc.codven
                                    order by q82_parc
        loop
            IF extract(year from dInicioAtividade)::integer = iAnousu THEN
                IF v_record_cadvenc.q82_parc < extract(month from dInicioAtividade)::integer THEN
                    perform fc_debug('No existia atividade no mÔøΩs '||v_record_cadvenc.q82_parc||' do ano de '||iAnousu,lRaise,false,false);
                    perform fc_debug('PULANDO ESTE MÔøΩS',lRaise,false,false);
                    perform fc_debug(' ',lRaise,false,false);

                    CONTINUE WHEN 1 = 1;
                END IF;
            END IF;
            
            perform fc_debug('Processando vencimentos ',lRaise,false,false);
            perform fc_debug('PARCELA : '||v_record_cadvenc.q82_parc||' VENCIMENTO : '||v_record_cadvenc.q82_venc,lRaise,false,false);
            perform fc_debug('1 -- Deletando do arrecad numpre : '||v_numpre||' numpar : '||v_record_cadvenc.q82_parc,lRaise,false,false);

            delete from arrecad
                where k00_numpre = v_numpre
                and k00_numpar = v_record_cadvenc.q82_parc;

            -- if to_number(substr(v_record_cadvenc.q82_venc,1,4)||substr(v_record_cadvenc.q82_venc,6,2),'999999') >
                -- to_number(substr(v_record_cadcalc.inicio,1,4)||substr(v_record_cadcalc.inicio,6,2),'999999') then

                v_manual = v_manual || 'vencimento do cadastro de vencimentos: ' || v_record_cadvenc.q82_venc || ' maior ou igual a data de inicio da atividade mais 1 mes\n';

                select count(*)
                into iAux
                from arreinscr
                where k00_numpre = v_numpre
                    and k00_inscr = iInscr;

                if iAux = 0 then

                v_manual = v_manual || 'inserindo numpre no arreinscr: ' || v_numpre || '\n';
                insert into arreinscr values (v_numpre,iInscr);
                end if;

                /* se nao for primeiro calculo do exercicio */
                if v_prim is false then

                if v_comcalculo is true then

                    perform fc_debug('Deletando do isscalc numpre : '||v_numpre,lRaise,false,false);
                    v_manual = v_manual || 'deletando numpre do isscalc e arrecad: ' || v_numpre || '\n';
                    delete from isscalc where q01_numpre = v_numpre;

                end if;

                insert into isscalc values (iAnousu,iInscr,v_record_cadcalc.cadcalc,v_receita,v_numpre,v_record_cadcalc.valori / v_valinflator);
                insert into numpres values (v_numpre);
                v_prim = true;

                end if;

                v_valor         := v_record_cadcalc.valori / v_valinflator;
                v_valorgrav     := 0;
                v_descrvariavel := 'arrecadacao de issqn variavel nao fixado';
                v_manual        := v_manual || 'valor: ' || v_valor || '\n';

                /* verifica se tem valor fixado lancado */
                select q34_valor
                into v_valorgrav
                from varfix
                        inner join varfixval on varfix.q33_codigo = varfixval.q34_codigo
                where varfix.q33_inscr    = iInscr
                    and varfixval.q34_numpar = v_record_cadvenc.q82_parc;

                if v_valorgrav is not null then

                v_descrvariavel = 'arrecadacao de issqn variavel fixado';
                bTemFixado = true;
                else

                v_valorgrav     = 0;
                v_descrvariavel = 'arrecadacao de issqn variavel nao fixado';
                end if;

                perform fc_debug('Tipo de valor : '||v_descrvariavel ,lRaise,false,false);

                v_numpar = v_record_cadvenc.q82_parc;
                v_manual = v_manual || 'parcela: ' || v_numpar || '\n';

                select q05_codigo
                into iAux
                from issvar
                        inner join arreinscr on q05_numpre = arreinscr.k00_numpre
                where q05_ano   = iAnousu
                    and q05_mes   = v_numpar
                    and k00_inscr = iInscr
                    and q05_valor > 0;

                v_vencvar = v_record_cadvenc.q82_venc;

                --Se For optante pelo simples, altera o dia do vencimento para dia 20
                if dDataInicialCadSimples < v_vencvar  and lOptanteSimples ='t' then
                v_vencvar = to_char(v_vencvar, 'YYYY-MM')||'-20';
                end if;

                perform fc_debug('Vencimento variavel : '||v_vencvar,lRaise,false,false);


                select q05_numpre
                from issvar
                into v_numprejapago
                        inner join arrecant on q05_numpre = arrecant.k00_numpre and q05_numpar = arrecant.k00_numpar
                        inner join arreinscr on arrecant.k00_numpre = arreinscr.k00_numpre
                where q05_ano   = iAnousu
                and q05_mes   = v_numpar
                and k00_inscr = iInscr;

                perform fc_debug('Numpre ja pago '||v_numprejapago||' Numpar : '||v_numpar||' Anousu : '||iAnousu,lRaise,false,false);

                select q05_numpre
                into iNumpreArquivoSimples
                from issvar
                        inner join issarqsimplesregissvar on q68_issvar = q05_codigo
                        inner join arreinscr on q05_numpre = arreinscr.k00_numpre
                where q05_ano   = iAnousu
                and q05_mes   = v_numpar
                and k00_inscr = iInscr;

                perform fc_debug('Numpre no arquivo do simples '||iNumpreArquivoSimples||' Numpar : '||v_numpar||' Anousu : '||iAnousu,lRaise,false,false);

                if v_numprejapago is null and iNumpreArquivoSimples is null  then

                perform fc_debug('2 -- deletando do arrecad numpre : '||v_numpre||' numpar : '||v_numpar,lRaise,false,false);

                delete from arrecad where k00_numpre = v_numpre and k00_numpar = v_numpar ;

                perform * from fc_statusdebitos(v_numpre,v_numpar) where rtstatus = 'PAGO' or rtstatus = 'CANCELADO'  limit 1;
                if found then
                    continue;
                end if;

                perform * from issvardiv
                            inner join issvar    on q05_codigo = q19_issvar
                            inner join arreinscr on k00_numpre = q05_numpre
                    where issvar.q05_ano       = iAnousu
                        and issvar.q05_mes       = v_numpar
                        and arreinscr.k00_inscr  = iInscr ;

                if found then
                    return '28 - INSCRICAO COM CALCULO IMPORTADO PARA DIVIDA';
                end if;

                perform fc_debug('deletando do issvar Ano : '||iAnousu||' Mes : '||v_numpar||' Inscricao : '||iInscr,lRaise,false,false);
                perform *
                    from arreinscr
                        inner join issvar    on issvar.q05_numpre    = arreinscr.k00_numpre
                        inner join issvarlev on issvarlev.q18_codigo = issvar.q05_codigo
                    where issvar.q05_ano       = iAnousu
                    and issvar.q05_mes       = v_numpar
                    and arreinscr.k00_inscr  = iInscr ;
                if found then
                    return '28 - EMPRESA JA POSSUI LEVANTAMENTO FISCAL PARA COMPETENCIA : '||v_numpar||'/'||iAnousu ;
                end if;

                delete from issvarnotas
                using issvar
                where issvar.q05_codigo = issvarnotas.q06_codigo
                    and issvar.q05_ano    = iAnousu
                    and issvar.q05_mes    = v_numpar ;

                delete from issvar
                    using arreinscr
                    where issvar.q05_ano       = iAnousu
                    and issvar.q05_mes       = v_numpar
                    and arreinscr.k00_inscr  = iInscr
                    and arreinscr.k00_numpre = issvar.q05_numpre ;

                delete from informacaodebito
                    where informacaodebito.k163_numpre = v_numpre
                    and informacaodebito.k163_numpar = v_numpar;

                insert into issvar (q05_codigo, q05_numpre, q05_numpar, q05_valor, q05_ano, q05_mes, q05_histor, q05_aliq, q05_bruto)
                            values (nextval('issvar_q05_codigo_seq'), v_numpre, v_numpar, v_valorgrav, iAnousu, v_numpar, v_descrvariavel, v_valor, 0);

                perform fc_debug('INSERT NUMERO 1 NO ARRECAD (issvar)',lRaise,false,false);
                perform fc_debug('Numpre : '||v_numpre||' Numpar : '||v_numpar||' Valor : '||round(v_valorgrav,2)||' Vencimento : '||v_vencvar||' Tipo : '||v_record_cadvenc.q92_tipo,lRaise,false,false);

                if dDtbaixa is not null then
                   v_numpar = 1;
                   v_numtot = 1;
                end if;

                insert into arrecad (k00_numcgm, k00_dtoper, k00_receit, k00_hist,                  k00_valor,            k00_dtvenc, k00_numpre, k00_numpar, k00_numtot, k00_numdig, k00_tipo)
                        values      (v_numcgm,   v_vencvar,  v_receita,  v_record_cadvenc.q82_hist, round(v_valorgrav,2),  v_vencvar, v_numpre,   v_numpar,   v_numtot,   v_numdig,v_record_cadvenc.q92_tipo);

                if v_codbco != 0 and v_comcalculo is false then

                    select fc_numbco(v_codbco,v_codage) into v_numbco;
                    /*
                        Comentado insert na arrebanco conforme solicitado pelo Paulo e Dalpozzo, pois nao faz
                        o menor sentido gerar arrebanco no calculo de issqn / alvara
                    */
                    --insert into arrebanco values (v_numpre,v_numpar,v_codbco,v_codage,v_numbco,'');

                end if;

                else
                perform fc_debug('Ja pago',lRaise,false,false);
                end if;

                v_numpar = v_numpar + 1;

            -- end if;
        end loop;

        perform fc_debug('FIM DO PROCESSAMENTO DO VARIAVEL',lRaise,false,false);

    else  -- SE NAO FOR VARIAVEL

        perform fc_debug('',lRaise,false,false);
        perform fc_debug('--------------------- P R O C E S S A N D O   (  N A O  )  V A R I A V E L  ---------------------',lRaise,false,false);
        perform fc_debug('vencimento: '||v_record_cadcalc.codven,lRaise,false,false);
        perform fc_debug('',lRaise,false,false);

        v_manual = v_manual || 'processando com base no cadastro de vencimentos\n';

        lJaPassouUltVenc   = false;
        v_jagravou         = false;
        iQtdeVencProcessar = 0;

        -- busca quantas parcelas vai calcular e registrar no arrecad (variavel iQtdeVencProcessar)
        select count(*)
        into v_totvenc
        from cadvencdesc
                inner join cadvenc on q82_codigo = q92_codigo
        where cadvencdesc.q92_codigo = v_record_cadcalc.codven ;

        select count(*)
        into iQtdeVencProcessar
        from cadvencdesc
                inner join cadvenc on q82_codigo = q92_codigo
        where cadvencdesc.q92_codigo = v_record_cadcalc.codven
            and ( case
                    when cadvencdesc.q92_formacalcparcvenc = 1
                    then case when q82_venc >= dInicioAtividade then true else false end
                    when cadvencdesc.q92_formacalcparcvenc = 3
                    then q82_venc >= dDatahj and q82_venc >= dInicioAtividade
                    else
                    case
                        when cadvenc.q82_calculaparcvenc is true
                        then true
                        else
                        q82_venc >= dDatahj and q82_venc >= dInicioAtividade
                    end
                end );
            -- esse case tem a finalidade de processar ou nao parcelas
            -- vencidas de acordo com os parametros do cadastro de
            -- vencimentos(cadvencdescm,cadvenc)
        perform fc_debug('Pesquisando quantidade de vencimentos no periodo de atividade',lRaise,false,false);
        perform fc_debug('Data inicial : '||coalesce(dInicioAtividade,'2999-01-01'::date)    ,lRaise,false,false);
        perform fc_debug('Data final   : '||iUltimoDiaAno ,lRaise,false,false);
        perform fc_debug('Quantidade de vencimentos encontrada : '||iQtdeVencProcessar||''    ,lRaise,false,false);

        -- utiliza a variavel iTotalVencimentosCad, que e o total de vencimentos para mais abaixo saber em que parcela jogar os centavos de diferenca de arredondamento
        -- utiliza a variavel dMaiorVencimentoCadvenc para no caso da empresa iniciar depois do ultimo vencimento, calcular tudo em uma parcela e jogar no vencimento 31-12
        select max(cadvenc.q82_venc), coalesce(max(cadvencdesc.q92_diasvcto),0), count(*)
        into dMaiorVencimentoCadvenc, iDiasParaVencimento, iTotalVencimentosCad
        from cadvencdesc
                inner join cadvenc on q82_codigo = q92_codigo
        where cadvencdesc.q92_codigo = v_record_cadcalc.codven;

        -- passando conteudo da variavel do cissqn para cadvencdesc
        iDiasvctoCissqn := iDiasParaVencimento;

        perform fc_debug('Quantidade de vencimentos a processar : '||iQtdeVencProcessar,  lRaise,false,false);
        perform fc_debug('Dias para o vencimento(q92_diasvcto)  : '||iDiasParaVencimento, lRaise,false,false);
        perform fc_debug('Maior vencimento do cadastro          : '||dMaiorVencimentoCadvenc||' Data atual : '||dDatahj,lRaise,false,false);

        select count(*), sum(k00_valor)
        into iQtdParcelasPagas, nValorTotalPago
        from ( select k00_numpre, k00_numpar, sum(k00_valor) as k00_valor
                    from arrecant
                where k00_numpre = v_numpre
                group by k00_numpre, k00_numpar ) as x;

        perform fc_debug('Quantidade de parcelas pagas : '||coalesce(iQtdParcelasPagas,0)||' Valor total pago : '||coalesce(nValorTotalPago,0),lRaise,false,false);

        begin

        select  case when (dDtBaixa is not null) and (v_cadcalc_fix is true) then
                    1
                when count(*) = 1 then
                    1
                else
                    ( count(*) - iQtdParcelasPagas )
                end as qtdparcelas,

                case when (dDtBaixa is not null) and (v_cadcalc_fix is true) then
                     100
                when count(*) = 1 then
                    100
                else
                    ( 100 / ( count(*) - iQtdParcelasPagas ) )
                end as percpago

            into iQtdParcelas, nPercPagoNovo
            from cadvencdesc
                left outer join cadvenc on q82_codigo = q92_codigo
            where cadvencdesc.q92_codigo = v_record_cadcalc.codven
            and ( case
                    when cadvencdesc.q92_formacalcparcvenc = 1
                    then case when q82_venc >= dInicioAtividade then true else false end
                    when cadvencdesc.q92_formacalcparcvenc = 3

                    then q82_venc >= dDatahj and q82_venc >= dInicioAtividade
                    else
                    case
                        when cadvenc.q82_calculaparcvenc is true
                        then true
                        else
                        q82_venc >= dDatahj and q82_venc >= dInicioAtividade
                    end
                end ) ;

        exception

            when division_by_zero then
            nPercPagoNovo := 0;

        end;

        if iQtdParcelasPagas = 0 then
        nPercPagoNovo := 0;
        end if;

        /*
        * Quando a quantidade total de parcelas for negativo, significa que  o calculo n√£o possui mais
        * parcelas em aberto, todas est√£o abertas. logo, o total de parcelas devera ser 1
        */
        if iQtdParcelas < 0 then
        iQtdParcelas = 1;
        end if;

        if nPercPagoNovo < 0 then
        nPercPagoNovo = 100;
        end if;

        nDescontoPagamentoParcela := coalesce( ( nValorTotalPago / iQtdParcelas ) ,0);

        perform fc_debug(' PROCURANDO PAGAMENTOS : ',lRaise,false,false);
        perform fc_debug('--------------------------------------------------------------------------------------------',lRaise,false,false);
        perform fc_debug(' TOTAL PAGO           : '||coalesce( nValorTotalPago, 0)           ,lRaise,false,false);
        perform fc_debug(' PARCELAS PAGAS       : '||coalesce( iQtdParcelasPagas, 0)         ,lRaise,false,false);
        perform fc_debug(' PARCELAS A CALCULAR  : '||coalesce( iQtdParcelas, 0)              ,lRaise,false,false);
        perform fc_debug(' DESCONTO POR PARCELA : '||coalesce( nDescontoPagamentoParcela, 0) ,lRaise,false,false);
        perform fc_debug(' PERCENTUAL NOVO      : '||coalesce( nPercPagoNovo, 0)             ,lRaise,false,false);
        perform fc_debug('--------------------------------------------------------------------------------------------',lRaise,false,false);

        -- sempre deve deletar o calculo anterior no caso de encontrar um
        perform fc_debug('DELETANDO DO ARRECAD NUMPRE : '||v_numpre,lRaise,false,false);
        delete from arrecad where k00_numpre = v_numpre;

        v_vencproc := 0;

        -- Loop que gera/processa financeiro
        for v_record_cadvenc in select *
                                from cadvencdesc
                                        left join cadvenc on q82_codigo = q92_codigo
                                where cadvencdesc.q92_codigo = v_record_cadcalc.codven
                            order by q82_parc
        loop

        perform fc_debug('------------------------------------------------------------------------------------------',lRaise,false,false);
        perform fc_debug('Processando Vencimento : '||v_record_cadvenc.q82_venc||' Parcela : '||v_record_cadvenc.q82_parc||' Inicio : '||v_record_cadcalc.inicio,lRaise,false,false);
        perform fc_debug('------------------------------------------------------------------------------------------',lRaise,false,false);

        /**
            * Guardar o vencimento atual do cadvenc
            */
        dVencimentoAtual := v_record_cadvenc.q82_venc;

        if dVencimentoAtual is null then
            dVencimentoAtual := dDatahj;
        end if;


        /**
            * Se a quantidade de vencimentos a processar for igual a zero
            */
        if iQtdeVencProcessar = 0 then

            -- soma dias para o vencimento na data do ultimo vencimento
            if iDiasvctoCissqn > 0 then

            dVencimentoAtual := ( dDatahj + iDiasvctoCissqn )::date;
            perform fc_debug('Trocou o vencimento para : '||dVencimentoAtual,lRaise,false,false);

            if
              extract(month from dDatahj) = 12 and
              extract(year from dDatahj) != extract(year from dVencimentoAtual)
            then
                -- 31 de Dezembro do ano atual
                perform fc_debug('Vencimento ' || dVencimentoAtual ||' ultrapassa limite atual',lRaise,false,false);
                dVencimentoAtual = extract(year from dDatahj) || '-12-31';
                perform fc_debug('Trocou o vencimento para : '||dVencimentoAtual,lRaise,false,false);
            end if;

            else

            -- se mesmo somando os dias para vencimento o debito continuar vencido joga para 31/12
            dVencimentoAtual := cast(to_char(iAnousu,'9999')||'-12-31' as date);

            perform fc_debug('Trocou o vencimento para ultimo dia do ano : '||dVencimentoAtual,lRaise,false,false);
            end if;

        end if;

        perform fc_debug('Vencimento apos processamento das regras : '||dVencimentoAtual,lRaise,false,false);

        /**
            * Variavel para controlar a forma para calculo de parcelas vencidas
            */
        lProcessaParcVencidas := ( case
                                        when v_record_cadvenc.q92_formacalcparcvenc = 1
                                        then true
                                        when v_record_cadvenc.q92_formacalcparcvenc = 3
                                        then v_record_cadvenc.q82_venc between dDatahj and iUltimoDiaAno
                                        else
                                        case
                                            when v_record_cadvenc.q82_calculaparcvenc is true
                                            then true
                                            else
                                            v_record_cadvenc.q82_venc between dDatahj and iUltimoDiaAno
                                        end
                                    end );

        perform fc_debug('Processando parcelas vencidas(lProcessaParcVencidas) ? '||(case when lProcessaParcVencidas is true then 'SIM' else 'NAO' end),lRaise,false,false);

        v_vencproc       = v_vencproc + 1;
        lProcessaParcela = false;

        perform fc_debug('Vencimento do cadvenc : '||dVencimentoAtual||' Maior Vencimento : '||dMaiorVencimentoCadvenc,lRaise,false,false);

        -- se vencimento do cadvenc do registro atual do for maior que o maximo vencimento do cadvenc
        if v_record_cadvenc.q82_venc > dMaiorVencimentoCadvenc then

            perform fc_debug('',lRaise,false,false);
            perform fc_debug('Vencimento do cadastro de vencimentos maior que o maximo vencimento, pasando lJaPassouUltVenc para true',lRaise,false,false);
            perform fc_debug('',lRaise,false,false);
            lJaPassouUltVenc = true;
        end if;

        perform fc_debug('Proximo vencimento : '||v_vencproc||'  Total de vencimentos : '||iTotalVencimentosCad||' Passa: '||( case when lProcessaParcela is true then 'true' else 'false' end ),lRaise,false,false);

        if (dDtbaixa is null) then 
           perform fc_debug('Data da baixa em branco',lRaise,false,false);
        end if;

        lProcessaParcela = true;

        if extract( year from v_record_cadcalc.inicio) <> iAnousu then

            perform fc_debug('Ano de inicio diferente do atual ',lRaise,false,false);
            lProcessaParcela = true;
            v_numtot         = v_totvenc;

        else

            perform fc_debug('Ano de inicio igual do atual ',lRaise,false,false);
            if v_record_cadvenc.q82_venc >= dInicioAtividade or dVencimentoAtual is null or iQtdeVencProcessar = 0 or lProcessaParcVencidas is true then

            perform fc_debug('Vencimento: '||dVencimentoAtual||' maior ou igual a inicio: '||dInicioAtividade||' ou vencimento is null ',lRaise,false,false);
            lProcessaParcela = true;

            else

            perform fc_debug('Passando lProcessaParcela para FALSE -- Vencimento: '||dVencimentoAtual||' menor que inicio: '||dInicioAtividade,lRaise,false,false);
            lProcessaParcela = false;

            end if;
            v_numtot = iQtdeVencProcessar;

        end if;

        if dDtbaixa is not null and dVencimentoAtual > dDtbaixa then

            perform fc_debug('Data de baixa : '||dDtbaixa,lRaise,false,false);
            -- perform fc_debug('1 - Passando lProcessaParcela para false',lRaise,false,false);
            -- lProcessaParcela = false;
        end if;

        if dDatahj > dVencimentoAtual and lProcessaParcVencidas is false then

            perform fc_debug('Inicio maior que data de vencimento e processar parcelas vencidas NAO',lRaise,false,false);
            lProcessaParcela = false;
        end if;

        perform fc_debug('----------------------------------------------------------------------------------------------',lRaise,false,false);
        perform fc_debug('Total de vencimentos     : '||iTotalVencimentosCad ,lRaise,false,false);
        perform fc_debug('Vecimentos do cadastro   : '||v_vencproc,lRaise,false,false);
        perform fc_debug('Qtd parcelas a calcular  : '||v_totvenc,lRaise,false,false);
        perform fc_debug('Ja passou ult vencimento : '||(case when lJaPassouUltVenc is true then 'SIM' else 'NAO' end),lRaise,false,false);
        perform fc_debug('Inicio                   : '||v_record_cadcalc.inicio,lRaise,false,false);
        perform fc_debug('Vencimento do Cadastro   : '||dVencimentoAtual,lRaise,false,false);
        perform fc_debug('----------------------------------------------------------------------------------------------',lRaise,false,false);

        if    v_record_cadcalc.inicio > dVencimentoAtual
            and iTotalVencimentosCad <> v_vencproc
            and lJaPassouUltVenc is false                 then

            perform fc_debug('Passando lProcessaParcela para false',lRaise,false,false);
            lProcessaParcela = false;
        end if;

        perform fc_debug('Passando para o proximo vencimento(lProcessaParcela) ? '||(case when lProcessaParcela is true then 'True' else 'False' end),lRaise,false,false);

        if lProcessaParcela is true then

            -- DESCOBRIR EM QUE CASO E UTILIZADO
            --
            -- Desabilitado o if abaixo pois √© justamente ele que impede o calculo para anos posteriores,
            v_venc = dVencimentoAtual;

            perform fc_debug('Quantidade de vencimentos(iQtdeVencProcessar) a processar : '||iQtdeVencProcessar,lRaise,false,false);
            if iQtdeVencProcessar = 0 then
            nPercentualParcela = 100;
            else

            -- Verificacao do valor total proporcional
            if v_record_cadcalc.tipopro = 'D' then

                v_venc     = v_record_cadvenc.q82_venc;

                v_venccalc = v_venc;
                if lJaPassouUltVenc is true or v_record_cadvenc.q82_venc = dMaiorVencimentoCadvenc then

                v_venccalc = to_char(iAnousu,'9999') || '-12-31';
                dMaiorVencimentoCadvenc  = v_venccalc;

                end if;
                v_anoiniciocalc := extract(year from v_record_cadcalc.inicio);
                if v_anoiniciocalc < iAnousu then
                dInicioAtividadecalc = to_char(iAnousu,'9999') || '-01-01';
                else
                dInicioAtividadecalc = v_record_cadcalc.inicio;
                end if;

                perform fc_debug('Total de vencimentos : '||dMaiorVencimentoCadvenc||' Inicio: '||dInicioAtividadecalc,lRaise,false,false);
                v_diasdesdeinicio = (iUltimoDiaAno - dInicioAtividadecalc)::integer + 1;
                perform fc_debug('Vencimento calculo : '||v_venccalc||' Inicio: '||dInicioAtividadecalc,lRaise,false,false);
                v_diasdestevcto = ((v_venccalc - dInicioAtividadecalc)::integer + 1) - v_diasjasomados;

                if bGeral is true or dDtbaixa is not null then
                nPercentualParcela = 100::float8 / iQtdeVencProcessar::float8;
                   if (dDtbaixa is not null) then
                      nPercentualParcela = 100::float8;
                   end if;
                else
                nPercentualParcela = (100::float8 / v_diasdesdeinicio::float8)::float8 * v_diasdestevcto::float8;
                v_diasjasomados = v_diasjasomados + v_diasdestevcto;
                end if;

            else

                nPercentualParcela = 100::float8 / iQtdParcelas::float8;

            end if;
            end if;

            perform fc_debug('Vencimento : '||v_venc||' Dias de vencimento : '||iDiasvctoCissqn,lRaise,false,false);

            if v_venc is null and iAnousu < v_anoatualservidor then

            v_venc = to_char(iAnousu,'9999')||'-12-31';

            --- verificado o vcto do alvara quando calculado, data calc. + 30 dias
            if iDiasvctoCissqn > 0 and iAnousu = v_anoatualservidor then

                select dDatahj + iDiasvctoCissqn
                into v_venc;


                v_manual = v_manual || 'alterado vencimento para: '||v_venc||'\n';

            end if;
            end if;

            if ((dDtBaixa is not null) and (v_cadcalc_fix is true)) then
    
               select dDatahj + iDiasvctoCissqn
               into v_venc;

               perform fc_debug('Alterado vencimento por ser baixa de ISSQN Fixo para (dDatahj + iDiasvctoCissqn): '||v_venc,lRaise,false,false);

            end if;

            perform fc_debug('Percentual : '||nPercentualParcela,lRaise,false,false);
            perform fc_debug('Vencimento : '||v_venc,lRaise,false,false);

            if v_prim is false then
            if v_comcalculo is false then
                insert into arreinscr values (v_numpre,iInscr);
            else
                perform fc_debug('deletando do isscalc o numpre : '||v_numpre,lRaise,false,false);
                v_manual = v_manual || 'deletando numpre do isscalc e arrecad: ' || v_numpre || '\n';
                delete from isscalc where q01_numpre = v_numpre;
            end if;

            insert into isscalc values (iAnousu, iInscr, v_record_cadcalc.cadcalc, v_receita, v_numpre, v_record_cadcalc.valor);
            insert into numpres values ( v_numpre );
            v_prim = true;

            end if;

            /**
            * Valida quando executa calculo geral
            */
            if bGeral is true then

            /**
                * Subtraido valor que ja foi pago, caso contrario subtraira 0
                */
            nValorParcela = round( ( (v_record_cadcalc.valorintegr) * nPercentualParcela / 100) - coalesce(nDescontoPagamentoParcela, 0), 2);

            perform fc_debug('(1) Valor Parcela : '||nValorParcela,lRaise,false,false);
            perform fc_debug('Valor Total       : '||v_record_cadcalc.valorintegr||' Valor Parcial(parcela) : '||nValorParcela,lRaise,false,false);

            /**
                * Ultima Parcela
                */
            if v_numpar = v_numtot then

                /**
                * Arredonda os centavos e joga na ultima
                */
                perform fc_debug('',lRaise,false,false);
                perform fc_debug('(4) Valor Parcela : '||nValorParcela,lRaise,false,false);
                nValorParcela = nValorParcela + ((v_record_cadcalc.valorintegr) - (nValorParcela * v_numtot));
                perform fc_debug('(5) Valor Parcela : '||nValorParcela,lRaise,false,false);
            end if;

            perform fc_debug('100 -- Delete from arrecad numpre : '||v_numpre||' numpar : '||v_record_cadvenc.q82_parc,lRaise,false,false);

            -- se a parcela esta paga ou cancelada passa para a proxima
            perform * from fc_statusdebitos(v_numpre, v_record_cadvenc.q82_parc)
                where rtstatus = 'PAGO'
                    or rtstatus = 'CANCELADO' limit 1;
            if found then

                perform fc_debug('1 -- PARCELA '||v_record_cadvenc.q82_parc||' ESTA PAGA OU CANCELADA ',lRaise,false,false);
                continue;
            end if;

            nValorParcela := round(nValorParcela,2);
            perform fc_debug('INSERT NUMERO 2 NO ARRECAD',lRaise,false,false);
            perform fc_debug('Numpre : '||v_numpre||' Numpar : '||v_numpar||' Valor : '||round(nValorParcela,2)||' Vencimento : '||v_venc||' Tipo : '||v_record_cadvenc.q92_tipo,lRaise,false,false);

            if nValorParcela > 0 then

                /**
                * Alterado para inserir o q82_parc ao inves do numpar pois a pl nao levava em consideracao parcelas
                * pagas e ou canceladas e assim gerando inconsistencia com numpar pago/cancelado e em aberto ao mesmo tempo
                */
                insert into arrecad (k00_numcgm, k00_dtoper, k00_receit, k00_hist,                  k00_valor,              k00_dtvenc, k00_numpre, k00_numpar,                k00_numtot, k00_numdig, k00_tipo)
                             values (v_numcgm,   v_dtbase,   v_receita,  v_record_cadvenc.q92_hist, round(nValorParcela,2), v_venc,     v_numpre,   v_record_cadvenc.q82_parc, v_numtot,   v_numdig,    v_record_cadvenc.q92_tipo);

            end if;

            else

            -- se a parcela esta paga ou cancelada passa para a proxima
            perform * from fc_statusdebitos(v_numpre, v_record_cadvenc.q82_parc)
                where rtstatus = 'PAGO'
                    or rtstatus = 'CANCELADO' limit 1;

            if found then

                perform fc_debug('2 -- PARCELA '||v_record_cadvenc.q82_parc||' ESTA PAGA OU CANCELADA ',lRaise,false,false);
                v_numpar = v_numpar + 1;
                continue;

                /**
                * Replicada mesma logica descrita acima quando o calculo eh geral
                */
            else
                v_numpar = v_record_cadvenc.q82_parc;
            end if;

            if v_numpar is null then
                v_numpar := 1;
            end if;

            v_dtvencano   = v_venc;
            nValorParcela = round( ( ( v_record_cadcalc.valorintegr * nPercentualParcela ) / 100 ) - nDescontoPagamentoParcela,2 );

            v_manual = v_manual || 'valor da parcela: ' || nValorParcela || '\n';

            perform fc_debug('Valor integral        : '||v_record_cadcalc.valorintegr||' Percentual : '||nPercentualParcela,lRaise,false,false);
            perform fc_debug('Valor da parcela      : '||nValorParcela,lRaise,false,false);
            perform fc_debug('Percentual da parcela : '||nPercentualParcela,lRaise,false,false);
            perform fc_debug('Desconto da parcela   : '||nDescontoPagamentoParcela,lRaise,false,false);

            perform fc_debug('300 -- Delete from arrecad numpre : '||v_numpre||' numpar : '||v_numpar,lRaise,false,false);

            if nValorParcela > 0 then

                perform fc_debug('INSERT NUMERO 3 NO ARRECAD',lRaise,false,false);
                perform fc_debug('Numpre : '||v_numpre||' Numpar : '||v_numpar||' Valor : '||round(nValorParcela,2)||' - Vencimento : '||v_dtvencano||' Tipo : '||v_record_cadvenc.q92_tipo,lRaise,false,false);

                if v_record_cadcalc.cadcalc = 1 then
                    -- Alvara
                    iNumeroParcelas = iNumeroParcelasAlvara;
                elsif v_record_cadcalc.cadcalc = 2 then
                    -- ISSQN
                    if dDtbaixa is not null then 
                       iNumeroParcelas = 1;
                    else 
                       iNumeroParcelas = iNumeroParcelasIssqn;
                    end if;
                end if;

                /**
                * Parcela o c√°lculo, seguindo o principio:
                * -- Dividir o Valor Pela Quantidade
                * -- Criar parcelas sempre com base de vencimento
                * -- Gravar nova estrutura de d√©bitos no arrecad
                **/
                if iNumeroParcelas > 1 or (v_record_cadcalc.cadcalc = 2 and iNumeroParcelas > 0) then

                    perform fc_debug('+----------------------------------------------',lRaise,false,false);
                    perform fc_debug('| Divisao da Parcela em - '|| iNumeroParcelas ||' - partes ',lRaise,false,false);
                    perform fc_debug('+--------------------------------------------',lRaise,false,false);

                    -- Se ao calcular o issqn a data de vencimento da √∫ltima parcela
                    -- extrapolar o ano atual, n√£o √© poss√≠vel parcelar em tantas vezes
                    if
                        v_record_cadcalc.cadcalc = 2 and
                        iNumeroParcelas > 1 and
                        extract(year from dDatahj + iDiasvctoCissqn + (interval '1' month * (iNumeroParcelas - 1))) != extract(year from dDatahj)
                    then
                        return '29 - Uma ou mais parcelas venceriam em um ano posterior ao atual. Diminua a quantidade de parcelas.';
                    end if;

                    for rParcelas
                    in select numero_parcela,
                            valor_parcela,
                            data_vencimento
                        from fc_issqn_divide_valores(round(nValorParcela,2), iNumeroParcelas,dInicioAtividade )
                    loop
                        -- ---------------------------- --
                        --  Inserindo Dados no Arrecad  --
                        -- ---------------------------- --
                        perform fc_debug('| Parcela           : '|| rParcelas.numero_parcela ,lRaise,false,false);
                        perform fc_debug('| ValorParcela      : '|| rParcelas.valor_parcela  ,lRaise,false,false);
                        perform fc_debug('| VencimentoParcela : '|| rParcelas.data_vencimento,lRaise,false,false);

                        if v_record_cadcalc.cadcalc = 2 and iNumeroParcelas > 0 and rParcelas.data_vencimento < dDatahj then
                            rParcelas.data_vencimento = dDatahj + iDiasvctoCissqn + (interval '1' month * (rParcelas.numero_parcela - 1));
                        end if;

                        -- Se estivermos calculando o ISSQN e s√≥ estivermos parcelando uma vez
                        -- e estamos em dezembro e a data de vencimento ficar pro ano que vem,
                        -- ajustamos ela pro √∫ltimo dia desse ano (31/12)
                        if
                            v_record_cadcalc.cadcalc = 2 and
                            iNumeroParcelas = 1 and
                            extract(month from dDatahj) = 12 and
                            extract(year from dDatahj) != extract(year from rParcelas.data_vencimento)
                        then
                            -- 31 de Dezembro do ano atual
                            rParcelas.data_vencimento = extract(year from dDatahj) || '-12-31';
                        end if;

                        if dDtbaixa is not null then 
                           perform fc_debug('GERANDO NOVO NUMPRE PARA BAIXA',lRaise,false,false);
                           select nextval('numpref_k03_numpre_seq')
                           into v_numpre;
                           insert into arreinscr values (v_numpre,iInscr);
                        end if;

                        perform fc_debug('INSERT NUMERO 3 (A) NO ARRECAD',lRaise,false,false);
                        perform fc_debug('Numpre : '||v_numpre||' Numpar : '||v_numpar||' Valor : '||round(nValorParcela,2)||' - Vencimento : '||v_dtvencano||' Tipo : '||v_record_cadvenc.q92_tipo,lRaise,false,false);

                        insert into arrecad
                               (k00_numcgm,
                                k00_dtoper,
                                k00_receit,
                                k00_hist,
                                k00_valor,
                                k00_dtvenc,
                                k00_numpre,
                                k00_numpar,
                                k00_numtot,
                                k00_numdig,
                                k00_tipo)
                        values (v_numcgm,
                                v_dtbase,
                                v_receita,
                                v_record_cadvenc.
                                q92_hist,
                                rParcelas.valor_parcela,   -- VALOR ANTERIOR -> round(nValorParcela,2)
                                rParcelas.data_vencimento, -- VALOR ANTERIOR -> v_dtvencano,
                                v_numpre,
                                rParcelas.numero_parcela,  -- VALOR ANTERIOR -> v_numpar
                                iNumeroParcelas,           -- VALOR ANTERIOR -> v_numtot
                                v_numdig,
                                v_record_cadvenc.q92_tipo);

                        perform fc_debug('+--------------------------------------------',lRaise,false,false);
                    end loop;

                    -- Juntar as parcelas que sejam do mesmo m√™s em uma s√≥
                    for arrecadAuxiliar in
                        select
                            k00_numpre,
                            k00_numpar,
                            k00_numcgm,
                            k00_dtoper,
                            k00_receit,
                            k00_hist,
                            sum(k00_valor) as k00_valor,
                            min(k00_dtvenc) as k00_dtvenc,
                            k00_numtot,
                            k00_numdig,
                            k00_tipo,
                            k00_tipojm
                        from
                            arrecad
                        where
                            k00_numpre = v_numpre
                          and k00_dtvenc > dDatahj 
                        group by
                            k00_numpre,
                            k00_numpar,
                            k00_numcgm,
                            k00_dtoper,
                            k00_receit,
                            k00_hist,
--                            k00_dtvenc,
                            k00_numtot,
                            k00_numdig,
                            k00_tipo,
                            k00_tipojm
                    loop
                        delete from arrecad where arrecad.k00_numpre = arrecadAuxiliar.k00_numpre and arrecad.k00_numpar = arrecadAuxiliar.k00_numpar;
                        perform fc_debug('INSERT NUMERO 3 (B) NO ARRECAD',lRaise,false,false);
                        perform fc_debug('Numpre : '||arrecadAuxiliar.k00_numpre||' Numpar : '||arrecadAuxiliar.k00_numpar||' Valor : '||arrecadAuxiliar.k00_valor||' - Vencimento : '||arrecadAuxiliar.k00_dtvenc||' Tipo : '||arrecadAuxiliar.k00_tipo,lRaise,false,false);

                        insert into arrecad values (
                            arrecadAuxiliar.k00_numpre,
                            arrecadAuxiliar.k00_numpar,
                            arrecadAuxiliar.k00_numcgm,
                            arrecadAuxiliar.k00_dtoper,
                            arrecadAuxiliar.k00_receit,
                            arrecadAuxiliar.k00_hist,
                            arrecadAuxiliar.k00_valor,
                            arrecadAuxiliar.k00_dtvenc,
                            arrecadAuxiliar.k00_numtot,
                            arrecadAuxiliar.k00_numdig,
                            arrecadAuxiliar.k00_tipo,
                            arrecadAuxiliar.k00_tipojm
                        );
                    end loop;

                else -- Fim da Validacao de parcelas e Tipo de Calculo, seguindo como era antes

                    iNumTot = v_numtot;
                    if v_numtot = 0 then
                        iNumTot = 1;
                    end if;

                    perform fc_debug('INSERT NUMERO 3 (C) NO ARRECAD',lRaise,false,false);
                    perform fc_debug('Numpre : '||v_numpre||' Numpar : '||v_numpar||' Valor : '||round(nValorParcela,2)||' - Vencimento : '||v_dtvencano||' Tipo : '||v_record_cadvenc.q92_tipo,lRaise,false,false);

                    insert into arrecad (k00_numcgm, k00_dtoper, k00_receit, k00_hist,                  k00_valor,              k00_dtvenc,  k00_numpre, k00_numpar, k00_numtot, k00_numdig, k00_tipo)
                                 values (v_numcgm,   dDatahj,   v_receita,  v_record_cadvenc.q92_hist, round(nValorParcela,2), v_dtvencano, v_numpre,   v_numpar,   iNumTot,    v_numdig,   v_record_cadvenc.q92_tipo);
                end if;
            end if;

            end if; -- final do bloco para bgeral is false
            perform fc_debug('Valor parcela : '||nValorParcela,lRaise,false,false);

            if v_comcalculo is false then
                select fc_numbco(v_codbco,v_codage) into v_numbco;
                /*
                    Comentado insert na arrebanco conforme solicitado pelo Paulo e Dalpozzo, pois nao faz
                    o menor sentido gerar arrebanco no calculo de issqn / alvara
                */
                --insert into arrebanco values (v_numpre,v_numpar,v_codbco,v_codage,v_numbco);
            end if;

            v_numpar = v_numpar + 1;
            v_jagravou = true;

            if nPercentualParcela = 100 then

            perform fc_debug('Saindo do for percentual = 100',lRaise,false,false);
            exit;
            end if;

        -- se lProcessaParcela is false
        else

            v_manual = v_manual || 'nao utilizando vencimento: ' || v_record_cadvenc.q82_venc || '\n';
            perform fc_debug('Nao processando financeiro lProcessaParcela is false',lRaise,false,false);

        end if;

        end loop;
    end if;

    end if;

end loop;


for v_record_cadcalc in select * from numpres loop

    select q01_manual
    into sManualText
    from isscalc
    where q01_inscr  = iInscr
        and q01_anousu = iAnousu
        and q01_manual is not null
    limit 1;

    v_manual := v_manual || 'atualizando log do calculo do numpre: ' || v_record_cadcalc.numpre || '\n';
    update isscalc
        set q01_manual = coalesce(sManualText,' ') || v_manual
    where q01_inscr = iInscr and q01_anousu = iAnousu;

end loop;

return '01-OK';

end;

$$ language 'plpgsql';   

SQL1
    );
    }

    public function downFcIssqnAlterada()
    {
    DB::statement(<<<SQL1

create or replace function fc_issqn(integer,date,integer,date,boolean,boolean,integer,varchar,integer,integer, integer default 0)
returns varchar(200)
as $$
declare

iInscr                    alias   for $1; -- inscricao que esta sendo calculada
dDatahj                   alias   for $2; -- data do sistema
iAnousu                   alias   for $3; -- ano do sistema
dDtbaixa                  alias   for $4; -- data da baixa se estiver calculando baixa
bRecalc                   alias   for $5; -- se e um recalculo
bGeral                    alias   for $6; -- se e um calculo geral
iInstit                   alias   for $7; -- instituicao
sAtivs                    alias   for $8; -- relacao das atividades separadas por virgula. Exemplo: 1,2,3,4,5
iTipoCalculo              alias   for $9; -- Tipo de calculo: 0 - Todos,
                            --                                1 - ISSQN,
                            --                                2 - Alvara
iNumeroParcelasAlvara     alias   for $10;-- Quantidade de Parcelas em que o Alvare deve ser dividido
iNumeroParcelasIssqn      alias   for $11;-- Quantidade de Parcelas em que o ISSQN deve ser dividido

iCalcfixvar               integer default 0;
iDiasvctoCissqn           integer default 0;
iConsiderarMesInicio      integer default 0;
iMesFinal                 integer default 0;
iDiaInicio                integer default 0;

v_vencproc                integer default 0;
v_quantexcluido           integer default 0;
v_diasjasomados           integer default 0;
v_anoatualservidor        integer;
v_tipo_quant              integer;
iTotalVencimentosCad      integer;
v_anoiniciocalc           integer;
v_mesinicio               integer;
v_numprejapago            integer;
v_uqtab                   integer;
v_uqcad                   float8;
v_ativprinc               integer;
v_codvencadcalc           integer;
v_codven                  integer;
v_sequencia               integer;
v_tabativ                 integer;
v_ativtipo                integer;
v_tipcalc                 integer;
iInscrexiste              integer;
v_quantativ               integer;
v_numpre                  integer;
v_numpar                  integer;
v_numtot                  integer;
v_numdig                  integer;
v_numcgm                  integer;
v_receita                 integer;
v_codbco                  integer;
iAux                      integer;
v_diasdesdeinicio         integer;
v_diasdestevcto           integer;
iTotalDiasAno             integer;
v_q81_rec                 integer;
iQtdeVencProcessar        integer;
v_totvenc                 integer default 0;
iQtdParcelas              integer;
iQtdParcelasPagas         integer;
v_forcal                  integer;
iDiasParaVencimento       integer default 0;
iAnoCadastroEmpresa       integer default 0;
iNumTot                   integer default 1;

v_qprovisorio             float8 default 1;
nValorParcela             float8;
v_valor                   float8;
v_valorgrav               float8 default 0;
v_quant                   float8;
v_base                    float8;
v_valinflator             float8;
v_q81_qini                float8;
v_q81_qfim                float8;
v_q81_val                 float8;
nPercentualParcela        float8;

nValorTotalPago           float8 default 0; -- total pago usado no caso de um recalculo
nDescontoPagamentoParcela float8 default 0; -- desconto por parcela, e o valor a ser descontado por parcela no caso de um recalculo com pagamentos
nPercPagoNovo             float8 default 0; -- percentual pago referente ao total do calculo, usado para calcular o desconto por parcela no caso de um recalculo com pagamento

dDataCadastro             date;
v_venc                    date;
v_vencvar                 date;
v_venccalc                date;
iPrimeiroDiaAno           date;
iUltimoDiaAno             date;
v_dtvencano               date;
dMaiorVencimentoCadvenc   date;
dInicioAtividade          date;
dInicioAtividadecalc      date;
v_dtbase                  date;
v_databaixa               date;
dDtProporcionalidade      date;
dVencimentoAtual          date;
dDataInicialCadSimples    date;

q81Tipos                  varchar;

v_cep                     char(8);
v_cepinstit               char(8);
v_integr                  char(1);
v_var                     char(1);
v_codage                  char(5);
v_numbco                  char(15);

v_descrvariavel           varchar(50);
v_descrtipcalc            varchar(40);
v_descrcadcalc            varchar(40);
sDescrProporcionalidade   varchar;

v_text                    text;
v_textexclui              text;
v_textexclui2             text;
v_manual                  text default '\n';
tManual                   text;
sSqlInsert                text;
sManualCorrecao           text;
sManualText               text;

v_provisorio              boolean default false;
v_cadcalc_var             boolean default false;
v_cadcalc_fix             boolean default false;
v_comcalculo              boolean default false;
v_continuar               boolean default true;
v_prim                    boolean;
v_jagravou                boolean;
lJaPassouUltVenc          boolean;
bTemFixado                boolean default false; -- se tem valor fixado(varfixval)
lProcessaParcVencidas     boolean default false;
lProcessaParcela          boolean;
lTabelasCriadas           boolean;
lInscricaoMei             boolean default false;
lOptanteSimples           boolean default false;

v_record_ativ             record;
v_record_tipcalc          record;
v_record_cadvenc          record;
v_record_cadcalc          record;
v_record_ativprinc        record;
v_record_maiorvalor       record;
v_record_somatodos        record;
v_record_variavel         record;
v_numprejacalculado       record;
v_record_excluir          record;
rDebitos                  record;
rParcelas           record;

dtOperacao                varchar;
iNumpreArquivoSimples     integer;

lRaise                    boolean default false; -- variavel para debug
lAbatimento               boolean default false;

lCalculaVistoriasMEI      boolean default false;

iNumeroParcelas           integer; -- Vari√°vel auxiliar para o n√∫mero de parcelas

arrecadAuxiliar           record; -- Vari√°vel para juntar parcelas iguais

begin

select to_char(fc_getsession('DB_datausu')::date, 'DD/MM/YYYY') into dtOperacao;

    if  dtOperacao is null then
        RAISE EXCEPTION 'Erro: variavel de sessao DB_datausu nao declarado!';
    end if;

if     iTipoCalculo = 1 then
    v_manual :=  '  ======================= Calculo de ISSQN          | Data de Calculo: ' || dtOperacao ||'  =========== \n';
elsif  iTipoCalculo = 2 then
    v_manual :=  '  ======================= Calculo de Alvara         | Data de Calculo: ' || dtOperacao ||'  =========== \n';
else
    v_manual :=  '  ======================= Calculo de ISSQN e Alvara | Data de Calculo: ' || dtOperacao ||'  =========== \n';
end if;

lRaise  := ( case when fc_getsession('DB_debugon') is null then false else true end );

perform fc_debug('INICIANDO CALCULO PARA INSCRICAO : '||iInscr||' EXERCICIO : '||iAnousu,lRaise,true,false);

perform fc_debug('DATA DE BAIXA - '||dDtbaixa,lRaise,false,false);

perform *
   from tabativ
  inner join clasativ
     on q82_ativ      = q07_ativ
  inner join issbaseporte
     on q45_inscr     = q07_inscr
  where q07_inscr = iInscr
    and (exists(select 1
                  from ativtipo
                 where q80_ativ = q07_ativ)
        or exists(select 1
                    from issportetipo
                   where q41_codclasse = q82_classe
                     and q41_codporte  = q45_codporte));

if not found then
    return '24-Empresa sem tipo de calculo configurado!';
end if;

--
-- Realizamos a confer√™ncia dos dados para sabermos se a inscri√ß√£o √© optante pelo SIMPLES
--
-- Caso seja optante n√£o ser√° calculado a taxa de alvar√° da empresa.
--
-- Deve possui cadastro na tabela meicgm...
-- OU
-- A empresa deve ser optante com:
--  - A categoria 3 - MEI;
--  - Data de in√≠cio do cadastro no simples menor ou igual a data de calculo;
--  - N√£o pode estar com o cadastro do simples baixado (isscadsimplesbaixa)
--

-- perform *
--     from meicgm
--         inner join issbase on issbase.q02_numcgm = meicgm.q115_numcgm
--     where issbase.q02_inscr = iInscr;

-- if found then
--     lInscricaoMei = true;
-- end if;

perform *
    from isscadsimples
    where isscadsimples.q38_inscr     = iInscr
    and isscadsimples.q38_categoria = 3
    and isscadsimples.q38_dtinicial <= dDatahj
    and not exists ( select 1
                        from isscadsimplesbaixa
                        where isscadsimplesbaixa.q39_isscadsimples = isscadsimples.q38_sequencial
                        and q39_dtbaixa <= dDatahj );
if found then
    lInscricaoMei = true;
end if;
--
-- FIM DA CONFER√äNCIA DE CALCULO DO MEI
--

-- Realizamos a confer√™ncia dos dados para sabermos se a inscri√ß√£o √© optante pelo SIMPLES
-- Caso seja optante, a data de vencimento ser√° sempre o dia 20 do m√™s.
select q38_dtinicial
    into dDataInicialCadSimples
    from isscadsimples
    where isscadsimples.q38_inscr     = iInscr
    and isscadsimples.q38_dtinicial <= dDatahj
    and not exists ( select 1
                        from isscadsimplesbaixa
                        where isscadsimplesbaixa.q39_isscadsimples = isscadsimples.q38_sequencial
                        and q39_dtbaixa <= dDatahj );
if found then
    lOptanteSimples = true;
end if;
-- FIM DA CONFER√äNCIA DE EMPRESAS OPTANTE PELO SIMPLES

--
-- Verificando par√¢metro Calcula Vistorias para MEI:
--
select y32_calculavistoriamei::boolean
    into lCalculaVistoriasMEI
    from parfiscal;

select extract(year from q02_dtinic)
    into iAnoCadastroEmpresa
    from issbase
    where q02_inscr = iInscr;

if iAnousu < iAnoCadastroEmpresa then
    return '24-N√£o pode ser feito calculo para exercicio menor que o ano de cadastramento da empresa.';
end if;

select fc_issqn_criatemptable(lRaise)
    into lTabelasCriadas;
if lTabelasCriadas is false then
    return '24-Problema ao criar as tabelas temporarias. ';
end if;

for rDebitos in
    select q01_numpre
    from isscalc
    where q01_inscr  = iInscr
        and q01_anousu = iAnousu
loop

    -- Verifica se existe Pagamento Parcial para o d√©bito informado
    select fc_verifica_abatimento(1,rDebitos.q01_numpre)::boolean into lAbatimento;

    if lAbatimento then
    return '24-Opera√ß√£o Cancelada, D√©bito com Pagamento Parcial!';
    end if;

end loop;


sSqlInsert := '
insert into ativs (q07_inscr,q07_perman,q07_seq,q07_calcula,q07_ativ,q03_descr,q07_datain,q07_datafi,q07_databx,q07_quant,q11_tipcalc)
        select distinct
                q07_inscr,
                q07_perman,
                q07_seq, \'*\'::char(1) as q07_calcula,
                q07_ativ,
                q03_descr,
                q07_datain,
                q07_datafi,
                q07_databx,
                q07_quant,
                q11_tipcalc
            from tabativ
                left outer join tabativtipcalc on q11_inscr   = q07_inscr
                                            and q11_seq     = q07_seq
                inner join ativtipo            on q07_ativ    = q80_ativ
                inner join tipcalc             on q80_tipcal  = q81_codigo
                inner join ativid              on q07_ativ    = q03_ativ
                inner join cadcalc             on q81_cadcalc = q85_codigo
        where q07_inscr ='||  iInscr ||'
            and q07_seq in ('||sAtivs||')
    union
        select distinct
                q07_inscr,
                q07_perman,
                q07_seq, \'*\'::char(1) as q07_calcula,
                q07_ativ,
                q03_descr,
                q07_datain,
                q07_datafi,
                q07_databx,
                q07_quant,
                q11_tipcalc
            from tabativ
                left outer join tabativtipcalc on q11_inscr     = q07_inscr
                                            and q11_seq       = q07_seq
                inner join clasativ            on q82_ativ      = q07_ativ
                inner join issbaseporte        on q45_inscr     = q07_inscr
                inner join issportetipo        on q41_codclasse = q82_classe
                                            and q41_codporte  = q45_codporte
                inner join tipcalc             on q81_codigo    = q41_codtipcalc
                inner join ativid              on q07_ativ      = q03_ativ
                inner join cadcalc             on q81_cadcalc   = q85_codigo
        where q07_inscr =  '|| iInscr ||'
            and q07_seq in ('||sAtivs||')' ;

execute sSqlInsert;

select count(*) from ativs into v_sequencia;

v_sequencia = 1;
--
-- Primeiro dia do ano para calculo
--
iPrimeiroDiaAno = to_char(iAnousu, '9999') || '-01-01';

--
-- Ultimo dia do ano para calculo
--
iUltimoDiaAno = to_char(iAnousu, '9999') || '-12-31';

--
-- Total de dias do ano
--
iTotalDiasAno  = iUltimoDiaAno::date - iPrimeiroDiaAno::date + 1;

select q02_inscr
    from issbase
    into iInscrexiste
    where q02_inscr = iInscr;
if iInscrexiste is null then
    return '11-INSCRICAO NAO CADASTRADA ';
end if;

select q02_dtinic,
        q02_dtinic,
        extract (month from q02_dtinic)
    from issbase
    into dInicioAtividade,
        dDataCadastro,
        v_mesinicio
    where q02_inscr = iInscr;

if dInicioAtividade is null then

    select q02_dtinic,
            extract (month from q02_dtinic)
    from issbase
    into dInicioAtividade,
            v_mesinicio
    where q02_inscr = iInscr;

    if dInicioAtividade is null then
    select min(q07_datain),
            extract (month from min(q07_datain))
        into dInicioAtividade,
            v_mesinicio
        from tabativ
        where (q07_datafi is null or q07_datafi >= dDatahj)
        and (q07_databx is null or q07_databx >= dDatahj);

    if dInicioAtividade is null then
        return '12-INSCRICAO SEM DATA DE INICIO CONFIGURADA! ';
    else
        v_manual = v_manual || 'data de inicio da inscricao (baseada na menor data de inicio das atividades): ' || dInicioAtividade || '\n';
    end if;
    else

    v_manual = v_manual || 'data de inicio da inscricao (baseada na data de cadastramento): ' || dInicioAtividade || '\n';

    end if;

else
    v_manual = v_manual || 'data de inicio da inscricao: ' || dInicioAtividade || '\n';
end if;

select extract (year from dDatahj) into v_anoatualservidor;

perform fc_debug('inscr: '||iInscr||' - inicio: '||dInicioAtividade,lRaise,false,false);

--*********************************** se calcula por area ou por quantidade de funcionarios ********************************************************
--
-- grande bloco que verifica a variavel v_quant, que ser√° utilizada para encontrar posteriormente o tipcalc a ser utilizado no calculo
--
select q60_campoutilcalc
    into v_tipo_quant
    from parissqn;

--  se o campo q60_campoutilcalc for igual a 1 calcula pelo campo q30_area

if v_tipo_quant is null then
    return '13-PARAMETRO DE QUANTIDADE PARA O CALCULO NAO CONFIGURADO NA TABELA PARISSQN';
end if;

--**************************************************************************************************************************************************

select q04_vbase,
        q04_calfixvar,
        q04_diasvcto
    from cissqn
    into v_base,
        iCalcfixvar,
        iDiasvctoCissqn
    where cissqn.q04_anousu = iAnousu;

if v_base = 0 or v_base is null then
    return '14-SEM VALOR BASE CADASTRADO NOS PARAMETROS ';
end if;

v_manual = v_manual || 'valor base configurado nos parametros: ' || v_base || '\n';

select q04_dtbase
    from cissqn
    into v_dtbase
where cissqn.q04_anousu = iAnousu;
if v_dtbase is null then
    return '15-SEM DATA BASE CADASTRADA NOS PARAMETROS ';
end if;
v_manual = v_manual || 'data base configurada nos parametros: ' || v_dtbase || '\n';

select distinct i02_valor
    from cissqn
    into v_valinflator
        inner join infla on q04_inflat = i02_codigo
    where cissqn.q04_anousu = iAnousu
    and date_part('year',i02_data)     = iAnousu
    and date_part('month',i02_data)    = date_part('month', fc_getsession('DB_datausu')::date);

if v_valinflator is null then
    v_valinflator = 1;
--      return 'valor do inflator nao configurado corretamente';
end if;
v_manual = v_manual || 'valor do inflator configurada nos parametros: ' || v_valinflator || '\n';

perform fc_debug('valor do inflator : '||v_valinflator,lRaise,false,false);

select q02_dtbaix
    from issbase
    into v_databaixa
    where q02_inscr = iInscr;
if v_databaixa is not null then
    return '16-INSCRICAO JA BAIXADA ';
end if;

-- Q81_TIPO:
-- 1 issqn
-- 4 alvara
-- 5 taxa de expediente


drop table if exists w_q81tipos;
create temp table w_q81tipos on commit drop as select 1 as q81_tipo union select 4  union select 5;


-- validacao para saoborja cgc = '88489786000101'
if exists(  select 1 from  issbase where q02_inscr = iInscr and  q02_formalocalvara = 2 ) and  exists(select 1 from db_config where prefeitura is true and cgc = '88489786000101') then

  delete from w_q81tipos where q81_tipo <> 1;
end if;


        select count(*) into v_quantativ from (
        select distinct q07_seq
            from tabativ
                inner join ativtipo on ativtipo.q80_ativ = tabativ.q07_ativ
                inner join tipcalc on q81_codigo = q80_tipcal
            where q81_tipo in (select q81_tipo from  w_q81tipos)
            and q07_inscr = iInscr
            and (q07_datafi is null or q07_datafi >= dDatahj)
            and (q07_databx is null or q07_databx >= dDatahj)) as x;


perform fc_debug('Total de atividades : '||v_quantativ,lRaise,false,false);

select q07_inscr
    from tabativ
    into v_tabativ
        inner join ativtipo on ativtipo.q80_ativ = tabativ.q07_ativ
    where q07_inscr = iInscr
    and q07_datain <= dDatahj;

select q07_inscr
    from tabativ
    into v_ativtipo
        inner join ativtipo on ativtipo.q80_ativ = tabativ.q07_ativ
    where q07_inscr = iInscr
    and (q07_datafi is null or q07_datafi >= dDatahj)
    and (q07_databx is null or q07_databx >= dDatahj);

select q07_inscr
    from tabativ
    into v_tipcalc
        inner join ativtipo on ativtipo.q80_ativ = tabativ.q07_ativ
        inner join tipcalc  on q81_codigo = q80_tipcal
    where q81_tipo in (select q81_tipo from  w_q81tipos)
    and q07_inscr = iInscr
    and q07_datain <= dDatahj;

perform fc_debug('Data da baixa : '||dDtbaixa,lRaise,false,false);

-- verifica quais os tipos de calculo para as atividades cadastradas para a inscricao

select cep
    from db_config
    into v_cepinstit
    where codigo = iInstit;

if v_cepinstit is null then
    return '17-PROBLEMAS COM A TABELA DB_CONFIG';
end if;
v_manual = v_manual || 'cep da instituicao: ' || v_cepinstit || '\n';

select q02_cep
    from issbase
    into v_cep
where q02_inscr = iInscr;

v_manual = v_manual || 'cep da inscricao: ' || v_cep || '\n';

v_manual = v_manual || '\n--- p r i m e i r a   e t a p a  -----\n';

select distinct
        q07_inscr
    into iAux
    from ativs;

perform fc_debug('Inscricao tabela temporaria (ativs) : '||iAux,lRaise,false,false);

-- ativs √© uma tabela tempor√°ria criada antes de chamar a funcao

for v_record_ativ in execute 'select * from ativs where q07_inscr = ' || iInscr loop


    v_manual = v_manual || '\nprocessando atividade: ' || v_record_ativ.q07_ativ || ' - ' || v_record_ativ.q03_descr || ' - sequencia: ' || v_record_ativ.q07_seq || ' - inicio: ' || v_record_ativ.q07_datain || '\n';
    --
    -- sempre entra aqui porque ninguem utiliza o esquema da tabela
    -- tabativtipcalc (tipo de calculo especifico para aquela atividade daquela inscricao)
    --
    if v_record_ativ.q11_tipcalc is null then
    v_text = 'select distinct
                        tipcalc.*,
                        cadcalc.q85_outromun,
                        cadcalc.q85_var,
                        case
                        when q81_tipo = 4 then
                            ( select q83_codven
                                from ativtipo ativtipo2
                                    inner join tipcalc    on ativtipo2.q80_tipcal = q81_codigo
                                    left  join tipcalcexe on q83_tipcalc = q81_codigo
                                                        and q83_anousu  = (select extract(year from q02_dtinic)
                                                                            from issbase
                                                                            where q02_inscr = '||iInscr||')
                            where ativtipo2.q80_ativ = ativtipo.q80_ativ and ativtipo2.q80_tipcal = ativtipo.q80_tipcal )
                        else
                            q83_codven
                        end as q83_codven
                from ativtipo
                        inner join tipcalc    on q80_tipcal = q81_codigo
                        left join tipcalcexe  on q83_tipcalc = q81_codigo
                                            and q83_anousu = ' || iAnousu || '
                        inner join cadcalc    on cadcalc.q85_codigo = tipcalc.q81_cadcalc
                where q81_tipo in (select q81_tipo from  w_q81tipos)
                    and q80_ativ = ' || v_record_ativ.q07_ativ || '
                union
                select tipcalc.*,
                        cadcalc.q85_outromun,
                        cadcalc.q85_var,
                        case
                        when q81_tipo = 4 then
                            ( select q83_codven
                                from tipcalc tipcalc2
                                    left  join tipcalcexe tipcalcexe2   on tipcalcexe2.q83_tipcalc = tipcalc2.q81_codigo
                                                            and tipcalcexe2.q83_anousu  = (select extract(year from issbase2.q02_dtinic)
                                                                                from issbase issbase2
                                                                                where issbase2.q02_inscr = '||iInscr||')
                                    inner join cadcalc cadcalc2  on cadcalc2.q85_codigo = tipcalc2.q81_cadcalc
                                    inner join clasativ clasativ2  on clasativ2.q82_classe = issportetipo.q41_codclasse and clasativ2.q82_ativ = ' || v_record_ativ.q07_ativ || '
                            where tipcalc2.q81_codigo = issportetipo.q41_codtipcalc )
                        else
                            q83_codven
                        end as q83_codven
                from issportetipo
                        inner join issbaseporte on q45_inscr = ' || iInscr || '
                        inner join tipcalc      on q41_codtipcalc = q81_codigo
                        left  join tipcalcexe   on q83_tipcalc = q81_codigo
                                            and q83_anousu = ' || iAnousu || '
                        inner join cadcalc      on cadcalc.q85_codigo = tipcalc.q81_cadcalc
                        inner join clasativ     on q82_classe = q41_codclasse
                where q45_codporte = q41_codporte
                    and q81_tipo in (select q81_tipo from  w_q81tipos)
                    and q82_ativ = ' || v_record_ativ.q07_ativ;

    v_textexclui = 'select distinct
                            tipcalc.q81_cadcalc
                        from ativtipo
                            inner join tipcalc on q80_tipcal = q81_codigo
                            inner join cadcalc on cadcalc.q85_codigo = tipcalc.q81_cadcalc
                        where q81_tipo in (select q81_tipo from  w_q81tipos)
                        and q80_ativ = ' || v_record_ativ.q07_ativ || '
                    union
                    select tipcalc.q81_cadcalc
                        from issportetipo
                            inner join issbaseporte on q45_inscr = ' || iInscr || '
                            inner join tipcalc      on q41_codtipcalc = q81_codigo
                            inner join cadcalc      on cadcalc.q85_codigo = tipcalc.q81_cadcalc
                            inner join clasativ     on q82_classe = q41_codclasse
                        where q45_codporte = q41_codporte
                        and q81_tipo in (select q81_tipo from  w_q81tipos)
                        and q82_ativ = ' || v_record_ativ.q07_ativ;
    else
    v_text = 'select tipcalc.*,
                        q85_outromun,
                        cadcalc.q85_var
                from tipcalc
                        left outer join cadcalc on cadcalc.q85_codigo = tipcalc.q81_cadcalc
                where q81_tipo in (select q81_tipo from  w_q81tipos)
                    and q81_codigo = ' || v_record_ativ.q11_tipcalc;
    end if;

    -- deletando calculo atual da inscricao do ano
    v_manual = v_manual || 'deletando os calculos antigos da inscricao no ano - apenas os que nao serao recalculados neste calculo\n';

    v_textexclui2 = 'select q01_numpre,
                            q01_cadcal
                        from isscalc
                    where q01_cadcal not in ( ' || v_textexclui || ')
                        and q01_inscr  = ' || iInscr || '
                        and q01_anousu = ' || iAnousu;

    -- limpa o financeiro do que foi selecionado pelo usuario antes de calcular

    for v_record_excluir in execute v_textexclui2 loop
        perform fc_debug('Numpre : '||v_record_excluir.q01_numpre,lRaise,false,false);
        -- verifica se n√£o esta no arrecant
        select k00_numpre
            from arrecant
            into v_numprejapago
            where k00_numpre = v_record_excluir.q01_numpre;
        if v_numprejapago is null then

            v_manual = v_manual || '     deletando numpre ' || v_record_excluir.q01_numpre || ' - calculo: ' || v_record_excluir.q01_cadcal || '\n';
            delete from arrecad where k00_numpre = v_record_excluir.q01_numpre;
            delete from isscalc where q01_numpre = v_record_excluir.q01_numpre;
            v_quantexcluido = v_quantexcluido + 1;

        else
            perform fc_debug('Numpre ja pago : '||v_record_excluir.q01_numpre,lRaise,false,false);
        end if;
    end loop;

    v_manual = v_manual || 'quantidade de calculos excluidos: ' || v_quantexcluido || ' \n';

    perform fc_debug('Atividade : '||v_record_ativ.q07_ativ,lRaise,false,false);

    perform fc_debug('',lRaise,false,false);
    perform fc_debug('Sql buscando os tipos de calculo : '||v_text,lRaise,false,false);
    perform fc_debug('',lRaise,false,false);

    -- para cada atividade retornada com seu tipo de calculo...


    for v_record_tipcalc in execute v_text loop


        select *
        into v_quant, tManual
        from fc_buscaquantidadeempresa( cast(iInscr                 as integer),
                                        cast(iAnousu                as integer),
                                        cast(v_tipo_quant           as integer),
                                        cast(v_record_ativ.q07_ativ as integer)
                                        );
        perform fc_debug('========== Excluindo registros da porcalculo e "do": '||tManual,lRaise,false,false);


        perform fc_debug('========== QUANTIDADES : '||tManual,lRaise,false,false);

        v_manual = v_manual||tManual;

        if v_quant = 0 and v_tipo_quant = 3 then
            return '30 - Erro buscando as quantidades para o calculo por pontua√ß√£o. Verifique o cadastro de pontua√ß√£o das Classes, Areas, Zonas e Empregados';
        end if;


    perform fc_debug('========== Tipo de calculo : '||v_record_tipcalc.q81_codigo||'-'||v_record_tipcalc.q81_descr||' - Quantidade : '||v_quant,lRaise,false,false);

    v_continuar = true;

    perform fc_debug('p r o c e s s a n d o  tipo de calculo: ' || v_record_tipcalc.q81_codigo || ' - ' || v_record_tipcalc.q81_descr || ' - gera: ' || v_record_tipcalc.q81_gera,lRaise,false,false);

    if  extract(year from dInicioAtividade )::integer = iAnousu then
        v_q81_rec  = v_record_tipcalc.q81_recexe;
        v_q81_qini = v_record_tipcalc.q81_qiexe;
        v_q81_qfim = v_record_tipcalc.q81_qfexe;
        v_q81_val  = v_record_tipcalc.q81_valexe;
    else
        v_q81_rec  = v_record_tipcalc.q81_recpro;
        v_q81_qini = v_record_tipcalc.q81_qipro;
        v_q81_qfim = v_record_tipcalc.q81_qfpro;
        v_q81_val  = v_record_tipcalc.q81_valpro;
    end if;

    perform fc_debug('Quantidade : '||coalesce(v_quant,0)||' Entre : '||coalesce(v_q81_qini,0)||' e '||coalesce(v_q81_qfim,0),lRaise,false,false);

    if coalesce(v_quant,0) >= coalesce(v_q81_qini,0) and coalesce(v_quant,0) <= coalesce(v_q81_qfim,999999) then

        perform fc_debug('Dentro do if da quantidade gera : '||v_record_tipcalc.q81_gera,lRaise,false,false);
        perform fc_debug('Ano de inicio de atividades : '||to_number(substr(dInicioAtividade,1,4),'99999'),lRaise,false,false);

        if v_record_tipcalc.q81_gera = 1 and to_number(substr(dInicioAtividade,1,4),'99999') < iAnousu then
        perform fc_debug('nao vai processar...',lRaise,false,false);
        v_manual = v_manual || '\n';
        else

        perform fc_debug('entrou no gera 2 do if - verificando pagamentos ',lRaise,false,false);

        v_manual = v_manual || 'verificando pagamentos\n';

        for v_numprejacalculado in
            select q01_numpre
            from isscalc
            where q01_anousu = iAnousu
                and q01_inscr  = iInscr
                and q01_cadcal = v_record_tipcalc.q81_cadcalc
                and q01_recei  = v_q81_rec
        loop

            perform fc_debug('procurando numpre : '||v_numprejacalculado.q01_numpre,lRaise,false,false);
            v_manual = v_manual || 'processando numpre: ' || v_numprejacalculado.q01_numpre || '\n';

            -- trocado ordem de verifica√ß√£o de arrecad, arrecant para arrecant, arrecad
            select k00_numpre
            from arrecant
            into v_numprejapago
            where k00_numpre = v_numprejacalculado.q01_numpre;

            perform fc_debug('q01_numpre: '|| v_numprejacalculado.q01_numpre,lRaise,false,false);

            if v_numprejacalculado.q01_numpre is not null then

            perform fc_debug('v_numprejacalculado.q01_numpre is not null - v_numprejapago: '||v_numprejapago,lRaise,false,false);

            if v_numprejapago is null then

                v_manual = v_manual || 'numpre em aberto\n';

                if dDtbaixa is not null and v_record_tipcalc.q85_var is true then
                v_manual = v_manual || 'se data da baixa preenchido e variavel, deleta do arrecad e issvar\n';

                perform fc_debug('Deletando do arrecad e issvar',lRaise,false,false);

                delete from arrecad where k00_numpre = v_numprejacalculado.q01_numpre and k00_numpar >= to_number(substr(dDtbaixa,6,2),'999') + 1;

                perform *
                    from issvarlev
                        inner join issvar on issvar.q05_codigo = issvarlev.q18_codigo
                    where q05_numpre = v_numprejacalculado.q01_numpre
                    and q05_numpar >= to_number(substr(dDtbaixa,6,2),'999') + 1;
                if found then
                    return '28 - EMPRESA JA POSSUI LEVANTAMENTO FISCAL PARA COMPETENCIA : '||(to_number(substr(dDtbaixa,6,2),'999') + 1)||'/'||iAnousu ;
                end if;

                delete from issvarnotas
                    using issvar
                    where issvar.q05_codigo  = issvarnotas.q06_codigo
                    and issvar.q05_numpre  = v_numprejacalculado.q01_numpre
                    and issvar.q05_numpar >= to_number(substr(dDtbaixa,6,2),'999') + 1;

                delete from issvar
                    where q05_numpre = v_numprejacalculado.q01_numpre
                    and q05_numpar >= to_number(substr(dDtbaixa,6,2),'999') + 1;

                elsif dDtbaixa is not null then

                v_manual = v_manual || 'se data da baixa preenchido e nao variavel, insere no isscalcant e deleta do isscalc e arrecad\n';

                perform fc_debug('Deletando do arrecad e isscalv',lRaise,false,false);

                insert into isscalcant select * from isscalc where q01_numpre = v_numprejacalculado.q01_numpre;
                delete from isscalc where q01_numpre = v_numprejacalculado.q01_numpre;
                delete from arrecad where k00_numpre = v_numprejacalculado.q01_numpre;
                end if;

            else

                perform fc_debug('Numpre pago.',lRaise,false,false);

                v_manual = v_manual || 'numpre pago\n';
                v_comcalculo = true;

            end if;

            if v_numprejapago is null then

                select k00_numpre
                from arrecad
                into v_numprejapago
                where k00_numpre = v_numprejacalculado.q01_numpre;

                if v_numprejapago is not null then

                if bRecalc is false then
                    return '18-INSCRICAO JA CALCULADA E RECALCULO NAO PASSADO COMO PARAMETRO';
                end if;

                perform fc_debug('recalculo',lRaise,false,false);

                v_manual = v_manual || 'recalculo\n';
                v_comcalculo = true;
                end if;

            end if;

            end if;

        end loop;

        perform fc_debug('saiu do procura pagamentos - continuar : '||( case when v_continuar is true then 'true' else 'false' end ),lRaise,false,false);

        if v_continuar then

            -- se true, busca quantidade do tabativ, senao default 1
            if v_record_tipcalc.q81_uqtab is false then

            perform fc_debug('uqtab false',lRaise,false,false);

            v_uqtab = 1;
            v_manual = v_manual || 'quantidade utilizada da tabela para calculo (utilizada default do sistema):' || v_uqtab || '\n';
            else

            perform fc_debug('uqtab true',lRaise,false,false);

            if v_record_ativ.q07_quant = 0 then
                v_uqtab = 1;
            else
                v_uqtab = v_record_ativ.q07_quant;
            end if;
            v_manual = v_manual || 'quantidade utilizada para calculo (baseada na quantidade da atividade lancada): ' || v_uqtab || '\n';
            end if;

            -- se true, busca quantidade do issquant, senao default 1
            if v_record_tipcalc.q81_uqcad is false then
            v_uqcad = 1;
            else
            select q30_mult
                from issquant
                into v_uqcad
                where issquant.q30_inscr = iInscr
                and issquant.q30_anousu = iAnousu;
            if v_uqcad is null then
                return '19-MULTIPLICADOR NAO LANCADO PARA ESTA INSCRICAO';
            end if;

            end if;

            if v_record_tipcalc.q81_integr is true then
            v_integr = '1';
            else
            v_integr = '0';
            end if;
            v_manual = v_manual || 'integral: (1 = sim - 0 = nao): ' || v_integr || '\n';

            select case when q85_var is true then '1' else '0' end as q85_var
            from cadcalc
            into v_var
            where cadcalc.q85_codigo = v_record_tipcalc.q81_cadcalc;
            if v_var is null then
            return '20-NAO DEFINIDO NO CADASTRO DE CALCULO SE VARIAVEL OU NAO';
            end if;

            select q85_forcal
            from cadcalc
            into v_forcal
            where cadcalc.q85_codigo = v_record_tipcalc.q81_cadcalc;
            if v_forcal is null then
            return '21-NAO DEFINIDO NO CADASTRO DE CALCULO A FORMA DE CALCULO';
            end if;
            v_manual = v_manual || 'forma de calculo (1 = atividade principal - 2 = atividade com maior valor - 3 = soma do valor das atividades):' || v_forcal || '\n';

            select q85_perman
            from cadcalc
            into v_provisorio
            where cadcalc.q85_codigo = v_record_tipcalc.q81_cadcalc;

            if v_provisorio is true and v_record_ativ.q07_perman is false then

            v_qprovisorio = v_record_tipcalc.q81_percprovis;
            v_manual = v_manual || 'provisorio: vai acrescer ' || v_qprovisorio || ' por centro no valor calculado\n';

            perform fc_debug('Provisorio -- '||v_qprovisorio,lRaise,false,false);

            else

            v_qprovisorio = 1;

            perform fc_debug('Nao provisorio',lRaise,false,false);

            end if;

            perform fc_debug('inserindo na tabela tudo... sequencia:'||v_sequencia,lRaise,false,false);
            perform fc_debug('q81_val: '||v_q81_val||' - v_base: '|| v_base||' - uqtab: '||v_uqtab||' - uqcad: '||v_uqcad||' - qprovisorio: '||v_qprovisorio||' - valinflator: '||v_valinflator,lRaise,false,false);

            if v_record_tipcalc.q83_codven is null then
            return '25-VENCIMENTO NAO ENCONTRADO NO CADASTRO DE TIPO DE CALCULO';
            else
            v_codven = v_record_tipcalc.q83_codven;
            end if;
            perform fc_debug('q81_codigo: '||v_record_tipcalc.q81_codigo||' - q81_cadcalc: '||v_record_tipcalc.q81_cadcalc||' - vencimento: '||v_codven,lRaise,false,false);

            --
            -- Se empresa for optante pelo simples nao deve ser efetuado
            -- calculo de alvara nem de taxa de expediente
            --
            if lInscricaoMei then

            if v_record_tipcalc.q81_cadcalc = 1 then
                perform fc_debug('Inscricao optante pelo MEI, nao calculando alvara.',lRaise,false,false);
                continue;
            end if;

            -- Se par√¢metro Calcula Vistorias para MEI: estiver N√ÉO ent√£o ignora taxa de expediente

            if lCalculaVistoriasMEI = false then

                if v_record_tipcalc.q81_tipo = 5 then -- tipo de c√°lculo for taxa

                perform fc_debug('Inscricao optante pelo MEI, nao calculando TAXA DE EXPEDIENTE.',lRaise,false,false);
                continue;
                end if;

            end if;

            end if;


            insert into
            tudo values (iAnousu,
                            iInscr,
                            v_record_ativ.q07_ativ,
                            v_record_tipcalc.q81_codigo,
                            v_record_tipcalc.q81_cadcalc,
                            v_base,
                            v_record_tipcalc.q81_recexe,
                            v_record_tipcalc.q81_recpro,
                            coalesce(v_quant,0),
                            v_record_tipcalc.q81_qiexe,
                            v_record_tipcalc.q81_qfexe,
                            v_uqtab,
                            v_uqcad,
                            v_forcal,
                            v_codven,
                            v_integr,
                            v_record_tipcalc.q81_tippro,
                            v_q81_val * v_base * v_uqtab * v_uqcad * v_qprovisorio * v_valinflator,
                            v_q81_val * v_qprovisorio * v_valinflator,
                            v_record_ativ.q07_datain,
                            coalesce( v_record_ativ.q07_datafi,(iAnousu||'-12-31')::date ),
                            v_var,
                            v_record_tipcalc.q81_gera,
                            v_sequencia);

                v_sequencia = v_sequencia + 1;

        end if;

        end if;

    end if;

    end loop;
    perform fc_debug('Terminou atividade',lRaise,false,false);

end loop;

--
-- SEGUNDA FASE DO CALCULO
-- na fase anterior a rotina insere registros na tabela tudo e nela √© que se baseia daqui para frente para saber o que calcular
--

perform fc_debug('--------------------------------------------------------------------------------------------------------',lRaise,false,false);
perform fc_debug('',lRaise,false,false);
perform fc_debug('SEGUNDA FASE DO CALCULO',lRaise,false,false);
perform fc_debug('',lRaise,false,false);
perform fc_debug('Na fase anterior a rotina insere registros na tabela tudo e nela √© que se baseia daqui para frente para saber o que calcular',lRaise,false,false);


if    iTipoCalculo = 1  then
    perform fc_debug('Excluindo registros de Alvar√°', lRaise, false, false);
    DELETE FROM tudo where cadcalc not in (2,3);
elsif iTipoCalculo = 2  then

    perform fc_debug('Excluindo registros de ISSQN', lRaise, false, false);
    DELETE FROM tudo where cadcalc not in (1,4,9);
end if;


select count(*) into iAux from tudo;

v_manual = v_manual || '\nregistros processados na etapa de tipos de calculo: ' || iAux || '\n';
v_manual = v_manual || '\n---- s e g u n d a  e t a p a  -----\n';
v_manual = v_manual || 'agrupando por cadastro de calculo' || '\n';

perform fc_debug('Quantidade de registros tabela tudo : '||iAux,lRaise,false,false);


--
-- for na tabela tudo com os tipos de calculo a utilizar
-- veja o detalhe do group by, que faz com que apenas um cadcalc (ALVARA/ISSQN FIXO/ISSQN VARIAVEL) seja utilizado por calculo
-- nessa fase o sistema cria registros na tabela porcalculo que ser√° utilizada nessa fase
--
for v_record_cadcalc in select cadcalc, forcal, var from tudo group by cadcalc, forcal, var loop

    select q85_descr
    into v_descrcadcalc
    from cadcalc
    where q85_codigo = v_record_cadcalc.cadcalc;

    v_manual = v_manual || '   processando calculo ' || v_record_cadcalc.cadcalc || ' - ' || v_descrcadcalc || '\n';

    perform fc_debug('Var : '||v_record_cadcalc.var||' cadcalc : '||v_record_cadcalc.cadcalc,lRaise,false,false);

    -- se for variavel
    if v_record_cadcalc.var = '1' then

    v_manual = v_manual || '      variavel\n';

    -- pode ser fixado por inscricao
    for v_record_variavel in select * from tudo where tudo.cadcalc = v_record_cadcalc.cadcalc limit 1 loop

        perform fc_debug('Inserindo na tabela tudo fixado por inscricao',lRaise,false,false);

        execute 'insert into porcalculo values ('
        || iAnousu || ','
        || iInscr  || ','
        || v_base || ','
        || v_record_variavel.tipcalc || ','
        || v_record_variavel.cadcalc || ','
        || v_record_variavel.forcal  || ','
        || v_record_variavel.codven  || ','
        || v_record_variavel.integr  || ','
        || '''' || v_record_variavel.tipopro || '''' || ','
        || '''' || v_record_variavel.inicio  || '''' || ','
        || '''' || coalesce( v_record_variavel.final,(iAnousu||'-12-31')::date )   || '''' || ','
        || 0 || ','
        || v_record_variavel.valori || ','
        || 0 || ','
        || '''' || v_record_variavel.var     || '''' || ','
        || v_record_variavel.gera || ','
        || v_record_variavel.seq
        || ');';

    end loop;

    -- se NAO for variavel
    else

    perform fc_debug('Inserindo na tabela tudo pela atividade principal',lRaise,false,false);

    v_manual = v_manual || '      nao variavel\n';

    -- se for pela atividade principal
    if v_record_cadcalc.forcal = 1 then
        v_manual = v_manual || '         calculando pela atividade principal\n';

        select q07_ativ
        into v_ativprinc
        from ativprinc
                inner join tabativ on q07_inscr = q88_inscr and q07_seq = q88_seq
        where q88_inscr = iInscr;
        if v_ativprinc is null then
        return '22-SEM ATIVIDADE PRINCIPAL CADASTRADA PARA ESTA INSCRICAO ';
        end if;

--        for v_record_ativprinc in select * from tudo where tudo.cadcalc = v_record_cadcalc.cadcalc and tudo.ativ = v_ativprinc loop
--        DESCOBRIR QUEM E PORQUE COMENTARAM A LINHA ACIMA
--        PORQUE PELA LOGICA DEVERIA UTILIZAR A LINHA COMENTADA

        for v_record_ativprinc in select * from tudo where tudo.cadcalc = v_record_cadcalc.cadcalc limit 1 loop

        v_manual = v_manual || '            inserindo na tabela de tipos de calculo a processar - tipcalc: ' || v_record_ativprinc.tipcalc || ' - cadcalc: ' || v_record_ativprinc.cadcalc || '\n';
        execute 'insert into porcalculo values ('
        || iAnousu || ','
        || iInscr  || ','
        || v_base || ','
        || v_record_ativprinc.tipcalc || ','
        || v_record_ativprinc.cadcalc || ','
        || v_record_ativprinc.forcal  || ','
        || v_record_ativprinc.codven  || ','
        || v_record_ativprinc.integr  || ','
        || '''' || v_record_ativprinc.tipopro || '''' || ','
        || '''' || v_record_ativprinc.inicio  || '''' || ','
        || '''' || coalesce( v_record_ativprinc.final, (iAnousu||'-12-31')::date )   || '''' || ','
        || v_record_ativprinc.valor   || ','
        || v_record_ativprinc.valori  || ','
        || 0 || ','
        || '''' || v_record_ativprinc.var     || '''' || ','
        || v_record_ativprinc.gera    || ','
        || v_record_ativprinc.seq
        || ');';

        end loop;

    end if;

    -- pela atividade que gerou o maior valor
    if v_record_cadcalc.forcal = 2 then
        v_manual = v_manual || 'calculando pela atividade que gerou o maior valor\n';

        for v_record_maiorvalor in select * from tudo where tudo.cadcalc = v_record_cadcalc.cadcalc order by valor desc limit 1 loop

        perform fc_debug('Inserindo na tabela porcalculo pela atividade de maior valor valor : '||v_record_maiorvalor.valor||' - tipo de calculo: '||v_record_maiorvalor.tipcalc||' - vencimento: '||v_record_maiorvalor.codven,lRaise,false,false);

        execute 'insert into porcalculo values ('
        || iAnousu || ','
        || iInscr  || ','
        || v_base || ','
        || v_record_maiorvalor.tipcalc || ','
        || v_record_maiorvalor.cadcalc || ','
        || v_record_maiorvalor.forcal  || ','
        || v_record_maiorvalor.codven  || ','
        || v_record_maiorvalor.integr  || ','
        || '''' || v_record_maiorvalor.tipopro || '''' || ','
        || '''' || v_record_maiorvalor.inicio  || '''' || ','
        || '''' || coalesce( v_record_maiorvalor.final, (iAnousu||'-12-31')::date )   || '''' || ','
        || v_record_maiorvalor.valor   || ','
        || v_record_maiorvalor.valori  || ','
        || 0 || ','
        || '''' || v_record_maiorvalor.var     || '''' || ','
        || v_record_maiorvalor.gera    || ','
        || v_record_maiorvalor.seq
        || ');';

        end loop;

    end if;

    -- pela soma de todos os valores calculados
    -- ainda nao implementado totalmente
    -- ou seja, nao funciona ainda...
    -- TESTADORES DEVEM UTILIZAR ESSA FORMULA DE CALCULO NOS TESTES
    if v_record_cadcalc.forcal = 3 then
        v_manual = v_manual || 'calculando pela soma de todos os valores\n';

        for v_record_somatodos in select * ,
                                        (select sum(valor) as somatotal
                                            from tudo
                                            where tudo.cadcalc = v_record_cadcalc.cadcalc) as somatotal
                                    from tudo
                                    where tudo.cadcalc = v_record_cadcalc.cadcalc loop

        select q85_codven
        from cadcalc
        into v_codvencadcalc
        where q85_codigo = v_record_cadcalc.cadcalc;
        if v_codvencadcalc is null then
            return '23-SEM VENCIMENTO PADRAO NO CADASTRO DE VENCIMENTOS ';
        end if;

        execute 'insert into porcalculo values ('
        || iAnousu || ','
        || iInscr  || ','
        || v_base || ','
        || v_record_somatodos.tipcalc || ','
        || v_record_cadcalc.cadcalc || ','
        || v_record_cadcalc.forcal  || ','
        || v_codvencadcalc            || ','
        || v_record_somatodos.integr  || ','
        || '''' || v_record_somatodos.tipopro || '''' || ','
        || '''' || v_record_somatodos.inicio  || '''' || ','
        || '''' || coalesce( v_record_somatodos.final, (iAnousu||'-12-31')::date)   || '''' || ','
        || v_record_somatodos.somatotal || ','
        || v_record_somatodos.valori  || ','
        || 0 || ','
        || '''' || v_record_somatodos.var     || '''' || ','
        || v_record_somatodos.gera    || ','
        || v_record_somatodos.seq
        || ');';

        end loop;

    end if;

    end if;

end loop;
-- fim do for do select na tabela tudo, que gera os registros na porcalculo

v_manual = v_manual || '\n---- t e r c e i r a  e t a p a  -----\n';
v_manual = v_manual || 'agrupando por tipo de vencimento e preparando para calcular\n';

perform fc_debug('--------------------------------------------------------------------------------------------------------',lRaise,false,false);
perform fc_debug('',lRaise,false,false);
perform fc_debug('TERCEIRA ETAPA DO CALCULO ',lRaise,false,false);
perform fc_debug('',lRaise,false,false);
perform fc_debug('agrupando por tipo de vencimento e preparando para calcular',lRaise,false,false);

for v_record_cadcalc in select porcalculo.*,
                                tipcalc.q81_excedenteativ
                            from porcalculo
                                inner join tipcalc on q81_codigo = porcalculo.tipcalc
loop

    if v_record_cadcalc.q81_excedenteativ > 0 then
    v_valor = v_record_cadcalc.valor;

    perform fc_debug('Seq : '||v_record_cadcalc.seq||' Valor : '||v_valor,lRaise,false,false);

    v_valor = v_valor + (v_valor * v_record_cadcalc.q81_excedenteativ * (v_quantativ - 1));
    update porcalculo set valor = v_valor where seq = v_record_cadcalc.seq ;

    perform fc_debug('Apos calcular 30% por atividade excedente - Seq : '||v_record_cadcalc.seq||' Valor : '||v_valor,lRaise,false,false);

    end if;
end loop;

perform fc_debug('Descobrindo de calcula fixo ou variavel ',lRaise,false,false);

--
-- Verifica se calcula fixo ou variavel
--
for v_record_cadvenc in select distinct cadcalc from porcalculo
loop

    if v_record_cadvenc.cadcalc = 2 then

    v_cadcalc_fix = true;

    elsif v_record_cadvenc.cadcalc = 3 then

    v_cadcalc_var = true;

    end if;

end loop;

if v_cadcalc_fix = true and v_cadcalc_var = true and iCalcfixvar = 1 then

    perform fc_debug('Inscricao com dois (2) calculos fixo/var, excluido calculo fixo',lRaise,false,false);
    delete from porcalculo where cadcalc = 2;
    v_manual = v_manual || '\n inscricao com dois (2) calculos fixo/variavel, calculado somente variavel \n';

end if;

if v_cadcalc_fix = true and v_cadcalc_var = true and iCalcfixvar = 2 then

    perform fc_debug('Inscricao com dois (2) calculos fixo/var, excluido calculo variavel',lRaise,false,false);

    delete from porcalculo where cadcalc = 3;
    v_manual = v_manual || '\n inscricao com dois (2) calculos fixo/variavel, calculado somente fixo \n';

end if;

perform fc_debug('select trazendo os vencimentos para gerar as proporcionalidades',lRaise,false,false);

for v_record_cadvenc in select codven from porcalculo group by codven
loop

    v_manual = v_manual || 'processando vencimento ' || v_record_cadvenc.codven || '\n';

    perform fc_debug('Codigo do cadastro de vencimentos : '||v_record_cadvenc.codven,lRaise,false,false);

    for v_record_cadcalc in select distinct tipcalc, cadcalc, valor, integr, inicio, final, tipopro, seq from porcalculo where codven = v_record_cadvenc.codven loop

    perform fc_debug('Tipo de calulo : '||v_record_cadcalc.tipcalc||' Cadcalc : '||v_record_cadcalc.cadcalc||'seq :'||v_record_cadcalc.seq,lRaise,false,false);

    select q81_abrev
    into v_descrtipcalc
        from tipcalc
        where q81_codigo = v_record_cadcalc.tipcalc;

    select q85_descr
        into v_descrcadcalc
        from cadcalc
        where q85_codigo = v_record_cadcalc.cadcalc;

    v_manual = v_manual || 'processando tipcalc: ' || v_record_cadcalc.tipcalc || ' - ' || v_descrtipcalc || ' - cadcalc: ' || v_record_cadcalc.cadcalc || ' - ' || v_descrcadcalc || '\n';

    v_valor = v_record_cadcalc.valor;
    v_manual = v_manual || 'valor: ' || v_valor || '\n';

    -- se √© para calcular com proporcionalidade
    if v_record_cadcalc.integr = '0' then

        perform fc_debug('Valor sem proporcionalidade : '||v_valor,lRaise,false,false);

        v_manual = v_manual || '   nao integral = proporcional ' || ' inicio: ' || v_record_cadcalc.inicio || ' - ano atual ' || iAnousu || '\n';

        -- soh calcula integralidade se ano do inicio da atividade for igual ao atual ou for calculo de baixa
        if extract(year from v_record_cadcalc.inicio)::integer = iAnousu or dDtbaixa is not null then
        --
        -- Calculo da proporcionalidade
        --
        if dDtbaixa is null then
            dDtProporcionalidade := v_record_cadcalc.final;
        else
            dDtProporcionalidade := dDtbaixa;
        end if;

        perform fc_debug('Tipo   - '||v_record_cadcalc.tipopro,lRaise,false,false);
        perform fc_debug('Inicio - '||v_record_cadcalc.inicio,lRaise,false,false);
        perform fc_debug('Final  - '||dDtProporcionalidade,lRaise,false,false);

        --
        -- Funcao fc_issqn_proporcionalidade
        --   retorna o valor proporcional ao periodo de atividade do exercio e a descricao do tipo de proporcionalidade
        --
        select rnValorProporcional,rsTipoProporcionalidade
            into v_valor,sDescrProporcionalidade
            from fc_issqn_proporcionalidade(v_record_cadcalc.valor::numeric,v_record_cadcalc.tipopro::varchar,v_record_cadcalc.inicio::date,dDtProporcionalidade::date,iAnousu::integer,dDtbaixa::date);

        v_manual = v_manual ||sDescrProporcionalidade||' \n';

        end if;

        perform fc_debug('fc_issqn_proporcionalidade(' || v_record_cadcalc.valor::numeric || ',' || v_record_cadcalc.tipopro::varchar || ',' || v_record_cadcalc.inicio::date || ',' || dDtProporcionalidade::date || ',' || iAnousu::integer || ',' || dDtbaixa::date || ')');

        perform fc_debug('Valor com a proporcionalide : '||v_valor,lRaise,false,false);

        v_manual = v_manual || 'valor ja calculado a proporcionalidade: ' || v_valor || '\n';

    else

        v_manual = v_manual || 'integral\n';

    end if;

    update porcalculo set valorintegr = v_valor  where seq = v_record_cadcalc.seq ;

    end loop;

end loop;

select count(*)
    into iAux
    from porcalculo;

v_manual = v_manual || '\n---- q u a r t a  e t a p a  -----\n';
v_manual = v_manual || 'total de calculos que o sistema vai processar: ' || iAux || '\n';

if iAux = 0 then
    return '24-NENHUM CALCULO EFETUADO!';
end if;

--
-- QUARTA FASE - GERANDO FINANCEIRO
--
perform fc_debug('--------------------------------------------------------------------------------------------------------',lRaise,false,false);
perform fc_debug('',lRaise,false,false);
perform fc_debug('QUARTA FASE DO CALCULO (GERANDO FINANCEIRO) ',lRaise,false,false);
perform fc_debug('Quantidade de registros tabela porcalculo : '||iAux,lRaise,false,false);
perform fc_debug('',lRaise,false,false);
perform fc_debug('--------------------------------------------------------------------------------------------------------',lRaise,false,false);

/* for nos calculo da inscricao que esta sendo calculada */

perform fc_debug('Percorrendo os calculo da inscricao que esta sendo calculada',lRaise,false,false);

dInicioAtividade := dDataCadastro;

for v_record_cadcalc in select * from porcalculo loop

    select q81_abrev
    from tipcalc
    into v_descrtipcalc
    where q81_codigo = v_record_cadcalc.tipcalc;

    select q85_descr
    from cadcalc
    into v_descrcadcalc
    where q85_codigo = v_record_cadcalc.cadcalc;

    v_manual = v_manual || '\nprocessando calculo ' || v_record_cadcalc.cadcalc || ' - ' || v_descrcadcalc ||  ' - tipo de calculo: ' || v_record_cadcalc.tipcalc || ' - ' || v_descrtipcalc || '\n';

    -- data de baixa preenchida e √© variavel
    -- resumindo, √© para nao fazer nada se for vari√°vel e calculo para baixa

    if dDtbaixa is not null and v_record_cadcalc.var = '1' then

       perform fc_debug('Com data de baixa passada como parametro: ' || dDtbaixa || ' e variavel: nao calcula',lRaise,false,false);
       v_manual = v_manual || 'com data de baixa passada como parametro: ' || dDtbaixa || ' e variavel: nao calcula\n';

        /* Quando eh baixa de MEI deve apagar arrecad posterior a competencia da baixa */
        if lInscricaoMei and v_numprejacalculado.q01_numpre is not null then
            perform fc_debug('Apagando arrecad de inscricao MEI com data de baixa passada como parametro: ' || dDtbaixa ,lRaise,false,false);
            delete from arrecad where k00_numpre = v_numprejacalculado.q01_numpre and k00_dtvenc > dDtbaixa and k00_numpar >= to_number(substr(dDtbaixa,6,2),'999');

        end if;

    -- senao
    else

    select q01_numpre
        from isscalc
        into v_numpre
        where q01_anousu = iAnousu
        and q01_inscr = iInscr
        and q01_cadcal = v_record_cadcalc.cadcalc;

    if v_numpre is null then

        perform fc_debug('Sem calculo para o exercicio ',lRaise,false,false);
        v_comcalculo = false;

    else

        perform fc_debug('Com calculo para o exercicio numpre : '||v_numpre,lRaise,false,false);
        v_comcalculo = true;

    end if;

    if v_comcalculo is false then
        v_manual = v_manual || 'calculo novo\n';

        select nextval('numpref_k03_numpre_seq')
        into v_numpre;

        perform fc_debug('Calculo Novo ',lRaise,false,false);
        perform fc_debug('Novo numpre : '||v_numpre,lRaise,false,false);

        if v_numpre is null then
        return '25-ERRO DO PROCESSAR SEQUENCIA DO NUMPRE';
        end if;

    else

        v_manual = v_manual || 'calculo ja existe... utilizar o mesmo numpre\n';
        select q01_numpre
        from isscalc
        into v_numpre
        where q01_anousu = iAnousu
            and q01_inscr = iInscr
            and q01_cadcal = v_record_cadcalc.cadcalc;

    end if;

    v_numpar = 1;

    select max(q82_parc)
        into v_numtot
        from cadvenc
            inner join cadvencdesc on q82_codigo = q92_codigo
        where cadvenc.q82_codigo = v_record_cadcalc.codven;

    if v_record_cadcalc.var = '1' then
        v_numtot = 12;
    else

        if v_numtot is null then
        v_numtot = 0;
        end if;

        if bGeral is false then
        v_numtot = 1;
        end if;
    end if;

    v_manual = v_manual || 'total de parcelas baseado no vencimento: ' || v_numtot || '\n';

    perform fc_debug('Codigo do Vencimento encontrado : '||v_record_cadcalc.codven,lRaise,false,false);
    perform fc_debug('Numpre : '||v_numpre||' Parcela : '||v_numpar||' Numtot : '||v_numtot||' Cadcalc : '||v_record_cadcalc.cadcalc,lRaise,false,false);

    if dDtbaixa is not null then 
       v_numpar = 1;
       v_numtot = 1;
    end if;

    select fc_digito(v_numpre,v_numpar,v_numtot)
        into v_numdig;

    if v_numdig is null then
        return '26-ERRO DO PROCESSAR FUNCAO DE CALCULO DO DIGITO VERIFICADOR';
    end if;

    select k15_codbco,
            k15_codage
        into v_codbco,
            v_codage
        from cadvencdescban
            inner join cadban on cadvencdescban.q93_cadban = cadban.k15_codigo
    where q93_codigo = v_record_cadcalc.codven;

    if v_codbco is null then
        v_codbco = 0;
    end if;

    if v_codage is null then
        v_codage = 0;
    end if;

    select q02_numcgm
        into v_numcgm
        from issbase
        where q02_inscr = iInscr;
    if v_numcgm is null then
        return '27-CGM DA INSCRICAO : '||iInscr||' NAO ENCONTRADO DO CADASTRO DO CGM';
    end if;

    perform fc_debug('Inicio : '||v_record_cadcalc.inicio||' Anousu : '||iAnousu,lRaise,false,false);

    if iAnousu = to_number(substr(v_record_cadcalc.inicio,1,4),'99999') then

        v_manual = v_manual || 'Ano atual igual ao ano de inicio da atividade: ' || iAnousu || '\n';

        perform fc_debug('Ano atual igual ao ano de inicio',lRaise,false,false);

        select recexe
        into v_receita
        from tudo
        where tudo.seq = v_record_cadcalc.seq;
    else

        v_manual = v_manual||'Ano atual diferente do ano de inicio da atividade: ' || iAnousu || '\n';
        perform fc_debug('Ano atual diferente ao ano de inicio',lRaise,false,false);
        select recpro
        into v_receita
        from tudo
        where tudo.seq = v_record_cadcalc.seq;

    end if;

    v_prim = false;

    /**
        * Se for variavel
        */
    if v_record_cadcalc.var = '1' then
        v_manual = v_manual || 'variavel\n';

        perform fc_debug('Calculando variavel -- vencimento : '||v_record_cadcalc.codven,lRaise,false,false);

        -- se for baixa
        perform fc_debug('',lRaise,false,false);
        perform fc_debug('COMECANDO A PROCESSAR OS VENCIMENTOS (PELO CADASTRO DE VENCIMENTOS CADVENC) ',lRaise,false,false);
        perform fc_debug('',lRaise,false,false);

        /* este for calcula pelo cadvenc todas as parcelas pelo select do porcalculo(todos os calculos da inscricao) */
        for v_record_cadvenc in select * from cadvenc
                                        inner join cadvencdesc on q82_codigo = q92_codigo
                                    where cadvenc.q82_codigo = v_record_cadcalc.codven
                                    order by q82_parc
        loop
            IF extract(year from dInicioAtividade)::integer = iAnousu THEN
                IF v_record_cadvenc.q82_parc < extract(month from dInicioAtividade)::integer THEN
                    perform fc_debug('No existia atividade no mÔøΩs '||v_record_cadvenc.q82_parc||' do ano de '||iAnousu,lRaise,false,false);
                    perform fc_debug('PULANDO ESTE MÔøΩS',lRaise,false,false);
                    perform fc_debug(' ',lRaise,false,false);

                    CONTINUE WHEN 1 = 1;
                END IF;
            END IF;
            
            perform fc_debug('Processando vencimentos ',lRaise,false,false);
            perform fc_debug('PARCELA : '||v_record_cadvenc.q82_parc||' VENCIMENTO : '||v_record_cadvenc.q82_venc,lRaise,false,false);
            perform fc_debug('1 -- Deletando do arrecad numpre : '||v_numpre||' numpar : '||v_record_cadvenc.q82_parc,lRaise,false,false);

            delete from arrecad
                where k00_numpre = v_numpre
                and k00_numpar = v_record_cadvenc.q82_parc;

            -- if to_number(substr(v_record_cadvenc.q82_venc,1,4)||substr(v_record_cadvenc.q82_venc,6,2),'999999') >
                -- to_number(substr(v_record_cadcalc.inicio,1,4)||substr(v_record_cadcalc.inicio,6,2),'999999') then

                v_manual = v_manual || 'vencimento do cadastro de vencimentos: ' || v_record_cadvenc.q82_venc || ' maior ou igual a data de inicio da atividade mais 1 mes\n';

                select count(*)
                into iAux
                from arreinscr
                where k00_numpre = v_numpre
                    and k00_inscr = iInscr;

                if iAux = 0 then

                v_manual = v_manual || 'inserindo numpre no arreinscr: ' || v_numpre || '\n';
                insert into arreinscr values (v_numpre,iInscr);
                end if;

                /* se nao for primeiro calculo do exercicio */
                if v_prim is false then

                if v_comcalculo is true then

                    perform fc_debug('Deletando do isscalc numpre : '||v_numpre,lRaise,false,false);
                    v_manual = v_manual || 'deletando numpre do isscalc e arrecad: ' || v_numpre || '\n';
                    delete from isscalc where q01_numpre = v_numpre;

                end if;

                insert into isscalc values (iAnousu,iInscr,v_record_cadcalc.cadcalc,v_receita,v_numpre,v_record_cadcalc.valori / v_valinflator);
                insert into numpres values (v_numpre);
                v_prim = true;

                end if;

                v_valor         := v_record_cadcalc.valori / v_valinflator;
                v_valorgrav     := 0;
                v_descrvariavel := 'arrecadacao de issqn variavel nao fixado';
                v_manual        := v_manual || 'valor: ' || v_valor || '\n';

                /* verifica se tem valor fixado lancado */
                select q34_valor
                into v_valorgrav
                from varfix
                        inner join varfixval on varfix.q33_codigo = varfixval.q34_codigo
                where varfix.q33_inscr    = iInscr
                    and varfixval.q34_numpar = v_record_cadvenc.q82_parc;

                if v_valorgrav is not null then

                v_descrvariavel = 'arrecadacao de issqn variavel fixado';
                bTemFixado = true;
                else

                v_valorgrav     = 0;
                v_descrvariavel = 'arrecadacao de issqn variavel nao fixado';
                end if;

                perform fc_debug('Tipo de valor : '||v_descrvariavel ,lRaise,false,false);

                v_numpar = v_record_cadvenc.q82_parc;
                v_manual = v_manual || 'parcela: ' || v_numpar || '\n';

                select q05_codigo
                into iAux
                from issvar
                        inner join arreinscr on q05_numpre = arreinscr.k00_numpre
                where q05_ano   = iAnousu
                    and q05_mes   = v_numpar
                    and k00_inscr = iInscr
                    and q05_valor > 0;

                v_vencvar = v_record_cadvenc.q82_venc;

                --Se For optante pelo simples, altera o dia do vencimento para dia 20
                if dDataInicialCadSimples < v_vencvar  and lOptanteSimples ='t' then
                v_vencvar = to_char(v_vencvar, 'YYYY-MM')||'-20';
                end if;

                perform fc_debug('Vencimento variavel : '||v_vencvar,lRaise,false,false);


                select q05_numpre
                from issvar
                into v_numprejapago
                        inner join arrecant on q05_numpre = arrecant.k00_numpre and q05_numpar = arrecant.k00_numpar
                        inner join arreinscr on arrecant.k00_numpre = arreinscr.k00_numpre
                where q05_ano   = iAnousu
                and q05_mes   = v_numpar
                and k00_inscr = iInscr;

                perform fc_debug('Numpre ja pago '||v_numprejapago||' Numpar : '||v_numpar||' Anousu : '||iAnousu,lRaise,false,false);

                select q05_numpre
                into iNumpreArquivoSimples
                from issvar
                        inner join issarqsimplesregissvar on q68_issvar = q05_codigo
                        inner join arreinscr on q05_numpre = arreinscr.k00_numpre
                where q05_ano   = iAnousu
                and q05_mes   = v_numpar
                and k00_inscr = iInscr;

                perform fc_debug('Numpre no arquivo do simples '||iNumpreArquivoSimples||' Numpar : '||v_numpar||' Anousu : '||iAnousu,lRaise,false,false);

                if v_numprejapago is null and iNumpreArquivoSimples is null  then

                perform fc_debug('2 -- deletando do arrecad numpre : '||v_numpre||' numpar : '||v_numpar,lRaise,false,false);

                delete from arrecad where k00_numpre = v_numpre and k00_numpar = v_numpar ;

                perform * from fc_statusdebitos(v_numpre,v_numpar) where rtstatus = 'PAGO' or rtstatus = 'CANCELADO'  limit 1;
                if found then
                    continue;
                end if;

                perform * from issvardiv
                            inner join issvar    on q05_codigo = q19_issvar
                            inner join arreinscr on k00_numpre = q05_numpre
                    where issvar.q05_ano       = iAnousu
                        and issvar.q05_mes       = v_numpar
                        and arreinscr.k00_inscr  = iInscr ;

                if found then
                    return '28 - INSCRICAO COM CALCULO IMPORTADO PARA DIVIDA';
                end if;

                perform fc_debug('deletando do issvar Ano : '||iAnousu||' Mes : '||v_numpar||' Inscricao : '||iInscr,lRaise,false,false);
                perform *
                    from arreinscr
                        inner join issvar    on issvar.q05_numpre    = arreinscr.k00_numpre
                        inner join issvarlev on issvarlev.q18_codigo = issvar.q05_codigo
                    where issvar.q05_ano       = iAnousu
                    and issvar.q05_mes       = v_numpar
                    and arreinscr.k00_inscr  = iInscr ;
                if found then
                    return '28 - EMPRESA JA POSSUI LEVANTAMENTO FISCAL PARA COMPETENCIA : '||v_numpar||'/'||iAnousu ;
                end if;

                delete from issvarnotas
                using issvar
                where issvar.q05_codigo = issvarnotas.q06_codigo
                    and issvar.q05_ano    = iAnousu
                    and issvar.q05_mes    = v_numpar ;

                delete from issvar
                    using arreinscr
                    where issvar.q05_ano       = iAnousu
                    and issvar.q05_mes       = v_numpar
                    and arreinscr.k00_inscr  = iInscr
                    and arreinscr.k00_numpre = issvar.q05_numpre ;

                delete from informacaodebito
                    where informacaodebito.k163_numpre = v_numpre
                    and informacaodebito.k163_numpar = v_numpar;

                insert into issvar (q05_codigo, q05_numpre, q05_numpar, q05_valor, q05_ano, q05_mes, q05_histor, q05_aliq, q05_bruto)
                            values (nextval('issvar_q05_codigo_seq'), v_numpre, v_numpar, v_valorgrav, iAnousu, v_numpar, v_descrvariavel, v_valor, 0);

                perform fc_debug('INSERT NUMERO 1 NO ARRECAD (issvar)',lRaise,false,false);
                perform fc_debug('Numpre : '||v_numpre||' Numpar : '||v_numpar||' Valor : '||round(v_valorgrav,2)||' Vencimento : '||v_vencvar||' Tipo : '||v_record_cadvenc.q92_tipo,lRaise,false,false);

                if dDtbaixa is not null then
                   v_numpar = 1;
                   v_numtot = 1;
                end if;

                insert into arrecad (k00_numcgm, k00_dtoper, k00_receit, k00_hist,                  k00_valor,            k00_dtvenc, k00_numpre, k00_numpar, k00_numtot, k00_numdig, k00_tipo)
                        values      (v_numcgm,   v_vencvar,  v_receita,  v_record_cadvenc.q82_hist, round(v_valorgrav,2),  v_vencvar, v_numpre,   v_numpar,   v_numtot,   v_numdig,v_record_cadvenc.q92_tipo);

                if v_codbco != 0 and v_comcalculo is false then

                    select fc_numbco(v_codbco,v_codage) into v_numbco;
                    /*
                        Comentado insert na arrebanco conforme solicitado pelo Paulo e Dalpozzo, pois nao faz
                        o menor sentido gerar arrebanco no calculo de issqn / alvara
                    */
                    --insert into arrebanco values (v_numpre,v_numpar,v_codbco,v_codage,v_numbco,'');

                end if;

                else
                perform fc_debug('Ja pago',lRaise,false,false);
                end if;

                v_numpar = v_numpar + 1;

            -- end if;
        end loop;

        perform fc_debug('FIM DO PROCESSAMENTO DO VARIAVEL',lRaise,false,false);

    else  -- SE NAO FOR VARIAVEL

        perform fc_debug('',lRaise,false,false);
        perform fc_debug('--------------------- P R O C E S S A N D O   (  N A O  )  V A R I A V E L  ---------------------',lRaise,false,false);
        perform fc_debug('vencimento: '||v_record_cadcalc.codven,lRaise,false,false);
        perform fc_debug('',lRaise,false,false);

        v_manual = v_manual || 'processando com base no cadastro de vencimentos\n';

        lJaPassouUltVenc   = false;
        v_jagravou         = false;
        iQtdeVencProcessar = 0;

        -- busca quantas parcelas vai calcular e registrar no arrecad (variavel iQtdeVencProcessar)
        select count(*)
        into v_totvenc
        from cadvencdesc
                inner join cadvenc on q82_codigo = q92_codigo
        where cadvencdesc.q92_codigo = v_record_cadcalc.codven ;

        select count(*)
        into iQtdeVencProcessar
        from cadvencdesc
                inner join cadvenc on q82_codigo = q92_codigo
        where cadvencdesc.q92_codigo = v_record_cadcalc.codven
            and ( case
                    when cadvencdesc.q92_formacalcparcvenc = 1
                    then case when q82_venc >= dInicioAtividade then true else false end
                    when cadvencdesc.q92_formacalcparcvenc = 3
                    then q82_venc >= dDatahj and q82_venc >= dInicioAtividade
                    else
                    case
                        when cadvenc.q82_calculaparcvenc is true
                        then true
                        else
                        q82_venc >= dDatahj and q82_venc >= dInicioAtividade
                    end
                end );
            -- esse case tem a finalidade de processar ou nao parcelas
            -- vencidas de acordo com os parametros do cadastro de
            -- vencimentos(cadvencdescm,cadvenc)
        perform fc_debug('Pesquisando quantidade de vencimentos no periodo de atividade',lRaise,false,false);
        perform fc_debug('Data inicial : '||coalesce(dInicioAtividade,'2999-01-01'::date)    ,lRaise,false,false);
        perform fc_debug('Data final   : '||iUltimoDiaAno ,lRaise,false,false);
        perform fc_debug('Quantidade de vencimentos encontrada : '||iQtdeVencProcessar||''    ,lRaise,false,false);

        -- utiliza a variavel iTotalVencimentosCad, que e o total de vencimentos para mais abaixo saber em que parcela jogar os centavos de diferenca de arredondamento
        -- utiliza a variavel dMaiorVencimentoCadvenc para no caso da empresa iniciar depois do ultimo vencimento, calcular tudo em uma parcela e jogar no vencimento 31-12
        select max(cadvenc.q82_venc), coalesce(max(cadvencdesc.q92_diasvcto),0), count(*)
        into dMaiorVencimentoCadvenc, iDiasParaVencimento, iTotalVencimentosCad
        from cadvencdesc
                inner join cadvenc on q82_codigo = q92_codigo
        where cadvencdesc.q92_codigo = v_record_cadcalc.codven;

        -- passando conteudo da variavel do cissqn para cadvencdesc
        iDiasvctoCissqn := iDiasParaVencimento;

        perform fc_debug('Quantidade de vencimentos a processar : '||iQtdeVencProcessar,  lRaise,false,false);
        perform fc_debug('Dias para o vencimento(q92_diasvcto)  : '||iDiasParaVencimento, lRaise,false,false);
        perform fc_debug('Maior vencimento do cadastro          : '||dMaiorVencimentoCadvenc||' Data atual : '||dDatahj,lRaise,false,false);

        select count(*), sum(k00_valor)
        into iQtdParcelasPagas, nValorTotalPago
        from ( select k00_numpre, k00_numpar, sum(k00_valor) as k00_valor
                    from arrecant
                where k00_numpre = v_numpre
                group by k00_numpre, k00_numpar ) as x;

        perform fc_debug('Quantidade de parcelas pagas : '||coalesce(iQtdParcelasPagas,0)||' Valor total pago : '||coalesce(nValorTotalPago,0),lRaise,false,false);

        begin

        select  case when (dDtBaixa is not null) and (v_cadcalc_fix is true) then
                    1
                when count(*) = 1 then
                    1
                else
                    ( count(*) - iQtdParcelasPagas )
                end as qtdparcelas,

                case when (dDtBaixa is not null) and (v_cadcalc_fix is true) then
                     100
                when count(*) = 1 then
                    100
                else
                    ( 100 / ( count(*) - iQtdParcelasPagas ) )
                end as percpago

            into iQtdParcelas, nPercPagoNovo
            from cadvencdesc
                left outer join cadvenc on q82_codigo = q92_codigo
            where cadvencdesc.q92_codigo = v_record_cadcalc.codven
            and ( case
                    when cadvencdesc.q92_formacalcparcvenc = 1
                    then case when q82_venc >= dInicioAtividade then true else false end
                    when cadvencdesc.q92_formacalcparcvenc = 3

                    then q82_venc >= dDatahj and q82_venc >= dInicioAtividade
                    else
                    case
                        when cadvenc.q82_calculaparcvenc is true
                        then true
                        else
                        q82_venc >= dDatahj and q82_venc >= dInicioAtividade
                    end
                end ) ;

        exception

            when division_by_zero then
            nPercPagoNovo := 0;

        end;

        if iQtdParcelasPagas = 0 then
        nPercPagoNovo := 0;
        end if;

        /*
        * Quando a quantidade total de parcelas for negativo, significa que  o calculo n√£o possui mais
        * parcelas em aberto, todas est√£o abertas. logo, o total de parcelas devera ser 1
        */
        if iQtdParcelas < 0 then
        iQtdParcelas = 1;
        end if;

        if nPercPagoNovo < 0 then
        nPercPagoNovo = 100;
        end if;

        nDescontoPagamentoParcela := coalesce( ( nValorTotalPago / iQtdParcelas ) ,0);

        perform fc_debug(' PROCURANDO PAGAMENTOS : ',lRaise,false,false);
        perform fc_debug('--------------------------------------------------------------------------------------------',lRaise,false,false);
        perform fc_debug(' TOTAL PAGO           : '||coalesce( nValorTotalPago, 0)           ,lRaise,false,false);
        perform fc_debug(' PARCELAS PAGAS       : '||coalesce( iQtdParcelasPagas, 0)         ,lRaise,false,false);
        perform fc_debug(' PARCELAS A CALCULAR  : '||coalesce( iQtdParcelas, 0)              ,lRaise,false,false);
        perform fc_debug(' DESCONTO POR PARCELA : '||coalesce( nDescontoPagamentoParcela, 0) ,lRaise,false,false);
        perform fc_debug(' PERCENTUAL NOVO      : '||coalesce( nPercPagoNovo, 0)             ,lRaise,false,false);
        perform fc_debug('--------------------------------------------------------------------------------------------',lRaise,false,false);

        -- sempre deve deletar o calculo anterior no caso de encontrar um
        perform fc_debug('DELETANDO DO ARRECAD NUMPRE : '||v_numpre,lRaise,false,false);
        delete from arrecad where k00_numpre = v_numpre;

        v_vencproc := 0;

        -- Loop que gera/processa financeiro
        for v_record_cadvenc in select *
                                from cadvencdesc
                                        left join cadvenc on q82_codigo = q92_codigo
                                where cadvencdesc.q92_codigo = v_record_cadcalc.codven
                            order by q82_parc
        loop

        perform fc_debug('------------------------------------------------------------------------------------------',lRaise,false,false);
        perform fc_debug('Processando Vencimento : '||v_record_cadvenc.q82_venc||' Parcela : '||v_record_cadvenc.q82_parc||' Inicio : '||v_record_cadcalc.inicio,lRaise,false,false);
        perform fc_debug('------------------------------------------------------------------------------------------',lRaise,false,false);

        /**
            * Guardar o vencimento atual do cadvenc
            */
        dVencimentoAtual := v_record_cadvenc.q82_venc;

        if dVencimentoAtual is null then
            dVencimentoAtual := dDatahj;
        end if;


        /**
            * Se a quantidade de vencimentos a processar for igual a zero
            */
        if iQtdeVencProcessar = 0 then

            -- soma dias para o vencimento na data do ultimo vencimento
            if iDiasvctoCissqn > 0 then

            dVencimentoAtual := ( dDatahj + iDiasvctoCissqn )::date;
            perform fc_debug('Trocou o vencimento para : '||dVencimentoAtual,lRaise,false,false);

            if
              extract(month from dDatahj) = 12 and
              extract(year from dDatahj) != extract(year from dVencimentoAtual)
            then
                -- 31 de Dezembro do ano atual
                perform fc_debug('Vencimento ' || dVencimentoAtual ||' ultrapassa limite atual',lRaise,false,false);
                dVencimentoAtual = extract(year from dDatahj) || '-12-31';
                perform fc_debug('Trocou o vencimento para : '||dVencimentoAtual,lRaise,false,false);
            end if;

            else

            -- se mesmo somando os dias para vencimento o debito continuar vencido joga para 31/12
            dVencimentoAtual := cast(to_char(iAnousu,'9999')||'-12-31' as date);

            perform fc_debug('Trocou o vencimento para ultimo dia do ano : '||dVencimentoAtual,lRaise,false,false);
            end if;

        end if;

        perform fc_debug('Vencimento apos processamento das regras : '||dVencimentoAtual,lRaise,false,false);

        /**
            * Variavel para controlar a forma para calculo de parcelas vencidas
            */
        lProcessaParcVencidas := ( case
                                        when v_record_cadvenc.q92_formacalcparcvenc = 1
                                        then true
                                        when v_record_cadvenc.q92_formacalcparcvenc = 3
                                        then v_record_cadvenc.q82_venc between dDatahj and iUltimoDiaAno
                                        else
                                        case
                                            when v_record_cadvenc.q82_calculaparcvenc is true
                                            then true
                                            else
                                            v_record_cadvenc.q82_venc between dDatahj and iUltimoDiaAno
                                        end
                                    end );

        perform fc_debug('Processando parcelas vencidas(lProcessaParcVencidas) ? '||(case when lProcessaParcVencidas is true then 'SIM' else 'NAO' end),lRaise,false,false);

        v_vencproc       = v_vencproc + 1;
        lProcessaParcela = false;

        perform fc_debug('Vencimento do cadvenc : '||dVencimentoAtual||' Maior Vencimento : '||dMaiorVencimentoCadvenc,lRaise,false,false);

        -- se vencimento do cadvenc do registro atual do for maior que o maximo vencimento do cadvenc
        if v_record_cadvenc.q82_venc > dMaiorVencimentoCadvenc then

            perform fc_debug('',lRaise,false,false);
            perform fc_debug('Vencimento do cadastro de vencimentos maior que o maximo vencimento, pasando lJaPassouUltVenc para true',lRaise,false,false);
            perform fc_debug('',lRaise,false,false);
            lJaPassouUltVenc = true;
        end if;

        perform fc_debug('Proximo vencimento : '||v_vencproc||'  Total de vencimentos : '||iTotalVencimentosCad||' Passa: '||( case when lProcessaParcela is true then 'true' else 'false' end ),lRaise,false,false);

        if (dDtbaixa is null) then 
           perform fc_debug('Data da baixa em branco',lRaise,false,false);
        end if;

        lProcessaParcela = true;

        if extract( year from v_record_cadcalc.inicio) <> iAnousu then

            perform fc_debug('Ano de inicio diferente do atual ',lRaise,false,false);
            lProcessaParcela = true;
            v_numtot         = v_totvenc;

        else

            perform fc_debug('Ano de inicio igual do atual ',lRaise,false,false);
            if v_record_cadvenc.q82_venc >= dInicioAtividade or dVencimentoAtual is null or iQtdeVencProcessar = 0 or lProcessaParcVencidas is true then

            perform fc_debug('Vencimento: '||dVencimentoAtual||' maior ou igual a inicio: '||dInicioAtividade||' ou vencimento is null ',lRaise,false,false);
            lProcessaParcela = true;

            else

            perform fc_debug('Passando lProcessaParcela para FALSE -- Vencimento: '||dVencimentoAtual||' menor que inicio: '||dInicioAtividade,lRaise,false,false);
            lProcessaParcela = false;

            end if;
            v_numtot = iQtdeVencProcessar;

        end if;

        if dDtbaixa is not null and dVencimentoAtual > dDtbaixa then

            perform fc_debug('Data de baixa : '||dDtbaixa,lRaise,false,false);
            -- perform fc_debug('1 - Passando lProcessaParcela para false',lRaise,false,false);
            -- lProcessaParcela = false;
        end if;

        if dDatahj > dVencimentoAtual and lProcessaParcVencidas is false then

            perform fc_debug('Inicio maior que data de vencimento e processar parcelas vencidas NAO',lRaise,false,false);
            lProcessaParcela = false;
        end if;

        perform fc_debug('----------------------------------------------------------------------------------------------',lRaise,false,false);
        perform fc_debug('Total de vencimentos     : '||iTotalVencimentosCad ,lRaise,false,false);
        perform fc_debug('Vecimentos do cadastro   : '||v_vencproc,lRaise,false,false);
        perform fc_debug('Qtd parcelas a calcular  : '||v_totvenc,lRaise,false,false);
        perform fc_debug('Ja passou ult vencimento : '||(case when lJaPassouUltVenc is true then 'SIM' else 'NAO' end),lRaise,false,false);
        perform fc_debug('Inicio                   : '||v_record_cadcalc.inicio,lRaise,false,false);
        perform fc_debug('Vencimento do Cadastro   : '||dVencimentoAtual,lRaise,false,false);
        perform fc_debug('----------------------------------------------------------------------------------------------',lRaise,false,false);

        if    v_record_cadcalc.inicio > dVencimentoAtual
            and iTotalVencimentosCad <> v_vencproc
            and lJaPassouUltVenc is false                 then

            perform fc_debug('Passando lProcessaParcela para false',lRaise,false,false);
            lProcessaParcela = false;
        end if;

        perform fc_debug('Passando para o proximo vencimento(lProcessaParcela) ? '||(case when lProcessaParcela is true then 'True' else 'False' end),lRaise,false,false);

        if lProcessaParcela is true then

            -- DESCOBRIR EM QUE CASO E UTILIZADO
            --
            -- Desabilitado o if abaixo pois √© justamente ele que impede o calculo para anos posteriores,
            v_venc = dVencimentoAtual;

            perform fc_debug('Quantidade de vencimentos(iQtdeVencProcessar) a processar : '||iQtdeVencProcessar,lRaise,false,false);
            if iQtdeVencProcessar = 0 then
            nPercentualParcela = 100;
            else

            -- Verificacao do valor total proporcional
            if v_record_cadcalc.tipopro = 'D' then

                v_venc     = v_record_cadvenc.q82_venc;

                v_venccalc = v_venc;
                if lJaPassouUltVenc is true or v_record_cadvenc.q82_venc = dMaiorVencimentoCadvenc then

                v_venccalc = to_char(iAnousu,'9999') || '-12-31';
                dMaiorVencimentoCadvenc  = v_venccalc;

                end if;
                v_anoiniciocalc := extract(year from v_record_cadcalc.inicio);
                if v_anoiniciocalc < iAnousu then
                dInicioAtividadecalc = to_char(iAnousu,'9999') || '-01-01';
                else
                dInicioAtividadecalc = v_record_cadcalc.inicio;
                end if;

                perform fc_debug('Total de vencimentos : '||dMaiorVencimentoCadvenc||' Inicio: '||dInicioAtividadecalc,lRaise,false,false);
                v_diasdesdeinicio = (iUltimoDiaAno - dInicioAtividadecalc)::integer + 1;
                perform fc_debug('Vencimento calculo : '||v_venccalc||' Inicio: '||dInicioAtividadecalc,lRaise,false,false);
                v_diasdestevcto = ((v_venccalc - dInicioAtividadecalc)::integer + 1) - v_diasjasomados;

                if bGeral is true or dDtbaixa is not null then
                nPercentualParcela = 100::float8 / iQtdeVencProcessar::float8;
                   if (dDtbaixa is not null) then
                      nPercentualParcela = 100::float8;
                   end if;
                else
                nPercentualParcela = (100::float8 / v_diasdesdeinicio::float8)::float8 * v_diasdestevcto::float8;
                v_diasjasomados = v_diasjasomados + v_diasdestevcto;
                end if;

            else

                nPercentualParcela = 100::float8 / iQtdParcelas::float8;

            end if;
            end if;

            perform fc_debug('Vencimento : '||v_venc||' Dias de vencimento : '||iDiasvctoCissqn,lRaise,false,false);

            if v_venc is null and iAnousu < v_anoatualservidor then

            v_venc = to_char(iAnousu,'9999')||'-12-31';

            --- verificado o vcto do alvara quando calculado, data calc. + 30 dias
            if iDiasvctoCissqn > 0 and iAnousu = v_anoatualservidor then

                select dDatahj + iDiasvctoCissqn
                into v_venc;


                v_manual = v_manual || 'alterado vencimento para: '||v_venc||'\n';

            end if;
            end if;

            if ((dDtBaixa is not null) and (v_cadcalc_fix is true)) then
    
               select dDatahj + iDiasvctoCissqn
               into v_venc;

               perform fc_debug('Alterado vencimento por ser baixa de ISSQN Fixo para (dDatahj + iDiasvctoCissqn): '||v_venc,lRaise,false,false);

            end if;

            perform fc_debug('Percentual : '||nPercentualParcela,lRaise,false,false);
            perform fc_debug('Vencimento : '||v_venc,lRaise,false,false);

            if v_prim is false then
            if v_comcalculo is false then
                insert into arreinscr values (v_numpre,iInscr);
            else
                perform fc_debug('deletando do isscalc o numpre : '||v_numpre,lRaise,false,false);
                v_manual = v_manual || 'deletando numpre do isscalc e arrecad: ' || v_numpre || '\n';
                delete from isscalc where q01_numpre = v_numpre;
            end if;

            insert into isscalc values (iAnousu, iInscr, v_record_cadcalc.cadcalc, v_receita, v_numpre, v_record_cadcalc.valor);
            insert into numpres values ( v_numpre );
            v_prim = true;

            end if;

            /**
            * Valida quando executa calculo geral
            */
            if bGeral is true then

            /**
                * Subtraido valor que ja foi pago, caso contrario subtraira 0
                */
            nValorParcela = round( ( (v_record_cadcalc.valorintegr) * nPercentualParcela / 100) - coalesce(nDescontoPagamentoParcela, 0), 2);

            perform fc_debug('(1) Valor Parcela : '||nValorParcela,lRaise,false,false);
            perform fc_debug('Valor Total       : '||v_record_cadcalc.valorintegr||' Valor Parcial(parcela) : '||nValorParcela,lRaise,false,false);

            /**
                * Ultima Parcela
                */
            if v_numpar = v_numtot then

                /**
                * Arredonda os centavos e joga na ultima
                */
                perform fc_debug('',lRaise,false,false);
                perform fc_debug('(4) Valor Parcela : '||nValorParcela,lRaise,false,false);
                nValorParcela = nValorParcela + ((v_record_cadcalc.valorintegr) - (nValorParcela * v_numtot));
                perform fc_debug('(5) Valor Parcela : '||nValorParcela,lRaise,false,false);
            end if;

            perform fc_debug('100 -- Delete from arrecad numpre : '||v_numpre||' numpar : '||v_record_cadvenc.q82_parc,lRaise,false,false);

            -- se a parcela esta paga ou cancelada passa para a proxima
            perform * from fc_statusdebitos(v_numpre, v_record_cadvenc.q82_parc)
                where rtstatus = 'PAGO'
                    or rtstatus = 'CANCELADO' limit 1;
            if found then

                perform fc_debug('1 -- PARCELA '||v_record_cadvenc.q82_parc||' ESTA PAGA OU CANCELADA ',lRaise,false,false);
                continue;
            end if;

            nValorParcela := round(nValorParcela,2);
            perform fc_debug('INSERT NUMERO 2 NO ARRECAD',lRaise,false,false);
            perform fc_debug('Numpre : '||v_numpre||' Numpar : '||v_numpar||' Valor : '||round(nValorParcela,2)||' Vencimento : '||v_venc||' Tipo : '||v_record_cadvenc.q92_tipo,lRaise,false,false);

            if nValorParcela > 0 then

                /**
                * Alterado para inserir o q82_parc ao inves do numpar pois a pl nao levava em consideracao parcelas
                * pagas e ou canceladas e assim gerando inconsistencia com numpar pago/cancelado e em aberto ao mesmo tempo
                */
                insert into arrecad (k00_numcgm, k00_dtoper, k00_receit, k00_hist,                  k00_valor,              k00_dtvenc, k00_numpre, k00_numpar,                k00_numtot, k00_numdig, k00_tipo)
                             values (v_numcgm,   v_dtbase,   v_receita,  v_record_cadvenc.q92_hist, round(nValorParcela,2), v_venc,     v_numpre,   v_record_cadvenc.q82_parc, v_numtot,   v_numdig,    v_record_cadvenc.q92_tipo);

            end if;

            else

            -- se a parcela esta paga ou cancelada passa para a proxima
            perform * from fc_statusdebitos(v_numpre, v_record_cadvenc.q82_parc)
                where rtstatus = 'PAGO'
                    or rtstatus = 'CANCELADO' limit 1;

            if found then

                perform fc_debug('2 -- PARCELA '||v_record_cadvenc.q82_parc||' ESTA PAGA OU CANCELADA ',lRaise,false,false);
                v_numpar = v_numpar + 1;
                continue;

                /**
                * Replicada mesma logica descrita acima quando o calculo eh geral
                */
            else
                v_numpar = v_record_cadvenc.q82_parc;
            end if;

            if v_numpar is null then
                v_numpar := 1;
            end if;

            v_dtvencano   = v_venc;
            nValorParcela = round( ( ( v_record_cadcalc.valorintegr * nPercentualParcela ) / 100 ) - nDescontoPagamentoParcela,2 );

            v_manual = v_manual || 'valor da parcela: ' || nValorParcela || '\n';

            perform fc_debug('Valor integral        : '||v_record_cadcalc.valorintegr||' Percentual : '||nPercentualParcela,lRaise,false,false);
            perform fc_debug('Valor da parcela      : '||nValorParcela,lRaise,false,false);
            perform fc_debug('Percentual da parcela : '||nPercentualParcela,lRaise,false,false);
            perform fc_debug('Desconto da parcela   : '||nDescontoPagamentoParcela,lRaise,false,false);

            perform fc_debug('300 -- Delete from arrecad numpre : '||v_numpre||' numpar : '||v_numpar,lRaise,false,false);

            if nValorParcela > 0 then

                perform fc_debug('INSERT NUMERO 3 NO ARRECAD',lRaise,false,false);
                perform fc_debug('Numpre : '||v_numpre||' Numpar : '||v_numpar||' Valor : '||round(nValorParcela,2)||' - Vencimento : '||v_dtvencano||' Tipo : '||v_record_cadvenc.q92_tipo,lRaise,false,false);

                if v_record_cadcalc.cadcalc = 1 then
                    -- Alvara
                    iNumeroParcelas = iNumeroParcelasAlvara;
                elsif v_record_cadcalc.cadcalc = 2 then
                    -- ISSQN
                    if dDtbaixa is not null then 
                       iNumeroParcelas = 1;
                    else 
                       iNumeroParcelas = iNumeroParcelasIssqn;
                    end if;
                end if;

                /**
                * Parcela o c√°lculo, seguindo o principio:
                * -- Dividir o Valor Pela Quantidade
                * -- Criar parcelas sempre com base de vencimento
                * -- Gravar nova estrutura de d√©bitos no arrecad
                **/
                if iNumeroParcelas > 1 or (v_record_cadcalc.cadcalc = 2 and iNumeroParcelas > 0) then

                    perform fc_debug('+----------------------------------------------',lRaise,false,false);
                    perform fc_debug('| Divisao da Parcela em - '|| iNumeroParcelas ||' - partes ',lRaise,false,false);
                    perform fc_debug('+--------------------------------------------',lRaise,false,false);

                    -- Se ao calcular o issqn a data de vencimento da √∫ltima parcela
                    -- extrapolar o ano atual, n√£o √© poss√≠vel parcelar em tantas vezes
                    if
                        v_record_cadcalc.cadcalc = 2 and
                        iNumeroParcelas > 1 and
                        extract(year from dDatahj + iDiasvctoCissqn + (interval '1' month * (iNumeroParcelas - 1))) != extract(year from dDatahj)
                    then
                        return '29 - Uma ou mais parcelas venceriam em um ano posterior ao atual. Diminua a quantidade de parcelas.';
                    end if;

                    for rParcelas
                    in select numero_parcela,
                            valor_parcela,
                            data_vencimento
                        from fc_issqn_divide_valores(round(nValorParcela,2), iNumeroParcelas,dInicioAtividade )
                    loop
                        -- ---------------------------- --
                        --  Inserindo Dados no Arrecad  --
                        -- ---------------------------- --
                        perform fc_debug('| Parcela           : '|| rParcelas.numero_parcela ,lRaise,false,false);
                        perform fc_debug('| ValorParcela      : '|| rParcelas.valor_parcela  ,lRaise,false,false);
                        perform fc_debug('| VencimentoParcela : '|| rParcelas.data_vencimento,lRaise,false,false);

                        if v_record_cadcalc.cadcalc = 2 and iNumeroParcelas > 0 and rParcelas.data_vencimento < dDatahj then
                            rParcelas.data_vencimento = dDatahj + iDiasvctoCissqn + (interval '1' month * (rParcelas.numero_parcela - 1));
                        end if;

                        -- Se estivermos calculando o ISSQN e s√≥ estivermos parcelando uma vez
                        -- e estamos em dezembro e a data de vencimento ficar pro ano que vem,
                        -- ajustamos ela pro √∫ltimo dia desse ano (31/12)
                        if
                            v_record_cadcalc.cadcalc = 2 and
                            iNumeroParcelas = 1 and
                            extract(month from dDatahj) = 12 and
                            extract(year from dDatahj) != extract(year from rParcelas.data_vencimento)
                        then
                            -- 31 de Dezembro do ano atual
                            rParcelas.data_vencimento = extract(year from dDatahj) || '-12-31';
                        end if;

                        if dDtbaixa is not null then 
                           perform fc_debug('GERANDO NOVO NUMPRE PARA BAIXA',lRaise,false,false);
                           select nextval('numpref_k03_numpre_seq')
                           into v_numpre;
                           insert into arreinscr values (v_numpre,iInscr);
                        end if;

                        perform fc_debug('INSERT NUMERO 3 (A) NO ARRECAD',lRaise,false,false);
                        perform fc_debug('Numpre : '||v_numpre||' Numpar : '||v_numpar||' Valor : '||round(nValorParcela,2)||' - Vencimento : '||v_dtvencano||' Tipo : '||v_record_cadvenc.q92_tipo,lRaise,false,false);

                        insert into arrecad
                               (k00_numcgm,
                                k00_dtoper,
                                k00_receit,
                                k00_hist,
                                k00_valor,
                                k00_dtvenc,
                                k00_numpre,
                                k00_numpar,
                                k00_numtot,
                                k00_numdig,
                                k00_tipo)
                        values (v_numcgm,
                                v_dtbase,
                                v_receita,
                                v_record_cadvenc.
                                q92_hist,
                                rParcelas.valor_parcela,   -- VALOR ANTERIOR -> round(nValorParcela,2)
                                rParcelas.data_vencimento, -- VALOR ANTERIOR -> v_dtvencano,
                                v_numpre,
                                rParcelas.numero_parcela,  -- VALOR ANTERIOR -> v_numpar
                                iNumeroParcelas,           -- VALOR ANTERIOR -> v_numtot
                                v_numdig,
                                v_record_cadvenc.q92_tipo);

                        perform fc_debug('+--------------------------------------------',lRaise,false,false);
                    end loop;

                    -- Juntar as parcelas que sejam do mesmo m√™s em uma s√≥
                    for arrecadAuxiliar in
                        select
                            k00_numpre,
                            k00_numpar,
                            k00_numcgm,
                            k00_dtoper,
                            k00_receit,
                            k00_hist,
                            sum(k00_valor) as k00_valor,
                            min(k00_dtvenc) as k00_dtvenc,
                            k00_numtot,
                            k00_numdig,
                            k00_tipo,
                            k00_tipojm
                        from
                            arrecad
                        where
                            k00_numpre = v_numpre
                          and k00_dtvenc > dDatahj 
                        group by
                            k00_numpre,
                            k00_numpar,
                            k00_numcgm,
                            k00_dtoper,
                            k00_receit,
                            k00_hist,
--                            k00_dtvenc,
                            k00_numtot,
                            k00_numdig,
                            k00_tipo,
                            k00_tipojm
                    loop
                        delete from arrecad where arrecad.k00_numpre = arrecadAuxiliar.k00_numpre and arrecad.k00_numpar = arrecadAuxiliar.k00_numpar;
                        perform fc_debug('INSERT NUMERO 3 (B) NO ARRECAD',lRaise,false,false);
                        perform fc_debug('Numpre : '||arrecadAuxiliar.k00_numpre||' Numpar : '||arrecadAuxiliar.k00_numpar||' Valor : '||arrecadAuxiliar.k00_valor||' - Vencimento : '||arrecadAuxiliar.k00_dtvenc||' Tipo : '||arrecadAuxiliar.k00_tipo,lRaise,false,false);

                        insert into arrecad values (
                            arrecadAuxiliar.k00_numpre,
                            arrecadAuxiliar.k00_numpar,
                            arrecadAuxiliar.k00_numcgm,
                            arrecadAuxiliar.k00_dtoper,
                            arrecadAuxiliar.k00_receit,
                            arrecadAuxiliar.k00_hist,
                            arrecadAuxiliar.k00_valor,
                            arrecadAuxiliar.k00_dtvenc,
                            arrecadAuxiliar.k00_numtot,
                            arrecadAuxiliar.k00_numdig,
                            arrecadAuxiliar.k00_tipo,
                            arrecadAuxiliar.k00_tipojm
                        );
                    end loop;

                else -- Fim da Validacao de parcelas e Tipo de Calculo, seguindo como era antes

                    iNumTot = v_numtot;
                    if v_numtot = 0 then
                        iNumTot = 1;
                    end if;

                    perform fc_debug('INSERT NUMERO 3 (C) NO ARRECAD',lRaise,false,false);
                    perform fc_debug('Numpre : '||v_numpre||' Numpar : '||v_numpar||' Valor : '||round(nValorParcela,2)||' - Vencimento : '||v_dtvencano||' Tipo : '||v_record_cadvenc.q92_tipo,lRaise,false,false);

                    insert into arrecad (k00_numcgm, k00_dtoper, k00_receit, k00_hist,                  k00_valor,              k00_dtvenc,  k00_numpre, k00_numpar, k00_numtot, k00_numdig, k00_tipo)
                                 values (v_numcgm,   dDatahj,   v_receita,  v_record_cadvenc.q92_hist, round(nValorParcela,2), v_dtvencano, v_numpre,   v_numpar,   iNumTot,    v_numdig,   v_record_cadvenc.q92_tipo);
                end if;
            end if;

            end if; -- final do bloco para bgeral is false
            perform fc_debug('Valor parcela : '||nValorParcela,lRaise,false,false);

            if v_comcalculo is false then
                select fc_numbco(v_codbco,v_codage) into v_numbco;
                /*
                    Comentado insert na arrebanco conforme solicitado pelo Paulo e Dalpozzo, pois nao faz
                    o menor sentido gerar arrebanco no calculo de issqn / alvara
                */
                --insert into arrebanco values (v_numpre,v_numpar,v_codbco,v_codage,v_numbco);
            end if;

            v_numpar = v_numpar + 1;
            v_jagravou = true;

            if nPercentualParcela = 100 then

            perform fc_debug('Saindo do for percentual = 100',lRaise,false,false);
            exit;
            end if;

        -- se lProcessaParcela is false
        else

            v_manual = v_manual || 'nao utilizando vencimento: ' || v_record_cadvenc.q82_venc || '\n';
            perform fc_debug('Nao processando financeiro lProcessaParcela is false',lRaise,false,false);

        end if;

        end loop;
    end if;

    end if;

end loop;


for v_record_cadcalc in select * from numpres loop

    select q01_manual
    into sManualText
    from isscalc
    where q01_inscr  = iInscr
        and q01_anousu = iAnousu
        and q01_manual is not null
    limit 1;

    v_manual := v_manual || 'atualizando log do calculo do numpre: ' || v_record_cadcalc.numpre || '\n';
    update isscalc
        set q01_manual = coalesce(sManualText,' ') || v_manual
    where q01_inscr = iInscr and q01_anousu = iAnousu;

end loop;

return '01-OK';

end;

$$ language 'plpgsql'; 

SQL1
    );
    }

    public function upFcVistoriasCharqueadasAlterada()
    {
    DB::statement(<<<SQL1

create or replace function fc_vistorias_charqueadas(integer)
returns varchar(200)
as $$
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
  v_q138_caracteristica  integer;
  v_q81_recexe           integer;
  v_q92_hist             integer;
  v_q81_valexe           float8;
  v_q92_tipo             integer;
  v_y71_inscr            integer;
  v_inscr_baixo_risco    integer;
  v_q02_numcgm           integer;
  v_ativ                 integer;
  v_anousu               integer;
  v_ativprinc            integer;
  v_diasvenc             integer;

  iFormaCalculoAtividade integer;
  icodvencimento         integer default 0;

  lCalculaVistoriaMei    boolean;
  lCalcProvisorio        boolean default false;
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
  calc_baixo_risco       boolean default true;
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
      return '21-erro ao selecionar origem dos dados (inscriÁ„o ou sanit·rio)!';
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
           lCalculaVistoriaMei,
           lCalcProvisorio
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

      select y71_inscr,q02_numcgm,y69_numpre,q138_caracteristica
        into v_y71_inscr,v_q02_numcgm,v_y69_numpre,V_Q138_CARACTERISTICA
        from vistinscr
             inner join issbase on q02_inscr = y71_inscr
             left outer join vistorianumpre on y69_codvist = v_vistoria
             left  join issbasecaracteristica on q02_inscr = q138_inscr
       where y71_codvist = v_vistoria;

      if v_y71_inscr is null then
        lVistoriaLocalizacao = false;
      else
        lVistoriaLocalizacao = true;
      end if;

      if  V_Q138_CARACTERISTICA = 33 then
        return 'INSCRI«√O COM CARACTERÕSTICA VISTORIA: ISENTO';
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

      if lCalcProvisorio is true then
        
          v_achou = false;

          if lraise is true then
            raise notice 'v_y74_codsani: %', v_y74_codsani;
            raise notice 'antes for...';
          end if;

          if lCalculaPorPorteAtividade then

            -- Pega o primeiro tipo de calculo encontrado na tabela de ligaÁ„o com o porte
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
              and saniatividade.y83_databx is null
              and date_part('year', saniatividade.y83_dtfim) >= date_part('year', CURRENT_DATE)
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
              and saniatividade.y83_databx is null
              and date_part('year', saniatividade.y83_dtfim) >= date_part('year', CURRENT_DATE)
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
                and saniatividade.y83_databx is null
                and date_part('year', saniatividade.y83_dtfim) >= date_part('year', CURRENT_DATE)
                and q81_tipo in ( select q81_codigo from w_origemdados )
                and y83_ativprinc is true;
            else

              select q80_ativ
                into v_ativprinc
                from saniatividade
                    inner join ativtipo on saniatividade.y83_ativ = ativtipo.q80_ativ
                    inner join tipcalc on tipcalc.q81_codigo = ativtipo.q80_tipcal
              where y83_codsani = v_y74_codsani
                and saniatividade.y83_databx is null
                and date_part('year', saniatividade.y83_dtfim) >= date_part('year', CURRENT_DATE)
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
                and saniatividade.y83_databx is null
                and date_part('year', saniatividade.y83_dtfim) >= date_part('year', CURRENT_DATE)
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
                and saniatividade.y83_databx is null
                and date_part('year', saniatividade.y83_dtfim) >= date_part('year', CURRENT_DATE)
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
        else
        
          v_achou = false;

          if lraise is true then
            raise notice 'v_y74_codsani: %', v_y74_codsani;
            raise notice 'antes for...';
          end if;

          if lCalculaPorPorteAtividade then

            -- Pega o primeiro tipo de calculo encontrado na tabela de ligaÁ„o com o porte
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
              and saniatividade.y83_databx is null
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
    end if;

    if lVistoriaLocalizacao = true then

      if lCalculaPorPorteAtividade then

        -- Pega o primeiro tipo de calculo encontrado na tabela de ligaÁ„o com o porte
        -- Faz ligaÁ„o com tipcalc para verificar se È sanitario ou localizaÁ„o
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

          select q60_calcbaixorisco
            into calc_baixo_risco
            from parissqn;
 
          if calc_baixo_risco is false then
              
            select q30_inscr
              into V_INSCR_BAIXO_RISCO
              from issquant
              where q30_inscr  = V_Y71_INSCR
                and q30_graurisco = 'B';
  
            if V_INSCR_BAIXO_RISCO != 0 OR V_INSCR_BAIXO_RISCO is not null then
  
              delete from vistorianumpre where y69_codvist = iCodigoVistoria and y69_numpre = iNumpreGerado;
              return '26 - INSCRI«√O COM GRAU DE RISCO BAIXO, SE DESEJA CALCULAR ALTERE PARA SIM O PAR¬METRO GERAR C¡LCULO PARA BAIXO RISCO';
            end if;
          end if;

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
              return '24- Porte n„o encontrado no cadastro da empresa. Alvara sanitario : '||v_y74_codsani;
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

            v_text = v_text || ' select q81_qiexe,q81_qfexe,q81_codigo,q81_uqtab,q81_uqcad,q07_ativ as q80_ativ \n';
            v_text = v_text || '   from tabativ \n';
            v_text = v_text || '        inner join issbaseporte        on q45_inscr    = q07_inscr \n';
            v_text = v_text || '        inner join tabativportetipcalc on q143_issporte = q45_codporte \n';
            v_text = v_text || '                                      and q143_ativid   = q07_ativ \n';
            v_text = v_text || '        inner join tipcalc on q81_codigo = q143_tipcalc \n';
            v_text = v_text || '  where q07_ativ = ' || v_ativprinc || '\n';
            v_text = v_text || '    and q45_inscr = ' || v_y71_inscr || '\n';
            v_text = v_text || '    and q81_tipo in ( select q81_codigo from w_origemdados )\n';
            v_text = v_text || ' union \n';
            v_text = v_text || ' select q81_qiexe,q81_qfexe,q81_codigo,q81_uqtab,q81_uqcad,q82_ativ as q80_ativ \n';
            v_text = v_text || '   from issportetipo \n';
            v_text = v_text || '        inner join issbaseporte on q45_inscr          = ' || v_y71_inscr || '\n';
            v_text = v_text || '        inner join tipcalc      on q41_codtipcalc     = q81_codigo \n';
            v_text = v_text || '        inner join cadcalc      on cadcalc.q85_codigo = tipcalc.q81_cadcalc \n';
            v_text = v_text || '        inner join clasativ     on q82_classe         = q41_codclasse \n';
            v_text = v_text || '  where q45_codporte = q41_codporte \n';
            v_text = v_text || '    and q81_tipo in ( select q81_codigo from w_origemdados ) \n';
            v_text = v_text || '    and q82_ativ = ' || v_ativprinc || '\n';

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

$$ language 'plpgsql';   

SQL1
    );
    }

    public function downFcVistoriasCharqueadasAlterada()
    {
    DB::statement(<<<SQL1

create or replace function fc_vistorias_charqueadas(integer)
returns varchar(200)
as $$
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
      return '21-erro ao selecionar origem dos dados (inscriÁ„o ou sanit·rio)!';
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

        -- Pega o primeiro tipo de calculo encontrado na tabela de ligaÁ„o com o porte
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

        -- Pega o primeiro tipo de calculo encontrado na tabela de ligaÁ„o com o porte
        -- Faz ligaÁ„o com tipcalc para verificar se È sanitario ou localizaÁ„o
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
              return '24- Porte n„o encontrado no cadastro da empresa. Alvara sanitario : '||v_y74_codsani;
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

            v_text = v_text || ' select q81_qiexe,q81_qfexe,q81_codigo,q81_uqtab,q81_uqcad,q07_ativ as q80_ativ \n';
            v_text = v_text || '   from tabativ \n';
            v_text = v_text || '        inner join issbaseporte        on q45_inscr    = q07_inscr \n';
            v_text = v_text || '        inner join tabativportetipcalc on q143_issporte = q45_codporte \n';
            v_text = v_text || '                                      and q143_ativid   = q07_ativ \n';
            v_text = v_text || '        inner join tipcalc on q81_codigo = q143_tipcalc \n';
            v_text = v_text || '  where q07_ativ = ' || v_ativprinc || '\n';
            v_text = v_text || '    and q45_inscr = ' || v_y71_inscr || '\n';
            v_text = v_text || '    and q81_tipo in ( select q81_codigo from w_origemdados )\n';
            v_text = v_text || ' union \n';
            v_text = v_text || ' select q81_qiexe,q81_qfexe,q81_codigo,q81_uqtab,q81_uqcad,q82_ativ as q80_ativ \n';
            v_text = v_text || '   from issportetipo \n';
            v_text = v_text || '        inner join issbaseporte on q45_inscr          = ' || v_y71_inscr || '\n';
            v_text = v_text || '        inner join tipcalc      on q41_codtipcalc     = q81_codigo \n';
            v_text = v_text || '        inner join cadcalc      on cadcalc.q85_codigo = tipcalc.q81_cadcalc \n';
            v_text = v_text || '        inner join clasativ     on q82_classe         = q41_codclasse \n';
            v_text = v_text || '  where q45_codporte = q41_codporte \n';
            v_text = v_text || '    and q81_tipo in ( select q81_codigo from w_origemdados ) \n';
            v_text = v_text || '    and q82_ativ = ' || v_ativprinc || '\n';

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

$$ language 'plpgsql';    

SQL1
    );
    }

}
