<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28969EtapaDestinoMatricula extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        return true;
        DB::connection()->getPdo()->exec(<<<SQL
            create table escola.etapadestinomatricula (
                ed364_codigo serial primary key,
                ed364_matricula integer not null,
                ed364_etapadestino integer not null,

                constraint etapadestinomatricula_ed364_matricula_fk
                    foreign key (ed364_matricula) references matricula (ed60_i_codigo),
                constraint etapadestinomatricula_ed364_etapadestino_fk
                    foreign key (ed364_etapadestino) references serie (ed11_i_codigo)
            );

            insert into etapadestinomatricula
            select nextval('etapadestinomatricula_ed364_codigo_seq'), ed60_i_codigo, alunopossib.ed79_i_serie from matricula
                join turma ON turma.ed57_i_codigo = matricula.ed60_i_turma
                join calendario ON calendario.ed52_i_codigo = turma.ed57_i_calendario
                join aluno ON aluno.ed47_i_codigo = matricula.ed60_i_aluno
                join alunocurso ON alunocurso.ed56_i_aluno = aluno.ed47_i_codigo
                join alunopossib ON alunopossib.ed79_i_alunocurso = alunocurso.ed56_i_codigo
                WHERE turma.ed57_i_tipoturma = 7 and ed52_i_ano in(2023, 2024) and ed60_c_situacao = 'MATRICULADO' and ed60_c_concluida = 'S';

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
        DB::connection()->getPdo()->exec(<<<SQL
            drop table escola.etapadestinomatricula;
SQL
        );

    }
}
