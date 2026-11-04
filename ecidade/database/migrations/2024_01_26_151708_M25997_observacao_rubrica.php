<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25997ObservacaoRubrica extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
        drop table if exists pessoal.rhobservacaorubricaservidor;
        CREATE TYPE TIPOS_FOLHA AS ENUM('salario', 'decimo', 'fixo', 'suplementar', 'complementar', 'adiantamento', 'ferias', 'rescisao');

        create table pessoal.rhobservacaorubricaservidor (
            rh315_sequencial serial primary key,
            rh315_matricula integer,
            rh315_rubrica varchar(4), 
            rh315_anousu integer,
            rh315_mesusu integer,
            rh315_observacao text,
            rh315_tipo TIPOS_FOLHA,
            rh315_instit integer
        );

        ALTER TABLE pessoal.rhobservacaorubricaservidor 
            ADD CONSTRAINT 
            rhobservacaorubricaservidor_mt_rub_ae_me_tp_in UNIQUE (rh315_matricula, rh315_rubrica, rh315_anousu, rh315_mesusu, rh315_tipo);

        CREATE INDEX rhobservacaorubricaservidor_mt_ae_me_tp_in ON rhobservacaorubricaservidor USING btree(rh315_matricula, rh315_anousu, rh315_mesusu, rh315_tipo);
        SELECT configuracoes.fc_auditoria_cria_funcao('pessoal.rhobservacaorubricaservidor');

SQL;
        DB::connection()->getPdo()->exec($sql);

        $this->upDicionario();
        $this->criaMenu();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = <<<SQL
        delete from db_menu where id_item = 229037;
        delete from db_itensmenu where id_item_filho = 229037;
        SELECT fc_remove_dicionario_tabela('pessoal', 'rhobservacaorubricaservidor');
        SELECT configuracoes.fc_auditoria_remove_funcao('pessoal.rhobservacaorubricaservidor');
        drop type TIPOS_FOLHA;
        DROP TABLE pessoal.rhobservacaorubricaservidor;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    public function upDicionario()
    {
        $sql = <<<SQL
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

        COMMENT ON TABLE pessoal.rhobservacaorubricaservidor IS
        '{
            "descricao": "Adicionando observacao na rubrica no ponto / calculo do servidor",
            "sigla": "rh315",
            "dataincl": "2024-01-29",
            "rotulo": "Observação da rubrica do servidor",
            "tipotabela": 1,
            "naolibclass": false,
            "naolibfunc": false,
            "naolibprog": false,
            "naolibform": false
        }';

        COMMENT ON COLUMN pessoal.rhobservacaorubricaservidor.rh315_sequencial IS '
        {
            "descricao": "Chave primária da tabela",
            "rotulo": "rh315_sequencial",
            "rotulorel": "rh315_sequencial",
            "maiusculo": false,
            "autocompl": false,
            "aceitatipo": 1,
            "tamanho": 10,
            "tipoobj": "text"
        }';

        COMMENT ON COLUMN pessoal.rhobservacaorubricaservidor.rh315_matricula IS '
        {
            "descricao": "Matrícula do servidor",
            "rotulo": "rh315_matricula",
            "rotulorel": "rh315_matricula",
            "maiusculo": false,
            "autocompl": false,
            "aceitatipo": 1,
            "tamanho": 10,
            "tipoobj": "text"
        }';

        COMMENT ON COLUMN pessoal.rhobservacaorubricaservidor.rh315_rubrica IS '
        {
            "descricao": "Rubrica do servidor",
            "rotulo": "rh315_rubrica",
            "rotulorel": "rh315_rubrica",
            "maiusculo": false,
            "autocompl": false,
            "aceitatipo": 3,
            "tamanho": 4,
            "tipoobj": "text"
        }';

        COMMENT ON COLUMN pessoal.rhobservacaorubricaservidor.rh315_anousu IS '
        {
            "descricao": "Ano folha",
            "rotulo": "rh315_anousu",
            "rotulorel": "rh315_anousu",
            "maiusculo": false,
            "autocompl": false,
            "aceitatipo": 1,
            "tamanho": 10,
            "tipoobj": "text"
        }';

        COMMENT ON COLUMN pessoal.rhobservacaorubricaservidor.rh315_mesusu IS '
        {
            "descricao": "Ano folha",
            "rotulo": "rh315_mesusu",
            "rotulorel": "rh315_mesusu",
            "maiusculo": false,
            "autocompl": false,
            "aceitatipo": 1,
            "tamanho": 10,
            "tipoobj": "text"
        }';

        COMMENT ON COLUMN pessoal.rhobservacaorubricaservidor.rh315_observacao IS '
        {
            "descricao": "Ano folha",
            "rotulo": "rh315_observacao",
            "rotulorel": "rh315_observacao",
            "maiusculo": false,
            "autocompl": false,
            "aceitatipo": 3,
            "tamanho": 400,
            "tipoobj": "text"
        }';

        COMMENT ON COLUMN pessoal.rhobservacaorubricaservidor.rh315_tipo IS '
        {
            "descricao": "Tipo de Folha",
            "rotulo": "rh315_tipo",
            "rotulorel": "rh315_tipo",
            "maiusculo": false,
            "autocompl": false,
            "aceitatipo": 3,
            "tamanho": 100,
            "tipoobj": "text"
        }';

        COMMENT ON COLUMN pessoal.rhobservacaorubricaservidor.rh315_instit IS '
        {
            "descricao": "Instituição do servidor",
            "rotulo": "rh315_instit",
            "rotulorel": "rh315_instit",
            "maiusculo": false,
            "autocompl": false,
            "aceitatipo": 1,
            "tamanho": 10,
            "tipoobj": "text"
        }';

        SELECT fc_gera_dicionario_apartir_tabela('pessoal', 'rhobservacaorubricaservidor');

        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    protected function criaMenu()
    {
        $sql = <<<SQL
        insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229037 ,'Histórico Rubrica Servidor' ,'Histórico Rubrica Servidor' ,'web/recursos-humanos/pessoal/historico-rubrica' ,'1' ,'1' ,'Histórico Rubrica Servidor' ,'true' );
        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 1797 ,229037 ,63 ,952 );
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
