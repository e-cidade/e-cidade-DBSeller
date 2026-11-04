<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M23080mensagemCabecalhoImovelMatriculaDbpref extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
        
        delete from db_confmensagem  where cod = 'imovel_cab_matricula' and instit = 1 ;
        INSERT INTO db_confmensagem (cod, mens, alinhamento, instit) VALUES ('imovel_cab_matricula', '<font style="font-weight: bold; font-size: 15px; color: rgb(0, 0, 0);" face="Verdana, Arial, Helvetica, sans-serif">Digite o Número da Matrícula.</font>', 'center', 1);
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

        delete from db_confmensagem  where cod = 'imovel_cab_matricula' and instit = 1 ;
SQL
        );
    }
}
