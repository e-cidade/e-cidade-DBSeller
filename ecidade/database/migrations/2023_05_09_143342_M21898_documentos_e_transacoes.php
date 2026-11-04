<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M21898DocumentosETransacoes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into conhistdoc( c53_coddoc ,c53_descr ,c53_tipo )
values (350, '13º COMPETÊNCIA - SERVIDORES RPPS' ,300 ),
       (351, 'ESTORNO DE 13º COMPETÊNCIA - SERVIDORES RPPS' ,301 ),
       (352, 'ENCARGOS 13º COMPETÊNCIA - SERVIDORES RPPS' ,300 ),
       (353, 'ESTORNO DE ENCARGOS 13º COMPETÊNCIA - SERVIDORES RPPS' ,301 ),
       (354, '13º COMPETÊNCIA - SERVIDORES RGPS' ,300 ),
       (355, 'ESTORNO DE 13º COMPETÊNCIA - SERVIDORES RGPS' ,301 ),
       (356, 'INSS SOBRE 13º COMPETÊNCIA - SERVIDORES RGPS' ,300 ),
       (357, 'ESTORNO DE INSS SOBRE 13º COMPETÊNCIA - SERVIDORES RGPS' ,301 ),
       (358, 'FGTS SOBRE 13º COMPETÊNCIA - SERVIDORES RGPS' ,300 ),
       (359, 'ESTORNO DE FGTS SOBRE 13º COMPETÊNCIA - SERVIDORES RGPS' ,301 ),
       (360, 'FÉRIAS COMPETÊNCIA - SERVIDORES RPPS' ,300 ),
       (361, 'ESTORNO DE FÉRIAS COMPETÊNCIA - SERVIDORES RPPS' ,301 ),
       (362, 'ABONO CONSTITUCIONAL FÉRIAS COMPETÊNCIA - SERVIDORES RPPS' ,300 ),
       (363, 'ESTORNO DE ABONO CONSTITUCIONAL FÉRIAS COMPETÊNCIA - SERVIDORES RPPS' ,301 ),
       (364, 'ENCARGOS FÉRIAS COMPETÊNCIA - SERVIDORES RPPS' ,300 ),
       (365, 'ESTORNO DE ENCARGOS FÉRIAS COMPETÊNCIA - SERVIDORES RPPS' ,301 ),
       (366, 'FÉRIAS COMPETÊNCIA - SERVIDORES RGPS' ,300 ),
       (367, 'ESTORNO DE FÉRIAS COMPETÊNCIA - SERVIDORES RGPS' ,301 ),
       (368, 'ABONO CONSTITUCIONAL FÉRIAS COMPETÊNCIA - SERVIDORES RGPS' ,300 ),
       (369, 'ESTORNO DE ABONO CONSTITUCIONAL FÉRIAS COMPETÊNCIA - SERVIDORES RGPS' ,301 ),
       (370, 'INSS SOBRE FÉRIAS COMPETÊNCIA - SERVIDORES RGPS' ,300 ),
       (371, 'ESTORNO DE INSS SOBRE FÉRIAS COMPETÊNCIA - SERVIDORES RGPS' ,301 ),
       (372, 'FGTS SOBRE FÉRIAS COMPETÊNCIA - SERVIDORES RGPS' ,300 ),
       (373, 'ESTORNO DE FGTS SOBRE FÉRIAS COMPETÊNCIA - SERVIDORES RGPS' ,301 );

insert into conhistdocregra
select nextval('conhistdocregra_c92_sequencial_seq'),
       c53_coddoc,
       'DOCUMENTO'||' '||c53_coddoc,
       'select 1 from conhistdoc where c53_coddoc ='||c53_coddoc,2023
   from conhistdoc
  where c53_coddoc between 350 and 373;

insert into vinculoeventoscontabeis select nextval('vinculoeventoscontabeis_c115_sequencial_seq'),350,351;
insert into vinculoeventoscontabeis select nextval('vinculoeventoscontabeis_c115_sequencial_seq'),352,353;
insert into vinculoeventoscontabeis select nextval('vinculoeventoscontabeis_c115_sequencial_seq'),354,355;
insert into vinculoeventoscontabeis select nextval('vinculoeventoscontabeis_c115_sequencial_seq'),356,357;
insert into vinculoeventoscontabeis select nextval('vinculoeventoscontabeis_c115_sequencial_seq'),358,359;
insert into vinculoeventoscontabeis select nextval('vinculoeventoscontabeis_c115_sequencial_seq'),360,361;
insert into vinculoeventoscontabeis select nextval('vinculoeventoscontabeis_c115_sequencial_seq'),362,363;
insert into vinculoeventoscontabeis select nextval('vinculoeventoscontabeis_c115_sequencial_seq'),364,365;
insert into vinculoeventoscontabeis select nextval('vinculoeventoscontabeis_c115_sequencial_seq'),366,367;
insert into vinculoeventoscontabeis select nextval('vinculoeventoscontabeis_c115_sequencial_seq'),368,369;
insert into vinculoeventoscontabeis select nextval('vinculoeventoscontabeis_c115_sequencial_seq'),370,371;
insert into vinculoeventoscontabeis select nextval('vinculoeventoscontabeis_c115_sequencial_seq'),372,373;

insert into conhist (c50_codhist, c50_compl, c50_descr)
values (9880, 't', 'APROPRIAÇÃO PELA COMPETÊNCIA - FÉRIAS'),
       (9881, 't', 'ESTORNO DE APROPRIAÇÃO PELA COMPETÊNCIA - FÉRIAS'),
       (9882, 't', 'APROPRIAÇÃO PELA COMPETÊNCIA - 13 SALARIO'),
       (9883, 't', 'ESTORNO DE APROPRIAÇÃO PELA COMPETÊNCIA - 13 SALARIO');


insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,350 ,1 );
insert into contranslan(c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9882 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,351 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9883 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,352 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9882 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,353 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9883 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,354 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9882 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,355 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9883 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,356 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9882 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,357 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9883 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,358 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9882 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,359 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9883 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,360 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9880 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,361 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9881 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,362 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9880 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,363 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9881 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,364 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9880 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,365 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9881 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,366 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9880 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,367 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9881 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,368 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9880 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,369 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9881 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,370 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9880 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,371 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9881 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,372 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9880 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,373 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9881 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );

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
        $docs = "350,351,352,353,354,355,356,357,358,359,360,361,362,363,364,365,366,367,368,369,370,371,372,373";
        DB::connection()->getPdo()->exec(<<<SQL

create temp table w_transacao as select c45_seqtrans from contrans where c45_coddoc in ($docs);
delete from contranslan using w_transacao where c46_seqtrans = c45_seqtrans;
delete from contrans using w_transacao where contrans.c45_seqtrans = w_transacao.c45_seqtrans;
delete from conhist where c50_codhist in (9880,9881,9882,9883);
delete from vinculoeventoscontabeis where c115_conhistdocinclusao in ($docs);
delete from conhistdocregra where c92_conhistdoc in ($docs);
delete from conhistdoc where c53_coddoc in ($docs);

SQL
        );
    }
}
