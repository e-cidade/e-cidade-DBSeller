<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M28456AjustaVariavelEmpempenhooutrosdados extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL



        update empempenhooutrosdados
            set e171_dados = (replace(e171_dados::text, 'unidade_gestora', 'ug_ato_juridico'))::json
        where e171_dados::text ilike '%ato_juridico%,%unidade_gestora%';


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


        update empempenhooutrosdados set e171_dados = (replace(e171_dados::text, 'ug_ato_juridico', 'unidade_gestora'))::json
        where e171_dados::text ilike '%ato_juridico%,%ug_ato_juridico%';


SQL
        );
    }
}
