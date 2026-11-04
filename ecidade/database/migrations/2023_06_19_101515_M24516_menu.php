<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24516Menu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
update db_itensmenu set funcao = '' where id_item = 6997;

insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente )
values ( 228947 ,'Receitas do Município Recursos Livres' ,'Receitas do Município Recursos Livres' ,'emp4_gerarslipretencao001.php' ,'1' ,'1' ,'Realiza a transferência de recurso quando empenho é de recurso vinculado.' ,'true' ),
       ( 228948 ,'Cobertura dos Recursos extra-orçamentários' ,'Cobertura dos Recursos extra-orçamentários' ,'web/financeiro/tesouraria/slip/gerar/cobertura-recurso-extra' ,'1' ,'1' ,'Realiza a transferência da conta principal para conta extra para conta não ficar negativa.' ,'true' );

insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo )
values ( 6997 ,228947 ,1 ,39 ),
       ( 6997 ,228948 ,2 ,39 );
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
update db_itensmenu set funcao = 'emp4_gerarslipretencao001.php' where id_item = 6997;
delete from db_menu where id_item_filho in (228947, 228948);
delete from db_itensmenu where id_item in (228947, 228948);
SQL
        );
    }
}
