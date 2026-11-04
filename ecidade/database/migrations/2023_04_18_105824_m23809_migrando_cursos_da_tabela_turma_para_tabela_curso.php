<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23809MigrandoCursosDaTabelaTurmaParaTabelaCurso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('escola.turma')->selectRaw('distinct ed29_i_codigo, ed57_i_censocursoprofiss')
            ->join('base', 'ed57_i_base', '=', 'ed31_i_codigo')
            ->join('cursoedu', 'ed31_i_curso', '=', 'ed29_i_codigo')
            ->get()->map(function ($registros) {
                if (!is_null($registros->ed57_i_censocursoprofiss)) {
                    DB::table('cursoedu')->where('ed29_i_codigo', $registros->ed29_i_codigo)->update([
                        'ed29_censocursoprofiss' => $registros->ed57_i_censocursoprofiss
                    ]);
                };
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        return true;
    }
}
