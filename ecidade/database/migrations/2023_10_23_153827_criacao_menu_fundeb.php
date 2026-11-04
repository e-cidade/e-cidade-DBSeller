<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CriacaoMenuFundeb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228985 ,'Fundeb' ,'Fundeb' ,'' ,'1' ,'1' ,'Rotina de Cálculo do Fundeb' ,'true' );
            insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 1818 ,228985 ,145 ,952 );
            insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228986 ,'Parâmetros Fundeb' ,'Parâmetros Fundeb' ,'web/recursos-humanos/pessoal/procedimentos/fundeb' ,'1' ,'1' ,'Rotina de Parametrização do Cálculo do Fundeb' ,'true' );
            insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228985 ,228986 ,1 ,952 );
            insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228987 ,'Processamento Rubricas no Ponto de Salário' ,'Processamento Rubricas no Ponto de Salário' ,'web/recursos-humanos/pessoal/procedimentos/fundeb-processamento' ,'1' ,'1' ,'Rotina de Cálculo do Fundeb no Ponto de Salário' ,'true' );
            insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228985 ,228987 ,2 ,952 );   
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
            delete from db_menu where id_item_filho in (228985,228986,228987) AND modulo = 952;
            delete from db_itensmenu where id_item in (228985,228986,228987);
SQL
        );
    }
}
