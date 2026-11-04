<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27434AdicionaInformacoesDicionarioComprasPncp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec("
            insert into db_syscampo(codcam ,nomecam ,conteudo ,descricao ,valorinicial ,rotulo ,tamanho ,nulo ,maiusculo ,autocompl ,aceitatipo ,tipoobj ,rotulorel ) values ( 1015635 ,'pn03_solicita' ,'int4' ,'Solicitação de compras da qual a compra se origina' ,'' ,'Solicitação' ,1 ,'true' ,'false' ,'false' ,1 ,'text' ,'Solicitação' );
            insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 1011003 ,1015635 ,10 ,0 );
            UPDATE db_syscampo SET nulo = 'true' where codcam = 1014624;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->getPdo()->exec("
            delete from db_sysarqcamp where codcam = 1015635;
            delete from db_syscampodef where codcam = 1015635;
            delete from db_syscampo where codcam = 1015635;
            UPDATE db_syscampo SET nulo = 'false' where codcam = 1014624;
        ");
    }
}
