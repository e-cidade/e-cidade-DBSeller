<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26846AlteracaoTamanhoCampoComplementoTabelaAluno extends Migration
{
    public function upEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
ALTER TABLE escola.aluno ALTER COLUMN ed47_v_compl TYPE VARCHAR(70);
SQL
        );
    }

    public function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
update db_syscampo set nomecam = 'ed47_v_compl', conteudo = 'varchar(70)', descricao = 'Complemento do numero do endereco', valorinicial = '', rotulo = 'Complemento', nulo = 't', tamanho = 70, maiusculo = 't', autocompl = 'f', aceitatipo = 3, tipoobj = 'text', rotulorel = 'Complemento' where codcam = 1008870;
SQL
        );
    }

    public function downEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
ALTER TABLE escola.aluno ALTER COLUMN ed47_v_compl TYPE VARCHAR(20);
SQL
        );
    }

    public function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
update db_syscampo set nomecam = 'ed47_v_compl', conteudo = 'varchar(20)', descricao = 'Complemento do numero do endereco', valorinicial = '', rotulo = 'Complemento', nulo = 't', tamanho = 20, maiusculo = 't', autocompl = 'f', aceitatipo = 3, tipoobj = 'text', rotulorel = 'Complemento' where codcam = 1008870;
SQL
        );        
    }

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
}
