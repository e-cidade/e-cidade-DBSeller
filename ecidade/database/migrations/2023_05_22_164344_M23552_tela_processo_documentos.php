<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23552TelaProcessoDocumentos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upCriarDocumentoMensagem();
        $this->upMudarTipoDeProcessoDeProcessosEletronicosQueSaoMensagens();
    }

    public function upCriarDocumentoMensagem()
    {
        DB::connection()->getPdo()->exec(<<<SQL
INSERT INTO protocolo.tipoprocesso
(p109_sequencial, p109_nome)
VALUES(4, 'MENSAGEM');


SQL
        );
    }

    public function upMudarTipoDeProcessoDeProcessosEletronicosQueSaoMensagens()
    {
        DB::connection()->getPdo()->exec(<<<SQL
select  fc_putsession('DB_anousu','2023');
select  fc_putsession('DB_id_usuario','1');
select  fc_putsession('DB_instit','1');
select  fc_putsession('DB_coddepto','0');
with processos_mensagem  as (
	select
		distinct
	                 protocolo.protprocesso.*
	from
		procandam
	inner join processosvinculados on
		p92_processofilho = p61_codproc
	inner join procandamint on
		p78_codandam = p61_codandam
	left join protprocessodocumento on
		p01_procandamint = p78_sequencial
		and p01_estorage is true
	left join protocolo.historicovisualizacaoprocandam on
		p113_procandamint_id = p78_sequencial
	left join db_usuarios on
		id_usuario = p113_usuario_id
	inner join protocolo.protprocesso on
		protocolo.protprocesso.p58_codproc = processosvinculados.p92_processofilho
	where
		protocolo.protprocesso.p58_obs ilike '%Mensagem criada%'
)
update  protocolo.protprocesso set p58_tipoprocesso = 4 where p58_codproc  in (

  select p58_codproc from processos_mensagem
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
        $this->downMudarTipoDeProcessoDeProcessosEletronicosQueSaoMensagens();
        $this->downCriarDocumentoMensagem();
    }


    public function downCriarDocumentoMensagem()
    {
        DB::connection()->getPdo()->exec(<<<SQL
   DELETE FROM  protocolo.tipoprocesso WHERE  p109_sequencial = 4;
SQL
        );
    }


    public function downMudarTipoDeProcessoDeProcessosEletronicosQueSaoMensagens()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from historico_tipo_processo where p112_tipoprocesso = 4;
select  fc_putsession('DB_anousu','2023');
select  fc_putsession('DB_id_usuario','1');
select  fc_putsession('DB_instit','1');
select  fc_putsession('DB_coddepto','0');
with processos_mensagem  as (
	select
		distinct
	                 protocolo.protprocesso.*
	from
		procandam
	inner join processosvinculados on
		p92_processofilho = p61_codproc
	inner join procandamint on
		p78_codandam = p61_codandam
	left join protprocessodocumento on
		p01_procandamint = p78_sequencial
		and p01_estorage is true
	left join protocolo.historicovisualizacaoprocandam on
		p113_procandamint_id = p78_sequencial
	left join db_usuarios on
		id_usuario = p113_usuario_id
	inner join protocolo.protprocesso on
		protocolo.protprocesso.p58_codproc = processosvinculados.p92_processofilho
	where
		protocolo.protprocesso.p58_obs ilike '%Mensagem criada%'
)
update  protocolo.protprocesso set p58_tipoprocesso = 2 where p58_codproc  in (

  select p58_codproc from processos_mensagem
);
SQL
        );
    }
}
