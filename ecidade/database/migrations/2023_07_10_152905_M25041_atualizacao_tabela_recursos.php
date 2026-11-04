<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25041AtualizacaoTabelaRecursos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into fontesiconfi values
('502','Recursos não vinculados da compensação de impostos.',2,'Controle dos recursos não vinculados provenientes da compensação de impostos para atendimento ao disposto no artigo 9º da LC 141/2012.');
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
delete from fontesiconfi where codigo_siconfi in ('502');
SQL
        );
    }
}
