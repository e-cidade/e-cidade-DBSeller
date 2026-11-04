<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25461AlteracaoTabelaSelecao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
        alter table pessoal.selecao alter column r44_where type text;
        update db_syscampo set nomecam = 'r44_where', conteudo = 'text', descricao = 'Condição de busca ao gerar relatório.', valorinicial = '', rotulo = 'Condição', nulo = 't', tamanho = 1, maiusculo = 't', autocompl = 'f', aceitatipo = 0, tipoobj = 'text', rotulorel = 'Condição' where codcam = 4474;
        update db_syscampo set nomecam = 'r44_desc1', conteudo = 'text', descricao = 'descricao 1', valorinicial = '', rotulo = 'descricao 1', nulo = 't', tamanho = 1, maiusculo = 't', autocompl = 'f', aceitatipo = 0, tipoobj = 'text', rotulorel = 'descricao 1' where codcam = 12367;
        update db_syscampo set nomecam = 'r44_desc2', conteudo = 'text', descricao = 'descricao 2', valorinicial = '', rotulo = 'descricao 2', nulo = 't', tamanho = 1, maiusculo = 't', autocompl = 'f', aceitatipo = 0, tipoobj = 'text', rotulorel = 'descricao 2' where codcam = 4475;

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
        alter table pessoal.selecao alter column r44_where type varchar(400);
        update db_syscampo set nomecam = 'r44_where', conteudo = 'varchar', descricao = 'Condição de busca ao gerar relatório.', valorinicial = '', rotulo = 'Condição', nulo = 't', tamanho = 200, maiusculo = 't', autocompl = 'f', aceitatipo = 0, tipoobj = 'text', rotulorel = 'Condição' where codcam = 4474;
        update db_syscampo set nomecam = 'r44_desc1', conteudo = 'varchar', descricao = 'descricao 1', valorinicial = '', rotulo = 'descricao 1', nulo = 't', tamanho = 200, maiusculo = 't', autocompl = 'f', aceitatipo = 0, tipoobj = 'text', rotulorel = 'descricao 1' where codcam = 12367;
        update db_syscampo set nomecam = 'r44_desc2', conteudo = 'varchar', descricao = 'descricao 2', valorinicial = '', rotulo = 'descricao 2', nulo = 't', tamanho = 200, maiusculo = 't', autocompl = 'f', aceitatipo = 0, tipoobj = 'text', rotulorel = 'descricao 2' where codcam = 4475;
        
SQL
       );
    }
}
