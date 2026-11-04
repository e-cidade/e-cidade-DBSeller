<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class M24560AdicionaColunaDistritosanitarioCgsund extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ambulatorial.cgs_und', function (Blueprint $table) {
            $table->unsignedInteger('z01_distritosanitario')->nullable();

            $table->foreign('z01_distritosanitario')->references('s153_i_codigo')->on(
                'ambulatorial.sau_distritosanitario'
            );
        });

        DB::unprepared(
            <<<SQL
insert into db_syscampo values(1009244,'z01_distritosanitario','int4','Indica a qual Distrito Sanitário o cgs pertence.','0', 'Distrito Sanitário',6,'f','f','f',1,'text','Distrito Sanitário');
insert into db_sysarqcamp values(1010144,1009244,82,0);
insert into db_sysforkey values(1010144,1009244,1,3044,0);
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
        Schema::table('ambulatorial.cgs_und', function (Blueprint $table) {
            $table->dropColumn('z01_distritosanitario');
        });

        DB::unprepared(
            <<<SQL
delete from db_sysforkey where codcam =  1009244;
delete from db_sysarqcamp where codcam =  1009244;
delete from db_syscampo where codcam =  1009244;
SQL
        );
    }
}
