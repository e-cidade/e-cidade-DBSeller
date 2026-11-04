<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23606AdicionaCamposLabexame extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('laboratorio.lab_exame', function (Blueprint $table) {
            $table->boolean('la08_obriga_peso')->default(false);
            $table->boolean('la08_obriga_altura')->default(false);
            $table->boolean('la08_obriga_volumeamostra')->default(false);
        });

        DB::connection()->getPdo()->exec(<<<SQL

            insert into db_syscampo values(1015469,'la08_obriga_peso','bool','Campo que indica a obrigatoriedade ou não de peso.','f', 'Peso',1,'t','f','f',5,'text','Peso');
            insert into db_syscampo values(1015470,'la08_obriga_altura','bool','Campo que indica a obrigatoriedade ou não de altura.','f', 'Altura',1,'t','f','f',5,'text','Altura');
            insert into db_syscampo values(1015471,'la08_obriga_volumeamostra','bool','Campo que indica a obrigatoriedade ou não do volume da amostra.','f', 'Volume Amostra',1,'t','f','f',5,'text','Volume Amostra');

            insert into db_sysarqcamp values(2758,1015471,16,0);
            insert into db_sysarqcamp values(2758,1015470,17,0);
            insert into db_sysarqcamp values(2758,1015469,18,0);

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
        Schema::table('laboratorio.lab_exame', function (Blueprint $table) {
            $table->dropColumn('la08_obriga_peso');
            $table->dropColumn('la08_obriga_altura');
            $table->dropColumn('la08_obriga_volumeamostra');
        });

        DB::connection()->getPdo()->exec(<<<SQL

            delete from db_sysarqcamp where codcam in (1015469, 1015470, 1015471);
            delete from db_syscampo where codcam in (1015469, 1015470, 1015471);

SQL
        );
    }
}
