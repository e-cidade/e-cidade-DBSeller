<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M24499CertidaoLancamentoMatricula extends Migration
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
        DB::connection()->getPdo()->exec(
            <<<SQL
            --certidao de lancamento
            insert into db_sysarquivo values (1011094, 'certlancimov', 'Tabela para guardar as certidões de lançamento emitidas.', 'j176', '2023-05-31', 'Certidão de Lançamento', 0, 'f', 'f', 'f', 'f' );
            insert into db_sysarqmod values (2,1011094);
            insert into db_syscampo values(1015134,'j176_sequencial','int4','Sequencial da tabela certlancimov.','0', 'Sequencial certlancimov',8,'f','f','f',1,'text','Sequencial certlancimov');
            insert into db_syscampo values(1015135,'j176_usuario','int4','Usuário que emitiu a certidão de lançamento da matrícula.','0', 'Usuario',8,'f','f','f',1,'text','Usuario');
            insert into db_syscampo values(1015136,'j176_matricula','int4','Matrícula que a certidão de lançamento está vinculada.','0', 'Matrícula',8,'f','f','f',1,'text','Matrícula');
            insert into db_syscampo values(1015137,'j176_emissao','varchar(30)','Data e hora da emissão da certidão de lançamento da matricula.','', 'Data e hora da emissão',30,'f','f','f',0,'text','Data e hora da emissão');
            insert into db_syscampo values(1015138,'j176_processo','varchar(50)','Número do processo vinculado à certidão de lançamento da matrícula.','', 'Processo',50,'t','f','f',0,'text','Processo');
            insert into db_syscampo values(1015139,'j176_observacao','text','Observação da certidão de lançamento da matrícula.','', 'Observação',1,'t','f','f',0,'text','Observação');
            insert into db_syscampo values(1015140,'j176_titular','varchar(100)','Titular do processo.','', 'Titular',100,'t','f','f',0,'text','Titular');
            update db_syscampo set nomecam = 'j176_usuario', conteudo = 'int4', descricao = 'Usuário que emitiu a certidão de lançamento da matrícula.', valorinicial = '0', rotulo = 'Usuario', nulo = 't', tamanho = 8, maiusculo = 'f', autocompl = 'f', aceitatipo = 1, tipoobj = 'text', rotulorel = 'Usuario' where codcam = 1015135;
            insert into db_sysarqcamp values(1011094,1015134,1,0);
            insert into db_sysarqcamp values(1011094,1015135,2,0);
            insert into db_sysarqcamp values(1011094,1015136,3,0);
            insert into db_sysarqcamp values(1011094,1015137,4,0);
            insert into db_sysarqcamp values(1011094,1015138,5,0);
            insert into db_sysarqcamp values(1011094,1015139,6,0);
            insert into db_sysarqcamp values(1011094,1015140,7,0);
            insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011094,1015134,1,1015134);
            insert into db_sysforkey values(1011094,1015136,1,27,0);
            insert into db_sysforkey values(1011094,1015135,1,109,0);

            --certidao de valor venal
            insert into db_syscampo values(1015156,'j177_sequencial','int4','Sequencial da tabela certvalven.','0', 'Sequencial certvalven',8,'f','f','f',1,'text','Sequencial certvalven');
            insert into db_syscampo values(1015158,'j177_matricula','int4','Matrícula que a certidão de valor venal está vinculada.','0', 'Matrícula',10,'f','f','f',1,'text','Matrícula');
            insert into db_syscampo values(1015160,'j177_emissao','varchar(30)','Data e hora da emissão da certidão de valor venal.','', 'Data e hora da emissão',30,'f','f','f',0,'text','Data e hora da emissão');
            insert into db_syscampo values(1015162,'j177_usuario','int4','Usuário que emitiu a certidão.','0', 'Usuário',8,'t','f','f',1,'text','Usuário');
            insert into db_syscampo values(1015164,'j177_titular','varchar(100)','Titular do processo.','', 'Titular',100,'t','f','f',0,'text','Titular');
            insert into db_syscampo values(1015166,'j177_processo','varchar(50)','Número do processo vinculado à certidão de valor venal.','', 'Processo',50,'t','f','f',0,'text','Processo');
            insert into db_syscampo values(1015168,'j177_observacao','text','Observação da certidão de valor venal.','', 'Observação',1,'t','f','f',0,'text','Observação');
            insert into db_sysarquivo values (1011097, 'certvalven', 'Tabela para guardar as certidões de valor venal emitidas.', 'j177', '2023-06-09', 'Certidão de Valor Venal', 0, 'f', 'f', 'f', 'f' );
            insert into db_sysarqmod values (2,1011097);
            insert into db_sysarqcamp values(1011097,1015156,1,0);
            insert into db_sysarqcamp values(1011097,1015158,2,0);
            insert into db_sysarqcamp values(1011097,1015160,3,0);
            insert into db_sysarqcamp values(1011097,1015162,4,0);
            insert into db_sysarqcamp values(1011097,1015164,5,0);
            insert into db_sysarqcamp values(1011097,1015166,6,0);
            insert into db_sysarqcamp values(1011097,1015168,7,0);
            insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011097,1015156,1,1015156);
            insert into db_sysforkey values(1011097,1015162,1,109,0);
            insert into db_sysforkey values(1011097,1015158,1,27,0);


            --certidao de foro e laudemio
            insert into db_syscampo values(1015157,'j178_sequencial','int4','Sequencial da tabela certforlaud.','0', 'Sequencial certforlaud',8,'f','f','f',1,'text','Sequencial certforlaud');
            insert into db_syscampo values(1015159,'j178_matricula','int4','Matrícula que a certidão de foro e laudêmio está vinculada.','0', 'Matrícula',8,'f','f','f',1,'text','Matrícula');
            insert into db_syscampo values(1015161,'j178_emissao','varchar(30)','Data e hora da emissão da certidão de foro e laudêmio.','', 'Data e hora da emissão',30,'f','f','f',0,'text','Data e hora da emissão');
            insert into db_syscampo values(1015163,'j178_usuario','int4','Usuário que emitiu a certidão.','0', 'Usuário',8,'t','f','f',1,'text','Usuário');
            insert into db_syscampo values(1015165,'j178_titular','varchar(100)','Titular do processo.','', 'Titular',100,'t','f','f',0,'text','Titular');
            insert into db_syscampo values(1015167,'j178_processo','varchar(50)','Número do processo vinculado à certidão de foro e laudêmio.','', 'Processo',50,'t','f','f',0,'text','Processo');
            insert into db_syscampo values(1015169,'j178_observacao','text','Observação da certidão de foro e laudemio.','', 'Observação',1,'t','f','f',0,'text','Observação');
            insert into db_sysarquivo values (1011098, 'certforlaud', 'Tabela para guardar as certidões de Foro e Laudêmio emitidas.', 'j178', '2023-06-09', 'Certidao de Foro e Laudêmio', 0, 'f', 'f', 'f', 'f' );
            insert into db_sysarqmod values (2,1011098);
            insert into db_sysarqcamp values(1011098,1015157,1,0);
            insert into db_sysarqcamp values(1011098,1015159,2,0);
            insert into db_sysarqcamp values(1011098,1015161,3,0);
            insert into db_sysarqcamp values(1011098,1015163,4,0);
            insert into db_sysarqcamp values(1011098,1015165,5,0);
            insert into db_sysarqcamp values(1011098,1015167,6,0);
            insert into db_sysarqcamp values(1011098,1015169,7,0);
            insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011098,1015157,1,1015157);
            insert into db_sysforkey values(1011098,1015163,1,109,0);
            insert into db_sysforkey values(1011098,1015159,1,27,0);
SQL
        );
    }

    private function upEstrutura()
    {
        DB::connection()->getPdo()->exec(
            <<<SQL
            --certidao de lancamento
            CREATE TABLE certlancimov(
                j176_sequencial SERIAL,
                j176_matricula INTEGER NOT NULL,
                j176_emissao VARCHAR(30) NOT NULL,
                j176_usuario INTEGER,
                j176_titular VARCHAR(100),
                j176_processo VARCHAR(50),
                j176_observacao TEXT,
                CONSTRAINT certlancimov_sequencial_pk PRIMARY KEY (j176_sequencial),
                CONSTRAINT certlancimov_usuarios_fk FOREIGN KEY (j176_usuario) REFERENCES db_usuarios (id_usuario),
                CONSTRAINT certlancimov_iptubase_fk FOREIGN KEY (j176_matricula) REFERENCES iptubase (j01_matric)
            );

            INSERT INTO db_documentotemplatetipo VALUES(64, 'Certidão de Lançamento da Matrícula');
            INSERT INTO db_documentotemplatepadrao VALUES(nextval('db_documentotemplatepadrao_db81_sequencial_seq'), 64, 'Certidão de Lançamento', 'documentos/templates/cadastro/modelo_certidao_lancamento.docx');

            --certidao de valor venal
            CREATE TABLE certvalven(
                j177_sequencial SERIAL,
                j177_matricula INTEGER NOT NULL,
                j177_emissao VARCHAR(30) NOT NULL,
                j177_usuario INTEGER,
                j177_titular VARCHAR(100),
                j177_processo VARCHAR(50),
                j177_observacao TEXT,
                CONSTRAINT certvalven_sequencial_pk PRIMARY KEY (j177_sequencial),
                CONSTRAINT certvalven_usuarios_fk FOREIGN KEY (j177_usuario) REFERENCES db_usuarios (id_usuario),
                CONSTRAINT certvalven_iptubase_fk FOREIGN KEY (j177_matricula) REFERENCES iptubase (j01_matric)
            );

            INSERT INTO db_documentotemplatetipo VALUES(65, 'Certidão de Valor Venal da Matrícula');
            INSERT INTO db_documentotemplatepadrao VALUES(nextval('db_documentotemplatepadrao_db81_sequencial_seq'), 65, 'Certidão de Valor Venal', 'documentos/templates/cadastro/modelo_certidao_valor_venal.docx');

            --certidao de foro e laudemio
            CREATE TABLE certforlaud(
                j178_sequencial SERIAL,
                j178_matricula INTEGER NOT NULL,
                j178_emissao VARCHAR(30) NOT NULL,
                j178_usuario INTEGER,
                j178_titular VARCHAR(100),
                j178_processo VARCHAR(50),
                j178_observacao TEXT,
                CONSTRAINT certforlaud_sequencial_pk PRIMARY KEY (j178_sequencial),
                CONSTRAINT certforlaud_usuarios_fk FOREIGN KEY (j178_usuario) REFERENCES db_usuarios (id_usuario),
                CONSTRAINT certforlaud_iptubase_fk FOREIGN KEY (j178_matricula) REFERENCES iptubase (j01_matric)
            );

            INSERT INTO db_documentotemplatetipo VALUES(66, 'Certidão de Foro e Laudêmio da Matrícula');
            INSERT INTO db_documentotemplatepadrao VALUES(nextval('db_documentotemplatepadrao_db81_sequencial_seq'), 66, 'Certidão de Foro e Laudêmio', 'documentos/templates/cadastro/modelo_certidao_foro_laudemio.docx');
SQL
        );
    }

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(
            <<<SQL
            --certidao de lancamento
            delete from db_sysforkey where codarq = 1011094;
            delete from db_sysforkey where codarq = 1011094;
            delete from db_sysprikey where codarq = 1011094;
            delete from db_sysarqcamp where codarq = 1011094;
            delete from db_syscampo where codcam in (1015134, 1015135, 1015136, 1015137, 1015138, 1015139, 1015140);
            delete from db_sysarqmod where codmod = 2 and codarq = 1011094;
            delete from db_acount where codarq = 1011094;
            delete from db_sysarquivo where codarq = 1011094 and nomearq = 'certlancimov';

            --certidao de valor venal
            delete from db_sysforkey where codarq = 1011097;
            delete from db_sysforkey where codarq = 1011097;
            delete from db_sysprikey where codarq = 1011097;
            delete from db_sysarqcamp where codarq = 1011097;
            delete from db_acount where codarq = 1011097;
            delete from db_sysarqmod where codmod = 2 and codarq = 1011097;
            delete from db_sysarquivo where codarq = 1011097 and nomearq = 'certvalven';
            delete from db_syscampo where codcam in (1015156, 1015158, 1015160, 1015162, 1015164, 1015166, 1015168);

            --certidao de foro e laudemio
            delete from db_sysforkey where codarq = 1011098;
            delete from db_sysforkey where codarq = 1011098;
            delete from db_sysprikey where codarq = 1011098;
            delete from db_sysarqcamp where codarq = 1011098;
            delete from db_acount where codarq = 1011098;
            delete from db_sysarqmod where codmod = 2 and codarq = 1011098;
            delete from db_sysarquivo where codarq = 1011098 and nomearq = 'certforlaud';
            delete from db_syscampo where codcam in (1015157, 1015159, 1015161, 1015163, 1015165, 1015167, 1015169);
SQL
        );
    }

    private function downEstrutura()
    {
        DB::connection()->getPdo()->exec(
            <<<SQL
            --certidao de foro e laudemio
            delete from db_documentotemplate where db82_templatetipo  in (64, 65, 66);
            delete from db_documentotemplatepadrao where db81_templatetipo in (64, 65, 66);
            delete from db_documentotemplatetipo where db80_sequencial in (64, 65, 66);

            --certidao de lancamento
            DELETE FROM certlancimov;
            DROP TABLE certlancimov;

            --certidao de valor venal
            DELETE FROM certvalven;
            DROP TABLE certvalven;
            
            --certidao de foro e laudemio
            DELETE FROM certforlaud;
            DROP TABLE certforlaud;
SQL
        );
    }
}
