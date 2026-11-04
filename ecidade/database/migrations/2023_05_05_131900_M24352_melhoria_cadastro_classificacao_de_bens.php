<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24352MelhoriaCadastroClassificacaoDeBens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionario();
        $this->upTabelas();
    }

    public function upTabelas()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            alter table clabens add column t64_ativa boolean default true;
SQL
        );

    }

    public function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            insert into db_syscampo (codcam,nomecam,conteudo,descricao,valorinicial,rotulo,tamanho,nulo,
                                     maiusculo,autocompl,aceitatipo,tipoobj,rotulorel)
                         values (1015042,'t64_ativa','bool','Campo para informar se classificacao esta ativa ou nao',
                                 't','Status da classificacao',1,'f','f','f',5,'text','Status da classificacao');
            insert into db_sysarqcamp values(925,1015042,10,0);
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
        $this->downDicionario();
        $this->downTabelas();
    }

    public function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            delete from db_sysarqcamp where codcam = 1015042;
            delete from db_syscampo where codcam = 1015042;
SQL
        );
    }

    public function downTabelas()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            alter table clabens drop column t64_ativa;
SQL
        );
    }
}
