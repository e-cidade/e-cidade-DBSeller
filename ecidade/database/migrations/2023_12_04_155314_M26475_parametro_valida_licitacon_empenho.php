<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26475ParametroValidaLicitaconEmpenho extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionario();
        $this->upEstrutura();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downDicionario();
        $this->downEstrutura();
    }

    public function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            update db_sysarquivo set nomearq = 'acordoparam' where codarq = 1010775;
            update db_syscampo set nomecam = 'ac59_homologacaoauto' where codcam = 1013060;

            insert into db_syscampo values(1015573,'ac59_validalicitacon','int4','Permite a emissão do empenho com ou sem aviso, ou não permite com aviso caso a licitação não encontra-se no portal LICITACON TCE/RS.','0', 'Valida Licitacon',4,'f','f','f',1,'text','Valida Licitacon');
            insert into db_syscampodef values(1015573,'1','');
            insert into db_syscampodef values(1015573,'2','');
            insert into db_syscampodef values(1015573,'3','');
            insert into db_sysarqcamp values(1010775,1015573,5,0);

            update db_itensmenu set descricao = 'Parâmetros Globais' where id_item = 228399;
SQL
        );
    }

    public function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            update db_sysarquivo set nomearq = 'homologacaoacordo' where codarq = 1010775;
            update db_syscampo set nomecam = 'ac59_automatica' where codcam = 1013060;

            delete from db_sysarqcamp where codarq = 1010775 and codcam = 1015573;
            delete from db_syscampodef where codcam = 1015573;
            delete from db_syscampo where codcam = 1015573;

            update db_itensmenu set descricao = 'Homologação' where id_item = 228399;
SQL
        );
    }

    public function upEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            alter table homologacaoacordo RENAME TO acordoparam;
            alter table acordoparam RENAME ac59_automatica TO ac59_homologacaoauto;
            alter table acordoparam add column ac59_validalicitacon integer default 1;
SQL
        );
    }

    public function downEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            alter table acordoparam RENAME TO homologacaoacordo;
            alter table homologacaoacordo RENAME ac59_homologacaoauto TO ac59_automatica;
            alter table homologacaoacordo drop column ac59_validalicitacon;
SQL
        );
    }
}
