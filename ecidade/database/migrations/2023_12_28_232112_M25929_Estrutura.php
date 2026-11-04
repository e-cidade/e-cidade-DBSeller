<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25929Estrutura extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upEstrutura();
        $this->upDicionario();
        $this->addFks();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP TABLE pessoal.lancamentoajudacusto");
        DB::statement("DROP TABLE pessoal.configuracaoajudacusto");
        DB::statement("DROP TABLE pessoal.ajudacusto");
    }

    private function upEstrutura()
    {
        $sql = <<<SQL
        create table pessoal.configuracaoajudacusto(
            rh311_sequencial serial,
            rh311_instit integer not null,
            rh311_valormax float not null,
            rh311_percentual float,
            rh311_limite integer,
            rh311_idademax integer,
            rh311_rubric varchar(4) not null,
            rh311_validadependente boolean default false,
            rh311_rubricdepend varchar(4) ,
            constraint configuracaoajutacusto_sequ_pk primary key (rh311_sequencial),
            constraint configuracaoajutacusto_instit_fk FOREIGN KEY (rh311_instit) REFERENCES configuracoes.db_config(codigo)
        );
 
        create table pessoal.ajudacusto(
            rh312_sequencial serial,
            rh312_instit integer not null,
            rh312_regist integer not null,
            rh312_habilita_dependente boolean default false,
            rh312_dependente integer,
            rh312_local varchar(120) not null,
            rh312_especializacao varchar(120) not null,
            rh312_graduacao varchar(120) not null,
            rh312_unidade_ensino integer not null,
            rh312_valor numeric not null,
            rh312_quantidade integer not null,
            rh312_competencia varchar(7) not null,
            rh312_bolsa_publica boolean default false,
            rh312_ativo boolean default true,
            constraint ajudacusto_sequ_pk primary key (rh312_sequencial),
            constraint ajudacusto_instit_fk FOREIGN KEY (rh312_instit) REFERENCES configuracoes.db_config(codigo),
            constraint ajudacusto_regist_fk FOREIGN KEY (rh312_regist) REFERENCES pessoal.rhpessoal(rh01_regist),
            constraint ajudacusto_cgm_fk FOREIGN KEY (rh312_unidade_ensino) REFERENCES protocolo.cgm(z01_numcgm),
            constraint ajudacusto_depend_fk FOREIGN KEY (rh312_dependente) REFERENCES pessoal.rhdepend(rh31_codigo)
        );

        create table pessoal.lancamentoajudacusto(
            rh313_sequencial serial,
            rh313_ajuda_custo integer not null,
            rh313_ano integer not null,
            rh313_mes integer not null,
            rh313_valor numeric not null,
            rh313_rubric varchar(4) not null,
            rh313_instit integer not null,
            constraint lancamentoajudacusto_sequ_pk primary key (rh313_sequencial),
            constraint lancamentoajudacusto_instit_fk FOREIGN KEY (rh313_instit) REFERENCES configuracoes.db_config(codigo),
            constraint lancamentoajudacusto_ajuda_fk FOREIGN KEY (rh313_ajuda_custo) REFERENCES ajudacusto(rh312_sequencial),
            constraint lancamentoajudacusto_rubric_instit_fk FOREIGN KEY (rh313_rubric, rh313_instit) REFERENCES pessoal.rhrubricas(rh27_rubric, rh27_instit),
            constraint lancamentoajudacusto_ajuda_ano_mes_in unique (rh313_ajuda_custo,rh313_ano,rh313_mes)
        );
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    private function upDicionario()
    {
        $sql = <<<SQL
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;
        
        COMMENT ON TABLE pessoal.configuracaoajudacusto IS
            '{
                "descricao": "Tabela para os configuração da ajuda de custo",
                "sigla": "rh311",
                "dataincl": "2023-12-28",
                "rotulo": "Tabela para os configuração da ajuda de custo",
                "tipotabela": 1,
                "naolibclass": false,
                "naolibfunc": false,
                "naolibprog": false,
                "naolibform": false
            }';
        
        COMMENT ON COLUMN pessoal.configuracaoajudacusto.rh311_sequencial IS '
            {
                "descricao": "Chave primária da tabela",
                "rotulo": "rh311_sequencial",
                "rotulorel": "rh311_sequencial",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

        COMMENT ON COLUMN pessoal.configuracaoajudacusto.rh311_instit IS '
            {
                "descricao": "Instituição configurada",
                "rotulo": "rh311_instit",
                "rotulorel": "rh311_instit",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

        COMMENT ON COLUMN pessoal.configuracaoajudacusto.rh311_valormax IS '
            {
                "descricao": "Valor limite configurado",
                "rotulo": "rh311_valormax",
                "rotulorel": "rh311_valormax",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 4,
                "tamanho": 10,
                "tipoobj": "text"
            }';

        COMMENT ON COLUMN pessoal.configuracaoajudacusto.rh311_percentual IS '
            {
                "descricao": "Valor percentual configurado",
                "rotulo": "rh311_percentual",
                "rotulorel": "rh311_percentual",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 4,
                "tamanho": 10,
                "tipoobj": "text"
            }';

        COMMENT ON COLUMN pessoal.configuracaoajudacusto.rh311_limite IS '
            {
                "descricao": "Limite de dependentes",
                "rotulo": "rh311_limite",
                "rotulorel": "rh311_limite",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

        COMMENT ON COLUMN pessoal.configuracaoajudacusto.rh311_idademax IS '
            {
                "descricao": "Idade máxima dos dependentes",
                "rotulo": "rh311_idademax",
                "rotulorel": "rh311_idademax",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

        COMMENT ON COLUMN pessoal.configuracaoajudacusto.rh311_rubric IS '
            {
                "descricao": "Rubrica de lançamento de ajuda de custo",
                "rotulo": "rh311_rubric",
                "rotulorel": "rh311_rubric",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 3,
                "tamanho": 4,
                "tipoobj": "text"
            }';
    
        COMMENT ON COLUMN pessoal.configuracaoajudacusto.rh311_validadependente IS '
            {
                "descricao": "Valida se dependente irá receber rubrica diferenciada",
                "rotulo": "rh311_validadependente",
                "rotulorel": "rh311_validadependente",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 5,
                "tamanho": 10,
                "tipoobj": "text"
            }';
    
        COMMENT ON COLUMN pessoal.configuracaoajudacusto.rh311_rubricdepend IS '
            {
                "descricao": "Valida se dependente irá receber rubrica diferenciada",
                "rotulo": "rh311_rubricdepend",
                "rotulorel": "rh311_rubricdepend",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 3,
                "tamanho": 4,
                "tipoobj": "text"
            }';
        
        SELECT fc_gera_dicionario_apartir_tabela('pessoal', 'configuracaoajudacusto');

        COMMENT ON TABLE pessoal.ajudacusto IS
            '{
                "descricao": "Tabela para os cadastro de ajuda de custo",
                "sigla": "rh312",
                "dataincl": "2023-12-28",
                "rotulo": "Tabela para os cadastro de ajuda de custo",
                "tipotabela": 1,
                "naolibclass": false,
                "naolibfunc": false,
                "naolibprog": false,
                "naolibform": false
            }';
        
        COMMENT ON COLUMN pessoal.ajudacusto.rh312_sequencial IS '
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
        
        COMMENT ON COLUMN pessoal.ajudacusto.rh312_instit IS '
            {
                "descricao": "Instituição da ajuda de custo",
                "rotulo": "rh312_instit",
                "rotulorel": "rh312_instit",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';
        
        COMMENT ON COLUMN pessoal.ajudacusto.rh312_regist IS '
            {
                "descricao": "Matrícula da ajuda de custo",
                "rotulo": "rh312_regist",
                "rotulorel": "rh312_regist",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';
        
        COMMENT ON COLUMN pessoal.ajudacusto.rh312_habilita_dependente IS '
            {
                "descricao": "Habilita lançamento para dependente",
                "rotulo": "rh312_habilita_dependente",
                "rotulorel": "rh312_habilita_dependente",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 5,
                "tamanho": 10,
                "tipoobj": "text"
            }';
        
        COMMENT ON COLUMN pessoal.ajudacusto.rh312_dependente IS '
            {
                "descricao": "Dependente como ajuda de custo",
                "rotulo": "rh312_dependente",
                "rotulorel": "rh312_dependente",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';
        
        COMMENT ON COLUMN pessoal.ajudacusto.rh312_local IS '
            {
                "descricao": "Local",
                "rotulo": "rh312_local",
                "rotulorel": "rh312_local",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 3,
                "tamanho": 120,
                "tipoobj": "text"
            }';
        
        COMMENT ON COLUMN pessoal.ajudacusto.rh312_especializacao IS '
            {
                "descricao": "Especialização",
                "rotulo": "rh312_especializacao",
                "rotulorel": "rh312_especializacao",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 3,
                "tamanho": 120,
                "tipoobj": "text"
            }';
        
        COMMENT ON COLUMN pessoal.ajudacusto.rh312_graduacao IS '
            {
                "descricao": "Graduação",
                "rotulo": "rh312_graduacao",
                "rotulorel": "rh312_graduacao",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 3,
                "tamanho": 120,
                "tipoobj": "text"
            }';
        
        COMMENT ON COLUMN pessoal.ajudacusto.rh312_unidade_ensino IS '
            {
                "descricao": "Unidade de Ensino",
                "rotulo": "rh312_unidade_ensino",
                "rotulorel": "rh312_unidade_ensino",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';
        
        COMMENT ON COLUMN pessoal.ajudacusto.rh312_valor IS '
            {
                "descricao": "Unidade de Ensino",
                "rotulo": "rh312_valor",
                "rotulorel": "rh312_valor",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';
        
        COMMENT ON COLUMN pessoal.ajudacusto.rh312_valor IS '
            {
                "descricao": "Valor da Ajuda",
                "rotulo": "rh312_valor",
                "rotulorel": "rh312_valor",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 4,
                "tamanho": 10,
                "tipoobj": "text"
            }';
        
        COMMENT ON COLUMN pessoal.ajudacusto.rh312_quantidade IS '
            {
                "descricao": "Quantidade de parcelas",
                "rotulo": "rh312_quantidade",
                "rotulorel": "rh312_quantidade",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';
        
        COMMENT ON COLUMN pessoal.ajudacusto.rh312_competencia IS '
            {
                "descricao": "Competência de inicio da ajuda",
                "rotulo": "rh312_competencia",
                "rotulorel": "rh312_competencia",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 3,
                "tamanho": 7,
                "tipoobj": "text"
            }';
        
        COMMENT ON COLUMN pessoal.ajudacusto.rh312_bolsa_publica IS '
            {
                "descricao": "Bolsa pública",
                "rotulo": "rh312_bolsa_publica",
                "rotulorel": "rh312_bolsa_publica",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 5,
                "tamanho": 10,
                "tipoobj": "text"
            }';
        
        COMMENT ON COLUMN pessoal.ajudacusto.rh312_ativo IS '
            {
                "descricao": "Ativo",
                "rotulo": "rh312_ativo",
                "rotulorel": "rh312_ativo",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 5,
                "tamanho": 10,
                "tipoobj": "text"
            }';
        
        SELECT fc_gera_dicionario_apartir_tabela('pessoal', 'ajudacusto');
 
        COMMENT ON TABLE pessoal.lancamentoajudacusto IS
            '{
                "descricao": "Tabela de lançamento de ajuda de custo",
                "sigla": "rh313",
                "dataincl": "2023-12-28",
                "rotulo": "Tabela de lançamento de ajuda de custo",
                "tipotabela": 1,
                "naolibclass": false,
                "naolibfunc": false,
                "naolibprog": false,
                "naolibform": false
            }';
            
        COMMENT ON COLUMN pessoal.lancamentoajudacusto.rh313_sequencial IS '
            {
                "descricao": "Chave primária da tabela",
                "rotulo": "rh313_sequencial",
                "rotulorel": "rh313_sequencial",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';
            
        COMMENT ON COLUMN pessoal.lancamentoajudacusto.rh313_ajuda_custo IS '
            {
                "descricao": "Chave estrangeira da ajuda de custo",
                "rotulo": "rh313_ajuda_custo",
                "rotulorel": "rh313_ajuda_custo",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';
            
        COMMENT ON COLUMN pessoal.lancamentoajudacusto.rh313_ano IS '
            {
                "descricao": "Ano da folha",
                "rotulo": "rh313_ano",
                "rotulorel": "rh313_ano",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';
                    
        COMMENT ON COLUMN pessoal.lancamentoajudacusto.rh313_mes IS '
        {
            "descricao": "Mês da folha",
            "rotulo": "rh313_mes",
            "rotulorel": "rh313_mes",
            "maiusculo": false,
            "autocompl": false,
            "aceitatipo": 1,
            "tamanho": 10,
            "tipoobj": "text"
        }';
        
        COMMENT ON COLUMN pessoal.lancamentoajudacusto.rh313_valor IS '
        {
            "descricao": "Valor lançado",
            "rotulo": "rh313_valor",
            "rotulorel": "rh313_valor",
            "maiusculo": false,
            "autocompl": false,
            "aceitatipo": 4,
            "tamanho": 10,
            "tipoobj": "text"
        }';

        COMMENT ON COLUMN pessoal.lancamentoajudacusto.rh313_rubric IS '
        {
            "descricao": "Rubrica lançada",
            "rotulo": "rh313_rubric",
            "rotulorel": "rh313_rubric",
            "maiusculo": false,
            "autocompl": false,
            "aceitatipo": 3,
            "tamanho": 4,
            "tipoobj": "text"
        }';
                    
        COMMENT ON COLUMN pessoal.lancamentoajudacusto.rh313_instit IS '
        {
            "descricao": "Instituição do servidor",
            "rotulo": "rh313_instit",
            "rotulorel": "rh313_instit",
            "maiusculo": false,
            "autocompl": false,
            "aceitatipo": 1,
            "tamanho": 10,
            "tipoobj": "text"
        }';
        SELECT fc_gera_dicionario_apartir_tabela('pessoal', 'lancamentoajudacusto');
        
        select fc_auditoria_cria_funcao('pessoal.lancamentoajudacusto');
        select fc_auditoria_cria_funcao('pessoal.configuracaoajudacusto');
        select fc_auditoria_cria_funcao('pessoal.ajudacusto');

        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;

       
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    private function addFks()
    {

        $sql = <<<SQL
        ALTER TABLE configuracaoajudacusto
         ADD CONSTRAINT configuracaoajutacusto_rubric_instit_fk FOREIGN KEY (rh311_rubric, rh311_instit) REFERENCES pessoal.rhrubricas(rh27_rubric, rh27_instit);
        ALTER TABLE configuracaoajudacusto
        ADD CONSTRAINT configuracaoajutacusto_rubric_depend_instit_fk FOREIGN KEY (rh311_rubricdepend, rh311_instit) REFERENCES pessoal.rhrubricas(rh27_rubric, rh27_instit);
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
