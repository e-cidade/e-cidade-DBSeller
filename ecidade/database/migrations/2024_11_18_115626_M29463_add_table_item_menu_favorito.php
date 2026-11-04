<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29463AddTableItemMenuFavorito extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upTableMenuFavorito();
        $this->upAuditoria();
        $this->upDicionarioDeDados();
    }

    public function upTableMenuFavorito()
    {
        Schema::create('configuracoes.itens_menu_usuario_favoritos', function (Blueprint $table) {
            $table->bigInteger('id_item_menu')->unsigned();
            $table->bigInteger('id_usuario')->unsigned();
            $table->bigInteger('id_insituicao')->unsigned();
            $table->bigInteger('modulo')->unsigned();
            $table->bigInteger('area')->unsigned();
            $table->foreign('id_item_menu')->references('id_item')->on('configuracoes.db_itensmenu');
            $table->foreign('id_usuario')->references('id_usuario')->on('configuracoes.db_usuarios');
            $table->foreign('id_insituicao')->references('codigo')->on('configuracoes.db_config');
            $table->primary(['id_item_menu', 'id_usuario', 'id_insituicao']);
        });
    }

    public function upAuditoria()
    {
        DB::statement('select public.fc_set_pg_search_path();');
        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('configuracoes.itens_menu_usuario_favoritos');");
    }

    public function upDicionarioDeDados()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            COMMENT ON TABLE configuracoes.itens_menu_usuario_favoritos IS
              '{
                  "descricao": "Tabela para o sistema de itens de menu favoritados",
                  "sigla": "",
                  "dataincl": "2024-11-18",
                  "rotulo": "Tabela para o sistema de mensageria",
                  "tipotabela": 0,
                  "naolibclass": false,
                  "naolibfunc": false,
                  "naolibprog": false,
                  "naolibform": false
               }';

            COMMENT ON COLUMN configuracoes.itens_menu_usuario_favoritos.id_item_menu IS '
                        {
                            "descricao": "Chave estrangeira da tabela de itens de menu",
                            "rotulo": "id_item_menu",
                            "rotulorel": "id_item_menu",
                            "maiusculo": false,
                            "autocompl": false,
                            "aceitatipo": 1,
                            "tamanho": 10,
                            "tipoobj": "text"
                        }';

            COMMENT ON COLUMN configuracoes.itens_menu_usuario_favoritos.id_usuario IS '
                        {
                            "descricao": "Chave estrangeira da tabela de usuarios",
                            "rotulo": "id_usuario",
                            "rotulorel": "id_usuario",
                            "maiusculo": false,
                            "autocompl": false,
                            "aceitatipo": 1,
                            "tamanho": 10,
                            "tipoobj": "text"
                        }';

            COMMENT ON COLUMN configuracoes.itens_menu_usuario_favoritos.id_insituicao IS '
                        {
                            "descricao": "Chave estrangeira da tabela de insituicoes",
                            "rotulo": "id_insituicao",
                            "rotulorel": "id_insituicao",
                            "maiusculo": false,
                            "autocompl": false,
                            "aceitatipo": 1,
                            "tamanho": 10,
                            "tipoobj": "text"
                        }';

            COMMENT ON COLUMN configuracoes.itens_menu_usuario_favoritos.modulo IS '
                        {
                            "descricao": "modulo onde fica o item de menu",
                            "rotulo": "modulo",
                            "rotulorel": "modulo",
                            "maiusculo": false,
                            "autocompl": false,
                            "aceitatipo": 1,
                            "tamanho": 10,
                            "tipoobj": "text"
                        }';

            COMMENT ON COLUMN configuracoes.itens_menu_usuario_favoritos.area IS '
                        {
                            "descricao": "area onde fica o item de menu",
                            "rotulo": "area",
                            "rotulorel": "area",
                            "maiusculo": false,
                            "autocompl": false,
                            "aceitatipo": 1,
                            "tamanho": 10,
                            "tipoobj": "text"
                        }';

            SELECT fc_gera_dicionario_apartir_tabela('configuracoes', 'itens_menu_usuario_favoritos');

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
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
        $this->downAuditoria();
        $this->downDicionarioDeDados();
        $this->downForeignKey();
        $this->downTableItensMenuFavoritos();
    }

    public function downAuditoria()
    {
        DB::statement("SELECT configuracoes.fc_auditoria_remove_funcao('configuracoes.itens_menu_usuario_favoritos');");
    }

    public function downDicionarioDeDados()
    {
        DB::connection()->getPdo()->exec(<<<SQL

            SELECT fc_remove_dicionario_tabela('configuracoes', 'itens_menu_usuario_favoritos');
SQL
        );
    }

    public function downForeignKey()
    {
        Schema::table('configuracoes.itens_menu_usuario_favoritos', function (Blueprint $table) {
            $table->dropForeign(['id_item_menu']);
            $table->dropForeign(['id_usuario']);
            $table->dropForeign(['id_insituicao']);
        });
    }

    public function downTableItensMenuFavoritos()
    {
        Schema::dropIfExists('configuracoes.itens_menu_usuario_favoritos');
    }
}
