<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M21898Menus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
update db_itensmenu
   set descricao = 'Apropriação por Competência ' , help = 'Apropriação por Competência ' ,
       desctec = 'Rotinas de Apropriação por Competência '
 where id_item = 9478;

update db_itensmenu
   set descricao = 'Decimo e Férias ',
       help = 'Decimo e Férias ',
       desctec = 'Apropriação de Decimo e Férias '
 where id_item = 9476;

update db_itensmenu
   set descricao = 'Processar',
       help = 'Processa apropriação',
       funcao = 'web/financeiro/contabilidade/procedimento/rotinas-mensais/apropriacao/decimo-ferias/apropriar',
       desctec = 'Processa apropriação'
 where id_item = 9479;

update db_itensmenu
   set descricao = 'Estornar',
       help = 'Estornar apropriação',
       funcao = 'web/financeiro/contabilidade/procedimento/rotinas-mensais/apropriacao/decimo-ferias/estornar',
       desctec = 'Estornar apropriação'
 where id_item = 9480;

update db_itensmenu set libcliente = 'false' where id_item = 9477;
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
update db_itensmenu
   set descricao = 'Provisao' ,
       help = 'Provisao' ,
       desctec = 'Provisao'
 where id_item = 9478;

update db_itensmenu
   set descricao = 'Férias ',
       help = 'Férias ',
       desctec = 'Apropriação de Decimo e Férias '
 where id_item = 9476;

update db_itensmenu
   set descricao = 'Processar',
       help = 'Processa',
       funcao = 'con4_processarlancamentoprovisaoferias001.php?tipofolha=2&lEstorno=false',
       desctec = 'Processa apropriação'
 where id_item = 9479;

update db_itensmenu
   set descricao = 'Estornar',
       help = 'Estornar',
       funcao = 'con4_processarlancamentoprovisaoferias001.php?tipofolha=2&lEstorno=true',
       desctec = 'Estornar apropriação'
 where id_item = 9480;

update db_itensmenu set libcliente = 'true' where id_item = 9477;
SQL
        );
    }
}
