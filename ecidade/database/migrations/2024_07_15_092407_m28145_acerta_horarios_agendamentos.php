<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28145AcertaHorariosAgendamentos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL

    --update agendamentos de consulta
UPDATE agendamentos SET sd23_c_hora = TO_CHAR(EXTRACT(HOUR from sd23_c_hora::time - INTERVAL '1 minute'), 'FM00') || ':' || TO_CHAR(EXTRACT(MINUTE from sd23_c_hora::time - INTERVAL '1 minute'), 'FM00') where sd23_i_ficha > 1;

    --update agendamentos de exame
    UPDATE sau_agendaexames
    SET s113_c_hora = TO_CHAR(EXTRACT(HOUR from s113_c_hora::time - INTERVAL '1 minute'), 'FM00') || ':' || TO_CHAR(EXTRACT(MINUTE from s113_c_hora::time - INTERVAL '1 minute'), 'FM00')
    where s113_i_ficha > 1
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

    --update agendamentos de consulta
UPDATE agendamentos SET sd23_c_hora = TO_CHAR(EXTRACT(HOUR from sd23_c_hora::time + INTERVAL '1 minute'), 'FM00') || ':' || TO_CHAR(EXTRACT(MINUTE from sd23_c_hora::time + INTERVAL '1 minute'), 'FM00') where sd23_i_ficha > 1;

    --update agendamentos de exame
    UPDATE sau_agendaexames
    SET s113_c_hora = TO_CHAR(EXTRACT(HOUR from s113_c_hora::time + INTERVAL '1 minute'), 'FM00') || ':' || TO_CHAR(EXTRACT(MINUTE from s113_c_hora::time + INTERVAL '1 minute'), 'FM00')
    where s113_i_ficha > 1
SQL
        );
    }
}
