<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23491AtualizaFaixaCep extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
        UPDATE configuracoes.cadendermunicipio SET db72_cepinicial = '92510001', db72_cepfinal = '92539999' where db72_sequencial in ( select db72_sequencial FROM cadendermunicipio a inner join configuracoes.cadenderestado on db71_sequencial = a.db72_cadenderestado and db71_sigla = 'RS' inner join configuracoes.cadendermunicipiosistema on db125_cadendermunicipio = db72_sequencial and db125_db_sistemaexterno = 4 and db125_codigosistema is not null where a.db72_descricao ilike '%Montenegro%');
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
        UPDATE configuracoes.cadendermunicipio SET db72_cepinicial = '95780000', db72_cepfinal = '95782999' where db72_sequencial in ( select db72_sequencial FROM cadendermunicipio a inner join configuracoes.cadenderestado on db71_sequencial = a.db72_cadenderestado and db71_sigla = 'RS' inner join configuracoes.cadendermunicipiosistema on db125_cadendermunicipio = db72_sequencial and db125_db_sistemaexterno = 4 and db125_codigosistema is not null where a.db72_descricao ilike '%Montenegro%');
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
