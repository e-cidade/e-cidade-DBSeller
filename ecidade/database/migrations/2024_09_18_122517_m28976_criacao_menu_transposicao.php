<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28976CriacaoMenuTransposicao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->criaItemMenu();
        $this->criaEstrutura();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropaItemMenu();
        $this->dropaEstrutura();
    }

    private function criaItemMenu()
    {
        DB::table('db_itensmenu')->insert([
            'id_item' => 229305,
            'descricao' => 'Transposição/Inscrição',
            'help' => 'Transposição/Inscrição',
            'funcao' => 'edu4_transposicao001.php',
            'itemativo' => '1',
            'manutencao' => '1',
            'desctec' => 'Inscrição de alunos para transposição de escolas da rede',
            'libcliente' => false
        ]);

        DB::table('db_menu')->insert([
            'id_item' => 1101100,
            'id_item_filho' => 229305,
            'menusequencia' => 6,
            'modulo' => 1100747
        ]);
    }

    private function dropaItemMenu()
    {
        DB::table('db_menu')
            ->where('id_item_filho', 229305)
            ->where('modulo', 1100747)
            ->delete();

        DB::table('db_itensmenu')
            ->where('id_item', 229305)
            ->delete();
    }

    private function criaEstrutura()
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS escola.candidatotransposicao (
                ed349_codigo BIGSERIAL PRIMARY KEY,
                ed349_aluno BIGINT NOT NULL,
                ed349_etapa BIGINT NOT NULL,
                ed349_ano INTEGER NOT NULL,
                ed349_escolaorigem BIGINT NOT NULL,
                ed349_escola1 BIGINT,
                ed349_irmao1 BIGINT,
                ed349_escola2 BIGINT,
                ed349_irmao2 BIGINT,
                ed349_escola3 BIGINT,
                ed349_irmao3 BIGINT,
                ed349_servidor1 BIGINT,
                ed349_servidor2 BIGINT,
                ed349_servidor3 BIGINT,
                ed349_matricula BIGINT,
                CONSTRAINT transposicao_ed349_matricula_fk FOREIGN KEY (ed349_matricula) REFERENCES escola.matricula (ed60_i_codigo) ON DELETE CASCADE,
                CONSTRAINT transposicao_ed349_aluno_fk FOREIGN KEY (ed349_aluno) REFERENCES escola.aluno (ed47_i_codigo) ON DELETE CASCADE,
                CONSTRAINT transposicao_ed349_etapa_fk FOREIGN KEY (ed349_etapa) REFERENCES escola.serie (ed11_i_codigo),
                CONSTRAINT transposicao_ed349_escolaorigem_fk FOREIGN KEY (ed349_escolaorigem) REFERENCES escola.escola (ed18_i_codigo),
                CONSTRAINT transposicao_ed349_escola1_fk FOREIGN KEY (ed349_escola1) REFERENCES escola.escola (ed18_i_codigo),
                CONSTRAINT transposicao_ed349_escola2_fk FOREIGN KEY (ed349_escola2) REFERENCES escola.escola (ed18_i_codigo),
                CONSTRAINT transposicao_ed349_escola3_fk FOREIGN KEY (ed349_escola3) REFERENCES escola.escola (ed18_i_codigo),
                CONSTRAINT transposicao_ed349_irmao1_fk FOREIGN KEY (ed349_irmao1) REFERENCES escola.aluno (ed47_i_codigo) ON DELETE CASCADE,
                CONSTRAINT transposicao_ed349_irmao2_fk FOREIGN KEY (ed349_irmao2) REFERENCES escola.aluno (ed47_i_codigo) ON DELETE CASCADE,
                CONSTRAINT transposicao_ed349_irmao3_fk FOREIGN KEY (ed349_irmao3) REFERENCES escola.aluno (ed47_i_codigo) ON DELETE CASCADE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Habilitando triggers
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;");

        // Adicionando comentários à tabela
        DB::statement("COMMENT ON TABLE escola.candidatotransposicao IS '{
            \"descricao\": \"Armazena as informações dos alunos que fizeram a transposição.\",
            \"sigla\": \"ed349\",
            \"dataincl\": \"2024-09-19\",
            \"rotulo\": \"candidatotransposicao\",
            \"tipotabela\": 0,
            \"naolibclass\": false,
            \"naolibfunc\": false,
            \"naolibprog\": false,
            \"naolibform\": false
        }'");

        // Adicionando comentários às colunas
        DB::statement("COMMENT ON COLUMN escola.candidatotransposicao.ed349_codigo IS '{
            \"descricao\": \"Chave primária da tabela\",
            \"rotulo\": \"ed349_codigo\",
            \"rotulorel\": \"ed349_codigo\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.candidatotransposicao.ed349_aluno IS '{
            \"descricao\": \"ID do aluno que está fazendo a transposição\",
            \"rotulo\": \"ed349_aluno\",
            \"rotulorel\": \"ed349_aluno\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.candidatotransposicao.ed349_etapa IS '{
            \"descricao\": \"Código da etapa para qual está indo\",
            \"rotulo\": \"ed349_etapa\",
            \"rotulorel\": \"ed349_etapa\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.candidatotransposicao.ed349_ano IS '{
            \"descricao\": \"Ano da transposição\",
            \"rotulo\": \"ed349_ano\",
            \"rotulorel\": \"ed349_ano\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.candidatotransposicao.ed349_escolaorigem IS '{
            \"descricao\": \"Escola de origem\",
            \"rotulo\": \"ed349_escolaorigem\",
            \"rotulorel\": \"ed349_escolaorigem\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.candidatotransposicao.ed349_escola1 IS '{
            \"descricao\": \"Primeira opção de escola de destino\",
            \"rotulo\": \"ed349_escola1\",
            \"rotulorel\": \"ed349_escola1\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.candidatotransposicao.ed349_escola2 IS '{
            \"descricao\": \"Segunda opção de escola de destino\",
            \"rotulo\": \"ed349_escola2\",
            \"rotulorel\": \"ed349_escola2\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.candidatotransposicao.ed349_escola3 IS '{
            \"descricao\": \"Terceira opção de escola de destino\",
            \"rotulo\": \"ed349_escola3\",
            \"rotulorel\": \"ed349_escola3\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.candidatotransposicao.ed349_irmao1 IS '{
            \"descricao\": \"Código do irmão na primeira opção de escola de destino\",
            \"rotulo\": \"ed349_irmao1\",
            \"rotulorel\": \"ed349_irmao1\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.candidatotransposicao.ed349_irmao2 IS '{
            \"descricao\": \"Código do irmão na segunda opção de escola de destino\",
            \"rotulo\": \"ed349_irmao2\",
            \"rotulorel\": \"ed349_irmao2\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.candidatotransposicao.ed349_irmao3 IS '{
            \"descricao\": \"Código do irmão na terceira opção de escola de destino\",
            \"rotulo\": \"ed349_irmao3\",
            \"rotulorel\": \"ed349_irmao3\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.candidatotransposicao.ed349_servidor1 IS '{
            \"descricao\": \"Código do primeiro servidor\",
            \"rotulo\": \"ed349_servidor1\",
            \"rotulorel\": \"ed349_servidor1\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.candidatotransposicao.ed349_servidor2 IS '{
            \"descricao\": \"Código do primeiro servidor\",
            \"rotulo\": \"ed349_servidor2\",
            \"rotulorel\": \"ed349_servidor2\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.candidatotransposicao.ed349_servidor3 IS '{
            \"descricao\": \"Código do primeiro servidor\",
            \"rotulo\": \"ed349_servidor3\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"rotulorel\": \"ed349_servidor3\",
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.candidatotransposicao.ed349_matricula IS '{
            \"descricao\": \"Armazena a matrícula do aluno\",
            \"rotulo\": \"ed349_matricula\",
            \"rotulorel\": \"ed349_matricula\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.candidatotransposicao.created_at IS '{
            \"descricao\": \"Armazena data e hora da inclusão do registro\",
            \"rotulo\": \"created_at\",
            \"rotulorel\": \"created_at\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.candidatotransposicao.updated_at IS '{
            \"descricao\": \"Armazena data e hora da alteração do registro\",
            \"rotulo\": \"updated_at\",
            \"rotulorel\": \"updated_at\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::select("SELECT fc_gera_dicionario_apartir_tabela(?, ?)", ['escola', 'candidatotransposicao']);

        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;");
    }

    private function dropaEstrutura()
    {
        DB::statement('DROP TABLE IF EXISTS escola.candidatotransposicao');
    }
}
