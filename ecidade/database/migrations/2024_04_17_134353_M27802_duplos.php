<?php

use Illuminate\Database\Migrations\Migration;

class M27802Duplos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionario();
    }

    public function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            delete from db_syscampodep where codcam = 195275384;
            insert into db_syscampodep values(195275384,'216');

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            SELECT fc_gera_dicionario_apartir_tabela('pessoal', 'ajudacusto');

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return true
     */
    public function down()
    {
        return true;
    }
}
