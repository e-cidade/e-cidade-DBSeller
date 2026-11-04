<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28539TamanhoCampoAgenciaFolha extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
            alter table pessoal.folha alter column r38_agenc type VARCHAR(7);
SQL;
    DB::connection()->getPdo()->exec($sql);


        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = <<<SQL
            alter table pessoal.folha add r38_agencbkp varchar(7) null;
            update pessoal.folha set r38_agencbkp = r38_agenc where r38_agencbkp is null;
            update pessoal.folha set r38_agenc = '' where r38_agencbkp is not null;
            alter table pessoal.folha alter column r38_agenc type varchar(5);
            update pessoal.folha set r38_agenc = substr(trim(r38_agencbkp),1,5) where r38_agencbkp is not null;
            ALTER TABLE pessoal.folha DROP COLUMN r38_agencbkp;
SQL;
    }
}
