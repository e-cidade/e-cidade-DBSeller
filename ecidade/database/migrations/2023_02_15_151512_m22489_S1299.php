<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M22489S1299 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
     $sql=<<<SQL
        insert into habitacao.avaliacaopergunta( db103_sequencial ,db103_avaliacaotiporesposta ,db103_avaliacaogrupopergunta ,db103_descricao ,db103_identificador ,db103_obrigatoria ,db103_ativo ,db103_ordem ,db103_tipo ,db103_mascara ,db103_dblayoutcampo ,db103_perguntaidentificadora ,db103_camposql ,db103_identificadorcampo ,db103_somenteleitura ) values ( 4000373 ,1 ,4000243 ,'Possui informações de pagamento de rendimentos do trabalho no período de apuração?' ,'evtPgtos-s10' ,'true' ,'true' ,7 ,1 ,'' ,0 ,'false' ,'' ,'evtPgtos' ,'false' );
        insert into habitacao.avaliacaoperguntaopcao( db104_sequencial ,db104_avaliacaopergunta ,db104_descricao ,db104_identificador ,db104_aceitatexto ,db104_peso ,db104_valorresposta ,db104_identificadorcampo ) values ( 4001501 ,4000373 ,'Sim' ,'sim_evtPgtos' ,'false' ,0 ,'S' ,'evtPgtos_S' );
        insert into habitacao.avaliacaoperguntaopcao( db104_sequencial ,db104_avaliacaopergunta ,db104_descricao ,db104_identificador ,db104_aceitatexto ,db104_peso ,db104_valorresposta ,db104_identificadorcampo ) values ( 4001502 ,4000373 ,'Nao' ,'nao_evtPgtos' ,'false' ,0 ,'N' ,'evtPgtos_N' );
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
        $sql=<<<SQL
        delete from habitacao.avaliacaoperguntaopcao where db104_sequencial IN(4001501, 4001502);
        delete from habitacao.avaliacaopergunta where db103_sequencial = 4000373 AND db103_avaliacaogrupopergunta = 4000243;
SQL;
    DB::connection()->getPdo()->exec($sql);
    }
}