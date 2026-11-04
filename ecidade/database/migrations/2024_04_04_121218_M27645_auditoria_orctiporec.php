<?php

use Illuminate\Database\Migrations\Migration;

class M27645AuditoriaOrctiporec extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
select configuracoes.fc_auditoria_cria_funcao('orcamento.orctiporec');
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
         DB::connection()->getPdo()->exec(<<<SQL
select configuracoes.fc_auditoria_remove_funcao('orcamento.orctiporec');
SQL
         );
    }
}
