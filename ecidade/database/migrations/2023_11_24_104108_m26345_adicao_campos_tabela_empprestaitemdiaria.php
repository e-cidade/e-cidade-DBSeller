<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26345AdicaoCamposTabelaEmpprestaitemdiaria extends Migration
{
    private function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_syscampo( codcam ,nomecam ,conteudo ,descricao ,valorinicial ,rotulo ,tamanho ,nulo ,maiusculo ,autocompl ,aceitatipo ,tipoobj ,rotulorel ) values ( 1015558 ,'e446_estadodestino' ,'text' ,'Estado de destino.' ,'' ,'Estado de Destino' ,1 ,'true' ,'false' ,'false' ,0 ,'text' ,'Estado de Destino' );
insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 1010329 ,1015558 ,11 ,0 );
insert into db_syscampo( codcam ,nomecam ,conteudo ,descricao ,valorinicial ,rotulo ,tamanho ,nulo ,maiusculo ,autocompl ,aceitatipo ,tipoobj ,rotulorel ) values ( 1015559 ,'e446_paisdestino' ,'varchar(255)' ,'País de destino.' ,'' ,'País de destino' ,255 ,'true' ,'false' ,'false' ,0 ,'text' ,'País de destino' );
insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 1010329 ,1015559 ,12 ,0 );
SQL
        );
    }

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_sysarqcamp where codcam = 1015558;
delete from db_syscampo where codcam = 1015558;
delete from db_sysarqcamp where codcam = 1015559;
delete from db_syscampo where codcam = 1015559;
SQL
        );
    }

    private function upEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
ALTER TABLE empenho.empprestaitemdiaria ADD COLUMN e446_estadodestino text DEFAULT NULL;
ALTER TABLE empenho.empprestaitemdiaria ADD COLUMN e446_paisdestino varchar(255) DEFAULT NULL;
SQL
        );
    }

    private function downEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
ALTER TABLE empenho.empprestaitemdiaria DROP COLUMN e446_estadodestino;
ALTER TABLE empenho.empprestaitemdiaria DROP COLUMN e446_paisdestino;
SQL
        );
    }

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
}
