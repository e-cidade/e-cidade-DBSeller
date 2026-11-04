<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29032AtualizacaoJustificativa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
 INSERT INTO justificativainstrumentoprevio (e174_sequencial, e174_descricao, e174_ativo) VALUES ( 9,'Convênios celebrados entre órgão públicos', true);
INSERT INTO justificativainstrumentoprevio (e174_sequencial, e174_descricao, e174_ativo) VALUES (10,'Contratação de pessoal por prazo determinado (CPD) não empenhado em elemento próprio', true);
INSERT INTO justificativainstrumentoprevio (e174_sequencial, e174_descricao, e174_ativo) VALUES (11,'Premiações culturais, Artísticas, Científicas e Desportivas', true);


INSERT INTO justificativaatojuridico (e173_sequencial, e173_descricao, e173_ativo) VALUES (13, 'Contratação de pessoal por prazo determinado (CPD)não empenhado em elemento próprio', true);
INSERT INTO justificativaatojuridico (e173_sequencial, e173_descricao, e173_ativo) VALUES (14, 'Premiações culturais, Artísticas, Científicas e Desportivas', true);

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
DELETE FROM justificativainstrumentoprevio WHERE e174_sequencial IN (9,10,11);
DELETE FROM justificativaatojuridico WHERE e173_sequencial IN (13,14);

SQL
        );
    }
}
