<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M21918ItemMenuDbprefImoveisMatricula extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {                  
        DB::statement("insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228829 ,'Imóveis Matrícula' ,'Pesquisa imóvel informando somente a matrícula' ,'digitamatriculanovomatricula.php' ,'1' ,'1' ,'Pesquisa imóvel informando somente a matrícula' ,'false' );");
        DB::statement("insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 5458 ,228829 ,11 ,5457 );");
        DB::statement("insert into configuracoes.db_permissao (id_usuario, id_item, permissaoativa, anousu, id_instit, id_modulo) values (325, 228829, 1, 2022, 1, 5457);");
        DB::statement("insert into configuracoes.db_menupref (m_codigo, m_descricao, m_arquivo, m_imgs, m_ativo, m_publico) values (19, 'Imóveis Matrícula', 'digitamatriculanovomatricula.php', null, '0', true);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("delete from db_itensmenu where id_item in (228829);");
        DB::statement("delete from db_menu where id_item_filho in (228829, 5457);");
        DB::statement("delete from configuracoes.db_permissao where id_item in (228829);");
        DB::statement("delete from configuracoes.db_menupref where m_codigo in (19);");
    }
}
