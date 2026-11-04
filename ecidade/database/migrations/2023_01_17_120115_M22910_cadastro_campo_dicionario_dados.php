<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M22910CadastroCampoDicionarioDados extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_syscampo values(1014699,'possui_anexo_formacao','bool','Campo para buscar label ','f', 'Anexo da Formação',1,'t','f','f',5,'text','Anexo da Formação');
insert into db_syscampodef values(1014699,'t','Sim');
insert into db_syscampodef values(1014699,'f','Não');
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
delete from db_syscampodef where codcam = 1014699;
delete from db_syscampo where codcam = 1014699;
SQL
            );
    }
}
