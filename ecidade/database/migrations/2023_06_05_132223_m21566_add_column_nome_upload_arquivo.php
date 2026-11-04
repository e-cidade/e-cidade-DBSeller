<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M21566AddColumnNomeUploadArquivo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('protprocessodocumento', function (Blueprint $table) {
            $table->string('p01_upload_name')
                ->nullable()
                ->after('p01_c70codlan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('protprocessodocumento', function (Blueprint $table) {
            $table->dropColumn('p01_upload_name');
        });
    }
}
