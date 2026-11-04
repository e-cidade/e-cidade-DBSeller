<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23123ItemMenuStatusPaciente extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228843 ,'Status do Paciente no Atendimento' ,'Localizar o paciente na unidade' ,'web/saude/ambulatorial/consultas/status-paciente' ,'1' ,'1' ,'Rotina para consulta da localizaзгo do paciente na unidade de saъde.' ,'true' );");
        DB::statement("insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 31 ,228843 ,195 ,1000004 );");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("delete from db_menu where id_item_filho = 228843;");
        DB::statement("delete from db_itensmenu where id_item = 228843;");
    }
}
