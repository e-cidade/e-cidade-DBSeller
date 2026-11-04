<?php

use Illuminate\Database\Migrations\Migration;

class AlteraDefinicoesDeCampoTabelaPlanoContratacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            UPDATE db_syscampo SET descricao = 'Código', rotulo = 'Código' WHERE codcam = 69717147;
            UPDATE db_syscampo SET descricao = 'Unidade', rotulo = 'Unidade' WHERE codcam = 124953584;
            UPDATE db_syscampo SET descricao = 'Ano', rotulo = 'Ano' WHERE codcam = 307934481;
            UPDATE db_syscampo SET descricao = 'Instituição', rotulo = 'Instituição' WHERE codcam = 169404082;
            UPDATE db_syscampo SET descricao = 'Status', rotulo = 'Status' WHERE codcam = 289507325;
            UPDATE db_syscampo SET descricao = 'PNCP', rotulo = 'PNCP' WHERE codcam = 254174170;
            UPDATE db_syscampo SET descricao = 'Data de Cadastro', rotulo = 'Data de Cadastro' WHERE codcam = 138413049;
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
        //
    }
}
