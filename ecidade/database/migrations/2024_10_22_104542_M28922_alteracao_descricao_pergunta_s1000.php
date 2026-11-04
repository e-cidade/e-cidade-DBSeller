<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28922AlteracaoDescricaoPerguntaS1000 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            update habitacao.avaliacaopergunta set db103_descricao = 'Indicador de tributação sobre a folha de pagamento - PIS/PASEP.', db103_identificadorcampo = 'indTribFolhaPisPasep' where db103_sequencial=4000371;
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
            update habitacao.avaliacaopergunta set db103_descricao = 'Indicador de tributação sobre a folha de pagamento - PIS e CONFINS.', db103_identificadorcampo = 'indTribFolhaPisCofins' where db103_sequencial=4000371;
SQL
        );
    }
}
