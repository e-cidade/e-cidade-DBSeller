<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23082emissaoProjetoTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop view v_suplementacao_despesa;
drop view v_suplementacao_receita;
create or replace view v_suplementacao_despesa as
 select orcsuplem.o46_codlei       as sequencial_projeto,
        orcorgao.o40_orgao         as orgao,
        orcorgao.o40_descr         as descricao_orgao,
        orcunidade.o41_unidade     as unidade,
        orcunidade.o41_descr       as descricao_unidade,
        orcfuncao.o52_funcao       as funcao,
        orcfuncao.o52_descr        as descricao_funcao,
        orcsubfuncao.o53_subfuncao as subfuncao,
        orcsubfuncao.o53_descr     as descricao_subfuncao,
        orcprograma.o54_programa   as programa,
        orcprograma.o54_descr      as descricao_programa,
        orcprojativ.o55_projativ   as projativ,
        orcprojativ.o55_descr      as descricao_projativ,
        substr(fc_estruturaldespesa(orcelemento.o56_elemento),3,10) as elemento_despesa,
        orcelemento.o56_elemento   as elemento,
        orcelemento.o56_descr      as descricao_elemento,
        fonterecurso.gestao     as recurso,
        orcdotacao.o58_coddot      as dotacao,
        orcsuplemval.o47_valor     as valor_suplementacao
   from orcsuplemval
        inner join orcdotacao   on orcdotacao.o58_anousu      = orcsuplemval.o47_anousu
                               and orcdotacao.o58_coddot      = orcsuplemval.o47_coddot
        inner join orcorgao     on orcorgao.o40_anousu        = orcdotacao.o58_anousu
                               and orcorgao.o40_orgao         = orcdotacao.o58_orgao
        inner join orcunidade   on orcunidade.o41_anousu      = orcdotacao.o58_anousu
                               and orcunidade.o41_orgao       = orcdotacao.o58_orgao
                               and orcunidade.o41_unidade     = orcdotacao.o58_unidade
        inner join orcprograma  on orcprograma.o54_anousu     = orcdotacao.o58_anousu
                               and orcprograma.o54_programa   = orcdotacao.o58_programa
        inner join orcprojativ  on orcprojativ.o55_anousu     = orcdotacao.o58_anousu
                               and orcprojativ.o55_projativ   = orcdotacao.o58_projativ
        inner join orcelemento  on orcelemento.o56_codele     = orcdotacao.o58_codele
                               and orcelemento.o56_anousu     = orcdotacao.o58_anousu
        inner join orctiporec   on orctiporec.o15_codigo      = orcdotacao.o58_codigo
        join fonterecurso on fonterecurso.orctiporec_id = orctiporec.o15_codigo
        and fonterecurso.exercicio = orcdotacao.o58_anousu
        inner join orcfuncao    on orcfuncao.o52_funcao       = orcdotacao.o58_funcao
        inner join orcsubfuncao on orcsubfuncao.o53_subfuncao = orcdotacao.o58_subfuncao
        inner join orcsuplem    on orcsuplem.o46_codsup       = orcsuplemval.o47_codsup
        inner join orcprojeto   on orcprojeto.o39_codproj     = orcsuplem.o46_codlei;


create or replace view v_suplementacao_receita as
select orcsuplem.o46_codlei  as sequencial_projeto,
       orcreceita.o70_codrec as receita,
       orcsuplemrec.o85_valor  as valor,
       orcreceita.o70_valor  as valor_orcado_receita,
       orcfontes.o57_descr   as descricao,
       orcfontes.o57_fonte   as estrutural,
       orcreceita.o70_anousu as ano,
       fonterecurso.gestao as recurso,
       fonterecurso.descricao  as descricao_recurso
  from orcsuplemrec
       inner join orcsuplem  on orcsuplem.o46_codsup   = orcsuplemrec.o85_codsup
       inner join orcprojeto on orcprojeto.o39_codproj = orcsuplem.o46_codlei
       inner join orcreceita on orcreceita.o70_anousu  = orcsuplemrec.o85_anousu
                            and orcreceita.o70_codrec  = orcsuplemrec.o85_codrec
       inner join orcfontes  on orcfontes.o57_codfon   = orcreceita.o70_codfon
                            and orcfontes.o57_anousu   = orcreceita.o70_anousu
       inner join orctiporec on orctiporec.o15_codigo  = orcreceita.o70_codigo
       join fonterecurso on fonterecurso.orctiporec_id = orctiporec.o15_codigo
       and fonterecurso.exercicio = orcreceita.o70_anousu;
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
        //
    }
}
