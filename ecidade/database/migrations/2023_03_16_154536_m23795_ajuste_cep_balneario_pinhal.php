<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M23795AjusteCepBalnearioPinhal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql  = <<<SQL
        update
	        configuracoes.cadendermunicipio
        set
	        db72_cepinicial = '95599000',
	        db72_cepfinal = '95599999'
        where
	        db72_sequencial in (
	    select
		    db72_sequencial
	    from
		    cadendermunicipio a
	    inner join configuracoes.cadenderestado on
		    db71_sequencial = a.db72_cadenderestado
		    and db71_sigla = 'RS'
	    inner join configuracoes.cadendermunicipiosistema on
		    db125_cadendermunicipio = db72_sequencial
		    and db125_db_sistemaexterno = 4
		    and db125_codigosistema is not null
	    where
		a.db72_descricao ilike '%Balneário Pinhal%');
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
        $sql  = <<<SQL
        SELECT 1;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}