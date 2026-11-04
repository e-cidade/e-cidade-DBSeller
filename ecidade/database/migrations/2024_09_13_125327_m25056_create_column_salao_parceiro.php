<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25056CreateColumnSalaoParceiro extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
        
        DB::unprepared(<<<SQL

        ALTER TABLE issqn.issconfiguracaogruposervico ADD q136_salaoparceiro BOOLEAN DEFAULT FALSE;

        insert into db_syscampo values(1015668,'q136_salaoparceiro','bool','Atividade apta para salão parceiro','false', 'Salão parceiro',1,'t','f','f',5,'text','Salão parceiro');
        insert into db_sysarqcamp values(3430,1015668,9,0);

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
        ALTER TABLE issqn.issconfiguracaogruposervico DROP q136_salaoparceiro;

        delete from db_sysarqcamp
        where codarq = 3430
        and codcam = 1015668;

        delete from db_syscampo
        where codcam = 1015668;
        
SQL
    );
    }
}
