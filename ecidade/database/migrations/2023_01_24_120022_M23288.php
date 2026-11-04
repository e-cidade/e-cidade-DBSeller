<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23288 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_syscampo values(1014715,'ac59_cadastrocomissoesfiscal','bool','Exibe ou não os campos de nome, cpf e matrícula do fiscal no cadastro de comissões','f', 'Exibe campo fiscal no cadastro de comissões',1,'t','f','f',5,'text','Fiscal em cadastro de comissões');
insert into db_sysarqcamp values(1010775,1014715,4,0);
ALTER TABLE homologacaoacordo
    ADD COLUMN ac59_cadastrocomissoesfiscal BOOLEAN;
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
        delete from db_sysarqcamp where codarq = 1010775;
        delete from db_syscampo where codcam = 1014715;
        alter table homologacaoacordo drop column  ac59_cadastrocomissoesfiscal;
SQL
        );
    }
}
