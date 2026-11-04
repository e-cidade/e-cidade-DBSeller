<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24619TriggerEmpenho extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upTrigger();
        $this->upMigrarCgmsJaExistentes();
    }

    public function upTrigger()
    {
        DB::connection()->getPdo()->exec(<<<sql
        CREATE OR REPLACE FUNCTION fc_atualiza_cadastro_fornecedor_empenho()
                    RETURNS TRIGGER
                    LANGUAGE plpgsql
                    AS $$
                    DECLARE

            sOperacao varchar DEFAULT lower(TG_OP);
            qtdResgistros integer;

        BEGIN

                    IF sOperacao IN ('insert', 'update') THEN

                      SELECT
            count(*)
                      INTO
            qtdResgistros
        FROM
            pcforne
        WHERE
            pc60_numcgm = new.e60_numcgm;

        IF qtdResgistros = 0 THEN
            INSERT INTO  compras.pcforne (
            pc60_numcgm,
            pc60_dtlanc,
            pc60_obs,
            pc60_bloqueado,
            pc60_hora,
            pc60_usuario,
            pc60_indicativocprb
        )
        VALUES (
            new.e60_numcgm,
            to_char(now(),'yyyy-mm-dd')::date,
            '',
            FALSE,
            to_char(now(),'HH24:MI'),
            1,
            FALSE
        );
        END IF;

        RETURN NEW;
        ELSE
                      RETURN NEW;
        END IF;
        END;

        $$;

    CREATE TRIGGER tg_atualiza_cadastro_fornecedor_empenho AFTER INSERT OR UPDATE ON
    empenho.empempenho FOR EACH ROW EXECUTE PROCEDURE fc_atualiza_cadastro_fornecedor_empenho();

sql
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->getPdo()->exec(<<<sql
 DROP TRIGGER IF EXISTS tg_atualiza_cadastro_fornecedor_empenho  ON empenho.empempenho ;
 DROP FUNCTION IF EXISTS fc_atualiza_cadastro_fornecedor_empenho;
sql
        );
    }


    public function upMigrarCgmsJaExistentes()
    {
        DB::connection()->getPdo()->exec(<<<sql
INSERT INTO 	compras.pcforne (
    pc60_numcgm,
	pc60_dtlanc,
	pc60_obs,
	pc60_bloqueado,
	pc60_hora,
	pc60_usuario,
	pc60_indicativocprb
)
SELECT
	DISTINCT ON
	(e60_numcgm)
e60_numcgm AS pc60_numcgm,
	to_char(now(),'yyyy-mm-dd')::date AS pc60_dtlanc,
	'' AS pc60_obs,
	FALSE AS pc60_bloqueado,
	to_char(now(),'HH24:MI') AS pc60_hora,
	1 AS pc60_usuario,
	FALSE AS pc60_indicativocprb
FROM
	empenho.empempenho
LEFT JOIN compras.pcforne ON
	compras.pcforne.pc60_numcgm = empenho.empempenho.e60_numcgm
WHERE
	pc60_numcgm IS NULL ;

sql
        );
    }
}
