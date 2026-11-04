<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23000AdicionandoCamposFormulario2210 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            //ATUALIZANDO VERSÃO DO LAYOUT E-SOCIAL
        $sql = <<<SQL
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 3000015, 1);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 3000017, 5);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 3000018, 6);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 3000019, 7);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 3000014, 4);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 3000013, 3);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 3000016, 2);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 3000020, 8);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 3000023, 12);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 3000025, 13);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 3000027, 15);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 3000021, 9);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 3000022, 11);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 3000030, 18);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 3000029, 16);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 3000028, 17);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 3000031, 19);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 3000032, 20);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 3000026, 14);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 3000033, 21);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 3000036, 24);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 3000041, 28);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 3000044, 34);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 4000103, 35);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 4000104, 36);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 4000105, 37);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 4000106, 31);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 4000108, 39);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 4000107, 38);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 4000109, 40);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 4000111, 42);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 4000114, 45);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 4000112, 43);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 4000113, 44);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 4000115, 46);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 4000110, 41);
            insert into recursoshumanos.esocialversaoformulario values ((select  nextval(' esocialversaoformulario_rh211_sequencial_seq')), 'S1.1', 4000116, 48);

            insert into recursoshumanos.esocialversao values ((select  nextval(' esocialversao_rh210_sequencial_seq')), 'S1.1');

            insert into habitacao.avaliacaopergunta( db103_sequencial ,db103_avaliacaotiporesposta ,db103_avaliacaogrupopergunta ,db103_descricao ,db103_identificador ,db103_obrigatoria ,db103_ativo ,db103_ordem ,db103_tipo ,db103_mascara ,db103_dblayoutcampo ,db103_perguntaidentificadora ,db103_camposql ,db103_identificadorcampo ,db103_somenteleitura ) values ( 4000368 ,2 ,4000235 ,'Último dia trabalho' ,'ultimo-dia-trabalho' ,'false' ,'true' ,12 ,5 ,'' ,0 ,'false' ,'' ,'ultDiaTrab' ,'false' );
            insert into habitacao.avaliacaoperguntaopcao( db104_sequencial ,db104_avaliacaopergunta ,db104_descricao ,db104_identificador ,db104_aceitatexto ,db104_peso ,db104_valorresposta ,db104_identificadorcampo ) values ( 4001493 ,4000368 ,'' ,'63b7134a737a1' ,'true' ,0 ,'' ,'ultDiaTrab' );
            insert into habitacao.avaliacaopergunta( db103_sequencial ,db103_avaliacaotiporesposta ,db103_avaliacaogrupopergunta ,db103_descricao ,db103_identificador ,db103_obrigatoria ,db103_ativo ,db103_ordem ,db103_tipo ,db103_mascara ,db103_dblayoutcampo ,db103_perguntaidentificadora ,db103_camposql ,db103_identificadorcampo ,db103_somenteleitura ) values ( 4000369 ,1 ,4000235 ,'Houve afastamento?' ,'houve-afastamento' ,'false' ,'true' ,13 ,1 ,'' ,0 ,'false' ,'' ,'houveAfast' ,'false' );
            insert into habitacao.avaliacaoperguntaopcao( db104_sequencial ,db104_avaliacaopergunta ,db104_descricao ,db104_identificador ,db104_aceitatexto ,db104_peso ,db104_valorresposta ,db104_identificadorcampo ) values ( 4001494 ,4000369 ,'Não' ,'nao63b7134dcfc80' ,'false' ,0 ,'N' ,'houveAfast_N' );
            insert into habitacao.avaliacaoperguntaopcao( db104_sequencial ,db104_avaliacaopergunta ,db104_descricao ,db104_identificador ,db104_aceitatexto ,db104_peso ,db104_valorresposta ,db104_identificadorcampo ) values ( 4001495 ,4000369 ,'Sim' ,'sim63b7134f322e6' ,'false' ,0 ,'S' ,'houveAfast_S' );
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
            delete from recursoshumanos.esocialversaoformulario where rh211_versao = 'S1.1';
            delete from habitacao.avaliacaoperguntaopcao where db104_sequencial = 4001495 and db104_avaliacaopergunta = 4000369;
            delete from habitacao.avaliacaoperguntaopcao where db104_sequencial = 4001494 and db104_avaliacaopergunta = 4000369;
            delete from habitacao.avaliacaoperguntaopcao where db104_sequencial = 4001493 and db104_avaliacaopergunta = 4000368;
            delete from habitacao.avaliacaopergunta where db103_sequencial = 4000368 and db103_avaliacaogrupopergunta = 4000235;
            delete from habitacao.avaliacaopergunta where db103_sequencial = 4000369 and db103_avaliacaogrupopergunta = 4000235;
            delete from recursoshumanos.esocialversao where rh210_versao = 'S1.1';
SQL;
    DB::connection()->getPdo()->exec($sql);
    }
}
