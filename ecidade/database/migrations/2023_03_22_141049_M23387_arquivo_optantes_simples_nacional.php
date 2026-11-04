<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23387ArquivoOptantesSimplesNacional extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        $this->upDicionario();
        $this->upEstrutura();
    }

    private function upEstrutura()
    {
        DB::connection()->getPdo()->exec(
            <<<SQL
        CREATE TABLE arqsimples(
            q183_sequencial SERIAL,
            q183_nomearq varchar(50),
            q183_dt_import date,
            q183_periodo_ini date,
            q183_periodo_fim date,
            q183_data_limite date,
            CONSTRAINT arqsimples_sequencial_pk PRIMARY KEY (q183_sequencial)
        );

        CREATE TABLE arqsimplesreg(
            q184_sequencial SERIAL,
            q184_arqsimples integer,
            q184_dt_solicitacao date,
            q184_cnpj varchar(14),
            CONSTRAINT arqsimplesreg_sequencial_pk PRIMARY KEY (q184_sequencial),
            CONSTRAINT arqsimplesreg_arqsimples_fk FOREIGN KEY (q184_arqsimples) REFERENCES arqsimples (q183_sequencial)
        );

        CREATE TABLE arqsimplesregcnae(
            q185_sequencial SERIAL,
            q185_arqsimplesreg integer,
            q185_cnae varchar(7),
            q185_tipo varchar(1),
            CONSTRAINT arqsimplesregcnae_sequencial_pk PRIMARY KEY (q185_sequencial),
            CONSTRAINT arqsimplesregcnae_arqsimplesreg_fk FOREIGN KEY (q185_arqsimplesreg) REFERENCES arqsimplesreg (q184_sequencial)
        );

        CREATE TABLE arqsimplesenvio(
            q186_sequencial SERIAL,
            q186_arqsimples integer,
            q186_arqsimplesreg integer,
            q186_situacao integer,
            CONSTRAINT arqsimplesenvio_sequencial_pk PRIMARY KEY (q186_sequencial),
            CONSTRAINT arqsimplesenvio_arqsimples_fk FOREIGN KEY (q186_arqsimples) REFERENCES arqsimples (q183_sequencial),
            CONSTRAINT arqsimplesenvio_arqsimplesreg_fk FOREIGN KEY (q186_arqsimplesreg) REFERENCES arqsimplesreg (q184_sequencial)
        );
SQL
        );
    }

    private function upDicionario()
    {
        DB::connection()->getPdo()->exec(
            <<<SQL
insert into db_sysarquivo values (1011051, 'arqsimples', 'Tabela de armazenamento do arquivo de optantes do simples nacional.', 'q183', '2023-03-22', '', 0, 'f', 'f', 'f', 'f' );
insert into db_sysarqmod values (3,1011051);
insert into db_sysarquivo values (1011052, 'arqsimplesreg', 'Tabela para armazenamento de registros de optantes pelo simples nacional.', 'q184', '2023-03-22', '', 0, 'f', 'f', 'f', 'f' );
insert into db_sysarqmod values (3,1011052);
insert into db_sysarquivo values (1011053, 'arqsimplesregcnae', 'Tabela para armazenamento de registros de cnaes de optantes pelo simples nacional.', 'q185', '2023-03-22', '', 0, 'f', 'f', 'f', 'f' );
insert into db_sysarqmod values (3,1011053);
insert into db_sysarquivo values (1011054, 'arqsimplesenvio', 'Tabela para armazenamento de registros de arquivos enviados referente aos optantes pelo simples nacional.', 'q186', '2023-03-22', '', 0, 'f', 'f', 'f', 'f' );
insert into db_sysarqmod values (3,1011054);
insert into db_syscampo values(1014911,'q183_sequencial','int4','Sequencial da tabela arqsimples.','0', 'Sequencial',10,'f','f','t',1,'text','Sequencial');
insert into db_syscampo values(1014912,'q183_nomearq','varchar(50)','Nome de um arquivo.','', 'Nome do arquivo',50,'f','t','f',0,'text','Nome do arquivo');
insert into db_syscampo values(1014913,'q183_dt_import','date','Data de importação de um arquivo.','null', 'Data de importação',10,'f','f','f',0,'text','Data de importação');
insert into db_syscampo values(1014914,'q183_periodo_ini','date','Perí­odo inicial de um registro.','null', 'Perí­odo inicial',10,'f','f','f',0,'text','Perí­odo inicial');
insert into db_syscampo values(1014915,'q183_periodo_fim','date','Perí­odo final de um registro.','null', 'Perí­odo final',10,'f','f','f',0,'text','Perí­odo final');
insert into db_syscampo values(1014916,'q183_data_limite','date','Data limite para o processamento do arquivo.','null', 'Data limite',10,'f','f','f',0,'text','Data limite');
insert into db_syscampo values(1014917,'q184_sequencial','int4','Sequencial da tabela arqsimplesreg.','0', 'Sequencial',10,'f','f','t',1,'text','Sequencial');
insert into db_syscampo values(1014918,'q184_arqsimples','int4','Campo relacionado ao sequencial da tabela arqsimples.','0', 'Sequencial arqsimples',10,'f','f','f',1,'text','Sequencial arqsimples');
insert into db_syscampo values(1014919,'q184_dt_solicitacao','date','Data de solicitação do processamento de um arquivo de optantes pelo simples nacional.','null', 'Data de solicitação',10,'t','f','f',0,'text','Data de solicitação');
insert into db_syscampo values(1014920,'q184_cnpj','varchar(14)','Campo do Cnpj.','', 'Cnpj',14,'f','f','f',0,'text','Cnpj');
insert into db_syscampo values(1014921,'q185_sequencial','int4','Sequencial da tabela arqsimplesregcnae.','0', 'Sequencial',10,'f','f','t',1,'text','Sequencial');
insert into db_syscampo values(1014922,'q185_arqsimplesreg','int4','Campo relacionado ao sequencial da tabela arqsimplesreg.','0', 'Sequencial arqsimplesreg',10,'f','f','f',1,'text','Sequencial arqsimplesreg');
insert into db_syscampo values(1014923,'q185_cnae','varchar(7)','Campo do cnae.','', 'Cnae',7,'f','f','f',0,'text','Cnae');
insert into db_syscampo values(1014924,'q185_tipo','varchar(1)','Tipo de cnae','', 'Tipo de cnae',1,'f','f','f',0,'text','Tipo de cnae');
insert into db_syscampo values(1014925,'q186_sequencial','int4','Sequencial da tabela arqsimplesenvio.','0', 'Sequencial',10,'f','f','t',1,'text','Sequencial');
insert into db_syscampo values(1014926,'q186_arqsimples','int4','Campo relacionado ao sequencial da tabela arqsimples.','0', 'Sequencial arqsimples',10,'f','f','f',1,'text','Sequencial arqsimples');
insert into db_syscampo values(1014927,'q186_arqsimplesreg','int4','Campo relacionado ao sequencial da tabela arqsimplesreg.','0', 'Sequencial arqsimplesreg',10,'f','f','f',1,'text','Sequencial arqsimplesreg');
insert into db_syscampo values(1014928,'q186_situacao','int4','Situação de uma pendência.','0', 'Situação',1,'f','f','f',1,'text','Situação');
insert into db_sysarqcamp values(1011051,1014911,1,0);
insert into db_sysarqcamp values(1011051,1014912,2,0);
insert into db_sysarqcamp values(1011051,1014913,3,0);
insert into db_sysarqcamp values(1011051,1014914,4,0);
insert into db_sysarqcamp values(1011051,1014915,5,0);
insert into db_sysarqcamp values(1011051,1014916,6,0);
insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011051,1014911,1,1014911);
insert into db_sysarqcamp values(1011052,1014917,1,0);
insert into db_sysarqcamp values(1011052,1014918,2,0);
insert into db_sysarqcamp values(1011052,1014919,3,0);
insert into db_sysarqcamp values(1011052,1014920,4,0);
insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011052,1014917,1,1014917);
insert into db_sysarqcamp values(1011053,1014921,1,0);
insert into db_sysarqcamp values(1011053,1014922,2,0);
insert into db_sysarqcamp values(1011053,1014923,3,0);
insert into db_sysarqcamp values(1011053,1014924,4,0);
insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011053,1014921,1,1014921);
insert into db_sysarqcamp values(1011054,1014925,1,0);
insert into db_sysarqcamp values(1011054,1014926,2,0);
insert into db_sysarqcamp values(1011054,1014927,3,0);
insert into db_sysarqcamp values(1011054,1014928,4,0);
insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011054,1014925,1,1014925);
insert into db_sysforkey values(1011052,1014918,1,1011051,0);
insert into db_sysforkey values(1011053,1014922,1,1011052,0);
insert into db_sysforkey values(1011054,1014926,1,1011051,0);
insert into db_sysforkey values(1011054,1014927,1,1011052,0);
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228888 ,'Novos Estabelecimentos' ,'Novos Estabelecimentos' ,'web/tributario/issqn/procedimentos/simples-nacional/novos-estabelecimentos' ,'1' ,'1' ,'Rotina para processar validação de pendências para novos optantes do Simples Nacional.' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 9855 ,228888 ,4 ,40 );
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
        $this->downDicionario();
        $this->downEstrutura();
    }

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(
            <<<SQL
delete from db_sysforkey where codarq = 1011052;
delete from db_sysforkey where codarq = 1011053;
delete from db_sysforkey where codarq = 1011054;
delete from db_sysarqcamp where codarq in (1011054, 1011053, 1011052, 1011051);
delete from db_sysprikey where codarq = 1011051;
delete from db_sysprikey where codarq = 1011051;
delete from db_sysprikey where codarq = 1011051;
delete from db_sysprikey where codarq = 1011052;
delete from db_sysprikey where codarq = 1011053;
delete from db_sysprikey where codarq = 1011054;
delete from db_syscampo where codcam in (
1014911,
1014912,
1014913,
1014914,
1014915,
1014916,
1014917,
1014918,
1014919,
1014920,
1014921,
1014922,
1014923,
1014924,
1014925,
1014926,
1014927,
1014928
);
delete from db_sysarqmod where codarq in (1011051, 1011052, 1011053, 1011054);
delete from db_sysarquivo where codarq in (1011051, 1011052, 1011053, 1011054);
delete from db_itensmenu where id_item = 228888;
delete from db_menu where id_item_filho = 228888 AND modulo = 40;
SQL
        );
    }

    private function downEstrutura()
    {
        DB::connection()->getPdo()->exec(
            <<<SQL
DROP TABLE arqsimplesenvio;
DROP TABLE arqsimplesregcnae;
DROP TABLE arqsimplesreg;
DROP TABLE arqsimples;
SQL
        );
    }
}
