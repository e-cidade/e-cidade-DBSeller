<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M28187CreateTableInformacoesComplementares extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upFuncaoDicionario();
        $this->upItensMenu();
        $this->upTable();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downItensMenu();
        $this->downDicionario();
    }

    private function upFuncaoDicionario()
    {
        $sql = <<<SQL
/*
 * fc_hash_int(text, integer)
 *
 *   . responsavel por gerar um hash em INT4 apartir de um text
 *   . se usar 32, o retorno deve ser bigint, senao ira retornar
 *   . numeros negativos, devera ser alterado a funcao
 *
 * Parametros:
 *  $1 - texto a ser processado.
 *  $2 - quantidade de bits para cálculo (default: 32)
 *
 * Retorno:
 *  INT4 - hash gerado apartir do algoritmo
 *
 * Referencias:
 *   https://stackoverflow.com/questions/9809381/hashing-a-string-to-a-numeric-value-in-postgresql
 *   https://stackoverflow.com/questions/8316164/convert-hex-in-text-representation-to-decimal-number/8316731#8316731
 *
 * para executar com debug: set client_min_messages = debug;
 *
 */
CREATE OR REPLACE FUNCTION fc_hash_int(text, integer DEFAULT 32) RETURNS integer AS
$$
DECLARE
	hash_int INTEGER;
BEGIN
	EXECUTE format(E'SELECT (\'x\'||substr(md5(%L),1,8))::bit(%s)::int;', $1, $2)
	INTO 	hash_int;

	RETURN hash_int;
END;
$$
LANGUAGE plpgsql;

/*
 *
 * fc_dicionario_salt()
 *
 *   . retorna o 'salt' para ser usado na geração do hash dos IDs do dicionário
 *
 */
CREATE OR REPLACE FUNCTION fc_dicionario_salt()
RETURNS INTEGER AS
$$
	SELECT 50000000;
$$
LANGUAGE sql IMMUTABLE;

/*
 *
 * fc_remove_dicionario_tabela(text, text)
 *
 *   . responsavel por limpar dicionario de dados (tabelas db_sys*) apartir de um DROP TABLE
 *
 * Parametros:
 *  $1 - nome do esquema para buscar do PostgreSQL.
 *  $2 - nome da tabela para buscar do PostgreSQL.
 *
 *  Exemplos:
 *     SELECT fc_remove_dicionario_tabela('caixa', 'arrecad'); -- Tabela caixa.arrecad
 *
 */
CREATE OR REPLACE FUNCTION fc_remove_dicionario_tabela(text, text)
RETURNS void AS
$$
DECLARE
	sysarquivo 		INTEGER;
	sysmodulo 		INTEGER;
	syscampos 		INTEGER[];
	relid			REGCLASS;
BEGIN
	-- Salva o "relation id" do catálogo do PostgreSQL
	relid := format('%I.%I', $1, $2)::regclass;

	SELECT 	a.codarq
	INTO 	sysarquivo
	FROM	configuracoes.db_sysarquivo a
			JOIN configuracoes.db_sysarqmod am ON am.codarq = a.codarq
			JOIN configuracoes.db_sysmodulo m  ON am.codmod = m.codmod
	WHERE 	regexp_replace(lower(to_ascii(nomemod)), '[^A-Za-z]' , '', 'g') = $1
	AND 	a.nomearq = $2;

	IF sysarquivo IS NULL THEN
		RAISE INFO 'Tabela %.% nao encontrada no dicionario de dados!', $1, $2;
	END IF;

	RAISE DEBUG 'sysarquivo: %', sysarquivo;

	SELECT 	codmod
	INTO 	sysmodulo
	FROM 	configuracoes.db_sysmodulo
	WHERE 	regexp_replace(lower(to_ascii(nomemod)), '[^A-Za-z]' , '', 'g') = $1;

	SELECT 	array_agg(codcam)
	INTO 	syscampos
	FROM 	configuracoes.db_sysarqcamp
	WHERE 	codarq = sysarquivo;

	-- 0) Cria tabelas temporárias para salvar conteúdo anterior
    RAISE DEBUG 'Dropando tabelas temporarias';

    DROP TABLE IF EXISTS tmp_db_processa;
	DROP TABLE IF EXISTS tmp_sysarquivo;
	DROP TABLE IF EXISTS tmp_syscampo;
	DROP TABLE IF EXISTS tmp_syscampodef;
	DROP TABLE IF EXISTS tmp_syscampodep;
    DROP TABLE IF EXISTS tmp_db_sysforkey;
	DROP TABLE IF EXISTS tmp_db_acount;
	DROP TABLE IF EXISTS tmp_db_sysclasses;
    DROP TABLE IF EXISTS tmp_iptutabelasdepend;
    DROP TABLE IF EXISTS tmp_iptutabelasconfigcampochave;
    DROP TABLE IF EXISTS tmp_iptutabelasconfigcampocorrecao;
    DROP TABLE IF EXISTS tmp_iptutabelasconfigvirada;
    DROP TABLE IF EXISTS tmp_iptutabelasconfig;
    DROP TABLE IF EXISTS tmp_iptutabelas;

    RAISE DEBUG 'Criando tabelas temporarias';

    CREATE TEMP TABLE tmp_db_processa AS
        SELECT *
        FROM db_processa WHERE codarq = sysarquivo;

    CREATE TEMP TABLE tmp_sysarquivo AS
		SELECT *, pg_catalog.obj_description(relid, 'pg_class') AS comentario
		FROM db_sysarquivo WHERE codarq = sysarquivo;

	CREATE TEMP TABLE tmp_syscampo AS
		SELECT *, pg_catalog.col_description(relid, (SELECT attnum FROM pg_attribute WHERE attrelid = relid AND attname = nomecam)) AS comentario
		FROM db_syscampo WHERE codcam = ANY(syscampos)
		 AND NOT EXISTS (SELECT 1 FROM configuracoes.db_sysarqcamp ac WHERE ac.codcam = db_syscampo.codcam AND ac.codarq IS DISTINCT FROM sysarquivo);

	CREATE TEMP TABLE tmp_syscampodef AS
		SELECT db_syscampodef.*
		FROM db_syscampodef INNER JOIN db_syscampo ON db_syscampo.codcam = db_syscampodef.codcam
		WHERE db_syscampodef.codcam = ANY(syscampos);

	CREATE TEMP TABLE tmp_syscampodep AS
		SELECT db_syscampodep.*
		FROM db_syscampodep INNER JOIN db_syscampo ON db_syscampo.codcam = db_syscampodep.codcam
		WHERE db_syscampodep.codcam = ANY(syscampos);

    CREATE TEMP TABLE tmp_db_sysforkey AS
    SELECT db_sysforkey.*
    FROM db_sysforkey INNER JOIN db_syscampo ON db_syscampo.codcam = db_sysforkey.codcam
    WHERE db_sysforkey.codcam = ANY(syscampos) AND db_sysforkey.codarq <> sysarquivo;

    CREATE TEMP TABLE tmp_db_acount AS
            SELECT db_acount.*
                FROM db_acount
                WHERE codarq = sysarquivo;

    CREATE TEMP TABLE tmp_db_sysclasses AS
            SELECT db_sysclasses.*
                FROM db_sysclasses
                WHERE codarq = sysarquivo;

    CREATE TEMP TABLE tmp_iptutabelasdepend AS
    SELECT iptutabelasdepend.*
    FROM iptutabelasdepend
    WHERE j128_iptutabelas in (SELECT j121_sequencial FROM iptutabelas WHERE j121_codarq = sysarquivo);

    CREATE TEMP TABLE tmp_iptutabelasconfigcampochave AS
    SELECT iptutabelasconfigcampochave.*
    FROM iptutabelasconfigcampochave
    WHERE j124_iptutabelasconfig in (SELECT j122_sequencial
                                     FROM iptutabelas
                                          JOIN iptutabelasconfig ON j122_iptutabelas = j121_sequencial
                                     WHERE j121_codarq = sysarquivo);

    CREATE TEMP TABLE tmp_iptutabelasconfigcampocorrecao AS
    SELECT iptutabelasconfigcampocorrecao.*
    FROM iptutabelasconfigcampocorrecao
    WHERE j123_iptutabelasconfig in (SELECT j122_sequencial
                                     FROM iptutabelas
                                              JOIN iptutabelasconfig ON j122_iptutabelas = j121_sequencial
                                     WHERE j121_codarq = sysarquivo);

    CREATE TEMP TABLE tmp_iptutabelasconfigvirada AS
    SELECT iptutabelasconfigvirada.*
    FROM iptutabelasconfigvirada
    WHERE j129_iptutabelasconfig in (SELECT j122_sequencial
                                     FROM iptutabelas
                                              JOIN iptutabelasconfig ON j122_iptutabelas = j121_sequencial
                                     WHERE j121_codarq = sysarquivo);

    CREATE TEMP TABLE tmp_iptutabelasconfig AS
    SELECT iptutabelasconfig.*
    FROM iptutabelasconfig
    WHERE j122_iptutabelas in (SELECT j121_sequencial FROM iptutabelas WHERE j121_codarq = sysarquivo);

    CREATE TEMP TABLE tmp_iptutabelas AS
    SELECT iptutabelas.*
    FROM iptutabelas
    WHERE j121_codarq = sysarquivo;

    -- 1) Remove tudo
    RAISE DEBUG 'Deletando registros das tabelas auxiliares';

    DELETE FROM iptutabelasconfigcampochave
        WHERE j124_iptutabelasconfig in (SELECT j122_sequencial
                                         FROM iptutabelas
                                              JOIN iptutabelasconfig ON j122_iptutabelas = j121_sequencial
                                         WHERE j121_codarq = sysarquivo);
    DELETE FROM iptutabelasconfigcampocorrecao
    WHERE j123_iptutabelasconfig in (SELECT j122_sequencial
                                     FROM iptutabelas
                                              JOIN iptutabelasconfig ON j122_iptutabelas = j121_sequencial
                                     WHERE j121_codarq = sysarquivo);
    DELETE FROM iptutabelasconfigvirada
    WHERE j129_iptutabelasconfig in (SELECT j122_sequencial
                                     FROM iptutabelas
                                              JOIN iptutabelasconfig ON j122_iptutabelas = j121_sequencial
                                     WHERE j121_codarq = sysarquivo);
    DELETE FROM cadastro.iptutabelasconfig WHERE j122_iptutabelas in
        (SELECT j121_sequencial FROM iptutabelas WHERE j121_codarq = sysarquivo);
    DELETE FROM cadastro.iptutabelasdepend WHERE j128_iptutabelas in
        (SELECT j121_sequencial FROM iptutabelas WHERE j121_codarq = sysarquivo);
    DELETE FROM cadastro.iptutabelasdepend WHERE j128_iptutabelasdepend in
        (SELECT j121_sequencial FROM iptutabelas WHERE j121_codarq = sysarquivo);
	DELETE FROM cadastro.iptutabelas WHERE j121_codarq = sysarquivo;
	DELETE FROM configuracoes.db_sysclasses WHERE codarq = sysarquivo;
	DELETE FROM configuracoes.db_acount WHERE codarq = sysarquivo;
	DELETE FROM configuracoes.db_sysarqmod WHERE codarq = sysarquivo AND codmod = sysmodulo;
	DELETE FROM configuracoes.db_sysforkey WHERE codcam = ANY(syscampos);
	DELETE FROM configuracoes.db_sysprikey WHERE codarq = sysarquivo;
	DELETE FROM configuracoes.db_sysarqcamp WHERE codarq = sysarquivo AND codcam = ANY(syscampos);
	DELETE FROM configuracoes.db_syscampodef WHERE codcam = ANY(syscampos);
	DELETE FROM configuracoes.db_syscampodep WHERE codcam = ANY(syscampos);
	DELETE FROM configuracoes.db_processa WHERE codarq = sysarquivo;
	DELETE FROM configuracoes.db_syscampo c WHERE c.codcam = ANY(syscampos)
	    AND NOT EXISTS (SELECT 1 FROM configuracoes.db_sysarqcamp ac
	                             WHERE ac.codcam = c.codcam AND ac.codarq IS DISTINCT FROM sysarquivo);

	DELETE FROM configuracoes.db_sysarquivo WHERE codarq = sysarquivo;

    RETURN;
END;
$$
LANGUAGE plpgsql;

/*
 *
 * fc_gera_dicionario_apartir_tabela(text, text)
 *
 *   . responsavel por gerar dicionario de dados (tabelas db_sys*) apartir de uma tabela existente no PostgreSQL
 *
 * Parametros:
 *  $1 - nome do esquema para buscar do PostgreSQL.
 *  $2 - nome da tabela para buscar do PostgreSQL.
 *
 *  Exemplos:
 *     SELECT fc_gera_dicionario_apartir_tabela('caixa', 'arrecad'); -- Tabela caixa.arrecad
 *
 */
CREATE OR REPLACE FUNCTION fc_gera_dicionario_apartir_tabela(text, text)
RETURNS void AS
$$
DECLARE
	sysarquivo 		INTEGER;
	sysarquivofk	INTEGER;
	syscampo 		INTEGER;
	sysmodulo 		INTEGER;
	syssequencia 	INTEGER;
	r 				RECORD;
	relid			REGCLASS;

    tDescricao       TEXT;
    sRotulo          VARCHAR;
    bMaiusculo       BOOLEAN;
    bAutocompl       BOOLEAN;
    iAceitatipo      INTEGER;
    sTipoobj         VARCHAR;
    sRotulorel       VARCHAR;
BEGIN
	SELECT 	codmod
	INTO 	sysmodulo
	FROM 	configuracoes.db_sysmodulo
	WHERE 	regexp_replace(lower(to_ascii(nomemod)), '[^A-Za-z]' , '', 'g') = $1;

    -- Salva o "relation id" do catálogo do PostgreSQL
	relid := format('%I.%I', $1, $2)::regclass;

	-- 0) Remove tudo
	RAISE DEBUG '<fc_gera_dicionario_apartir_tabela> 1 - Chamando a funcao fc_remove_dicionario_tabela';
	PERFORM fc_remove_dicionario_tabela($1, $2);
    RAISE DEBUG '<fc_gera_dicionario_apartir_tabela> 1 - Removeu registros, caso existam';

	SELECT 	codarq
	INTO 	sysarquivo
	FROM	tmp_sysarquivo
	WHERE 	nomearq = $2;

	IF sysarquivo IS NULL THEN
        RAISE DEBUG '<fc_gera_dicionario_apartir_tabela> Gerando novo hash para a tabela do dicionario de dados';
		sysarquivo := fc_hash_int($2, 28) + fc_dicionario_salt();
        -- 1) db_sysarquivo
        INSERT INTO configuracoes.db_sysarquivo(codarq, nomearq) VALUES (sysarquivo, $2);
    ELSE
        RAISE DEBUG '<fc_gera_dicionario_apartir_tabela> Inserindo registros na tabela db_sysarquivo do dicionario de dados';
        -- 1) db_sysarquivo
        INSERT INTO configuracoes.db_sysarquivo (codarq, nomearq, descricao, sigla, dataincl, rotulo, tipotabela, naolibclass, naolibprog, naolibform)
            (select codarq, nomearq, descricao, sigla, dataincl, rotulo, tipotabela, naolibclass, naolibprog, naolibform from tmp_sysarquivo);
	END IF;


    RAISE DEBUG '<fc_gera_dicionario_apartir_tabela> Inserindo registros na tabela db_sysarqmod do dicionario de dados';
	-- 2) db_sysarqmod
	INSERT INTO configuracoes.db_sysarqmod(codmod, codarq)
	VALUES (sysmodulo, sysarquivo);

	-- 3) db_syscampo, db_syssequencia, db_sysarqcamp
	FOR r IN
		SELECT 	columns.table_catalog,
				columns.table_schema,
				columns.table_name,
				columns.column_name,
				columns.column_default,
				columns.udt_name,
				(columns.is_nullable='YES') AS is_nullable,
				columns.character_maximum_length AS size,
				columns.ordinal_position AS position,
				sequences.sequence_name,
				sequences.increment::integer AS increment,
				sequences.start_value::integer AS start_value,
				sequences.minimum_value::integer AS minimum_value,
				sequences.maximum_value::bigint AS maximum_value,
				kc.constraint_catalog,
				kc.constraint_schema,
				kc.constraint_name,
				kc.ordinal_position AS position_in_constraint,
				kc.position_in_unique_constraint,
				(SELECT 	array_agg(constraint_type::text)
				 FROM 		information_schema.table_constraints tc
				 WHERE 		tc.constraint_catalog = kc.constraint_catalog
				 AND 		tc.constraint_schema = kc.constraint_schema
				 AND 		tc.constraint_name = kc.constraint_name) AS constraint_type
				
		FROM 	information_schema.columns
				LEFT JOIN information_schema.sequences 	ON  sequences.sequence_catalog = columns.table_catalog
														AND sequences.sequence_schema = columns.table_schema
														AND format('nextval(%L::regclass)', sequences.sequence_name) = columns.column_default

				LEFT JOIN information_schema.key_column_usage kc			ON 	kc.table_catalog = columns.table_catalog
																			AND kc.table_schema = columns.table_schema
																			AND	kc.table_name = columns.table_name
																			AND kc.column_name = columns.column_name
		WHERE 	columns.table_schema = $1
		AND 	columns.table_name = $2
	LOOP
		-- 3.1) db_syscampo
		SELECT 	codcam
		INTO 	syscampo
		FROM	tmp_syscampo
		WHERE 	nomecam = r.column_name;

        -- Implementado essa verificação, pois pode haver o mesmo campo em mais de 1 tabela
		IF syscampo IS NULL THEN
           SELECT codcam
           INTO   syscampo
           FROM	db_syscampo
           WHERE nomecam = r.column_name;

           INSERT INTO tmp_syscampo
           SELECT *, pg_catalog.col_description(relid, (SELECT attnum FROM pg_attribute WHERE attrelid = relid AND attname = nomecam)) AS comentario
           FROM db_syscampo WHERE nomecam = r.column_name;

		END IF;

		IF syscampo IS NULL THEN
			syscampo := fc_hash_int(r.column_name, 28) + fc_dicionario_salt();
		END IF;

        SELECT coalesce(descricao, ''),
               coalesce(rotulo, ''),
               coalesce(maiusculo, false),
               coalesce(autocompl, false),
               coalesce(aceitatipo, 0),
               coalesce(tipoobj, ''),
               coalesce(rotulorel, '')
          INTO tDescricao,
               sRotulo,
               bMaiusculo,
               bAutocompl,
               iAceitatipo,
               sTipoobj,
               sRotulorel
        FROM tmp_syscampo
		WHERE codcam = syscampo
		  AND (comentario = '' OR comentario IS NULL);

		IF NOT FOUND THEN
           tDescricao  = '';
           sRotulo     = '';
           bMaiusculo  = false;
           bAutocompl  = false;
           iAceitatipo = 0;
           sTipoobj    = '';
           sRotulorel  = '';
        END IF;

		INSERT INTO configuracoes.db_syscampo(codcam, nomecam, conteudo, nulo, tamanho, valorinicial, 
		                                      descricao, rotulo, maiusculo, autocompl, aceitatipo, tipoobj, rotulorel)
		VALUES (syscampo, r.column_name, r.udt_name || coalesce('('||r.size||')', ''), r.is_nullable, r.size, r.column_default,
		        tDescricao, sRotulo, bMaiusculo, bAutocompl, iAceitatipo, sTipoobj, sRotulorel)
		ON CONFLICT (nomecam)
			DO UPDATE SET conteudo = EXCLUDED.conteudo, nulo = EXCLUDED.nulo, tamanho = EXCLUDED.tamanho;
		
		-- 3.2) db_syssequencia
		IF r.sequence_name IS NOT NULL THEN
			syssequencia := fc_hash_int(r.sequence_name, 28) + fc_dicionario_salt();

			INSERT INTO configuracoes.db_syssequencia (codsequencia, nomesequencia, incrseq, minvalueseq, maxvalueseq, startseq)
			VALUES (syssequencia, r.sequence_name, r.increment, r.minimum_value, r.maximum_value, r.start_value)
			ON CONFLICT (nomesequencia)
				DO UPDATE SET
					incrseq = EXCLUDED.incrseq, minvalueseq = EXCLUDED.minvalueseq,
					maxvalueseq = EXCLUDED.maxvalueseq, startseq = EXCLUDED.startseq;
		ELSE
			syssequencia := NULL;
		END IF;

		-- 3.3) db_sysarqcamp
		INSERT INTO configuracoes.db_sysarqcamp (codarq, codcam, seqarq, codsequencia)
		VALUES (sysarquivo, syscampo, r.position, syssequencia)
		ON CONFLICT ON CONSTRAINT db_sysarqcamp_codc_coda_seqa_pk
			DO NOTHING;

		-- 3.4) db_sysprikey
		IF 'PRIMARY KEY' = ANY(r.constraint_type) THEN
			INSERT INTO configuracoes.db_sysprikey (codarq, codcam, sequen, camiden)
			VALUES (sysarquivo, syscampo, r.position_in_constraint, syscampo);
		END IF;

		-- 3.5) db_sysforkey
		IF 'FOREIGN KEY' = ANY(r.constraint_type) THEN
			SELECT 	a.codarq
			INTO 	sysarquivofk
			FROM 	information_schema.referential_constraints rc
					JOIN information_schema.table_constraints tc 	ON  tc.constraint_catalog = rc.unique_constraint_catalog
																	AND tc.constraint_schema = rc.unique_constraint_schema
																	AND tc.constraint_name = rc.unique_constraint_name

					JOIN configuracoes.db_sysmodulo m ON regexp_replace(lower(to_ascii(m.nomemod)), '[^A-Za-z]' , '', 'g') = tc.table_schema
					JOIN configuracoes.db_sysarquivo a ON a.nomearq = tc.table_name
					JOIN configuracoes.db_sysarqmod am ON am.codmod = m.codmod AND am.codarq = a.codarq

			WHERE 	rc.constraint_catalog = r.constraint_catalog
			AND 	rc.constraint_schema = r.constraint_schema
			AND 	rc.constraint_name = r.constraint_name;

			IF sysarquivofk IS NOT NULL THEN
				INSERT INTO configuracoes.db_sysforkey(codarq, codcam, sequen, referen)
				VALUES (sysarquivo, syscampo, r.position_in_constraint, sysarquivofk);
			END IF;
		ELSE
			sysarquivofk := NULL;
		END IF;

		-- 3.6) Atualiza metadados no dicionario da COLUNA apartir do comentario salvo
		PERFORM fc_atualiza_dicionario_apartir_comentario('table column', format('%I.%I.%I', $1, $2, r.column_name), comentario)
		FROM 	tmp_syscampo
		WHERE 	nomecam = r.column_name
		AND 	comentario IS NOT NULL;

	END LOOP;

	-- 4) Atualiza metadados no dicionario da TABELA apartir do comentario salvo
	PERFORM fc_atualiza_dicionario_apartir_comentario('table', format('%I.%I', $1, $2), comentario)
	FROM 	tmp_sysarquivo
	WHERE 	nomearq = $2
	AND 	comentario IS NOT NULL;

	INSERT INTO db_syscampodep
	   SELECT tmp_syscampodep.*
	   FROM tmp_syscampodep LEFT JOIN db_syscampodep ON db_syscampodep.codcam = tmp_syscampodep.codcam
	   WHERE db_syscampodep.codcam IS NULL;

    INSERT INTO db_syscampodef
       SELECT tmp_syscampodef.*
       FROM tmp_syscampodef LEFT JOIN db_syscampodef ON db_syscampodef.codcam = tmp_syscampodef.codcam
	                                                AND db_syscampodef.defcampo = tmp_syscampodef.defcampo
	   WHERE db_syscampodef.codcam IS NULL;

    INSERT INTO db_sysforkey
    SELECT tmp_db_sysforkey.*
    FROM tmp_db_sysforkey LEFT JOIN db_sysforkey ON db_sysforkey.codcam = tmp_db_sysforkey.codcam
                                                AND db_sysforkey.codarq = tmp_db_sysforkey.codarq
    WHERE db_sysforkey.codcam IS NULL;

    INSERT INTO db_acount
       SELECT tmp_db_acount.*
       FROM tmp_db_acount LEFT JOIN db_acount ON db_acount.codarq = tmp_db_acount.codarq
	   WHERE db_acount.codarq IS NULL;

    INSERT INTO db_sysclasses
       SELECT tmp_db_sysclasses.*
       FROM tmp_db_sysclasses LEFT JOIN db_sysclasses ON db_sysclasses.codarq = tmp_db_sysclasses.codarq
	   WHERE db_sysclasses.codarq IS NULL;

    INSERT INTO iptutabelas
       SELECT tmp_iptutabelas.*
       FROM tmp_iptutabelas LEFT JOIN iptutabelas ON iptutabelas.j121_sequencial = tmp_iptutabelas.j121_sequencial
	   WHERE iptutabelas.j121_sequencial IS NULL;

    INSERT INTO db_processa
       SELECT tmp_db_processa.*
       FROM tmp_db_processa LEFT JOIN db_processa ON db_processa.codarq = tmp_db_processa.codarq
                                                 AND db_processa.id_item = tmp_db_processa.id_item
       WHERE db_processa.codarq IS NULL;

    INSERT INTO iptutabelasdepend
       SELECT tmp_iptutabelasdepend.*
       FROM tmp_iptutabelasdepend LEFT JOIN iptutabelasdepend
         ON iptutabelasdepend.j128_sequencial = tmp_iptutabelasdepend.j128_sequencial
	   WHERE iptutabelasdepend.j128_sequencial IS NULL;

    INSERT INTO iptutabelasconfig
       SELECT tmp_iptutabelasconfig.*
       FROM tmp_iptutabelasconfig LEFT JOIN iptutabelasconfig
         ON iptutabelasconfig.j122_sequencial = tmp_iptutabelasconfig.j122_sequencial
	   WHERE iptutabelasconfig.j122_sequencial IS NULL;

    INSERT INTO iptutabelasconfigcampochave
       SELECT tmp_iptutabelasconfigcampochave.*
       FROM tmp_iptutabelasconfigcampochave LEFT JOIN iptutabelasconfigcampochave
         ON iptutabelasconfigcampochave.j124_sequencial = tmp_iptutabelasconfigcampochave.j124_sequencial
	   WHERE iptutabelasconfigcampochave.j124_sequencial IS NULL;

    INSERT INTO iptutabelasconfigcampocorrecao
       SELECT tmp_iptutabelasconfigcampocorrecao.*
       FROM tmp_iptutabelasconfigcampocorrecao LEFT JOIN iptutabelasconfigcampocorrecao
         ON iptutabelasconfigcampocorrecao.j123_sequencial = tmp_iptutabelasconfigcampocorrecao.j123_sequencial
	   WHERE iptutabelasconfigcampocorrecao.j123_sequencial IS NULL;

    INSERT INTO iptutabelasconfigvirada
       SELECT tmp_iptutabelasconfigvirada.*
       FROM tmp_iptutabelasconfigvirada LEFT JOIN iptutabelasconfigvirada
         ON iptutabelasconfigvirada.j129_sequencial = tmp_iptutabelasconfigvirada.j129_sequencial
	   WHERE iptutabelasconfigvirada.j129_sequencial IS NULL;

    RETURN;
END;
$$
LANGUAGE plpgsql;

/*
 *
 * fc_atualiza_dicionario_apartir_comentario(text, text, text)
 *
 *   . responsavel por gerar dicionario de dados (tabelas db_sys*) apartir de uma tabela existente no PostgreSQL
 *
 * Parametros;
 *  $1 - tipo de objeto atualizar (valores: schema, table, table column)
 *  $2 - nome do objeto (schema: foo, table: foo.bar, table column: foo.bar.baz)
 *  $3 - comentário no formato JSON
 *
 *  Exemplos:
 *     SELECT fc_atualiza_dicionario_apartir_comentario(
 * 				'table', 'caixa.arrecad', '{"descricao": "Debitos do Contribuinte"}'); -- Tabela caixa.arrecad
 *
 */
CREATE OR REPLACE FUNCTION fc_atualiza_dicionario_apartir_comentario(tipo_obj text, nome_obj text, comentario_obj text)
RETURNS void AS
$$
DECLARE
	sysmodulo 		TEXT;
	sysarquivo 		TEXT;
	syscampo 		TEXT;
	systabela   	TEXT;
	comentario 		JSON;
	sql_update		TEXT;
	sql_where 		TEXT;
	lista_from 		TEXT;
	lista_join 		TEXT;
	campos_update 	TEXT[];
	linhas			INTEGER;
BEGIN
	comentario := comentario_obj::json;
	sysmodulo  := split_part(nome_obj, '.', 1);
	sql_where  := format('%s = %L',
						E'regexp_replace(lower(to_ascii(db_sysmodulo.nomemod)), \'[^A-Za-z]\' , \'\', \'g\')', sysmodulo);

	RAISE DEBUG 'tipo_obj: %  nome_obj: %  comentario: %', tipo_obj, nome_obj, comentario_obj;

	CASE tipo_obj
		WHEN 'schema' THEN
			systabela := 'configuracoes.db_sysmodulo';
			campos_update := ARRAY['descricao',  'dataincl', 'ativo'];
		WHEN 'table' THEN
			sysarquivo := split_part(nome_obj, '.', 2);
			systabela  := 'configuracoes.db_sysarquivo';
			
			lista_from := 'configuracoes.db_sysarqmod, configuracoes.db_sysmodulo';
			lista_join := 'db_sysarquivo.codarq = db_sysarqmod.codarq AND db_sysarqmod.codmod = db_sysmodulo.codmod';
			sql_where  := format('%s AND %s = %L', sql_where, 'db_sysarquivo.nomearq', sysarquivo);

			campos_update := ARRAY['descricao', 'sigla', 'dataincl', 'rotulo', 'tipotabela',
								   'naolibclass', 'naolibfunc', 'naolibprog', 'naolibform'];
		WHEN 'table column' THEN
			sysarquivo := split_part(nome_obj, '.', 2);
			syscampo   := split_part(nome_obj, '.', 3);
			systabela  := 'configuracoes.db_syscampo';
			
			lista_from := 'configuracoes.db_sysarqcamp, configuracoes.db_sysarquivo, configuracoes.db_sysarqmod, configuracoes.db_sysmodulo';
			lista_join := 'db_syscampo.codcam = db_sysarqcamp.codcam';
			lista_join := lista_join ||' AND db_sysarqcamp.codarq = db_sysarquivo.codarq';
			lista_join := lista_join ||' AND db_sysarqmod.codarq = db_sysarquivo.codarq';
			lista_join := lista_join ||' AND db_sysarqmod.codmod = db_sysmodulo.codmod';

			sql_where  := format('%s AND %s = %L AND %s = %L', sql_where, 
							'db_sysarquivo.nomearq', sysarquivo,
							'db_syscampo.nomecam', syscampo);
			
			campos_update := ARRAY['conteudo', 'descricao', 'valorinicial', 'rotulo', 'tamanho',
								   'nulo', 'maiusculo', 'autocompl', 'aceitatipo', 'tipoobj', 'rotulorel'];

			-- Atualizar db_syscampodef
			DELETE FROM db_syscampodef
			WHERE codcam = (SELECT codcam FROM db_syscampo WHERE nomecam = syscampo);

			INSERT INTO db_syscampodef(codcam, defcampo, defdescr)
			SELECT 	codcam, value->>'defcampo', value->>'defdescr'
			FROM 	json_array_elements(comentario->'syscampodef'),
					db_syscampo
			WHERE 	nomecam = syscampo;
		ELSE
			RAISE EXCEPTION 'Tipo de objeto % inválido!', tipo_obj;
	END CASE;

	SELECT 	format('UPDATE %s SET %s', systabela, string_agg(format('%I = %L', key, value), ', '))
	INTO 	sql_update
	FROM 	json_each_text(comentario)
	WHERE 	lower(key) = ANY(campos_update);

	IF lista_from IS NULL THEN
		sql_update := format('%s WHERE %s;', sql_update, sql_where);
	ELSE
		sql_update := format('%s FROM %s WHERE %s AND %s;', sql_update, lista_from, lista_join, sql_where);
	END IF;

	RAISE DEBUG 'sysmodulo: %, sysarquivo: %, syscampo: %, comentario: %',
		sysmodulo, sysarquivo, syscampo, comentario;

	RAISE DEBUG 'sql_update: %', sql_update;

	EXECUTE sql_update;
	GET DIAGNOSTICS linhas = ROW_COUNT;

	RAISE DEBUG 'linhas: %', linhas;

	RETURN;
END;
$$
LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION configuracoes.fc_dicionario_gatilho_ddl()
RETURNS event_trigger AS
$$
DECLARE
	r 				RECORD;
	_schema_name 	TEXT;
	_current_query 	TEXT;
BEGIN
	IF fc_getsession('__disable_trigger_dicionario__') IS NOT NULL THEN
		RAISE DEBUG 'Event Trigger do Dicionario de Dados Desabilitada';
		RETURN;
	END IF;

	FOR r IN
		SELECT	objid, objsubid, schema_name, pg_class.relname::text AS table_name, command_tag, object_type, object_identity
		FROM	pg_event_trigger_ddl_commands()
				LEFT JOIN pg_class ON pg_class.oid = objid
		WHERE 	command_tag IN ('CREATE TABLE', 'ALTER TABLE', 'COMMENT')
	LOOP
		RAISE DEBUG 'pg_event_trigger_ddl_commands: %', r;

		-- Apenas algums subcomandos do ALTER TABLE são permitidos como RENAME, ADD, DROP, SET SCHEMA e ALTER
		IF r.command_tag = 'ALTER TABLE' THEN
			_current_query := trim(regexp_replace(replace(upper(current_query()), 'ALTER TABLE ', ''), '\s+', ' ', 'g'));
			CONTINUE WHEN _current_query !~ '(RENAME|ADD|DROP|SET SCHEMA|ALTER) ';
		END IF;

		IF r.object_type = 'schema' AND r.command_tag = 'COMMENT' THEN
			_schema_name = r.object_identity;
		ELSE
			_schema_name = r.schema_name;
		END IF;

		-- Processa geracao do dicionario de dados apenas se o "esquema" existir na "db_sysmodulo"
		IF EXISTS (SELECT 1 FROM configuracoes.db_sysmodulo WHERE regexp_replace(lower(to_ascii(nomemod)), '[^A-Za-z]' , '', 'g') = _schema_name) THEN

			-- Processa comandos de tabela
			IF r.object_type = 'table' AND r.command_tag <> 'COMMENT' THEN
				-- Particoes de tabelas particionadas (db_logsacessa, db_auditoria e debitos) devem ser ignoradas
				CONTINUE WHEN r.table_name ~ '^(db_logsacessa|db_auditoria|debitos)_[0-9]' OR r.table_name ~ '^(db_logsacessa|db_auditoria|debitos)$';
                RAISE DEBUG '<fc_dicionario_gatilho_ddl> Chamando a funcao fc_gera_dicionario_apartir_tabela';
				RAISE DEBUG '%', format('fc_gera_dicionario_apartir_tabela(%L, %L)', r.schema_name, r.table_name);
				PERFORM	fc_gera_dicionario_apartir_tabela(r.schema_name, r.table_name);

			-- Processa comandos de comentarios
			ELSIF r.command_tag = 'COMMENT' THEN
				IF r.object_type = 'table column' THEN
					PERFORM fc_atualiza_dicionario_apartir_comentario(
								r.object_type, r.object_identity, pg_catalog.col_description(r.objid, r.objsubid));
				ELSIF r.object_type = 'table' THEN
					PERFORM fc_atualiza_dicionario_apartir_comentario(
								r.object_type, r.object_identity, pg_catalog.obj_description(r.objid, 'pg_class'));
				ELSIF r.object_type = 'schema' THEN
					PERFORM fc_atualiza_dicionario_apartir_comentario(
								r.object_type, r.object_identity, pg_catalog.obj_description(r.objid, 'pg_namespace'));
				END IF;
			END IF;

			IF r.object_type <> 'schema' THEN
				-- Salva na sessao tabelas que foram processadas
				PERFORM fc_putsession('__evtg_dicionario_gatilho_ddl_tables__', array_agg(distinct i)::text)
				FROM 	unnest(array_append(fc_getsession('__evtg_dicionario_gatilho_ddl_tables__')::text[], format('%s.%s', r.schema_name, r.table_name))) AS i;
			END IF;
		END IF;
	END LOOP;

    RETURN;
END;
$$
SECURITY DEFINER
LANGUAGE plpgsql;

DROP EVENT TRIGGER IF EXISTS evtg_dicionario_gatilho_ddl;
CREATE EVENT TRIGGER evtg_dicionario_gatilho_ddl
	ON ddl_command_end
	WHEN tag IN ('CREATE TABLE', 'ALTER TABLE', 'COMMENT')
	EXECUTE PROCEDURE configuracoes.fc_dicionario_gatilho_ddl();
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;

CREATE OR REPLACE FUNCTION configuracoes.fc_dicionario_gatilho_ddl_drop()
RETURNS event_trigger AS
$$
DECLARE
	r 	RECORD;
BEGIN
	IF fc_getsession('__disable_trigger_dicionario__') IS NOT NULL THEN
		RAISE DEBUG 'Event Trigger do Dicionario de Dados Desabilitada';
		RETURN;
	END IF;

	FOR r IN
		SELECT	schema_name, object_name AS table_name
		FROM	pg_event_trigger_dropped_objects()
		WHERE 	object_type = 'table'
	LOOP
		-- Processa geracao do dicionario de dados apenas se o "esquema" existir na "db_sysmodulo"
		IF EXISTS (SELECT 1 FROM configuracoes.db_sysmodulo WHERE regexp_replace(lower(to_ascii(nomemod)), '[^A-Za-z]' , '', 'g') = r.schema_name) THEN
			RAISE DEBUG 'pg_event_trigger_dropped_objects: %', r;
            RAISE DEBUG '<fc_dicionario_gatilho_ddl_drop> 2 - Chamando a funcao fc_remove_dicionario_tabela';
			PERFORM fc_remove_dicionario_tabela(r.schema_name, r.table_name);

			-- Salva na sessao tabelas que foram processadas
			PERFORM fc_putsession('__evtg_dicionario_gatilho_ddl_tables__', array_agg(distinct i)::text)
			FROM 	unnest(array_append(fc_getsession('__evtg_dicionario_gatilho_ddl_tables__')::text[], format('%s.%s', r.schema_name, r.table_name))) AS i;
		END IF;
	END LOOP;

    RETURN;
END;
$$
SECURITY DEFINER
LANGUAGE plpgsql;

DROP EVENT TRIGGER IF EXISTS evtg_dicionario_gatilho_ddl_drop;
CREATE EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop
	ON sql_drop
	EXECUTE PROCEDURE configuracoes.fc_dicionario_gatilho_ddl_drop();
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL;
        $this->execute($sql);
    }

    private function upItensMenu()
    {
        $sql = <<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229281 ,'S-1280 - Informações Complementares aos Eventos Periódicos' ,'S-1280 - Informações Complementares aos Eventos Periódicos' ,'web/recursos-humanos/esocial/informacoes/complementares/eventos-periodicos' ,'1' ,'1' ,'S-1280 - Informações Complementares aos Eventos Periódicos' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 10466 ,229281 ,22 ,10216 );

SQL;
        $this->execute($sql);
    }

    private function upTable()
    {
        $sql = <<<SQL
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            CREATE SEQUENCE IF NOT EXISTS recursoshumanos.informacoes_complementares_id_seq
                INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

            CREATE TABLE IF NOT EXISTS recursoshumanos.informacoes_complementares (
                eso40_sequencial integer NOT NULL DEFAULT nextval('recursoshumanos.informacoes_complementares_id_seq'),
                eso40_tp_insc varchar not null,
                eso40_num_insc varchar not null,
                eso40_nr_insc varchar not null,
                eso40_ind_subst_patr varchar,
                eso40_perc_red_contrib varchar,
                eso40_cod_lotacao varchar,
                eso40_fator_mes varchar,
                eso40_fator_13 varchar,
                eso40_perc_transf varchar,
                eso40_cod_inst integer,
                eso40_periodo varchar(7),
                CONSTRAINT informacoes_complementares_inst_fk FOREIGN KEY (eso40_cod_inst) REFERENCES configuracoes.db_config(codigo),
                CONSTRAINT informacoes_complementares_pk PRIMARY KEY (eso40_sequencial)
            );

            COMMENT ON TABLE recursoshumanos.informacoes_complementares IS
                '{"descricao": "Tabela de cadastro das Informações Complementares",
                  "sigla": "eso40",
                  "dataincl": "2024-07-16",
                  "rotulo": "informacoes_complementares",
                  "tipotabela": "0",
                  "naolibclass": "false",
                  "naolibfunc": "false",
                  "naolibprog": "false",
                  "naolibform": "false"
                 }';

            COMMENT ON COLUMN recursoshumanos.informacoes_complementares.eso40_sequencial IS
                 '{"descricao": "Código da Informação complementar",
                    "rotulo": "Código da Informação complementar",
                    "rotulorel": "Código da Informação complementar",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 1,
                    "tamanho": 10,
                    "tipoobj": "text"
                }';

                COMMENT ON COLUMN recursoshumanos.informacoes_complementares.eso40_tp_insc IS
                 '{"descricao": "Código da Informação Tipo de inscrição 1-CNPJ ou 2-CPF",
                    "rotulo": "eso40_tp_insc",
                    "rotulorel": "eso40_tp_insc",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 1,
                    "tamanho": 10,
                    "tipoobj": "text"
                }';

                COMMENT ON COLUMN recursoshumanos.informacoes_complementares.eso40_num_insc IS
                 '{"descricao": "Número da inscrição Código da CNPJ ou CPF",
                    "rotulo": "eso40_num_insc",
                    "rotulorel": "Número da inscrição",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 1,
                    "tamanho": 10,
                    "tipoobj": "text"
                }';

                COMMENT ON COLUMN recursoshumanos.informacoes_complementares.eso40_nr_insc IS
                 '{"descricao": "Informar o numero de inscricao do contribuinte.",
                    "rotulo": "eso40_nr_insc",
                    "rotulorel": "eso40_nr_insc",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 1,
                    "tamanho": 10,
                    "tipoobj": "text"
                }';

                COMMENT ON COLUMN recursoshumanos.informacoes_complementares.eso40_cod_inst IS
                 '{"descricao": "Chave estrangeira da tabela configuracoes.db_config.",
                    "rotulo": "eso40_cod_inst",
                    "rotulorel": "eso40_cod_inst",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 1,
                    "tamanho": 10,
                    "tipoobj": "text"
                }';

            COMMENT ON COLUMN recursoshumanos.informacoes_complementares.eso40_ind_subst_patr IS
                '{"descricao": "Indicativo de substituição da contribuição previdenciária patronal.",
                    "rotulo": "eso40_ind_subst_patr",
                    "rotulorel": "eso40_ind_subst_patr",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 1,
                    "tamanho": 10,
                    "tipoobj": "text"
                }';

            COMMENT ON COLUMN recursoshumanos.informacoes_complementares.eso40_perc_red_contrib IS
                '{"descricao": "Percentual não substituído pela contribuição prevista na Lei 12.546/2011.",
                    "rotulo": "eso40_perc_red_contrib",
                    "rotulorel": "eso40_perc_red_contrib",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 1,
                    "tamanho": 10,
                    "tipoobj": "text"
                }';

            COMMENT ON COLUMN recursoshumanos.informacoes_complementares.eso40_cod_lotacao IS
                '{"descricao": "Informar o código atribuído pelo empregador para a lotação tributária.",
                    "rotulo": "eso40_cod_lotacao",
                    "rotulorel": "eso40_cod_lotacao",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 1,
                    "tamanho": 10,
                    "tipoobj": "text"
                }';

            COMMENT ON COLUMN recursoshumanos.informacoes_complementares.eso40_fator_mes IS
                '{"descricao": "Informe o fator a ser utilizado para cálculo da contribuição patronal do mês dos trabalhadores envolvidos na execução das atividades enquadradas no Anexo IV em conjunto com as dos Anexos I a III e V da Lei Complementar 123/2006.",
                    "rotulo": "eso40_fator_mes",
                    "rotulorel": "eso40_fator_mes",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 1,
                    "tamanho": 10,
                    "tipoobj": "text"
                }';

            COMMENT ON COLUMN recursoshumanos.informacoes_complementares.eso40_fator_13 IS
                '{"descricao": "Informe o fator a ser utilizado para cálculo da contribuição patronal do décimo terceiro dos trabalhadores envolvidos na execução das atividades enquadradas no Anexo IV em conjunto com as dos Anexos I a III e V da Lei Complementar 123/2006.",
                    "rotulo": "eso40_fator_13",
                    "rotulorel": "eso40_fator_13",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 1,
                    "tamanho": 10,
                    "tipoobj": "text"
                }';

            COMMENT ON COLUMN recursoshumanos.informacoes_complementares.eso40_perc_transf IS
                '{"descricao": "Informe o percentual de contribuição social devida em caso de transformação em sociedade de fins lucrativos - Lei 11.096/2005.",
                    "rotulo": "eso40_perc_transf",
                    "rotulorel": "eso40_perc_transf",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 1,
                    "tamanho": 10,
                    "tipoobj": "text"
                }';

           COMMENT ON COLUMN recursoshumanos.informacoes_complementares.eso40_periodo IS
                '{"descricao": "Informe o período",
                    "rotulo": "eso40_periodo",
                    "rotulorel": "eso40_periodo",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 1,
                    "tamanho": 7,
                    "tipoobj": "text"
                }';

        SELECT fc_gera_dicionario_apartir_tabela('recursoshumanos', 'informacoes_complementares');

        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;

        insert into recursoshumanos.esocialformulariotipo values(56, 'S-1280 - Informações Complementares aos Eventos Períodicos');
        insert into habitacao.avaliacao values (4000125, 5, 'S-1280 - Informações Complementares aos Eventos Períodicos', 'S-1280 - Informações Complementares aos Eventos Períodicos', true, 's1280_informacoes_complementares', null, false);
        insert into recursoshumanos.esocialversaoformulario values (nextval('recursoshumanos.esocialversaoformulario_rh211_sequencial_seq'), 'S1.2', 4000125, 56);

SQL;
        $this->execute($sql);
    }

    private function downDicionario()
    {
        $sql = <<<SQL
        delete from recursoshumanos.esocialversaoformulario where rh211_avaliacao = 4000125 and rh211_esocialformulariotipo = 56;
        delete from habitacao.avaliacao where  db101_sequencial = 4000125;
        delete from recursoshumanos.esocialformulariotipo where rh209_sequencial = 56;
        ALTER TABLE recursoshumanos.informacoes_complementares DROP CONSTRAINT informacoes_complementares_inst_fk;
        DROP TABLE recursoshumanos.informacoes_complementares;
        DROP SEQUENCE recursoshumanos.informacoes_complementares_id_seq;
SQL;
        $this->execute($sql);
    }

    private function downItensMenu()
    {
        $sql = <<<SQL
        delete from db_menu where id_item_filho = 229281;
        delete from db_itensmenu where id_item = 229281;
SQL;
        $this->execute($sql);
    }

    private function execute($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
