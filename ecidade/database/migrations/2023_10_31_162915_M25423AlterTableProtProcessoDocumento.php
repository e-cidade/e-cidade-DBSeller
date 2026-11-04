<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25423AlterTableProtProcessoDocumento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
        select public.fc_set_pg_search_path();
        SELECT configuracoes.fc_auditoria_cria_funcao('protocolo.protprocessodocumento');
        ALTER TABLE protocolo.protprocessodocumento ALTER COLUMN p01_sequencial SET DEFAULT  nextval('protprocessodocumento_p01_sequencial_seq');
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
        select public.fc_set_pg_search_path();
        SELECT configuracoes.fc_auditoria_remove_funcao('protocolo.protprocessodocumento');
        ALTER TABLE protocolo.protprocessodocumento ALTER COLUMN p01_sequencial SET DEFAULT  null;
SQL
        );
    }
}
