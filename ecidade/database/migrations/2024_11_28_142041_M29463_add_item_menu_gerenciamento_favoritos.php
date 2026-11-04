<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29463AddItemMenuGerenciamentoFavoritos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upItemMenuGerenciamentoFavoritos();
    }

    public function upItemMenuGerenciamentoFavoritos() {
        $sql = "
            insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api ) values ( 229358 ,'Manutenção de Favoritos' ,'web/configuracao/favoritos' ,'web/configuracao/favoritos' ,'1' ,'1' ,'Menu de manutenção de favoritos' ,'true' ,'false' );
            insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 32 ,229358 ,590 ,1 );
        ";
        DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downItemMenuGerenciamentoFavoritos();
    }

    public function downItemMenuGerenciamentoFavoritos()
    {
        $sql = "
            delete from  db_menu where id_item_filho =  229358;
            delete from db_itensmenu where id_item  =  229358;
        ";
        DB::connection()->getPdo()->exec($sql);
    }
}
