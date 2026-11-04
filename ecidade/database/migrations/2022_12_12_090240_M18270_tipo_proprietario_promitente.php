<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M18270TipoProprietarioPromitente extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        $instit = DB::table("db_config")
                  ->where("prefeitura", true)
                  ->get(["cgc"])
                  ->first();

        if (trim($instit->cgc) != "88489786000101") {

          $this->getExpressaoFalecimento();
          $this->upDicionario();
          $this->upEstrutura();
          $this->upMigration();
          $this->upViewProprietario();
          $this->upViewBuscaEnvolvidos();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $instit = DB::table("db_config")
        ->where("prefeitura", true)
        ->get(["cgc"])
        ->first();

        if (trim($instit->cgc) != "88489786000101") {
          
          $this->downDicionario();
          $this->downEstrutura();
          $this->downViewProprietario();
          $this->downViewBuscaEnvolvidos();
        }
    }

    private function upDicionario(){
      DB::connection()->getPdo()->exec(<<<SQL
        INSERT INTO db_sysarquivo VALUES (1010529, 'tipoproprietario', 'Tipo de Proprietario', 'j163', '2020-03-03', 'Tipo de Proprietario', 0, 'f', 'f', 'f', 'f' );
            INSERT INTO db_sysarquivo VALUES (1010530, 'tipopromitente', 'Tipo de Promitente', 'j164', '2020-03-03', 'Tipo de Promitente', 0, 'f', 'f', 'f', 'f' );
            INSERT INTO db_sysarquivo VALUES (1010542, 'tipoproprietariopromitente', 'Relaciona as tabelas tipoproprietario e tipopromiente', 'j165', '2020-03-09', 'tipoproprietario e tipopromitente', 0, 'f', 'f', 'f', 'f' );

            INSERT INTO db_sysarqmod VALUES (2,1010529);
            INSERT INTO db_sysarqmod VALUES (2,1010530);

            INSERT INTO db_syscampo VALUES (1011079,'j163_descricao','varchar(50)','Descrição do tipo de proprietario','', '',50,'f','f','f',2,'text','');
            INSERT INTO db_syscampo VALUES (1011083,'j163_tipoproprietario','int4','Sequencial do tipo de proprietario','0', 'j163_tipoproprietario',10,'f','f','f',1,'text','');
            INSERT INTO db_syscampo VALUES (1011269,'j163_abreviatura','varchar(50)','Abreviatura tipo de proprietário','', 'Abreviatura',50,'f','t','f',0,'text','abreviatura tipo de proprietário');
            INSERT INTO db_syscampo VALUES (1011273,'j163_pesfisjur','int4','parâmetro de pessoa física ou jurídica','0', 'Pessoa física ou jurídica',10,'t','f','f',1,'text','Pessoa física ou jurídica');

            INSERT INTO db_syscampo VALUES (1011080,'j164_descricao','varchar(50)','Descrição do tipo de promitente','', '',50,'f','f','f',2,'text','');
            INSERT INTO db_syscampo VALUES (1011081,'j164_promitipo','varchar(2)','Sigla do tipo de promitente','', '',2,'f','f','f',2,'text','');
            INSERT INTO db_syscampo VALUES (1011084,'j164_tipopromitente','int4','Sequencial do tipo de promitente','0', 'j164_tipopromitente',10,'f','f','f',1,'text','');
            INSERT INTO db_syscampo VALUES (1011270,'j164_abreviatura','varchar(50)','Abreviatura tipo de promitente','', 'Abreviatura',50,'f','t','f',0,'text','');

            INSERT INTO db_syscampo VALUES (1011146,'j165_tipoproprietariopromitente','int4','Sequencial da tabela tipoproprietariopromitente','0', 'Sequencial',10,'f','f','f',1,'text','Sequencial');
            INSERT INTO db_syscampo VALUES (1011147,'j165_tipoproprietario','int4','Sequencial da tabela tipoproprietario','0', 'Sequencial tipoproprietario',10,'f','f','f',1,'text','Sequencial tipoproprietario');
            INSERT INTO db_syscampo VALUES (1011148,'j165_tipopromitente','int4','Sequencial da tabela tipopromitente','0', 'Sequencial tipopromitente',10,'f','f','f',1,'text','Sequencial tipopromitente');

            INSERT INTO db_sysarqcamp VALUES(1010529,1011083,1,0);
            INSERT INTO db_sysarqcamp VALUES(1010529,1011079,2,0);
            INSERT INTO db_sysarqcamp VALUES(1010529,1011269,3,0);
            INSERT INTO db_sysarqcamp VALUES(1010529,1011273,4,0);
            INSERT INTO db_sysprikey (codarq,codcam,sequen,camiden) VALUES(1010529,1011083,1,1011083);
            INSERT INTO db_syssequencia VALUES(1000893, 'tipoproprietario_j163_tipoproprietario_seq', 1, 1, 9223372036854775807, 1, 1);
            UPDATE db_sysarqcamp SET codsequencia = 1000893 WHERE codarq = 1010529 AND codcam = 1011083;

            INSERT INTO db_sysarqcamp VALUES(1010530,1011084,1,0);
            INSERT INTO db_sysarqcamp VALUES(1010530,1011080,2,0);
            INSERT INTO db_sysarqcamp VALUES(1010530,1011081,3,0);
            INSERT INTO db_sysarqcamp VALUES(1010530,1011270,4,0);
            INSERT INTO db_sysprikey (codarq,codcam,sequen,camiden) VALUES(1010530,1011084,1,1011084);
            INSERT INTO db_syssequencia VALUES(1000892, 'tipopromitente_j164_tipopromitente_seq', 1, 1, 9223372036854775807, 1, 1);
            UPDATE db_sysarqcamp SET codsequencia = 1000892 WHERE codarq = 1010530 AND codcam = 1011084;

            INSERT INTO db_sysarqcamp VALUES(1010542,1011146,1,0);
            INSERT INTO db_sysarqcamp VALUES(1010542,1011147,2,0);
            INSERT INTO db_sysarqcamp VALUES(1010542,1011148,3,0);
            INSERT INTO db_sysprikey (codarq,codcam,sequen,camiden) VALUES(1010542,1011146,1,1011146);
            INSERT INTO db_syssequencia VALUES(1000894, 'tipoproprietariopromitente_j165_tipoproprietariopromitente_seq', 1, 1, 9223372036854775807, 1, 1);
            UPDATE db_sysarqcamp SET codsequencia = 1000894 WHERE codarq = 1010542 AND codcam = 1011146;

            INSERT INTO db_sysforkey VALUES(1010542,1011147,1,1010529,0);
            INSERT INTO db_sysforkey VALUES(1010542,1011148,1,1010530,0);

            --iptubase
            INSERT INTO db_syscampo VALUES(1011150,'j01_tipoproprietario','int4','Relação iptubase e tipoproprietario','0', 'Relação iptubase e tipoproprietario',10,'f','f','f',1,'text','Relação iptubase e tipoproprietario');
            INSERT INTO db_sysarqcamp VALUES(27,1011150,8,0);
            INSERT INTO db_sysforkey VALUES(27,1011150,1,1010529,0);

            --averbacgm
            INSERT INTO db_syscampo VALUES(1011157,'j76_tipoproprietario','int4','Tipo de Proprietario','0', 'Tipo de Proprietario',10,'f','f','f',1,'text','Tipo de Proprietario');
            INSERT INTO db_syscampo VALUES(1011158,'j76_tipopromitente','int4','Tipo de Promitente','0', 'Tipo de Promitente',10,'f','f','f',1,'text','Tipo de Promitente');
            INSERT INTO db_sysarqcamp VALUES(1651,1011157,6,0);
            INSERT INTO db_sysarqcamp VALUES(1651,1011158,7,0);
            INSERT INTO db_sysforkey VALUES(1651,1011157,1,1010529,0);
            INSERT INTO db_sysforkey VALUES(1651,1011158,1,1010530,0);

            --propri
            INSERT INTO db_syscampo VALUES(1011161,'j42_tipoproprietario','int4','relação da tabela tipoproprietario','0', 'j42_tipoproprietario',10,'f','f','f',1,'text','tipo de proprietario');
            INSERT INTO db_sysarqcamp VALUES(34,1011161,3,0);
            INSERT INTO db_sysforkey VALUES(34,1011161,1,1010529,0);

            --promitente
            INSERT INTO db_syscampo VALUES(1011341,'j41_tipopromitente','int4','Tipo de Promitente','0', 'Tipo de Promitente',11,'f','f','f',1,'text','Tipo de Promitente');
            INSERT INTO db_sysarqcamp VALUES(33,1011341,5,0);
            INSERT INTO db_sysforkey VALUES(33,1011341,1,1010530,0);

            --Adiciona tipo proprietario e tipo promitente em averbacgmold
            INSERT INTO db_syscampo VALUES(1011264,'j79_tipopromitente','int4','relação com tabela tipopromitente','0', 'relação com tabela tipopromitente',10,'t','f','f',1,'text','relação com tabela tipopromitente');
            INSERT INTO db_syscampo VALUES(1011265,'j79_tipoproprietario','int4','relação com tabela tipoproprietario','0', 'relação com tabela tipoproprietario',10,'t','f','f',1,'text','relação com tabela tipoproprietario');
            INSERT INTO db_sysarqcamp VALUES(1650,1011264,7,0);
            INSERT INTO db_sysarqcamp VALUES(1650,1011265,6,0);
            INSERT INTO db_sysforkey VALUES(1650,1011264,1,1010530,0);
            INSERT INTO db_sysforkey VALUES(1650,1011265,1,1010529,0);

            INSERT INTO db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) VALUES ( 228251 ,'Titularidade' ,'menu de acesso para tipos de proprietário e tipos de promitente' ,'' ,'1' ,'1' ,'menu de acesso para tipos de proprietário e tipos de promitente' ,'true' );
            INSERT INTO db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) VALUES ( 228237 ,'Tipo de Proprietário' ,'Inclusão, Alteração, Exclusão de tipo de proprietário' ,'cad1_tipoproprietario.php' ,'1' ,'1' ,'Cadastro de tipo de Proprietário' ,'true' );
            INSERT INTO db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) VALUES ( 228238 ,'Tipo de Promitente' ,'Inclusão, Alteração e Exclusão de tipo de promitente' ,'cad1_tipopromitente.php' ,'1' ,'1' ,'Cadastro de tipo de promitente' ,'true' );

            INSERT INTO db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) VALUES ( 228251 ,228237 ,1 ,578 );
            INSERT INTO db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) VALUES ( 228251 ,228238 ,1 ,578 );
            INSERT INTO db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) VALUES ( 29 ,228251 ,288 ,578 );

SQL
    );       
    }
    private function upEstrutura(){
      DB::connection()->getPdo()->exec(<<<SQL
         
         CREATE TABLE cadastro.tipoproprietario(
                j163_tipoproprietario SERIAL NOT NULL,
                j163_descricao TEXT NOT NULL,
                j163_abreviatura TEXT NOT NULL,
                j163_pesfisjur INT NULL,
                CONSTRAINT tipoproprietario_sequ_pk PRIMARY KEY (j163_tipoproprietario)
            );
            CREATE TABLE cadastro.tipopromitente(
                j164_tipopromitente SERIAL NOT NULL,
                j164_descricao TEXT NOT NULL,
                j164_promitipo CHARACTER (1) NOT NULL,
                j164_abreviatura TEXT NOT NULL,
                CONSTRAINT tipopromitente_sequ_pk PRIMARY KEY (j164_tipopromitente)
            );
            CREATE TABLE cadastro.tipoproprietariopromitente(
                j165_tipoproprietariopromitente SERIAL NOT NULL,
                j165_tipoproprietario INT,
                j165_tipopromitente INT,
                CONSTRAINT tipoproprietariopromitente_sequ_pk PRIMARY KEY (j165_tipoproprietariopromitente),
                CONSTRAINT tipoproprietariopromitente_tipoproprietario_fk FOREIGN KEY (j165_tipoproprietario) REFERENCES tipoproprietario(j163_tipoproprietario),
                CONSTRAINT tipoproprietariopromitente_tipopromitente_fk FOREIGN KEY (j165_tipopromitente) REFERENCES tipopromitente(j164_tipopromitente)
            );

            INSERT INTO tipoproprietario (j163_descricao, j163_abreviatura, j163_pesfisjur) VALUES ('Proprietário', 'PROPRI', 0);
            INSERT INTO tipoproprietario (j163_descricao,j163_abreviatura,j163_pesfisjur)
                                                    VALUES ('Espólio de', 'ESP', 0);
            INSERT INTO tipoproprietario (j163_descricao, j163_abreviatura, j163_pesfisjur) VALUES ('Sucessão de', 'SUC', 0);
            INSERT INTO tipopromitente (j164_descricao, j164_promitipo, j164_abreviatura) VALUES ('Com contrato', 'C', 'CC');
            INSERT INTO tipopromitente (j164_descricao, j164_promitipo, j164_abreviatura) VALUES ('Sem contrato', 'S', 'SC');
            INSERT INTO tipopromitente (j164_descricao, j164_promitipo, j164_abreviatura) VALUES ('Representante do Espólio', 'R', 'REPESP');
            INSERT INTO tipopromitente (j164_descricao, j164_promitipo, j164_abreviatura) VALUES ('Inventariante', 'I', 'INV');
            INSERT INTO tipoproprietariopromitente (j165_tipoproprietario, j165_tipopromitente)
                SELECT
                    (SELECT j163_tipoproprietario
                       FROM tipoproprietario
                      WHERE j163_descricao = 'Proprietário') AS tipoproprietario,

                    (SELECT j164_tipopromitente
                       FROM tipopromitente
                      WHERE j164_promitipo = 'C') AS tipopromitente
            ;
            INSERT INTO tipoproprietariopromitente (j165_tipoproprietario, j165_tipopromitente)
                SELECT
                    (SELECT j163_tipoproprietario
                       FROM tipoproprietario
                      WHERE j163_descricao = 'Proprietário') AS tipoproprietario,

                    (SELECT j164_tipopromitente
                       FROM tipopromitente
                      WHERE j164_promitipo = 'S') AS tipopromitente
            ;
            INSERT INTO tipoproprietariopromitente (j165_tipoproprietario, j165_tipopromitente)
                SELECT
                    (SELECT j163_tipoproprietario
                       FROM tipoproprietario
                      WHERE j163_descricao = 'Espólio de') AS tipoproprietario,

                    (SELECT j164_tipopromitente
                       FROM tipopromitente
                      WHERE j164_promitipo = 'R') AS tipopromitente
            ;
            INSERT INTO tipoproprietariopromitente (j165_tipoproprietario, j165_tipopromitente)
                SELECT
                    (SELECT j163_tipoproprietario
                       FROM tipoproprietario
                      WHERE j163_descricao = 'Sucessão de') AS tipoproprietario,

                    (SELECT j164_tipopromitente
                       FROM tipopromitente
                      WHERE j164_promitipo = 'I') AS tipopromitente
            ;
            --Modifica tabela iptubase para receber campo de tipo de proprietario
            ALTER TABLE iptubase ADD j01_tipoproprietario INT DEFAULT 1;
            --Insere FK
            ALTER TABLE iptubase ADD CONSTRAINT iptubase_tipoproprietario_fk FOREIGN KEY (j01_tipoproprietario) REFERENCES tipoproprietario (j163_tipoproprietario);

            --Modifica tabela averbacgm para receber campo tipo de proprietario
            ALTER TABLE averbacgm ADD j76_tipoproprietario INT;
            --Modifica tabela averbacgm para receber campo tipo de promitente
            ALTER TABLE averbacgm ADD j76_tipopromitente INT;

            --Insere FK
            ALTER TABLE averbacgm ADD CONSTRAINT averbacgm_tipoproprietario_fk FOREIGN KEY (j76_tipoproprietario) REFERENCES tipoproprietario (j163_tipoproprietario);
            ALTER TABLE averbacgm ADD CONSTRAINT averbacgm_tipopromitente_fk FOREIGN KEY (j76_tipopromitente) REFERENCES tipopromitente (j164_tipopromitente);

            --Modifica tabela propri para receber campo de tipo de proprietario
            ALTER TABLE propri ADD j42_tipoproprietario INT DEFAULT 1;
            --Insere FK
            ALTER TABLE propri ADD CONSTRAINT propri_tipoproprietario_fk FOREIGN KEY (j42_tipoproprietario) REFERENCES tipoproprietario (j163_tipoproprietario);

            --Modifica tabela promitente para receber campo de tipo de promitente
            ALTER TABLE promitente ADD j41_tipopromitente INT DEFAULT 1;
            --Insere FK
            ALTER TABLE promitente ADD CONSTRAINT promitente_tipopromitente_fk FOREIGN KEY (j41_tipopromitente) REFERENCES tipopromitente (j164_tipopromitente);

            --Modifica tabela averbacgmold para receber campo tipo de proprietario
            ALTER TABLE averbacgmold ADD j79_tipoproprietario INT;
            --Modifica tabela averbacgmold para receber campo tipo de promitente
            ALTER TABLE averbacgmold ADD j79_tipopromitente INT;
            --Modifica tabela averbatipo para receber campo de validacao se exluir proprietarios e promitentes ou não.
--             ALTER TABLE averbatipo ADD j93_excluipropripromi BOOLEAN DEFAULT TRUE;

            --Insere FK
            ALTER TABLE averbacgmold ADD CONSTRAINT averbacgmold_tipoproprietario_fk FOREIGN KEY (j79_tipoproprietario) REFERENCES tipoproprietario (j163_tipoproprietario);
            ALTER TABLE averbacgmold ADD CONSTRAINT averbacgmold_tipopromitente_fk FOREIGN KEY (j79_tipopromitente) REFERENCES tipopromitente (j164_tipopromitente);

            --Altera todos os registros com o tipo de proprietario fixo proprietario.
            UPDATE tipoproprietario
            SET j163_abreviatura = (SELECT
                                        CASE WHEN(v04_expfalecimentocda = '') THEN 'ESP'
                                             WHEN(v04_expfalecimentocda is null ) THEN 'ESP'
                                        ELSE
                                            v04_expfalecimentocda
                                        END
                                    FROM pardiv
                                    WHERE v04_instit in (select codigo from db_config where prefeitura is true))
            WHERE j163_tipoproprietario = (
            select j163_tipoproprietario 
              from tipoproprietario 
             where j163_descricao ilike '%Espólio de%');

SQL
      );

    }

    public function upViewProprietario() {
        DB::connection()->getPdo()->exec(<<<SQL

DROP VIEW if exists venal;
DROP VIEW if exists averbacoes;
DROP VIEW if exists proprietario;

CREATE OR REPLACE VIEW cadastro.proprietario AS
SELECT x.z01_numcgm,
   x.j01_matric,
   x.z01_cgccpf,
        CASE
            WHEN x.totpropri > 0 THEN
            CASE
                WHEN x.totpropri = 1 THEN rtrim(x.proprietario::text)
                WHEN x.totpropri = 2 THEN rtrim(x.proprietario::text) || ' E OUTRO'::text
                ELSE rtrim(x.proprietario::text) || ' E OUTROS'::text
            END::character varying
            ELSE x.proprietario
        END AS proprietario,
    btrim(substr(
        CASE
            WHEN x.totpromi > 0 THEN
            CASE
                WHEN x.totpromi = 1 THEN rtrim(x.z01_nome)
                WHEN x.totpromi = 2 THEN rtrim(x.z01_nome) || ' E OUTRO'::text
                ELSE rtrim(x.z01_nome) || ' E OUTROS'::text
            END
            ELSE x.z01_nome
        END, 1, 40))::character varying AS z01_nome_original,
    btrim(substr(CASE
            WHEN x.totpromi > 0 THEN
            CASE
                WHEN x.totpromi = 1 THEN coalesce(x.tipopro, '') ||rtrim(x.z01_nome)
                WHEN x.totpromi = 2 THEN coalesce(x.tipopro, '')||rtrim(x.z01_nome) || ' E OUTRO'::text
                ELSE coalesce(x.tipopro, '')||rtrim(x.z01_nome) || ' E OUTROS'::text
            END
            ELSE coalesce(x.tipopro, '')||x.z01_nome
        END, 1, 40))::character varying AS z01_nome,
    btrim(
        CASE
            WHEN x.totpromi > 0 THEN
            CASE
                WHEN x.totpromi = 1 THEN rtrim(x.z01_nomecompleto)
                WHEN x.totpromi = 2 THEN rtrim(x.z01_nomecompleto) || ' E OUTRO'::text
                ELSE rtrim(x.z01_nomecompleto) || ' E OUTROS'::text
            END
            ELSE x.z01_nomecompleto
        END)::character varying AS z01_nomecompleto_original,
   btrim(
        CASE
            WHEN x.totpromi > 0 THEN
            CASE
                WHEN x.totpromi = 1 THEN coalesce(x.tipopro, '')||rtrim(x.z01_nomecompleto)
                WHEN x.totpromi = 2 THEN coalesce(x.tipopro, '')||rtrim(x.z01_nomecompleto) || ' E OUTRO'::text
                ELSE coalesce(x.tipopro, '')||rtrim(x.z01_nomecompleto) || ' E OUTROS'::text
            END
            ELSE coalesce(x.tipopro, '')||x.z01_nomecompleto
        END)::character varying AS z01_nomecompleto,
        CASE
            WHEN length(btrim(x.z01_ender::text)) = 0 AND length(btrim(x.z01_cxpostal::text)) > 0 AND to_number(x.z01_cxpostal::text, '999999'::text) > 0::numeric THEN ('CAIXA POSTAL: '::text || x.z01_cxpostal::text)::character varying
            ELSE x.z01_ender::character varying(80)
        END AS z01_ender,
   x.z01_munic,
   x.z01_bairro,
   x.z01_cep,
   x.z01_uf,
   x.z01_numero,
   x.z01_compl,
   x.codpri,
   x.nomepri::character varying(40) AS nomepri,
   x.tipopri::character varying(40) AS tipopri,
   x.j39_numero,
   x.j39_compl,
   x.j34_setor,
   x.j34_quadra,
   x.j34_lote,
   x.j34_zona,
   x.j34_bairro,
   x.j40_refant,
   x.j40_registrocartografico,
   x.j01_idbql,
   x.j14_codigo,
   x.j14_nome,
   x.j14_tipo,
   x.j13_codi,
   x.j13_descr,
   x.j01_baixa,
   x.j34_area,
   x.j34_areal,
   x.j44_numcgm,
   x.j41_numcgm,
   x.j43_matric,
   x.j01_datacad,
        CASE
            WHEN x.j43_munic IS NULL THEN x.z01_munic
            ELSE x.j43_munic
        END AS j43_munic,
        CASE
            WHEN x.j43_ender IS NULL THEN x.z01_ender
            ELSE x.j43_ender
        END AS j43_ender,
        CASE
            WHEN x.j43_cep IS NULL THEN x.z01_cep
            ELSE x.j43_cep
        END AS j43_cep,
        CASE
            WHEN x.j43_uf IS NULL THEN x.z01_uf
            ELSE x.j43_uf
        END AS j43_uf,
    x.j43_dest,
        CASE
            WHEN x.j43_numimo IS NULL THEN x.z01_numero
            ELSE x.j43_numimo
        END AS j43_numimo,
        CASE
            WHEN x.j43_cxpost IS NULL THEN x.z01_cxpostal::text
            ELSE to_char(x.j43_cxpost, '99999999999999999999'::text)
        END AS j43_cxpost,
        CASE
            WHEN x.j43_comple IS NULL THEN x.z01_compl
            ELSE x.j43_comple
        END AS j43_comple,
    x.j01_tipoimp::character varying(20) AS j01_tipoimp,
    x.j01_codave,
    x.j37_zona,
    x.z01_cgmpri,
    x.j39_pavim,
    x.j05_codigoproprio,
    x.j06_setorloc,
    x.j06_quadraloc,
    x.j06_lote,
    x.j05_descr,
    x.pql_localizacao,
    cgmpropri.z01_cgccpf AS z01_cgccpfpropri,
    cgmpropri.z01_nomecomple AS z01_nomecomplepri,
    cgmpropri.z01_ender AS z01_enderpri,
    cgmpropri.z01_munic AS z01_municpri,
    cgmpropri.z01_bairro AS z01_bairropri,
    cgmpropri.z01_cep AS z01_ceppri,
    cgmpropri.z01_uf AS z01_ufpri,
    cgmpropri.z01_numero AS z01_numeropri,
    cgmpropri.z01_compl AS z01_complpri,
    j01_tipoproprietario,
    tipopro,
    j163_abreviatura,
    j164_abreviatura
   FROM ( SELECT iptubase.j01_matric,
                 iptubase.j01_datacad,
            cgm.z01_numcgm,
          CASE
                    WHEN promite.z01_numcgm IS NULL THEN cgm.z01_cgccpf
                    ELSE cgmpromite.z01_cgccpf
                END AS z01_cgccpf,
          CASE
                    WHEN promite.z01_nome IS NULL THEN cgm.z01_nome::text
                    ELSE (( SELECT COALESCE(btrim(cfiptu.j18_textoprom::text) || ' '::text, ''::text) AS "coalesce"
                       FROM cfiptu
                      ORDER BY cfiptu.j18_anousu DESC
                     LIMIT 1)) || substr(promite.z01_nome, 1, 50)::text
                END AS z01_nome,
          CASE
                    WHEN promite.z01_nome IS NULL THEN cgm.z01_nome::text
                    ELSE (( SELECT COALESCE(btrim(cfiptu.j18_textoprom::text) || ' '::text, ''::text) AS "coalesce"
                       FROM cfiptu
                      ORDER BY cfiptu.j18_anousu DESC
                     LIMIT 1)) || promite.z01_nome::text
                END AS z01_nomecompleto,
                CASE
                    WHEN promite.z01_numcgm IS NULL THEN cgm.z01_numcgm
                    ELSE promite.z01_numcgm
                END AS z01_cgmpri,
            cgm.z01_nome AS proprietario,
                CASE
                    WHEN iptuender.j43_ender IS NULL THEN
                    CASE
                        WHEN promitente.j41_numcgm IS NULL THEN cgm.z01_ender
                        ELSE promite.z01_ender
                    END
                    ELSE iptuender.j43_ender
                END AS z01_ender,
                CASE
                    WHEN iptuender.j43_ender IS NULL THEN
                    CASE
                        WHEN promitente.j41_numcgm IS NULL THEN cgm.z01_munic
                        ELSE promite.z01_munic
                    END
                    ELSE iptuender.j43_munic
                END AS z01_munic,
                CASE
                    WHEN iptuender.j43_ender IS NULL THEN
                    CASE
                        WHEN promitente.j41_numcgm IS NULL THEN cgm.z01_bairro
                        ELSE promite.z01_bairro
                    END
                    ELSE iptuender.j43_bairro
                END AS z01_bairro,
                CASE
                    WHEN iptuender.j43_ender IS NULL THEN
                    CASE
                        WHEN promitente.j41_numcgm IS NULL THEN cgm.z01_cep
                        ELSE promite.z01_cep
                    END::bpchar
                    ELSE iptuender.j43_cep
                END AS z01_cep,
                CASE
                    WHEN iptuender.j43_ender IS NULL THEN
                    CASE
                        WHEN promitente.j41_numcgm IS NULL THEN cgm.z01_uf
                        ELSE promite.z01_uf
                    END::bpchar
                    ELSE iptuender.j43_uf
                END AS z01_uf,
                CASE
                    WHEN iptuender.j43_ender IS NULL THEN
                    CASE
                        WHEN promitente.j41_numcgm IS NULL THEN cgm.z01_numero
                        ELSE promite.z01_numero
                    END
                    ELSE iptuender.j43_numimo
                END AS z01_numero,
                CASE
                    WHEN iptuender.j43_ender IS NULL THEN
                    CASE
                        WHEN promitente.j41_numcgm IS NULL THEN cgm.z01_compl
                        ELSE promite.z01_compl
                    END::bpchar
                    ELSE iptuender.j43_comple
                END AS z01_compl,
                CASE
                    WHEN iptuender.j43_cxpost IS NULL THEN
                    CASE
                        WHEN promitente.j41_numcgm IS NULL THEN cgm.z01_cxpostal
                        ELSE promite.z01_cxpostal
                    END
                    ELSE iptuender.j43_cxpost::character varying(20)
                END AS z01_cxpostal,
                CASE
                    WHEN rr.j14_codigo IS NULL THEN r.j14_codigo
                    ELSE rr.j14_codigo
                END AS codpri,
                CASE
                    WHEN rr.j14_nome IS NULL THEN r.j14_nome
                    ELSE rr.j14_nome
                END AS nomepri,
                CASE
                    WHEN rr.j14_tipo IS NULL THEN rt.j88_sigla
                    ELSE rrt.j88_sigla
                END AS tipopri,
                CASE
                    WHEN length(btrim(testadanumero.j15_numero::character varying::text)) > 0 AND iptuconstr.j39_matric IS NULL THEN testadanumero.j15_numero
                    ELSE iptuconstr.j39_numero
                END AS j39_numero,
                CASE
                    WHEN length(btrim(testadanumero.j15_compl::text)) > 0 AND iptuconstr.j39_matric IS NULL THEN testadanumero.j15_compl
                    ELSE iptuconstr.j39_compl
                END AS j39_compl,
            lote.j34_setor,
            lote.j34_quadra,
            lote.j34_lote,
            lote.j34_zona,
            lote.j34_bairro,
            iptuant.j40_refant,
            iptubase.j01_idbql,
       r.j14_codigo,
       r.j14_nome,
            rt.j88_sigla::text AS j14_tipo,
            bairro.j13_codi,
            bairro.j13_descr,
            iptubase.j01_baixa,
            lote.j34_area,
            lote.j34_areal,
            imobil.j44_numcgm,
            promitente.j41_numcgm,
            iptuender.j43_matric,
            iptuender.j43_dest,
            iptuender.j43_ender,
            iptuender.j43_numimo,
            iptuender.j43_comple,
            iptuender.j43_bairro,
            iptuender.j43_munic,
            iptuender.j43_uf,
            iptuender.j43_cep,
            iptuender.j43_cxpost,
            iptuant.j40_registrocartografico,
       CASE
                    WHEN iptuconstr.j39_idcons IS NULL THEN 'Territorial'::text
                    ELSE 'Predial'::text
                END AS j01_tipoimp,
            iptubase.j01_codave,
            face.j37_zona,
            iptuconstr.j39_pavim,
            ( SELECT count(propri.j42_matric) AS count
                   FROM propri
                  WHERE propri.j42_matric = iptubase.j01_matric) AS totpropri,
            ( SELECT count(promitente_1.j41_matric) AS count
                   FROM promitente promitente_1
                  WHERE promitente_1.j41_matric = iptubase.j01_matric) AS totpromi,
            setorloc.j05_codigoproprio,
            loteloc.j06_setorloc,
            loteloc.j06_quadraloc,
            loteloc.j06_lote,
            setorloc.j05_descr,
            (((((setorloc.j05_codigoproprio::text || '-'::text) || setorloc.j05_descr::text) || '/'::text) || loteloc.j06_quadraloc::text) || '/'::text) || loteloc.j06_lote::text AS pql_localizacao,
            iptubase.j01_tipoproprietario,             
            tipoproprietario.j163_abreviatura,         
            tipopromitente.j164_abreviatura,           
            CASE                  
            WHEN (fc_regrasconfig(1) = 0) THEN 
              CASE              
              WHEN (promite.z01_nome IS NULL) THEN upper(COALESCE((tipoproprietario.j163_abreviatura || ' '::text), ''::text))              
              ELSE upper(COALESCE((tipopromitente.j164_abreviatura || ' '::text), ''::text))      
               END               
            WHEN (fc_regrasconfig(1) = 1) THEN upper(COALESCE((tipoproprietario.j163_abreviatura || ' '::text), ''::text))                    
            WHEN (fc_regrasconfig(1) = 2) THEN 
              CASE              
              WHEN (promite.z01_nome IS NULL) THEN upper(COALESCE((tipoproprietario.j163_abreviatura || ' '::text), ''::text))              
              ELSE upper(COALESCE((tipopromitente.j164_abreviatura || ' '::text), ''::text))      
               END               
            ELSE NULL::text   
             END AS tipopro
           FROM iptubase
             JOIN cgm ON cgm.z01_numcgm = iptubase.j01_numcgm
             LEFT JOIN iptuconstr ON iptuconstr.j39_matric = iptubase.j01_matric AND iptuconstr.j39_idprinc IS TRUE AND iptuconstr.j39_dtdemo IS NULL
             LEFT JOIN ruas rr ON rr.j14_codigo = iptuconstr.j39_codigo
             LEFT JOIN ruastipo rrt ON rrt.j88_codigo = rr.j14_tipo
             LEFT JOIN iptuant ON iptuant.j40_matric = iptubase.j01_matric
             JOIN lote ON iptubase.j01_idbql = lote.j34_idbql
             LEFT JOIN testpri ON testpri.j49_idbql = lote.j34_idbql
             LEFT JOIN testadanumero ON testadanumero.j15_idbql = testpri.j49_idbql AND testadanumero.j15_face = testpri.j49_face
             LEFT JOIN face ON face.j37_face = testpri.j49_face
             LEFT JOIN ruas r ON r.j14_codigo = testpri.j49_codigo
             LEFT JOIN ruastipo rt ON rt.j88_codigo = r.j14_tipo
             LEFT JOIN imobil ON iptubase.j01_matric = imobil.j44_matric
             LEFT JOIN promitente ON iptubase.j01_matric = promitente.j41_matric AND promitente.j41_tipopro IS TRUE
             LEFT JOIN cgm cgmpromite ON promitente.j41_numcgm = cgmpromite.z01_numcgm
             LEFT JOIN cgm promite ON promite.z01_numcgm = promitente.j41_numcgm
             LEFT JOIN bairro ON bairro.j13_codi = lote.j34_bairro
             LEFT JOIN iptuender ON iptubase.j01_matric = iptuender.j43_matric
             LEFT JOIN loteloc ON loteloc.j06_idbql = iptubase.j01_idbql
             LEFT JOIN setorloc ON setorloc.j05_codigo = loteloc.j06_setorloc
             LEFT JOIN setor ON setor.j30_codi = lote.j34_setor
             LEFT JOIN tipopromitente ON tipopromitente.j164_promitipo = promitente.j41_promitipo
            LEFT JOIN tipoproprietario ON tipoproprietario.j163_tipoproprietario = iptubase.j01_tipoproprietario) x
     LEFT JOIN cgm cgmpropri ON x.z01_numcgm = cgmpropri.z01_numcgm;

create or replace view cadastro.averbacoes as
SELECT protprocesso.p58_codproc,
       protprocesso.p58_dtproc,
       averbacao.j75_codigo,
       averbacao.j75_matric,
       averbacao.j75_data,
       averbacao.j75_obs,
       averbacao.j75_tipo,
       averbacao.j75_dttipo,
       averbacao.j75_situacao,
       averbacao.j75_regra,
       proprietario.j40_refant,
       setorloc.j05_codigoproprio,
       loteloc.j06_setorloc,
       loteloc.j06_quadraloc,
       loteloc.j06_lote,
       loteam.j34_descr,
       proprietario.codpri,
       proprietario.nomepri,
       proprietario.z01_cgccpf,
       proprietario.tipopri,
       proprietario.j39_numero,
       proprietario.j39_compl,
       proprietario.j34_area,
       proprietario.j34_setor,
       proprietario.j34_quadra,
       proprietario.j34_lote,
       proprietario.j01_baixa,
       iptuconstr.j39_area,
       ( SELECT array_to_string(array_accum((cgm.z01_numcgm || ' - '::text) || cgm.z01_nome::text), ','::text) AS array_to_string
           FROM cgm 
             JOIN averbacgm ON cgm.z01_numcgm = averbacgm.j76_numcgm
          WHERE averbacao.j75_codigo = averbacgm.j76_averbacao) AS z01_nomeadq,
       ( SELECT array_to_string(array_accum((cgm.z01_numcgm || ' - '::text) || cgm.z01_nome::text), ','::text) AS array_to_string
           FROM cgm 
             JOIN averbacgmold ON cgm.z01_numcgm = averbacgmold.j79_numcgm
          WHERE averbacao.j75_codigo = averbacgmold.j79_averbacao) AS z01_nometrans,
       db_usuarios.login,
       db_usuarios.nome,
       rhpessoal.rh01_regist,
       rhfuncao.rh37_descr
   FROM averbacao
   LEFT JOIN averbaprocesso ON averbacao.j75_codigo = averbaprocesso.j77_averbacao
   LEFT JOIN protprocesso   ON protprocesso.p58_codproc = averbaprocesso.j77_codproc
   JOIN iptubase            ON averbacao.j75_matric = iptubase.j01_matric
   LEFT JOIN loteloc        ON iptubase.j01_idbql = loteloc.j06_idbql
   LEFT JOIN setorloc       ON loteloc.j06_setorloc = setorloc.j05_codigo
   JOIN proprietario        ON iptubase.j01_matric = proprietario.j01_matric
   LEFT JOIN iptuconstr     ON iptubase.j01_matric = iptuconstr.j39_matric
   LEFT JOIN loteloteam     ON proprietario.j01_idbql = loteloteam.j34_idbql
   LEFT JOIN loteam         ON loteloteam.j34_loteam = loteam.j34_loteam
   LEFT JOIN db_usuarios    ON db_usuarios.id_usuario = fc_getsession('db_id_usuario'::text)::integer
   LEFT JOIN db_usuacgm     ON db_usuacgm.id_usuario = db_usuarios.id_usuario
   LEFT JOIN rhpessoal      ON rhpessoal.rh01_numcgm = db_usuacgm.cgmlogin
   LEFT JOIN rhpessoalmov   ON rhpessoalmov.rh02_regist = rhpessoal.rh01_regist
                           AND rhpessoalmov.rh02_anousu = fc_anofolha(fc_getsession('db_instit'::text)::integer)
                           AND rhpessoalmov.rh02_mesusu = fc_mesfolha(fc_getsession('db_instit'::text)::integer)
   LEFT JOIN rhpesrescisao  ON rhpessoalmov.rh02_seqpes = rhpesrescisao.rh05_seqpes
   LEFT JOIN rhfuncao       ON rhpessoalmov.rh02_funcao = rhfuncao.rh37_funcao
                           AND rhpessoalmov.rh02_instit = rhfuncao.rh37_instit
  WHERE     db_usuarios.usuarioativo::text = 1::text
        AND db_usuarios.usuext = 0
        AND rhpessoalmov.rh02_instit::text = fc_getsession('db_instit'::text)
        AND rhpesrescisao.rh05_recis IS NULL;

create or replace view cadastro.venal as
SELECT COALESCE((SELECT sum(iptucale.j22_valor) AS sum 
                   FROM cadastro.iptucale 
                  WHERE ((iptucale.j22_anousu = iptucalc.j23_anousu) AND (iptucale.j22_matric = iptucalc.j23_matric))), 
                  (0)::double precision) AS j22_valor, 
       ('R$ '::text || translate(
       to_char((iptucalc.j23_vlrter + 
       COALESCE((SELECT sum(iptucale.j22_valor) AS sum 
                   FROM cadastro.iptucale 
                  WHERE ((iptucale.j22_anousu = iptucalc.j23_anousu) 
                    AND (iptucale.j22_matric = iptucalc.j23_matric))), (0)::double precision)), 
       '999,999,999,990.99'::text), 
       ',.'::text,
       '.,'::text)) AS j23_venaltotal,
       proprietario.j40_refant,
       loteloc.j06_setorloc,
       loteloc.j06_quadraloc,
       loteloc.j06_lote,
       loteam.j34_descr,
       proprietario.j01_matric,
       proprietario.codpri,
       proprietario.nomepri,
       proprietario.z01_cgccpf,
       proprietario.tipopri,
       proprietario.j39_numero,
       proprietario.j39_compl,
       proprietario.j34_area,
       iptucalc.j23_anousu,
       iptucalc.j23_matric,
       iptucalc.j23_testad,
       iptucalc.j23_arealo,
       iptucalc.j23_areafr,
       iptucalc.j23_areaed,
       iptucalc.j23_m2terr,
       iptucalc.j23_vlrter,
       iptucalc.j23_aliq,
       iptucalc.j23_vlrisen,
       iptucalc.j23_tipoim,
       iptucalc.j23_manual,
       iptucalc.j23_tipocalculo,
       to_date(fc_getsession('db_datausu'::text), 'YYYY-MM-DD'::text) AS data_sessao,
       fc_dataextenso(to_date(fc_getsession('db_datausu'::text), 'YYYY-MM-DD'::text)) AS data_sessao_extenso,
       db_usuarios.login, 
       db_usuarios.nome,
       rhpessoal.rh01_regist,
       rhfuncao.rh37_descr
  FROM ((((cadastro.iptucalc JOIN proprietario ON ((iptucalc.j23_matric = proprietario.j01_matric)))
  LEFT JOIN cadastro.loteloc ON ((proprietario.j01_idbql = loteloc.j06_idbql)))
  LEFT JOIN cadastro.loteloteam 
    ON ((proprietario.j01_idbql = loteloteam.j34_idbql)))
  LEFT JOIN cadastro.loteam           
    ON ((loteloteam.j34_loteam = loteam.j34_loteam)))
  LEFT JOIN configuracoes.db_usuarios 
    ON ((db_usuarios.id_usuario = (fc_getsession('db_id_usuario'::text))::integer))
  LEFT JOIN configuracoes.db_usuacgm  
    ON db_usuacgm.id_usuario = db_usuarios.id_usuario
  LEFT JOIN pessoal.rhpessoal         
    ON rh01_numcgm = cgmlogin
  LEFT JOIN pessoal.rhpessoalmov      
    ON rh02_regist = rh01_regist
   AND rh02_anousu = fc_anofolha(fc_getsession('db_instit')::integer)
   AND rh02_mesusu = fc_mesfolha(fc_getsession('db_instit')::integer)
  LEFT JOIN pessoal.rhpesrescisao     ON rh02_seqpes = rh05_seqpes
  LEFT JOIN pessoal.rhfuncao          ON rh02_funcao = rh37_funcao
                                     AND rh02_instit = rh37_instit
WHERE rh05_seqpes IS NULL
  AND rh02_instit = cast(fc_getsession('db_instit') as integer)
  AND db_usuarios.usuarioativo = 1
  AND db_usuarios.usuext = 0
  AND (iptucalc.j23_anousu = (fc_getsession('db_anousu'::text))::integer);
SQL
        );        
    }

    public function upViewBuscaEnvolvidos() {
        DB::connection()->getPdo()->exec(<<<SQL
          create or replace function fc_busca_envolvidos(boolean,integer,char(1),integer) returns setof tp_socio_promitente  as
$$
declare

lPrincipal		  alias for  $1; -- Traz apenas o proprietario principal
iRegra			    alias for  $2; -- Traz a regra configurada na db_config, pardiv ou parjuridico
iTipoOrigem	    alias for  $3; -- Verifica se é "M" Matrícula, "I" Inscrição ou "C" Cgm
iCodOrigem	    alias for  $4; -- Traz o código da Matrícula ou Inscrição

iNumcgm         integer;
iMatricula      integer;
iInscricao      integer;

sNome           varchar(40);

lraise          boolean default false;

sSql            text	  default '';

rSocios         record;
rPromitente     record;
rProprietarios  record;

rtp_promitente  tp_socio_promitente%ROWTYPE;

begin

 if iTipoOrigem = 'I' then

   -- Traz CGM do Issbase

   select z01_numcgm, z01_nome, q02_inscr
     into iNumcgm, sNome, iInscricao
     from issbase
	        inner join cgm  on z01_numcgm = q02_numcgm
	  where q02_inscr = iCodOrigem;

	rtp_promitente.riNumcgm	   := iNumcgm;
	rtp_promitente.rvNome	     := sNome;
	rtp_promitente.riInscr	   := iInscricao;
	rtp_promitente.riTipoEnvol := 4;
	return  next rtp_promitente;

	-- Traz CGM dos Socios
	if iRegra = 1 and lPrincipal = false then

		sSql := 'select z01_nome, z01_numcgm, q02_inscr
			         from issbase
						        inner join socios on q95_cgmpri = q02_numcgm
						        inner join cgm    on z01_numcgm = q95_numcgm
				      where q95_tipo  = 1
                and q02_inscr = '||iCodOrigem;

		for rSocios in execute sSql loop

			rtp_promitente.riNumcgm	   := rSocios.z01_numcgm;
			rtp_promitente.rvNome	     := rSocios.z01_nome;
			rtp_promitente.riInscr	   := rSocios.q02_inscr;
			rtp_promitente.riTipoEnvol := 5;
			return  next rtp_promitente;

		end loop;

	end if;

elsif iTipoOrigem = 'M' then

  if lraise then
	  raise notice 'Regra IPTU: % ',iRegra;
  end if;

  -- Traz CGM do Proprietário e Promitente
  if iRegra = 0 then
  
    select z01_numcgm, z01_nome, j01_matric
	   into iNumcgm, sNome, iMatricula
     from cgm
	        inner join iptubase on iptubase.j01_numcgm = cgm.z01_numcgm
	  where j01_matric = iCodOrigem;

	  rtp_promitente.riNumcgm	   := iNumcgm;
	  rtp_promitente.rvNome		   := sNome;
	  rtp_promitente.riMatric	   := iMatricula;
	  rtp_promitente.riTipoEnvol := 1;
	  rtp_promitente.riInscr	   := null;
	  return next rtp_promitente;

      if lPrincipal = false then

		 sSql := ' select z01_numcgm, z01_nome, j41_matric
				         from promitente
					            inner join cgm on z01_numcgm = j41_numcgm
					      where j41_matric = '||iCodOrigem||'
             order by j41_tipopro desc';

		 for rPromitente in execute sSql loop

			 rtp_promitente.riNumcgm	  := rPromitente.z01_numcgm;
			 rtp_promitente.rvNome		  := rPromitente.z01_nome;
			 rtp_promitente.riMatric	  := rPromitente.j41_matric;
			 rtp_promitente.riTipoEnvol := 3;
			 return  next rtp_promitente;
		 end loop;

		 sSql := 'select z01_numcgm, z01_nome, j42_matric
				        from propri
				             inner join cgm on z01_numcgm = j42_numcgm
				       where j42_matric = '||iCodOrigem;

		 for rProprietarios in execute sSql loop

		   rtp_promitente.riNumcgm	  := rProprietarios.z01_numcgm;
			 rtp_promitente.rvNome		  := rProprietarios.z01_nome;
			 rtp_promitente.riMatric	  := rProprietarios.j42_matric;
			 rtp_promitente.riTipoEnvol := 2;
		   return  next rtp_promitente;
		 end loop;
	end if;

      -- Se lPrincipal for true mesmo sendo regra 2 retorna apenas o Proprietário
    if lPrincipal = false then

		 sSql := ' select z01_numcgm, z01_nome, j41_matric
				         from promitente
					            inner join cgm on z01_numcgm = j41_numcgm
					      where j41_tipopro is false AND j41_matric = '||iCodOrigem||'
             order by j41_tipopro desc';

		 for rPromitente in execute sSql loop

			 rtp_promitente.riNumcgm	  := rPromitente.z01_numcgm;
			 rtp_promitente.rvNome		  := rPromitente.z01_nome;
			 rtp_promitente.riMatric	  := rPromitente.j41_matric;
			 rtp_promitente.riTipoEnvol := 3;
			 return  next rtp_promitente;
		 end loop;

		 sSql := 'select z01_numcgm, z01_nome, j42_matric
				        from propri
				             inner join cgm on z01_numcgm = j42_numcgm
				       where j42_matric = '||iCodOrigem;

		 for rProprietarios in execute sSql loop

		   rtp_promitente.riNumcgm	  := rProprietarios.z01_numcgm;
			 rtp_promitente.rvNome		  := rProprietarios.z01_nome;
			 rtp_promitente.riMatric	  := rProprietarios.j42_matric;
			 rtp_promitente.riTipoEnvol := 2;
		   return  next rtp_promitente;
		 end loop;

	  end if;

   -- Traz CGM do Proprietário
  elsif iRegra = 1 then

	--   select z01_numcgm, upper(coalesce(j163_descricao||' - ', 'ProprietÃ¡rio - '))||z01_nome, j01_matric
	-- 	  into iNumcgm, sNome, iMatricula
	-- 	  from cgm
	--          inner join iptubase on iptubase.j01_numcgm = cgm.z01_numcgm
	-- 		 left join tipoproprietario on j163_tipoproprietario = j01_tipoproprietario
	--    where j01_matric = iCodOrigem;

	   SELECT z01_numcgm,
	   		  z01_nome,
			  j01_matric
	     INTO iNumcgm,
		 	  sNome,
			  iMatricula
	     FROM proprietario
	    WHERE j01_matric = iCodOrigem;

	   rtp_promitente.riNumcgm	  := iNumcgm;
	   rtp_promitente.rvNome	    := sNome;
	   rtp_promitente.riMatric	  := iMatricula;
	   rtp_promitente.riTipoEnvol := 1;
	   rtp_promitente.riInscr	    := null;
	   return  next rtp_promitente;

       -- Se lPrincipal for true retorna outros proprietário
       if lPrincipal = false then

		  sSql := ' select z01_numcgm, z01_nome, j42_matric
					        from propri
					             inner join cgm on z01_numcgm = j42_numcgm
					       where j42_matric = '|| iCodOrigem;

		  for rProprietarios in execute sSql loop

			  rtp_promitente.riNumcgm		 := rProprietarios.z01_numcgm;
			  rtp_promitente.rvNome			 := rProprietarios.z01_nome;
			  rtp_promitente.riMatric		 := rProprietarios.j42_matric;
			  rtp_promitente.riTipoEnvol := 2;
			  return  next rtp_promitente;
		  end loop;

	   end if;

	-- Traz CGM do  Promitente
  elsif iRegra = 2 then

	   sSql := 'select z01_numcgm, z01_nome, j41_matric
				        from promitente
				             inner join cgm on z01_numcgm = j41_numcgm
				       where j41_matric = '||iCodOrigem||' order by j41_tipopro desc ';

				 for rPromitente in execute sSql loop

						rtp_promitente.riNumcgm	   := rPromitente.z01_numcgm;
						rtp_promitente.rvNome	     := rPromitente.z01_nome;
						rtp_promitente.riMatric	   := rPromitente.j41_matric;
						rtp_promitente.riTipoEnvol := 3;
						return  next rtp_promitente;
				 end loop;

       -- Se nao encontrou forca regra = 1
       if not found then
          for rPromitente in select * from fc_busca_envolvidos(lPrincipal, 1, 'M', iCodOrigem) loop

         	  rtp_promitente.riNumcgm		 := rPromitente.riNumcgm;
	  		    rtp_promitente.rvNome			 := rPromitente.rvNome;
			      rtp_promitente.riMatric		 := rPromitente.riMatric;
			      rtp_promitente.riTipoEnvol := 1;
			      return  next rtp_promitente;
          end loop;
       end if;

    elsif iRegra = 3 then

SELECT cgm.z01_numcgm, cgm.z01_nome, j01_matric
        INTO iNumcgm, sNome, iMatricula
    FROM (
             SELECT CASE
                        WHEN promitente.j41_numcgm IS NULL
                            THEN iptubase.j01_numcgm
                        ELSE promitente.j41_numcgm
                        END AS k00_numcgm,
                    CASE
                        WHEN promitente.j41_matric IS NULL
                            THEN iptubase.j01_matric
                        ELSE promitente.j41_matric
                        END AS j01_matric
             FROM iptubase
                      LEFT JOIN promitente ON promitente.j41_matric = iptubase.j01_matric AND j41_tipopro IS true
             WHERE iptubase.j01_matric = iCodOrigem) AS X
     INNER JOIN cgm ON cgm.z01_numcgm = x.k00_numcgm;

	  rtp_promitente.riNumcgm	   := iNumcgm;
	  rtp_promitente.rvNome		   := sNome;
	  rtp_promitente.riMatric	   := iMatricula;
	  rtp_promitente.riTipoEnvol := 1;
	  rtp_promitente.riInscr	   := null;
	  return next rtp_promitente;

      -- Se lPrincipal for true mesmo sendo regra 2 retorna apenas o ProprietÃ¡rio
    if lPrincipal = false then

		 sSql := ' select z01_numcgm, z01_nome, j41_matric
				         from promitente
					            inner join cgm on z01_numcgm = j41_numcgm
					      where j41_tipopro is false AND j41_matric = '||iCodOrigem||'
             order by j41_tipopro desc';

		 for rPromitente in execute sSql loop

			 rtp_promitente.riNumcgm	  := rPromitente.z01_numcgm;
			 rtp_promitente.rvNome		  := rPromitente.z01_nome;
			 rtp_promitente.riMatric	  := rPromitente.j41_matric;
			 rtp_promitente.riTipoEnvol := 3;
			 return  next rtp_promitente;
		 end loop;

		 sSql := 'select z01_numcgm, z01_nome, j42_matric
				        from propri
				             inner join cgm on z01_numcgm = j42_numcgm
				       where j42_matric = '||iCodOrigem;

		 for rProprietarios in execute sSql loop

		   rtp_promitente.riNumcgm	  := rProprietarios.z01_numcgm;
			 rtp_promitente.rvNome		  := rProprietarios.z01_nome;
			 rtp_promitente.riMatric	  := rProprietarios.j42_matric;
			 rtp_promitente.riTipoEnvol := 2;
		   return  next rtp_promitente;
		 end loop;
	end if;
	end if;

end if;

if iTipoOrigem = 'C' then

   select z01_numcgm, z01_nome
	   into iNumcgm, sNome
	   from cgm
	  where z01_numcgm = iCodOrigem;

	rtp_promitente.riNumcgm		 := iNumcgm;
	rtp_promitente.rvNome		   := sNome;
	rtp_promitente.riMatric		 := null;
	rtp_promitente.riTipoEnvol := 1;
	rtp_promitente.riInscr		 := null;
	return  next rtp_promitente;

end if;

 return;

end;

$$ language 'plpgsql';
         

SQL
       );
    }

    private function getExpressaoFalecimento() {
        
        
        $instit = DB::table("db_config")
                  ->where("prefeitura", true)
                  ->get(["codigo"])
                  ->first();
                  
        $pardiv = DB::table("pardiv")
                                 ->where("v04_instit", $instit->codigo)
                                 ->get(["v04_expfalecimentocda"])
                                 ->first();
        
        return ($pardiv->v04_expfalecimentocda != "" && $pardiv->v04_expfalecimentocda != null) ? $pardiv->v04_expfalecimentocda : "Espólio de";
    }

    private function upMigration(){
      DB::connection()->getPdo()->exec(<<<SQL
      

      UPDATE tipoproprietario
            SET j163_abreviatura = (SELECT
                                        CASE WHEN(v04_expfalecimentocda = '') THEN 'ESP'
                                             WHEN(v04_expfalecimentocda is null ) THEN 'ESP'
                                        ELSE
                                            v04_expfalecimentocda
                                        END
                                    FROM pardiv
                                    WHERE v04_instit in (select codigo from db_config where prefeitura is true))
            WHERE j163_tipoproprietario = (
                select j163_tipoproprietario from tipoproprietario where j163_descricao ilike '%Espólio de%');
SQL
        );
            
            
            $this->upTipoProprietarioEspolioOuSucessao();
            $this->upIptubase();
            $this->upPromitente();
            $this->upPropri();
    }

    public function upTipoProprietarioEspolioOuSucessao(){

        $v04_expfalecimentocda = $this->getExpressaoFalecimento();
        $parametroDaDivida = str_replace('Ã', 'A', strtoupper($v04_expfalecimentocda));
        $parametroDaDivida = str_replace('Ó', 'O', strtoupper($parametroDaDivida));
        $parametroDaDivida = str_replace(' DE', '', strtoupper($parametroDaDivida));
        $parametroDaDivida = str_replace('SUCESSAO', 'SUC',$parametroDaDivida);
        $parametroDaDivida = str_replace('ESPOLIO', 'ESP', $parametroDaDivida);

        DB::connection()->getPdo()->exec(<<<SQL
            UPDATE tipoproprietario
               SET j163_abreviatura = '{$v04_expfalecimentocda}'
             WHERE j163_abreviatura = '{$parametroDaDivida}';
SQL
);
    }

    public function upIptubase()
    {   
        $v04_expfalecimentocda = $this->getExpressaoFalecimento();
        DB::connection()->getPdo()->exec(<<<SQL

          alter table iptubase disable trigger tg_iptubase_numcgm_alt;
          alter table iptubase disable trigger tg_iptubase_totcon_alt;
             with dadosatualizar as (
             select j01_matric as matric
               from iptubase
              inner join cgm 
                 on iptubase.j01_numcgm = cgm.z01_numcgm
              where z01_dtfalecimento IS NOT NULL)
            UPDATE iptubase 
               SET j01_tipoproprietario = (SELECT j163_tipoproprietario
                                             FROM tipoproprietario
                                            WHERE j163_descricao ILIKE '%{$v04_expfalecimentocda}%' or to_ascii(j163_descricao) ILIKE '%{$v04_expfalecimentocda}%')
              FROM dadosatualizar                                                      
             WHERE j01_matric = matric;

          alter table iptubase enable trigger tg_iptubase_numcgm_alt;
          alter table iptubase enable trigger tg_iptubase_totcon_alt;  

SQL
        );
    }

    public function upPromitente()
    {
        DB::connection()->getPdo()->exec(<<<SQL
         
          UPDATE promitente 
             SET j41_tipopromitente = j164_tipopromitente
            FROM tipopromitente
           WHERE tipopromitente.j164_promitipo = promitente.j41_promitipo              
             AND j41_promitipo <> 'C';
SQL
        );
        
    }

    public function upPropri()
    {   
        $v04_expfalecimentocda = $this->getExpressaoFalecimento();

        DB::connection()->getPdo()->exec(<<<SQL
        
        UPDATE propri 
           SET j42_tipoproprietario = (SELECT j163_tipoproprietario
                                         FROM tipoproprietario
                                        WHERE j163_descricao ILIKE '%{$v04_expfalecimentocda}%')
           where propri.j42_numcgm in (select propri.j42_numcgm
                                         from propri
                                        inner join cgm 
                                           on propri.j42_numcgm = cgm.z01_numcgm
                                        where z01_dtfalecimento IS NOT NULL);

SQL
     );

    }

    private function downDicionario() {
        
      DB::connection()->getPdo()->exec(<<<SQL
        
        DELETE FROM db_menu      WHERE id_item_filho IN (228237, 228238);
        DELETE FROM db_itensmenu WHERE id_item       IN (228237, 228238, 228251);

        DELETE FROM db_menu WHERE id_item_filho = 228251 AND modulo = 578;
        DELETE FROM db_menu WHERE id_item_filho = 228238 AND modulo = 578;
        DELETE FROM db_menu WHERE id_item_filho = 228237 AND modulo = 578;

        DELETE FROM db_sysforkey WHERE codarq = 27 AND codcam = 1010626;
        DELETE FROM db_sysforkey WHERE codarq = 1010542 AND codcam = 1011147;
        DELETE FROM db_sysforkey WHERE codarq = 1010542 AND codcam = 1011148;
        DELETE FROM db_sysforkey WHERE codarq = 27 AND codcam = 1011150;
        DELETE FROM db_sysforkey WHERE codarq = 34 AND codcam = 1011161;
        DELETE FROM db_sysforkey WHERE codarq = 1650 AND codcam IN (1011264, 1011265);
        DELETE FROM db_sysforkey WHERE codarq = 1651 AND codcam IN (1011157, 1011158);
        DELETE FROM db_sysforkey WHERE codarq = 33 AND codcam IN (1011341);

        DELETE FROM db_sysprikey WHERE codarq IN (1010529, 1010530, 1010542);

        DELETE FROM db_syssequencia WHERE codsequencia IN (1000892, 1000893, 1000894);

        DELETE FROM db_sysarqcamp WHERE codarq = 1651 AND codcam = 1011157;
        DELETE FROM db_sysarqcamp WHERE codarq = 1651 AND codcam = 1011158;
        DELETE FROM db_sysarqcamp WHERE codarq = 34 AND codcam = 1011161;
        DELETE FROM db_sysarqcamp WHERE codarq = 1650 AND codcam IN (1011264, 1011265);
        DELETE FROM db_sysarqcamp WHERE codarq = 33 AND codcam IN (1011341);
        DELETE FROM db_sysarqcamp WHERE codcam IN (1010529, 1010530, 1010542, 1011150, 1010626, 1011079, 1011080, 1011081, 1011083, 1011084, 1011146, 1011147, 1011148, 1011269,1011270, 1011273, 1011341); --1011285

        DELETE FROM db_syscampo   WHERE codcam IN (1011079, 1011080, 1011081, 1011083, 1011084, 1011146, 1011147, 1011148, 1011150, 1011157, 1011158, 1011161, 1011264, 1011265, 1011269, 1011270, 1011273, 1011341); --1011285

        DELETE FROM db_sysarqmod  WHERE codarq IN (1010529, 1010530, 1010542);

        DELETE FROM db_acount WHERE codarq IN (1010529, 1010530, 1010542);

        DELETE FROM db_sysarquivo WHERE codarq IN (1010529, 1010530, 1010542);
SQL
      );
    }
    private function downEstrutura(){
      DB::connection()->getPdo()->exec(<<<SQL
      
        DROP TABLE IF EXISTS cadastro.tipoproprietario CASCADE;
        DROP TABLE IF EXISTS cadastro.tipopromitente CASCADE;
        DROP TABLE IF EXISTS cadastro.tipoproprietariopromitente CASCADE;

        --Remove coluna na iptubase que referencia tipoproprietario
        ALTER TABLE cadastro.iptubase DROP COLUMN IF EXISTS j01_tipoproprietario;
        --Remove coluna na averbacgm que referencia tipoproprietario
        ALTER TABLE cadastro.averbacgm DROP COLUMN IF EXISTS j76_tipoproprietario;
        --Remove coluna na averbacgm que referencia tipopromitente
        ALTER TABLE cadastro.averbacgm DROP COLUMN IF EXISTS j76_tipopromitente;
        --Remove coluna na averbacgmold que referencia tipoproprietario
        ALTER TABLE cadastro.averbacgmold DROP COLUMN IF EXISTS j79_tipoproprietario;
        --Remove coluna na averbacgmold que referencia tipopromitente
        ALTER TABLE cadastro.averbacgmold DROP COLUMN IF EXISTS j79_tipopromitente;
        --Remove coluna na propri que referencia tipoproprietario
        ALTER TABLE cadastro.propri DROP COLUMN IF EXISTS j42_tipoproprietario;
        --Remove coluna na promitente que referencia tipopromitente
        ALTER TABLE cadastro.promitente DROP COLUMN IF EXISTS j41_tipopromitente;
SQL
      );
    }

    private function downViewProprietario(){
      DB::connection()->getPdo()->exec(<<<SQL

        DROP VIEW if exists venal;
        DROP VIEW if exists averbacoes;
        DROP VIEW if exists proprietario;

CREATE OR REPLACE VIEW public.proprietario AS
SELECT x.z01_numcgm,
   x.j01_matric,
   x.z01_cgccpf,
        CASE
            WHEN x.totpropri > 0 THEN
            CASE
                WHEN x.totpropri = 1 THEN rtrim(x.proprietario::text)
                WHEN x.totpropri = 2 THEN rtrim(x.proprietario::text) || ' E OUTRO'::text
                ELSE rtrim(x.proprietario::text) || ' E OUTROS'::text
            END::character varying
            ELSE x.proprietario
        END AS proprietario,
    btrim(substr(
        CASE
            WHEN x.totpromi > 0 THEN
            CASE
                WHEN x.totpromi = 1 THEN rtrim(x.z01_nome)
                WHEN x.totpromi = 2 THEN rtrim(x.z01_nome) || ' E OUTRO'::text
                ELSE rtrim(x.z01_nome) || ' E OUTROS'::text
            END
            ELSE x.z01_nome
        END, 1, 40))::character varying AS z01_nome,
    btrim(
        CASE
            WHEN x.totpromi > 0 THEN
            CASE
                WHEN x.totpromi = 1 THEN rtrim(x.z01_nomecompleto)
                WHEN x.totpromi = 2 THEN rtrim(x.z01_nomecompleto) || ' E OUTRO'::text
                ELSE rtrim(x.z01_nomecompleto) || ' E OUTROS'::text
            END
            ELSE x.z01_nomecompleto
        END)::character varying AS z01_nomecompleto,
        CASE
            WHEN length(btrim(x.z01_ender::text)) = 0 AND length(btrim(x.z01_cxpostal::text)) > 0 AND to_number(x.z01_cxpostal::text, '999999'::text) > 0::numeric THEN ('CAIXA POSTAL: '::text || x.z01_cxpostal::text)::character varying
            ELSE x.z01_ender::character varying(80)
        END AS z01_ender,
   x.z01_munic,
   x.z01_bairro,
   x.z01_cep,
   x.z01_uf,
   x.z01_numero,
   x.z01_compl,
   x.codpri,
   x.nomepri::character varying(40) AS nomepri,
   x.tipopri::character varying(40) AS tipopri,
   x.j39_numero,
   x.j39_compl,
   x.j34_setor,
   x.j34_quadra,
   x.j34_lote,
   x.j34_zona,
   x.j34_bairro,
   x.j40_refant,
   x.j01_idbql,
   x.j14_codigo,
   x.j14_nome,
   x.j14_tipo,
   x.j13_codi,
   x.j13_descr,
   x.j01_baixa,
   x.j34_area,
   x.j34_areal,
   x.j44_numcgm,
   x.j41_numcgm,
   x.j43_matric,
   x.j01_datacad,
        CASE
            WHEN x.j43_munic IS NULL THEN x.z01_munic
            ELSE x.j43_munic
        END AS j43_munic,
        CASE
            WHEN x.j43_ender IS NULL THEN x.z01_ender
            ELSE x.j43_ender
        END AS j43_ender,
        CASE
            WHEN x.j43_cep IS NULL THEN x.z01_cep
            ELSE x.j43_cep
        END AS j43_cep,
        CASE
            WHEN x.j43_uf IS NULL THEN x.z01_uf
            ELSE x.j43_uf
        END AS j43_uf,
    x.j43_dest,
        CASE
            WHEN x.j43_numimo IS NULL THEN x.z01_numero
            ELSE x.j43_numimo
        END AS j43_numimo,
        CASE
            WHEN x.j43_cxpost IS NULL THEN x.z01_cxpostal::text
            ELSE to_char(x.j43_cxpost, '99999999999999999999'::text)
        END AS j43_cxpost,
        CASE
            WHEN x.j43_comple IS NULL THEN x.z01_compl
            ELSE x.j43_comple
        END AS j43_comple,
    x.j01_tipoimp::character varying(20) AS j01_tipoimp,
    x.j01_codave,
    x.j37_zona,
    x.z01_cgmpri,
    x.j39_pavim,
    x.j05_codigoproprio,
    x.j06_setorloc,
    x.j06_quadraloc,
    x.j06_lote,
    x.j05_descr,
    x.pql_localizacao,
    cgmpropri.z01_cgccpf AS z01_cgccpfpropri,
    cgmpropri.z01_nomecomple AS z01_nomecomplepri,
    cgmpropri.z01_ender AS z01_enderpri,
    cgmpropri.z01_munic AS z01_municpri,
    cgmpropri.z01_bairro AS z01_bairropri,
    cgmpropri.z01_cep AS z01_ceppri,
    cgmpropri.z01_uf AS z01_ufpri,
    cgmpropri.z01_numero AS z01_numeropri,
    cgmpropri.z01_compl AS z01_complpri,
    x.j40_registrocartografico
   FROM ( SELECT iptubase.j01_matric,
                 iptubase.j01_datacad,
            cgm.z01_numcgm,
          CASE
                    WHEN promite.z01_numcgm IS NULL THEN cgm.z01_cgccpf
                    ELSE cgmpromite.z01_cgccpf
                END AS z01_cgccpf,
          CASE
                    WHEN promite.z01_nome IS NULL THEN cgm.z01_nome::text
                    ELSE (( SELECT COALESCE(btrim(cfiptu.j18_textoprom::text) || ' '::text, ''::text) AS "coalesce"
                       FROM cfiptu
                      ORDER BY cfiptu.j18_anousu DESC
                     LIMIT 1)) || substr(promite.z01_nome, 1, 50)::text
                END AS z01_nome,
          CASE
                    WHEN promite.z01_nome IS NULL THEN cgm.z01_nome::text
                    ELSE (( SELECT COALESCE(btrim(cfiptu.j18_textoprom::text) || ' '::text, ''::text) AS "coalesce"
                       FROM cfiptu
                      ORDER BY cfiptu.j18_anousu DESC
                     LIMIT 1)) || promite.z01_nome::text
                END AS z01_nomecompleto,
                CASE
                    WHEN promite.z01_numcgm IS NULL THEN cgm.z01_numcgm
                    ELSE promite.z01_numcgm
                END AS z01_cgmpri,
            cgm.z01_nome AS proprietario,
                CASE
                    WHEN iptuender.j43_ender IS NULL THEN
                    CASE
                        WHEN promitente.j41_numcgm IS NULL THEN cgm.z01_ender
                        ELSE promite.z01_ender
                    END
                    ELSE iptuender.j43_ender
                END AS z01_ender,
                CASE
                    WHEN iptuender.j43_ender IS NULL THEN
                    CASE
                        WHEN promitente.j41_numcgm IS NULL THEN cgm.z01_munic
                        ELSE promite.z01_munic
                    END
                    ELSE iptuender.j43_munic
                END AS z01_munic,
                CASE
                    WHEN iptuender.j43_ender IS NULL THEN
                    CASE
                        WHEN promitente.j41_numcgm IS NULL THEN cgm.z01_bairro
                        ELSE promite.z01_bairro
                    END
                    ELSE iptuender.j43_bairro
                END AS z01_bairro,
                CASE
                    WHEN iptuender.j43_ender IS NULL THEN
                    CASE
                        WHEN promitente.j41_numcgm IS NULL THEN cgm.z01_cep
                        ELSE promite.z01_cep
                    END::bpchar
                    ELSE iptuender.j43_cep
                END AS z01_cep,
                CASE
                    WHEN iptuender.j43_ender IS NULL THEN
                    CASE
                        WHEN promitente.j41_numcgm IS NULL THEN cgm.z01_uf
                        ELSE promite.z01_uf
                    END::bpchar
                    ELSE iptuender.j43_uf
                END AS z01_uf,
                CASE
                    WHEN iptuender.j43_ender IS NULL THEN
                    CASE
                        WHEN promitente.j41_numcgm IS NULL THEN cgm.z01_numero
                        ELSE promite.z01_numero
                    END
                    ELSE iptuender.j43_numimo
                END AS z01_numero,
                CASE
                    WHEN iptuender.j43_ender IS NULL THEN
                    CASE
                        WHEN promitente.j41_numcgm IS NULL THEN cgm.z01_compl
                        ELSE promite.z01_compl
                    END::bpchar
                    ELSE iptuender.j43_comple
                END AS z01_compl,
                CASE
                    WHEN iptuender.j43_cxpost IS NULL THEN
                    CASE
                        WHEN promitente.j41_numcgm IS NULL THEN cgm.z01_cxpostal
                        ELSE promite.z01_cxpostal
                    END
                    ELSE iptuender.j43_cxpost::character varying(20)
                END AS z01_cxpostal,
                CASE
                    WHEN rr.j14_codigo IS NULL THEN r.j14_codigo
                    ELSE rr.j14_codigo
                END AS codpri,
                CASE
                    WHEN rr.j14_nome IS NULL THEN r.j14_nome
                    ELSE rr.j14_nome
                END AS nomepri,
                CASE
                    WHEN rr.j14_tipo IS NULL THEN rt.j88_sigla
                    ELSE rrt.j88_sigla
                END AS tipopri,
                CASE
                    WHEN length(btrim(testadanumero.j15_numero::character varying::text)) > 0 AND iptuconstr.j39_matric IS NULL THEN testadanumero.j15_numero
                    ELSE iptuconstr.j39_numero
                END AS j39_numero,
                CASE
                    WHEN length(btrim(testadanumero.j15_compl::text)) > 0 AND iptuconstr.j39_matric IS NULL THEN testadanumero.j15_compl
                    ELSE iptuconstr.j39_compl
                END AS j39_compl,
            lote.j34_setor,
            lote.j34_quadra,
            lote.j34_lote,
            lote.j34_zona,
            lote.j34_bairro,
            iptuant.j40_refant,
            iptubase.j01_idbql,
       r.j14_codigo,
       r.j14_nome,
            rt.j88_sigla::text AS j14_tipo,
            bairro.j13_codi,
            bairro.j13_descr,
            iptubase.j01_baixa,
            lote.j34_area,
            lote.j34_areal,
            imobil.j44_numcgm,
            promitente.j41_numcgm,
            iptuender.j43_matric,
            iptuender.j43_dest,
            iptuender.j43_ender,
            iptuender.j43_numimo,
            iptuender.j43_comple,
            iptuender.j43_bairro,
            iptuender.j43_munic,
            iptuender.j43_uf,
            iptuender.j43_cep,
            iptuender.j43_cxpost,
            iptuant.j40_registrocartografico,
       CASE
                    WHEN iptuconstr.j39_idcons IS NULL THEN 'Territorial'::text
                    ELSE 'Predial'::text
                END AS j01_tipoimp,
            iptubase.j01_codave,
            face.j37_zona,
            iptuconstr.j39_pavim,
            ( SELECT count(propri.j42_matric) AS count
                   FROM propri
                  WHERE propri.j42_matric = iptubase.j01_matric) AS totpropri,
            ( SELECT count(promitente_1.j41_matric) AS count
                   FROM promitente promitente_1
                  WHERE promitente_1.j41_matric = iptubase.j01_matric) AS totpromi,
            setorloc.j05_codigoproprio,
            loteloc.j06_setorloc,
            loteloc.j06_quadraloc,
            loteloc.j06_lote,
            setorloc.j05_descr,
            (((((setorloc.j05_codigoproprio::text || '-'::text) || setorloc.j05_descr::text) || '/'::text) || loteloc.j06_quadraloc::text) || '/'::text) || loteloc.j06_lote::text AS pql_localizacao
           FROM iptubase
             JOIN cgm ON cgm.z01_numcgm = iptubase.j01_numcgm
             LEFT JOIN iptuconstr ON iptuconstr.j39_matric = iptubase.j01_matric AND iptuconstr.j39_idprinc IS TRUE AND iptuconstr.j39_dtdemo IS NULL
             LEFT JOIN ruas rr ON rr.j14_codigo = iptuconstr.j39_codigo
             LEFT JOIN ruastipo rrt ON rrt.j88_codigo = rr.j14_tipo
             LEFT JOIN iptuant ON iptuant.j40_matric = iptubase.j01_matric
             JOIN lote ON iptubase.j01_idbql = lote.j34_idbql
             LEFT JOIN testpri ON testpri.j49_idbql = lote.j34_idbql
             LEFT JOIN testadanumero ON testadanumero.j15_idbql = testpri.j49_idbql AND testadanumero.j15_face = testpri.j49_face
             LEFT JOIN face ON face.j37_face = testpri.j49_face
             LEFT JOIN ruas r ON r.j14_codigo = testpri.j49_codigo
             LEFT JOIN ruastipo rt ON rt.j88_codigo = r.j14_tipo
             LEFT JOIN imobil ON iptubase.j01_matric = imobil.j44_matric
             LEFT JOIN promitente ON iptubase.j01_matric = promitente.j41_matric AND promitente.j41_tipopro IS TRUE
             LEFT JOIN cgm cgmpromite ON promitente.j41_numcgm = cgmpromite.z01_numcgm
             LEFT JOIN cgm promite ON promite.z01_numcgm = promitente.j41_numcgm
             LEFT JOIN bairro ON bairro.j13_codi = lote.j34_bairro
             LEFT JOIN iptuender ON iptubase.j01_matric = iptuender.j43_matric
             LEFT JOIN loteloc ON loteloc.j06_idbql = iptubase.j01_idbql
             LEFT JOIN setorloc ON setorloc.j05_codigo = loteloc.j06_setorloc
             LEFT JOIN setor ON setor.j30_codi = lote.j34_setor) x
     LEFT JOIN cgm cgmpropri ON x.z01_numcgm = cgmpropri.z01_numcgm;

create or replace view averbacoes as
SELECT protprocesso.p58_codproc,
       protprocesso.p58_dtproc,
       averbacao.j75_codigo,
       averbacao.j75_matric,
       averbacao.j75_data,
       averbacao.j75_obs,
       averbacao.j75_tipo,
       averbacao.j75_dttipo,
       averbacao.j75_situacao,
       averbacao.j75_regra,
       proprietario.j40_refant,
       setorloc.j05_codigoproprio,
       loteloc.j06_setorloc,
       loteloc.j06_quadraloc,
       loteloc.j06_lote,
       loteam.j34_descr,
       proprietario.codpri,
       proprietario.nomepri,
       proprietario.z01_cgccpf,
       proprietario.tipopri,
       proprietario.j39_numero,
       proprietario.j39_compl,
       proprietario.j34_area,
       proprietario.j34_setor,
       proprietario.j34_quadra,
       proprietario.j34_lote,
       proprietario.j01_baixa,
       iptuconstr.j39_area,
--       averbacgm.j76_numcgm,

       (select array_to_string(array_accum(z01_numcgm||' - '||z01_nome),',')
          from cgm
               inner join averbacgm on cgm.z01_numcgm = averbacgm.j76_numcgm
         where averbacao.j75_codigo = averbacgm.j76_averbacao ) as z01_nomeadq,

       (select array_to_string(array_accum(z01_numcgm||' - '||z01_nome),',')
          from cgm
               inner join averbacgmold on cgm.z01_numcgm = averbacgmold.j79_numcgm
         where averbacao.j75_codigo = averbacgmold.j79_averbacao ) as z01_nometrans,

--       cgmadq.z01_nome AS z01_nomeadq,
--       averbacgmold.j79_numcgm,
--       cgmtrans.z01_nome AS z01_nometrans,

       db_usuarios."login",
       db_usuarios.nome,
       rhpessoal.rh01_regist,
       rhfuncao.rh37_descr
   FROM averbacao

--   JOIN averbacgm           ON averbacao.j75_codigo = averbacgm.j76_averbacao
--   JOIN cgm cgmadq          ON cgmadq.z01_numcgm = averbacgm.j76_numcgm

   LEFT JOIN averbaprocesso ON averbacao.j75_codigo = averbaprocesso.j77_averbacao
   LEFT JOIN protprocesso   ON protprocesso.p58_codproc = averbaprocesso.j77_codproc

--   LEFT JOIN averbacgmold   ON averbacao.j75_codigo = averbacgmold.j79_averbacao
--   LEFT JOIN cgm cgmtrans   ON cgmtrans.z01_numcgm = averbacgmold.j79_numcgm

   JOIN iptubase            ON averbacao.j75_matric = iptubase.j01_matric
   LEFT JOIN loteloc        ON iptubase.j01_idbql = loteloc.j06_idbql
   LEFT JOIN setorloc       ON loteloc.j06_setorloc = setorloc.j05_codigo
   JOIN proprietario        ON iptubase.j01_matric = proprietario.j01_matric
   LEFT JOIN iptuconstr     ON iptubase.j01_matric = iptuconstr.j39_matric
   LEFT JOIN loteloteam     ON proprietario.j01_idbql = loteloteam.j34_idbql
   LEFT JOIN loteam         ON loteloteam.j34_loteam = loteam.j34_loteam
   LEFT JOIN db_usuarios    ON db_usuarios.id_usuario = fc_getsession('db_id_usuario'::text)::integer
   LEFT JOIN db_usuacgm     ON db_usuacgm.id_usuario = db_usuarios.id_usuario
   LEFT JOIN rhpessoal      ON rhpessoal.rh01_numcgm = db_usuacgm.cgmlogin
   LEFT JOIN rhpessoalmov   ON rhpessoalmov.rh02_regist = rhpessoal.rh01_regist
                           AND rhpessoalmov.rh02_anousu = fc_anofolha(fc_getsession('db_instit'::text)::integer)
                           AND rhpessoalmov.rh02_mesusu = fc_mesfolha(fc_getsession('db_instit'::text)::integer)
   LEFT JOIN rhpesrescisao  ON rhpessoalmov.rh02_seqpes = rhpesrescisao.rh05_seqpes
   LEFT JOIN rhfuncao       ON rhpessoalmov.rh02_funcao = rhfuncao.rh37_funcao
                           AND rhpessoalmov.rh02_instit = rhfuncao.rh37_instit
  WHERE     db_usuarios.usuarioativo::text = 1::text
        AND db_usuarios.usuext = 0
        AND rhpessoalmov.rh02_instit::text = fc_getsession('db_instit'::text)
        AND rhpesrescisao.rh05_recis is null;

create or replace view venal as
SELECT COALESCE((SELECT sum(iptucale.j22_valor) AS sum FROM cadastro.iptucale WHERE ((iptucale.j22_anousu = iptucalc.j23_anousu) AND (iptucale.j22_matric = iptucalc.j23_matric))), (0)::double precision) AS j22_valor, ('R$ '::text || translate(to_char((iptucalc.j23_vlrter + COALESCE((SELECT sum(iptucale.j22_valor) AS sum FROM cadastro.iptucale WHERE ((iptucale.j22_anousu = iptucalc.j23_anousu) AND (iptucale.j22_matric = iptucalc.j23_matric))), (0)::double precision)), '999,999,999,990.99'::text), ',.'::text, '.,'::text)) AS j23_venaltotal,
  proprietario.j40_refant,
  loteloc.j06_setorloc,
  loteloc.j06_quadraloc,
  loteloc.j06_lote,
  loteam.j34_descr,
  proprietario.j01_matric,
  proprietario.codpri,
  proprietario.nomepri,
  proprietario.z01_cgccpf,
  proprietario.tipopri,
  proprietario.j39_numero,
  proprietario.j39_compl,
  proprietario.j34_area,
  iptucalc.j23_anousu,
  iptucalc.j23_matric,
  iptucalc.j23_testad,
  iptucalc.j23_arealo,
  iptucalc.j23_areafr,
  iptucalc.j23_areaed,
  iptucalc.j23_m2terr,
  iptucalc.j23_vlrter,
  iptucalc.j23_aliq,
  iptucalc.j23_vlrisen,
  iptucalc.j23_tipoim,
  iptucalc.j23_manual,
  iptucalc.j23_tipocalculo,
  to_date(fc_getsession('db_datausu'::text), 'YYYY-MM-DD'::text) AS data_sessao,
  fc_dataextenso(to_date(fc_getsession('db_datausu'::text), 'YYYY-MM-DD'::text)) AS data_sessao_extenso,
  db_usuarios."login", db_usuarios.nome,
    rh01_regist,
    rh37_descr
FROM ((((cadastro.iptucalc JOIN proprietario ON ((iptucalc.j23_matric = proprietario.j01_matric)))
LEFT JOIN cadastro.loteloc ON ((proprietario.j01_idbql = loteloc.j06_idbql)))
LEFT JOIN cadastro.loteloteam ON ((proprietario.j01_idbql = loteloteam.j34_idbql)))
LEFT JOIN cadastro.loteam ON ((loteloteam.j34_loteam = loteam.j34_loteam)))

LEFT JOIN configuracoes.db_usuarios ON ((db_usuarios.id_usuario = (fc_getsession('db_id_usuario'::text))::integer))
left join configuracoes.db_usuacgm on db_usuacgm.id_usuario = db_usuarios.id_usuario
left join pessoal.rhpessoal     on rh01_numcgm = cgmlogin
left join pessoal.rhpessoalmov  on rh02_regist = rh01_regist
                                   and rh02_anousu = fc_anofolha(fc_getsession('db_instit')::integer)
                                   and rh02_mesusu = fc_mesfolha(fc_getsession('db_instit')::integer)
left join pessoal.rhpesrescisao on rh02_seqpes = rh05_seqpes
left join pessoal.rhfuncao      on rh02_funcao = rh37_funcao
                                   and rh02_instit = rh37_instit

WHERE rh05_seqpes is null
  and rh02_instit = cast(fc_getsession('db_instit') as integer)
  and db_usuarios.usuarioativo = 1
  and db_usuarios.usuext = 0
  and (iptucalc.j23_anousu = (fc_getsession('db_anousu'::text))::integer);
 
SQL
        );
    }
    
    private function downViewBuscaEnvolvidos(){
      DB::connection()->getPdo()->exec(<<<SQL
        create or replace function fc_busca_envolvidos(boolean,integer,char(1),integer) returns setof tp_socio_promitente  as
$$
declare

lPrincipal		  alias for  $1; -- Traz apenas o proprietario principal
iRegra			    alias for  $2; -- Traz a regra configurada na db_config, pardiv ou parjuridico
iTipoOrigem	    alias for  $3; -- Verifica se é "M" Matrícula, "I" Inscrição ou "C" Cgm
iCodOrigem	    alias for  $4; -- Traz o código da Matrícula ou Inscrição

iNumcgm         integer;
iMatricula      integer;
iInscricao      integer;

sNome           varchar(40);

lraise          boolean default false;

sSql            text	  default '';

rSocios         record;
rPromitente     record;
rProprietarios  record;

rtp_promitente  tp_socio_promitente%ROWTYPE;

begin

 if iTipoOrigem = 'I' then

   -- Traz CGM do Issbase

   select z01_numcgm, z01_nome, q02_inscr
     into iNumcgm, sNome, iInscricao
     from issbase
	        inner join cgm  on z01_numcgm = q02_numcgm
	  where q02_inscr = iCodOrigem;

	rtp_promitente.riNumcgm	   := iNumcgm;
	rtp_promitente.rvNome	     := sNome;
	rtp_promitente.riInscr	   := iInscricao;
	rtp_promitente.riTipoEnvol := 4;
	return  next rtp_promitente;

	-- Traz CGM dos Socios
	if iRegra = 1 and lPrincipal = false then

		sSql := 'select z01_nome, z01_numcgm, q02_inscr
			         from issbase
						        inner join socios on q95_cgmpri = q02_numcgm
						        inner join cgm    on z01_numcgm = q95_numcgm
				      where q95_tipo  = 1
                and q02_inscr = '||iCodOrigem;

		for rSocios in execute sSql loop

			rtp_promitente.riNumcgm	   := rSocios.z01_numcgm;
			rtp_promitente.rvNome	     := rSocios.z01_nome;
			rtp_promitente.riInscr	   := rSocios.q02_inscr;
			rtp_promitente.riTipoEnvol := 5;
			return  next rtp_promitente;

		end loop;

	end if;

elsif iTipoOrigem = 'M' then

  if lraise then
	  raise notice 'Regra IPTU: % ',iRegra;
  end if;

  -- Traz CGM do Proprietário e Promitente
  if iRegra = 0 then

    SELECT cgm.z01_numcgm, cgm.z01_nome, j01_matric
        INTO iNumcgm, sNome, iMatricula
    FROM (
             SELECT CASE
                        WHEN promitente.j41_numcgm IS NULL
                            THEN iptubase.j01_numcgm
                        ELSE promitente.j41_numcgm
                        END AS k00_numcgm,
                    CASE
                        WHEN promitente.j41_matric IS NULL
                            THEN iptubase.j01_matric
                        ELSE promitente.j41_matric
                        END AS j01_matric
             FROM iptubase
                      LEFT JOIN promitente ON promitente.j41_matric = iptubase.j01_matric AND j41_tipopro IS true
             WHERE iptubase.j01_matric = iCodOrigem) AS X
     INNER JOIN cgm ON cgm.z01_numcgm = x.k00_numcgm;

	  rtp_promitente.riNumcgm	   := iNumcgm;
	  rtp_promitente.rvNome		   := sNome;
	  rtp_promitente.riMatric	   := iMatricula;
	  rtp_promitente.riTipoEnvol := 1;
	  rtp_promitente.riInscr	   := null;
	  return next rtp_promitente;

      -- Se lPrincipal for true mesmo sendo regra 2 retorna apenas o Proprietário
    if lPrincipal = false then

		 sSql := ' select z01_numcgm, z01_nome, j41_matric
				         from promitente
					            inner join cgm on z01_numcgm = j41_numcgm
					      where j41_tipopro is false AND j41_matric = '||iCodOrigem||'
             order by j41_tipopro desc';

		 for rPromitente in execute sSql loop

			 rtp_promitente.riNumcgm	  := rPromitente.z01_numcgm;
			 rtp_promitente.rvNome		  := rPromitente.z01_nome;
			 rtp_promitente.riMatric	  := rPromitente.j41_matric;
			 rtp_promitente.riTipoEnvol := 3;
			 return  next rtp_promitente;
		 end loop;

		 sSql := 'select z01_numcgm, z01_nome, j42_matric
				        from propri
				             inner join cgm on z01_numcgm = j42_numcgm
				       where j42_matric = '||iCodOrigem;

		 for rProprietarios in execute sSql loop

		   rtp_promitente.riNumcgm	  := rProprietarios.z01_numcgm;
			 rtp_promitente.rvNome		  := rProprietarios.z01_nome;
			 rtp_promitente.riMatric	  := rProprietarios.j42_matric;
			 rtp_promitente.riTipoEnvol := 2;
		   return  next rtp_promitente;
		 end loop;

	  end if;

   -- Traz CGM do Proprietário
  elsif iRegra = 1 then

	  select z01_numcgm, z01_nome, j01_matric
		  into iNumcgm, sNome, iMatricula
		  from cgm
	         inner join iptubase on iptubase.j01_numcgm = cgm.z01_numcgm
	   where j01_matric = iCodOrigem;

	   rtp_promitente.riNumcgm	  := iNumcgm;
	   rtp_promitente.rvNome	    := sNome;
	   rtp_promitente.riMatric	  := iMatricula;
	   rtp_promitente.riTipoEnvol := 1;
	   rtp_promitente.riInscr	    := null;
	   return  next rtp_promitente;

       -- Se lPrincipal for true retorna outros proprietário
       if lPrincipal = false then

		  sSql := ' select z01_numcgm, z01_nome, j42_matric
					        from propri
					             inner join cgm on z01_numcgm = j42_numcgm
					       where j42_matric = '|| iCodOrigem;

		  for rProprietarios in execute sSql loop

			  rtp_promitente.riNumcgm		 := rProprietarios.z01_numcgm;
			  rtp_promitente.rvNome			 := rProprietarios.z01_nome;
			  rtp_promitente.riMatric		 := rProprietarios.j42_matric;
			  rtp_promitente.riTipoEnvol := 2;
			  return  next rtp_promitente;
		  end loop;

	   end if;

	-- Traz CGM do  Promitente
  elsif iRegra = 2 then

	   sSql := 'select z01_numcgm, z01_nome, j41_matric
				        from promitente
				             inner join cgm on z01_numcgm = j41_numcgm
				       where j41_matric = '||iCodOrigem||' order by j41_tipopro desc ';

				 for rPromitente in execute sSql loop

						rtp_promitente.riNumcgm	   := rPromitente.z01_numcgm;
						rtp_promitente.rvNome	     := rPromitente.z01_nome;
						rtp_promitente.riMatric	   := rPromitente.j41_matric;
						rtp_promitente.riTipoEnvol := 3;
						return  next rtp_promitente;
				 end loop;

       -- Se nao encontrou forca regra = 1
       if not found then
          for rPromitente in select * from fc_busca_envolvidos(lPrincipal, 1, 'M', iCodOrigem) loop

         	  rtp_promitente.riNumcgm		 := rPromitente.riNumcgm;
	  		    rtp_promitente.rvNome			 := rPromitente.rvNome;
			      rtp_promitente.riMatric		 := rPromitente.riMatric;
			      rtp_promitente.riTipoEnvol := 1;
			      return  next rtp_promitente;
          end loop;
       end if;

	end if;

end if;

if iTipoOrigem = 'C' then

   select z01_numcgm, z01_nome
	   into iNumcgm, sNome
	   from cgm
	  where z01_numcgm = iCodOrigem;

	rtp_promitente.riNumcgm		 := iNumcgm;
	rtp_promitente.rvNome		   := sNome;
	rtp_promitente.riMatric		 := null;
	rtp_promitente.riTipoEnvol := 1;
	rtp_promitente.riInscr		 := null;
	return  next rtp_promitente;

end if;

 return;

end;

$$ language 'plpgsql';


SQL
      );
    }

}
