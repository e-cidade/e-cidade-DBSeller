<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M20021RemovendoFkAdmissaopreliminar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $this->upDicionario();
        
        $sql = <<<SQL
            ALTER TABLE esocial.avaliacaogruporespostaadmissaopreliminar
                DROP CONSTRAINT IF EXISTS avaliacaogruporespostaadmissaopreliminar_regist_fk;
            
            -- Permite que o usuário preencha o campo matricula.
            update habitacao.avaliacaopergunta set db103_somenteleitura = false where db103_sequencial = 4000299;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downDicionario();
       
       $sql = <<<SQL
            ALTER TABLE esocial.avaliacaogruporespostaadmissaopreliminar
        ADD CONSTRAINT avaliacaogruporespostaadmissaopreliminar_regist_fk FOREIGN KEY (eso18_regist)
        REFERENCES rhpessoal;

        update habitacao.avaliacaopergunta set db103_somenteleitura = true where db103_sequencial = 4000299;

SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    private function upDicionario()
    {
        $sql = <<<SQL
        delete from configuracoes.db_sysforkey where codarq = 1010314 and codcam = 1013464;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    private function downDicionario()
    {
        $sql = <<<SQL
        insert into configuracoes.db_sysforkey values (1010314,1013464,1,1153,0);
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
