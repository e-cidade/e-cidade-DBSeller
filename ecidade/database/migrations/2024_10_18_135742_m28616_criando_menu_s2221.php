<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28616CriandoMenuS2221 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL

        insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api ) values ( 229329 ,'S-2221 - Exame Toxicológico do Motorista Profissional Empregado' ,'S-2221 - Exame Toxicológico do Motorista Profissional Empregado' ,'web/recursos-humanos/esocial/exame-toxicologico-motorista-profissional' ,'1' ,'1' ,'S-2221 - Exame Toxicológico do Motorista Profissional Empregado' ,'true' ,'false' );
        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228612 ,229329 ,5 ,10216 );

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
            delete from db_menu where id_item_filho = 229329;
            delete from db_itensmenu where id_item = 229329;
SQL;
        DB::connection()->getPdo()->exec($sql);

    }
}
