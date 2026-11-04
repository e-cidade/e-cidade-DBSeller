<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23464CriandoProfissionaisEscolaView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE VIEW escola.profissionais_escolas AS
            WITH profissionais_ativos AS
              ( SELECT *
               FROM rechumano
               INNER JOIN rechumanoescola ON rechumanoescola.ed75_i_rechumano = rechumano.ed20_i_codigo
               LEFT JOIN rechumanoativ ON rechumanoativ.ed22_i_rechumanoescola = rechumanoescola.ed75_i_codigo
               LEFT JOIN atividaderh ON atividaderh.ed01_i_codigo =rechumanoativ.ed22_i_atividade
               LEFT JOIN rechumanopessoal ON rechumanopessoal.ed284_i_rechumano = rechumano.ed20_i_codigo
               LEFT JOIN rhpessoal ON rhpessoal.rh01_regist = rechumanopessoal.ed284_i_rhpessoal
               LEFT JOIN cgm AS cgmrh ON cgmrh.z01_numcgm = rhpessoal.rh01_numcgm
               LEFT JOIN rechumanocgm ON rechumanocgm.ed285_i_rechumano = rechumano.ed20_i_codigo ),
                 origem_profissinal AS
              ( SELECT CASE
                           WHEN ed285_i_cgm IS NULL THEN rh01_numcgm
                           ELSE ed285_i_cgm
                       END AS cgm,
                       rh01_regist AS matricula,
                       profissionais_ativos.*
               FROM profissionais_ativos)
            SELECT DISTINCT cgm AS cod_cgm,
                            matricula,
                            ed20_i_codigo AS cod_rechumano,
                            ed75_i_codigo AS cod_rechumano_escola,
                            ed75_i_escola AS cod_escola,
                            CASE
                                WHEN ed01_c_regencia = 'S' THEN TRUE
                                ELSE FALSE
                            END AS is_regente,
                            EXISTS
              (SELECT 1
               FROM escolagestorcenso
               WHERE ed325_rechumano = ed20_i_codigo
                 AND ed325_escola = ed75_i_escola) AS is_gestor
            FROM origem_profissinal
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP view if exists escola.profissionais_escolas');
    }
}
