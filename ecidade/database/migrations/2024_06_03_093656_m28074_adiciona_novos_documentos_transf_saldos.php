<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28074AdicionaNovosDocumentosTransfSaldos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
insert into contabilidade.conhistdoc (c53_coddoc,c53_descr,c53_tipo) values (2034, 'TRANSFERENCIA DE SALDOS DE RPNP INSCRITOS A LIQUIDAR', 2000);

insert into contabilidade.vinculoeventoscontabeis
values (nextval('contabilidade.vinculoeventoscontabeis_c115_sequencial_seq'), 2034, null);

insert into contabilidade.contrans(c45_seqtrans, c45_anousu, c45_coddoc, c45_instit)
values (nextval('contrans_c45_seqtrans_seq'), 2024, 2034, 1);

insert into contabilidade.contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem )
    values ( nextval('contranslan_c46_seqtranslan_seq') , currval('contrans_c45_seqtrans_seq'), 9600 ,'PRIMEIRO LANCAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANCAMENTO' ,1 );

    insert into contabilidade.conhistdocregra select nextval('conhistdocregra_c92_sequencial_seq'), 2034, 'Registros', 
'
    select c75_numemp as empenho,
           c70_valor as valor,
           o206_recurso as codigo_recurso                                
      from conlancam
      join conlancamdoc on c71_codlan = c70_codlan 
      join conlancamemp on c70_codlan = c75_codlan 
      join orcamento.origemcomplementorecurso on o206_origem = 2 
                                             and o206_numero = c70_codlan
      join conlancaminstit   on c02_codlan =  c70_codlan                                    
      where c71_coddoc in ( 1011)
      and c70_anousu = fc_getsession(\'DB_anousu\')::int -1 
      and c02_instit = fc_getsession(\'DB_instit\')::int;
' , 2024;






insert into contabilidade.conhistdoc (c53_coddoc,c53_descr,c53_tipo) values (2035, 'TRANSFERENCIA DE SALDOS DE RPNP INSCRITOS EM LIQUIDAÇÃO', 2000);

insert into contabilidade.vinculoeventoscontabeis
values (nextval('contabilidade.vinculoeventoscontabeis_c115_sequencial_seq'), 2035, null);

insert into contabilidade.contrans(c45_seqtrans, c45_anousu, c45_coddoc, c45_instit)
values (nextval('contrans_c45_seqtrans_seq'), 2024, 2035, 1);

insert into contabilidade.contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem )
    values ( nextval('contranslan_c46_seqtranslan_seq') , currval('contrans_c45_seqtrans_seq'), 9600 ,'PRIMEIRO LANCAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANCAMENTO' ,1 );

    insert into contabilidade.conhistdocregra select nextval('conhistdocregra_c92_sequencial_seq'), 2035, 'Registros', 
'
    select c75_numemp as empenho,
           c70_valor as valor,
           o206_recurso as codigo_recurso                             
      from conlancam
      join conlancamdoc on c71_codlan = c70_codlan 
      join conlancamemp on c70_codlan = c75_codlan 
      join orcamento.origemcomplementorecurso on o206_origem = 2 
                                             and o206_numero = c70_codlan
      join conlancaminstit   on c02_codlan =  c70_codlan                                    
      where c71_coddoc in ( 1012)
      and c70_anousu = fc_getsession(\'DB_anousu\')::int -1 
      and c02_instit = fc_getsession(\'DB_instit\')::int;
' , 2024;


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


delete from contabilidade.conhistdocregra where c92_conhistdoc in (2034, 2035);
delete from contabilidade.vinculoeventoscontabeis where c115_conhistdocinclusao in (2034, 2035);
delete from contabilidade.contranslan where c46_seqtrans in (
    select c45_seqtrans from contabilidade.contrans where c45_coddoc in (2034, 2035)
);
delete from contabilidade.contrans where c45_coddoc in (2034, 2035);
delete from contabilidade.conhistdoc where c53_coddoc  in (2034, 2035);



SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
