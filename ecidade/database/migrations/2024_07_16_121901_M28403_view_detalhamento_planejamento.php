<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28403ViewDetalhamentoPlanejamento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
create index if not exists valores_planejamento_in on planejamento.valores(pl10_chave, pl10_origem);
create index if not exists detalhamentoiniciativa_iniciativaprojativ_in on planejamento.detalhamentoiniciativa(pl20_iniciativaprojativ);

create view planejamento.detalhamnento_planejamento as
select
       pl9_planejamento as planejamento_id,
       pl9_codigo as programaestrategico_id,
       pl12_codigo as iniciativaprojativ_id,
       pl20_codigo as detalhamentoiniciativa_id,
       pl9_orcprograma,
       pl12_orcprojativ,
       pl20_anoorcamento,
       pl20_instituicao,
       pl20_orcorgao,
       pl20_orcunidade,
       pl20_orcfuncao,
       pl20_orcsubfuncao,
       pl20_orcelemento,
       pl20_recurso,
       pl20_concarpeculiar,
       pl20_subtitulo,
       pl20_esferaorcamentaria,
       pl20_valorbase,
       json_agg(
          json_build_object(
             'ano', pl10_ano,
             'valor', pl10_valor
          )
       ) as valores
  from planejamento.programaestrategico
  join planejamento.iniciativaprojativ
       on iniciativaprojativ.pl12_programaestrategico = programaestrategico.pl9_codigo
  join planejamento.detalhamentoiniciativa
       on detalhamentoiniciativa.pl20_iniciativaprojativ = iniciativaprojativ.pl12_codigo
  left join planejamento.valores on pl10_chave = detalhamentoiniciativa.pl20_codigo
            and pl10_origem = 'DETALHAMENTO INICIATIVA'
group by pl9_planejamento,
         pl9_codigo,
         pl12_codigo,
         pl20_codigo,
         pl9_orcprograma,
         pl12_orcprojativ,
         pl20_anoorcamento,
         pl20_instituicao,
         pl20_orcorgao,
         pl20_orcunidade,
         pl20_orcfuncao,
         pl20_orcsubfuncao,
         pl20_orcelemento,
         pl20_recurso,
         pl20_concarpeculiar,
         pl20_subtitulo,
         pl20_esferaorcamentaria,
         pl20_valorbase
;
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
        DB::unprepared(<<<SQL
drop index valores_planejamento_in;
drop index detalhamentoiniciativa_iniciativaprojativ_in;
drop view planejamento.detalhamnento_planejamento;
SQL
        );
    }
}
