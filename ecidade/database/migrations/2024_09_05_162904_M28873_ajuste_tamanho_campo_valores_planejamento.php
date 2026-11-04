<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28873AjusteTamanhoCampoValoresPlanejamento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop view if exists planejamento.detalhamnento_planejamento;
        
alter table planejamento.valores alter column pl10_valor type numeric(20, 2);

alter table planejamento.programaestrategico alter column pl9_valorbase type numeric(20, 2);

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
    json_agg (
        json_build_object ('ano', pl10_ano, 'valor', pl10_valor)
    ) as valores
from
    planejamento.programaestrategico
    join planejamento.iniciativaprojativ on iniciativaprojativ.pl12_programaestrategico = programaestrategico.pl9_codigo
    join planejamento.detalhamentoiniciativa on detalhamentoiniciativa.pl20_iniciativaprojativ = iniciativaprojativ.pl12_codigo
    left join planejamento.valores on pl10_chave = detalhamentoiniciativa.pl20_codigo
    and pl10_origem = 'DETALHAMENTO INICIATIVA'
group by
    pl9_planejamento,
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
    pl20_valorbase;        

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

drop view if exists planejamento.detalhamnento_planejamento;

alter table planejamento.valores alter column pl10_valor type numeric(15, 2);

alter table planejamento.programaestrategico alter column pl9_valorbase type numeric(15, 2);

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
    json_agg (
        json_build_object ('ano', pl10_ano, 'valor', pl10_valor)
    ) as valores
from
    planejamento.programaestrategico
    join planejamento.iniciativaprojativ on iniciativaprojativ.pl12_programaestrategico = programaestrategico.pl9_codigo
    join planejamento.detalhamentoiniciativa on detalhamentoiniciativa.pl20_iniciativaprojativ = iniciativaprojativ.pl12_codigo
    left join planejamento.valores on pl10_chave = detalhamentoiniciativa.pl20_codigo
    and pl10_origem = 'DETALHAMENTO INICIATIVA'
group by
    pl9_planejamento,
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
    pl20_valorbase;    

SQL
        );
    }
}
