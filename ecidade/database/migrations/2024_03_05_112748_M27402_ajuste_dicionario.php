<?php

use Illuminate\Database\Migrations\Migration;

class M27402AjusteDicionario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
alter table orcamento.orctiporecconvenio alter column o16_saldoaberturacp type numeric;
alter table orcamento.orctiporecconvenio alter column o16_saldoabertura type numeric;
update db_syscampo set aceitatipo = 4 where codcam = 3547;
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
        DB::statement(<<<SQL
update db_syscampo set aceitatipo = 4 where codcam = 3547;
SQL
        );
    }
}
