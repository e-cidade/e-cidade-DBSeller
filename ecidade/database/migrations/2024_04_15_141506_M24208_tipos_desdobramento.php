<?php

use App\Domain\Financeiro\Tesouraria\Models\TipoDesdobramento;
use Illuminate\Database\Migrations\Migration;

class M24208TiposDesdobramento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        TipoDesdobramento::create(['k222_codigo' => 1, 'k222_descricao' => 'MDE']);
        TipoDesdobramento::create(['k222_codigo' => 2, 'k222_descricao' => 'ASPS']);
        TipoDesdobramento::create(['k222_codigo' => 3, 'k222_descricao' => 'FUNDEB']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement(<<<SQL
truncate caixa.tipodesdobramento CASCADE;
SQL
        );
    }
}
