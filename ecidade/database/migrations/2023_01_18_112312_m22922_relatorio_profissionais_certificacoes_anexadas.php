<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M22922RelatorioProfissionaisCertificacoesAnexadas extends Migration
{
    private function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228831 ,'Profissionais com Certificações Anexadas' ,'Profissionais com Certificações Anexadas' ,'edu2_profissionaiscertificacoes001.php' ,'1' ,'1' ,'Profissionais com Certificações Anexadas.' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 6871 ,228831 ,4 ,7159 );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228832 ,'Profissionais com Certificações Anexadas' ,'Profissionais com Certificações Anexadas' ,'edu2_profissionaiscertificacoes001.php' ,'1' ,'1' ,'Profissionais com Certificações Anexadas.' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 6871 ,228832 ,5 ,1100747 );
SQL
        );
    }

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_menu where id_item_filho = 228832;
delete from db_itensmenu where id_item = 228832;
delete from db_menu where id_item_filho = 228831;
delete from db_itensmenu where id_item = 228831;
SQL
        );
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionario();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downDicionario();
    }
}
