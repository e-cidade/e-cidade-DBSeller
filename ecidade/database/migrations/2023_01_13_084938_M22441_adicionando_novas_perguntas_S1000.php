<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M22441AdicionandoNovasPerguntasS1000 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
            insert into habitacao.avaliacaopergunta( db103_sequencial ,db103_avaliacaotiporesposta ,db103_avaliacaogrupopergunta ,db103_descricao ,db103_identificador ,db103_obrigatoria ,db103_ativo ,db103_ordem ,db103_tipo ,db103_mascara ,db103_dblayoutcampo ,db103_perguntaidentificadora ,db103_camposql ,db103_identificadorcampo ,db103_somenteleitura ) values ( 4000370 ,2 ,3000196 ,'Data da transformação em sociedade de fins lucrativos - Lei 11.096/2005' ,'data-transformacao-sociedade-lucrativos' ,'false' ,'true' ,10 ,5 ,'' ,0 ,'false' ,'' ,'dtTrans11096' ,'false' );
            insert into habitacao.avaliacaoperguntaopcao( db104_sequencial ,db104_avaliacaopergunta ,db104_descricao ,db104_identificador ,db104_aceitatexto ,db104_peso ,db104_valorresposta ,db104_identificadorcampo ) values ( 4001496 ,4000370 ,'' ,'63c7d90125bd9' ,'true' ,0 ,'' ,'dtTrans11096' );
            insert into habitacao.avaliacaopergunta( db103_sequencial ,db103_avaliacaotiporesposta ,db103_avaliacaogrupopergunta ,db103_descricao ,db103_identificador ,db103_obrigatoria ,db103_ativo ,db103_ordem ,db103_tipo ,db103_mascara ,db103_dblayoutcampo ,db103_perguntaidentificadora ,db103_camposql ,db103_identificadorcampo ,db103_somenteleitura ) values ( 4000371 ,1 ,3000196 ,'Indicador de tributação sobre a folha de pagamento - PIS e COFINS.' ,'indicador-tributacao' ,'false' ,'true' ,11 ,1 ,'' ,0 ,'false' ,'' ,'indTribFolhaPisCofins' ,'false' );
            insert into habitacao.avaliacaoperguntaopcao( db104_sequencial ,db104_avaliacaopergunta ,db104_descricao ,db104_identificador ,db104_aceitatexto ,db104_peso ,db104_valorresposta ,db104_identificadorcampo ) values ( 4001497 ,4000371 ,'Sim' ,'sim63c7d9050db56' ,'false' ,0 ,'S' ,'indTribFolhaPisCofins_S' );
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = <<<SQL
            delete from habitacao.avaliacaoperguntaopcao where db104_sequencial = 4001496 and db104_avaliacaopergunta = 4000370;
            delete from habitacao.avaliacaoperguntaopcao where db104_sequencial = 4001497 and db104_avaliacaopergunta = 4000371;
            delete from habitacao.avaliacaopergunta where db103_sequencial = 4000370 and db103_avaliacaogrupopergunta = 3000196;
            delete from habitacao.avaliacaopergunta where db103_sequencial = 4000371 and db103_avaliacaogrupopergunta = 3000196;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
