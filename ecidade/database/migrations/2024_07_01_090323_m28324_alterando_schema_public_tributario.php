<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M28324AlterandoSchemaPublicTributario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
            alter table if exists parissqnfrequenciaatualizacaocadastros set schema issqn;
            alter table if exists certlancimov set schema cadastro;
            alter table if exists certforlaud set schema cadastro;
            alter table if exists certvalven set schema cadastro;
            alter table if exists arqsimplesreg set schema issqn;
            alter table if exists arqsimples set schema issqn;
            alter table if exists isscadsimplesatualizacoes set schema issqn;
            alter table if exists isscadsimplesatualizacoesinclusao set schema issqn;
            alter table if exists isscadsimplesatualizacoesbaixa set schema issqn;
            alter table if exists arqsimplesenvio set schema issqn;
            alter table if exists arqsimplesregcnae set schema issqn;
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
        // Sem reversao por conta de acerto de consistencia no banco de dados.
    }
}
