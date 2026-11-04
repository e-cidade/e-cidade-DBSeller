<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M25393CalculoIptuValenca extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionario();
        $this->upFuncoes();
        $this->upTabelas();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downDicionario();
        $this->downFuncoes();
        $this->downTabelas();
    }

    private function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL

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
		RAISE INFO 'Tabela %.% não encontrada no dicionário de dados!', $1, $2;
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
	DROP TABLE IF EXISTS tmp_sysarquivo;
	DROP TABLE IF EXISTS tmp_syscampo;
	DROP TABLE IF EXISTS tmp_syscampodef;
	DROP TABLE IF EXISTS tmp_syscampodep;
	DROP TABLE IF EXISTS tmp_db_acount;
	DROP TABLE IF EXISTS tmp_db_sysclasses;
    DROP TABLE IF EXISTS tmp_iptutabelasdepend;
    DROP TABLE IF EXISTS tmp_iptutabelasconfigcampochave;
    DROP TABLE IF EXISTS tmp_iptutabelasconfig;
    DROP TABLE IF EXISTS tmp_iptutabelas;

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

    CREATE TEMP TABLE tmp_iptutabelasconfig AS
    SELECT iptutabelasconfig.*
    FROM iptutabelasconfig
    WHERE j122_iptutabelas in (SELECT j121_sequencial FROM iptutabelas WHERE j121_codarq = sysarquivo);

    CREATE TEMP TABLE tmp_iptutabelas AS
    SELECT iptutabelas.*
    FROM iptutabelas
    WHERE j121_codarq = sysarquivo;

    -- 1) Remove tudo
    DELETE FROM iptutabelasconfigcampochave
        WHERE j124_iptutabelasconfig in (SELECT j122_sequencial
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
	DELETE FROM configuracoes.db_sysforkey WHERE codarq = sysarquivo;
	DELETE FROM configuracoes.db_sysprikey WHERE codarq = sysarquivo;
	DELETE FROM configuracoes.db_sysarqcamp WHERE codarq = sysarquivo AND codcam = ANY(syscampos);
	DELETE FROM configuracoes.db_syscampodef WHERE codcam = ANY(syscampos);
	DELETE FROM configuracoes.db_syscampodep WHERE codcam = ANY(syscampos);
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
	PERFORM fc_remove_dicionario_tabela($1, $2);

	SELECT 	codarq
	INTO 	sysarquivo
	FROM	tmp_sysarquivo
	WHERE 	nomearq = $2;

	IF sysarquivo IS NULL THEN
		sysarquivo := fc_hash_int($2, 28) + fc_dicionario_salt();
	END IF;

	-- 1) db_sysarquivo
	INSERT INTO configuracoes.db_sysarquivo(codarq, nomearq)
	VALUES (sysarquivo, $2);

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

	INSERT INTO db_syscampodep SELECT * FROM tmp_syscampodep;
    INSERT INTO db_syscampodef SELECT * FROM tmp_syscampodef;
    INSERT INTO db_acount SELECT * FROM tmp_db_acount;
    INSERT INTO db_sysclasses SELECT * FROM tmp_db_sysclasses;
    INSERT INTO iptutabelas SELECT * FROM tmp_iptutabelas;
    INSERT INTO iptutabelasdepend SELECT * FROM tmp_iptutabelasdepend;
    INSERT INTO iptutabelasconfig SELECT * FROM tmp_iptutabelasconfig;
    INSERT INTO iptutabelasconfigcampochave SELECT * FROM tmp_iptutabelasconfigcampochave;

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


            insert into db_sysfuncoes( codfuncao ,nomefuncao ,nomearquivo ,obsfuncao ,corpofuncao ,triggerfuncao ) values ( 229 ,'fc_iptu_getaliquota_valenca_2023' ,'iptu_getaliquota_valenca_2023.sql' ,'Função para buscar a alíquota de iptu' ,'create or replace function fc_iptu_getaliquota_valenca_2023(integer, integer, integer, boolean, boolean) returns tp_aliquota_iptu as $$ declare iMatricula alias for $1; iIdbql alias for $2; iAnousu alias for $3; bPredial alias for $4; bRaise alias for $5; nAliquota numeric default 0; iRegistros integer default 0; rtp_aliquota_iptu tp_aliquota_iptu%ROWTYPE; begin perform fc_debug(\'DEFININDO QUAL ALIQUOTA APLICAR ...\', bRaise); rtp_aliquota_iptu.coderro = 0; rtp_aliquota_iptu.descrerro = \'\'; rtp_aliquota_iptu.aliquota = 0; if bPredial then select coalesce(max(j82_valorterreno), 0)::numeric, count(*) into nAliquota, iRegistros from iptubase join iptuconstr on j39_matric = j01_matric and j39_idprinc is true and j39_dtdemo is null join carconstr on j48_matric = j39_matric and j48_idcons = j39_idcons join caracter on j31_codigo = j48_caract and j31_grupo = 500 join lotesetorfiscal on j91_idbql = j01_idbql join setorfiscalvalor on j82_setorfiscal = j91_codigo and j82_anousu = iAnousu and j82_caract = j48_caract where j01_matric = iMatricula group by j01_matric; if not found then rtp_aliquota_iptu.coderro = 13; rtp_aliquota_iptu.descrerro = \' OR NAO ENCONTRADA PARA O ANO \'||iAnousu; rtp_aliquota_iptu.aliquota = 0; return rtp_aliquota_iptu; end if; if iRegistros > 1 then rtp_aliquota_iptu.coderro = 38; rtp_aliquota_iptu.descrerro = \' \'||iMatricula; rtp_aliquota_iptu.aliquota = 0; return rtp_aliquota_iptu; end if; else select coalesce(j110_fator, 0)::numeric into nAliquota from lote join zonafator on j110_zona = j34_zona and j110_anousu = iAnousu where j34_idbql = iIdbql; if not found then rtp_aliquota_iptu.coderro = 13; rtp_aliquota_iptu.descrerro = \' OR NAO ENCONTRADA PARA O ANO \'||iAnousu; rtp_aliquota_iptu.aliquota = 0; return rtp_aliquota_iptu; end if; end if; perform fc_debug(\'< getaliquota > aliquota final: \'||nAliquota, bRaise); execute \'update tmpdadosiptu set aliq = \'||nAliquota; rtp_aliquota_iptu.coderro = 0; rtp_aliquota_iptu.descrerro = \'\'; rtp_aliquota_iptu.aliquota = nAliquota; return rtp_aliquota_iptu; end; $$ language \'plpgsql\'; ' ,'0' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1211 ,229 ,1 ,'iMatricula' ,'int4' ,0 ,0 ,'0' ,'MATRICULA DO IMÓVEL' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1212 ,229 ,2 ,'iIdbql' ,'int4' ,0 ,0 ,'0' ,'CODIGO IDBQL ' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1213 ,229 ,3 ,'iAnousu' ,'int4' ,0 ,0 ,'0' ,'ANO DE CALCULO ' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1214 ,229 ,4 ,'bPredial' ,'bool' ,0 ,0 ,'FALSE' ,'IMOVEL PREDIAL' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1215 ,229 ,5 ,'bRaise' ,'bool' ,0 ,0 ,'FALSE' ,'VARIAVEL PARA DEBUG' );
            insert into db_sysfuncoes( codfuncao ,nomefuncao ,nomearquivo ,obsfuncao ,corpofuncao ,triggerfuncao ) values ( 230 ,'fc_iptu_calculavvt_valenca_2023' ,'iptu_calculavvt_valenca_2023.sql' ,'Função de cálculo do valor venal do terreno de Valença' ,'create or replace function fc_iptu_calculavvt_valenca_2023(integer,integer,integer,numeric,boolean,boolean) returns tp_iptu_calculavvt as $$ declare iIdbql alias for $1; iMatricula alias for $2; iAnousu alias for $3; nFracao alias for $4; lMostrademo alias for $5; lRaise alias for $6; lPredial boolean default false; nVm2t numeric default 0; nAreaLoteCorrigi numeric default 0; nAreaTerreno numeric default 0; nValor numeric default 0; nTestada numeric default 0; nFatorSituacao numeric default 0; nFatorCondFisTerreno numeric default 0; iZona bigint default 0; rtp_iptu_calculavvt tp_iptu_calculavvt%ROWTYPE; begin rtp_iptu_calculavvt.rnAreaTotalC := 0; rtp_iptu_calculavvt.rnArea := 0; rtp_iptu_calculavvt.rnTestada := 0; rtp_iptu_calculavvt.riCoderro := 0; rtp_iptu_calculavvt.rtDemo := \'\'; rtp_iptu_calculavvt.rtMsgerro := \'\'; rtp_iptu_calculavvt.rtErro := \'\'; rtp_iptu_calculavvt.rbErro := \'f\'; perform fc_debug(\'< calculo vvt > INICIANDO CALCULO DO VALOR VENAL TERRITORIAL...\', lRaise); select case when j39_matric is not null then true else false end into lPredial from iptubase left join iptuconstr on j39_matric = j01_matric and j39_dtdemo is null where j01_matric = iMatricula; if not found then rtp_iptu_calculavvt.rbErro := \'t\'; rtp_iptu_calculavvt.riCoderro := 9; rtp_iptu_calculavvt.rtErro := \' DADOS DA MATRICULA NAO ENCONTRADO\'; return rtp_iptu_calculavvt; end if; /* verifica a area do lote */ select case when j34_area = 0 then j34_areal else j34_area end, j34_zona into nAreaTerreno, iZona from lote where j34_idbql = iIdbql; if nAreaTerreno is null or nAreaTerreno = 0 then rtp_iptu_calculavvt.rbErro := \'t\'; rtp_iptu_calculavvt.riCoderro := 3; rtp_iptu_calculavvt.rtErro := \'AREA DO LOTE NAO ENCONTRADA OU ZERADA\'; return rtp_iptu_calculavvt; end if; nAreaLoteCorrigi := ( nAreaTerreno * ( nFracao / 100::numeric ) ); perform fc_debug(\'< calculo vvt > Area do terreno: \'||nAreaTerreno, lRaise); /* busca valor do m2 do terreno */ select j51_valorm2t::numeric into nVm2t from zonasvalor where j51_anousu = iAnousu and j51_zona = iZona; if not found then rtp_iptu_calculavvt.rbErro := \'t\'; rtp_iptu_calculavvt.riCoderro := 7; rtp_iptu_calculavvt.rtMsgErro := \'VERIFIQUE O VALOR DO M2 DO TERRENO PARA A ZONA\'; return rtp_iptu_calculavvt; end if; perform fc_debug(\'< calculo vvt > Valor do m2 do terreno: \'||nVm2t, lRaise); /* busca fator Situacao */ select j74_fator::numeric into nFatorSituacao from carlote inner join caracter on j31_codigo = j35_caract inner join carfator on j74_anousu = iAnousu and j74_caract = j35_caract where j35_idbql = iIdbql and j31_grupo = 100; if nFatorSituacao = 0 or nFatorSituacao is null then rtp_iptu_calculavvt.rbErro := \'t\'; rtp_iptu_calculavvt.riCoderro := 24; rtp_iptu_calculavvt.rtErro := \' PARA GRUPO 100 OU SEM VALOR PARA O ANO DE \'||iAnousu; rtp_iptu_calculavvt.rtMsgErro := \'SEM CARACTERISTICA PARA GRUPO 100 OU SEM VALOR PARA O ANO DE \'||iAnousu; return rtp_iptu_calculavvt; end if; /* busca fator Condicao Fisica do Terreno */ select j74_fator::numeric into nFatorCondFisTerreno from carlote inner join caracter on j31_codigo = j35_caract inner join carfator on j74_anousu = iAnousu and j74_caract = j35_caract where j35_idbql = iIdbql and j31_grupo = 200; if nFatorCondFisTerreno = 0 or nFatorCondFisTerreno is null then rtp_iptu_calculavvt.rbErro := \'t\'; rtp_iptu_calculavvt.riCoderro := 24; rtp_iptu_calculavvt.rtErro := \' PARA GRUPO 200 OU SEM VALOR PARA O ANO DE \'||iAnousu; rtp_iptu_calculavvt.rtMsgErro := \'SEM CARACTERISTICA PARA GRUPO 200 OU SEM VALOR PARA O ANO DE \'||iAnousu; return rtp_iptu_calculavvt; end if; nValor := round( nAreaLoteCorrigi * nVm2t * nFatorSituacao * nFatorCondFisTerreno, 2); if nValor <= 0 or nValor is null then rtp_iptu_calculavvt.rbErro := \'t\'; rtp_iptu_calculavvt.riCoderro := 113; rtp_iptu_calculavvt.rtErro := \'\'; return rtp_iptu_calculavvt; end if; /* formula de calculo do terreno */ perform fc_debug(\'< calculo vvt > Formula: VVT = (nAreaLoteCorrigi * nVm2t * nFatorSituacao * nFatorCondFisTerreno)\', lRaise); perform fc_debug(\'\', lRaise); perform fc_debug(\'< calculo vvt > Formula: \'||nValor||\' = (\'||nAreaLoteCorrigi||\' * (\'||nVm2t||\' * \'||nFatorSituacao||\' * \'||nFatorCondFisTerreno||\')\', lRaise, lRaise, lRaise); rtp_iptu_calculavvt.rnArea := nAreaTerreno; rtp_iptu_calculavvt.rnVvt := nValor; rtp_iptu_calculavvt.rnAreaTotalC := nAreaLoteCorrigi; rtp_iptu_calculavvt.rnTestada := nTestada; rtp_iptu_calculavvt.rtDemo := \'\'; rtp_iptu_calculavvt.rtMsgerro := \'\'; rtp_iptu_calculavvt.rbErro := \'f\'; update tmpdadosiptu set vvt = rtp_iptu_calculavvt.rnVvt, vm2t= nVm2t, areat=nAreaLoteCorrigi; return rtp_iptu_calculavvt; end; $$ language \'plpgsql\'; ' ,'0' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1216 ,230 ,1 ,'iIdbql' ,'int4' ,0 ,0 ,'0' ,'CODIGO DO IDBQL' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1217 ,230 ,2 ,'iMatricula' ,'int4' ,0 ,0 ,'0' ,'CODIGO DA MATRICULA' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1218 ,230 ,3 ,'iAnousu' ,'int4' ,0 ,0 ,'0' ,'ANO DE CALCULO' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1219 ,230 ,4 ,'nFracao' ,'numeric' ,0 ,0 ,'0' ,'FRACAO DO LOTE' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1220 ,230 ,5 ,'lMostrademo' ,'bool' ,0 ,0 ,'FALSE' ,'VARIAVEL DE CONTROLE PARA GERAR DEMONSTRATIVO' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1221 ,230 ,6 ,'lRaise' ,'bool' ,0 ,0 ,'FALSE' ,'VARIAVEL PARA DEBUG' );
            insert into db_sysfuncoes( codfuncao ,nomefuncao ,nomearquivo ,obsfuncao ,corpofuncao ,triggerfuncao ) values ( 231 ,'fc_iptu_calculavvc_valenca_2023' ,'iptu_calculavvc_valenca_2023.sql' ,'Função de cálculo do valor venal da construção de Valença' ,'create or replace function fc_iptu_calculavvc_valenca_2023(integer,integer,boolean,boolean) returns tp_iptu_calculavvc as $$ declare iMatricula alias for $1; iAnousu alias for $2; lMostrademo alias for $3; lRaise alias for $4; nAreatc numeric default 0; nVm2c numeric default 0; nVvcP numeric default 0; nVvc numeric default 0; nFatorIdade numeric default 0; nFatorLocalizacao numeric default 0; iNumerocontr integer default 0; iPontos integer default 0; iTotalPontos integer default 0; iTipoConstrucao integer default 0; lAtualiza boolean default true; rConstr record; rCargrup record; jCargrup json; tMsg text; rtp_iptu_calculavvc tp_iptu_calculavvc%ROWTYPE; begin perform fc_debug(\'INICIANDO CALCULO VVC ...\', lRaise); rtp_iptu_calculavvc.rnVvc := 0; rtp_iptu_calculavvc.rnTotarea := 0; rtp_iptu_calculavvc.riNumconstr := 0; rtp_iptu_calculavvc.rtDemo := \'\'; rtp_iptu_calculavvc.rtMsgerro := \'Retorno ok\' ; rtp_iptu_calculavvc.rbErro := \'f\'; rtp_iptu_calculavvc.riCodErro := 0; rtp_iptu_calculavvc.rtErro := \'\'; /* busca os grupos de caracteristicas com pontuacao */ select json_agg(cargrup.*) into jCargrup from cargrup where j32_grupo in (1700, 1800, 1900, 2000, 2100, 2200, 2300, 2400, 7800, 7900, 8000, 8100); iNumerocontr := 0; for rConstr in select j39_matric, j39_idcons, j39_idprinc, j39_area, j39_areap, coalesce(j39_ano, 0) as j39_ano from iptuconstr where j39_matric = iMatricula and j39_dtdemo is null loop /* busca os pontos de cada construcao */ iTotalPontos := 0; for rCargrup in select value->>\'j32_grupo\' as codigo, value->>\'j32_descr\' as descricao from json_array_elements(jCargrup) loop select coalesce(j31_pontos, 0) into iPontos from carconstr join caracter on j31_codigo = j48_caract and j31_grupo = rCargrup.codigo where j48_matric = rConstr.j39_matric and j48_idcons = rConstr.j39_idcons; if not found then tMsg := \' PARA O GRUPO \'||rCargrup.codigo||\' - \'||rCargrup.descricao; perform fc_debug(\'< calculo vvc > Matricula \'||rConstr.j39_matric||\' Edificacao \'|| rConstr.j39_idcons||\' nao encontrado \'||tMsg, lRaise); rtp_iptu_calculavvc.rtErro := tMsg; rtp_iptu_calculavvc.rtMsgErro := \'sem caracteristica \'||tMsg; rtp_iptu_calculavvc.riCodErro := 23; rtp_iptu_calculavvc.rbErro := \'t\'; return rtp_iptu_calculavvc; end if; iTotalPontos := iTotalPontos + iPontos; end loop; if iTotalPontos = 0 then perform fc_debug(\'< calculo vvc > Matricula \'||rConstr.j39_matric||\' Edificacao \'|| rConstr.j39_idcons||\' sem pontuacao.\', lRaise); rtp_iptu_calculavvc.rtErro := \' PARA O GRUPO 500\'; rtp_iptu_calculavvc.rtMsgErro := \'SEM PONTUACAO PARA A EDIFICACAO \'||rConstr.j39_idcons; rtp_iptu_calculavvc.riCodErro := 23; rtp_iptu_calculavvc.rbErro := \'t\'; return rtp_iptu_calculavvc; end if; /* busca a caracteristica do grupo 500 - tipo construcao */ select j48_caract into iTipoConstrucao from carconstr join caracter on j31_codigo = j48_caract and j31_grupo = 500 where j48_matric = rConstr.j39_matric and j48_idcons = rConstr.j39_idcons; if not found then perform fc_debug(\'< calculo vvc > Matricula \'||rConstr.j39_matric||\' Edificacao \'|| rConstr.j39_idcons||\' nao encontrado a caracteristica do grupo 500.\', lRaise); rtp_iptu_calculavvc.rtErro := \' 500\'; rtp_iptu_calculavvc.rtMsgErro := \'SEM CARACTERISITICA PARA O GRUPO 500\'; rtp_iptu_calculavvc.riCodErro := 102; rtp_iptu_calculavvc.rbErro := \'t\'; return rtp_iptu_calculavvc; end if; /* busca o valor do m2 para a construcao */ select j71_valor::numeric into nVm2c from carvalor where j71_anousu = iAnousu and j71_caract = iTipoConstrucao and iTotalPontos between j71_quantini and j71_quantfim; if not found then perform fc_debug(\'< calculo vvc > Matricula \'||rConstr.j39_matric||\' Edificacao \'|| rConstr.j39_idcons||\' nao encontrado para caracteristica \'||iTipoConstrucao, lRaise); rtp_iptu_calculavvc.rtErro := \' OU GRUPO 500 NAO ENCONTRADO OU SEM VALOR PARA O ANO DE \'||iAnousu; rtp_iptu_calculavvc.rtMsgErro := \'SEM CARACTERISITICA PARA O GRUPO 500 OU SEM VALOR PARA O ANO DE \'||iAnousu; rtp_iptu_calculavvc.riCodErro := 29; rtp_iptu_calculavvc.rbErro := \'t\'; return rtp_iptu_calculavvc; end if; /* busca fator idade da construcao */ select coalesce(j118_fatorreajuste, 0)::numeric into nFatorIdade from iptupadraoconstrpontos where j118_iptupadraoconstr = 1 and j118_anousu = iAnousu and j118_caracter = iTipoConstrucao and (iAnousu - rConstr.j39_ano) between j118_pontosini and j118_pontosfim; if not found or nFatorIdade = 0 then perform fc_debug(\'< calculo vvc > Matricula \'||rConstr.j39_matric||\' Edificacao \'|| rConstr.j39_idcons||\' fator idade da construcao nao encontrado ou zerado para o ano de \'||iAnousu, lRaise); rtp_iptu_calculavvc.rtErro := \' OU SEM VALOR PARA O ANO DE \'||iAnousu; rtp_iptu_calculavvc.rtMsgErro := \'FATOR IDADE DA CONSTRUCAO NAO ENCONTRADO OU SEM VALOR PARA O ANO DE \'||iAnousu; rtp_iptu_calculavvc.riCodErro := 36; rtp_iptu_calculavvc.rbErro := \'t\'; return rtp_iptu_calculavvc; end if; perform j74_fator from carconstr join caracter on j31_codigo = j48_caract and j31_grupo = 7200 join carfator on j74_anousu = iAnousu and j74_caract = j48_caract where j48_matric = rConstr.j39_matric and j48_idcons = rConstr.j39_idcons; if found then nFatorIdade = 1; end if; /* busca o fator localizacao da construcao */ select j74_fator into nFatorLocalizacao from carconstr join caracter on j31_codigo = j48_caract and j31_grupo = 6700 join carfator on j74_anousu = iAnousu and j74_caract = j48_caract where j48_matric = rConstr.j39_matric and j48_idcons = rConstr.j39_idcons; if not found or nFatorLocalizacao = 0 then perform fc_debug(\'< calculo vvc > Matricula \'||rConstr.j39_matric||\' Edificacao \'|| rConstr.j39_idcons||\' fator localizacao da construcao nao encontrado ou zerado para o ano de \'||iAnousu, lRaise); rtp_iptu_calculavvc.rtErro := \' OU SEM VALOR PARA O ANO DE \'||iAnousu; rtp_iptu_calculavvc.rtMsgErro := \'FATOR LOCALIZACAO DA CONSTRUCAO NAO ENCONTRADO OU SEM VALOR PARA O ANO DE \'||iAnousu; rtp_iptu_calculavvc.riCodErro := 37; rtp_iptu_calculavvc.rbErro := \'t\'; return rtp_iptu_calculavvc; end if; /* calcula o valor venal da construcao */ nVvcp := rConstr.j39_area * nVm2c * nFatorIdade * nFatorLocalizacao; nAreatc := nAreatc + rConstr.j39_area; nVvc := nVvc + nVvcp; perform fc_debug(\'< calculo vvc > Valor venal total parcial - nVvc - \'||nVvc||\' nVm2c - \'||nVm2c|| \'area - \'||rConstr.j39_area, lRaise); iNumerocontr := iNumerocontr + 1; insert into tmpiptucale (anousu, matric, idcons, areaed, vm2, pontos, valor) values (iAnousu, iMatricula, rConstr.j39_idcons, rConstr.j39_area, nVm2c, 0, nVvcp); if lAtualiza then update tmpdadosiptu set predial = true; lAtualiza := false; end if; end loop; nVvc := round(nVvc, 2); perform fc_debug(\'< calculo vvc > Valor venal total final - nVvc - \'||nVvc, lRaise); rtp_iptu_calculavvc.rnVvc := nVvc::numeric; rtp_iptu_calculavvc.rnTotarea := nAreatc::numeric; rtp_iptu_calculavvc.riNumconstr := iNumerocontr; rtp_iptu_calculavvc.rtDemo := \'\'; rtp_iptu_calculavvc.rbErro := \'f\'; update tmpdadosiptu set vvc = rtp_iptu_calculavvc.rnVvc; return rtp_iptu_calculavvc; end; $$ language \'plpgsql\'; ' ,'0' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1222 ,231 ,1 ,'iMatricula' ,'int4' ,0 ,0 ,'0' ,'MATRICULA DO IMOVEL' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1223 ,231 ,2 ,'iAnousu' ,'int4' ,0 ,0 ,'0' ,'ANO DE CALCULO' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1224 ,231 ,3 ,'lMostrademo' ,'bool' ,0 ,0 ,'FALSE' ,'VARIAVEL DE CONTROLE PARA GERAR DEMONSTRATIVO' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1225 ,231 ,4 ,'lRaise' ,'bool' ,0 ,0 ,'FALSE' ,'VARIAVEL PARA DEBUG' );
            insert into db_sysfuncoes( codfuncao ,nomefuncao ,nomearquivo ,obsfuncao ,corpofuncao ,triggerfuncao ) values ( 232 ,'fc_calculoiptu_valenca_2023' ,'calculoiptu_valenca_2023.sql' ,'Cálculo IPTU de Valença' ,'CREATE OR REPLACE FUNCTION fc_calculoiptu_valenca_2023(integer,integer,boolean,boolean,boolean,boolean,boolean,integer,integer) RETURNS varchar(100) AS $$ declare iMatricula alias for $1; iAnousu alias for $2; lGerafinanc alias for $3; lAtualizaParcela alias for $4; lNovonumpre alias for $5; lCalculogeral alias for $6; lDemonstrativo alias for $7; iParcelaini alias for $8; iParcelafim alias for $9; iIdbql integer default 0; iNumcgm integer default 0; iCodcli integer default 0; iCodisen integer default 0; iTipois integer default 0; iParcelas integer default 0; iNumconstr integer default 0; iCodErro integer default 0; dDatabaixa date; nAreal numeric default 0; nAreac numeric default 0; nTotarea numeric default 0; nFracao numeric default 0; nFracaolote numeric default 0; nAliquota numeric default 0; nIsenaliq numeric default 0; nArealo numeric default 0; nVvc numeric(15,2) default 0; nVvt numeric(15,2) default 0; nVv numeric(15,2) default 0; nViptu numeric(15,2) default 0; tRetorno text default \'\'; tDemo text default \'\'; tErro text default \'\'; lPredial boolean; lFinanceiro boolean; lDadosIptu boolean; lErro boolean; lIsentaxas boolean; lTempagamento boolean; lEmpagamento boolean; lTaxasCalculadas boolean; lRaise boolean default false; -- true para habilitar raise na funcao principal lSubRaise boolean default false; -- true para habilitar raise nas sub-funcoes rCfiptu record; begin lRaise := ( case when fc_getsession(\'DB_debugon\') is null then false else true end ); lSubRaise := lRaise; perform fc_debug(\'INICIANDO CALCULO\',lRaise,true,false); perform fc_debug(\'\',lRaise,true,false); /** * Guarda os parametros do calculo */ select * from into rCfiptu cfiptu where j18_anousu = iAnousu; select j34_area, coalesce(j34_totcon, 0) into nArealo, nTotarea from iptubase join lote on j34_idbql = j01_idbql where j01_matric = iMatricula; if not found then select fc_iptu_geterro( 3, \'ERRO - Sem dados para a matricula \'||iMatricula ) into tRetorno; return tRetorno; end if; /** * Executa PRE CALCULO */ select r_iIdbql, r_nAreal, r_nFracao, r_iNumcgm, r_dDatabaixa, r_nFracaolote, r_tDemo, r_lTempagamento, r_lEmpagamento, r_iCodisen, r_iTipois, r_nIsenaliq, r_lIsentaxas, r_nArealote, r_iCodCli, r_tRetorno into iIdbql, nAreal, nFracao, iNumcgm, dDatabaixa, nFracaolote, tDemo, lTempagamento, lEmpagamento, iCodisen, iTipois, nIsenaliq, lIsentaxas, nArealo, iCodCli, tRetorno from fc_iptu_precalculo( iMatricula, iAnousu, lCalculogeral, lAtualizaParcela, lDemonstrativo, lRaise ); perform fc_debug(\' RETORNO DA PRE CALCULO: \', lRaise); perform fc_debug(\' iIdbql -> \' || iIdbql, lRaise); perform fc_debug(\' nAreal -> \' || nAreal, lRaise); perform fc_debug(\' nFracao -> \' || nFracao, lRaise); perform fc_debug(\' iNumcgm -> \' || iNumcgm, lRaise); perform fc_debug(\' dDatabaixa -> \' || dDatabaixa, lRaise); perform fc_debug(\' nFracaolote -> \' || nFracaolote, lRaise); perform fc_debug(\' tDemo -> \' || tDemo, lRaise); perform fc_debug(\' lTempagamento -> \' || lTempagamento, lRaise); perform fc_debug(\' lEmpagamento -> \' || lEmpagamento, lRaise); perform fc_debug(\' iCodisen -> \' || iCodisen, lRaise); perform fc_debug(\' iTipois -> \' || iTipois, lRaise); perform fc_debug(\' nIsenaliq -> \' || nIsenaliq, lRaise); perform fc_debug(\' lIsentaxas -> \' || lIsentaxas, lRaise); perform fc_debug(\' nArealote -> \' || nArealo, lRaise); perform fc_debug(\' iCodCli -> \' || iCodCli, lRaise); perform fc_debug(\' tRetorno -> \' || tRetorno, lRaise); perform fc_debug(\'\',lRaise,true,false); /** * Variavel de retorno contem a msg * de erro retornada do pre calculo */ if trim(tRetorno) <> \'\' then return tRetorno; end if; update tmpdadosiptu set matric = iMatricula; update tmpdadostaxa set anousu = iAnousu, matric = iMatricula, idbql = iIdbql, valref = rCfiptu.j18_vlrref; /** * Calcula valor do terreno */ perform fc_debug(\'PARAMETROS fc_iptu_calculavvt_valenca_2023 IDBQL: \'||iIdbql||\' - iMatricula: \'||iMatricula||\' - Anousu: \'||iAnousu||\' - FRACAO DO LOTE: \'||nFracaolote||\' - DEMO: \'||lDemonstrativo||\' - DEBUG: \'||lRaise, lRaise); select rnvvt, rnarea, rtdemo, rtmsgerro, rberro, riCodErro, rtErro into nVvt, nAreac, tDemo, tRetorno, lErro, iCodErro, tErro from fc_iptu_calculavvt_valenca_2023( iIdbql, iMatricula, iAnousu, nFracaolote, lDemonstrativo, lRaise); perform fc_debug(\'RETORNO fc_iptu_calculavvt_valenca_2023 -> VVT: \'||nVvt||\' - AREA CONSTRUIDA: \'||nAreac||\' - RETORNO: \'||tRetorno||\' - ERRO: \'||lErro, lRaise); perform fc_debug(\'\', lRaise); if lErro is true then select fc_iptu_geterro( iCodErro, tErro ) into tRetorno; return tRetorno; end if; /** * Calcula valor da construcao */ perform fc_debug(\'PARAMETROS fc_iptu_calculavvc_valenca_2023 MATRICULA: \'||iMatricula||\' - ANOUSU: \'||iAnousu||\' - DEMO: \'||lDemonstrativo||\' - DEBUG: \'||lRaise, lRaise); select rnvvc, rntotarea, rinumconstr, rtdemo, rtmsgerro, rberro, riCodErro, rtErro into nVvc, nTotarea, iNumconstr, tDemo, tRetorno, lErro, iCodErro, tErro from fc_iptu_calculavvc_valenca_2023( iMatricula, iAnousu, lDemonstrativo, lRaise ); perform fc_debug(\'RETORNO fc_iptu_calculavvc_valenca_2023 -> VVC: \'||nVvc||\' - AREA TOTAL: \'||nTotarea||\' - NUMERO DE CONSTRUCOES: \'||iNumconstr||\' - RETORNO: \'||tRetorno||\' - ERRO: \'||lErro, lRaise); perform fc_debug(\'\', lRaise); if lErro is true then select fc_iptu_geterro(iCodErro, tErro) into tRetorno; return tRetorno; end if; select predial into lPredial from tmpdadosiptu; /* BUSCA A ALIQUOTA */ perform fc_debug(\'BUSCA A ALIQUOTA DO IPTU \', lRaise); select coderro, descrerro, aliquota into iCodErro, tErro, nAliquota from fc_iptu_getaliquota_valenca_2023(iMatricula, iIdbql, iAnousu, lPredial, lSubRaise); if nAliquota = 0 then select fc_iptu_geterro( iCodErro, tErro ) into tRetorno; return tRetorno; end if; perform fc_debug(\'RETORNO DA BUSCA A ALIQUOTA DO IPTU \', lRaise); perform fc_debug(\' \', lRaise); /*--------- CALCULA O VALOR VENAL -----------*/ perform fc_debug(\'valor venal construcao (nVvc) - \'||nVvc||\' valor venal terreno (nVvt) - \'||nVvt, lRaise); nVv := nVvc + nVvt; perform fc_debug(\'valor venal total - \'||nVv, lRaise); nViptu := nVv * ( nAliquota / 100 ); if nViptu < rCfiptu.j18_vlrref then perform fc_debug(\'Valor do IPTU menor que a UFIVA\', lRaise); nViptu := rCfiptu.j18_vlrref; end if; perform fc_debug(\'valor iptu \'||nViptu||\' - aliquota \'||nAliquota||\'%\', lRaise); perform fc_debug(\' \', lRaise); perform fc_debug(\'Inserindo as receitas de IPTU na tabela tmprecval \', lRaise); perform fc_debug(\' \', lRaise); if lPredial then insert into tmprecval values (rCfiptu.j18_rpredi, nViptu, 1, false); else insert into tmprecval values (rCfiptu.j18_rterri, nViptu, 1, false); end if; perform fc_debug(\'Inserindo valor e codigo de vencimento do IPTU na tabela tmpdadosiptu\', lRaise); perform fc_debug(\' \', lRaise); update tmpdadosiptu set viptu = nViptu, codvenc = rCfiptu.j18_vencim; /*-------------------------------------------*/ select count(*) into iParcelas from cadvencdesc inner join cadvenc on q92_codigo = q82_codigo where q92_codigo = rCfiptu.j18_vencim ; if not found or iParcelas = 0 then select fc_iptu_geterro(14,\'\') into tRetorno; return tRetorno; end if; update tmpdadostaxa set valiptu = nViptu, vvt = nVvt, nparc = iParcelas, totareaconst = nTotarea; /* CALCULA AS TAXAS */ perform fc_debug(\'PARAMETROS fc_iptu_calculataxas ANOUSU \'||iAnousu||\' -- CODCLI \'||iCodcli||\' -- Debug: \'||lSubRaise, lRaise); perform fc_debug(\' \', lRaise); select fc_iptu_calculataxas(iMatricula, iAnousu, iCodcli, lSubRaise) into lTaxasCalculadas; perform fc_debug(\'RETORNO fc_iptu_calculataxas --->>> TAXAS CALCULADAS - \'||lTaxasCalculadas, lRaise); /* MONTA O DEMONSTRATIVO */ select fc_iptu_demonstrativo(iMatricula,iAnousu,iIdbql,lSubRaise ) into tDemo; /* GERA FINANCEIRO */ if lDemonstrativo is false then -- Se nao for demonstrativo gera o financeiro, caso contrario retorna o demonstrativo select fc_iptu_geradadosiptu(iMatricula,iIdbql,iAnousu,nIsenaliq,lDemonstrativo,lSubRaise) into lDadosIptu; if lGerafinanc then select fc_iptu_gerafinanceiro(iMatricula,iAnousu,iParcelaini,iParcelafim,lCalculogeral,lTempagamento,lNovonumpre,lDemonstrativo,lSubRaise) into lFinanceiro; end if; else return tDemo; end if; if lDemonstrativo is false then update iptucalc set j23_manual = tDemo where j23_matric = iMatricula and j23_anousu = iAnousu; end if; select fc_iptu_geterro(1, \'\') into tRetorno; return tRetorno; end; $$ LANGUAGE \'plpgsql\'; ' ,'0' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1226 ,232 ,1 ,'iMatricula' ,'int4' ,0 ,0 ,'0' ,'MATRICULA' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1227 ,232 ,2 ,'iAnousu' ,'int4' ,0 ,0 ,'0' ,'ANO DE CALCULO' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1228 ,232 ,3 ,'lGerafinanceiro' ,'bool' ,0 ,0 ,'FALSE' ,'VARIAVEL DE CONTROLE PARA GERAR FINANCEIRO' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1229 ,232 ,4 ,'lAtualizaParcela' ,'bool' ,0 ,0 ,'FALSE' ,'VARIAVEL DE CONTROLE PARA ATUALIZAR PARCELAS' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1230 ,232 ,5 ,'lNovonumpre' ,'bool' ,0 ,0 ,'FALSE' ,'VARIAVEL DE CONTROLE PARA GERAR UM NOVO NUMPRE' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1238 ,232 ,6 ,'bCalculogeral' ,'bool' ,0 ,0 ,'FALSE' ,'SE CALCULO GERAL' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1239 ,232 ,7 ,'bDemo' ,'bool' ,0 ,0 ,'FALSE' ,'SE E DEMONSTRATIVO' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1240 ,232 ,8 ,'iParcelaini' ,'int4' ,0 ,0 ,'0' ,'PARCELA INICIAL' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1241 ,232 ,9 ,'iParcelafim' ,'int4' ,0 ,0 ,'0' ,'PARCELA FINAL' );
            insert into db_sysfuncoes( codfuncao ,nomefuncao ,nomearquivo ,obsfuncao ,corpofuncao ,triggerfuncao ) values ( 233 ,'fc_iptu_getareaconstrloteidbql' ,'iptu_getareaconstrloteidbql.sql' ,'Função para buscar a área total construída' ,'drop function if exists fc_iptu_getareaconstrloteidbql(integer, integer); create or replace function cadastro.fc_iptu_getareaconstrloteidbql(integer, integer) returns numeric as $$ declare iIdbql alias for $1; iAnousu alias for $2; nTotalAreaConstruida numeric default 0; sSql varchar := \'\'; bConstrucaoIrregular boolean default false; begin select true into bConstrucaoIrregular from db_config where db21_codigomunicipoestado = \'3306107\' and prefeitura is true; sSql := \'with matriculas as (\'; sSql := sSql || \'select j39_matric, j39_idcons, j39_area \'; sSql := sSql || \'from iptubase \'; sSql := sSql || \' join iptuconstr on j39_matric = j01_matric \'; sSql := sSql || \'where j01_baixa is null \'; sSql := sSql || \' and j01_idbql = \' || iIdbql; sSql := sSql || \' and j39_dtdemo is null), \'; if bConstrucaoIrregular then sSql := sSql || \'matriculas_irregulares as (\'; sSql := sSql || \' select j39_matric as matric, j39_idcons as idcons \'; sSql := sSql || \' from iptubase \'; sSql := sSql || \' join iptuconstr on j39_matric = j01_matric \'; sSql := sSql || \' join carconstr on j48_matric = j39_matric \'; sSql := sSql || \' and j48_idcons = j39_idcons \'; sSql := sSql || \' join caracter on j31_codigo = j48_caract \'; sSql := sSql || \' and j31_grupo = 7200 \'; sSql := sSql || \' join carfator on j74_anousu = \' || iAnousu; sSql := sSql || \' and j74_caract = j48_caract \'; sSql := sSql || \' where j01_baixa is null \'; sSql := sSql || \' and j01_idbql = \' || iIdbql; sSql := sSql || \' and j39_dtdemo is null), \'; sSql := sSql || \'matriculas_ativas as (\'; sSql := sSql || \' select j39_matric, j39_idcons, j39_area \'; sSql := sSql || \' from matriculas \'; sSql := sSql || \' left join matriculas_irregulares on matric = j39_matric \'; sSql := sSql || \' and idcons = j39_idcons \'; sSql := sSql || \' where matric is null) \'; else sSql := sSql || \'matriculas_ativas as (\'; sSql := sSql || \' select j39_matric, j39_idcons, j39_area \'; sSql := sSql || \' from matriculas) \'; end if; sSql := sSql || \'select round(coalesce(sum(j39_area), 0), 2)::numeric \'; sSql := sSql || \'from matriculas_ativas; \'; execute sSql into nTotalAreaConstruida; return nTotalAreaConstruida; end; $$ language \'plpgsql\'; ' ,'0' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1231 ,233 ,1 ,'iIdbql' ,'int4' ,0 ,0 ,'0' ,'CODIGO DO IDBQL' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1232 ,233 ,2 ,'iAnousu' ,'int4' ,0 ,0 ,'0' ,'ANO DE CALCULO' );
            insert into db_sysfuncoes( codfuncao ,nomefuncao ,nomearquivo ,obsfuncao ,corpofuncao ,triggerfuncao ) values ( 234 ,'fc_iptu_criatemptable' ,'iptu_criatemptable.sql' ,'Função que cria as tabelas temporárias para os cálculos' ,'create or replace function fc_iptu_criatemptable(boolean) returns boolean as $$ declare lRaise alias for $1; rbErro boolean default false; nome name; begin /** * FUNCAO PARA CRIAR AS TABELAS TEMPORARIAS */ perform fc_debug(\'\', lRaise); perform fc_debug(\' <iptu_criatemptable> INICIANDO CRIACAO DE ESTRUTURAS TEMPORARIAS...\', lRaise); begin /* * NAO REMOVER CAMPOS DESSAS TABELAS, ESSA ALTERACAO PODE CAUSAR PROBLEMAS EM TODOS OS CALCULOS * QUANDO USAR AS TABELAS TEMPORARIAS NAO USE SELECT * INTO VAI1, VAR2,VAR3 FROM XXX. * USE: SELECT CAMPO1,CAMPO2,CAMPO3 INTO VAR1, VAR2,VAR3 FROM XXXX. */ /** * Tabela que guarda as receitas e valores das mesmas, para gerar o financeiro(arrecad) */ create temporary table tmprecval( \"receita\" integer,\"valor\" numeric,\"hist\" integer,\"taxa\" boolean,\"aliq\" numeric ); perform fc_debug(\' <iptu_criatemptable> TABELA TMPRECVAL CRIADA\', lRaise); /** * Tabela que guarda os dados referente ao comportamento do calculo durante o processamento das sub-funcoes */ create temporary table tmpdadosiptu( \"aliq\" numeric, \"vvc\" numeric, \"vvt\" numeric, \"viptu\" numeric, \"fracao\" numeric, \"areat\" numeric, \"predial\" boolean, \"codvenc\" integer, \"tipoisen\" integer, \"vm2t\" numeric, \"testada\" numeric, \"matric\" integer, \"isentaxas\" boolean ); insert into tmpdadosiptu values (0,0,0,0,0,0,false,0,0,0,0,0); perform fc_debug(\' <iptu_criatemptable> TABELA TMPDADOSIPTU CRIADA\', lRaise); /** * Tabela que guarda os dados das contrucoes calculadas, alimentada pela fc_iptu_calculavvc */ create temporary table tmpiptucale( \"anousu\" integer, \"matric\" integer, \"idcons\" integer, \"areaed\" numeric, \"vm2\" numeric, \"pontos\" integer, \"valor\" numeric, \"edificacao\" boolean, \"caracteristica\" integer, \"aliquota\" numeric ); perform fc_debug(\' <iptu_criatemptable> TABELA TMPIPTUCALE CRIADA\', lRaise); /** * Tabela que guarda os valores para calcular as taxas */ create temporary table tmpdadostaxa( \"anousu\" integer, \"matric\" integer, \"zona\" integer, \"idbql\" integer, \"nparc\" integer, \"valiptu\" numeric, \"valref\" numeric, \"vvt\" numeric, \"totareaconst\" numeric ); insert into tmpdadostaxa values (0,0,0,0,0,0,0,0,0); perform fc_debug(\' <iptu_criatemptable> TABELA TMPDADOSTAXA CRIADA\', lRaise); /** * Tabela com os parametros para o comportamento da fase do calculo que gera o financeiro */ create temporary table tmpfinanceiro(\"anousu\" integer,\"matric\" integer,\"idbql\" integer,\"valiptu\" numeric,\"valref\" numeric,\"vvt\" numeric); insert into tmpfinanceiro values (0,0,0,0,0,0); perform fc_debug(\' <iptu_criatemptable> TABELA TMPFINANCEIRO CRIADA\', lRaise); /** * Tabela que guarda as receitas e percentual de isencao das taxas */ create temporary table tmptaxapercisen(\"rectaxaisen\" integer,\"percisen\" numeric, \"histcalcisen\" integer,\"valsemisen\" numeric); perform fc_debug(\' <iptu_criatemptable> TABELA TMPTAXAPERCISEN CRIADA\', lRaise); /** * Tabela que guarda os valores para \"outras\" taxas (taxa bombeiro, limpeza) */ create temporary table tmpoutrosvalores(\"valor\" numeric,\"descricao\" varchar); perform fc_debug(\' <iptu_criatemptable> TABELA TMPTAXAPERCISEN CRIADA\', lRaise); /** * Tabela que guarda os valores de vencimentos */ create temporary table tmp_cadvenc as select q92_codigo, q92_tipo, q92_hist, q92_vlrminimo, q82_parc, q82_venc, q82_perc, q82_hist from cadvencdesc inner join cadvenc on q92_codigo = q82_codigo limit 0; perform fc_debug(\' <iptu_criatemptable> TABELA TMP_CADVENC CRIADA\', lRaise); /** * Tabela para guardar o numpre gerado na diversos (iptu_complementar) */ create temporary table tmpipturecalculonump ( matricula integer, anousu integer, numpre integer ); perform fc_debug(\' <iptu_criatemptable> TABELA TMPIPTURECALCULONUMP CRIADA\', lRaise); /** * Tabela para guardar o numpre gerado na diversos (iptu_complementar) */ create temporary table tmpipturecalculocreditonump ( matricula integer, anousu integer, numpre integer ); perform fc_debug(\' <iptu_criatemptable> TABELA TMPIPTURECALCULOCREDITONUMP CRIADA\', lRaise); exception when duplicate_table then truncate tmprecval; truncate tmpdadosiptu; truncate tmpiptucale; truncate tmpdadostaxa; truncate tmpfinanceiro; truncate tmptaxapercisen; truncate tmpoutrosvalores; truncate tmp_cadvenc; truncate tmpipturecalculonump; truncate tmpipturecalculocreditonump; insert into tmpdadosiptu values (0,0,0,0,0,0,false,0,0,0,0,0,false); insert into tmpdadostaxa values (0,0,0,0,0,0,0,0,0); insert into tmpfinanceiro values (0,0,0,0,0,0); end; perform fc_debug(\' <iptu_criatemptable> FIM CRIACAO DE ESTRUTURAS TEMPORARIAS\', lRaise); perform fc_debug(\'\', lRaise); return rbErro; end; $$ language \'plpgsql\';' ,'0' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1233 ,234 ,1 ,'lRaise' ,'bool' ,0 ,0 ,'FALSE' ,'VARIAVEL PARA DEBUG' );
            insert into db_sysfuncoes( codfuncao ,nomefuncao ,nomearquivo ,obsfuncao ,corpofuncao ,triggerfuncao ) values ( 235 ,'fc_iptu_fracionalote' ,'iptu_fracionalote.sql' ,'Função para calcular o fracionamento dos lotes' ,'drop function if exists fc_iptu_fracionalote(integer,integer,boolean,boolean); drop function if exists fc_iptu_fracionalote(integer,integer,boolean,boolean,boolean); drop type if exists tp_iptu_fracionalote; create type cadastro.tp_iptu_fracionalote as (rnFracao numeric, rtDemo text, rtMsgerro text, rbErro boolean); create or replace function cadastro.fc_iptu_fracionalote(integer,integer,boolean,boolean) returns tp_iptu_fracionalote as $$ declare iMatricula alias for $1; iAnousu alias for $2; bMostrademo alias for $3; lRaise alias for $4; rtp_iptu_fracionalote tp_iptu_fracionalote%ROWTYPE; begin rtp_iptu_fracionalote.rnFracao := 0; rtp_iptu_fracionalote.rtDemo := \'\'; rtp_iptu_fracionalote.rtMsgerro := \'\'; rtp_iptu_fracionalote.rbErro := \'f\'; select * into rtp_iptu_fracionalote from fc_iptu_fracionalote(iMatricula, iAnousu, bMostrademo, lRaise, true); return rtp_iptu_fracionalote; end; $$ language \'plpgsql\'; create or replace function cadastro.fc_iptu_fracionalote(integer,integer,boolean,boolean,boolean) returns tp_iptu_fracionalote as $$ declare iMatricula alias for $1; iAnousu alias for $2; bMostrademo alias for $3; --Não utilizada no escopo lRaise alias for $4; lAtualizaFracaoForcada alias for $5; cSetor char(4); cQuadra char(4); cLote char(4); iIptufrac integer; iTotalMatriculas integer; iIdbql integer default 0; nTotalAreaConstruida numeric; rnFracao numeric default 0; nAreacalc numeric default 0; nJ01_fracao numeric default 0; lFracionaIdbql boolean; tManual text default \'\'; rFracao record; rtp_iptu_fracionalote tp_iptu_fracionalote%ROWTYPE; begin perform fc_debug(\'\', lRaise); perform fc_debug(\' <fracionalote> INICIANDO FRACIONAMENTO DO LOTE...\', lRaise); select j18_fracionaidbql into lFracionaIdbql from cadastro.cfiptu where j18_anousu = iAnousu; rtp_iptu_fracionalote.rnFracao := 0; rtp_iptu_fracionalote.rtDemo := \'\'; rtp_iptu_fracionalote.rtMsgerro := \'\'; rtp_iptu_fracionalote.rbErro := \'f\'; select j01_idbql, j34_setor, j34_quadra, j34_lote into iIdbql, cSetor, cQuadra, cLote from iptubase join lote on j34_idbql = j01_idbql where j01_matric = iMatricula; /* * Conta quantas Matriculas tem para o lote da Matricula a ser calculada */ if lFracionaIdbql then perform fc_debug(\' <fracionalote> Fracionamento por Idbql: \'||iIdbql, lRaise); select count(j01_idbql) into iTotalMatriculas from iptubase join lote on j34_idbql = j01_idbql where j01_baixa is null and j34_idbql = iIdbql; else perform fc_debug(\' <fracionalote> fracionamento por Setor: \'||cSetor||\' - Quadra: \'||cQuadra||\' Lote: \'||cLote, lRaise); select count(j01_idbql) into iTotalMatriculas from iptubase join lote on j34_idbql = j01_idbql where j01_baixa is null and j34_setor = cSetor and j34_quadra = cQuadra and j34_lote = cLote; end if; perform fc_debug(\' <fracionalote> iMatricula : \' || iMatricula, lRaise); perform fc_debug(\' <fracionalote> fracao : \' || rnFracao, lRaise); perform fc_debug(\' <fracionalote> total de iMatriculas: \' || iTotalMatriculas, lRaise); if iTotalMatriculas = 1 then if rnFracao is null or rnFracao = 0 then rnFracao = 100::numeric; else perform fc_debug(\' <fracionalote> Calculando area construida da iMatricula... \' || iMatricula, lRaise); /* * Retorna a area total construida da MATRICULA */ select into nAreacalc fc_iptu_getareaconstrmat( iMatricula ); perform fc_debug(\' <fracionalote> Fracao de novo: \' || rnFracao, lRaise); perform fc_debug(\' <fracionalote> fracaocalc: \' || nAreacalc, lRaise); if nAreacalc is null or nAreacalc = 0 then rnFracao = 100; else rnFracao = ( (nAreacalc / rnFracao ) * 100 ); perform fc_debug(\' <fracionalote> nAreacalc: \'||nAreacalc||\' - rnFracao: \' || rnFracao, lRaise); end if; end if; else /* * Retorna a area total construida do LOTE */ if lFracionaIdbql then select into nTotalAreaConstruida fc_iptu_getareaconstrloteidbql(iIdbql, iAnousu); perform fc_debug(\' <fracionalote> Busca area total construida por idbql: \'||nTotalAreaConstruida, lRaise); else select into nTotalAreaConstruida fc_iptu_getareaconstrlote(cSetor,cQuadra,cLote); perform fc_debug(\' <fracionalote> Busca area total construida por Setor/Quadra/Lote: \'||nTotalAreaConstruida, lRaise); end if; perform fc_debug(\' <fracionalote> Total construido no lote: \' || nTotalAreaConstruida, lRaise); tManual := tManual || \'total construido no lote: \' || nTotalAreaConstruida || \' - \'; if nTotalAreaConstruida = 0 then select j01_fracao into nJ01_fracao from iptubase where j01_matric = iMatricula; if nJ01_fracao = 0 or nJ01_fracao is null then if lAtualizaFracaoForcada then update iptubase set j01_fracao = 0 where j01_idbql = iIdbql; end if; rnFracao = 100::numeric; else rnFracao = nJ01_fracao; end if; else perform fc_debug(\' <fracionalote> Fraciona rFracao \', lRaise); for rFracao in select j01_matric, sum(j39_area) as j39_area from iptubase join iptuconstr on j39_matric = j01_matric where j01_baixa is null and j39_dtdemo is null and j01_matric = iMatricula group by j01_matric loop perform fc_debug(\' <fracionalote> processando fracao iMatricula: \'||coalesce(rFracao.j01_matric,0)||\' - construido desta: \' || coalesce(rFracao.j39_area,0), lRaise ); select j25_matric into iIptufrac from iptufrac where j25_matric = rFracao.j01_matric and j25_anousu = iAnousu; perform fc_debug(\' <fracionalote> iptufrac: \' || coalesce( iIptufrac, 0 ), lRaise); if iIptufrac is null or iIptufrac = 0 then perform fc_debug(\' <fracionalote> insert no iptufrac\', lRaise); insert into iptufrac values (iAnousu, rFracao.j01_matric, iIdbql, rFracao.j39_area / nTotalAreaConstruida * 100); else perform fc_debug(\' <fracionalote> update no iptufrac\', lRaise); update iptufrac set j25_fracao = rFracao.j39_area / nTotalAreaConstruida * 100, j25_idbql = iIdbql where j25_matric = rFracao.j01_matric and j25_anousu = iAnousu; end if; end loop; select j25_fracao into rnFracao from iptufrac where j25_matric = iMatricula and j25_anousu = iAnousu; if rnFracao is null or rnFracao = 0 then rnFracao = 100::numeric; end if; end if; end if; select j01_fracao into nJ01_fracao from iptubase where j01_matric = iMatricula; if nJ01_fracao is not null and nJ01_fracao > 0 then rnFracao = nJ01_fracao; end if; rtp_iptu_fracionalote.rnFracao := rnFracao; rtp_iptu_fracionalote.rtDemo := tManual; perform fc_debug(\' <fracionalote> texto demonstrativo :\' || tManual, lRaise); perform fc_debug(\' <fracionalote> FIM FRACIONAMENTO DO LOTE\', lRaise); perform fc_debug(\' \', lRaise); return rtp_iptu_fracionalote; end; $$ language \'plpgsql\';' ,'0' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1234 ,235 ,1 ,'iMatricula' ,'int4' ,0 ,0 ,'0' ,'CODIGO DA MATRICULA' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1235 ,235 ,2 ,'iAnousu' ,'int4' ,0 ,0 ,'0' ,'ANO DE CALCULO' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1236 ,235 ,3 ,'bMostrademo' ,'bool' ,0 ,0 ,'FALSE' ,'VARIAVEL DE CONTROLE PARA GERAR DEMONSTRATIVO' );
            insert into db_sysfuncoesparam( db42_sysfuncoesparam ,db42_funcao ,db42_ordem ,db42_nome ,db42_tipo ,db42_tamanho ,db42_precisao ,db42_valor_default ,db42_descricao ) values ( 1237 ,235 ,4 ,'lRaise' ,'bool' ,0 ,0 ,'FALSE' ,'VARIAVEL PARA DEBUG' );

SQL
        );
    }

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL

            delete from db_sysfuncoesparam where db42_funcao between 229 and 235;
            delete from db_sysfuncoes where codfuncao between 229 and 235;

SQL
        );
    }

    private function upTabelas()
    {
        DB::connection()->getPdo()->exec(<<<SQL

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            ALTER TABLE cadastro.cfiptu ADD column j18_fracionaidbql boolean DEFAULT false;
            SELECT fc_atualiza_dicionario_apartir_comentario('table column',
                   'cadastro.cfiptu.j18_fracionaidbql',
                   '{ "descricao": "Calcula Fração do Lote pelo Idbql",
                      "rotulo": "Fraciona por Idbql",
                      "rotulorel": "Fraciona por Idbql",
                      "maiusculo": false,
                      "autocompl": false,
                      "aceitatipo": 5,
                      "tamanho": 1,
                      "tipoobj": "text"
                    }') ;

            ALTER TABLE cadastro.setorfiscalvalor ADD column j82_caract integer;
            ALTER TABLE ONLY cadastro.setorfiscalvalor ADD CONSTRAINT setorfiscalvalor_caract_fk
                FOREIGN KEY (j82_caract) REFERENCES cadastro.caracter(j31_codigo) MATCH FULL DEFERRABLE;
            SELECT fc_atualiza_dicionario_apartir_comentario('table column',
                   'cadastro.setorfiscalvalor.j82_caract',
                   '{ "descricao": "Caracteristica do imóvel",
                      "rotulo": "Caracteristica do imóvel",
                      "rotulorel": "Caracteristica do imóvel",
                      "maiusculo": false,
                      "autocompl": false,
                      "aceitatipo": 1,
                      "tamanho": 10,
                      "tipoobj": "text"
                    }') ;

            DROP INDEX IF EXISTS setorfiscalvalor_setorfiscal_ano_in;
            CREATE UNIQUE INDEX IF NOT EXISTS setorfiscalvalor_setorfiscal_ano_caract_in ON setorfiscalvalor (j82_setorfiscal, j82_anousu, j82_caract);

            SELECT fc_atualiza_dicionario_apartir_comentario('table column',
                   'cadastro.cfiptu.j18_validarano',
                   '{ "descricao": "Validação do campo j39_ano",
                      "rotulo": "Validação do campo j39_ano",
                      "rotulorel": "Validação do campo j39_ano",
                      "maiusculo": false,
                      "autocompl": false,
                      "aceitatipo": 5,
                      "tamanho": 1,
                      "tipoobj": "text"
                    }') ;

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;

SQL
        );
    }

    private function downTabelas()
    {
        DB::connection()->getPdo()->exec(<<<SQL

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            ALTER TABLE cadastro.cfiptu DROP column j18_fracionaidbql;
            ALTER TABLE cadastro.setorfiscalvalor DROP column j82_caract;

            DROP INDEX IF EXISTS setorfiscalvalor_setorfiscal_ano_caract_in;
            CREATE UNIQUE INDEX IF NOT EXISTS setorfiscalvalor_setorfiscal_ano_in ON setorfiscalvalor (j82_setorfiscal, j82_anousu);

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;

SQL
        );
    }

    private function upFuncoes()
    {
        DB::connection()->getPdo()->exec(<<<SQL

create or replace function cadastro.fc_iptu_calculavvc_valenca_2023(integer,integer,boolean,boolean)
    returns tp_iptu_calculavvc as
$$

    declare

        iMatricula          alias for $1;
        iAnousu             alias for $2;
        lMostrademo         alias for $3;
        lRaise              alias for $4;

        nAreatc             numeric default 0;
        nVm2c               numeric default 0;
        nVvcP               numeric default 0;
        nVvc                numeric default 0;
        nFatorIdade         numeric default 0;
        nFatorLocalizacao   numeric default 0;

        iNumerocontr        integer default 0;
        iPontos             integer default 0;
        iTotalPontos        integer default 0;
        iTipoConstrucao     integer default 0;

        lAtualiza           boolean default true;

        rConstr             record;
        rCargrup            record;

        jCargrup            json;

        tMsg                text;

        rtp_iptu_calculavvc tp_iptu_calculavvc%ROWTYPE;

    begin

        perform fc_debug('INICIANDO CALCULO VVC ...', lRaise);

        rtp_iptu_calculavvc.rnVvc       := 0;
        rtp_iptu_calculavvc.rnTotarea   := 0;
        rtp_iptu_calculavvc.riNumconstr := 0;
        rtp_iptu_calculavvc.rtDemo      := '';
        rtp_iptu_calculavvc.rtMsgerro   := 'Retorno ok' ;
        rtp_iptu_calculavvc.rbErro      := 'f';
        rtp_iptu_calculavvc.riCodErro   := 0;
        rtp_iptu_calculavvc.rtErro      := '';

        /* busca os grupos de caracteristicas com pontuacao */
        select json_agg(cargrup.*)
          into jCargrup
        from cargrup
        where j32_grupo in (1700, 1800, 1900, 2000, 2100, 2200, 2300, 2400, 7800, 7900, 8000, 8100);

        iNumerocontr := 0;

        for rConstr in select j39_matric, j39_idcons, j39_idprinc, j39_area, j39_areap, coalesce(j39_ano, 0) as j39_ano
                      from iptuconstr
                     where j39_matric = iMatricula
                       and j39_dtdemo is null
        loop

                /* busca os pontos de cada construcao */
                iTotalPontos := 0;
                for rCargrup in select (value->>'j32_grupo')::int as codigo, value->>'j32_descr' as descricao
                                from json_array_elements(jCargrup)
                loop
                        select coalesce(j31_pontos, 0)
                        into iPontos
                        from carconstr
                             join caracter on j31_codigo = j48_caract
                                          and j31_grupo  = rCargrup.codigo
                        where j48_matric = rConstr.j39_matric
                          and j48_idcons = rConstr.j39_idcons;

                        if not found then
                            tMsg := ' PARA O GRUPO '||rCargrup.codigo||' - '||rCargrup.descricao;
                            perform fc_debug('< calculo vvc > Matricula '||rConstr.j39_matric||' Edificacao '||
                                             rConstr.j39_idcons||' nao encontrado '||tMsg, lRaise);

                            rtp_iptu_calculavvc.rtErro      := tMsg;
                            rtp_iptu_calculavvc.rtMsgErro   := 'sem caracteristica '||tMsg;
                            rtp_iptu_calculavvc.riCodErro   := 23;
                            rtp_iptu_calculavvc.rbErro      := 't';

                            return rtp_iptu_calculavvc;
                        end if;

                        iTotalPontos := iTotalPontos + iPontos;
                end loop;

                if iTotalPontos = 0 then
                    perform fc_debug('< calculo vvc > Matricula '||rConstr.j39_matric||' Edificacao '||
                                     rConstr.j39_idcons||' sem pontuacao.', lRaise);
                    rtp_iptu_calculavvc.rtErro      := ' PARA O GRUPO 500';
                    rtp_iptu_calculavvc.rtMsgErro   := 'SEM PONTUACAO PARA A EDIFICACAO '||rConstr.j39_idcons;
                    rtp_iptu_calculavvc.riCodErro   := 23;
                    rtp_iptu_calculavvc.rbErro      := 't';
                    return rtp_iptu_calculavvc;
                end if;

                /* busca a caracteristica do grupo 500 - tipo construcao */
                select j48_caract
                  into iTipoConstrucao
                from carconstr
                     join caracter on j31_codigo = j48_caract
                                  and j31_grupo  = 500
                where j48_matric = rConstr.j39_matric
                  and j48_idcons = rConstr.j39_idcons;

                if not found then
                    perform fc_debug('< calculo vvc > Matricula '||rConstr.j39_matric||' Edificacao '||
                                     rConstr.j39_idcons||' nao encontrado a caracteristica do grupo 500.', lRaise);
                    rtp_iptu_calculavvc.rtErro      := ' 500';
                    rtp_iptu_calculavvc.rtMsgErro   := 'SEM CARACTERISITICA PARA O GRUPO 500';
                    rtp_iptu_calculavvc.riCodErro   := 102;
                    rtp_iptu_calculavvc.rbErro      := 't';
                    return rtp_iptu_calculavvc;
                end if;

                /* busca o valor do m2 para a construcao */
                select j71_valor::numeric
                  into nVm2c
                from carvalor
                where j71_anousu = iAnousu
                  and j71_caract = iTipoConstrucao
                  and iTotalPontos between j71_quantini and j71_quantfim;

                if not found then
                    perform fc_debug('< calculo vvc > Matricula '||rConstr.j39_matric||' Edificacao '||
                                     rConstr.j39_idcons||' nao encontrado para caracteristica '||iTipoConstrucao, lRaise);
                    rtp_iptu_calculavvc.rtErro      := ' OU GRUPO 500 NAO ENCONTRADO OU SEM VALOR PARA O ANO DE '||iAnousu;
                    rtp_iptu_calculavvc.rtMsgErro   := 'SEM CARACTERISITICA PARA O GRUPO 500 OU SEM VALOR PARA O ANO DE '||iAnousu;
                    rtp_iptu_calculavvc.riCodErro   := 29;
                    rtp_iptu_calculavvc.rbErro      := 't';
                    return rtp_iptu_calculavvc;
                end if;

                select coalesce(j74_fator, 0)
                  into nFatorIdade
                from carconstr
                     join caracter on j31_codigo = j48_caract
                                  and j31_grupo = 7200
                     join carfator on j74_anousu = iAnousu
                                  and j74_caract = j48_caract
                where j48_matric = rConstr.j39_matric
                  and j48_idcons = rConstr.j39_idcons;

                if not found or nFatorIdade = 0 then
                    /* validar o ano da construcao */
                    if rConstr.j39_ano = 0 then
                        perform fc_debug('< calculo vvc > Matricula '||rConstr.j39_matric||' Edificacao '||
                                         rConstr.j39_idcons||' com ano da construcao invalido.', lRaise);
                        rtp_iptu_calculavvc.rtErro      := '';
                        rtp_iptu_calculavvc.rtMsgErro   := 'ANO DA CONSTRUCAO INVALIDO - CONSTRUCAO: '||rConstr.j39_idcons;
                        rtp_iptu_calculavvc.riCodErro   := 116;
                        rtp_iptu_calculavvc.rbErro      := 't';
                        return rtp_iptu_calculavvc;
                    end if;

                    /* busca fator idade da construcao */
                    select coalesce(j118_fatorreajuste, 0)::numeric
                    into nFatorIdade
                    from iptupadraoconstrpontos
                    where j118_anousu = iAnousu
                      and j118_caracter = iTipoConstrucao
                      and (iAnousu - rConstr.j39_ano) between j118_pontosini and j118_pontosfim;

                    if not found or nFatorIdade = 0 then
                        perform fc_debug('< calculo vvc > Matricula '||rConstr.j39_matric||' Edificacao '||
                                         rConstr.j39_idcons||' fator idade da construcao nao encontrado ou zerado para o ano de '||iAnousu, lRaise);
                        rtp_iptu_calculavvc.rtErro      := ' OU SEM VALOR PARA O ANO DE '||iAnousu;
                        rtp_iptu_calculavvc.rtMsgErro   := 'FATOR IDADE DA CONSTRUCAO NAO ENCONTRADO OU SEM VALOR PARA O ANO DE '||iAnousu;
                        rtp_iptu_calculavvc.riCodErro   := 36;
                        rtp_iptu_calculavvc.rbErro      := 't';
                        return rtp_iptu_calculavvc;
                    end if;
                end if;

                /* busca o fator localizacao da construcao */
                select coalesce(j74_fator, 0)::numeric
                  into nFatorLocalizacao
                from carconstr
                     join caracter on j31_codigo = j48_caract
                                  and j31_grupo = 6700
                     join carfator on j74_anousu = iAnousu
                                  and j74_caract = j48_caract
                where j48_matric = rConstr.j39_matric
                  and j48_idcons = rConstr.j39_idcons;

                if not found or nFatorLocalizacao = 0 then
                    perform fc_debug('< calculo vvc > Matricula '||rConstr.j39_matric||' Edificacao '||
                                     rConstr.j39_idcons||' fator localizacao da construcao nao encontrado ou zerado para o ano de '||iAnousu, lRaise);
                    rtp_iptu_calculavvc.rtErro    := ' OU SEM VALOR PARA O ANO DE '||iAnousu;
                    rtp_iptu_calculavvc.rtMsgErro := 'FATOR LOCALIZACAO DA CONSTRUCAO NAO ENCONTRADO OU SEM VALOR PARA O ANO DE '||iAnousu;
                    rtp_iptu_calculavvc.riCodErro := 37;
                    rtp_iptu_calculavvc.rbErro    := 't';
                    return rtp_iptu_calculavvc;
                end if;

                /* calcula o valor venal da construcao */
                nVvcp   := rConstr.j39_area * nVm2c * nFatorIdade * nFatorLocalizacao;
                nAreatc := nAreatc + rConstr.j39_area;
                nVvc    := nVvc + nVvcp;

                perform fc_debug('< calculo vvc > Valor venal total parcial - nVvc - '||nVvc||' nVm2c - '||nVm2c|| 'area - '||rConstr.j39_area, lRaise);

                iNumerocontr := iNumerocontr + 1;

                insert into tmpiptucale (anousu, matric, idcons, areaed, vm2, pontos, valor)
                                 values (iAnousu, iMatricula, rConstr.j39_idcons, rConstr.j39_area, nVm2c, 0, nVvcp);
                if lAtualiza then
                    update tmpdadosiptu set predial = true;
                    lAtualiza := false;
                end if;
        end loop;

        nVvc := round(nVvc, 2);
        perform fc_debug('< calculo vvc > Valor venal total final - nVvc - '||nVvc, lRaise);

        rtp_iptu_calculavvc.rnVvc       := nVvc::numeric;
        rtp_iptu_calculavvc.rnTotarea   := nAreatc::numeric;
        rtp_iptu_calculavvc.riNumconstr := iNumerocontr;
        rtp_iptu_calculavvc.rtDemo      := '';
        rtp_iptu_calculavvc.rbErro      := 'f';

        update tmpdadosiptu set vvc = rtp_iptu_calculavvc.rnVvc;

        return rtp_iptu_calculavvc;
    end;
$$ language 'plpgsql';


create or replace function cadastro.fc_iptu_calculavvt_valenca_2023(integer,integer,integer,numeric,boolean,boolean) returns tp_iptu_calculavvt as
$$

declare

    iIdbql                   alias for $1;
    iMatricula               alias for $2;
    iAnousu                  alias for $3;
    nFracao                  alias for $4;
    lMostrademo              alias for $5;
    lRaise                   alias for $6;

    lPredial                 boolean default false;

    nVm2t                    numeric default 0;
    nAreaLoteCorrigi         numeric default 0;
    nAreaTerreno             numeric default 0;
    nValor                   numeric default 0;
    nTestada                 numeric default 0;
    nFatorSituacao           numeric default 0;
    nFatorCondFisTerreno     numeric default 0;

    iZona                    bigint default 0;

    rtp_iptu_calculavvt      tp_iptu_calculavvt%ROWTYPE;

begin

    rtp_iptu_calculavvt.rnAreaTotalC := 0;
    rtp_iptu_calculavvt.rnArea       := 0;
    rtp_iptu_calculavvt.rnTestada    := 0;
    rtp_iptu_calculavvt.riCoderro    := 0;
    rtp_iptu_calculavvt.rtDemo       := '';
    rtp_iptu_calculavvt.rtMsgerro    := '';
    rtp_iptu_calculavvt.rtErro       := '';
    rtp_iptu_calculavvt.rbErro       := 'f';

    perform fc_debug('< calculo vvt > INICIANDO CALCULO DO VALOR VENAL TERRITORIAL...', lRaise);

    select case when j39_matric is not null
                    then true
                else false
               end
    into lPredial
    from iptubase
             left join iptuconstr
                       on j39_matric = j01_matric
                           and j39_dtdemo is null
    where j01_matric = iMatricula;

    if not found then
        rtp_iptu_calculavvt.rbErro    := 't';
        rtp_iptu_calculavvt.riCoderro := 9;
        rtp_iptu_calculavvt.rtErro    := ' DADOS DA MATRICULA NAO ENCONTRADO';

        return rtp_iptu_calculavvt;
    end if;

    /* verifica a area do lote */
    select j34_area::numeric, j34_zona
      into nAreaTerreno, iZona
    from lote
    where j34_idbql = iIdbql;

    if nAreaTerreno is null or nAreaTerreno = 0 then

        rtp_iptu_calculavvt.rbErro    := 't';
        rtp_iptu_calculavvt.riCoderro := 3;
        rtp_iptu_calculavvt.rtErro    := 'AREA DO LOTE NAO ENCONTRADA OU ZERADA';

        return rtp_iptu_calculavvt;
    end if;

    nAreaLoteCorrigi := ( nAreaTerreno * ( nFracao / 100::numeric ) );

    perform fc_debug('< calculo vvt > Area do terreno: '||nAreaTerreno, lRaise);

    /* busca valor do m2 do terreno */

    select j51_valorm2t::numeric
      into nVm2t
    from zonasvalor
    where j51_anousu = iAnousu
      and j51_zona = iZona;

    if not found then

      rtp_iptu_calculavvt.rbErro    := 't';
      rtp_iptu_calculavvt.riCoderro := 7;
      rtp_iptu_calculavvt.rtMsgErro := 'VERIFIQUE O VALOR DO M2 DO TERRENO PARA A ZONA';

      return rtp_iptu_calculavvt;
    end if;

    perform fc_debug('< calculo vvt > Valor do m2 do terreno: '||nVm2t, lRaise);

    /* busca fator Situacao */
    select j74_fator::numeric
      into nFatorSituacao
    from carlote
           inner join caracter
                   on j31_codigo = j35_caract
           inner join carfator
                   on j74_anousu = iAnousu
                  and j74_caract = j35_caract
    where j35_idbql = iIdbql
      and j31_grupo = 100;

    if nFatorSituacao = 0 or nFatorSituacao is null then

      rtp_iptu_calculavvt.rbErro    := 't';
      rtp_iptu_calculavvt.riCoderro := 24;
      rtp_iptu_calculavvt.rtErro    := ' PARA GRUPO 100 OU SEM VALOR PARA O ANO DE '||iAnousu;
      rtp_iptu_calculavvt.rtMsgErro := 'SEM CARACTERISTICA PARA GRUPO 100 OU SEM VALOR PARA O ANO DE '||iAnousu;

      return rtp_iptu_calculavvt;
    end if;

  /* busca fator Condicao Fisica do Terreno */

    select j74_fator::numeric
    into nFatorCondFisTerreno
    from carlote
             inner join caracter
                        on j31_codigo = j35_caract
             inner join carfator
                        on j74_anousu = iAnousu
                            and j74_caract = j35_caract
    where j35_idbql = iIdbql
      and j31_grupo = 200;

   if nFatorCondFisTerreno = 0 or nFatorCondFisTerreno is null then

      rtp_iptu_calculavvt.rbErro    := 't';
      rtp_iptu_calculavvt.riCoderro := 24;
      rtp_iptu_calculavvt.rtErro    := ' PARA GRUPO 200 OU SEM VALOR PARA O ANO DE '||iAnousu;
      rtp_iptu_calculavvt.rtMsgErro := 'SEM CARACTERISTICA PARA GRUPO 200 OU SEM VALOR PARA O ANO DE '||iAnousu;

      return rtp_iptu_calculavvt;
   end if;


	 nValor := round( nAreaLoteCorrigi * nVm2t * nFatorSituacao * nFatorCondFisTerreno, 2);

   if nValor <= 0 or nValor is null then

      rtp_iptu_calculavvt.rbErro    := 't';
      rtp_iptu_calculavvt.riCoderro := 113;
      rtp_iptu_calculavvt.rtErro    := '';

      return rtp_iptu_calculavvt;
   end if;

    /* formula de calculo do terreno */
   perform fc_debug('< calculo vvt > Formula: VVT = (nAreaLoteCorrigi * nVm2t * nFatorSituacao * nFatorCondFisTerreno)', lRaise);
   perform fc_debug('', lRaise);
   perform fc_debug('< calculo vvt > Formula: '||nValor||' = ('||nAreaLoteCorrigi||' * ('||nVm2t||' * '||nFatorSituacao||' * '||nFatorCondFisTerreno||')', lRaise, lRaise, lRaise);

   rtp_iptu_calculavvt.rnArea       := nAreaTerreno;
   rtp_iptu_calculavvt.rnVvt        := nValor;
   rtp_iptu_calculavvt.rnAreaTotalC := nAreaLoteCorrigi;
   rtp_iptu_calculavvt.rnTestada    := nTestada;
   rtp_iptu_calculavvt.rtDemo       := '';
   rtp_iptu_calculavvt.rtMsgerro    := '';
   rtp_iptu_calculavvt.rbErro       := 'f';

   update tmpdadosiptu
      set vvt = rtp_iptu_calculavvt.rnVvt,
          vm2t= nVm2t,
          areat=nAreaLoteCorrigi;

   return rtp_iptu_calculavvt;

end;
$$  language 'plpgsql';


create or replace function cadastro.fc_iptu_getaliquota_valenca_2023(integer, integer, integer, boolean, boolean)
    returns tp_aliquota_iptu as
$$

    declare

        iMatricula         alias for $1;
        iIdbql             alias for $2;
        iAnousu            alias for $3;
        bPredial           alias for $4;
        bRaise             alias for $5;

        nAliquota          numeric default 0;

        iRegistros         integer default 0;

        rtp_aliquota_iptu  tp_aliquota_iptu%ROWTYPE;

    begin

        perform fc_debug('DEFININDO QUAL ALIQUOTA APLICAR ...', bRaise);

        rtp_aliquota_iptu.coderro   = 0;
        rtp_aliquota_iptu.descrerro = '';
        rtp_aliquota_iptu.aliquota  = 0;

        if bPredial then
            select coalesce(max(j82_valorterreno), 0)::numeric, count(*)
            into nAliquota, iRegistros
            from iptubase
                 join iptuconstr on j39_matric = j01_matric
                                and j39_idprinc is true
                                and j39_dtdemo is null
                 join carconstr  on j48_matric = j39_matric
                                and j48_idcons = j39_idcons
                 join caracter   on j31_codigo = j48_caract
                                and j31_grupo  = 600
                 join lotesetorfiscal on j91_idbql = j01_idbql
                 join setorfiscalvalor on j82_setorfiscal = j91_codigo
                                      and j82_anousu = iAnousu
                                      and j82_caract = j48_caract
            where j01_matric = iMatricula
            group by j01_matric;

            if not found then
                rtp_aliquota_iptu.coderro   = 13;
                rtp_aliquota_iptu.descrerro = ' OR NAO ENCONTRADA PARA O ANO '||iAnousu;
                rtp_aliquota_iptu.aliquota  = 0;

                return rtp_aliquota_iptu;
            end if;

            if iRegistros > 1 then
                rtp_aliquota_iptu.coderro   = 38;
                rtp_aliquota_iptu.descrerro = ' '||iMatricula;
                rtp_aliquota_iptu.aliquota  = 0;

                return rtp_aliquota_iptu;
            end if;
        else
            select coalesce(j110_fator, 0)::numeric
              into nAliquota
            from lote
                 join zonafator on j110_zona = j34_zona
                               and j110_anousu = iAnousu
            where j34_idbql = iIdbql;

            if not found then
                rtp_aliquota_iptu.coderro   = 13;
                rtp_aliquota_iptu.descrerro = ' OR NAO ENCONTRADA PARA O ANO '||iAnousu;
                rtp_aliquota_iptu.aliquota  = 0;

                return rtp_aliquota_iptu;
            end if;
        end if;

        perform fc_debug('< getaliquota > aliquota final: '||nAliquota, bRaise);

        execute 'update tmpdadosiptu set aliq = '||nAliquota;

        rtp_aliquota_iptu.coderro   = 0;
        rtp_aliquota_iptu.descrerro = '';
        rtp_aliquota_iptu.aliquota  = nAliquota;

        return rtp_aliquota_iptu;

    end;
$$ language 'plpgsql';


CREATE OR REPLACE FUNCTION cadastro.fc_calculoiptu_valenca_2023(integer,integer,boolean,boolean,boolean,boolean,boolean,integer,integer)
    RETURNS varchar(100) AS
$$

declare

    iMatricula                          alias   for $1;
    iAnousu                             alias   for $2;
    lGerafinanc                         alias   for $3;
    lAtualizaParcela                    alias   for $4;
    lNovonumpre                         alias   for $5;
    lCalculogeral                       alias   for $6;
    lDemonstrativo                      alias   for $7;
    iParcelaini                         alias   for $8;
    iParcelafim                         alias   for $9;

    iIdbql                              integer default 0;
    iNumcgm                             integer default 0;
    iCodcli                             integer default 0;
    iCodisen                            integer default 0;
    iTipois                             integer default 0;
    iParcelas                           integer default 0;
    iNumconstr                          integer default 0;
    iCodErro                            integer default 0;

    dDatabaixa                          date;

    nAreal                              numeric default 0;
    nAreac                              numeric default 0;
    nTotarea                            numeric default 0;
    nFracao                             numeric default 0;
    nFracaolote                         numeric default 0;
    nAliquota                           numeric default 0;
    nIsenaliq                           numeric default 0;
    nArealo                             numeric default 0;
    nVvc                                numeric(15,2) default 0;
    nVvt                                numeric(15,2) default 0;
    nVv                                 numeric(15,2) default 0;
    nViptu                              numeric(15,2) default 0;

    tRetorno                            text default '';
    tDemo                               text default '';
    tErro                               text default '';

   lPredial                             boolean;
   lFinanceiro                          boolean;
   lDadosIptu                           boolean;
   lErro                                boolean;
   lIsentaxas                           boolean;
   lTempagamento                        boolean;
   lEmpagamento                         boolean;
   lTaxasCalculadas                     boolean;
   lRaise                               boolean default false; -- true para habilitar raise na funcao principal
   lSubRaise                            boolean default false; -- true para habilitar raise nas sub-funcoes

   rCfiptu                              record;

begin

    lRaise := ( case when fc_getsession('DB_debugon') is null then false else true end );
    lSubRaise := lRaise;

    perform fc_debug('INICIANDO CALCULO',lRaise,true,false);
    perform fc_debug('',lRaise,true,false);

    /**
     * Guarda os parametros do calculo
     */

    select * from into rCfiptu cfiptu where j18_anousu = iAnousu;

    select j34_area, coalesce(j34_totcon, 0)
    into nArealo, nTotarea
    from iptubase
             join lote on j34_idbql = j01_idbql
    where j01_matric = iMatricula;

    if not found then
        select fc_iptu_geterro( 3, 'ERRO - Sem dados para a matricula '||iMatricula ) into tRetorno;
        return tRetorno;
    end if;

    /**
     * Executa PRE CALCULO
     */
    select r_iIdbql, r_nAreal, r_nFracao, r_iNumcgm, r_dDatabaixa, r_nFracaolote,
           r_tDemo, r_lTempagamento, r_lEmpagamento, r_iCodisen, r_iTipois, r_nIsenaliq,
           r_lIsentaxas, r_nArealote, r_iCodCli, r_tRetorno
    into iIdbql, nAreal, nFracao, iNumcgm, dDatabaixa, nFracaolote, tDemo, lTempagamento,
        lEmpagamento, iCodisen, iTipois, nIsenaliq, lIsentaxas, nArealo, iCodCli, tRetorno
    from fc_iptu_precalculo( iMatricula, iAnousu, lCalculogeral, lAtualizaParcela, lDemonstrativo, lRaise );

    perform fc_debug(' RETORNO DA PRE CALCULO: ',            lRaise);
    perform fc_debug('  iIdbql        -> ' || iIdbql,        lRaise);
    perform fc_debug('  nAreal        -> ' || nAreal,        lRaise);
    perform fc_debug('  nFracao       -> ' || nFracao,       lRaise);
    perform fc_debug('  iNumcgm       -> ' || iNumcgm,       lRaise);
    perform fc_debug('  dDatabaixa    -> ' || dDatabaixa,    lRaise);
    perform fc_debug('  nFracaolote   -> ' || nFracaolote,   lRaise);
    perform fc_debug('  tDemo         -> ' || tDemo,         lRaise);
    perform fc_debug('  lTempagamento -> ' || lTempagamento, lRaise);
    perform fc_debug('  lEmpagamento  -> ' || lEmpagamento,  lRaise);
    perform fc_debug('  iCodisen      -> ' || iCodisen,      lRaise);
    perform fc_debug('  iTipois       -> ' || iTipois,       lRaise);
    perform fc_debug('  nIsenaliq     -> ' || nIsenaliq,     lRaise);
    perform fc_debug('  lIsentaxas    -> ' || lIsentaxas,    lRaise);
    perform fc_debug('  nArealote     -> ' || nArealo,       lRaise);
    perform fc_debug('  iCodCli       -> ' || iCodCli,       lRaise);
    perform fc_debug('  tRetorno      -> ' || tRetorno,      lRaise);
    perform fc_debug('',lRaise,true,false);

    /**
     * Variavel de retorno contem a msg
     * de erro retornada do pre calculo
     */

    if trim(tRetorno) <> '' then
        return tRetorno;
    end if;

    update tmpdadosiptu set matric = iMatricula;

    update tmpdadostaxa
    set anousu = iAnousu,
        matric = iMatricula,
        idbql  = iIdbql,
        valref = rCfiptu.j18_vlrref;

    /**
     * Calcula valor do terreno
     */

    perform fc_debug('PARAMETROS fc_iptu_calculavvt_valenca_2023 IDBQL: '||iIdbql||' - iMatricula: '||iMatricula||' - Anousu: '||iAnousu||' - FRACAO DO LOTE: '||nFracaolote||' - DEMO: '||lDemonstrativo||' - DEBUG: '||lRaise, lRaise);

    select rnvvt, rnarea, rtdemo, rtmsgerro, rberro, riCodErro, rtErro
    into nVvt, nAreac, tDemo, tRetorno, lErro, iCodErro, tErro
    from fc_iptu_calculavvt_valenca_2023( iIdbql, iMatricula, iAnousu, nFracaolote, lDemonstrativo, lRaise);

    perform fc_debug('RETORNO fc_iptu_calculavvt_valenca_2023 -> VVT: '||nVvt||' - AREA CONSTRUIDA: '||nAreac||' - RETORNO: '||tRetorno||' - ERRO: '||lErro, lRaise);
    perform fc_debug('', lRaise);

    if lErro is true then
        select fc_iptu_geterro( iCodErro, tErro ) into tRetorno;
        return tRetorno;
    end if;

    /**
     * Calcula valor da construcao
     */

    perform fc_debug('PARAMETROS fc_iptu_calculavvc_valenca_2023 MATRICULA: '||iMatricula||' - ANOUSU: '||iAnousu||' - DEMO: '||lDemonstrativo||' - DEBUG: '||lRaise, lRaise);

    select rnvvc, rntotarea, rinumconstr, rtdemo, rtmsgerro, rberro, riCodErro, rtErro
      into nVvc, nTotarea, iNumconstr, tDemo, tRetorno, lErro, iCodErro, tErro
    from fc_iptu_calculavvc_valenca_2023( iMatricula, iAnousu, lDemonstrativo, lRaise );

    perform fc_debug('RETORNO fc_iptu_calculavvc_valenca_2023 -> VVC: '||nVvc||' - AREA TOTAL: '||nTotarea||' - NUMERO DE CONSTRUCOES: '||iNumconstr||' - RETORNO: '||tRetorno||' - ERRO: '||lErro, lRaise);
    perform fc_debug('', lRaise);

    if lErro is true then
        select fc_iptu_geterro(iCodErro, tErro) into tRetorno;
        return tRetorno;
    end if;

    select predial into lPredial from tmpdadosiptu;

    /* BUSCA A ALIQUOTA  */

    perform fc_debug('BUSCA A ALIQUOTA DO IPTU ', lRaise);

    select coderro, descrerro, aliquota
    into iCodErro, tErro, nAliquota
    from fc_iptu_getaliquota_valenca_2023(iMatricula, iIdbql, iAnousu, lPredial, lSubRaise);

    if nAliquota = 0 then
        select fc_iptu_geterro( iCodErro, tErro ) into tRetorno;
        return tRetorno;
    end if;

    perform fc_debug('RETORNO DA BUSCA A ALIQUOTA DO IPTU ', lRaise);
    perform fc_debug(' ', lRaise);

    /*--------- CALCULA O VALOR VENAL -----------*/

    perform fc_debug('valor venal construcao (nVvc) - '||nVvc||' valor venal terreno (nVvt) - '||nVvt, lRaise);

    nVv    := nVvc + nVvt;

    perform fc_debug('valor venal total - '||nVv, lRaise);

    nViptu := nVv * ( nAliquota / 100 );

    if nViptu < rCfiptu.j18_vlrref then
        perform fc_debug('Valor do IPTU menor que a UFIVA', lRaise);
        nViptu := rCfiptu.j18_vlrref;
    end if;

    perform fc_debug('valor iptu '||nViptu||' - aliquota '||nAliquota||'%', lRaise);
    perform fc_debug(' ', lRaise);
    perform fc_debug('Inserindo as receitas de IPTU na tabela tmprecval ', lRaise);
    perform fc_debug(' ', lRaise);

    if lPredial then
        insert into tmprecval values (rCfiptu.j18_rpredi, nViptu, 1, false);
    else
        insert into tmprecval values (rCfiptu.j18_rterri, nViptu, 1, false);
    end if;

    perform fc_debug('Inserindo valor e codigo de vencimento do IPTU na tabela tmpdadosiptu', lRaise);
    perform fc_debug(' ', lRaise);

    update tmpdadosiptu
    set viptu   = nViptu,
        codvenc = rCfiptu.j18_vencim;

    /*-------------------------------------------*/

    select count(*)
    into iParcelas
    from cadvencdesc
             inner join cadvenc on q92_codigo = q82_codigo
    where q92_codigo = rCfiptu.j18_vencim ;

    if not found or iParcelas = 0 then
        select fc_iptu_geterro(14,'') into tRetorno;
        return tRetorno;
    end if;

    update tmpdadostaxa set valiptu = nViptu, vvt = nVvt, nparc = iParcelas, totareaconst = nTotarea;

    /* CALCULA AS TAXAS */

    perform fc_debug('PARAMETROS fc_iptu_calculataxas  ANOUSU '||iAnousu||' -- CODCLI '||iCodcli||' -- Debug: '||lSubRaise, lRaise);
    perform fc_debug(' ', lRaise);

    select fc_iptu_calculataxas(iMatricula, iAnousu, iCodcli, lSubRaise)
    into lTaxasCalculadas;

    perform fc_debug('RETORNO fc_iptu_calculataxas --->>> TAXAS CALCULADAS - '||lTaxasCalculadas, lRaise);

    /* MONTA O DEMONSTRATIVO */
    select fc_iptu_demonstrativo(iMatricula,iAnousu,iIdbql,lSubRaise )
    into tDemo;

    /* GERA FINANCEIRO */
    if lDemonstrativo is false then -- Se nao for demonstrativo gera o financeiro, caso contrario retorna o demonstrativo
        select fc_iptu_geradadosiptu(iMatricula,iIdbql,iAnousu,nIsenaliq,lDemonstrativo,lSubRaise)
        into lDadosIptu;
        if lGerafinanc then
            select fc_iptu_gerafinanceiro(iMatricula,iAnousu,iParcelaini,iParcelafim,lCalculogeral,lTempagamento,lNovonumpre,lDemonstrativo,lSubRaise)
            into lFinanceiro;
        end if;
    else
        return tDemo;
    end if;

    if lDemonstrativo is false then
        update iptucalc
        set j23_manual = tDemo
        where j23_matric = iMatricula
          and j23_anousu = iAnousu;
    end if;

    select fc_iptu_geterro(1, '') into tRetorno;

    return tRetorno;

end;
$$ LANGUAGE 'plpgsql';


create or replace function fc_iptu_criatemptable(boolean) returns boolean as
$$
declare

     lRaise alias for $1;

     rbErro boolean default false;
     nome   name;

begin

   /**
    * FUNCAO PARA CRIAR AS TABELAS TEMPORARIAS
    */
  perform fc_debug('', lRaise);
  perform fc_debug(' <iptu_criatemptable> INICIANDO CRIACAO DE ESTRUTURAS TEMPORARIAS...', lRaise);

  begin

    /*
     * NAO REMOVER CAMPOS DESSAS TABELAS, ESSA ALTERACAO PODE CAUSAR PROBLEMAS EM TODOS OS CALCULOS
     * QUANDO USAR AS TABELAS TEMPORARIAS NAO USE SELECT * INTO VAI1, VAR2,VAR3 FROM XXX.
     * USE: SELECT CAMPO1,CAMPO2,CAMPO3 INTO  VAR1, VAR2,VAR3 FROM XXXX.
     */

    /**
     * Tabela que guarda as receitas e valores das mesmas, para gerar o financeiro(arrecad)
     */
    create temporary table tmprecval( "receita" integer,"valor" numeric,"hist" integer,"taxa" boolean,"aliq" numeric );
    perform fc_debug(' <iptu_criatemptable> TABELA TMPRECVAL CRIADA', lRaise);

    /**
     * Tabela que guarda os dados referente ao comportamento do calculo durante o processamento das sub-funcoes
     */
    create temporary table tmpdadosiptu( "aliq"      numeric,
                                         "vvc"       numeric,
                                         "vvt"       numeric,
                                         "viptu"     numeric,
                                         "fracao"    numeric,
                                         "areat"     numeric,
                                         "predial"   boolean,
                                         "codvenc"   integer,
                                         "tipoisen"  integer,
                                         "vm2t"      numeric,
                                         "testada"   numeric,
                                         "matric"    integer,
                                         "isentaxas" boolean );
    insert into tmpdadosiptu values (0,0,0,0,0,0,false,0,0,0,0,0);
    perform fc_debug(' <iptu_criatemptable> TABELA TMPDADOSIPTU CRIADA', lRaise);

    /**
     * Tabela que guarda os dados das contrucoes calculadas, alimentada pela fc_iptu_calculavvc
     */
    create temporary table tmpiptucale( "anousu"         integer,
                                        "matric"         integer,
                                        "idcons"         integer,
                                        "areaed"         numeric,
                                        "vm2"            numeric,
                                        "pontos"         integer,
                                        "valor"          numeric,
                                        "edificacao"     boolean,
                                        "caracteristica" integer,
                                        "aliquota"       numeric );
    perform fc_debug(' <iptu_criatemptable> TABELA TMPIPTUCALE CRIADA', lRaise);

    /**
     * Tabela que guarda os valores para calcular as taxas
     */
    create temporary table tmpdadostaxa( "anousu"  integer,
                                         "matric"  integer,
                                         "zona"    integer,
                                         "idbql"   integer,
                                         "nparc"   integer,
                                         "valiptu" numeric,
                                         "valref"  numeric,
                                         "vvt"     numeric,
                                         "totareaconst" numeric );
    insert into tmpdadostaxa values (0,0,0,0,0,0,0,0,0);
    perform fc_debug(' <iptu_criatemptable> TABELA TMPDADOSTAXA CRIADA', lRaise);

    /**
     * Tabela com os parametros para o comportamento da fase do calculo que gera o financeiro
     */
    create temporary table tmpfinanceiro("anousu" integer,"matric" integer,"idbql" integer,"valiptu" numeric,"valref" numeric,"vvt" numeric);
    insert into tmpfinanceiro values (0,0,0,0,0,0);
    perform fc_debug(' <iptu_criatemptable> TABELA TMPFINANCEIRO CRIADA', lRaise);

    /**
     * Tabela que guarda as receitas e percentual de isencao das taxas
     */
    create temporary table tmptaxapercisen("rectaxaisen" integer,"percisen" numeric, "histcalcisen" integer,"valsemisen" numeric);
    perform fc_debug(' <iptu_criatemptable> TABELA TMPTAXAPERCISEN CRIADA', lRaise);

    /**
     * Tabela que guarda os valores para "outras" taxas (taxa bombeiro, limpeza)
     */
    create temporary table tmpoutrosvalores("valor" numeric,"descricao" varchar);
    perform fc_debug(' <iptu_criatemptable> TABELA TMPTAXAPERCISEN CRIADA', lRaise);

    /**
     * Tabela que guarda os valores de vencimentos
     */
    create temporary table tmp_cadvenc as
      select q92_codigo,
             q92_tipo,
             q92_hist,
             q92_vlrminimo,
             q82_parc,
             q82_venc,
             q82_perc,
             q82_hist
        from cadvencdesc
             inner join cadvenc on q92_codigo = q82_codigo
       limit 0;
    perform fc_debug(' <iptu_criatemptable> TABELA TMP_CADVENC CRIADA', lRaise);

    /**
     * Tabela para guardar o numpre gerado na diversos (iptu_complementar)
     */
    create temporary table tmpipturecalculonump (
        matricula integer,
        anousu    integer,
        numpre    integer
    );
    perform fc_debug(' <iptu_criatemptable> TABELA TMPIPTURECALCULONUMP CRIADA', lRaise);

    /**
     * Tabela para guardar o numpre gerado na diversos (iptu_complementar)
     */
    create temporary table tmpipturecalculocreditonump (
        matricula integer,
        anousu    integer,
        numpre    integer
    );
    perform fc_debug(' <iptu_criatemptable> TABELA TMPIPTURECALCULOCREDITONUMP CRIADA', lRaise);

  exception
       when duplicate_table then
            truncate tmprecval;
            truncate tmpdadosiptu;
            truncate tmpiptucale;
            truncate tmpdadostaxa;
            truncate tmpfinanceiro;
            truncate tmptaxapercisen;
            truncate tmpoutrosvalores;
            truncate tmp_cadvenc;
            truncate tmpipturecalculonump;
            truncate tmpipturecalculocreditonump;
            insert into tmpdadosiptu  values (0,0,0,0,0,0,false,0,0,0,0,0,false);
            insert into tmpdadostaxa  values (0,0,0,0,0,0,0,0,0);
            insert into tmpfinanceiro values (0,0,0,0,0,0);
  end;

  perform fc_debug(' <iptu_criatemptable> FIM CRIACAO DE ESTRUTURAS TEMPORARIAS', lRaise);
  perform fc_debug('', lRaise);

  return rbErro;

end;
$$  language 'plpgsql';

drop function if exists fc_iptu_getareaconstrloteidbql(integer, integer);
create or replace function cadastro.fc_iptu_getareaconstrloteidbql(integer, integer)
    returns numeric
as $$

    declare

        iIdbql                 alias for $1;
        iAnousu                alias for $2;

        nTotalAreaConstruida   numeric default 0;

        sSql                   varchar := '';

        bConstrucaoIrregular   boolean default false;

    begin

        /* se o cliente for Valenca, faz uma validacao para calcular a fracao */
        select true
          into bConstrucaoIrregular
        from db_config
        where db21_codigomunicipoestado = '3306107';

        sSql := 'with matriculas as (';
        sSql := sSql || 'select j39_matric, j39_idcons, j39_area ';
        sSql := sSql || 'from iptubase ';
        sSql := sSql || '     join iptuconstr on j39_matric = j01_matric ';
        sSql := sSql || 'where j01_baixa  is null ';
        sSql := sSql || '  and j01_idbql  = ' || iIdbql;
        sSql := sSql || '  and j39_dtdemo is null), ';

        if bConstrucaoIrregular then
            sSql := sSql || 'matriculas_irregulares as (';
            sSql := sSql || '    select j39_matric as matric, j39_idcons as idcons ';
            sSql := sSql || '    from iptubase ';
            sSql := sSql || '         join iptuconstr on j39_matric = j01_matric ';
            sSql := sSql || '         join carconstr on j48_matric = j39_matric ';
            sSql := sSql || '                       and j48_idcons = j39_idcons ';
            sSql := sSql || '         join caracter on j31_codigo = j48_caract ';
            sSql := sSql || '                      and j31_grupo = 7200 ';
            sSql := sSql || '         join carfator on j74_anousu = ' || iAnousu;
            sSql := sSql || '                      and j74_caract = j48_caract ';
            sSql := sSql || '    where j01_baixa  is null ';
            sSql := sSql || '      and j01_idbql  = ' || iIdbql;
            sSql := sSql || '      and j39_dtdemo is null), ';
            sSql := sSql || 'matriculas_ativas as (';
            sSql := sSql || '    select j39_matric, j39_idcons, j39_area ';
            sSql := sSql || '    from matriculas ';
            sSql := sSql || '         left join matriculas_irregulares on matric = j39_matric ';
            sSql := sSql || '                                         and idcons = j39_idcons ';
            sSql := sSql || '    where matric is null) ';
        else
            sSql := sSql || 'matriculas_ativas as (';
            sSql := sSql || '    select j39_matric, j39_idcons, j39_area ';
            sSql := sSql || '    from matriculas) ';
        end if;

        sSql := sSql || 'select round(coalesce(sum(j39_area), 0), 2)::numeric ';
        sSql := sSql || 'from matriculas_ativas; ';

        execute sSql into nTotalAreaConstruida;

        return nTotalAreaConstruida;

    end;
$$ language 'plpgsql';

drop function if exists fc_iptu_fracionalote(integer,integer,boolean,boolean);
drop function if exists fc_iptu_fracionalote(integer,integer,boolean,boolean,boolean);

drop   type if exists tp_iptu_fracionalote;
create type cadastro.tp_iptu_fracionalote as (rnFracao numeric, rtDemo text, rtMsgerro text, rbErro boolean);

create or replace function cadastro.fc_iptu_fracionalote(integer,integer,boolean,boolean)
    returns tp_iptu_fracionalote
as $$

    declare

        iMatricula            alias for $1;
        iAnousu               alias for $2;
        bMostrademo           alias for $3;
        lRaise                alias for $4;

        rtp_iptu_fracionalote tp_iptu_fracionalote%ROWTYPE;

        begin

            rtp_iptu_fracionalote.rnFracao  := 0;
            rtp_iptu_fracionalote.rtDemo    := '';
            rtp_iptu_fracionalote.rtMsgerro := '';
            rtp_iptu_fracionalote.rbErro    := 'f';

            select *
              into rtp_iptu_fracionalote
            from fc_iptu_fracionalote(iMatricula, iAnousu, bMostrademo, lRaise, true);

            return rtp_iptu_fracionalote;

        end;
$$ language 'plpgsql';


create or replace function cadastro.fc_iptu_fracionalote(integer,integer,boolean,boolean,boolean)
    returns tp_iptu_fracionalote
as $$

    declare

        iMatricula 	           alias for $1;
        iAnousu    	           alias for $2;
        bMostrademo            alias for $3; --Não utilizada no escopo
        lRaise                 alias for $4;
        lAtualizaFracaoForcada alias for $5;

        cSetor	               char(4);
        cQuadra	               char(4);
        cLote		           char(4);

        iIptufrac	           integer;
        iTotalMatriculas	   integer;
        iIdbql 	               integer  default 0;

        nTotalAreaConstruida   numeric;
        rnFracao 	           numeric  default 0;
        nAreacalc	           numeric  default 0;
        nJ01_fracao            numeric  default 0;

        bFracionaIdbql         boolean;
        bConstrucaoIrregular   boolean default false;

        tManual 	           text     default '';
        tSql                   text     default '';

        rFracao	               record;

        rtp_iptu_fracionalote  tp_iptu_fracionalote%ROWTYPE;

    begin

        perform fc_debug('', lRaise);
        perform fc_debug(' <fracionalote> INICIANDO FRACIONAMENTO DO LOTE...', lRaise);

        select j18_fracionaidbql
          into bFracionaIdbql
        from cadastro.cfiptu
        where j18_anousu = iAnousu;

        rtp_iptu_fracionalote.rnFracao  := 0;
        rtp_iptu_fracionalote.rtDemo    := '';
        rtp_iptu_fracionalote.rtMsgerro := '';
        rtp_iptu_fracionalote.rbErro    := 'f';

        select j01_idbql, j34_setor, j34_quadra, j34_lote
          into iIdbql, cSetor, cQuadra, cLote
        from iptubase
             join lote on j34_idbql = j01_idbql
        where j01_matric = iMatricula;

        /*
         * Conta quantas Matriculas tem para o lote da Matricula a ser calculada
         */
        if bFracionaIdbql then
            perform fc_debug(' <fracionalote> Fracionamento por Idbql: '||iIdbql, lRaise);

            select count(j01_idbql)
              into iTotalMatriculas
            from iptubase
                 join lote on j34_idbql = j01_idbql
            where j01_baixa is null
              and j34_idbql = iIdbql;
        else
            perform fc_debug(' <fracionalote> fracionamento por Setor: '||cSetor||' - Quadra: '||cQuadra||' Lote: '||cLote, lRaise);

            select count(j01_idbql)
              into iTotalMatriculas
            from iptubase
                 join lote on j34_idbql = j01_idbql
            where j01_baixa is null
              and j34_setor  = cSetor
              and j34_quadra = cQuadra
              and j34_lote   = cLote;
        end if;

        perform fc_debug(' <fracionalote> iMatricula          : ' || iMatricula, lRaise);
        perform fc_debug(' <fracionalote> fracao              : ' || rnFracao, lRaise);
        perform fc_debug(' <fracionalote> total de iMatriculas: ' || iTotalMatriculas, lRaise);

        if iTotalMatriculas = 1 then
            if rnFracao is null or rnFracao = 0 then
               rnFracao = 100::numeric;
            else
                perform fc_debug(' <fracionalote> Calculando area construida da iMatricula... ' || iMatricula, lRaise);

                /*
                 * Retorna a area total construida da MATRICULA
                 */
                select into nAreacalc fc_iptu_getareaconstrmat( iMatricula );

                perform fc_debug(' <fracionalote> Fracao de novo: ' || rnFracao, lRaise);
                perform fc_debug(' <fracionalote> fracaocalc: ' || nAreacalc, lRaise);

                if nAreacalc is null or nAreacalc = 0 then
                    rnFracao = 100;
                else
                    rnFracao = ( (nAreacalc / rnFracao ) * 100 );
                    perform fc_debug(' <fracionalote> nAreacalc: '||nAreacalc||' - rnFracao: ' || rnFracao, lRaise);
                end if;
            end if;
        else
            /*
             * Retorna a area total construida do LOTE
             */
            if bFracionaIdbql then
                select into nTotalAreaConstruida fc_iptu_getareaconstrloteidbql(iIdbql, iAnousu);
                perform fc_debug(' <fracionalote> Busca area total construida por idbql: '||nTotalAreaConstruida, lRaise);
            else
                select into nTotalAreaConstruida fc_iptu_getareaconstrlote(cSetor,cQuadra,cLote);
                perform fc_debug(' <fracionalote> Busca area total construida por Setor/Quadra/Lote: '||nTotalAreaConstruida, lRaise);
            end if;

            perform fc_debug(' <fracionalote> Total construido no lote: ' || nTotalAreaConstruida, lRaise);

            tManual := tManual || 'total construido no lote: ' || nTotalAreaConstruida || ' - ';

            if nTotalAreaConstruida = 0 then

                select j01_fracao
                into nJ01_fracao
                from iptubase
                where j01_matric = iMatricula;

                if nJ01_fracao = 0 or nJ01_fracao is null then

                    if lAtualizaFracaoForcada then
                        update iptubase set j01_fracao = 0 where j01_idbql = iIdbql;
                    end if;

                    rnFracao = 100::numeric;
                else
                    rnFracao = nJ01_fracao;
                end if;

            else
                perform fc_debug(' <fracionalote> Fraciona rFracao ', lRaise);

                /* se o cliente for Valenca, faz uma validacao para calcular a fracao */
                select true
                into bConstrucaoIrregular
                from db_config
                where db21_codigomunicipoestado = '3306107'
                  and prefeitura is true;

                tSql :=         'select j01_matric, sum(j39_area) as j39_area ';
                tSql := tSql || 'from iptubase ';
                tSql := tSql || '     join iptuconstr on j39_matric = j01_matric ';

                if bConstrucaoIrregular then
                    perform fc_debug(' <fracionalote> Desconsidera construcoes irregulares na matricula', lRaise);
                    tSql := tSql || ' join carconstr on j48_matric = j39_matric ';
                    tSql := tSql || '               and j48_idcons = j39_idcons ';
                    tSql := tSql || ' join caracter on j31_codigo = j48_caract ';
                    tSql := tSql || '              and j31_grupo = 7200 ';
                    tSql := tSql || ' left join carfator on j74_anousu = ' || iAnousu;
                    tSql := tSql || '                   and j74_caract = j48_caract ';
                end if;

                tSql := tSql || 'where j01_baixa  is null ';
                tSql := tSql || '  and j01_matric = ' || iMatricula;
                tSql := tSql || '  and j39_dtdemo is null ';

                if bConstrucaoIrregular then
                    tSql := tSql || 'and j74_caract is null ';
                end if;

                tSql := tSql || 'group by j01_matric ';

                for rFracao in execute tSql loop

                    perform fc_debug(' <fracionalote> processando fracao iMatricula: '||coalesce(rFracao.j01_matric,0)||' - construido desta: ' || coalesce(rFracao.j39_area,0), lRaise );

                    select j25_matric
                      into iIptufrac
                    from iptufrac
                    where j25_matric = rFracao.j01_matric
                      and j25_anousu = iAnousu;

                    perform fc_debug(' <fracionalote>    iptufrac: ' || coalesce( iIptufrac, 0 ), lRaise);
                    perform fc_debug(' <fracionalote>    Area Total Construida: '||nTotalAreaConstruida, lRaise);

                    if iIptufrac is null or iIptufrac = 0 then
                        perform fc_debug(' <fracionalote>    insert no iptufrac', lRaise);
                        insert into iptufrac values (iAnousu, rFracao.j01_matric, iIdbql, rFracao.j39_area / nTotalAreaConstruida * 100);
                    else
                        perform fc_debug(' <fracionalote>    update no iptufrac', lRaise);
                        update iptufrac
                            set j25_fracao = rFracao.j39_area / nTotalAreaConstruida * 100,
                                j25_idbql  = iIdbql
                        where j25_matric = rFracao.j01_matric
                          and j25_anousu = iAnousu;
                    end if;
                end loop;

                select j25_fracao
                  into rnFracao
                from iptufrac
                where j25_matric = iMatricula
                  and j25_anousu = iAnousu;

                if rnFracao is null or rnFracao = 0 then
                   rnFracao = 100::numeric;
                end if;
            end if;
        end if;

        select j01_fracao
        into nJ01_fracao
        from iptubase
        where j01_matric = iMatricula;

        if nJ01_fracao is not null and nJ01_fracao > 0 then
           rnFracao = nJ01_fracao;
        end if;

        rtp_iptu_fracionalote.rnFracao := rnFracao;
        rtp_iptu_fracionalote.rtDemo   := tManual;

        perform fc_debug(' <fracionalote> texto demonstrativo :' || tManual, lRaise);
        perform fc_debug(' <fracionalote> FIM FRACIONAMENTO DO LOTE', lRaise);
        perform fc_debug(' ', lRaise);

        return rtp_iptu_fracionalote;
    end;
$$ language 'plpgsql';

SQL
        );
    }

    private function downFuncoes()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            drop function    if exists fc_iptu_getareaconstrloteidbql(integer, integer);
SQL
        );
    }
}
