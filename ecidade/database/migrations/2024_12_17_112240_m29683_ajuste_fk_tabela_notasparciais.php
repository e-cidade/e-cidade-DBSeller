<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29683AjusteFkTabelaNotasparciais extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
        ALTER TABLE escola.procavaliacaoparcial 
            DROP CONSTRAINT IF EXISTS escola_procavaliacaoparcial_ed340_regencia_foreign;
        ALTER TABLE escola.procresultadoparcial 
            DROP CONSTRAINT IF EXISTS escola_procresultadoparcial_ed342_regencia_foreign;

        ALTER TABLE escola.procavaliacaoparcial 
            ADD CONSTRAINT escola_procavaliacaoparcial_ed340_regencia_foreign 
            FOREIGN KEY (ed340_regencia) 
            REFERENCES escola.regencia(ed59_i_codigo) ON DELETE CASCADE;
        ALTER TABLE escola.procresultadoparcial 
            ADD CONSTRAINT escola_procresultadoparcial_ed342_regencia_foreign 
            FOREIGN KEY (ed342_regencia) 
            REFERENCES escola.regencia(ed59_i_codigo) ON DELETE CASCADE;
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
        ALTER TABLE escola.procavaliacaoparcial 
            DROP CONSTRAINT IF EXISTS escola_procavaliacaoparcial_ed340_regencia_foreign;
        ALTER TABLE escola.procresultadoparcial 
            DROP CONSTRAINT IF EXISTS escola_procresultadoparcial_ed342_regencia_foreign;
SQL
        );
    }
}
