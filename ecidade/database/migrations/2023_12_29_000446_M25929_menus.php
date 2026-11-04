<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25929Menus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
        insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229017 ,'Ajuda de Custo' ,'Pessoal > Procedimentos > Ajuda de Custo' ,'' ,'1' ,'1' ,'Cadastro de ajuda de custo' ,'true' );
        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 1818 ,229017 ,148 ,952 );
        insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229018 ,'Configurações' ,'Configurações' ,'web/recursos-humanos/pessoal/ajuda-custo/configuracao' ,'1' ,'1' ,'Configurações de ajuda de custo' ,'true' );
        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 229017 ,229018 ,1 ,952 );
        insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229019 ,'Lançamento' ,'Lançamento' ,'web/recursos-humanos/pessoal/ajuda-custo/lancamento' ,'1' ,'1' ,'Lançamento de ajuda de custo' ,'true' );
        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 229017 ,229019 ,2 ,952 );
        insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229020 ,'Relatório' ,'Relatório de ajuda de custo' ,'web/recursos-humanos/pessoal/ajuda-custo/relatorio' ,'1' ,'1' ,'Relatório de ajuda de custo' ,'true' );
        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 229017 ,229020 ,3 ,952 );
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
        delete from db_menu where id_item_filho in (229017,229018, 229019, 229020);
        delete from db_itensmenu where id_item in (229017,229018, 229019, 229020);
SQL;
        DB::connection()->getPdo()->exec($sql);

    }
}
