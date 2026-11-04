<?php

use Illuminate\Database\Migrations\Migration;

class M27572AjudaGrauDependente extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
        ALTER TABLE pessoal.configuracaoajudacusto ADD COLUMN rh311_grauparentesco text;
        ALTER TABLE pessoal.configuracaoajudacusto ADD COLUMN rh311_servidordependente boolean default false;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = <<<SQL
        ALTER TABLE pessoal.configuracaoajudacusto DROP COLUMN rh311_grauparentesco;
        ALTER TABLE pessoal.configuracaoajudacusto DROP COLUMN rh311_servidordependente ;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
