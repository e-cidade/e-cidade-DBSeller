<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25876AdicionaMenuRelatorioGuiaPrevidenciaLotacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec($this->menu());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->getPdo()->exec($this->menu(true));
    }

    public function menu($rollback = false)
    {
        $sql = <<<SQL
        insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228975 ,'Tipos de Guia Previdência (GPS)' ,'Rotina de emissão de Tipos de Guia Previdência (GPS)' ,'web/recursos-humanos/pessoal/relatorios/tipo_guia_previdencia' ,'1' ,'1' ,'Rotina de emissão de Tipos de Guia Previdência (GPS)' ,'false' );
        delete from db_menu where id_item_filho = 228975 AND modulo = 952;
        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 2458 ,228975 ,33 ,952 );

SQL;
        if ($rollback) {
            $sql = <<<SQL
                delete from configuracoes.db_menu where id_item_filho = 228975 AND modulo = 952;
                delete from configuracoes.db_itensmenu where id_item = 228975;
SQL;
        }
        return $sql;
    }
}
