<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28367AlteraObrigatoriedadeCnpjEsocial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
            update configuracoes.db_cadattdinamicoatributos set db109_obrigatorio = 'f' where db109_nome = 'cnpjMandElet_esocial';
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
        $sql=<<<SQL
            update configuracoes.db_cadattdinamicoatributos set db109_obrigatorio = 't' where db109_nome = 'cnpjMandElet_esocial';
SQL;
        DB::connection()->getPdo()->exec($sql);  
    }
}
