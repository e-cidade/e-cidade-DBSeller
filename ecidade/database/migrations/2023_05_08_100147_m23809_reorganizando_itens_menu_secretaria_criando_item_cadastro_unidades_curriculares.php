<?php

use Illuminate\Database\Migrations\Migration;

class M23809ReorganizandoItensMenuSecretariaCriandoItemCadastroUnidadesCurriculares extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("update db_itensmenu set id_item = 9000 , descricao = 'Composiηγo Curricular' , help = 'Composiηγo Curricular' , itemativo = '1' , manutencao = '1' , desctec = 'Composiηγo Curricular' , libcliente = 'true' where id_item = 9000");
        DB::statement("delete from db_menu where id_item_filho = 228653 AND modulo = 7159");
        DB::statement("insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 9000 ,228653 ,2 ,7159 );");
        DB::statement("delete from db_menu where id_item_filho = 10397 AND modulo = 7159;");
        DB::statement("insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 9000 ,10397 ,1 ,7159 );");
        DB::statement("insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 9000 ,228918 ,3 ,7159 );");
        DB::statement("insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228918 ,'Unidades Curriculares' ,'Unidades Curriculares' ,'web/educacao/secretaria/cadastros/composicao-curricular/crud' ,'1' ,'1' ,'Cadastro de Unidades Curriculares' ,'true' );");
        DB::statement("update db_menu set menusequencia = 4 where id_item_filho = 9001");
        DB::statement("update db_menu set menusequencia = 5 where id_item_filho = 9001");
    }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
    public function down()
    {
        DB::statement("delete from db_menu where id_item_filho = 228918 AND modulo = 7159");
        DB::statement("delete from db_itensmenu where id_item = 228918;");
    }
}
