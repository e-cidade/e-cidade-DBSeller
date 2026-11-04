<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24311AjusteRegistrosSemTurmaturnoreferente extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            WITH registros_sem_turmaturno_refente AS
            (
                select turmaturnoreferente.ed336_codigo as codTurno, diario_classe_bncc.ed155_codigo as codigoRegistro from diario_classe_bncc
                    join regencia on ed155_regencia = ed59_i_codigo
                    join turma on ed59_i_turma = ed57_i_codigo
                    join turmaturnoreferente on ed336_turma = ed57_i_codigo
                    where ed155_turmaturnoreferente is null
            )
            update diario_classe_bncc
            set ed155_turmaturnoreferente = registros_sem_turmaturno_refente.codTurno
            from registros_sem_turmaturno_refente
            where ed155_codigo = registros_sem_turmaturno_refente.codigoRegistro
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
