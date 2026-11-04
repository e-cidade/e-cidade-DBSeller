<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M27392FcIptuVerificacalculoVerificaCampogerafinanc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            -- DROP FUNCTION public.fc_iptu_verificacalculo(int4, int4, int4, int4);

            CREATE OR REPLACE FUNCTION public.fc_iptu_verificacalculo(integer, integer, integer, integer)
            RETURNS tp_iptu_verificacalc
            LANGUAGE plpgsql
            AS \$function$
                declare
            
                    iMatricula  alias for $1;
                    iAnousu     alias for $2;
                    iParcini ALIAS FOR $3; --Não utilizado no escopo
                    iParcfim ALIAS FOR $4; --Não utilizado no escopo
                    
                    iNumpre           integer default 0;
                    iParcArrecad      integer default 0;
                    iTotParcArrecad   integer default 0;
                    iParcArrepaga     integer default 0;
                    iTotParcArrepaga  integer default 0;
                    iParcArrecant     integer default 0;
                    iTotParcArrecant  integer default 0;
                    iDivold           integer default 0;
                    iNumpreVerifica   integer default 0;
                    icount            integer default 0;
                
                    lRaise            boolean default false;
                    rtp_Retorno       tp_iptu_verificacalc%ROWTYPE;
                    lAbatimento       boolean default false;
            
                begin
            
                    lRaise  := ( case when fc_getsession('DB_debugon') is null then false else true end );
                    
                    rtp_Retorno.rbErro    := false;
                    rtp_Retorno.riCodErro := 0::integer;
                    
                    /*
                    * Verifica a situação do Calculo de IPTU
                    *  Situações de bloqueio:
                    *   1 - Importado para Divida
                    *   2 - Totalmente Pago
                    *   3 - Totalmente Cancelado
                    */
                    SELECT DISTINCT arrecad.k00_numpre,
                                    arrecad.k00_numtot,
                                    (SELECT coalesce(count(k00_numpar), 0) FROM arrecad WHERE k00_numpre = iptunump.j20_numpre),
                                    (SELECT coalesce(count(k00_numpar), 0) FROM arrepaga WHERE k00_numpre = iptunump.j20_numpre),
                                    (SELECT coalesce(count(k00_numpar), 0) FROM arrecant WHERE k00_numpre = iptunump.j20_numpre),
                                    coalesce((SELECT divold.k10_numpre FROM divold WHERE k10_numpre = iptunump.j20_numpre LIMIT 1), 0)
                        INTO iNumpre,
                            iParcArrecad,
                            iTotParcArrecad,
                            iParcArrepaga,
                            iParcArrecant,
                            iDivold
                    FROM iptubase
                            INNER JOIN iptunump ON iptunump.j20_matric = iptubase.j01_matric
                                                    AND iptunump.j20_anousu = iAnousu
                            LEFT JOIN arrecad ON arrecad.k00_numpre = iptunump.j20_numpre
                    WHERE iptubase.j01_matric = iMatricula
                        AND j20_numpre IS NOT NULL;
                    
                    IF found
                    THEN
                    
                        IF iDivold <> 0
                        THEN -- Com Importação para a Dívida
                    
                        rtp_Retorno.rbErro := TRUE;
                        rtp_Retorno.riCodErro := 32 :: INTEGER;
                        ELSIF iNumpre IS NULL AND iParcArrepaga <> 0
                        THEN -- Em processo de Pagamento
                    
                            rtp_Retorno.rbErro := FALSE;
                            rtp_Retorno.riCodErro := 27 :: INTEGER;
                        ELSIF iParcArrecant <> 0 AND iParcArrepaga = 0
                        THEN -- Calculo Cancelado
                    
                            rtp_Retorno.rbErro := TRUE;
                            rtp_Retorno.riCodErro := 34 :: INTEGER;
                        END IF;
                    
                    END IF;
                    
                    /**
                    * Verifica se existe Pagamento Parcial para o débito informado
                    */
                    SELECT j20_numpre
                        INTO iNumpreVerifica
                    FROM iptunump
                    WHERE j20_matric = iMatricula
                        AND j20_anousu = iAnousu
                    LIMIT 1;
                    
                    if found then
                    
                        select fc_verifica_abatimento( 1, (select j20_numpre
                                                        FROM iptunump
                                                        WHERE j20_matric = iMatricula
                                                            AND j20_anousu = iAnousu
                                                        LIMIT 1)) :: BOOLEAN INTO lAbatimento;
                    
                        if lAbatimento then
                    
                        rtp_Retorno.rbErro    := true;
                        rtp_Retorno.riCodErro := 114::integer;
                        end if;
                    
                    end if;
                    
                    perform fc_debug ( '' , lRaise);
                    perform fc_debug (' <iptu_verificacalculo> ERRO        - ' || rtp_Retorno.rbErro   , lRaise);
                    perform fc_debug (' <iptu_verificacalculo> CODIGO ERRO - ' || rtp_Retorno.riCodErro, lRaise);
                    perform fc_debug ( '' , lRaise);
                    
                    
                    
                    create temp table if not exists tmpipturecalculo (
                        matricula     integer,
                        anousu        integer,
                        valor         numeric,
                        valor_isencao numeric,
                        receita       INTEGER,
                        historico     INTEGER
                    );
                    
                    delete from tmpipturecalculo;

                    if rtp_Retorno.riCodErro = 27 then
                        
                        insert into tmpipturecalculo
                        SELECT iMatricula, iAnousu, j21_valor, abs(j21_valorisen), j21_receit, j21_codhis
                        FROM (SELECT j21_valor,
                                    CASE
                                    WHEN iptucalhconf.j89_codhis IS NOT NULL THEN (SELECT CASE
                                                                                            WHEN sum(x.j21_valor) IS NOT NULL
                                                                                                    THEN sum(x.j21_valor)
                                                                                            ELSE 0
                                                                                                END
                                                                                    FROM iptucalv x
                                                                                    WHERE x.j21_anousu = iptucalv.j21_anousu
                                                                                        AND x.j21_matric = iptucalv.j21_matric
                                                                                        AND x.j21_receit = iptucalv.j21_receit
                                                                                        AND x.j21_codhis = iptucalhconf.j89_codhis)
                                    ELSE 0
                                        END AS j21_valorisen,
                                    j21_receit,
                                    j21_codhis
                            FROM iptucalv
                                    INNER JOIN iptucalh ON iptucalh.j17_codhis = j21_codhis
                                    LEFT JOIN iptucalhconf ON iptucalhconf.j89_codhispai = j21_codhis
                                    INNER JOIN tabrec ON tabrec.k02_codigo = j21_receit
                                    LEFT JOIN iptucadtaxaexe ON iptucadtaxaexe.j08_tabrec = j21_receit
                                                                AND iptucadtaxaexe.j08_anousu = j21_anousu
                            WHERE j21_matric = iMatricula
                                AND j21_anousu = iAnousu
                                AND j17_codhis NOT IN (SELECT j89_codhis FROM iptucalhconf)
                                And j21_gerafinanc is true
                            ORDER BY iptucalh.j17_codhis) AS x;
                            
                        select count(1)  into icount from tmpipturecalculo;
                    
                        if icount = 0 then
                            insert into tmpipturecalculo
                                SELECT 
                                    iMatricula, iAnousu,j157_valor, abs(j157_valorisen), j157_receit, j157_codhis                
                                FROM (SELECT j157_valor,
                                            CASE
                                            WHEN iptucalhconf.j89_codhis IS NOT NULL THEN (
                                                    SELECT CASE
                                                                WHEN sum(x.j157_valor) IS NOT null THEN sum(x.j157_valor)
                                                                ELSE 0
                                                            END
                                                    FROM iptucalvold x
                                                        WHERE x.j157_anousu = iptucalvold.j157_anousu
                                                            AND x.j157_matric = iptucalvold.j157_matric
                                                            AND x.j157_receit = iptucalvold.j157_receit
                                                            AND x.j157_codhis = iptucalhconf.j89_codhis
                                                )
                                                ELSE 0
                                            END AS j157_valorisen,
                                            j157_receit,
                                            j157_codhis
                                FROM iptucalvold
                                        INNER JOIN iptucalh ON iptucalh.j17_codhis = j157_codhis
                                        LEFT JOIN iptucalhconf ON iptucalhconf.j89_codhispai = j157_codhis
                                        INNER JOIN tabrec ON tabrec.k02_codigo = j157_receit
                                        LEFT JOIN iptucadtaxaexe ON iptucadtaxaexe.j08_tabrec = j157_receit AND iptucadtaxaexe.j08_anousu = j157_anousu
                                WHERE j157_matric = iMatricula
                                    AND j157_anousu = iAnousu
                                    AND j17_codhis NOT IN (SELECT j89_codhis FROM iptucalhconf)
                                    and j157_gerafinanc is true
                                ORDER BY iptucalh.j17_codhis, j157_sequencial desc  limit 1) AS x;
                            
                        end if;
                    end if;
                    return rtp_Retorno;
            
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
        DB::connection()->getPdo()->exec(<<<SQL
            -- DROP FUNCTION public.fc_iptu_verificacalculo(int4, int4, int4, int4);

            CREATE OR REPLACE FUNCTION public.fc_iptu_verificacalculo(integer, integer, integer, integer)
            RETURNS tp_iptu_verificacalc
            LANGUAGE plpgsql
            AS \$function$
                declare
            
                    iMatricula  alias for $1;
                    iAnousu     alias for $2;
                    iParcini ALIAS FOR $3; --Não utilizado no escopo
                    iParcfim ALIAS FOR $4; --Não utilizado no escopo
                    
                    iNumpre           integer default 0;
                    iParcArrecad      integer default 0;
                    iTotParcArrecad   integer default 0;
                    iParcArrepaga     integer default 0;
                    iTotParcArrepaga  integer default 0;
                    iParcArrecant     integer default 0;
                    iTotParcArrecant  integer default 0;
                    iDivold           integer default 0;
                    iNumpreVerifica   integer default 0;
                    
                    lRaise            boolean default false;
                    rtp_Retorno       tp_iptu_verificacalc%ROWTYPE;
                    lAbatimento       boolean default false;
            
                begin
            
                    lRaise  := ( case when fc_getsession('DB_debugon') is null then false else true end );
                    
                    rtp_Retorno.rbErro    := false;
                    rtp_Retorno.riCodErro := 0::integer;
                    
                    /*
                    * Verifica a situação do Calculo de IPTU
                    *  Situações de bloqueio:
                    *   1 - Importado para Divida
                    *   2 - Totalmente Pago
                    *   3 - Totalmente Cancelado
                    */
                    SELECT DISTINCT arrecad.k00_numpre,
                                    arrecad.k00_numtot,
                                    (SELECT coalesce(count(k00_numpar), 0) FROM arrecad WHERE k00_numpre = iptunump.j20_numpre),
                                    (SELECT coalesce(count(k00_numpar), 0) FROM arrepaga WHERE k00_numpre = iptunump.j20_numpre),
                                    (SELECT coalesce(count(k00_numpar), 0) FROM arrecant WHERE k00_numpre = iptunump.j20_numpre),
                                    coalesce((SELECT divold.k10_numpre FROM divold WHERE k10_numpre = iptunump.j20_numpre LIMIT 1), 0)
                        INTO iNumpre,
                            iParcArrecad,
                            iTotParcArrecad,
                            iParcArrepaga,
                            iParcArrecant,
                            iDivold
                    FROM iptubase
                            INNER JOIN iptunump ON iptunump.j20_matric = iptubase.j01_matric
                                                    AND iptunump.j20_anousu = iAnousu
                            LEFT JOIN arrecad ON arrecad.k00_numpre = iptunump.j20_numpre
                    WHERE iptubase.j01_matric = iMatricula
                        AND j20_numpre IS NOT NULL;
                    
                    IF found
                    THEN
                    
                        IF iDivold <> 0
                        THEN -- Com Importação para a Dívida
                    
                        rtp_Retorno.rbErro := TRUE;
                        rtp_Retorno.riCodErro := 32 :: INTEGER;
                        ELSIF iNumpre IS NULL AND iParcArrepaga <> 0
                        THEN -- Em processo de Pagamento
                    
                            rtp_Retorno.rbErro := FALSE;
                            rtp_Retorno.riCodErro := 27 :: INTEGER;
                        ELSIF iParcArrecant <> 0 AND iParcArrepaga = 0
                        THEN -- Calculo Cancelado
                    
                            rtp_Retorno.rbErro := TRUE;
                            rtp_Retorno.riCodErro := 34 :: INTEGER;
                        END IF;
                    
                    END IF;
                    
                    /**
                    * Verifica se existe Pagamento Parcial para o débito informado
                    */
                    SELECT j20_numpre
                        INTO iNumpreVerifica
                    FROM iptunump
                    WHERE j20_matric = iMatricula
                        AND j20_anousu = iAnousu
                    LIMIT 1;
                    
                    if found then
                    
                        select fc_verifica_abatimento( 1, (select j20_numpre
                                                        FROM iptunump
                                                        WHERE j20_matric = iMatricula
                                                            AND j20_anousu = iAnousu
                                                        LIMIT 1)) :: BOOLEAN INTO lAbatimento;
                    
                        if lAbatimento then
                    
                        rtp_Retorno.rbErro    := true;
                        rtp_Retorno.riCodErro := 114::integer;
                        end if;
                    
                    end if;
                    
                    perform fc_debug ( '' , lRaise);
                    perform fc_debug (' <iptu_verificacalculo> ERRO        - ' || rtp_Retorno.rbErro   , lRaise);
                    perform fc_debug (' <iptu_verificacalculo> CODIGO ERRO - ' || rtp_Retorno.riCodErro, lRaise);
                    perform fc_debug ( '' , lRaise);
                    
                    
                    
                    create temp table if not exists tmpipturecalculo (
                        matricula     integer,
                        anousu        integer,
                        valor         numeric,
                        valor_isencao numeric,
                        receita       INTEGER,
                        historico     INTEGER
                    );
                    
                    delete from tmpipturecalculo;
                    
                    if rtp_Retorno.riCodErro = 27 then
                    
                        insert into tmpipturecalculo
                        SELECT iMatricula, iAnousu, j21_valor, abs(j21_valorisen), j21_receit, j21_codhis
                        FROM (SELECT j21_valor,
                                    CASE
                                    WHEN iptucalhconf.j89_codhis IS NOT NULL THEN (SELECT CASE
                                                                                            WHEN sum(x.j21_valor) IS NOT NULL
                                                                                                    THEN sum(x.j21_valor)
                                                                                            ELSE 0
                                                                                                END
                                                                                    FROM iptucalv x
                                                                                    WHERE x.j21_anousu = iptucalv.j21_anousu
                                                                                        AND x.j21_matric = iptucalv.j21_matric
                                                                                        AND x.j21_receit = iptucalv.j21_receit
                                                                                        AND x.j21_codhis = iptucalhconf.j89_codhis)
                                    ELSE 0
                                        END AS j21_valorisen,
                                    j21_receit,
                                    j21_codhis
                            FROM iptucalv
                                    INNER JOIN iptucalh ON iptucalh.j17_codhis = j21_codhis
                                    LEFT JOIN iptucalhconf ON iptucalhconf.j89_codhispai = j21_codhis
                                    INNER JOIN tabrec ON tabrec.k02_codigo = j21_receit
                                    LEFT JOIN iptucadtaxaexe ON iptucadtaxaexe.j08_tabrec = j21_receit
                                                                AND iptucadtaxaexe.j08_anousu = j21_anousu
                            WHERE j21_matric = iMatricula
                                AND j21_anousu = iAnousu
                                AND j17_codhis NOT IN (SELECT j89_codhis FROM iptucalhconf)
                            ORDER BY iptucalh.j17_codhis) AS x;
                    
                    end if;
                    
                    return rtp_Retorno;
            
                end;
            
            \$function$;

SQL
    );
    }
}
