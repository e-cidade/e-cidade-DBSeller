<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28976AlterTableCandidatoTransposicao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            ALTER TABLE escola.candidatotransposicao
                ADD COLUMN ed349_etapaorigem BIGINT NOT NULL;
        ");

        DB::statement("
            ALTER TABLE escola.candidatotransposicao
                ADD CONSTRAINT transposicao_ed349_etapaorigem_fk
                FOREIGN KEY (ed349_etapaorigem)
                REFERENCES escola.serie (ed11_i_codigo);
        ");

        // Habilitando triggers
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;");

        // Adicionando comentários à coluna criada
        DB::statement("COMMENT ON COLUMN escola.candidatotransposicao.ed349_etapaorigem IS '{
            \"descricao\": \"Armazena o código da etapa de origem\",
            \"rotulo\": \"ed349_etapaorigem\",
            \"rotulorel\": \"ed349_etapaorigem\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::select("SELECT fc_gera_dicionario_apartir_tabela(?, ?)", ['escola', 'candidatotransposicao']);

        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("
            ALTER TABLE escola.candidatotransposicao
                DROP CONSTRAINT IF EXISTS transposicao_ed349_etapaorigem_fk;
        ");

        DB::statement("
            ALTER TABLE escola.candidatotransposicao
                DROP COLUMN IF EXISTS ed349_etapaorigem;
        ");
    }
}
