<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25720AdicionaColunaEstorageBensbaix extends Migration
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
insert into db_syscampo values(1015524,'t55_estorage','bool','Define se o arquivo está ou não no e-storage','f', 'Estorage',1,'f','f','f',5,'text','Estorage');
insert into db_sysarqcamp values(917,1015524,6,0);

SQL
        );
    }

    private function upEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
alter table bensbaix add column t55_estorage boolean default false;
alter table bensbaix alter column t55_documento type bigint;
SQL
        );
    }

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_sysarqcamp where codcam = 1015524;
delete from db_syscampo where codcam = 1015524;

SQL
        );
    }

    private function downEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
alter table bensbaix drop column t55_estorage;
alter table bensbaix alter column t55_documento type oid;
SQL
        );
    }
}

