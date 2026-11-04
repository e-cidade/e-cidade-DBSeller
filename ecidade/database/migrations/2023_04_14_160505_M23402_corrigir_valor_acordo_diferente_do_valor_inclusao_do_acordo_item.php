<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M23402CorrigirValorAcordoDiferenteDoValorInclusaoDoAcordoItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_acordo', function (Blueprint $table) {
            $table->integer('sequencial');
            $table->decimal('valor', 15, 2);
        });

        $acordosValorDiferente = DB::table('acordo')
            ->join('acordoposicao', 'acordo.ac16_sequencial', '=', 'acordoposicao.ac26_acordo')
            ->join('acordoitem', 'acordoposicao.ac26_sequencial', '=', 'acordoitem.ac20_acordoposicao')
            ->select('ac16_sequencial', 'ac16_valor', DB::raw('ROUND(SUM(ac20_valortotal),2) as soma_valortotal'))
            ->where('ac26_acordoposicaotipo', 1)
            ->groupBy('ac16_sequencial', 'ac16_valor')
            ->havingRaw('ROUND(SUM(ac20_valortotal), 2) <> ac16_valor')
            ->get();

        foreach ($acordosValorDiferente as $item) {
            DB::table('temp_acordo')->insert(
                ['sequencial' => $item->ac16_sequencial, 'valor' => $item->ac16_valor]
            );

            DB::table('acordo')
                ->where('ac16_sequencial', $item->ac16_sequencial)
                ->update(['ac16_valor' => $item->soma_valortotal]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableTemp = DB::table('temp_acordo')->get();

        foreach ($tableTemp as $item) {
            DB::table('acordo')
                ->where('ac16_sequencial', $item->sequencial)
                ->update(['ac16_valor' => $item->valor]);
        }

        Schema::drop('temp_acordo');
    }
}
