<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24348AdicionaDiversosD950 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
        -- Incluindo Diversos
        insert into pessoal.pesdiver select * from (select fc_anofolha(r07_instit) as ano, fc_mesfolha(r07_instit) as mes, 'D950', 'IRRF - Deducao', 528, r07_instit from pessoal.pesdiver group by r07_instit) as x where ano is not null and mes is not null;
        -- Incluindo Rubrica de diferenca
        insert into pessoal.rhrubricas (rh27_rubric, rh27_descr, rh27_instit, rh27_pd) select 'R957', 'Diferença de Deducoes', rh27_instit, 3 from pessoal.rhrubricas group by rh27_instit;
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
        delete from pessoal.pesdiver where r07_codigo = 'D950';
        delete from pessoal.rhrubricas where rh27_rubric = 'R957';
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
