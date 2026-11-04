<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25741AddTableProtocoloMensageria extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upCriarTableMensageria();
        $this->upForeignKey();
        $this->upAuditoria();
        $this->upDicionarioDeDados();
        $this->upImportarMensagensAntigas();
    }

    public function upCriarTableMensageria()
    {
        Schema::create('protocolo.mensageria', function (Blueprint $table) {
            $table->increments('p123_id');
            $table->longText('p123_mensagem');
            $table->timestamp('p123_data_criacao')->useCurrent();
            $table->timestamp('p123_data_visualizada')->nullable();
            $table->bigInteger('p123_resposta_mensagem')->nullable();
            $table->boolean('p123_interna')->nullable();
            $table->bigInteger('p123_processo');
            $table->bigInteger('p123_despacho')->nullable();
            $table->string('p123_cpfcnpj');
            $table->string('p123_nome');
        });
    }

    public function upDicionarioDeDados()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            COMMENT ON TABLE protocolo.mensageria IS
              '{
                  "descricao": "Tabela para o sistema de mensageria",
                  "sigla": "p123",
                  "dataincl": "2023-10-25",
                  "rotulo": "Tabela para o sistema de mensageria",
                  "tipotabela": 0,
                  "naolibclass": false,
                  "naolibfunc": false,
                  "naolibprog": false,
                  "naolibform": false
               }';

            COMMENT ON COLUMN protocolo.mensageria.p123_id IS '
                        {
                            "descricao": "Chave primária da tabela",
                            "rotulo": "p123_id",
                            "rotulorel": "p123_id",
                            "maiusculo": false,
                            "autocompl": false,
                            "aceitatipo": 1,
                            "tamanho": 10,
                            "tipoobj": "text"
                        }';

            COMMENT ON COLUMN protocolo.mensageria.p123_mensagem IS '
                        {
                            "descricao": "Texto da mensagem",
                            "rotulo": "p123_mensagem",
                            "rotulorel": "p123_mensagem",
                            "maiusculo": false,
                            "autocompl": false,
                            "aceitatipo": 3,
                            "tamanho": 255,
                            "tipoobj": "text"
                        }';

            COMMENT ON COLUMN protocolo.mensageria.p123_data_criacao IS '
                        {
                            "descricao": "Data de criação da mensagem",
                            "rotulo": "p123_data_criacao",
                            "rotulorel": "p123_data_criacao",
                            "maiusculo": false,
                            "autocompl": false,
                            "aceitatipo": 3,
                            "tamanho": 23,
                            "tipoobj": "text"
                        }';

            COMMENT ON COLUMN protocolo.mensageria.p123_data_visualizada IS '
                        {
                            "descricao": "Data que a mensagem foi visualizada",
                            "rotulo": "p123_data_visualizada",
                            "rotulorel": "p123_data_visualizada",
                            "maiusculo": false,
                            "autocompl": false,
                            "aceitatipo": 3,
                            "tamanho": 23,
                            "tipoobj": "text"
                        }';

            COMMENT ON COLUMN protocolo.mensageria.p123_resposta_mensagem IS '
                        {
                            "descricao": "Id da mensagem que esta sendo respondida",
                            "rotulo": "p123_resposta_mensagem",
                            "rotulorel": "p123_resposta_mensagem",
                            "maiusculo": false,
                            "autocompl": false,
                            "aceitatipo": 1,
                            "tamanho": 10,
                            "tipoobj": "text"
                        }';

            COMMENT ON COLUMN protocolo.mensageria.p123_interna IS '
                        {
                            "descricao": "Boolean para verifica se a mensagem é interna",
                            "rotulo": "p123_interna",
                            "rotulorel": "p123_interna",
                            "maiusculo": false,
                            "autocompl": false,
                            "aceitatipo": 5,
                            "tamanho": 10,
                            "tipoobj": "text"
                        }';

            COMMENT ON COLUMN protocolo.mensageria.p123_processo IS '
                        {
                            "descricao": "Id do processo de origem da mensagem",
                            "rotulo": "p123_processo",
                            "rotulorel": "p123_processo",
                            "maiusculo": false,
                            "autocompl": false,
                            "aceitatipo": 1,
                            "tamanho": 10,
                            "tipoobj": "text"
                        }';

            COMMENT ON COLUMN protocolo.mensageria.p123_despacho IS '
                        {
                            "descricao": "Id do despacho que a mensagem foi transformada",
                            "rotulo": "p123_despacho",
                            "rotulorel": "p123_despacho",
                            "maiusculo": false,
                            "autocompl": false,
                            "aceitatipo": 1,
                            "tamanho": 10,
                            "tipoobj": "text"
                        }';

            COMMENT ON COLUMN protocolo.mensageria.p123_cpfcnpj IS '
                        {
                            "descricao": "CPF/CNPJ do remetente da mensagem",
                            "rotulo": "p123_cpfcnpj",
                            "rotulorel": "p123_cpfcnpj",
                            "maiusculo": false,
                            "autocompl": false,
                            "aceitatipo": 3,
                            "tamanho": 18,
                            "tipoobj": "text"
                        }';

            COMMENT ON COLUMN protocolo.mensageria.p123_nome IS '
                        {
                            "descricao": "Nome do remetente da mensagem",
                            "rotulo": "p123_nome",
                            "rotulorel": "p123_nome",
                            "maiusculo": false,
                            "autocompl": false,
                            "aceitatipo": 2,
                            "tamanho": 50,
                            "tipoobj": "text"
                        }';

            SELECT fc_gera_dicionario_apartir_tabela('protocolo', 'mensageria');

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL
        );
    }

    public function upImportarMensagensAntigas()
    {
        DB::connection()->getPdo()->exec(<<<SQL
        INSERT INTO protocolo.mensageria (p123_id, p123_mensagem, p123_processo, p123_data_criacao, p123_interna, p123_nome, p123_cpfcnpj, p123_resposta_mensagem, p123_data_visualizada)
        SELECT
    	protocolo.procandamint.p78_sequencial as codigo_mensagem,
        protocolo.procandamint.p78_despacho AS mensagem,
        protocolo.processosvinculados.p92_processopai  AS processo_pai,
        TO_TIMESTAMP(protocolo.procandamint.p78_data || ' ' || protocolo.procandamint.p78_hora, 'YYYY-MM-DD HH24:MI:SS') AS data_criacao,
        CASE
            WHEN protocolo.procandamint.p78_tipodespacho IN (1002, 1003) THEN TRUE
            ELSE FALSE
        END AS interna,
        CASE
            WHEN protocolo.procandamint.p78_tipodespacho IN (1002, 1003) THEN configuracoes.db_usuarios.nome
            ELSE protocolo.protprocesso.p58_requer
        END AS nome,
        CASE
            WHEN protocolo.procandamint.p78_tipodespacho IN (1002, 1003) THEN
            (SELECT protocolo.cgm.z01_cgccpf
             FROM configuracoes.db_usuarios
             INNER JOIN configuracoes.db_usuacgm ON
                configuracoes.db_usuacgm.id_usuario = configuracoes.db_usuarios.id_usuario
             INNER JOIN protocolo.cgm ON
                protocolo.cgm.z01_numcgm = configuracoes.db_usuacgm.cgmlogin LIMIT 1
            )
            ELSE protocolo.cgm.z01_cgccpf
        END AS cpfcnnpj,
        CASE  WHEN p78_tipodespacho IN (1002, 1000) THEN
                            (
                            SELECT
                                p78_sequencial
                            FROM
                                procandamint
                            WHERE
                                p78_codandam = p61_codandam
                                AND p78_tipodespacho IN (1001, 1,1003)
                            )
                        ELSE
                          NULL
                        END AS referencia_codigo,
        CASE WHEN p113_data_registro IS NULL THEN null ELSE p113_data_registro END AS mensagens_data_lida
	    FROM protocolo.protprocesso
	    INNER JOIN protocolo.processosvinculados ON
	        protocolo.processosvinculados.p92_processofilho = protocolo.protprocesso.p58_codproc
	    INNER JOIN protocolo.procandam ON
	        protocolo.procandam.p61_codproc = protocolo.processosvinculados.p92_processofilho
	    INNER JOIN protocolo.procandamint ON
	        protocolo.procandamint.p78_codandam = protocolo.procandam.p61_codandam
	    INNER JOIN protocolo.cgm ON
	        protocolo.cgm.z01_numcgm = protocolo.protprocesso.p58_numcgm
	    LEFT JOIN configuracoes.db_usuarios ON
	        configuracoes.db_usuarios.id_usuario = protocolo.procandamint.p78_usuario
	    LEFT JOIN configuracoes.db_usuacgm ON
	        configuracoes.db_usuacgm.id_usuario = configuracoes.db_usuarios.id_usuario
	    LEFT JOIN historicovisualizacaoprocandam
	            ON historicovisualizacaoprocandam.p113_procandamint_id = procandamint.p78_sequencial
	    ORDER BY codigo_mensagem ASC
SQL
        );

        $ultmoSeq = DB::selectOne('SELECT max(p123_id) + 1 as ultimo_seq FROM protocolo.mensageria');
        if (empty($ultmoSeq->ultimo_seq)) {
            return;
        }
        DB::connection()->getPdo()->exec("ALTER SEQUENCE mensageria_p123_id_seq RESTART WITH {$ultmoSeq->ultimo_seq}");
    }

    public function upForeignKey()
    {
        Schema::table('protocolo.mensageria', function (Blueprint $table) {
            $table->foreign('p123_resposta_mensagem')
                ->references('p123_id')
                ->on('protocolo.mensageria');
            $table->foreign('p123_processo')
                ->references('p58_codproc')
                ->on('protocolo.protprocesso');
            $table->foreign('p123_despacho')
                ->references('p78_sequencial')
                ->on('protocolo.procandamint');
        });
    }

    public function upAuditoria()
    {
        DB::statement('select public.fc_set_pg_search_path();');
        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('protocolo.mensageria');");
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
        $this->downTableMensageria();
    }

    public function downAuditoria()
    {
        DB::statement("SELECT configuracoes.fc_auditoria_remove_funcao('protocolo.mensageria');");
    }

    public function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL

            SELECT fc_remove_dicionario_tabela('protocolo', 'mensageria');
SQL
        );
    }

    public function downForeignKey()
    {
        Schema::table('protocolo.mensageria', function (Blueprint $table) {
            $table->dropForeign(['p123_resposta_mensagem']);
            $table->dropForeign(['p123_processo']);
            $table->dropForeign(['p123_despacho']);
        });
    }

    public function downTableMensageria()
    {
        Schema::dropIfExists('protocolo.mensageria');
    }
}
