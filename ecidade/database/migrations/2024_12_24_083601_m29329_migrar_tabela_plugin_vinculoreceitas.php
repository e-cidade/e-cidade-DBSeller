<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29329MigrarTabelaPluginVinculoreceitas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!$this->checkTableExists()) {
            return;
        }

        DB::unprepared("
            INSERT INTO caixa.vinculoreceitaprocedencia (k202_receita,k202_procdiver)
            SELECT receita, procdiver FROM plugins.vinculoreceitaprocedencia;
        ");
    }

    private function checkTableExists()
    {
        $tableExists = DB::select("
            SELECT EXISTS (
                SELECT 1 FROM information_schema.tables
                WHERE
                    table_schema = 'plugins'
                    AND table_name = 'vinculoreceitaprocedencia'
            )
       ");

       return $tableExists[0]->exists;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!$this->checkTableExists()) {
            return;
        }

        DB::unprepared("
            DELETE FROM caixa.vinculoreceitaprocedencia WHERE k202_procdiver
            IN (SELECT procdiver FROM plugins.vinculoreceitaprocedencia);
        ");
    }
}
