<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23809CritandoCampoUnidadeCurricularTebelaRegencia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionario();
        Schema::table('escola.regencia', function (Blueprint $table) {
            $table->integer('ed59_unidade_curricular')->nullable();

            $table->foreign('ed59_unidade_curricular', 'unidade_curricular_fk')
                ->references('ed199_id')->on('secretariadeeducacao.unidadescurriculares');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downDicionario();
        Schema::table('escola.regencia', function (Blueprint $table) {
            $table->dropColumn('ed59_unidade_curricular');
        });
    }

    public function upDicionario()
    {
        DB::statement("insert into db_syscampo values(1014239, 'ed59_unidade_curricular', 'int4', 'Unidade Curricular', '0', 'Unidade Curricular', 11, 't','f', 'f', 1, 'text', 'Unidade Curricular')");
        DB::statement("insert into db_sysarqcamp values(1010084,1014239,18,0)");
        DB::statement("insert into db_sysforkey values(1010084,1014239,1,1011072,0);");
    }

    public function downDicionario()
    {
        DB::statement("delete from db_sysarqcamp where codcam = 1014239");
        DB::statement("delete from db_sysforkey where referen = 1011072");
        DB::statement("delete from db_syscampo where codcam = 1014239");
    }
}
