<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M28609 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL

        DROP TABLE IF EXISTS w_empprestaitemdiaria;
        CREATE TABLE w_empprestaitemdiaria AS
        SELECT e446_sequencial, e446_tipodiaria
        FROM empprestaitemdiaria
        WHERE TRIM(e446_tipodiaria) IN ('nacional', 'foraestado', 'dentroestado', 'internacional');

        UPDATE empprestaitemdiaria
        SET e446_tipodiaria = 0
        WHERE LOWER(TRIM(e446_tipodiaria)) = 'internacional';

        UPDATE empprestaitemdiaria
        SET e446_tipodiaria = 1
        WHERE LOWER(TRIM(e446_tipodiaria)) in ('nacional', 'foraestado', 'dentroestado');

        COMMENT ON COLUMN empenho.empprestaitemdiaria.e446_tipodiaria IS 'Tipo de Diária: Valores permitidos: 0- Internacional; 1- Nacional';
        alter table empenho.empprestaitemdiaria alter column e446_tipodiaria type int using e446_tipodiaria::int;
        update db_syscampo set descricao = 'Tipo de diária\nValores permitidos:\n0-Internacional\n1-Nacional' where nomecam = 'e446_tipodiaria';
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


        ALTER TABLE empenho.empprestaitemdiaria
        ALTER COLUMN e446_tipodiaria TYPE character varying(50)
        USING e446_tipodiaria::character varying(50);

        UPDATE empprestaitemdiaria
        SET e446_tipodiaria = w_empprestaitemdiaria.e446_tipodiaria
        FROM w_empprestaitemdiaria
        WHERE empprestaitemdiaria.e446_sequencial = w_empprestaitemdiaria.e446_sequencial;


        -- Revertendo o comentário da coluna
        COMMENT ON COLUMN empenho.empprestaitemdiaria.e446_tipodiaria IS 'Tipo de Diária: Valores permitidos: "internacional", "nacional", "foraestado", "dentroestado"';

        -- Revertendo a descrição no db_syscampo
        UPDATE db_syscampo
        SET descricao = 'Tipo de diária '
        WHERE nomecam = 'e446_tipodiaria';

SQL
        );
    }
}
