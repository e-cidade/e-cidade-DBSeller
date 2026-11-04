<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28306AcertoSchemaEstrutura extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $sql = " alter table if exists  suspensaoorcamentaria   set schema orcamento; ";    
        DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = "alter table if exists  suspensaoorcamentaria   set schema public;";
        DB::connection()->getPdo()->exec($sql);
    }
}
