<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26911EstruturaControleafastamento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $ControleAfastamento = DB::table('pessoal.controleafastamento')
                         ->select(DB::raw('count(*) as total'))
                                     ->groupBy('rh231_afastamento',
                                         'rh231_rubrica',
                                         'rh231_tabelaprevidencia',
                                         'rh231_instituicao',
                                         'rh231_ano',
                                         'rh231_mes'
                                     )
                                     ->havingRaw('count(*) > ?', [1])
                                     ->get();

        if ($ControleAfastamento->count() > 0) {
            DB::connection()->getPdo()->exec(<<<SQL
               drop table if exists ajuste_controleafastamento;

               create table ajuste_controleafastamento as
               select min(rh231_sequencial) as rh231_sequencial,
                      rh231_afastamento,
                      rh231_rubrica,
                      rh231_tabelaprevidencia,
                      rh231_instituicao,
                      rh231_ano,
                      rh231_mes
               from controleafastamento
               group by rh231_instituicao,
                        rh231_ano,
                        rh231_mes,
                        rh231_tabelaprevidencia,
                        rh231_afastamento,
                        rh231_rubrica;

               truncate controleafastamento;

               insert into controleafastamento
                  select * from ajuste_controleafastamento;

               drop index if exists controleafastamento_rh231_sequencial_in;
               create unique index IF NOT EXISTS controleafastamento_instit_ano_mes_tabprev_afast_rub_uk
                  on controleafastamento (rh231_instituicao, rh231_ano, rh231_mes, rh231_tabelaprevidencia,
                                          rh231_afastamento, rh231_rubrica);

               ANALYZE controleafastamento;
SQL
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            drop index if exists controleafastamento_instit_ano_mes_tabprev_afast_rub_uk;
SQL
        );
    }
}
