<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M25001ParametrosSepultamento extends Migration
{
    public function up()
    {
        $this->upDicionario();
        $this->upEstrutura();
    }

    public function down()
    {
        $this->downDicionario();
        $this->downEstrutura();
    }

    private function upDicionario()
    {
        $sql = <<<SQL
            insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228953 ,'Parâmetros' ,'Parâmetros Cemitério' ,'web/tributario/cemiterio/procedimentos/parametros' ,'1' ,'1' ,'Parâmetros de configuração do módulo Cemitério.' ,'true' );
            insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 32 ,228953 ,577 ,289579 );
            insert into db_sysarquivo values (1011114, 'parametroscemiterio', 'Tabela para armazenamento dos parâmetros de configurações do módulo Cemitério.', '', '2023-07-07', 'Parâmetros Cemitério', 0, 'f', 'f', 'f', 'f' );
            insert into db_sysarqmod values (49,1011114);
            insert into db_syscampo values(1015231,'cem36_sequencial','int4','Sequencial da tabela parametroscemiterio.','0', 'Sequencial parametroscemiterio',10,'f','f','f',1,'text','Sequencial parametroscemiterio');
            insert into db_syscampo values(1015232,'cem36_obrigatoriedadetaxasepultamento','bool','Obrigatoriedade da taxa de sepultamento','f', 'Obrigatoriedade Taxa Sepultamento',1,'f','f','f',5,'text','Obrigatoriedade Taxa Sepultamento');
            insert into db_syscampo values(1015233,'cem36_ano','int4','Ao do parâmetro.','0', 'Ano',5,'f','f','f',1,'text','Ano');
            insert into db_sysarqcamp values(1011114,1015231,1,0);
            insert into db_sysarqcamp values(1011114,1015232,2,0);
            insert into db_sysarqcamp values(1011114,1015233,3,0);
            insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011114,1015231,1,1015231);
            insert into db_sysindices values(1008881,'parametroscemiterio_sequencial_pk',1011114,'0');
            insert into db_syscadind values(1008881,1015231,1);
SQL;

        $this->executeQuery($sql);
    }

    private function upEstrutura()
    {
        $sql = <<<SQL
            create table cemiterio.parametroscemiterio(
                cem36_sequencial SERIAL,
                cem36_obrigatoriedadetaxasepultamento BOOLEAN NOT NULL DEFAULT false,
                cem36_ano INTEGER NOT NULL,
                CONSTRAINT parametroscemiterio_sequencial_pk PRIMARY KEY (cem36_sequencial)
            );

            insert into parametroscemiterio (cem36_obrigatoriedadetaxasepultamento,cem36_ano) values (false, 2023);
            select configuracoes.fc_auditoria_cria_funcao('cemiterio.parametroscemiterio');
SQL;

        $this->executeQuery($sql);
    }

    private function downDicionario()
    {
        $sql = <<<SQL
            delete from db_menu where id_item_filho = 228953 AND modulo = 289579;
            delete from db_itensmenu where id_item = 228953 and funcao = 'web/tributario/cemiterio/procedimentos/parametros';
            delete from db_syscadind where codind = 1008881 and codcam = 1015231;
            delete from db_sysindices where codind = 1008881 and nomeind = 'parametroscemiterio_sequencial_pk';
            delete from db_sysprikey where codarq = 1011114 and codcam = 1015231;
            delete from db_sysarqcamp where codarq = 1011114 and codcam in (1015231, 1015232, 1015233);
            delete from db_syscampo where codcam in (1015231, 1015232, 1015233) and nomecam in ('cem36_sequencial', 'cem36_obrigatoriedadetaxasepultamento', 'cem36_ano');
            delete from db_sysarqmod where codmod = 49 and codarq = 1011114;
            delete from db_sysarquivo where codarq = 1011114 and nomearq = 'parametroscemiterio';
SQL;

        $this->executeQuery($sql);
    }

    private function downEstrutura()
    {
        $sql = <<<SQL
            drop table if exists cemiterio.parametroscemiterio;
SQL;

        $this->executeQuery($sql);
    }

    private function executeQuery($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
