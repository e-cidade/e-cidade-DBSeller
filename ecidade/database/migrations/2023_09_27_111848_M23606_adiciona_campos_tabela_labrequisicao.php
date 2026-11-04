<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23606AdicionaCamposTabelaLabrequisicao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('laboratorio.lab_requisicao', function (Blueprint $table) {
            $table->decimal('la22_peso', 6, 3)->nullable();
            $table->integer('la22_altura')->nullable();
            $table->decimal('la22_volumeamostra', 5, 3)->nullable();
        });

        DB::connection()->getPdo()->exec(<<<SQL

            insert into db_syscampo values(1015472,'la22_peso','float8','Campo para inserção do peso do paciente.','0', 'Peso',8,'t','f','f',0,'text','Peso');
            insert into db_syscampo values(1015473,'la22_altura','int4','Campo para inserção da altura do paciente.','0', 'Altura',5,'t','f','f',1,'text','Altura');
            insert into db_syscampo values(1015474,'la22_volumeamostra','float8','Campo para inserção do volume da amostra coletada do paciente.','0', 'Volume Amostra',8,'t','f','f',4,'text','Volume Amostra');

            insert into db_sysarqcamp values(2773,1015474,15,0);
            insert into db_sysarqcamp values(2773,1015473,16,0);
            insert into db_sysarqcamp values(2773,1015472,17,0);

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
        Schema::table('laboratorio.lab_requisicao', function (Blueprint $table) {
            $table->dropColumn('la22_peso');
            $table->dropColumn('la22_altura');
            $table->dropColumn('la22_volumeamostra');
        });

        DB::connection()->getPdo()->exec(<<<SQL

            delete from db_sysarqcamp where codcam in (1015472, 1015473, 1015474);
            delete from db_syscampo where codcam in (1015472, 1015473, 1015474);

SQL
        );
    }
}
