<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27075AjustaTipoColuna extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $sql = <<<SQL

create table w_seq_coluna_up as 
select distinct o115_sequencial as seq_coluna,
       o115_anousu         ,
       o115_descricao      ,
       o115_tipo           ,
       o115_valoresdefault ,
       o115_nomecoluna     ,
       o115_formula        ,
       o115_origem         ,
       o115_relatorio      
 from orcparamseq
 join orcparamseqorcparamseqcoluna on (o116_codparamrel, o116_codseq) = (o69_codparamrel, o69_codseq)
 join orcparamseqcoluna   on  (o116_orcparamseqcoluna) = (o115_sequencial)
 where o69_codparamrel = 266
   and o115_nomecoluna in ( 'insuficiencia_financeira', 'disp_caixa_liquida' );

update orcparamseqcoluna set o115_tipo = 1
  from w_seq_coluna_up
  where o115_sequencial = seq_coluna;

SQL;

        DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {


        $sql = <<<SQL
create table w_seq_coluna_down as 
select distinct o115_sequencial as seq_coluna,
       o115_anousu         ,
       o115_descricao      ,
       o115_tipo           ,
       o115_valoresdefault ,
       o115_nomecoluna     ,
       o115_formula        ,
       o115_origem         ,
       o115_relatorio      
 from orcparamseq
 join orcparamseqorcparamseqcoluna on (o116_codparamrel, o116_codseq) = (o69_codparamrel, o69_codseq)
 join orcparamseqcoluna   on  (o116_orcparamseqcoluna) = (o115_sequencial)
 where o69_codparamrel = 266
   and o115_nomecoluna in ( 'insuficiencia_financeira', 'disp_caixa_liquida' );

update orcparamseqcoluna set o115_tipo = 2
  from w_seq_coluna_down
  where o115_sequencial = seq_coluna;

SQL;

        DB::connection()->getPdo()->exec($sql);
    }
}
