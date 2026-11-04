<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28016RotinaElaboracaoPca extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionarioRotina();
        $this->upTabelaPca();
        $this->dicionarioPlanoContratacao();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downDicionarioRotina();
        $this->downTabelaPca();
    }

    /**
     * @return void
     */
    private function upDicionarioRotina()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229292 ,'Plano de Contratações' ,'Plano de Contratações' ,'' ,'1' ,'1' ,'Plano de Contratações' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228804 ,229292 ,16 ,228802 );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229293 ,'Inclusão' ,'Inclusão Plano de Contratações' ,'pca1_planocontratacao001.php' ,'1' ,'1' ,'Inclusão Plano de Contratações' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 229292 ,229293 ,1 ,228802 );
update db_menu set menusequencia = 17 where id_item = 228804 and modulo = 228802;
update db_itensmenu set id_item = 228863 , descricao = 'Elaboração' , help = 'Elaboração do Plano de Contratações' , funcao = 'pncp1_planocontratacoes001.php' , itemativo = '1' , manutencao = '1' , desctec = 'Elaboração do Plano de Contratações' , libcliente = 'true' where id_item = 228863;
delete from db_menu where id_item_filho = 228863 AND modulo = 228802;
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 229292 ,228863 ,2 ,228802 );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229299 ,'Bloquear UInidade Requisitante' ,'Bloquear UInidade Requisitante' ,'pncp4_unidaderequisitante001.php' ,'1' ,'1' ,'Bloquear UInidade Requisitante' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228805 ,229299 ,2 ,228802 );
SQL
        );
    }

    /**
     * @return void
     */
    private function downDicionarioRotina()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_itensmenu where id_item = 229292;
delete from db_itensmenu where id_item = 229293;
delete from db_menu where id_item_filho = 229292 AND modulo = 228802;
delete from db_menu where id_item_filho = 229293 AND modulo = 228802;
delete from db_menu where id_item_filho = 228863 AND modulo = 228802;
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228804 ,228863 ,10 ,228802 );
update db_menu set menusequencia = 9 where id_item = 228804 and modulo = 228802;
update db_itensmenu set id_item = 228863 , descricao = 'Plano de Contratações' , help = 'Plano de Contratações' , funcao = 'pncp1_planocontratacoes001.php' , itemativo = '1' , manutencao = '1' , desctec = 'Plano de Contratações' , libcliente = 'true' where id_item = 228863;
delete from db_itensmenu where id_item = 229299;
delete from db_menu where id_item_filho = 229299 AND modulo = 228802;
SQL
        );
    }

    private function upTabelaPca()
{
    Schema::create('pncp.planocontratacao', function (Blueprint $table) {
        $table->increments('pn05_codigo');
        $table->string('pn05_unidade', 100)->references('pn02_codigo')->on('unidadespncp');
        $table->integer('pn05_ano');
        $table->integer('pn05_instituicao');
        $table->integer('pn05_status');
        $table->integer('pn05_pncp')->nullable(true);
        $table->date('pn05_datacadastro');
    });

    Schema::create('pncp.planocontratacaoitens', function (Blueprint $table) {
        $table->increments('pn06_codigo');
        $table->integer('pn06_planocontratacao')->references('pn05_codigo')->on('planocontratacao');
        $table->integer('pn06_item');
        $table->integer('pn06_codmater');
        $table->integer('pn06_categoriaitem');
        $table->integer('pn06_classificacaocatalogo');
        $table->integer('pn06_classificacaosuperiorcodigo');
        $table->integer('pn06_unidadefornecimentocodigo');
        $table->float('pn06_quantidade');
        $table->float('pn06_valorunitario');
        $table->float('pn06_valortotal');
        $table->float('pn06_valororcamento');
        $table->integer('pn06_unidaderequisitante');
        $table->date('pn06_datadesejada');
    });

    Schema::create('pncp.verificaunidaderequisitante', function (Blueprint $table) {
        $table->increments('pn07_codigo');
        $table->boolean('pn07_habilitado');
        $table->integer('pn07_instit');
        $table->integer('pn07_usuario');
    });

    DB::connection()->getPdo()->exec(<<<SQL
select configuracoes.fc_auditoria_cria_funcao('pncp.planocontratacao');
select configuracoes.fc_auditoria_cria_funcao('pncp.planocontratacaoitens');
select configuracoes.fc_auditoria_cria_funcao('pncp.verificaunidaderequisitante');
SQL
    );
}

    private function downTabelaPca()
    {
        DB::connection()->getPdo()->exec(<<<SQL
select configuracoes.fc_auditoria_remove_funcao('pncp.planocontratacao');
select configuracoes.fc_auditoria_remove_funcao('pncp.planocontratacaoitens');
select configuracoes.fc_auditoria_remove_funcao('pncp.verificaunidaderequisitante');
SQL
        );
        Schema::drop('pncp.planocontratacaoitens');
        Schema::drop('pncp.planocontratacao');
        Schema::drop('pncp.verificaunidaderequisitante');
    }

    private function dicionarioPlanoContratacao()
    {
        DB::connection()->getPdo()->exec(<<<SQL
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;
      COMMENT ON TABLE pncp.planocontratacao IS
  '{
      "descricao": "Plano Contratacao",
      "sigla": "pn05",
      "dataincl": "2024-08-09",
      "rotulo": "Plano Contratacao",
      "tipotabela": 0,
      "naolibclass": false,
      "naolibfunc": false,
      "naolibprog": false,
      "naolibform": false
   }';
COMMENT ON COLUMN pncp.planocontratacao.pn05_codigo is '{
      "descricao": "Chave primária da tabela",
      "rotulo": "Código",
      "rotulorel": "Código",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN pncp.planocontratacao.pn05_unidade is '{
      "descricao": "Unidade Compradora",
      "rotulo": "Unidade Compradora",
      "rotulorel": "Unidade Compradora",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 2,
      "tamanho": 100,
      "tipoobj": "text"
}';
COMMENT ON COLUMN pncp.planocontratacao.pn05_ano is '{
      "descricao": "Ano do Plano",
      "rotulo": "Ano do Plano",
      "rotulorel": "Ano do Plano",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN pncp.planocontratacao.pn05_instituicao is '{
      "descricao": "Instituicao",
      "rotulo": "Instituicao",
      "rotulorel": "Instituicao",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN pncp.planocontratacao.pn05_status is '{
      "descricao": "Status da Elaboracao",
      "rotulo": "Status da Elaboracao",
      "rotulorel": "Status da Elaboracao",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN pncp.planocontratacao.pn05_pncp is '{
      "descricao": "Sequencial no PNCP",
      "rotulo": "Sequencial no PNCP",
      "rotulorel": "Sequencial no PNCP",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN pncp.planocontratacao.pn05_datacadastro is '{
      "descricao": "Data do Cadastro",
      "rotulo": "Data do Cadastro",
      "rotulorel": "Data do Cadastro",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON TABLE pncp.verificaunidaderequisitante IS
  '{
      "descricao": "Verifica Unidade Requisitante",
      "sigla": "pn07",
      "dataincl": "2024-08-09",
      "rotulo": "Verifica Unidade Requisitante",
      "tipotabela": 0,
      "naolibclass": false,
      "naolibfunc": false,
      "naolibprog": false,
      "naolibform": false
   }';
COMMENT ON COLUMN pncp.verificaunidaderequisitante.pn07_codigo is '{
      "descricao": "Chave primária da tabela",
      "rotulo": "Código",
      "rotulorel": "Código",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN pncp.verificaunidaderequisitante.pn07_habilitado is '{
      "descricao": "Verifica se esta habilitado",
      "rotulo": "Verifica se esta habilitado",
      "rotulorel": "Verifica se esta habilitado",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN pncp.verificaunidaderequisitante.pn07_instit is '{
      "descricao": "Instituicao",
      "rotulo": "Instituicao",
      "rotulorel": "Instituicao",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN pncp.verificaunidaderequisitante.pn07_usuario is '{
      "descricao": "Usuario",
      "rotulo": "Usuario",
      "rotulorel": "Usuario",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON TABLE pncp.planocontratacaoitens IS
  '{
      "descricao": "Plano Contratacao Itens",
      "sigla": "pn06",
      "dataincl": "2024-08-09",
      "rotulo": "Plano Contratacao Itens",
      "tipotabela": 0,
      "naolibclass": false,
      "naolibfunc": false,
      "naolibprog": false,
      "naolibform": false
   }';
COMMENT ON COLUMN pncp.planocontratacaoitens.pn06_codigo is '{
      "descricao": "Chave primária da tabela",
      "rotulo": "Código",
      "rotulorel": "Código",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN pncp.planocontratacaoitens.pn06_planocontratacao is '{
      "descricao": "Codigo Plano Contratacao",
      "rotulo": "Codigo Plano Contratacao",
      "rotulorel": "Codigo Plano Contratacao",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 2,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN pncp.planocontratacaoitens.pn06_item is '{
      "descricao": "Codigo item",
      "rotulo": "Codigo item",
      "rotulorel": "Codigo item",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN pncp.planocontratacaoitens.pn06_codmater is '{
      "descricao": "Codigo do material",
      "rotulo": "Codigo do material",
      "rotulorel": "Codigo do material",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN pncp.planocontratacaoitens.pn06_categoriaitem is '{
      "descricao": "Categoria item",
      "rotulo": "Categoria item",
      "rotulorel": "Categoria item",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN pncp.planocontratacaoitens.pn06_classificacaocatalogo is '{
      "descricao": "Classificacao catalogo",
      "rotulo": "Classificacao catalogo",
      "rotulorel": "Classificacao catalogo",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN pncp.planocontratacaoitens.pn06_classificacaosuperiorcodigo is '{
      "descricao": "Classificacao superior codigo",
      "rotulo": "Classificacao superior codigo",
      "rotulorel": "Classificacao superior codigo",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN pncp.planocontratacaoitens.pn06_unidadefornecimentocodigo is '{
      "descricao": "Unidade fornecimento codigo",
      "rotulo": "Unidade fornecimento codigo",
      "rotulorel": "Unidade fornecimento codigo",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN pncp.planocontratacaoitens.pn06_quantidade is '{
      "descricao": "Quantidade",
      "rotulo": "Quantidade",
      "rotulorel": "Quantidade",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN pncp.planocontratacaoitens.pn06_valorunitario is '{
      "descricao": "Valor unitario",
      "rotulo": "Valor unitario",
      "rotulorel": "Valor unitario",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN pncp.planocontratacaoitens.pn06_valortotal is '{
      "descricao": "Valor total",
      "rotulo": "Valor total",
      "rotulorel": "Valor total",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN pncp.planocontratacaoitens.pn06_valororcamento is '{
      "descricao": "Valor orcamento",
      "rotulo": "Valor orcamento",
      "rotulorel": "Valor orcamento",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN pncp.planocontratacaoitens.pn06_unidaderequisitante is '{
      "descricao": "Unidade requisitante",
      "rotulo": "Unidade requisitante",
      "rotulorel": "Unidade requisitante",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';
COMMENT ON COLUMN pncp.planocontratacaoitens.pn06_datadesejada is '{
      "descricao": "Data desejada",
      "rotulo": "Data desejada",
      "rotulorel": "Data desejada",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 1,
      "tamanho": 10,
      "tipoobj": "text"
}';

SELECT fc_gera_dicionario_apartir_tabela('pncp', 'planocontratacao');
SELECT fc_gera_dicionario_apartir_tabela('pncp', 'planocontratacaoitens');
SELECT fc_gera_dicionario_apartir_tabela('pncp', 'verificaunidaderequisitante');

ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL
        );
    }
}
