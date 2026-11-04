<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26915AdicionaMenuCertidaoContribuicao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "
        insert into configuracoes.db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229025 ,'Certidão Contribuição IN 128/2022' ,'Certidão Contribuição IN 128/2022' ,'web/recursos-humanos/rh/relatorios/certidao_tempo_contribuicao' ,'1' ,'1' ,'Certidão de Tempo de Contribuição Anexo V-IN 128/2022' ,'false' );
        insert into configuracoes.db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 30 ,229025 ,854 ,2323 );";
        DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = "
        delete from configuracoes.db_menu where id_item_filho = 229025;
        delete from configuracoes.db_itensmenu where id_item = 229025;
        ";
        DB::connection()->getPdo()->exec($sql);
    }
}
