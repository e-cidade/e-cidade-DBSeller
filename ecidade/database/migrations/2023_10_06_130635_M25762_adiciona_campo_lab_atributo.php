<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25762AdicionaCampoLabAtributo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('laboratorio.lab_atributo', function (Blueprint $table) {
            $table->string('la25_outrasinformacoes')->nullable();
        });

        DB::connection()->getPdo()->exec(<<<SQL

            insert into db_syscampo values(1015483,'la25_outrasinformacoes','varchar(20)','Esse campo irá relacionar a opção escolhida ao valor colocado na parte \"Outras informações\" no respectivo campo, para ser exibido na digitação dos resultados.','', 'Exibir dado na digitação',20,'t','f','f',0,'text','Exibir dado na digitação');
            insert into db_sysarqcamp values(2899,1015483,9,0);

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
        Schema::table('laboratorio.lab_atributo', function (Blueprint $table) {
            $table->dropColumn('la25_outrasinformacoes');
        });

        DB::connection()->getPdo()->exec(<<<SQL

            delete from db_sysarqcamp where codcam in (1015483);
            delete from db_syscampo where codcam in (1015483);

SQL
        );
    }
}
