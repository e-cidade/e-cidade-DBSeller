<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29173CriaEstruturaMaterialfamilias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material.matfamilias', function (Blueprint $table) {
            $table->bigIncrements('m105_sequencial');
            $table->string('m105_descricao');
        });

        Schema::table('compras.pcmater', function (Blueprint $table) {
            $table->bigInteger('pc01_familia')->nullable();
            $table->foreign('pc01_familia')->references('m105_sequencial')->on('matfamilias');
        });

        DB::connection()->getPdo()->exec(<<<SQL
            insert into db_sysarquivo values (1011178, 'matfamilias', 'Famílias do material', 'm105', '2024-10-14', 'Famílias Material', 0, 'f', 'f', 'f', 'f' );
            insert into db_sysarqmod values (13,1011178);

            insert into db_syscampo( codcam ,nomecam ,conteudo ,descricao ,valorinicial ,rotulo ,tamanho ,nulo ,maiusculo ,autocompl ,aceitatipo ,tipoobj ,rotulorel ) values ( 1015673 ,'pc01_familia' ,'int4' ,'Família' ,'' ,'Família' ,6 ,'true' ,'false' ,'false' ,1 ,'text' ,'Família' );
            delete from db_syscampodef where codcam = 1015673;
            insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 855 ,1015673 ,15 ,0 );

            insert into db_syscampo( codcam ,nomecam ,conteudo ,descricao ,valorinicial ,rotulo ,tamanho ,nulo ,maiusculo ,autocompl ,aceitatipo ,tipoobj ,rotulorel ) values ( 1015674 ,'m105_sequencial' ,'int4' ,'Sequencial' ,'' ,'Sequencial' ,8 ,'false' ,'false' ,'false' ,1 ,'text' ,'Sequencial' );
            delete from db_syscampodef where codcam = 1015674;
            insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 1011178 ,1015674 ,1 ,0 );
            insert into db_syscampo( codcam ,nomecam ,conteudo ,descricao ,valorinicial ,rotulo ,tamanho ,nulo ,maiusculo ,autocompl ,aceitatipo ,tipoobj ,rotulorel ) values ( 1015675 ,'m105_descricao' ,'varchar(64)' ,'Descrição do Material' ,'' ,'Descrição' ,64 ,'false' ,'false' ,'false' ,0 ,'text' ,'Descrição' );
            delete from db_syscampodef where codcam = 1015675;
            insert into db_sysarqcamp ( codarq ,codcam ,seqarq ,codsequencia ) values ( 1011178 ,1015675 ,2 ,0 );

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
        Schema::dropIfExists('matfamilias');
        Schema::table('compras.pcmater', function (Blueprint $table) {
            $table->dropColumn('pc01_familia');
        });

        DB::connection()->getPdo()->exec(<<<SQL
            delete from db_sysarqcamp WHERE codcam = 1015674;
            delete from db_syscampo WHERE codcam = 1015675;

            delete from db_sysarqcamp WHERE codcam = 1015673;
            delete from db_syscampo WHERE codcam = 1015673;

            delete from db_sysarqmod where codarq = 1011178;
            delete from db_sysarquivo where codarq = 1011178;
SQL
        );
    }
}
