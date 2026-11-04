<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28952EstruturaMatriculaEscolaAluno extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Criando ou alterando a tabela
        $this->criaAlteraTabela();
        // Adiciona comentários na tabela e colunas
        $this->addComentarios();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('escola.matriculaescolaaluno');
    }

    private function criaAlteraTabela()
    {
        // Aqui eu só crio a tabela se ela não existir no banco
        if (!Schema::hasTable('escola.matriculaescolaaluno')) {
            Schema::create('escola.matriculaescolaaluno', function (Blueprint $table) {
                $table->bigIncrements('ed365_codigo');
                $table->string('ed365_matricula', 50)->nullable();
                $table->date('ed365_datamatriculainicial')->nullable();
                $table->bigInteger('ed365_aluno');
                $table->bigInteger('ed365_escola');

                $table->foreign('ed365_escola', 'escola_matriculaescolaaluno_ed365_escola_fk')
                    ->references('ed18_i_codigo')
                    ->on('escola.escola')
                    ->onDelete('cascade');
                $table->foreign('ed365_aluno', 'escola_matriculaescolaaluno_ed365_aluno_fk')
                    ->references('ed47_i_codigo')
                    ->on('escola.aluno')
                    ->onDelete('cascade');
            });
        } else {
            // Como Nova Friburgo criou a tabela sem utilizar o prefixo, estou verificando se a tabela existe e inserindo o prefixo nas colunas
            $columns = DB::select("
                            SELECT column_name FROM information_schema.columns
                                WHERE table_name = 'matriculaescolaaluno'
                                    AND table_schema = 'escola'
                       ");

            foreach ($columns as $column) {
                if (substr($column->column_name, 0, 6) != 'ed365_') {
                    if ($column->column_name == 'matriculainicial') {
                        Schema::table('escola.matriculaescolaaluno', function (Blueprint $table) use ($column) {
                            $novoNome = 'ed365_data' . $column->column_name;
                            $table->renameColumn($column->column_name, $novoNome);
                        });
                    } else {
                        Schema::table('escola.matriculaescolaaluno', function (Blueprint $table) use ($column) {
                            $novoNome = 'ed365_' . $column->column_name;
                            $table->renameColumn($column->column_name, $novoNome);
                        });
                    }
                }
            }
        }
    }

    private function addComentarios()
    {
        // Habilitando triggers
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;");

        // Adicionando comentários à tabela
        DB::statement("COMMENT ON TABLE escola.matriculaescolaaluno IS '{
                \"descricao\": \"Armazena os dados da matricula inicial do aluno.\",
                \"sigla\": \"ed365\",
                \"dataincl\": \"2024-09-22\",
                \"rotulo\": \"matriculaescolaaluno\",
                \"tipotabela\": 1,
                \"naolibclass\": false,
                \"naolibfunc\": false,
                \"naolibprog\": false,
                \"naolibform\": false
            }'");

        // Adicionando comentários às colunas
        DB::statement("COMMENT ON COLUMN escola.matriculaescolaaluno.ed365_codigo IS '{
                \"descricao\": \"Chave primária da tabela\",
                \"rotulo\": \"ed365_codigo\",
                \"rotulorel\": \"ed365_codigo\",
                \"maiusculo\": false,
                \"autocompl\": false,
                \"aceitatipo\": 1,
                \"tamanho\": 18,
                \"tipoobj\": \"text\"
            }'");

        DB::statement("COMMENT ON COLUMN escola.matriculaescolaaluno.ed365_matricula IS '{
                \"descricao\": \"Armazena a matrícula inicial do aluno.\",
                \"rotulo\": \"ed365_matricula\",
                \"rotulorel\": \"ed365_matricula\",
                \"maiusculo\": false,
                \"autocompl\": false,
                \"aceitatipo\": 1,
                \"tamanho\": 18,
                \"tipoobj\": \"text\"
            }'");

        DB::statement("COMMENT ON COLUMN escola.matriculaescolaaluno.ed365_datamatriculainicial IS '{
                \"descricao\": \"Armazena a data da matrícula inicial do aluno.\",
                \"rotulo\": \"ed365_datamatriculainicial\",
                \"rotulorel\": \"ed365_datamatriculainicial\",
                \"maiusculo\": false,
                \"autocompl\": false,
                \"aceitatipo\": 3,
                \"tamanho\": 18,
                \"tipoobj\": \"text\"
            }'");

        DB::statement("COMMENT ON COLUMN escola.matriculaescolaaluno.ed365_aluno IS '{
                \"descricao\": \"Armazena o código do aluno.\",
                \"rotulo\": \"ed365_aluno\",
                \"rotulorel\": \"ed365_aluno\",
                \"maiusculo\": false,
                \"autocompl\": false,
                \"aceitatipo\": 1,
                \"tamanho\": 18,
                \"tipoobj\": \"text\"
            }'");

        DB::statement("COMMENT ON COLUMN escola.matriculaescolaaluno.ed365_escola IS '{
                \"descricao\": \"Armazena o código da escola.\",
                \"rotulo\": \"ed365_escola\",
                \"rotulorel\": \"ed365_escola\",
                \"maiusculo\": false,
                \"autocompl\": false,
                \"aceitatipo\": 1,
                \"tamanho\": 18,
                \"tipoobj\": \"text\"
            }'");

        DB::select("SELECT fc_gera_dicionario_apartir_tabela(?, ?)", ['escola', 'matriculaescolaaluno']);

        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;");
    }
}
