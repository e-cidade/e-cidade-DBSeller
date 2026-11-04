<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24565NovaLinhaAnexoIVRGF extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into orcparamseq values (185,30,'Demais contribuições sociais', 1, 0, 1, false, false, false, false, false, 'Demais contribuições sociais', true, false, 29, 2, null, false, 3);

insert into orcparamseqorcparamseqcoluna( o116_sequencial ,o116_codseq ,o116_codparamrel ,o116_orcparamseqcoluna ,o116_ordem ,o116_periodo ,o116_formula ) values ( nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq') ,30 ,185 ,217 ,1 ,12 ,'#saldo_anterior_credito - #saldo_anterior_debito' );
insert into orcparamseqorcparamseqcoluna( o116_sequencial ,o116_codseq ,o116_codparamrel ,o116_orcparamseqcoluna ,o116_ordem ,o116_periodo ,o116_formula ) values ( nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq') ,30 ,185 ,217 ,1 ,13 ,'#saldo_anterior_credito - #saldo_anterior_debito' );
insert into orcparamseqorcparamseqcoluna( o116_sequencial ,o116_codseq ,o116_codparamrel ,o116_orcparamseqcoluna ,o116_ordem ,o116_periodo ,o116_formula ) values ( nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq') ,30 ,185 ,217 ,1 ,14 ,'#saldo_anterior_credito - #saldo_anterior_debito' );
insert into orcparamseqorcparamseqcoluna( o116_sequencial ,o116_codseq ,o116_codparamrel ,o116_orcparamseqcoluna ,o116_ordem ,o116_periodo ,o116_formula ) values ( nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq') ,30 ,185 ,217 ,1 ,15 ,'#saldo_anterior_credito - #saldo_anterior_debito' );
insert into orcparamseqorcparamseqcoluna( o116_sequencial ,o116_codseq ,o116_codparamrel ,o116_orcparamseqcoluna ,o116_ordem ,o116_periodo ,o116_formula ) values ( nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq') ,30 ,185 ,217 ,1 ,16 ,'#saldo_anterior_credito - #saldo_anterior_debito' );

insert into orcparamseqorcparamseqcoluna( o116_sequencial ,o116_codseq ,o116_codparamrel ,o116_orcparamseqcoluna ,o116_ordem ,o116_periodo ,o116_formula ) values ( nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq') ,30 ,185 ,218 ,2 ,12 ,'(in_array(substr(#estrutural, 0, 1), array(1, 3, 5, 7)) && #sinal_final == \"C\") || (in_array(substr(#estrutural, 0, 1), array(2, 4, 6, 8)) && #sinal_final == \"D\") ? #saldo_final *= -1 : #saldo_final' );
insert into orcparamseqorcparamseqcoluna( o116_sequencial ,o116_codseq ,o116_codparamrel ,o116_orcparamseqcoluna ,o116_ordem ,o116_periodo ,o116_formula ) values ( nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq') ,30 ,185 ,218 ,2 ,13 ,'(in_array(substr(#estrutural, 0, 1), array(1, 3, 5, 7)) && #sinal_final == \"C\") || (in_array(substr(#estrutural, 0, 1), array(2, 4, 6, 8)) && #sinal_final == \"D\") ? #saldo_final *= -1 : #saldo_final' );
insert into orcparamseqorcparamseqcoluna( o116_sequencial ,o116_codseq ,o116_codparamrel ,o116_orcparamseqcoluna ,o116_ordem ,o116_periodo ,o116_formula ) values ( nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq') ,30 ,185 ,218 ,2 ,14 ,'(in_array(substr(#estrutural, 0, 1), array(1, 3, 5, 7)) && #sinal_final == \"C\") || (in_array(substr(#estrutural, 0, 1), array(2, 4, 6, 8)) && #sinal_final == \"D\") ? #saldo_final *= -1 : #saldo_final' );
insert into orcparamseqorcparamseqcoluna( o116_sequencial ,o116_codseq ,o116_codparamrel ,o116_orcparamseqcoluna ,o116_ordem ,o116_periodo ,o116_formula ) values ( nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq') ,30 ,185 ,218 ,2 ,15 ,'(in_array(substr(#estrutural, 0, 1), array(1, 3, 5, 7)) && #sinal_final == \"C\") || (in_array(substr(#estrutural, 0, 1), array(2, 4, 6, 8)) && #sinal_final == \"D\") ? #saldo_final *= -1 : #saldo_final' );
insert into orcparamseqorcparamseqcoluna( o116_sequencial ,o116_codseq ,o116_codparamrel ,o116_orcparamseqcoluna ,o116_ordem ,o116_periodo ,o116_formula ) values ( nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq') ,30 ,185 ,218 ,2 ,16 ,'(in_array(substr(#estrutural, 0, 1), array(1, 3, 5, 7)) && #sinal_final == \"C\") || (in_array(substr(#estrutural, 0, 1), array(2, 4, 6, 8)) && #sinal_final == \"D\") ? #saldo_final *= -1 : #saldo_final' );

update orcparamseq set o69_ordem = 30 where o69_codparamrel = 185 and o69_codseq = 29;
update orcparamseqorcparamseqcoluna set o116_formula = 'L[26]->ateperiodo+L[27]->ateperiodo+L[29]->ateperiodo+L[30]->ateperiodo' where  o116_codparamrel = 185  and o116_codseq = 25 and o116_ordem = 2;
update orcparamseqorcparamseqcoluna set o116_formula = 'L[26]->noperiodo+L[27]->noperiodo+L[29]->noperiodo+L[30]->noperiodo' where  o116_codparamrel = 185  and o116_codseq = 25 and o116_ordem = 1;

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
delete from orcparamseqorcparamseqcoluna where o116_codseq = 30 and o116_codparamrel = 185; 
delete from orcparamseq where o69_codparamrel = 185 and o69_codseq = 30;
update orcparamseq set o69_ordem = 29 where o69_codparamrel = 185 and o69_codseq = 29;
update orcparamseqorcparamseqcoluna set o116_formula = 'L[26]->ateperiodo+L[27]->ateperiodo+L[29]->ateperiodo' where  o116_codparamrel = 185  and o116_codseq = 25 and o116_ordem = 2;
update orcparamseqorcparamseqcoluna set o116_formula = 'L[26]->noperiodo+L[27]->noperiodo+L[29]->noperiodo' where  o116_codparamrel = 185  and o116_codseq = 25 and o116_ordem = 1;

SQL
        );
    }
}
