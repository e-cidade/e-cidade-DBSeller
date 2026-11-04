<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23385CriandoCampoMatriculaAnterior extends Migration
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
     g*
     * @return void
     */
    public function down()
    {
        $this->downDicionario();
        $this->downEstrutura();
    }

    private function upDicionario()
    {
        $sql = <<<SQL
        insert into configuracoes.db_syscampo values(1015074,'rh01_matriculaanterior','int4','Matricula anterior do servidor no eSocial','0', 'Matrícula Anterior eSocial',9,'t','f','f',1,'text','Matrícula Anterior eSocial');
        insert into configuracoes.db_sysarqcamp values(1153,1015074,28,0);
        update db_syscampo set nomecam = 'rh01_matriculaanterior', conteudo = 'varchar(30)', descricao = 'Matricula anterior do servidor no eSocial', valorinicial = '', rotulo = 'Matrícula Anterior eSocial', nulo = 't', tamanho = 30, maiusculo = 'f', autocompl = 'f', aceitatipo = 1, tipoobj = 'text', rotulorel = 'Matrícula Anterior eSocial' where codcam = 1015074;


SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    private function downDicionario()
    {
        $sql = <<<SQL
        delete from configuracoes.db_sysarqcamp where codcam = 1015074;
        delete from configuracoes.db_syscampo where codcam = 1015074;

SQL;
        DB::connection()->getPdo()->exec($sql);

    }

    private function upEstrutura()
    {
        $sql = <<<SQL
        ALTER TABLE pessoal.rhpessoal ADD COLUMN rh01_matriculaanterior varchar(30);
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    private function downEstrutura()
    {
        $sql = <<<SQL
            ALTER TABLE pessoal.rhpessoal DROP COLUMN rh01_matriculaanterior
SQL;
        DB::connection()->getPdo()->exec($sql);


    }
}
