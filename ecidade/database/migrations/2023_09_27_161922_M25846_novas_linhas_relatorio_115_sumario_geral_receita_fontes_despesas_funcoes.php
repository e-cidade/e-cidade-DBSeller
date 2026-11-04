<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25846NovasLinhasRelatorio115SumarioGeralReceitaFontesDespesasFuncoes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
delete from orcamento.orcparamseqfiltropadrao where o132_orcparamrel = 115 and o132_orcparamseq in (25,26,27);
delete from configuracoes.orcparamseqorcparamseqcoluna where o116_codparamrel = 115 and o116_codseq in (25,26,27);
delete from orcamento.orcparamseq where o69_codparamrel = 115 and o69_codseq in (25,26,27);       

INSERT INTO orcamento.orcparamseq VALUES(115, 25, '4.7.2 - Receita de Contribuições Intra', 1, 1, 0, false, false, false, false, false, '4.7.2 - Receita de Contribuições Intra', false, false, 18, 4, '', false, 0);
INSERT INTO orcamento.orcparamseq VALUES(115, 26, '4.7 - RECEITAS INTRA', 1, 1, 0, false, false, false, false, false, '4.7 - RECEITAS INTRA', false, true, 17, 2, '', false, 0);
INSERT INTO orcamento.orcparamseq VALUES(115, 27, '4.7.9 - Outras Receitas Correntes Intra', 1, 1, 0, false, false, false, false, false, '4.7.9 - Outras Receitas Correntes Intra', false, false, 19, 4, '', false, 0);

insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),27,115,142,1,17,''); 
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),27,115,142,1,18,''); 
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),27,115,142,1,19,''); 
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),27,115,142,1,20,''); 
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),27,115,142,1,21,''); 
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),27,115,142,1,22,''); 
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),27,115,142,1,23,''); 
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),27,115,142,1,24,''); 
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),27,115,142,1,25,''); 
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),27,115,142,1,26,''); 
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),27,115,142,1,27,''); 
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),27,115,142,1,28,''); 
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),26,115,142,1,17,'(L[18]->total+L[19]->total)');
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),26,115,142,1,18,'(L[18]->total+L[19]->total)');
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),26,115,142,1,19,'(L[18]->total+L[19]->total)');
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),26,115,142,1,20,'(L[18]->total+L[19]->total)');
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),26,115,142,1,21,'(L[18]->total+L[19]->total)');
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),26,115,142,1,22,'(L[18]->total+L[19]->total)');
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),26,115,142,1,23,'(L[18]->total+L[19]->total)');
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),26,115,142,1,24,'(L[18]->total+L[19]->total)');
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),26,115,142,1,25,'(L[18]->total+L[19]->total)');
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),26,115,142,1,26,'(L[18]->total+L[19]->total)');
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),26,115,142,1,27,'(L[18]->total+L[19]->total)');
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),26,115,142,1,28,'(L[18]->total+L[19]->total)');
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),25,115,142,1,17,'(L[18]->total+L[19]->total)');
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),25,115,142,1,18,'(L[18]->total+L[19]->total)');
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),25,115,142,1,19,'(L[18]->total+L[19]->total)');
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),25,115,142,1,20,'(L[18]->total+L[19]->total)');
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),25,115,142,1,21,'(L[18]->total+L[19]->total)');
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),25,115,142,1,22,'(L[18]->total+L[19]->total)');
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),25,115,142,1,23,'(L[18]->total+L[19]->total)');
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),25,115,142,1,24,'(L[18]->total+L[19]->total)');
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),25,115,142,1,25,'(L[18]->total+L[19]->total)');
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),25,115,142,1,26,'(L[18]->total+L[19]->total)');
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),25,115,142,1,27,'(L[18]->total+L[19]->total)');
insert into orcparamseqorcparamseqcoluna values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'),25,115,142,1,28,'(L[18]->total+L[19]->total)');

insert into orcparamseqfiltropadrao values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 115, 25, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>                                   
<filter>                                                                      
 <contas>                                                                     
  <conta estrutural="472000000000000" nivel="" exclusao="false" indicador=""/>
 </contas>                                                                    
 <orgao operador="in" valor="" id="orgao"/>                                   
 <unidade operador="in" valor="" id="unidade"/>                               
 <funcao operador="in" valor="" id="funcao"/>                                 
 <subfuncao operador="in" valor="" id="subfuncao"/>                           
 <programa operador="in" valor="" id="programa"/>                             
 <projativ operador="in" valor="" id="projativ"/>                             
 <recurso operador="in" valor="" id="recurso"/>                               
 <recursocontalinha numerolinha="" id="recursocontalinha"/>                   
 <observacao valor=""/>                                                       
 <desdobrarlinha valor="false"/>                                              
</filter>');

insert into orcparamseqfiltropadrao values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 115, 26, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>                                   
<filter>                                                                      
 <contas>                                                                     
  <conta estrutural="472000000000000" nivel="" exclusao="false" indicador=""/>
 </contas>                                                                    
 <orgao operador="in" valor="" id="orgao"/>                                   
 <unidade operador="in" valor="" id="unidade"/>                               
 <funcao operador="in" valor="" id="funcao"/>                                 
 <subfuncao operador="in" valor="" id="subfuncao"/>                           
 <programa operador="in" valor="" id="programa"/>                             
 <projativ operador="in" valor="" id="projativ"/>                             
 <recurso operador="in" valor="" id="recurso"/>                               
 <recursocontalinha numerolinha="" id="recursocontalinha"/>                   
 <observacao valor=""/>                                                       
 <desdobrarlinha valor="false"/>                                              
</filter>');

insert into orcparamseqfiltropadrao values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 115, 27, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>                                   
<filter>                                                                      
 <contas>                                                                     
  <conta estrutural="479000000000000" nivel="" exclusao="false" indicador=""/>
 </contas>                                                                    
 <orgao operador="in" valor="" id="orgao"/>                                   
 <unidade operador="in" valor="" id="unidade"/>                               
 <funcao operador="in" valor="" id="funcao"/>                                 
 <subfuncao operador="in" valor="" id="subfuncao"/>                           
 <programa operador="in" valor="" id="programa"/>                             
 <projativ operador="in" valor="" id="projativ"/>                             
 <recurso operador="in" valor="" id="recurso"/>                               
 <recursocontalinha numerolinha="" id="recursocontalinha"/>                   
 <observacao valor=""/>                                                       
 <desdobrarlinha valor="false"/>                                              
</filter>');

update orcparamseqorcparamseqcoluna set o116_formula = '(F[1]+F[20]+L[23]->total)' where o116_codparamrel = 115 and o116_codseq = 23 and o116_orcparamseqcoluna = 142;
update orcparamseqorcparamseqcoluna set o116_formula = '(F[2]+F[11]+F[17])' where o116_codparamrel = 115 and o116_codseq = 1 and o116_orcparamseqcoluna = 142;
update orcparamseqorcparamseqcoluna set o116_formula = '(L[21]->total+L[22]->total)' where o116_codparamrel = 115 and o116_codseq = 17 and o116_orcparamseqcoluna = 142;
update orcparamseqorcparamseqcoluna set o116_formula = '(L[25]->total+L[26]->total)' where o116_codparamrel = 115 and o116_codseq = 24 and o116_orcparamseqcoluna = 142;
update orcparamseqorcparamseqcoluna set o116_formula = '(L[3]->total+L[4]->total+L[5]->total+L[6]->total+L[7]->total+L[8]->total+L[9]->total+L[10]->total)' where o116_codparamrel = 115 and o116_codseq = 2 and o116_orcparamseqcoluna = 142;
update orcparamseqorcparamseqcoluna set o116_formula = '(L[18]->total+L[19]->total)' where o116_codparamrel = 115 and o116_codseq = 26 and o116_orcparamseqcoluna = 142;
update orcparamseqorcparamseqcoluna set o116_formula = '(L[12]->total+L[13]->total+L[14]->total+L[15]->total+L[16]->total)' where o116_codparamrel = 115 and o116_codseq = 11 and o116_orcparamseqcoluna = 142;
update orcparamseqorcparamseqcoluna set o116_formula = '(L[18]->total+L[19]->total)' where o116_codparamrel = 115 and o116_codseq = 25 and o116_orcparamseqcoluna = 142;

update orcamento.orcparamseq set o69_ordem = 1  where o69_codseq =  1 and o69_codparamrel = 115;
update orcamento.orcparamseq set o69_ordem = 2  where o69_codseq =  2 and o69_codparamrel = 115;
update orcamento.orcparamseq set o69_ordem = 3  where o69_codseq =  3 and o69_codparamrel = 115;
update orcamento.orcparamseq set o69_ordem = 4  where o69_codseq =  4 and o69_codparamrel = 115;
update orcamento.orcparamseq set o69_ordem = 5  where o69_codseq =  5 and o69_codparamrel = 115;
update orcamento.orcparamseq set o69_ordem = 6  where o69_codseq =  6 and o69_codparamrel = 115;
update orcamento.orcparamseq set o69_ordem = 7  where o69_codseq =  7 and o69_codparamrel = 115;
update orcamento.orcparamseq set o69_ordem = 8  where o69_codseq =  8 and o69_codparamrel = 115;
update orcamento.orcparamseq set o69_ordem = 9  where o69_codseq =  9 and o69_codparamrel = 115;
update orcamento.orcparamseq set o69_ordem = 10 where o69_codseq = 10 and o69_codparamrel = 115;
update orcamento.orcparamseq set o69_ordem = 11 where o69_codseq = 11 and o69_codparamrel = 115;
update orcamento.orcparamseq set o69_ordem = 12 where o69_codseq = 12 and o69_codparamrel = 115;
update orcamento.orcparamseq set o69_ordem = 13 where o69_codseq = 13 and o69_codparamrel = 115;
update orcamento.orcparamseq set o69_ordem = 14 where o69_codseq = 14 and o69_codparamrel = 115;
update orcamento.orcparamseq set o69_ordem = 15 where o69_codseq = 15 and o69_codparamrel = 115;
update orcamento.orcparamseq set o69_ordem = 16 where o69_codseq = 16 and o69_codparamrel = 115;
update orcamento.orcparamseq set o69_ordem = 17 where o69_codseq = 26 and o69_codparamrel = 115;
update orcamento.orcparamseq set o69_ordem = 18 where o69_codseq = 25 and o69_codparamrel = 115;
update orcamento.orcparamseq set o69_ordem = 19 where o69_codseq = 27 and o69_codparamrel = 115;
update orcamento.orcparamseq set o69_ordem = 20 where o69_codseq = 17 and o69_codparamrel = 115;
update orcamento.orcparamseq set o69_ordem = 21 where o69_codseq = 21 and o69_codparamrel = 115;
update orcamento.orcparamseq set o69_ordem = 22 where o69_codseq = 22 and o69_codparamrel = 115;
update orcamento.orcparamseq set o69_ordem = 23 where o69_codseq = 19 and o69_codparamrel = 115;
update orcamento.orcparamseq set o69_ordem = 24 where o69_codseq = 23 and o69_codparamrel = 115;
update orcamento.orcparamseq set o69_ordem = 25 where o69_codseq = 18 and o69_codparamrel = 115;
update orcamento.orcparamseq set o69_ordem = 26 where o69_codseq = 20 and o69_codparamrel = 115;
update orcamento.orcparamseq set o69_ordem = 27 where o69_codseq = 24 and o69_codparamrel = 115;
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
        //
        $sql = <<<SQL
delete from orcamento.orcparamseqfiltropadrao where o132_orcparamrel = 115 and o132_orcparamseq in (25,26,27);
delete from configuracoes.orcparamseqorcparamseqcoluna where o116_codparamrel = 115 and o116_codseq in (25,26,27);
delete from orcamento.orcparamseq where o69_codparamrel = 115 and o69_codseq in (25,26,27);     

update orcparamseqorcparamseqcoluna set o116_formula = '(F[1]+F[17]+L[20]->total)' where o116_codparamrel = 115 and o116_codseq = 23 and o116_orcparamseqcoluna = 142;
update orcparamseqorcparamseqcoluna set o116_formula = '(F[2]+F[11])' where o116_codparamrel = 115 and o116_codseq = 1 and o116_orcparamseqcoluna = 142;
update orcparamseqorcparamseqcoluna set o116_formula = '(L[12]->total+L[13]->total+L[14]->total+L[15]->total+L[16]->total)' where o116_codparamrel = 115 and o116_codseq = 11 and o116_orcparamseqcoluna = 142;
update orcparamseqorcparamseqcoluna set o116_formula = '(L[18]->total+L[19]->total)' where o116_codparamrel = 115 and o116_codseq = 17 and o116_orcparamseqcoluna = 142;
update orcparamseqorcparamseqcoluna set o116_formula = '(L[22]->total+L[23]->total)' where o116_codparamrel = 115 and o116_codseq = 24 and o116_orcparamseqcoluna = 142;
update orcparamseqorcparamseqcoluna set o116_formula = '(L[3]->total+L[4]->total+L[5]->total+L[6]->total+L[7]->total+L[8]->total+L[9]->total+L[10]->total)' where o116_codparamrel = 115 and o116_codseq = 2 and o116_orcparamseqcoluna = 142;

update orcparamseq set o69_ordem = 1  where o69_codseq =  1 and o69_codparamrel = 115;
update orcparamseq set o69_ordem = 2  where o69_codseq =  2 and o69_codparamrel = 115;
update orcparamseq set o69_ordem = 3  where o69_codseq =  3 and o69_codparamrel = 115;
update orcparamseq set o69_ordem = 4  where o69_codseq =  4 and o69_codparamrel = 115;
update orcparamseq set o69_ordem = 5  where o69_codseq =  5 and o69_codparamrel = 115;
update orcparamseq set o69_ordem = 6  where o69_codseq =  6 and o69_codparamrel = 115;
update orcparamseq set o69_ordem = 7  where o69_codseq =  7 and o69_codparamrel = 115;
update orcparamseq set o69_ordem = 8  where o69_codseq =  8 and o69_codparamrel = 115;
update orcparamseq set o69_ordem = 9  where o69_codseq =  9 and o69_codparamrel = 115;
update orcparamseq set o69_ordem = 10 where o69_codseq = 10 and o69_codparamrel = 115;
update orcparamseq set o69_ordem = 11 where o69_codseq = 11 and o69_codparamrel = 115;
update orcparamseq set o69_ordem = 12 where o69_codseq = 12 and o69_codparamrel = 115;
update orcparamseq set o69_ordem = 13 where o69_codseq = 13 and o69_codparamrel = 115;
update orcparamseq set o69_ordem = 14 where o69_codseq = 14 and o69_codparamrel = 115;
update orcparamseq set o69_ordem = 15 where o69_codseq = 15 and o69_codparamrel = 115;
update orcparamseq set o69_ordem = 16 where o69_codseq = 16 and o69_codparamrel = 115;
update orcparamseq set o69_ordem = 17 where o69_codseq = 17 and o69_codparamrel = 115;
update orcparamseq set o69_ordem = 18 where o69_codseq = 18 and o69_codparamrel = 115;
update orcparamseq set o69_ordem = 19 where o69_codseq = 19 and o69_codparamrel = 115;
update orcparamseq set o69_ordem = 20 where o69_codseq = 20 and o69_codparamrel = 115;
update orcparamseq set o69_ordem = 21 where o69_codseq = 21 and o69_codparamrel = 115;
update orcparamseq set o69_ordem = 22 where o69_codseq = 22 and o69_codparamrel = 115;
update orcparamseq set o69_ordem = 23 where o69_codseq = 23 and o69_codparamrel = 115;
update orcparamseq set o69_ordem = 24 where o69_codseq = 24 and o69_codparamrel = 115;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
