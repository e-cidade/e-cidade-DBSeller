<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24384AlteraCampoNoDicionarioDeDados extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
update db_syscampo set nomecam = 'fa08_i_cgsund', conteudo = 'int4', descricao = 'Requisitante', valorinicial = '0', rotulo = 'Requisitante', nulo = 't', tamanho = 10, maiusculo = 'f', autocompl = 'f', aceitatipo = 1, tipoobj = 'text', rotulorel = 'Requisitante' where codcam = 12221;
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
update db_syscampo set nomecam = 'fa08_i_cgsund', conteudo = 'int4', descricao = 'Requisitante', valorinicial = '0', rotulo = 'Requisitante', nulo = 't', tamanho = 7, maiusculo = 'f', autocompl = 'f', aceitatipo = 1, tipoobj = 'text', rotulorel = 'Requisitante' where codcam = 12221;
SQL
        );
    }
}
