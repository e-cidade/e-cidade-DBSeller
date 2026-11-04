<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M22969NovosRelatorios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente )
values ( 228826 ,'Orçamento por Recurso' ,'Orçamento por Recurso' ,'orc2_orcamentoporecurso001.php' ,'1' ,'1' ,'Lista as despesas e receitas que possuem a lista de recurso informado' ,'true' ),
       ( 228827 ,'Plano de Governo por Recurso' ,'Plano de Governo por Recurso' ,'pla2_orcamentoporecurso001.php' ,'1' ,'1' ,'Lista as estimativas da despesa e receita que possuem o recurso selecionado' ,'true' );

insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo )
values ( 4150 ,228826 ,13 ,116 ),
       ( 228363 ,228827 ,6 ,228358 );
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
delete from db_menu where id_item_filho in (228826, 228827);
delete from db_itensmenu where id_item in (228826, 228827);
SQL
        );
    }
}
