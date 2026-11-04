<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CriandoCampoTabelaRhparametrosfundeb extends Migration
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

    public function upDicionario()
    {
        $sql  = <<<SQL
            insert into db_syscampo values(1015637,'rh284_rubrica_abatimento','varchar(4)','Rubrica de Abatimento','', 'Rubrica de Abatimento',4,'t','t','f',0,'text','Rubrica de Abatimento');
            insert into db_sysarqcamp values(1011056,1015637,7,0);       
SQL;
            DB::connection()->getPdo()->exec($sql);
    }

    private function upEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL

        ALTER TABLE rhparametrosfundeb ADD COLUMN rh284_rubrica_abatimento VARCHAR(4) NULL;
SQL
);
    }

    private function downEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL

        ALTER TABLE rhparametrosfundeb DROP COLUMN rh284_rubrica_abatimento;
SQL
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function downDicionario()
    {
        $sql  = <<<SQL
            delete from db_sysarqcamp where codcam in (1015637);
            delete from db_syscampo where codcam in (1015637);
SQL;
            DB::connection()->getPdo()->exec($sql);
    }
}
