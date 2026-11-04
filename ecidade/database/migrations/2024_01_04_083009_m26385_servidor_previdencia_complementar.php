<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26385ServidorPrevidenciaComplementar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec($this->menu());
        DB::connection()->getPdo()->exec($this->estrutura());
        DB::connection()->getPdo()->exec($this->dicionario());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->getPdo()->exec($this->menu(true));
        DB::connection()->getPdo()->exec($this->estrutura(true));
    }

    public function menu($rollback = false)
    {
        $sql = <<<SQL
            insert into configuracoes.db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229022 ,'Previdência Complementar' ,'Previdência Complementar' ,'web/recursos-humanos/pessoal/previdencia_complementar_servidor' ,'1' ,'1' ,'Cadastro/Exclusão de Previdência Complementar do Servidor.' ,'true' );
            insert into configuracoes.db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 4504 ,229022 ,11 ,952 );
SQL;
        if ($rollback) {
            $sql = <<<SQL
                delete from configuracoes.db_menu where id_item_filho = 229022 AND modulo = 952;
                delete from configuracoes.db_itensmenu where id_item = 229022;
SQL;
        }
        return $sql;
    }

    public function estrutura($rollback = false)
    {
        $sql = <<<SQL
            CREATE TABLE pessoal.servidorprevidenciacomplementar(
                rh312_sequencial serial,
                rh312_instituicao int4 NOT NULL,
                rh312_matricula int4 NOT NULL,
                rh312_tipoprevidencia int4 NOT NULL,
                rh312_cnpj varchar(14) not null,
                rh312_deducaorelativa float default 0,
                rh312_contribuicaopatrocinador float default 0,
                CONSTRAINT servidorprevidenciacomplementar_sequ_pk PRIMARY KEY (rh312_sequencial));
            CREATE UNIQUE INDEX rh312_matricula ON pessoal.servidorprevidenciacomplementar(rh312_matricula);

            SELECT configuracoes.fc_auditoria_cria_funcao('pessoal.servidorprevidenciacomplementar');
SQL;
        if ($rollback) {
            $sql = <<<SQL
                SELECT configuracoes.fc_auditoria_remove_funcao('pessoal.servidorprevidenciacomplementar');
                DROP INDEX rh312_matricula;
                DROP TABLE pessoal.servidorprevidenciacomplementar;
                DROP SEQUENCE IF EXISTS pessoal.servidorprevidenciacomplementar_rh312_sequencial_seq;
SQL;
        }
        return $sql;
    }

    public function dicionario()
    {
        return <<<SQL
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            COMMENT ON TABLE pessoal.servidorprevidenciacomplementar IS
            '{
                "descricao": "Configuração de previdencia complementar",
                "sigla": "rh312",
                "dataincl": "2024-01-04",
                "rotulo": "Configuração de previdencia complementar",
                "tipotabela": 1,
                "naolibclass": false,
                "naolibfunc": false,
                "naolibprog": false,
                "naolibform": false
            }';
        
            COMMENT ON COLUMN pessoal.servidorprevidenciacomplementar.rh312_sequencial IS '
            {
                "descricao": "Chave primária da tabela",
                "rotulo": "rh312_sequencial",
                "rotulorel": "rh312_sequencial",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN pessoal.servidorprevidenciacomplementar.rh312_instituicao IS '
            {
                "descricao": "Instituição configurada",
                "rotulo": "rh312_instituicao",
                "rotulorel": "rh312_instituicao",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN pessoal.servidorprevidenciacomplementar.rh312_matricula IS '
            {
                "descricao": "Matrícula do Servidor",
                "rotulo": "rh312_matricula",
                "rotulorel": "rh312_matricula",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN pessoal.servidorprevidenciacomplementar.rh312_tipoprevidencia IS '
            {
                "descricao": "Tipo de Previdencia do Servidor",
                "rotulo": "rh312_tipoprevidencia",
                "rotulorel": "rh312_tipoprevidencia",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            
            COMMENT ON COLUMN pessoal.servidorprevidenciacomplementar.rh312_deducaorelativa IS '
            {
                "descricao": "Valor da Dedução Relativa",
                "rotulo": "rh312_deducaorelativa",
                "rotulorel": "rh312_deducaorelativa",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 4,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN pessoal.servidorprevidenciacomplementar.rh312_contribuicaopatrocinador IS '
            {
                "descricao": "Valor da Contribuição do Patrocinador",
                "rotulo": "rh312_contribuicaopatrocinador",
                "rotulorel": "rh312_contribuicaopatrocinador",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 4,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN pessoal.servidorprevidenciacomplementar.rh312_cnpj IS '
            {
                "descricao": "CNPJ da entidade de Previdencia Complementar",
                "rotulo": "rh312_cnpj",
                "rotulorel": "rh312_cnpj",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 3,
                "tamanho": 14,
                "tipoobj": "text"
            }';
    
            SELECT fc_gera_dicionario_apartir_tabela('pessoal', 'servidorprevidenciacomplementar');

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL;
    }
}
