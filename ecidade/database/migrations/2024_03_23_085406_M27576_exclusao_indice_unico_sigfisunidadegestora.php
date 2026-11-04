<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27576ExclusaoIndiceUnicoSigfisunidadegestora extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
alter table contabilidade.sigfisunidadegestora drop constraint if exists contabilidade_sigfisunidadegestora_c179_codigo_c179_instit_uniq;
alter table contabilidade.sigfisunidadegestora drop constraint if exists contabilidade_sigfisunidadegestora_c179_codigo_unique;

create unique index contabilidade_sigfisunidadegestora_c179_instit_uniq on contabilidade.sigfisunidadegestora(c179_instit);        
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
create unique index contabilidade_sigfisunidadegestora_c179_codigo_c179_instit_uniq on contabilidade.sigfisunidadegestora(c179_codigo, c179_instit);
create unique index contabilidade_sigfisunidadegestora_c179_codigo_unique on contabilidade.sigfisunidadegestora(c179_codigo);
SQL
        );
    }
}
