<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28685AjusteSchemaTabelaMensageriaacordo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upAjustesTabelaMensageriaacordo();
        $this->upDicionarioDadosTabelaMensageriaAcordo();

        $this->addTabelaMensageriaAcordoDias();
        $this->upAuditoriaTabelaMensageriaAcordoDias();
        $this->upDicionarioDadosTabelaMensageriaAcordoDias();

        $this->upTabelaMensageriaAcordoUsuarioNotificados();
        $this->upAuditoriaTabelaMensageriaAcordoUsuarioNotificados();
        $this->upDicionarioDadosTabelaMensageriaAcordoUsuarioNotificados();
    }

    public function upAjustesTabelaMensageriaacordo()
    {
        Schema::table('acordos.mensageriaacordo', function (Blueprint $table) {
            $table->dropColumn('ac51_sequencial');
        });

        Schema::table('acordos.mensageriaacordo', function (Blueprint $table) {
            $table->bigIncrements('ac51_sequencial');
            $table->bigInteger('instituicao_id')->nullable();;
            $table->timestamps();
            $table->foreign('instituicao_id')->references('codigo')->on('db_config');
        });
    }

    public function upDicionarioDadosTabelaMensageriaAcordo()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            COMMENT ON COLUMN acordos.mensageriaacordo.instituicao_id IS '
                        {
                            "descricao": "Instituicao referente a configuracao",
                            "rotulo": "instituicao_id",
                            "rotulorel": "instituicao_id",
                            "maiusculo": false,
                            "autocompl": false,
                            "aceitatipo": 1,
                            "tamanho": 10,
                            "tipoobj": "text"
                        }';

            SELECT fc_gera_dicionario_apartir_tabela('acordos', 'mensageriaacordo');

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL
        );
    }

    public function addTabelaMensageriaAcordoDias()
    {
        Schema::create('acordos.mensageria_acordo_dias', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('dia');
            $table->bigInteger('mensageriaacordo_id')->nullable();;
            $table->timestamps();
            $table->foreign('mensageriaacordo_id')->references('ac51_sequencial')->on('acordos.mensageriaacordo');
        });
    }

    public function upAuditoriaTabelaMensageriaAcordoDias()
    {
        DB::statement('select public.fc_set_pg_search_path();');
        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('acordos.mensageria_acordo_dias');");
    }

    public function upDicionarioDadosTabelaMensageriaAcordoDias()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            COMMENT ON TABLE acordos.mensageria_acordo_dias IS
              '{
                  "descricao": "Tabela configuracao acordo notificar dia",
                  "sigla": "",
                  "dataincl": "2023-10-25",
                  "rotulo": "Tabela configuracao acordo notificar dia",
                  "tipotabela": 0,
                  "naolibclass": false,
                  "naolibfunc": false,
                  "naolibprog": false,
                  "naolibform": false
               }';

            COMMENT ON COLUMN acordos.mensageria_acordo_dias.dia IS '
                        {
                            "descricao": "Dias antecedencia que deve ser verificado os acordos",
                            "rotulo": "dia",
                            "rotulorel": "dia",
                            "maiusculo": false,
                            "autocompl": false,
                            "aceitatipo": 1,
                            "tamanho": 10,
                            "tipoobj": "text"
                        }';

            COMMENT ON COLUMN acordos.mensageria_acordo_dias.mensageriaacordo_id IS '
                        {
                            "descricao": "Chave estrangeira para mensageriaacordo",
                            "rotulo": "mensageriaacordo_id",
                            "rotulorel": "mensageriaacordo_id",
                            "maiusculo": false,
                            "autocompl": false,
                            "aceitatipo": 1,
                            "tamanho": 10,
                            "tipoobj": "text"
                        }';

            SELECT fc_gera_dicionario_apartir_tabela('acordos', 'mensageria_acordo_dias');

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL
        );
    }

    public function upTabelaMensageriaAcordoUsuarioNotificados()
    {
        Schema::create('acordos.mensageria_acordo_usuario_notificados', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('mensageria_acordo_dias')->nullable();
            $table->bigInteger('usuario_id')->nullable();
            $table->bigInteger('acordo_id')->nullable();
            $table->timestamps();
            $table->foreign('usuario_id')->references('id_usuario')->on('configuracoes.db_usuarios');
            $table->foreign('acordo_id')->references('ac16_sequencial')->on('acordos.acordo');
        });
    }

    public function upAuditoriaTabelaMensageriaAcordoUsuarioNotificados()
    {
        DB::statement('select public.fc_set_pg_search_path();');
        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('acordos.mensageria_acordo_usuario_notificados');");
    }

    public function upDicionarioDadosTabelaMensageriaAcordoUsuarioNotificados()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            COMMENT ON TABLE acordos.mensageria_acordo_usuario_notificados IS
              '{
                  "descricao": "Tabela que armazena usuário já notificados",
                  "sigla": "",
                  "dataincl": "2023-10-25",
                  "rotulo": "Tabela usuarios notifcados acordo",
                  "tipotabela": 0,
                  "naolibclass": false,
                  "naolibfunc": false,
                  "naolibprog": false,
                  "naolibform": false
               }';

            COMMENT ON COLUMN acordos.mensageria_acordo_usuario_notificados.mensageria_acordo_dias IS '
                        {
                            "descricao": "Dias com antecedencia que o usuario foi notificado",
                            "rotulo": "mensageria_acordo_dias_id",
                            "rotulorel": "mensageria_acordo_dias_id",
                            "maiusculo": false,
                            "autocompl": false,
                            "aceitatipo": 1,
                            "tamanho": 10,
                            "tipoobj": "text"
                        }';

            COMMENT ON COLUMN acordos.mensageria_acordo_usuario_notificados.usuario_id IS '
                        {
                            "descricao": "Chave estrangeira para tabela db_usuarios",
                            "rotulo": "usuario_id",
                            "rotulorel": "usuario_id",
                            "maiusculo": false,
                            "autocompl": false,
                            "aceitatipo": 1,
                            "tamanho": 10,
                            "tipoobj": "text"
                        }';

            COMMENT ON COLUMN acordos.mensageria_acordo_usuario_notificados.acordo_id IS '
                        {
                            "descricao": "Chave estrangeira para tabela acordos.acordo",
                            "rotulo": "acordo_id",
                            "rotulorel": "acordo_id",
                            "maiusculo": false,
                            "autocompl": false,
                            "aceitatipo": 1,
                            "tamanho": 10,
                            "tipoobj": "text"
                        }';


            SELECT fc_gera_dicionario_apartir_tabela('acordos', 'mensageria_acordo_usuario_notificados');

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
       $this->downAuditoriaTabelaMensageriaAcordoUsuarioNotificados();
       $this->downDicionarioDadosTabelaMensageriaAcordoUsuarioNotificados();
       $this->downTabelaMensageriaAcordoUsuarioNotificados();

       $this->downAuditoriaTabelaMensageriaAcordoDias();
       $this->downDicionarioDadosTabelaMensageriaAcordoDias();
       $this->downTabelaMensageriaAcordoDias();

       $this->downAjustesTabelaMensageriaacordo();
    }

    public function downAjustesTabelaMensageriaacordo() {
        Schema::table('acordos.mensageriaacordo', function (Blueprint $table) {
            $table->dropForeign(['instituicao_id']);
            $table->dropColumn(['instituicao_id']);
            $table->dropTimestamps();

            $table->integer('ac51_sequencial')->change();
        });
    }

    public function downAuditoriaTabelaMensageriaAcordoDias()
    {
        DB::statement("SELECT configuracoes.fc_auditoria_remove_funcao('acordos.mensageria_acordo_dias');");
    }

    public function downDicionarioDadosTabelaMensageriaAcordoDias()
    {
        DB::connection()->getPdo()->exec(<<<SQL
           SELECT fc_remove_dicionario_tabela('acordos', 'mensageria_acordo_dias');
SQL
        );
    }

    public function downTabelaMensageriaAcordoDias()
    {
        Schema::table('acordos.mensageria_acordo_dias', function (Blueprint $table) {
            $table->dropForeign(['mensageriaacordo_id']);
        });

        Schema::dropIfExists('acordos.mensageria_acordo_dias');
    }

    public function downAuditoriaTabelaMensageriaAcordoUsuarioNotificados()
    {
        DB::statement("SELECT configuracoes.fc_auditoria_remove_funcao('acordos.mensageria_acordo_usuario_notificados');");
    }

    public function downDicionarioDadosTabelaMensageriaAcordoUsuarioNotificados()
    {
        DB::connection()->getPdo()->exec(<<<SQL
           SELECT fc_remove_dicionario_tabela('acordos', 'mensageria_acordo_usuario_notificados');
SQL
        );
    }

    public function downTabelaMensageriaAcordoUsuarioNotificados()
    {
        Schema::table('acordos.mensageria_acordo_usuario_notificados', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['acordo_id']);
        });

        Schema::dropIfExists('mensageria_acordo_usuario_notificados');
    }
}
