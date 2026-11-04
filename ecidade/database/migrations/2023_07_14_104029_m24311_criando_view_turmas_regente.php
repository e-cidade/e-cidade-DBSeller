<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24311CriandoViewTurmasRegente extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE VIEW escola.turmas_regentes AS
                WITH profissional AS
                (
                    SELECT  distinct ed20_i_codigo, rh01_numcgm, ed285_i_cgm, ed75_i_escola as escola_professor
                    FROM rechumano
                    JOIN rechumanoescola
                    ON rechumanoescola.ed75_i_rechumano = rechumano.ed20_i_codigo AND rechumanoescola.ed75_i_saidaescola is null
                    LEFT JOIN rechumanocgm
                    ON rechumanocgm.ed285_i_rechumano = rechumano.ed20_i_codigo
                    LEFT JOIN rechumanopessoal
                    ON rechumanopessoal.ed284_i_rechumano = ed20_i_codigo
                    LEFT JOIN rhpessoal
                    ON rhpessoal.rh01_regist = rechumanopessoal.ed284_i_rhpessoal
                ), regencias_normal AS
                (
                    SELECT  distinct regenciahorario.ed58_i_regencia AS codigo, profissional.*
                    FROM profissional
                    JOIN regenciahorario
                    ON regenciahorario.ed58_i_rechumano = profissional.ed20_i_codigo
                    JOIN regencia
                    ON regencia.ed59_i_codigo = regenciahorario.ed58_i_regencia
                    JOIN turma
                    ON turma.ed57_i_codigo = regencia.ed59_i_turma
                    WHERE ed58_ativo is true
                    and turma.ed57_i_escola = profissional.escola_professor
                ), regencias_substituta AS
                (
                    SELECT  distinct docentesubstituto.ed322_regencia AS codigo, profissional.*
                    FROM profissional
                    JOIN docentesubstituto
                    ON docentesubstituto.ed322_rechumano = profissional.ed20_i_codigo
                    JOIN regenciahorario
                    ON regenciahorario.ed58_i_regencia = docentesubstituto.ed322_regencia
                    JOIN regencia
                    ON regencia.ed59_i_codigo = regenciahorario.ed58_i_regencia
                    JOIN turma
                    ON turma.ed57_i_codigo = regencia.ed59_i_turma
                    JOIN calendario
                    ON calendario.ed52_i_codigo = turma.ed57_i_calendario
                    WHERE ed58_ativo is true
                    AND ed59_c_freqglob <> 'A'
                    AND current_date >= ed322_periodoinicial
                    AND ( (ed322_periodofinal is null AND current_date <= ed52_d_fim) or current_date <= ed322_periodofinal )
                    and turma.ed57_i_escola = profissional.escola_professor
                ), regencias AS
                (
                    SELECT  *
                    FROM regencias_normal
                    UNION ALL
                    SELECT  *
                    FROM regencias_substituta
                )
                SELECT
                        ed20_i_codigo as codigo_rechumano,
                        case when rh01_numcgm is not null
                            then rh01_numcgm
                            else ed285_i_cgm
                        end as cgm,
                        escola_professor,
                        turma.ed57_i_codigo as codigo_turma,
                        serie.ed11_i_codigo as codigo_etapa,
                        ed59_i_codigo as codigo_regencia,
                        ed52_i_ano as ano,
                        ed52_i_codigo as calendario,
                        ed52_d_inicio as inicio_calendario,
                        ed52_d_fim as fim_calendario
                FROM regencias
                JOIN regencia
                ON regencia.ed59_i_codigo = regencias.codigo
                JOIN serie
                ON serie.ed11_i_codigo = regencia.ed59_i_serie
                JOIN turma
                ON turma.ed57_i_codigo = regencia.ed59_i_turma
                JOIN calendario
                ON calendario.ed52_i_codigo = turma.ed57_i_calendario
                JOIN disciplina
                ON disciplina.ed12_i_codigo = regencia.ed59_i_disciplina
                JOIN caddisciplina
                ON caddisciplina.ed232_i_codigo = disciplina.ed12_i_caddisciplina
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS escola.turmas_regentes');
    }
}

