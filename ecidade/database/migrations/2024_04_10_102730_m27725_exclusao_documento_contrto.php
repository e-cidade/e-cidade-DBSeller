<?php

use Illuminate\Database\Migrations\Migration;

class M27725ExclusaoDocumentoContrto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229254 ,'Exclusão Documento' ,'Exclusão Documento' ,'web/patrimonial/pncp/procedimentos/contrato/exclusao-contrato ' ,'1' ,'1' ,'Exclusão do Documento do Contrato' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228848 ,229254 ,7 ,228802 );

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
delete from db_menu where id_item_filho = 229254 AND modulo = 228802;
delete from db_itensmenu where id_item = 229254;
SQL
        );
    }
}
