<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M21367RecalculoIptuTabelasOld extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        $this->upEstrutura();
        $this->upDicionario();
    }

    public function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL

            /* Tabela iptucalcold */
            INSERT INTO db_sysarquivo VALUES (1010971, 'iptucalcold', 'Tabela para armazenamento de dados anteriores após efetuar recálculos', 'j223', '2022-07-20', 'iptucalcold', 0, 'f', 'f', 'f', 'f' );
            INSERT INTO db_sysarqmod VALUES (2,1010971);
            INSERT INTO db_syscampo VALUES(1014360,'j223_sequencial','int4','Sequencial','0', 'j223_sequencial',10,'f','f','f',1,'text','j223_sequencial');
            INSERT INTO db_syssequencia VALUES(1001079, 'iptucalcold_j223_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
            UPDATE db_sysarqcamp set codsequencia = 1001079 WHERE codarq = 1010971 and codcam = 1014360;

            INSERT INTO db_syscampo VALUES(1014361,'j223_anousu','int4','Exercício do Cálculo','0', 'Exercício',4,'f','f','f',1,'text','Exercício');
            INSERT INTO db_syscampo VALUES(1014362,'j223_matric','int4','Matrícula do Imóvel do arquivo iptubase','0', 'Mátricula',10,'f','f','f',1,'text','Mátricula');
            INSERT INTO db_syscampo VALUES(1014363,'j223_testad','float8','Testada gerada para o cálculo do iptu','0', 'Testada do Cálculo',15,'f','f','f',4,'text','Testada do Cálculo');
            INSERT INTO db_syscampo VALUES(1014364,'j223_arealo','float8','Área gerada no momento do cálculo','0', 'Área Calculada',15,'f','f','f',4,'text','Área Calculada');
            INSERT INTO db_syscampo VALUES(1014365,'j223_areafr','float8','Área fracionada gerada no momento do cálculo','0', 'Área Fracionada',15,'f','f','f',4,'text','Área Fracionada');
            INSERT INTO db_syscampo VALUES(1014366,'j223_areaed','float8','Área total edificada gerada no momento do cálculo','0', 'Área Total Edificada',15,'f','f','f',4,'text','Área Total Edificada');
            INSERT INTO db_syscampo VALUES(1014367,'j223_m2terr','float8','Valor do m2 lançado para o cálculo do valor venal do terreno','0', 'Valor M2 Terreno',15,'f','f','f',4,'text','Valor M2 Terreno');
            INSERT INTO db_syscampo VALUES(1014368,'j223_vlrter','float8','Valor venal do terreno gerado no cálculo','0', 'Valor Venal Terreno',15,'f','f','f',4,'text','Valor Venal Terreno');
            INSERT INTO db_syscampo VALUES(1014369,'j223_aliq','float8','Alíquota gerada para o IPTU','0', 'Alíquota do IPTU',15,'f','f','f',4,'text','Alíquota do IPTU');
            INSERT INTO db_syscampo VALUES(1014370,'j223_vlrisen','float8','Valor gerado de Isenção no cálculo do IPTU','0', 'Valor da Isenção',15,'f','f','f',4,'text','Valor da Isenção');
            INSERT INTO db_syscampo VALUES(1014371,'j223_tipoim','varchar(1)','Tipo de Imposto - P predial e T territorial','', 'Tipo de Imposto',1,'f','f','f',2,'text','Tipo de Imposto');

            INSERT INTO db_syscampodef VALUES(1014371,'P','Predial');
            INSERT INTO db_syscampodef VALUES(1014371,'T','Territorial');

            INSERT INTO db_syscampo VALUES(1014372,'j223_manual','text','Log do Cálculo','', 'Log do Cálculo',1,'f','t','f',0,'text','Log do Cálculo');
            INSERT INTO db_syscampo VALUES(1014373,'j223_tipocalculo','int4','Tipo de Cálculo','0', 'Tipo de Cálculo',10,'f','f','f',1,'text','Tipo de Cálculo');
            INSERT INTO db_syscampo VALUES(1014374,'j223_iptucalclog','int4','Vínculo com o campo do código do log de cálculo que deu origem ao dado na tabela old. Ao gerar um novo cálculo (recálculo) os dados antigos da tabela iptucalc são enviados para iptucalcold.','0', 'Iptucalclog',10,'f','f','f',1,'text','Iptucalclog');

            INSERT INTO db_sysarqcamp VALUES(1010971,1014360,1,1001079);
            INSERT INTO db_sysarqcamp VALUES(1010971,1014361,2,0);
            INSERT INTO db_sysarqcamp VALUES(1010971,1014362,3,0);
            INSERT INTO db_sysarqcamp VALUES(1010971,1014363,4,0);
            INSERT INTO db_sysarqcamp VALUES(1010971,1014364,5,0);
            INSERT INTO db_sysarqcamp VALUES(1010971,1014365,6,0);
            INSERT INTO db_sysarqcamp VALUES(1010971,1014366,7,0);
            INSERT INTO db_sysarqcamp VALUES(1010971,1014367,8,0);
            INSERT INTO db_sysarqcamp VALUES(1010971,1014368,9,0);
            INSERT INTO db_sysarqcamp VALUES(1010971,1014369,10,0);
            INSERT INTO db_sysarqcamp VALUES(1010971,1014370,11,0);
            INSERT INTO db_sysarqcamp VALUES(1010971,1014371,12,0);
            INSERT INTO db_sysarqcamp VALUES(1010971,1014372,13,0);
            INSERT INTO db_sysarqcamp VALUES(1010971,1014373,14,0);
            INSERT INTO db_sysarqcamp VALUES(1010971,1014374,15,0);

            INSERT INTO db_sysprikey (codarq,codcam,sequen,camiden) VALUES(1010971,1014360,1,1014360);
            INSERT INTO db_sysforkey VALUES(1010971,1014374,1,1320,0);
            INSERT INTO db_sysindices VALUES(1008800,'iptucalcold_iptucalclog_in',1010971,'0');
            INSERT INTO db_syscadind VALUES(1008800,1014374,1);

            /* Tabela iptucaleold */
            INSERT INTO db_sysarquivo VALUES (1010523, 'iptucaleold', 'Guarda o histórico dos valores venais calculados para as construcoes', 'j162', '2020-02-17', '', 0, 'f', 'f', 'f', 'f');
            INSERT INTO db_sysarqmod VALUES (2, 1010523);

            INSERT INTO db_syscampo VALUES(1014376,'j162_sequencial','int4','Sequencial chave primária para tabela iptucaleold','0', 'Sequencial',10,'f','f','f',1,'text','Sequencial');

            INSERT INTO db_syscampo VALUES (1011024, 'j162_anousu', 'int4', 'Exercício do calculo dos valores venais', '0', 'Exercicio', 4, 'f', 'f', 'f', 0, 'text', 'Exercicio');
            INSERT INTO db_syscampo VALUES (1011025, 'j162_matric', 'int4', 'matricula do Imóvel', '0', 'Matricula', 10, 'f', 'f', 'f', 0, 'text', 'Matricula');
            INSERT INTO db_syscampo VALUES (1011026, 'j162_idcons', 'int4', 'Codigo da construcao', '0', 'Construcao', 4, 'f', 'f', 'f', 0, 'text', 'Construcao');
            INSERT INTO db_syscampo VALUES (1011027, 'j162_areaed', 'float8', 'Area construida e processado no calculo', '0', 'Area Construida', 15, 'f', 'f', 'f', 0, 'text', 'Area Construida');
            INSERT INTO db_syscampo VALUES (1011028, 'j162_vm2', 'float8', 'Valor do m2 da construcao utilizado no calculo', '0', 'Valor M2 Construcao', 15, 'f', 'f', 'f', 0, 'text', 'Valor M2 Construcao');
            INSERT INTO db_syscampo VALUES (1011029, 'j162_pontos', 'int4', 'Numero de pontos processados no calculo', '0', 'Pontuacao', 4, 'f', 'f', 'f', 0, 'text', 'Pontuacao');
            INSERT INTO db_syscampo VALUES (1011030, 'j162_valor', 'float8', 'Valor venal calculado para a edificacao', '0', 'Valor venal', 15, 'f', 'f', 'f', 0, 'text', 'Valor venal');

            INSERT INTO db_syscampo VALUES(1011032,'j162_iptucalclog','int4','Vínculo com o campo do código do log de cálculo que deu origem ao dado na tabela old. Ao gerar um novo cálculo (recálculo) os dados antigos da tabela iptucale são enviados para iptucaleold. ','0', 'Iptucalclog',10,'f','f','f',1,'text','Iptucalclog');

            INSERT INTO db_sysarqcamp VALUES(1010523,1014376,1,1001080);
            INSERT INTO db_sysarqcamp VALUES(1010523,1011024,2,0);
            INSERT INTO db_sysarqcamp VALUES(1010523,1011025,3,0);
            INSERT INTO db_sysarqcamp VALUES(1010523,1011026,4,0);
            INSERT INTO db_sysarqcamp VALUES(1010523,1011027,5,0);
            INSERT INTO db_sysarqcamp VALUES(1010523,1011028,6,0);
            INSERT INTO db_sysarqcamp VALUES(1010523,1011029,7,0);
            INSERT INTO db_sysarqcamp VALUES(1010523,1011030,8,0);
            INSERT INTO db_sysarqcamp VALUES(1010523,1011032,9,0);

            INSERT INTO db_sysprikey (codarq,codcam,sequen,camiden) VALUES(1010523,1014376,1,1014376);

            INSERT INTO db_sysforkey VALUES(1010523,1011032,1,1320,0);
            INSERT INTO db_sysindices VALUES(1008801,'iptucaleold_iptucalclog_in',1010523,'0');
            INSERT INTO db_syscadind VALUES(1008801,1011032,1);

            /* Tabela iptucalvold */
            INSERT INTO db_sysarquivo VALUES (1010500, 'iptucalvold', 'Tabela para guardar o histórico da iptucalv.', 'j157', '2020-01-10', 'Histórico Iptucalv', 0, 'f', 'f', 'f', 'f' );
            INSERT INTO db_sysarqmod VALUES (2,1010500);

            INSERT INTO db_syscampo VALUES(1014377,'j157_sequencial','int4','Chave primária iptucalvold','0', 'Sequencial iptucalvold',10,'f','f','f',1,'text','Sequencial iptucalvold');
            INSERT INTO db_syscampo VALUES(1010897,'j157_anousu','int4','Exercício do calculo dos valores.','0', 'Exercicio',11,'f','f','f',1,'text','ExercicioExercicio');
            INSERT INTO db_syscampo VALUES(1010898,'j157_matric','int4','Matricula do imóvel do arquivo iptubase.','0', 'Matricula',11,'f','f','f',1,'text','Matricula');
            INSERT INTO db_syscampo VALUES(1010899,'j157_receit','int4','Código da receita do tabrec.','0', 'Receita',11,'f','f','f',1,'text','Receita');
            INSERT INTO db_syscampo VALUES(1010900,'j157_valor','int4','Valor do débito','0', 'Valor',11,'f','f','f',1,'text','Valor');
            INSERT INTO db_syscampo VALUES(1010901,'j157_quant','float8','Quantidade','0', 'Quantidade',15,'f','f','f',4,'text','Quantidade');
            INSERT INTO db_syscampo VALUES(1010902,'j157_codhis','float8','Código do histórico de cálculo do IPTU','0', 'Código do histórico',10,'f','f','f',4,'text','Código do histórico');
            INSERT INTO db_syscampo VALUES(1010903,'j157_iptucalclog','int4','Código do vinculo com a tabela iptucalclog.','0', 'Sequencial Iptucalclog',11,'f','f','f',1,'text','Sequencial Iptucalclog');

            INSERT INTO db_sysarqcamp VALUES(1010500,1014377,1,0);
            INSERT INTO db_sysarqcamp VALUES(1010500,1010897,2,0);
            INSERT INTO db_sysarqcamp VALUES(1010500,1010898,3,0);
            INSERT INTO db_sysarqcamp VALUES(1010500,1010899,4,0);
            INSERT INTO db_sysarqcamp VALUES(1010500,1010900,5,0);
            INSERT INTO db_sysarqcamp VALUES(1010500,1010901,6,0);
            INSERT INTO db_sysarqcamp VALUES(1010500,1010902,7,0);
            INSERT INTO db_sysarqcamp VALUES(1010500,1010903,8,0);

            INSERT INTO db_sysprikey (codarq,codcam,sequen,camiden) VALUES(1010500,1014377,1,1014377);
            INSERT INTO db_sysforkey VALUES(1010500,1010903,1,1320,0);
            INSERT INTO db_sysindices VALUES(1008802,'iptucalvold_iptucalclog_in',1010500,'0');
            INSERT INTO db_syscadind VALUES(1008802,1010903,1);

            /* Tabela iptunumpold - alteracoes */

            INSERT INTO db_syscampo VALUES(1014379,'j130_iptucalclog','int4','Vínculo com o campo do código do log de cálculo que deu origem ao dado na tabela old. Ao gerar um novo cálculo (recálculo) os dados antigos da tabela iptunump são enviados para iptunumpold. ','0', 'Iptucalclog',10,'f','f','f',1,'text','Iptucalclog');

            INSERT INTO db_sysarqcamp VALUES(3177,1014379,5,0);

            INSERT INTO db_sysforkey VALUES(3177,1014379,1,1320,0);
            INSERT INTO db_sysindices VALUES(1008799,'iptunumpold_iptucalclog_in',3177,'0');
            INSERT INTO db_syscadind VALUES(1008799,1014379,1);

            /* Tabela iptutaxanumpold */

            INSERT INTO db_sysarquivo VALUES (1010517, 'iptutaxanumpold', 'Tabela de histórico de taxas do iptu', 'j159', '2020-02-03', 'Histórico de taxas de IPTU', 0, 'f', 'f', 'f', 'f' );
            INSERT INTO db_sysarqmod VALUES (2,1010517);

            INSERT INTO db_syscampo VALUES(1014483,'j159_sequencial','int4','Sequencial da tabela iptutaxanumpold','0', 'Sequencial iptutaxanumpold',10,'f','f','f',1,'text','Sequencial');

            INSERT INTO db_syscampo VALUES(1010985,'j159_codigo','int4','Código da iptutaxanump','0', 'Código',10,'f','f','f',1,'text','Código');
            INSERT INTO db_syscampo VALUES(1010987,'j159_matric','int4','Matrícula do imóvel da taxa de iptu','0', 'Matrícula',10,'f','f','f',1,'text','Matrícula');
            INSERT INTO db_syscampo VALUES(1010989,'j159_numpre','int8','Numpre da taxa de iptu','0', 'Numpre',10,'f','f','f',1,'text','Numpre');
            INSERT INTO db_syscampo VALUES(1010991,'j159_iptucadtaxaexe','int4','Código do Cadastro de Taxa no Exercício','0', 'Código do Cadastro de Taxa no Exercício',10,'f','f','f',1,'text','Código do Cadastro de Taxa no Exercício');
            INSERT INTO db_syscampo VALUES(1010993,'j159_iptucalclog','int4','Sequencial da iptucalclog','0', 'Sequencial da iptucalclog',10,'f','f','f',1,'text','Sequencial da iptucalclog');

            INSERT INTO db_sysarqcamp VALUES(1010517,1014483,1,0);
            INSERT INTO db_sysarqcamp VALUES(1010517,1010985,2,0);
            INSERT INTO db_sysarqcamp VALUES(1010517,1010987,3,0);
            INSERT INTO db_sysarqcamp VALUES(1010517,1010989,4,0);
            INSERT INTO db_sysarqcamp VALUES(1010517,1010991,5,0);
            INSERT INTO db_sysarqcamp VALUES(1010517,1010993,6,0);

            INSERT INTO db_sysprikey (codarq,codcam,sequen,camiden) VALUES(1010517,1014483,1,1014483);
            INSERT INTO db_sysforkey VALUES(1010517,1010991,1,1629,0);
            INSERT INTO db_sysforkey VALUES(1010517,1010993,1,1320,0);
            INSERT INTO db_sysforkey VALUES(1010517,1010987,1,27,0);

            INSERT INTO db_syssequencia VALUES(1001089, 'iptutaxanumpold_j159_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
            UPDATE db_sysarqcamp set codsequencia = 1001089 WHERE codarq = 1010517 and codcam = 1014483;

            INSERT INTO db_sysindices VALUES(1008806,'iptutaxanumpold_codigo_uk',1010517,'1');
            INSERT INTO db_syscadind VALUES(1008806,1010985,1);

            /* Tabela iptutaxacalvold */

            INSERT INTO db_sysarquivo VALUES (1010516, 'iptutaxacalvold', 'Tabela de histórico de calculo de taxa de iptu', 'j158', '2020-02-03', 'iptutaxacalv', 0, 'f', 'f', 'f', 'f' );
            INSERT INTO db_sysarqmod VALUES (2,1010516);

            INSERT INTO db_syscampo VALUES(1014484,'j158_sequencial','int4','Sequencial da tabela iptutaxacalvold','0', 'Sequencial',10,'f','f','f',1,'text','Sequencial');
            INSERT INTO db_syscampo VALUES(1010982,'j158_codigo','int4','Código da tabela iptutaxacalv','0', 'Código',10,'f','f','f',1,'text','Código');
            INSERT INTO db_syscampo VALUES(1010983,'j158_iptutaxanumpold','int4','Código de vinculo com a iptutaxanumpold','0', 'Código Iptutaxanumpold',10,'f','f','f',1,'text','Código Iptutaxanumpold');
            INSERT INTO db_syscampo VALUES(1010984,'j158_codhis','int4','Código de vinculo com a iptucalh','0', 'Código Iptucalh',10,'f','f','f',1,'text','Código Iptucalh');
            INSERT INTO db_syscampo VALUES(1010986,'j158_receit','int4','Código de vinculo com a receita','0', 'Código da Receita',10,'f','f','f',1,'text','Código da Receita');
            INSERT INTO db_syscampo VALUES(1010988,'j158_valor','float8','Valor','0', 'Valor',10,'f','f','f',4,'text','Valor');
            INSERT INTO db_syscampo VALUES(1010990,'j158_quant','float8','Alíquota referente a quantidade da taxa calculada.','0', 'Quantidade',10,'t','f','f',4,'text','Quantidade');
            INSERT INTO db_syscampo VALUES(1011791,'j158_areaed','float8','A área edificada é a soma das áreas das construções cadastradas com aquela matricula','0', 'Histórico da área edificada',10,'t','f','f',4,'text','Histórico da área edificada');
            INSERT INTO db_syscampo VALUES(1010992,'j158_iptucalclog','int4','Código iptucalclog.','0', 'Código de vínculo com a iptucalclog',10,'f','f','f',1,'text','Código de vínculo com a iptucalclog');

            INSERT INTO db_sysarqcamp VALUES(1010516,1014484,1,0);
            INSERT INTO db_sysarqcamp VALUES(1010516,1010982,2,0);
            INSERT INTO db_sysarqcamp VALUES(1010516,1010983,3,0);
            INSERT INTO db_sysarqcamp VALUES(1010516,1010984,4,0);
            INSERT INTO db_sysarqcamp VALUES(1010516,1010986,5,0);
            INSERT INTO db_sysarqcamp VALUES(1010516,1010988,6,0);
            INSERT INTO db_sysarqcamp VALUES(1010516,1010990,7,0);
            INSERT INTO db_sysarqcamp VALUES(1010516,1011791,8,0);
            INSERT INTO db_sysarqcamp VALUES(1010516,1010992,9,0);

            INSERT INTO db_sysprikey (codarq,codcam,sequen,camiden) VALUES(1010516,1014484,1,1014484);

            INSERT INTO db_sysforkey VALUES(1010516,1010983,1,1010517,0);
            INSERT INTO db_sysforkey VALUES(1010516,1010984,1,904,0);
            INSERT INTO db_sysforkey VALUES(1010516,1010986,1,75,0);
            INSERT INTO db_sysforkey VALUES(1010516,1010992,1,1320,0);

            INSERT INTO db_syssequencia VALUES(1001090, 'iptutaxacalvold_j158_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
            UPDATE db_sysarqcamp set codsequencia = 1001090 WHERE codarq = 1010516 and codcam = 1014484;

SQL
    );

    }

    public function upEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL

            /* Tabela iptucalcold */
            CREATE SEQUENCE cadastro.iptucalcold_j223_sequencial_seq
              INCREMENT 1
              MINVALUE 1
              MAXVALUE 9223372036854775807
              START 1
              CACHE 1;

            CREATE TABLE cadastro.iptucalcold (
                j223_sequencial int NOT NULL DEFAULT nextval('iptucalcold_j223_sequencial_seq'),
                j223_anousu int NOT NULL,
                j223_matric int NOT NULL,
                j223_testad double precision,
                j223_arealo double precision,
                j223_areafr double precision,
                j223_areaed double precision,
                j223_m2terr double precision,
                j223_vlrter double precision,
                j223_aliq double precision,
                j223_vlrisen double precision,
                j223_tipoim char,
                j223_manual text,
                j223_tipocalculo int,
                j223_iptucalclog int NOT NULL,
                CONSTRAINT iptucalcold_pk PRIMARY KEY (j223_sequencial),
                CONSTRAINT iptucalcold_iptucalclog_fk FOREIGN KEY (j223_iptucalclog) REFERENCES iptucalclog(j27_codigo) MATCH FULL DEFERRABLE
            );

            CREATE SEQUENCE cadastro.iptucaleold_j162_sequencial_seq
              INCREMENT 1
              MINVALUE 1
              MAXVALUE 9223372036854775807
              START 1
              CACHE 1;

            CREATE TABLE cadastro.iptucaleold (
                j162_sequencial integer NOT NULL DEFAULT nextval('iptucaleold_j162_sequencial_seq'),
                j162_anousu integer NOT NULL DEFAULT 0,
                j162_matric integer NOT NULL DEFAULT 0,
                j162_idcons integer NOT NULL DEFAULT 0,
                j162_areaed double precision DEFAULT 0,
                j162_vm2 double precision DEFAULT 0,
                j162_pontos integer DEFAULT 0,
                j162_valor double precision DEFAULT 0,
                j162_iptucalclog integer NOT NULL,
                CONSTRAINT iptucaleold_pk PRIMARY KEY (j162_sequencial),
                CONSTRAINT iptucaleold_matric_idcons_fk FOREIGN KEY (j162_matric, j162_idcons) REFERENCES iptuconstr(j39_matric, j39_idcons) MATCH FULL DEFERRABLE,
                CONSTRAINT iptucaleold_iptucalclog_fk FOREIGN KEY (j162_iptucalclog) REFERENCES iptucalclog(j27_codigo) MATCH FULL DEFERRABLE
            );

            /* Tabela iptucalvold */
            CREATE SEQUENCE cadastro.iptucalvold_j157_sequencial_seq
              INCREMENT 1
              MINVALUE 1
              MAXVALUE 9223372036854775807
              START 1
              CACHE 1;

            CREATE TABLE cadastro.iptucalvold
            (
                j157_sequencial integer NOT NULL DEFAULT nextval('iptucalvold_j157_sequencial_seq'),
                j157_anousu integer NOT NULL,
                j157_matric integer NOT NULL,
                j157_receit integer,
                j157_valor numeric,
                j157_quant integer,
                j157_codhis integer NOT NULL,
                j157_iptucalclog integer NOT NULL,
                CONSTRAINT iptucalvold_pk PRIMARY KEY (j157_sequencial),
                CONSTRAINT codhis_fk FOREIGN KEY (j157_codhis) REFERENCES iptucalh(j17_codhis),
                CONSTRAINT matric_fk FOREIGN KEY (j157_matric) REFERENCES iptubase(j01_matric),
                CONSTRAINT receit_fk FOREIGN KEY (j157_receit) REFERENCES tabrec(k02_codigo),
                CONSTRAINT iptucalvold_iptucalclog_fk FOREIGN KEY (j157_iptucalclog) REFERENCES iptucalclog(j27_codigo) MATCH FULL DEFERRABLE
            );

            /* Tabela iptunumpold - adiciona fk para iptucalclog */
            ALTER TABLE cadastro.iptunumpold ADD COLUMN j130_iptucalclog int4 DEFAULT NULL;
            ALTER TABLE cadastro.iptunumpold ADD CONSTRAINT "iptunumpold_iptucalclog_fk" FOREIGN KEY (j130_iptucalclog) REFERENCES iptucalclog(j27_codigo);


            /* Tabela iptutaxanumpold */
            CREATE SEQUENCE cadastro.iptutaxanumpold_j159_sequencial_seq
              INCREMENT 1
              MINVALUE 1
              MAXVALUE 9223372036854775807
              START 1
              CACHE 1;

            CREATE TABLE cadastro.iptutaxanumpold (
                j159_sequencial int NOT NULL DEFAULT nextval('iptutaxanumpold_j159_sequencial_seq'),
                j159_codigo int NOT NULL,
                j159_matric int NOT NULL,
                j159_numpre bigint,
                j159_iptucadtaxaexe int,
                j159_iptucalclog int NOT NULL,
                CONSTRAINT iptutaxanumpold_pk PRIMARY KEY (j159_sequencial),
                CONSTRAINT iptutaxanumpold_uk UNIQUE (j159_codigo),
                CONSTRAINT iptutaxanumpold_iptucalclog_fk FOREIGN KEY (j159_iptucalclog) REFERENCES iptucalclog(j27_codigo) MATCH FULL DEFERRABLE
            );

            /* Tabela iptutaxacalvold */
            CREATE SEQUENCE cadastro.iptutaxacalvold_j158_sequencial_seq
              INCREMENT 1
              MINVALUE 1
              MAXVALUE 9223372036854775807
              START 1
              CACHE 1;

            CREATE TABLE cadastro.iptutaxacalvold (
                j158_sequencial int NOT NULL DEFAULT nextval('iptutaxacalvold_j158_sequencial_seq'),
                j158_codigo int NOT NULL,
                j158_iptutaxanumpold int NOT NULL,
                j158_codhis int NOT NULL,
                j158_receit int NOT NULL,
                j158_valor double precision DEFAULT 0,
                j158_quant double precision DEFAULT 0,
                j158_areaed double precision DEFAULT 0,
                j158_iptucalclog int NOT NULL,
                CONSTRAINT iptutaxacalvold_pk PRIMARY KEY (j158_sequencial),
                CONSTRAINT iptutaxacalvold_iptutaxanumpold_fk FOREIGN KEY (j158_iptutaxanumpold) REFERENCES iptutaxanumpold(j159_sequencial),
                CONSTRAINT iptutaxacalvold_iptucalh_fk FOREIGN KEY (j158_codhis) REFERENCES iptucalh(j17_codhis) MATCH FULL DEFERRABLE,
                CONSTRAINT iptutaxacalvold_tabrec_fk FOREIGN KEY (j158_receit) REFERENCES tabrec(k02_codigo) MATCH FULL DEFERRABLE,
                CONSTRAINT iptutaxacalvold_iptucalclog_fk FOREIGN KEY (j158_iptucalclog) REFERENCES iptucalclog(j27_codigo) MATCH FULL DEFERRABLE
            );

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
        $this->downEstrutura();
        $this->downDicionario();
    }

    public function downDicionario()
    {

        DB::connection()->getPdo()->exec(<<<SQL

            /* iptucalcold */

            DELETE FROM db_sysforkey WHERE codcam = 1014374;

            DELETE FROM db_sysindices WHERE codind = 1008800;
            DELETE FROM db_syscadind WHERE codind = 1008800;

            DELETE FROM db_syssequencia WHERE codsequencia = 1001079;
            DELETE FROM db_sysprikey WHERE codarq = 1010971;

            DELETE FROM db_syscampodef WHERE codcam = 1014371;

            DELETE FROM db_sysarqcamp WHERE codarq = 1010971;
            DELETE FROM db_syscampo WHERE codcam in (1014360, 1014361, 1014362, 1014363, 1014364, 1014365, 1014366, 1014367, 1014368, 1014369, 1014370, 1014371, 1014372, 1014373, 1014374);
            DELETE FROM db_sysarqmod WHERE codarq = 1010971;
            DELETE FROM db_sysarquivo WHERE codarq = 1010971;

            /* iptucaleold */

            DELETE FROM db_sysforkey WHERE codcam = 1011032;

            DELETE FROM db_sysindices WHERE codind = 1008801;
            DELETE FROM db_syscadind WHERE codind = 1008801;

            DELETE FROM db_sysprikey WHERE codarq = 1010523;

            DELETE FROM db_sysarqcamp WHERE codarq = 1010523;
            DELETE FROM db_sysarqmod WHERE codarq = 1010523;
            DELETE FROM db_syscampo WHERE codcam in (1014376, 1011024, 1011025, 1011026, 1011027, 1011028, 1011029, 1011030, 1011031, 1011032);
            DELETE FROM db_sysarquivo WHERE codarq = 1010523;

            /* iptucalvold */

            DELETE FROM db_sysforkey WHERE codcam = 1010903;

            DELETE FROM db_sysindices WHERE codind = 1008802;
            DELETE FROM db_syscadind WHERE codind = 1008802;

            DELETE FROM db_sysprikey WHERE codarq = 1010500;

            DELETE FROM db_sysarqcamp WHERE codarq = 1010500;
            DELETE FROM db_syscampo WHERE codcam in (1010897, 1010898, 1010899, 1010900, 1010901, 1010902, 1010902, 1010903, 1014377);
            DELETE FROM db_sysarqmod WHERE codarq = 1010500;
            DELETE FROM db_sysarquivo WHERE codarq = 1010500;

            /* iptunumpold */

            DELETE FROM db_syssequencia WHERE codsequencia = 1001089;

            DELETE FROM db_sysforkey WHERE codcam = 1014379;

            DELETE FROM db_sysindices WHERE codind = 1008799;
            DELETE FROM db_syscadind WHERE codind = 1008799;

            DELETE FROM db_sysarqcamp WHERE codcam = 1014379;
            DELETE FROM db_syscampo WHERE codcam = 1014379;

            /* iptutaxanumpold */

            DELETE FROM db_syscadind  WHERE codind = 1008806 and codcam = 1010985;
            DELETE FROM db_sysindices WHERE codarq = 1010517 and codind = 1008806;

            DELETE FROM db_sysforkey WHERE codarq = 1010517;

            DELETE FROM db_sysprikey WHERE codarq = 1010517;

            DELETE FROM db_sysarqcamp WHERE codarq = 1010517;
            DELETE FROM db_syscampo   WHERE codcam in (1014483, 1010985, 1010987, 1010989, 1010991, 1010993);
            DELETE FROM db_sysarqmod  WHERE codarq = 1010517;
            DELETE FROM db_sysarquivo WHERE codarq = 1010517;

            /* iptutaxacalvold */

            DELETE FROM db_syssequencia WHERE codsequencia = 1001090;

            DELETE FROM db_sysforkey WHERE codarq = 1010516;

            DELETE FROM db_sysprikey WHERE codarq = 1010516;

            DELETE FROM db_sysarqcamp WHERE codarq = 1010516;
            DELETE FROM db_syscampo   WHERE codcam in (1014484, 1010982, 1010983, 1010984, 1010986, 1010988, 1010990, 1011791, 1010992);
            DELETE FROM db_sysarqmod  WHERE codarq = 1010516;
            DELETE FROM db_sysarquivo WHERE codarq = 1010516;



SQL
        );

    }

    public function downEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL

            ALTER TABLE cadastro.iptunumpold DROP COLUMN j130_iptucalclog;

            DROP TABLE cadastro.iptutaxacalvold;
            DROP TABLE cadastro.iptutaxanumpold;

            DROP TABLE cadastro.iptucalvold;
            DROP TABLE cadastro.iptucaleold;
            DROP TABLE cadastro.iptucalcold;

            DROP SEQUENCE cadastro.iptutaxacalvold_j158_sequencial_seq;
            DROP SEQUENCE cadastro.iptutaxanumpold_j159_sequencial_seq;
            DROP SEQUENCE cadastro.iptucalcold_j223_sequencial_seq;
            DROP SEQUENCE cadastro.iptucaleold_j162_sequencial_seq;
            DROP SEQUENCE cadastro.iptucalvold_j157_sequencial_seq;


SQL
        );

    }
}
