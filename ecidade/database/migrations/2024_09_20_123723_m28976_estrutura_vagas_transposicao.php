<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28976EstruturaVagasTransposicao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * @throws Exception
     */
    public function up()
    {
        $this->criaEstrutura();
        $this->criaItemMenu();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     * @throws Exception
     */
    public function down()
    {
        $this->dropaEstrutura();
        $this->dropaItemMenu();
    }

    private function criaEstrutura()
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS escola.vagastransposicao (
                ed350_codigo BIGSERIAL PRIMARY KEY,
                ed350_ano INTEGER NOT NULL,
                ed350_escola INTEGER NOT NULL,
                ed350_etapa INTEGER NOT NULL,
                ed350_vagas INTEGER NOT NULL DEFAULT 0,
                ed350_saldo INTEGER NOT NULL DEFAULT 0,
                ed350_vagaspcd INTEGER NOT NULL DEFAULT 0,
                ed350_saldopcd INTEGER NOT NULL DEFAULT 0,
                CONSTRAINT escola_vagastransposicao_ed350_escola_fk FOREIGN KEY (ed350_escola) REFERENCES escola.escola (ed18_i_codigo),
                CONSTRAINT escola_vagastransposicao_ed350_etapa_fk FOREIGN KEY (ed350_etapa) REFERENCES escola.serie (ed11_i_codigo),
                CONSTRAINT vagastransposicao_ano_escola_etapa_in UNIQUE (ed350_ano, ed350_escola, ed350_etapa)
            )
        ");

        // Habilitando triggers
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;");

        // Adicionando comentários à tabela
        DB::statement("COMMENT ON TABLE escola.vagastransposicao IS '{
            \"descricao\": \"Armazena as informações das vagas de transposição.\",
            \"sigla\": \"ed350\",
            \"dataincl\": \"2024-09-20\",
            \"rotulo\": \"vagastransposicao\",
            \"tipotabela\": 0,
            \"naolibclass\": false,
            \"naolibfunc\": false,
            \"naolibprog\": false,
            \"naolibform\": false
        }'");

        // Adicionando comentários às colunas
        DB::statement("COMMENT ON COLUMN escola.vagastransposicao.ed350_codigo IS '{
            \"descricao\": \"Chave primária da tabela\",
            \"rotulo\": \"ed350_codigo\",
            \"rotulorel\": \"ed350_codigo\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.vagastransposicao.ed350_ano IS '{
            \"descricao\": \"Armazena o ano das vagas\",
            \"rotulo\": \"ed350_ano\",
            \"rotulorel\": \"ed350_ano\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.vagastransposicao.ed350_escola IS '{
            \"descricao\": \"Armazena a escola que está fornecendo as vagas\",
            \"rotulo\": \"ed350_escola\",
            \"rotulorel\": \"ed350_escola\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.vagastransposicao.ed350_etapa IS '{
            \"descricao\": \"Armazena a etapa que está fornecendo as vagas\",
            \"rotulo\": \"ed350_etapa\",
            \"rotulorel\": \"ed350_etapa\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.vagastransposicao.ed350_vagas IS '{
            \"descricao\": \"Armazena a quantidade de vagas que está sendo fornecida\",
            \"rotulo\": \"ed350_vagas\",
            \"rotulorel\": \"ed350_vagas\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.vagastransposicao.ed350_saldo IS '{
            \"descricao\": \"Armazena o saldo de vagas que está sendo fornecida\",
            \"rotulo\": \"ed350_saldo\",
            \"rotulorel\": \"ed350_saldo\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.vagastransposicao.ed350_vagaspcd IS '{
            \"descricao\": \"Armazena a quantidade de vagas pcd que está sendo fornecida\",
            \"rotulo\": \"ed350_vagaspcd\",
            \"rotulorel\": \"ed350_vagaspcd\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.vagastransposicao.ed350_saldopcd IS '{
            \"descricao\": \"Armazena o saldo de vagas pcd que está sendo fornecida\",
            \"rotulo\": \"ed350_saldopcd\",
            \"rotulorel\": \"ed350_saldopcd\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::select("SELECT fc_gera_dicionario_apartir_tabela(?, ?)", ['escola', 'vagastransposicao']);

        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;");
    }

    /**
     * @throws Exception
     */
    private function criaItemMenu()
    {
        DB::beginTransaction();

        try {
            DB::table('db_itensmenu')->insert([
                'id_item' => 3000309,
                'descricao' => 'Vagas',
                'help' => 'Vagas',
                'funcao' => 'edu4_transposicaovagas.php',
                'itemativo' => '1',
                'manutencao' => '1',
                'desctec' => 'Cadastro de vagas para transposição de alunos para outras escolas da rede',
                'libcliente' => false
            ]);

            DB::table('db_itensmenu')->insert([
                'id_item' => 3000311,
                'descricao' => 'Transposição',
                'help' => 'Transposição',
                'funcao' => '',
                'itemativo' => '1',
                'manutencao' => '1',
                'desctec' => 'Menu de transposição',
                'libcliente' => false
            ]);

            DB::table('db_itensmenu')->insert([
                'id_item' => 3000312,
                'descricao' => 'Critérios de Designação',
                'help' => 'Critérios de Designação',
                'funcao' => 'edu4_criteriosdesignacao.php',
                'itemativo' => '1',
                'manutencao' => '1',
                'desctec' => 'Rotina para configuração dos critérios de designação',
                'libcliente' => false
            ]);

            DB::table('db_itensmenu')->insert([
                'id_item' => 3000313,
                'descricao' => 'Processar',
                'help' => 'Processar',
                'funcao' => 'edu4_processartransposicao.php',
                'itemativo' => '1',
                'manutencao' => '1',
                'desctec' => 'Rotina para processamento das designações e encerramento de inscrições',
                'libcliente' => false
            ]);

            DB::table('db_menu')->insert([
                'id_item' => 3444,
                'id_item_filho' => 3000311,
                'menusequencia' => 26,
                'modulo' => 7159
            ]);

            DB::table('db_menu')->insert([
                'id_item' => 3000311,
                'id_item_filho' => 3000309,
                'menusequencia' => 1,
                'modulo' => 7159
            ]);

            DB::table('db_menu')->insert([
                'id_item' => 3000311,
                'id_item_filho' => 3000312,
                'menusequencia' => 2,
                'modulo' => 7159
            ]);

            DB::table('db_menu')->insert([
                'id_item' => 3000311,
                'id_item_filho' => 3000313,
                'menusequencia' => 3,
                'modulo' => 7159
            ]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function dropaEstrutura()
    {
        DB::statement("DROP TABLE IF EXISTS escola.vagastransposicao");
    }

    /**
     * @throws Exception
     */
    private function dropaItemMenu()
    {
        DB::beginTransaction();
        try {
            DB::table('db_menu')
                ->whereIn('id_item_filho', [3000311, 3000309, 3000312, 3000313])
                ->where('modulo', 7159)
                ->delete();

            DB::table('db_itensmenu')
                ->whereIn('id_item', [3000311, 3000309, 3000312, 3000313])
                ->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
