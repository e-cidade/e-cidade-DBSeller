<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;


class M22728AdicionandoColunasEstorage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL

        ALTER TABLE escola.alunonecessidade ADD COLUMN ed214_i_anexo_estorage int8 default null;

        insert into db_syscampo values(1014692,'ed214_i_anexo_estorage','int8','id anexo diagnóstico','0', 'id anexo diagnóstico',10,'t','f','f',1,'text','id anexo diagnóstico');

        insert into db_sysarqcamp values(1907,1014692,9,0);

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
        
        ALTER TABLE escola.alunonecessidade DROP COLUMN ed214_i_anexo_estorage;

        delete from db_sysarqcamp where codcam in (1014692);

        delete from db_syscampo where codcam in (1014692);
SQL
        );
    }
}