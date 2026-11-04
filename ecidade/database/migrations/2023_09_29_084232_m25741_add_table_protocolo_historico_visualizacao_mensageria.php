<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25741AddTableProtocoloHistoricoVisualizacaoMensageria extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upCriarTableHistoricoVisualizacao();
        $this->upForeignKey();
        $this->upAuditoria();
        $this->upDicionarioDeDados();
    }

    public function upCriarTableHistoricoVisualizacao()
    {
        Schema::create('protocolo.historico_visualizacao_mensageria', function (Blueprint $table) {
            $table->increments('p125_id');
            $table->bigInteger('p125_processo');
            $table->string('p125_cpfcnpj');
            $table->string('p125_nome');
            $table->timestamp('p125_data');
        });
    }

    public function upDicionarioDeDados()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            COMMENT ON TABLE protocolo.historico_visualizacao_mensageria IS
              '{
                  "descricao": "Tabela histórico de visualização",
                  "sigla": "p125",
                  "dataincl": "2023-10-25",
                  "rotulo": "Tabela histórico de visualização",
                  "tipotabela": 0,
                  "naolibclass": false,
                  "naolibfunc": false,
                  "naolibprog": false,
                  "naolibform": false
               }';

            COMMENT ON COLUMN protocolo.historico_visualizacao_mensageria.p125_id IS '
                        {
                            "descricao": "Chave primária da tabela",
                            "rotulo": "p125_id",
                            "rotulorel": "p125_id",
                            "maiusculo": false,
                            "autocompl": false,
                            "aceitatipo": 1,
                            "tamanho": 10,
                            "tipoobj": "text"
                        }';

            COMMENT ON COLUMN protocolo.historico_visualizacao_mensageria.p125_processo IS '
                        {
                            "descricao": "Id do processo referente as mensagens",
                            "rotulo": "p125_processo",
                            "rotulorel": "p125_processo",
                            "maiusculo": false,
                            "autocompl": false,
                            "aceitatipo": 1,
                            "tamanho": 10,
                            "tipoobj": "text"
                        }';

            COMMENT ON COLUMN protocolo.historico_visualizacao_mensageria.p125_cpfcnpj IS '
                        {
                            "descricao": "CPF/CNPJ do visualizador da mensagem",
                            "rotulo": "p125_cpfcnpj",
                            "rotulorel": "p125_cpfcnpj",
                            "maiusculo": false,
                            "autocompl": false,
                            "aceitatipo": 3,
                            "tamanho": 18,
                            "tipoobj": "text"
                        }';

            COMMENT ON COLUMN protocolo.historico_visualizacao_mensageria.p125_nome IS '
                        {
                            "descricao": "Nome do visualizador da mensagem",
                            "rotulo": "p125_nome",
                            "rotulorel": "p125_nome",
                            "maiusculo": false,
                            "autocompl": false,
                            "aceitatipo": 2,
                            "tamanho": 50,
                            "tipoobj": "text"
                        }';

            COMMENT ON COLUMN protocolo.historico_visualizacao_mensageria.p125_data IS '
                        {
                            "descricao": "Data da visualização",
                            "rotulo": "p125_data",
                            "rotulorel": "p125_data",
                            "maiusculo": false,
                            "autocompl": false,
                            "aceitatipo": 3,
                            "tamanho": 23,
                            "tipoobj": "text"
                        }';

            SELECT fc_gera_dicionario_apartir_tabela('protocolo', 'historico_visualizacao_mensageria');

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL
        );
    }

    public function upForeignKey()
    {
        Schema::table('protocolo.historico_visualizacao_mensageria', function (Blueprint $table) {
            $table->foreign('p125_processo')
                ->references('p58_codproc')
                ->on('protocolo.protprocesso');
        });
    }


    public function upAuditoria()
    {
        DB::statement('select public.fc_set_pg_search_path();');
        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('protocolo.historico_visualizacao_mensageria');");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downAuditoria();
        $this->downDicionario();
        $this->downForeignKey();
        $this->downTableHistoricoVisualizacao();
    }

    public function downAuditoria()
    {
        DB::statement("SELECT configuracoes.fc_auditoria_remove_funcao('protocolo.historico_visualizacao_mensageria');");
    }

    public function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL

            SELECT fc_remove_dicionario_tabela('protocolo', 'historico_visualizacao_mensageria');
SQL
        );
    }

    public function downForeignKey()
    {
        Schema::table('protocolo.historico_visualizacao_mensageria', function (Blueprint $table) {
            $table->dropForeign(['p125_processo']);
        });
    }

    public function downTableHistoricoVisualizacao()
    {
        Schema::dropIfExists('protocolo.historico_visualizacao_mensageria');
    }
}
