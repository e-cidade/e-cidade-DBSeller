<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28290RemoveAuditoria extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->funcaoUp();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->funcaoDown();
    }

    private function funcaoUp()
    {
        DB::connection()->getPdo()->exec(<<<SQL

-- Funcao para remover trigger de auditoria das tabelas
CREATE OR REPLACE FUNCTION configuracoes.fc_auditoria_remove_funcao(TEXT) RETURNS VOID AS
$$
DECLARE
	sEsquema	TEXT;
	sTabela		TEXT;
	aTabela		TEXT[];

	sSQL1		TEXT;
    sSQL2		TEXT;

	rTabela		RECORD;
	rTrigger	RECORD;
BEGIN

	-- Separa Esquema e Tabela
	IF position('.' in $1) > 0 THEN
		aTabela  := string_to_array($1, '.');
		sEsquema := aTabela[1];
		sTabela  := aTabela[2];
	ELSE
		sEsquema := 'public';
		sTabela  := $1;
	END IF;

	FOR rTabela IN
		SELECT	*
		FROM	configuracoes.vw_auditoria_lista_tabelas
		WHERE	esquema LIKE sEsquema
		AND		nome    LIKE sTabela
	LOOP
		RAISE DEBUG 'Removendo funcao e trigger de auditoria da tabela %.%',
			rTabela.esquema, rTabela.nome;

		sSql1 = 'SELECT DISTINCT (t.tgfoid::regproc::text) AS funcao ';
        sSql1 = sSql1||'FROM pg_catalog.pg_class c ';
        sSql1 = sSql1||'     JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace ';
        sSql1 = sSql1||'     JOIN pg_catalog.pg_trigger t ON t.tgrelid = c.oid ';
        sSql1 = sSql1||'WHERE c.relname OPERATOR(pg_catalog.~) ''^('||rTabela.nome||')$'' ';
        sSql1 = sSql1||'  AND n.nspname OPERATOR(pg_catalog.~) ''^('||rTabela.esquema||')$'' ';
        sSql1 = sSql1||'  AND t.tgisinternal IS FALSE ';
        sSql1 = sSql1||'  AND t.tgname ~ ''^tg_auditoria_(insert_delete|update)_'' ';
        sSql1 = sSql1||'  AND t.tgfoid::regproc::text ~ ''fc_auditoria_tabela_'' ';

        RAISE DEBUG 'SQL busca funcao %', sSQL1;

		FOR rTrigger IN EXECUTE sSQL1
		LOOP
			-- Apaga Funcao e Trigger(s) de Auditoria
			RAISE DEBUG 'Removendo funcao %', rTrigger.funcao;
			sSQL2 := format('DROP FUNCTION IF EXISTS %s() CASCADE;', rTrigger.funcao);
			EXECUTE sSQL2;
		END LOOP;
	END LOOP;

	RETURN;
END;

$$
LANGUAGE plpgsql;

-- Wrapper para remover funcoes para todas tabelas
CREATE OR REPLACE FUNCTION configuracoes.fc_auditoria_remove_funcao() RETURNS VOID AS
$$
	SELECT configuracoes.fc_auditoria_remove_funcao('%.%');
$$
LANGUAGE sql;

SQL
        );
    }

    private function funcaoDown()
    {
        DB::connection()->getPdo()->exec(<<<SQL

-- Funcao para remover trigger de auditoria das tabelas
CREATE OR REPLACE FUNCTION configuracoes.fc_auditoria_remove_funcao(TEXT) RETURNS VOID AS
$$
DECLARE
	sEsquema	TEXT;
	sTabela		TEXT;
	aTabela		TEXT[];

	sSQL		TEXT;

	rTabela		RECORD;
	rTrigger	RECORD;
BEGIN

	-- Separa Esquema e Tabela
	IF position('.' in $1) > 0 THEN
		aTabela  := string_to_array($1, '.');
		sEsquema := aTabela[1];
		sTabela  := aTabela[2];
	ELSE
		sEsquema := 'public';
		sTabela  := $1;
	END IF;

	FOR rTabela IN
		SELECT	*
		FROM	configuracoes.vw_auditoria_lista_tabelas
		WHERE	esquema LIKE sEsquema
		AND		nome    LIKE sTabela
	LOOP
		RAISE DEBUG 'Removendo funcao e trigger de auditoria da tabela %.%',
			rTabela.esquema, rTabela.nome;

		FOR rTrigger IN
			SELECT	DISTINCT tgfoid::regproc::text AS funcao
			FROM	pg_trigger
			WHERE	tgisinternal IS FALSE
			AND		tgfoid::regproc::text ~ '^fc_auditoria_tabela_'
			AND		tgrelid = format('%I.%I', rTabela.esquema, rTabela.nome)::regclass
		LOOP
			-- Apaga Funcao e Trigger(s) de Auditoria
			RAISE DEBUG 'Removendo funcao %', rTrigger.funcao;
			sSQL := format('DROP FUNCTION IF EXISTS %s() CASCADE;', rTrigger.funcao);
			EXECUTE sSQL;
		END LOOP;
	END LOOP;

	RETURN;
END;
$$
LANGUAGE plpgsql;

-- Wrapper para remover funcoes para todas tabelas
CREATE OR REPLACE FUNCTION configuracoes.fc_auditoria_remove_funcao() RETURNS VOID AS
$$
	SELECT configuracoes.fc_auditoria_remove_funcao('%.%');
$$
LANGUAGE sql;

SQL
        );
    }
}
