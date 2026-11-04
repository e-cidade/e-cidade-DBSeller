<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24383AtualizacaoConfiguracao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // relatorio 271
        DB::connection()->getPdo()->exec(<<<SQL
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 74, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="111110100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="111110200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="111111900000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="111113000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="111115000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="111210100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="111210200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="111210300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="111310000000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="notin" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');

insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 77, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="112410100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112410200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112410300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112419900000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112430100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112430300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112439900000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112440100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112440100 00000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112440200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112440300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112449900000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112450100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112450200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112450300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112459900000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112910300000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="112930300000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="112940300000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="112950300000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="113510800000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="114110100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="114110200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="114110300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="114119900000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="114200000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="114300000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="114910100000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="114910200000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="114910300000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="114910400000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="114919900000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="121110301000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121110302000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121110399000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121119700000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121119903000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="121119999000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="121130300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121139700000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121139903000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="121139999000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="121140301000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121140302000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121140303000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121140304000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121140399000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121149700000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121149903000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="121149999000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="121150301000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121150302000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121150303000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121150304000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121150399000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121159700000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121159903000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="121159999000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="121310100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121310200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121310300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121310400000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121319800000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121319900000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="112430200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="114110400000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121110307000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121110308000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="notin" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
SQL
        );


        // relatorio 274
        DB::connection()->getPdo()->exec(<<<SQL
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 274, 11, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="331909400000000" nivel="7" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="notin" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');


insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 274, 12, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="331909100000000" nivel="7" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="notin" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
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
