<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M18298HistoricoProprietarios extends Migration
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
        $this->execute(
            <<<SQL
        -- Cria menu para acessar manutenção de histórico de proprietários
        insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228520 ,'Manutenção Histórico de Proprietários' ,'Manutenção Histórico de Proprietários' ,'cad4_historicoproprietarios001.php' ,'1' ,'1' ,'Histórico de Proprietários do módulo cadastro' ,'true' );
        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 32 ,228520 ,538 ,578 );
        
        -- Cria tabela cadastro.historicoproprietarios
        insert into db_sysarquivo values (1010806, 'historicoproprietarios', 'Salva histórico de proprietários averbados', 'j172', '2021-06-09', 'historicoproprietarios', 0, 'f', 'f', 'f', 'f' );
        insert into db_sysarqmod values (2,1010806);

        -- Cria campos da tabela cadastro.historicoproprietarios
        insert into db_syscampo values(1013290,'j172_sequencial','int4','Campo sequencial tabela historicoproprietarios','0', 'Sequencial',10,'f','f','f',1,'text','Sequencial');
        insert into db_syscampo values(1013291,'j172_data','date','Data historicoproprietarios','null', 'Data',10,'f','f','f',1,'text','Data');
        insert into db_syscampo values(1013292,'j172_protocolo','varchar(50)','Protocolo historicoproprietarios','', 'Protocolo',50,'f','t','f',0,'text','Protocolo');
        insert into db_syscampo values(1013293,'j172_tipoproprietario','int4','fk da tabela tipo proprietario','0', 'Tipo Adquirente Proprietário',10,'t','f','f',1,'text','Tipo Adquirente Proprietário');
        insert into db_syscampo values(1013294,'j172_adquirente','varchar(250)','Adquirente','', 'Adquirente',250,'f','t','f',0,'text','Adquirente');
        insert into db_syscampo values(1013295,'j172_observacao','text','Observação','', 'Observação',250,'t','t','f',0,'text','Observação');
        insert into db_syscampo values(1013296,'j172_matric','int4','fk da tabela historicoproprietarios que refencia iptubase','0', 'Matrícula',10,'f','f','f',1,'text','Matrícula');
        insert into db_syscampo values(1013297,'j172_tipopromitente','int4','fk da tabela tipo proprietario','0', 'Tipo Adquirente Promitente',10,'t','f','f',1,'text','Tipo Adquirente Promitente');
        insert into db_syscampo values(1013298,'j172_numcgm','int4','CGM referenciado na tabela historicoproprietarios','0', 'CGM',10,'f','f','f',1,'text','CGM');

        -- Atribiu campos para a tabela cadastro.historicoproprietarios
        insert into db_sysarqcamp values(1010806,1013290,1,0);
        insert into db_sysarqcamp values(1010806,1013291,2,0);
        insert into db_sysarqcamp values(1010806,1013292,3,0);
        insert into db_sysarqcamp values(1010806,1013293,4,0);
        insert into db_sysarqcamp values(1010806,1013294,5,0);
        insert into db_sysarqcamp values(1010806,1013295,6,0);
        insert into db_sysarqcamp values(1010806,1013296,7,0);
        insert into db_sysarqcamp values(1010806,1013297,8,0);
        insert into db_sysarqcamp values(1010806,1013298,9,0);

        -- Atribiu Primary Key para a tabela cadastro.historicoproprietarios
        insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1010806,1013290,1,1013290);

        -- Atribiu Foreign Key referenciando tabela tipoproprietario
        insert into db_sysforkey values(1010806,1013293,1,1010529,0);

        -- Atribiu Foreign Key referenciando tabela iptubase
        insert into db_sysforkey values(1010806,1013296,1,27,0);

        -- Atribiu Foreign Key referenciando tabela tipopromitente
        insert into db_sysforkey values(1010806,1013297,1,1010530,0);

        -- Atribiu Foreign Key referenciando tabela cgm
        insert into db_sysforkey values(1010806,1013298,1,42,0);

        -- Cria sequence para a tabela cadastro.historicoproprietarios
        insert into db_syssequencia values(1001008, 'historicoproprietarios_j172_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
        update db_sysarqcamp set codsequencia = 1001008 where codarq = 1010806 and codcam = 1013290;

        -- Cria campo percentual de posse do histórico de proprietários
        insert into db_syscampo values(1013386,'j172_percentual','float4','Percentual de Posse do histórico de proprietários','0', 'Percentual de Posse',10,'f','f','f',4,'text','Percentual de Posse');
        insert into db_sysarqcamp values(1010806,1013386,10,0);

SQL
        );
    }

    private function upEstrutura()
    {
        $this->execute(
            <<<SQL
            create table cadastro.historicoproprietarios (
                j172_sequencial serial,
                j172_data date,
                j172_protocolo varchar(250),
                j172_tipoproprietario integer,
                j172_tipopromitente integer,
                j172_adquirente varchar(250),
                j172_observacao text,
                j172_matric integer,
                j172_numcgm integer,
                j172_percentual float,
                primary key(j172_sequencial),
                constraint j172_tipoproprietario_fk foreign key (j172_tipoproprietario) references tipoproprietario (j163_tipoproprietario),
                constraint j172_tipopromitente_fk foreign key (j172_tipopromitente) references tipopromitente (j164_tipopromitente),
                constraint j172_matric_fk foreign key (j172_matric) references iptubase (j01_matric),
                constraint j172_numcgm_fk foreign key (j172_numcgm) references cgm (z01_numcgm)
            );
SQL
        );
    }

    private function downDicionario()
    {
        $this->execute(
            <<<SQL
         -- Remove sequence da tabela cadastro.historicoproprietarios
        update db_sysarqcamp set codsequencia = 0 where codsequencia = 1001008 and codarq = 1010806 and codcam = 1013290;
        delete from db_syssequencia where codsequencia = 1001008;

        -- Remove Foreign Key da tabela cadastro.historicoproprietarios referenciando tabela tipoproprietario
        delete from db_sysforkey where codarq = 1010806;
        
        -- Remove Primary Key da tabela cadastro.historicoproprietarios
        delete from db_sysprikey where codarq = 1010806 and codcam = 1013290;

        -- Desnvicula campos da tabela cadastro.historicoproprietarios
        delete from db_sysarqcamp where codarq = 1010806;

        -- Remove campos da tabela cadastro.historicoproprietarios
        delete from db_syscampo where codcam in (1013290, 1013291, 1013292, 1013293, 1013294, 1013295, 1013296, 1013297, 1013298);

        -- Remove tabela cadastro.historicoproprietarios
        delete from db_sysarqmod where codmod = 2 and codarq = 1010806;
        delete from db_sysarquivo where codarq = 1010806;

        -- Remove menu manutenção de histórico de proprietários
        delete from db_menu where id_item = 32 and id_item_filho = 228520;
        delete from db_itensmenu where id_item = 228520;

SQL
        );
    }

    private function downEstrutura()
    {
        $this->execute(
            <<<SQL
        drop table cadastro.historicoproprietarios;
SQL
        );
    }

    private function execute($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
