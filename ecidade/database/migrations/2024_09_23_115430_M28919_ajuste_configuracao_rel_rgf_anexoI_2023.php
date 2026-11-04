<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28919AjusteConfiguracaoRelRgfAnexoI2023 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL

--criamos uma tabela teporaria com os periodos do relatorio 
drop table if exists w_periodos;
create temp table w_periodos (periodo int);
insert into w_periodos values (12),(13),(14),(15),(16),(17),(18),(19),(20),(21),(22),(23),(24),(25),(26),(27),(28);

--excluimos as colunas mensais das linhas
delete from orcparamseqorcparamseqcoluna
where o116_codparamrel = 274
  and o116_codseq in (16,17,18)
  and o116_orcparamseqcoluna in (
    select o115_sequencial
      from orcparamseqcoluna
    where o115_nomecoluna in ('mes_1', 'mes_2', 'mes_3', 'mes_4', 'mes_5', 'mes_6', 'mes_7', 'mes_8', 
'mes_9','mes_10', 'mes_11', 'mes_12')
    );


--inserimos as colunas mensais nas linhas     
insert into orcparamseqorcparamseqcoluna 
select nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 
       16, 
       274, 
       o115_sequencial, 
       substr(o115_descricao,4)::int as ordem, 
       periodo 
  from orcparamseqcoluna 
       inner join w_periodos on true 
 where o115_nomecoluna in ('mes_1', 'mes_2', 'mes_3', 'mes_4', 'mes_5', 'mes_6', 'mes_7', 'mes_8', 'mes_9', 'mes_10', 'mes_11', 'mes_12') 
   and o115_relatorio = 274;

insert into orcparamseqorcparamseqcoluna 
select nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 
       17, 
       274, 
       o115_sequencial, 
       substr(o115_descricao,4)::int as ordem, 
       periodo 
  from orcparamseqcoluna 
       inner join w_periodos on true 
 where o115_nomecoluna in ('mes_1', 'mes_2', 'mes_3', 'mes_4', 'mes_5', 'mes_6', 'mes_7', 'mes_8', 'mes_9', 'mes_10', 'mes_11', 'mes_12') 
   and o115_relatorio = 274;   

insert into orcparamseqorcparamseqcoluna select nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 17,274,845, 14, periodo from w_periodos; 
insert into orcparamseqorcparamseqcoluna select nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 17,274,841, 14, periodo from w_periodos; 

insert into orcparamseqorcparamseqcoluna 
select nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 
       18, 
       274, 
       o115_sequencial, 
       substr(o115_descricao,4)::int as ordem, 
       periodo 
  from orcparamseqcoluna 
       inner join w_periodos on true 
 where o115_nomecoluna in ('mes_1', 'mes_2', 'mes_3', 'mes_4', 'mes_5', 'mes_6', 'mes_7', 'mes_8', 'mes_9', 'mes_10', 'mes_11', 'mes_12') 
   and o115_relatorio = 274;   

insert into orcparamseqorcparamseqcoluna select nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 18,274,845, 14, periodo from w_periodos; 
insert into orcparamseqorcparamseqcoluna select nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 18,274,841, 14, periodo from w_periodos; 

drop table if exists w_periodos;

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
