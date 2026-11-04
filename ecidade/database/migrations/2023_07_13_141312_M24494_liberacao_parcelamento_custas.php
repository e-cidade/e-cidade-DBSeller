<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M24494LiberacaoParcelamentoCustas extends Migration
{
    public function up()
    {
        $this->upDicionario();
        $this->upEstrututra();
    }

    public function down()
    {
        $this->downDicionario();
        $this->downEstrututra();
    }

    private function upDicionario()
    {
        $sql = <<<SQL
        insert into db_sysarquivo values (1011115, 'custasparcelamento', 'Tabela responsável por armazenar as informações de parcelamento custas', 'ar53', '2023-07-13', 'Parcelamento de custas', 0, 'f', 'f', 'f', 'f' );
        insert into db_sysarqmod values (54,1011115);

        insert into db_syscampo values(1015241,'ar53_sequencial','int4','Sequencial da tabela custasparcelamento','0', 'Sequencial',10,'f','f','f',1,'text','Sequencial');
        insert into db_syscampo values(1015242,'ar53_processoforo','int4','Referencia um processo do foro.','0', 'Processo do foro',10,'t','f','f',1,'text','Processo do foro');
        insert into db_syscampo values(1015243,'ar53_inicial','int4','Referencia uma inicial','0', 'Inicial',10,'t','f','f',1,'text','Inicial');
        insert into db_syscampo values(1015244,'ar53_taxa','int4','Referencia uma taxa','0', 'Taxa',10,'f','f','f',1,'text','Taxa');

        insert into db_syscampo (codcam, nomecam, conteudo, descricao, valorinicial, rotulo, nulo, tamanho, maiusculo, autocompl, aceitatipo, tipoobj, rotulorel) values
        (1015302,'ar53_data', 'varchar(11)', 'Data de autorização de parcelamento', '', 'Data', 'f', 11, 'f', 'f', 0, 'text', 'Data'),
        (1015303,'ar53_parcelas', 'int4', 'Número máximo de parcelas', '0', 'Número máximo de parcelas', 't', 10, 'f', 'f', 1, 'text', 'Número máximo de parcelas'),
        (1015305,'ar53_parcelamento', 'int4', 'Número do parcelamento vinculado', '0', 'Parcelamento', 't', 10, 'f', 'f', 1, 'text', 'Parcelamento'),
        (1015304,'ar53_usuario', 'int4', 'Usuário que realizou a alteração', '0', 'Usuário', 'f', 10, 'f', 'f', 1, 'text', 'Usuário');

        insert into db_sysarqcamp values(1011115,1015241,1,0);
        insert into db_sysarqcamp values(1011115,1015242,2,0);
        insert into db_sysarqcamp values(1011115,1015243,3,0);
        insert into db_sysarqcamp values(1011115,1015244,4,0);
        insert into db_sysarqcamp values(1011115,1015305,5,0);
        insert into db_sysarqcamp values(1011115,1015303,6,0);
        insert into db_sysarqcamp values(1011115,1015304,7,0);
        insert into db_sysarqcamp values(1011115,1015302,8,0);

        insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011115,1015241,1,1015241);
        insert into db_sysforkey values(1011115,1015242,1,3069,0);
        insert into db_sysforkey values(1011115,1015243,1,108,0);
        insert into db_sysforkey values(1011115,1015244,1,3221,0);
        insert into db_sysforkey values(1011115,1015304,1,109,0);
        insert into db_sysforkey values(1011115,1015305,1,103,0);

        insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228954 ,'Liberação' ,'Liberação do Parcelamento de Custas' ,'arr4_custaparcelamento001.php' ,'1' ,'1' ,'Liberação do Parcelamento de Custas' ,'true' );
        insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228958 ,'Manutenção' ,'Manutenção do Parcelamento de Custas' ,'arr4_custaparcelamento002.php' ,'1' ,'1' ,'Manutenção do Parcelamento de Custas' ,'true' );
        insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228960 ,'Parcelamento de custas' ,'Parcelamento de custas' ,'' ,'1' ,'1' ,'Parcelamento de custas' ,'true' );

        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 10469 ,228960 ,5 ,313 );
        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228960 ,228954 ,1 ,313 );
        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228960 ,228958 ,2 ,313 );
SQL;

        $this->executeQuery($sql);
    }

    private function upEstrututra()
    {
        $sql = <<<SQL
            CREATE TABLE arrecadacao.custasparcelamento(
                ar53_sequencial SERIAL,
                ar53_processoforo INTEGER,
                ar53_inicial INTEGER,
                ar53_taxa INTEGER NOT NULL,

                ar53_data VARCHAR(11) NOT NULL,
                ar53_parcelas INTEGER,
                ar53_usuario INTEGER NOT NULL,
                ar53_parcelamento INTEGER,

                CONSTRAINT custasparcelamento_sequ_pk PRIMARY KEY (ar53_sequencial),
                CONSTRAINT custasparcelamento_processoforo_fk FOREIGN KEY (ar53_processoforo) REFERENCES processoforo,
                CONSTRAINT custasparcelamento_inicial_fk FOREIGN KEY (ar53_inicial) REFERENCES inicial,
                CONSTRAINT custasparcelamento_taxa_fk FOREIGN KEY (ar53_taxa) REFERENCES taxa,

                CONSTRAINT custasparcelamento_db_usuarios_fk FOREIGN KEY (ar53_usuario) REFERENCES db_usuarios,
                CONSTRAINT custasparcelamento_termo_fk FOREIGN KEY (ar53_parcelamento) REFERENCES termo
            );
SQL;

        $this->executeQuery($sql);
    }

    private function downDicionario()
    {
        $sql = <<<SQL
        delete from db_sysprikey where codarq = 1011115 and codcam = 1015241 and sequen = 1 and camiden = 1015241;
        delete from db_sysforkey where codarq = 1011115 and codcam in (1015242, 1015243, 1015244, 1015304, 1015305) and sequen = 1 and referen in (3069, 108, 3221, 109, 103);
        delete from db_sysarqcamp where codarq = 1011115 and codcam in (1015241, 1015242, 1015243, 1015244, 1015245, 1015241, 1015242, 1015243, 1015244, 1015305, 1015303, 1015304, 1015302) and seqarq in (1, 2, 3, 4, 5, 6, 7, 8);
        delete from db_syscampo where codcam in (1015241, 1015242, 1015243, 1015244, 1015245, 1015244, 1015302, 1015303, 1015305, 1015304) and nomecam in ('ar53_sequencial', 'ar53_processoforo', 'ar53_inicial', 'ar53_taxa', 'ar53_data', 'ar53_parcelas', 'ar53_parcelamento', 'ar53_usuario');
        delete from db_sysarqmod where codmod = 54 and codarq = 1011115;
        delete from db_acount where codarq = 1011115;
        delete from db_sysarquivo where codarq = 1011115 and nomearq = 'custasparcelamento';
        delete from db_menu where id_item = 228960 and id_item_filho in (228954, 228958) and menusequencia in (1,2) and modulo = 313;
        delete from db_menu where id_item = 10469 and id_item_filho = 228960 and menusequencia = 5 and modulo = 313;
        delete from db_itensmenu where id_item in (228960, 228954, 228958) and descricao in ('Parcelamento de custas', 'Liberação', 'Manutenção');
SQL;

        $this->executeQuery($sql);
    }

    private function downEstrututra()
    {
        $sql = <<<SQL
            DROP TABLE arrecadacao.custasparcelamento;
SQL;

        $this->executeQuery($sql);
    }

    private function executeQuery($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
