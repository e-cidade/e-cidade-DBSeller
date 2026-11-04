<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27234AlterarTipoCampoUnidadeDoUnidadespncp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionario();
        $this->upEstrutura();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downDicionario();
        $this->downEstrutura();
    }

    private function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
update db_syscampo set conteudo = 'varchar(100)', tamanho = 100 where codcam = 1014604;
update db_syscampo set conteudo = 'varchar(100)', tamanho = 100 where codcam = 1014719;
update db_syscampo set conteudo = 'varchar(100)', tamanho = 100 where codcam = 1014721;
SQL
        );
    }

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
update db_syscampo set conteudo = 'int4', tamanho = 10 where codcam = 1014604;
update db_syscampo set conteudo = 'int4', tamanho = 10 where codcam = 1014719;
update db_syscampo set conteudo = 'int4', tamanho = 10 where codcam = 1014721;
SQL
        );
    }

    private function upEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
ALTER TABLE compraspncp DROP CONSTRAINT pncp_compraspncp_pn03_unidade_foreign;
ALTER TABLE contratospncp DROP CONSTRAINT pncp_contratospncp_pn04_unidade_foreign;

ALTER TABLE pncp.unidadespncp ALTER COLUMN pn02_unidade type varchar(100);
ALTER TABLE pncp.contratospncp ALTER COLUMN pn04_unidade type varchar(100);
ALTER TABLE pncp.compraspncp ALTER COLUMN pn03_unidade type varchar(100);
SQL
        );

        Schema::table('pncp.compraspncp', function (Blueprint $table) {
            $table->foreign('pn03_unidade')->references('pn02_unidade')->on('unidadespncp');
        });

        Schema::table('pncp.contratospncp', function (Blueprint $table) {
            $table->foreign('pn04_unidade')->references('pn02_unidade')->on('unidadespncp');
        });
    }

    private function downEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
ALTER TABLE compraspncp DROP CONSTRAINT pncp_compraspncp_pn03_unidade_foreign;
ALTER TABLE contratospncp DROP CONSTRAINT pncp_contratospncp_pn04_unidade_foreign;

ALTER TABLE pncp.unidadespncp ALTER COLUMN pn02_unidade type integer using(pn02_unidade::integer);
ALTER TABLE pncp.contratospncp ALTER COLUMN pn04_unidade type integer using(pn04_unidade::integer);
ALTER TABLE pncp.compraspncp ALTER COLUMN pn03_unidade type integer using (pn03_unidade::integer);
SQL
        );

        Schema::table('pncp.compraspncp', function (Blueprint $table) {
            $table->foreign('pn03_unidade')->references('pn02_unidade')->on('unidadespncp');
        });

        Schema::table('pncp.contratospncp', function (Blueprint $table) {
            $table->foreign('pn04_unidade')->references('pn02_unidade')->on('unidadespncp');
        });
    }
}
