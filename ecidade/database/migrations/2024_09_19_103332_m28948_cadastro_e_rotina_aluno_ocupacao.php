<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28948CadastroERotinaAlunoOcupacao extends Migration
{
    protected $cidadao = 'ouvidoria.cidadao';
    protected $aluno = 'escola.aluno';
    protected $ocupacao = 'escola.cboocupacao';

    var $existMobase = false;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        $this->adicionarRotina();
        $this->criaTabelaOcupacao();
        $this->alteraTabelaAluno();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        $this->deletarRotina();
        $this->dropaAlteracoesTabelaAluno();
        $this->dropaTabelaOcupacao();
    }

    /**
     * Rotina Nova
     * 
     */
    public function adicionarRotina()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu(id_item, descricao, help, funcao, itemativo, manutencao, desctec, libcliente)
values (3000337, 'Profissões', '#', '#', '1', '1', 'Cadastro / Edição / Consultas de Profissões', 'true'),
       (3000338, 'Inclusão', '#', 'edu1_ocupacoes_inclusao.php', '1', '1', 'Cadastro de Profissões', 'true'),
       (3000339, 'Alteração / Exclusão', '#', 'edu1_ocupacoes.php', '1', '1', 'Edição e Consultas de Profissões', 'false');

insert into db_menu(id_item, id_item_filho, menusequencia, modulo)
values (3470, 3000337, 27, 7159),
       (3000337, 3000338, 1, 7159),
       (3000337, 3000339, 2, 7159);
SQL
            );
    }

    public function deletarRotina()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_menu where id_item_filho in (3000337, 3000338, 3000339);
delete from db_itensmenu where id_item in (3000337, 3000338, 3000339);
SQL
            );
    }

    /**
     * Criar tabela escola.cboocupacao
     */
    public function criaTabelaOcupacao()
    {
        Schema::create($this->ocupacao, function (Blueprint $table) {
            $table->bigIncrements('mo304_codigo'); // (Primary Key)
            $table->string('mo304_titulo', 255);
        });

        // Habilitando triggers
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;");

        // Adicionando comentários para a tabela
        DB::statement("COMMENT ON TABLE escola.cboocupacao IS '{
            \"descricao\": \"Guarda uma tabela cbo para o modulo escola, esta tendo como diferencia a questao de que ela pode ser atualizada e alterada\",
            \"sigla\": \"mo304\",
            \"dataincl\": \"2024-09-19\",
            \"rotulo\": \"cboocupacao\",
            \"tipotabela\": 0,
            \"naolibclass\": false,
            \"naolibfunc\": false,
            \"naolibprog\": false,
            \"naolibform\": false
        }'");

        // Adicionando comentários para as colunas
        DB::statement("COMMENT ON COLUMN escola.cboocupacao.mo304_codigo IS '{
            \"descricao\": \"Chave primaria da tabela\",
            \"rotulo\": \"mo304_codigo\",
            \"rotulorel\": \"mo304_codigo\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 10,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.cboocupacao.mo304_titulo IS '{
            \"descricao\": \"Titulo da profissao selecionada\",
            \"rotulo\": \"mo304_titulo\",
            \"rotulorel\": \"mo304_titulo\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 3,
            \"tamanho\": 255,
            \"tipoobj\": \"text\"
        }'");

        DB::select("SELECT fc_gera_dicionario_apartir_tabela(?, ?)", ['escola', 'cboocupacao']);

        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;");
    }

    public function dropaTabelaOcupacao()
    {
        foreach (
            [
                $this->ocupacao,
            ] as $table
        ) {
            Schema::dropIfExists($table);
        }
    }

    /**
     * Alterar tabela aluno / mobase
     */
    public function alteraTabelaAluno()
    {
        Schema::table($this->cidadao, function (Blueprint $table) {
            $table->bigInteger('ov02_ocupacao')->unsigned()->nullable();
            $table->foreign('ov02_ocupacao')
                ->references('mo304_codigo')
                ->on('escola.cboocupacao');
        });

        Schema::table($this->aluno, function (Blueprint $table) {
            $table->bigInteger('ed47_i_ocupacaopai')->unsigned()->nullable();
            $table->foreign('ed47_i_ocupacaopai')
                ->references('mo304_codigo')
                ->on('escola.cboocupacao');

            $table->bigInteger('ed47_i_ocupacaomae')->unsigned()->nullable();
            $table->foreign('ed47_i_ocupacaomae')
                ->references('mo304_codigo')
                ->on('escola.cboocupacao');

            $table->bigInteger('ed47_i_ocupacaoresp')->unsigned()->nullable();
            $table->foreign('ed47_i_ocupacaoresp')
                ->references('mo304_codigo')
                ->on('escola.cboocupacao');
        });

    }

    public function dropaAlteracoesTabelaAluno()
    {
        Schema::table($this->aluno, function (Blueprint $table) {
            foreach (
                [
                    'ed47_i_ocupacaopai',
                    'ed47_i_ocupacaomae',
                    'ed47_i_ocupacaoresp',
                ] as $column
            ) {
                $tableName = str_replace('.', '_', $table->getTable());

                $table->dropForeign($tableName . '_' . $column . '_foreign');
                $table->dropColumn($column);
            }
        });

        Schema::table($this->cidadao, function (Blueprint $table) {
            $tableName = str_replace('.', '_', $table->getTable());
            $table->dropForeign($tableName . '_ov02_ocupacao_foreign');
            $table->dropColumn('ov02_ocupacao');
        });
    }
}
