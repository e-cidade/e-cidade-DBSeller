<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24383AtualizacaoConfiguracaoAnexo2Rgf extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from orcparamseqfiltropadrao where o132_anousu = 2023 and o132_orcparamrel = 265;

 update orcparamrel
    set o42_descrrel = 'RGF - ANEXO II - ED. 12/13 - DÍVIDA CONSOLIDADA LÍQUIDA'
  where o42_codparrel = 265;

insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 265, 2, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="212110100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212130100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212140100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212150100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212210100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="218410000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="218430000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="218440000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="218450000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="222110100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="222130100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="222140100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="222150100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="222210100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="228310000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="228330000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="228340000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="22835 000000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 265, 5, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="212110201000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212110298000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212110300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212130201000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212130298000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212130298000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212139900000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212140201000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212140298000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212140300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212150201000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212150298000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212150300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212510100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212510200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212530100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212530200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212540100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural=" 21254020000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212550100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212550200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212810100000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="212830100000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="212840100000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="222110200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="222110300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="222130200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="222139900000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="222140200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="222140300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="222150200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="222150300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="222510100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="222510200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="222530000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="222540000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="222550000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="222810100000000" nivel="" exclusao="true" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 265, 6, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="212210200000000" nivel="" exclusao="false" /><conta estrutural="212210300000000" nivel="" exclusao="false" /><conta estrutural="212610100000000" nivel="" exclusao="false" /><conta estrutural="212610200000000" nivel="" exclusao="false" /><conta estrutural="212910100000000" nivel="" exclusao="true" /><conta estrutural="222210200000000" nivel="" exclusao="false" /><conta estrutural="222210300000000" nivel="" exclusao="false" /><conta estrutural="222610100000000" nivel="" exclusao="false" /><conta estrutural="222610200000000" nivel="" exclusao="false" /><conta estrutural="222910100000000" nivel="" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 265, 7, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="212130400000000" nivel="" exclusao="false" /><conta estrutural="212130500000000" nivel="" exclusao="false" /><conta estrutural="222130400000000" nivel="" exclusao="false" /><conta estrutural="222130500000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 265, 9, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="212310100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212310200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212330100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212330200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212340100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212340200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212350000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212510300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212510400000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212530300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212530400000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212540300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212540400000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212550300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212550400000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212810200000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="212830200000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="212840200000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="212850000000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="213110102000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="213110302000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="213210102000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="213210202000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="222310000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="222330000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="222340000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="222350000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="222510300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="222510400000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="222810200000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="222830000000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="222840000000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="222850000000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="223110102000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="223111002000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="223210102000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="223210202000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 265, 10, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="212410000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212610300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212610400000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212910200000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="222410000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="222610300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="222610400000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="222910200000000" nivel="" exclusao="true" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 265, 12, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="214111200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="214131200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="214210300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="214240300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="214310300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="214350300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="224110200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="224130200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="224210100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="224240100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="224310100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="224350100000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 265, 13, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="211410102000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="211410600000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="211420200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="211430102000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="211430600000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="211440600000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="211450600000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="221410100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="221420100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="221420200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="221430101000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 265, 14, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="211410700000000" nivel="" exclusao="false" /><conta estrutural="211430700000000" nivel="" exclusao="false" /><conta estrutural="221410200000000" nivel="" exclusao="false" /><conta estrutural="221430200000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 265, 15, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="211410900000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="221410300000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 265, 16, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="213110103000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="213110303000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="213210103000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="213210203000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="223110103000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="223111003000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="223210103000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="223210203000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor="
"/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 265, 17, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="218110000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="218130000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="218140000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="218150000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="228110000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural=" 22813000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="228140000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural=" 22815000000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 265, 18, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="211110402000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="211110502000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="211110700000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural=" 21121040200000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="211210502000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="211210700000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="211310302000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="211310402000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="213110502000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural=" 21311060200000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="213110702000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="213110802000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="213111100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="221110302000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="221110402000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="221110700000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="221210202000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="221210302000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="221310202000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="221310302000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="223110402000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="223110502000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="223110602000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="223110702000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="223111100000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 265, 19, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="853700000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="863210000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="863220000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="218910105000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="218910108000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="218930105000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="218940105000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="218950105000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 265, 22, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
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
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor="
"/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 265, 23, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="622130700000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="631300000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="632100000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="632700000000000" nivel="" exclusao="false" indicador=""/>
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
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 265, 24, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="218810000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="218830000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="218840000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="218850000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="228810000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="228830000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="228840000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="228850000000000" nivel="" exclusao="false" indicador=""/>
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
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 265, 25, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="112410100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112410200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112410300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112419900000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112430100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112430200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112430300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112439900000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112440100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112440200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112440300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112449900000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112450100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112450200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112450300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112459900000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112910300000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="112930300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112940300000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="112950300000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="113510800000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="114110100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="114110200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="114110300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="114110400000000" nivel="" exclusao="false" indicador=""/>
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
  <conta estrutural="121110307000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121110308000000" nivel="" exclusao="false" indicador=""/>
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
 <observacao valor=" "/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 265, 34, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="211110401000000" nivel="" exclusao="false" /><conta estrutural="211110501000000" nivel="" exclusao="false" /><conta estrutural="211210401000000" nivel="" exclusao="false" /><conta estrutural="211210501000000" nivel="" exclusao="false" /><conta estrutural="211310301000000" nivel="" exclusao="false" /><conta estrutural="211310401000000" nivel="" exclusao="false" /><conta estrutural="213110501000000" nivel="" exclusao="false" /><conta estrutural="213110601000000" nivel="" exclusao="false" /><conta estrutural="213110701000000" nivel="" exclusao="false" /><conta estrutural="213110801000000" nivel="" exclusao="false" /><conta estrutural="221110301000000" nivel="" exclusao="false" /><conta estrutural="221110401000000" nivel="" exclusao="false" /><conta estrutural="221210201000000" nivel="" exclusao="false" /><conta estrutural="221210301000000" nivel="" exclusao="false" /><conta estrutural="221310201000000" nivel="" exclusao="false" /><conta estrutural="221310301000000" nivel="" exclusao="false" /><conta estrutural="223110401000000" nivel="" exclusao="false" /><conta estrutural="223110501000000" nivel="" exclusao="false" /><conta estrutural="223110601000000" nivel="" exclusao="false" /><conta estrutural="223110701000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 265, 35, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="211110403000000" nivel="" exclusao="false" /><conta estrutural="211110503000000" nivel="" exclusao="false" /><conta estrutural="211210403000000" nivel="" exclusao="false" /><conta estrutural="211210503000000" nivel="" exclusao="false" /><conta estrutural="211310303000000" nivel="" exclusao="false" /><conta estrutural="211310403000000" nivel="" exclusao="false" /><conta estrutural="213110503000000" nivel="" exclusao="false" /><conta estrutural="213110603000000" nivel="" exclusao="false" /><conta estrutural="213110703000000" nivel="" exclusao="false" /><conta estrutural="213110803000000" nivel="" exclusao="false" /><conta estrutural="221110303000000" nivel="" exclusao="false" /><conta estrutural="221110403000000" nivel="" exclusao="false" /><conta estrutural="221210203000000" nivel="" exclusao="false" /><conta estrutural="221210303000000" nivel="" exclusao="false" /><conta estrutural="221310203000000" nivel="" exclusao="false" /><conta estrutural="221310303000000" nivel="" exclusao="false" /><conta estrutural="223110403000000" nivel="" exclusao="false" /><conta estrutural="223110503000000" nivel="" exclusao="false" /><conta estrutural="223110603000000" nivel="" exclusao="false" /><conta estrutural="223110703000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 265, 36, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="227200000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 265, 37, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="631100000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="631200000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="631500000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="631700000000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor="
"/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 265, 38, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="212110205000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212130205000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212140205000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="212150205000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 265, 39, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="218610100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="228610100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="228619900000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 265, 40, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="218910106000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="218930106000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="218940106000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="218950106000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 265, 41, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="331910000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="333910000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="344910000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="345910000000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>');

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

    }
}
