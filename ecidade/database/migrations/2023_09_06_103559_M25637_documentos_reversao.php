<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25637DocumentosReversao extends Migration
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
values
       (420, 'REVERSÃO - 13º COMPETÊNCIA - SERVIDORES RPPS' ,300 ),
       (421, 'REVERSÃO - ESTORNO DE 13º COMPETÊNCIA - SERVIDORES RPPS' ,301 ),
       (422, 'REVERSÃO - ENCARGOS 13º COMPETÊNCIA - SERVIDORES RPPS' ,300 ),
       (423, 'REVERSÃO - ESTORNO DE ENCARGOS 13º COMPETÊNCIA - SERVIDORES RPPS' ,301 ),
       (424, 'REVERSÃO - 13º COMPETÊNCIA - SERVIDORES RGPS' ,300 ),
       (425, 'REVERSÃO - ESTORNO DE 13º COMPETÊNCIA - SERVIDORES RGPS' ,301 ),
       (426, 'REVERSÃO - INSS SOBRE 13º COMPETÊNCIA - SERVIDORES RGPS' ,300 ),
       (427, 'REVERSÃO - ESTORNO DE INSS SOBRE 13º COMPETÊNCIA - SERVIDORES RGPS' ,301 ),
       (428, 'REVERSÃO - FGTS SOBRE 13º COMPETÊNCIA - SERVIDORES RGPS' ,300 ),
       (429, 'REVERSÃO - ESTORNO DE FGTS SOBRE 13º COMPETÊNCIA - SERVIDORES RGPS' ,301 ),
       (430, 'REVERSÃO - FÉRIAS COMPETÊNCIA - SERVIDORES RPPS' ,300 ),
       (431, 'REVERSÃO - ESTORNO DE FÉRIAS COMPETÊNCIA - SERVIDORES RPPS' ,301 ),
       (432, 'REVERSÃO - ABONO CONSTITUCIONAL FÉRIAS COMPETÊNCIA - SERVIDORES RPPS' ,300 ),
       (433, 'REVERSÃO - ESTORNO DE ABONO CONSTITUCIONAL FÉRIAS COMPETÊNCIA - SERVIDORES RPPS' ,301 ),
       (434, 'REVERSÃO - ENCARGOS FÉRIAS COMPETÊNCIA - SERVIDORES RPPS' ,300 ),
       (435, 'REVERSÃO - ESTORNO DE ENCARGOS FÉRIAS COMPETÊNCIA - SERVIDORES RPPS' ,301 ),
       (436, 'REVERSÃO - FÉRIAS COMPETÊNCIA - SERVIDORES RGPS' ,300 ),
       (437, 'REVERSÃO - ESTORNO DE FÉRIAS COMPETÊNCIA - SERVIDORES RGPS' ,301 ),
       (438, 'REVERSÃO - ABONO CONSTITUCIONAL FÉRIAS COMPETÊNCIA - SERVIDORES RGPS' ,300 ),
       (439, 'REVERSÃO - ESTORNO DE ABONO CONSTITUCIONAL FÉRIAS COMPETÊNCIA - SERVIDORES RGPS' ,301 ),
       (440, 'REVERSÃO - INSS SOBRE FÉRIAS COMPETÊNCIA - SERVIDORES RGPS' ,300 ),
       (441, 'REVERSÃO - ESTORNO DE INSS SOBRE FÉRIAS COMPETÊNCIA - SERVIDORES RGPS' ,301 ),
       (442, 'REVERSÃO - FGTS SOBRE FÉRIAS COMPETÊNCIA - SERVIDORES RGPS' ,300 ),
       (443, 'REVERSÃO - ESTORNO DE FGTS SOBRE FÉRIAS COMPETÊNCIA - SERVIDORES RGPS' ,301 );

insert into conhistdocregra
select nextval('conhistdocregra_c92_sequencial_seq'),
       c53_coddoc,
       'DOCUMENTO'||' '||c53_coddoc,
       'select 1 from conhistdoc where c53_coddoc ='||c53_coddoc,2023
   from conhistdoc
  where c53_coddoc between 420 and 443;

insert into vinculoeventoscontabeis select nextval('vinculoeventoscontabeis_c115_sequencial_seq'), 420, 421;
insert into vinculoeventoscontabeis select nextval('vinculoeventoscontabeis_c115_sequencial_seq'), 422, 423;
insert into vinculoeventoscontabeis select nextval('vinculoeventoscontabeis_c115_sequencial_seq'), 424, 425;
insert into vinculoeventoscontabeis select nextval('vinculoeventoscontabeis_c115_sequencial_seq'), 426, 427;
insert into vinculoeventoscontabeis select nextval('vinculoeventoscontabeis_c115_sequencial_seq'), 428, 429;
insert into vinculoeventoscontabeis select nextval('vinculoeventoscontabeis_c115_sequencial_seq'), 430, 431;
insert into vinculoeventoscontabeis select nextval('vinculoeventoscontabeis_c115_sequencial_seq'), 432, 433;
insert into vinculoeventoscontabeis select nextval('vinculoeventoscontabeis_c115_sequencial_seq'), 434, 435;
insert into vinculoeventoscontabeis select nextval('vinculoeventoscontabeis_c115_sequencial_seq'), 436, 437;
insert into vinculoeventoscontabeis select nextval('vinculoeventoscontabeis_c115_sequencial_seq'), 438, 439;
insert into vinculoeventoscontabeis select nextval('vinculoeventoscontabeis_c115_sequencial_seq'), 440, 441;
insert into vinculoeventoscontabeis select nextval('vinculoeventoscontabeis_c115_sequencial_seq'), 442, 443;

insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,420 ,1 );
insert into contranslan(c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9882 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,421 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9883 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,422 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9882 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,423 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9883 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,424 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9882 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,425 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9883 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,426 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9882 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,427 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9883 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,428 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9882 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,429 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9883 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,430 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9880 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,431 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9881 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,432 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9880 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,433 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9881 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,434 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9880 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,435 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9881 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,436 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9880 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,437 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9881 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,438 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9880 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,439 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9881 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,440 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9880 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,441 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9881 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,442 ,1 );
insert into contranslan( c46_seqtranslan ,c46_seqtrans ,c46_codhist ,c46_obs ,c46_valor ,c46_obrigatorio ,c46_evento ,c46_descricao ,c46_ordem ) values (nextval('contranslan_c46_seqtranslan_seq') ,currval('contrans_c45_seqtrans_seq') ,9880 ,'PRIMEIRO LANÇAMENTO' ,0 ,'true' ,0 ,'PRIMEIRO LANÇAMENTO' ,1 );
insert into contrans( c45_seqtrans ,c45_anousu ,c45_coddoc ,c45_instit ) values (nextval('contrans_c45_seqtrans_seq') ,2023 ,443 ,1 );
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
        $docs = "420,421,422,423,424,425,426,427,428,429,430,431,432,433,434,435,436,437,438,439,440,441,442,443";
        DB::connection()->getPdo()->exec(<<<SQL
create temp table w_transacao as select c45_seqtrans from contrans where c45_coddoc in ($docs);
delete from contranslan using w_transacao where c46_seqtrans = c45_seqtrans;
delete from contrans using w_transacao where contrans.c45_seqtrans = w_transacao.c45_seqtrans;
delete from vinculoeventoscontabeis where c115_conhistdocinclusao in ($docs);
delete from conhistdocregra where c92_conhistdoc in ($docs);
delete from conhistdoc where c53_coddoc in ($docs);
SQL
        );
    }
}
