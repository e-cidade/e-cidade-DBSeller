<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M22790GerenciamentoValoresFontesRecursosExerciciosAnteriores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $sql = <<<SQL


select setval('conhistdocregra_c92_sequencial_seq', (select max(c92_sequencial) from contabilidade.conhistdocregra));

insert into conhistdoc( c53_coddoc ,c53_descr ,c53_tipo ) values ( 2021 ,'RECURSOS DO EXERCÍCIO ANTERIOR - CONTROLES' ,2000 );
insert into conhist( c50_codhist ,c50_compl ,c50_descr ) values ( 9601 ,'t' ,'ABERTURA DO EXERCICIO' );
insert into contrans select nextval('contrans_c45_seqtrans_seq'),  2023 , 2021 ,1 ;

insert into contranslan
select nextval('contranslan_c46_seqtranslan_seq'),
       currval('contrans_c45_seqtrans_seq'),
                    9601 ,
                    'PRIMEIRO LANÇAMENTO' ,
                    0 ,
                    'true' ,
                    0 ,
                    'PRIMEIRO LANÇAMENTO' ,1 ;
insert into conhistdocregra
 select nextval('conhistdocregra_c92_sequencial_seq'),
         2021,
        'Origem dos dados documento 2021',
        '',
        2023;

        insert into vinculoeventoscontabeis select nextval('vinculoeventoscontabeis_c115_sequencial_seq'),2021,null;





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



delete from conhistdocregra where c92_conhistdoc = 2021;
delete from contranslan where c46_codhist = 9601;
delete from contrans where c45_coddoc = 2021;
delete from conhist where c50_codhist = 9601;
delete from conhistdoc where c53_coddoc = 2021;
delete from vinculoeventoscontabeis where c115_conhistdocinclusao = 2021;

SQL;
        DB::connection()->getPdo()->exec($sql);
    }




}
