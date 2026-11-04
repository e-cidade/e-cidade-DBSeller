<?php

use Illuminate\Database\Migrations\Migration;

class M27655RotinaExclusaoDocumentoContratacaoEditalAviso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229250 ,'Exclusão Documento' ,'Exclusão Documento' ,'web/patrimonial/pncp/procedimentos/contratacao-edital-aviso/exclusao-documento' ,'1' ,'1' ,'Exclusão de documento de uma Contratação/Edital/Aviso' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228816 ,229250 ,5 ,228802 );
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
delete from db_menu where id_item_filho = 229250 AND modulo = 228802;
delete from db_itensmenu where id_item = 22950;
SQL
        );
    }
}
