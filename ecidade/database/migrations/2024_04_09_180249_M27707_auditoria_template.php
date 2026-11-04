<?php

use Illuminate\Database\Migrations\Migration;

class M27707AuditoriaTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upFuncao();
        $this->upTrigger();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downFuncao();
        $this->upTrigger();
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function upTrigger()
    {
        $aTabelasTrigger = $this->buscaTabelasTrigger();

        if (count($aTabelasTrigger) > 0) {
            foreach ($aTabelasTrigger as $aTabelaTrigger) {
                $sQuery = "select configuracoes.fc_auditoria_remove_funcao('{$aTabelaTrigger->tabela}');";
                DB::select($sQuery);

                $sQuery = "select configuracoes.fc_auditoria_cria_funcao('{$aTabelaTrigger->tabela}');";
                DB::select($sQuery);
            }
        }
    }

    public function buscaTabelasTrigger()
    {
        return DB::select(
            "select distinct (event_object_schema||'.'||event_object_table) as tabela
        from information_schema.triggers
        where trigger_name ilike 'tg_auditoria_update_%' or trigger_name ilike 'tg_auditoria_insert_delete_%'"
        );
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function upFuncao()
    {
        DB::connection()->getPdo()->exec(<<<SQL

/***
 *
 *  TEMPLATE de PL para auditoria de tabelas
 *
 *  Variáveis do Template
 *   . %tpl_tabela_esquema               = nome do esquema da tabela a ser auditada
 *   . %tpl_tabela_nome                  = nome da tabela a ser auditada
 *   . %tpl_tabela_hash                  = hash de identificacao para nomes dos objetos (funcao, trigger) de auditoria
 *   . %tpl_data_hora                    = data e hora da criação ou última atualização
 *   . %tpl_bloco_codigo_definicao_chave = bloco de codigo da definicao da chave (xChave)
 *   . %tpl_bloco_codigo_update          = bloco de codigo a ser processado no UPDATE para montar Array com valores realmente alterados
 *   . %tpl_array_campo_nome             = definicao de array com nome dos campos da tabela a ser auditada
 *   . %tpl_array_insert_campo_valor_old = definicao de array com valores OLD dos campos para INSERT
 *   . %tpl_array_insert_campo_valor_new = definicao de array com valores NEW dos campos para INSERT
 *   . %tpl_array_delete_campo_valor_old = definicao de array com valores OLD dos campos para DELETE
 *   . %tpl_array_delete_campo_valor_new = definicao de array com valores NEW dos campos para DELETE
 *
 */


CREATE OR REPLACE FUNCTION configuracoes.fc_auditoria_template() RETURNS TEXT AS
$$
	SELECT \$SQL$
CREATE OR REPLACE FUNCTION {%tpl_tabela_esquema}.fc_auditoria_tabela_{%tpl_tabela_hash}() RETURNS trigger AS
\$AUDITORIA$
/***
 * Função para registro de auditoria de INSERT/UPDATE/DELETE
 * . tabela    = {%tpl_tabela_esquema}.{%tpl_tabela_nome}
 * . hash      = {%tpl_tabela_hash}
 * . timestamp = {%tpl_data_hora}
 */
DECLARE
	xMudancas configuracoes.tp_auditoria_mudancas_campo;
	xChave    configuracoes.tp_auditoria_chave_primaria;

	tDataHora   TIMESTAMP   DEFAULT COALESCE(fc_getsession('DB_datausu')::TIMESTAMP, NOW());
	sLogin      VARCHAR(20) DEFAULT COALESCE(fc_getsession('DB_login'), 'dbseller');
	sIP         VARCHAR(50) DEFAULT COALESCE(fc_getsession('DB_ip'), 'IP nao informado');
	iLogsAcessa INTEGER     DEFAULT fc_getsession('DB_acessado')::INTEGER;
	iInstit     INTEGER     DEFAULT fc_getsession('DB_instit')::INTEGER;
	iUsuario    INTEGER     DEFAULT COALESCE(fc_getsession('DB_id_usuario')::INTEGER, 1);
	iCodDepto   INTEGER     DEFAULT fc_getsession('DB_coddepto')::INTEGER;
	dData       DATE        DEFAULT tDataHora::DATE;

	rRegistro   {%tpl_tabela_esquema}.{%tpl_tabela_nome}%ROWTYPE;
	rLogsAcessa RECORD;

	aCampo      TEXT[];
	aValorOld   TEXT[];
	aValorNew   TEXT[];
BEGIN
	IF TG_OP = 'DELETE' THEN
		rRegistro := OLD;
	ELSE
		rRegistro := NEW;
	END IF;

	/* Caso não tenha instituição definida na sessão, busca padrão prefeitura */
	IF iInstit IS NULL THEN
		SELECT	codigo
		INTO	iInstit
		FROM	db_config
		WHERE	prefeitura IS TRUE
		ORDER	BY codigo
		LIMIT	1;

		IF iInstit IS NULL THEN
			SELECT	codigo
			INTO	iInstit
			FROM	db_config
			ORDER	BY codigo
			LIMIT	1;
			IF iInstit IS NULL THEN
				RAISE EXCEPTION 'Impossível realizar auditoria. Nenhuma instituição encontrada nesta base de dados!';
			END IF;
		END IF;
	END IF;

	/* Caso não tenha um db_logsacessa definido na sessão, então devemos gerar um automático */
	IF iLogsAcessa IS NULL THEN
		/* Ir direto na particao da db_logsacessa com bloco protegido
		   porque a mesma pode não existir */
		BEGIN
			EXECUTE	format('
				SELECT	codsequen
				FROM	db_logsacessa_%s_%s
				WHERE	instit  = %L
				AND		data    = %L
				AND		hora    = %L
				AND		arquivo = %L',
				to_char(tDataHora, 'FMYYYYMM'), iInstit, iInstit, dData,
				to_char(tDataHora, 'HH24:MI:SS'), 'classes/db_{%tpl_tabela_nome}_classe.php')
			INTO	iLogsAcessa;
		EXCEPTION
			WHEN undefined_table THEN
				iLogsAcessa := NULL;
		END;

		IF iLogsAcessa IS NULL THEN
			SELECT	b.codmod, c.id_item
			INTO	rLogsAcessa
			FROM	db_sysarquivo a
					JOIN db_sysarqmod b ON b.codarq = a.codarq
					JOIN db_auditoria_migracao_depara_codarq_codmod_id_modulo c ON c.codarq = b.codarq AND c.codmod = b.codmod
			WHERE	nomearq = '{%tpl_tabela_nome}';

			IF rLogsAcessa.codmod IS NULL OR rLogsAcessa.codmod = '' THEN
			   rLogsAcessa.codmod = fc_getsession('DB_modulo')::INTEGER;
			END IF;

			IF rLogsAcessa.id_item IS NULL OR rLogsAcessa.id_item = '' THEN
			   rLogsAcessa.id_item = fc_getsession('DB_itemmenu_acessado')::INTEGER;
			END IF;

			/* @TODO: verificar se realmente encontrou informações no dicionário de dados */
			INSERT INTO db_logsacessa (
				codsequen, data, hora, arquivo, obs, instit, auditoria, id_usuario, id_modulo, id_item, ip, coddepto
			) VALUES (
				NEXTVAL('db_logsacessa_codsequen_seq'), dData,
				to_char(tDataHora, 'HH24:MI:SS'), 'classes/db_{%tpl_tabela_nome}_classe.php',
				'LogsAcessa Automatico DML Manual', iInstit, TRUE,
				iUsuario, rLogsAcessa.codmod, rLogsAcessa.id_item, sIP, iCodDepto
			);

			iLogsAcessa = CURRVAL('db_logsacessa_codsequen_seq');

			/* Marcamos como já alterado para não executar novamente o update para cada linha */
			PERFORM fc_putsession('__logsacessa_'||iLogsAcessa||'_auditoria_alterado__', 't');
		END IF;
	END IF;

	/* Define chave primária da tabela */
	{%tpl_bloco_codigo_definicao_chave}

	/* Verifica mudanças realizadas de acordo com operação */
	IF TG_OP = 'INSERT' THEN

		xMudancas := ROW(
			ARRAY[ {%tpl_array_campo_nome} ],
			ARRAY[ {%tpl_array_insert_campo_valor_old} ],
			ARRAY[ {%tpl_array_insert_campo_valor_new} ] );

	ELSIF TG_OP = 'UPDATE' THEN

		IF ROW(OLD.*) IS DISTINCT FROM ROW(NEW.*) THEN

			{%tpl_bloco_codigo_update}

		ELSE
			RETURN rRegistro;
		END IF;

		xMudancas := ROW(aCampo, aValorOld, aValorNew);
	ELSE

		xMudancas := ROW(
			ARRAY[ {%tpl_array_campo_nome} ],
			ARRAY[ {%tpl_array_delete_campo_valor_old} ],
			ARRAY[ {%tpl_array_delete_campo_valor_new} ] );

	END IF;

	/* Grava dados na auditoria */
	INSERT INTO db_auditoria (
		sequencial,
		esquema,
		tabela,
		operacao,
		datahora_sessao,
		usuario,
		chave,
		mudancas,
		logsacessa,
		instit,
		gatilho /* TRUE=via trigger, senao NULL ou FALSE=script de migracao */
	) VALUES (
		NEXTVAL('db_acount_id_acount_seq'), -- FUTURAMENTE REMOVER
		TG_TABLE_SCHEMA,
		TG_TABLE_NAME,
		SUBSTR(TG_OP,1,1),
		tDataHora,
		sLogin,
		xChave,
		xMudancas,
		iLogsAcessa,
		iInstit,
		TRUE
	);

	/* Se o log de acesso ainda não foi alterado nesta sessão, então alteramos ... */
	IF fc_getsession('__logsacessa_'||iLogsAcessa||'_auditoria_alterado__') IS NULL THEN
		/* ... para indicar que possui auditoria */
		UPDATE	db_logsacessa
		SET		auditoria = TRUE
		WHERE	instit    = iInstit
		AND		data      = dData
		AND		codsequen = iLogsAcessa
		AND		auditoria IS FALSE;

		/* Marcamos como já alterado para não executar novamente o update para cada linha */
		PERFORM fc_putsession('__logsacessa_'||iLogsAcessa||'_auditoria_alterado__', 't');
	END IF;

	RETURN rRegistro;
END;
\$AUDITORIA$
SECURITY DEFINER
LANGUAGE plpgsql;

/* @TODO: melhorar uso das variáveis de sessão __disable_audit__ por conta da noção de ON/OFF */
CREATE TRIGGER tg_auditoria_insert_delete_{%tpl_tabela_hash}
	AFTER INSERT OR DELETE ON {%tpl_tabela_esquema}.{%tpl_tabela_nome}
	FOR EACH ROW
		WHEN	(fc_getsession('__disable_audit__') IS NULL
		AND		 fc_getsession('__disable_audit_{%tpl_tabela_esquema}_{%tpl_tabela_nome}__') IS NULL)
	EXECUTE PROCEDURE {%tpl_tabela_esquema}.fc_auditoria_tabela_{%tpl_tabela_hash}();

CREATE TRIGGER tg_auditoria_update_{%tpl_tabela_hash}
	AFTER UPDATE ON {%tpl_tabela_esquema}.{%tpl_tabela_nome}
	FOR EACH ROW
		WHEN	(NEW.* IS DISTINCT FROM OLD.*
		AND		 fc_getsession('__disable_audit__') IS NULL
		AND		 fc_getsession('__disable_audit_{%tpl_tabela_esquema}_{%tpl_tabela_nome}__') IS NULL)
	EXECUTE PROCEDURE {%tpl_tabela_esquema}.fc_auditoria_tabela_{%tpl_tabela_hash}();

\$SQL$::TEXT;

$$
LANGUAGE sql;


SQL
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function downFuncao()
    {
        DB::connection()->getPdo()->exec(<<<SQL

/***
 *
 *  TEMPLATE de PL para auditoria de tabelas
 *
 *  Variáveis do Template
 *   . %tpl_tabela_esquema               = nome do esquema da tabela a ser auditada
 *   . %tpl_tabela_nome                  = nome da tabela a ser auditada
 *   . %tpl_tabela_hash                  = hash de identificacao para nomes dos objetos (funcao, trigger) de auditoria
 *   . %tpl_data_hora                    = data e hora da criação ou última atualização
 *   . %tpl_bloco_codigo_definicao_chave = bloco de codigo da definicao da chave (xChave)
 *   . %tpl_bloco_codigo_update          = bloco de codigo a ser processado no UPDATE para montar Array com valores realmente alterados
 *   . %tpl_array_campo_nome             = definicao de array com nome dos campos da tabela a ser auditada
 *   . %tpl_array_insert_campo_valor_old = definicao de array com valores OLD dos campos para INSERT
 *   . %tpl_array_insert_campo_valor_new = definicao de array com valores NEW dos campos para INSERT
 *   . %tpl_array_delete_campo_valor_old = definicao de array com valores OLD dos campos para DELETE
 *   . %tpl_array_delete_campo_valor_new = definicao de array com valores NEW dos campos para DELETE
 *
 */


CREATE OR REPLACE FUNCTION configuracoes.fc_auditoria_template() RETURNS TEXT AS
$$
	SELECT \$SQL$
CREATE OR REPLACE FUNCTION {%tpl_tabela_esquema}.fc_auditoria_tabela_{%tpl_tabela_hash}() RETURNS trigger AS
\$AUDITORIA$
/***
 * Função para registro de auditoria de INSERT/UPDATE/DELETE
 * . tabela    = {%tpl_tabela_esquema}.{%tpl_tabela_nome}
 * . hash      = {%tpl_tabela_hash}
 * . timestamp = {%tpl_data_hora}
 */
DECLARE
	xMudancas configuracoes.tp_auditoria_mudancas_campo;
	xChave    configuracoes.tp_auditoria_chave_primaria;

	tDataHora   TIMESTAMP   DEFAULT COALESCE(fc_getsession('DB_datausu')::TIMESTAMP, NOW());
	sLogin      VARCHAR(20) DEFAULT COALESCE(fc_getsession('DB_login'), 'dbseller');
	iLogsAcessa INTEGER     DEFAULT fc_getsession('DB_acessado')::INTEGER;
	iInstit     INTEGER     DEFAULT fc_getsession('DB_instit')::INTEGER;
	iUsuario    INTEGER     DEFAULT COALESCE(fc_getsession('DB_id_usuario')::INTEGER, 1);
	dData       DATE        DEFAULT tDataHora::DATE;

	rRegistro   {%tpl_tabela_esquema}.{%tpl_tabela_nome}%ROWTYPE;
	rLogsAcessa RECORD;

	aCampo      TEXT[];
	aValorOld   TEXT[];
	aValorNew   TEXT[];
BEGIN
	IF TG_OP = 'DELETE' THEN
		rRegistro := OLD;
	ELSE
		rRegistro := NEW;
	END IF;

	/* Caso não tenha instituição definida na sessão, busca padrão prefeitura */
	IF iInstit IS NULL THEN
		SELECT	codigo
		INTO	iInstit
		FROM	db_config
		WHERE	prefeitura IS TRUE
		ORDER	BY codigo
		LIMIT	1;

		IF iInstit IS NULL THEN
			SELECT	codigo
			INTO	iInstit
			FROM	db_config
			ORDER	BY codigo
			LIMIT	1;
			IF iInstit IS NULL THEN
				RAISE EXCEPTION 'Impossível realizar auditoria. Nenhuma instituição encontrada nesta base de dados!';
			END IF;
		END IF;
	END IF;

	/* Caso não tenha um db_logsacessa definido na sessão, então devemos gerar um automático */
	IF iLogsAcessa IS NULL THEN
		/* Ir direto na particao da db_logsacessa com bloco protegido
		   porque a mesma pode não existir */
		BEGIN
			EXECUTE	format('
				SELECT	codsequen
				FROM	db_logsacessa_%s_%s
				WHERE	instit  = %L
				AND		data    = %L
				AND		hora    = %L
				AND		arquivo = %L',
				to_char(tDataHora, 'FMYYYYMM'), iInstit, iInstit, dData,
				to_char(tDataHora, 'HH24:MI:SS'), 'classes/db_{%tpl_tabela_nome}_classe.php')
			INTO	iLogsAcessa;
		EXCEPTION
			WHEN undefined_table THEN
				iLogsAcessa := NULL;
		END;

		IF iLogsAcessa IS NULL THEN
			SELECT	b.codmod, c.id_item
			INTO	rLogsAcessa
			FROM	db_sysarquivo a
					JOIN db_sysarqmod b ON b.codarq = a.codarq
					JOIN db_auditoria_migracao_depara_codarq_codmod_id_modulo c ON c.codarq = b.codarq AND c.codmod = b.codmod
			WHERE	nomearq = '{%tpl_tabela_nome}';

			/* @TODO: verificar se realmente encontrou informações no dicionário de dados */
			INSERT INTO db_logsacessa (
				codsequen, data, hora, arquivo, obs, instit, auditoria, id_usuario, id_modulo, id_item
			) VALUES (
				NEXTVAL('db_logsacessa_codsequen_seq'), dData,
				to_char(tDataHora, 'HH24:MI:SS'), 'classes/db_{%tpl_tabela_nome}_classe.php',
				'LogsAcessa Automatico DML Manual', iInstit, TRUE,
				iUsuario, rLogsAcessa.codmod, rLogsAcessa.id_item
			);

			iLogsAcessa = CURRVAL('db_logsacessa_codsequen_seq');

			/* Marcamos como já alterado para não executar novamente o update para cada linha */
			PERFORM fc_putsession('__logsacessa_'||iLogsAcessa||'_auditoria_alterado__', 't');
		END IF;
	END IF;

	/* Define chave primária da tabela */
	{%tpl_bloco_codigo_definicao_chave}

	/* Verifica mudanças realizadas de acordo com operação */
	IF TG_OP = 'INSERT' THEN

		xMudancas := ROW(
			ARRAY[ {%tpl_array_campo_nome} ],
			ARRAY[ {%tpl_array_insert_campo_valor_old} ],
			ARRAY[ {%tpl_array_insert_campo_valor_new} ] );

	ELSIF TG_OP = 'UPDATE' THEN

		IF ROW(OLD.*) IS DISTINCT FROM ROW(NEW.*) THEN

			{%tpl_bloco_codigo_update}

		ELSE
			RETURN rRegistro;
		END IF;

		xMudancas := ROW(aCampo, aValorOld, aValorNew);
	ELSE

		xMudancas := ROW(
			ARRAY[ {%tpl_array_campo_nome} ],
			ARRAY[ {%tpl_array_delete_campo_valor_old} ],
			ARRAY[ {%tpl_array_delete_campo_valor_new} ] );

	END IF;

	/* Grava dados na auditoria */
	INSERT INTO db_auditoria (
		sequencial,
		esquema,
		tabela,
		operacao,
		datahora_sessao,
		usuario,
		chave,
		mudancas,
		logsacessa,
		instit,
		gatilho /* TRUE=via trigger, senao NULL ou FALSE=script de migracao */
	) VALUES (
		NEXTVAL('db_acount_id_acount_seq'), -- FUTURAMENTE REMOVER
		TG_TABLE_SCHEMA,
		TG_TABLE_NAME,
		SUBSTR(TG_OP,1,1),
		tDataHora,
		sLogin,
		xChave,
		xMudancas,
		iLogsAcessa,
		iInstit,
		TRUE
	);

	/* Se o log de acesso ainda não foi alterado nesta sessão, então alteramos ... */
	IF fc_getsession('__logsacessa_'||iLogsAcessa||'_auditoria_alterado__') IS NULL THEN
		/* ... para indicar que possui auditoria */
		UPDATE	db_logsacessa
		SET		auditoria = TRUE
		WHERE	instit    = iInstit
		AND		data      = dData
		AND		codsequen = iLogsAcessa
		AND		auditoria IS FALSE;

		/* Marcamos como já alterado para não executar novamente o update para cada linha */
		PERFORM fc_putsession('__logsacessa_'||iLogsAcessa||'_auditoria_alterado__', 't');
	END IF;

	RETURN rRegistro;
END;
\$AUDITORIA$
SECURITY DEFINER
LANGUAGE plpgsql;

/* @TODO: melhorar uso das variáveis de sessão __disable_audit__ por conta da noção de ON/OFF */
CREATE TRIGGER tg_auditoria_insert_delete_{%tpl_tabela_hash}
	AFTER INSERT OR DELETE ON {%tpl_tabela_esquema}.{%tpl_tabela_nome}
	FOR EACH ROW
		WHEN	(fc_getsession('__disable_audit__') IS NULL
		AND		 fc_getsession('__disable_audit_{%tpl_tabela_esquema}_{%tpl_tabela_nome}__') IS NULL)
	EXECUTE PROCEDURE {%tpl_tabela_esquema}.fc_auditoria_tabela_{%tpl_tabela_hash}();

CREATE TRIGGER tg_auditoria_update_{%tpl_tabela_hash}
	AFTER UPDATE ON {%tpl_tabela_esquema}.{%tpl_tabela_nome}
	FOR EACH ROW
		WHEN	(NEW.* IS DISTINCT FROM OLD.*
		AND		 fc_getsession('__disable_audit__') IS NULL
		AND		 fc_getsession('__disable_audit_{%tpl_tabela_esquema}_{%tpl_tabela_nome}__') IS NULL)
	EXECUTE PROCEDURE {%tpl_tabela_esquema}.fc_auditoria_tabela_{%tpl_tabela_hash}();

\$SQL$::TEXT;

$$
LANGUAGE sql;

SQL
        );
    }
}
