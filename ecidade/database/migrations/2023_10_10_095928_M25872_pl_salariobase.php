<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25872PlSalariobase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL

DROP FUNCTION IF EXISTS fc_salariobase(integer, integer, integer, integer);
CREATE OR REPLACE FUNCTION pessoal.fc_salariobase(
    integer,
    integer,
    integer,
    integer)
    RETURNS numeric
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $$

DECLARE

    REGISTRO                       ALIAS FOR $1;
    ANO                            ALIAS FOR $2;
    MES                            ALIAS FOR $3;
    INSTIT                         ALIAS FOR $4;

    F002	                       FLOAT4  := 0;
    F008	                       FLOAT4  := 0;
    F010	                       FLOAT8  := 0;
    F024	                       FLOAT4  := 0;

    VALOR_PADRAO                   FLOAT8  := 0;

    DATA_BASE                      DATE;
    DATA_PROGR                     DATE;
    dR11_DATAF                     DATE;

    DIVERSOMINIMO                  varchar(4) := '    ';

    VALOR_PROG                     FLOAT8  := 0;

    R_PES                          RECORD;
    R_PAD                          RECORD;
    R_PROGR                        RECORD;

    VALORMIN                       FLOAT8 := 0;

BEGIN

    SELECT rh30_regime     as r01_regime,
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
           end as r01_progr
      INTO R_PES
    FROM RHPESSOAL
         JOIN CGM                   ON RH01_NUMCGM = Z01_NUMCGM
         JOIN RHPESSOALMOV          ON RH02_ANOUSU = ANO
                                   AND RH02_MESUSU = MES
                                   AND RH02_REGIST = REGISTRO
                                   AND RH02_INSTIT = INSTIT
         JOIN RHREGIME              ON RH30_CODREG = RH02_CODREG
                                   AND RH30_INSTIT = RH02_INSTIT
         LEFT JOIN  RHPESRESCISAO   ON RH05_SEQPES = RH02_SEQPES
         LEFT JOIN  RHPESPADRAO     ON RH02_SEQPES = RH03_SEQPES
    WHERE RH01_REGIST = REGISTRO;

    SELECT R11_DATAF
      INTO dR11_DATAF
    FROM CFPESS
    WHERE R11_ANOUSU = ANO
      AND R11_MESUSU = MES
      AND R11_INSTIT = INSTIT
    ORDER BY R11_ANOUSU DESC, R11_MESUSU DESC LIMIT 1;

    SELECT r02_hrssem, r02_tipo, r02_valor, r02_minimo
      INTO R_PAD
    FROM PADROES
    WHERE R02_ANOUSU = ANO
      AND R02_MESUSU = MES
      AND R02_INSTIT = INSTIT
      AND R02_REGIME = R_PES.R01_REGIME
      AND R02_CODIGO = R_PES.R01_PADRAO;

    -- verifica horas semanais

    IF R_PES.R01_HRSSEM <> 0 OR R_PES.R01_HRSSEM <> NULL THEN
        F002 = R_PES.R01_HRSSEM;
    ELSE
        IF R_PAD.R02_HRSSEM <> NULL THEN
            F002 = R_PAD.R02_HRSSEM;
        END IF;
    END IF;

    -- verifica horas mensais

    IF R_PES.R01_HRSSEM <> NULL OR R_PES.R01_HRSSEM > 0 THEN
        F008 = R_PES.R01_HRSSEM * 5;
    ELSE
        F008 = F002 * 5;
    END IF;

    -- verifica salario sem progressao

    IF R_PES.R01_SALARI <> NULL OR R_PES.R01_SALARI > 0 THEN
        F010 = R_PES.R01_SALARI;
    ELSE
        IF R_PES.R01_PADRAO <> '' AND R_PES.R01_PADRAO IS NOT NULL THEN
            IF R_PAD.R02_TIPO = 'H' THEN
                VALOR_PADRAO = ROUND(R_PAD.R02_VALOR*F008,2);
            ELSE
                VALOR_PADRAO = R_PAD.R02_VALOR;
            END IF;
            IF R_PES.R01_HRSSEM > 0 AND R_PAD.R02_HRSSEM > 0 THEN
                F010 = ((VALOR_PADRAO/R_PAD.R02_HRSSEM)*R_PES.R01_HRSSEM);
            ELSE
                F010 = VALOR_PADRAO;
            END IF;
            DIVERSOMINIMO = R_PAD.R02_MINIMO;
        ELSE
            F010 = 0;
        END IF;
    END IF;

    -- data base

    IF R_PES.R01_TPVINC = 'A' THEN
        DATA_BASE = dR11_DATAF;
    ELSE
        DATA_BASE = R_PES.R01_ADMISS;
    END IF;

    -- data de progressao

    IF R_PES.R01_ANTER IS NULL THEN
        DATA_PROGR = R_PES.R01_ADMISS;
    ELSE
        DATA_PROGR = R_PES.R01_ANTER;
    END IF;

    -- MESES DE PROGRESSAO
    F024 = FC_CONTA_MESES( DATA_PROGR, DATA_BASE );

    IF R_PES.R01_PROGR = 'S' AND ( R_PES.R01_SALARI = 0 OR R_PES.R01_SALARI IS NULL ) THEN

        FOR R_PROGR IN SELECT R24_VALOR
                       FROM PROGRESS
                       WHERE R24_ANOUSU = ANO
                         AND R24_MESUSU = MES
                         AND R24_INSTIT = INSTIT
                         AND R24_PADRAO = R_PES.R01_PADRAO
                         AND R24_REGIME = R_PES.R01_REGIME
                         AND R24_MESES <= F024
                       ORDER BY R24_MESES
        LOOP
                IF R_PES.R01_HRSSEM > 0 AND R_PAD.R02_HRSSEM > 0 THEN
                    VALOR_PROG = ((R_PROGR.R24_VALOR/R_PAD.R02_HRSSEM)*R_PES.R01_HRSSEM);
                ELSE
                    VALOR_PROG = R_PROGR.R24_VALOR;
                END IF;

        END LOOP;

        IF VALOR_PROG > 0 THEN
            F010 = VALOR_PROG;
            -- * caso padrao por hora e avaliacao chamada pela consulta de funcionario
            IF R_PAD.R02_TIPO = 'H' THEN
                F010 = F010 * F008;
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

    END IF;

    RETURN F010::numeric;

END;
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
             DROP FUNCTION IF EXISTS fc_salariobase(integer, integer, integer, integer);
SQL
        );
    }
}
