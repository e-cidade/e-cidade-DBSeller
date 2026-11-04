<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23720ParametroEmissaoRendimentosDbpref extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prefeitura.configdbpref', function (Blueprint $table) {
            $table->boolean('w13_comprovanterendimentosanoanterior')->default(true);
        });

        DB::statement("insert into db_syscampo values(1014774,'w13_comprovanterendimentosanoanterior','bool','Libera emissão do Comprovante de Rendimentos pro ano anterior. SIM: sistema irá permitir emissão do comprovante normalmente; NÃO: sistema não irá permitir emissão do comprovante pro ano anterior;','true', 'Comprovante de Rendimentos ano anterior',1,'f','f','f',5,'text','Comprovante de Rendimentos ano anterior');");
        DB::statement("insert into db_syscampodef values(1014774,'t','Sim');");
        DB::statement("insert into db_syscampodef values(1014774,'f','Não');");
        DB::statement("insert into db_sysarqcamp values(1383,1014774,29,0);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("delete from db_sysarqcamp where codcam = 1014774;");
        DB::statement("delete from db_syscampodef where codcam = 1014774;");
        DB::statement("delete from db_syscampo where codcam = 1014774;");

        Schema::table('prefeitura.configdbpref', function (Blueprint $table) {
            $table->dropColumn('w13_comprovanterendimentosanoanterior');
        });
    }
}
