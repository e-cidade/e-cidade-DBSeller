<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23791AtualizaFonteDeRecursoDeContaPagadora extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       //
       $sql = <<<SQL


       insert into db_syscampo values(1014791,'k29_fr_contapagadora','int4','Busca fonte de recurso da conta agadora','0', 'Validar FR de conta pagadora :',10,'t','f','f',1,'text','Validar FR de conta pagadora :');
       insert into db_sysarqcamp values(1503,1014791,16,0);

       alter table caiparametro add COLUMN k29_fr_contapagadora int default 0;






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

        delete from db_sysarqcamp where codcam = 1014791;

        delete from db_syscampo where codcam = 1014791;

        alter table caiparametro drop COLUMN k29_fr_contapagadora;

SQL;

    DB::connection()->getPdo()->exec($sql);
    }
}
