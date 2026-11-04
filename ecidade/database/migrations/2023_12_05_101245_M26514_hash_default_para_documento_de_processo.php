<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26514HashDefaultParaDocumentoDeProcesso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(
            <<<SQL
CREATE EXTENSION IF NOT EXISTS  pgcrypto;
update
	protocolo.protprocessodocumento
set
	p01_documento_hash =  encode(digest(protocolo.protprocessodocumento.p01_sequencial::varchar,
	'sha1'),
	'hex')
where
	protocolo.protprocessodocumento.p01_sequencial in (
	select
		p01_sequencial
	from
		protocolo.protprocessodocumento
	where
		p01_documento_hash is null
		or p01_documento_hash = ''
		or p01_documento_hash = '0'
    );
      
alter table protocolo.protprocessodocumento alter column p01_documento_hash SET default encode(digest(CURRVAL('protprocessodocumento_p01_sequencial_seq')::varchar, 'sha1'),'hex');
alter table protocolo.protprocessodocumento alter column p01_documento_hash set NOT NULL;
ALTER TABLE protocolo.protprocessodocumento ADD UNIQUE (p01_documento_hash);
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

    }
}
