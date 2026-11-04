<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23727AjustandoTableaBnccreferencial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
        with ano_passado
            as (select * from bnccreferencial where ed168_ano = 2022)
         update bnccreferencial
            set ed168_objeto_conhecimento = ano_passado.ed168_objeto_conhecimento
             from ano_passado
           where bnccreferencial.ed168_codigoreferencial = ano_passado.ed168_codigoreferencial
           and bnccreferencial.ed168_ano = 2023"
        );
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
