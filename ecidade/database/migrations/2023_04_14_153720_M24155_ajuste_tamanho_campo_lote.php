<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M24155AjusteTamanhoCampoLote extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upEstrutura();
        $this->upDicionario();
    }

    private function upEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
        ALTER TABLE itbidadosimovel ALTER COLUMN it22_lote TYPE varchar(10);
SQL
    );
    }

    private function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
        update db_syscampo set nomecam = 'it22_lote', conteudo = 'varchar(10)', descricao = 'Identificacao do Lote', valorinicial = '', rotulo = 'Lote', nulo = 't', tamanho = 10, maiusculo = 't', autocompl = 'f', aceitatipo = 0, tipoobj = 'text', rotulorel = 'Lote' where codcam = 9005;
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
        $this->downEstrutura();
        $this->downDicionario();
    }

    private function downEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
        ALTER TABLE itbidadosimovel ALTER COLUMN it22_lote TYPE character(4);
SQL
    );
    }

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
        ALTER TABLE itbidadosimovel ALTER COLUMN it22_lote TYPE character(4);
        update db_syscampo set nomecam = 'it22_lote', conteudo = 'character(4)', descricao = 'Identificacao do Lote', valorinicial = '', rotulo = 'Lote', nulo = 't', tamanho = 10, maiusculo = 't', autocompl = 'f', aceitatipo = 0, tipoobj = 'text', rotulorel = 'Lote' where codcam = 9005;
SQL
    );
    }
}
