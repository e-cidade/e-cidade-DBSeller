<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28742InverterModoOcorrencia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        DB::unprepared(<<<SQL
        UPDATE configuracoes.db_syscampodef set defdescr = 'Automático' where defcampo = '1' and codcam = 15090;
        UPDATE configuracoes.db_syscampodef set defdescr = 'Manual' where defcampo = '2' and codcam = 15090;
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
        DB::unprepared(<<<SQL
        UPDATE configuracoes.db_syscampodef set defdescr = 'Manual' where defcampo = '1' and codcam = 15090;
        UPDATE configuracoes.db_syscampodef set defdescr = 'Automático' where defcampo = '2' and codcam = 15090;
SQL
    );
    }
}
