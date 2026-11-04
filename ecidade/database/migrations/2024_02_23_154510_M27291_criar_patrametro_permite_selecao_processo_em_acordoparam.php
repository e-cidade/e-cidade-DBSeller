<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27291CriarPatrametroPermiteSelecaoProcessoEmAcordoparam extends Migration
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
insert into db_syscampo values(1015631,'ac59_permiteporinstit','bool','Seleciona processo por instituição','t','Seleciona processo por instituição',1,'f','f','f',5,'text','Seleciona processo por instituição');
insert into db_sysarqcamp values(1010775,1015631,6,0);
SQL
        );
    }

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_sysarqcamp where codarq = 1010775 and codcam = 1015631;
delete from db_syscampo where codcam = 1015631;
SQL
        );
    }

    private function upEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
alter table acordoparam add column ac59_permiteporinstit BOOLEAN DEFAULT TRUE;
SQL
        );
    }

    private function downEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
alter table acordoparam drop column ac59_permiteporinstit;
SQL
        );
    }
}
