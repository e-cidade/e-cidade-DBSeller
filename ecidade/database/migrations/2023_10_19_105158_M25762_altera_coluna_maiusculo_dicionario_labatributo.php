<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25762AlteraColunaMaiusculoDicionarioLabatributo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL

            update db_syscampo set nomecam = 'la25_formula', conteudo = 'varchar(100)', descricao = 'Formulá para calcular resultados de atributos de exames', valorinicial = '', rotulo = 'Fórmula', nulo = 't', tamanho = 100, maiusculo = 'f', autocompl = 'f', aceitatipo = 0, tipoobj = 'text', rotulorel = 'Fórmula' where codcam = 1010783;

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

            update db_syscampo set nomecam = 'la25_formula', conteudo = 'varchar(100)', descricao = 'Formulá para calcular resultados de atributos de exames', valorinicial = '', rotulo = 'Fórmula', nulo = 't', tamanho = 100, maiusculo = 't', autocompl = 'f', aceitatipo = 0, tipoobj = 'text', rotulorel = 'Fórmula' where codcam = 1010783;

SQL
        );
    }
}
