<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25840IntegracaoInfisc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            insert into db_syscampo values(1015514,'k11_infisc','text','Esse campo deve ser marcado caso este caixa seja utilizado para transações da integração com a INFISC','', 'INFISC',1,'f','f','f',0,'text','INFISC');
            insert into db_sysarqcamp values(199,1015514,19,0);

            alter table caixa.cfautent add column k11_infisc boolean default 'f';

            CREATE TABLE issqn.issvar_infisc_integra_debitos (
                q196_sequencial SERIAL NOT NULL,
                q196_issvar INTEGER NOT NULL,
                q196_integra_debitos INTEGER NOT NULL
            );

            insert into db_sysarquivo values (1011157, 'issvar_infisc_integra_debitos', 'Tabela que salva vinculo de débitos criados a partir do processamento de estrutura de integra débitos da integração com a INFISC', 'q196', '2023-10-30', 'Issvar Infisc Integra Debitos', 0, 'f', 'f', 'f', 'f' );
            insert into db_sysarqmod values (3,1011157);

            insert into db_syscampo values(1015521,'q196_sequencial','int8','Sequencial da tabela issvar_infisc_integra_debitos','0', 'Sequencial da tabela issvar_infisc_integra_debitos',11,'f','f','f',1,'text','Sequencial da tabela issvar_infisc_integ');
            insert into db_syscampo values(1015522,'q196_issvar','int8','Sequencial da tabela issvar','0', 'Sequencial da tabela issvar',11,'f','f','f',1,'text','Sequencial da tabela issvar');
            insert into db_syscampo values(1015523,'q196_integra_debitos','int8','Sequencial da tabela integra_debitos','0', 'Sequencial da tabela integra_debitos',11,'f','f','f',1,'text','Sequencial da tabela integra_debitos');

            insert into db_syssequencia values(1001172, 'issvar_infisc_integra_debitos_q196_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);

            insert into db_sysarqcamp values(1011157,1015521,1,1001172);
            insert into db_sysarqcamp values(1011157,1015522,2,0);
            insert into db_sysarqcamp values(1011157,1015523,3,0);

            insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011157,1015521,1,1015521);

            CREATE TABLE issqn.inscricao_debito_infisc_integra_debitos_mov (
                q197_sequencial SERIAL NOT NULL,
                q197_diversos INTEGER NOT NULL,
                q197_status TEXT NOT NULL
            );

            insert into db_sysarquivo values (1011159, 'inscricao_debito_infisc_integra_debitos_mov', 'Tabela que salva débito que foram inscritos em cobrança para serem integrados com a INFISC', 'q197', '2023-11-03', 'inscricao_debito_infisc_integra_debitos_mov', 0, 'f', 'f', 'f', 'f' );
            insert into db_sysarqmod values (3,1011159);

            insert into db_syscampo values(1015527,'q197_sequencial','int8','Sequencial da tabela inscricao_debito_infisc_integra_debitos_mov','0', 'Sequencial',11,'f','f','f',1,'text','Sequencial');
            insert into db_syscampo values(1015528,'q197_diversos','int8','Código da tabela diversos.','0', 'Diversos',11,'f','f','f',1,'text','Diversos');
            insert into db_syscampo values(1015529,'q197_status','varchar(255)','Status que define se foi processado ou não.','', 'Status',255,'f','f','f',0,'text','Status');

            insert into db_syssequencia values(1001173, 'inscricao_debito_infisc_integra_debitos_mov_q197_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);

            insert into db_sysarqcamp values(1011159,1015527,1,1001173);
            insert into db_sysarqcamp values(1011159,1015528,2,0);
            insert into db_sysarqcamp values(1011159,1015529,3,0);

            insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011159,1015527,1,1015527);
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
            delete from db_sysarqcamp where codcam = 1015514;
            delete from db_syscampo where codcam = 1015514;

            alter table caixa.cfautent drop column k11_infisc;

            delete from db_sysprikey where codarq in (1011157, 1011159);
            delete from db_sysarqcamp where codarq in (1011157, 1011159);
            delete from db_syssequencia where codsequencia in (1001172, 1001173);
            delete from db_syscampo where codcam in (1015521, 1015522, 1015523, 1015527, 1015528, 1015529);
            delete from db_sysarqmod where codarq in (1011157, 1011159);
            delete from db_sysarquivo where codarq in (1011157, 1011159);

            drop table issqn.issvar_infisc_integra_debitos;
            drop table issqn.inscricao_debito_infisc_integra_debitos_mov;
SQL
        );
    }
}
