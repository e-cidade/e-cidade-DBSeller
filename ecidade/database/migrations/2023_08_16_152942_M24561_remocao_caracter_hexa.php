<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24561RemocaoCaracterHexa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
update contabilidade.planoreceita set nome = replace(nome, chr(x'A0'::int), '')
 where strpos(nome,chr(x'A0'::int))>0;

update contabilidade.planodespesa set nome = replace(nome, chr(x'A0'::int), '')
 where strpos(nome,chr(x'A0'::int))>0;

update contabilidade.pcasp set nome = replace(nome, chr(x'A0'::int), '')
 where strpos(nome,chr(x'A0'::int))>0;
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
    }
}
