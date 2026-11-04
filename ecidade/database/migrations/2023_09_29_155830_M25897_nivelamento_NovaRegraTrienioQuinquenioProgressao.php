<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25897NivelamentoNovaRegraTrienioQuinquenioProgressao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(<<<SQL1

        CREATE OR REPLACE FUNCTION "db_fxxx" (integer,integer,integer,integer)
RETURNS varchar(250)
AS
$$

DECLARE

    REGISTRO                       ALIAS FOR $1;
    ANO                            ALIAS FOR $2;
    MES                            ALIAS FOR $3;
    INSTIT                         ALIAS FOR $4;

    HRSSEM                         FLOAT4  := 0;
    F001	                          FLOAT4  := 0;
    F002	                          FLOAT4  := 0;
    F003	                          DATE;
    F004	                          INTEGER := 0;
    F005	                          INTEGER := 0;
    F006	                          INTEGER := 0;
    F006_CLT                       INTEGER := 0;
    F008	                          FLOAT4  := 0;
    F009	                          INTEGER := 0;
    F007	                          FLOAT8  := 0;
    F010	                          FLOAT8  := 0;
    F011	                          FLOAT4  := 0;
    F012	                          INTEGER := 0;
    F013	                          INTEGER := 0;
    F014	                          INTEGER := 0;
    F015	                          FLOAT4  := 0;
    F022	                          INTEGER := 0;
    F024	                          FLOAT4  := 0;
    F025	                          INTEGER := 0;
    F026	                          VARCHAR(25) := ' ';
    F030	                          FLOAT8  := 0;
    D913	                          FLOAT8  := 0;
    D908	                          FLOAT8  := 0;
    VALOR_PADRAO                   FLOAT8  := 0;
    VALOR_PADRAO_PREV              FLOAT8  := 0;
    valor_progress                 FLOAT8  := 0;
    DATA_BASE                      DATE;
    DATA_PROGR                     DATE;
    DATA_TRIEN                     DATE;
    DATA_ATUAL                     DATE;
    DIVERSOMINIMO                  VARCHAR(4) := '    ';
    DIVERSOMINIMOPREV              VARCHAR(4) := '    ';
    PADRAO                         VARCHAR(25);
    VALMIN                         FLOAT8 := 0;
    ANOS                           INTEGER := 0;
    PERC_PROG                      FLOAT4  := 0;
    VALOR_PROG                     FLOAT8  := 0;
    VALOR_PROG_PREV                FLOAT8  := 0;
    MAX_TRIENIO                    INTEGER := 0;
    PRIMEIRO_DOANO                 DATE;

    R_PES             RECORD;
    R_PAD             RECORD;
    R_PAD_PREV        RECORD;
    R_PROGR           RECORD;
    R_CFPESS          RECORD;
    R_DEPEND          RECORD;
    R_FERIAS          RECORD;
    VALORMIN     FLOAT8 := 0;
    VALORMINPREV FLOAT8 := 0;

BEGIN

FOR R_PES IN SELECT RH01_REGIST     as r01_regist,
                    rh30_regime     as r01_regime,
                    rh03_padrao     as r01_padrao,
                    rh03_padraoprev as r01_padraoprev,
                    rh02_hrssem     as r01_hrssem,
                    rh02_salari     as r01_salari,
                    rh30_vinculo    as r01_tpvinc,
                    rh01_admiss     as r01_admiss,
                    rh01_progres    as r01_anter,
                    case when rh01_progres is not null
                         then 'S'
                         else 'N'
                    end as r01_progr,
                    rh01_trienio as r01_trien,
                    rh01_nasc    as r01_nasc,
                    rh02_tbprev  as r01_tbprev
    FROM RHPESSOAL
         INNER JOIN CGM             ON RH01_NUMCGM    = Z01_NUMCGM
         INNER JOIN RHPESSOALMOV    ON RH02_ANOUSU    = ANO
                                   AND RH02_MESUSU    = MES
                                   AND RH02_REGIST    = REGISTRO
                                   AND RH02_INSTIT    = INSTIT
         LEFT JOIN  RHPESRESCISAO   ON RH05_SEQPES    = RH02_SEQPES
         LEFT JOIN  RHPESPADRAO     ON RH02_SEQPES    = RH03_SEQPES
         INNER JOIN RHREGIME        ON RH30_CODREG    = RH02_CODREG
                                   AND RH30_INSTIT    = RH02_INSTIT
         WHERE RH01_REGIST = REGISTRO
               LOOP
END LOOP;

FOR R_CFPESS IN SELECT *
                FROM CFPESS
                WHERE R11_ANOUSU = ANO
                  AND R11_MESUSU = MES
                  AND R11_INSTIT = INSTIT
		ORDER BY R11_ANOUSU DESC ,
		         R11_MESUSU DESC
	        LIMIT 1 LOOP
END LOOP;

FOR R_FERIAS IN SELECT *
                FROM CADFERIA
                WHERE R30_ANOUSU = ANO
   	          AND R30_MESUSU = MES
      	       AND R30_REGIST = REGISTRO
		ORDER BY R30_PERAI DESC
	        LIMIT 1 LOOP
END LOOP;

--raise notice 'regime: %', R_Pes.R01_REGIME;
--raise notice 'padrao: %', R_Pes.R01_padrao;

FOR R_PAD IN
   SELECT *
   FROM PADROES
   WHERE R02_ANOUSU = ANO
     AND R02_MESUSU = MES
     AND R02_INSTIT = INSTIT
     AND R02_REGIME = R_PES.R01_REGIME
     AND R02_CODIGO = R_PES.R01_PADRAO LOOP
END LOOP;

-- Padrao de previdncia
FOR R_PAD_PREV IN
   SELECT *
   FROM PADROES
   WHERE R02_ANOUSU = ANO
     AND R02_MESUSU = MES
     AND R02_INSTIT = INSTIT
     AND R02_REGIME = R_PES.R01_REGIME
     AND R02_CODIGO = R_PES.R01_PADRAOPREV LOOP
END LOOP;

DATA_ATUAL = ano||'-'||mes||'-'||ndias(ano,mes);

-- verifica horas semanais

IF R_PES.R01_HRSSEM <> 0 OR R_PES.R01_HRSSEM <> NULL THEN
   F002 = R_PES.R01_HRSSEM;
ELSE
   IF R_PAD.R02_HRSSEM <> NULL THEN
      F002 = R_PAD.R02_HRSSEM;
   END IF;

END IF;

PADRAO = substr(R_PAD.R02_DESCR,1,25);

-- verifica horas mensais

IF R_PES.R01_HRSSEM <> NULL OR R_PES.R01_HRSSEM > 0 THEN
   F008 = R_PES.R01_HRSSEM * 5;
ELSE
   F008 = F002 * 5;
END IF;

-- verifica salario sem progressao

IF R_PES.R01_SALARI <> NULL OR R_PES.R01_SALARI > 0 THEN
   F007 = R_PES.R01_SALARI;
   F010 = R_PES.R01_SALARI;
ELSE
--raise notice 'padrao : %', R_PES.R01_PADRAO;
   IF R_PES.R01_PADRAO <> '' AND R_PES.R01_PADRAO IS NOT NULL THEN
      IF R_PAD.R02_TIPO = 'H' THEN
         VALOR_PADRAO = ROUND(R_PAD.R02_VALOR*F008,2);
      ELSE
         VALOR_PADRAO = R_PAD.R02_VALOR;
      END IF;
      IF R_PES.R01_HRSSEM > 0 AND R_PAD.R02_HRSSEM > 0 THEN
         F007 = ((VALOR_PADRAO/R_PAD.R02_HRSSEM)*R_PES.R01_HRSSEM);
         F010 = ((VALOR_PADRAO/R_PAD.R02_HRSSEM)*R_PES.R01_HRSSEM);
      ELSE
         F007 = VALOR_PADRAO;
         F010 = VALOR_PADRAO;
      END IF;
      DIVERSOMINIMO = R_PAD.R02_MINIMO;
   ELSE
      F007 = 0;
      F010 = 0;
   END IF;
END IF;

-- Padro de previdncia
IF R_PES.R01_PADRAOPREV <> '' AND R_PES.R01_PADRAOPREV IS NOT NULL THEN
  IF R_PAD_PREV.R02_TIPO = 'H' THEN
     VALOR_PADRAO_PREV = ROUND(R_PAD_PREV.R02_VALOR*F008,2);
  ELSE
     VALOR_PADRAO_PREV = R_PAD_PREV.R02_VALOR;
  END IF;
  IF R_PES.R01_HRSSEM > 0 AND R_PAD_PREV.R02_HRSSEM > 0 THEN
     F030 = ((VALOR_PADRAO_PREV/R_PAD_PREV.R02_HRSSEM)*R_PES.R01_HRSSEM);
  ELSE
     F030 = VALOR_PADRAO_PREV;
  END IF;
  DIVERSOMINIMOPREV = R_PAD_PREV.R02_MINIMO;
ELSE
  F030 = 0;
END IF;

--raise notice 'f007: %', f007;
--raise notice 'f010: %', f010;

-- data base

IF R_PES.R01_TPVINC = 'A' THEN
   DATA_BASE = R_CFPESS.R11_DATAF;
ELSE
   DATA_BASE = R_PES.R01_ADMISS;
END IF;

-- data de progressao

IF R_PES.R01_ANTER IS NULL THEN
   DATA_PROGR = R_PES.R01_ADMISS;
ELSE
   DATA_PROGR = R_PES.R01_ANTER;
END IF;

-- data para trienio

IF R_PES.R01_TRIEN IS NULL THEN
   DATA_TRIEN = R_PES.R01_ADMISS;
ELSE
   DATA_TRIEN = R_PES.R01_TRIEN;
END IF;

ANOS = fc_idade(DATA_PROGR,DATA_BASE);

-- ANOS PARA AVANCO
F012 = fc_idade(DATA_PROGR,DATA_BASE);

-- QUANTIDADE DE TRIENIOS
F013 = round(fc_idade(DATA_TRIEN, DATA_BASE)/3) ;

-- MESES DE PROGRESSAO
F024 = FC_CONTA_MESES( DATA_PROGR, DATA_BASE );

F024 = F024 ;

IF R_PES.R01_PROGR = 'S' AND ( R_PES.R01_SALARI = 0 OR R_PES.R01_SALARI IS NULL ) THEN

  FOR R_PROGR IN SELECT *
                 FROM PROGRESS
                 WHERE R24_ANOUSU = ANO
                   AND R24_MESUSU = MES
                   AND R24_INSTIT = INSTIT
                   AND R24_PADRAO = R_PES.R01_PADRAO
                   AND R24_REGIME = R_PES.R01_REGIME
		   AND R24_MESES <= F024
	         ORDER BY R24_MESES
                 LOOP
       F014 = F014+1;
       F015 = R_PROGR.R24_PERC;

       IF R_PES.R01_HRSSEM > 0 AND R_PAD.R02_HRSSEM > 0 THEN
          VALOR_PROG = ((R_PROGR.R24_VALOR/R_PAD.R02_HRSSEM)*R_PES.R01_HRSSEM);
       ELSE
          VALOR_PROG = R_PROGR.R24_VALOR;
       END IF;

       -- Padro de previdncia
       IF R_PES.R01_HRSSEM > 0 AND R_PAD_PREV.R02_HRSSEM > 0 THEN
          VALOR_PROG_PREV = ((R_PROGR.R24_VALOR/R_PAD_PREV.R02_HRSSEM)*R_PES.R01_HRSSEM);
       ELSE
          VALOR_PROG_PREV = R_PROGR.R24_VALOR;
       END IF;

       PERC_PROG  = R_PROGR.R24_PERC;
       PADRAO = substr(R_PROGR.R24_DESCR,1,25);
   END LOOP;
   IF VALOR_PROG > 0 THEN
      F010 = VALOR_PROG;
      -- * caso padrao por hora e avaliacao chamada pela consulta de funcionario
      IF R_PAD.R02_TIPO = 'H' THEN
         F010 = F010 * F008;
      END IF;
--   ELSE
--      F010 += round(F007*round(PERC_PROG::FLOAT/100,2),2);
   END IF;

   -- Padro de previdncia
   IF VALOR_PROG_PREV > 0 THEN
      F030 = VALOR_PROG;
      IF R_PAD_PREV.R02_TIPO = 'H' THEN
         F030 = F030 * F008;
      END IF;
   END IF;

   IF DIVERSOMINIMO <> '    ' THEN
      SELECT R07_VALOR
      INTO VALORMIN
      FROM PESDIVER
      WHERE R07_ANOUSU = ANO
        AND R07_MESUSU = MES
        AND R07_INSTIT = INSTIT
      	AND R07_CODIGO = DIVERSOMINIMO;
      IF F010 < VALORMIN THEN
         F010 = VALORMIN;
      END IF;
   END IF;

   -- Padro de previdncia
   IF DIVERSOMINIMOPREV <> '    ' THEN
      SELECT R07_VALOR
      INTO VALORMINPREV
      FROM PESDIVER
      WHERE R07_ANOUSU = ANO
        AND R07_MESUSU = MES
        AND R07_INSTIT = INSTIT
      	AND R07_CODIGO = DIVERSOMINIMOPREV;
      IF F030 < VALORMINPREV THEN
         F030 = VALORMINPREV;
      END IF;
   END IF;
END IF;

-- calcula salario hora

-- raise notice 'f007 : %', f007;

IF (R_PES.R01_HRSSEM = 0 OR R_PES.R01_HRSSEM IS NULL) then
--   OR (R_PAD.R02_HRSSEM = 0 OR R_PAD.R02_HRSSEM IS NULL) THEN
   IF F002 > 0 THEN
      F001 = F007/(F002 * 5);
      F011 = F010/(F002 * 5);
   ELSE
      F001 = 0;
      F011 = 0;
   END IF;
END IF;

--raise notice 'regist: %', R_pes.r01_regist;
-- DATA DE ADMISSAO

F003 = R_PES.R01_ADMISS;

SELECT R07_VALOR
FROM PESDIVER
INTO MAX_TRIENIO
WHERE R07_ANOUSU = ANO
  AND R07_MESUSU = MES
  AND R07_INSTIT = INSTIT
  AND R07_CODIGO = 'D909';

--raise notice 'max trienio : %', max_trienio;
--raise notice 'F013        : %', F013;
IF F013 > MAX_TRIENIO AND MAX_TRIENIO > 0 THEN
   F013 = MAX_TRIENIO;
END IF;

-- QUANTIDADE DE QUINQUENIOS
F022 = (DATA_BASE - DATA_PROGR) / 1825;

--raise notice 'DATA        : %', DATA_ATUAL;
-- IDADE DO FUNCIONARIO
F004 = (DATA_ATUAL - R_PES.R01_NASC) / 365;

-- DEPENDENTES PARA IRF
F005 = 0;

-- DEPENDENTES PARA SAL. FAM
F006 = 0;

FOR R_DEPEND IN SELECT *
               FROM RHDEPEND
               WHERE RH31_REGIST = R_PES.R01_REGIST
               LOOP

    SELECT R07_VALOR
      FROM PESDIVER
      INTO D908
     WHERE R07_ANOUSU = ANO
       AND R07_MESUSU = MES
       AND R07_INSTIT = INSTIT
       AND R07_CODIGO = 'D908';

   SELECT R07_VALOR
     FROM PESDIVER
     INTO D913
    WHERE R07_ANOUSU = ANO
      AND R07_MESUSU = MES
      AND R07_INSTIT = INSTIT
      AND R07_CODIGO = 'D913';

   IF R_DEPEND.RH31_DEPEND = 'C' THEN
      IF ( R_PES.R01_REGIME = 1 OR R_PES.R01_REGIME = 3 ) AND R_PES.R01_TBPREV = R_CFPESS.R11_TBPREV THEN
         IF ((DATA_ATUAL - R_DEPEND.RH31_DTNASC)/365) < D913 THEN
               F006 = F006 + 1;
         END IF;
         IF ((DATA_ATUAL - R_DEPEND.RH31_DTNASC)/365) < D908 THEN
               F006_CLT = F006_CLT;
         END IF;

      ELSE
         IF R_PES.R01_REGIME = 1 OR R_PES.R01_REGIME = 3 THEN
            IF ((DATA_ATUAL - R_DEPEND.RH31_DTNASC)/365) < D913 THEN
                 F006 = F006 + 1;
            END IF;
         ELSE
            IF ((DATA_ATUAL - R_DEPEND.RH31_DTNASC)/365) < D908 THEN
               F006 = F006 + 1;
            END IF;
	 END IF;
      END IF;
   ELSE
      IF R_DEPEND.RH31_DEPEND = 'S' THEN
         F006 = F006 + 1;
         F006_CLT = F006_CLT + 1;
      END IF;
   END IF;
   IF R_DEPEND.RH31_IRF != '0' THEN
      IF R_DEPEND.RH31_IRF IN ('1', '4', '5', '6', '7') THEN
         F005 = F005 + 1;
      ELSE
         IF R_DEPEND.RH31_IRF IN ('2', '3') THEN
            IF ((DATA_ATUAL - R_DEPEND.RH31_DTNASC)/365) <= 21 THEN
               F005 = F005 + 1;
            END IF;
         END IF;
      END IF;
   END IF;
END LOOP;

IF (R_PES.R01_TBPREV = 0 OR R_PES.R01_TBPREV IS NULL) AND (R_PES.R01_REGIME = 2 OR R_PES.R01_REGIME = 3) THEN
   F006 = 0;
   F006_CLT = 0;
END IF;

PRIMEIRO_DOANO = ANO||'-'||01||'-'||01;

-- Mese para 13 salario
IF F003 < PRIMEIRO_DOANO THEN
   F009 = 12;
ELSE
   IF EXTRACT( DAY FROM F003) > 15 THEN
      F009 = (13 - EXTRACT( MONTH FROM F003)) -1;
   ELSE
      F009 = (13 - EXTRACT( MONTH FROM F003));
   END IF;
END IF;

-- dias do mes
--raise notice 'ano        : %', ano;
--raise notice 'mes        : %', mes;
--raise notice 'dias        : %', ndias(ano,mes);

select ndias(ano,mes) into F025;

-- F026 = PADRAO;

if padrao is not null then
  F026 = PADRAO;
end if;

-- F001 - 001,11
-- F002 - 012,11
-- F003 - 023,11
-- F004 - 034,11
-- F005 - 045,11
-- F006 - 056,11
-- F006 - 067,11
-- F007 - 078,11
-- F008 - 089,11
-- F009 - 100,11
-- F010 - 111,11
-- F011 - 122,11
-- F012 - 133,11
-- F013 - 144,11
-- F014 - 155,11
-- F015 - 166,11
-- F022 - 177,11
-- F024 - 188,11
-- F025 - 199,11
-- F030 - 111,11
-- F026 -
RETURN TO_CHAR(F001,'9999999.99')||
       TO_CHAR(F002,'9999999.99')||
       ' '||F003||
       TO_CHAR(F004,'9999999.99')||
       TO_CHAR(F005,'9999999.99')||
       TO_CHAR(F006,'9999999.99')||
       TO_CHAR(F006_CLT,'9999999.99')||
       TO_CHAR(F007,'9999999.99')||
       TO_CHAR(F008,'9999999.99')||
       TO_CHAR(F009,'9999999.99')||
       TO_CHAR(F010,'9999999.99')||
       TO_CHAR(F011,'9999999.99')||
       TO_CHAR(F012,'9999999.99')||
       TO_CHAR(F013,'9999999.99')||
       TO_CHAR(F014,'9999999.99')||
       TO_CHAR(F015,'9999999.99')||
       TO_CHAR(F022,'9999999.99')||
       TO_CHAR(F024,'9999999.99')||
       TO_CHAR(F025,'9999999.99')||
       TO_CHAR(F030,'9999999.99')||
       ' '||F026;

END;
$$ LANGUAGE 'plpgsql';

SQL1
    );
    }
}
