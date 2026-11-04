<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26607ProcessoItbi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upAdicionaCampo();
        $this->dicionarioDeDados();
    }


    public function dicionarioDeDados()
    {
        DB::connection()->getPdo()->exec(
            <<<SQL
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;
        
        COMMENT ON COLUMN itbi.itbi.it01_protprocesso IS
          '{
              "descricao": "Chave primária da tabela",
              "rotulo": "Processo Sequencial",
              "rotulorel": "Processo Sequencial",
              "maiusculo": false,
              "autocompl": false,
              "aceitatipo": 1,
              "tamanho": 10,
              "tipoobj": "text"
           }';
        
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
        select configuracoes.fc_auditoria_cria_funcao('itbi.itbi');
SQL
        );
    }


    public function upAdicionaCampo()
    {

        DB::connection()->getPdo()->exec(
            <<<SQL
       ALTER TABLE itbi.itbi ALTER COLUMN it01_guia  SET DEFAULT  nextval('itbi_it01_guia_seq');
       ALTER TABLE itbi.itbi ADD COLUMN it01_protprocesso BIGINT;
       ALTER TABLE itbi.itbi ADD CONSTRAINT itbi_protprocesso_fk FOREIGN KEY (it01_protprocesso) REFERENCES protocolo.protprocesso (p58_codproc);
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
        Schema::table("itbi.itbi", function (Blueprint $table) {
            $table->dropColumn("it01_protprocesso");
        });
    }
}
