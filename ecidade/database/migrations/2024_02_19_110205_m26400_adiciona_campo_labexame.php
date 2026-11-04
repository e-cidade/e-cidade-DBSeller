<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26400AdicionaCampoLabexame extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('laboratorio.lab_exame', function (Blueprint $table) {
            $table->boolean('la08_exibehistoricoresultados')->default(false);
        });

        DB::connection()->getPdo()->exec(<<<SQL

    insert into db_syscampo values(1015624,'la08_exibehistoricoresultados','bool','Marcar esta opção irá fazer com que o histórico de resultados do paciente, para esse exame seja exibido no laudo do exame.','f', 'Exibir histórico dos resultados',1,'f','f','f',5,'text','Exibir histórico dos resultados');
    insert into db_sysarqcamp values(2758,1015624,19,0);


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
            $table->dropColumn('la08_exibehistoricoresultados');
        });

        DB::connection()->getPdo()->exec(<<<SQL

    delete from configuracoes.db_sysarqcamp where codcam = 1015624;
    delete from configuracoes.db_syscampo where codcam = 1015624;

SQL
        );
    }
}
