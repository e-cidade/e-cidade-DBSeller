<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26204AdicionaColunaEstorageBens extends Migration
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
insert into db_syscampo values(1015530,'t52_estorage','bool','Define se a imagem do bem está no e-storage','f', 'Estorage',1,'f','f','f',5,'text','Estorage');
insert into db_sysarqcamp values(914,1015530,17,0);
SQL
        );
    }

    private function upEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
alter table bens add column t52_estorage boolean default false;
SQL
        );
    }

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_sysarqcamp where codcam = 1015530;
delete from db_syscampo where codcam = 1015530;

SQL
        );
    }

    private function downEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
alter table bens drop column t52_estorage;
SQL
        );
    }
}
